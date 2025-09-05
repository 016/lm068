// f-video-detail_2.js - 视频详情页面专用脚本

document.addEventListener('DOMContentLoaded', function() {
    // 初始化视频播放功能
    initVideoPlayer();
    
    // 初始化交互按钮
    initInteractionButtons();
    
    // 初始化评论功能
    initCommentSystem();
    
    // 初始化推荐视频点击
    initRecommendedVideos();
});

// 初始化视频播放功能
function initVideoPlayer() {
    const playBtn = document.querySelector('.play-btn');
    const videoContainer = document.querySelector('.video-player-container');
    
    if (playBtn && videoContainer) {
        playBtn.addEventListener('click', function() {
            // 这里可以集成真实的视频播放器
            // 例如：YouTube、Bilibili 或其他视频播放器
            alert('视频播放功能待集成第三方播放器');
            
            // 示例：创建简单的播放状态
            playBtn.innerHTML = '<i class="bi bi-pause-fill"></i>';
            playBtn.classList.add('playing');
        });
    }
}

// 初始化交互按钮
function initInteractionButtons() {
    const likeBtn = document.querySelector('.interaction-buttons .btn-outline-primary');
    const favoriteBtn = document.querySelector('.interaction-buttons .btn-outline-warning');
    const shareBtn = document.querySelector('.interaction-buttons .btn-outline-info');
    
    // 点赞功能
    if (likeBtn) {
        likeBtn.addEventListener('click', function() {
            const badge = this.querySelector('.badge');
            const currentCount = parseInt(badge.textContent);
            const isLiked = this.classList.contains('btn-primary');
            
            if (isLiked) {
                // 取消点赞
                this.classList.remove('btn-primary');
                this.classList.add('btn-outline-primary');
                badge.textContent = currentCount - 1;
                this.querySelector('i').className = 'bi bi-hand-thumbs-up me-1';
            } else {
                // 点赞
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
                badge.textContent = currentCount + 1;
                this.querySelector('i').className = 'bi bi-hand-thumbs-up-fill me-1';
            }
        });
    }
    
    // 收藏功能
    if (favoriteBtn) {
        favoriteBtn.addEventListener('click', function() {
            const badge = this.querySelector('.badge');
            const currentCount = parseInt(badge.textContent);
            const isFavorited = this.classList.contains('btn-warning');
            
            if (isFavorited) {
                // 取消收藏
                this.classList.remove('btn-warning');
                this.classList.add('btn-outline-warning');
                badge.textContent = currentCount - 1;
                this.querySelector('i').className = 'bi bi-star me-1';
            } else {
                // 收藏
                this.classList.remove('btn-outline-warning');
                this.classList.add('btn-warning');
                badge.textContent = currentCount + 1;
                this.querySelector('i').className = 'bi bi-star-fill me-1';
            }
        });
    }
    
    // 分享功能
    if (shareBtn) {
        shareBtn.addEventListener('click', function() {
            // 创建分享选项
            const shareModal = createShareModal();
            document.body.appendChild(shareModal);
            
            // 显示模态框
            const modal = new bootstrap.Modal(shareModal);
            modal.show();
            
            // 模态框关闭后移除元素
            shareModal.addEventListener('hidden.bs.modal', function() {
                shareModal.remove();
            });
        });
    }
}

// 创建分享模态框
function createShareModal() {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">分享视频</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary btn-sm" onclick="shareToWeChat()">
                            <i class="bi bi-wechat me-2"></i>微信
                        </button>
                        <button class="btn btn-info btn-sm" onclick="shareToWeibo()">
                            <i class="bi bi-share me-2"></i>微博
                        </button>
                        <button class="btn btn-success btn-sm" onclick="copyLink()">
                            <i class="bi bi-clipboard me-2"></i>复制链接
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    return modal;
}

// 分享功能实现
function shareToWeChat() {
    alert('微信分享功能待开发');
}

function shareToWeibo() {
    alert('微博分享功能待开发');
}

function copyLink() {
    const currentUrl = window.location.href;
    navigator.clipboard.writeText(currentUrl).then(function() {
        alert('链接已复制到剪贴板');
    }).catch(function() {
        alert('复制失败，请手动复制链接');
    });
}

// 初始化评论系统
function initCommentSystem() {
    const commentForm = document.querySelector('.comment-form');
    const commentTextarea = commentForm?.querySelector('textarea');
    const submitBtn = commentForm?.querySelector('.btn-primary');
    
    if (commentForm && commentTextarea && submitBtn) {
        // 发表评论
        submitBtn.addEventListener('click', function() {
            const commentText = commentTextarea.value.trim();
            
            if (commentText === '') {
                alert('请输入评论内容');
                return;
            }
            
            // 创建新评论
            const newComment = createCommentElement(commentText);
            const commentsList = document.querySelector('.comments-list');
            
            // 插入到评论列表开头
            commentsList.insertBefore(newComment, commentsList.firstChild);
            
            // 清空输入框
            commentTextarea.value = '';
            
            // 更新评论数量
            updateCommentCount(1);
            
            // 显示成功消息
            showMessage('评论发表成功！', 'success');
        });
        
        // 回复按钮点击事件
        document.addEventListener('click', function(e) {
            if (e.target.closest('.comment-actions .btn-outline-secondary')) {
                const replyBtn = e.target.closest('.comment-actions .btn-outline-secondary');
                const commentItem = replyBtn.closest('.comment-item');
                const username = commentItem.querySelector('.comment-header strong').textContent;
                
                // 在评论框中添加回复信息
                commentTextarea.focus();
                commentTextarea.value = `@${username} `;
            }
        });
        
        // 点赞评论
        document.addEventListener('click', function(e) {
            if (e.target.closest('.comment-actions .btn-outline-primary')) {
                const likeBtn = e.target.closest('.comment-actions .btn-outline-primary');
                const currentCount = parseInt(likeBtn.textContent.trim());
                const isLiked = likeBtn.classList.contains('btn-primary');
                
                if (isLiked) {
                    likeBtn.classList.remove('btn-primary');
                    likeBtn.classList.add('btn-outline-primary');
                    likeBtn.innerHTML = `<i class="bi bi-hand-thumbs-up me-1"></i>${currentCount - 1}`;
                } else {
                    likeBtn.classList.remove('btn-outline-primary');
                    likeBtn.classList.add('btn-primary');
                    likeBtn.innerHTML = `<i class="bi bi-hand-thumbs-up-fill me-1"></i>${currentCount + 1}`;
                }
            }
        });
    }
}

// 创建评论元素
function createCommentElement(commentText) {
    const commentDiv = document.createElement('div');
    commentDiv.className = 'comment-item';
    
    const currentTime = new Date().toISOString().split('T')[0];
    
    commentDiv.innerHTML = `
        <div class="comment-avatar">
            <img src="https://picsum.photos/40/40?random=newuser" alt="用户头像">
        </div>
        <div class="comment-content">
            <div class="comment-header">
                <strong>我</strong>
                <small class="text-muted ms-2">${currentTime}</small>
            </div>
            <div class="comment-text">
                ${commentText}
            </div>
            <div class="comment-actions">
                <button class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-reply me-1"></i>回复
                </button>
                <button class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-hand-thumbs-up me-1"></i>0
                </button>
            </div>
        </div>
    `;
    
    return commentDiv;
}

// 更新评论数量
function updateCommentCount(increment) {
    const commentBadge = document.querySelector('.card-header .badge');
    if (commentBadge) {
        const currentCount = parseInt(commentBadge.textContent);
        commentBadge.textContent = currentCount + increment;
    }
}

// 显示消息提示
function showMessage(message, type = 'info') {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // 插入到页面顶部
    const container = document.querySelector('.container');
    container.insertBefore(alert, container.firstChild);
    
    // 3秒后自动消失
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 3000);
}

// 初始化推荐视频点击
function initRecommendedVideos() {
    const recommendedItems = document.querySelectorAll('.recommended-item, .related-video-item');
    
    recommendedItems.forEach(item => {
        item.addEventListener('click', function() {
            // 这里可以跳转到对应的视频详情页
            alert('跳转到视频详情页功能待开发');
            
            // 示例：可以获取视频信息进行跳转
            const title = this.querySelector('.recommended-title, .related-title')?.textContent;
            console.log('点击了视频：', title);
        });
    });
}

// 滚动时更新进度条（可选功能）
function initScrollProgress() {
    window.addEventListener('scroll', function() {
        const scrolled = (window.pageYOffset / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
        
        // 这里可以添加阅读进度条的逻辑
        console.log('阅读进度：', Math.round(scrolled) + '%');
    });
}