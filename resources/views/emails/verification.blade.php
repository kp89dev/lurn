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

    {{-- Subcopy --}}
    @if (isset($subcopy))
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endif

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        @endcomponent
    @endslot
@endcomponent
