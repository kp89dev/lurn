<?php
namespace App\Http\Controllers;

use App\Models\Faq;
use Closure;

class SupportController extends Controller
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
        return view('pages.support');
    }

    public function faq()
    {
        return view('pages.faq')->withItems(
                Faq::select(['question', 'answer'])->get()
        );
    }
}
