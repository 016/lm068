// main.js - 通用JavaScript功能，可在多页面间共享
document.addEventListener('DOMContentLoaded', function() {
    // 初始化主题切换功能
    initThemeToggle();
    
    // 初始化悬浮按钮功能
    initFloatingButtons();
    
    // 初始化微信二维码功能
    // initWeChatQRCode();
    
});

// 初始化主题切换功能
function initThemeToggle() {
    console.log('theme changing...');
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