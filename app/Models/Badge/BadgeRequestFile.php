<?php
namespace App\Models\Badge;

use Illuminate\Database\Eloquent\Model;

class BadgeRequestFile extends Model
{
    protected $fillable = ['file_path'];
    
    public function badgeRequest()
    {
        return $this->belongsTo(BadgeRequest::class);
    }
}
