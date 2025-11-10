/**
 * 数据持久化模块
 * 负责自动保存、数据恢复、本地存储
 */

class FormPersistence {
    constructor(formUtils) {
        this.formUtils = formUtils;
        this.form = formUtils.form;
        this.autoSaveInterval = null;
    }

    /**
     * 初始化自动保存功能
     */
    initializeAutoSave() {
        this.autoSaveInterval = setInterval(() => {
            if (this.formUtils.isModified &&
                this.formUtils.validator.validateForm()) {
                this.autoSave();
            }
        }, this.formUtils.options.autoSaveInterval);
    }

    /**
     * 执行自动保存
     */
    autoSave() {
        const data = this.formUtils.collectFormData();
        const formId = this.form.id || 'form';

        try {
            localStorage.setItem(`${formId}_autoSave`, JSON.stringify(data));
            console.log('表单已自动保存');

            // 触发自动保存事件
            this.form.dispatchEvent(new CustomEvent('formutils:autosave', {
                detail: { data }
            }));
        } catch (error) {
            console.error('自动保存失败:', error);
        }
    }

    /**
     * 加载自动保存的数据
     */
    loadAutoSave() {
        const formId = this.form.id || 'form';
        const savedData = localStorage.getItem(`${formId}_autoSave`);

        if (savedData) {
            try {
                const data = JSON.parse(savedData);
                this.populateForm(data);
                this.formUtils.showNotification('已恢复自动保存的数据', 'info');
                return true;
            } catch (error) {
                console.error('加载自动保存数据失败:', error);
                return false;
            }
        }
        return false;
    }

    /**
     * 清除自动保存的数据
     */
    clearAutoSave() {
        const formId = this.form.id || 'form';
        localStorage.removeItem(`${formId}_autoSave`);
        console.log('已清除自动保存数据');
    }

    /**
     * 收集表单数据
     * 返回包含表单所有数据的对象，包括多选组件数据
     * 使用示例：
     * const data = formUtils.collectFormData();
     * console.log(data); // { name: 'test', tag_ids: '1,2,3', ... }
     */
    collectFormData() {
        const formData = new FormData(this.form);
        const data = Object.fromEntries(formData.entries());

        // 添加多选组件的数据
        Object.entries(this.formUtils.multiSelectInstances).forEach(([key, instance]) => {
            if (instance && typeof instance.getValue === 'function') {
                data[`${key}_ids`] = instance.getValue();
                if (typeof instance.getSelected === 'function') {
                    data[key] = instance.getSelected();
                }
            }
        });

        return data;
    }

    /**
     * 填充表单数据
     */
    populateForm(data) {
        Object.entries(data).forEach(([key, value]) => {
            const field = this.form.querySelector(`[name="${key}"]`);
            if (field) {
                if (field.type === 'checkbox') {
                    field.checked = value;
                } else if (field.type === 'radio') {
                    const radioInput = this.form.querySelector(
                        `[name="${key}"][value="${value}"]`
                    );
                    if (radioInput) radioInput.checked = true;
                } else {
                    field.value = value;
                }

                // 触发input事件以更新相关UI
                field.dispatchEvent(new Event('input', { bubbles: true }));
            }
        });
    }

    /**
     * 导出表单数据为JSON
     */
    exportToJSON() {
        const data = this.formUtils.collectFormData();
        const dataStr = JSON.stringify(data, null, 2);
        const dataBlob = new Blob([dataStr], { type: 'application/json' });

        const url = URL.createObjectURL(dataBlob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `form-data-${Date.now()}.json`;
        link.click();
        URL.revokeObjectURL(url);
    }

    /**
     * 从JSON导入表单数据
     */
    importFromJSON(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                try {
                    const data = JSON.parse(e.target.result);
                    this.populateForm(data);
                    this.formUtils.showNotification('数据导入成功', 'success');
                    resolve(data);
                } catch (error) {
                    this.formUtils.showNotification('数据导入失败', 'error');
                    reject(error);
                }
            };
            reader.onerror = () => reject(reader.error);
            reader.readAsText(file);
        });
    }

    /**
     * 销毁持久化功能
     */
    destroy() {
        if (this.autoSaveInterval) {
            clearInterval(this.autoSaveInterval);
            this.autoSaveInterval = null;
        }
    }
}

window.FormPersistence = FormPersistence;