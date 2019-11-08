<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestQuestionAnswer extends Model
{
    protected $fillable = ['title', 'order', 'status', 'question_id', 'is_answer'];
    protected $dates = ['updated_at', 'created_at'];
    
    public function question()
    {
        return $this->belongsTo(TestQuestion::class);
    }
    
    public function scopeEnabled($query)
    {
        $query->where($this->getTable().'.status', 1);
    }
}
