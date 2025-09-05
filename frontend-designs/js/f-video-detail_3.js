// f-video-detail_3.js - 视频详情页面专用脚本 (第二版本)

document.addEventListener('DOMContentLoaded', function() {
    // 初始化视频播放功能
    initVideoPlayer();
    
    // 初始化交互按钮
    initInteractionButtons();
    
    // 初始化评论系统
    initAdvancedCommentSystem();
    
    // 初始化推荐视频
    initRecommendationSystem();
    
    // 初始化动画效果
    initAnimations();
});

// 初始化视频播放功能
function initVideoPlayer() {
    const playBtn = document.querySelector('.play-btn');
    const videoOverlay = document.querySelector('.video-overlay');
    const videoCover = document.querySelector('.video-cover');
    
    if (playBtn && videoOverlay) {
        // 播放按钮点击事件
        playBtn.addEventListener('click', function() {
            handleVideoPlay();
        });
        
        // 点击视频封面也可以播放
        if (videoCover) {
            videoCover.addEventListener('click', function() {
                handleVideoPlay();
            });
        }
        
        // 键盘快捷键 - 空格播放
        document.addEventListener('keydown', function(e) {
            if (e.code === 'Space' && !isTyping()) {
                e.preventDefault();
                handleVideoPlay();
            }
        });
    }
}

// 处理视频播放
function handleVideoPlay() {
    const playBtn = document.querySelector('.play-btn');
    const videoOverlay = document.querySelector('.video-overlay');
    
    // 添加播放动画
    playBtn.style.transform = 'scale(1.2)';
    setTimeout(() => {
        playBtn.style.transform = 'scale(1)';
    }, 200);
    
    // 这里可以集成真实的视频播放器
    showVideoPlayerModal();
}

// 显示视频播放器模态框
function showVideoPlayerModal() {
    const modal = document.createElement('div');
    modal.className = 'modal fade video-player-modal';
    modal.innerHTML = `
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">JavaScript基础教程详解</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="video-player-container">
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item w-100" style="height: 500px;" 
                                src="about:blank" frameborder="0" allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // 显示模态框
    const modalInstance = new bootstrap.Modal(modal);
    modalInstance.show();
    
    // 模态框关闭后移除
    modal.addEventListener('hidden.bs.modal', function() {
        modal.remove();
    });
    
    // 模拟加载视频
    setTimeout(() => {
        const iframe = modal.querySelector('iframe');
        iframe.src = 'https://www.youtube.com/embed/dQw4w9WgXcQ'; // 示例视频
    }, 1000);
}

// 检查是否正在输入
function isTyping() {
    const activeElement = document.activeElement;
    return activeElement.tagName === 'INPUT' || activeElement.tagName === 'TEXTAREA';
}

// 初始化交互按钮
function initInteractionButtons() {
    const interactionBtns = document.querySelectorAll('.interaction-btn');
    
    interactionBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.dataset.action;
            
            switch(action) {
                case 'like':
                    handleLikeAction(this);
                    break;
                case 'favorite':
                    handleFavoriteAction(this);
                    break;
                case 'share':
                    handleShareAction();
                    break;
                case 'download':
                    handleDownloadAction();
                    break;
            }
        });
    });
}

// 处理点赞操作
function handleLikeAction(btn) {
    const countElement = btn.querySelector('.btn-count');
    const iconElement = btn.querySelector('i');
    const textElement = btn.querySelector('.btn-text');
    
    let currentCount = parseInt(countElement.textContent);
    const isLiked = btn.classList.contains('btn-primary');
    
    if (isLiked) {
        // 取消点赞
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-outline-primary');
        iconElement.className = 'bi bi-heart';
        textElement.textContent = '喜欢';
        countElement.textContent = currentCount - 1;
        
        // 动画效果
        animateButton(btn, 'unlike');
        showToast('已取消点赞', 'info');
    } else {
        // 点赞
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-primary');
        iconElement.className = 'bi bi-heart-fill';
        textElement.textContent = '已喜欢';
        countElement.textContent = currentCount + 1;
        
        // 动画效果
        animateButton(btn, 'like');
        showToast('点赞成功！', 'success');
        
        // 创建飞出的爱心效果
        createFloatingHeart(btn);
    }
}

// 处理收藏操作
function handleFavoriteAction(btn) {
    const countElement = btn.querySelector('.btn-count');
    const iconElement = btn.querySelector('i');
    const textElement = btn.querySelector('.btn-text');
    
    let currentCount = parseInt(countElement.textContent);
    const isFavorited = btn.classList.contains('btn-warning');
    
    if (isFavorited) {
        // 取消收藏
        btn.classList.remove('btn-warning');
        btn.classList.add('btn-outline-warning');
        iconElement.className = 'bi bi-bookmark';
        textElement.textContent = '收藏';
        countElement.textContent = currentCount - 1;
        
        animateButton(btn, 'unfavorite');
        showToast('已取消收藏', 'info');
    } else {
        // 收藏
        btn.classList.remove('btn-outline-warning');
        btn.classList.add('btn-warning');
        iconElement.className = 'bi bi-bookmark-fill';
        textElement.textContent = '已收藏';
        countElement.textContent = currentCount + 1;
        
        animateButton(btn, 'favorite');
        showToast('收藏成功！', 'success');
    }
}

// 处理分享操作
function handleShareAction() {
    const shareModal = createAdvancedShareModal();
    document.body.appendChild(shareModal);
    
    const modalInstance = new bootstrap.Modal(shareModal);
    modalInstance.show();
    
    shareModal.addEventListener('hidden.bs.modal', function() {
        shareModal.remove();
    });
}

// 创建高级分享模态框
function createAdvancedShareModal() {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">分享视频</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="share-options">
                        <div class="share-option" onclick="shareToWeChat()" data-platform="wechat">
                            <div class="share-icon bg-success">
                                <i class="bi bi-wechat text-white"></i>
                            </div>
                            <span>微信</span>
                        </div>
                        <div class="share-option" onclick="shareToWeibo()" data-platform="weibo">
                            <div class="share-icon bg-danger">
                                <i class="bi bi-share text-white"></i>
                            </div>
                            <span>微博</span>
                        </div>
                        <div class="share-option" onclick="shareToQQ()" data-platform="qq">
                            <div class="share-icon bg-info">
                                <i class="bi bi-chat-dots text-white"></i>
                            </div>
                            <span>QQ</span>
                        </div>
                        <div class="share-option" onclick="copyLink()" data-platform="link">
                            <div class="share-icon bg-secondary">
                                <i class="bi bi-clipboard text-white"></i>
                            </div>
                            <span>复制链接</span>
                        </div>
                    </div>
                    <div class="share-link mt-3">
                        <input type="text" class="form-control" value="${window.location.href}" readonly>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // 添加样式
    const style = document.createElement('style');
    style.textContent = `
        .share-options {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .share-option {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        .share-option:hover {
            background: var(--bg-light);
            transform: translateY(-2px);
            border-color: var(--border-color);
        }
        .share-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
    `;
    modal.appendChild(style);
    
    return modal;
}

// 处理下载操作
function handleDownloadAction() {
    showDownloadModal();
}

// 显示下载模态框
function showDownloadModal() {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-download me-2"></i>下载资源
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="download-list">
                        <div class="download-item">
                            <div class="download-info">
                                <i class="bi bi-file-zip text-warning me-2"></i>
                                <div>
                                    <h6>源代码包</h6>
                                    <small class="text-muted">包含完整示例代码 (2.5MB)</small>
                                </div>
                            </div>
                            <button class="btn btn-outline-primary btn-sm" onclick="downloadFile('source-code.zip')">
                                <i class="bi bi-download"></i>
                            </button>
                        </div>
                        <div class="download-item">
                            <div class="download-info">
                                <i class="bi bi-file-pdf text-danger me-2"></i>
                                <div>
                                    <h6>课程笔记</h6>
                                    <small class="text-muted">详细笔记整理 (1.2MB)</small>
                                </div>
                            </div>
                            <button class="btn btn-outline-primary btn-sm" onclick="downloadFile('notes.pdf')">
                                <i class="bi bi-download"></i>
                            </button>
                        </div>
                        <div class="download-item">
                            <div class="download-info">
                                <i class="bi bi-file-text text-info me-2"></i>
                                <div>
                                    <h6>练习题集</h6>
                                    <small class="text-muted">配套练习题目 (800KB)</small>
                                </div>
                            </div>
                            <button class="btn btn-outline-primary btn-sm" onclick="downloadFile('exercises.pdf')">
                                <i class="bi bi-download"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // 添加样式
    const style = document.createElement('style');
    style.textContent = `
        .download-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .download-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        .download-item:hover {
            background: var(--bg-light);
            transform: translateX(4px);
        }
        .download-info {
            display: flex;
            align-items: center;
            flex: 1;
        }
        .download-info h6 {
            margin: 0 0 0.25rem 0;
            font-weight: 600;
        }
    `;
    modal.appendChild(style);
    
    document.body.appendChild(modal);
    
    const modalInstance = new bootstrap.Modal(modal);
    modalInstance.show();
    
    modal.addEventListener('hidden.bs.modal', function() {
        modal.remove();
    });
}

// 初始化高级评论系统
function initAdvancedCommentSystem() {
    const commentTextarea = document.querySelector('.comment-textarea');
    const submitBtn = document.querySelector('.submit-comment');
    const loadMoreBtn = document.querySelector('.load-more');
    
    if (commentTextarea && submitBtn) {
        // 自动调整文本框高度
        commentTextarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
        
        // 发表评论
        submitBtn.addEventListener('click', function() {
            handleCommentSubmit();
        });
        
        // Ctrl+Enter 快速发表
        commentTextarea.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'Enter') {
                handleCommentSubmit();
            }
        });
    }
    
    // 加载更多评论
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            loadMoreComments();
        });
    }
    
    // 评论点赞
    document.addEventListener('click', function(e) {
        if (e.target.closest('.comment-like')) {
            handleCommentLike(e.target.closest('.comment-like'));
        }
    });
}

// 处理评论提交
function handleCommentSubmit() {
    const textarea = document.querySelector('.comment-textarea');
    const commentText = textarea.value.trim();
    
    if (commentText === '') {
        showToast('请输入评论内容', 'warning');
        textarea.focus();
        return;
    }
    
    // 创建新评论
    const newComment = createAdvancedCommentElement(commentText);
    const commentsList = document.querySelector('.comments-list');
    
    // 添加进入动画
    newComment.style.opacity = '0';
    newComment.style.transform = 'translateY(20px)';
    
    commentsList.insertBefore(newComment, commentsList.firstChild);
    
    // 触发动画
    setTimeout(() => {
        newComment.style.transition = 'all 0.5s ease';
        newComment.style.opacity = '1';
        newComment.style.transform = 'translateY(0)';
    }, 100);
    
    // 清空输入框
    textarea.value = '';
    textarea.style.height = 'auto';
    
    // 更新评论数量
    updateCommentCount(1);
    
    showToast('评论发表成功！', 'success');
}

// 创建高级评论元素
function createAdvancedCommentElement(commentText) {
    const commentDiv = document.createElement('div');
    commentDiv.className = 'comment-item';
    
    const currentTime = new Date().toLocaleString();
    
    commentDiv.innerHTML = `
        <img src="https://picsum.photos/40/40?random=newuser" alt="用户头像" class="comment-avatar">
        <div class="comment-body">
            <div class="comment-info">
                <span class="comment-author">我</span>
                <span class="comment-time">${currentTime}</span>
                <span class="badge bg-success ms-2">刚刚</span>
            </div>
            <div class="comment-content">
                ${commentText}
            </div>
            <div class="comment-footer">
                <button class="btn btn-outline-secondary btn-sm comment-action">
                    <i class="bi bi-reply"></i> 回复
                </button>
                <button class="btn btn-outline-primary btn-sm comment-action comment-like">
                    <i class="bi bi-heart"></i> 0
                </button>
            </div>
        </div>
    `;
    
    return commentDiv;
}

// 更新评论数量
function updateCommentCount(increment) {
    const commentCount = document.querySelector('.comment-count');
    if (commentCount) {
        const currentCount = parseInt(commentCount.textContent);
        commentCount.textContent = currentCount + increment;
    }
}

// 处理评论点赞
function handleCommentLike(btn) {
    const countSpan = btn.querySelector('i').nextSibling;
    const currentCount = parseInt(countSpan.textContent.trim());
    const isLiked = btn.classList.contains('btn-primary');
    
    if (isLiked) {
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-outline-primary');
        btn.querySelector('i').className = 'bi bi-heart';
        countSpan.textContent = ` ${currentCount - 1}`;
    } else {
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-primary');
        btn.querySelector('i').className = 'bi bi-heart-fill';
        countSpan.textContent = ` ${currentCount + 1}`;
        
        // 创建飞出的爱心效果
        createFloatingHeart(btn);
    }
    
    animateButton(btn, isLiked ? 'unlike' : 'like');
}

// 加载更多评论
function loadMoreComments() {
    const loadMoreBtn = document.querySelector('.load-more');
    const commentsList = document.querySelector('.comments-list');
    
    loadMoreBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>加载中...';
    loadMoreBtn.disabled = true;
    
    // 模拟加载延迟
    setTimeout(() => {
        // 创建模拟评论数据
        const mockComments = [
            {
                author: '王小华',
                time: '2024-01-13 10:15',
                content: '讲解得很详细，代码示例也很实用，学到了很多新知识！',
                likes: 2
            },
            {
                author: '赵同学',
                time: '2024-01-12 16:30',
                content: '这个教程对我帮助很大，特别是那个数组方法的部分，之前一直搞不懂。',
                likes: 1
            }
        ];
        
        mockComments.forEach(comment => {
            const commentElement = createMockCommentElement(comment);
            commentElement.style.opacity = '0';
            commentElement.style.transform = 'translateY(20px)';
            commentsList.appendChild(commentElement);
            
            setTimeout(() => {
                commentElement.style.transition = 'all 0.5s ease';
                commentElement.style.opacity = '1';
                commentElement.style.transform = 'translateY(0)';
            }, 100);
        });
        
        loadMoreBtn.innerHTML = '<i class="bi bi-arrow-down-circle me-1"></i>加载更多评论';
        loadMoreBtn.disabled = false;
        
        updateCommentCount(mockComments.length);
    }, 1500);
}

// 创建模拟评论元素
function createMockCommentElement(comment) {
    const div = document.createElement('div');
    div.className = 'comment-item';
    div.innerHTML = `
        <img src="https://picsum.photos/40/40?random=${Math.random()}" alt="用户头像" class="comment-avatar">
        <div class="comment-body">
            <div class="comment-info">
                <span class="comment-author">${comment.author}</span>
                <span class="comment-time">${comment.time}</span>
            </div>
            <div class="comment-content">
                ${comment.content}
            </div>
            <div class="comment-footer">
                <button class="btn btn-outline-secondary btn-sm comment-action">
                    <i class="bi bi-reply"></i> 回复
                </button>
                <button class="btn btn-outline-primary btn-sm comment-action comment-like">
                    <i class="bi bi-heart"></i> ${comment.likes}
                </button>
            </div>
        </div>
    `;
    return div;
}

// 初始化推荐系统
function initRecommendationSystem() {
    const recommendationCards = document.querySelectorAll('.recommendation-card');
    const relatedVideos = document.querySelectorAll('.related-video');
    
    // 推荐视频卡片点击
    recommendationCards.forEach(card => {
        card.addEventListener('click', function() {
            const title = this.querySelector('.rec-title').textContent;
            showToast(`正在跳转到: ${title}`, 'info');
            
            // 这里可以实现真实的跳转逻辑
            console.log('跳转到推荐视频:', title);
        });
    });
    
    // 相关视频点击
    relatedVideos.forEach(video => {
        video.addEventListener('click', function() {
            const title = this.querySelector('.related-title').textContent;
            showToast(`正在跳转到: ${title}`, 'info');
            
            console.log('跳转到相关视频:', title);
        });
    });
}

// 初始化动画效果
function initAnimations() {
    // 滚动时的元素动画
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    });
    
    // 观察需要动画的元素
    const animatedElements = document.querySelectorAll('.sidebar-card, .recommendation-card');
    animatedElements.forEach(el => observer.observe(el));
}

// 按钮动画效果
function animateButton(button, type) {
    button.style.transform = 'scale(1.1)';
    button.style.transition = 'transform 0.2s ease';
    
    setTimeout(() => {
        button.style.transform = 'scale(1)';
    }, 200);
    
    if (type === 'like' || type === 'favorite') {
        button.style.animation = 'bounce 0.6s ease';
        setTimeout(() => {
            button.style.animation = '';
        }, 600);
    }
}

// 创建飞出的爱心效果
function createFloatingHeart(button) {
    const heart = document.createElement('div');
    heart.innerHTML = '❤️';
    heart.style.cssText = `
        position: absolute;
        font-size: 1.5rem;
        pointer-events: none;
        z-index: 1000;
        animation: floatHeart 2s ease-out forwards;
    `;
    
    const rect = button.getBoundingClientRect();
    heart.style.left = (rect.left + rect.width / 2) + 'px';
    heart.style.top = rect.top + 'px';
    
    document.body.appendChild(heart);
    
    setTimeout(() => {
        heart.remove();
    }, 2000);
}

// 显示吐司消息
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show toast-message`;
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // 样式
    toast.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        z-index: 10000;
        min-width: 300px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border-radius: 0.5rem;
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 4000);
}

// 分享函数实现
function shareToWeChat() {
    showToast('微信分享功能开发中...', 'info');
}

function shareToWeibo() {
    showToast('微博分享功能开发中...', 'info');
}

function shareToQQ() {
    showToast('QQ分享功能开发中...', 'info');
}

function copyLink() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        showToast('链接已复制到剪贴板！', 'success');
    }).catch(() => {
        // 备用复制方法
        const textArea = document.createElement('textarea');
        textArea.value = url;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showToast('链接已复制到剪贴板！', 'success');
    });
}

// 下载文件
function downloadFile(filename) {
    showToast(`正在准备下载 ${filename}...`, 'info');
    
    // 模拟下载延迟
    setTimeout(() => {
        showToast(`${filename} 下载完成！`, 'success');
    }, 2000);
}

// 添加动画样式
const animationStyles = document.createElement('style');
animationStyles.textContent = `
    @keyframes floatHeart {
        0% {
            transform: translateY(0) scale(1);
            opacity: 1;
        }
        50% {
            transform: translateY(-30px) scale(1.2);
            opacity: 1;
        }
        100% {
            transform: translateY(-60px) scale(0.8);
            opacity: 0;
        }
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-10px);
        }
        60% {
            transform: translateY(-5px);
        }
    }
`;
document.head.appendChild(animationStyles);