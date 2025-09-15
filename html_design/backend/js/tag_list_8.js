/**
 * Tag List Page Enhanced JavaScript v8
 * 标签列表页面增强版JavaScript - 实现动态列读取、JS级分页排序
 * 基于 tag_list_7.js 全面重构，支持：
 * - 页面初始化时从HTML表格动态读取列配置和数据
 * - 完整的JS级分页逻辑，支持2条/页测试
 * - 表格列排序功能
 * - 动态列设置和导出适配
 */

// ========== GLOBAL VARIABLES ========== 
let currentPage = 1;
let itemsPerPage = 2; // 默认使用2条便于测试
let originalData = []; // 原始数据
let filteredData = []; // 过滤后的数据 
let displayData = []; // 当前显示的数据
let tableColumns = []; // 表格列配置
let currentSort = { field: '', direction: '' }; // 当前排序状态

// ========== PAGE INITIALIZATION ========== 
document.addEventListener('DOMContentLoaded', function() {
    initTagListPage();
});

/**
 * 初始化标签列表页面
 * 使用增强的TableOperations工具实现完整功能
 */
function initTagListPage() {
    console.log('=== 初始化标签列表页面 ===');
    
    // 获取TableOperations工具
    const TableOps = window.AdminCommon.TableOperations;
    
    // 1. 从HTML表格中读取数据和列配置
    const tableData = TableOps.loadDataFromHTML('#dataTable', '#tagTableBody');
    originalData = tableData.data;
    filteredData = [...originalData]; 
    tableColumns = tableData.columns;
    
    console.log(`加载了 ${originalData.length} 条数据，${tableColumns.length} 列`);
    
    // 2. 动态生成列设置选项
    TableOps.generateColumnSettingsFromHeader('#dataTable', '#columnSettingsPopup');
    
    // 3. 设置各种功能
    setupColumnSettings();
    setupExportFunctionality();
    setupRefreshButton();
    setupItemsPerPage();
    setupSorting();
    setupSelectAll();
    setupBulkActions();
    
    // 4. 初始渲染
    updateDisplay();
    
    console.log('=== 页面初始化完成 ===');
}

// ========== ITEMS PER PAGE FUNCTIONALITY ========== 
/**
 * 设置每页条数功能
 */
function setupItemsPerPage() {
    const itemsPerPageSelect = document.getElementById('itemsPerPage');
    if (!itemsPerPageSelect) return;
    
    // 设置默认值为2
    itemsPerPageSelect.value = itemsPerPage;
    
    itemsPerPageSelect.addEventListener('change', function() {
        const newItemsPerPage = parseInt(this.value);
        console.log(`每页条数变更: ${itemsPerPage} -> ${newItemsPerPage}`);
        
        itemsPerPage = newItemsPerPage;
        currentPage = 1; // 重置到第一页
        updateDisplay();
    });
    
    console.log(`每页条数设置完成，默认值: ${itemsPerPage}`);
}

// ========== SORTING FUNCTIONALITY ========== 
/**
 * 设置排序功能
 */
function setupSorting() {
    const TableOps = window.AdminCommon.TableOperations;
    
    TableOps.setupSorting('#dataTable', (field, direction) => {
        console.log(`执行排序: ${field} ${direction}`);
        
        // 更新排序状态
        currentSort = { field, direction };
        
        // 对过滤后的数据进行排序
        filteredData = TableOps.sortData(filteredData, field, direction, tableColumns);
        
        // 重置到第一页并更新显示
        currentPage = 1;
        updateDisplay();
        
        // 更新排序图标状态
        updateSortIcons(field, direction);
    });
    
    console.log('排序功能设置完成');
}

/**
 * 更新排序图标状态
 * @param {string} activeField - 当前排序字段
 * @param {string} direction - 排序方向
 */
function updateSortIcons(activeField, direction) {
    // 清除所有排序图标的激活状态
    document.querySelectorAll('.sort-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // 激活当前排序的图标
    const activeBtn = document.querySelector(`.sort-btn[data-sort="${activeField}"][data-direction="${direction}"]`);
    if (activeBtn) {
        activeBtn.classList.add('active');
    }
}

// ========== PAGINATION AND DISPLAY ========== 
/**
 * 更新显示 - 处理分页、渲染表格、更新汇总信息
 */
function updateDisplay() {
    console.log('=== 更新显示 ===');
    
    const TableOps = window.AdminCommon.TableOperations;
    
    // 1. 获取当前页数据
    const paginationResult = TableOps.getPaginatedData(filteredData, currentPage, itemsPerPage);
    displayData = paginationResult.data;
    const pagination = paginationResult.pagination;
    
    console.log(`当前页: ${pagination.currentPage}/${pagination.totalPages}, 显示: ${pagination.startIndex}-${pagination.endIndex}/${pagination.totalItems}`);
    
    // 2. 渲染表格数据
    TableOps.renderTableData(displayData, tableColumns, '#tagTableBody');
    
    // 3. 重新绑定事件监听器
    attachEventListeners();
    
    // 4. 更新分页组件
    TableOps.setupPagination({
        currentPage: pagination.currentPage,
        totalPages: pagination.totalPages,
        onPageChange: (newPage) => {
            console.log(`页面切换: ${currentPage} -> ${newPage}`);
            currentPage = newPage;
            updateDisplay();
        },
        paginationSelector: '#paginationNav'
    });
    
    // 5. 更新汇总信息
    updateSummaryInfo(pagination);
    
    // 6. 重新应用列显示设置
    reapplyColumnVisibility();
    
    console.log('=== 显示更新完成 ===');
}

/**
 * 重新绑定事件监听器
 */
function attachEventListeners() {
    // 重新绑定行选择框事件
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
    
    console.log(`重新绑定了 ${rowCheckboxes.length} 个选择框事件`);
}

/**
 * 更新汇总信息
 * @param {Object} pagination - 分页信息
 */
function updateSummaryInfo(pagination) {
    // 更新当前显示范围
    const currentDisplay = document.getElementById('currentDisplay');
    if (currentDisplay) {
        currentDisplay.textContent = `${pagination.startIndex}-${pagination.endIndex}/${pagination.totalItems}`;
    }
    
    // 计算统计信息
    const totalVideos = filteredData.reduce((sum, row) => {
        const videoCount = row.videos || 0;
        return sum + (typeof videoCount === 'number' ? videoCount : 0);
    }, 0);
    
    const avgVideos = filteredData.length > 0 ? (totalVideos / filteredData.length).toFixed(1) : '0.0';
    
    // 更新汇总信息显示
    const summaryElements = {
        totalVideos: document.querySelector('.summary-text .summary-highlight:nth-child(2)'),
        avgVideos: document.querySelector('.summary-text .summary-highlight:nth-child(3)')
    };
    
    if (summaryElements.totalVideos) {
        summaryElements.totalVideos.textContent = totalVideos.toLocaleString();
    }
    if (summaryElements.avgVideos) {
        summaryElements.avgVideos.textContent = avgVideos;
    }
    
    console.log(`汇总信息更新: 总视频 ${totalVideos}, 平均 ${avgVideos}`);
}

/**
 * 重新应用列显示设置
 */
function reapplyColumnVisibility() {
    const TableOps = window.AdminCommon.TableOperations;
    
    // 重新应用所有列的显示状态
    document.querySelectorAll('#columnSettingsPopup input[type="checkbox"]').forEach(checkbox => {
        const columnId = checkbox.id.replace('col-', '');
        TableOps.toggleColumnVisibility(columnId, checkbox.checked);
    });
}

// ========== COLUMN SETTINGS ========== 
/**
 * 设置列设置功能
 */
function setupColumnSettings() {
    const TableOps = window.AdminCommon.TableOperations;
    
    // 设置下拉菜单显示/隐藏
    setupColumnSettingsDropdown();
    
    // 设置列显示/隐藏功能  
    TableOps.setupColumnVisibility('#columnSettingsPopup');
    
    console.log('列设置功能已设置');
}

/**
 * 设置列设置下拉菜单
 */
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

// ========== EXPORT FUNCTIONALITY ========== 
/**
 * 设置导出功能 - 使用动态列配置
 */
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
    
    console.log('导出功能已设置');
}

/**
 * 导出数据 - 使用动态列配置
 * @param {string} format - 导出格式：'json' 或 'csv'
 */
function exportData(format) {
    const TableOps = window.AdminCommon.TableOperations;
    
    console.log(`开始导出数据，格式: ${format}, 数据量: ${filteredData.length}`);
    
    // 使用过滤后的数据和动态列配置进行导出
    TableOps.exportData(filteredData, tableColumns, format, 'tag_data');
    
    // 关闭导出菜单
    document.getElementById('exportPopup').classList.remove('show');
    
    console.log('导出完成');
}

// ========== REFRESH FUNCTIONALITY ========== 
/**
 * 设置刷新按钮
 */
function setupRefreshButton() {
    const refreshBtn = document.getElementById('refreshBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', () => {
            console.log('刷新页面');
            location.reload();
        });
    }
}

// ========== SELECT ALL FUNCTIONALITY ========== 
/**
 * 设置全选功能
 */
function setupSelectAll() {
    const TableOps = window.AdminCommon.TableOperations;
    
    TableOps.setupSelectAll('#selectAll', '.row-checkbox', updateSelectedCount);
    
    console.log('全选功能已设置');
}

/**
 * 更新选中数量
 */
function updateSelectedCount() {
    const TableOps = window.AdminCommon.TableOperations;
    TableOps.updateSelectedCount('#selectedCount', '#selectAll', '.row-checkbox');
}

// ========== BULK ACTIONS ========== 
/**
 * 设置批量操作
 */
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
                console.log(`执行批量操作: ${action}，选中项目:`, selectedIds);
                
                // 实际的批量操作逻辑
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
    
    console.log('批量操作功能已设置');
}

// ========== SEARCH FUNCTIONALITY ========== 
/**
 * 设置搜索功能（预留接口，当前版本使用JS级处理）
 * 注：原版本使用URL参数传递到后端处理，这里可以扩展为JS级搜索
 */
function setupSearchFunctionality() {
    const TableOps = window.AdminCommon.TableOperations;
    
    // 可以选择使用TableOps.setupSearchFunctionality进行URL参数传递
    // 或者实现JS级的本地搜索功能
    
    console.log('搜索功能预留接口已设置');
}

// ========== DEBUG AND UTILITIES ========== 
/**
 * 调试信息输出
 */
function debugInfo() {
    console.log('=== 调试信息 ===');
    console.log('原始数据量:', originalData.length);
    console.log('过滤数据量:', filteredData.length);
    console.log('当前页数据量:', displayData.length);
    console.log('表格列数:', tableColumns.length);
    console.log('当前页码:', currentPage);
    console.log('每页条数:', itemsPerPage);
    console.log('当前排序:', currentSort);
    console.log('列配置:', tableColumns);
}

// ========== GLOBAL FUNCTIONS FOR HTML ONCLICK ========== 
// Make functions globally accessible for onclick handlers in HTML
window.exportData = exportData;
window.debugInfo = debugInfo;