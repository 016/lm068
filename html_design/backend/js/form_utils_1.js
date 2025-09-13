/**
 * 表单工具类 - 通用表单功能集合
 * 提取自 content_edit_form_12.html 页面的JS代码
 * 适用于管理后台的所有表单页面
 * 
 * 功能模块：
 * - 表单验证
 * - 文件上传处理
 * - 表单数据收集
 * - 字符计数器
 * - 自动保存
 * - 多选组件管理
 * - 通知系统
 */

class FormUtils {
    constructor(formSelector, options = {}) {
        this.form = typeof formSelector === 'string' ? document.querySelector(formSelector) : formSelector;
        this.options = {
            enableAutoSave: options.enableAutoSave || false,
            autoSaveInterval: options.autoSaveInterval || 30000, // 30秒
            enableCharacterCounter: options.enableCharacterCounter !== false,
            enableFileUpload: options.enableFileUpload !== false,
            enableNotification: options.enableNotification !== false,
            ...options
        };
        
        this.multiSelectInstances = {};
        this.isModified = false;
        this.autoSaveInterval = null;
        
        if (this.form) {
            this.init();
        }
    }

    /**
     * 初始化表单工具
     * 调用示例：
     * const formUtils = new FormUtils('#myForm', {
     *     enableAutoSave: true,
     *     autoSaveInterval: 30000
     * });
     */
    init() {
        this.bindFormEvents();
        
        if (this.options.enableCharacterCounter) {
            this.initializeCharacterCounters();
        }
        
        if (this.options.enableFileUpload) {
            this.initializeFileUploads();
        }
        
        if (this.options.enableAutoSave) {
            this.initializeAutoSave();
        }
        
        console.log('FormUtils initialized for form:', this.form);
    }

    /**
     * 绑定表单基础事件
     * 自动处理表单提交、输入变化、取消操作等
     */
    bindFormEvents() {
        if (!this.form) return;

        // 表单提交事件
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleFormSubmit(e);
        });

        // 输入字段变化事件
        const inputs = this.form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('input', () => {
                this.markAsModified();
                this.validateField(input);
            });
        });

        // 取消按钮
        const cancelBtn = this.form.querySelector('[data-cancel], #btn-cancel, .btn-cancel');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                this.handleCancel();
            });
        }
    }

    /**
     * 处理表单提交
     * 包含表单验证、数据收集、提交状态管理
     * 使用示例：
     * formUtils.handleFormSubmit(); // 手动触发提交处理
     */
    handleFormSubmit(e) {
        if (!this.validateForm()) {
            this.showNotification('请检查表单中的错误信息', 'error');
            return false;
        }

        const submitBtn = this.form.querySelector('button[type="submit"]');
        const originalText = submitBtn ? submitBtn.innerHTML : '';
        
        // 显示加载状态
        if (submitBtn) {
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
        }
        
        // 收集表单数据
        const formData = this.collectFormData();
        
        console.log('提交表单数据:', formData);
        
        // 触发自定义提交事件，允许外部处理
        const submitEvent = new CustomEvent('formutils:submit', {
            detail: { formData, formUtils: this },
            cancelable: true
        });
        
        if (!this.form.dispatchEvent(submitEvent)) {
            // 如果事件被取消，恢复按钮状态
            if (submitBtn) {
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
            return false;
        }
        
        // 如果没有外部处理器，执行默认行为（模拟API调用）
        if (!submitEvent.defaultPrevented) {
            setTimeout(() => {
                // 恢复按钮状态
                if (submitBtn) {
                    submitBtn.classList.remove('loading');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
                
                this.showNotification('表单已成功保存！', 'success');
                this.markAsClean();
            }, 2000);
        }
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
        Object.entries(this.multiSelectInstances).forEach(([key, instance]) => {
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
     * 验证整个表单
     * 返回布尔值表示表单是否有效
     * 使用示例：
     * if (formUtils.validateForm()) {
     *     // 表单有效，可以提交
     * }
     */
    validateForm() {
        let isValid = true;
        
        // 验证必填字段
        const requiredFields = this.form.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });
        
        return isValid;
    }

    /**
     * 验证单个字段
     * 支持必填、长度、格式等验证规则
     * 使用示例：
     * formUtils.validateField(document.getElementById('email'));
     */
    validateField(field) {
        const value = field.value.trim();
        const fieldName = field.name || field.id;
        
        // 移除之前的错误状态
        field.classList.remove('error');
        this.clearFieldError(field.parentElement);
        
        // 必填字段验证
        if (field.hasAttribute('required') && !value) {
            field.classList.add('error');
            this.setFieldError(field.parentElement, '此字段为必填项');
            return false;
        }
        
        // 特定字段验证
        switch (fieldName) {
            case 'name_cn':
            case 'name_en':
            case 'title_cn':
            case 'title_en':
                if (value.length > 200) {
                    field.classList.add('error');
                    this.setFieldError(field.parentElement, '标题长度不能超过200字符');
                    return false;
                }
                break;
                
            case 'duration':
                if (value && !this.isValidDuration(value)) {
                    field.classList.add('error');
                    this.setFieldError(field.parentElement, '请输入有效的时间格式(如: 12:35)');
                    return false;
                }
                break;
                
            case 'email':
                if (value && !this.isValidEmail(value)) {
                    field.classList.add('error');
                    this.setFieldError(field.parentElement, '请输入有效的邮箱地址');
                    return false;
                }
                break;
        }
        
        return true;
    }

    /**
     * 设置字段错误信息
     * 在字段下方显示错误提示
     */
    setFieldError(container, message) {
        this.clearFieldError(container);
        const errorDiv = document.createElement('div');
        errorDiv.className = 'validation-error';
        errorDiv.innerHTML = `<i class="bi bi-exclamation-circle"></i> ${message}`;
        errorDiv.style.color = 'var(--danger, #dc3545)';
        errorDiv.style.fontSize = '0.75rem';
        errorDiv.style.marginTop = '0.25rem';
        container.appendChild(errorDiv);
    }

    /**
     * 清除字段错误信息
     */
    clearFieldError(container) {
        const existingError = container.querySelector('.validation-error');
        if (existingError) {
            existingError.remove();
        }
    }

    /**
     * 初始化字符计数器
     * 为所有带有 maxlength 属性的输入框添加字符计数显示
     * 使用示例：
     * formUtils.initializeCharacterCounters();
     */
    initializeCharacterCounters() {
        const textareas = this.form.querySelectorAll('textarea[maxlength], input[maxlength]');
        textareas.forEach(textarea => {
            this.updateCharacterCounter(textarea);
            textarea.addEventListener('input', () => {
                this.updateCharacterCounter(textarea);
            });
        });
    }

    /**
     * 更新字符计数器显示
     * 根据输入长度更新计数器，并改变颜色提示
     */
    updateCharacterCounter(field) {
        const maxLength = parseInt(field.getAttribute('maxlength'));
        const currentLength = field.value.length;
        const formText = field.parentElement.querySelector('.form-text');
        
        if (formText && maxLength) {
            const percentage = (currentLength / maxLength) * 100;
            const originalText = formText.textContent.split('(')[0];
            
            formText.textContent = `${originalText}(${currentLength}/${maxLength})`;
            
            // 更新样式
            formText.classList.remove('warning', 'danger');
            if (percentage > 90) {
                formText.classList.add('danger');
            } else if (percentage > 75) {
                formText.classList.add('warning');
            }
        }
    }

    /**
     * 初始化文件上传功能
     * 自动为文件输入框添加预览和验证功能
     * 使用示例：
     * formUtils.initializeFileUploads();
     */
    initializeFileUploads() {
        const fileInputs = this.form.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                this.handleFileUpload(e);
            });
        });
    }

    /**
     * 处理文件上传
     * 包含文件类型验证、大小验证、预览功能
     */
    handleFileUpload(e) {
        const file = e.target.files[0];
        const input = e.target;
        if (!file) return;
        
        // 获取预览元素
        const previewId = input.id.replace('Upload', 'Preview');
        const preview = document.getElementById(previewId);
        
        // 验证文件类型（如果是图片上传）
        if (input.accept && input.accept.includes('image/')) {
            if (!file.type.startsWith('image/')) {
                this.showNotification('请选择有效的图片文件', 'error');
                input.value = '';
                return;
            }
            
            // 验证文件大小 (5MB)
            if (file.size > 5 * 1024 * 1024) {
                this.showNotification('图片文件不能超过5MB', 'error');
                input.value = '';
                return;
            }
            
            // 预览图片
            if (preview) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.src = e.target.result;
                    preview.classList.add('preview-updating');
                    setTimeout(() => {
                        preview.classList.remove('preview-updating');
                    }, 300);
                };
                reader.readAsDataURL(file);
            }
        }
        
        this.showNotification(`文件 ${file.name} 已选择`, 'success');
        this.markAsModified();
    }

    /**
     * 初始化多选组件
     * 创建和管理多选下拉组件实例
     * 使用示例：
     * formUtils.initializeMultiSelect('tags', {
     *     container: '#tagsMultiSelect',
     *     data: [{ id: '1', text: '标签1' }],
     *     selected: [{ id: '1', text: '标签1' }]
     * });
     */
    initializeMultiSelect(key, options) {
        if (!window.MultiSelectDropdown) {
            console.warn('MultiSelectDropdown 组件未找到，请确保已引入相关JS文件');
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
            this.markAsModified();
            
            // 触发自定义事件
            this.form.dispatchEvent(new CustomEvent('formutils:multiselect:change', {
                detail: { key, ...e.detail }
            }));
        });
        
        this.multiSelectInstances[key] = instance;
        return instance;
    }

    /**
     * 初始化自动保存功能
     * 定期自动保存表单数据到本地存储
     * 使用示例：
     * formUtils.initializeAutoSave();
     */
    initializeAutoSave() {
        this.autoSaveInterval = setInterval(() => {
            if (this.isModified && this.validateForm()) {
                this.autoSave();
            }
        }, this.options.autoSaveInterval);
    }

    /**
     * 执行自动保存
     * 将表单数据保存到 localStorage
     */
    autoSave() {
        const data = this.collectFormData();
        const formId = this.form.id || 'form';
        localStorage.setItem(`${formId}_autoSave`, JSON.stringify(data));
        console.log('表单已自动保存');
        
        // 触发自动保存事件
        this.form.dispatchEvent(new CustomEvent('formutils:autosave', {
            detail: { data }
        }));
    }

    /**
     * 加载自动保存的数据
     * 从 localStorage 恢复之前保存的表单数据
     */
    loadAutoSave() {
        const formId = this.form.id || 'form';
        const savedData = localStorage.getItem(`${formId}_autoSave`);
        
        if (savedData) {
            try {
                const data = JSON.parse(savedData);
                this.populateForm(data);
                this.showNotification('已恢复自动保存的数据', 'info');
            } catch (error) {
                console.error('加载自动保存数据失败:', error);
            }
        }
    }

    /**
     * 填充表单数据
     * 根据数据对象填充表单字段
     */
    populateForm(data) {
        Object.entries(data).forEach(([key, value]) => {
            const field = this.form.querySelector(`[name="${key}"]`);
            if (field) {
                if (field.type === 'checkbox') {
                    field.checked = value;
                } else if (field.type === 'radio') {
                    const radioInput = this.form.querySelector(`[name="${key}"][value="${value}"]`);
                    if (radioInput) radioInput.checked = true;
                } else {
                    field.value = value;
                }
            }
        });
    }

    /**
     * 标记表单为已修改状态
     * 更新页面标题，启用离开页面确认
     */
    markAsModified() {
        this.isModified = true;
        const title = document.title.replace(/^\* /, '');
        document.title = '* ' + title;
    }

    /**
     * 标记表单为未修改状态
     * 清除修改标记
     */
    markAsClean() {
        this.isModified = false;
        document.title = document.title.replace(/^\* /, '');
    }

    /**
     * 处理取消操作
     * 如果表单已修改，询问用户确认后返回上一页
     */
    handleCancel() {
        if (this.isModified) {
            if (confirm('您有未保存的更改，确定要离开吗？')) {
                window.history.back();
            }
        } else {
            window.history.back();
        }
    }

    /**
     * 显示通知消息
     * 使用 Toast 组件显示操作反馈
     * 使用示例：
     * formUtils.showNotification('保存成功', 'success');
     */
    showNotification(message, type = 'info') {
        if (window.AdminCommon && window.AdminCommon.showToast) {
            window.AdminCommon.showToast(message, type);
        } else {
            // 降级处理
            console.log(`${type.toUpperCase()}: ${message}`);
            alert(message);
        }
    }

    /**
     * 验证时长格式 (MM:SS)
     */
    isValidDuration(duration) {
        const regex = /^\d{1,3}:[0-5]\d$/;
        return regex.test(duration);
    }

    /**
     * 验证邮箱格式
     */
    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    /**
     * 获取多选组件实例
     * 使用示例：
     * const tagsInstance = formUtils.getMultiSelectInstance('tags');
     * tagsInstance.setSelected([{ id: '1', text: '新标签' }]);
     */
    getMultiSelectInstance(key) {
        return this.multiSelectInstances[key];
    }

    /**
     * 重置表单
     * 清空所有字段和多选组件
     */
    resetForm() {
        this.form.reset();
        
        // 重置多选组件
        Object.values(this.multiSelectInstances).forEach(instance => {
            if (instance && typeof instance.setSelected === 'function') {
                instance.setSelected([]);
            }
        });
        
        // 清除错误状态
        this.form.querySelectorAll('.error').forEach(field => {
            field.classList.remove('error');
        });
        
        this.form.querySelectorAll('.validation-error').forEach(error => {
            error.remove();
        });
        
        this.markAsClean();
    }

    /**
     * 销毁表单工具实例
     * 清理定时器和事件监听器
     */
    destroy() {
        // 清理自动保存定时器
        if (this.autoSaveInterval) {
            clearInterval(this.autoSaveInterval);
        }
        
        // 销毁多选组件
        Object.values(this.multiSelectInstances).forEach(instance => {
            if (instance && typeof instance.destroy === 'function') {
                instance.destroy();
            }
        });
        
        console.log('FormUtils 已销毁');
    }
}

/**
 * 表单工具辅助函数集合
 * 提供独立的表单相关工具函数
 */
const FormUtilsHelper = {
    /**
     * 快速创建表单工具实例
     * 使用示例：
     * const formUtils = FormUtilsHelper.create('#myForm');
     */
    create(formSelector, options = {}) {
        return new FormUtils(formSelector, options);
    },

    /**
     * 为页面上所有表单初始化基础功能
     * 使用示例：
     * FormUtilsHelper.initializeAllForms();
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
     * 当页面有未保存的修改时，询问用户确认
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
                e.returnValue = '您有未保存的更改，确定要离开吗？';
            }
        });
    }
};

// 全局导出
window.FormUtils = FormUtils;
window.FormUtilsHelper = FormUtilsHelper;

// 页面加载完成后的自动初始化
document.addEventListener('DOMContentLoaded', () => {
    // 为带有 data-form-utils 属性的表单自动初始化
    document.querySelectorAll('form[data-form-utils]').forEach(form => {
        try {
            const options = JSON.parse(form.dataset.formUtils || '{}');
            const instance = new FormUtils(form, options);
            form._formUtils = instance; // 保存引用以便后续访问
        } catch (error) {
            console.error('FormUtils 自动初始化失败:', error);
        }
    });
});