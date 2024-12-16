<?php

namespace Filapress\Media\Traits;

use Filapress\Media\Models\FilapressMediaUsage;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait ModelInteractsWithMedia
{
    public static function bootModelInteractsWithMedia(): void
    {
        static::saved(function ($model) {
            $model->syncMediaUsage();
        });

        static::deleted(function ($model) {
            if (! isset($model->forceDeleting) || $model->forceDeleting) {
                FilapressMediaUsage::query()
                    ->where('usage_type', $model->getMorphClass())
                    ->where('usage_id', $model->getKey())
                    ->delete();
            }
        });
    }

    /**
     * Synchronize the media usage for the current model by updating the database.
     * Removes obsolete media usage entries and adds new ones based on the current
     * media items associated with the model.
     */
    public function syncMediaUsage(): void
    {
        $items = $this->getMediaItems();
        $existingIds = FilapressMediaUsage::query()
            ->where('usage_type', $this->getMorphClass())
            ->where('usage_id', $this->getKey())
            ->get('media_id')
            ->pluck('media_id')
            ->toArray();

        $toAdd = array_diff($items, $existingIds);
        $toDelete = array_diff($existingIds, $items);

        // Delete obsolete media usages
        if (! empty($toDelete)) {
            FilapressMediaUsage::query()
                ->where('usage_type', $this->getMorphClass())
                ->where('usage_id', $this->getKey())
                ->whereIn('media_id', $toDelete)
                ->delete();
        }

        // Add new media usages
        foreach ($toAdd as $mediaId) {
            FilapressMediaUsage::create([
                'media_id' => $mediaId,
                'usage_type' => $this->getMorphClass(),
                'usage_id' => $this->getKey(),
            ]);
        }
    }

    /**
     * Return an array of media ids of all media used in this model.
     */
    abstract public function getMediaItems(): array;
}
