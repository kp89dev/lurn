<?php
namespace App\Http\Controllers\Admin\TestResults;

use App\Http\Controllers\Controller;
use App\Models\TestResult;
use niklasravnsborg\LaravelPdf\PdfWrapper;

class TestResultsController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin.role.auth:test-results,read')->only('index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $results = TestResult::latest()->simplePaginate(20);

        return view('admin.test-results.index', compact('results'));
    }

    /**
     * @param TestResult $testResult
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(TestResult $testResult)
    {
        return view('admin.test-results.show', compact('testResult'));
    }

    public function downloadPdf(TestResult $testResult)
   {
        $pdf = app(PdfWrapper::class)->loadView('admin.test-results.pdf', compact('testResult'));

        return $pdf->stream("test-results.pdf");
    }
}
