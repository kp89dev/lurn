<?php
/**
 * Date: 3/22/18
 * Time: 10:51 AM
 */

namespace App\Models\Queries\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

trait SurveyScopes
{
    /**
     * @param Builder|QueryBuilder $query
     * @param string $event
     * @return Builder|QueryBuilder
     */
    public function scopeByEvent($query, string $event)
    {
        return $query->whereHas('surveyEvents', function ($query) use ($event) {
            /** @var Builder|QueryBuilder $query */
            $query->where('name', $event);
        });
    }

    /**
     * @param Builder|QueryBuilder $query
     * @param User $user
     * @return Builder|QueryBuilder
     */
    public function scopeUnanswered($query, User $user)
    {
        return $query->whereHas('questions', function ($query) use ($user) {
            /** @var Builder|QueryBuilder $query */
            $query->doesntHave('userAnswer');
        });
    }
}
