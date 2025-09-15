/**
 * Backend Admin Main JavaScript v4 - Common Functions
 * Updated with reusable table operations functionality
 * 标注：基于 main_3.js 更新，新增了可重用的表格操作功能
 */

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
            if (activeTheme === 'dark') {
                themeIcon.className = 'bi bi-moon theme-icon';
            } else {
                themeIcon.className = 'bi bi-sun theme-icon';
            }
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

// ========== Toast FUNCTIONALITY ========== 
// 显示Toast消息
function showToast(message, type = '') {
    // 创建toast容器（如果不存在）
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    // 创建toast元素
    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    let typeClass = ''
    if (type != ''){
        typeClass =  `text-bg-${type}`
    }
    toast.className = `toast align-items-center ${typeClass} border-0`;
    toast.id = toastId;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    // 显示toast
    const bsToast = new bootstrap.Toast(toast, {
        delay: 3000
    });
    bsToast.show();
    
    // 自动移除
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}

// ========== MODAL FUNCTIONALITY ========== 
function showModal(modalId) {
    const modal = new bootstrap.Modal(document.getElementById(modalId));
    modal.show();
}

// ========== NOTIFICATION FUNCTIONALITY ========== 
function setupNotificationBlink() {
    // Simulate real-time updates
    const badge = document.querySelector('.notification-badge');
    if (badge) {
        setInterval(() => {
            badge.style.display = badge.style.display === 'none' ? 'block' : 'none';
        }, 3000);
    }
}

// ========== Character Counter ========== 
// 初始化字符计数器
function initializeCharacterCounters(input_form) {
    const textareas = input_form.querySelectorAll('textarea[maxlength], input[maxlength]');
    textareas.forEach(textarea => {
        updateCharacterCounter(textarea);
        textarea.addEventListener('input', () => {
            updateCharacterCounter(textarea);
        });
    });
}

// 更新字符计数器
function updateCharacterCounter(field) {
    const maxLength = parseInt(field.getAttribute('maxlength'));
    const currentLength = field.value.length;
    const formText = field.parentElement.querySelector('.form-text');
    
    if (formText && maxLength) {
        const percentage = (currentLength / maxLength) * 100;
        const originalText = formText.textContent.split('(')[0];
        
        formText.textContent = `${originalText}(${currentLength}/${maxLength})`;
        
        // 更新样式
        formText.classList.remove('warning', 'danger');
        if (percentage > 90) {
            formText.classList.add('danger');
        } else if (percentage > 75) {
            formText.classList.add('warning');
        }
    }
}

// ========== UTILITY FUNCTIONS ========== 
function formatDate(date) {
    return new Date(date).toLocaleDateString('zh-CN');
}

function formatNumber(num) {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M';
    } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K';
    }
    return num.toString();
}

// ========== FORM VALIDATION UTILITIES ========== 
function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function showFieldError(field, message) {
    clearFieldError(field);
    field.style.borderColor = 'var(--danger)';
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.style.color = 'var(--danger)';
    errorDiv.style.fontSize = '0.75rem';
    errorDiv.style.marginTop = '0.25rem';
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
}

function clearFieldError(field) {
    field.style.borderColor = '';
    const errorDiv = field.parentNode.querySelector('.field-error');
    if (errorDiv) {
        errorDiv.remove();
    }
}

function clearValidation(e) {
    clearFieldError(e.target);
}

function validateField(e) {
    const field = e.target;
    const value = field.value.trim();
    
    if (!value && field.hasAttribute('required')) {
        showFieldError(field, '此字段为必填项');
    } else if (field.type === 'email' && value && !isValidEmail(value)) {
        showFieldError(field, '请输入有效的邮箱地址');
    } else {
        clearFieldError(field);
    }
}

/* ========== COMMON SWITCH FUNCTIONALITY ========== */
/* 标注：新增 - 通用的开关控件功能，适用于标签编辑等页面 */

/**
 * Initialize switches by reading their HTML checkbox attributes
 * This allows server-side templates to set initial states
 */
function initializeSwitches() {
    console.log('Initializing switches by reading HTML checkbox attributes...');
    
    // Find all custom switches on the page
    const switches = document.querySelectorAll('.custom-switch input[type="checkbox"]');
    
    switches.forEach(checkbox => {
        const switchId = checkbox.id;
        if (switchId) {
            // Read the current checked state from the HTML attribute
            const isChecked = checkbox.checked;
            
            console.log(`Switch ${switchId}: HTML checkbox checked = ${isChecked}`);
            
            // Set the visual state based on the checkbox state
            setSwitchVisualState(switchId, isChecked);
        }
    });
    
    console.log('All switches initialized from HTML checkbox attributes');
}

/**
 * Set switch visual state based on checkbox value
 * This function only updates the visual appearance, not the checkbox state
 */
function setSwitchVisualState(switchId, isChecked) {
    const checkbox = document.getElementById(switchId);
    if (!checkbox) return;
    
    const switchElement = checkbox.closest('.custom-switch');
    const slider = switchElement.querySelector('.switch-slider');

    // Update visual appearance based on checkbox state
    if (isChecked) {
        slider.style.backgroundColor = 'var(--accent-primary)';
        slider.style.setProperty('--switch-translate', 'translateX(24px)');
    } else {
        slider.style.backgroundColor = 'var(--border-medium)';
        slider.style.setProperty('--switch-translate', 'translateX(0)');
    }

    console.log(`Switch ${switchId} visual state set to: ${isChecked ? 'ON' : 'OFF'}`);
}

/**
 * Set switch value (both checkbox and visual state)
 * This function updates both the checkbox checked property and visual state
 */
function setSwitchValue(switchId, value) {
    const checkbox = document.getElementById(switchId);
    if (!checkbox) return;
    
    // Set checkbox state
    checkbox.checked = value;
    
    // Update visual state
    setSwitchVisualState(switchId, value);
    
    console.log(`Switch ${switchId} value set to: ${value ? 'ON' : 'OFF'}`);
}

/**
 * Toggle switch value
 */
function toggleSwitch(switchId) {
    const checkbox = document.getElementById(switchId);
    if (!checkbox) return false;
    
    const switchGroup = checkbox.closest('.switch-group');
    
    // Check if switch is disabled
    if (checkbox.disabled || switchGroup.classList.contains('disabled')) {
        console.log(`Switch ${switchId} is disabled, cannot toggle`);
        return false;
    }

    // Toggle the value
    const newValue = !checkbox.checked;
    setSwitchValue(switchId, newValue);
    
    // Trigger change event for form validation/handling
    checkbox.dispatchEvent(new Event('change', { bubbles: true }));
    
    console.log(`Switch ${switchId} toggled to: ${newValue ? 'ON' : 'OFF'}`);
    return true;
}

/**
 * Setup switch click handlers
 */
function setupSwitchInteraction(switchId) {
    const checkbox = document.getElementById(switchId);
    if (!checkbox) return;
    
    const switchElement = checkbox.closest('.custom-switch');
    const switchGroup = checkbox.closest('.switch-group');
    const slider = switchElement.querySelector('.switch-slider');
    const label = switchGroup.querySelector('.switch-label');

    // Handle click on slider
    if (slider) {
        slider.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSwitch(switchId);
        });
    }

    // Handle click on label
    if (label) {
        label.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSwitch(switchId);
        });
    }

    // Handle direct checkbox change (for programmatic changes or accessibility)
    checkbox.addEventListener('change', function() {
        setSwitchVisualState(switchId, this.checked);
    });

    console.log(`Switch interaction setup completed for: ${switchId}`);
}

/**
 * API for external control of switches
 */
const switchAPI = {
    setValue: (switchId, value) => setSwitchValue(switchId, value),
    getValue: (switchId) => {
        const checkbox = document.getElementById(switchId);
        return checkbox ? checkbox.checked : false;
    },
    toggle: (switchId) => toggleSwitch(switchId),
    setEnabled: function(switchId, enabled) {
        const checkbox = document.getElementById(switchId);
        if (!checkbox) return;
        
        const switchGroup = checkbox.closest('.switch-group');
        const switchElement = checkbox.closest('.custom-switch');
        
        checkbox.disabled = !enabled;
        
        if (enabled) {
            switchGroup.classList.remove('disabled');
            switchElement.classList.remove('disabled');
        } else {
            switchGroup.classList.add('disabled');
            switchElement.classList.add('disabled');
        }
        
        console.log(`Switch ${switchId} is now ${enabled ? 'enabled' : 'disabled'}`);
    },
    isEnabled: (switchId) => {
        const checkbox = document.getElementById(switchId);
        return checkbox ? !checkbox.disabled : false;
    }
};

/**
 * Alert message function for forms
 */
function showAlert(type, message) {
    const alertContainer = document.getElementById('alertContainer');
    if (!alertContainer) return;
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    alertContainer.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv && alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 5000);

    // Scroll to top to show alert
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

/* ========== TAG VIEW PAGE SPECIFIC COMMON FUNCTIONS ========== */
/* 标注：新增 - 标签查看页面的公共功能 */

/**
 * Setup info card hover effects
 */
function setupInfoCardEffects() {
    document.querySelectorAll('.info-card').forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.borderColor = 'var(--accent-primary)';
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.borderColor = 'var(--border-light)';
        });
    });
}

/**
 * Setup analytics hover effects
 */
function setupAnalyticsEffects() {
    document.querySelectorAll('.analytics-item').forEach(item => {
        item.addEventListener('mouseenter', () => {
            item.style.transform = 'translateY(-2px)';
            item.style.boxShadow = 'var(--shadow-md)';
        });
        
        item.addEventListener('mouseleave', () => {
            item.style.transform = 'translateY(0)';
            item.style.boxShadow = 'none';
        });
    });
}

/**
 * Initialize tag view page specific effects
 */
function initializeTagViewEffects() {
    setupInfoCardEffects();
    setupAnalyticsEffects();
}

/**
 * Format large numbers for display
 */
function formatLargeNumber(num) {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M';
    } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K';
    }
    return num.toString();
}

/**
 * Update tag statistics display
 */
function updateTagStats(videoCount, viewCount, likeCount, commentCount) {
    const statsElements = {
        videoCount: document.querySelector('.analytics-item:nth-child(1) .analytics-value'),
        viewCount: document.querySelector('.analytics-item:nth-child(2) .analytics-value'), 
        likeCount: document.querySelector('.analytics-item:nth-child(3) .analytics-value'),
        commentCount: document.querySelector('.analytics-item:nth-child(4) .analytics-value')
    };
    
    if (statsElements.videoCount) {
        statsElements.videoCount.textContent = formatLargeNumber(videoCount);
    }
    if (statsElements.viewCount) {
        statsElements.viewCount.textContent = formatLargeNumber(viewCount);
    }
    if (statsElements.likeCount) {
        statsElements.likeCount.textContent = formatLargeNumber(likeCount);
    }
    if (statsElements.commentCount) {
        statsElements.commentCount.textContent = formatLargeNumber(commentCount);
    }
}

/**
 * Animate number counting effect
 */
function animateNumber(element, start, end, duration = 1000) {
    const range = end - start;
    const minTimer = 50;
    let stepTime = Math.abs(Math.floor(duration / range));
    stepTime = Math.max(stepTime, minTimer);
    
    const startTime = new Date().getTime();
    const endTime = startTime + duration;
    
    function run() {
        const now = new Date().getTime();
        const remaining = Math.max((endTime - now) / duration, 0);
        const value = Math.round(end - (remaining * range));
        element.textContent = formatLargeNumber(value);
        
        if (value == end) {
            clearInterval(timer);
        }
    }
    
    const timer = setInterval(run, stepTime);
    run();
}

/**
 * Initialize animated counters for tag view page
 */
function initializeAnimatedCounters() {
    const animatedElements = document.querySelectorAll('.analytics-value, .quick-stat-value');
    
    animatedElements.forEach(element => {
        const text = element.textContent.trim();
        let targetValue = 0;
        
        // Parse text to get numeric value
        if (text.includes('M')) {
            targetValue = parseFloat(text) * 1000000;
        } else if (text.includes('K')) {
            targetValue = parseFloat(text) * 1000;
        } else {
            targetValue = parseInt(text.replace(/,/g, ''));
        }
        
        if (!isNaN(targetValue) && targetValue > 0) {
            animateNumber(element, 0, targetValue, 2000);
        }
    });
}

/* ========== REUSABLE TABLE OPERATIONS ========== */
/* 标注：新增 - 可重用的表格操作功能，适用于多个列表页面 */

/**
 * 通用的表格操作工具类
 * 提供列设置、搜索、分页等功能，可在多个列表页之间共用
 */
const TableOperations = {
    
    /**
     * 从HTML表格header中动态生成列设置选项
     * @param {string} tableSelector - 表格选择器
     * @param {string} popupSelector - 列设置弹窗选择器
     */
    generateColumnSettingsFromHeader: function(tableSelector = 'table', popupSelector = '#columnSettingsPopup') {
        const headerCells = document.querySelectorAll(`${tableSelector} .table-header th[data-column]`);
        const columnSettingsPopup = document.querySelector(popupSelector);
        
        if (!columnSettingsPopup) return;
        
        // 清空现有的列设置选项
        columnSettingsPopup.innerHTML = '';
        
        headerCells.forEach(headerCell => {
            const columnId = headerCell.getAttribute('data-column');
            
            // 跳过checkbox列
            if (columnId === 'checkbox') return;
            
            // 获取列名称
            let columnName = this.extractColumnName(headerCell, columnId);
            
            // 创建checkbox选项
            const checkboxDiv = document.createElement('div');
            checkboxDiv.className = 'popup-checkbox';
            checkboxDiv.innerHTML = `
                <input type="checkbox" id="col-${columnId}" checked>
                <label for="col-${columnId}">${columnName}</label>
            `;
            
            columnSettingsPopup.appendChild(checkboxDiv);
        });
        
        console.log('Generated column settings from table header');
    },
    
    /**
     * 从header cell中提取列名称
     * @param {Element} headerCell - header cell元素
     * @param {string} columnId - 列ID
     * @returns {string} 列名称
     */
    extractColumnName: function(headerCell, columnId) {
        if (columnId === 'actions') {
            return '操作';
        }
        
        // 从header cell中获取文本，排除排序图标
        const textNodes = Array.from(headerCell.childNodes).filter(node => 
            node.nodeType === Node.TEXT_NODE || 
            (node.nodeType === Node.ELEMENT_NODE && !node.classList.contains('d-flex'))
        );
        
        if (textNodes.length > 0) {
            return textNodes[0].textContent.trim();
        }
        
        // 如果没有直接文本节点，从.d-flex中获取第一个文本内容
        const flexDiv = headerCell.querySelector('.d-flex');
        if (flexDiv) {
            return flexDiv.textContent.trim().split('\n')[0].trim();
        }
        
        return columnId; // 备用方案
    },
    
    /**
     * 设置列显示/隐藏功能
     * @param {string} popupSelector - 列设置弹窗选择器
     */
    setupColumnVisibility: function(popupSelector = '#columnSettingsPopup') {
        document.querySelectorAll(`${popupSelector} input[type="checkbox"]`).forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const columnId = checkbox.id.replace('col-', '');
                this.toggleColumnVisibility(columnId, checkbox.checked);
            });
        });
    },
    
    /**
     * 切换列的显示/隐藏状态
     * @param {string} columnId - 列ID
     * @param {boolean} isVisible - 是否显示
     */
    toggleColumnVisibility: function(columnId, isVisible) {
        // 处理header和filter cells
        const columns = document.querySelectorAll(`[data-column="${columnId}"]`);
        columns.forEach(col => {
            col.style.display = isVisible ? '' : 'none';
        });
        
        // 处理数据行中的特殊列
        const specialColumns = {
            'actions': '.table-actions',
            'id': '.table-id',
            'name': '.table-name'
        };
        
        if (specialColumns[columnId]) {
            document.querySelectorAll(specialColumns[columnId]).forEach(cell => {
                cell.style.display = isVisible ? '' : 'none';
            });
        }
    },
    
    /**
     * 设置搜索功能：回车键触发搜索，下拉选择后自动搜索
     * @param {string} tableSelector - 表格选择器
     */
    setupSearchFunctionality: function(tableSelector = 'table') {
        const filterInputs = document.querySelectorAll(`${tableSelector} .table-filter-cell input[type="text"]`);
        const filterSelects = document.querySelectorAll(`${tableSelector} .table-filter-cell select`);
        
        // 为文本输入框添加回车键监听
        filterInputs.forEach(input => {
            input.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.applyFilters();
                }
            });
        });
        
        // 为下拉选择框添加change监听
        filterSelects.forEach(select => {
            select.addEventListener('change', () => {
                this.applyFilters();
            });
        });
    },
    
    /**
     * 应用筛选器 - 将筛选条件作为GET参数传递到URL
     */
    applyFilters: function() {
        const filterParams = new URLSearchParams();
        const currentUrl = new URL(window.location);
        
        // 收集所有筛选条件
        const filterInputs = document.querySelectorAll('.table-filter-cell input[type="text"], .table-filter-cell select');
        
        filterInputs.forEach(input => {
            const columnName = input.closest('[data-column]').getAttribute('data-column');
            const value = input.value.trim();
            
            if (value && value !== '') {
                filterParams.set(columnName, value);
            }
        });
        
        // 保留现有的非筛选参数
        const preserveParams = ['page', 'limit'];
        preserveParams.forEach(param => {
            if (currentUrl.searchParams.has(param)) {
                filterParams.set(param, currentUrl.searchParams.get(param));
            }
        });
        
        // 构建新的URL
        const newUrl = `${currentUrl.pathname}?${filterParams.toString()}`;
        
        console.log('Applying filters with URL:', newUrl);
        
        // 重定向到新的URL（PHP将处理筛选）
        window.location.href = newUrl;
    },
    
    /**
     * 设置分页功能
     * @param {Object} options - 分页选项
     * @param {number} options.currentPage - 当前页码
     * @param {number} options.totalPages - 总页数
     * @param {Function} options.onPageChange - 页码变化回调
     * @param {string} options.paginationSelector - 分页容器选择器
     */
    setupPagination: function(options = {}) {
        const {
            currentPage = 1,
            totalPages = 1,
            onPageChange = () => {},
            paginationSelector = '#paginationNav'
        } = options;
        
        const paginationNav = document.querySelector(paginationSelector);
        if (!paginationNav) return;
        
        this.renderPagination(paginationNav, currentPage, totalPages, onPageChange);
    },
    
    /**
     * 渲染分页组件
     * @param {Element} container - 分页容器
     * @param {number} currentPage - 当前页码
     * @param {number} totalPages - 总页数
     * @param {Function} onPageChange - 页码变化回调
     */
    renderPagination: function(container, currentPage, totalPages, onPageChange) {
        container.innerHTML = '';
        
        // Previous button
        const prevBtn = document.createElement('li');
        prevBtn.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevBtn.innerHTML = `<a class="page-link" href="#" aria-label="Previous"><i class="bi bi-chevron-left"></i></a>`;
        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (currentPage > 1) {
                onPageChange(currentPage - 1);
            }
        });
        container.appendChild(prevBtn);
        
        // Page numbers
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);
        
        if (startPage > 1) {
            const firstPage = document.createElement('li');
            firstPage.className = 'page-item';
            firstPage.innerHTML = '<a class="page-link" href="#" data-page="1">1</a>';
            firstPage.addEventListener('click', (e) => {
                e.preventDefault();
                onPageChange(1);
            });
            container.appendChild(firstPage);
            
            if (startPage > 2) {
                const ellipsis = document.createElement('li');
                ellipsis.className = 'page-item disabled';
                ellipsis.innerHTML = '<span class="page-link">...</span>';
                container.appendChild(ellipsis);
            }
        }
        
        for (let i = startPage; i <= endPage; i++) {
            const pageBtn = document.createElement('li');
            pageBtn.className = `page-item ${i === currentPage ? 'active' : ''}`;
            pageBtn.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
            pageBtn.addEventListener('click', (e) => {
                e.preventDefault();
                onPageChange(i);
            });
            container.appendChild(pageBtn);
        }
        
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const ellipsis = document.createElement('li');
                ellipsis.className = 'page-item disabled';
                ellipsis.innerHTML = '<span class="page-link">...</span>';
                container.appendChild(ellipsis);
            }
            
            const lastPage = document.createElement('li');
            lastPage.className = 'page-item';
            lastPage.innerHTML = `<a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>`;
            lastPage.addEventListener('click', (e) => {
                e.preventDefault();
                onPageChange(totalPages);
            });
            container.appendChild(lastPage);
        }
        
        // Next button
        const nextBtn = document.createElement('li');
        nextBtn.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextBtn.innerHTML = `<a class="page-link" href="#" aria-label="Next"><i class="bi bi-chevron-right"></i></a>`;
        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (currentPage < totalPages) {
                onPageChange(currentPage + 1);
            }
        });
        container.appendChild(nextBtn);
    },
    
    /**
     * 设置全选功能
     * @param {string} selectAllSelector - 全选checkbox选择器
     * @param {string} rowCheckboxSelector - 行checkbox选择器
     * @param {Function} onSelectionChange - 选择变化回调
     */
    setupSelectAll: function(selectAllSelector = '#selectAll', rowCheckboxSelector = '.row-checkbox', onSelectionChange = () => {}) {
        const selectAllCheckbox = document.querySelector(selectAllSelector);
        
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                const rowCheckboxes = document.querySelectorAll(rowCheckboxSelector);
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                onSelectionChange();
            });
        }
    },
    
    /**
     * 更新选中数量显示
     * @param {string} countSelector - 数量显示选择器
     * @param {string} selectAllSelector - 全选checkbox选择器
     * @param {string} rowCheckboxSelector - 行checkbox选择器
     */
    updateSelectedCount: function(countSelector = '#selectedCount', selectAllSelector = '#selectAll', rowCheckboxSelector = '.row-checkbox') {
        const selectedRows = document.querySelectorAll(`${rowCheckboxSelector}:checked`);
        const selectedCountSpan = document.querySelector(countSelector);
        const selectAllCheckbox = document.querySelector(selectAllSelector);
        
        if (selectedCountSpan) {
            selectedCountSpan.textContent = selectedRows.length;
        }
        
        // Update select all checkbox state
        if (selectAllCheckbox) {
            const rowCheckboxes = document.querySelectorAll(rowCheckboxSelector);
            if (selectedRows.length === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            } else if (selectedRows.length === rowCheckboxes.length) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = true;
            } else {
                selectAllCheckbox.indeterminate = true;
            }
        }
    }
};

// ========== GLOBAL EXPORTS ========== 
// Make functions globally accessible for page-specific usage
window.AdminCommon = {
    showModal,
    showToast,
    setupNotificationBlink,
    formatDate,
    formatNumber,
    formatLargeNumber,
    setTheme: window.setTheme,
    updateThemeDisplay: window.updateThemeDisplay,
    setupDropdown,
    ValidationUtils: {
        isValidEmail,
        showFieldError,
        clearFieldError,
        clearValidation,
        validateField,
        initializeCharacterCounters,
        
    },
    SwitchUtils: {
        initializeSwitches,
        setSwitchVisualState,
        setSwitchValue,
        toggleSwitch,
        setupSwitchInteraction,
        showAlert
    },
    TagViewUtils: {
        initializeTagViewEffects,
        setupInfoCardEffects,
        setupAnalyticsEffects,
        updateTagStats,
        animateNumber,
        initializeAnimatedCounters
    },
    TableOperations: TableOperations
};

window.switchAPI = switchAPI;