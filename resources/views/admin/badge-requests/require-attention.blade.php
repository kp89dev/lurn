@extends('admin.layout')

@section('pagetitle')
    Badge Requests
@endsection

@section('breadcrumb')
    <li>
        <i class="icon-book-open"></i>
        <a href="{{  route('badge.requests.new') }}">Badge Requests</a>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 col-sm-12"></div>
        <div class="col-md-4 col-sm-12">
            <div class="table-group-actions pull-right">
                <span></span>
                <a href='{{ route('badge.requests.old') }}' class="btn btn-sm green table-group-action-submit">
                    View Old Requests
                </a>
            </div>
        </div>
    </div>
    <table class="table table-striped table-bordered table-hover dataTable no-footer">
        <thead>
        <tr role="row" class="heading">
            <th>Image</th>
            <th>Title</th>
            <th>Course</th>
            <th>User</th>
            <th>Comment</th>
            <th>Files</th>
            <th class="text-center">Created At</th>
            <th class="text-center">Actions</th>
        </tr>
        </thead>
        @forelse($badgeReq as $req)
            <tr role="row" class="filter">
                <td><img src="{{ $req->badge->getSrcAttribute() }}" width="100"></td>
                <td>{{ $req->badge->title }}</td>
                <td rowspan="1" colspan="1">{{ $req->badge->course->title }}</td>
                <td rowspan="1" colspan="1">
                    <a href="{{ route('users.show', ['user' => $req->user->id]) }}">
                        {{ $req->user->name }}
                    </a>
                </td>
                <td>{{ $req->comment }}</td>
                <td>
                    @foreach($req->files as $file)
                        <a href="{{ route('file.download') . "?file=" . $file->file_path }}" target="_blank">
                            Attachment-{{$loop->index}} <br/>
                        </a>
                    @endforeach
                </td>
                <td class="text-center">
                    <span class="todo-tasklist-date"> {{ $req->created_at }} </span><br/>
                </td>
                <td class="text-center">
                    <form action="{{ route('badge.requests.reject', ['badgeRequest' => $req]) }}" style="display:inline" method="post">
                        {{ csrf_field() }}
                        <input type="submit" value="Reject" class="btn btn-circle red btn-sm">
                    </form>
                    <form action="{{ route('badge.requests.approve', ['badgeRequest' => $req]) }}" style="display:inline" method="post">
                        {{ csrf_field() }}
                        <input type="submit" value="Approve" class="btn btn-circle green-haze btn-sm">
                    </form>
                </td>
            </tr>
        @empty
            <tr role="row">
                <td colspan="7">No New Requests</td>
            </tr>
        @endforelse
    </table>
    <div class="row">
        {{ $badgeReq->links() }}
    </div>
@endsection
