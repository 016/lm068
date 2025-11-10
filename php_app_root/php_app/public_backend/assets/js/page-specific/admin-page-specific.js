/**
 * Admin Page Specific - 页面特定功能
 *
 * 依赖：admin-utils.js (formatLargeNumber)
 *
 * 提供功能：
 * - 标签查看页面特效
 * - 信息卡片悬停效果
 * - 分析项悬停效果
 * - 统计数据更新
 * - 数字动画效果
 */

(function() {
    'use strict';

    // ========== TAG VIEW PAGE SPECIFIC COMMON FUNCTIONS ==========

    /**
     * Setup info card hover effects
     */
    function setupInfoCardEffects() {
        document.querySelectorAll('.info-card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.borderColor = 'var(--accent-primary)';
            });

            card.addEventListener('mouseleave', () => {
                card.style.borderColor = 'var(--border-light)';
            });
        });
    }

    /**
     * Setup analytics hover effects
     */
    function setupAnalyticsEffects() {
        document.querySelectorAll('.analytics-item').forEach(item => {
            item.addEventListener('mouseenter', () => {
                item.style.transform = 'translateY(-2px)';
                item.style.boxShadow = 'var(--shadow-md)';
            });

            item.addEventListener('mouseleave', () => {
                item.style.transform = 'translateY(0)';
                item.style.boxShadow = 'none';
            });
        });
    }

    /**
     * Initialize tag view page specific effects
     */
    function initializeTagViewEffects() {
        setupInfoCardEffects();
        setupAnalyticsEffects();
    }

    /**
     * Update tag statistics display
     */
    function updateTagStats(videoCount, viewCount, likeCount, commentCount) {
        const statsElements = {
            videoCount: document.querySelector('.analytics-item:nth-child(1) .analytics-value'),
            viewCount: document.querySelector('.analytics-item:nth-child(2) .analytics-value'),
            likeCount: document.querySelector('.analytics-item:nth-child(3) .analytics-value'),
            commentCount: document.querySelector('.analytics-item:nth-child(4) .analytics-value')
        };

        // Use formatLargeNumber from AdminCommon if available
        const formatNumber = (window.AdminCommon && window.AdminCommon.formatLargeNumber)
            ? window.AdminCommon.formatLargeNumber
            : (num) => num.toString();

        if (statsElements.videoCount) {
            statsElements.videoCount.textContent = formatNumber(videoCount);
        }
        if (statsElements.viewCount) {
            statsElements.viewCount.textContent = formatNumber(viewCount);
        }
        if (statsElements.likeCount) {
            statsElements.likeCount.textContent = formatNumber(likeCount);
        }
        if (statsElements.commentCount) {
            statsElements.commentCount.textContent = formatNumber(commentCount);
        }
    }

    /**
     * Animate number counting effect
     */
    function animateNumber(element, start, end, duration = 1000) {
        const range = end - start;
        const minTimer = 50;
        let stepTime = Math.abs(Math.floor(duration / range));
        stepTime = Math.max(stepTime, minTimer);

        const startTime = new Date().getTime();
        const endTime = startTime + duration;

        // Use formatLargeNumber from AdminCommon if available
        const formatNumber = (window.AdminCommon && window.AdminCommon.formatLargeNumber)
            ? window.AdminCommon.formatLargeNumber
            : (num) => num.toString();

        function run() {
            const now = new Date().getTime();
            const remaining = Math.max((endTime - now) / duration, 0);
            const value = Math.round(end - (remaining * range));
            element.textContent = formatNumber(value);

            if (value == end) {
                clearInterval(timer);
            }
        }

        const timer = setInterval(run, stepTime);
        run();
    }

    /**
     * Initialize animated counters for tag view page
     */
    function initializeAnimatedCounters() {
        const animatedElements = document.querySelectorAll('.analytics-value, .quick-stat-value');

        animatedElements.forEach(element => {
            const text = element.textContent.trim();
            let targetValue = 0;

            // Parse text to get numeric value
            if (text.includes('M')) {
                targetValue = parseFloat(text) * 1000000;
            } else if (text.includes('K')) {
                targetValue = parseFloat(text) * 1000;
            } else {
                targetValue = parseInt(text.replace(/,/g, ''));
            }

            if (!isNaN(targetValue) && targetValue > 0) {
                animateNumber(element, 0, targetValue, 2000);
            }
        });
    }

    // ========== GLOBAL EXPORTS ==========
    if (!window.AdminCommon) {
        window.AdminCommon = {};
    }

    if (!window.AdminCommon.TagViewUtils) {
        window.AdminCommon.TagViewUtils = {};
    }

    window.AdminCommon.TagViewUtils.initializeTagViewEffects = initializeTagViewEffects;
    window.AdminCommon.TagViewUtils.setupInfoCardEffects = setupInfoCardEffects;
    window.AdminCommon.TagViewUtils.setupAnalyticsEffects = setupAnalyticsEffects;
    window.AdminCommon.TagViewUtils.updateTagStats = updateTagStats;
    window.AdminCommon.TagViewUtils.animateNumber = animateNumber;
    window.AdminCommon.TagViewUtils.initializeAnimatedCounters = initializeAnimatedCounters;

    console.log('Admin Page Specific 已加载');
})();
