/* 视频列表页面JavaScript - f-video-list_2.js */

// DOM加载完成后执行
document.addEventListener('DOMContentLoaded', function() {
    // 初始化页面功能
    initializeVideoListPage();
});

// 页面初始化函数
function initializeVideoListPage() {
    // 初始化主题切换
    initThemeToggle();
    
    // 初始化搜索功能
    initSearchFunctionality();
    
    // 初始化筛选器
    initFilters();
    
    // 初始化视频卡片交互
    initVideoCardInteractions();
    
    // 初始化悬浮按钮
    initFloatingButtons();
    
    // 初始化邮件订阅
    initEmailSubscription();
    
    // 初始化页面加载动画
    initPageAnimations();
}

// 主题切换功能
function initThemeToggle() {
    const themeToggle = document.getElementById('themeToggle');
    const html = document.documentElement;
    const icon = themeToggle.querySelector('i');
    
    // 获取当前主题
    const currentTheme = localStorage.getItem('theme') || 'light';
    html.setAttribute('data-bs-theme', currentTheme);
    updateThemeIcon(icon, currentTheme);
    
    // 主题切换事件
    themeToggle.addEventListener('click', function() {
        const currentTheme = html.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        html.setAttribute('data-bs-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeIcon(icon, newTheme);
    });
}

// 更新主题图标
function updateThemeIcon(icon, theme) {
    icon.className = theme === 'dark' ? 'bi bi-sun' : 'bi bi-moon';
}

// 搜索功能初始化
function initSearchFunctionality() {
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    
    // 搜索按钮点击事件
    searchBtn.addEventListener('click', performSearch);
    
    // 回车键搜索
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });
    
    // 实时搜索建议（防抖）
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            showSearchSuggestions(searchInput.value);
        }, 300);
    });
}

// 执行搜索
function performSearch() {
    const searchInput = document.getElementById('searchInput');
    const keyword = searchInput.value.trim();
    
    if (keyword) {
        // 显示加载状态
        showLoadingState();
        
        // 模拟搜索请求
        setTimeout(function() {
            // 更新搜索结果
            updateSearchResults(keyword);
            hideLoadingState();
        }, 500);
    }
}

// 显示搜索建议
function showSearchSuggestions(keyword) {
    if (keyword.length < 2) return;
    
    // 模拟搜索建议
    const suggestions = [
        'JavaScript基础教程',
        'React实战项目',
        'Vue3组件开发',
        'Node.js后端开发',
        'CSS布局技巧'
    ].filter(item => item.toLowerCase().includes(keyword.toLowerCase()));
    
    // 这里可以显示搜索建议下拉框
    console.log('搜索建议:', suggestions);
}

// 筛选器初始化
function initFilters() {
    const tagFilter = document.getElementById('tagFilter');
    const collectionFilter = document.getElementById('collectionFilter');
    
    // 标签筛选事件
    tagFilter.addEventListener('change', function() {
        applyFilters();
    });
    
    // 合集筛选事件
    collectionFilter.addEventListener('change', function() {
        applyFilters();
    });
}

// 应用筛选器
function applyFilters() {
    const tagValue = document.getElementById('tagFilter').value;
    const collectionValue = document.getElementById('collectionFilter').value;
    
    // 显示加载状态
    showLoadingState();
    
    // 模拟筛选请求
    setTimeout(function() {
        // 更新筛选结果
        updateFilterResults(tagValue, collectionValue);
        hideLoadingState();
    }, 300);
}

// 视频卡片交互初始化
function initVideoCardInteractions() {
    const videoCards = document.querySelectorAll('.video-card');
    
    videoCards.forEach(function(card) {
        // 鼠标悬停效果
        card.addEventListener('mouseenter', function() {
            this.classList.add('shadow-medium');
        });
        
        card.addEventListener('mouseleave', function() {
            this.classList.remove('shadow-medium');
        });
        
        // 点击播放
        const playOverlay = card.querySelector('.play-overlay');
        const thumbnail = card.querySelector('.video-thumbnail');
        const title = card.querySelector('.video-title');
        
        [playOverlay, thumbnail, title].forEach(function(element) {
            if (element) {
                element.addEventListener('click', function() {
                    playVideo(card);
                });
            }
        });
    });
}

// 播放视频
function playVideo(card) {
    const title = card.querySelector('.video-title').textContent;
    
    // 添加点击动画
    card.classList.add('fade-in');
    
    // 模拟跳转到视频详情页
    console.log('播放视频:', title);
    
    // 实际项目中这里应该是跳转到视频详情页
    // window.location.href = '/video-detail?id=' + videoId;
}

// 悬浮按钮功能
function initFloatingButtons() {
    const backToTopBtn = document.getElementById('backToTop');
    const contactUsBtn = document.getElementById('contactUs');
    
    // 回到顶部功能
    backToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // 联系我们功能
    contactUsBtn.addEventListener('click', function() {
        // 这里可以显示联系我们的模态框或跳转到联系页面
        showContactModal();
    });
    
    // 监听滚动，控制按钮显示
    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            backToTopBtn.style.opacity = '1';
            backToTopBtn.style.transform = 'scale(1)';
        } else {
            backToTopBtn.style.opacity = '0';
            backToTopBtn.style.transform = 'scale(0.8)';
        }
    });
}

// 显示联系我们模态框
function showContactModal() {
    // 创建简单的提示
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-info position-fixed';
    alertDiv.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
    `;
    alertDiv.innerHTML = `
        <strong>联系我们</strong><br>
        邮箱: contact@example.com<br>
        电话: 400-123-4567
        <button type="button" class="btn-close float-end" style="margin-top: -20px;"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // 自动关闭
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
    
    // 手动关闭
    alertDiv.querySelector('.btn-close').addEventListener('click', () => {
        alertDiv.remove();
    });
}

// 邮件订阅功能
function initEmailSubscription() {
    const subscribeBtn = document.querySelector('footer .btn');
    const emailInput = document.querySelector('footer input[type="email"]');
    
    if (subscribeBtn && emailInput) {
        subscribeBtn.addEventListener('click', function() {
            const email = emailInput.value.trim();
            
            if (validateEmail(email)) {
                subscribeEmail(email);
            } else {
                showMessage('请输入有效的邮箱地址', 'warning');
            }
        });
        
        // 回车键订阅
        emailInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                subscribeBtn.click();
            }
        });
    }
}

// 邮箱验证
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// 邮件订阅处理
function subscribeEmail(email) {
    const subscribeBtn = document.querySelector('footer .btn');
    const originalText = subscribeBtn.textContent;
    
    // 显示加载状态
    subscribeBtn.textContent = '订阅中...';
    subscribeBtn.disabled = true;
    
    // 模拟订阅请求
    setTimeout(function() {
        // 重置按钮状态
        subscribeBtn.textContent = originalText;
        subscribeBtn.disabled = false;
        
        // 清空输入框
        document.querySelector('footer input[type="email"]').value = '';
        
        // 显示成功消息
        showMessage('订阅成功！感谢您的订阅', 'success');
    }, 1000);
}

// 页面动画初始化
function initPageAnimations() {
    // 为视频卡片添加进入动画
    const videoCards = document.querySelectorAll('.video-card');
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    videoCards.forEach(function(card, index) {
        // 添加延迟动画
        card.style.animationDelay = (index * 0.1) + 's';
        observer.observe(card);
    });
}

// 显示加载状态
function showLoadingState() {
    const videoCards = document.querySelectorAll('.video-card');
    videoCards.forEach(function(card) {
        card.classList.add('loading');
    });
}

// 隐藏加载状态
function hideLoadingState() {
    const videoCards = document.querySelectorAll('.video-card');
    videoCards.forEach(function(card) {
        card.classList.remove('loading');
    });
}

// 更新搜索结果
function updateSearchResults(keyword) {
    // 更新搜索结果统计
    const resultsInfo = document.querySelector('.search-results-info');
    if (resultsInfo) {
        resultsInfo.innerHTML = `
            <span class="text-muted">搜索结果: 共找到 <strong>64</strong> 个视频</span>
            <span class="ms-3 text-muted">当前筛选: 
                <span class="badge bg-secondary me-1">"${keyword}"</span>
            </span>
        `;
    }
    
    // 这里可以更新视频列表
    console.log('更新搜索结果:', keyword);
}

// 更新筛选结果
function updateFilterResults(tag, collection) {
    const resultsInfo = document.querySelector('.search-results-info');
    if (resultsInfo) {
        let filterText = '';
        if (tag) filterText += `<span class="badge bg-secondary me-1">"${getFilterDisplayName(tag)}"</span>`;
        if (collection) filterText += `<span class="badge bg-secondary me-1">"${getFilterDisplayName(collection)}"</span>`;
        
        resultsInfo.innerHTML = `
            <span class="text-muted">搜索结果: 共找到 <strong>42</strong> 个视频</span>
            <span class="ms-3 text-muted">当前筛选: ${filterText}</span>
        `;
    }
    
    console.log('更新筛选结果:', { tag, collection });
}

// 获取筛选器显示名称
function getFilterDisplayName(value) {
    const filterNames = {
        'programming': '编程',
        'frontend': '前端',
        'backend': '后端',
        'database': '数据库',
        'javascript': 'JavaScript',
        'react': 'React',
        'vue': 'Vue',
        'nodejs': 'Node.js',
        'tutorial': '教程合集',
        'practical': '实战合集',
        'basic': '入门合集',
        'component': '组件合集',
        'style': '样式合集',
        'tool': '工具合集',
        'data': '数据合集'
    };
    
    return filterNames[value] || value;
}

// 显示消息提示
function showMessage(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
    `;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // 自动关闭
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 3000);
}

// 工具函数：防抖
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

// 工具函数：节流
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
    };
}