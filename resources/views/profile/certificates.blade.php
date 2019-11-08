@if (user()->certificates()->count())
    <h3>
        <i class="certificate icon"></i>
        Certificates
    </h3>

    <table class="ui orange table">
        <thead>
            <th colspan="2">
                Course
            </th>
        </thead>
        <tbody>
            @foreach (user()->certificates as $certificate)
                <tr>
                    <td>{{ $certificate->certificate_title }}</td>
                    <td class="right aligned">
                        <form method="post" action="{{ route('user-certificate') }}" target="_blank">
                            {{ csrf_field() }}
                            <input type="hidden" name="cert_id" value="{{ $certificate->id }}"/>

                            <button class="ui positive button">
                                <i class="cloud download alternate icon"></i> Download
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
