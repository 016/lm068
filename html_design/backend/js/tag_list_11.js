/**
 * Tag List Page JavaScript v11 - 优化版，使用精简表格管理器
 * 基于 tag_list_10.js 重构，配合 main_7.js 的优化使用更精简的代码
 * 
 * 主要改进：
 * - 代码更加精简，减少冗余
 * - 与优化版 TableManager 和 CommonTableActions 配合
 * - 保持所有原有功能不变，逻辑保持一致
 * - 提高代码可读性和维护性
 */

// ========== 页面初始化 ========== 
document.addEventListener('DOMContentLoaded', function() {
    initTagListPage();
});

/**
 * 初始化标签列表页面
 * 使用优化版 TableManager 和 CommonTableActions 进行统一管理
 */
function initTagListPage() {
    console.log('=== 初始化标签列表页面（优化版 TableManager）===');
    
    // 1. 创建表格管理器实例 - 配置保持与原版一致
    const tableManager = new window.AdminCommon.TableManager({
        tableSelector: '#dataTable',
        tbodySelector: '#tagTableBody',
        paginationSelector: '#paginationNav',
        itemsPerPageSelector: '#itemsPerPage',
        selectedCountSelector: '#selectedCount',
        selectAllSelector: '#selectAll',
        rowCheckboxSelector: '.row-checkbox',
        columnSettingsPopupSelector: '#columnSettingsPopup',
        currentDisplaySelector: '#currentDisplay',
        defaultItemsPerPage: 2, // 保持原有的测试设置
        enableSort: true,
        enablePagination: true,
        enableColumnSettings: true,
        enableSearch: true
    });
    
    // 2. 初始化表格管理器
    tableManager.init();
    
    // 3. 创建通用操作功能实例
    const tableActions = new window.AdminCommon.CommonTableActions(tableManager, {
        exportBtnSelector: '#exportBtn',
        exportPopupSelector: '#exportPopup',
        refreshBtnSelector: '#refreshBtn',
        columnSettingsBtnSelector: '#columnSettingsBtn',
        columnSettingsPopupSelector: '#columnSettingsPopup',
        bulkActionsBtnSelector: '#bulkActionsBtn',
        bulkActionsDropdownSelector: '#bulkActionsDropdown'
    });
    
    // 4. 初始化操作功能
    tableActions.init();
    
    // 5. 自定义批量操作处理逻辑 - 保持与原版一致
    tableActions.handleBulkAction = function(action, selectedIds) {
        console.log(`标签列表页面批量操作: ${action}，选中项目:`, selectedIds);

        switch(action) {
            case 'enable':
                window.AdminCommon.showToast(`开发中-启用了 ${selectedIds.length} 个标签`, 'primary');
                break;
            case 'disable':
                window.AdminCommon.showToast(`开发中-禁用了 ${selectedIds.length} 个标签`, 'info');
                break;
            case 'delete':
                if (confirm(`确定要删除 ${selectedIds.length} 个标签吗？`)) {
                    window.AdminCommon.showToast(`开发中-删除了 ${selectedIds.length} 个标签`, 'danger');
                    // 这里可以添加实际的删除逻辑
                }
                break;
        }
    };
    
    // 6. 将实例保存到全局，方便调试和扩展
    window.tagListManager = {
        tableManager: tableManager,
        tableActions: tableActions
    };
    
    console.log('=== 标签列表页面初始化完成（优化版 TableManager）===');
}

// ========== 导出函数供HTML调用 ========== 
/**
 * 导出数据 - 供HTML的onclick调用
 * @param {string} format - 导出格式：'json' 或 'csv'
 */
function exportData(format) {
    if (window.tagListManager && window.tagListManager.tableActions) {
        window.tagListManager.tableActions.exportData(format);
    } else {
        console.error('tagListManager 未初始化');
    }
}
// 确保 exportData 全局可访问，供 HTML onclick 调用
window.exportData = exportData;