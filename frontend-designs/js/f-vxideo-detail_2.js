// 视频详情页JavaScript - f-video-detail_2.js

document.addEventListener('DOMContentLoaded', function() {
    // 点赞功能
    const likeBtn = document.querySelector('.btn-outline-primary');
    if (likeBtn && likeBtn.textContent.includes('点赞')) {
        likeBtn.addEventListener('click', function() {
            const badge = this.querySelector('.badge');
            let count = parseInt(badge.textContent);
            
            if (this.classList.contains('btn-outline-primary')) {
                // 点赞
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
                count++;
            } else {
                // 取消点赞
                this.classList.remove('btn-primary');
                this.classList.add('btn-outline-primary');
                count--;
            }
            
            badge.textContent = count;
        });
    }

    // 收藏功能
    const favoriteBtn = document.querySelector('.btn-outline-warning');
    if (favoriteBtn && favoriteBtn.textContent.includes('收藏')) {
        favoriteBtn.addEventListener('click', function() {
            const badge = this.querySelector('.badge');
            let count = parseInt(badge.textContent);
            
            if (this.classList.contains('btn-outline-warning')) {
                // 收藏
                this.classList.remove('btn-outline-warning');
                this.classList.add('btn-warning');
                count++;
            } else {
                // 取消收藏
                this.classList.remove('btn-warning');
                this.classList.add('btn-outline-warning');
                count--;
            }
            
            badge.textContent = count;
        });
    }

    // 评论点赞功能
    const commentLikeBtns = document.querySelectorAll('.comment-actions .btn-outline-primary');
    commentLikeBtns.forEach(btn => {
        if (btn.textContent.includes('点赞')) {
            btn.addEventListener('click', function() {
                const badge = this.querySelector('.badge');
                let count = parseInt(badge.textContent);
                
                if (this.classList.contains('btn-outline-primary')) {
                    this.classList.remove('btn-outline-primary');
                    this.classList.add('btn-primary');
                    count++;
                } else {
                    this.classList.remove('btn-primary');
                    this.classList.add('btn-outline-primary');
                    count--;
                }
                
                badge.textContent = count;
            });
        }
    });

    // 评论表单提交
    const commentForm = document.querySelector('.comment-form textarea');
    const submitBtn = document.querySelector('.comment-form .btn-primary');
    
    if (commentForm && submitBtn) {
        submitBtn.addEventListener('click', function() {
            const content = commentForm.value.trim();
            if (content) {
                // 模拟评论提交
                alert('评论已提交！内容：' + content);
                commentForm.value = '';
            } else {
                alert('请输入评论内容！');
            }
        });
    }

    // 分享功能
    const shareDropdown = document.querySelector('[data-bs-toggle="dropdown"]');
    if (shareDropdown && shareDropdown.textContent.includes('分享')) {
        const shareItems = shareDropdown.parentNode.querySelectorAll('.dropdown-item');
        shareItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const platform = this.textContent.trim();
                
                if (platform.includes('复制链接')) {
                    // 复制当前页面链接
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(window.location.href).then(() => {
                            alert('链接已复制到剪贴板！');
                        });
                    } else {
                        alert('链接：' + window.location.href);
                    }
                } else {
                    alert('分享到' + platform);
                }
            });
        });
    }

    // 视频封面悬停效果增强
    const videoCover = document.querySelector('.video-cover-img');
    if (videoCover) {
        videoCover.addEventListener('mouseenter', function() {
            this.style.cursor = 'pointer';
        });
        
        videoCover.addEventListener('click', function() {
            alert('视频播放功能待实现');
        });
    }

    // 关联视频点击统计
    const relatedVideos = document.querySelectorAll('.related-video-item, .related-videos-section .card');
    relatedVideos.forEach(item => {
        item.addEventListener('click', function(e) {
            if (e.target.tagName === 'A') {
                e.preventDefault();
                console.log('用户点击了关联视频:', this.textContent.trim());
                // 这里可以添加实际的页面跳转逻辑
            }
        });
    });

    // 标签和合集点击处理
    const tags = document.querySelectorAll('.video-tags-collections .badge');
    tags.forEach(tag => {
        tag.addEventListener('click', function(e) {
            e.preventDefault();
            const tagName = this.textContent.trim();
            console.log('用户点击了标签/合集:', tagName);
            // 这里可以添加标签筛选跳转逻辑
        });
    });

    // 平滑滚动到评论区
    function scrollToComments() {
        const commentsSection = document.querySelector('.comments-section');
        if (commentsSection) {
            commentsSection.scrollIntoView({ behavior: 'smooth' });
        }
    }

    // 可以通过URL参数直接跳转到评论区
    if (window.location.hash === '#comments') {
        setTimeout(scrollToComments, 500);
    }

    // 页面加载完成后的初始化操作
    console.log('视频详情页已加载完成');
});