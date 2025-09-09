/**
 * Tag Edit Form JavaScript
 * 标签编辑表单交互功能
 */

document.addEventListener('DOMContentLoaded', function() {
    // 获取预览相关元素
    const previewBtn = document.getElementById('tagPreviewBtn');
    const previewIcon = document.getElementById('previewIcon');
    const previewText = document.getElementById('previewText');
    
    // 获取表单输入元素
    const nameCnInput = document.getElementById('name_cn');
    const colorClassSelect = document.getElementById('color_class');
    const iconClassSelect = document.getElementById('icon_class');
    
    // 实时预览功能
    function updateTagPreview() {
        // 获取当前值
        const name = nameCnInput.value || '标签名称';
        const colorClass = colorClassSelect.value || 'btn-outline-primary';
        const iconClass = iconClassSelect.value || 'bi-star';
        
        // 更新预览文本
        previewText.textContent = name;
        
        // 更新预览图标
        previewIcon.className = `bi ${iconClass}`;
        
        // 更新预览按钮样式 - 移除所有可能的颜色类
        previewBtn.className = 'btn';
        previewBtn.classList.add(colorClass);
    }
    
    // 绑定事件监听器
    if (nameCnInput) {
        nameCnInput.addEventListener('input', updateTagPreview);
    }
    
    if (colorClassSelect) {
        colorClassSelect.addEventListener('change', updateTagPreview);
    }
    
    if (iconClassSelect) {
        iconClassSelect.addEventListener('change', updateTagPreview);
    }
    
    // 初始化预览
    updateTagPreview();
    
    // 表单验证
    const form = document.getElementById('tagEditForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // 基本验证
            const nameCn = nameCnInput.value.trim();
            const nameEn = document.getElementById('name_en').value.trim();
            
            if (!nameCn) {
                showAlert('请输入中文标题', 'danger');
                nameCnInput.focus();
                return;
            }
            
            if (!nameEn) {
                showAlert('请输入英文标题', 'danger');
                document.getElementById('name_en').focus();
                return;
            }
            
            // 长度验证
            if (nameCn.length > 50) {
                showAlert('中文标题长度不能超过50个字符', 'danger');
                nameCnInput.focus();
                return;
            }
            
            if (nameEn.length > 50) {
                showAlert('英文标题长度不能超过50个字符', 'danger');
                document.getElementById('name_en').focus();
                return;
            }
            
            // 简介长度验证
            const shortDescCn = document.getElementById('short_desc_cn').value;
            const shortDescEn = document.getElementById('short_desc_en').value;
            
            if (shortDescCn.length > 100) {
                showAlert('中文简介长度不能超过100个字符', 'danger');
                return;
            }
            
            if (shortDescEn.length > 100) {
                showAlert('英文简介长度不能超过100个字符', 'danger');
                return;
            }
            
            // 描述长度验证
            const descCn = document.getElementById('desc_cn').value;
            const descEn = document.getElementById('desc_en').value;
            
            if (descCn.length > 500) {
                showAlert('中文描述长度不能超过500个字符', 'danger');
                return;
            }
            
            if (descEn.length > 500) {
                showAlert('英文描述长度不能超过500个字符', 'danger');
                return;
            }
            
            // 如果验证通过，可以提交表单
            showAlert('表单验证通过，正在保存...', 'success');
            
            // 这里可以添加实际的表单提交逻辑
            // 例如使用 fetch API 提交到后端
            console.log('Form data:', new FormData(form));
        });
    }
    
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
                icon_class: iconClassSelect.value,
                short_desc_cn: document.getElementById('short_desc_cn').value,
                short_desc_en: document.getElementById('short_desc_en').value
            };
            
            console.log('Preview data:', previewData);
            showAlert('预览功能开发中...', 'info');
        });
    }
});