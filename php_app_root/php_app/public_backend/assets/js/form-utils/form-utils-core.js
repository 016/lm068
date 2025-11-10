/**
 * FormUtils 核心类 - 组合模式重构版
 * 文件: form-utils-core.js
 *
 * 职责:
 * - 管理表单实例和配置
 * - 协调各个功能模块
 * - 提供统一的API接口
 */

class FormUtils {
    constructor(formSelector, options = {}) {
        // 获取表单元素
        this.form = typeof formSelector === 'string'
            ? document.querySelector(formSelector)
            : formSelector;

        if (!this.form) {
            console.error('表单元素未找到:', formSelector);
            return;
        }

        // 合并默认配置
        this.options = {
            enableAutoSave: false,
            enableConfirmBeforeUnload: true,
            autoSaveInterval: 30000,
            enableCharacterCounter: true,
            enableFileUpload: true,
            enableNotification: true,
            enablePreview: false,
            previewConfig: {},
            ...options
        };

        // 状态管理
        this.isModified = false;
        this.handleSubmit = false;
        this.multiSelectInstances = {};

        // 初始化各个功能模块(组合模式)
        this.validator = new FormValidator(this);
        this.submitHandler = new FormSubmitHandler(this);
        this.uploader = new FormFileUploader(this);
        this.multiSelectManager = new FormMultiSelectManager(this);
        this.previewManager = new FormPreviewManager(this);
        this.persistence = new FormPersistence(this);

        // 初始化表单
        this.init();
    }

    /**
     * 初始化表单工具
     */
    init() {
        this.bindFormEvents();

        if (this.options.enableCharacterCounter) {
            this.previewManager.initializeCharacterCounters();
        }

        if (this.options.enableFileUpload) {
            this.uploader.initialize();
        }

        if (this.options.enableAutoSave) {
            this.persistence.initializeAutoSave();
        }

        if (this.options.enablePreview) {
            this.previewManager.initialize();
        }

        if (this.options.enableConfirmBeforeUnload) {
            this.initializeConfirmBeforeUnload();
        }

        console.log('FormUtils initialized for form:', this.form);
    }

    /**
     * 绑定表单基础事件
     */
    bindFormEvents() {
        // 表单提交事件
        this.form.addEventListener('submit', (e) => {
            if (this.handleSubmit) {
                e.preventDefault();
                this.submitHandler.handleFormSubmit(e);
            }
        });

        // 输入字段变化事件
        const inputs = this.form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('input', () => {
                this.markAsModified();
                this.validator.validateField(input);
            });

            // 监听 hidden input 的变化
            if (input.type === 'hidden') {
                const observer = new MutationObserver((mutations) => {
                    mutations.forEach((mutation) => {
                        if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                            this.markAsModified();
                        }
                    });
                });

                observer.observe(input, {
                    attributes: true,
                    attributeFilter: ['value']
                });
            }
        });

        // 取消按钮
        const cancelBtn = this.form.querySelector('[data-cancel], #btn-cancel, .btn-cancel');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => this.handleCancel());
        }
    }

    /**
     * 初始化离开页面确认
     */
    initializeConfirmBeforeUnload() {
        window.addEventListener('beforeunload', (e) => {
            const activeElement = e.target.activeElement;
            const isFormSubmit = activeElement && activeElement.type === 'submit';

            if (this.isModified && !isFormSubmit) {
                e.preventDefault();
                e.returnValue = '您有未保存的更改,确定要离开吗?';
            }
        });
    }

    /**
     * 标记表单为已修改状态
     */
    markAsModified() {
        this.isModified = true;
        const title = document.title.replace(/^\* /, '');
        document.title = '* ' + title;
    }

    /**
     * 标记表单为未修改状态
     */
    markAsClean() {
        this.isModified = false;
        document.title = document.title.replace(/^\* /, '');
    }

    /**
     * 处理取消操作
     */
    handleCancel() {
        window.history.back();
    }

    /**
     * 显示通知消息
     */
    showNotification(message, type = 'info') {
        if (window.AdminCommon && window.AdminCommon.showToast) {
            window.AdminCommon.showToast(message, type);
        } else {
            console.log(`${type.toUpperCase()}: ${message}`);
            alert(message);
        }
    }

    /**
     * 收集表单数据 - 委托给持久化模块
     */
    collectFormData() {
        return this.persistence.collectFormData();
    }

    /**
     * 验证表单 - 委托给验证模块
     */
    validateForm() {
        return this.validator.validateForm();
    }

    /**
     * 重置表单
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
        this.form.querySelectorAll('.is-invalid').forEach(field => {
            field.classList.remove('is-invalid');
        });

        this.form.querySelectorAll('.invalid-feedback').forEach(error => {
            error.remove();
        });

        this.markAsClean();
    }

    /**
     * 销毁表单工具实例
     */
    destroy() {
        this.persistence.destroy();

        Object.values(this.multiSelectInstances).forEach(instance => {
            if (instance && typeof instance.destroy === 'function') {
                instance.destroy();
            }
        });

        console.log('FormUtils 已销毁');
    }

    /**
     * 获取多选组件实例 - 委托给多选管理器
     */
    getMultiSelectInstance(key) {
        return this.multiSelectManager.getInstance(key);
    }

    /**
     * 初始化多选组件 - 委托给多选管理器
     */
    initializeMultiSelect(key, options) {
        return this.multiSelectManager.initialize(key, options);
    }
}

// 全局导出
window.FormUtils = FormUtils;