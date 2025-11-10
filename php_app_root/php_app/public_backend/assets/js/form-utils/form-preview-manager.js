/**
 * 预览与UI辅助模块
 * 负责实时预览、字符计数、模态框显示
 */

class FormPreviewManager {
    constructor(formUtils) {
        this.formUtils = formUtils;
        this.form = formUtils.form;
        this.previewConfig = formUtils.options.previewConfig;
    }

    /**
     * 初始化预览功能
     */
    initialize() {
        if (!this.previewConfig || Object.keys(this.previewConfig).length === 0) {
            return;
        }

        const config = this.previewConfig;

        // 实时更新预览文本
        if (config.nameInput && config.previewText) {
            const nameInput = document.getElementById(config.nameInput);
            const previewText = document.getElementById(config.previewText);

            if (nameInput && previewText) {
                nameInput.addEventListener('input', () => {
                    previewText.textContent = nameInput.value ||
                        config.defaultText || '标题';
                });
            }
        }

        // 实时更新预览图标
        if (config.iconInput && config.previewIcon) {
            const iconInput = document.getElementById(config.iconInput);
            const previewIcon = document.getElementById(config.previewIcon);

            if (iconInput && previewIcon) {
                iconInput.addEventListener('input', () => {
                    previewIcon.className = `bi ${iconInput.value ||
                    config.defaultIcon || 'bi-star'}`;
                });
            }
        }

        // 实时更新预览颜色
        if (config.colorSelect && config.previewBtn) {
            const colorSelect = document.getElementById(config.colorSelect);
            const previewBtn = document.getElementById(config.previewBtn);

            if (colorSelect && previewBtn) {
                colorSelect.addEventListener('change', () => {
                    previewBtn.className = `btn ${colorSelect.value}`;
                });
            }
        }
    }

    /**
     * 显示通用的预览模态框
     */
    showCommonPreviewModal(previewData, title = '预览') {
        const {
            nameCn,
            nameEn,
            shortDescCn,
            iconClass,
            colorClass,
            selectedVideos
        } = previewData;

        const previewContent = `
            <div class="preview-modal-content">
                <h5>${title}效果</h5>
                <div class="preview-display">
                    <button type="button" class="btn ${colorClass || 'btn-outline-primary'}">
                        <i class="bi ${iconClass || 'bi-star'}"></i>
                        ${nameCn || '标题'}
                    </button>
                </div>
                <div class="preview-details mt-3">
                    ${nameCn ? `<p><strong>中文标题:</strong> ${nameCn}</p>` : ''}
                    ${nameEn ? `<p><strong>英文标题:</strong> ${nameEn}</p>` : ''}
                    ${shortDescCn ? `<p><strong>简介:</strong> ${shortDescCn}</p>` : ''}
                    ${selectedVideos ? `<p><strong>关联视频数:</strong> ${selectedVideos.length} 个</p>` : ''}
                    ${selectedVideos && selectedVideos.length > 0 ?
            `<div class="preview-videos">
                            <strong>关联视频:</strong>
                            <ul class="list-unstyled mt-2">
                                ${selectedVideos.slice(0, 5).map(video =>
                `<li>• ${video.text}</li>`
            ).join('')}
                                ${selectedVideos.length > 5 ?
                `<li>... 还有 ${selectedVideos.length - 5} 个视频</li>` : ''}
                            </ul>
                        </div>` : ''
        }
                </div>
            </div>
        `;

        // 创建模态框
        const modalDiv = document.createElement('div');
        modalDiv.innerHTML = `
            <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${title}</h5>
                            <button type="button" class="btn-close" onclick="this.closest('.modal').remove()"></button>
                        </div>
                        <div class="modal-body">
                            ${previewContent}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="this.closest('.modal').remove()">关闭</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modalDiv);
    }

    /**
     * 初始化字符计数器
     */
    initializeCharacterCounters() {
        const textareas = this.form.querySelectorAll('textarea[maxlength], input[maxlength]');
        textareas.forEach(textarea => {
            this.updateCharacterCounter(textarea);
            textarea.addEventListener('input', () => {
                this.updateCharacterCounter(textarea);
            });
        });
    }

    /**
     * 更新字符计数器显示
     */
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
}

window.FormPreviewManager = FormPreviewManager;