/**
 * 多选组件管理模块
 * 负责多选下拉组件的初始化、事件处理、数据管理
 */

class FormMultiSelectManager {
    constructor(formUtils) {
        this.formUtils = formUtils;
        this.form = formUtils.form;
        this.instances = {};
    }

    /**
     * 初始化多选组件
     */
    initialize(key, options) {
        if (!window.MultiSelectDropdown) {
            console.warn('MultiSelectDropdown 组件未找到,请确保已引入相关JS文件');
            return null;
        }

        const container = typeof options.container === 'string'
            ? document.querySelector(options.container)
            : options.container;

        if (!container) {
            console.warn(`多选组件容器未找到: ${options.container}`);
            return null;
        }

        const instance = new MultiSelectDropdown(container, {
            placeholder: options.placeholder || '请选择...',
            searchPlaceholder: options.searchPlaceholder || '搜索选项...',
            hiddenInputName: options.hiddenInputName || `${key}_ids`,
            maxDisplayItems: options.maxDisplayItems || 3,
            columns: options.columns || 1,
            data: options.data || [],
            selected: options.selected || [],
            allowClear: options.allowClear !== false
        });

        // 绑定变更事件
        container.addEventListener('multiselect:change', (e) => {
            console.log(`${key} 选择变更:`, e.detail);
            this.formUtils.markAsModified();

            // 触发自定义事件
            this.form.dispatchEvent(new CustomEvent('formutils:multiselect:change', {
                detail: { key, ...e.detail }
            }));
        });

        this.instances[key] = instance;
        return instance;
    }

    /**
     * 获取多选组件实例
     */
    getInstance(key) {
        return this.instances[key];
    }

    /**
     * 通用的视频选择变更处理
     */
    handleCommonContentsChange(
        detail,
        maxLimit = 5000,
        limitMessage = '建议视频数量不超过{limit}个'
    ) {
        const { action, item, selected } = detail;

        switch (action) {
            case 'add':
                this.formUtils.showNotification(`已添加视频: ${item.text}`, 'success');
                break;
            case 'remove':
                this.formUtils.showNotification(`已移除视频: ${item.text}`, 'info');
                break;
            case 'clear':
                this.formUtils.showNotification('已清空所有视频', 'warning');
                break;
        }

        // 更新视频统计信息
        this.updateCommonContentStats(selected);

        // 视频数量限制提示
        if (selected.length > maxLimit) {
            const message = limitMessage.replace('{limit}', maxLimit);
            this.formUtils.showNotification(message, 'warning');
        }
    }

    /**
     * 通用的视频统计信息更新
     */
    updateCommonContentStats(
        selectedVideos,
        selector = '.stats-row .stat-item .stat-value',
        formatLargeNumbers = false
    ) {
        const statValueElement = document.querySelector(selector);
        if (statValueElement && selectedVideos) {
            const count = selectedVideos.length;
            let formattedCount;

            if (formatLargeNumbers && count > 999) {
                formattedCount = `${(count / 1000).toFixed(1)}K`;
            } else {
                formattedCount = count.toString();
            }

            statValueElement.textContent = formattedCount;
        }
    }

    /**
     * 重置所有多选组件
     */
    resetAll() {
        Object.values(this.instances).forEach(instance => {
            if (instance && typeof instance.setSelected === 'function') {
                instance.setSelected([]);
            }
        });
    }

    /**
     * 销毁所有多选组件
     */
    destroyAll() {
        Object.values(this.instances).forEach(instance => {
            if (instance && typeof instance.destroy === 'function') {
                instance.destroy();
            }
        });
        this.instances = {};
    }
}

window.FormMultiSelectManager = FormMultiSelectManager;