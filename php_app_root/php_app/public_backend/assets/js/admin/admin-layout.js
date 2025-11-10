/**
 * Admin Layout - 管理后台布局管理
 *
 * 依赖：无
 *
 * 提供功能：
 * - 侧边栏展开/折叠
 * - 下拉菜单管理
 * - 主题切换（明暗模式）
 * - 响应式布局处理
 */

(function() {
    'use strict';

    // ========== GLOBAL VARIABLES ==========
    let sidebar, toggleBtn, mobileOverlay;

    // ========== INITIALIZATION ==========
    document.addEventListener('DOMContentLoaded', function() {
        initializeCommonElements();
    });

    function initializeCommonElements() {
        sidebar = document.getElementById('sidebar');
        toggleBtn = document.getElementById('toggleSidebar');
        mobileOverlay = document.getElementById('mobileOverlay');

        setupSidebarFunctionality();
        setupDropdowns();
        setupThemeFunctionality();
        setupResponsiveHandlers();

        console.log('Admin Layout 已初始化');
    }

    // ========== SIDEBAR FUNCTIONALITY ==========
    function setupSidebarFunctionality() {
        if (!sidebar || !toggleBtn) return;

        // Fixed toggle function with proper state management
        toggleBtn.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                // Mobile: show full sidebar with overlay
                sidebar.classList.toggle('show');
                mobileOverlay.classList.toggle('active');
            } else {
                // Desktop: collapse/expand sidebar
                sidebar.classList.toggle('collapsed');

                // Debug log to check state
                console.log('Sidebar collapsed state:', sidebar.classList.contains('collapsed'));
                console.log('Sidebar width after toggle:', getComputedStyle(sidebar).width);
            }
        });

        // Close mobile menu when overlay is clicked
        if (mobileOverlay) {
            mobileOverlay.addEventListener('click', () => {
                sidebar.classList.remove('show');
                mobileOverlay.classList.remove('active');
            });
        }
    }

    // ========== DROPDOWN FUNCTIONALITY ==========
    function setupDropdowns() {
        setupDropdown('notificationBtn', 'notificationDropdown');
        setupDropdown('userBtn', 'userDropdown');
        setupDropdown('themeToggleBtn', 'themeDropdown');
    }

    function setupDropdown(triggerId, dropdownId) {
        const trigger = document.getElementById(triggerId);
        const dropdown = document.getElementById(dropdownId);

        if (!trigger || !dropdown) return;

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            // Close other dropdowns
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                if (menu !== dropdown) menu.classList.remove('show');
            });
            dropdown.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!trigger.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });
    }

    // ========== THEME FUNCTIONALITY ==========
    function setupThemeFunctionality() {
        const html = document.documentElement;
        const themeIcon = document.getElementById('themeIcon');
        let currentTheme = localStorage.getItem('theme') || 'light';

        function updateThemeDisplay() {
            const activeTheme = html.getAttribute('data-theme');

            // Update icon to reflect CURRENT active theme
            if (themeIcon) {
                themeIcon.className = activeTheme === 'dark'
                    ? 'bi bi-moon theme-icon'
                    : 'bi bi-sun theme-icon';
            }
        }

        function updateThemeDropdown() {
            // Update dropdown to show currently selected preference
            document.querySelectorAll('.theme-option').forEach(option => {
                option.classList.toggle('active', option.dataset.theme === currentTheme);
            });
        }

        function setTheme(theme) {
            let actualTheme = theme;

            if (theme === 'auto') {
                actualTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }

            html.setAttribute('data-theme', actualTheme);
            localStorage.setItem('theme', theme);
            currentTheme = theme;

            // Update both display and dropdown
            updateThemeDisplay();
            updateThemeDropdown();
        }

        // Theme option clicks
        document.querySelectorAll('.theme-option').forEach(option => {
            option.addEventListener('click', () => {
                setTheme(option.dataset.theme);
                document.getElementById('themeDropdown')?.classList.remove('show');
            });
        });

        // System theme change listener for auto mode
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if (currentTheme === 'auto') {
                setTheme('auto');
            }
        });

        // Initialize theme
        setTheme(currentTheme);

        // Make theme functions globally accessible
        window.setTheme = setTheme;
        window.updateThemeDisplay = updateThemeDisplay;
    }

    // ========== RESPONSIVE HANDLERS ==========
    function setupResponsiveHandlers() {
        // Handle window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768 && sidebar) {
                sidebar.classList.remove('show');
                mobileOverlay?.classList.remove('active');
            }
        });
    }

    // ========== GLOBAL EXPORTS ==========
    if (!window.AdminCommon) {
        window.AdminCommon = {};
    }

    window.AdminCommon.setupDropdown = setupDropdown;

    console.log('Admin Layout 已加载');
})();
