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

    document.addEventListener('click', function (event) {
        var element = event.target.closest('[data-confirm]');
        if (!element) return;
        if (!window.confirm(element.getAttribute('data-confirm'))) {
            event.preventDefault();
        }
    });

    function hidePermissionPopup() {
        var modal = document.getElementById('permissionModal');
        if (!modal) {
            return;
        }

        modal.hidden = true;
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('permission-modal-open');
    }

    window.showPermissionPopup = function (message) {
        if (!message) {
            return;
        }

        var modal = document.getElementById('permissionModal');
        var messageEl = document.getElementById('permissionModalMessage');

        if (!modal || !messageEl) {
            window.alert(message);
            return;
        }

        messageEl.textContent = message;
        modal.hidden = false;
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('permission-modal-open');

        var okButton = document.getElementById('permissionModalOk');
        if (okButton) {
            okButton.focus();
        }
    };

    window.hidePermissionPopup = hidePermissionPopup;

    document.addEventListener('click', function (event) {
        var okButton = event.target.closest('#permissionModalOk');
        if (okButton) {
            event.preventDefault();
            hidePermissionPopup();
            return;
        }

        if (event.target.classList.contains('permission-modal-backdrop')) {
            hidePermissionPopup();
            return;
        }

        var element = event.target.closest('[data-permission-message]');
        if (!element) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();
        window.showPermissionPopup(element.getAttribute('data-permission-message'));
    });

    document.addEventListener('keydown', function (event) {
        var modal = document.getElementById('permissionModal');
        if (modal && !modal.hidden && event.key === 'Escape') {
            hidePermissionPopup();
            return;
        }

        if (event.key !== 'Enter' && event.key !== ' ') {
            return;
        }

        var element = event.target.closest('[data-permission-message][role="button"]');
        if (!element) {
            return;
        }

        event.preventDefault();
        window.showPermissionPopup(element.getAttribute('data-permission-message'));
    });
}());
