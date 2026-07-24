@props([
    'label',
    'value',
    'icon' => 'fa-file-lines',
    'color' => 'blue',
    'meta' => null,
    'href' => null,
])

@php
    $tag = $href ? 'a' : 'article';
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif {{ $attributes->class(['stat-card', 'stat-card-link' => (bool) $href]) }}>
    <div class="stat-icon stat-{{ $color }}">
        <i class="fa-solid {{ $icon }}"></i>
    </div>
    <div class="stat-copy">
        <span>{{ $label }}</span>
        <strong>{{ number_format($value) }}</strong>
        @if($meta)
            <small>{{ $meta }}</small>
        @endif
    </div>
</{{ $tag }}>
