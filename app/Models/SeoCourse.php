<?php
namespace App\Models;

use App\Models\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class SeoCourse extends Model
{
    use Searchable;

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    
    protected $fillable = [
        'course_id',
        'title',
        'site_name',
        'separator',
        'description',
        'keywords',
        'robots',
        'author',
        'publisher',
        'og_enabled',
        'og_prefix',
        'og_type',
        'og_title',
        'og_site_name',
        'og_description',
        'og_properties',
        'twitter_enabled',
        'twitter_card',
        'twitter_site',
        'twitter_title',
        'twitter_meta',
    ];

}
