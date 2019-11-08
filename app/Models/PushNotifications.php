<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushNotifications extends Model
{
    protected $dates = ['start', 'end', 'created_at', 'updated_at'];
    
    protected $fillable = [
        'admin_title',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'timezone',
        'start_utc',
        'end_utc',
        'all_visitors',
        'content',
        'cta_type',
        'internal_cta_type',
        'internal_course_slug',
        'internal_news_slug',
        'internal_link',
        'external_link',
        'button_text'
    ];

    public function user()
    {
        return $this->belongsToMany(User::class);
    }
}
