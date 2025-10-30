// i18n-helper.js - 国际化辅助工具类
// 提供语言检测、切换和翻译功能

class I18nHelper {
    constructor() {
        // 优先级: PHP后端变量 > localStorage > 默认值
        // 利用PHP在视图中输出的全局配置，这是最可靠的信息源
        this.supportedLangs = window.PHP_I18N_CONFIG?.supportedLangs || ['zh', 'en'];
        this.currentLang = this.detectLanguage();
    }

    /**
     * 检测当前语言
     * 优先级: PHP后端变量 > localStorage > 默认中文
     */
    detectLanguage() {
        // 1. 优先从PHP后端传递的全局变量读取，这是最准确的
        const phpLang = window.PHP_I18N_CONFIG?.currentLang;
        if (phpLang && this.supportedLangs.includes(phpLang)) {
            // 同步到localStorage，以便在纯前端或无后端变量的页面中使用
            localStorage.setItem('i18n_lang', phpLang);
            return phpLang;
        }

        // 2. 从localStorage读取作为备用
        const storedLang = localStorage.getItem('i18n_lang');
        if (storedLang && this.supportedLangs.includes(storedLang)) {
            return storedLang;
        }

        // 3. 默认中文
        return 'zh';
    }

    /**
     * 获取翻译文本
     * @param {string} key - 翻译键名
     * @param {object} vars - 变量替换对象 例如: {count: 5}
     * @returns {string} 翻译后的文本
     */
    t(key, vars = {}) {
        // 从配置中获取翻译文本
        let text = I18N_CONFIG[this.currentLang]?.[key];

        // 如果找不到翻译,返回键名本身
        if (!text) {
            console.warn(`[i18n] Translation key not found: ${key}`);
            return key;
        }

        // 替换变量 {count} => 实际值
        Object.keys(vars).forEach(varKey => {
            const regex = new RegExp(`\\{${varKey}\\}`, 'g');
            text = text.replace(regex, vars[varKey]);
        });

        return text;
    }

    /**
     * [修改] 切换语言
     * @param {string} lang - 目标语言代码 (zh/en)
     */
    switchLanguage(lang) {
        if (!this.supportedLangs.includes(lang) || lang === this.currentLang) {
            console.warn(`[i18n] Language not supported or already active: ${lang}`);
            return;
        }

        // 保存到localStorage，以便下次打开时记忆
        localStorage.setItem('i18n_lang', lang);

        const currentPath = window.location.pathname;
        const currentLangPrefix = `/${this.currentLang}`;
        let newPath;

        // 检查当前路径是否以旧语言代码开头
        if (currentPath.startsWith(currentLangPrefix + '/') || currentPath === currentLangPrefix) {
            // 如果是 /zh/content 或 /zh，则替换语言部分
            const basePath = currentPath.substring(currentLangPrefix.length);
            newPath = `/${lang}${basePath || '/'}`; // 如果basePath为空(原路径为/zh), 补上'/'
        } else {
            // 如果路径不含语言前缀 (例如根路径 '/' 默认显示中文内容), 则直接在前面添加
            // 确保不会出现 //content 这样的双斜杠
            newPath = `/${lang}${currentPath === '/' ? '' : currentPath}`;
        }

        // 重新构建URL，并保留原有的查询参数和哈希值
        const newUrl = `${window.location.origin}${newPath}${window.location.search}${window.location.hash}`;

        window.location.href = newUrl;
    }

    /**
     * 应用翻译到页面
     * 查找所有带 data-i18n 属性的元素并替换文本
     */
    applyTranslations() {
        // 查找所有带 data-i18n 属性的元素
        document.querySelectorAll('[data-i18n]').forEach(el => {
            const key = el.getAttribute('data-i18n');
            const vars = el.getAttribute('data-i18n-vars');

            // 解析变量
            let varObj = {};
            if (vars) {
                try {
                    varObj = JSON.parse(vars);
                } catch (e) {
                    console.warn(`[i18n] Failed to parse vars for key: ${key}`);
                }
            }

            const text = this.t(key, varObj);

            // 根据元素类型设置文本
            if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
                // 输入框设置placeholder
                if (el.hasAttribute('placeholder')) {
                    el.placeholder = text;
                }
            } else {
                // 普通元素设置textContent
                el.textContent = text;
            }
        });

        // 处理 data-i18n-placeholder 属性
        document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
            const key = el.getAttribute('data-i18n-placeholder');
            const text = this.t(key);
            el.placeholder = text;
        });

        // 处理 data-i18n-title 属性
        document.querySelectorAll('[data-i18n-title]').forEach(el => {
            const key = el.getAttribute('data-i18n-title');
            const text = this.t(key);
            el.title = text;
        });

        // 特殊处理: 更新语言切换按钮显示
        this.updateLanguageSwitcher();
    }

    /**
     * 更新语言切换按钮显示
     */
    updateLanguageSwitcher() {
        const currentLangLabel = document.getElementById('current-lang-label');
        if (currentLangLabel) {
            currentLangLabel.textContent = this.currentLang === 'zh' ? 'CN' : 'EN';
        }

        // 更新下拉菜单中的active状态
        document.querySelectorAll('.lang-switch-item').forEach(item => {
            const itemLang = item.getAttribute('data-lang');
            if (itemLang === this.currentLang) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    }

    /**
     * 获取当前语言
     * @returns {string} 当前语言代码
     */
    getCurrentLang() {
        return this.currentLang;
    }

    /**
     * 检查是否是中文
     * @returns {boolean}
     */
    isChinese() {
        return this.currentLang === 'zh';
    }

    /**
     * 检查是否是英文
     * @returns {boolean}
     */
    isEnglish() {
        return this.currentLang === 'en';
    }
}

// 创建全局实例
window.i18n = new I18nHelper();

// 页面加载完成后自动应用翻译
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        window.i18n.applyTranslations();
    });
} else {
    // DOM已经加载完成
    window.i18n.applyTranslations();
}
