<?php
namespace App\Models\Badge;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class BadgeRequest extends Model
{
    protected $fillable = ['comment', 'badge_id', 'status'];

    public function files()
    {
        return $this->hasMany(BadgeRequestFile::class);
    }

    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
