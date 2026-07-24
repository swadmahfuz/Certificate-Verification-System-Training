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

    document.querySelectorAll('[data-confirm]').forEach(function (element) {
        element.addEventListener('click', function (event) {
            if (!window.confirm(element.getAttribute('data-confirm'))) {
                event.preventDefault();
            }
        });
    });
}());
