<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoDefault extends Model
{

    protected $fillable = [
        'title',
        'site_name',
        'separator',
        'description',
        'keywords',
        'robots',
        'author',
        'publisher',
        'webmasters_google',
        'webmasters_bing',
        'webmasters_alexa',
        'webmasters_pinterest',
        'webmasters_yandex',
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
        'analytics_google'
    ];

}
