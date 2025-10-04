/**
 * 视频链接编辑页面 JavaScript - 第1版
 * 基于 content_edit_11.js 重构
 * 仅保留页面特定的业务逻辑，表单通用功能由 form_utils_2.js 提供
 */

class VideoLinkEditManager {
    constructor() {
        this.form = document.getElementById('videoLinkEditForm');
        this.formUtils = null;

        this.init();
    }

    /**
     * 初始化视频链接编辑页面
     */
    init() {
        // 初始化表单工具（依赖 form_utils_2.js）
        this.initializeFormUtils();

        // 初始化页面特定的功能
        this.initializePageFeatures();

        console.log('VideoLinkEditManager initialized');
    }

    /**
     * 初始化表单工具
     * 使用通用的 FormUtils 类处理表单基础功能
     */
    initializeFormUtils() {
        if (!window.FormUtils) {
            console.error('FormUtils 未找到，请确保已引入 form_utils_2.js');
            return;
        }

        // 创建表单工具实例，启用所有功能
        this.formUtils = new FormUtils('#videoLinkEditForm', {
            enableAutoSave: false, // 暂时禁用自动保存
            enableCharacterCounter: true,
            enableFileUpload: false, // 视频链接表单不需要文件上传
            enableNotification: true
        });
    }

    /**
     * 初始化页面特定的功能
     * 配置视频链接表单的特定行为
     */
    initializePageFeatures() {
        if (!this.formUtils) {
            console.error('FormUtils 未初始化，无法初始化页面特定功能');
            return;
        }

        // 监听平台选择变化
        const platformSelect = document.getElementById('platform_id');
        if (platformSelect) {
            platformSelect.addEventListener('change', (e) => {
                this.handlePlatformChange(e.target.value);
            });
        }

        // 监听外部URL变化，自动提取视频ID
        const externalUrlInput = document.getElementById('external_url');
        const externalVideoIdInput = document.getElementById('external_video_id');
        if (externalUrlInput && externalVideoIdInput) {
            externalUrlInput.addEventListener('blur', (e) => {
                const url = e.target.value;
                const videoId = this.extractVideoIdFromUrl(url);
                if (videoId && !externalVideoIdInput.value) {
                    externalVideoIdInput.value = videoId;
                    this.showNotification('已自动提取视频ID', 'success');
                }
            });
        }

        // 监听内容选择变化
        const contentSelect = document.getElementById('content_id');
        if (contentSelect) {
            contentSelect.addEventListener('change', (e) => {
                this.handleContentChange(e.target.value);
            });
        }
    }

    /**
     * 处理平台选择变化
     * 根据不同平台给出不同的提示
     */
    handlePlatformChange(platformId) {
        const platformName = document.querySelector(`#platform_id option[value="${platformId}"]`)?.textContent;
        if (platformName) {
            this.showNotification(`已选择平台: ${platformName}`, 'info');
        }
    }

    /**
     * 处理内容选择变化
     */
    handleContentChange(contentId) {
        const contentTitle = document.querySelector(`#content_id option[value="${contentId}"]`)?.textContent;
        if (contentTitle) {
            this.showNotification(`已选择内容: ${contentTitle}`, 'info');
        }
    }

    /**
     * 从URL中提取视频ID
     * 支持常见的视频平台URL格式
     */
    extractVideoIdFromUrl(url) {
        if (!url) return null;

        try {
            const urlObj = new URL(url);

            // YouTube格式: https://www.youtube.com/watch?v=VIDEO_ID 或 https://youtu.be/VIDEO_ID
            if (urlObj.hostname.includes('youtube.com')) {
                return urlObj.searchParams.get('v');
            } else if (urlObj.hostname.includes('youtu.be')) {
                return urlObj.pathname.substring(1);
            }

            // BiliBili格式: https://www.bilibili.com/video/BV...
            if (urlObj.hostname.includes('bilibili.com')) {
                const match = urlObj.pathname.match(/\/video\/(BV[\w]+)/);
                return match ? match[1] : null;
            }

            // 抖音格式: https://www.douyin.com/video/...
            if (urlObj.hostname.includes('douyin.com')) {
                const match = urlObj.pathname.match(/\/video\/(\d+)/);
                return match ? match[1] : null;
            }

            // 如果无法识别，返回null
            return null;
        } catch (e) {
            console.error('URL解析错误:', e);
            return null;
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

        console.log('VideoLinkEditManager destroyed');
    }
}

// 页面加载完成后初始化
document.addEventListener('DOMContentLoaded', () => {
    window.videoLinkEditManager = new VideoLinkEditManager();
});
