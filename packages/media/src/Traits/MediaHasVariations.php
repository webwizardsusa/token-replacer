<?php

namespace Filapress\Media\Traits;

use Filapress\Media\Images\ImageFactory;
use Filapress\Media\ImageVariants;
use Filapress\Media\MediaType;
use Filapress\Media\Models\FilapressMedia;
use Filapress\Media\Models\FilapressMediaVariant;
use Intervention\Image\Image;
use Webwizardsusa\TokenReplace\Transformers\ArrayTransformer;

/**
 * @mixin MediaType
 */
trait MediaHasVariations
{
    function mediaHasVariationsAfterAttach(FilapressMedia $media): FilapressMedia
    {
        $this->generateVariants($media);
        return $media;
    }


    public function generateVariants(FilapressMedia $media): FilapressMedia
    {
        $source = ImageFactory::make()->fromStorage($media->disk, $media->path);

        foreach( $this->getVariations($media) as $variant) {
            $this->generateVariant($media, $variant, $source);
        }
        return $media;
    }

    public function generateVariant(FilapressMedia $media, string $variant, ?Image $source = null): FilapressMedia
    {
        $source = $source ?? ImageFactory::make()->fromStorage($media->disk, $media->path);
        $existing = $media->variants->first(fn(FilapressMediaVariant $model) => $model->name === $variant);
        if (!$media->id) {
            $media->save();
        }
        if ($existing) {
            $existing->deleteFiles();
        } else {
            $existing = app(FilapressMediaVariant::class);
            $existing->fill([
                'media_id' => $media->id,
                'name' => $variant,
                'disk' => config('filapress.media.variant_disk')
            ]);
        }

        $generator = ImageVariants::make()->has($variant) ? ImageVariants::make()->get($variant) : null;
        $results = null;
        if ($generator) {
            $path = $this->makePathGenerator(config('filapress.media.variant_path'), $media->path)
                ->with('variant', new ArrayTransformer(['name' => $variant]))
                ->transform();
            $results = $generator->generateFromImage($source)
                ->saveTo($path, config('filapress.media.variant_disk'))
                ->getResults();

        }
        if ($results) {
            $existing->fill(
                $results['source']->toArray()
            );
            $sizes = array_map(function ($sizeData) {
                return $sizeData->toArray();
            }, $results['sizes']);
            $existing->sizes = $sizes;
            $existing->save();
        } else {
            if ($existing->id) {
                $existing->delete();
            }
        }

        return $media;
    }


    protected function getVariations(FilapressMedia $media): array {
        $variants = $this->config('variants', []);
        if ($collectionVariants = $media->getCollection()?->variants()) {
            $variants = array_unique(array_merge($variants, $collectionVariants));
        }

        return $variants;
    }


}
