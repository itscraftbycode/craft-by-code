<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeApiInputMiddleware
{
    private const MAX_INPUT_DEPTH = 8;

    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();

        if ($this->getArrayDepth($input) > self::MAX_INPUT_DEPTH) {
            return response()->json([
                'status' => 'error',
                'code' => 'PAYLOAD_DEPTH_EXCEEDED',
                'message' => 'The provided JSON payload exceeds maximum allowable nesting depth.',
            ], 400);
        }

        $sanitized = $this->sanitizeArray($input);
        $request->merge($sanitized);

        return $next($request);
    }

    private function sanitizeArray(array $data): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            $cleanKey = $this->sanitizeString((string) $key);

            if (is_array($value)) {
                $result[$cleanKey] = $this->sanitizeArray($value);
            } elseif (is_string($value)) {
                $result[$cleanKey] = $this->sanitizeString($value);
            } else {
                $result[$cleanKey] = $value;
            }
        }

        return $result;
    }

    private function sanitizeString(string $val): string
    {
        // 1. Strip null bytes
        $val = str_replace(chr(0), '', $val);

        // 2. Normalize UTF-8 characters if intl extension is present
        if (function_exists('normalizer_normalize') && class_exists('Normalizer')) {
            $normalized = \Normalizer::normalize($val, \Normalizer::FORM_C);
            if ($normalized !== false) {
                $val = $normalized;
            }
        }

        // 3. Strip dangerous script and event handler tags from strings
        $val = preg_replace('/<(script|iframe|object|embed|applet)[^>]*>.*?<\/\1>/is', '', $val);
        $val = preg_replace('/<(script|iframe|object|embed|applet)[^>]*>/is', '', $val);
        $val = preg_replace('/on\w+\s*=\s*(["\']).*?\1/is', '', $val);
        $val = preg_replace('/javascript:[^\s"\'<>]+/is', '', $val);

        return trim($val);
    }

    private function getArrayDepth(array $array): int
    {
        $maxDepth = 1;
        foreach ($array as $value) {
            if (is_array($value)) {
                $depth = $this->getArrayDepth($value) + 1;
                if ($depth > $maxDepth) {
                    $maxDepth = $depth;
                }
            }
        }

        return $maxDepth;
    }
}

