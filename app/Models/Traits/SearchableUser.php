<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * @method static byEmail(string $email)
 * @method static searchByEmailOrName(string $term,int $limit)
 */
trait SearchableUser
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $email
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByEmail(Builder $query, string $email)
    {
        return $query->whereEmail($email);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $term
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchByEmailOrName(Builder $query, string $term, int $limit = 10)
    {
        return $query->where('email', 'like', '%'. $term .'%')
            ->orWhere('name', 'like', '%'. $term .'%')
            ->limit($limit);
    }
}
