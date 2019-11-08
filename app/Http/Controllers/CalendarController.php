<?php
namespace App\Http\Controllers;

use Closure;

class CalendarController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, Closure $next) {
            $this->seoSetup($request);

            return $next($request);
        });
    }

    public function index()
    {
        return view('pages.calendar');
    }
}
