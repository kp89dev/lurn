@extends('admin.layout')

@section('pagetitle')
    News
@endsection

@section('breadcrumb')
    <li>
        <i class="icon-book-open"></i>
        <a href="{{ route('news.index') }}">News</a>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 col-sm-12"></div>
        <div class="col-md-4 col-sm-12">
            <div class="table-group-actions pull-right">
                <span></span>
                <a href='{{ route('news.create') }}' class="btn btn-sm green table-group-action-submit">
                    <i class="fa fa-plus"></i> Add News
                </a>
            </div>
        </div>
    </div>
    <table class="table table-striped table-bordered table-hover dataTable no-footer">
        <thead>
            <tr role="row" class="heading">
                <th>ID</th>
                <th>Title</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
        </thead>
        @foreach ($news as $article)
            <tr role="row" class="filter">
                <td>{{ $article->id }}</td>
                <td>{{ $article->title }}</td>
                <td>{{ $article->created_at }}</td>
                <td>{{ $article->updated_at }}</td>
                <td>
                    <a href="{{ route('news.edit', $article->id) }}" class="btn btn-sm green table-group-action-submit">
                        <i class="fa fa-edit"></i>
                    </a>
                    <form action="{{ route('news.destroy', $article->id) }}" method="POST" style="display: inline">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <button class="btn btn-sm red table-group-action-submit"
                                onclick="return confirm('Are you sure you want to delete this News? This action cannot be undone.')">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
    <div class="row">
        {{ $news->links() }}
    </div>
@endsection
