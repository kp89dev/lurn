@extends('admin.layout')

@section('pagetitle', 'Workflows')

@section('breadcrumb')
    <li>
        <i class="fa fa-code-fork"></i>
        <a href="{{ route('workflows.index', compact('course')) }}">Workflows</a>
    </li>
    <li>
        <i class="icon-edit"></i>
        @if ($workflow->name)
            <a href="{{ route('workflows.edit', compact('workflow')) }}">Edit</a>
        @else
            <a href="{{ route('workflows.create', compact('workflow')) }}">Add New</a>
        @endif
    </li>
@endsection

@section('content')
    <link rel="stylesheet" href="{{ mix('css/workflows.css') }}">

    <form action="POST" method="post" class="form-horizontal form-bordered" enctype="multipart/form-data" id="workflows" >
        <div class="btn-toolbar margin-bottom-10">
            <div class="btn-group" data-toggle="buttons">
                <a href="" class="btn default btn-sm" :class="{ blue: activeSettings }" @click="setSettings()">Settings</a>
            </div>
            <div class="btn-group" data-toggle="buttons">
                <a href="" class="btn default btn-sm" :class="{ blue: activeGoal }" @click="setGoalAsActiveNode()">@Goal</a>
            </div>
            <div class="btn-group pull-right">
                <a href="" class="btn green btn-sm" @click="saveWorkflow();" onclick="return false;" :class='{ disabled: savingWorkflow }'>Save Workflow</a>
            </div>
            <div v-if="! savingWorkflow && workflowError" class="btn-group pull-right" style="line-height: 30px">
                <span class="note note-danger"><strong>Failed!</strong> @{{ workflowError }}</span>
            </div>
            <div v-if="savingWorkflow" class="btn-group pull-right" style="line-height: 30px">
                <span class="fa fa-spinner fa-spin"></span> <span class="has-success">Saving...</span>
            </div>
            <div v-if="! savingWorkflow && workflowSuccess" class="btn-group pull-right" style="line-height: 30px">
                <span class="note note-success"><strong>Saved!</strong></span>
            </div>
        </div>

        <div class="workflow-builder">
            <table>
                <tr>
                    <td class="content">
                        <graph></graph>
                    </td>
                    <td class="sidebar" v-if="! activeSettings">
                        <sidebar></sidebar>
                    </td>
                    <td class="sidebar" v-if="activeSettings">
                        <settings></settings>
                    </td>
                </tr>
            </table>
        </div>
    </form>
@endsection

@section('js')
    <!--<script src="//cdnjs.cloudflare.com/ajax/libs/vue/2.3.4/vue.min.js"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.2/vue.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vuex/3.0.0/vuex.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue-resource/1.3.4/vue-resource.min.js"></script>

    <script>
        window._token = "{{ csrf_token() }}";

        window.configuration = {
            nodeTypes: [
                { key: 'ifelse', title: 'If/Then', default: { type: 'ifelse', key: 'Ifelse' , conditions: [], nodes_false: [], nodes_true: []}},
                { key: 'delay', title: 'Delay', default: { type: 'delay', key: 'Delay', value: { delayUnit: null, delay: null } }},
                { key: 'action', title: 'Action',default: { type: 'action', value: null }}
            ],
            conditions: {!! $conditions !!},
            actions: {!! $actions !!}
        };

        window.workflow = {!! json_encode($workflow->workflow) !!};
        window.goal = {!! json_encode($workflow->goal) !!};

        window.workflow_details = {
            id: '{{ $workflow->id }}',
            name: '{{ $workflow->name }}'
        };
    </script>
    <script src="{{ mix('js/workflow.js') }}"></script>
@endsection
