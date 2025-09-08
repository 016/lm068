// f-contact-us_3.js - 联系我们页面专属脚本（第二版本）

document.addEventListener('DOMContentLoaded', function() {
    // 初始化导航菜单功能
    initNavigationMenu();
    
    // 初始化内容切换功能
    initContentSwitching();
    
    // 初始化联系表单功能
    initContactForm();
    
    // 初始化 Markdown 内容渲染
    initMarkdownRendering();
    
    // 初始化动画效果
    initAnimations();
});

// 初始化导航菜单功能
function initNavigationMenu() {
    const navGroupHeaders = document.querySelectorAll('.nav-group-header');
    
    navGroupHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const navGroup = this.parentElement;
            const isActive = navGroup.classList.contains('active');
            
            // 如果是网站协议组，允许折叠/展开
            if (this.dataset.target === 'policies') {
                navGroup.classList.toggle('active');
                
                // 更新图标
                const icon = this.querySelector('.toggle-icon');
                if (navGroup.classList.contains('active')) {
                    icon.className = 'bi bi-chevron-down toggle-icon';
                } else {
                    icon.className = 'bi bi-chevron-right toggle-icon';
                }
            }
            // 如果是联系我们组，保持激活状态并显示内容
            else if (this.dataset.target === 'contact' && !isActive) {
                // 关闭其他组
                document.querySelectorAll('.nav-group').forEach(group => {
                    if (group !== navGroup) {
                        group.classList.remove('active');
                        const toggleIcon = group.querySelector('.toggle-icon');
                        if (toggleIcon) {
                            toggleIcon.className = 'bi bi-chevron-right toggle-icon';
                        }
                    }
                });
                
                // 激活当前组
                navGroup.classList.add('active');
                
                // 激活默认内容（客服电话）
                showContent('contact-info');
                setActiveNavItem(document.querySelector('[data-content="contact-info"]'));
            }
        });
    });
}

// 初始化内容切换功能
function initContentSwitching() {
    const navItemBtns = document.querySelectorAll('.nav-item-btn');
    
    navItemBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const contentId = this.dataset.content;
            
            if (contentId) {
                showContent(contentId);
                setActiveNavItem(this);
                
                // 确保所属的导航组是展开的
                const navGroup = this.closest('.nav-group');
                if (navGroup && !navGroup.classList.contains('active')) {
                    navGroup.classList.add('active');
                    const icon = navGroup.querySelector('.toggle-icon');
                    if (icon) {
                        icon.className = 'bi bi-chevron-down toggle-icon';
                    }
                }
            }
        });
    });
}

// 显示指定内容
function showContent(contentId) {
    // 隐藏所有内容区域
    document.querySelectorAll('.content-section').forEach(section => {
        section.classList.remove('active');
    });
    
    // 显示目标内容
    const targetContent = document.getElementById(contentId);
    if (targetContent) {
        targetContent.classList.add('active');
        
        // 如果是Markdown内容，重新渲染
        if (targetContent.querySelector('.policy-card')) {
            renderMarkdownInElement(targetContent.querySelector('.policy-card'));
        }
        
        // 平滑滚动到内容顶部
        targetContent.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

// 设置活动导航项
function setActiveNavItem(activeBtn) {
    // 移除所有活动状态
    document.querySelectorAll('.nav-item-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // 设置当前活动项
    if (activeBtn) {
        activeBtn.classList.add('active');
    }
}

// 初始化联系表单功能
function initContactForm() {
    const form = document.getElementById('contactForm');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // 获取表单数据
            const formData = {
                name: document.getElementById('name').value.trim(),
                email: document.getElementById('email').value.trim(),
                subject: document.getElementById('subject').value,
                message: document.getElementById('message').value.trim()
            };
            
            // 验证表单
            if (!validateForm(formData)) {
                return;
            }
            
            // 提交表单
            submitContactForm(formData);
        });
        
        // 实时验证
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
        });
    }
}

// 验证表单
function validateForm(formData) {
    let isValid = true;
    
    // 检查必填字段
    if (!formData.name) {
        showFieldError('name', '请输入您的姓名');
        isValid = false;
    }
    
    if (!formData.email) {
        showFieldError('email', '请输入邮箱地址');
        isValid = false;
    } else if (!isValidEmail(formData.email)) {
        showFieldError('email', '请输入有效的邮箱地址');
        isValid = false;
    }
    
    if (!formData.subject) {
        showFieldError('subject', '请选择留言主题');
        isValid = false;
    }
    
    if (!formData.message) {
        showFieldError('message', '请输入留言内容');
        isValid = false;
    } else if (formData.message.length < 10) {
        showFieldError('message', '留言内容至少需要10个字符');
        isValid = false;
    }
    
    return isValid;
}

// 验证单个字段
function validateField(field) {
    const value = field.value.trim();
    clearFieldError(field.id);
    
    switch (field.id) {
        case 'name':
            if (!value) {
                showFieldError('name', '请输入您的姓名');
                return false;
            }
            break;
        case 'email':
            if (!value) {
                showFieldError('email', '请输入邮箱地址');
                return false;
            } else if (!isValidEmail(value)) {
                showFieldError('email', '请输入有效的邮箱地址');
                return false;
            }
            break;
        case 'subject':
            if (!value) {
                showFieldError('subject', '请选择留言主题');
                return false;
            }
            break;
        case 'message':
            if (!value) {
                showFieldError('message', '请输入留言内容');
                return false;
            } else if (value.length < 10) {
                showFieldError('message', '留言内容至少需要10个字符');
                return false;
            }
            break;
    }
    
    return true;
}

// 显示字段错误
function showFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (field) {
        field.classList.add('is-invalid');
        
        // 移除旧的错误信息
        clearFieldError(fieldId);
        
        // 添加新的错误信息
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        errorDiv.id = fieldId + '-error';
        
        field.parentElement.appendChild(errorDiv);
    }
}

// 清除字段错误
function clearFieldError(fieldId) {
    const field = document.getElementById(fieldId);
    const existingError = document.getElementById(fieldId + '-error');
    
    if (field) {
        field.classList.remove('is-invalid');
    }
    
    if (existingError) {
        existingError.remove();
    }
}

// 邮箱验证
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// 提交联系表单
function submitContactForm(formData) {
    const submitBtn = document.querySelector('#contactForm button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // 显示加载状态
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>发送中...';
    submitBtn.disabled = true;
    
    // 模拟提交过程
    setTimeout(() => {
        showSuccessMessage('感谢您的留言！我们会在24小时内回复您。');
        document.getElementById('contactForm').reset();
        
        // 清除所有验证错误
        document.querySelectorAll('.is-invalid').forEach(field => {
            field.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback').forEach(error => {
            error.remove();
        });
        
        // 恢复按钮状态
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 2000);
}

// 显示成功消息
function showSuccessMessage(message) {
    const alertElement = document.createElement('div');
    alertElement.className = 'alert alert-success alert-dismissible fade show position-fixed';
    alertElement.style.cssText = `
        top: 100px;
        right: 20px;
        z-index: 10000;
        min-width: 350px;
        max-width: 500px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    `;
    
    alertElement.innerHTML = `
        <i class="bi bi-check-circle me-2"></i>${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertElement);
    
    // 自动移除
    setTimeout(() => {
        if (alertElement.parentElement) {
            alertElement.remove();
        }
    }, 5000);
}

// 初始化 Markdown 内容渲染
function initMarkdownRendering() {
    document.querySelectorAll('.policy-card').forEach(card => {
        renderMarkdownInElement(card);
    });
}

// 渲染单个元素中的 Markdown 内容
function renderMarkdownInElement(element) {
    if (!element) return;
    
    let html = element.innerHTML;
    
    // 处理标题
    html = html.replace(/^### (.*$)/gm, '<h3>$1</h3>');
    html = html.replace(/^## (.*$)/gm, '<h2>$1</h2>');
    
    // 处理粗体
    html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    
    // 处理列表
    html = html.replace(/^[\s]*-[\s]+(.*$)/gm, '<li>$1</li>');
    html = html.replace(/(<li>.*<\/li>)/gs, (match) => {
        return '<ul>' + match + '</ul>';
    });
    
    // 处理段落
    html = html.split('\n\n').map(paragraph => {
        paragraph = paragraph.trim();
        if (paragraph && !paragraph.startsWith('<')) {
            return '<p>' + paragraph + '</p>';
        }
        return paragraph;
    }).join('\n\n');
    
    // 清理多余的空格和换行
    html = html.replace(/\n\s*\n/g, '\n');
    html = html.replace(/>\s+</g, '><');
    
    element.innerHTML = html;
}

// 初始化动画效果
function initAnimations() {
    // 为联系卡片添加延迟动画
    const contactCards = document.querySelectorAll('.contact-card');
    contactCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });
    
    // 观察元素进入视口时触发动画
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);
    
    // 观察所有需要动画的元素
    document.querySelectorAll('.contact-card, .message-form-card, .policy-card').forEach(el => {
        observer.observe(el);
    });
}