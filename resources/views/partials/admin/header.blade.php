<header class="admin-header">
    <div class="header-left">
        <button class="header-menu" type="button" data-sidebar-open aria-label="Open menu">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div>
            <div class="header-product">Training Certificate Verification System (CVS)</div>
            <div class="header-organization">TÜV Austria Bureau of Inspection &amp; Certification</div>
        </div>
    </div>

    <div class="header-right">
        <div class="header-time d-none d-md-flex">
            <i class="fa-regular fa-clock"></i>
            <span>{{ now()->format('d M Y, h:i A') }}</span>
        </div>
        <div class="dropdown">
            <button class="user-menu dropdown-toggle" data-bs-toggle="dropdown" type="button">
                <span class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                <span class="d-none d-sm-block text-start">
                    <strong>{{ auth()->user()->name }}</strong>
                    <small>{{ auth()->user()->designation ?? 'User' }}</small>
                </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><span class="dropdown-item-text small text-muted">{{ auth()->user()->email }}</span></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="dropdown-item text-danger" type="submit">
                            <i class="fa-solid fa-right-from-bracket me-2"></i>Log out
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
