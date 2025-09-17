/**
 * Collection List Page JavaScript v2 - 基于优化版表格管理器
 * 基于 tag_list_11.js 修改，适配合集管理页面的特定需求
 * 
 * 主要修改：
 * - 表格选择器从 '#tagTableBody' 改为 '#collectionTableBody'
 * - 批量操作逻辑调整为合集相关的操作提示
 * - 保持与标签页面相同的技术架构和代码风格
 * - 添加合集特有的功能扩展
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
    console.log('=== 初始化合集列表页面（优化版 TableManager）===');
    
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
    
    // 6. 初始化合集特有的功能增强
    initCollectionSpecificFeatures();
    
    // 7. 将实例保存到全局，方便调试和扩展
    window.collectionListManager = {
        tableManager: tableManager,
        tableActions: tableActions
    };
    
    console.log('=== 合集列表页面初始化完成（优化版 TableManager）===');
}

/**
 * 初始化合集特有的功能增强
 */
function initCollectionSpecificFeatures() {
    console.log('初始化合集特有功能增强...');
    
    // 1. 合集样式hover效果增强
    setupCollectionStyleHoverEffects();
    
    // 2. 合集描述tooltip功能
    setupCollectionDescriptionTooltips();
    
    // 3. 合集图标动画效果
    setupCollectionIconAnimations();
    
    console.log('合集特有功能增强初始化完成');
}

/**
 * 设置合集样式的hover效果
 */
function setupCollectionStyleHoverEffects() {
    document.addEventListener('mouseenter', function(e) {
        if (e.target.closest('.collection-style')) {
            const collectionStyle = e.target.closest('.collection-style');
            const icon = collectionStyle.querySelector('.style-icon');
            const indicator = collectionStyle.querySelector('.style-color-indicator');
            
            if (icon) {
                icon.style.transform = 'scale(1.1)';
                icon.style.boxShadow = '0 2px 4px rgba(0, 0, 0, 0.1)';
            }
            
            if (indicator) {
                indicator.style.transform = 'scale(1.2)';
                indicator.style.boxShadow = '0 2px 4px rgba(0, 0, 0, 0.2)';
            }
        }
    }, true);
    
    document.addEventListener('mouseleave', function(e) {
        if (e.target.closest('.collection-style')) {
            const collectionStyle = e.target.closest('.collection-style');
            const icon = collectionStyle.querySelector('.style-icon');
            const indicator = collectionStyle.querySelector('.style-color-indicator');
            
            if (icon) {
                icon.style.transform = 'scale(1)';
                icon.style.boxShadow = 'none';
            }
            
            if (indicator) {
                indicator.style.transform = 'scale(1)';
                indicator.style.boxShadow = '0 1px 2px rgba(0, 0, 0, 0.1)';
            }
        }
    }, true);
    
    console.log('合集样式hover效果已设置');
}

/**
 * 设置合集描述的tooltip功能
 */
function setupCollectionDescriptionTooltips() {
    // 为表格中的描述单元格添加完整描述的data属性
    document.querySelectorAll('[data-column="description"]').forEach(cell => {
        const fullText = cell.textContent.trim();
        if (fullText.length > 20) { // 如果描述较长，添加tooltip
            cell.classList.add('collection-description');
            cell.setAttribute('data-full-description', fullText);
            
            // 截断显示的文本
            if (fullText.length > 25) {
                cell.textContent = fullText.substring(0, 25) + '...';
            }
        }
    });
    
    console.log('合集描述tooltip功能已设置');
}

/**
 * 设置合集图标的动画效果
 */
function setupCollectionIconAnimations() {
    // 为颜色指示器添加渐变动画
    document.querySelectorAll('.style-color-indicator').forEach(indicator => {
        // 已通过CSS实现动画，这里可以添加额外的交互逻辑
        indicator.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // 添加一个临时的"脉冲"效果
            this.style.animation = 'none';
            requestAnimationFrame(() => {
                this.style.animation = '';
                this.style.transform = 'scale(1.3)';
                
                setTimeout(() => {
                    this.style.transform = '';
                }, 200);
            });
        });
    });
    
    console.log('合集图标动画效果已设置');
}

/**
 * 合集相关的工具函数
 */
const CollectionUtils = {
    /**
     * 根据合集类型获取对应的图标
     */
    getIconByType: function(type) {
        const iconMap = {
            'tech': '🔬',
            'music': '🎵',
            'gaming': '🎮',
            'food': '🍽️',
            'travel': '✈️',
            'education': '📚',
            'sports': '⚽',
            'entertainment': '🎬'
        };
        return iconMap[type] || '📁';
    },
    
    /**
     * 根据合集类型获取对应的颜色渐变
     */
    getGradientByType: function(type) {
        const gradientMap = {
            'tech': 'linear-gradient(135deg, #6366f1, #8b5cf6)',
            'music': 'linear-gradient(135deg, #10b981, #059669)',
            'gaming': 'linear-gradient(135deg, #f59e0b, #d97706)',
            'food': 'linear-gradient(135deg, #ec4899, #be185d)',
            'travel': 'linear-gradient(135deg, #3b82f6, #1d4ed8)',
            'education': 'linear-gradient(135deg, #8b5cf6, #7c3aed)',
            'sports': 'linear-gradient(135deg, #059669, #047857)',
            'entertainment': 'linear-gradient(135deg, #dc2626, #b91c1c)'
        };
        return gradientMap[type] || 'linear-gradient(135deg, #64748b, #475569)';
    },
    
    /**
     * 格式化合集统计信息
     */
    formatCollectionStats: function(stats) {
        return {
            totalCollections: this.formatNumber(stats.total || 0),
            activeCollections: this.formatNumber(stats.active || 0),
            disabledCollections: this.formatNumber(stats.disabled || 0),
            totalContent: this.formatNumber(stats.content || 0)
        };
    },
    
    /**
     * 数字格式化工具
     */
    formatNumber: function(num) {
        if (num >= 1000000) {
            return (num / 1000000).toFixed(1) + 'M';
        } else if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K';
        }
        return num.toString();
    }
};

// ========== 导出函数供HTML调用 ========== 
/**
 * 导出数据 - 供HTML的onclick调用
 * @param {string} format - 导出格式：'json' 或 'csv'
 */
function exportData(format) {
    if (window.collectionListManager && window.collectionListManager.tableActions) {
        console.log(`导出合集数据，格式: ${format}`);
        window.collectionListManager.tableActions.exportData(format);
    } else {
        console.error('collectionListManager 未初始化');
    }
}

/**
 * 创建新合集 - 供HTML调用
 */
function createNewCollection() {
    console.log('创建新合集');
    // 这里可以跳转到合集创建页面或打开创建模态框
    // window.location.href = '/admin/collections/create';
    alert('跳转到合集创建页面'); // 临时提示
}

/**
 * 批量导入合集 - 供HTML调用
 */
function importCollections() {
    console.log('批量导入合集');
    // 这里可以打开文件选择器或导入模态框
    alert('打开批量导入功能'); // 临时提示
}

// 确保函数全局可访问，供 HTML onclick 调用
window.exportData = exportData;
window.createNewCollection = createNewCollection;
window.importCollections = importCollections;
window.CollectionUtils = CollectionUtils;

console.log('合集列表页面JavaScript加载完成');