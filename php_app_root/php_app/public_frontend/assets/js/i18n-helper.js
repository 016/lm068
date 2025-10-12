// i18n-helper.js - 国际化辅助工具类
// 提供语言检测、切换和翻译功能

class I18nHelper {
    constructor() {
        // 必须先初始化支持的语言列表
        this.supportedLangs = ['zh', 'en'];
        // 然后再检测当前语言
        this.currentLang = this.detectLanguage();
    }

    /**
     * 检测当前语言
     * 优先级: URL参数 > localStorage > 默认中文
     */
    detectLanguage() {
        // 优先从URL参数读取
        const urlParams = new URLSearchParams(window.location.search);
        const urlLang = urlParams.get('lang');
        if (urlLang && this.supportedLangs.includes(urlLang)) {
            // 同步到localStorage
            localStorage.setItem('i18n_lang', urlLang);
            return urlLang;
        }

        // 从localStorage读取
        const storedLang = localStorage.getItem('i18n_lang');
        if (storedLang && this.supportedLangs.includes(storedLang)) {
            return storedLang;
        }

        // 默认中文
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
     * 切换语言
     * @param {string} lang - 目标语言代码 (zh/en)
     */
    switchLanguage(lang) {
        if (!this.supportedLangs.includes(lang)) {
            console.error(`[i18n] Unsupported language: ${lang}`);
            return;
        }

        // 保存到localStorage
        localStorage.setItem('i18n_lang', lang);

        // 更新URL参数并刷新页面
        const url = new URL(window.location.href);
        url.searchParams.set('lang', lang);
        window.location.href = url.toString();
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

    /**
     * 为URL添加语言参数
     * @param {string} url - 原始URL
     * @returns {string} 添加了语言参数的URL
     */
    addLangParam(url) {
        try {
            const urlObj = new URL(url, window.location.origin);
            urlObj.searchParams.set('lang', this.currentLang);
            return urlObj.toString();
        } catch (e) {
            // 如果URL解析失败,返回原始URL
            return url;
        }
    }

    /**
     * 保持当前URL的语言参数
     * 用于表单提交、分页链接等场景
     */
    preserveLangParam() {
        const urlParams = new URLSearchParams(window.location.search);
        const currentLang = urlParams.get('lang');

        if (currentLang && this.supportedLangs.includes(currentLang)) {
            // 为所有表单添加隐藏的lang参数
            document.querySelectorAll('form').forEach(form => {
                // 检查是否已经有lang参数
                const existingLangInput = form.querySelector('input[name="lang"]');
                if (!existingLangInput) {
                    const langInput = document.createElement('input');
                    langInput.type = 'hidden';
                    langInput.name = 'lang';
                    langInput.value = currentLang;
                    form.appendChild(langInput);
                }
            });
        }
    }
}

// 创建全局实例
window.i18n = new I18nHelper();

// 页面加载完成后自动应用翻译
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        window.i18n.applyTranslations();
        window.i18n.preserveLangParam();
    });
} else {
    // DOM已经加载完成
    window.i18n.applyTranslations();
    window.i18n.preserveLangParam();
}
