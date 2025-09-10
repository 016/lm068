/* Video Edit Page JavaScript - Version 10 */
/* 支持多选组件的视频编辑页面功能 */
/* 基于 video_edit_9.js 修改，新增多选组件初始化和管理 */

class VideoEditManager {
    constructor() {
        this.form = document.getElementById('videoEditForm');
        this.multiSelectInstances = {};
        
        this.init();
    }

    init() {
        this.initializeMultiSelectComponents();
        this.bindFormEvents();
        this.initializeCharacterCounters();
        this.initializeThumbnailUpload();
        // this.initializeAutoSave();
        
        console.log('VideoEditManager initialized');
    }

    // 初始化多选组件
    initializeMultiSelectComponents() {
        // 模拟标签数据
        const tagsData = [
            { id: '1', text: '前端开发' },
            { id: '2', text: 'JavaScript' },
            { id: '3', text: 'React' },
            { id: '4', text: 'Vue.js' },
            { id: '5', text: 'Angular' },
            { id: '6', text: 'TypeScript' },
            { id: '7', text: 'CSS3' },
            { id: '8', text: 'HTML5' },
            { id: '9', text: 'Node.js' },
            { id: '10', text: '性能优化' },
            { id: '11', text: '响应式设计' },
            { id: '12', text: '移动端开发' },
            { id: '13', text: 'webpack' },
            { id: '14', text: 'ES6+' },
            { id: '15', text: 'UI/UX' },
            { id: '16', text: '工程化' },
            { id: '17', text: '测试' },
            { id: '18', text: '部署' }
        ];

        // 模拟合集数据
        const collectionsData = [
            { id: '1', text: '前端基础教程' },
            { id: '2', text: 'JavaScript进阶' },
            { id: '3', text: 'React实战项目' },
            { id: '4', text: 'Vue开发指南' },
            { id: '5', text: '性能优化专题' },
            { id: '6', text: '工具链使用' },
            { id: '7', text: '设计模式' },
            { id: '8', text: '算法与数据结构' },
            { id: '9', text: '移动端开发' },
            { id: '10', text: '全栈开发' }
        ];

        // 初始化标签多选组件
        const tagsContainer = document.getElementById('videoTagsMultiSelect');
        if (tagsContainer) {
            this.multiSelectInstances.tags = new MultiSelectDropdown(tagsContainer, {
                placeholder: '选择视频标签...',
                searchPlaceholder: '搜索标签...',
                hiddenInputName: 'tag_ids',
                maxDisplayItems: 3,
                columns: 2,
                data: tagsData,
                selected: [
                    { id: '1', text: '前端开发' },
                    { id: '7', text: '设计模式' },
                    { id: '2', text: 'JavaScript' }
                ],
                allowClear: true
            });

            // 绑定标签变更事件
            tagsContainer.addEventListener('multiselect:change', (e) => {
                console.log('标签选择变更:', e.detail);
                this.handleTagsChange(e.detail);
            });
        }

        // 初始化合集多选组件
        const collectionsContainer = document.getElementById('videoCollectionsMultiSelect');
        if (collectionsContainer) {
            this.multiSelectInstances.collections = new MultiSelectDropdown(collectionsContainer, {
                placeholder: '选择视频合集...',
                searchPlaceholder: '搜索合集...',
                hiddenInputName: 'collection_ids',
                maxDisplayItems: 2,
                columns: 1,
                data: collectionsData,
                selected: [
                    { id: '2', text: 'JavaScript进阶' }
                ],
                allowClear: true
            });

            // 绑定合集变更事件
            collectionsContainer.addEventListener('multiselect:change', (e) => {
                console.log('合集选择变更:', e.detail);
                this.handleCollectionsChange(e.detail);
            });
        }
    }

    // 处理标签变更
    handleTagsChange(detail) {
        const { action, item, selected } = detail;
        
        switch (action) {
            case 'add':
                this.showNotification(`已添加标签: ${item.text}`, 'success');
                break;
            case 'remove':
                this.showNotification(`已移除标签: ${item.text}`, 'info');
                break;
            case 'clear':
                this.showNotification('已清空所有标签', 'warning');
                break;
        }

        // // 实时更新表单验证状态
        // this.validateTags(selected);
    }

    // 处理合集变更
    handleCollectionsChange(detail) {
        const { action, item, selected } = detail;
        
        switch (action) {
            case 'add':
                this.showNotification(`已添加到合集: ${item.text}`, 'success');
                break;
            case 'remove':
                this.showNotification(`已从合集移除: ${item.text}`, 'info');
                break;
            case 'clear':
                this.showNotification('已清空所有合集', 'warning');
                break;
        }

        // // 实时更新表单验证状态
        // this.validateCollections(selected);
    }

    // // 验证合集选择
    // validateCollections(selected) {
    //     const maxCollections = 5;
    //     const collectionsContainer = document.getElementById('videoCollectionsMultiSelect');
    //     const collectionsWrapper = collectionsContainer?.querySelector('.multi-select-wrapper');
        
    //     if (collectionsWrapper) {
    //         collectionsWrapper.classList.remove('error', 'success');
            
    //         if (selected.length > maxCollections) {
    //             collectionsWrapper.classList.add('error');
    //             this.setFieldError(collectionsContainer, `最多只能选择 ${maxCollections} 个合集`);
    //         } else {
    //             this.clearFieldError(collectionsContainer);
    //         }
    //     }
    // }

    // 设置字段错误
    setFieldError(container, message) {
        this.clearFieldError(container);
        const errorDiv = document.createElement('div');
        errorDiv.className = 'validation-error';
        errorDiv.innerHTML = `<i class="bi bi-exclamation-circle"></i> ${message}`;
        container.appendChild(errorDiv);
    }

    // 清除字段错误
    clearFieldError(container) {
        const existingError = container.querySelector('.validation-error');
        if (existingError) {
            existingError.remove();
        }
    }

    // 绑定表单事件
    bindFormEvents() {
        if (!this.form) return;

        // 表单提交事件
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleFormSubmit(e);
        });

        // 输入字段变化事件
        const inputs = this.form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('input', () => {
                this.markAsModified();
                this.validateField(input);
            });
        });

        // 取消按钮
        const cancelBtn = this.form.querySelector('#btn-cancel');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                this.handleCancel();
            });
        }
    }

    // 处理表单提交
    handleFormSubmit(e) {
        if (!this.validateForm()) {
            this.showNotification('请检查表单中的错误信息', 'error');
            return false;
        }

        const submitBtn = this.form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // 显示加载状态
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
        
        // 收集表单数据
        const formData = this.collectFormData();
        
        console.log('提交表单数据:', formData);
        
        // 模拟API调用
        setTimeout(() => {
            // 恢复按钮状态
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            
            this.showNotification('视频信息已成功保存！', 'success');
            this.markAsClean();
        }, 2000);
    }

    // 收集表单数据
    collectFormData() {
        const formData = new FormData(this.form);
        const data = Object.fromEntries(formData.entries());
        
        // 添加多选组件的数据
        if (this.multiSelectInstances.tags) {
            data.tag_ids = this.multiSelectInstances.tags.getValue();
            data.tags = this.multiSelectInstances.tags.getSelected();
        }
        
        if (this.multiSelectInstances.collections) {
            data.collection_ids = this.multiSelectInstances.collections.getValue();
            data.collections = this.multiSelectInstances.collections.getSelected();
        }
        
        return data;
    }

    // 验证表单
    validateForm() {
        let isValid = true;
        
        // 验证必填字段
        const requiredFields = this.form.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });
        
        return isValid;
    }

    // 验证单个字段
    validateField(field) {
        const value = field.value.trim();
        const fieldName = field.name || field.id;
        
        // 移除之前的错误状态
        field.classList.remove('error');
        this.clearFieldError(field.parentElement);
        
        // 必填字段验证
        if (field.hasAttribute('required') && !value) {
            field.classList.add('error');
            this.setFieldError(field.parentElement, '此字段为必填项');
            return false;
        }
        
        // 特定字段验证
        switch (fieldName) {
            case 'name_cn':
            case 'name_en':
                if (value.length > 200) {
                    field.classList.add('error');
                    this.setFieldError(field.parentElement, '标题长度不能超过200字符');
                    return false;
                }
                break;
                
            case 'duration':
                if (value && !this.isValidDuration(value)) {
                    field.classList.add('error');
                    this.setFieldError(field.parentElement, '请输入有效的时间格式(如: 12:35)');
                    return false;
                }
                break;
        }
        
        return true;
    }

    // 验证时长格式
    isValidDuration(duration) {
        const regex = /^\d{1,3}:[0-5]\d$/;
        return regex.test(duration);
    }

    // 初始化字符计数器
    initializeCharacterCounters() {
        const textareas = this.form.querySelectorAll('textarea[maxlength], input[maxlength]');
        textareas.forEach(textarea => {
            this.updateCharacterCounter(textarea);
            textarea.addEventListener('input', () => {
                this.updateCharacterCounter(textarea);
            });
        });
    }

    // 更新字符计数器
    updateCharacterCounter(field) {
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

    // 初始化缩略图上传
    initializeThumbnailUpload() {
        const thumbnailUpload = document.getElementById('thumbnailUpload');
        const thumbnailPreview = document.getElementById('thumbnailPreview');
        
        if (thumbnailUpload && thumbnailPreview) {
            thumbnailUpload.addEventListener('change', (e) => {
                this.handleThumbnailUpload(e, thumbnailPreview);
            });
        }
    }

    // 处理缩略图上传
    handleThumbnailUpload(e, preview) {
        const file = e.target.files[0];
        if (!file) return;
        
        // 验证文件类型
        if (!file.type.startsWith('image/')) {
            this.showNotification('请选择有效的图片文件', 'error');
            return;
        }
        
        // 验证文件大小 (5MB)
        if (file.size > 5 * 1024 * 1024) {
            this.showNotification('图片文件不能超过5MB', 'error');
            return;
        }
        
        // 预览图片
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result;
            preview.classList.add('preview-updating');
            setTimeout(() => {
                preview.classList.remove('preview-updating');
            }, 300);
        };
        reader.readAsDataURL(file);
        
        this.showNotification('缩略图已更新', 'success');
        this.markAsModified();
    }

    // 初始化自动保存
    initializeAutoSave() {
        this.autoSaveInterval = setInterval(() => {
            if (this.isModified && this.validateForm()) {
                this.autoSave();
            }
        }, 1000); // 30秒自动保存一次
    }

    // 自动保存
    autoSave() {
        const data = this.collectFormData();
        localStorage.setItem('videoEditForm_autoSave', JSON.stringify(data));
        console.log('Form auto-saved');
    }

    // 标记为已修改
    markAsModified() {
        this.isModified = true;
        document.title = '* ' + document.title.replace(/^\* /, '');
    }

    // 标记为未修改
    markAsClean() {
        this.isModified = false;
        document.title = document.title.replace(/^\* /, '');
    }

    // 处理取消操作
    handleCancel() {
        if (this.isModified) {
            if (confirm('您有未保存的更改，确定要离开吗？')) {
                window.history.back();
            }
        } else {
            window.history.back();
        }
    }

    // 显示通知
    showNotification(message, type = 'info') {
        window.AdminCommon.showToast(message, type)
    }

    // 销毁组件
    destroy() {
        // 清理自动保存定时器
        if (this.autoSaveInterval) {
            clearInterval(this.autoSaveInterval);
        }
        
        // 销毁多选组件
        Object.values(this.multiSelectInstances).forEach(instance => {
            if (instance && typeof instance.destroy === 'function') {
                instance.destroy();
            }
        });
        
        console.log('VideoEditManager destroyed');
    }
}

// 页面加载完成后初始化
document.addEventListener('DOMContentLoaded', () => {
    window.videoEditManager = new VideoEditManager();
});

// 页面卸载前清理
window.addEventListener('beforeunload', (e) => {
    if (window.videoEditManager?.isModified) {
        e.preventDefault();
        e.returnValue = '您有未保存的更改，确定要离开吗？';
    }
});