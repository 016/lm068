// f-contact-us_2.js - 联系我们页面专属脚本

document.addEventListener('DOMContentLoaded', function() {
    // 初始化标签切换功能
    initTabSwitching();
    
    // 初始化联系表单功能
    initContactForm();
    
    // 初始化 Markdown 内容渲染
    initMarkdownRendering();
});

// 初始化标签切换功能
function initTabSwitching() {
    const tabLinks = document.querySelectorAll('[data-bs-toggle="pill"]');
    
    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // 移除所有活动状态
            tabLinks.forEach(l => l.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('show', 'active');
            });
            
            // 激活当前标签
            this.classList.add('active');
            const targetId = this.getAttribute('href');
            const targetPane = document.querySelector(targetId);
            
            if (targetPane) {
                targetPane.classList.add('show', 'active');
                
                // 平滑滚动到内容区域
                targetPane.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }
        });
    });
}

// 初始化联系表单功能
function initContactForm() {
    const form = document.getElementById('contactForm');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // 获取表单数据
            const formData = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                subject: document.getElementById('subject').value,
                message: document.getElementById('message').value
            };
            
            // 简单验证
            if (!formData.name || !formData.email || !formData.subject || !formData.message) {
                showAlert('请填写所有必填字段', 'danger');
                return;
            }
            
            if (!validateEmail(formData.email)) {
                showAlert('请输入有效的邮箱地址', 'danger');
                return;
            }
            
            // 提交表单
            submitContactForm(formData);
        });
    }
}

// 提交联系表单
function submitContactForm(formData) {
    const submitBtn = document.querySelector('#contactForm button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // 显示加载状态
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>提交中...';
    submitBtn.disabled = true;
    
    // 模拟提交过程
    setTimeout(() => {
        showAlert('感谢您的留言，我们会尽快回复您！', 'success');
        document.getElementById('contactForm').reset();
        
        // 恢复按钮状态
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 2000);
}

// 邮箱验证
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// 显示提示消息
function showAlert(message, type = 'info') {
    // 创建提示框
    const alertElement = document.createElement('div');
    alertElement.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertElement.style.cssText = `
        top: 100px;
        right: 20px;
        z-index: 10000;
        min-width: 300px;
        max-width: 500px;
    `;
    
    alertElement.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // 添加到页面
    document.body.appendChild(alertElement);
    
    // 自动移除
    setTimeout(() => {
        alertElement.remove();
    }, 5000);
}

// 初始化 Markdown 内容渲染
function initMarkdownRendering() {
    const markdownContents = document.querySelectorAll('.markdown-content');
    
    markdownContents.forEach(content => {
        // 简单的 Markdown 渲染 (仅支持基本语法)
        let html = content.innerHTML;
        
        // 处理标题
        html = html.replace(/^### (.*$)/gm, '<h3>$1</h3>');
        html = html.replace(/^## (.*$)/gm, '<h2>$1</h2>');
        
        // 处理列表
        html = html.replace(/^- (.*$)/gm, '<li>$1</li>');
        html = html.replace(/(<li>.*<\/li>)/gs, '<ul>$1</ul>');
        
        // 处理段落
        html = html.replace(/\n\n/g, '</p><p>');
        html = '<p>' + html + '</p>';
        
        // 清理空段落
        html = html.replace(/<p>\s*<\/p>/g, '');
        html = html.replace(/<p>\s*(<h[1-6])/g, '$1');
        html = html.replace(/(<\/h[1-6]>)\s*<\/p>/g, '$1');
        html = html.replace(/<p>\s*(<ul>)/g, '$1');
        html = html.replace(/(<\/ul>)\s*<\/p>/g, '$1');
        
        content.innerHTML = html;
    });
}

// 平滑滚动到锚点
function smoothScrollToAnchor(target) {
    const element = document.querySelector(target);
    if (element) {
        const offsetTop = element.getBoundingClientRect().top + window.pageYOffset - 100;
        window.scrollTo({
            top: offsetTop,
            behavior: 'smooth'
        });
    }
}