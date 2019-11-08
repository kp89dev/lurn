<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * @method static SearchByIdOrTitle(string $term,int $limit)
 */
trait SearchableCourse
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $term
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchByIdOrTitle(Builder $query, string $term, int $limit = 10)
    {
        return $query->where('id', 'like', '%'. $term .'%')
            ->orWhere('title', 'like', '%'. $term .'%')
            ->limit($limit);
    }
}
