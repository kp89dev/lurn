<?php
namespace App\Models;

class InfusionsoftContact extends Module
{
    protected $table = 'user_infusionsoft';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeAccount($query, string $account)
    {
        return $query->where('is_account', $account);
    }
}
