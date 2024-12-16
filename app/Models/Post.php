<?php

namespace App\Models;

use App\Html\PostDefinition;
use Filapress\Media\Elements\MediaElement;
use Filapress\Media\Models\FilapressMedia;
use Filapress\Media\Traits\ModelInteractsWithMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\HtmlString;

class Post extends Model
{
    use ModelInteractsWithMedia;

    //
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        static::creating(function (Post $post) {
            if (! $post->user_id) {
                $post->user_id = auth()->id();
            }
        });

        static::saving(function (Post $post) {
            if ($post->published && ! $post->published_at) {
                $post->published_at = now();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(FilapressMedia::class);
    }

    public function renderBody(): HtmlString
    {
        return new HtmlString(PostDefinition::for($this->body)
            ->parse());
    }

    public function getMediaItems(): array
    {
        $items = [];
        if ($this->image_id) {
            $items[] = $this->image_id;
        }

        $elements = MediaElement::make()
            ->extract($this->body);

        foreach ($elements as $element) {
            if ($id = $element->getAttribute('media')) {
                $items[] = $id;
            }
        }

        return $items;
    }
}
