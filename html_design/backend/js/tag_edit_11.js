/**
 * 标签编辑页面 JavaScript - 第11版
 * 基于 tag_edit_10.js 重构，参考 ContentEditManager 类结构设计
 * 使用类封装页面逻辑，依赖 FormUtils 通用表单工具
 */

class TagEditManager {
    constructor() {
        this.form = document.getElementById('tagEditForm');
        this.formUtils = null;
        
        this.init();
    }

    /**
     * 初始化标签编辑页面
     */
    init() {
        // 初始化表单工具（依赖 form_utils_1.js）
        this.initializeFormUtils();
        
        // 初始化页面特定的多选组件
        this.initializePageMultiSelects();
        
        // 初始化页面特定功能
        this.initializePageFeatures();
        
        // 绑定页面特定事件
        this.bindPageEvents();
        
        console.log('TagEditManager initialized');
    }

    /**
     * 初始化表单工具
     * 使用通用的 FormUtils 类处理表单基础功能
     */
    initializeFormUtils() {
        if (!window.FormUtils) {
            console.error('FormUtils 未找到，请确保已引入 form_utils_1.js');
            return;
        }

        // 创建表单工具实例，启用所有功能
        this.formUtils = new FormUtils('#tagEditForm', {
            enableAutoSave: true,
            enableCharacterCounter: true,
            enableFileUpload: true,
            enableNotification: true
        });

        // 监听表单提交事件，添加页面特定的处理逻辑
        this.form.addEventListener('formutils:submit', (e) => {
            this.handlePageSpecificSubmit(e.detail);
        });
    }

    /**
     * 初始化页面特定的多选组件
     * 配置视频的多选下拉组件
     */
    initializePageMultiSelects() {
        if (!this.formUtils) {
            console.error('FormUtils 未初始化，无法创建多选组件');
            return;
        }

        // 模拟视频数据
        const videoData = [
            { id: 'v101', text: '【搞笑】动物搞笑合集第一期【搞笑】动物搞笑合集第一期【搞笑】动物搞笑合集第一期' },
            { id: 'v102', text: '【搞笑】网络热门段子精选' },
            { id: 'v103', text: '【搞笑】街头恶搞大合集' },
            { id: 'v104', text: '【搞笑】宠物萌宠搞怪瞬间' },
            { id: 'v105', text: '【搞笑】校园趣事分享' },
            { id: 'v106', text: '【搞笑】办公室日常爆笑办公室日常爆笑办公室日常爆笑办公室日常爆笑办公室日常爆笑办公室日常爆笑' },
            { id: 'v107', text: '【搞笑】生活中的尴尬时刻' },
            { id: 'v108', text: '【搞笑】网红模仿秀合集' },
            { id: 'v109', text: '【搞笑】儿童童言无忌' },
            { id: 'v110', text: '【搞笑】运动失误搞笑瞬间' },
            { id: 'v111', text: '【搞笑】厨房烹饪意外合集' },
            { id: 'v112', text: '【搞笑】旅游途中趣事分享' },
            { id: 'v113', text: '【搞笑】家庭聚会爆笑时刻' },
            { id: 'v114', text: '【搞笑】公共场所尴尬瞬间' },
            { id: 'v115', text: '【搞笑】网络直播搞笑片段' },
            { id: 'v116', text: '【搞笑】老人爆笑语录合集老人爆笑语录合集老人爆笑语录合集老人爆笑语录合集老人爆笑语录合集老人爆笑语录合集' },
            { id: 'v117', text: '【搞笑】交通工具趣事' },
            { id: 'v118', text: '【搞笑】购物时的奇遇记' },
            { id: 'v119', text: '【搞笑】节日庆典搞怪时刻节日庆典搞怪时刻节日庆典搞怪时刻节日庆典搞怪时刻节日庆典搞怪时刻' },
            { id: 'v120', text: '【搞笑】科技产品使用囧事' }
        ];

        // 模拟已选中的视频
        const selectedVideoIds = ['v101', 'v104', 'v107', 'v109', 'v112', 'v115'];
        const selectedVideos = videoData.filter(video => selectedVideoIds.includes(video.id));

        // 初始化视频多选组件
        const videosInstance = this.formUtils.initializeMultiSelect('videos', {
            container: '#videoMultiSelect',
            placeholder: '选择关联视频...',
            searchPlaceholder: '搜索视频标题...',
            hiddenInputName: 'related_videos',
            maxDisplayItems: 7,
            columns: 4,
            data: videoData,
            selected: selectedVideos,
            allowClear: true
        });

        // 绑定视频变更的特定处理
        if (videosInstance) {
            document.getElementById('videoMultiSelect').addEventListener('multiselect:change', (e) => {
                this.handleVideosChange(e.detail);
            });
        }
    }

    /**
     * 初始化页面特定功能
     * 设置标签预览功能
     */
    initializePageFeatures() {
        this.initializeTagPreview();
    }

    /**
     * 初始化标签预览功能
     * 实时预览标签标题、图标和颜色效果
     */
    initializeTagPreview() {
        const nameInput = document.getElementById('name_cn');
        const iconInput = document.getElementById('icon_class');
        const colorSelect = document.getElementById('color_class');
        const previewText = document.getElementById('previewText');
        const previewIcon = document.getElementById('previewIcon');
        const previewBtn = document.getElementById('tagPreviewBtn');

        // 实时更新预览文本
        if (nameInput && previewText) {
            nameInput.addEventListener('input', () => {
                previewText.textContent = nameInput.value || '标签标题';
            });
        }

        // 实时更新预览图标
        if (iconInput && previewIcon) {
            iconInput.addEventListener('input', () => {
                previewIcon.className = `bi ${iconInput.value || 'bi-star'}`;
            });
        }

        // 实时更新预览颜色
        if (colorSelect && previewBtn) {
            colorSelect.addEventListener('change', () => {
                previewBtn.className = `btn ${colorSelect.value}`;
            });
        }
    }

    /**
     * 绑定页面特定事件
     */
    bindPageEvents() {
        // 预览按钮功能
        const previewBtnSelector = document.querySelector('.btn-outline-primary');
        if (previewBtnSelector && previewBtnSelector.textContent.includes('预览')) {
            previewBtnSelector.addEventListener('click', () => {
                this.showTagPreview();
            });
        }
    }

    /**
     * 处理视频选择变更
     * 在视频选择变化时显示相应的通知信息和统计更新
     */
    handleVideosChange(detail) {
        const { action, item, selected } = detail;
        
        switch (action) {
            case 'add':
                this.showNotification(`已添加视频: ${item.text}`, 'success');
                break;
            case 'remove':
                this.showNotification(`已移除视频: ${item.text}`, 'info');
                break;
            case 'clear':
                this.showNotification('已清空所有视频', 'warning');
                break;
        }

        // 更新视频统计信息
        this.updateVideoStats(selected);

        // 视频数量限制提示
        if (selected.length > 100) {
            this.showNotification('建议单个标签关联的视频数量不超过100个', 'warning');
        }
    }

    /**
     * 更新视频统计信息
     * 更新页面上的视频数量显示
     */
    updateVideoStats(selectedVideos) {
        const statValueElement = document.querySelector('.stats-row .stat-item .stat-value');
        if (statValueElement && selectedVideos) {
            const count = selectedVideos.length;
            const formattedCount = count > 999 ? `${(count / 1000).toFixed(1)}K` : count.toString();
            statValueElement.textContent = formattedCount;
        }
    }

    /**
     * 显示标签预览
     * 创建模态框展示标签的预览效果
     */
    showTagPreview() {
        const nameCn = document.getElementById('name_cn')?.value || '标签名称';
        const nameEn = document.getElementById('name_en')?.value || 'Tag Name';
        const shortDescCn = document.getElementById('short_desc_cn')?.value || '标签简介';
        const iconClass = document.getElementById('icon_class')?.value || 'bi-star';
        const colorClass = document.getElementById('color_class')?.value || 'btn-outline-primary';
        
        const videoSelector = this.formUtils?.getMultiSelectInstance('videos');
        const selectedVideos = videoSelector ? videoSelector.getSelected() : [];

        const previewContent = `
            <div class="tag-preview-modal">
                <h5>标签预览效果</h5>
                <div class="preview-tag-display">
                    <button type="button" class="btn ${colorClass}">
                        <i class="bi ${iconClass}"></i>
                        ${nameCn}
                    </button>
                </div>
                <div class="preview-details mt-3">
                    <p><strong>中文标题:</strong> ${nameCn}</p>
                    <p><strong>英文标题:</strong> ${nameEn}</p>
                    <p><strong>简介:</strong> ${shortDescCn}</p>
                    <p><strong>关联视频数:</strong> ${selectedVideos.length} 个</p>
                    ${selectedVideos.length > 0 ? 
                        `<div class="preview-videos">
                            <strong>关联视频:</strong>
                            <ul class="list-unstyled mt-2">
                                ${selectedVideos.slice(0, 5).map(video => `<li>• ${video.text}</li>`).join('')}
                                ${selectedVideos.length > 5 ? `<li>... 还有 ${selectedVideos.length - 5} 个视频</li>` : ''}
                            </ul>
                        </div>` : ''
                    }
                </div>
            </div>
        `;

        // 创建模态框显示预览
        const modalDiv = document.createElement('div');
        modalDiv.innerHTML = `
            <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">标签预览</h5>
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
     * 处理页面特定的表单提交逻辑
     * 在通用表单提交基础上添加标签编辑页面的特殊处理
     */
    handlePageSpecificSubmit(detail) {
        const { formData } = detail;
        
        // 获取选中的视频数据
        const videoSelector = this.formUtils.getMultiSelectInstance('videos');
        const selectedVideos = videoSelector ? videoSelector.getSelected() : [];
        
        console.log('标签表单提交数据:', formData);
        console.log('关联视频:', selectedVideos);
        
        // 验证标签特定的业务规则
        if (this.validateTagRules(formData, selectedVideos)) {
            console.log('标签编辑页面提交验证通过:', formData);
            
            // 执行提交处理
            this.executeSubmit(formData, selectedVideos);
        } else {
            // 阻止默认提交流程
            detail.preventDefault?.();
        }
    }

    /**
     * 验证标签特定的业务规则
     * 检查标签编辑页面的特殊验证逻辑
     */
    validateTagRules(formData, selectedVideos) {
        let isValid = true;

        // 检查标签名称
        if (!formData.name_cn || formData.name_cn.trim().length < 1) {
            this.showNotification('标签名称不能为空', 'error');
            isValid = false;
        }

        // 检查标签名称长度
        if (formData.name_cn && formData.name_cn.trim().length > 20) {
            this.showNotification('标签名称不能超过20个字符', 'error');
            isValid = false;
        }

        // 检查图标类名格式
        if (formData.icon_class && !formData.icon_class.startsWith('bi-')) {
            this.showNotification('图标类名必须以 "bi-" 开头', 'error');
            isValid = false;
        }

        // 检查英文名称格式（如果提供）
        if (formData.name_en && !/^[a-zA-Z0-9\s\-_]+$/.test(formData.name_en)) {
            this.showNotification('英文名称只能包含字母、数字、空格、连字符和下划线', 'error');
            isValid = false;
        }

        return isValid;
    }

    /**
     * 执行提交处理
     * 处理标签表单的实际提交逻辑
     */
    executeSubmit(formData, selectedVideos) {
        // 确认提交
        if (!confirm('确定要保存标签修改吗？')) {
            return;
        }

        // 模拟提交过程
        const submitBtn = document.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>保存中...';
        
        // 模拟异步提交
        setTimeout(() => {
            // 恢复按钮状态
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            
            this.showNotification('标签信息已成功保存！', 'success');
            this.formUtils.markAsClean();
            
            console.log('标签数据提交完成:', {
                formData,
                selectedVideos: selectedVideos.length
            });
        }, 2000);
    }

    /**
     * 显示通知消息
     * 使用 FormUtils 的通知功能
     */
    showNotification(message, type = 'info') {
        if (this.formUtils) {
            this.formUtils.showNotification(message, type);
        }
    }

    /**
     * 获取页面数据摘要
     * 返回当前页面的关键信息，用于调试和监控
     */
    getPageSummary() {
        if (!this.formUtils) return null;

        const formData = this.formUtils.collectFormData();
        const videosInstance = this.formUtils.getMultiSelectInstance('videos');

        return {
            tagId: formData.id,
            nameCn: formData.name_cn,
            nameEn: formData.name_en,
            shortDescCn: formData.short_desc_cn,
            iconClass: formData.icon_class,
            colorClass: formData.color_class,
            videosCount: videosInstance ? videosInstance.getSelected().length : 0,
            isModified: this.formUtils.isModified
        };
    }

    /**
     * 获取选中的视频
     * 外部接口方法
     */
    getSelectedVideos() {
        const videoSelector = this.formUtils?.getMultiSelectInstance('videos');
        return videoSelector ? videoSelector.getSelected() : [];
    }

    /**
     * 设置选中的视频
     * 外部接口方法
     */
    setSelectedVideos(videoIds) {
        const videoSelector = this.formUtils?.getMultiSelectInstance('videos');
        if (videoSelector) {
            if (typeof videoIds === 'string') {
                videoSelector.setValue(videoIds);
            } else if (Array.isArray(videoIds)) {
                videoSelector.setValue(videoIds.join(','));
            }
        }
    }

    /**
     * 销毁页面管理器
     * 清理页面特定的资源和监听器
     */
    destroy() {
        if (this.formUtils) {
            this.formUtils.destroy();
        }
        
        console.log('TagEditManager destroyed');
    }
}

// 页面加载完成后初始化
document.addEventListener('DOMContentLoaded', () => {
    window.tagEditManager = new TagEditManager();
});

// 页面卸载前清理
window.addEventListener('beforeunload', (e) => {
    if (window.tagEditManager?.formUtils?.isModified) {
        e.preventDefault();
        e.returnValue = '您有未保存的更改，确定要离开吗？';
    }
});

// 兼容性：暴露给外部使用的工具函数（保持向后兼容）
window.TagEditForm = {
    updateVideoStats: (selectedVideos) => {
        window.tagEditManager?.updateVideoStats(selectedVideos);
    },
    showTagPreview: () => {
        window.tagEditManager?.showTagPreview();
    },
    getSelectedVideos: () => {
        return window.tagEditManager?.getSelectedVideos() || [];
    },
    setSelectedVideos: (videoIds) => {
        window.tagEditManager?.setSelectedVideos(videoIds);
    }
};

// 开发调试功能（仅在开发环境使用）
if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
    // 在控制台添加调试命令
    window.debugTagEdit = {
        getSummary: () => window.tagEditManager?.getPageSummary(),
        getFormData: () => window.tagEditManager?.formUtils?.collectFormData(),
        resetForm: () => window.tagEditManager?.formUtils?.resetForm(),
        showTest: (msg, type) => window.tagEditManager?.showNotification(msg || '测试通知', type || 'info'),
        showPreview: () => window.tagEditManager?.showTagPreview()
    };
    
    console.log('标签编辑页面调试工具已加载，使用 debugTagEdit 对象进行调试');
}