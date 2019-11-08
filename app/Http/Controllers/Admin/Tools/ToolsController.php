<?php
namespace App\Http\Controllers\Admin\Tools;

use App\Models\CourseTool;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\AuthProvider\SourceUrlHandler;
use App\Models\Source;
use GuzzleHttp\Client;

class ToolsController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin.role.auth:tools,read')
            ->only('index', 'launchpad');
        $this->middleware('admin.role.auth:tools,write')
            ->only('create', 'store', 'edit', 'update', 'destroy');
    }

    public function index()
    {
        $settings = CourseTool::paginate(20);

        return view('admin.tools.index', compact('settings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $courseTool  = new CourseTool();
        $action = route('tools.store', ['courseTool' => $courseTool]);
        $method = '';

        return view('admin.tools.create-edit', compact('courseTool', 'action', 'method'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        CourseTool::create($request->only('course_id', 'tool_name'));

        return redirect()
            ->route('tools.index')
            ->with('alert-success', 'Rule succesfully added');
    }

    /**
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $courseTool = CourseTool::find($id);
        $action = route('tools.update', ['courseTool' => $courseTool]);
        $method = method_field('PUT');

        return view('admin.tools.create-edit', compact('courseTool', 'action', 'method'));
    }

    /**
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request)
    {
        $courseTool = CourseTool::find($id);
        $courseTool->fill($request->only('course_id', 'tool_name'));
        $courseTool->save();

        return redirect()->route('tools.index',  [ 'course' => $courseTool ])
            ->with('alert-success', 'Rule succesfully modified');
    }

    /**
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $courseTool = CourseTool::find($id);
        $courseTool->delete();

        return redirect()->route('tools.index')
            ->with('alert-success', 'Rule succesfully deleted');
    }

    public function launchpad()
    {
        $handler = new SourceUrlHandler;
        $source = Source::where('access_word', 'access-launchpad')->first();
        $tool = 'Launchpad';

        if ($source) {
            $client = app()->make(Client::class);
            $response = $client->get($handler->getLoginUrl(user(), $source));
            //\Illuminate\Support\Facades\Log::info($response->getStatusCode());
        }

        return view('admin.tools.launchpad.index', compact('tool'));
    }
}
