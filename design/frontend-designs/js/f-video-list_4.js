// f-video-list_4.js - 视频列表页面脚本

document.addEventListener('DOMContentLoaded', function() {
    // 初始化Choices.js插件
    initChoices();
    
    // 初始化主题切换
    initThemeToggle();
    
    // 初始化悬浮按钮
    initFloatingButtons();
    
    // 初始化视频卡片交互
    initVideoCards();
});

// 初始化Choices.js多选下拉框
function initChoices() {
    // 标签筛选
    const tagFilter = new Choices('#tagFilter', {
        allowHTML: true,
        removeItemButton: true,
        placeholderValue: '请选择标签...',
        noChoicesText: '没有可选标签',
        noResultsText: '未找到匹配标签',
        itemSelectText: '',
        shouldSort: false,
        position: 'bottom',
        classNames: {
            containerOuter: ['choices', 'choices-tag-filter'],
            containerInner: ['choices__inner'],
            input: ['choices__input'],
            inputCloned: ['choices__input--cloned'],
            list: ['choices__list'],
            listItems: ['choices__list--multiple'],
            listSingle: ['choices__list--single'],
            listDropdown: ['choices__list--dropdown'],
            item: ['choices__item'],
            itemSelectable: ['choices__item--selectable'],
            itemDisabled: ['choices__item--disabled'],
            itemChoice: ['choices__item--choice'],
            placeholder: ['choices__placeholder'],
            group: ['choices__group'],
            groupHeading: ['choices__heading'],
            button: ['choices__button'],
            activeState: ['is-active'],
            focusState: ['is-focused'],
            openState: ['is-open'],
            disabledState: ['is-disabled'],
            highlightedState: ['is-highlighted'],
            selectedState: ['is-selected'],
            flippedState: ['is-flipped'],
            loadingState: ['is-loading']
        }
    });

    // 合集筛选
    const collectionFilter = new Choices('#collectionFilter', {
        allowHTML: true,
        removeItemButton: true,
        placeholderValue: '请选择合集...',
        noChoicesText: '没有可选合集',
        noResultsText: '未找到匹配合集',
        itemSelectText: '',
        shouldSort: false,
        position: 'bottom',
        classNames: {
            containerOuter: ['choices', 'choices-collection-filter'],
            containerInner: ['choices__inner'],
            input: ['choices__input'],
            inputCloned: ['choices__input--cloned'],
            list: ['choices__list'],
            listItems: ['choices__list--multiple'],
            listSingle: ['choices__list--single'],
            listDropdown: ['choices__list--dropdown'],
            item: ['choices__item'],
            itemSelectable: ['choices__item--selectable'],
            itemDisabled: ['choices__item--disabled'],
            itemChoice: ['choices__item--choice'],
            placeholder: ['choices__placeholder'],
            group: ['choices__group'],
            groupHeading: ['choices__heading'],
            button: ['choices__button'],
            activeState: ['is-active'],
            focusState: ['is-focused'],
            openState: ['is-open'],
            disabledState: ['is-disabled'],
            highlightedState: ['is-highlighted'],
            selectedState: ['is-selected'],
            flippedState: ['is-flipped'],
            loadingState: ['is-loading']
        }
    });

    // 添加自定义样式修复
    setTimeout(() => {
        addChoicesStyleFixes();
    }, 100);
}

// 修复Choices.js样式问题
function addChoicesStyleFixes() {
    const style = document.createElement('style');
    style.textContent = `
        /* 修复下拉菜单z-index问题 */
        .choices__list--dropdown {
            z-index: 9999 !important;
            position: absolute !important;
        }
        
        /* 修复删除按钮居中问题 */
        .choices__button {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            right: 6px !important;
        }
        
        /* 修复下拉菜单颜色 */
        .choices__item--choice {
            background-color: white !important;
            color: var(--text-primary) !important;
        }
        
        .choices__item--choice:hover,
        .choices__item--choice.is-highlighted {
            background-color: var(--brand-color) !important;
            color: white !important;
        }
        
        [data-bs-theme="dark"] .choices__item--choice {
            background-color: var(--dark-color) !important;
            color: var(--text-primary) !important;
        }
        
        [data-bs-theme="dark"] .choices__item--choice:hover,
        [data-bs-theme="dark"] .choices__item--choice.is-highlighted {
            background-color: var(--brand-color) !important;
            color: white !important;
        }
        
        /* 修复placeholder颜色 */
        [data-bs-theme="dark"] .choices__placeholder {
            color: #9ca3af !important;
            opacity: 0.8 !important;
        }
        
        /* 确保下拉菜单在卡片上方 */
        .choices.is-open .choices__list--dropdown {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        }
    `;
    document.head.appendChild(style);
}

// 初始化主题切换功能
function initThemeToggle() {
    const themeDropdown = document.getElementById('themeDropdown');
    const themeItems = document.querySelectorAll('[data-theme]');
    
    // 获取当前主题
    const getCurrentTheme = () => {
        return localStorage.getItem('theme') || 'auto';
    };
    
    // 设置主题
    const setTheme = (theme) => {
        localStorage.setItem('theme', theme);
        
        if (theme === 'auto') {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.setAttribute('data-bs-theme', prefersDark ? 'dark' : 'light');
        } else {
            document.documentElement.setAttribute('data-bs-theme', theme);
        }
        
        // 更新活动状态
        themeItems.forEach(item => {
            item.classList.toggle('active', item.getAttribute('data-theme') === theme);
        });
    };
    
    // 监听主题切换点击
    themeItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            const theme = item.getAttribute('data-theme');
            setTheme(theme);
        });
    });
    
    // 初始化主题
    setTheme(getCurrentTheme());
    
    // 监听系统主题变化
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        if (getCurrentTheme() === 'auto') {
            setTheme('auto');
        }
    });
}

// 初始化悬浮按钮功能
function initFloatingButtons() {
    const backToTopBtn = document.getElementById('backToTop');
    const contactUsBtn = document.getElementById('contactUs');
    
    // 回到顶部按钮
    if (backToTopBtn) {
        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // 根据滚动位置显示/隐藏按钮
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopBtn.style.opacity = '1';
                backToTopBtn.style.visibility = 'visible';
            } else {
                backToTopBtn.style.opacity = '0';
                backToTopBtn.style.visibility = 'hidden';
            }
        });
    }
    
    // 联系我们按钮
    if (contactUsBtn) {
        contactUsBtn.addEventListener('click', () => {
            // 这里可以添加联系我们的逻辑
            alert('联系我们功能待开发');
        });
    }
}

// 初始化视频卡片交互
function initVideoCards() {
    const videoCards = document.querySelectorAll('.video-card');
    
    videoCards.forEach(card => {
        const playOverlay = card.querySelector('.play-overlay');
        const videoTitle = card.querySelector('.video-title');
        
        // 播放按钮点击事件
        if (playOverlay) {
            playOverlay.addEventListener('click', (e) => {
                e.preventDefault();
                // 这里可以添加视频播放逻辑
                console.log('播放视频:', videoTitle?.textContent);
            });
        }
        
        // 标题点击事件
        if (videoTitle) {
            videoTitle.addEventListener('click', (e) => {
                e.preventDefault();
                // 这里可以添加跳转到视频详情页的逻辑
                console.log('跳转到视频详情:', videoTitle.textContent);
            });
        }
        
        // 卡片悬停效果
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-4px)';
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0)';
        });
    });
}

// 搜索功能
function initSearch() {
    const searchInput = document.getElementById('searchInput');
    const searchForm = searchInput?.closest('form');
    
    if (searchForm) {
        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const query = searchInput.value.trim();
            if (query) {
                // 这里可以添加搜索逻辑
                console.log('搜索:', query);
            }
        });
    }
}

// 分页功能
function initPagination() {
    const paginationBtns = document.querySelectorAll('.pagination-btn');
    
    paginationBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            if (!btn.classList.contains('active')) {
                // 这里可以添加分页逻辑
                console.log('跳转到页面:', btn.textContent);
            }
        });
    });
}