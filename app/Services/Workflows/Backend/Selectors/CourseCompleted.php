<?php
namespace App\Services\Workflows\Backend\Selectors;

use App\Services\Workflows\Backend\Selectors\Contracts\SelectorAbstract;
use App\Services\Workflows\Backend\Selectors\Contracts\SelectorContract;

class CourseCompleted extends SelectorAbstract implements SelectorContract
{
    public function join($query, $values)
    {
        return $query->join('user_courses as ' . $this->getAlias('user_courses'), $this->getAlias('user_courses') . '.user_id', '=', 'users.id');
    }

    public function where($query, $values)
    {
        return $query->where($this->getAlias('user_courses') . '.completed_at', '>=', now()->subMinutes(5));
    }
}
