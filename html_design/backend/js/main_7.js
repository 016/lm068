/**
 * Backend Admin Main JavaScript v7 - 优化版表格管理
 * 基于 main_6.js 优化，合并重复功能，提高代码效率和可维护性
 * 
 * 主要优化：
 * - 重构 TableOperations：专注工具方法，移除状态管理
 * - 优化 TableManager：统一排序和事件管理，减少重复代码
 * - 简化 CommonTableActions：合并下拉菜单逻辑
 * - 使用事件委托减少事件绑定开销
 * - 优化分页和显示更新逻辑
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
    
    toggleBtn.addEventListener('click', () => {
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('show');
            mobileOverlay.classList.toggle('active');
        } else {
            sidebar.classList.toggle('collapsed');
            console.log('Sidebar collapsed state:', sidebar.classList.contains('collapsed'));
        }
    });
    
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
        document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
            if (menu !== dropdown) menu.classList.remove('show');
        });
        dropdown.classList.toggle('show');
    });
    
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
        if (themeIcon) {
            themeIcon.className = activeTheme === 'dark' 
                ? 'bi bi-moon theme-icon' 
                : 'bi bi-sun theme-icon';
        }
    }
    
    function updateThemeDropdown() {
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
        
        updateThemeDisplay();
        updateThemeDropdown();
    }
    
    document.querySelectorAll('.theme-option').forEach(option => {
        option.addEventListener('click', () => {
            setTheme(option.dataset.theme);
            document.getElementById('themeDropdown')?.classList.remove('show');
        });
    });
    
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        if (currentTheme === 'auto') {
            setTheme('auto');
        }
    });
    
    setTheme(currentTheme);
    window.setTheme = setTheme;
    window.updateThemeDisplay = updateThemeDisplay;
}

// ========== RESPONSIVE HANDLERS ========== 
function setupResponsiveHandlers() {
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768 && sidebar) {
            sidebar.classList.remove('show');
            mobileOverlay?.classList.remove('active');
        }
    });
}

// ========== Toast FUNCTIONALITY ========== 
function showToast(message, type = '') {
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    const typeClass = type ? `text-bg-${type}` : '';
    toast.className = `toast align-items-center ${typeClass} border-0`;
    toast.id = toastId;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
    bsToast.show();
    
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
    const badge = document.querySelector('.notification-badge');
    if (badge) {
        setInterval(() => {
            badge.style.display = badge.style.display === 'none' ? 'block' : 'none';
        }, 3000);
    }
}

// ========== CHARACTER COUNTER ========== 
function initializeCharacterCounters(input_form) {
    const textareas = input_form.querySelectorAll('textarea[maxlength], input[maxlength]');
    textareas.forEach(textarea => {
        updateCharacterCounter(textarea);
        textarea.addEventListener('input', () => {
            updateCharacterCounter(textarea);
        });
    });
}

function updateCharacterCounter(field) {
    const maxLength = parseInt(field.getAttribute('maxlength'));
    const currentLength = field.value.length;
    const formText = field.parentElement.querySelector('.form-text');
    
    if (formText && maxLength) {
        const percentage = (currentLength / maxLength) * 100;
        const originalText = formText.textContent.split('(')[0];
        
        formText.textContent = `${originalText}(${currentLength}/${maxLength})`;
        
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

function formatLargeNumber(num) {
    return formatNumber(num);
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
function initializeSwitches() {
    console.log('Initializing switches by reading HTML checkbox attributes...');
    
    const switches = document.querySelectorAll('.custom-switch input[type="checkbox"]');
    
    switches.forEach(checkbox => {
        const switchId = checkbox.id;
        if (switchId) {
            const isChecked = checkbox.checked;
            console.log(`Switch ${switchId}: HTML checkbox checked = ${isChecked}`);
            setSwitchVisualState(switchId, isChecked);
        }
    });
    
    console.log('All switches initialized from HTML checkbox attributes');
}

function setSwitchVisualState(switchId, isChecked) {
    const checkbox = document.getElementById(switchId);
    if (!checkbox) return;
    
    const switchElement = checkbox.closest('.custom-switch');
    const slider = switchElement.querySelector('.switch-slider');

    if (isChecked) {
        slider.style.backgroundColor = 'var(--accent-primary)';
        slider.style.setProperty('--switch-translate', 'translateX(24px)');
    } else {
        slider.style.backgroundColor = 'var(--border-medium)';
        slider.style.setProperty('--switch-translate', 'translateX(0)');
    }

    console.log(`Switch ${switchId} visual state set to: ${isChecked ? 'ON' : 'OFF'}`);
}

function setSwitchValue(switchId, value) {
    const checkbox = document.getElementById(switchId);
    if (!checkbox) return;
    
    checkbox.checked = value;
    setSwitchVisualState(switchId, value);
    console.log(`Switch ${switchId} value set to: ${value ? 'ON' : 'OFF'}`);
}

function toggleSwitch(switchId) {
    const checkbox = document.getElementById(switchId);
    if (!checkbox) return false;
    
    const switchGroup = checkbox.closest('.switch-group');
    
    if (checkbox.disabled || switchGroup.classList.contains('disabled')) {
        console.log(`Switch ${switchId} is disabled, cannot toggle`);
        return false;
    }

    const newValue = !checkbox.checked;
    setSwitchValue(switchId, newValue);
    
    checkbox.dispatchEvent(new Event('change', { bubbles: true }));
    console.log(`Switch ${switchId} toggled to: ${newValue ? 'ON' : 'OFF'}`);
    return true;
}

function setupSwitchInteraction(switchId) {
    const checkbox = document.getElementById(switchId);
    if (!checkbox) return;
    
    const switchElement = checkbox.closest('.custom-switch');
    const switchGroup = checkbox.closest('.switch-group');
    const slider = switchElement.querySelector('.switch-slider');
    const label = switchGroup.querySelector('.switch-label');

    if (slider) {
        slider.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSwitch(switchId);
        });
    }

    if (label) {
        label.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSwitch(switchId);
        });
    }

    checkbox.addEventListener('change', function() {
        setSwitchVisualState(switchId, this.checked);
    });

    console.log(`Switch interaction setup completed for: ${switchId}`);
}

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
    
    setTimeout(() => {
        if (alertDiv && alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 5000);

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

/* ========== TAG VIEW PAGE SPECIFIC COMMON FUNCTIONS ========== */
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

function initializeTagViewEffects() {
    setupInfoCardEffects();
    setupAnalyticsEffects();
}

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

function initializeAnimatedCounters() {
    const animatedElements = document.querySelectorAll('.analytics-value, .quick-stat-value');
    
    animatedElements.forEach(element => {
        const text = element.textContent.trim();
        let targetValue = 0;
        
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

/* ========== 优化版表格操作工具类 ========== */
/**
 * 优化版表格操作工具类 - 专注于数据处理和工具方法
 * 移除状态管理逻辑，提高代码复用性
 */
const TableOperations = {
    
    /**
     * 从HTML表格中读取数据并解析列配置
     */
    loadDataFromHTML: function(tableSelector = '#dataTable', bodySelector = 'tbody') {
        const table = document.querySelector(tableSelector);
        if (!table) {
            console.error('Table not found:', tableSelector);
            return { columns: [], data: [] };
        }

        const headerCells = table.querySelectorAll('thead .table-header th[data-column]');
        const columns = [];
        
        headerCells.forEach(headerCell => {
            const columnId = headerCell.getAttribute('data-column');
            if (columnId === 'checkbox') return;
            
            const columnName = this.extractColumnName(headerCell, columnId);
            const isSortable = headerCell.querySelector('.sort-icon') !== null;
            
            columns.push({
                id: columnId,
                name: columnName,
                sortable: isSortable,
                type: this.detectColumnType(columnId, headerCell)
            });
        });

        const dataRows = table.querySelectorAll(`${bodySelector} tr.table-row`);
        const data = [];
        
        dataRows.forEach((row, index) => {
            const rowData = { _rowIndex: index, _rowId: row.getAttribute('data-id') };
            
            columns.forEach(column => {
                const cell = row.querySelector(`[data-column="${column.id}"]`);
                if (cell) {
                    rowData[column.id] = this.extractCellData(cell, column);
                }
            });
            
            data.push(rowData);
        });

        console.log(`Loaded ${data.length} rows with ${columns.length} columns from HTML table`);
        return { columns, data };
    },
    
    /**
     * 从header cell中提取列名称
     */
    extractColumnName: function(headerCell, columnId) {
        if (columnId === 'actions') {
            return '操作';
        }
        
        const flexDiv = headerCell.querySelector('.d-flex');
        if (flexDiv) {
            const textContent = flexDiv.textContent.trim();
            return textContent.split('\n')[0].trim();
        }
        
        const textNodes = Array.from(headerCell.childNodes).filter(node => 
            node.nodeType === Node.TEXT_NODE && node.textContent.trim()
        );
        
        if (textNodes.length > 0) {
            return textNodes[0].textContent.trim();
        }
        
        return columnId;
    },
    
    /**
     * 检测列的数据类型
     */
    detectColumnType: function(columnId, headerCell) {
        const typeMapping = {
            'id': 'number',
            'content_cnt': 'number',
            'views': 'number',
            'count': 'number',
            'status': 'status',
            'actions': 'actions'
        };
        
        return typeMapping[columnId] || 'text';
    },
    
    /**
     * 从表格单元格中提取数据
     */
    extractCellData: function(cell, column) {
        const cellType = column.type;
        const cellContent = cell.textContent.trim();
        
        switch (cellType) {
            case 'number':
                const numberMatch = cellContent.match(/[\d,]+/);
                if (numberMatch) {
                    return parseInt(numberMatch[0].replace(/,/g, ''));
                }
                return 0;
                
            case 'status':
                const badge = cell.querySelector('.badge');
                if (badge) {
                    const statusText = badge.textContent.trim().split(' ').pop();
                    const isActive = badge.classList.contains('badge-success');
                    return {
                        text: statusText,
                        value: isActive ? 'active' : 'inactive',
                        isActive: isActive
                    };
                }
                return { text: cellContent, value: 'unknown', isActive: false };
                
            case 'actions':
                return cell.innerHTML;
                
            default:
                return cellContent;
        }
    },
    
    /**
     * 排序功能
     */
    sortData: function(data, field, direction, columns) {
        const column = columns.find(col => col.id === field);
        if (!column) return data;
        
        const sortedData = [...data].sort((a, b) => {
            let aVal = a[field];
            let bVal = b[field];
            
            switch (column.type) {
                case 'number':
                    aVal = typeof aVal === 'number' ? aVal : 0;
                    bVal = typeof bVal === 'number' ? bVal : 0;
                    return direction === 'asc' ? aVal - bVal : bVal - aVal;
                    
                case 'status':
                    aVal = aVal?.text || '';
                    bVal = bVal?.text || '';
                    break;
                    
                default:
                    aVal = String(aVal || '').toLowerCase();
                    bVal = String(bVal || '').toLowerCase();
            }
            
            if (aVal < bVal) return direction === 'asc' ? -1 : 1;
            if (aVal > bVal) return direction === 'asc' ? 1 : -1;
            return 0;
        });
        
        console.log(`Sorted ${sortedData.length} rows by ${field} ${direction}`);
        return sortedData;
    },
    
    /**
     * 分页功能
     */
    getPaginatedData: function(data, currentPage = 1, itemsPerPage = 10) {
        const totalItems = data.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const currentPageData = data.slice(startIndex, endIndex);
        
        return {
            data: currentPageData,
            pagination: {
                currentPage,
                totalPages,
                itemsPerPage,
                totalItems,
                startIndex: startIndex + 1,
                endIndex: Math.min(endIndex, totalItems)
            }
        };
    },
    
    /**
     * 渲染表格数据
     */
    renderTableData: function(data, columns, tbodySelector = '#tagTableBody') {
        const tbody = document.querySelector(tbodySelector);
        if (!tbody) return;
        
        tbody.innerHTML = '';
        
        data.forEach((row, index) => {
            const tr = document.createElement('tr');
            tr.className = 'table-row';
            tr.setAttribute('data-id', row._rowId || row.id || index);
            
            // 添加checkbox列
            const checkboxTd = document.createElement('td');
            checkboxTd.className = 'table-cell';
            checkboxTd.innerHTML = `
                <div class="form-check">
                    <input class="form-check-input row-checkbox" type="checkbox" value="${row._rowId || row.id || index}">
                </div>
            `;
            tr.appendChild(checkboxTd);
            
            // 添加数据列
            columns.forEach(column => {
                const td = document.createElement('td');
                td.className = 'table-cell';
                td.setAttribute('data-column', column.id);
                
                switch (column.type) {
                    case 'status':
                        const statusData = row[column.id];
                        const badgeClass = statusData?.isActive ? 'badge-success' : 'badge-danger';
                        td.innerHTML = `
                            <span class="badge rounded-pill ${badgeClass}">
                                <i class="bi bi-circle-fill badge-icon"></i> ${statusData?.text || '未知'}
                            </span>
                        `;
                        break;
                        
                    case 'number':
                        if (column.id === 'content_cnt') {
                            td.innerHTML = `
                                <a href="/videos/index?tag_id=${row["id"]}" class="content-link" target="_blank">${row[column.id]?.toLocaleString() || '0'}</a>
                            `;
                        } else {
                            td.textContent = row[column.id]?.toLocaleString() || '0';
                        }
                        break;
                        
                    case 'actions':
                        td.innerHTML = row[column.id] || '';
                        td.classList.add('table-actions');
                        break;
                        
                    default:
                        td.textContent = row[column.id] || '';
                        if (column.id === 'id') td.classList.add('table-id');
                        if (column.id === 'name') td.classList.add('table-name');
                }
                
                tr.appendChild(td);
            });
            
            tbody.appendChild(tr);
        });
    },
    
    /**
     * 导出数据
     */
    exportData: function(data, columns, format = 'json', filename = 'table_data') {
        const timestamp = new Date().toISOString().split('T')[0];
        const fullFilename = `${filename}_${timestamp}.${format}`;
        
        if (format === 'json') {
            const exportData = data.map(row => {
                const exportRow = {};
                columns.forEach(column => {
                    let value = row[column.id];
                    
                    if (column.type === 'status' && value?.text) {
                        value = value.text;
                    } else if (column.type === 'actions') {
                        value = '操作';
                    }
                    
                    exportRow[column.name] = value;
                });
                return exportRow;
            });
            
            const jsonData = JSON.stringify(exportData, null, 2);
            const blob = new Blob([jsonData], { type: 'application/json' });
            this.downloadFile(blob, fullFilename);
            
        } else if (format === 'csv') {
            const csvHeader = columns.map(col => `"${col.name}"`).join(',') + '\n';
            
            const csvData = data.map(row => {
                return columns.map(column => {
                    let value = row[column.id];
                    
                    if (column.type === 'status' && value?.text) {
                        value = value.text;
                    } else if (column.type === 'actions') {
                        value = '操作';
                    } else if (typeof value === 'number') {
                        return value;
                    }
                    
                    return `"${String(value || '').replace(/"/g, '""')}"`;
                }).join(',');
            }).join('\n');
            
            const csvContent = csvHeader + csvData;
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            this.downloadFile(blob, fullFilename);
        }
    },
    
    /**
     * 下载文件
     */
    downloadFile: function(blob, filename) {
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        a.click();
        URL.revokeObjectURL(url);
        
        console.log(`Downloaded file: ${filename}`);
    }
};

/* ========== 优化版表格管理器 ========== */
/**
 * 优化版表格管理器 - 统一事件管理，减少重复绑定
 */
class TableManager {
    constructor(config = {}) {
        this.config = {
            tableSelector: '#dataTable',
            tbodySelector: '#tagTableBody',
            paginationSelector: '#paginationNav',
            itemsPerPageSelector: '#itemsPerPage',
            selectedCountSelector: '#selectedCount',
            selectAllSelector: '#selectAll',
            rowCheckboxSelector: '.row-checkbox',
            columnSettingsPopupSelector: '#columnSettingsPopup',
            currentDisplaySelector: '#currentDisplay',
            defaultItemsPerPage: 2,
            enableSort: true,
            enablePagination: true,
            enableColumnSettings: true,
            enableSearch: true,
            ...config
        };
        
        // 状态变量
        this.currentPage = 1;
        this.itemsPerPage = this.config.defaultItemsPerPage;
        this.originalData = [];
        this.filteredData = [];
        this.displayData = [];
        this.tableColumns = [];
        this.currentSort = { field: '', direction: '' };
        
        console.log('TableManager initialized with config:', this.config);
    }
    
    /**
     * 初始化表格管理器
     */
    init() {
        console.log('=== TableManager 初始化开始 ===');
        
        // 1. 从HTML表格中读取数据和列配置
        const tableData = TableOperations.loadDataFromHTML(this.config.tableSelector, 'tbody');
        this.originalData = tableData.data;
        this.filteredData = [...this.originalData];
        this.tableColumns = tableData.columns;
        
        console.log(`加载了 ${this.originalData.length} 条数据，${this.tableColumns.length} 列`);
        
        // 2. 设置功能模块
        if (this.config.enableColumnSettings) {
            this.setupColumnSettings();
        }
        
        if (this.config.enableSearch) {
            this.setupSearchFunctionality();
        }
        
        if (this.config.enableSort) {
            this.setupSorting();
        }
        
        this.setupItemsPerPage();
        this.setupSelectAll();
        
        // 3. 使用事件委托设置事件监听
        this.setupEventDelegation();
        
        // 4. 初始渲染
        this.updateDisplay();
        
        console.log('=== TableManager 初始化完成 ===');
        return this;
    }
    
    /**
     * 使用事件委托减少事件绑定开销
     */
    setupEventDelegation() {
        const table = document.querySelector(this.config.tableSelector);
        if (!table) return;
        
        // 委托处理排序点击
        table.addEventListener('click', (e) => {
            const sortableHeader = e.target.closest('.sortable-header');
            if (sortableHeader) {
                e.preventDefault();
                e.stopPropagation();
                
                const columnId = sortableHeader.getAttribute('data-column');
                if (columnId) {
                    const nextDirection = this.getNextSortDirection(columnId);
                    this.performSort(columnId, nextDirection);
                }
            }
        });
        
        // 委托处理行选择框变化
        table.addEventListener('change', (e) => {
            if (e.target.classList.contains('row-checkbox')) {
                this.updateSelectedCount();
            }
        });
        
        console.log('事件委托设置完成');
    }
    
    /**
     * 设置列设置功能
     */
    setupColumnSettings() {
        this.generateColumnSettingsFromHeader();
        this.setupColumnVisibility();
        console.log('列设置功能已启用');
    }
    
    /**
     * 动态生成列设置选项
     */
    generateColumnSettingsFromHeader() {
        const headerCells = document.querySelectorAll(`${this.config.tableSelector} .table-header th[data-column]`);
        const columnSettingsPopup = document.querySelector(this.config.columnSettingsPopupSelector);
        
        if (!columnSettingsPopup) return;
        
        columnSettingsPopup.innerHTML = '';
        
        headerCells.forEach(headerCell => {
            const columnId = headerCell.getAttribute('data-column');
            
            if (columnId === 'checkbox') return;
            
            const columnName = TableOperations.extractColumnName(headerCell, columnId);
            
            const checkboxDiv = document.createElement('div');
            checkboxDiv.className = 'popup-checkbox';
            checkboxDiv.innerHTML = `
                <input type="checkbox" id="col-${columnId}" checked>
                <label for="col-${columnId}">${columnName}</label>
            `;
            
            columnSettingsPopup.appendChild(checkboxDiv);
        });
        
        console.log('Generated column settings from table header');
    }
    
    /**
     * 设置列显示/隐藏功能
     */
    setupColumnVisibility() {
        document.querySelectorAll(`${this.config.columnSettingsPopupSelector} input[type="checkbox"]`).forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const columnId = checkbox.id.replace('col-', '');
                this.toggleColumnVisibility(columnId, checkbox.checked);
            });
        });
    }
    
    /**
     * 切换列的显示/隐藏状态
     */
    toggleColumnVisibility(columnId, isVisible) {
        const columns = document.querySelectorAll(`[data-column="${columnId}"]`);
        columns.forEach(col => {
            col.style.display = isVisible ? '' : 'none';
        });
        
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
    }
    
    /**
     * 设置搜索功能
     */
    setupSearchFunctionality() {
        const filterInputs = document.querySelectorAll(`${this.config.tableSelector} .table-filter-cell input[type="text"]`);
        const filterSelects = document.querySelectorAll(`${this.config.tableSelector} .table-filter-cell select`);
        
        filterInputs.forEach(input => {
            input.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.applyFilters();
                }
            });
        });
        
        filterSelects.forEach(select => {
            select.addEventListener('change', () => {
                this.applyFilters();
            });
        });
    }
    
    /**
     * 应用筛选器
     */
    applyFilters() {
        const filterParams = new URLSearchParams();
        const currentUrl = new URL(window.location);
        
        const filterInputs = document.querySelectorAll('.table-filter-cell input[type="text"], .table-filter-cell select');
        
        filterInputs.forEach(input => {
            const columnName = input.closest('[data-column]').getAttribute('data-column');
            const value = input.value.trim();
            
            if (value && value !== '') {
                filterParams.set(columnName, value);
            }
        });
        
        const preserveParams = ['page', 'limit'];
        preserveParams.forEach(param => {
            if (currentUrl.searchParams.has(param)) {
                filterParams.set(param, currentUrl.searchParams.get(param));
            }
        });
        
        const newUrl = `${currentUrl.pathname}?${filterParams.toString()}`;
        console.log('Applying filters with URL:', newUrl);
        window.location.href = newUrl;
    }
    
    /**
     * 设置排序功能
     */
    setupSorting() {
        console.log('设置排序功能...');
        
        const sortableHeaders = document.querySelectorAll('.sortable-header');
        sortableHeaders.forEach(header => {
            header.style.cursor = 'pointer';
            const columnId = header.getAttribute('data-column');
            header.title = `点击排序 ${this.getColumnDisplayName(columnId)}`;
        });
        
        console.log(`设置了 ${sortableHeaders.length} 个可排序表头`);
    }
    
    /**
     * 获取列的显示名称
     */
    getColumnDisplayName(columnId) {
        const column = this.tableColumns.find(col => col.id === columnId);
        return column ? column.name : columnId;
    }
    
    /**
     * 获取下一个排序方向
     */
    getNextSortDirection(columnId) {
        if (this.currentSort.field !== columnId) {
            return 'asc';
        }
        
        switch (this.currentSort.direction) {
            case '':
            case 'asc':
                return 'desc';
            case 'desc':
                return 'asc';
            default:
                return 'asc';
        }
    }
    
    /**
     * 执行排序
     */
    performSort(columnId, direction) {
        console.log(`执行排序: ${columnId} ${direction}`);
        
        this.currentSort = { field: columnId, direction };
        this.filteredData = TableOperations.sortData(this.filteredData, columnId, direction, this.tableColumns);
        
        this.currentPage = 1;
        this.updateDisplay();
        this.updateSortIcons(columnId, direction);
    }
    
    /**
     * 更新排序图标状态
     */
    updateSortIcons(activeField, direction) {
        document.querySelectorAll('.sort-icon').forEach(icon => {
            icon.classList.remove('active');
        });
        
        const activeIcon = document.querySelector(`.sort-icon[data-sort="${activeField}"][data-direction="${direction}"]`);
        if (activeIcon) {
            activeIcon.classList.add('active');
        }
        
        console.log(`排序图标状态更新: ${activeField} ${direction}`);
    }
    
    /**
     * 设置每页条数功能
     */
    setupItemsPerPage() {
        const itemsPerPageSelect = document.querySelector(this.config.itemsPerPageSelector);
        if (!itemsPerPageSelect) return;
        
        itemsPerPageSelect.value = this.itemsPerPage;
        
        itemsPerPageSelect.addEventListener('change', (e) => {
            const newItemsPerPage = parseInt(e.target.value);
            console.log(`每页条数变更: ${this.itemsPerPage} -> ${newItemsPerPage}`);
            
            this.itemsPerPage = newItemsPerPage;
            this.currentPage = 1;
            this.updateDisplay();
        });
        
        console.log(`每页条数设置完成，默认值: ${this.itemsPerPage}`);
    }
    
    /**
     * 设置全选功能
     */
    setupSelectAll() {
        const selectAllCheckbox = document.querySelector(this.config.selectAllSelector);
        
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', (e) => {
                const isChecked = e.target.checked;
                const rowCheckboxes = document.querySelectorAll(this.config.rowCheckboxSelector);
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                this.updateSelectedCount();
            });
        }
        
        console.log('全选功能已设置');
    }
    
    /**
     * 更新选中数量
     */
    updateSelectedCount() {
        const selectedRows = document.querySelectorAll(`${this.config.rowCheckboxSelector}:checked`);
        const selectedCountSpan = document.querySelector(this.config.selectedCountSelector);
        const selectAllCheckbox = document.querySelector(this.config.selectAllSelector);
        
        if (selectedCountSpan) {
            selectedCountSpan.textContent = selectedRows.length;
        }
        
        if (selectAllCheckbox) {
            const rowCheckboxes = document.querySelectorAll(this.config.rowCheckboxSelector);
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
    
    /**
     * 更新显示 - 处理分页、渲染表格、更新汇总信息
     */
    updateDisplay() {
        console.log('=== TableManager 更新显示 ===');
        
        const paginationResult = TableOperations.getPaginatedData(
            this.filteredData, 
            this.currentPage, 
            this.itemsPerPage
        );
        this.displayData = paginationResult.data;
        const pagination = paginationResult.pagination;
        
        console.log(`当前页: ${pagination.currentPage}/${pagination.totalPages}, 显示: ${pagination.startIndex}-${pagination.endIndex}/${pagination.totalItems}`);
        
        TableOperations.renderTableData(this.displayData, this.tableColumns, this.config.tbodySelector);
        
        if (this.config.enablePagination) {
            this.setupPagination(pagination);
        }
        
        this.updateSummaryInfo(pagination);
        this.reapplyColumnVisibility();
        
        console.log('=== TableManager 显示更新完成 ===');
    }
    
    /**
     * 设置分页功能
     */
    setupPagination(pagination) {
        const paginationNav = document.querySelector(this.config.paginationSelector);
        if (!paginationNav) return;
        
        this.renderPagination(paginationNav, pagination.currentPage, pagination.totalPages);
    }
    
    /**
     * 渲染分页组件
     */
    renderPagination(container, currentPage, totalPages) {
        container.innerHTML = '';
        
        if (totalPages <= 1) return;
        
        // Previous button
        const prevBtn = document.createElement('li');
        prevBtn.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevBtn.innerHTML = `<a class="page-link" href="#" aria-label="Previous"><i class="bi bi-chevron-left"></i></a>`;
        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (currentPage > 1) {
                this.currentPage = currentPage - 1;
                this.updateDisplay();
            }
        });
        container.appendChild(prevBtn);
        
        // Page numbers
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);
        
        for (let i = startPage; i <= endPage; i++) {
            const pageBtn = document.createElement('li');
            pageBtn.className = `page-item ${i === currentPage ? 'active' : ''}`;
            pageBtn.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
            pageBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.currentPage = i;
                this.updateDisplay();
            });
            container.appendChild(pageBtn);
        }
        
        // Next button
        const nextBtn = document.createElement('li');
        nextBtn.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextBtn.innerHTML = `<a class="page-link" href="#" aria-label="Next"><i class="bi bi-chevron-right"></i></a>`;
        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (currentPage < totalPages) {
                this.currentPage = currentPage + 1;
                this.updateDisplay();
            }
        });
        container.appendChild(nextBtn);
    }
    
    /**
     * 更新汇总信息
     */
    updateSummaryInfo(pagination) {
        const currentDisplay = document.querySelector(this.config.currentDisplaySelector);
        if (currentDisplay) {
            currentDisplay.textContent = `${pagination.startIndex}-${pagination.endIndex}/${pagination.totalItems}`;
        }
        
        console.log(`汇总信息更新: ${currentDisplay?.textContent}`);
    }
    
    /**
     * 重新应用列显示设置
     */
    reapplyColumnVisibility() {
        document.querySelectorAll(`${this.config.columnSettingsPopupSelector} input[type="checkbox"]`).forEach(checkbox => {
            const columnId = checkbox.id.replace('col-', '');
            this.toggleColumnVisibility(columnId, checkbox.checked);
        });
    }
    
    /**
     * 获取选中的行数据
     */
    getSelectedRows() {
        const selectedCheckboxes = document.querySelectorAll(`${this.config.rowCheckboxSelector}:checked`);
        const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);
        return selectedIds;
    }
    
    /**
     * 刷新数据
     */
    refresh() {
        console.log('刷新表格数据');
        location.reload();
    }
    
    /**
     * 导出数据
     */
    exportData(format, filename = 'table_data') {
        console.log(`开始导出数据，格式: ${format}, 数据量: ${this.filteredData.length}`);
        TableOperations.exportData(this.filteredData, this.tableColumns, format, filename);
    }
}

/* ========== 优化版通用表格操作功能类 ========== */
/**
 * 优化版通用表格操作功能类 - 合并下拉菜单逻辑，减少重复代码
 */
class CommonTableActions {
    constructor(tableManager, config = {}) {
        this.tableManager = tableManager;
        this.config = {
            exportBtnSelector: '#exportBtn',
            exportPopupSelector: '#exportPopup',
            refreshBtnSelector: '#refreshBtn',
            columnSettingsBtnSelector: '#columnSettingsBtn',
            columnSettingsPopupSelector: '#columnSettingsPopup',
            bulkActionsBtnSelector: '#bulkActionsBtn',
            bulkActionsDropdownSelector: '#bulkActionsDropdown',
            ...config
        };
        
        // 下拉菜单配置
        this.dropdownConfigs = [
            { trigger: this.config.exportBtnSelector, dropdown: this.config.exportPopupSelector },
            { trigger: this.config.columnSettingsBtnSelector, dropdown: this.config.columnSettingsPopupSelector },
            { trigger: this.config.bulkActionsBtnSelector, dropdown: this.config.bulkActionsDropdownSelector }
        ];
    }
    
    /**
     * 初始化所有操作功能
     */
    init() {
        this.setupDropdowns();
        this.setupRefreshButton();
        this.setupBulkActions();
        
        console.log('CommonTableActions 初始化完成');
        return this;
    }
    
    /**
     * 统一设置所有下拉菜单
     */
    setupDropdowns() {
        this.dropdownConfigs.forEach(config => {
            const trigger = document.querySelector(config.trigger);
            const dropdown = document.querySelector(config.dropdown);
            
            if (trigger && dropdown) {
                trigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    dropdown.classList.toggle('show');
                });
                
                document.addEventListener('click', (e) => {
                    if (!trigger.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.remove('show');
                    }
                });
            }
        });
        
        console.log('统一下拉菜单设置完成');
    }
    
    /**
     * 设置刷新按钮
     */
    setupRefreshButton() {
        const refreshBtn = document.querySelector(this.config.refreshBtnSelector);
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => {
                this.tableManager.refresh();
            });
        }
        
        console.log('刷新按钮已设置');
    }
    
    /**
     * 设置批量操作
     */
    setupBulkActions() {
        document.querySelectorAll('[data-action]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const action = btn.dataset.action;
                const selectedRows = this.tableManager.getSelectedRows();
                
                if (selectedRows.length === 0) {
                    alert('请先选择要操作的项目');
                    return;
                }
                
                console.log(`执行批量操作: ${action}，选中项目:`, selectedRows);
                this.handleBulkAction(action, selectedRows);
                
                const bulkActionsDropdown = document.querySelector(this.config.bulkActionsDropdownSelector);
                if (bulkActionsDropdown) {
                    bulkActionsDropdown.classList.remove('show');
                }
            });
        });
        
        console.log('批量操作功能已设置');
    }
    
    /**
     * 处理批量操作 - 可在子类中重写
     */
    handleBulkAction(action, selectedIds) {
        switch(action) {
            case 'enable':
                alert(`启用了 ${selectedIds.length} 个项目`);
                break;
            case 'disable':
                alert(`禁用了 ${selectedIds.length} 个项目`);
                break;
            case 'delete':
                if (confirm(`确定要删除 ${selectedIds.length} 个项目吗？`)) {
                    alert(`删除了 ${selectedIds.length} 个项目`);
                }
                break;
        }
    }
    
    /**
     * 导出数据的全局函数 - 供HTML onclick调用
     */
    exportData(format) {
        console.log(`导出数据，格式: ${format}`);
        this.tableManager.exportData(format);
        
        const exportPopup = document.querySelector(this.config.exportPopupSelector);
        if (exportPopup) {
            exportPopup.classList.remove('show');
        }
    }
}

// ========== GLOBAL EXPORTS ========== 
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
        initializeCharacterCounters
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
    TableOperations: TableOperations,
    TableManager: TableManager,
    CommonTableActions: CommonTableActions
};

window.switchAPI = switchAPI;