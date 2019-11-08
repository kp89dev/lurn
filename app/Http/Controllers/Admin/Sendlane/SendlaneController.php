<?php
namespace App\Http\Controllers\Admin\Sendlane;

use App\Http\Controllers\Controller;
use App\Models\Sendlane;
use Illuminate\Http\Request;

class SendlaneController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin.role.auth:sendlane,read')
            ->only('index');
        $this->middleware('admin.role.auth:sendlane,write')
            ->only('create', 'store', 'edit', 'update', 'destroy');
    }

    public function index()
    {
        $accounts = Sendlane::all();

        return view('admin.sendlane.index', compact('accounts'));
    }

    public function create()
    {
        $sendlane = new Sendlane();
        $action = route('sendlane.store');
        $method = '';

        return view('admin.sendlane.create-edit', compact('sendlane', 'action', 'method'));
    }

    public function store(Request $request)
    {
        Sendlane::create($request->only('email', 'subdomain', 'api', 'hash'));

        return redirect()->route('courses.index')->with('alert-success', 'Sendlane account succesfully added');
    }

    public function edit( Sendlane $sendlane)
    {
        $action = route('sendlane.update', compact('sendlane'));
        $method = method_field('PUT');

        return view('admin.sendlane.create-edit', compact('sendlane', 'action', 'method'));
    }

    public function update(Request $request, Sendlane $sendlane)
    {
        $sendlane->fill($request->only('email', 'subdomain', 'api', 'hash'));
        $sendlane->save();

        return redirect()->route('courses.index')->with('alert-success', 'Sendlane Account succesfully modified');
    }
}
