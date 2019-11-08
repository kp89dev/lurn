<?php
namespace App\Models;

use App\Models\Badge\BadgeRequest;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $guarded = ['id'];

    protected $appends = ['src'];

    public function scopeEnabled($query)
    {
        $query->whereStatus(1);
    }
    
    public function getSrcAttribute()
    {
        return app()->isLocal()
            ? 'https://unsplash.it/300/300'
            : str_finish(config('app.cdn_static'), '/') . $this->image;
    }
    
    public function requests()
    {
        return $this->hasMany(BadgeRequest::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
