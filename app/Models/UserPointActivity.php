<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPointActivity extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'transaction',
        'points',
        'pending'
    ];

    public function scopeWithUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
