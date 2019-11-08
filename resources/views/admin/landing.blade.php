@extends('admin.layout')

@section('pagetitle')
    Lurn Nation<small>Admin</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="fa icon-settings"></i>
        <a href="{{ route('admin') }}">Admin Dashboard</a>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4 col-sm-12"></div>
        <div class="col-md-4 col-sm-12">
                <h3>Dashboard</h3>
        </div>
    </div>

@endsection
