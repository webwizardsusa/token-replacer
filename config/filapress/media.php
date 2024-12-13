<?php

return [
    'disk' => 'public',
    'path' => 'uploads/{{date:Y}}/{{date:m}}/{{file:filename}}.{{file:extension}}',
    'types' => [
        \App\Media\Types\ImageType::configure(
            disk: 'public',
            path: 'images/{{date:Y}}/{{date:m}}/{{file:filename}}.{{file:extension}}',
            responsiveSizes: [1200, 800, 600, 400],
            variants:['card']
        ),
    ],

    'responsive_sizes' => [1200, 800, 600, 400],
    'image_quality' => 90,
    'variant_disk' => 'public',
    'variant_path' => 'images/variants/{{variant:name}}/{{date:Y}}/{{date:m}}/{{file:filename}}.{{file:extension}}',
    'thumbnail_width' => 200,
    'thumbnail_height' => 200,
    'thumbnail_quality' => 90,
    'thumbnail_disk' => 'public',
    'thumbnail_path' => 'images/{{date:Y}}/{{date:m}}/{{file:filename}}.thumbnail.{{file:extension}}',
    'image_driver' => 'gd',
    'collections' => [
        \App\Media\Collections\ContentCollection::class,
    ],
    'image_variants' => [
        \App\Media\ImageVariants\Card::class,
    ],
];
