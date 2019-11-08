<?php

namespace App\Models\QueryBuilder;

use Illuminate\Support\Facades\DB;

class RoiCalculator
{
    protected $days;

    public function __construct(int $days)
    {
        $this->days = $days;
    }

    public function get()
    {
        $count = DB::table('users')
            ->whereRaw('DATE_ADD(created_at, INTERVAL ' . $this->days . ' DAY) <= NOW()')
            ->where('status', '!=', 0)
            ->distinct()
            ->count();

        $query = DB::table('user_courses')
            ->selectRaw('sum(
                if (courses.free = 1, 0, 
                  if (
                    user_courses.subscription_payment = 1,
                    course_infusionsoft.subscription_price, course_infusionsoft.price
                  ) * user_courses.payments_made)) as total,
                datediff(date(user_courses.created_at), date(users.created_at)) as days')
            ->join('courses', 'courses.id', '=', 'user_courses.course_id')
            ->join('course_infusionsoft', 'course_infusionsoft.course_id', '=', 'user_courses.course_id')
            ->join('users', 'users.id', '=', 'user_courses.user_id')
            ->whereRaw('user_courses.created_at <= DATE_ADD(users.created_at, INTERVAL ' . $this->days . ' day)')
            ->where('users.status', '!=', 0)
            ->whereRaw('DATE_ADD(users.created_at, INTERVAL ' . $this->days . ' day) <= NOW()')
            ->groupBy('days')
            ->orderBy('days');

        $data = $query->get();
        $total = 0;

        foreach ($data as $item) {
            $value = $item->total;
            $item->total += $total;
            $total += $value;
        }

        return compact('data', 'count');
    }
}