<?php

namespace Filapress\Images\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FilapressImage extends Model
{

    protected $guarded = [];
    protected $table = 'filapress_images';
    protected $casts = [
        'formats' => 'array',
        'temporary' => 'boolean',
        'width' => 'integer',
        'height' => 'integer',
        'filesize' => 'integer'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('filapress.users.models.user'));
    }

}
