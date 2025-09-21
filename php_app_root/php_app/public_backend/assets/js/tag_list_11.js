/**
 * Tag List Page JavaScript v11 - 重构版，使用通用TableOperations
 * 基于 tag_list_10.js 重构，配合 main_12.js 的优化使用更精简的代码
 * 
 * 主要改进：
 * - 代码更加精简，减少冗余
 * - 与优化版 TableManager 和 CommonTableActions 配合
 * - 批量操作和删除功能迁移至 main_12.js 的 TableOperations 中实现复用
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
        defaultItemsPerPage: 5, // 保持原有的测试设置
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
    
    // 5. 自定义批量操作处理逻辑 - 使用通用的TableOperations批量操作方法
    tableActions.handleBulkAction = function(action, selectedIds) {
        console.log(`标签列表页面批量操作: ${action}，选中项目:`, selectedIds);
        
        // 使用TableOperations的通用批量操作方法
        window.AdminCommon.TableOperations.handleBulkAction({
            action: action,
            selectedIds: selectedIds,
            endpoint: '/tags/bulk-action',
            entityName: '标签',
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
    
    // 6. 初始化批量导入功能 - 使用默认配置适配标签页面
    if (window.AdminCommon.BulkImportUtils) {
        window.AdminCommon.BulkImportUtils.setupBulkImport(); // 默认配置适配标签页面
        console.log('标签批量导入功能已初始化');
    }
    
    // 7. 设置删除按钮的事件监听器 - 使用通用的TableOperations删除功能
    window.AdminCommon.TableOperations.setupDeleteButtonEventListeners({
        tbodySelector: tableManager.config.tbodySelector,
        deleteButtonSelector: '.delete-tag',
        endpoint: '/tags/{id}',
        entityName: '标签',
        tableManager: tableManager
    });
    
    // 8. 将实例保存到全局，方便调试和扩展
    window.tagListManager = {
        tableManager: tableManager,
        tableActions: tableActions
    };
    
    console.log('=== 标签列表页面初始化完成（优化版 TableManager）===');
}

// ========== 删除按钮功能实现 ========== 
// 注释：原有的删除功能已迁移至 main_12.js 的 TableOperations 中
// 现在使用通用的 TableOperations.setupDeleteButtonEventListeners 和 handleSingleDelete 方法

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