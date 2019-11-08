<?php
namespace App\Services\Workflows\Backend\Selectors;

use App\Services\Workflows\Backend\Selectors\Contracts\SelectorAbstract;
use App\Services\Workflows\Backend\Selectors\Contracts\SelectorContract;

class DosentOwnCourse extends SelectorAbstract implements SelectorContract
{
    public function join($query, $value)
    {
        return $query->leftJoin('user_courses as ' . $this->getAlias('user_courses') , $this->getAlias('user_courses') . '.user_id', '=', 'users.id');
    }

    public function where($query, $value)
    {
        return $query->where($this->getAlias('user_courses') . '.course_id', '<>', $value[0]['value']);
    }
}
