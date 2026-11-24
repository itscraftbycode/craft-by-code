<?php

namespace App\Security;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SecureFileUpload
{
    public const ALLOWED_CATEGORIES = [
        'branding',
        'projects',
        'testimonials',
        'blog',
        'seo',
        'avatars',
    ];

    /**
     * Strict MIME -> canonical extension map. SVG, HTML, PHP, and executables are forbidden.
     */
    private const ALLOWED_MIME_MAP = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    private const ALLOWED_IMAGE_TYPES = [
        IMAGETYPE_JPEG,
        IMAGETYPE_PNG,
        IMAGETYPE_WEBP,
    ];

    public static function storeImage(UploadedFile $file, string $category, int $maxKilobytes = 4096): string
    {
        if (! in_array($category, self::ALLOWED_CATEGORIES, true)) {
            SecurityLogger::log('path_traversal_blocked', 'Rejected upload to invalid media category', 'high', auth()->user(), request(), [
                'category' => $category,
            ]);
            throw ValidationException::withMessages(['file' => 'Invalid upload category.']);
        }

        if (! $file->isValid()) {
            throw ValidationException::withMessages(['file' => 'Uploaded file failed transfer integrity checks.']);
        }

        $maxBytes = $maxKilobytes * 1024;
        $size = (int) $file->getSize();
        if ($size <= 0 || $size > $maxBytes) {
            throw ValidationException::withMessages(['file' => "Image must not exceed {$maxKilobytes} KB."]);
        }

        $realTmpPath = $file->getRealPath();
        if (! $realTmpPath || ! is_file($realTmpPath)) {
            throw ValidationException::withMessages(['file' => 'Uploaded temporary file could not be inspected.']);
        }

        // Verify true magic bytes with resilient fallback if finfo extension is disabled
        $detectedMime = '';
        if (class_exists(\finfo::class)) {
            try {
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $detectedMime = (string) $finfo->file($realTmpPath);
            } catch (\Throwable $e) {
                $detectedMime = '';
            }
        } elseif (function_exists('mime_content_type')) {
            $detectedMime = (string) @mime_content_type($realTmpPath);
        }

        if (empty($detectedMime) || ! isset(self::ALLOWED_MIME_MAP[$detectedMime])) {
            $imageInfo = @getimagesize($realTmpPath);
            if (is_array($imageInfo) && ! empty($imageInfo['mime']) && isset(self::ALLOWED_MIME_MAP[$imageInfo['mime']])) {
                $detectedMime = (string) $imageInfo['mime'];
            }
        }

        if (empty($detectedMime) || ! isset(self::ALLOWED_MIME_MAP[$detectedMime])) {
            $candidateMime = (string) $file->getMimeType();
            if (isset(self::ALLOWED_MIME_MAP[$candidateMime])) {
                $detectedMime = $candidateMime;
            }
        }

        if (! isset(self::ALLOWED_MIME_MAP[$detectedMime])) {
            SecurityLogger::log('upload_rejected', 'Blocked uploaded file with disallowed MIME signature', 'high', auth()->user(), request(), [
                'detected_mime' => $detectedMime,
                'original_name' => $file->getClientOriginalName(),
                'category' => $category,
            ]);
            throw ValidationException::withMessages(['file' => 'Only JPEG, PNG, and WebP images are permitted. SVG and executable files are blocked.']);
        }

        // Verify valid raster image structure
        $imageInfo = @getimagesize($realTmpPath);
        if (! is_array($imageInfo) || empty($imageInfo[0]) || empty($imageInfo[1]) || ! in_array($imageInfo[2], self::ALLOWED_IMAGE_TYPES, true)) {
            SecurityLogger::log('upload_rejected', 'Blocked malformed or spoofed image upload', 'high', auth()->user(), request(), [
                'detected_mime' => $detectedMime,
                'category' => $category,
            ]);
            throw ValidationException::withMessages(['file' => 'Uploaded file is not a valid raster image.']);
        }

        // Reject polyglot payloads containing embedded PHP, script tags, or inline SVG XML
        $rawBytes = @file_get_contents($realTmpPath);
        if ($rawBytes === false || preg_match('/<\?php|<\?=|<script\b|<svg\b/i', $rawBytes)) {
            SecurityLogger::log('upload_rejected', 'Blocked potential polyglot or embedded script payload inside upload', 'critical', auth()->user(), request(), [
                'detected_mime' => $detectedMime,
                'category' => $category,
            ]);
            throw ValidationException::withMessages(['file' => 'Uploaded file rejected by security content inspection.']);
        }

        $canonicalExt = self::ALLOWED_MIME_MAP[$detectedMime];
        $safeFilename = Str::uuid()->toString().'.'.$canonicalExt;

        $targetDir = storage_path('app/uploads/'.$category);
        if (! File::isDirectory($targetDir)) {
            File::makeDirectory($targetDir, 0775, true, true);
        }

        try {
            $file->move($targetDir, $safeFilename);
            @chmod($targetDir.DIRECTORY_SEPARATOR.$safeFilename, 0664);
        } catch (\Throwable $e) {
            SecurityLogger::log('upload_failed', 'Failed moving uploaded file: '.$e->getMessage(), 'high', auth()->user(), request());
            throw ValidationException::withMessages(['image_file' => 'Server failed to save uploaded image. Please check directory permissions on storage/app/uploads.']);
        }

        return '/media/'.$category.'/'.$safeFilename;
    }

    public static function deleteManagedFile(?string $publicPath): void
    {
        if (! is_string($publicPath) || trim($publicPath) === '') {
            return;
        }

        // Never delete repository static assets
        if (str_starts_with($publicPath, '/assets/') || str_starts_with($publicPath, 'assets/')) {
            return;
        }

        // Delete non-public media files served via /media/{category}/{filename}
        if (preg_match('#^/media/(branding|projects|testimonials|blog|seo|avatars)/([a-f0-9\-]{36}\.(jpg|png|webp))$#i', $publicPath, $matches)) {
            $baseDir = realpath(storage_path('app/uploads/'.$matches[1]));
            if ($baseDir) {
                $targetPath = $baseDir.DIRECTORY_SEPARATOR.$matches[2];
                $resolved = realpath($targetPath);
                if ($resolved && str_starts_with($resolved, $baseDir.DIRECTORY_SEPARATOR) && is_file($resolved)) {
                    @unlink($resolved);
                }
            }

            return;
        }

        // Only delete legacy uploaded files strictly inside public/uploads
        if (str_starts_with($publicPath, '/uploads/')) {
            $publicUploads = realpath(public_path('uploads'));
            $legacyFullPath = realpath(public_path(ltrim($publicPath, '/')));

            if ($publicUploads && $legacyFullPath && str_starts_with($legacyFullPath, $publicUploads.DIRECTORY_SEPARATOR) && is_file($legacyFullPath)) {
                @unlink($legacyFullPath);
            }
        }
    }
}

