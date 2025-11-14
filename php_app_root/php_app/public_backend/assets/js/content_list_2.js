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

    // 1. 初始化状态多选组件
    initStatusMultiSelect();

    // 2. 创建表格管理器实例 - 配置适配内容管理
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
        defaultItemsPerPage: 20, // 内容列表默认显示10条
        enableSort: true,
        enablePagination: true,
        enableColumnSettings: true,
        enableSearch: true,
        // multi dd input for filter support
        multiSelectColumns: [{ columnName: 'status_id', instanceName: 'statusMultiSelectInstance', containerId: 'statusMultiSelect' }],
        // 配置需要在筛选时保持的URL参数
        persistentUrlParams: ['tag_id', 'collection_id']

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
        console.log(`内容列表页面批量操作: ${action}，选中项目:`, selectedIds);
        
        // 使用TableOperations的通用批量操作方法
        window.AdminCommon.TableOperations.handleBulkAction({
            action: action,
            selectedIds: selectedIds,
            endpoint: '/contents/bulk-action',
            entityName: '内容',
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
    
    // 6. 初始化批量导入功能 - 使用默认配置适配内容页面
    if (window.AdminCommon.BulkImportUtils) {
        window.AdminCommon.BulkImportUtils.setupBulkImport({
            endpoint: '/contents/bulk-import',
            entityName: '内容'
        });
        console.log('内容批量导入功能已初始化');
    }
    
    // 7. 设置删除按钮的事件监听器 - 使用通用的TableOperations删除功能
    window.AdminCommon.TableOperations.setupDeleteButtonEventListeners({
        tbodySelector: tableManager.config.tbodySelector,
        deleteButtonSelector: '.delete-item',
        endpoint: '/contents/{id}',
        entityName: '内容',
        tableManager: tableManager
    });

    // 8. 初始化批量设置功能
    const batchSettingsManager = new window.AdminCommon.BatchSettingsManager(tableManager, {
        endpoint: '/contents/batch-update'
    });
    batchSettingsManager.init();

    // 9. 初始化区域切换功能
    const sectionToggleManager = new window.AdminCommon.SectionToggleManager();
    sectionToggleManager.init();

    // 10. 将实例保存到全局，方便调试和扩展
    window.contentListManager = {
        tableManager: tableManager,
        tableActions: tableActions,
        batchSettingsManager: batchSettingsManager,
        sectionToggleManager: sectionToggleManager
    };
    
    console.log('=== 内容列表页面初始化完成（优化版 TableManager）===');
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
    if (window.contentListManager && window.contentListManager.tableActions) {
        window.contentListManager.tableActions.exportData(format);
    } else {
        console.error('contentListManager 未初始化');
    }
}
// 确保 exportData 全局可访问，供 HTML onclick 调用
window.exportData = exportData;

// ========== 状态多选组件初始化 ==========

/**
 * 初始化状态多选组件
 * 使用 multi_select_dropdown_3.js 实现
 */
function initStatusMultiSelect() {
    // 检查必要的数据是否存在
    if (!window.contentIndexData) {
        console.error('contentIndexData 未定义');
        return;
    }

    const statusList = window.contentIndexData.statusList || [];
    const selectedStatusIds = window.contentIndexData.selectedStatusIds || [];

    // 将选中的ID转换为对应的对象
    const normalizedSelectedIds = selectedStatusIds.map(id => String(id));

    const selectedStatuses = statusList.filter(oneType =>
        normalizedSelectedIds.includes(String(oneType.id))
    );

    console.log('初始化状态多选组件:', {
        statusList,
        selectedStatusIds,
        selectedStatuses
    });

    // 创建多选组件实例
    const statusMultiSelect = new MultiSelectDropdown('#statusMultiSelect', {
        placeholder: '全部状态',
        maxDisplayItems: 2,
        columns: 4,
        searchPlaceholder: '搜索状态...',
        hiddenInputName: 'status_ids',
        data: statusList,
        selected: selectedStatuses,
        allowClear: true,
        dropdownWidth: '500px',
        dropdownAlign: 'right'  // 居中
    });

    /**
     * 处理多选框变化事件，触发表格筛选
     */
    function handleMultiSelectChange(e) {
        // 获取 TableManager 实例并触发筛选
        if (window.contentListManager && window.contentListManager.tableManager) {
            console.log('状态多选框关闭，自动触发筛选');
            window.contentListManager.tableManager.applyFilters();
        }
    }

    // 监听多选组件的变化，当下拉框关闭时自动触发搜索
    const element = document.getElementById('statusMultiSelect');
    element.addEventListener('multiselect:close', handleMultiSelectChange);
    // not open because each time change will rise reload
    // element.addEventListener('multiselect:change', handleMultiSelectChange);

    // 保存到全局以便访问
    window.statusMultiSelectInstance = statusMultiSelect;

    console.log('状态多选组件初始化完成');
}