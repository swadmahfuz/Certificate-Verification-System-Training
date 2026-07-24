@props([
    'label',
    'value',
    'icon' => 'fa-file-lines',
    'color' => 'blue',
    'meta' => null,
])

<article class="stat-card">
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
</article>
