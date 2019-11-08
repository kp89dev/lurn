<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyView extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Scope a query to only include results with answers.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAnswers($query)
    {
        return $query->where('answered', 1);
    }

    /**
     * Scope a query to only include results that were viewed and not answered.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnlyViewed($query)
    {
        return $query->where('answered', 0);
    }
}
