/**
 * Tag Edit Form JavaScript - Version 8
 * 标签编辑表单交互功能（简化版，移除form submit处理）
 */

document.addEventListener('DOMContentLoaded', function() {
    // 获取预览相关元素
    const previewBtn = document.getElementById('tagPreviewBtn');
    const previewIcon = document.getElementById('previewIcon');
    const previewText = document.getElementById('previewText');
    
    // 获取表单输入元素
    const nameCnInput = document.getElementById('name_cn');
    const colorClassSelect = document.getElementById('color_class');
    const iconClassInput = document.getElementById('icon_class'); // 改为input
    
    // 实时预览功能
    function updateTagPreview() {
        // 获取当前值
        const name = nameCnInput.value || '标签名称';
        const colorClass = colorClassSelect.value || 'btn-outline-primary';
        let iconClass = iconClassInput.value || 'bi-star';
        
        // 清理图标类名（确保以 bi- 开头）
        if (iconClass && !iconClass.startsWith('bi-')) {
            iconClass = 'bi-' + iconClass.replace(/^bi-?/, '');
        }
        
        // 更新预览文本
        previewText.textContent = name;
        
        // 更新预览图标
        previewIcon.className = `bi ${iconClass}`;
        
        // 更新预览按钮样式 - 移除所有可能的颜色类
        previewBtn.className = 'btn';
        previewBtn.classList.add(colorClass);
        
        // 添加更新动画
        previewBtn.classList.add('preview-updating');
        setTimeout(() => {
            previewBtn.classList.remove('preview-updating');
        }, 300);
    }
    
    // 绑定事件监听器
    if (nameCnInput) {
        nameCnInput.addEventListener('input', updateTagPreview);
    }
    
    if (colorClassSelect) {
        colorClassSelect.addEventListener('change', updateTagPreview);
    }
    
    if (iconClassInput) {
        iconClassInput.addEventListener('input', updateTagPreview);
        
        // 图标输入框失去焦点时自动格式化
        iconClassInput.addEventListener('blur', function() {
            let value = this.value.trim();
            if (value && !value.startsWith('bi-')) {
                value = 'bi-' + value.replace(/^bi-?/, '');
                this.value = value;
                updateTagPreview();
            }
        });
    }
    
    // 初始化预览
    updateTagPreview();
    
    // 字符计数功能
    function setupCharacterCounter(inputId, maxLength) {
        const input = document.getElementById(inputId);
        if (!input) return;
        
        const formText = input.nextElementSibling;
        if (!formText || !formText.classList.contains('form-text')) return;
        
        function updateCounter() {
            const currentLength = input.value.length;
            const remaining = maxLength - currentLength;
            const originalText = formText.textContent.split('（')[0];
            
            if (remaining < maxLength * 0.2) {
                formText.innerHTML = `${originalText}（还可输入${remaining}个字符）`;
                formText.style.color = remaining < 10 ? 'var(--danger)' : 'var(--warning)';
            } else {
                formText.innerHTML = originalText;
                formText.style.color = '';
            }
        }
        
        input.addEventListener('input', updateCounter);
        updateCounter();
    }
    
    // 为各个输入字段设置字符计数
    setupCharacterCounter('name_cn', 50);
    setupCharacterCounter('name_en', 50);
    setupCharacterCounter('short_desc_cn', 100);
    setupCharacterCounter('short_desc_en', 100);
    setupCharacterCounter('desc_cn', 500);
    setupCharacterCounter('desc_en', 500);
    
    // 显示提示消息的函数
    function showAlert(message, type = 'info') {
        const alertContainer = document.getElementById('alertContainer');
        if (!alertContainer) return;
        
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        alertContainer.appendChild(alertDiv);
        
        // 自动移除提示（成功消息3秒，其他5秒）
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, type === 'success' ? 3000 : 5000);
    }
    
    // 取消按钮功能
    const cancelBtn = document.querySelector('.btn-outline-secondary');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            if (confirm('确定要取消编辑吗？未保存的更改将丢失。')) {
                // 这里可以添加返回列表页面的逻辑
                window.history.back();
            }
        });
    }
    
    // 预览按钮功能
    const previewFormBtn = document.querySelector('.btn-outline-primary');
    if (previewFormBtn) {
        previewFormBtn.addEventListener('click', function() {
            // 这里可以添加标签预览功能
            const previewData = {
                name_cn: nameCnInput.value,
                name_en: document.getElementById('name_en').value,
                color_class: colorClassSelect.value,
                icon_class: iconClassInput.value,
                short_desc_cn: document.getElementById('short_desc_cn').value,
                short_desc_en: document.getElementById('short_desc_en').value
            };
            
            console.log('Preview data:', previewData);
            showAlert('预览功能开发中...', 'info');
        });
    }
    
    // 图标输入框的智能提示功能
    if (iconClassInput) {
        const commonIcons = [
            'bi-star', 'bi-heart', 'bi-fire', 'bi-lightning', 'bi-trophy',
            'bi-gem', 'bi-rocket', 'bi-magic', 'bi-sun', 'bi-moon',
            'bi-music-note', 'bi-camera', 'bi-palette', 'bi-puzzle',
            'bi-gift', 'bi-balloon', 'bi-tag', 'bi-tags', 'bi-bookmark',
            'bi-collection', 'bi-folder', 'bi-archive', 'bi-box'
        ];
        
        // 添加输入提示（可以在未来扩展为下拉菜单）
        iconClassInput.addEventListener('focus', function() {
            if (!this.value) {
                this.placeholder = '如：bi-star, bi-heart, bi-fire...';
            }
        });
        
        iconClassInput.addEventListener('blur', function() {
            this.placeholder = '请输入 Bootstrap 图标类名，如 bi-star';
        });
    }
});