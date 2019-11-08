@extends('admin.layout')

@section('pagetitle')
    Viewing User
@endsection

@section('breadcrumb')
    <li>
        <i class="icon-bubble"></i>
        <a href="{{ route('feedback.index') }}">Feedback</a>
    </li>
    <li>
        <i class="icon-eye"></i>
        <a href="{{ route('feedback.show', $feedback->id) }}">Viewing {{ $feedback->id }}</a>
    </li>
@endsection

@section('content')
    <table class="table">
        <thead>
            <tr>
                <th>User</th>
                <th>Share Likeability (1-10)</th>
                <th>Feedback</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <a href="{{ route('users.show', $feedback->user->id) }}">
                        {{ $feedback->user->name }}
                    </a>
                </td>
                <td>{{ $feedback->grade }}</td>
                <td>{!! str_replace("\n", '<br>', htmlentities($feedback->feedback, ENT_QUOTES)) !!}</td>
            </tr>
        </tbody>
    </table>
@endsection
