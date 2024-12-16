<?php

namespace Filapress\Media\Support;

use InvalidArgumentException;
use Symfony\Component\Mime\MimeTypes;

class FileUtils
{
    /**
     * Converts a human-readable file size string into its equivalent size in bytes.
     *
     * @param string $size The file size string, which should be in a format like "10k", "5m", or "2g".
     *                     Units are case-insensitive and supported units are:
     *                     - "k" for kilobytes
     *                     - "m" for megabytes
     *                     - "g" for gigabytes
     *
     * @return int The size in bytes.
     *
     * @throws InvalidArgumentException If the provided size string is not in a valid format.
     */
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

    /**
     * Converts a size in bytes into a human-readable format with appropriate units.
     *
     * @param int $bytes The size in bytes to be converted.
     * @param int $precision The number of decimal places to include in the formatted output (default is 2).
     *
     * @return string The formatted size string with the appropriate unit (e.g., "10.24 KB", "5.00 MB").
     */
    public static function convertBytesToFriendly(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision).' '.$units[$pow];
    }

    /**
     * Removes duplicate slashes from the given path.
     *
     * @param string $path The file or directory path to clean.
     * @return string The cleaned path with duplicate slashes replaced by a single slash.
     */
    public static function cleanPath(string $path): string
    {
        return preg_replace('/\/{2,}/', '/', $path);
    }

    /**
     * Resolves a sequence of path segments into a single, normalized path.
     *
     * Handles relative (`.`) and parent (`..`) references, removes redundant slashes,
     * and respects whether the resolved path is absolute or relative.
     *
     * @param string ...$segments A variable number of path segments to resolve.
     * @return string The resolved and normalized file or directory path.
     */
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

    /**
     * Appends a specified string to the filename within the given file path.
     *
     * @param string $path The original file path.
     * @param string $append The string to append to the filename.
     * @return string The updated file path with the appended string in the filename.
     */
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

    /**
     * Retrieves the file extension associated with the given MIME type.
     *
     * @param string $mimeType The MIME type to resolve to a file extension.
     * @return string|null The first file extension matching the MIME type, or null if none is found.
     */
    public static function extensionFromMime(string $mimeType): ?string
    {
        $mimeTypes = new MimeTypes;
        $extensions = $mimeTypes->getExtensions($mimeType);

        // Return the first extension if available, or null if none is found
        return $extensions[0] ?? null;
    }

    /**
     * Converts the given filename to snake_case format.
     *
     * @param string $filename The original filename.
     * @return string The filename converted to snake_case.
     */
    public static function cleanFilename(string $filename): string
    {
        return \Str::snake($filename);
    }

    /**
     * Retrieves the MIME type associated with a file extension from the given path.
     *
     * @param string $path The file path or file extension.
     * @return string|null The MIME type corresponding to the file's extension, or null if not found.
     */
    public static function mimeTypeFromExtension(string $path): ?string
    {
        $extension = str_contains($path, '.') ? pathinfo($path, PATHINFO_EXTENSION) : $path;
        $mimeTypes = new MimeTypes;
        $mimes = $mimeTypes->getMimeTypes($extension);

        return $mimes[0];
    }

    /**
     * Replaces or appends a file's extension with a specified new extension.
     *
     * @param string $filename The original file name, including its current extension if it exists.
     * @param string $newExtension The new file extension to set, with or without a leading period.
     * @return string The modified file name with the new extension applied.
     */
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
        return $filename.".$newExtension";
    }
}
