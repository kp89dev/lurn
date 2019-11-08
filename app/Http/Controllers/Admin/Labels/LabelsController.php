<?php
namespace App\Http\Controllers\Admin\Labels;

use App\Http\Requests\Admin\Label\StoreLabelRequest;
use App\Models\Labels;
use App\Http\Controllers\Controller;

class LabelsController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin.role.auth:labels,read')
            ->only('index');
        $this->middleware('admin.role.auth:labels,write')
            ->only('create', 'store', 'edit', 'update', 'destroy');
    }

    public function index()
    {
        $labels = Labels::orderBy('created_at', 'DESC')->simplePaginate(20);

        return view('admin.labels.index', compact('labels'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $label = new Labels();
        $action = route('labels.store');
        $method = 'POST';

        return view('admin.labels.create-edit', compact('label', 'action', 'method'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreLabelRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLabelRequest $request)
    {
        Labels::create($request->only('title'));

        return redirect()
                ->route('labels.index')
                ->with('alert-success', 'Label succesfully added');
    }

    /**
     * @param int $eventId
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($labelId)
    {
        $label = Labels::find($labelId);

        $action = route('labels.update', ['label' => $label->id]);
        $method = method_field('PUT');

        return view('admin.labels.create-edit', compact('label', 'action', 'method'));
    }

    /**
     * @param StoreLabelRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(StoreLabelRequest $request)
    {
        $label = Labels::find($request->label);

        $label->fill($request->all());
        $label->save();

        return redirect()->route('labels.index')->with('alert-success', 'Label succesfully modified');
    }

    /* Remove a label from the database
     * 
     * @param int $id
     * @param $request
     */
    public function destroy($id)
    {
        Labels::find($id)->delete();
        request()->session()->flash('alert-success', 'The Label was successfully deleted!');

        return redirect()->back();
    }
}
