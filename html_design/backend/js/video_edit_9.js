/**
 * Video Edit Form JavaScript - Version 9
 * 视频编辑表单交互功能 - 基于 tag_edit_8.js 修改
 */

document.addEventListener('DOMContentLoaded', function() {
    // 获取预览相关元素
    const videoThumbnail = document.getElementById('videoThumbnail');
    const previewDuration = document.getElementById('previewDuration');
    
    // 获取表单输入元素
    const nameCnInput = document.getElementById('name_cn');
    const nameEnInput = document.getElementById('name_en');
    const durationInput = document.getElementById('duration');
    const thumbnailInput = document.getElementById('thumbnail');
    const colorClassSelect = document.getElementById('color_class');
    const iconClassInput = document.getElementById('icon_class');
    const statusSelect = document.getElementById('status_id');
    
    // 实时预览功能
    function updateVideoPreview() {
        // 更新缩略图
        if (thumbnailInput && thumbnailInput.value) {
            const thumbnailUrl = thumbnailInput.value;
            if (isValidUrl(thumbnailUrl)) {
                videoThumbnail.style.backgroundImage = `url('${thumbnailUrl}')`;
            }
        }
        
        // 更新时长显示
        if (durationInput && durationInput.value) {
            previewDuration.textContent = durationInput.value;
        }
        
        // 添加更新动画
        videoThumbnail.classList.add('preview-updating');
        setTimeout(() => {
            videoThumbnail.classList.remove('preview-updating');
        }, 300);
    }
    
    // URL 验证函数
    function isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }
    
    // 时长格式验证和格式化
    function formatDuration(input) {
        let value = input.replace(/[^\d:]/g, '');
        const parts = value.split(':');
        
        if (parts.length === 1 && parts[0].length > 2) {
            // 如果输入的是数字，自动转换为 mm:ss 格式
            const totalSeconds = parseInt(parts[0]) || 0;
            const minutes = Math.floor(totalSeconds / 60);
            const seconds = totalSeconds % 60;
            value = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        } else if (parts.length === 2) {
            const minutes = parseInt(parts[0]) || 0;
            const seconds = parseInt(parts[1]) || 0;
            if (seconds >= 60) {
                const additionalMinutes = Math.floor(seconds / 60);
                const remainingSeconds = seconds % 60;
                value = `${minutes + additionalMinutes}:${remainingSeconds.toString().padStart(2, '0')}`;
            } else {
                value = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            }
        }
        
        return value;
    }
    
    // 绑定事件监听器
    if (nameCnInput) {
        nameCnInput.addEventListener('input', updateVideoPreview);
    }
    
    if (nameEnInput) {
        nameEnInput.addEventListener('input', updateVideoPreview);
    }
    
    if (durationInput) {
        durationInput.addEventListener('input', function() {
            updateVideoPreview();
        });
        
        durationInput.addEventListener('blur', function() {
            this.value = formatDuration(this.value);
            updateVideoPreview();
        });
    }
    
    if (thumbnailInput) {
        thumbnailInput.addEventListener('input', function() {
            updateVideoPreview();
        });
        
        thumbnailInput.addEventListener('blur', function() {
            updateVideoPreview();
        });
    }
    
    if (colorClassSelect) {
        colorClassSelect.addEventListener('change', updateVideoPreview);
    }
    
    if (iconClassInput) {
        iconClassInput.addEventListener('input', updateVideoPreview);
        
        // 图标输入框失去焦点时自动格式化
        iconClassInput.addEventListener('blur', function() {
            let value = this.value.trim();
            if (value && !value.startsWith('bi-')) {
                value = 'bi-' + value.replace(/^bi-?/, '');
                this.value = value;
                updateVideoPreview();
            }
        });
    }
    
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            updateStatusDisplay(this.value);
        });
    }
    
    // 初始化预览
    updateVideoPreview();
    
    // 状态显示更新
    function updateStatusDisplay(statusValue) {
        const statusNames = {
            '0': '隐藏',
            '1': '草稿',
            '11': '创意',
            '18': '脚本开',
            '19': '脚本完',
            '21': '开拍',
            '29': '拍完',
            '31': '开剪',
            '39': '剪完',
            '91': '待发布',
            '99': '已发布'
        };
        
        console.log(`Status changed to: ${statusNames[statusValue] || '未知'}`);
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
    setupCharacterCounter('name_cn', 255);
    setupCharacterCounter('name_en', 255);
    setupCharacterCounter('short_desc_cn', 300);
    setupCharacterCounter('short_desc_en', 300);
    setupCharacterCounter('desc_cn', 1000);
    setupCharacterCounter('desc_en', 1000);
    
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
            // 这里可以添加视频预览功能
            const previewData = {
                name_cn: nameCnInput.value,
                name_en: nameEnInput.value,
                duration: durationInput.value,
                thumbnail: thumbnailInput.value,
                color_class: colorClassSelect.value,
                icon_class: iconClassInput.value,
                short_desc_cn: document.getElementById('short_desc_cn').value,
                short_desc_en: document.getElementById('short_desc_en').value,
                status_id: statusSelect.value
            };
            
            console.log('Preview data:', previewData);
            showAlert('预览功能开发中...', 'info');
        });
    }
    
    // 播放测试按钮功能
    const playTestBtn = document.querySelector('.btn-outline-info');
    if (playTestBtn) {
        playTestBtn.addEventListener('click', function() {
            showAlert('播放测试功能开发中...', 'info');
            console.log('Play test clicked');
        });
    }
    
    // 图标输入框的智能提示功能
    if (iconClassInput) {
        const commonVideoIcons = [
            'bi-play-circle', 'bi-camera-video', 'bi-film', 'bi-record-circle',
            'bi-play-btn', 'bi-pause-btn', 'bi-stop-btn', 'bi-skip-start',
            'bi-skip-end', 'bi-rewind', 'bi-fast-forward', 'bi-volume-up',
            'bi-volume-down', 'bi-volume-mute', 'bi-fullscreen', 'bi-cast',
            'bi-camera', 'bi-camcorder', 'bi-mic', 'bi-headphones',
            'bi-speaker', 'bi-tv', 'bi-display', 'bi-projector'
        ];
        
        // 添加输入提示（可以在未来扩展为下拉菜单）
        iconClassInput.addEventListener('focus', function() {
            if (!this.value) {
                this.placeholder = '如：bi-play-circle, bi-camera-video, bi-film...';
            }
        });
        
        iconClassInput.addEventListener('blur', function() {
            this.placeholder = '请输入 Bootstrap 图标类名，如 bi-play-circle';
        });
    }
    
    // 缩略图点击播放功能
    if (videoThumbnail) {
        videoThumbnail.addEventListener('click', function() {
            showAlert('视频播放功能开发中...', 'info');
            console.log('Video thumbnail clicked');
        });
    }
    
    // 表单验证功能
    function validateForm() {
        let isValid = true;
        const requiredFields = [
            { field: nameCnInput, message: '请输入中文标题' },
            { field: nameEnInput, message: '请输入英文标题' }
        ];
        
        // 清除之前的错误状态
        document.querySelectorAll('.form-control.error').forEach(field => {
            field.classList.remove('error');
        });
        document.querySelectorAll('.validation-error').forEach(error => {
            error.remove();
        });
        
        requiredFields.forEach(({ field, message }) => {
            if (field && !field.value.trim()) {
                field.classList.add('error');
                showValidationError(field, message);
                isValid = false;
            }
        });
        
        // 验证时长格式
        if (durationInput && durationInput.value) {
            const durationPattern = /^\d{1,3}:[0-5]\d$/;
            if (!durationPattern.test(durationInput.value)) {
                durationInput.classList.add('error');
                showValidationError(durationInput, '时长格式不正确（应为 mm:ss）');
                isValid = false;
            }
        }
        
        // 验证缩略图URL
        if (thumbnailInput && thumbnailInput.value && !isValidUrl(thumbnailInput.value)) {
            thumbnailInput.classList.add('error');
            showValidationError(thumbnailInput, '请输入有效的URL地址');
            isValid = false;
        }
        
        return isValid;
    }
    
    function showValidationError(field, message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'validation-error';
        errorDiv.innerHTML = `<i class="bi bi-exclamation-circle"></i> ${message}`;
        field.parentNode.appendChild(errorDiv);
    }
    
    // 表单提交处理
    const form = document.getElementById('videoEditForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (validateForm()) {
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
                
                // 模拟保存过程
                setTimeout(() => {
                    submitBtn.classList.remove('loading');
                    submitBtn.disabled = false;
                    showAlert('视频信息保存成功！', 'success');
                }, 2000);
            } else {
                showAlert('请检查并修正表单中的错误', 'danger');
            }
        });
    }
    
    // 自动保存功能（可选）
    let autoSaveTimer;
    function scheduleAutoSave() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(() => {
            if (validateForm()) {
                // 这里可以添加自动保存逻辑
                console.log('Auto-saving...');
            }
        }, 30000); // 30秒后自动保存
    }
    
    // 监听表单变化以触发自动保存
    const formInputs = form.querySelectorAll('input, select, textarea');
    formInputs.forEach(input => {
        input.addEventListener('input', scheduleAutoSave);
    });
});