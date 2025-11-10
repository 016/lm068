/**
 * Admin Utils - 管理后台工具函数
 *
 * 依赖：无（最底层工具函数）
 *
 * 提供功能：
 * - 日期格式化
 * - 数字格式化
 * - 表单验证工具
 * - 字段错误显示/清除
 * - 字符计数器
 */

(function() {
    'use strict';

    // ========== UTILITY FUNCTIONS ==========
    function formatDate(date) {
        return new Date(date).toLocaleDateString('zh-CN');
    }

    function formatNumber(num) {
        if (num >= 1000000) {
            return (num / 1000000).toFixed(1) + 'M';
        } else if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K';
        }
        return num.toString();
    }

    function formatLargeNumber(num) {
        return formatNumber(num);
    }

    // ========== FORM VALIDATION UTILITIES ==========
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function showFieldError(field, message) {
        clearFieldError(field);
        field.style.borderColor = 'var(--danger)';

        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.style.color = 'var(--danger)';
        errorDiv.style.fontSize = '0.75rem';
        errorDiv.style.marginTop = '0.25rem';
        errorDiv.textContent = message;

        field.parentNode.appendChild(errorDiv);
    }

    function clearFieldError(field) {
        field.style.borderColor = '';
        const errorDiv = field.parentNode.querySelector('.field-error');
        if (errorDiv) {
            errorDiv.remove();
        }
    }

    function clearValidation(e) {
        clearFieldError(e.target);
    }

    function validateField(e) {
        const field = e.target;
        const value = field.value.trim();

        if (!value && field.hasAttribute('required')) {
            showFieldError(field, '此字段为必填项');
        } else if (field.type === 'email' && value && !isValidEmail(value)) {
            showFieldError(field, '请输入有效的邮箱地址');
        } else {
            clearFieldError(field);
        }
    }

    // ========== CHARACTER COUNTER ==========
    // 初始化字符计数器
    function initializeCharacterCounters(input_form) {
        const textareas = input_form.querySelectorAll('textarea[maxlength], input[maxlength]');
        textareas.forEach(textarea => {
            updateCharacterCounter(textarea);
            textarea.addEventListener('input', () => {
                updateCharacterCounter(textarea);
            });
        });
    }

    // 更新字符计数器
    function updateCharacterCounter(field) {
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

    // ========== GLOBAL EXPORTS ==========
    if (!window.AdminCommon) {
        window.AdminCommon = {};
    }

    window.AdminCommon.formatDate = formatDate;
    window.AdminCommon.formatNumber = formatNumber;
    window.AdminCommon.formatLargeNumber = formatLargeNumber;

    if (!window.AdminCommon.ValidationUtils) {
        window.AdminCommon.ValidationUtils = {};
    }

    window.AdminCommon.ValidationUtils.isValidEmail = isValidEmail;
    window.AdminCommon.ValidationUtils.showFieldError = showFieldError;
    window.AdminCommon.ValidationUtils.clearFieldError = clearFieldError;
    window.AdminCommon.ValidationUtils.clearValidation = clearValidation;
    window.AdminCommon.ValidationUtils.validateField = validateField;
    window.AdminCommon.ValidationUtils.initializeCharacterCounters = initializeCharacterCounters;

    console.log('Admin Utils 已加载');
})();
