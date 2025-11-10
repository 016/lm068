/**
 * 表单验证模块
 * 负责所有表单字段验证逻辑
 */

class FormValidator {
    constructor(formUtils) {
        this.formUtils = formUtils;
        this.form = formUtils.form;
    }

    /**
     * 验证整个表单
     */
    validateForm() {
        let isValid = true;

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
     */
    validateField(field) {
        const value = field.value.trim();
        const fieldName = field.name || field.id;

        // 移除之前的错误状态
        field.classList.remove('is-invalid');
        this.clearFieldError(field.parentElement);

        // 必填字段验证
        if (field.hasAttribute('required') && !value) {
            field.classList.add('is-invalid');
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
                    field.classList.add('is-invalid');
                    this.setFieldError(field.parentElement, '标题长度不能超过200字符');
                    return false;
                }
                break;

            case 'duration':
                if (value && !this.isValidDuration(value)) {
                    field.classList.add('is-invalid');
                    this.setFieldError(field.parentElement, '请输入有效的时间格式(如: 123)');
                    return false;
                }
                break;

            case 'email':
                if (value && !this.isValidEmail(value)) {
                    field.classList.add('is-invalid');
                    this.setFieldError(field.parentElement, '请输入有效的邮箱地址');
                    return false;
                }
                break;

            case 'icon_class':
                const iconValidation = this.validateIconClass(value);
                if (!iconValidation.isValid) {
                    field.classList.add('is-invalid');
                    this.setFieldError(field.parentElement, iconValidation.message);
                    return false;
                }
                break;
        }

        return true;
    }

    /**
     * 验证图标类名
     */
    validateIconClass(iconClass, required = false) {
        if (!iconClass) {
            if (required) {
                return { isValid: false, message: '图标类名不能为空' };
            }
            return { isValid: true, message: '' };
        }

        if (!iconClass.startsWith('bi-')) {
            return { isValid: false, message: '图标类名必须以 "bi-" 开头' };
        }

        return { isValid: true, message: '' };
    }

    /**
     * 验证英文名称
     */
    validateEnglishName(nameEn, required = false) {
        if (!nameEn) {
            if (required) {
                return { isValid: false, message: '英文名称不能为空' };
            }
            return { isValid: true, message: '' };
        }

        if (!/^[a-zA-Z0-9\s\-_]+$/.test(nameEn)) {
            return {
                isValid: false,
                message: '英文名称只能包含字母、数字、空格、连字符和下划线'
            };
        }

        return { isValid: true, message: '' };
    }

    /**
     * 验证中文名称长度
     */
    validateChineseName(nameCn, minLength = 1, maxLength = 20, required = true) {
        if (!nameCn || nameCn.trim().length === 0) {
            if (required) {
                return { isValid: false, message: '名称不能为空' };
            }
            return { isValid: true, message: '' };
        }

        const trimmedName = nameCn.trim();

        if (trimmedName.length < minLength) {
            return {
                isValid: false,
                message: `名称至少需要${minLength}个字符`
            };
        }

        if (trimmedName.length > maxLength) {
            return {
                isValid: false,
                message: `名称不能超过${maxLength}个字符`
            };
        }

        return { isValid: true, message: '' };
    }

    /**
     * 验证时长格式
     */
    isValidDuration(duration) {
        return /^\d+$/.test(duration);
    }

    /**
     * 验证邮箱格式
     */
    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    /**
     * 设置字段错误信息
     */
    setFieldError(container, message) {
        this.clearFieldError(container);
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.innerHTML = `<i class="bi bi-exclamation-circle"></i> ${message}`;
        container.appendChild(errorDiv);
    }

    /**
     * 清除字段错误信息
     */
    clearFieldError(container) {
        const existingError = container.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }
    }

    /**
     * 清除所有表单错误
     */
    clearAllErrors() {
        this.form.querySelectorAll('.is-invalid').forEach(field => {
            field.classList.remove('is-invalid');
        });

        this.form.querySelectorAll('.invalid-feedback').forEach(error => {
            error.remove();
        });
    }

    /**
     * 显示表单验证错误(用于AJAX返回)
     */
    displayFormErrors(errors) {
        Object.entries(errors).forEach(([fieldName, errorMessage]) => {
            const field = this.form.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.classList.add('is-invalid');

                const existingError = field.parentNode.querySelector('.invalid-feedback');
                if (existingError) {
                    existingError.remove();
                }

                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.textContent = errorMessage;
                field.parentNode.appendChild(errorDiv);
            }
        });
    }
}

window.FormValidator = FormValidator;