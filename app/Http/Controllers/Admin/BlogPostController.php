<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Security\HtmlSanitizer;
use App\Security\SecureFileUpload;
use App\Security\UrlValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogPostController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::query();
        if ($request->filled('search')) {
            $s = UrlValidator::escapeLike((string) $request->input('search'));
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")->orWhere('category', 'like', "%{$s}%");
            });
        }
        $posts = $query->latest('published_at')->paginate(10)->withQueryString();

        return view('admin.blog.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.blog.form', ['post' => new BlogPost()]);
    }

    public function show(BlogPost $post)
    {
        return redirect()->route('admin.blog.edit', $post->id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'slug' => 'nullable|string|max:200|unique:blog_posts,slug',
            'category' => 'required|string|max:50',
            'image_file' => 'nullable|file|max:8192|mimes:jpg,jpeg,png,webp',
            'image' => 'nullable|string|max:500',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'read_time' => 'nullable|string|max:30',
            'author_name' => 'nullable|string|max:100',
            'is_featured' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
        ], [
            'image_file.uploaded' => 'The image failed to upload. The file may exceed server upload limits (upload_max_filesize / post_max_size in php.ini) or directory write permissions.',
            'image_file.max' => 'The image file must not exceed 8MB.',
        ]);

        if ($request->hasFile('image_file')) {
            try {
                $validated['image'] = SecureFileUpload::storeImage($request->file('image_file'), 'blog', 8192);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return back()->withInput()->withErrors($e->errors());
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Blog image upload failed: '.$e->getMessage());

                return back()->withInput()->withErrors(['image_file' => 'Failed to save uploaded image: '.$e->getMessage()]);
            }
        } elseif (! empty($validated['image'])) {
            $imageInput = trim($validated['image']);
            if (str_starts_with($imageInput, 'blob:')) {
                $validated['image'] = '/assets/images/blog/blog-code.jpg';
            } elseif (! UrlValidator::isSafeMediaPath($imageInput)) {
                return back()->withInput()->withErrors(['image' => 'Featured image URL must be a valid http/https URL or relative media path.']);
            } else {
                $validated['image'] = $imageInput;
            }
        } else {
            $validated['image'] = '/assets/images/blog/blog-code.jpg';
        }

        $validated['slug'] = ! empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $validated['content'] = HtmlSanitizer::sanitize($validated['content']);
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_published'] = $request->boolean('is_published');
        $validated['published_at'] = now();

        BlogPost::create($validated);

        return redirect()->route('admin.blog.index')->with('success', 'Article published successfully.');
    }

    public function edit(BlogPost $post)
    {
        return view('admin.blog.form', compact('post'));
    }

    public function update(Request $request, BlogPost $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'slug' => 'nullable|string|max:200|unique:blog_posts,slug,'.$post->id,
            'category' => 'required|string|max:50',
            'image_file' => 'nullable|file|max:8192|mimes:jpg,jpeg,png,webp',
            'image' => 'nullable|string|max:500',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'read_time' => 'nullable|string|max:30',
            'author_name' => 'nullable|string|max:100',
            'is_featured' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
        ], [
            'image_file.uploaded' => 'The image failed to upload. The file may exceed server upload limits (upload_max_filesize / post_max_size in php.ini) or directory write permissions.',
            'image_file.max' => 'The image file must not exceed 8MB.',
        ]);

        if ($request->hasFile('image_file')) {
            try {
                $previous = $post->image;
                $validated['image'] = SecureFileUpload::storeImage($request->file('image_file'), 'blog', 8192);
                SecureFileUpload::deleteManagedFile($previous);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return back()->withInput()->withErrors($e->errors());
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Blog image upload failed: '.$e->getMessage());

                return back()->withInput()->withErrors(['image_file' => 'Failed to save uploaded image: '.$e->getMessage()]);
            }
        } elseif (! empty($request->input('image'))) {
            $imageInput = trim($request->input('image'));
            if (str_starts_with($imageInput, 'blob:')) {
                unset($validated['image']);
            } elseif (! UrlValidator::isSafeMediaPath($imageInput)) {
                return back()->withInput()->withErrors(['image' => 'Featured image URL must be a valid http/https URL or relative media path.']);
            } else {
                $validated['image'] = $imageInput;
            }
        } else {
            unset($validated['image']);
        }

        $validated['slug'] = ! empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $validated['content'] = HtmlSanitizer::sanitize($validated['content']);
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_published'] = $request->boolean('is_published');

        $post->update($validated);

        return redirect()->route('admin.blog.index')->with('success', 'Article updated.');
    }

    public function destroy(BlogPost $post)
    {
        SecureFileUpload::deleteManagedFile($post->image);
        $post->delete();

        return redirect()->route('admin.blog.index')->with('success', 'Article removed.');
    }
}
