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
    /**
     * Handles the event after attaching media by generating its variations.
     *
     * @param FilapressMedia $media The media instance to which variations need to be generated.
     * @return FilapressMedia The processed media instance with variations.
     */
    public function mediaHasVariationsAfterAttach(FilapressMedia $media): FilapressMedia
    {
        $this->generateVariants($media);

        return $media;
    }

    /**
     * Generates variations of the provided media instance.
     *
     * Creates and processes multiple variations of the media based on defined formats
     * and stores them appropriately.
     *
     * @param FilapressMedia $media The media instance for which the variations are to be generated.
     * @return FilapressMedia The media instance with all variations created.
     */
    public function generateVariants(FilapressMedia $media): FilapressMedia
    {
        $source = ImageFactory::make()->fromStorage($media->disk, $media->path);

        foreach ($this->getVariations($media) as $variant) {
            $this->generateVariant($media, $variant, $source);
        }

        return $media;
    }

    /**
     * Generates a media variant for the given media entity.
     *
     * This method creates or updates a specific variant of a given media file.
     * If a variant already exists, its files are deleted before proceeding.
     * If no variant exists, a new instance is created and populated.
     *
     * A generator, if available for the provided variant, is used to process the source
     * media file and generate various sizes saved to the configured disk.
     * The resulting data is then saved to the database.
     *
     * @param FilapressMedia $media The media entity for which the variant is created or updated.
     * @param string $variant The name of the variant to be generated.
     * @param Image|null $source The optional source image to generate the variant from. If not provided, it defaults to the original media file.
     *
     * @return FilapressMedia The updated media entity with its variants.
     */
    public function generateVariant(FilapressMedia $media, string $variant, ?Image $source = null): FilapressMedia
    {
        $source = $source ?? ImageFactory::make()->fromStorage($media->disk, $media->path);
        $existing = $media->variants->first(fn (FilapressMediaVariant $model) => $model->name === $variant);
        if (! $media->id) {
            $media->save();
        }
        if ($existing) {
            $existing->deleteFiles();
        } else {
            $existing = app(FilapressMediaVariant::class);
            $existing->fill([
                'media_id' => $media->id,
                'name' => $variant,
                'disk' => config('filapress.media.variant_disk'),
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

    /**
     * Retrieves a list of variations for the given media.
     *
     * Combines the default configuration variants with the collection-specific
     * variants, if available, and ensures the result is a unique set of variations.
     *
     * @param FilapressMedia $media The media object to retrieve variations for.
     * @return array The unique list of variations.
     */
    protected function getVariations(FilapressMedia $media): array
    {
        $variants = $this->config('variants', []);
        if ($collectionVariants = $media->getCollection()?->variants()) {
            $variants = array_unique(array_merge($variants, $collectionVariants));
        }

        return $variants;
    }
}
