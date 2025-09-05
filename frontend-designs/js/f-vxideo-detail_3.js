// 视频详情页JavaScript - f-video-detail_3.js (紧凑卡片版)

document.addEventListener('DOMContentLoaded', function() {
    // 视频播放按钮
    const playBtn = document.querySelector('.video-play-btn');
    if (playBtn) {
        playBtn.addEventListener('click', function() {
            // 模拟视频播放
            const icon = this.querySelector('i');
            const text = this.querySelector('i + text') || this.lastChild;
            
            if (icon.classList.contains('bi-play-fill')) {
                icon.className = 'bi bi-pause-fill me-2';
                this.innerHTML = '<i class="bi bi-pause-fill me-2"></i>暂停视频';
                console.log('开始播放视频');
            } else {
                icon.className = 'bi bi-play-fill me-2';
                this.innerHTML = '<i class="bi bi-play-fill me-2"></i>播放视频';
                console.log('暂停播放视频');
            }
        });
    }

    // 点赞功能
    const likeBtn = document.getElementById('likeBtn');
    if (likeBtn) {
        let isLiked = false;
        likeBtn.addEventListener('click', function() {
            const icon = this.querySelector('i');
            const countSpan = this.querySelector('.like-count');
            let count = parseInt(countSpan.textContent);
            
            if (!isLiked) {
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
                icon.className = 'bi bi-hand-thumbs-up-fill';
                countSpan.textContent = count + 1;
                isLiked = true;
            } else {
                this.classList.remove('btn-primary');
                this.classList.add('btn-outline-primary');
                icon.className = 'bi bi-hand-thumbs-up';
                countSpan.textContent = count - 1;
                isLiked = false;
            }
        });
    }

    // 收藏功能
    const favoriteBtn = document.getElementById('favoriteBtn');
    if (favoriteBtn) {
        let isFavorited = false;
        favoriteBtn.addEventListener('click', function() {
            const icon = this.querySelector('i');
            const countSpan = this.querySelector('.favorite-count');
            let count = parseInt(countSpan.textContent);
            
            if (!isFavorited) {
                this.classList.remove('btn-outline-warning');
                this.classList.add('btn-warning');
                icon.className = 'bi bi-star-fill';
                countSpan.textContent = count + 1;
                isFavorited = true;
            } else {
                this.classList.remove('btn-warning');
                this.classList.add('btn-outline-warning');
                icon.className = 'bi bi-star';
                countSpan.textContent = count - 1;
                isFavorited = false;
            }
        });
    }

    // 复制链接功能
    const copyLinkBtn = document.getElementById('copyLink');
    if (copyLinkBtn) {
        copyLinkBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (navigator.clipboard) {
                navigator.clipboard.writeText(window.location.href).then(() => {
                    // 临时显示复制成功提示
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="bi bi-check me-2"></i>已复制';
                    this.classList.add('text-success');
                    
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.classList.remove('text-success');
                    }, 2000);
                });
            } else {
                alert('链接：' + window.location.href);
            }
        });
    }

    // 内容折叠/展开功能
    const toggleBtn = document.querySelector('.toggle-content');
    const contentArea = document.querySelector('.content-expandable');
    if (toggleBtn && contentArea) {
        toggleBtn.addEventListener('click', function() {
            const isExpanded = this.dataset.expanded === 'true';
            const icon = this.querySelector('i');
            
            if (isExpanded) {
                contentArea.style.maxHeight = '100px';
                contentArea.style.overflow = 'hidden';
                this.dataset.expanded = 'false';
                icon.className = 'bi bi-chevron-down';
            } else {
                contentArea.style.maxHeight = 'none';
                contentArea.style.overflow = 'visible';
                this.dataset.expanded = 'true';
                icon.className = 'bi bi-chevron-up';
            }
        });
    }

    // 评论字数统计
    const commentInput = document.querySelector('.comment-input');
    const charCount = document.querySelector('.char-count');
    if (commentInput && charCount) {
        commentInput.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = `${length}/500`;
            
            if (length > 450) {
                charCount.classList.add('text-warning');
            } else {
                charCount.classList.remove('text-warning');
            }
            
            if (length >= 500) {
                charCount.classList.add('text-danger');
                charCount.classList.remove('text-warning');
            } else {
                charCount.classList.remove('text-danger');
            }
        });
    }

    // 评论提交
    const submitCommentBtn = document.querySelector('.submit-comment');
    if (submitCommentBtn && commentInput) {
        submitCommentBtn.addEventListener('click', function() {
            const content = commentInput.value.trim();
            if (content) {
                // 模拟评论提交
                this.innerHTML = '<i class="bi bi-check"></i> 已发表';
                this.classList.add('btn-success');
                this.disabled = true;
                
                setTimeout(() => {
                    this.innerHTML = '发表';
                    this.classList.remove('btn-success');
                    this.classList.add('btn-primary');
                    this.disabled = false;
                    commentInput.value = '';
                    charCount.textContent = '0/500';
                }, 2000);
                
                console.log('评论已提交:', content);
            } else {
                // 输入框获得焦点并添加提示样式
                commentInput.focus();
                commentInput.classList.add('border-danger');
                setTimeout(() => {
                    commentInput.classList.remove('border-danger');
                }, 2000);
            }
        });
    }

    // 评论点赞
    const commentLikeBtns = document.querySelectorAll('.comment-like');
    commentLikeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const icon = this.querySelector('i');
            const countSpan = this.querySelector('span');
            let count = parseInt(countSpan.textContent);
            
            if (this.classList.contains('btn-outline-primary')) {
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
                icon.className = 'bi bi-hand-thumbs-up-fill me-1';
                countSpan.textContent = count + 1;
            } else {
                this.classList.remove('btn-primary');
                this.classList.add('btn-outline-primary');
                icon.className = 'bi bi-hand-thumbs-up me-1';
                countSpan.textContent = count - 1;
            }
        });
    });

    // 加载更多评论
    const loadMoreBtn = document.querySelector('.load-more-comments');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            this.innerHTML = '<i class="bi bi-arrow-repeat me-2"></i>加载中...';
            this.disabled = true;
            
            // 模拟加载
            setTimeout(() => {
                this.innerHTML = '<i class="bi bi-arrow-down me-2"></i>加载更多评论';
                this.disabled = false;
                console.log('加载了更多评论');
            }, 1500);
        });
    }

    // 评论排序
    const sortSelect = document.querySelector('.comment-sort select');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const sortType = this.value;
            console.log('评论排序方式:', sortType);
            // 这里可以添加实际的排序逻辑
        });
    }

    // 视频封面悬停效果
    const videoCover = document.querySelector('.video-cover');
    if (videoCover) {
        videoCover.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });
        
        videoCover.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    }

    // 侧边栏相关视频点击统计
    const sidebarVideos = document.querySelectorAll('.related-video-card a, .recommended-item a');
    sidebarVideos.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const title = this.textContent.trim();
            console.log('用户点击了侧边栏视频:', title);
        });
    });

    // 资源下载按钮
    const resourceBtns = document.querySelectorAll('.resource-links .btn');
    resourceBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const resourceType = this.textContent.trim();
            console.log('用户下载资源:', resourceType);
            
            // 临时改变按钮状态
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="bi bi-check me-1"></i>下载中...';
            this.disabled = true;
            
            setTimeout(() => {
                this.innerHTML = originalText;
                this.disabled = false;
            }, 1000);
        });
    });

    // 标签和徽章点击处理
    const badges = document.querySelectorAll('.video-taxonomy .badge');
    badges.forEach(badge => {
        badge.addEventListener('click', function() {
            const tagName = this.textContent.trim();
            console.log('用户点击了标签:', tagName);
        });
    });

    // 页面滚动时的视觉效果
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const rate = scrolled * -0.5;
        
        // 视频封面视差效果
        const videoHero = document.querySelector('.video-hero');
        if (videoHero) {
            videoHero.style.transform = `translateY(${rate}px)`;
        }
    });

    console.log('视频详情页（紧凑版）已加载完成');
});