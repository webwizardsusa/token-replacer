<?php

use Filapress\Media\Policies\FilapressMediaPolicy;

/*
 * NOTE: All path's utilize WebWizards Token Replacer package to dynamically generate paths. They provide the date
 * and file tokens. Variants add a variant token, that has a 'name' variable.
 */
return [
    /*
     * The following items set up the storage for your media uploads and files.
     */
    'disk' => 'public',
    'path' => 'uploads/{date:Y}/{date:m}/{file:basename}.{file:extension}',

    /*
     * Configure your media types here.
     */
    'types' => [
        \Filapress\Media\Types\ImageType::configure(
            disk: 'public',
            path: 'images/{date:Y}/{date:m}/{file:basename}.{file:extension}',
            responsiveSizes: [1200, 800, 600, 400],
        ),
    ],

    /**
     * The following sets the responsive sizes to generate.
     */
    'responsive_sizes' => [1200, 800, 600, 400],

    /*
     * The variance determines when a responsive size should be generated. It's a multiplier by the width. If the
     * image width is less than width*{variance} then it will not generate that size image.
     */
    'responsive_variance' => 1.5,

    /*
     * Sets the image driver Intervention will utilize in image generation. Can either be 'gd' or 'imagick'.
     */
    'image_driver' => 'gd',

    'image_quality' => 90,
    'thumbnail_width' => 200,
    'thumbnail_height' => 200,
    'thumbnail_quality' => 90,
    'thumbnail_disk' => 'public',
    'thumbnail_path' => 'uploads/{date:Y}/{date:m}/{file:basename}.thumbnail.{file:extension}',

    /*
     * Set the storage for your variants. By default, we store the image in a directory named by the variant.
     * This allows easy deletion of variants when they are no longer needed.
     */
    'variant_disk' => 'public',
    'variant_path' => 'images/variants/{{variant:name}}/{{date:Y}}/{{date:m}}/{{file:filename}}.{{file:extension}}',

    /*
     * Specify your collection classes here.
     */
    'collections' => [],

    /*
     * Specify your variant classes here.
     */
    'image_variants' => [],

    /*
     * When soft deleted models should be pruned. This value is parsed by DateInterval::createFromDateString
     * @see https://www.php.net/manual/en/dateinterval.createfromdatestring.php
     * You can set to null to disable pruning.
     */
    'prune_after' => '1 hours',

    /*
     * You can override the Laravel policy Media uses by changing this value.
     */
    'policy' => FilapressMediaPolicy::class,

    /*
     * This defines the actual model classes to use.
     */
    'media_model' => \Filapress\Media\Models\FilapressMedia::class,
    'variant_model' => \Filapress\Media\Models\FilapressMediaVariant::class,
    'usage_model' => \Filapress\Media\Models\FilapressMediaUsage::class,

];
