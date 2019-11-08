@component('mail::layout_custom')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            {{ config('app.name') }}
        @endcomponent
    @endslot
    
    {{-- Greeting --}}
    @if (isset($greeting))
        @slot('greeting')
            {{ $greeting }}
        @endslot
    @endif
    
    {{-- Before CTA --}}
    @if (isset($beforeCTA))
        @slot('beforeCTA')
            {{ $beforeCTA }}
        @endslot
    @endif

    {{-- CTA --}}
    @if (isset($action))
        @slot('action')
            @component('mail::button', ['url' => $action['url']])
                {{ $action['text'] }}
            @endcomponent
        @endslot
    @endif
    
    {{-- After CTA --}}
    @if (isset($afterCTA))
        @slot('afterCTA')
            {{ $afterCTA }}
        @endslot
    @endif
    
    {{-- Salutation --}}
    @if (! empty($salutation))
        @slot('salutation')
            {{ $salutation }}
        @endslot
    @else
        @slot('salutation')
            Regards,<br>{{ config('app.name') }}
        @endslot
    @endif

    {{-- Subcopy --}}
    @if (isset($subcopy))
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @else
        @if (isset($action))
            @slot('subcopy')
                @component('mail::subcopy')
                    If you’re having trouble clicking the "{{ $action['text'] }}" button, copy and paste the URL below
                    into your web browser: [{{ $action['url'] }}]({{ $action['url'] }})
                @endcomponent 
            @endslot
        @endif
    @endif

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        @endcomponent
    @endslot
@endcomponent
