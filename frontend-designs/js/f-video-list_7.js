// f-video-list_7.js - 视频列表页面脚本

document.addEventListener('DOMContentLoaded', function() {
    // 初始化原生多选下拉框
    initCustomMultiselect();
    
    // 初始化主题切换
    initThemeToggle();
    
    // 初始化悬浮按钮
    initFloatingButtons();
    
    // 初始化视频卡片交互
    initVideoCards();
});

// 初始化自定义多选下拉框
function initCustomMultiselect() {
    const multiselects = document.querySelectorAll('.custom-multiselect');
    
    multiselects.forEach(multiselect => {
        const display = multiselect.querySelector('.multiselect-display');
        const dropdown = multiselect.querySelector('.multiselect-dropdown');
        const options = multiselect.querySelectorAll('.dropdown-option');
        const placeholder = multiselect.querySelector('.placeholder-text');
        const arrowIcon = multiselect.querySelector('.arrow-icon');
        
        // 点击显示区域切换下拉菜单
        display.addEventListener('click', function() {
            const isOpen = dropdown.classList.contains('show');
            
            // 关闭所有其他下拉菜单
            document.querySelectorAll('.multiselect-dropdown.show').forEach(d => {
                d.classList.remove('show');
                d.parentElement.querySelector('.multiselect-display').classList.remove('active');
            });
            
            if (!isOpen) {
                dropdown.classList.add('show');
                display.classList.add('active');
            } else {
                dropdown.classList.remove('show');
                display.classList.remove('active');
            }
        });
        
        // 处理选项点击
        options.forEach(option => {
            const checkbox = option.querySelector('input[type="checkbox"]');
            const label = option.querySelector('label');
            
            // 点击选项区域或文字切换复选框
            option.addEventListener('click', function(e) {
                if (e.target !== checkbox) {
                    checkbox.checked = !checkbox.checked;
                }
                updateDisplay(multiselect);
            });
            
            // 直接点击复选框
            checkbox.addEventListener('change', function() {
                updateDisplay(multiselect);
            });
            
            // 点击文字也可以切换选中状态
            label.addEventListener('click', function(e) {
                e.preventDefault();
                checkbox.checked = !checkbox.checked;
                updateDisplay(multiselect);
            });
        });
    });
    
    // 点击外部关闭下拉菜单
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-multiselect')) {
            document.querySelectorAll('.multiselect-dropdown.show').forEach(dropdown => {
                dropdown.classList.remove('show');
                dropdown.parentElement.querySelector('.multiselect-display').classList.remove('active');
            });
        }
    });
}

// 更新显示区域
function updateDisplay(multiselect) {
    const display = multiselect.querySelector('.multiselect-display');
    const placeholder = multiselect.querySelector('.placeholder-text');
    const checkboxes = multiselect.querySelectorAll('input[type="checkbox"]:checked');
    const arrowIcon = multiselect.querySelector('.arrow-icon');
    
    // 清空已选中的项目显示
    const existingItems = display.querySelectorAll('.selected-item, .selected-count');
    existingItems.forEach(item => item.remove());
    
    if (checkboxes.length > 0) {
        // 隐藏占位符文字
        placeholder.style.display = 'none';
        
        // 创建选中项目容器
        const selectedContainer = document.createElement('div');
        selectedContainer.className = 'selected-items';
        
        const selectedArray = Array.from(checkboxes);
        const maxVisible = 5;
        
        // 显示前5个选中项目
        selectedArray.slice(0, maxVisible).forEach(checkbox => {
            const label = checkbox.parentElement.querySelector('label');
            const selectedItem = document.createElement('span');
            selectedItem.className = 'selected-item';
            selectedItem.innerHTML = `
                ${label.textContent}
                <button type="button" class="remove-btn" data-value="${checkbox.value}">
                    <i class="bi bi-x"></i>
                </button>
            `;
            
            // 添加删除按钮事件
            const removeBtn = selectedItem.querySelector('.remove-btn');
            removeBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                checkbox.checked = false;
                updateDisplay(multiselect);
            });
            
            selectedContainer.appendChild(selectedItem);
        });
        
        // 如果选中数量大于0，显示总数
        if (selectedArray.length > 0) {
            const countSpan = document.createElement('span');
            countSpan.className = 'selected-count';
            countSpan.textContent = `共${selectedArray.length}个`;
            selectedContainer.appendChild(countSpan);
        }
        
        display.insertBefore(selectedContainer, arrowIcon);
    } else {
        // 显示占位符文字
        placeholder.style.display = 'inline';
    }
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
        const videoThumbnail = card.querySelector('.video-thumbnail a');
        const videoTitle = card.querySelector('.video-title');
        
        // 缩略图点击事件
        if (videoThumbnail) {
            videoThumbnail.addEventListener('click', (e) => {
                e.preventDefault();
                // 这里可以添加视频播放逻辑
                console.log('点击视频封面:', videoTitle?.textContent);
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