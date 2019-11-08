<ul class="breadcrumbs">
    @foreach ($items as $item)
        <li>
            @isset ($item['url'])
                <a href="{{ $item['url'] }}">
                    {{ $item['title'] }}
                </a>
            @else
                {{ $item['title'] }}
            @endisset
        </li>
    @endforeach
</ul>
