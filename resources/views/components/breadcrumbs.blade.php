<nav class="container pt-4 pb-1 pb-sm-2" aria-label="breadcrumb">
    <ol class="breadcrumb">
        @foreach($items as $i => $item)
            @if(isset($item['url']) && $i < count($items) - 1)
                <li class="breadcrumb-item">
                    <a href="{{ $item['url'] }}">{{ $item['title'] }}</a>
                </li>
            @else
                <li class="breadcrumb-item active" aria-current="page">{{ $item['title'] }}</li>
            @endif
        @endforeach
    </ol>
</nav>
