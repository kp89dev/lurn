@extends('admin.layout')

@section('pagetitle')
    FAQ
    <small>frequently asked questions</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="icon-question"></i>
        <a href="{{ route('faq.index') }}">FAQ</a>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 col-sm-12"></div>
        <div class="col-md-4 col-sm-12">
            <div class="table-group-actions pull-right">
                <span></span>
                <a href='{{ route('faq.create') }}' class="btn btn-sm green table-group-action-submit">
                    <i class="fa fa-plus"></i> Add New Question
                </a>
            </div>
        </div>
    </div>
    <table class="table table-striped table-bordered table-hover dataTable no-footer">
        <thead>
            <tr role="row" class="heading">
                <th>ID</th>
                <th>Question</th>
                <th>Answer</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
        </thead>
        @foreach($faqs as $faq)
            <tr role="row" class="filter">
                <td>{{ $faq->id }}</td>
                <td>{{ $faq->question }}</td>
                <td>{!! $faq->answer !!}</td>
                <td>{{ $faq->created_at }}</td>
                <td>{{ $faq->updated_at }}</td>
                <td>
                    <a href="{{ route('faq.edit', $faq->id) }}" class="btn btn-sm green table-group-action-submit">
                        <i class="fa fa-edit"></i>
                    </a>
                    <form action="{{ route('faq.destroy', $faq->id) }}" method="POST" style="display: inline">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <button class="btn btn-sm red table-group-action-submit"
                                onclick="return confirm('Are you sure you want to delete this Question and Answer? This action cannot be undone.')">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
    <div class="row">
        {{ $faqs->links() }}
    </div>
@endsection
