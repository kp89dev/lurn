<?php

namespace App\Http\Controllers\Admin;

use App\Models\UserRole;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RolesController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin.role.auth:user-roles,read')->only('index');
        $this->middleware('admin.role.auth:user-roles,write')->only('create', 'store', 'edit', 'update', 'destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = UserRole::orderBy('id', 'desc')->paginate(20);

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $role = new UserRole();
        $action = route('roles.store');
        $method = '';
        $availablePermissions = UserRole::$availablePermissions;

        return view('admin.roles.create-edit', compact('role', 'method', 'action', 'availablePermissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->only('title', 'permissions');

        UserRole::create($data);

        return redirect()->route('roles.index')->with('alert-success', 'Role successfully added');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  UserRole  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(UserRole $role)
    {
        $action = route('roles.update', $role->id);
        $method = method_field('PUT');
        $availablePermissions = UserRole::$availablePermissions;

        return view('admin.roles.create-edit', compact('action', 'method', 'availablePermissions'))->withRole($role);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  UserRole  $role
     * @return \Illuminate\Http\Response
     */
    public function update(UserRole $role, Request $request)
    {
        $role->update($request->only('title', 'permissions'));

        return redirect()->route('roles.index')->with('alert-success', 'Role successfully updated');
    }
}
