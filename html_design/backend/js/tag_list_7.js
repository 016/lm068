/**
 * Tag List Page Specific JavaScript v7
 * 标签列表页面专用JavaScript - 使用共享的TableOperations工具
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
    // 使用共享的表格操作工具
    const TableOps = window.AdminCommon.TableOperations;
    
    loadTableDataFromHTML();
    
    // 使用共享工具生成列设置
    TableOps.generateColumnSettingsFromHeader('table', '#columnSettingsPopup');
    
    setupExportFunctionality();
    setupRefreshButton();
    
    // 使用共享工具设置列设置功能
    setupColumnSettingsDropdown();
    TableOps.setupColumnVisibility('#columnSettingsPopup');
    
    // 使用共享工具设置搜索功能
    TableOps.setupSearchFunctionality('table');
    
    // 使用共享工具设置表格功能
    setupTableFunctionality();
    setupBulkActions();
    
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
function setupColumnSettingsDropdown() {
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
}

// ========== TABLE FUNCTIONALITY ========== 
function setupTableFunctionality() {
    const TableOps = window.AdminCommon.TableOperations;
    
    // 使用共享工具设置全选功能
    TableOps.setupSelectAll('#selectAll', '.row-checkbox', updateSelectedCount);
    
    // Items per page functionality
    const itemsPerPageSelect = document.getElementById('itemsPerPage');
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
    
    // Re-apply column visibility settings using shared tool
    const TableOps = window.AdminCommon.TableOperations;
    document.querySelectorAll('#columnSettingsPopup input[type="checkbox"]').forEach(checkbox => {
        const columnId = checkbox.id.replace('col-', '');
        TableOps.toggleColumnVisibility(columnId, checkbox.checked);
    });
}

function attachCheckboxListeners() {
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
}

function updateSelectedCount() {
    // 使用共享工具更新选中数量
    const TableOps = window.AdminCommon.TableOperations;
    TableOps.updateSelectedCount('#selectedCount', '#selectAll', '.row-checkbox');
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
    const TableOps = window.AdminCommon.TableOperations;
    
    // 使用共享工具渲染分页
    TableOps.setupPagination({
        currentPage: currentPage,
        totalPages: totalPages,
        onPageChange: (newPage) => {
            currentPage = newPage;
            renderTable();
            renderPagination();
        },
        paginationSelector: '#paginationNav'
    });
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