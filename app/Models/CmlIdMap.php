<?php
/**
 * Date: 3/15/18
 * Time: 1:14 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmlIdMap extends Model
{
    protected $table = 'cml_id_map';

    protected $fillable = [
        'old_id',
        'new_id',
        'type',
        'connection',
    ];
}