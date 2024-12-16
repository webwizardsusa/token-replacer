<?php

namespace Filapress\Media\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FilapressMediaUsage extends Model
{
    public $incrementing = false;

    protected $guarded = [];

    public $timestamps = false;

    protected $table = 'filapress_media_usage';

    public function media(): BelongsTo
    {
        return $this->belongsTo(FilapressMedia::class, 'media_id');
    }

    public function usage(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo('usage');
    }
}
