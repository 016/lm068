// 视频详情页JavaScript功能 - f-video-detail_2.js

document.addEventListener('DOMContentLoaded', function() {
    
    // 主题切换功能（继承自video-list页面）
    initThemeToggle();
    
    // 回到顶部功能
    initBackToTop();
    
    // 视频播放功能
    initVideoPlayer();
    
    // 用户交互功能（点赞、收藏、分享）
    initUserInteractions();
    
    // 评论功能
    initComments();
    
    // 相关视频点击
    initRelatedVideos();
    
    // 联系我们功能
    initContactUs();
    
    // 分享功能
    initShareFunctions();
});

// 主题切换初始化
function initThemeToggle() {
    const themeDropdown = document.getElementById('themeDropdown');
    const themeItems = document.querySelectorAll('[data-theme]');
    
    if (!themeDropdown || !themeItems.length) return;
    
    // 获取当前主题
    const currentTheme = localStorage.getItem('theme') || 'auto';
    
    // 应用主题
    function applyTheme(theme) {
        const html = document.documentElement;
        const navbar = document.querySelector('.navbar');
        
        if (theme === 'auto') {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            theme = prefersDark ? 'dark' : 'light';
        }
        
        html.setAttribute('data-bs-theme', theme);
        if (navbar) {
            navbar.setAttribute('data-bs-theme', theme);
        }
    }
    
    // 初始应用主题
    applyTheme(currentTheme);
    
    // 主题切换事件
    themeItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const theme = this.dataset.theme;
            localStorage.setItem('theme', theme);
            applyTheme(theme);
        });
    });
}

// 回到顶部功能
function initBackToTop() {
    const backToTopBtn = document.getElementById('backToTop');
    
    if (!backToTopBtn) return;
    
    // 监听滚动事件
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopBtn.style.display = 'flex';
            backToTopBtn.style.opacity = '1';
        } else {
            backToTopBtn.style.opacity = '0';
            setTimeout(() => {
                if (window.pageYOffset <= 300) {
                    backToTopBtn.style.display = 'none';
                }
            }, 300);
        }
    });
    
    // 点击回到顶部
    backToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// 视频播放器功能
function initVideoPlayer() {
    const playOverlay = document.querySelector('.video-play-overlay');
    const videoCover = document.querySelector('.video-cover-wrapper');
    
    if (!playOverlay || !videoCover) return;
    
    playOverlay.addEventListener('click', function() {
        // 模拟视频播放
        console.log('播放视频');
        
        // 添加播放效果
        playOverlay.innerHTML = '<i class="bi bi-pause-circle-fill"></i>';
        
        // 实际项目中这里应该初始化真正的视频播放器
        // 比如嵌入YouTube、Bilibili等第三方播放器
        
        // 3秒后恢复播放按钮（演示用）
        setTimeout(() => {
            playOverlay.innerHTML = '<i class="bi bi-play-circle-fill"></i>';
        }, 3000);
    });
    
    // 鼠标悬停效果
    videoCover.addEventListener('mouseenter', function() {
        playOverlay.style.transform = 'translate(-50%, -50%) scale(1.1)';
    });
    
    videoCover.addEventListener('mouseleave', function() {
        playOverlay.style.transform = 'translate(-50%, -50%) scale(1)';
    });
}

// 用户交互功能初始化
function initUserInteractions() {
    // 点赞功能
    const likeBtn = document.querySelector('.btn-outline-success');
    if (likeBtn) {
        let liked = false;
        let likeCount = 123;
        
        likeBtn.addEventListener('click', function() {
            if (!liked) {
                liked = true;
                likeCount++;
                this.classList.remove('btn-outline-success');
                this.classList.add('btn-success');
                this.innerHTML = `<i class="bi bi-hand-thumbs-up-fill me-1"></i>点赞 ${likeCount}`;
            } else {
                liked = false;
                likeCount--;
                this.classList.remove('btn-success');
                this.classList.add('btn-outline-success');
                this.innerHTML = `<i class="bi bi-hand-thumbs-up me-1"></i>点赞 ${likeCount}`;
            }
        });
    }
    
    // 收藏功能
    const favoriteBtn = document.querySelector('.btn-outline-warning');
    if (favoriteBtn) {
        let favorited = false;
        let favoriteCount = 45;
        
        favoriteBtn.addEventListener('click', function() {
            if (!favorited) {
                favorited = true;
                favoriteCount++;
                this.classList.remove('btn-outline-warning');
                this.classList.add('btn-warning');
                this.innerHTML = `<i class="bi bi-star-fill me-1"></i>收藏 ${favoriteCount}`;
            } else {
                favorited = false;
                favoriteCount--;
                this.classList.remove('btn-warning');
                this.classList.add('btn-outline-warning');
                this.innerHTML = `<i class="bi bi-star me-1"></i>收藏 ${favoriteCount}`;
            }
        });
    }
}

// 评论功能初始化
function initComments() {
    const commentForm = document.querySelector('.comment-form');
    const commentTextarea = commentForm?.querySelector('textarea');
    const commentBtn = commentForm?.querySelector('.btn-primary');
    const commentsList = document.querySelector('.comments-list');
    const moreCommentsBtn = document.querySelector('.text-center .btn-outline-secondary');
    
    if (!commentForm) return;
    
    // 发表评论
    commentBtn?.addEventListener('click', function() {
        const commentText = commentTextarea?.value.trim();
        
        if (!commentText) {
            alert('请输入评论内容');
            return;
        }
        
        // 创建新评论元素
        const newComment = createCommentElement({
            avatar: 'https://picsum.photos/40/40?random=' + Date.now(),
            username: '当前用户',
            time: '刚刚',
            text: commentText,
            likes: 0
        });
        
        // 添加到评论列表顶部
        commentsList?.insertAdjacentHTML('afterbegin', newComment);
        
        // 清空输入框
        commentTextarea.value = '';
        
        // 显示成功消息
        showMessage('评论发表成功！', 'success');
    });
    
    // 评论点赞功能
    document.addEventListener('click', function(e) {
        if (e.target.closest('.comment-actions .btn-outline-primary')) {
            const btn = e.target.closest('.comment-actions .btn-outline-primary');
            const currentLikes = parseInt(btn.textContent.trim()) || 0;
            
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-primary');
            btn.innerHTML = `<i class="bi bi-hand-thumbs-up-fill me-1"></i>${currentLikes + 1}`;
        }
    });
    
    // 显示更多评论
    moreCommentsBtn?.addEventListener('click', function() {
        // 模拟加载更多评论
        const loadingText = '加载中...';
        const originalText = this.textContent;
        
        this.textContent = loadingText;
        this.disabled = true;
        
        setTimeout(() => {
            // 模拟添加更多评论
            for (let i = 0; i < 3; i++) {
                const newComment = createCommentElement({
                    avatar: `https://picsum.photos/40/40?random=${Date.now() + i}`,
                    username: `用户${Date.now() + i}`,
                    time: `2024-01-${15 - i}`,
                    text: `这是一条新加载的评论内容${i + 1}...`,
                    likes: Math.floor(Math.random() * 10)
                });
                
                commentsList?.insertAdjacentHTML('beforeend', newComment);
            }
            
            this.textContent = originalText;
            this.disabled = false;
        }, 1000);
    });
}

// 创建评论元素
function createCommentElement(comment) {
    return `
        <div class="comment-item mb-3">
            <div class="d-flex">
                <div class="comment-avatar me-3">
                    <img src="${comment.avatar}" alt="用户头像" class="rounded-circle">
                </div>
                <div class="comment-content flex-grow-1">
                    <div class="comment-header mb-1">
                        <span class="fw-semibold">${comment.username}</span>
                        <small class="text-muted ms-2">${comment.time}</small>
                    </div>
                    <p class="comment-text mb-2">${comment.text}</p>
                    <div class="comment-actions">
                        <button class="btn btn-sm btn-outline-secondary me-2">回复</button>
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-hand-thumbs-up me-1"></i>${comment.likes}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// 相关视频功能
function initRelatedVideos() {
    const relatedVideoItems = document.querySelectorAll('.related-video-item a, .related-sidebar-item a');
    
    relatedVideoItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            // 获取视频标题
            const title = this.querySelector('h6')?.textContent || '未知视频';
            
            // 模拟跳转到视频详情页
            console.log('跳转到视频:', title);
            
            // 实际项目中应该跳转到对应的视频详情页
            showMessage(`即将播放: ${title}`, 'info');
        });
    });
}

// 联系我们功能
function initContactUs() {
    const contactBtn = document.getElementById('contactUs');
    
    if (!contactBtn) return;
    
    contactBtn.addEventListener('click', function() {
        // 模拟联系我们功能
        showMessage('联系我们功能开发中，请发送邮件至 contact@example.com', 'info');
    });
}

// 分享功能初始化
function initShareFunctions() {
    const shareButtons = document.querySelectorAll('#shareModal .btn');
    
    shareButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const platform = this.textContent.trim();
            const videoTitle = document.querySelector('.video-title').textContent.trim();
            const currentUrl = window.location.href;
            
            switch(platform) {
                case '微信':
                    // 微信分享逻辑
                    showMessage('请使用微信扫码分享功能', 'info');
                    break;
                    
                case 'QQ':
                    // QQ分享逻辑
                    showMessage('QQ分享功能开发中', 'info');
                    break;
                    
                case '微博':
                    // 微博分享逻辑
                    showMessage('微博分享功能开发中', 'info');
                    break;
                    
                case '复制链接':
                    // 复制链接功能
                    copyToClipboard(currentUrl);
                    showMessage('链接已复制到剪贴板', 'success');
                    
                    // 关闭模态框
                    const modal = bootstrap.Modal.getInstance(document.getElementById('shareModal'));
                    modal?.hide();
                    break;
            }
        });
    });
}

// 复制到剪贴板功能
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text);
    } else {
        // 兼容旧浏览器
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
    }
}

// 显示消息提示
function showMessage(message, type = 'info') {
    // 创建消息提示元素
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // 3秒后自动消失
    setTimeout(() => {
        alertDiv?.remove();
    }, 3000);
}

// 平台链接点击处理
document.addEventListener('DOMContentLoaded', function() {
    const platformLinks = document.querySelectorAll('.platform-links .btn');
    
    platformLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const platform = this.textContent.trim();
            const videoTitle = document.querySelector('.video-title').textContent.trim();
            
            // 根据不同平台处理跳转逻辑
            switch(platform) {
                case 'YouTube':
                    showMessage(`即将跳转到 YouTube 观看: ${videoTitle}`, 'info');
                    break;
                case 'Bilibili':
                    showMessage(`即将跳转到 Bilibili 观看: ${videoTitle}`, 'info');
                    break;
                case '抖音':
                    showMessage(`即将跳转到抖音观看: ${videoTitle}`, 'info');
                    break;
            }
        });
    });
});

// 页面滚动优化
let ticking = false;

function updateScrollElements() {
    // 处理导航栏滚动效果
    const navbar = document.querySelector('.navbar');
    if (navbar && window.pageYOffset > 50) {
        navbar.style.background = 'rgba(255, 255, 255, 0.98)';
        navbar.style.backdropFilter = 'blur(20px)';
    } else if (navbar) {
        navbar.style.background = '';
        navbar.style.backdropFilter = '';
    }
    
    ticking = false;
}

window.addEventListener('scroll', function() {
    if (!ticking) {
        requestAnimationFrame(updateScrollElements);
        ticking = true;
    }
});

// 图片懒加载优化
function initLazyLoading() {
    const images = document.querySelectorAll('img[src]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.classList.add('fade-in');
                    observer.unobserve(img);
                }
            });
        });
        
        images.forEach(img => imageObserver.observe(img));
    }
}

// 初始化懒加载
document.addEventListener('DOMContentLoaded', initLazyLoading);