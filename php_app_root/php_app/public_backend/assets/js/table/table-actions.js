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

/**
 * BatchSettingsManager - 批量设置管理类
 *
 * 功能：
 * - 批量设置表格中选中项的字段值
 * - 支持通用字段设置
 * - 特殊支持 pub_at 字段的时间均匀分布
 */
class BatchSettingsManager {
    constructor(tableManager, config = {}) {
        this.tableManager = tableManager;
        this.config = {
            btnSelector: '#batchSettingsBtn',
            modalSelector: '#batchSettingsModal',
            fieldLabelSelector: '#batchFieldLabel',
            dateRangeSectionSelector: '#dateRangeSection',
            startDateTimeSelector: '#startDateTime',
            endDateTimeSelector: '#endDateTime',
            distributeBtnSelector: '#distributeTimesBtn',
            selectedItemsCountSelector: '#selectedItemsCount',
            selectedItemsTableBodySelector: '#selectedItemsTableBody',
            saveBtnSelector: '#saveBatchSettingsBtn',
            copyFirstValueBtnSelector: '#copyFirstValueBtn',
            valueColumnHeaderSelector: '#valueColumnHeader',
            endpoint: '/contents/batch-update',
            ...config
        };

        this.currentField = null;
        this.selectedItems = [];
        this.modal = null;
    }

    /**
     * 初始化批量设置功能
     */
    init() {
        const btn = document.querySelector(this.config.btnSelector);
        if (!btn) {
            console.warn('批量设置按钮未找到');
            return this;
        }

        // 获取Bootstrap Modal实例
        const modalElement = document.querySelector(this.config.modalSelector);
        if (!modalElement) {
            console.warn('批量设置Modal未找到');
            return this;
        }
        this.modal = new bootstrap.Modal(modalElement);

        // 绑定按钮点击事件
        btn.addEventListener('click', () => this.handleButtonClick());

        // 绑定时间分配按钮
        const distributeBtn = document.querySelector(this.config.distributeBtnSelector);
        if (distributeBtn) {
            distributeBtn.addEventListener('click', () => this.distributeDateTime());
        }

        // 绑定保存按钮
        const saveBtn = document.querySelector(this.config.saveBtnSelector);
        if (saveBtn) {
            saveBtn.addEventListener('click', () => this.saveBatchSettings());
        }

        // 绑定复制第一项按钮
        const copyBtn = document.querySelector(this.config.copyFirstValueBtnSelector);
        if (copyBtn) {
            copyBtn.addEventListener('click', () => this.copyFirstValue());
        }

        console.log('BatchSettingsManager 初始化完成');
        return this;
    }

    /**
     * 处理批量设置按钮点击
     */
    handleButtonClick() {
        // 获取选中的行
        const selectedRows = this.tableManager.getSelectedRows();

        if (selectedRows.length === 0) {
            if (window.showToast) {
                window.showToast('请先选择要设置的内容', 'warning');
            } else {
                alert('请先选择要设置的内容');
            }
            return;
        }

        // 获取data-field属性
        const btn = document.querySelector(this.config.btnSelector);
        this.currentField = btn.getAttribute('data-field') || 'pub_at';

        // 收集选中项的ID和标题
        this.selectedItems = this.collectSelectedItems(selectedRows);

        // 显示Modal
        this.showModal();
    }

    /**
     * 收集选中项目的信息
     */
    collectSelectedItems(selectedIds) {
        const items = [];
        const tbody = document.querySelector(this.tableManager.config.tbodySelector);

        selectedIds.forEach(id => {
            const row = tbody.querySelector(`tr[data-id="${id}"]`);
            if (row) {
                const titleCell = row.querySelector('.table-title');
                const title = titleCell ? titleCell.textContent.trim() : `内容 ${id}`;
                items.push({ id, title });
            }
        });

        return items;
    }

    /**
     * 从table header获取字段的显示名称
     */
    getFieldLabel(field) {
        // 尝试从table header中查找对应的列
        const tableHeader = document.querySelector('#tableHeader');
        if (tableHeader) {
            const th = tableHeader.querySelector(`th[data-column="${field}"]`);
            if (th) {
                // 获取th的文本内容，排除排序图标等元素
                const headerText = th.querySelector('.d-flex');
                if (headerText) {
                    // 只获取文本内容，不包括子元素
                    return headerText.childNodes[0].textContent.trim();
                }
                // 如果没有.d-flex元素，直接获取文本
                return th.textContent.trim();
            }
        }

        // 如果在table header中找不到，使用默认映射
        const defaultLabels = {
            'pub_at': '发布时间',
            'title': '标题',
            'author': '作者',
            'code': '内部代码',
            'status_id': '状态',
            'duration': '时长'
        };

        return defaultLabels[field] || field;
    }

    /**
     * 显示批量设置Modal
     */
    showModal() {
        // 更新字段标签 - 从table header动态获取
        const fieldLabel = this.getFieldLabel(this.currentField);
        document.querySelector(this.config.fieldLabelSelector).textContent = fieldLabel;

        // 显示/隐藏日期范围选择区域
        const dateRangeSection = document.querySelector(this.config.dateRangeSectionSelector);
        if (this.currentField === 'pub_at') {
            dateRangeSection.style.display = 'block';
        } else {
            dateRangeSection.style.display = 'none';
        }

        // 更新值列标题
        document.querySelector(this.config.valueColumnHeaderSelector).textContent = fieldLabel;

        // 更新选中项计数
        document.querySelector(this.config.selectedItemsCountSelector).textContent = this.selectedItems.length;

        // 填充选中项列表
        this.renderSelectedItems();

        // 显示Modal
        this.modal.show();
    }

    /**
     * 渲染选中项列表
     */
    renderSelectedItems() {
        const tbody = document.querySelector(this.config.selectedItemsTableBodySelector);
        tbody.innerHTML = '';

        this.selectedItems.forEach(item => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.id}</td>
                <td>${item.title}</td>
                <td>
                    <input type="text"
                           class="form-control form-control-sm batch-value-input"
                           data-id="${item.id}"
                           placeholder="请输入${this.currentField === 'pub_at' ? '时间 (YYYY-MM-DD HH:MM:SS)' : '值'}">
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    /**
     * 均匀分配日期时间
     */
    distributeDateTime() {
        const startInput = document.querySelector(this.config.startDateTimeSelector);
        const endInput = document.querySelector(this.config.endDateTimeSelector);

        const startDateTime = startInput.value;
        const endDateTime = endInput.value;

        if (!startDateTime || !endDateTime) {
            if (window.showToast) {
                window.showToast('请选择开始和结束时间', 'warning');
            } else {
                alert('请选择开始和结束时间');
            }
            return;
        }

        const start = new Date(startDateTime);
        const end = new Date(endDateTime);

        if (start >= end) {
            if (window.showToast) {
                window.showToast('结束时间必须大于开始时间', 'warning');
            } else {
                alert('结束时间必须大于开始时间');
            }
            return;
        }

        // 计算时间间隔
        const totalMs = end - start;
        const count = this.selectedItems.length;
        const intervalMs = count > 1 ? totalMs / (count - 1) : 0;

        // 分配时间到每个输入框
        const inputs = document.querySelectorAll('.batch-value-input');
        inputs.forEach((input, index) => {
            const itemTime = new Date(start.getTime() + intervalMs * index);
            input.value = this.formatDateTime(itemTime);
        });

        if (window.showToast) {
            window.showToast(`已为 ${count} 项内容分配时间`, 'success');
        }
    }

    /**
     * 格式化日期时间为 YYYY-MM-DD HH:MM:SS
     */
    formatDateTime(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const seconds = String(date.getSeconds()).padStart(2, '0');

        return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    }

    /**
     * 复制第一项的值到所有输入框
     */
    copyFirstValue() {
        const inputs = document.querySelectorAll('.batch-value-input');

        if (inputs.length === 0) {
            if (window.showToast) {
                window.showToast('没有找到输入框', 'warning');
            } else {
                alert('没有找到输入框');
            }
            return;
        }

        // 获取第一个输入框的值
        const firstValue = inputs[0].value.trim();

        if (!firstValue) {
            if (window.showToast) {
                window.showToast('第一项的值为空，请先填写', 'warning');
            } else {
                alert('第一项的值为空，请先填写');
            }
            return;
        }

        // 复制到所有其他输入框
        let copiedCount = 0;
        inputs.forEach((input, index) => {
            if (index > 0) { // 跳过第一个
                input.value = firstValue;
                copiedCount++;
            }
        });

        if (window.showToast) {
            window.showToast(`已复制到 ${copiedCount} 个输入框`, 'success');
        }
    }

    /**
     * 保存批量设置
     */
    saveBatchSettings() {
        // 收集所有输入的值
        const updates = [];
        const inputs = document.querySelectorAll('.batch-value-input');

        inputs.forEach(input => {
            const id = input.getAttribute('data-id');
            const value = input.value.trim();

            if (value) {
                updates.push({ id: parseInt(id), value });
            }
        });

        if (updates.length === 0) {
            if (window.showToast) {
                window.showToast('请至少为一项内容设置值', 'warning');
            } else {
                alert('请至少为一项内容设置值');
            }
            return;
        }

        // 准备请求数据
        const requestData = {
            field: this.currentField,
            updates: updates
        };

        // 发送Ajax请求
        const saveBtn = document.querySelector(this.config.saveBtnSelector);
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> 保存中...';

        fetch(this.config.endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(requestData)
        })
        .then(response => response.json())
        .then(data => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="bi bi-save"></i> 保存设置';

            if (data.success) {
                if (window.showToast) {
                    window.showToast(data.message || '批量设置成功', 'success');
                } else {
                    alert(data.message || '批量设置成功');
                }

                // 关闭Modal
                this.modal.hide();

                // 刷新页面
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                if (window.showToast) {
                    window.showToast(data.message || '批量设置失败', 'error');
                } else {
                    alert(data.message || '批量设置失败');
                }
            }
        })
        .catch(error => {
            console.error('批量设置错误:', error);
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="bi bi-save"></i> 保存设置';

            if (window.showToast) {
                window.showToast('批量设置失败: ' + error.message, 'error');
            } else {
                alert('批量设置失败: ' + error.message);
            }
        });
    }
}

/**
 * SectionToggleManager - 区域显示/隐藏切换管理类
 *
 * 功能：
 * - 控制页面区域的显示和隐藏
 * - 通过按钮切换区域状态
 * - 根据 data-show 属性初始化区域状态
 */
class SectionToggleManager {
    constructor(config = {}) {
        this.config = {
            toggleBtnSelector: '.toggle-section-btn',
            ...config
        };
    }

    /**
     * 初始化切换功能
     */
    init() {
        console.log('=== SectionToggleManager 初始化开始 ===');

        // 获取所有切换按钮
        const toggleButtons = document.querySelectorAll(this.config.toggleBtnSelector);

        if (toggleButtons.length === 0) {
            console.warn('未找到切换按钮');
            return this;
        }

        // 为每个按钮绑定点击事件
        toggleButtons.forEach(button => {
            const targetId = button.getAttribute('data-target');
            if (!targetId) {
                console.warn('切换按钮缺少 data-target 属性', button);
                return;
            }

            const targetSection = document.getElementById(targetId);
            if (!targetSection) {
                console.warn(`未找到目标区域: ${targetId}`);
                return;
            }

            // 根据 data-show 属性初始化区域状态
            this.initSectionState(targetSection, button);

            // 绑定点击事件
            button.addEventListener('click', () => {
                this.toggleSection(targetSection, button);
            });

            console.log(`已绑定切换按钮: ${targetId}`);
        });

        console.log('=== SectionToggleManager 初始化完成 ===');
        return this;
    }

    /**
     * 初始化区域状态
     * 根据 data-show 属性设置初始显示状态
     */
    initSectionState(targetSection, button) {
        const dataShow = targetSection.getAttribute('data-show');
        const shouldShow = dataShow === 'true';

        if (shouldShow) {
            targetSection.style.display = '';
            button.classList.add('active');
        } else {
            targetSection.style.display = 'none';
            button.classList.remove('active');

        }

        console.log(`初始化区域状态: ${targetSection.id}, 显示: ${shouldShow}`);
    }

    /**
     * 切换区域显示/隐藏
     */
    toggleSection(targetSection, button) {
        const currentDisplay = targetSection.style.display;
        const isHidden = currentDisplay === 'none';

        if (isHidden) {
            // 显示区域
            targetSection.style.display = '';
            targetSection.setAttribute('data-show', 'true');
            button.classList.add('active');
            console.log(`显示区域: ${targetSection.id}`);
        } else {
            // 隐藏区域
            targetSection.style.display = 'none';
            targetSection.setAttribute('data-show', 'false');
            button.classList.remove('active');
            console.log(`隐藏区域: ${targetSection.id}`);
        }
    }
}

    // ========== GLOBAL EXPORTS ==========
    if (!window.AdminCommon) {
        window.AdminCommon = {};
    }

    window.AdminCommon.CommonTableActions = CommonTableActions;
    window.AdminCommon.BatchSettingsManager = BatchSettingsManager;
    window.AdminCommon.SectionToggleManager = SectionToggleManager;

    console.log('Table Actions 已加载');
})();
