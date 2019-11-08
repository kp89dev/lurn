@extends('admin.layout')

@section('pagetitle')
Surveys <small>list</small>
@endsection

@section('breadcrumb')
<li>
    <i class="fa fa-code-fork"></i>
    <a href="/admin/surveys">Surveys</a>
</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 col-sm-12"></div>
        <div class="col-md-4 col-sm-12">
            <div class="table-group-actions pull-right">
                <a href='{{ route('surveys.create') }}' class="btn btn-sm green"><i class="fa fa-plus"></i> Add New Survey</a>
            </div>
        </div>
    </div>
    <table class="table table-striped table-bordered table-hover dataTable no-footer">
        <thead>
            <tr role="row" class="heading">
                <th>Title</th>
                <th>Description</th>
                <th>Questions</th>
                <th>Results</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        @foreach ($surveys as $survey)
            <tr role="row" class="filter">
                <td>{{ $survey->title }}</td>
                <td>{{ $survey->description }}</td>
                <td>{{ $survey->questions->count() }}</td>
                <td>{{ $survey->results()->withAnswers()->count() }}</td>
                <td>{{ $survey->typeName }}</td>
                <td>
                    <a href="{{ route('surveys.edit', $survey) }}" title="Edit" class="btn btn-sm green">
                        <i class="fa fa-edit"></i>
                    </a>
                    <a href="{{ route('surveys.show', $survey) }}" title="Results" class="btn btn-sm green">
                        <i class="fa fa-user"></i>
                    </a>
                </td>
            </tr>
        @endforeach
    </table>
@endsection
