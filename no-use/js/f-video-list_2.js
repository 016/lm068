/**
 * f-video-list 页面功能脚本
 * 负责视频列表页面的交互功能
 */

class VideoListManager {
    constructor() {
        this.videos = [];
        this.filteredVideos = [];
        this.currentPage = 1;
        this.videosPerPage = 8;
        this.filters = {
            tags: [],
            collection: '',
            search: '',
            sort: 'newest'
        };
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.initThemeToggle();
        this.initLanguageSwitch();
        this.loadVideoData();
        this.initFloatingButtons();
    }

    /**
     * 绑定事件监听器
     */
    bindEvents() {
        // 搜索功能
        const searchBtn = document.getElementById('searchBtn');
        const searchInput = document.getElementById('searchInput');
        
        if (searchBtn) {
            searchBtn.addEventListener('click', () => this.performSearch());
        }
        
        if (searchInput) {
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.performSearch();
                }
            });
        }

        // 标签筛选
        const tagFilter = document.getElementById('tagFilter');
        if (tagFilter) {
            tagFilter.addEventListener('change', () => this.applyTagFilter());
        }

        // 合集筛选
        const collectionFilter = document.getElementById('collectionFilter');
        if (collectionFilter) {
            collectionFilter.addEventListener('change', () => this.applyCollectionFilter());
        }

        // 清除筛选
        const clearFilters = document.getElementById('clearFilters');
        if (clearFilters) {
            clearFilters.addEventListener('click', () => this.clearAllFilters());
        }

        // 排序功能
        const sortOptions = document.querySelectorAll('[data-sort]');
        sortOptions.forEach(option => {
            option.addEventListener('click', (e) => {
                e.preventDefault();
                this.applySorting(option.dataset.sort);
            });
        });

        // 分页功能
        this.bindPaginationEvents();

        // 语言切换
        const langOptions = document.querySelectorAll('[data-lang]');
        langOptions.forEach(option => {
            option.addEventListener('click', (e) => {
                e.preventDefault();
                this.switchLanguage(option.dataset.lang);
            });
        });

        // 视频卡片点击
        this.bindVideoCardEvents();
    }

    /**
     * 绑定分页事件
     */
    bindPaginationEvents() {
        document.addEventListener('click', (e) => {
            if (e.target.closest('.page-link')) {
                e.preventDefault();
                const pageLink = e.target.closest('.page-link');
                const pageItem = pageLink.closest('.page-item');
                
                if (pageItem.classList.contains('disabled')) {
                    return;
                }

                const pageText = pageLink.textContent.trim();
                if (pageText.includes('上一页')) {
                    this.goToPage(this.currentPage - 1);
                } else if (pageText.includes('下一页')) {
                    this.goToPage(this.currentPage + 1);
                } else if (!isNaN(pageText)) {
                    this.goToPage(parseInt(pageText));
                }
            }
        });
    }

    /**
     * 绑定视频卡片点击事件
     */
    bindVideoCardEvents() {
        document.addEventListener('click', (e) => {
            const videoCard = e.target.closest('.video-card');
            if (videoCard) {
                const videoId = videoCard.dataset.videoId;
                if (videoId) {
                    this.viewVideo(videoId);
                }
            }
        });
    }

    /**
     * 执行搜索
     */
    performSearch() {
        const searchInput = document.getElementById('searchInput');
        const searchTerm = searchInput?.value?.trim() || '';
        
        this.filters.search = searchTerm;
        this.currentPage = 1;
        this.applyFilters();
        
        // 更新搜索摘要
        this.updateSearchSummary();
    }

    /**
     * 应用标签筛选
     */
    applyTagFilter() {
        const tagFilter = document.getElementById('tagFilter');
        const selectedTags = Array.from(tagFilter.selectedOptions).map(option => option.value);
        
        this.filters.tags = selectedTags.filter(tag => tag !== '');
        this.currentPage = 1;
        this.applyFilters();
        this.updateSearchSummary();
    }

    /**
     * 应用合集筛选
     */
    applyCollectionFilter() {
        const collectionFilter = document.getElementById('collectionFilter');
        this.filters.collection = collectionFilter?.value || '';
        this.currentPage = 1;
        this.applyFilters();
        this.updateSearchSummary();
    }

    /**
     * 应用排序
     */
    applySorting(sortType) {
        this.filters.sort = sortType;
        this.applyFilters();
        
        // 更新排序按钮文本
        const sortButton = document.querySelector('[data-bs-toggle="dropdown"]');
        if (sortButton) {
            const sortTexts = {
                'newest': '最新发布',
                'views': '观看次数',
                'title': '标题排序'
            };
            sortButton.innerHTML = `<i class="bi bi-sort-down"></i> ${sortTexts[sortType]}`;
        }
    }

    /**
     * 清除所有筛选
     */
    clearAllFilters() {
        // 重置筛选器
        const tagFilter = document.getElementById('tagFilter');
        const collectionFilter = document.getElementById('collectionFilter');
        const searchInput = document.getElementById('searchInput');
        
        if (tagFilter) {
            tagFilter.selectedIndex = 0;
            Array.from(tagFilter.options).forEach(option => option.selected = false);
        }
        
        if (collectionFilter) {
            collectionFilter.selectedIndex = 0;
        }
        
        if (searchInput) {
            searchInput.value = '';
        }

        // 重置筛选条件
        this.filters = {
            tags: [],
            collection: '',
            search: '',
            sort: 'newest'
        };
        
        this.currentPage = 1;
        this.applyFilters();
        this.updateSearchSummary();
    }

    /**
     * 应用筛选条件
     */
    applyFilters() {
        this.filteredVideos = this.videos.filter(video => {
            // 搜索筛选
            if (this.filters.search) {
                const searchLower = this.filters.search.toLowerCase();
                if (!video.title.toLowerCase().includes(searchLower) && 
                    !video.description.toLowerCase().includes(searchLower)) {
                    return false;
                }
            }

            // 标签筛选
            if (this.filters.tags.length > 0) {
                const hasMatchingTag = this.filters.tags.some(tag => 
                    video.tags.includes(tag)
                );
                if (!hasMatchingTag) {
                    return false;
                }
            }

            // 合集筛选
            if (this.filters.collection && video.collection !== this.filters.collection) {
                return false;
            }

            return true;
        });

        // 应用排序
        this.sortVideos();
        
        // 重新渲染视频列表
        this.renderVideos();
        this.renderPagination();
    }

    /**
     * 排序视频
     */
    sortVideos() {
        switch (this.filters.sort) {
            case 'newest':
                this.filteredVideos.sort((a, b) => new Date(b.date) - new Date(a.date));
                break;
            case 'views':
                this.filteredVideos.sort((a, b) => b.views - a.views);
                break;
            case 'title':
                this.filteredVideos.sort((a, b) => a.title.localeCompare(b.title));
                break;
        }
    }

    /**
     * 跳转到指定页面
     */
    goToPage(page) {
        const totalPages = Math.ceil(this.filteredVideos.length / this.videosPerPage);
        
        if (page < 1 || page > totalPages) {
            return;
        }

        this.currentPage = page;
        this.renderVideos();
        this.renderPagination();
        
        // 滚动到页面顶部
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    /**
     * 渲染视频列表
     */
    renderVideos() {
        const container = document.getElementById('videoContainer');
        if (!container) return;

        const startIndex = (this.currentPage - 1) * this.videosPerPage;
        const endIndex = startIndex + this.videosPerPage;
        const videosToShow = this.filteredVideos.slice(startIndex, endIndex);

        if (videosToShow.length === 0) {
            this.renderEmptyState(container);
            return;
        }

        container.innerHTML = videosToShow.map(video => this.createVideoCard(video)).join('');
    }

    /**
     * 创建视频卡片HTML
     */
    createVideoCard(video) {
        const tagsHtml = video.tags.map(tag => 
            `<span class="badge bg-primary">${tag}</span>`
        ).join('');

        const collectionBadge = video.collection ? 
            `<div class="collection-tag">
                <span class="badge bg-success">${video.collection}</span>
            </div>` : '';

        return `
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card video-card h-100 shadow-sm" data-video-id="${video.id}">
                <div class="video-thumbnail position-relative">
                    <img src="${video.thumbnail}" class="card-img-top" alt="${video.title}">
                    <div class="play-overlay">
                        <i class="bi bi-play-circle-fill"></i>
                    </div>
                    <div class="duration-badge">${video.duration}</div>
                </div>
                <div class="card-body">
                    <h6 class="card-title">${video.title}</h6>
                    <p class="card-text text-muted small mb-2">${video.date} | 作者: ${video.author}</p>
                    <p class="card-text small text-secondary">${video.description}</p>
                    <div class="tags mb-2">
                        ${tagsHtml}
                    </div>
                    ${collectionBadge}
                    <div class="video-stats mt-2">
                        <small class="text-muted">
                            <i class="bi bi-eye"></i> ${video.views.toLocaleString()} 次观看
                        </small>
                    </div>
                </div>
            </div>
        </div>`;
    }

    /**
     * 渲染空状态
     */
    renderEmptyState(container) {
        container.innerHTML = `
        <div class="col-12">
            <div class="empty-state">
                <i class="bi bi-camera-video"></i>
                <h5>没有找到相关视频</h5>
                <p>尝试调整搜索条件或清除筛选器来查看更多视频</p>
                <button class="btn btn-primary" id="clearFiltersEmpty">清除筛选</button>
            </div>
        </div>`;
        
        // 绑定清除筛选按钮
        const clearBtn = document.getElementById('clearFiltersEmpty');
        if (clearBtn) {
            clearBtn.addEventListener('click', () => this.clearAllFilters());
        }
    }

    /**
     * 渲染分页
     */
    renderPagination() {
        const totalPages = Math.ceil(this.filteredVideos.length / this.videosPerPage);
        const paginationContainer = document.querySelector('.pagination');
        
        if (!paginationContainer || totalPages <= 1) {
            if (paginationContainer) {
                paginationContainer.style.display = 'none';
            }
            return;
        }

        paginationContainer.style.display = 'flex';
        
        let paginationHtml = '';

        // 上一页
        const prevDisabled = this.currentPage === 1 ? 'disabled' : '';
        paginationHtml += `
        <li class="page-item ${prevDisabled}">
            <a class="page-link" href="#" tabindex="-1">
                <i class="bi bi-chevron-left"></i> 上一页
            </a>
        </li>`;

        // 页码
        const startPage = Math.max(1, this.currentPage - 2);
        const endPage = Math.min(totalPages, startPage + 4);

        for (let i = startPage; i <= endPage; i++) {
            const active = i === this.currentPage ? 'active' : '';
            paginationHtml += `
            <li class="page-item ${active}">
                <a class="page-link" href="#">${i}</a>
            </li>`;
        }

        // 下一页
        const nextDisabled = this.currentPage === totalPages ? 'disabled' : '';
        paginationHtml += `
        <li class="page-item ${nextDisabled}">
            <a class="page-link" href="#">
                下一页 <i class="bi bi-chevron-right"></i>
            </a>
        </li>`;

        paginationContainer.innerHTML = paginationHtml;
    }

    /**
     * 更新搜索摘要
     */
    updateSearchSummary() {
        const summaryElement = document.getElementById('searchSummary');
        if (!summaryElement) return;

        const totalVideos = this.filteredVideos.length;
        let summaryText = `搜索结果：共找到 <span class="fw-bold text-primary">${totalVideos}</span> 个视频`;
        
        const activeFilters = [];
        
        if (this.filters.search) {
            activeFilters.push(`"${this.filters.search}"`);
        }
        
        if (this.filters.tags.length > 0) {
            activeFilters.push(this.filters.tags.join(', '));
        }
        
        if (this.filters.collection) {
            activeFilters.push(this.filters.collection);
        }
        
        if (activeFilters.length > 0) {
            summaryText += ` | 当前筛选: ${activeFilters.join(' + ')}`;
        }

        summaryElement.innerHTML = summaryText;
    }

    /**
     * 初始化主题切换功能
     */
    initThemeToggle() {
        const themeToggle = document.getElementById('themeToggle');
        if (!themeToggle) return;

        // 从localStorage获取主题设置
        const currentTheme = localStorage.getItem('theme') || 'light';
        this.setTheme(currentTheme);

        themeToggle.addEventListener('click', () => {
            const newTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
            this.setTheme(newTheme);
            localStorage.setItem('theme', newTheme);
        });
    }

    /**
     * 设置主题
     */
    setTheme(theme) {
        document.documentElement.dataset.theme = theme;
        const themeToggle = document.getElementById('themeToggle');
        
        if (themeToggle) {
            const icon = themeToggle.querySelector('i');
            if (theme === 'dark') {
                icon.className = 'bi bi-sun-fill';
                themeToggle.title = '切换到浅色模式';
            } else {
                icon.className = 'bi bi-moon-fill';
                themeToggle.title = '切换到深色模式';
            }
        }
    }

    /**
     * 初始化语言切换功能
     */
    initLanguageSwitch() {
        const currentLang = localStorage.getItem('language') || 'zh-CN';
        this.updateLanguageUI(currentLang);
    }

    /**
     * 切换语言
     */
    switchLanguage(lang) {
        localStorage.setItem('language', lang);
        this.updateLanguageUI(lang);
        // 这里可以添加实际的语言切换逻辑
        console.log(`切换到语言: ${lang}`);
    }

    /**
     * 更新语言UI
     */
    updateLanguageUI(lang) {
        const langButton = document.querySelector('.dropdown-toggle');
        if (langButton) {
            const langText = lang === 'zh-CN' ? '中文' : 'English';
            langButton.innerHTML = `<i class="bi bi-globe"></i> ${langText}`;
        }
    }

    /**
     * 初始化悬浮按钮
     */
    initFloatingButtons() {
        const backToTop = document.getElementById('backToTop');
        const contactUs = document.getElementById('contactUs');

        if (backToTop) {
            backToTop.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });

            // 控制回到顶部按钮显示
            window.addEventListener('scroll', () => {
                if (window.scrollY > 300) {
                    backToTop.style.opacity = '1';
                    backToTop.style.pointerEvents = 'auto';
                } else {
                    backToTop.style.opacity = '0';
                    backToTop.style.pointerEvents = 'none';
                }
            });
        }

        if (contactUs) {
            contactUs.addEventListener('click', () => {
                // 这里可以打开联系我们的模态框或跳转到联系页面
                console.log('联系我们功能');
            });
        }
    }

    /**
     * 查看视频详情
     */
    viewVideo(videoId) {
        // 跳转到视频详情页面
        window.location.href = `/video-detail.html?id=${videoId}`;
    }

    /**
     * 加载视频数据
     */
    loadVideoData() {
        // 模拟视频数据 - 实际项目中应该从API获取
        this.videos = [
            {
                id: 1,
                title: 'JavaScript基础教程',
                description: '这是一个关于JavaScript基础知识的完整教程...',
                thumbnail: 'https://via.placeholder.com/300x169/007bff/ffffff?text=JavaScript%E5%9F%BA%E7%A1%80',
                date: '2024-01-15',
                author: '张老师',
                tags: ['编程', '基础'],
                collection: '教程合集',
                duration: '15:30',
                views: 1234
            },
            {
                id: 2,
                title: 'React实战教程',
                description: '深入学习React框架，构建现代化Web应用...',
                thumbnail: 'https://via.placeholder.com/300x169/28a745/ffffff?text=React%E5%AE%9E%E6%88%98',
                date: '2024-01-10',
                author: '李老师',
                tags: ['React', '实战'],
                collection: '实战合集',
                duration: '28:45',
                views: 2567
            },
            {
                id: 3,
                title: 'Node.js入门教程',
                description: '从零开始学习Node.js后端开发技术...',
                thumbnail: 'https://via.placeholder.com/300x169/17a2b8/ffffff?text=Node.js%E5%85%A5%E9%97%A8',
                date: '2024-01-08',
                author: '王老师',
                tags: ['Node', '后端'],
                collection: '入门合集',
                duration: '22:15',
                views: 1889
            },
            {
                id: 4,
                title: 'Vue3组件开发',
                description: '掌握Vue3最新特性，开发高质量组件...',
                thumbnail: 'https://via.placeholder.com/300x169/ffc107/000000?text=Vue3%E7%BB%84%E4%BB%B6',
                date: '2024-01-05',
                author: '赵老师',
                tags: ['Vue', '前端'],
                collection: '组件合集',
                duration: '19:30',
                views: 3456
            },
            {
                id: 5,
                title: 'CSS布局技巧',
                description: '掌握现代CSS布局技术，打造完美页面...',
                thumbnail: 'https://via.placeholder.com/300x169/dc3545/ffffff?text=CSS%E5%B8%83%E5%B1%80',
                date: '2024-01-03',
                author: '陈老师',
                tags: ['CSS', '布局'],
                collection: '样式合集',
                duration: '25:00',
                views: 2123
            },
            {
                id: 6,
                title: 'HTML5新特性',
                description: '探索HTML5的强大功能和最新特性...',
                thumbnail: 'https://via.placeholder.com/300x169/6c757d/ffffff?text=HTML5%E6%96%B0%E7%89%B9%E6%80%A7',
                date: '2024-01-01',
                author: '刘老师',
                tags: ['HTML5', '新特性'],
                collection: '基础合集',
                duration: '18:45',
                views: 1678
            },
            {
                id: 7,
                title: 'Git版本控制',
                description: '版本控制是开发者必备技能，全面掌握Git...',
                thumbnail: 'https://via.placeholder.com/300x169/fd7e14/ffffff?text=Git%E7%89%88%E6%9C%AC%E6%8E%A7%E5%88%B6',
                date: '2023-12-28',
                author: '孙老师',
                tags: ['Git', '版本控制'],
                collection: '工具合集',
                duration: '32:20',
                views: 4321
            },
            {
                id: 8,
                title: '数据库设计',
                description: '数据库设计原理与实践，构建高效数据架构...',
                thumbnail: 'https://via.placeholder.com/300x169/20c997/ffffff?text=%E6%95%B0%E6%8D%AE%E5%BA%93%E8%AE%BE%E8%AE%A1',
                date: '2023-12-25',
                author: '周老师',
                tags: ['数据库', '设计'],
                collection: '数据合集',
                duration: '45:10',
                views: 2890
            }
        ];

        // 初始化筛选结果
        this.filteredVideos = [...this.videos];
        
        // 渲染初始页面
        this.renderVideos();
        this.renderPagination();
        this.updateSearchSummary();
    }
}

// 页面加载完成后初始化
document.addEventListener('DOMContentLoaded', () => {
    new VideoListManager();
});

// 导出类以便测试
if (typeof module !== 'undefined' && module.exports) {
    module.exports = VideoListManager;
}