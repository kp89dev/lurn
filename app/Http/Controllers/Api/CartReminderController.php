<?php

namespace App\Http\Controllers\Api;

use App\Models\CartReminder;
use Illuminate\Http\Request;

class CartReminderController
{
    public function save(Request $request)
    {
        CartReminder::updateOrCreate($request->only('user_id', 'course_id'), $request->all());
    }

    public function remove(Request $request)
    {
        CartReminder::where($request->all())->delete();
    }
}