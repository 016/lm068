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
    
    // 5. 自定义批量操作处理逻辑 - 实现真实的AJAX批量操作
    tableActions.handleBulkAction = function(action, selectedIds) {
        console.log(`标签列表页面批量操作: ${action}，选中项目:`, selectedIds);
        
        if (selectedIds.length === 0) {
            alert('请先选择要操作的标签');
            return;
        }
        
        let actionText = '';
        switch(action) {
            case 'enable':
                actionText = '启用';
                break;
            case 'disable':
                actionText = '禁用';
                break;
            case 'delete':
                actionText = '删除';
                break;
            default:
                alert('不支持的操作');
                return;
        }
        
        // 删除操作需要确认
        if (action === 'delete' && !confirm(`确定要删除 ${selectedIds.length} 个标签吗？此操作不可撤销。`)) {
            return;
        }
        
        // 发送AJAX请求
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/tags/bulkAction', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    
                    if (response.success) {
                        // 成功处理
                        const message = response.success_count !== undefined && response.error_count !== undefined 
                            ? `成功${response.success_count}条，失败${response.error_count}条，点击确认后将自动刷新数据。`
                            : `${actionText}操作完成，点击确认后将自动刷新数据。`;
                        
                        alert(message);
                        
                        // 刷新页面保持当前URL格式
                        window.location.reload();
                    } else {
                        alert(`操作失败: ${response.message || '未知错误'}`);
                    }
                } catch (e) {
                    console.error('响应解析错误:', e);
                    alert('操作失败，服务器响应格式错误');
                }
            }
        };
        
        xhr.onerror = function() {
            alert('网络错误，请稍后重试');
        };
        
        // 发送请求数据
        const formData = `action=${encodeURIComponent(action)}&tag_ids=${encodeURIComponent(JSON.stringify(selectedIds))}`;
        xhr.send(formData);
    };
    
    // 6. 初始化批量导入功能
    if (window.AdminCommon.BulkImportUtils) {
        window.AdminCommon.BulkImportUtils.setupBulkImport();
        console.log('批量导入功能已初始化');
    }
    
    // 7. 将实例保存到全局，方便调试和扩展
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