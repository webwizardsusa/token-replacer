<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Webwizardsusa\OEmbed\OEmbedResponse;

/**
 * @property OEmbedResponse $response
 */
class OEmbed extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $table = 'oembeds';

    protected function casts(): array
    {
        return [
            'data' => 'array'
        ];
    }


    public static function fromUrl(string $url): ?static
    {
        $hash = static::hashUrl($url);
        $instance = static::query()->where('url_hash', $hash)
            ->first();
        if (!$instance) {
            $oembed = app(\Webwizardsusa\OEmbed\OEmbed::class)->fromUrl($url);
            if (!$oembed) {
                return null;
            }

            $instance = static::create([
                'url' => $url,
                'url_hash' => $hash,
                'title' => $oembed->getTitle(),
                'provider' => $oembed->getProvider(),
                'data' => $oembed->toArray()
            ]);
        }
        return $instance;
    }

    public static function hashUrl(string $url): string
    {
        return md5($url);
    }

    public function getResponseAttribute(): ?OEmbedResponse
    {

        if (is_array($this->data)) {
            return OEmbedResponse::hydrate($this->data);
        }
        return null;
    }
}
