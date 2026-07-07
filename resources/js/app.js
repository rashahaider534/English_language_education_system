import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    const shell = document.querySelector('[data-dashboard-shell]');
    const sidebar = document.querySelector('[data-dashboard-sidebar]');
    const backdrop = document.querySelector('[data-dashboard-backdrop]');
    const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
    const userMenu = document.querySelector('[data-user-menu]');
    const userMenuTrigger = document.querySelector('[data-user-menu-trigger]');
    const userMenuDropdown = document.querySelector('[data-user-menu-dropdown]');

    if (shell && sidebar && backdrop && sidebarToggle) {
        const setSidebarState = (open) => {
            sidebar.classList.toggle('is-open', open);
            backdrop.classList.toggle('is-visible', open);
            document.body.classList.toggle('overflow-hidden', open);
        };

        sidebarToggle.addEventListener('click', () => {
            const shouldOpen = !sidebar.classList.contains('is-open');
            setSidebarState(shouldOpen);
        });

        backdrop.addEventListener('click', () => setSidebarState(false));

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                setSidebarState(false);
            }
        });
    }

    if (userMenu && userMenuTrigger && userMenuDropdown) {
        userMenuTrigger.addEventListener('click', () => {
            const isOpen = userMenuDropdown.classList.toggle('is-open');
            userMenuTrigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        document.addEventListener('click', (event) => {
            if (!userMenu.contains(event.target)) {
                userMenuDropdown.classList.remove('is-open');
                userMenuTrigger.setAttribute('aria-expanded', 'false');
            }
        });
    }
});
