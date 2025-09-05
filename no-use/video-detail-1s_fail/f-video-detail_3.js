// 视频详情页JavaScript功能 - f-video-detail_3.js

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
    
    // 平台链接功能
    initPlatformLinks();
    
    // 初始化动画
    initAnimations();
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
    
    // 初始隐藏
    backToTopBtn.style.display = 'none';
    
    // 监听滚动事件
    let scrollThrottled = false;
    window.addEventListener('scroll', function() {
        if (!scrollThrottled) {
            requestAnimationFrame(() => {
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
                scrollThrottled = false;
            });
            scrollThrottled = true;
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
    const playButton = document.querySelector('.play-button');
    const videoThumbnail = document.querySelector('.video-thumbnail');
    
    if (!playButton) return;
    
    let isPlaying = false;
    
    playButton.addEventListener('click', function() {
        if (!isPlaying) {
            // 开始播放
            isPlaying = true;
            this.innerHTML = '<i class="bi bi-pause-fill"></i>';
            this.style.background = 'rgba(0, 0, 0, 0.7)';
            
            // 添加播放动画效果
            if (videoThumbnail) {
                videoThumbnail.style.filter = 'brightness(0.8)';
            }
            
            showMessage('视频开始播放', 'success');
            
            // 实际项目中这里应该初始化真正的视频播放器
            // 比如嵌入YouTube、Bilibili等第三方播放器
            
        } else {
            // 暂停播放
            isPlaying = false;
            this.innerHTML = '<i class="bi bi-play-fill"></i>';
            this.style.background = 'rgba(255, 255, 255, 0.9)';
            
            if (videoThumbnail) {
                videoThumbnail.style.filter = 'brightness(1)';
            }
            
            showMessage('视频已暂停', 'info');
        }
    });
    
    // 鼠标悬停效果
    const videoContainer = document.querySelector('.video-player-container');
    if (videoContainer) {
        videoContainer.addEventListener('mouseenter', function() {
            playButton.style.transform = 'scale(1.1)';
        });
        
        videoContainer.addEventListener('mouseleave', function() {
            playButton.style.transform = 'scale(1)';
        });
    }
}

// 用户交互功能初始化
function initUserInteractions() {
    // 点赞功能
    const likeBtn = document.querySelector('.like-btn');
    if (likeBtn) {
        let liked = false;
        let likeCount = parseInt(likeBtn.dataset.count) || 123;
        
        likeBtn.addEventListener('click', function() {
            const countSpan = this.querySelector('.btn-count');
            const icon = this.querySelector('i');
            
            if (!liked) {
                liked = true;
                likeCount++;
                this.classList.add('active');
                icon.className = 'bi bi-hand-thumbs-up-fill';
                countSpan.textContent = likeCount;
                
                // 添加点击动画
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 150);
                
                showMessage('点赞成功！', 'success');
            } else {
                liked = false;
                likeCount--;
                this.classList.remove('active');
                icon.className = 'bi bi-hand-thumbs-up';
                countSpan.textContent = likeCount;
                
                showMessage('已取消点赞', 'info');
            }
            
            // 更新数据属性
            this.dataset.count = likeCount;
        });
    }
    
    // 收藏功能
    const favoriteBtn = document.querySelector('.favorite-btn');
    if (favoriteBtn) {
        let favorited = false;
        let favoriteCount = parseInt(favoriteBtn.dataset.count) || 45;
        
        favoriteBtn.addEventListener('click', function() {
            const countSpan = this.querySelector('.btn-count');
            const icon = this.querySelector('i');
            
            if (!favorited) {
                favorited = true;
                favoriteCount++;
                this.classList.add('active');
                icon.className = 'bi bi-star-fill';
                countSpan.textContent = favoriteCount;
                
                // 添加点击动画
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 150);
                
                showMessage('收藏成功！', 'success');
            } else {
                favorited = false;
                favoriteCount--;
                this.classList.remove('active');
                icon.className = 'bi bi-star';
                countSpan.textContent = favoriteCount;
                
                showMessage('已取消收藏', 'info');
            }
            
            // 更新数据属性
            this.dataset.count = favoriteCount;
        });
    }
}

// 评论功能初始化
function initComments() {
    const commentForm = document.querySelector('.comment-form');
    const commentTextarea = commentForm?.querySelector('.comment-textarea');
    const commentBtn = commentForm?.querySelector('.comment-submit-btn');
    const commentsList = document.querySelector('.comments-list');
    const loadMoreBtn = document.querySelector('.load-more-btn');
    const commentCount = document.querySelector('.comment-count');
    
    if (!commentForm) return;
    
    let totalComments = 18;
    
    // 发表评论
    commentBtn?.addEventListener('click', function() {
        const commentText = commentTextarea?.value.trim();
        
        if (!commentText) {
            showMessage('请输入评论内容', 'warning');
            commentTextarea?.focus();
            return;
        }
        
        if (commentText.length < 5) {
            showMessage('评论内容太短，至少需要5个字符', 'warning');
            return;
        }
        
        // 创建新评论元素
        const newComment = createCommentElement({
            avatar: `https://picsum.photos/48/48?random=${Date.now()}`,
            username: '当前用户',
            time: '刚刚',
            text: commentText,
            likes: 0
        });
        
        // 添加到评论列表顶部
        commentsList?.insertAdjacentHTML('afterbegin', newComment);
        
        // 更新评论计数
        totalComments++;
        if (commentCount) {
            commentCount.textContent = `(共 ${totalComments} 条)`;
        }
        
        // 清空输入框
        commentTextarea.value = '';
        
        // 显示成功消息
        showMessage('评论发表成功！', 'success');
        
        // 滚动到新评论
        setTimeout(() => {
            const newCommentElement = commentsList?.firstElementChild;
            if (newCommentElement) {
                newCommentElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }, 300);
    });
    
    // 评论点赞功能
    document.addEventListener('click', function(e) {
        if (e.target.closest('.comment-actions .like-btn')) {
            const btn = e.target.closest('.comment-actions .like-btn');
            const currentLikes = parseInt(btn.dataset.likes) || 0;
            
            if (!btn.classList.contains('liked')) {
                btn.classList.add('liked');
                btn.innerHTML = `<i class="bi bi-hand-thumbs-up-fill"></i>${currentLikes + 1}`;
                btn.dataset.likes = currentLikes + 1;
                btn.style.color = '#ef4444';
                
                // 添加动画效果
                btn.style.transform = 'scale(1.1)';
                setTimeout(() => {
                    btn.style.transform = 'scale(1)';
                }, 150);
            }
        }
        
        // 回复按钮功能
        if (e.target.closest('.comment-actions .reply-btn')) {
            const replyBtn = e.target.closest('.comment-actions .reply-btn');
            const commentItem = replyBtn.closest('.comment-item');
            const author = commentItem.querySelector('.comment-author').textContent;
            
            // 滚动到评论输入框并自动填入@用户名
            commentTextarea?.focus();
            commentTextarea.value = `@${author} `;
            commentTextarea?.scrollIntoView({ behavior: 'smooth' });
            
            showMessage(`正在回复 ${author}`, 'info');
        }
    });
    
    // 显示更多评论
    loadMoreBtn?.addEventListener('click', function() {
        const originalText = this.textContent;
        this.textContent = '加载中...';
        this.disabled = true;
        
        setTimeout(() => {
            // 模拟添加更多评论
            const moreComments = [];
            for (let i = 0; i < 3; i++) {
                const randomId = Date.now() + i;
                moreComments.push(createCommentElement({
                    avatar: `https://picsum.photos/48/48?random=${randomId}`,
                    username: `用户${randomId.toString().slice(-4)}`,
                    time: `2024-01-${Math.floor(Math.random() * 15) + 1}`,
                    text: generateRandomComment(),
                    likes: Math.floor(Math.random() * 20)
                }));
            }
            
            // 添加到评论列表
            moreComments.forEach(comment => {
                commentsList?.insertAdjacentHTML('beforeend', comment);
            });
            
            // 更新评论计数
            totalComments += 3;
            if (commentCount) {
                commentCount.textContent = `(共 ${totalComments} 条)`;
            }
            
            this.textContent = originalText;
            this.disabled = false;
            
            showMessage('加载了3条新评论', 'success');
        }, 1000);
    });
}

// 创建评论元素
function createCommentElement(comment) {
    return `
        <div class="comment-item" style="opacity: 0; animation: slideUp 0.5s ease-out forwards;">
            <div class="comment-avatar">
                <img src="${comment.avatar}" alt="用户头像" class="avatar-img">
            </div>
            <div class="comment-content">
                <div class="comment-header">
                    <span class="comment-author">${comment.username}</span>
                    <span class="comment-time">${comment.time}</span>
                </div>
                <div class="comment-text">${comment.text}</div>
                <div class="comment-actions">
                    <button class="comment-action-btn reply-btn">
                        <i class="bi bi-reply"></i>回复
                    </button>
                    <button class="comment-action-btn like-btn" data-likes="${comment.likes}">
                        <i class="bi bi-hand-thumbs-up"></i>${comment.likes}
                    </button>
                </div>
            </div>
        </div>
    `;
}

// 生成随机评论内容
function generateRandomComment() {
    const comments = [
        '这个教程很有帮助，谢谢分享！',
        '讲解得很清楚，适合初学者学习。',
        '希望能出更多这样的教程内容。',
        '学到了很多新知识，收藏了！',
        '老师讲得真好，期待下一个视频。',
        '作为新手，这个教程对我来说很有用。',
        '内容很详细，跟着做了一遍，很有收获。',
        '希望能有更多实战项目的教程。',
        '这个系列教程都很不错，持续关注中。',
        '讲解逻辑很清晰，容易理解和掌握。'
    ];
    
    return comments[Math.floor(Math.random() * comments.length)];
}

// 相关视频功能
function initRelatedVideos() {
    const relatedVideoItems = document.querySelectorAll('.recommended-item, .related-video-item');
    
    relatedVideoItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            // 获取视频标题
            const title = this.querySelector('.recommended-title-text, .related-video-title')?.textContent || '未知视频';
            
            // 模拟跳转到视频详情页
            console.log('跳转到视频:', title);
            
            // 添加点击效果
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
            
            // 实际项目中应该跳转到对应的视频详情页
            showMessage(`即将播放: ${title}`, 'info');
        });
    });
}

// 平台链接功能
function initPlatformLinks() {
    const platformLinks = document.querySelectorAll('.platform-btn');
    
    platformLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const platform = this.textContent.trim();
            const videoTitle = document.querySelector('.video-title').textContent.trim();
            
            // 添加点击效果
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
            
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
}

// 联系我们功能
function initContactUs() {
    const contactBtn = document.getElementById('contactUs');
    
    if (!contactBtn) return;
    
    contactBtn.addEventListener('click', function() {
        showMessage('联系我们功能开发中，请发送邮件至 contact@example.com', 'info');
    });
}

// 分享功能初始化
function initShareFunctions() {
    const shareOptions = document.querySelectorAll('.share-option');
    
    shareOptions.forEach(option => {
        option.addEventListener('click', function() {
            const platform = this.textContent.trim();
            const videoTitle = document.querySelector('.video-title').textContent.trim();
            const currentUrl = window.location.href;
            
            // 添加点击效果
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
            
            switch(platform) {
                case '微信':
                    showMessage('请使用微信扫码分享功能', 'info');
                    break;
                    
                case 'QQ':
                    showMessage('QQ分享功能开发中', 'info');
                    break;
                    
                case '微博':
                    showMessage('微博分享功能开发中', 'info');
                    break;
                    
                case '复制链接':
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

// 初始化动画
function initAnimations() {
    // 使用Intersection Observer实现滚动动画
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '50px'
        });
        
        // 观察所有卡片元素
        const cards = document.querySelectorAll('.card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = `all 0.6s ease-out ${index * 0.1}s`;
            observer.observe(card);
        });
    }
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
    // 移除现有的消息提示
    const existingAlert = document.querySelector('.toast-message');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    // 创建消息提示元素
    const alertDiv = document.createElement('div');
    alertDiv.className = `toast-message alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = `
        top: 80px; 
        right: 20px; 
        z-index: 9999; 
        min-width: 320px;
        max-width: 400px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        border: none;
        border-radius: 0.75rem;
    `;
    
    // 选择图标
    let icon = 'info-circle';
    switch(type) {
        case 'success': icon = 'check-circle'; break;
        case 'warning': icon = 'exclamation-triangle'; break;
        case 'error': case 'danger': icon = 'x-circle'; break;
    }
    
    alertDiv.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="bi bi-${icon} me-2"></i>
            <span>${message}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // 3.5秒后自动消失
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 150);
        }
    }, 3500);
}

// 页面滚动优化
let ticking = false;

function updateScrollElements() {
    const navbar = document.querySelector('.navbar');
    const scrollTop = window.pageYOffset;
    
    // 处理导航栏滚动效果
    if (navbar) {
        if (scrollTop > 50) {
            navbar.style.background = 'rgba(255, 255, 255, 0.98)';
            navbar.style.backdropFilter = 'blur(20px)';
            navbar.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.1)';
        } else {
            navbar.style.background = '';
            navbar.style.backdropFilter = '';
            navbar.style.boxShadow = '';
        }
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
                    
                    // 添加加载完成后的淡入效果
                    img.addEventListener('load', function() {
                        this.style.opacity = '1';
                        this.style.transition = 'opacity 0.3s ease';
                    });
                    
                    // 如果图片已经加载完成，直接显示
                    if (img.complete) {
                        img.style.opacity = '1';
                    } else {
                        img.style.opacity = '0';
                    }
                    
                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px'
        });
        
        images.forEach(img => {
            imageObserver.observe(img);
        });
    }
}

// 键盘快捷键支持
document.addEventListener('keydown', function(e) {
    // 空格键播放/暂停
    if (e.code === 'Space' && !e.target.matches('input, textarea')) {
        e.preventDefault();
        const playButton = document.querySelector('.play-button');
        if (playButton) {
            playButton.click();
        }
    }
    
    // L键点赞
    if (e.code === 'KeyL' && !e.target.matches('input, textarea')) {
        e.preventDefault();
        const likeBtn = document.querySelector('.like-btn');
        if (likeBtn) {
            likeBtn.click();
        }
    }
    
    // S键收藏
    if (e.code === 'KeyS' && !e.target.matches('input, textarea')) {
        e.preventDefault();
        const favoriteBtn = document.querySelector('.favorite-btn');
        if (favoriteBtn) {
            favoriteBtn.click();
        }
    }
});

// 初始化所有功能
document.addEventListener('DOMContentLoaded', function() {
    initLazyLoading();
    
    // 添加页面加载完成的动画
    document.body.style.opacity = '0';
    document.body.style.transition = 'opacity 0.5s ease';
    
    setTimeout(() => {
        document.body.style.opacity = '1';
    }, 100);
    
    console.log('视频详情页初始化完成');
});