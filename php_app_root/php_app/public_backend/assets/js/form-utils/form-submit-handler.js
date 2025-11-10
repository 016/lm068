/**
 * 表单提交处理模块
 * 负责表单提交流程、AJAX请求、状态管理
 */

class FormSubmitHandler {
    constructor(formUtils) {
        this.formUtils = formUtils;
        this.form = formUtils.form;
    }

    /**
     * 处理表单提交
     */
    handleFormSubmit(e) {
        if (!this.formUtils.validator.validateForm()) {
            this.formUtils.showNotification('请检查表单中的错误信息', 'error');
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
        const formData = this.formUtils.collectFormData();

        console.log('提交表单数据:', formData);

        // 触发自定义提交事件
        const submitEvent = new CustomEvent('formutils:submit', {
            detail: { formData, formUtils: this.formUtils },
            cancelable: true
        });

        if (!this.form.dispatchEvent(submitEvent)) {
            // 事件被取消,恢复按钮状态
            if (submitBtn) {
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
            return false;
        }

        // 如果没有外部处理器,执行默认行为
        if (!submitEvent.defaultPrevented) {
            setTimeout(() => {
                if (submitBtn) {
                    submitBtn.classList.remove('loading');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }

                this.formUtils.showNotification('表单已成功保存!', 'success');
                this.formUtils.markAsClean();
            }, 2000);
        }
    }

    /**
     * 通用的表单提交流程
     */
    executeCommonSubmitFlow(
        formData,
        selectedVideos = [],
        confirmMessage = '确定要保存修改吗?',
        loadingText = '保存中...',
        successMessage = '信息已成功保存!',
        submitDelay = 2000
    ) {
        if (!confirm(confirmMessage)) {
            return;
        }

        const formAction = this.form.action;
        const formMethod = this.form.method || 'POST';

        if (formAction) {
            this.performRealSubmit(
                formAction,
                formMethod,
                formData,
                selectedVideos,
                loadingText,
                successMessage
            );
        } else {
            this.performMockSubmit(
                formData,
                selectedVideos,
                loadingText,
                successMessage,
                submitDelay
            );
        }
    }

    /**
     * 执行真实的AJAX提交
     */
    performRealSubmit(
        actionUrl,
        method,
        formData,
        selectedVideos,
        loadingText,
        successMessage
    ) {
        const submitBtn = this.form.querySelector('button[type="submit"]');
        const originalText = submitBtn ? submitBtn.innerHTML : '';

        // 显示加载状态
        if (submitBtn) {
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>${loadingText}`;
        }

        // 清除之前的错误
        this.formUtils.validator.clearAllErrors();

        // 准备提交数据
        const submitData = new FormData();
        Object.entries(formData).forEach(([key, value]) => {
            if (value !== null && value !== undefined) {
                submitData.append(key, value);
            }
        });

        // 提交AJAX请求
        fetch(actionUrl, {
            method: method,
            body: submitData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.formUtils.showNotification(
                        data.message || successMessage,
                        'success'
                    );
                    this.formUtils.markAsClean();

                    if (data.redirect) {
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1500);
                    }
                } else {
                    if (data.errors) {
                        this.formUtils.validator.displayFormErrors(data.errors);
                        this.formUtils.showNotification(
                            data.message || '表单验证失败,请检查错误信息',
                            'error'
                        );
                    } else {
                        this.formUtils.showNotification(
                            data.message || '操作失败',
                            'error'
                        );
                    }
                }
            })
            .catch(error => {
                console.error('提交失败:', error);
                this.formUtils.showNotification('网络错误,请稍后重试', 'error');
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.classList.remove('loading');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
    }

    /**
     * 执行模拟提交(用于测试)
     */
    performMockSubmit(
        formData,
        selectedVideos,
        loadingText,
        successMessage,
        submitDelay
    ) {
        const submitBtn = this.form.querySelector('button[type="submit"]');
        const originalText = submitBtn ? submitBtn.innerHTML : '';

        if (submitBtn) {
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>${loadingText}`;
        }

        setTimeout(() => {
            if (submitBtn) {
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }

            this.formUtils.showNotification(successMessage, 'success');
            this.formUtils.markAsClean();

            console.log('表单数据提交完成:', {
                formData,
                selectedVideos: selectedVideos.length
            });
        }, submitDelay);
    }
}

window.FormSubmitHandler = FormSubmitHandler;