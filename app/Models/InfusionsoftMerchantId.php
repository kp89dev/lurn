<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfusionsoftMerchantId extends Model
{
    protected $casts = ['ids' => 'array'];

    protected $guarded = ['id'];
}
