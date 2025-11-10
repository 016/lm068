/**
 * Table Actions - 通用表格操作功能类
 *
 * 依赖：
 * - table-manager.js (TableManager)
 * - table-operations.js (exportData)
 *
 * 提供功能：
 * - 下拉菜单管理
 * - 刷新按钮
 * - 批量操作
 * - 数据导出
 */

(function() {
    'use strict';

class CommonTableActions {
    constructor(tableManager, config = {}) {
        this.tableManager = tableManager;
        this.config = {
            exportBtnSelector: '#exportBtn',
            exportPopupSelector: '#exportPopup',
            refreshBtnSelector: '#refreshBtn',
            columnSettingsBtnSelector: '#columnSettingsBtn',
            columnSettingsPopupSelector: '#columnSettingsPopup',
            bulkActionsBtnSelector: '#bulkActionsBtn',
            bulkActionsDropdownSelector: '#bulkActionsDropdown',
            ...config
        };
        
        // 下拉菜单配置
        this.dropdownConfigs = [
            { trigger: this.config.exportBtnSelector, dropdown: this.config.exportPopupSelector },
            { trigger: this.config.columnSettingsBtnSelector, dropdown: this.config.columnSettingsPopupSelector },
            { trigger: this.config.bulkActionsBtnSelector, dropdown: this.config.bulkActionsDropdownSelector }
        ];
    }
    
    /**
     * 初始化所有操作功能
     */
    init() {
        this.setupDropdowns();
        this.setupRefreshButton();
        this.setupBulkActions();
        
        console.log('CommonTableActions 初始化完成');
        return this;
    }
    
    /**
     * 统一设置所有下拉菜单
     */
    setupDropdowns() {
        this.dropdownConfigs.forEach(config => {
            const trigger = document.querySelector(config.trigger);
            const dropdown = document.querySelector(config.dropdown);
            
            if (trigger && dropdown) {
                trigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    dropdown.classList.toggle('show');
                });
                
                document.addEventListener('click', (e) => {
                    if (!trigger.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.remove('show');
                    }
                });
            }
        });
        
        console.log('统一下拉菜单设置完成');
    }
    
    /**
     * 设置刷新按钮
     */
    setupRefreshButton() {
        const refreshBtn = document.querySelector(this.config.refreshBtnSelector);
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => {
                this.tableManager.refresh();
            });
        }
        
        console.log('刷新按钮已设置');
    }
    
    /**
     * 设置批量操作
     */
    setupBulkActions() {
        document.querySelectorAll('[data-action]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const action = btn.dataset.action;
                const selectedRows = this.tableManager.getSelectedRows();
                
                if (selectedRows.length === 0) {
                    alert('请先选择要操作的项目');
                    return;
                }
                
                console.log(`执行批量操作: ${action}，选中项目:`, selectedRows);
                this.handleBulkAction(action, selectedRows);
                
                const bulkActionsDropdown = document.querySelector(this.config.bulkActionsDropdownSelector);
                if (bulkActionsDropdown) {
                    bulkActionsDropdown.classList.remove('show');
                }
            });
        });
        
        console.log('批量操作功能已设置');
    }
    
    /**
     * 处理批量操作 - 可在子类中重写
     */
    handleBulkAction(action, selectedIds) {
        // 默认的批量操作处理逻辑
        switch(action) {
            case 'enable':
                alert(`启用了 ${selectedIds.length} 个项目`);
                break;
            case 'disable':
                alert(`禁用了 ${selectedIds.length} 个项目`);
                break;
            case 'delete':
                if (confirm(`确定要删除 ${selectedIds.length} 个项目吗？`)) {
                    alert(`删除了 ${selectedIds.length} 个项目`);
                }
                break;
        }
    }
    
    /**
     * 导出数据的全局函数 - 供HTML onclick调用
     */
    exportData(format) {
        console.log(`导出数据，格式: ${format}`);
        this.tableManager.exportData(format);
        
        // 关闭导出菜单
        const exportPopup = document.querySelector(this.config.exportPopupSelector);
        if (exportPopup) {
            exportPopup.classList.remove('show');
        }
    }
}

    // ========== GLOBAL EXPORTS ==========
    if (!window.AdminCommon) {
        window.AdminCommon = {};
    }

    window.AdminCommon.CommonTableActions = CommonTableActions;

    console.log('Table Actions 已加载');
})();
