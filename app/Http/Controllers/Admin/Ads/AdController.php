<?php
namespace App\Http\Controllers\Admin\Ads;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Http\Requests\Admin\Ad\StoreAdRequest;
use Illuminate\Http\Request;

class adController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin.role.auth:ads,read')
            ->only('index');
        $this->middleware('admin.role.auth:ads,write')
            ->only('create', 'store', 'edit', 'update', 'destroy');
    }

    /**
     * Show full ad list.
     * @return view
     */
    public function index()
    {
        $ads = Ad::orderBy('id', 'DESC')->simplePaginate(20);

        return view('admin.ads.index', compact('ads'));
    }

    /**
     * Show the form for creating a new resource.
     * 
     * @return view
     */
    public function create()
    {
        $ad = new Ad();
        //expandable location and position 
        $locations = ['dashboard' => 'User Dashboard'];
        $positions = ['first' => 'First', 'second' => 'Second'];
        $action = route('ads.store');
        $method = '';

        return view('admin.ads.create-edit', compact('ad', 'locations', 'positions', 'action', 'method'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreAdRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAdRequest $request)
    {
        $data = $request->only('admin_title', 'link', 'location', 'position', 'status');
        $data['status'] = $data['status'] ?? 0;

        $ad = Ad::create($data);
        $this->storeImages($ad);
        $ad->save();

        return redirect()->route('ads.index')->with('alert-success', 'Ad succesfully created');
    }

    /**
     * @param Ad $ad
     *
     * @return View
     */
    public function edit(Ad $ad)
    {
        $locations = ['dashboard' => 'User Dashboard'];
        $positions = ['first' => 'First', 'second' => 'Second'];
        $action = route('ads.update', ['ad' => $ad]);
        $method = method_field('PUT');

        return view('admin.ads.create-edit', compact('ad', 'locations', 'positions', 'action', 'method'));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Ad $ad)
    {
        $data = $request->only('admin_title', 'link', 'location', 'position', 'status');
        $data['status'] === null and $data['status'] = 0;

        $ad->fill($data);
        $this->storeImages($ad);
        $ad->save();

        return redirect()->route('ads.index')->with('alert-success', 'Ad succesfully modified');
    }

    private function storeImages(Ad $ad)
    {
        $randomString = str_random($n = 15);
        
        if ($image = request()->file('image')) {
            $ext = request()->file('image')->getClientOriginalExtension();
            $imageName = 'primary-image-'.$randomString.'.'.$ext;
            $ad->image = $image->storeAs("ads/$ad->id", $imageName, 'static');
        }

        if ($image = request()->file('hover_image')) {
            $ext = request()->file('hover_image')->getClientOriginalExtension();
            $hoverImageName = 'hover-image-'.$randomString.'.'.$ext;
            $ad->hover_image = $image->storeAs("ads/$ad->id", $hoverImageName, 'static');
        }  
    }
}
