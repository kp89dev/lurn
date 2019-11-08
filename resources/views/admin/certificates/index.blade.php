@extends('admin.layout')

@section('pagetitle')
    certifications
@endsection

@section('breadcrumb')
    <li>
        <i class="icon-book-open"></i>
        <a href="{{ route('certs.index', compact('course')) }}">certifications</a>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 col-sm-12"></div>
        <div class="col-md-4 col-sm-12">
            <div class="table-group-actions pull-right">
                <span></span>
                <a href='{{ route('certs.create', compact('course')) }}' class="btn btn-sm green table-group-action-submit">
                    <i class="fa fa-plus"></i> Add Certification
                </a>
            </div>
        </div>
    </div>
    <table class="table table-striped table-bordered table-hover dataTable no-footer">
        <thead>
        <tr role="row" class="heading">
            <th>Logo</th>
            <th>Title</th>
            <th class="text-center">Timestamps</th>
            <th>Actions</th>
        </tr>
        </thead>
        @foreach ($certs as $cert)
            <tr role="row" class="filter">
                <td><img src="{{ $cert->getSrc('logo') }}" width="100"></td>
                <td>{{ $cert->title }}</td>
                <td class="text-center">
                    <span class="todo-tasklist-badge badge badge-roundless">Created At</span> <span class="todo-tasklist-date"><i class="fa fa-calendar"></i> {{ $cert->created_at }} </span><br/>
                    <span class="todo-tasklist-badge badge badge-roundless">Updated At</span> <span class="todo-tasklist-date"><i class="fa fa-calendar"></i> {{ $cert->updated_at }} </span>
                </td>
                <td>
                    <a href="{{ route('certs.edit', compact('cert', 'course')) }}" class="btn btn-sm green table-group-action-submit">
                        <i class="fa fa-edit"></i>
                    </a>
                    <form action="{{ route('certs.destroy', compact('cert', 'course')) }}" method="POST" style="display: inline">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <button class="btn btn-sm red table-group-action-submit"
                                onclick="return confirm('Are you sure you want to delete this Certificate? This action cannot be undone.')">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                    <a href="{{route('certs.previewCert',['course'=>$course->id,'cert'=>$cert->id])}}" target="_blank" class="btn default"> Preview </a>
                </td>
            </tr>
        @endforeach
    </table>
    <div class="row text-center">
        {{ $certs->links() }}
    </div>
@endsection
