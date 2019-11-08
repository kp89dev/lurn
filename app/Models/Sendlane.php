<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sendlane extends Model
{
    protected $table = 'sendlane';

    protected $guarded = ['id'];


    public function prepareCredentialsForRequest()
    {
        return array_intersect_key($this->toArray(), array_flip(['subdomain', 'api', 'hash']));
    }
}
