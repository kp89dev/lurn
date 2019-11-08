@extends('admin.layout')

@section('pagetitle')
    Categories
    <small>courses categories</small>
@endsection

@section('breadcrumb')
    <li>
        <i class="icon-tag"></i>
        <a href="{{ route('categories.index') }}">Categories</a>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 col-sm-12"></div>
        <div class="col-md-4 col-sm-12">
            <div class="table-group-actions pull-right">
                <span></span>
                <a href='{{ route('categories.create') }}' class="btn btn-sm green table-group-action-submit">
                    <i class="fa fa-plus"></i> Add New Category
                </a>
            </div>
        </div>
    </div>
    <table class="table table-striped table-bordered table-hover dataTable no-footer">
        <thead>
            <tr role="row" class="heading">
                <th>ID</th>
                <th>Thumbnail</th>
                <th>Name</th>
                <th>Courses</th>
                <th>Bonuses</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
        </thead>
        @foreach($categories as $category)
            <tr role="row" class="filter">
                <td>{{ $category->id }}</td>
                <td width="120"><img src="{{ $category->getPrintableImageUrl() }}" width="120" style="margin: -8px"></td>
                <td>{{ $category->name }}</td>
                <td> <a href="{{ route('categories.index', 'category='.$category->id) }}">{{ $category->courseCount }}</a> </td>
                <td>{{ $category->bonusCount }}</td>
                <td>{{ $category->created_at }}</td>
                <td>{{ $category->updated_at }}</td>
                <td>
                    <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-sm green table-group-action-submit">
                        <i class="fa fa-edit"></i>
                    </a>
                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" style="display: inline">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <button class="btn btn-sm red table-group-action-submit"
                                onclick="return confirm('Are you sure you want to delete this Category? This action cannot be undone.')">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
    <div class="row">
        {{ $categories->links() }}
    </div>
@endsection
