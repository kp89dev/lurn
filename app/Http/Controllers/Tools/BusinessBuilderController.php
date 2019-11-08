<?php
namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Services\AuthProvider\SourceUrlHandler;
use App\Models\Source;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Closure;

class BusinessBuilderController extends Controller
{

    public function index()
    {
        return view('business-builder.index');
    }

    public function index_pa()
    {
        return view('business-builder.index-pa');
    }
    
    public function index_dpe()
    {
        return view('business-builder.index-dpe');
    }
}
    