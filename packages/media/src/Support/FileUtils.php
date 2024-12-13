<?php

namespace Filapress\Media\Support;

use InvalidArgumentException;
use Symfony\Component\Mime\MimeTypes;

class FileUtils
{
    public static function convertFriendlyToBytes(string $size): int
    {
        $size = trim(strtolower($size));

        // Match the number and the unit using regex
        if (preg_match('/^(\d+)([kmg])/', $size, $matches)) {
            $number = (int) $matches[1]; // Get the numeric part
            $unit = $matches[2] ?? '';  // Get the unit part (if any)

            // Convert to bytes based on the unit
            return match ($unit) {
                'k' => $number * 1024,
                'm' => $number * 1024 * 1024,
                'g' => $number * 1024 * 1024 * 1024,
                default => $number,
            };
        }

        throw new InvalidArgumentException("Invalid size format: $size");
    }

    public static function convertBytesToFriendly(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision).' '.$units[$pow];
    }

    public static function cleanPath(string $path): string
    {
        return preg_replace('/\/{2,}/', '/', $path);
    }

    public static function resolvePath(string ...$segments): string
    {
        // Start with an empty stack to build the resolved path
        $stack = [];

        // Determine if the path should be absolute
        $absolute = false;

        foreach ($segments as $segment) {
            // Normalize slashes and split the segment into parts
            $parts = explode('/', str_replace('\\', '/', $segment));

            foreach ($parts as $part) {
                if ($part === '' && empty($stack)) {
                    // Empty part at the start signifies an absolute path
                    $absolute = true;
                } elseif ($part === '.' || $part === '') {
                    // Skip '.' and data slashes
                    continue;
                } elseif ($part === '..') {
                    // Pop the stack for '..', if not at the root
                    if (! empty($stack) && end($stack) !== '') {
                        array_pop($stack);
                    } elseif (! $absolute) {
                        // Append '..' if relative path
                        $stack[] = $part;
                    }
                } else {
                    // Add the part to the stack
                    $stack[] = $part;
                }
            }
        }

        // Prepend a slash if it's an absolute path
        return ($absolute ? '/' : '').implode('/', $stack);
    }

    public static function appendFileName(string $path, string $append): string
    {
        $directory = dirname($path);
        $filename = pathinfo($path, PATHINFO_FILENAME);
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        // Append the string to the filename
        $newFilename = $filename.$append;

        // Rebuild the path
        return rtrim($directory, '/').'/'.$newFilename.'.'.$extension;
    }

    public static function extensionFromMime(string $mimeType): ?string
    {
        $mimeTypes = new MimeTypes;
        $extensions = $mimeTypes->getExtensions($mimeType);

        // Return the first extension if available, or null if none is found
        return $extensions[0] ?? null;
    }

    public static function cleanFilename(string $filename): string
    {
        return \Str::snake($filename);
    }

    public static function mimeTypeFromExtension(string $path): ?string
    {
        $extension = str_contains($path, '.') ? pathinfo($path, PATHINFO_EXTENSION) : $path;
        $mimeTypes = new MimeTypes;
        $mimes = $mimeTypes->getMimeTypes($extension);

        return $mimes[0];
    }

    public static function replaceExtension(string $filename, string $newExtension): string
    {
        $newExtension = ltrim($newExtension, '.');

        // Extract the file name without extension and current extension
        $pathInfo = pathinfo($filename);

        // If there is an extension, replace it
        if (isset($pathInfo['extension'])) {
            return $pathInfo['dirname'] !== '.'
                ? "{$pathInfo['dirname']}/{$pathInfo['filename']}.$newExtension"
                : "{$pathInfo['filename']}.$newExtension";
        }

        // If no extension exists, append the new one
        return $filename . ".$newExtension";
    }
}
