/**
 * Admin Notifications - 管理后台通知系统
 *
 * 依赖：Bootstrap 5
 *
 * 提供功能：
 * - Toast 消息通知
 * - Modal 模态框显示
 * - Tooltip 提示功能
 * - 描述文本 Tooltip 设置
 */

(function() {
    'use strict';

    // ========== TOOLTIP FUNCTIONALITY ==========
    // 显示Tooltip
    function showTooltip() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        // console.log(tooltipList);
    }

    /**
     * 重新初始化所有tooltips - 解决动态内容更新后tooltip失效问题
     * 这个方法会清除现有的tooltips并重新初始化所有带有tooltip属性的元素
     */
    function reinitializeTooltips() {
        // 清除现有的tooltips
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(element => {
            const existingTooltip = bootstrap.Tooltip.getInstance(element);
            if (existingTooltip) {
                existingTooltip.dispose();
            }
        });

        // 重新初始化所有tooltips
        showTooltip();

        console.log('所有tooltips已重新初始化');
    }

    /**
     * 设置描述文本的tooltip功能 - 通用方法
     * @param {Object} options - 配置选项
     * @param {string} options.selector - 目标元素选择器，默认为 '[data-column="description"]'
     * @param {number} options.maxLength - 文本截断长度，默认为 20
     * @param {string} options.placement - tooltip位置，默认为 'top'
     * @param {boolean} options.reinitialize - 是否重新初始化（清除现有tooltips），默认为 false
     */
    function setupDescriptionTooltips(options = {}) {
        const config = {
            selector: '[data-column="description"]',
            maxLength: 20,
            placement: 'top',
            reinitialize: false,
            ...options
        };

        // 如果需要重新初始化，先清除现有的tooltips
        if (config.reinitialize) {
            document.querySelectorAll(config.selector).forEach(cell => {
                const existingTooltip = bootstrap.Tooltip.getInstance(cell);
                if (existingTooltip) {
                    existingTooltip.dispose();
                }
            });
        }

        // 为指定选择器的元素添加完整描述的data属性
        document.querySelectorAll(config.selector).forEach(cell => {
            const fullText = cell.textContent.trim();
            if (fullText.length > config.maxLength) {
                // 添加Bootstrap tooltip属性
                cell.setAttribute('data-bs-toggle', 'tooltip');
                cell.setAttribute('data-bs-placement', config.placement);
                cell.setAttribute('data-bs-title', fullText);

                // 截断显示的文本
                cell.textContent = fullText.substring(0, config.maxLength) + '...';
            }
        });

        // 初始化新添加的tooltips
        showTooltip();

        console.log(`描述tooltip功能已设置，选择器: ${config.selector}, 截断长度: ${config.maxLength}${config.reinitialize ? ' (重新初始化)' : ''}`);
    }

    // ========== Toast FUNCTIONALITY ==========
    // 显示Toast消息
    function showToast(message, type = '') {
        // 创建toast容器（如果不存在）
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }

        // 创建toast元素
        const toastId = 'toast-' + Date.now();
        const toast = document.createElement('div');
        const typeClass = type ? `text-bg-${type}` : '';
        toast.className = `toast align-items-center ${typeClass} border-0`;
        toast.id = toastId;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        toastContainer.appendChild(toast);

        // 显示toast
        const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
        bsToast.show();

        // 自动移除
        toast.addEventListener('hidden.bs.toast', function() {
            toast.remove();
        });
    }

    // ========== MODAL FUNCTIONALITY ==========
    function showModal(modalId) {
        const modal = new bootstrap.Modal(document.getElementById(modalId));
        modal.show();
    }

    // ========== NOTIFICATION FUNCTIONALITY ==========
    function setupNotificationBlink() {
        // Simulate real-time updates
        const badge = document.querySelector('.notification-badge');
        if (badge) {
            setInterval(() => {
                badge.style.display = badge.style.display === 'none' ? 'block' : 'none';
            }, 3000);
        }
    }

    // ========== GLOBAL EXPORTS ==========
    if (!window.AdminCommon) {
        window.AdminCommon = {};
    }

    window.AdminCommon.showModal = showModal;
    window.AdminCommon.showToast = showToast;
    window.AdminCommon.showTooltip = showTooltip;
    window.AdminCommon.reinitializeTooltips = reinitializeTooltips;
    window.AdminCommon.setupDescriptionTooltips = setupDescriptionTooltips;
    window.AdminCommon.setupNotificationBlink = setupNotificationBlink;

    // 页面加载时全局初始化tooltips
    document.addEventListener('DOMContentLoaded', function() {
        showTooltip();
        console.log('全局tooltip初始化完成');
    });

    console.log('Admin Notifications 已加载');
})();
