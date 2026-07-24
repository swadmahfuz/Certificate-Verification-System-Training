@props(['status'])

@php
    $normalized = strtolower(trim($status ?? 'unknown'));
    $class = in_array($normalized, ['approved']) ? 'success'
        : (in_array($normalized, ['pending review', 'pending']) ? 'warning'
        : (in_array($normalized, ['pending approval', 'reviewed']) ? 'purple'
        : (in_array($normalized, ['deleted', 'expired']) ? 'danger' : 'secondary')));
@endphp

<span class="status-pill status-{{ $class }}">{{ $status ?: 'Unknown' }}</span>
