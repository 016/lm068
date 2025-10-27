// f-video-detail_5.js - 视频详情页面专用脚本

document.addEventListener('DOMContentLoaded', function() {
    // 初始化Markdown渲染
    initMarkdownRendering();
    
    // 初始化交互按钮
    initInteractionButtons();
    
    // 初始化评论功能
    // initCommentSystem();
    
    // 初始化推荐视频点击
    // initRecommendedVideos();
    
});

// 初始化Markdown渲染功能
function initMarkdownRendering() {
    //support text
    const markdownContent_support = document.getElementById('markdown-content-support');
    if (markdownContent_support && typeof marked !== 'undefined') {

        const CDN_DOMAIN = window.inputData.CND_URL;

        marked.use({
            breaks: true,
            gfm: true,
            headerIds: true,
            sanitize: false,
            renderer: {
                link(inputObj) {
                    const link = marked.Renderer.prototype.link.call(this, inputObj);
                    return link.replace('<a', '<a target="_blank" rel="noopener noreferrer"');
                },
                image(inputObj) {
                    // 类型检查和转换
                    href = inputObj.href;
                    title = inputObj.title;
                    text = inputObj.text;

                    // 如果是相对路径，添加域名前缀
                    if (href && !href.startsWith('http://') && !href.startsWith('https://')) {
                        href = CDN_DOMAIN + href;
                    }

                    // 构建 img 标签
                    let out = `<img src="${href}" alt="${text}"`;
                    if (title) {
                        out += ` title="${title}"`;
                    }
                    out += '>';
                    return out;
                }
            }
        });
        
        // 获取原始Markdown内容
        const markdownText = markdownContent_support.textContent || markdownContent_support.innerText;
        // console.log(markdownText);
        
        // 渲染为HTML
        try {
            const htmlContent = marked.parse(markdownText);
            // console.log(htmlContent)
            markdownContent_support.innerHTML = htmlContent;
            markdownContent_support.classList.add('markdown-rendered');
        } catch (error) {
            console.error('Markdown渲染失败:', error);
            // 保持原始文本格式
            markdownContent_support.style.whiteSpace = 'pre-wrap';
        }
    }

    // summary text
    const markdownContent_summary = document.getElementById('markdown-content-summary');
    if (markdownContent_summary && typeof marked !== 'undefined') {

        const CDN_DOMAIN = window.inputData.CND_URL;

        marked.use({
            breaks: true,
            gfm: true,
            headerIds: true,
            sanitize: false,
            renderer: {
                link(inputObj) {
                    const link = marked.Renderer.prototype.link.call(this, inputObj);
                    return link.replace('<a', '<a target="_blank" rel="noopener noreferrer"');
                },
                image(inputObj) {
                    // 类型检查和转换
                    href = inputObj.href;
                    title = inputObj.title;
                    text = inputObj.text;

                    // 如果是相对路径，添加域名前缀
                    if (href && !href.startsWith('http://') && !href.startsWith('https://')) {
                        href = CDN_DOMAIN + href;
                    }

                    // 构建 img 标签
                    let out = `<img src="${href}" alt="${text}"`;
                    if (title) {
                        out += ` title="${title}"`;
                    }
                    out += '>';
                    return out;
                }
            }
        });

        // 获取原始Markdown内容
        const markdownText = markdownContent_summary.textContent || markdownContent_summary.innerText;
        // console.log(markdownText);

        // 渲染为HTML
        try {
            const htmlContent = marked.parse(markdownText);
            // console.log(htmlContent)
            markdownContent_summary.innerHTML = htmlContent;
            markdownContent_summary.classList.add('markdown-rendered');
        } catch (error) {
            console.error('Markdown渲染失败:', error);
            // 保持原始文本格式
            markdownContent_summary.style.whiteSpace = 'pre-wrap';
        }
    }




}

// 初始化交互按钮
function initInteractionButtons() {
    const likeBtn = document.querySelector('.interaction-buttons-inline .btn-outline-primary');
    const favoriteBtn = document.querySelector('.interaction-buttons-inline .btn-outline-warning');
    const commentBtn = document.querySelector('.interaction-buttons-inline .btn-outline-info');
    
    // 点赞功能
    if (likeBtn) {
        likeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const badge = this.querySelector('.badge');
            const currentCount = parseInt(badge.textContent);
            const isLiked = this.classList.contains('btn-primary');
            
            if (isLiked) {
                // 取消点赞
                this.classList.remove('btn-primary');
                this.classList.add('btn-outline-primary');
                badge.textContent = currentCount - 1;
                this.querySelector('i').className = 'bi bi-hand-thumbs-up me-1';
                showToast('取消点赞', 'secondary');
            } else {
                // 点赞
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
                badge.textContent = currentCount + 1;
                this.querySelector('i').className = 'bi bi-hand-thumbs-up-fill me-1';
                showToast('点赞成功！', 'success');
                
                // 点赞动画效果
                this.style.transform = 'scale(1.2)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 200);
            }
        });
    }
    
    // 收藏功能
    if (favoriteBtn) {
        favoriteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const badge = this.querySelector('.badge');
            const currentCount = parseInt(badge.textContent);
            const isFavorited = this.classList.contains('btn-warning');
            
            if (isFavorited) {
                // 取消收藏
                this.classList.remove('btn-warning');
                this.classList.add('btn-outline-warning');
                badge.textContent = currentCount - 1;
                this.querySelector('i').className = 'bi bi-star me-1';
                showToast('取消收藏', 'secondary');
            } else {
                // 收藏
                this.classList.remove('btn-outline-warning');
                this.classList.add('btn-warning');
                badge.textContent = currentCount + 1;
                this.querySelector('i').className = 'bi bi-star-fill me-1';
                showToast('收藏成功！', 'warning');
                
                // 收藏动画效果
                this.style.transform = 'rotate(360deg) scale(1.1)';
                setTimeout(() => {
                    this.style.transform = 'rotate(0deg) scale(1)';
                }, 300);
            }
        });
    }
    
    // 评论跳转
    if (commentBtn) {
        commentBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const commentsSection = document.querySelector('.comments-list');
            if (commentsSection) {
                commentsSection.scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
                
                // 高亮评论区域
                const commentCard = commentsSection.closest('.card');
                if (commentCard) {
                    commentCard.style.boxShadow = '0 0 20px rgba(var(--brand-color-rgb), 0.3)';
                    setTimeout(() => {
                        commentCard.style.boxShadow = '';
                    }, 2000);
                }
            }
        });
    }
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
                showToast('请输入评论内容', 'warning');
                commentTextarea.focus();
                return;
            }
            
            if (commentText.length < 5) {
                showToast('评论内容至少需要5个字符', 'warning');
                return;
            }
            
            // 显示提交中状态
            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<i class="bi bi-hourglass-split"></i><span class="d-none d-sm-inline ms-1">提交中...</span>';
            
            // 模拟提交延迟
            setTimeout(() => {
                // 创建新评论
                const newComment = createCommentElement(commentText);
                const commentsList = document.querySelector('.comments-list');
                
                // 插入到评论列表开头
                commentsList.insertBefore(newComment, commentsList.firstChild);
                
                // 清空输入框
                commentTextarea.value = '';
                
                // 更新评论数量
                updateCommentCount(1);
                
                // 恢复按钮状态
                this.disabled = false;
                this.innerHTML = originalText;
                
                // 显示成功消息
                showToast('评论发表成功！', 'success');
                
                // 滚动到新评论
                newComment.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                
                // 高亮新评论
                newComment.style.backgroundColor = 'rgba(var(--brand-color-rgb), 0.1)';
                setTimeout(() => {
                    newComment.style.backgroundColor = '';
                }, 3000);
            }, 1000);
        });
        
        // 自动调整文本框高度
        commentTextarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 200) + 'px';
        });
        
        // 回复按钮点击事件
        document.addEventListener('click', function(e) {
            if (e.target.closest('.comment-actions .btn-outline-secondary')) {
                e.preventDefault();
                const replyBtn = e.target.closest('.comment-actions .btn-outline-secondary');
                const commentItem = replyBtn.closest('.comment-item');
                const username = commentItem.querySelector('.comment-header strong').textContent;
                
                // 在评论框中添加回复信息
                commentTextarea.focus();
                const currentValue = commentTextarea.value;
                const replyText = `@${username} `;
                
                if (!currentValue.includes(replyText)) {
                    commentTextarea.value = replyText + currentValue;
                }
                
                // 触发高度调整
                commentTextarea.dispatchEvent(new Event('input'));
                
                // 滚动到评论框
                commentForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
        
        // 点赞评论
        document.addEventListener('click', function(e) {
            if (e.target.closest('.comment-actions .btn-outline-primary')) {
                e.preventDefault();
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
                    
                    // 点赞动画
                    likeBtn.style.transform = 'scale(1.2)';
                    setTimeout(() => {
                        likeBtn.style.transform = 'scale(1)';
                    }, 200);
                }
            }
        });
    }
}

// 创建评论元素
function createCommentElement(commentText) {
    const commentDiv = document.createElement('div');
    commentDiv.className = 'comment-item';
    
    const currentTime = new Date().toLocaleDateString('zh-CN');
    
    // 处理@回复
    const processedText = commentText.replace(/@(\w+)/g, '<span class="text-primary fw-semibold">@$1</span>');
    
    commentDiv.innerHTML = `
        <div class="comment-avatar">
            <img src="https://picsum.photos/40/40?random=newuser${Date.now()}" alt="用户头像">
        </div>
        <div class="comment-content">
            <div class="comment-header">
                <strong>我</strong>
                <small class="text-muted ms-2">${currentTime}</small>
                <span class="badge bg-success ms-2">新评论</span>
            </div>
            <div class="comment-text">
                ${processedText}
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
    
    // 同时更新交互按钮中的评论数
    const commentBtn = document.querySelector('.interaction-buttons-inline .btn-outline-info .badge');
    if (commentBtn) {
        const currentCount = parseInt(commentBtn.textContent);
        commentBtn.textContent = currentCount + increment;
    }
}

// 初始化推荐视频点击
function initRecommendedVideos() {
    const recommendedItems = document.querySelectorAll('.recommended-item, .related-video-item');
    
    recommendedItems.forEach(item => {
        item.addEventListener('click', function(e) {
            // 如果点击的是按钮或链接，不执行跳转
            if (e.target.closest('.btn') || e.target.closest('a')) return;
            
            const title = this.querySelector('.recommended-title, .related-title')?.textContent;
            
            // 显示加载状态
            const originalContent = this.innerHTML;
            this.style.opacity = '0.7';
            this.style.pointerEvents = 'none';
            
            showToast(`正在跳转到: ${title}`, 'info');
            
            // 模拟跳转延迟
            setTimeout(() => {
                // 这里可以实现真实的页面跳转
                console.log('跳转到视频：', title);
                
                // 恢复状态
                this.style.opacity = '1';
                this.style.pointerEvents = 'auto';
                
                // 实际项目中可以使用以下代码跳转
                // window.location.href = `/video-detail?id=${videoId}`;
            }, 1000);
        });
        
        // 悬停效果增强
        item.addEventListener('mouseenter', function() {
            const thumbnail = this.querySelector('.recommended-thumbnail, .related-thumbnail, .video-thumbnail');
            if (thumbnail) {
                thumbnail.style.filter = 'brightness(1.1)';
            }
        });
        
        item.addEventListener('mouseleave', function() {
            const thumbnail = this.querySelector('.recommended-thumbnail, .related-thumbnail, .video-thumbnail');
            if (thumbnail) {
                thumbnail.style.filter = 'brightness(1)';
            }
        });
    });
    
    // 标签按钮点击事件 - 现在支持链接
    document.addEventListener('click', function(e) {
        if (e.target.closest('.tags-section .btn, .recommended-tags .btn, .related-tags .badge')) {
            // 如果是链接，添加视觉反馈但允许默认行为
            const tagElement = e.target.closest('.btn, .badge');
            const tagText = tagElement.textContent.trim();
            
            if (tagElement.tagName.toLowerCase() === 'a') {
                // 添加点击效果
                tagElement.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    tagElement.style.transform = 'scale(1)';
                }, 150);
                
                showToast(`正在搜索标签: ${tagText}`, 'info');
                return; // 让链接正常工作
            }
            
            // 原有的按钮逻辑
            e.preventDefault();
            e.stopPropagation();
            showToast(`正在搜索标签: ${tagText}`, 'info');
            console.log('搜索标签：', tagText);
        }
    });
}

// 显示Toast消息
function showToast(message, type = 'info') {
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
    toast.className = `toast align-items-center text-bg-${type} border-0`;
    toast.id = toastId;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    // 显示toast
    const bsToast = new bootstrap.Toast(toast, {
        delay: 3000
    });
    bsToast.show();
    
    // 自动移除
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}

// 公告点击事件 - 现在支持链接
document.addEventListener('click', function(e) {
    if (e.target.closest('.announcement-title a, .announcement-desc a')) {
        const announcement = e.target.closest('a');
        const title = announcement.textContent.trim();
        
        // 添加点击效果
        announcement.style.transform = 'scale(0.98)';
        setTimeout(() => {
            announcement.style.transform = 'scale(1)';
        }, 150);
        
        showToast(`正在打开公告: ${title}`, 'info');
        // 链接会自然跳转，无需阻止默认行为
    }
});

// 平台按钮点击事件
document.addEventListener('click', function(e) {
    if (e.target.closest('.platform-buttons .btn')) {
        const platform = e.target.closest('.btn');
        const platformName = platform.textContent.trim();
        
        // 添加点击动画
        platform.style.transform = 'scale(0.95)';
        setTimeout(() => {
            platform.style.transform = 'scale(1)';
        }, 150);
        
        showToast(`正在跳转到${platformName}平台`, 'success');
        
        // 这里可以实现平台跳转
        console.log('跳转到平台：', platformName);
    }
});

// 关联视频链接点击事件
document.addEventListener('click', function(e) {
    if (e.target.closest('.video-thumbnail-container a, .card-title a')) {
        const link = e.target.closest('a');
        const title = link.textContent.trim() || '视频';
        
        // 添加点击效果
        link.style.transform = 'scale(0.98)';
        setTimeout(() => {
            link.style.transform = 'scale(1)';
        }, 150);
        
        showToast(`正在跳转到: ${title}`, 'info');
        // 链接会自然跳转，无需阻止默认行为
    }
});

// 键盘快捷键支持
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + Enter 快速发表评论
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        const submitBtn = document.querySelector('.comment-form .btn-primary');
        if (submitBtn && !submitBtn.disabled) {
            submitBtn.click();
        }
    }
    
    // Escape 清空评论框
    if (e.key === 'Escape') {
        const textarea = document.querySelector('.comment-form textarea');
        if (textarea && textarea === document.activeElement) {
            textarea.value = '';
            textarea.style.height = 'auto';
            textarea.blur();
        }
    }
});

// 滚动时更新阅读进度（可选功能）
function initScrollProgress() {
    let ticking = false;
    
    window.addEventListener('scroll', function() {
        if (!ticking) {
            requestAnimationFrame(function() {
                const scrolled = (window.pageYOffset / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
                
                // 更新页面标题显示进度
                if (scrolled > 10 && scrolled < 90) {
                    document.title = `(${Math.round(scrolled)}%) 视频详情 - 视频创作展示网站`;
                } else {
                    document.title = '视频详情 - 视频创作展示网站';
                }
                
                ticking = false;
            });
            ticking = true;
        }
    });
}

// 可选：启用滚动进度
// initScrollProgress();