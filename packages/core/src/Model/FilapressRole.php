<?php

namespace Filapress\Core\Model;

use Illuminate\Database\Eloquent\Model;

class FilapressRole extends Model
{
    protected $casts = [
        'permissions' => 'array',
    ];

    protected $guarded = [];
}
