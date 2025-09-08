// 视频列表页面专用JavaScript - f-video-list_3.js

// 初始化函数
document.addEventListener('DOMContentLoaded', function() {
    // 初始化Choices.js多选组件
    initializeChoices();
    
    // 初始化主题切换
    initializeThemeToggle();
    
    // 初始化搜索表单
    initializeSearchForm();
    
    // 初始化悬浮按钮
    initializeFloatingButtons();
    
    // 初始化视频卡片事件
    initializeVideoCards();
});

// 初始化Choices.js多选组件
function initializeChoices() {
    // 标签筛选
    const tagFilter = new Choices('#tagFilter', {
        allowHTML: false,
        removeItemButton: true,
        searchEnabled: false,
        placeholder: true,
        placeholderValue: '请选择标签...',
        maxItemCount: -1,
        shouldSort: false
    });
    
    // 合集筛选
    const collectionFilter = new Choices('#collectionFilter', {
        allowHTML: false,
        removeItemButton: true,
        searchEnabled: false,
        placeholder: true,
        placeholderValue: '请选择合集...',
        maxItemCount: -1,
        shouldSort: false
    });
    
    // 监听选择变化
    document.getElementById('tagFilter').addEventListener('change', function() {
        updateSearchResults();
    });
    
    document.getElementById('collectionFilter').addEventListener('change', function() {
        updateSearchResults();
    });
}

// 初始化主题切换
function initializeThemeToggle() {
    // 获取当前主题设置
    const savedTheme = localStorage.getItem('theme') || 'auto';
    applyTheme(savedTheme);
    
    // 主题切换事件监听
    document.querySelectorAll('[data-theme]').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const theme = this.getAttribute('data-theme');
            applyTheme(theme);
            localStorage.setItem('theme', theme);
        });
    });
}

// 应用主题
function applyTheme(theme) {
    const navbar = document.querySelector('.navbar');
    const html = document.documentElement;
    
    if (theme === 'dark') {
        html.setAttribute('data-bs-theme', 'dark');
        navbar.setAttribute('data-bs-theme', 'dark');
        navbar.classList.remove('bg-body-tertiary');
        navbar.classList.add('navbar-dark', 'bg-dark');
    } else if (theme === 'light') {
        html.removeAttribute('data-bs-theme');
        navbar.setAttribute('data-bs-theme', 'light');
        navbar.classList.remove('navbar-dark', 'bg-dark');
        navbar.classList.add('bg-body-tertiary');
    } else {
        // 自动模式，根据系统设置
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            applyTheme('dark');
        } else {
            applyTheme('light');
        }
        return;
    }
    
    // 更新主题下拉菜单选中状态
    updateThemeDropdown(theme);
}

// 更新主题下拉菜单
function updateThemeDropdown(activeTheme) {
    document.querySelectorAll('[data-theme]').forEach(item => {
        item.classList.remove('active');
        if (item.getAttribute('data-theme') === activeTheme) {
            item.classList.add('active');
        }
    });
}

// 初始化搜索表单
function initializeSearchForm() {
    const searchForm = document.querySelector('form');
    const searchInput = document.getElementById('searchInput');
    
    // 表单提交事件
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        performSearch();
    });
    
    // 回车键搜索
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            performSearch();
        }
    });
}

// 执行搜索
function performSearch() {
    const formData = new FormData(document.querySelector('form'));
    const searchParams = new URLSearchParams();
    
    // 构建搜索参数
    for (let [key, value] of formData.entries()) {
        if (value.trim() !== '') {
            searchParams.append(key, value);
        }
    }
    
    // 模拟搜索（实际项目中应该重定向到服务器）
    console.log('搜索参数:', searchParams.toString());
    showLoading();
    
    // 模拟网络请求延迟
    setTimeout(() => {
        hideLoading();
        updateSearchResults();
    }, 1000);
}

// 显示加载状态
function showLoading() {
    const videoCards = document.querySelectorAll('.video-card');
    videoCards.forEach(card => {
        card.classList.add('loading');
    });
}

// 隐藏加载状态
function hideLoading() {
    const videoCards = document.querySelectorAll('.video-card');
    videoCards.forEach(card => {
        card.classList.remove('loading');
    });
}

// 更新搜索结果
function updateSearchResults() {
    // 这里应该根据实际筛选条件更新视频列表
    // 目前只是模拟更新统计数字
    const resultCount = Math.floor(Math.random() * 200) + 50;
    const resultText = document.querySelector('main .text-muted');
    if (resultText) {
        resultText.innerHTML = `搜索结果: 共找到 <strong>${resultCount}</strong> 个视频`;
    }
}

// 初始化悬浮按钮
function initializeFloatingButtons() {
    const backToTopBtn = document.getElementById('backToTop');
    const contactUsBtn = document.getElementById('contactUs');
    
    // 回到顶部按钮
    if (backToTopBtn) {
        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // 滚动时显示/隐藏按钮
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopBtn.style.opacity = '1';
                backToTopBtn.style.pointerEvents = 'auto';
            } else {
                backToTopBtn.style.opacity = '0.6';
                backToTopBtn.style.pointerEvents = 'none';
            }
        });
    }
    
    // 联系我们按钮
    if (contactUsBtn) {
        contactUsBtn.addEventListener('click', function() {
            alert('联系我们: support@videosite.com');
        });
    }
}

// 初始化视频卡片事件
function initializeVideoCards() {
    const videoCards = document.querySelectorAll('.video-card');
    
    videoCards.forEach(card => {
        const playOverlay = card.querySelector('.play-overlay');
        const videoTitle = card.querySelector('.video-title');
        
        // 播放按钮点击事件
        if (playOverlay) {
            playOverlay.addEventListener('click', function() {
                const title = videoTitle ? videoTitle.textContent : '未知视频';
                alert(`即将播放: ${title}`);
            });
        }
        
        // 标题点击事件
        if (videoTitle) {
            videoTitle.addEventListener('click', function() {
                alert(`进入视频详情页: ${this.textContent}`);
            });
        }
        
        // 卡片动画效果
        card.classList.add('fade-in');
    });
}

// 页面大小变化时的处理
window.addEventListener('resize', function() {
    // 处理响应式布局调整
    adjustLayout();
});

// 调整布局
function adjustLayout() {
    const windowWidth = window.innerWidth;
    const videoGrid = document.querySelector('.video-grid');
    
    // 根据屏幕宽度调整卡片布局
    if (windowWidth < 576) {
        // 移动端：单列布局
        videoGrid.classList.add('mobile-layout');
    } else {
        videoGrid.classList.remove('mobile-layout');
    }
}

// 模拟数据加载
function loadMoreVideos() {
    showLoading();
    
    // 模拟异步加载
    setTimeout(() => {
        hideLoading();
        console.log('加载更多视频数据...');
    }, 1500);
}

// 工具函数

// 防抖函数
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func.apply(this, args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// 节流函数
function throttle(func, limit) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// 格式化日期
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { 
        year: 'numeric', 
        month: '2-digit', 
        day: '2-digit' 
    };
    return date.toLocaleDateString('zh-CN', options);
}

// 等待DOM加载完成
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', adjustLayout);
} else {
    adjustLayout();
}