<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;

class OutreachController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cdn_url = str_finish(config('app.cdn_assets', '/'), '/');
        return view('home.outreach.index', compact('cdn_url'));
    }
}
