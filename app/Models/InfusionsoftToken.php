<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class InfusionsoftToken
 * @package App\Models
 *
 * @property string account
 * @property string access_token
 */
class InfusionsoftToken extends Model
{
    //protected $table = 'infusionsoft_tokens';
    public $timestamps = false;

    protected $dates = ['updated_at'];
}
