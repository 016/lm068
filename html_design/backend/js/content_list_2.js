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
        defaultItemsPerPage: 5, // 内容列表默认显示5条
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
    
    // 6. 初始化内容特定功能
    initContentSpecificFeatures();
    
    // 7. 将实例保存到全局，方便调试和扩展
    window.contentListManager = {
        tableManager: tableManager,
        tableActions: tableActions
    };
    
    console.log('=== 内容列表页面初始化完成（优化版 TableManager）===');
}

/**
 * 初始化内容特定功能
 */
function initContentSpecificFeatures() {
    console.log('初始化内容特定功能...');
    
    // 内容类型过滤功能
    initContentTypeFilter();
    
    // 状态过滤功能
    initStatusFilter();
    
    // 标题点击预览功能
    initTitlePreview();
}

/**
 * 初始化内容类型过滤功能
 */
function initContentTypeFilter() {
    const contentTypeFilter = document.querySelector('select[data-column="content_type"]');
    if (contentTypeFilter) {
        contentTypeFilter.addEventListener('change', function() {
            const selectedType = this.value;
            console.log('内容类型过滤:', selectedType);
            
            // 这里可以添加实际的过滤逻辑
            filterContentByType(selectedType);
        });
    }
}

/**
 * 初始化状态过滤功能
 */
function initStatusFilter() {
    const statusFilter = document.querySelector('select[data-column="status"]');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            const selectedStatus = this.value;
            console.log('状态过滤:', selectedStatus);
            
            // 这里可以添加实际的过滤逻辑
            filterContentByStatus(selectedStatus);
        });
    }
}

/**
 * 初始化标题点击预览功能
 */
function initTitlePreview() {
    document.addEventListener('click', function(e) {
        const titleElement = e.target.closest('.content-title');
        if (titleElement) {
            const row = titleElement.closest('.table-row');
            const contentId = row ? row.dataset.id : null;
            
            if (contentId) {
                console.log('预览内容:', contentId);
                // 这里可以添加预览功能
                previewContent(contentId);
            }
        }
    });
}

// ========== 内容管理相关功能函数 ========== 

/**
 * 根据内容类型过滤
 * @param {string} contentType - 内容类型
 */
function filterContentByType(contentType) {
    console.log('根据内容类型过滤:', contentType);
    // 这里添加实际的过滤逻辑
    // 可以通过AJAX请求后端API或前端过滤表格数据
}

/**
 * 根据状态过滤
 * @param {string} status - 状态
 */
function filterContentByStatus(status) {
    console.log('根据状态过滤:', status);
    // 这里添加实际的过滤逻辑
}

/**
 * 预览内容
 * @param {string} contentId - 内容ID
 */
function previewContent(contentId) {
    console.log('预览内容:', contentId);
    // 这里可以添加预览模态框或跳转到预览页面的逻辑
    // 例如：window.open(`/content/preview/${contentId}`, '_blank');
}

/**
 * 更新内容状态
 * @param {Array} contentIds - 内容ID数组
 * @param {number} statusId - 新状态ID
 */
function updateContentStatus(contentIds, statusId) {
    console.log('更新内容状态:', contentIds, '到状态:', statusId);
    
    // 这里添加实际的状态更新逻辑
    // 可以通过AJAX请求后端API
    /*
    fetch('/api/content/batch-update-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            content_ids: contentIds,
            status_id: statusId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // 更新UI显示
            updateTableRowsStatus(contentIds, statusId);
            // 刷新表格数据
            if (window.contentListManager.tableManager) {
                window.contentListManager.tableManager.refreshData();
            }
        }
    })
    .catch(error => {
        console.error('更新状态失败:', error);
        alert('更新状态失败，请重试');
    });
    */
}

/**
 * 删除内容
 * @param {Array} contentIds - 内容ID数组
 */
function deleteContent(contentIds) {
    console.log('删除内容:', contentIds);
    
    // 这里添加实际的删除逻辑
    /*
    fetch('/api/content/batch-delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            content_ids: contentIds
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // 从表格中移除已删除的行
            removeTableRows(contentIds);
            // 刷新表格数据
            if (window.contentListManager.tableManager) {
                window.contentListManager.tableManager.refreshData();
            }
        }
    })
    .catch(error => {
        console.error('删除失败:', error);
        alert('删除失败，请重试');
    });
    */
}

/**
 * 更新表格行的状态显示
 * @param {Array} contentIds - 内容ID数组
 * @param {number} statusId - 新状态ID
 */
function updateTableRowsStatus(contentIds, statusId) {
    contentIds.forEach(id => {
        const row = document.querySelector(`[data-id="${id}"]`);
        if (row) {
            const statusCell = row.querySelector('[data-column="status"] .badge');
            if (statusCell) {
                // 根据状态ID更新状态显示
                updateStatusBadge(statusCell, statusId);
            }
        }
    });
}

/**
 * 更新状态徽章
 * @param {Element} badge - 状态徽章元素
 * @param {number} statusId - 状态ID
 */
function updateStatusBadge(badge, statusId) {
    // 清除现有状态类
    badge.className = badge.className.replace(/badge-(success|warning|secondary|danger)/g, '');
    
    // 根据状态ID设置新的样式和文本
    switch(statusId) {
        case 1: // 草稿
            badge.classList.add('badge-secondary');
            badge.innerHTML = '<i class="bi bi-circle-fill badge-icon"></i> 草稿';
            break;
        case 91: // 待发布
            badge.classList.add('badge-warning');
            badge.innerHTML = '<i class="bi bi-circle-fill badge-icon"></i> 待发布';
            break;
        case 99: // 已发布
            badge.classList.add('badge-success');
            badge.innerHTML = '<i class="bi bi-circle-fill badge-icon"></i> 已发布';
            break;
        default:
            badge.classList.add('badge-secondary');
            badge.innerHTML = '<i class="bi bi-circle-fill badge-icon"></i> 未知状态';
    }
}

/**
 * 从表格中移除指定行
 * @param {Array} contentIds - 内容ID数组
 */
function removeTableRows(contentIds) {
    contentIds.forEach(id => {
        const row = document.querySelector(`[data-id="${id}"]`);
        if (row) {
            row.remove();
        }
    });
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

/**
 * 创建新内容 - 供HTML按钮调用
 */
function createNewContent() {
    console.log('创建新内容');
    // 这里可以跳转到内容创建页面或打开创建模态框
    // window.location.href = '/content/create';
    alert('跳转到内容创建页面（功能待实现）');
}

/**
 * 批量导入内容 - 供HTML按钮调用
 */
function batchImportContent() {
    console.log('批量导入内容');
    // 这里可以打开导入模态框或跳转到导入页面
    alert('批量导入功能（功能待实现）');
}