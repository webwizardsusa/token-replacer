<?php

namespace App\Models;

use App\Html\PostDefinition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class Post extends Model
{
    //
    protected $guarded = [];


    public function renderBody(): HtmlString
    {
        return new HtmlString(PostDefinition::for($this->body)
            ->parse());
    }
}
