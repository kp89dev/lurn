<?php
namespace App\Http\Controllers\Admin\Upsells;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Course\StoreUpsellRequest;
use App\Models\CourseInfusionsoft;
use App\Models\CourseUpsell;

class UpsellController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin.role.auth:course-upsells,read')->only('index');
        $this->middleware('admin.role.auth:course-upsells,write')->only('create', 'store', 'edit', 'update');
    }

    public function index()
    {
        $upsells = CourseUpsell::simplePaginate(20);

        return view('admin.upsells.index', compact('upsells'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $upsell = new CourseUpsell();
        $action = route('upsells.store');
        $method = '';

        return view('admin.upsells.create-edit', compact('upsell', 'action', 'method'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreUpsellRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpsellRequest $request)
    {
        $courseInfusionsoft = (new CourseInfusionsoft())->create(
            array_merge(
                ['upsell' => 1],
                array_filter($request->only('course_id', 'is_product_id', 'is_account', 'price', 'subscription'))
            )
        );

        (new CourseUpsell())->create(
            array_merge(
                ['course_infusionsoft_id' => $courseInfusionsoft->id],
                $request->only('succeeds_course_id', 'html', 'css', 'status')
            )
        );

        return redirect()->route('upsells.index')->with('alert-success', 'Upsell succesfully added');
    }

    /**
     * @param CourseUpsell $upsell
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(CourseUpsell $upsell)
    {
        $action = route('upsells.update', ['upsell' => $upsell->id]);
        $method = method_field('PUT');

        return view('admin.upsells.create-edit', compact('upsell', 'action', 'method'));
    }

    /**
     * @param StoreUpsellRequest $request
     * @param CourseUpsell       $upsell
     */
    public function update(StoreUpsellRequest $request, CourseUpsell $upsell)
    {
        $upsell->infusionsoft->update(
            array_merge(
                ['upsell' => 1],
                array_filter($request->only('course_id', 'is_product_id', 'is_account', 'price', 'subscription'))
            )
        );

        $upsell->update(
            $request->only('succeeds_course_id', 'html', 'css', 'status')
        );

        return redirect()->route('upsells.edit', $upsell)->with('alert-success', 'Upsell succesfully added');
    }
}
