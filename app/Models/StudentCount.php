<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentCount extends Model
{
    public $incrementing = false;
    protected $fillable = ['id', 'students'];
}
