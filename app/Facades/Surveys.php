<?php
/**
 * Date: 3/20/18
 * Time: 10:29 AM
 */

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Surveys extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'SurveysLibrary';
    }
}