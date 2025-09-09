/**
 * Collection Edit Form JavaScript - Version 2
 * 合集编辑表单交互功能 - 基于标签编辑表单适配
 */

document.addEventListener('DOMContentLoaded', function() {
    // 获取预览相关元素
    const previewBtn = document.getElementById('collectionPreviewBtn');
    const previewIcon = document.getElementById('previewIcon');
    const previewText = document.getElementById('previewText');
    
    // 获取表单输入元素
    const nameCnInput = document.getElementById('name_cn');
    const colorClassSelect = document.getElementById('color_class');
    const iconClassInput = document.getElementById('icon_class');
    
    // 实时预览功能
    function updateCollectionPreview() {
        // 获取当前值
        const name = nameCnInput.value || '合集名称';
        const colorClass = colorClassSelect.value || 'btn-outline-primary';
        let iconClass = iconClassInput.value || 'bi-collection';
        
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
        nameCnInput.addEventListener('input', updateCollectionPreview);
    }
    
    if (colorClassSelect) {
        colorClassSelect.addEventListener('change', updateCollectionPreview);
    }
    
    if (iconClassInput) {
        iconClassInput.addEventListener('input', updateCollectionPreview);
        
        // 图标输入框失去焦点时自动格式化
        iconClassInput.addEventListener('blur', function() {
            let value = this.value.trim();
            if (value && !value.startsWith('bi-')) {
                value = 'bi-' + value.replace(/^bi-?/, '');
                this.value = value;
                updateCollectionPreview();
            }
        });
    }
    
    // 初始化预览
    updateCollectionPreview();
    
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
    
    // 图标输入框的智能提示功能 - 合集相关图标
    if (iconClassInput) {
        const commonCollectionIcons = [
            'bi-collection', 'bi-folder', 'bi-archive', 'bi-box',
            'bi-journals', 'bi-bookmark', 'bi-stack', 'bi-layers',
            'bi-grid', 'bi-list', 'bi-card-list', 'bi-files',
            'bi-folder-fill', 'bi-collection-fill', 'bi-archive-fill',
            'bi-music-note-list', 'bi-camera-reels', 'bi-film',
            'bi-play-btn', 'bi-camera-video', 'bi-video',
            'bi-star', 'bi-heart', 'bi-fire', 'bi-trophy'
        ];
        
        // 添加输入提示（可以在未来扩展为下拉菜单）
        iconClassInput.addEventListener('focus', function() {
            if (!this.value) {
                this.placeholder = '如：bi-collection, bi-folder, bi-archive...';
            }
        });
        
        iconClassInput.addEventListener('blur', function() {
            this.placeholder = '请输入 Bootstrap 图标类名，如 bi-collection';
        });
    }
    
    // 合集类型选择功能（如果有的话）
    const collectionTypeOptions = document.querySelectorAll('.collection-type-option');
    if (collectionTypeOptions.length > 0) {
        collectionTypeOptions.forEach(option => {
            option.addEventListener('click', function() {
                // 移除所有活跃状态
                collectionTypeOptions.forEach(opt => opt.classList.remove('active'));
                // 添加当前活跃状态
                this.classList.add('active');
                
                // 可以在这里添加逻辑来更新隐藏的表单字段
                const collectionType = this.dataset.type;
                console.log('Selected collection type:', collectionType);
            });
        });
    }
    
    // 统计信息更新功能（模拟实时数据）
    function updateStatsDisplay() {
        const stats = [
            { selector: '.stat-item:nth-child(1) .stat-value', value: Math.floor(Math.random() * 100) + 30 },
            { selector: '.stat-item:nth-child(2) .stat-value', value: (Math.random() * 5 + 1).toFixed(1) + 'M' },
            { selector: '.stat-item:nth-child(3) .stat-value', value: Math.floor(Math.random() * 50) + 10 + 'K' },
            { selector: '.stat-item:nth-child(4) .stat-value', value: Math.floor(Math.random() * 2000) + 500 }
        ];
        
        stats.forEach(stat => {
            const element = document.querySelector(stat.selector);
            if (element && Math.random() > 0.7) { // 30% chance to update each stat
                element.textContent = stat.value;
                element.style.animation = 'none';
                setTimeout(() => {
                    element.style.animation = 'pulse 0.5s ease-in-out';
                }, 10);
            }
        });
    }
    
    // 每30秒更新一次统计信息（模拟实时更新）
    setInterval(updateStatsDisplay, 30000);
    
    // 表单验证增强
    function validateForm() {
        let isValid = true;
        const requiredFields = ['name_cn', 'name_en'];
        
        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            const value = field.value.trim();
            
            if (!value) {
                field.classList.add('error');
                isValid = false;
                
                // 添加错误提示
                let errorDiv = field.parentNode.querySelector('.validation-error');
                if (!errorDiv) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'validation-error';
                    field.parentNode.appendChild(errorDiv);
                }
                errorDiv.innerHTML = '<i class="bi bi-exclamation-circle"></i> 此字段为必填项';
            } else {
                field.classList.remove('error');
                const errorDiv = field.parentNode.querySelector('.validation-error');
                if (errorDiv) {
                    errorDiv.remove();
                }
            }
        });
        
        return isValid;
    }
    
    // 表单提交处理
    // const form = document.getElementById('collectionEditForm');
    // if (form) {
    //     form.addEventListener('submit', function(e) {
    //         e.preventDefault();
            
    //         if (!validateForm()) {
    //             showAlert('请填写所有必填字段', 'danger');
    //             return;
    //         }
            
    //         // 显示加载状态
    //         const submitBtn = form.querySelector('button[type="submit"]');
    //         if (submitBtn) {
    //             submitBtn.classList.add('loading');
    //             submitBtn.disabled = true;
    //         }
            
    //         // 模拟提交过程
    //         setTimeout(() => {
    //             showAlert('合集信息保存成功！', 'success');
                
    //             if (submitBtn) {
    //                 submitBtn.classList.remove('loading');
    //                 submitBtn.disabled = false;
    //             }
    //         }, 2000);
    //     });
    // }
    
    // 实时验证
    const requiredInputs = document.querySelectorAll('#name_cn, #name_en');
    requiredInputs.forEach(input => {
        input.addEventListener('blur', validateForm);
        input.addEventListener('input', function() {
            if (this.classList.contains('error') && this.value.trim()) {
                this.classList.remove('error');
                const errorDiv = this.parentNode.querySelector('.validation-error');
                if (errorDiv) {
                    errorDiv.remove();
                }
            }
        });
    });
});