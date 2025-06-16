<div class="container-fluid bg-light border-top py-2 mt-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center small px-3">
        <div class="d-flex align-items-center">
            <img src="{{ asset('favicon.ico') }}" alt="Favicon" width="24" height="24" class="me-2">
            <span class="text-muted">TÜVAT BD Certificate Verification System v3.0.1</span>
        </div>

        @auth
        <div class="text-muted">
            Developed in-house by 
            <a href="mailto:swad.mahfuz@gmail.com" class="text-decoration-none">Swad Ahmed Mahfuz</a> &copy; {{ date('Y') }}
        </div>
        @endauth
    </div>
</div>
