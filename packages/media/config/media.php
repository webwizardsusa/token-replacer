<?php

return [
    'disk' => 'public',
    'path' => 'uploads/{date:Y}/{date:m}/{file:basename}.{file:extension}',
    'types' => [
        \Filapress\Media\Types\ImageType::configure(
            disk: 'public',
            path: 'images/{date:Y}/{date:m}/{file:basename}.{file:extension}',
            responsiveSizes: [1200, 800, 600, 400],
        ),
    ],

    'responsive_sizes' => [1200, 800, 600, 400],
    'image_quality' => 90,
    'thumbnail_width' => 200,
    'thumbnail_height' => 200,
    'thumbnail_quality' => 90,
    'thumbnail_disk' => 'public',
    'thumbnail_path' => 'uploads/{date:Y}/{date:m}/{file:basename}.thumbnail.{file:extension}',
    'image_driver' => 'gd',
    'collections' => [],
    'image_variants' => [],
];
