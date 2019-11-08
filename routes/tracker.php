<?php
use Illuminate\Support\Facades\Route;

Route::get('ce', 'Tracker\IndexController@index')->name('internal-tracker');
