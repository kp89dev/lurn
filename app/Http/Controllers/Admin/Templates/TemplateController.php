<?php
namespace App\Http\Controllers\Admin\Templates;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTemplateRequest;
use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    /**
     * @return View
     */
    public function index()
    {
        $templates = Template::orderBy('id', 'DESC')->simplePaginate(20);

        return view('admin.templates.index', compact('templates'));
    }

    /**
     * @return View
     */
    public function create()
    {
        $template = new Template;
        $action = route('templates.store');
        $method = '';

        return view('admin.templates.create-edit', compact('template', 'action', 'method'));
    }

    /**
     * @param StoreTemplateRequest $request
     * @return RedirectResponse
     */
    public function store(StoreTemplateRequest $request)
    {
       $data = $request->only('title', 'content', 'subject');

        Template::create($data);

        return redirect()->route('templates.index')->with('alert-success', 'Template succesfully added');
    }

    /**
     * @param Template $template
     * @return View
     */
    public function edit(Template $template)
    {
        $action = route('templates.update', $template->id);
        $method = method_field('PUT');

        return view('admin.templates.create-edit', compact('template', 'action', 'method'));
    }

    /**
     * @param Template             $template
     * @param StoreTemplateRequest $request
     * @return RedirectResponse
     */
    public function update(Template $template, StoreTemplateRequest $request)
    {
        $template->fill($request->only('title', 'content', 'subject'));
        $template->save();

        return redirect()->route('templates.index')->with('alert-success', 'Template succesfully modified');
    }

    /**
     * @param Template $template
     * @return RedirectResponse
     */
    public function destroy(Template $template)
    {
        $template->delete();

        request()->session()->flash('alert-success', 'Templates were successfully deleted!');

        return redirect()->back();
    }

    /**
     * @param  Request $request
     * @return View
     */
    public function preview(Request $request)
    {
        $content = $request->content;

        return view('admin.templates.var.responsive', compact('content'));
    }
}
