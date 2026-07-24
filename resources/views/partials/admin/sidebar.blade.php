<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('images/TUV Austria Logo -  White Background.jpg') }}" alt="TÜV Austria">
        <div>
            <strong>TÜV Austria BIC</strong>
            <small>Training CVS</small>
        </div>
        <button class="sidebar-close d-lg-none" type="button" data-sidebar-close aria-label="Close menu">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <nav class="sidebar-nav" aria-label="Administration">
        <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="fa-solid fa-gauge-high"></i><span>Dashboard</span>
        </a>

        <div class="sidebar-label">Certificates</div>
        <a class="sidebar-link {{ request()->routeIs('certificates.index') ? 'active' : '' }}" href="{{ route('certificates.index') }}">
            <i class="fa-regular fa-file-lines"></i><span>All Certificates</span>
        </a>
        <a class="sidebar-link {{ request()->routeIs('certificate.createForm') ? 'active' : '' }}" href="{{ route('certificate.createForm') }}">
            <i class="fa-solid fa-plus"></i><span>Add Certificate</span>
        </a>
        <a class="sidebar-link {{ request()->routeIs('pendingCertificates') ? 'active' : '' }}" href="{{ route('pendingCertificates') }}">
            <i class="fa-regular fa-clock"></i>
            <span>Pending Certificates</span>
            @if(($myAssignments['total'] ?? 0) > 0)
                <span class="nav-count">{{ $myAssignments['total'] }}</span>
            @endif
        </a>
        <a class="sidebar-link {{ request()->routeIs('deletedCertificates') ? 'active' : '' }}" href="{{ route('deletedCertificates') }}">
            <i class="fa-regular fa-trash-can"></i><span>Deleted Certificates</span>
        </a>

        <div class="sidebar-label">People & Data</div>
        <a class="sidebar-link {{ request()->routeIs('trainers.*') ? 'active' : '' }}" href="{{ route('trainers.index') }}">
            <i class="fa-solid fa-person-chalkboard"></i><span>Trainers</span>
        </a>
        <a class="sidebar-link {{ request()->routeIs('signatories.*') ? 'active' : '' }}" href="{{ route('signatories.index') }}">
            <i class="fa-solid fa-signature"></i><span>Signatories</span>
        </a>
        <a class="sidebar-link {{ request()->routeIs('allUsers') ? 'active' : '' }}" href="{{ route('allUsers') }}">
            <i class="fa-solid fa-users"></i><span>Users</span>
        </a>
        <a class="sidebar-link {{ request()->routeIs('importsExports') ? 'active' : '' }}" href="{{ route('importsExports') }}">
            <i class="fa-solid fa-file-import"></i><span>Import / Export</span>
        </a>
        <a class="sidebar-link {{ request()->routeIs('activity-log.*') ? 'active' : '' }}" href="{{ route('activity-log.index') }}">
            <i class="fa-solid fa-clock-rotate-left"></i><span>Activity Log</span>
        </a>

        @php
            // Derive registrable base domain so portal links stay on *.tuvbd.com
            // even when the user is on www.training.tuvbd.com (not training.tuvbd.com).
            $host = strtolower(request()->getHost());
            if (strpos($host, 'www.') === 0) {
                $host = substr($host, 4);
            }

            $parts = explode('.', $host);
            $count = count($parts);
            $baseDomain = $host;

            if ($count > 2) {
                $tld = $parts[$count - 1];
                $sld = $parts[$count - 2];
                $twoLevelSet = ['co', 'com', 'org', 'net', 'gov', 'edu', 'ac'];

                if (strlen($tld) === 2 && in_array($sld, $twoLevelSet, true)) {
                    $baseDomain = implode('.', array_slice($parts, -3));
                } else {
                    $baseDomain = implode('.', array_slice($parts, -2));
                }
            }
        @endphp
        <div class="sidebar-label">CVS Portals</div>
        <a class="sidebar-link" href="https://training.{{ $baseDomain }}/dashboard" target="_blank">
            <i class="fa-solid fa-graduation-cap"></i><span>Training CVS Portal</span>
        </a>
        <a class="sidebar-link" href="https://inspection.{{ $baseDomain }}/dashboard" target="_blank">
            <i class="fa-solid fa-magnifying-glass"></i><span>Inspection CVS Portal</span>
        </a>
        <a class="sidebar-link" href="https://calibration.{{ $baseDomain }}/dashboard" target="_blank">
            <i class="fa-solid fa-wrench"></i><span>Calibration CVS Portal</span>
        </a>
        <a class="sidebar-link" href="https://reports.{{ $baseDomain }}/dashboard" target="_blank">
            <i class="fa-regular fa-file-lines"></i><span>Reports CVS Portal</span>
        </a>
        <a class="sidebar-link" href="https://certification.{{ $baseDomain }}/dashboard" target="_blank">
            <i class="fa-solid fa-certificate"></i><span>BA Certification Portal</span>
        </a>

        <div class="sidebar-label">Other</div>
        <a class="sidebar-link" href="{{ route('certificate.search') }}" target="_blank">
            <i class="fa-solid fa-magnifying-glass"></i><span>Verify Certificate</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <small>Certificate Verification System</small>
        <span>Version 5.0.1</span>
    </div>
</aside>
