<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;

class EducationController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home.education.index');
    }
}
