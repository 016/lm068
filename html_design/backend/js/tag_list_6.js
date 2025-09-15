/**
 * Tag List Page Specific JavaScript v6
 * 标签列表页面专用JavaScript - 从HTML表格中读取数据，动态生成列设置
 */

// ========== GLOBAL VARIABLES ========== 
let currentPage = 1;
let itemsPerPage = 5;
let filteredData = [];
let allTableData = [];

// ========== PAGE INITIALIZATION ========== 
document.addEventListener('DOMContentLoaded', function() {
    initTagListPage();
});

function initTagListPage() {
    loadTableDataFromHTML();
    generateColumnSettingsFromHeader();
    setupExportFunctionality();
    setupRefreshButton();
    setupColumnSettings();
    setupTableFunctionality();
    setupBulkActions();
    setupSearchFunctionality();
    
    // Initialize table display
    renderTable();
    renderPagination();
    updateSelectedCount();
}

// ========== READ DATA FROM HTML TABLE ========== 
/**
 * 从HTML表格中读取现有数据作为数据源
 * 这样PHP渲染的数据可以被JavaScript处理
 */
function loadTableDataFromHTML() {
    const tableRows = document.querySelectorAll('#tagTableBody tr.table-row');
    allTableData = [];
    
    tableRows.forEach((row, index) => {
        const idCell = row.querySelector('[data-column="id"]');
        const nameCell = row.querySelector('[data-column="name"]');
        const videosCell = row.querySelector('[data-column="videos"] .table-count-primary');
        const statusBadge = row.querySelector('[data-column="status"] .badge');
        
        if (idCell && nameCell && videosCell && statusBadge) {
            const id = idCell.textContent.trim();
            const name = nameCell.textContent.trim();
            const videos = parseInt(videosCell.textContent.replace(/,/g, ''));
            const statusText = statusBadge.textContent.trim().split(' ').pop(); // 获取"显示"或"隐藏"
            const status = statusBadge.classList.contains('badge-success') ? 'active' : 'inactive';
            
            allTableData.push({
                id: id,
                name: name,
                videos: videos,
                status: status,
                statusText: statusText
            });
        }
    });
    
    filteredData = [...allTableData];
    console.log('Loaded table data from HTML:', allTableData);
}

// ========== DYNAMIC COLUMN SETTINGS ========== 
/**
 * 从表格header中动态生成列设置选项
 * 这样添加新列时不需要手动更新列设置
 */
function generateColumnSettingsFromHeader() {
    const headerCells = document.querySelectorAll('.table-header th[data-column]');
    const columnSettingsPopup = document.getElementById('columnSettingsPopup');
    
    if (!columnSettingsPopup) return;
    
    // 清空现有的列设置选项
    columnSettingsPopup.innerHTML = '';
    
    headerCells.forEach(headerCell => {
        const columnId = headerCell.getAttribute('data-column');
        
        // 跳过checkbox列
        if (columnId === 'checkbox') return;
        
        // 获取列名称
        let columnName = '';
        if (columnId === 'actions') {
            columnName = '操作';
        } else {
            // 从header cell中获取文本，排除排序图标
            const textNodes = Array.from(headerCell.childNodes).filter(node => 
                node.nodeType === Node.TEXT_NODE || 
                (node.nodeType === Node.ELEMENT_NODE && !node.classList.contains('d-flex'))
            );
            if (textNodes.length > 0) {
                columnName = textNodes[0].textContent.trim();
            } else {
                // 如果没有直接文本节点，从.d-flex中获取第一个文本内容
                const flexDiv = headerCell.querySelector('.d-flex');
                if (flexDiv) {
                    columnName = flexDiv.textContent.trim().split('\n')[0].trim();
                }
            }
        }
        
        if (!columnName) columnName = columnId; // 备用方案
        
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
}

// ========== SEARCH FUNCTIONALITY ========== 
/**
 * 设置搜索功能：回车键触发搜索，下拉选择后自动搜索
 * 搜索参数通过URL $_GET 传递给PHP处理
 */
function setupSearchFunctionality() {
    const filterInputs = document.querySelectorAll('.table-filter-cell input[type="text"]');
    const filterSelects = document.querySelectorAll('.table-filter-cell select');
    
    // 为文本输入框添加回车键监听
    filterInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                applyFilters();
            }
        });
    });
    
    // 为下拉选择框添加change监听
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            applyFilters();
        });
    });
}

/**
 * 应用筛选器 - 将筛选条件作为GET参数传递到URL
 */
function applyFilters() {
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
}

// ========== EXPORT FUNCTIONALITY ========== 
function setupExportFunctionality() {
    const exportBtn = document.getElementById('exportBtn');
    const exportPopup = document.getElementById('exportPopup');
    
    if (!exportBtn || !exportPopup) return;
    
    exportBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        exportPopup.classList.toggle('show');
    });
    
    document.addEventListener('click', (e) => {
        if (!exportBtn.contains(e.target) && !exportPopup.contains(e.target)) {
            exportPopup.classList.remove('show');
        }
    });
}

// Export data function
function exportData(format) {
    const data = filteredData;
    
    if (format === 'json') {
        const jsonData = JSON.stringify(data, null, 2);
        const blob = new Blob([jsonData], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `tag_data_${new Date().toISOString().split('T')[0]}.json`;
        a.click();
        URL.revokeObjectURL(url);
    } else if (format === 'csv') {
        const csvHeader = 'ID,标签名称,关联视频,状态\n';
        const csvData = data.map(row => 
            `${row.id},"${row.name}",${row.videos},"${row.statusText}"`
        ).join('\n');
        const csvContent = csvHeader + csvData;
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `tag_data_${new Date().toISOString().split('T')[0]}.csv`;
        a.click();
        URL.revokeObjectURL(url);
    }
    
    document.getElementById('exportPopup').classList.remove('show');
}

// ========== REFRESH FUNCTIONALITY ========== 
function setupRefreshButton() {
    const refreshBtn = document.getElementById('refreshBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', () => {
            location.reload();
        });
    }
}

// ========== COLUMN SETTINGS ========== 
function setupColumnSettings() {
    const columnSettingsBtn = document.getElementById('columnSettingsBtn');
    const columnSettingsPopup = document.getElementById('columnSettingsPopup');
    
    if (!columnSettingsBtn || !columnSettingsPopup) return;
    
    columnSettingsBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        columnSettingsPopup.classList.toggle('show');
    });
    
    document.addEventListener('click', (e) => {
        if (!columnSettingsBtn.contains(e.target) && !columnSettingsPopup.contains(e.target)) {
            columnSettingsPopup.classList.remove('show');
        }
    });
    
    // Column visibility control
    document.querySelectorAll('#columnSettingsPopup input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            const columnId = checkbox.id.replace('col-', '');
            
            // Get all elements with the data-column attribute (header cells and filter cells)
            const columns = document.querySelectorAll(`[data-column="${columnId}"]`);
            
            columns.forEach(col => {
                if (checkbox.checked) {
                    col.style.display = '';
                } else {
                    col.style.display = 'none';
                }
            });
            
            // Special handling for action buttons in data rows
            if (columnId === 'actions') {
                document.querySelectorAll('.table-actions').forEach(cell => {
                    cell.style.display = checkbox.checked ? '' : 'none';
                });
            }
            
            // Special handling for data cells in body rows that don't have data-column
            if (columnId === 'id') {
                document.querySelectorAll('.table-id').forEach(cell => {
                    cell.style.display = checkbox.checked ? '' : 'none';
                });
            } else if (columnId === 'name') {
                document.querySelectorAll('.table-name').forEach(cell => {
                    cell.style.display = checkbox.checked ? '' : 'none';
                });
            }
        });
    });
}

// ========== TABLE FUNCTIONALITY ========== 
function setupTableFunctionality() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const itemsPerPageSelect = document.getElementById('itemsPerPage');
    
    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            updateSelectedCount();
        });
    }
    
    // Items per page functionality
    if (itemsPerPageSelect) {
        itemsPerPageSelect.addEventListener('change', () => {
            itemsPerPage = parseInt(itemsPerPageSelect.value);
            currentPage = 1;
            renderTable();
            renderPagination();
        });
    }
    
    // Sort functionality
    document.querySelectorAll('.sort-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const sortField = this.dataset.sort;
            const sortDirection = this.dataset.direction;
            console.log(`Sorting by ${sortField} in ${sortDirection} direction`);
            // Here you would implement the actual sorting logic
        });
    });
}

function renderTable() {
    // 当前使用HTML中的数据，不需要重新渲染
    // 这个函数保留用于未来的分页和筛选功能
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const pageData = filteredData.slice(startIndex, endIndex);
    
    console.log(`Rendering page ${currentPage}, showing items ${startIndex + 1}-${Math.min(endIndex, filteredData.length)} of ${filteredData.length}`);
    
    // Re-attach event listeners for existing checkboxes
    attachCheckboxListeners();
    updateCurrentDisplay();
    
    // Re-apply column visibility settings
    applyColumnVisibility();
}

// Function to apply column visibility to all rows including existing ones
function applyColumnVisibility() {
    document.querySelectorAll('#columnSettingsPopup input[type="checkbox"]').forEach(checkbox => {
        const columnId = checkbox.id.replace('col-', '');
        const isVisible = checkbox.checked;
        
        // Apply to header and filter cells
        const columns = document.querySelectorAll(`[data-column="${columnId}"]`);
        columns.forEach(col => {
            col.style.display = isVisible ? '' : 'none';
        });
        
        // Apply to data cells in body rows
        if (columnId === 'actions') {
            document.querySelectorAll('.table-actions').forEach(cell => {
                cell.style.display = isVisible ? '' : 'none';
            });
        } else if (columnId === 'id') {
            document.querySelectorAll('.table-id').forEach(cell => {
                cell.style.display = isVisible ? '' : 'none';
            });
        } else if (columnId === 'name') {
            document.querySelectorAll('.table-name').forEach(cell => {
                cell.style.display = isVisible ? '' : 'none';
            });
        }
    });
}

function attachCheckboxListeners() {
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
}

function updateSelectedCount() {
    const selectedRows = document.querySelectorAll('.row-checkbox:checked');
    const selectedCountSpan = document.getElementById('selectedCount');
    const selectAllCheckbox = document.getElementById('selectAll');
    
    if (selectedCountSpan) {
        selectedCountSpan.textContent = selectedRows.length;
    }
    
    // Update select all checkbox state
    if (selectAllCheckbox) {
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
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

function updateCurrentDisplay() {
    const startIndex = (currentPage - 1) * itemsPerPage + 1;
    const endIndex = Math.min(currentPage * itemsPerPage, filteredData.length);
    const currentDisplay = document.getElementById('currentDisplay');
    if (currentDisplay) {
        currentDisplay.textContent = `${startIndex}-${endIndex}/${filteredData.length}`;
    }
}

// ========== PAGINATION FUNCTIONALITY ========== 
function renderPagination() {
    const totalPages = Math.ceil(filteredData.length / itemsPerPage);
    const paginationNav = document.getElementById('paginationNav');
    if (!paginationNav) return;
    
    paginationNav.innerHTML = '';
    
    // Previous button
    const prevBtn = document.createElement('li');
    prevBtn.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
    prevBtn.innerHTML = `<a class="page-link" href="#" aria-label="Previous"><i class="bi bi-chevron-left"></i></a>`;
    prevBtn.addEventListener('click', (e) => {
        e.preventDefault();
        if (currentPage > 1) {
            currentPage--;
            renderTable();
            renderPagination();
        }
    });
    paginationNav.appendChild(prevBtn);
    
    // Page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    if (startPage > 1) {
        const firstPage = document.createElement('li');
        firstPage.className = 'page-item';
        firstPage.innerHTML = '<a class="page-link" href="#" data-page="1">1</a>';
        firstPage.addEventListener('click', (e) => {
            e.preventDefault();
            currentPage = 1;
            renderTable();
            renderPagination();
        });
        paginationNav.appendChild(firstPage);
        
        if (startPage > 2) {
            const ellipsis = document.createElement('li');
            ellipsis.className = 'page-item disabled';
            ellipsis.innerHTML = '<span class="page-link">...</span>';
            paginationNav.appendChild(ellipsis);
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        const pageBtn = document.createElement('li');
        pageBtn.className = `page-item ${i === currentPage ? 'active' : ''}`;
        pageBtn.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
        pageBtn.addEventListener('click', (e) => {
            e.preventDefault();
            currentPage = i;
            renderTable();
            renderPagination();
        });
        paginationNav.appendChild(pageBtn);
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            const ellipsis = document.createElement('li');
            ellipsis.className = 'page-item disabled';
            ellipsis.innerHTML = '<span class="page-link">...</span>';
            paginationNav.appendChild(ellipsis);
        }
        
        const lastPage = document.createElement('li');
        lastPage.className = 'page-item';
        lastPage.innerHTML = `<a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>`;
        lastPage.addEventListener('click', (e) => {
            e.preventDefault();
            currentPage = totalPages;
            renderTable();
            renderPagination();
        });
        paginationNav.appendChild(lastPage);
    }
    
    // Next button
    const nextBtn = document.createElement('li');
    nextBtn.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
    nextBtn.innerHTML = `<a class="page-link" href="#" aria-label="Next"><i class="bi bi-chevron-right"></i></a>`;
    nextBtn.addEventListener('click', (e) => {
        e.preventDefault();
        if (currentPage < totalPages) {
            currentPage++;
            renderTable();
            renderPagination();
        }
    });
    paginationNav.appendChild(nextBtn);
}

// ========== BULK ACTIONS ========== 
function setupBulkActions() {
    const bulkActionsBtn = document.getElementById('bulkActionsBtn');
    const bulkActionsDropdown = document.getElementById('bulkActionsDropdown');
    
    if (bulkActionsBtn && bulkActionsDropdown) {
        // Setup dropdown functionality using common dropdown setup
        window.AdminCommon.setupDropdown('bulkActionsBtn', 'bulkActionsDropdown');
        
        // Bulk action handlers
        document.querySelectorAll('[data-action]').forEach(btn => {
            btn.addEventListener('click', function() {
                const action = this.dataset.action;
                const selectedRows = document.querySelectorAll('.row-checkbox:checked');
                
                if (selectedRows.length === 0) {
                    alert('请先选择要操作的项目');
                    return;
                }
                
                const selectedIds = Array.from(selectedRows).map(cb => cb.value);
                console.log(`Performing ${action} on items:`, selectedIds);
                
                // Here you would implement the actual bulk action logic
                switch(action) {
                    case 'enable':
                        alert(`启用了 ${selectedIds.length} 个标签`);
                        break;
                    case 'disable':
                        alert(`禁用了 ${selectedIds.length} 个标签`);
                        break;
                    case 'delete':
                        if (confirm(`确定要删除 ${selectedIds.length} 个标签吗？`)) {
                            alert(`删除了 ${selectedIds.length} 个标签`);
                        }
                        break;
                }
                
                bulkActionsDropdown.classList.remove('show');
            });
        });
    }
}

// ========== GLOBAL FUNCTIONS FOR HTML ONCLICK ========== 
// Make functions globally accessible for onclick handlers in HTML
window.exportData = exportData;