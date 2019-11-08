<?php
namespace App\Http\Controllers\Admin\Questionnaires;

use App\Commands\Controllers\Admin\Survey\Create;
use App\Commands\Controllers\Admin\Survey\Edit;
use App\Commands\Controllers\Admin\Survey\Index;
use App\Commands\Controllers\Admin\Survey\Store;
use App\Commands\Controllers\Admin\Survey\Update;
use App\Http\Controllers\Controller;
use App\Models\Survey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\View;

class SurveyController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin.role.auth:surveys,read')->only('index');

        $this->middleware('admin.role.auth:surveys,write')->only(
            'create',
            'store',
            'edit',
            'update',
            'destroy'
        );

        $this->middleware(function ($request, $next) {
            View::share('surveyTypes', Survey::$types);

            return $next($request);
        })->only('create', 'edit');
    }

    /**
     * @param Index $index
     * @return View
     */
    public function index(Index $index)
    {
        return view('admin.surveys.index', $index->process());
    }

    /**
     * @param Survey $survey
     * @return View
     */
    public function show(Survey $survey)
    {
        return view('admin.surveys.results', compact('survey'));
    }

    /**
     * @param Create $create
     * @return View
     */
    public function create(Create $create)
    {
        return view('admin.surveys.create-edit', $create->process());
    }

    /**
     * @param Store $store
     * @return RedirectResponse
     */
    public function store(Store $store)
    {
        $store->process();

        return redirect()->route('surveys.index')->with('alert-success', 'Survey succesfully added');
    }

    /**
     * @param Survey $survey
     * @param Edit $edit
     * @return View
     */
    public function edit(Survey $survey, Edit $edit)
    {
        return view('admin.surveys.create-edit', $edit->setSurvey($survey)->process());
    }

    /**
     * @param Survey $survey
     * @param Update $update
     * @return RedirectResponse
     */
    public function update(Survey $survey, Update $update)
    {
        $update->setSurvey($survey)->process();

        return redirect()->route('surveys.index')->with('alert-success', 'Survey succesfully modified');
    }

    /**
     * @param Survey $survey
     * @return RedirectResponse
     * @throws \Exception
     */
    public function destroy(Survey $survey)
    {
        $survey->delete();

        request()->session()->flash('alert-success', 'Surveys were successfully deleted!');

        return redirect()->back();
    }

    /**
     * @param Survey $survey
     * @return RedirectResponse
     */
    public function stats(Survey $survey)
    {
        return '';
    }
}
