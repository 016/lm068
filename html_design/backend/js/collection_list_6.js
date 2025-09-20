/**
 * Collection List Page JavaScript v6 - 支持多列tooltip配置数组
 * 基于 collection_list_5.js 优化，tooltipConfig支持多个列的数组配置
 * 
 * 主要修改：
 * - tooltipConfig现在支持数组格式，可配置多个列的tooltip
 * - 向下兼容单个配置对象的格式
 * - 展示了如何为不同列设置不同的tooltip参数
 * 
 * tooltip配置示例：
 * 单个列：tooltipConfig: { selector: '[data-column="description"]', maxLength: 20, placement: 'top' }
 * 多个列：tooltipConfig: [
 *   { selector: '[data-column="description"]', maxLength: 20, placement: 'top' },
 *   { selector: '[data-column="title"]', maxLength: 30, placement: 'bottom' }
 * ]
 */

// ========== 页面初始化 ========== 
document.addEventListener('DOMContentLoaded', function() {
    initCollectionListPage();
});

/**
 * 初始化合集列表页面
 * 使用优化版 TableManager 和 CommonTableActions 进行统一管理
 */
function initCollectionListPage() {
    console.log('=== 初始化合集列表页面（多列tooltip配置版）===');
    
    // 1. 创建表格管理器实例 - 配置适配合集页面
    const tableManager = new window.AdminCommon.TableManager({
        tableSelector: '#dataTable',
        tbodySelector: '#collectionTableBody',  // 合集表格body选择器
        paginationSelector: '#paginationNav',
        itemsPerPageSelector: '#itemsPerPage',
        selectedCountSelector: '#selectedCount',
        selectAllSelector: '#selectAll',
        rowCheckboxSelector: '.row-checkbox',
        columnSettingsPopupSelector: '#columnSettingsPopup',
        currentDisplaySelector: '#currentDisplay',
        defaultItemsPerPage: 2, // 保持与标签页面一致的测试设置
        enableSort: true,
        enablePagination: true,
        enableColumnSettings: true,
        enableSearch: true,
        // v6 新增：多列tooltip配置数组，实现持久化
        tooltipConfig: [
            // 为 description 列设置 tooltip
            {
                selector: '[data-column="description"]',
                maxLength: 20,
                placement: 'top'
            },
            {
                selector: '[data-column="name"]',
                maxLength: 8,
                placement: 'top'
            }
        ]
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
    
    // 5. 自定义批量操作处理逻辑 - 适配合集管理需求
    tableActions.handleBulkAction = function(action, selectedIds) {
        console.log(`合集列表页面批量操作: ${action}，选中项目:`, selectedIds);
        
        switch(action) {
            case 'enable':
                window.AdminCommon.showToast(`开发中-成功启用了 ${selectedIds.length} 个合集`, 'primary');
                // 这里可以添加实际的启用逻辑
                break;
            case 'disable':
                window.AdminCommon.showToast(`开发中-成功禁用了 ${selectedIds.length} 个合集`, 'info');
                // 这里可以添加实际的禁用逻辑
                break;
            case 'delete':
                if (confirm(`确定要删除 ${selectedIds.length} 个合集吗？删除后将无法恢复，相关的内容关联也会被移除。`)) {
                    window.AdminCommon.showToast(`开发中-成功删除了 ${selectedIds.length} 个合集`, 'danger');
                    // 这里可以添加实际的删除逻辑
                }
                break;
        }
    };
    
    // 7. 将实例保存到全局，方便调试和扩展
    window.collectionListManager = {
        tableManager: tableManager,
        tableActions: tableActions
    };
    
    console.log('=== 合集列表页面初始化完成（多列tooltip配置版）===');
}


// ========== 导出函数供HTML调用 ========== 
/**
 * 导出数据 - 供HTML的onclick调用
 * @param {string} format - 导出格式：'json' 或 'csv'
 */
function exportData(format) {
    if (window.collectionListManager && window.collectionListManager.tableActions) {
        window.collectionListManager.tableActions.exportData(format);
    } else {
        console.error('collectionListManager 未初始化');
    }
}


// 确保函数全局可访问，供 HTML onclick 调用
window.exportData = exportData;