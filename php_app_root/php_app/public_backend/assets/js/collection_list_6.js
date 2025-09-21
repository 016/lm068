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
    
    // 5. 自定义批量操作处理逻辑 - 使用通用的TableOperations批量操作方法
    tableActions.handleBulkAction = function(action, selectedIds) {
        console.log(`合集列表页面批量操作: ${action}，选中项目:`, selectedIds);
        
        // 使用TableOperations的通用批量操作方法
        window.AdminCommon.TableOperations.handleBulkAction({
            action: action,
            selectedIds: selectedIds,
            endpoint: '/collections/bulk-action',
            entityName: '合集',
            onSuccess: function(response) {
                // 成功回调：刷新页面保持当前URL格式
                window.location.reload();
            },
            onError: function(errorMessage, response) {
                // 错误回调：使用默认的alert显示
                alert(errorMessage);
            }
        });
    };
    
    // 6. 设置删除按钮的事件监听器 - 使用通用的TableOperations删除功能
    window.AdminCommon.TableOperations.setupDeleteButtonEventListeners({
        tbodySelector: tableManager.config.tbodySelector,
        deleteButtonSelector: '.delete-tag',
        endpoint: '/collections/{id}',
        entityName: '合集',
        tableManager: tableManager
    });
    
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