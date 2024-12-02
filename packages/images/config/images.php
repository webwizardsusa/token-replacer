<?php
return [
    'jpeg_quality' => env('FILAPRESS_IMAGES_JPEG_QUALITY', 90),
    'webp_quality' => env('FILAPRESS_IMAGES_WEBP_QUALITY', 90),
    'responsive_sizes' => [1200, 800, 600, 400],
    'thumbnail_size' => env('FILAPRESS_IMAGES_THUMBNAIL_SIZE', '200x200'),
    'disk' => 'public',
    'path_generator' => '',
    'image_model' => \Filapress\Images\Models\FilapressImage::class,
];
