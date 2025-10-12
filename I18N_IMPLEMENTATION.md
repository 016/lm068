# i18n 功能实现说明文档

## 实现概览

本项目已成功实现前端视频列表页面的国际化(i18n)功能,支持中文(zh)和英文(en)两种语言,并预留了扩展接口方便未来添加其他语言。

## 实现架构

### 1. 后端PHP层

#### 核心类文件
- **`php_app/Core/I18n.php`** - 语言管理类
  - 从URL参数读取语言设置 (`?lang=zh` 或 `?lang=en`)
  - 提供语言检测和切换功能
  - 支持的语言: zh(中文), en(英文)
  - 默认语言: zh(中文)

#### Model层改造
- **`php_app/Models/Content.php`**
  - 新增 `getTitle($lang)` - 根据语言获取标题
  - 新增 `getDescription($lang)` - 根据语言获取描述
  - 新增 `getShortDescription($lang)` - 根据语言获取简介

- **`php_app/Models/Tag.php`**
  - 新增 `getName($lang)` - 根据语言获取标签名称
  - 新增 `getShortDescription($lang)` - 根据语言获取标签简介
  - 新增 `getDescription($lang)` - 根据语言获取标签描述

- **`php_app/Models/Collection.php`**
  - 新增 `getName($lang)` - 根据语言获取合集名称
  - 新增 `getShortDescription($lang)` - 根据语言获取合集简介
  - 新增 `getDescription($lang)` - 根据语言获取合集描述

#### Controller层改造
- **`php_app/Controllers/Frontend/VideoController.php`**
  - 在 `index()` 方法中获取当前语言
  - 将语言信息传递到视图层
  - 根据语言设置页面标题

### 2. 前端JavaScript层

#### 核心JS文件
- **`public_frontend/assets/js/i18n.js`** - i18n配置文件
  - 定义中文和英文的所有UI文本
  - 包含导航栏、筛选表单、分页、空状态等所有文本
  - 使用 `I18N_CONFIG` 对象存储配置

- **`public_frontend/assets/js/i18n-helper.js`** - i18n工具类
  - `I18nHelper` 类提供完整的i18n功能
  - 自动检测当前语言(优先级: URL参数 > localStorage > 默认中文)
  - `t(key, vars)` - 翻译函数,支持变量替换
  - `switchLanguage(lang)` - 切换语言并刷新页面
  - `applyTranslations()` - 自动应用翻译到页面元素
  - `preserveLangParam()` - 为表单自动添加语言参数
  - 创建全局实例 `window.i18n`

### 3. 视图层改造

#### 布局文件
- **`Views/frontend/layouts/main.php`**
  - 在 `<head>` 中传递PHP配置给JavaScript
  - 更新导航栏的语言切换按钮
  - 为导航链接添加 `data-i18n` 属性
  - 按顺序加载 i18n.js 和 i18n-helper.js

#### 视图文件
- **`Views/frontend/videos/index.php`**
  - 表单添加隐藏的 `lang` 参数保持语言设置
  - 标签和合集筛选根据语言显示对应文本
  - 视频标题、描述使用 Model 的多语言方法
  - 搜索框placeholder根据语言切换
  - 分页链接保持语言参数
  - 空状态文本支持多语言

## 使用方式

### 访问不同语言版本

```
# 访问中文版(默认)
http://www.example.com/videos

# 访问英文版
http://www.example.com/videos?lang=en

# 显式访问中文版
http://www.example.com/videos?lang=zh
```

### 切换语言

用户可以通过以下方式切换语言:

1. **顶部导航栏语言切换按钮**
   - 点击地球图标按钮
   - 选择"简体中文"或"English"
   - 页面自动刷新并切换语言

2. **URL参数切换**
   - 直接在URL中添加 `?lang=en` 或 `?lang=zh`
   - 服务端直接渲染对应语言

### SEO优化

- 每个语言版本有独立URL (`?lang=en`)
- 搜索引擎可以索引不同语言版本
- 服务端直接渲染,无需等待JavaScript执行
- 用户可以直接分享特定语言的链接

## 工作原理

### 1. 首次访问流程

```
用户访问 /videos?lang=en
    ↓
PHP读取URL参数,设置当前语言为 'en'
    ↓
Controller查询数据库,获取视频数据
    ↓
视图层渲染时调用 $video->getTitle('en')
    ↓
输出英文标题到HTML
    ↓
JavaScript加载后应用UI翻译
    ↓
用户看到完整的英文页面
```

### 2. 语言切换流程

```
用户点击语言切换按钮
    ↓
JavaScript调用 window.i18n.switchLanguage('en')
    ↓
保存语言到 localStorage
    ↓
更新URL参数: ?lang=en
    ↓
刷新页面
    ↓
重新执行首次访问流程
```

### 3. 数据库字段选择

```php
// PHP Model层
public function getTitle(?string $lang = null): string {
    $lang = $lang ?? \App\Core\I18n::getCurrentLang();
    $title = $lang === 'zh' ? $this->title_cn : $this->title_en;

    // 降级处理:如果当前语言字段为空,使用另一个语言
    if (empty($title)) {
        $title = $lang === 'zh' ? $this->title_en : $this->title_cn;
    }

    return $title ?? '';
}
```

### 4. UI元素翻译

```html
<!-- 方式1: PHP直接渲染 -->
<span class="placeholder-text">
    <?= $currentLang === 'zh' ? '请选择标签' : 'Select Tags' ?>
</span>

<!-- 方式2: JavaScript动态翻译(备用) -->
<span data-i18n="filter.tag_placeholder">请选择标签</span>
```

## 扩展新语言

如果未来需要添加日语(ja)等其他语言,只需以下几步:

### 1. 更新PHP配置

```php
// php_app/Core/I18n.php
private const SUPPORTED_LANGS = ['zh', 'en', 'ja'];  // 添加 'ja'
```

### 2. 更新JavaScript配置

```javascript
// public_frontend/assets/js/i18n.js
const I18N_CONFIG = {
    zh: { /* 中文配置 */ },
    en: { /* 英文配置 */ },
    ja: {  // 添加日语配置
        'nav.home': 'ホーム',
        'nav.videos': 'ビデオ',
        // ... 其他翻译
    }
};

// public_frontend/assets/js/i18n-helper.js
this.supportedLangs = ['zh', 'en', 'ja'];  // 添加 'ja'
```

### 3. 更新视图文件

```php
// 在需要的地方添加日语分支
<?php if ($currentLang === 'zh'): ?>
    中文文本
<?php elseif ($currentLang === 'en'): ?>
    English text
<?php elseif ($currentLang === 'ja'): ?>
    日本語テキスト
<?php endif; ?>
```

### 4. 更新布局文件

```php
// Views/frontend/layouts/main.php
<li>
    <a class="dropdown-item lang-switch-item"
       data-lang="ja"
       onclick="window.i18n.switchLanguage('ja')">
        日本語
    </a>
</li>
```

## 注意事项

### 1. 数据库字段要求

确保数据库表包含对应的多语言字段:
- `title_cn` 和 `title_en`
- `name_cn` 和 `name_en`
- `desc_cn` 和 `desc_en`
- `short_desc_cn` 和 `short_desc_en`

### 2. 语言参数保持

所有链接和表单都需要保持 `lang` 参数:
- 分页链接: `/videos?page=2&lang=en`
- 筛选链接: `/videos?tag_id=1&lang=en`
- 表单提交: 添加隐藏字段 `<input type="hidden" name="lang" value="<?= $currentLang ?>">`

### 3. JavaScript加载顺序

必须按以下顺序加载JS文件:
1. Bootstrap (依赖)
2. i18n.js (配置)
3. i18n-helper.js (工具类)
4. main.js (应用代码)
5. 页面专用JS

### 4. 降级处理

如果某个语言的字段为空,系统会自动降级到另一个语言,确保始终显示有效内容。

## 测试建议

### 1. 功能测试

- [ ] 访问 `/videos` 默认显示中文
- [ ] 访问 `/videos?lang=en` 显示英文
- [ ] 点击语言切换按钮正确切换
- [ ] 刷新页面语言保持不变
- [ ] 分页后语言保持不变
- [ ] 筛选后语言保持不变
- [ ] 表单提交后语言保持不变

### 2. 数据测试

- [ ] 视频标题根据语言显示
- [ ] 视频描述根据语言显示
- [ ] 标签名称根据语言显示
- [ ] 合集名称根据语言显示
- [ ] 空状态文本根据语言显示

### 3. UI测试

- [ ] 导航栏文本根据语言显示
- [ ] 筛选表单文本根据语言显示
- [ ] 搜索框placeholder根据语言显示
- [ ] 分页文本根据语言显示
- [ ] 语言切换按钮显示当前语言

## 已实现的功能

✅ URL参数控制语言 (`?lang=en`)
✅ 语言切换按钮
✅ localStorage记住用户语言偏好
✅ 服务端直接渲染对应语言内容
✅ 数据库内容多语言支持
✅ UI元素多语言支持
✅ SEO友好(独立URL)
✅ 分页保持语言参数
✅ 筛选保持语言参数
✅ 表单提交保持语言参数
✅ 字段为空时自动降级
✅ 预留扩展接口

## 总结

本实现采用了**双模式**i18n方案:
1. **服务端渲染** - PHP根据URL参数直接渲染对应语言的数据库内容,对SEO友好
2. **客户端翻译** - JavaScript动态替换UI元素文本,提供流畅的用户体验

这种方案既保证了首屏加载速度,又确保了搜索引擎可以索引不同语言版本,是一个完整且可扩展的i18n解决方案。
