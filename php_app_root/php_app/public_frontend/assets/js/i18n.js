// i18n 配置文件 - 国际化文本定义
// 支持中文(zh)和英文(en)两种语言

const I18N_CONFIG = {
    // 中文文本
    zh: {
        // ===== 导航栏 =====
        'nav.site_name': '视频创作',
        'nav.home': '首页',
        'nav.videos': '视频',
        'nav.about': '关于',
        'nav.login': '登录',
        'nav.register': '注册',

        // ===== 筛选表单 =====
        'filter.tag_placeholder': '请选择标签',
        'filter.collection_placeholder': '请选择合集',
        'filter.search_placeholder': '输入关键词搜索...',
        'filter.search_result': '搜索结果: 共找到',
        'filter.search_result_count': '个视频',
        'filter.selected_count': '共{count}个',
        'filter.search_btn': '搜索',

        // ===== 分页 =====
        'pagination.prev': '上一页',
        'pagination.next': '下一页',
        'pagination.info_total': '共',
        'pagination.info_videos': '个视频，当前第',
        'pagination.info_page': '页，共',
        'pagination.info_total_pages': '页',

        // ===== 空状态 =====
        'empty.title': '暂无视频',
        'empty.desc': '没有找到符合条件的视频，请尝试调整筛选条件',

        // ===== Footer =====
        'footer.navigation': '网站导航',
        'footer.home': '首页',
        'footer.video_list': '视频列表',
        'footer.user_center': '用户中心',
        'footer.favorites': '收藏夹',

        'footer.about_us': '关于我们',
        'footer.company_intro': '公司介绍',
        'footer.contact': '联系我们',
        'footer.join_us': '加入我们',
        'footer.privacy': '隐私政策',

        'footer.resources': '学习资源',
        'footer.tutorials': '编程教程',
        'footer.cases': '实战案例',
        'footer.blog': '技术博客',
        'footer.tools': '开发工具',

        'footer.subscribe': '邮件订阅',
        'footer.subscribe_desc': '加入邮件列表，获取最新视频更新和资讯',
        'footer.subscribe_placeholder': '请输入您的邮箱地址',
        'footer.subscribe_btn': '订阅',

        'footer.social': '社交媒体',
        'footer.youtube': 'YT',
        'footer.bilibili': 'Bilibili',
        'footer.douyin': '抖音',
        'footer.wechat': '微信',

        'footer.copyright': '视频创作展示网站. 保留所有权利.',
        'footer.terms': '使用条款',
        'footer.cookies': 'Cookie政策',

        // ===== 主题切换 =====
        'theme.title': '切换主题',
        'theme.dark': '深色',
        'theme.light': '浅色',
        'theme.auto': '自动',

        // ===== 悬浮按钮 =====
        'float.back_to_top': '回到顶部',
        'float.contact': '联系我们',

        // ===== 视频卡片 =====
        'video.author': '作者',
    },

    // 英文文本
    en: {
        // ===== Navigation =====
        'nav.site_name': 'Video Creation',
        'nav.home': 'Home',
        'nav.videos': 'Videos',
        'nav.about': 'About',
        'nav.login': 'Login',
        'nav.register': 'Register',

        // ===== Filter form =====
        'filter.tag_placeholder': 'Select Tags',
        'filter.collection_placeholder': 'Select Collections',
        'filter.search_placeholder': 'Search keywords...',
        'filter.search_result': 'Search Results: Found',
        'filter.search_result_count': 'videos',
        'filter.selected_count': 'Total {count}',
        'filter.search_btn': 'Search',

        // ===== Pagination =====
        'pagination.prev': 'Previous',
        'pagination.next': 'Next',
        'pagination.info_total': 'Total',
        'pagination.info_videos': 'videos, Page',
        'pagination.info_page': 'of',
        'pagination.info_total_pages': '',

        // ===== Empty state =====
        'empty.title': 'No Videos',
        'empty.desc': 'No videos found matching your criteria. Try adjusting your filters.',

        // ===== Footer =====
        'footer.navigation': 'Navigation',
        'footer.home': 'Home',
        'footer.video_list': 'Video List',
        'footer.user_center': 'User Center',
        'footer.favorites': 'Favorites',

        'footer.about_us': 'About Us',
        'footer.company_intro': 'Company',
        'footer.contact': 'Contact',
        'footer.join_us': 'Join Us',
        'footer.privacy': 'Privacy Policy',

        'footer.resources': 'Learning Resources',
        'footer.tutorials': 'Tutorials',
        'footer.cases': 'Cases',
        'footer.blog': 'Tech Blog',
        'footer.tools': 'Dev Tools',

        'footer.subscribe': 'Email Subscription',
        'footer.subscribe_desc': 'Join our mailing list for latest video updates',
        'footer.subscribe_placeholder': 'Enter your email address',
        'footer.subscribe_btn': 'Subscribe',

        'footer.social': 'Social Media',
        'footer.youtube': 'YT',
        'footer.bilibili': 'Bilibili',
        'footer.douyin': 'Douyin',
        'footer.wechat': 'WeChat',

        'footer.copyright': 'Video Creation Platform. All rights reserved.',
        'footer.terms': 'Terms of Use',
        'footer.cookies': 'Cookie Policy',

        // ===== Theme toggle =====
        'theme.title': 'Switch Theme',
        'theme.dark': 'Dark',
        'theme.light': 'Light',
        'theme.auto': 'Auto',

        // ===== Floating buttons =====
        'float.back_to_top': 'Back to Top',
        'float.contact': 'Contact Us',

        // ===== Video card =====
        'video.author': 'Author',
    }
};

// 导出配置供其他模块使用
if (typeof module !== 'undefined' && module.exports) {
    module.exports = I18N_CONFIG;
}
