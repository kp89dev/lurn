<?php

namespace App\Models\Traits;

use App\Models\User;

trait MergeableUser
{
    public function mainUser()
    {
        return $this->belongsToMany(User::class, 'user_merges', 'merged_user_id', 'into_user_id')
                    ->wherePivot('from_table', '=', $this->table);
    }

    public function isMerged()
    {
        return $this->mainUser()->count();
    }
}
