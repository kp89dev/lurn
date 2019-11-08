<?php

namespace App\Models\QueryBuilder;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserSpendings
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function get()
    {
        return DB::select("
            select sum(if (uc.subscription_payment, ci.subscription_price * uc.payments_made, ci.price)) as total
            from user_courses uc
            join course_infusionsoft ci
              on ci.id = uc.course_infusionsoft_id
            where uc.user_id = ?
              and uc.invoice_id is not null
        ", [$this->user->id])[0]->total;
    }
}