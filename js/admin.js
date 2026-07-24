(function () {
    'use strict';

    var sidebar = document.getElementById('adminSidebar');
    var backdrop = document.querySelector('.sidebar-backdrop');

    function openSidebar() {
        if (!sidebar) return;
        sidebar.classList.add('open');
        if (backdrop) backdrop.classList.add('show');
    }

    function closeSidebar() {
        if (!sidebar) return;
        sidebar.classList.remove('open');
        if (backdrop) backdrop.classList.remove('show');
    }

    document.querySelectorAll('[data-sidebar-open]').forEach(function (button) {
        button.addEventListener('click', openSidebar);
    });

    document.querySelectorAll('[data-sidebar-close]').forEach(function (button) {
        button.addEventListener('click', closeSidebar);
    });

    // Delegated so dynamically rendered action buttons also confirm.
    document.addEventListener('click', function (event) {
        var element = event.target.closest('[data-confirm]');
        if (!element) return;
        if (!window.confirm(element.getAttribute('data-confirm'))) {
            event.preventDefault();
        }
    });
}());
