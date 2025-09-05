// 视频详情页JavaScript - f-video-detail_4.js
// 升级版本：增强交互和动画效果

document.addEventListener('DOMContentLoaded', function() {
    console.log('视频详情页加载完成');
    
    // 初始化所有功能
    initThemeSystem();
    initFloatingButtons();
    initInteractionButtons();
    initCommentSystem();
    initVideoCards();
    initAccordion();
    initPagination();
    initShareModal();
    
    // 添加动画效果
    addScrollAnimations();
});

// ========================================
// 主题切换系统
// ========================================
function initThemeSystem() {
    const themeDropdown = document.getElementById('themeDropdown');
    const themeLinks = document.querySelectorAll('[data-theme]');
    const navbar = document.querySelector('.navbar');
    
    // 获取当前主题
    const currentTheme = localStorage.getItem('theme') || 'light';
    
    // 应用主题
    function applyTheme(theme) {
        if (theme === 'auto') {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            theme = prefersDark ? 'dark' : 'light';
        }
        
        document.documentElement.setAttribute('data-bs-theme', theme);
        navbar.setAttribute('data-bs-theme', theme);
        localStorage.setItem('theme', theme);
        
        // 更新所有卡片和组件
        updateComponentThemes(theme);
        
        console.log(`应用主题: ${theme}`);
    }
    
    // 更新组件主题
    function updateComponentThemes(theme) {
        const cards = document.querySelectorAll('.card');
        const buttons = document.querySelectorAll('.btn');
        
        cards.forEach(card => {
            card.style.transition = 'all 0.3s ease';
        });
        
        buttons.forEach(button => {
            button.style.transition = 'all 0.3s ease';
        });
    }
    
    // 初始化主题
    applyTheme(currentTheme);
    
    // 绑定主题切换事件
    themeLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const selectedTheme = e.currentTarget.getAttribute('data-theme');
            applyTheme(selectedTheme);
            
            // 添加点击反馈
            e.currentTarget.style.transform = 'scale(0.95)';
            setTimeout(() => {
                e.currentTarget.style.transform = 'scale(1)';
            }, 150);
        });
    });
    
    // 监听系统主题变化
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'auto') {
            applyTheme('auto');
        }
    });
}

// ========================================
// 悬浮按钮功能
// ========================================
function initFloatingButtons() {
    const backToTopBtn = document.getElementById('backToTop');
    const contactBtn = document.getElementById('contactUs');
    
    if (backToTopBtn) {
        // 回到顶部功能
        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
            
            // 添加点击动画
            backToTopBtn.style.transform = 'scale(0.9) rotate(360deg)';
            setTimeout(() => {
                backToTopBtn.style.transform = 'scale(1) rotate(0deg)';
            }, 300);
        });
        
        // 滚动显示/隐藏
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopBtn.style.opacity = '1';
                backToTopBtn.style.visibility = 'visible';
                backToTopBtn.style.transform = 'translateY(0)';
            } else {
                backToTopBtn.style.opacity = '0';
                backToTopBtn.style.visibility = 'hidden';
                backToTopBtn.style.transform = 'translateY(20px)';
            }
        });
    }
    
    if (contactBtn) {
        // 联系我们功能
        contactBtn.addEventListener('click', () => {
            // 模拟联系功能
            showNotification('联系我们功能开发中...', 'info');
            
            // 添加点击动画
            contactBtn.style.transform = 'scale(0.9)';
            setTimeout(() => {
                contactBtn.style.transform = 'scale(1)';
            }, 150);
        });
    }
}

// ========================================
// 交互按钮功能
// ========================================
function initInteractionButtons() {
    const likeBtn = document.querySelector('.like-btn');
    const favoriteBtn = document.querySelector('.favorite-btn');
    const shareBtn = document.querySelector('.share-btn');
    
    if (likeBtn) {
        likeBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const currentCount = parseInt(likeBtn.getAttribute('data-count')) || 0;
            const newCount = currentCount + 1;
            
            likeBtn.setAttribute('data-count', newCount);
            const countSpan = likeBtn.querySelector('span');
            if (countSpan) {
                countSpan.textContent = `点赞 ${newCount}`;
            }
            
            // 添加动画效果
            likeBtn.classList.add('active');
            likeBtn.style.transform = 'scale(1.1)';
            setTimeout(() => {
                likeBtn.style.transform = 'scale(1)';
            }, 200);
            
            showNotification('感谢您的点赞！', 'success');
        });
    }
    
    if (favoriteBtn) {
        favoriteBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const currentCount = parseInt(favoriteBtn.getAttribute('data-count')) || 0;
            const newCount = currentCount + 1;
            
            favoriteBtn.setAttribute('data-count', newCount);
            const countSpan = favoriteBtn.querySelector('span');
            if (countSpan) {
                countSpan.textContent = `收藏 ${newCount}`;
            }
            
            // 添加动画效果
            favoriteBtn.classList.add('active');
            favoriteBtn.style.transform = 'scale(1.1)';
            setTimeout(() => {
                favoriteBtn.style.transform = 'scale(1)';
            }, 200);
            
            showNotification('已添加到收藏！', 'warning');
        });
    }
}

// ========================================
// 评论系统
// ========================================
function initCommentSystem() {
    const commentTextarea = document.querySelector('.comment-textarea');
    const submitBtn = document.querySelector('.comment-submit-btn');
    const commentsList = document.querySelector('.comments-list');
    const replyBtns = document.querySelectorAll('.reply-btn');
    const commentLikeBtns = document.querySelectorAll('.comment-action-btn.like-btn');
    
    // 评论提交
    if (submitBtn && commentTextarea) {
        submitBtn.addEventListener('click', () => {
            const content = commentTextarea.value.trim();
            if (content) {
                addComment(content);
                commentTextarea.value = '';
                showNotification('评论发表成功！', 'success');
            } else {
                showNotification('请输入评论内容', 'warning');
                commentTextarea.focus();
            }
        });
        
        // 回车提交
        commentTextarea.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) {
                submitBtn.click();
            }
        });
    }
    
    // 回复按钮
    replyBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const commentItem = btn.closest('.comment-item');
            const author = commentItem.querySelector('.comment-author').textContent;
            
            if (commentTextarea) {
                commentTextarea.value = `@${author} `;
                commentTextarea.focus();
                
                // 滚动到评论输入框
                commentTextarea.scrollIntoView({ behavior: 'smooth' });
            }
            
            showNotification(`正在回复 @${author}`, 'info');
        });
    });
    
    // 评论点赞
    commentLikeBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const currentLikes = parseInt(btn.getAttribute('data-likes')) || 0;
            const newLikes = currentLikes + 1;
            
            btn.setAttribute('data-likes', newLikes);
            btn.innerHTML = `<i class="bi bi-hand-thumbs-up"></i>${newLikes}`;
            btn.classList.add('active');
            
            // 动画效果
            btn.style.transform = 'scale(1.1)';
            setTimeout(() => {
                btn.style.transform = 'scale(1)';
            }, 200);
        });
    });
}

// 添加新评论
function addComment(content) {
    const commentsList = document.querySelector('.comments-list');
    const commentCount = document.querySelector('.comment-count');
    
    if (commentsList) {
        const newComment = document.createElement('div');
        newComment.className = 'comment-item';
        newComment.innerHTML = `
            <div class="comment-avatar">
                <img src="https://picsum.photos/48/48?random=${Math.floor(Math.random() * 100)}" alt="用户头像" class="avatar-img">
            </div>
            <div class="comment-content">
                <div class="comment-header">
                    <span class="comment-author">新用户</span>
                    <span class="comment-time">刚刚</span>
                </div>
                <div class="comment-text">${content}</div>
                <div class="comment-actions">
                    <button class="comment-action-btn reply-btn">
                        <i class="bi bi-reply"></i>回复
                    </button>
                    <button class="comment-action-btn like-btn" data-likes="0">
                        <i class="bi bi-hand-thumbs-up"></i>0
                    </button>
                </div>
            </div>
        `;
        
        // 添加动画
        newComment.style.opacity = '0';
        newComment.style.transform = 'translateY(20px)';
        commentsList.insertBefore(newComment, commentsList.firstChild);
        
        setTimeout(() => {
            newComment.style.transition = 'all 0.5s ease';
            newComment.style.opacity = '1';
            newComment.style.transform = 'translateY(0)';
        }, 100);
        
        // 更新评论数量
        if (commentCount) {
            const currentCount = parseInt(commentCount.textContent.match(/\d+/)[0]) || 0;
            commentCount.textContent = `(共 ${currentCount + 1} 条)`;
        }
        
        // 重新绑定事件
        initCommentSystem();
    }
}

// ========================================
// 视频卡片交互
// ========================================
function initVideoCards() {
    const recommendedCards = document.querySelectorAll('.recommended-video-card');
    const relatedVideos = document.querySelectorAll('.related-video-item-modern');
    
    // 推荐视频卡片
    recommendedCards.forEach(card => {
        card.addEventListener('click', () => {
            const title = card.querySelector('.video-card-title').textContent;
            showNotification(`正在播放: ${title}`, 'info');
            
            // 模拟跳转
            card.style.transform = 'scale(0.98)';
            setTimeout(() => {
                card.style.transform = 'scale(1)';
            }, 200);
        });
        
        // 鼠标悬停效果
        card.addEventListener('mouseenter', () => {
            const playOverlay = card.querySelector('.video-play-overlay');
            if (playOverlay) {
                playOverlay.style.transform = 'translate(-50%, -50%) scale(1.1)';
            }
        });
        
        card.addEventListener('mouseleave', () => {
            const playOverlay = card.querySelector('.video-play-overlay');
            if (playOverlay) {
                playOverlay.style.transform = 'translate(-50%, -50%) scale(1)';
            }
        });
    });
    
    // 关联视频
    relatedVideos.forEach(video => {
        video.addEventListener('click', () => {
            const title = video.querySelector('.related-video-title').textContent;
            showNotification(`正在播放: ${title}`, 'info');
            
            // 动画效果
            video.style.transform = 'scale(0.98)';
            setTimeout(() => {
                video.style.transform = 'scale(1)';
            }, 200);
        });
    });
}

// ========================================
// 手风琴功能
// ========================================
function initAccordion() {
    const accordionButtons = document.querySelectorAll('.accordion-button');
    
    accordionButtons.forEach(button => {
        button.addEventListener('click', () => {
            // 添加点击动画
            button.style.transform = 'scale(0.98)';
            setTimeout(() => {
                button.style.transform = 'scale(1)';
            }, 150);
        });
    });
}

// ========================================
// 分页功能
// ========================================
function initPagination() {
    const paginationLinks = document.querySelectorAll('.pagination .page-link');
    
    paginationLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            
            // 移除所有活动状态
            paginationLinks.forEach(l => {
                l.parentElement.classList.remove('active');
            });
            
            // 添加当前活动状态
            if (!link.parentElement.classList.contains('disabled')) {
                link.parentElement.classList.add('active');
                
                // 动画效果
                link.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    link.style.transform = 'scale(1)';
                }, 150);
                
                showNotification(`加载第 ${link.textContent} 页评论`, 'info');
            }
        });
    });
}

// ========================================
// 分享弹窗
// ========================================
function initShareModal() {
    const shareOptions = document.querySelectorAll('.share-option');
    
    shareOptions.forEach(option => {
        option.addEventListener('click', () => {
            const platform = option.querySelector('span').textContent;
            
            // 模拟分享功能
            showNotification(`正在分享到 ${platform}...`, 'info');
            
            // 动画效果
            option.style.transform = 'scale(0.95)';
            setTimeout(() => {
                option.style.transform = 'scale(1)';
            }, 150);
            
            // 关闭弹窗
            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('shareModal'));
                if (modal) modal.hide();
            }, 1000);
        });
    });
}

// ========================================
// 滚动动画
// ========================================
function addScrollAnimations() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    // 观察元素
    const animatedElements = document.querySelectorAll([
        '.recommended-video-card',
        '.comment-item',
        '.sidebar-card-modern'
    ].join(','));
    
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
}

// ========================================
// 通知系统
// ========================================
function showNotification(message, type = 'info') {
    // 创建通知元素
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="bi bi-${getNotificationIcon(type)}"></i>
            <span>${message}</span>
        </div>
    `;
    
    // 添加样式
    Object.assign(notification.style, {
        position: 'fixed',
        top: '20px',
        right: '20px',
        zIndex: '10000',
        padding: '1rem 1.5rem',
        backgroundColor: getNotificationColor(type),
        color: 'white',
        borderRadius: '0.75rem',
        boxShadow: '0 8px 25px rgba(0,0,0,0.15)',
        transform: 'translateX(100%)',
        transition: 'all 0.3s ease',
        maxWidth: '350px',
        wordWrap: 'break-word'
    });
    
    // 添加到页面
    document.body.appendChild(notification);
    
    // 动画显示
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // 自动隐藏
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
    
    // 点击关闭
    notification.addEventListener('click', () => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    });
}

function getNotificationIcon(type) {
    const icons = {
        success: 'check-circle-fill',
        warning: 'exclamation-triangle-fill',
        error: 'x-circle-fill',
        info: 'info-circle-fill'
    };
    return icons[type] || icons.info;
}

function getNotificationColor(type) {
    const colors = {
        success: '#10b981',
        warning: '#f59e0b',
        error: '#ef4444',
        info: '#3b82f6'
    };
    return colors[type] || colors.info;
}

// ========================================
// 工具函数
// ========================================

// 防抖函数
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// 节流函数
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

// 平滑滚动
function smoothScrollTo(element, duration = 1000) {
    const targetPosition = element.offsetTop;
    const startPosition = window.pageYOffset;
    const distance = targetPosition - startPosition;
    let startTime = null;
    
    function animation(currentTime) {
        if (startTime === null) startTime = currentTime;
        const timeElapsed = currentTime - startTime;
        const run = ease(timeElapsed, startPosition, distance, duration);
        window.scrollTo(0, run);
        if (timeElapsed < duration) requestAnimationFrame(animation);
    }
    
    function ease(t, b, c, d) {
        t /= d / 2;
        if (t < 1) return c / 2 * t * t + b;
        t--;
        return -c / 2 * (t * (t - 2) - 1) + b;
    }
    
    requestAnimationFrame(animation);
}

// 监听页面能见性
function handleVisibilityChange() {
    if (document.hidden) {
        console.log('页面已隐藏');
    } else {
        console.log('页面可见');
    }
}

document.addEventListener('visibilitychange', handleVisibilityChange);

// 错误处理
window.addEventListener('error', function(e) {
    console.error('页面错误:', e.error);
});

// 性能监控
if ('performance' in window) {
    window.addEventListener('load', () => {
        const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
        console.log(`页面加载时间: ${loadTime}ms`);
    });
}