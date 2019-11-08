<?php
namespace App\Models;

use App\Services\AuthProvider\Signature;
use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    public function sourceToken()
    {
        return $this->hasMany(SourceToken::class);
    }
}
