<?php
namespace App\Services\Workflows\Backend\Selectors;

use App\Services\Workflows\Backend\Selectors\Contracts\SelectorAbstract;
use App\Services\Workflows\Backend\Selectors\Contracts\SelectorContract;

class Country extends SelectorAbstract implements SelectorContract
{
    public function join($query, $value)
    {
        return $query->join('user_logins as ' . $this->getAlias('user_logins'), $this->getAlias('user_logins') . '.user_id', '=', 'users.id');
    }

    public function where($query, $value)
    {
        return $query->where($this->getAlias('user_logins') . '.country', '=', $value[0]['value']);
    }
}
