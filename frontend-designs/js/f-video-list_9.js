// f-video-list_9.js - 视频列表页面脚本

document.addEventListener('DOMContentLoaded', function() {
    // 初始化原生多选下拉框
    initCustomMultiselect();
    
    // 初始化主题切换
    initThemeToggle();
    
    // 初始化悬浮按钮
    initFloatingButtons();
    
    // 初始化视频卡片交互
    initVideoCards();
    
    // 初始化微信二维码功能
    initWeChatQRCode();
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
        
        // 处理选项点击 - 简化逻辑，避免事件冲突
        options.forEach(option => {
            const checkbox = option.querySelector('input[type="checkbox"]');
            
            // 点击整个选项区域都可以切换复选框状态
            option.addEventListener('click', function(e) {
                // 阻止事件冒泡
                e.stopPropagation();
                // 切换选中状态
                checkbox.checked = !checkbox.checked;
                // 更新显示
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
    
    // 清空已选中的项目显示 - 修复bug：正确清理selected-items容器
    const existingContainer = display.querySelector('.selected-items');
    if (existingContainer) {
        existingContainer.remove();
    }
    
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

// 初始化微信二维码功能
function initWeChatQRCode() {
    const wechatLinks = document.querySelectorAll('footer a[href="#"]');
    
    wechatLinks.forEach(link => {
        const linkText = link.textContent.toLowerCase();
        
        // 找到微信链接
        if (linkText.includes('微信') || link.querySelector('i.bi-wechat')) {
            // 创建二维码容器
            const qrContainer = document.createElement('div');
            qrContainer.className = 'wechat-qr-tooltip';
            qrContainer.innerHTML = `
                <div class="qr-content">
                    <img src="https://picsum.photos/120/120?random=qr" alt="微信二维码" class="qr-image">
                    <p class="qr-text">扫码添加微信</p>
                </div>
            `;
            
            // 设置样式
            qrContainer.style.cssText = `
                position: absolute;
                bottom: 100%;
                left: 50%;
                transform: translateX(-50%);
                background: white;
                border: 2px solid #ffffff;
                border-radius: 8px;
                padding: 12px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                z-index: 10000;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
                white-space: nowrap;
                min-width: 140px;
            `;
            
            // 设置二维码图片样式
            const qrImage = qrContainer.querySelector('.qr-image');
            qrImage.style.cssText = `
                width: 120px;
                height: 120px;
                border-radius: 4px;
                display: block;
                margin: 0 auto 8px;
            `;
            
            // 设置文字样式
            const qrText = qrContainer.querySelector('.qr-text');
            qrText.style.cssText = `
                margin: 0;
                font-size: 12px;
                color: #333;
                text-align: center;
            `;
            
            // 添加箭头
            const arrow = document.createElement('div');
            arrow.style.cssText = `
                position: absolute;
                top: 100%;
                left: 50%;
                transform: translateX(-50%);
                width: 0;
                height: 0;
                border-left: 6px solid transparent;
                border-right: 6px solid transparent;
                border-top: 6px solid #07c160;
            `;
            qrContainer.appendChild(arrow);
            
            // 添加到页面
            link.style.position = 'relative';
            link.appendChild(qrContainer);
            
            // 鼠标悬停显示二维码
            link.addEventListener('mouseenter', () => {
                qrContainer.style.opacity = '1';
                qrContainer.style.visibility = 'visible';
                qrContainer.style.transform = 'translateX(-50%) translateY(-5px)';
            });
            
            // 鼠标离开隐藏二维码
            link.addEventListener('mouseleave', () => {
                qrContainer.style.opacity = '0';
                qrContainer.style.visibility = 'hidden';
                qrContainer.style.transform = 'translateX(-50%) translateY(0)';
            });
            
            // 点击也可以切换显示状态
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const isVisible = qrContainer.style.opacity === '1';
                
                if (isVisible) {
                    qrContainer.style.opacity = '0';
                    qrContainer.style.visibility = 'hidden';
                } else {
                    qrContainer.style.opacity = '1';
                    qrContainer.style.visibility = 'visible';
                    qrContainer.style.transform = 'translateX(-50%) translateY(-5px)';
                }
            });
        }
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