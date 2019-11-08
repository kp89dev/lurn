<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestQuestion extends Model
{
    protected $guarded = ['id'];
    protected $dates = ['created_at', 'updated_at'];
    
    public function test()
    {
        return $this->belongsTo(Test::class);
    }
    
    public function answers()
    {
        return $this->hasMany(TestQuestionAnswer::class, 'question_id', 'id');
    }
    
    public function getOrderedAnswers()
    {
        return $this->answers()->enabled()->orderby('order', 'asc')->get();
    }
    
    public function scopeEnabled($query)
    {
        $query->where($this->getTable().'.status', 1);
    }
}
