@extends('admin.layout')

@section('pagetitle')
    Feedback
    <small>Feedback messages</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="icon-bubble"></i>
        <a href="{{ route('feedback.index') }}">Feedback</a>
    </li>
@endsection

@section('content')
    <div>
        <a href="{{ route('feedback.download-csv') }}" class="btn btn-sm blue pull-right">
            <i class="fa fa-file-excel-o"></i>
            &nbsp;
            Download CSV
        </a>
        <div class="clearfix"></div>
    </div>
    <table class="table table-striped table-bordered table-hover dataTable no-footer" style="margin-top: 20px;">
        <thead>
            <tr role="row" class="heading">
                <th>ID</th>
                <th>User</th>
                <th>Share Likeability (1-10)</th>
                <th>Feedback</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        @foreach($feedbacks as $feedback)
            <tr role="row" class="filter">
                <td>{{ $feedback->id }}</td>
                <td>
                    <a href="{{ route('users.show', $feedback->user->id) }}">
                        {{ $feedback->user->name }}
                    </a>
                </td>
                <td>{{ $feedback->grade }}</td>
                <td>{!! str_replace("\n", '<br>', htmlentities($feedback->feedback, ENT_QUOTES)) !!}</td>
                <td>{{ $feedback->created_at }}</td>
                <td>
                    <a href="{{ route('feedback.show', $feedback->id) }}" class="btn btn-sm blue table-group-action-submit">
                        <i class="fa fa-eye"></i>
                    </a>
                    <form action="{{ route('feedback.destroy', $feedback->id) }}" method="POST" style="display: inline">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <button class="btn btn-sm red table-group-action-submit"
                                onclick="return confirm('Are you sure you want to delete this Feedback? This action cannot be undone.')">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
    <div class="row">
        {{ $feedbacks->links() }}
    </div>
@endsection
