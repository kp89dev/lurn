<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SourceEmail extends Model
{
    protected $guarded = ['id'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function source()
    {
        return $this->belongsTo(Source::class);
    }
}
