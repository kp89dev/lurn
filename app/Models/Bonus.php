<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bonus extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $with = ['course'];

    public function scopeNext($query, $points)
    {
        $query->where('points_required', '>', $points)
            ->orderBy('points_required');
    }

    public function scopeCurrent($query, $points)
    {
        $query->where('points_required', '<=', $points)
            ->orderBy('points_required', 'desc');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
