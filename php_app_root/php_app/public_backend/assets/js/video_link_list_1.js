/**
 * Video Link List Page JavaScript v1 - 基于内容列表页面适配
 * 基于 content_list_2.js 重构，适配视频链接管理功能
 *
 * 主要改进：
 * - 适配视频链接管理的业务逻辑
 * - 与优化版 TableManager 和 CommonTableActions 配合
 * - 支持平台、状态等特定过滤功能
 * - 保持代码精简和可维护性
 */

// ========== 页面初始化 ==========
document.addEventListener('DOMContentLoaded', function() {
    initVideoLinkListPage();
});

/**
 * 初始化视频链接列表页面
 * 使用优化版 TableManager 和 CommonTableActions 进行统一管理
 */
function initVideoLinkListPage() {
    console.log('=== 初始化视频链接列表页面（优化版 TableManager）===');

    // 1. 创建表格管理器实例 - 配置适配视频链接管理
    const tableManager = new window.AdminCommon.TableManager({
        tableSelector: '#dataTable',
        tbodySelector: '#videoLinkTableBody',
        paginationSelector: '#paginationNav',
        itemsPerPageSelector: '#itemsPerPage',
        selectedCountSelector: '#selectedCount',
        selectAllSelector: '#selectAll',
        rowCheckboxSelector: '.row-checkbox',
        columnSettingsPopupSelector: '#columnSettingsPopup',
        currentDisplaySelector: '#currentDisplay',
        defaultItemsPerPage: 10,
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
        console.log(`视频链接列表页面批量操作: ${action}，选中项目:`, selectedIds);

        // 使用TableOperations的通用批量操作方法
        window.AdminCommon.TableOperations.handleBulkAction({
            action: action,
            selectedIds: selectedIds,
            endpoint: '/video-links/bulk-action',
            entityName: '视频链接',
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

    // 6. 初始化批量导入功能 - 使用默认配置适配视频链接页面
    if (window.AdminCommon.BulkImportUtils) {
        window.AdminCommon.BulkImportUtils.setupBulkImport({
            endpoint: '/video-links/bulk-import',
            entityName: '视频链接'
        });
        console.log('视频链接批量导入功能已初始化');
    }

    // 7. 设置删除按钮的事件监听器 - 使用通用的TableOperations删除功能
    window.AdminCommon.TableOperations.setupDeleteButtonEventListeners({
        tbodySelector: tableManager.config.tbodySelector,
        deleteButtonSelector: '.delete-item',
        endpoint: '/video-links/{id}',
        entityName: '视频链接',
        tableManager: tableManager
    });

    // 8. 将实例保存到全局，方便调试和扩展
    window.videoLinkListManager = {
        tableManager: tableManager,
        tableActions: tableActions
    };

    console.log('=== 视频链接列表页面初始化完成（优化版 TableManager）===');
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
    if (window.videoLinkListManager && window.videoLinkListManager.tableActions) {
        window.videoLinkListManager.tableActions.exportData(format);
    } else {
        console.error('videoLinkListManager 未初始化');
    }
}
// 确保 exportData 全局可访问，供 HTML onclick 调用
window.exportData = exportData;
