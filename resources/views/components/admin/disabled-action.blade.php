@props([
    'enabled' => null,
    'permission' => null,
    'href' => '#',
    'message' => null,
    'variant' => 'sidebar',
    'icon' => null,
    'active' => false,
])

@php
    use App\Services\PermissionService;

    $permissions = app(PermissionService::class);

    if ($enabled === null && $permission !== null) {
        $enabled = match ($permission) {
            'super_admin' => auth()->check() && $permissions->isSuperAdmin(),
            'mutate' => auth()->check() && $permissions->canMutate(),
            default => true,
        };
    }

    $enabled = $enabled ?? true;
    $message = $message ?? PermissionService::deniedMessage(
        $permission === 'super_admin' ? 'super_admin' : 'mutate'
    );
@endphp

@if($enabled)
    @if($variant === 'sidebar')
        <a {{ $attributes->class(['sidebar-link', 'active' => $active]) }} href="{{ $href }}">
            @if($icon)<i class="fa-solid {{ $icon }}"></i>@endif
            <span>{{ $slot }}</span>
        </a>
    @else
        <a {{ $attributes->class(['btn']) }} href="{{ $href }}">
            @if($icon)<i class="fa-solid {{ $icon }} me-1"></i>@endif
            {{ $slot }}
        </a>
    @endif
@else
    @if($variant === 'sidebar')
        <span role="button" tabindex="0"
            {{ $attributes->class(['sidebar-link', 'sidebar-link-disabled']) }}
            data-permission-message="{{ $message }}"
            onclick="if(window.showPermissionPopup){window.showPermissionPopup({{ json_encode($message) }});}else{alert({{ json_encode($message) }});} return false;"
            aria-disabled="true"
            title="View only">
            @if($icon)<i class="fa-solid {{ $icon }}"></i>@endif
            <span>{{ $slot }}</span>
        </span>
    @else
        <button type="button"
            {{ $attributes->class(['btn', 'btn-disabled-permission']) }}
            data-permission-message="{{ $message }}"
            onclick="if(window.showPermissionPopup){window.showPermissionPopup({{ json_encode($message) }});}else{alert({{ json_encode($message) }});} return false;"
            aria-disabled="true"
            title="View only">
            @if($icon)<i class="fa-solid {{ $icon }} me-1"></i>@endif
            {{ $slot }}
        </button>
    @endif
@endif
