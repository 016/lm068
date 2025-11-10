/**
 * 文件上传模块
 * 负责文件上传、预览、验证
 */

class FormFileUploader {
    constructor(formUtils) {
        this.formUtils = formUtils;
        this.form = formUtils.form;
    }

    /**
     * 初始化文件上传功能
     */
    initialize() {
        const fileInputs = this.form.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                this.handleFileUpload(e);
            });
        });
    }

    /**
     * 处理文件上传
     */
    handleFileUpload(e) {
        const file = e.target.files[0];
        const input = e.target;

        if (!file) return;

        // 获取预览元素
        const previewId = input.id.replace('Upload', 'Preview');
        const preview = document.getElementById(previewId);

        // 验证文件类型(如果是图片上传)
        if (input.accept && input.accept.includes('image/')) {
            if (!file.type.startsWith('image/')) {
                this.formUtils.showNotification('请选择有效的图片文件', 'error');
                input.value = '';
                return;
            }

            // 验证文件大小(5MB)
            if (file.size > 5 * 1024 * 1024) {
                this.formUtils.showNotification('图片文件不能超过5MB', 'error');
                input.value = '';
                return;
            }

            // 预览图片
            if (preview) {
                this.previewImage(file, preview);
            }
        }

        this.formUtils.showNotification(`文件 ${file.name} 已选择`, 'success');
        this.formUtils.markAsModified();
    }

    /**
     * 预览图片
     */
    previewImage(file, previewElement) {
        const reader = new FileReader();
        reader.onload = (e) => {
            previewElement.src = e.target.result;
            previewElement.classList.add('preview-updating');
            setTimeout(() => {
                previewElement.classList.remove('preview-updating');
            }, 300);
        };
        reader.readAsDataURL(file);
    }

    /**
     * 验证文件类型
     */
    validateFileType(file, acceptedTypes) {
        return acceptedTypes.some(type => {
            if (type.endsWith('/*')) {
                const category = type.split('/')[0];
                return file.type.startsWith(category + '/');
            }
            return file.type === type;
        });
    }

    /**
     * 验证文件大小
     */
    validateFileSize(file, maxSizeMB) {
        return file.size <= maxSizeMB * 1024 * 1024;
    }
}

window.FormFileUploader = FormFileUploader;