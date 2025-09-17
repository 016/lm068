/**
 * 内容编辑页面 JavaScript - 第11版
 * 基于 content_edit_10.js 重构，移除form通用功能到独立文件
 * 仅保留页面特定的业务逻辑和多选组件配置
 */

class ContentEditManager {
    constructor() {
        this.form = document.getElementById('videoEditForm');
        this.formUtils = null;
        
        this.init();
    }

    /**
     * 初始化内容编辑页面
     */
    init() {
        // 初始化表单工具（依赖 form_utils_1.js）
        this.initializeFormUtils();
        
        // 初始化页面特定的多选组件
        this.initializePageMultiSelects();
        
        // 绑定页面特定事件
        this.bindPageEvents();
        
        console.log('ContentEditManager initialized');
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
        this.formUtils = new FormUtils('#videoEditForm', {
            enableAutoSave: false, // 暂时禁用自动保存
            enableCharacterCounter: true,
            enableFileUpload: true,
            enableNotification: true
        });
    }
    // 内容标签数据
    tagsData = [
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
    selectedTagIds = ['1', '4', '7', '15'];

    // 内容合集数据
    collectionsData = [
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
    selectedCollectionIds = ['1', '4', '7', '10'];

    /**
     * 初始化页面特定的多选组件
     * 配置标签和合集的多选下拉组件
     */
    initializePageMultiSelects() {
        if (!this.formUtils) {
            console.error('FormUtils 未初始化，无法创建多选组件');
            return;
        }


        const selectedTags = this.tagsData.filter(tag => this.selectedTagIds.includes(tag.id));
        // 初始化标签多选组件
        const tagsInstance = this.formUtils.initializeMultiSelect('tags', {
            container: '#videoTagsMultiSelect',
            placeholder: '选择内容标签...',
            searchPlaceholder: '搜索标签...',
            hiddenInputName: 'tag_ids',
            maxDisplayItems: 3,
            columns: 2,
            data: this.tagsData,
            selected: selectedTags,
            allowClear: true
        });

        const selectedCollections = this.collectionsData.filter(collection => this.selectedCollectionIds.includes(collection.id));
        // 初始化合集多选组件
        const collectionsInstance = this.formUtils.initializeMultiSelect('collections', {
            container: '#videoCollectionsMultiSelect',
            placeholder: '选择内容合集...',
            searchPlaceholder: '搜索合集...',
            hiddenInputName: 'collection_ids',
            maxDisplayItems: 2,
            columns: 1,
            data: this.collectionsData,
            selected: selectedCollections,
            allowClear: true
        });

        // 绑定标签变更的特定处理
        if (tagsInstance) {
            document.getElementById('videoTagsMultiSelect').addEventListener('multiselect:change', (e) => {
                this.handleTagsChange(e.detail);
            });
        }

        // 绑定合集变更的特定处理
        if (collectionsInstance) {
            document.getElementById('videoCollectionsMultiSelect').addEventListener('multiselect:change', (e) => {
                this.handleCollectionsChange(e.detail);
            });
        }
    }

    /**
     * 绑定页面特定事件
     */
    bindPageEvents() {
        // 监听内容类型变更，动态调整表单显示
        const contentTypeSelect = document.getElementById('content_type_id');
        if (contentTypeSelect) {
            contentTypeSelect.addEventListener('change', (e) => {
                this.handleContentTypeChange(e.target.value);
            });
        }

        // 监听状态变更，显示相应的提示信息
        const statusSelect = document.getElementById('status_id');
        if (statusSelect) {
            statusSelect.addEventListener('change', (e) => {
                this.handleStatusChange(e.target.value);
            });
        }
    }

    /**
     * 处理标签选择变更
     * 在标签选择变化时显示相应的通知信息
     */
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

        // 标签数量限制提示
        if (selected.length > 10) {
            this.showNotification('建议选择的标签数量不超过10个，以便更好的分类效果', 'warning');
        }
    }

    /**
     * 处理合集选择变更
     * 在合集选择变化时显示相应的通知信息
     */
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

        // 合集数量限制提示
        if (selected.length > 5) {
            this.showNotification('建议一个内容不要加入超过5个合集', 'warning');
        }
    }

    /**
     * 处理内容类型变更
     * 根据内容类型显示/隐藏相关字段
     */
    handleContentTypeChange(typeId) {
        const durationField = document.getElementById('duration')?.parentElement;
        const authorField = document.getElementById('author')?.parentElement;
        
        switch (typeId) {
            case '21': // 视频
                if (durationField) durationField.style.display = 'block';
                if (authorField) authorField.style.display = 'block';
                this.showNotification('已切换到视频内容类型', 'info');
                break;
            case '11': // 一般文章
                if (durationField) durationField.style.display = 'none';
                if (authorField) authorField.style.display = 'block';
                this.showNotification('已切换到文章内容类型', 'info');
                break;
            case '1': // 网站公告
                if (durationField) durationField.style.display = 'none';
                if (authorField) authorField.style.display = 'none';
                this.showNotification('已切换到公告内容类型', 'info');
                break;
        }
    }

    /**
     * 处理状态变更
     * 根据发布状态给出相应提示
     */
    handleStatusChange(statusId) {
        const statusMessages = {
            '0': { message: '内容已设为隐藏状态', type: 'warning' },
            '1': { message: '内容保存为草稿', type: 'info' },
            '11': { message: '创意阶段，开始构思', type: 'info' },
            '91': { message: '内容准备发布', type: 'success' },
            '99': { message: '内容已发布', type: 'success' }
        };

        const status = statusMessages[statusId];
        if (status) {
            this.showNotification(status.message, status.type);
        }
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
     * 销毁页面管理器
     * 清理页面特定的资源和监听器
     */
    destroy() {
        if (this.formUtils) {
            this.formUtils.destroy();
        }
        
        console.log('ContentEditManager destroyed');
    }
}

// 页面加载完成后初始化
document.addEventListener('DOMContentLoaded', () => {
    window.contentEditManager = new ContentEditManager();
});
