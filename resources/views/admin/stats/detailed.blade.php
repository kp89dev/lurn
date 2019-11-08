@extends('admin.layout')

@section('pagetitle')
    Stats
    <small>Total stats</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="fa fa-user"></i>
        <a href="{{ route('stats.index') }}">Stats</a>
    </li>
@endsection

@section('content')
    <table class="table table-striped table-bordered table-hover dataTable no-footer">
        <thead>
        <tr role="row" class="heading">
            <th>User</th>
            <th>Country</th>
            <th>Campaign name</th>
            <th>Campaign source</th>
            <th>Campaign medium</th>
            <th>Campaign content</th>
            <th>Date</th>
            <th>Revenue</th>
        </tr>
        </thead>
        @foreach ($newUsers as $user)
            <tr role="row" class="filter">
                <td>
                    <a href="{{ route('users.show', $user->id) }}">
                        {{ $user->name }}
                    </a>
                </td>
                <td>{{ $user->getCountry() }}</td>
                <td>{{ $user->getTrackerCampaignDetails() ? $user->getTrackerCampaignDetails()->name : '' }}</td>
                <td>{{ $user->getTrackerCampaignDetails() ? $user->getTrackerCampaignDetails()->source : '' }}</td>
                <td>{{ $user->getTrackerCampaignDetails() ? $user->getTrackerCampaignDetails()->medium  : '' }}</td>
                <td>{{ $user->getTrackerCampaignDetails() ? $user->getTrackerCampaignDetails()->content : '' }}</td>
                <td style="width:20%;" class="text-center">{{ $user->created_at }}</td>
                <td style="width:20%;" class="text-center">${{ number_format($user->getTotalSpendings(), 2) }}</td>
            </tr>
        @endforeach
    </table>
@endsection

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/2.8.0/css/flag-icon.min.css" rel="stylesheet" type="text/css">
@endsection
