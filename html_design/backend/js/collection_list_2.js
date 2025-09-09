/**
 * Collection List Page Specific JavaScript
 * 合集列表页面专用JavaScript - 基于标签列表页面功能适配
 */

// ========== PAGE SPECIFIC DATA ========== 
// Sample data for the collection table (simulate more data than shown)
const allTableData = [
    { id: '#001', name: '科技前沿系列', videos: 56, status: 'active', statusText: '显示' },
    { id: '#002', name: '音乐精选合集', videos: 89, status: 'active', statusText: '显示' },
    { id: '#003', name: '游戏攻略大全', videos: 124, status: 'inactive', statusText: '隐藏' },
    { id: '#004', name: '美食制作教程', videos: 45, status: 'active', statusText: '显示' },
    { id: '#005', name: '旅行风景记录', videos: 78, status: 'active', statusText: '显示' },
    { id: '#006', name: '健身训练指导', videos: 67, status: 'active', statusText: '显示' },
    { id: '#007', name: '编程学习路径', videos: 92, status: 'inactive', statusText: '隐藏' },
    { id: '#008', name: '时尚搭配指南', videos: 34, status: 'active', statusText: '显示' },
    { id: '#009', name: '摄影技巧分享', videos: 58, status: 'active', statusText: '显示' },
    { id: '#010', name: '动漫番剧推荐', videos: 156, status: 'active', statusText: '显示' }
];

let currentPage = 1;
let itemsPerPage = 5;
let filteredData = [...allTableData];

// ========== PAGE INITIALIZATION ========== 
document.addEventListener('DOMContentLoaded', function() {
    initCollectionListPage();
});

function initCollectionListPage() {
    setupExportFunctionality();
    setupRefreshButton();
    setupColumnSettings();
    setupTableFunctionality();
    setupBulkActions();
    
    // Initialize table display
    renderTable();
    renderPagination();
    updateSelectedCount();
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
        a.download = `collection_data_${new Date().toISOString().split('T')[0]}.json`;
        a.click();
        URL.revokeObjectURL(url);
    } else if (format === 'csv') {
        const csvHeader = 'ID,合集名称,关联视频,状态\n';
        const csvData = data.map(row => 
            `${row.id},"${row.name}",${row.videos},"${row.statusText}"`
        ).join('\n');
        const csvContent = csvHeader + csvData;
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `collection_data_${new Date().toISOString().split('T')[0]}.csv`;
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
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const pageData = filteredData.slice(startIndex, endIndex);
    
    const tbody = document.getElementById('collectionTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    pageData.forEach((row, index) => {
        const tr = document.createElement('tr');
        tr.className = 'table-row';
        tr.setAttribute('data-id', index + 1);
        
        const statusBadgeClass = row.status === 'active' ? 'badge-success' : 'badge-danger';
        
        tr.innerHTML = `
            <td class="table-cell">
                <div class="form-check">
                    <input class="form-check-input row-checkbox" type="checkbox" value="${index + 1}">
                </div>
            </td>
            <td class="table-cell table-id" data-column="id">${row.id}</td>
            <td class="table-cell table-name" data-column="name">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-collection" style="color: var(--accent-primary);"></i>
                    <span>${row.name}</span>
                </div>
            </td>
            <td class="table-cell" data-column="videos">
                <div class="collection-video-count">
                    <span class="table-count-primary">${row.videos}</span>
                    <span class="table-count-muted">个视频</span>
                </div>
            </td>
            <td class="table-cell" data-column="status">
                <span class="badge rounded-pill ${statusBadgeClass}">
                    <i class="bi bi-circle-fill badge-icon"></i> ${row.statusText}
                </span>
            </td>
            <td class="table-cell table-actions" data-column="actions">
                <div class="d-flex gap-2 justify-content-center">
                    <a href="#" class="btn btn-outline-primary btn-sm" title="编辑">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <a href="#" class="btn btn-outline-info btn-sm" title="查看">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="#" class="btn btn-outline-danger btn-sm" title="删除">
                        <i class="bi bi-trash"></i>
                    </a>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
    
    // Re-attach event listeners for new checkboxes
    attachCheckboxListeners();
    updateCurrentDisplay();
    
    // Re-apply column visibility settings after rendering new rows
    applyColumnVisibility();
}

// Function to apply column visibility to all rows including new ones
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
                    alert('请先选择要操作的合集');
                    return;
                }
                
                const selectedIds = Array.from(selectedRows).map(cb => cb.value);
                console.log(`Performing ${action} on collections:`, selectedIds);
                
                // Here you would implement the actual bulk action logic
                switch(action) {
                    case 'enable':
                        alert(`启用了 ${selectedIds.length} 个合集`);
                        break;
                    case 'disable':
                        alert(`禁用了 ${selectedIds.length} 个合集`);
                        break;
                    case 'delete':
                        if (confirm(`确定要删除 ${selectedIds.length} 个合集吗？`)) {
                            alert(`删除了 ${selectedIds.length} 个合集`);
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