// 联系我们页面专属JS - f-contact-us_5.js
// 优化版本 - 支持新的在线留言tab切换和改进的交互体验

document.addEventListener('DOMContentLoaded', function() {
    // 修复tab切换bug
    initTabSwitching();
    
    // 表单验证和提交处理
    initContactForm();
    
    // 初始化其他交互功能
    initOtherFeatures();
});

// 修复tab切换bug - 确保只显示活动tab，支持新增的在线留言tab
function initTabSwitching() {
    const tabLinks = document.querySelectorAll('[data-bs-toggle="pill"]');
    
    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // 移除所有active状态
            tabLinks.forEach(otherLink => {
                otherLink.classList.remove('active');
                const targetId = otherLink.getAttribute('href');
                const targetPane = document.querySelector(targetId);
                if (targetPane) {
                    targetPane.classList.remove('show', 'active');
                }
            });
            
            // 激活当前tab
            this.classList.add('active');
            const targetId = this.getAttribute('href');
            const targetPane = document.querySelector(targetId);
            if (targetPane) {
                targetPane.classList.add('show', 'active');
            }
        });
        
        // 添加显示动画
        link.addEventListener('shown.bs.tab', function(e) {
            const targetId = e.target.getAttribute('href');
            const targetContent = document.querySelector(targetId);
            
            if (targetContent) {
                // 添加进入动画
                targetContent.style.opacity = '0';
                targetContent.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    targetContent.style.transition = 'all 0.3s ease';
                    targetContent.style.opacity = '1';
                    targetContent.style.transform = 'translateY(0)';
                }, 50);
            }
        });
    });
}

// 初始化联系表单功能
function initContactForm() {
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        // 实时表单验证
        const inputs = contactForm.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            // 输入时的实时验证
            input.addEventListener('input', function() {
                validateField(this);
            });
            
            // 失焦时验证
            input.addEventListener('blur', function() {
                validateField(this);
            });
        });
        
        // 表单提交处理
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // 验证所有字段
            let isValid = true;
            inputs.forEach(input => {
                if (!validateField(input)) {
                    isValid = false;
                }
            });
            
            if (isValid) {
                submitForm(this);
            } else {
                showMessage('请检查并完善表单信息', 'warning');
                // 聚焦到第一个错误字段
                const firstError = contactForm.querySelector('.is-invalid');
                if (firstError) {
                    firstError.focus();
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }
    
    // 电话号码格式化
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\\D/g, '');
            if (value.startsWith('1') && value.length <= 11) {
                // 手机号码格式化: 138 1234 5678
                value = value.replace(/(\\d{3})(\\d{4})(\\d{4})/, '$1 $2 $3');
            } else if (value.startsWith('0') || value.startsWith('400')) {
                // 固定电话格式化: 010-12345678 或 400-123-4567
                value = value.replace(/(\\d{3,4})(\\d{3,4})(\\d{4})/, '$1-$2-$3');
            }
            e.target.value = value;
        });
    }
    
    // 字符计数器
    const messageTextarea = document.getElementById('message');
    if (messageTextarea) {
        const maxLength = 500;
        const counterDiv = document.createElement('div');
        counterDiv.className = 'text-end text-muted small mt-1';
        messageTextarea.parentNode.appendChild(counterDiv);
        
        function updateCounter() {
            const remaining = maxLength - messageTextarea.value.length;
            counterDiv.innerHTML = `
                <i class="bi bi-pencil me-1"></i>
                还可输入 <span class="${remaining < 50 ? 'text-warning' : remaining < 20 ? 'text-danger' : ''}">${remaining}</span> 个字符
            `;
        }
        
        messageTextarea.addEventListener('input', updateCounter);
        updateCounter();
    }
    
    // 选择框变化处理
    const subjectSelect = document.getElementById('subject');
    if (subjectSelect) {
        subjectSelect.addEventListener('change', function() {
            const messageTextarea = document.getElementById('message');
            if (messageTextarea && !messageTextarea.value) {
                const templates = {
                    'technical': '您好，我在使用过程中遇到了以下技术问题：\\n\\n1. 问题描述：\\n2. 出现时间：\\n3. 操作步骤：\\n4. 期望结果：\\n\\n请协助解决，谢谢！',
                    'business': '您好，我希望了解贵司的商务合作方案：\\n\\n1. 合作需求：\\n2. 预期目标：\\n3. 联系方式：\\n\\n期待您的回复！',
                    'feedback': '您好，我想提供以下意见和建议：\\n\\n1. 使用体验：\\n2. 改进建议：\\n3. 其他反馈：\\n\\n谢谢关注用户体验！',
                    'complaint': '您好，我需要投诉以下问题：\\n\\n1. 问题描述：\\n2. 发生时间：\\n3. 影响程度：\\n4. 期望处理：\\n\\n请尽快处理，谢谢！'
                };
                
                if (templates[this.value]) {
                    messageTextarea.value = templates[this.value];
                    messageTextarea.focus();
                    // 触发输入事件来更新字符计数器
                    messageTextarea.dispatchEvent(new Event('input'));
                }
            }
        });
    }
}

// 字段验证函数
function validateField(field) {
    const value = field.value.trim();
    const fieldType = field.type || field.tagName.toLowerCase();
    let isValid = true;
    let message = '';
    
    // 移除之前的验证状态
    field.classList.remove('is-valid', 'is-invalid');
    removeFieldFeedback(field);
    
    // 必填字段检查
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        message = '此字段为必填项';
    } else if (value) {
        // 特定字段验证
        switch (fieldType) {
            case 'email':
                const emailRegex = /^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/;
                if (!emailRegex.test(value)) {
                    isValid = false;
                    message = '请输入有效的邮箱地址';
                }
                break;
                
            case 'tel':
                const phoneRegex = /^[1][3-9]\\d{9}$|^0\\d{2,3}-?\\d{7,8}$|^400-?\\d{3}-?\\d{4}$/;
                if (value && !phoneRegex.test(value.replace(/\\s|-/g, ''))) {
                    isValid = false;
                    message = '请输入有效的电话号码';
                }
                break;
                
            case 'text':
                if (field.id === 'name' && value.length < 2) {
                    isValid = false;
                    message = '姓名至少需要2个字符';
                }
                break;
                
            case 'textarea':
                if (value.length < 10) {
                    isValid = false;
                    message = '留言内容至少需要10个字符';
                } else if (value.length > 500) {
                    isValid = false;
                    message = '留言内容不能超过500个字符';
                }
                break;
        }
    }
    
    // 应用验证结果
    if (isValid && value) {
        field.classList.add('is-valid');
        addFieldFeedback(field, '输入正确', 'valid-feedback');
    } else if (!isValid) {
        field.classList.add('is-invalid');
        addFieldFeedback(field, message, 'invalid-feedback');
    }
    
    return isValid;
}

// 添加字段反馈信息
function addFieldFeedback(field, message, className) {
    const feedback = document.createElement('div');
    feedback.className = className;
    feedback.innerHTML = `<i class="bi bi-${className === 'valid-feedback' ? 'check-circle-fill' : 'exclamation-circle-fill'} me-1"></i>${message}`;
    field.parentNode.appendChild(feedback);
}

// 移除字段反馈信息
function removeFieldFeedback(field) {
    const existingFeedback = field.parentNode.querySelectorAll('.valid-feedback, .invalid-feedback');
    existingFeedback.forEach(feedback => feedback.remove());
}

// 表单提交处理
function submitForm(form) {
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // 显示加载状态
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
        正在提交...
    `;
    
    // 收集表单数据
    const formData = new FormData(form);
    const data = {
        name: form.name.value,
        email: form.email.value,
        phone: form.phone.value || '',
        subject: form.subject.value,
        message: form.message.value,
        timestamp: new Date().toISOString(),
        userAgent: navigator.userAgent
    };
    
    // 模拟提交过程（在实际项目中这里应该是真实的API调用）
    setTimeout(() => {
        // 恢复按钮状态
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        
        // 显示成功消息
        showMessage('感谢您的留言！我们将在24小时内回复您。', 'success');
        
        // 重置表单
        form.reset();
        form.querySelectorAll('.is-valid, .is-invalid').forEach(field => {
            field.classList.remove('is-valid', 'is-invalid');
        });
        form.querySelectorAll('.valid-feedback, .invalid-feedback').forEach(feedback => {
            feedback.remove();
        });
        
        // 发送确认邮件提示
        setTimeout(() => {
            showMessage('确认邮件已发送到您的邮箱，请查收。', 'info');
        }, 2000);
        
    }, 2000);
}

// 消息提示函数
function showMessage(text, type = 'info') {
    // 创建消息元素
    const messageDiv = document.createElement('div');
    messageDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    messageDiv.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border: none;
    `;
    
    const icons = {
        success: 'check-circle-fill',
        warning: 'exclamation-triangle-fill',
        danger: 'x-circle-fill',
        info: 'info-circle-fill'
    };
    
    messageDiv.innerHTML = `
        <i class="bi bi-${icons[type] || icons.info} me-2"></i>
        ${text}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(messageDiv);
    
    // 自动消失
    setTimeout(() => {
        messageDiv.classList.remove('show');
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.parentNode.removeChild(messageDiv);
            }
        }, 150);
    }, 5000);
}

// 初始化其他交互功能
function initOtherFeatures() {
    // 联系方式卡片点击效果
    const contactItems = document.querySelectorAll('.contact-item');
    contactItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // 复制联系方式功能
    function addCopyFunctionality(selector, text) {
        const element = document.querySelector(selector);
        if (element) {
            element.style.cursor = 'pointer';
            element.title = '点击复制';
            
            element.addEventListener('click', function() {
                navigator.clipboard.writeText(text).then(() => {
                    showMessage('已复制到剪贴板', 'success');
                }).catch(() => {
                    // 兼容性处理
                    const textArea = document.createElement('textarea');
                    textArea.value = text;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    showMessage('已复制到剪贴板', 'success');
                });
            });
        }
    }
    
    // 添加复制功能到联系方式
    addCopyFunctionality('.contact-item .fs-4', '400-123-4567');
    addCopyFunctionality('.contact-item a[href^="mailto"]', 'support@example.com');
    
    // 键盘快捷键支持
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K 快速聚焦到搜索或表单
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const nameInput = document.getElementById('name');
            if (nameInput) {
                // 切换到在线留言标签
                const onlineMessageTab = document.querySelector('a[href="#online-message"]');
                if (onlineMessageTab) {
                    onlineMessageTab.click();
                }
                
                setTimeout(() => {
                    nameInput.focus();
                    nameInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 300);
            }
        }
        
        // Esc 键关闭消息提示
        if (e.key === 'Escape') {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const closeBtn = alert.querySelector('.btn-close');
                if (closeBtn) {
                    closeBtn.click();
                }
            });
        }
    });
    
    // 页面可见性API - 当页面重新可见时检查表单状态
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            // 页面重新可见，检查是否有未保存的表单数据
            const formInputs = document.querySelectorAll('#contactForm input, #contactForm select, #contactForm textarea');
            let hasData = false;
            
            formInputs.forEach(input => {
                if (input.value.trim()) {
                    hasData = true;
                }
            });
            
            if (hasData) {
                console.log('检测到未提交的表单数据');
            }
        }
    });
    
    // 增加社交媒体按钮交互效果
    const socialButtons = document.querySelectorAll('.contact-item .btn-outline-danger, .contact-item .btn-outline-info, .contact-item .btn-outline-secondary, .contact-item .btn-outline-success');
    socialButtons.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            this.style.transition = 'all 0.2s ease';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

}