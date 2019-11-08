<?php
namespace App\Models;

class TestResult extends Module
{
    protected $table = 'test_users';
    protected $casts = [
        'answer' => 'array'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function test()
    {
        return $this->belongsTo(Test::class);
    }
}
