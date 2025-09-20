/**
 * Content List Page JavaScript v2 - 基于标签列表页面适配
 * 基于 tag_list_11.js 重构，适配内容管理功能
 * 
 * 主要改进：
 * - 适配内容管理的业务逻辑
 * - 与优化版 TableManager 和 CommonTableActions 配合
 * - 支持内容类型、状态等特定过滤功能
 * - 保持代码精简和可维护性
 */

// ========== 页面初始化 ========== 
document.addEventListener('DOMContentLoaded', function() {
    initContentListPage();
});

/**
 * 初始化内容列表页面
 * 使用优化版 TableManager 和 CommonTableActions 进行统一管理
 */
function initContentListPage() {
    console.log('=== 初始化内容列表页面（优化版 TableManager）===');
    
    // 1. 创建表格管理器实例 - 配置适配内容管理
    const tableManager = new window.AdminCommon.TableManager({
        tableSelector: '#dataTable',
        tbodySelector: '#contentTableBody',
        paginationSelector: '#paginationNav',
        itemsPerPageSelector: '#itemsPerPage',
        selectedCountSelector: '#selectedCount',
        selectAllSelector: '#selectAll',
        rowCheckboxSelector: '.row-checkbox',
        columnSettingsPopupSelector: '#columnSettingsPopup',
        currentDisplaySelector: '#currentDisplay',
        defaultItemsPerPage: 2, // 内容列表默认显示5条
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
    
    // 5. 自定义批量操作处理逻辑 - 适配内容管理
    tableActions.handleBulkAction = function(action, selectedIds) {
        console.log(`内容列表页面批量操作: ${action}，选中项目:`, selectedIds);
        
        switch(action) {
            case 'publish':
                if (confirm(`确定要发布 ${selectedIds.length} 个内容吗？`)) {
                    alert(`发布了 ${selectedIds.length} 个内容`);
                    // 这里可以添加实际的发布逻辑
                    updateContentStatus(selectedIds, 99); // 99表示已发布状态
                }
                break;
            case 'draft':
                if (confirm(`确定要将 ${selectedIds.length} 个内容转为草稿吗？`)) {
                    alert(`将 ${selectedIds.length} 个内容转为草稿`);
                    // 这里可以添加实际的转草稿逻辑
                    updateContentStatus(selectedIds, 1); // 1表示草稿状态
                }
                break;
            case 'delete':
                if (confirm(`确定要删除 ${selectedIds.length} 个内容吗？此操作不可恢复！`)) {
                    alert(`删除了 ${selectedIds.length} 个内容`);
                    // 这里可以添加实际的删除逻辑
                    deleteContent(selectedIds);
                }
                break;
        }
    };
    
    // 7. 将实例保存到全局，方便调试和扩展
    window.contentListManager = {
        tableManager: tableManager,
        tableActions: tableActions
    };
    
    console.log('=== 内容列表页面初始化完成（优化版 TableManager）===');
}

// ========== 内容管理相关功能函数 ==========

/**
 * 更新内容状态
 * @param {Array} contentIds - 内容ID数组
 * @param {number} statusId - 新状态ID
 */
function updateContentStatus(contentIds, statusId) {
    console.log('更新内容状态:', contentIds, '到状态:', statusId);
    
    // 这里添加实际的状态更新逻辑
    // 可以通过AJAX请求后端API
}

/**
 * 删除内容
 * @param {Array} contentIds - 内容ID数组
 */
function deleteContent(contentIds) {
    console.log('删除内容:', contentIds);
    
    // 这里添加实际的删除逻辑
    // 可以通过AJAX请求后端API
}


// ========== 导出函数供HTML调用 ========== 

/**
 * 导出数据 - 供HTML的onclick调用
 * @param {string} format - 导出格式：'json' 或 'csv'
 */
function exportData(format) {
    if (window.contentListManager && window.contentListManager.tableActions) {
        window.contentListManager.tableActions.exportData(format);
    } else {
        console.error('contentListManager 未初始化');
    }
}
// 确保 exportData 全局可访问，供 HTML onclick 调用
window.exportData = exportData;