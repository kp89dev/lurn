@if ($tools = user()->tools)
    <h3>
        <i class="plug icon"></i>
        Course Tools
    </h3>
    <p>All the tools that your course subscriptions provide you access to.</p>

    <table class="ui blue table">
        <thead>
        <tr>
            <th>Course Name</th>
            <th>Tool</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($tools as $tool)
                <tr>
                    <td>{{ $tool->course->title }}</td>
                    <td>
                        <a href="{{ url('tools', $tool->slug) }}" class="ui primary button">
                            <i class="plug icon"></i>
                            {{ $tool->tool_name }}
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif