<?php

namespace App\Http\Controllers\Home\Legal;

use App\Http\Controllers\Controller;

class LegalController extends Controller
{
    /**
     * Show the SMS Privacy Policies Page.
     *
     * @return \Illuminate\Http\Response
     */
    public function smsPrivacy()
    {
        return view('home.legal.sms');
    }
    
    /**
     * Show the Anti Spam Policy Page.
     *
     * @return \Illuminate\Http\Response
     */
    public function antispam()
    {
        return view('home.legal.antispam');
    }
    
    /**
     * Show the Refund Policy Page.
     *
     * @return \Illuminate\Http\Response
     */
    public function refund()
    {
        return view('home.legal.refund');
    }
    
    /**
     * Show the DMCA Notice Page.
     *
     * @return \Illuminate\Http\Response
     */
    public function dmca()
    {
        return view('home.legal.dmca');
    }
    
    /**
     * Show the Terms of Use Page.
     *
     * @return \Illuminate\Http\Response
     */
    public function terms()
    {
        return view('home.legal.terms-of-use');
    }
    
    /**
     * Show the Privacy Policy.
     *
     * @return \Illuminate\Http\Response
     */
    public function privacy()
    {
        return view('home.legal.privacy');
    }
}
