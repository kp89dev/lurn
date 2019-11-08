<?php

namespace App\Http\Controllers\Home\Career;

use App\Http\Controllers\Controller;

class CareerController extends Controller
{
    /**
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home.career.index');
    }
    
    /**
     *
     * @return \Illuminate\Http\Response
     */
    public function webdeveloper()
    {
        return view('home.career.webdeveloper');
    }
    
    /**
     *
     * @return \Illuminate\Http\Response
     */
    public function juniorcopywriter()
    {
        return view('home.career.juniorcopywriter');
    }
    
    /**
     *
     * @return \Illuminate\Http\Response
     */
    public function lurncentermanager()
    {
        return view('home.career.lurncentermanager');
    }
    
    /**
     *
     * @return \Illuminate\Http\Response
     */
    public function customerhappinessspecialist()
    {
        return view('home.career.customerhappinessspecialist');
    }
    
    /**
     *
     * @return \Illuminate\Http\Response
     */
    public function associatecontentmanager()
    {
        return view('home.career.associatecontentmanager');
    }
    
    /**
     *
     * @return \Illuminate\Http\Response
     */
    public function designer()
    {
        return view('home.career.designer');
    }
}