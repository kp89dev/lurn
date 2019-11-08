<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Course\StoreFaqRequest;
use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin.role.auth:faq,read')
            ->only('index');
        $this->middleware('admin.role.auth:faq,write')
            ->only('create', 'store', 'edit', 'update', 'destroy');
    }

    /**
     * @return View
     */
    public function index()
    {
        $faqs = Faq::simplePaginate(20);

        return view('admin.faq.index', compact('faqs'));
    }

    /**
     * @return View
     */
    public function create()
    {
        $faq = new Faq;
        $action = route('faq.store');
        $method = '';

        return view('admin.faq.create-edit', compact('faq', 'action', 'method'));
    }

    /**
     * @param StoreFaqRequest $request
     * @return RedirectResponse
     */
    public function store(StoreFaqRequest $request)
    {
        $data = $request->only('question', 'answer');

        Faq::create($data);

        return redirect()->route('faq.index')->with('alert-success', 'Question & Answer succesfully added');
    }

    /**
     * @param Faq $faq
     * @return View
     */
    public function edit(Faq $faq)
    {
        $action = route('faq.update', $faq->id);
        $method = method_field('PUT');

        return view('admin.faq.create-edit', compact('faq', 'action', 'method'));
    }

    /**
     * @param Faq             $faq
     * @param StoreFaqRequest $request
     * @return RedirectResponse
     */
    public function update(Faq $faq, StoreFaqRequest $request)
    {
        $faq->fill($request->only('question', 'answer'));
        $faq->save();

        return redirect()->route('faq.index')->with('alert-success', 'Question & Answer succesfully modified');
    }

    /**
     * @param Faq $faq
     * @return RedirectResponse
     */
    public function destroy(Faq $faq)
    {
        $faq->delete();

        request()->session()->flash('alert-success', 'Question & Answer were successfully deleted!');

        return redirect()->back();
    }
}
