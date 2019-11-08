<?php

namespace App\Models;

use App\Models\Traits\FindBySlug;
use App\Models\Traits\Sluggable;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use Sluggable, FindBySlug;

    protected $guarded = ['id'];

    public function getExcerptAttribute()
    {
        $excerpt = strip_tags($this->content);

        return preg_replace('/((\S+\s+){30})/', '$1...', $excerpt);
    }
}
