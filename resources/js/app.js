import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    const shell = document.querySelector('[data-dashboard-shell]');
    const sidebar = document.querySelector('[data-dashboard-sidebar]');
    const backdrop = document.querySelector('[data-dashboard-backdrop]');
    const sidebarToggles = document.querySelectorAll('[data-sidebar-toggle]');
    const userMenu = document.querySelector('[data-user-menu]');
    const userMenuTrigger = document.querySelector('[data-user-menu-trigger]');
    const userMenuDropdown = document.querySelector('[data-user-menu-dropdown]');

    if (shell && sidebar && backdrop && sidebarToggles.length) {
        const DESKTOP_BREAKPOINT = 1024;
        const isDesktop = () => window.innerWidth >= DESKTOP_BREAKPOINT;
        const COLLAPSE_STORAGE_KEY = 'dashboardSidebarCollapsed';

        // Mobile off-canvas: sidebar slides in/out over the content.
        const setSidebarOpen = (open) => {
            sidebar.classList.toggle('is-open', open);
            backdrop.classList.toggle('is-visible', open);
            document.body.classList.toggle('overflow-hidden', open);
        };

        // Desktop collapse/expand: sidebar shrinks and content stretches into the freed space.
        const setSidebarCollapsed = (collapsed) => {
            shell.classList.toggle('is-sidebar-collapsed', collapsed);
            try {
                localStorage.setItem(COLLAPSE_STORAGE_KEY, collapsed ? '1' : '0');
            } catch (e) {
                // localStorage unavailable (private mode, etc.) — collapse state simply won't persist.
            }
        };

        if (isDesktop()) {
            let storedCollapsed = null;
            try {
                storedCollapsed = localStorage.getItem(COLLAPSE_STORAGE_KEY);
            } catch (e) {
                storedCollapsed = null;
            }
            if (storedCollapsed === '1') {
                setSidebarCollapsed(true);
            }
        }

        sidebarToggles.forEach((toggle) => {
            toggle.addEventListener('click', () => {
                if (isDesktop()) {
                    setSidebarCollapsed(!shell.classList.contains('is-sidebar-collapsed'));
                } else {
                    setSidebarOpen(!sidebar.classList.contains('is-open'));
                }
            });
        });

        backdrop.addEventListener('click', () => setSidebarOpen(false));

        window.addEventListener('resize', () => {
            if (isDesktop()) {
                setSidebarOpen(false);
            } else {
                shell.classList.remove('is-sidebar-collapsed');
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
