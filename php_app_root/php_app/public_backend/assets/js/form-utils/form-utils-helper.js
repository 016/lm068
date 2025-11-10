/**
 * 表单工具辅助函数集合
 * 提供独立的表单相关工具函数
 */

const FormUtilsHelper = {
    /**
     * 快速创建表单工具实例
     */
    create(formSelector, options = {}) {
        return new FormUtils(formSelector, options);
    },

    /**
     * 为页面上所有表单初始化基础功能
     */
    initializeAllForms(options = {}) {
        const forms = document.querySelectorAll('form');
        const instances = [];

        forms.forEach(form => {
            instances.push(new FormUtils(form, options));
        });

        return instances;
    },

    /**
     * 设置全局的离开页面确认
     */
    setupGlobalUnloadWarning() {
        window.addEventListener('beforeunload', (e) => {
            const modifiedForms = document.querySelectorAll('form');
            let hasModified = false;

            modifiedForms.forEach(form => {
                if (form._formUtils && form._formUtils.isModified) {
                    hasModified = true;
                }
            });

            if (hasModified) {
                e.preventDefault();
                e.returnValue = '您有未保存的更改,确定要离开吗?';
            }
        });
    },

    /**
     * 批量设置字段值
     */
    setFieldValues(form, values) {
        Object.entries(values).forEach(([name, value]) => {
            const field = form.querySelector(`[name="${name}"]`);
            if (field) {
                if (field.type === 'checkbox') {
                    field.checked = value;
                } else if (field.type === 'radio') {
                    const radio = form.querySelector(`[name="${name}"][value="${value}"]`);
                    if (radio) radio.checked = true;
                } else {
                    field.value = value;
                }
            }
        });
    },

    /**
     * 批量获取字段值
     */
    getFieldValues(form, fieldNames) {
        const values = {};
        fieldNames.forEach(name => {
            const field = form.querySelector(`[name="${name}"]`);
            if (field) {
                if (field.type === 'checkbox') {
                    values[name] = field.checked;
                } else if (field.type === 'radio') {
                    const checked = form.querySelector(`[name="${name}"]:checked`);
                    values[name] = checked ? checked.value : null;
                } else {
                    values[name] = field.value;
                }
            }
        });
        return values;
    },

    /**
     * 禁用/启用表单所有字段
     */
    setFormDisabled(form, disabled) {
        const fields = form.querySelectorAll('input, textarea, select, button');
        fields.forEach(field => {
            field.disabled = disabled;
        });
    },

    /**
     * 序列化表单为查询字符串
     */
    serializeForm(form) {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        return params.toString();
    },

    /**
     * 表单数据差异对比
     */
    getFormDiff(originalData, currentData) {
        const diff = {};
        Object.keys(currentData).forEach(key => {
            if (originalData[key] !== currentData[key]) {
                diff[key] = {
                    old: originalData[key],
                    new: currentData[key]
                };
            }
        });
        return diff;
    }
};

window.FormUtilsHelper = FormUtilsHelper;

// 页面加载完成后的自动初始化
document.addEventListener('DOMContentLoaded', () => {
    // 为带有 data-form-utils 属性的表单自动初始化
    document.querySelectorAll('form[data-form-utils]').forEach(form => {
        try {
            const options = JSON.parse(form.dataset.formUtils || '{}');
            const instance = new FormUtils(form, options);
            form._formUtils = instance;
        } catch (error) {
            console.error('FormUtils 自动初始化失败:', error);
        }
    });
});