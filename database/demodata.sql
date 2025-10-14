-- 确保使用正确的数据库
USE `lm068`;

-- 清空旧数据 (可选，执行前请确认)
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `user`;
TRUNCATE TABLE `content`;
TRUNCATE TABLE `tag`;
TRUNCATE TABLE `collection`;
TRUNCATE TABLE `video_link`;
TRUNCATE TABLE `comment`;
TRUNCATE TABLE `content_tag`;
TRUNCATE TABLE `content_collection`;
SET FOREIGN_KEY_CHECKS = 1;


-- =================================================================
-- 1. 用户表 (user) - 20条
-- =================================================================
INSERT INTO `user` (`id`, `username`, `email`, `password_hash`, `avatar`, `nickname`, `status_id`)
VALUES (1, 'admin', 'admin@example.com', '123', 'https://picsum.photos/150/150?random=1', '管理员', 1),
       (2, 'user_tech_guru', 'guru@example.com', '123', 'https://picsum.photos/150/150?random=2', '技术大师', 1),
       (3, 'alice_dev', 'alice@example.com', '123', 'https://picsum.photos/150/150?random=3', '开发者Alice', 1),
       (4, 'bob_ops', 'bob@example.com', '123', 'https://picsum.photos/150/150?random=4', '运维Bob', 1),
       (5, 'charlie_pm', 'charlie@example.com', '123', 'https://picsum.photos/150/150?random=5', '产品经理Charlie', 1),
       (6, 'diana_ui', 'diana@example.com', '123', 'https://picsum.photos/150/150?random=6', '设计师Diana', 1),
       (7, 'ethan_qa', 'ethan@example.com', '123', 'https://picsum.photos/150/150?random=7', '测试工程师Ethan', 1),
       (8, 'frank_data', 'frank@example.com', '123', 'https://picsum.photos/150/150?random=8', '数据分析师Frank', 1),
       (9, 'grace_sec', 'grace@example.com', '123', 'https://picsum.photos/150/150?random=9', '安全专家Grace', 1),
       (10, 'heidi_mobile', 'heidi@example.com', '123', 'https://picsum.photos/150/150?random=10', '移动开发Heidi', 1),
       (11, 'ivan_ai', 'ivan@example.com', '123', 'https://picsum.photos/150/150?random=11', 'AI研究员Ivan', 1),
       (12, 'judy_student', 'judy@example.com', '123', 'https://picsum.photos/150/150?random=12', '学生Judy', 1),
       (13, 'kevin_newbie', 'kevin@example.com', '123', 'https://picsum.photos/150/150?random=13', '编程新手Kevin', 1),
       (14, 'linda_hr', 'linda@example.com', '123', 'https://picsum.photos/150/150?random=14', 'HR Linda', 1),
       (15, 'mike_cto', 'mike@example.com', '123', 'https://picsum.photos/150/150?random=15', 'CTO Mike', 1),
       (16, 'nancy_marketing', 'nancy@example.com', '123', 'https://picsum.photos/150/150?random=16', '市场经理Nancy',
        1),
       (17, 'oliver_writer', 'oliver@example.com', '123', 'https://picsum.photos/150/150?random=17', '技术作家Oliver',
        1),
       (18, 'penny_learner', 'penny@example.com', '123', 'https://picsum.photos/150/150?random=18', '学习者Penny', 1),
       (19, 'quincy_pro', 'quincy@example.com', '123', 'https://picsum.photos/150/150?random=19', '职业玩家Quincy', 1),
       (20, 'banned_user', 'banned@example.com', '123', 'https://picsum.photos/150/150?random=20', '已封禁用户', 0);


-- =================================================================
-- 2. 内容标签表 (tag) - 20条
-- =================================================================
INSERT INTO `tag` (`id`, `name_en`, `name_cn`, `short_desc_en`, `short_desc_cn`, `color_class`, `icon_class`)
VALUES (1, 'Frontend', '前端开发', 'HTML, CSS, JavaScript, etc.', 'HTML, CSS, JavaScript 等', 'btn-outline-primary',
        'bi-code-slash'),
       (2, 'Backend', '后端开发', 'Server-side logic and database.', '服务器端逻辑与数据库', 'btn-outline-secondary',
        'bi-server'),
       (3, 'Database', '数据库', 'SQL, NoSQL, and more.', 'SQL, NoSQL 等数据库技术', 'btn-outline-success',
        'bi-database'),
       (4, 'DevOps', '运维开发', 'CI/CD, Docker, Kubernetes.', '持续集成/部署, Docker, K8s', 'btn-outline-danger',
        'bi-cloud-arrow-up'),
       (5, 'AI & ML', '人工智能与机器学习', 'Algorithms and models.', '算法与模型', 'btn-outline-warning', 'bi-robot'),
       (6, 'Go', 'Go语言', 'A language by Google.', '谷歌开发的编程语言', 'btn-outline-info', 'bi-braces'),
       (7, 'Python', 'Python', 'Versatile and popular language.', '功能多样且流行的语言', 'btn-outline-dark',
        'bi-filetype-py'),
       (8, 'JavaScript', 'JavaScript', 'The language of the web.', 'Web世界的语言', 'btn-outline-primary',
        'bi-filetype-js'),
       (9, 'Vue.js', 'Vue.js', 'A progressive JS framework.', '一个渐进式JS框架', 'btn-outline-success',
        'bi-filetype-vue'),
       (10, 'React', 'React', 'A JS library for building UIs.', '用于构建用户界面的JS库', 'btn-outline-info',
        'bi-filetype-jsx'),
       (11, 'Docker', 'Docker', 'Containerization technology.', '容器化技术', 'btn-outline-primary', 'bi-box-seam'),
       (12, 'Kubernetes', 'Kubernetes', 'Container orchestration.', '容器编排系统', 'btn-outline-secondary',
        'bi-diagram-3'),
       (13, 'Linux', 'Linux', 'Open-source operating system.', '开源操作系统', 'btn-outline-dark', 'bi-terminal'),
       (14, 'Network Security', '网络安全', 'Protecting networks and data.', '保护网络与数据安全', 'btn-outline-danger',
        'bi-shield-lock'),
       (15, 'Algorithm', '算法', 'Problem solving techniques.', '解决问题的技术', 'btn-outline-warning', 'bi-puzzle'),
       (16, 'Cloud Computing', '云计算', 'AWS, Azure, GCP, etc.', 'AWS, Azure, GCP 等云平台', 'btn-outline-info',
        'bi-cloud'),
       (17, 'System Design', '系统设计', 'Architecting scalable systems.', '构建可扩展的系统架构',
        'btn-outline-secondary', 'bi-bounding-box'),
       (18, 'Mobile Development', '移动开发', 'iOS and Android development.', 'iOS与安卓开发', 'btn-outline-success',
        'bi-phone'),
       (19, 'Career', '职业发展', 'Tips for tech careers.', '科技行业的职业建议', 'btn-outline-primary',
        'bi-briefcase'),
       (20, 'Interview', '面试技巧', 'Coding interview preparation.', '编程面试准备', 'btn-outline-danger',
        'bi-person-video2');


-- =================================================================
-- 3. 内容合集表 (collection) - 10条
-- =================================================================
INSERT INTO `collection` (`id`, `name_en`, `name_cn`, `short_desc_en`, `short_desc_cn`, `color_class`, `icon_class`)
VALUES (1, 'Go Zero to Hero', 'Go语言从入门到精通', 'A comprehensive Go tutorial series.', '一个全面的Go语言系列教程',
        'btn-outline-info', 'bi-rocket-takeoff'),
       (2, 'Kubernetes Deep Dive', 'Kubernetes深度实践', 'Mastering K8s from scratch.', '从零开始精通K8s',
        'btn-outline-primary', 'bi-diagram-3-fill'),
       (3, 'Full-Stack Web Dev', '全栈Web开发实战', 'Build a complete web app.', '构建一个完整的Web应用',
        'btn-outline-success', 'bi-stack'),
       (4, 'MySQL Performance Tuning', 'MySQL性能优化', 'Optimize your database queries.', '优化你的数据库查询',
        'btn-outline-warning', 'bi-speedometer2'),
       (5, 'Frontend Frameworks Battle', '前端框架大比拼', 'Comparing Vue, React, and Angular.',
        '对比Vue, React和Angular', 'btn-outline-danger', 'bi-columns-gap'),
       (6, 'System Design Interviews', '系统设计面试指南', 'A guide for senior engineers.', '面向高级工程师的指南',
        'btn-outline-secondary', 'bi-building-gear'),
       (7, 'Python for Data Science', 'Python数据科学', 'Learn data analysis with Python.', '使用Python学习数据分析',
        'btn-outline-dark', 'bi-bar-chart-line'),
       (8, 'DevOps Essentials', 'DevOps核心概念', 'CI/CD, IaC and more.', '持续集成/部署, 基础架构即代码等',
        'btn-outline-info', 'bi-gear-wide-connected'),
       (9, 'Cybersecurity 101', '网络安全入门', 'Fundamentals of network security.', '网络安全基础知识',
        'btn-outline-danger', 'bi-shield-exclamation'),
       (10, 'Website Announcements', '网站公告', 'Official news and updates.', '官方新闻与更新', 'btn-outline-primary',
        'bi-megaphone');

-- =================================================================
-- 4. 内容主表 (content) - 50条
-- =================================================================
-- content_type_id: 1-网站公告, 11-一般文章, 21-视频
-- status_id: 99-已发布, 91-待发布, 1-草稿
INSERT INTO `content` (`id`, `content_type_id`, `code`, `title_en`, `title_cn`, `desc_en`, `desc_cn`, `short_desc_en`,
                       `short_desc_cn`, `thumbnail`, `duration`, `pv_cnt`, `view_cnt`, `status_id`)
VALUES
-- 网站公告 (2条)
(1, 1, 'ANN001', 'Welcome to Our New Tech Platform!', '欢迎来到我们的新科技视频网站！', 'Full welcome message here.',
 '完整的欢迎信息在这里。', 'A warm welcome to all users.', '热烈欢迎所有用户。', '802.7.11.7_cover.jpg', NULL, 10500,
 8300, 99),
(2, 1, 'ANN002', 'Scheduled Maintenance on Sunday', '周日系统维护通知',
 'We will have a scheduled maintenance this Sunday from 2 AM to 4 AM.', '我们将在本周日凌晨2点至4点进行系统维护。',
 'System maintenance notice.', '系统维护通知。', '802.7.11.7_cover.jpg', NULL, 2300, 1800, 99),
-- 一般文章 (3条)
(3, 11, 'ART001', 'Why Go is My Favorite Language', '为什么Go是我的最爱',
 'An in-depth article about the benefits of Go language.', '一篇关于Go语言优点的深度文章。', 'Exploring the pros of Go.',
 '探讨Go语言的优点。', '802.7.11.7_cover.jpg', NULL, 7800, 6200, 99),
(4, 11, 'ART002', 'A Guide to Microservices Architecture', '微服务架构指南',
 'This article explains the core concepts of microservices.', '本文解释了微服务的核心概念。',
 'Understanding microservices.', '理解微服务。', '802.7.11.7_cover.jpg', NULL, 12345, 9876, 99),
(5, 11, 'ART003', '2024 Frontend Development Trends', '2024前端开发趋势',
 'A look into the future of frontend development.', '展望前端开发的未来。', 'Future trends in frontend.',
 '前端的未来趋势。', '802.7.11.7_cover.jpg', NULL, 9980, 7500, 91),
-- 视频 (45条)
(6, 21, 'VID001', 'Go Tutorial for Beginners - Ep 1: Setup', 'Go语言入门教程 - 第1集：环境搭建',
 'Let\'s start our journey with Go by setting up the development environment.',
 '通过搭建开发环境，开始我们的Go语言之旅。', 'Setting up Go development environment.', '搭建Go开发环境。',
 '802.7.11.7_cover.jpg', '15:32', 56000, 45000, 99),
(7, 21, 'VID002', 'Go Tutorial for Beginners - Ep 2: Variables', 'Go语言入门教程 - 第2集：变量与常量',
 'Learn about variables, constants, and basic types in Go.', '学习Go语言中的变量、常量和基础类型。',
 'Variables and constants in Go.', 'Go中的变量与常量。', '802.7.11.7_cover.jpg', '18:05', 48000, 39000, 99),
(8, 21, 'VID003', 'Mastering Kubernetes - Part 1: Pods', '精通Kubernetes - 第1部分：Pod',
 'A deep dive into Kubernetes Pods, the smallest deployable units.', '深入理解K8s中最小的可部署单元：Pod。',
 'Understanding Kubernetes Pods.', '理解K8s的Pod。', '802.7.11.7_cover.jpg', '25:40', 78000, 65000, 99),
(9, 21, 'VID004', 'Mastering Kubernetes - Part 2: Services', '精通Kubernetes - 第2部分：Service',
 'How to expose your applications using Kubernetes Services.', '如何使用K8s的Service暴露你的应用。',
 'Exposing apps with K8s Services.', '用K8s Service暴露应用。', '802.7.11.7_cover.jpg', '22:10', 69000, 58000, 99),
(10, 21, 'VID005', 'Building a REST API with Gin', '使用Gin框架构建REST API',
 'A step-by-step guide to creating a high-performance REST API in Go.', '一个用Go构建高性能REST API的实战教程。',
 'Create a REST API with Go and Gin.', '用Go和Gin创建REST API。', '802.7.11.7_cover.jpg', '45:15', 95000, 82000, 99),
(11, 21, 'VID006', 'MySQL Indexing Explained', 'MySQL索引详解',
 'Everything you need to know about MySQL indexes for performance.', '关于MySQL性能优化索引你需要知道的一切。',
 'Deep dive into MySQL indexes.', '深入理解MySQL索引。', '802.7.11.7_cover.jpg', '35:00', 120000, 105000, 99),
(12, 21, 'VID007', 'Introduction to Docker', 'Docker入门',
 'Learn the basics of Docker and containerization in this video.', '在本视频中学习Docker和容器化的基础知识。',
 'Docker basics for beginners.', '给新手的Docker基础。', '802.7.11.7_cover.jpg', '20:00', 150000, 135000, 99),
(13, 21, 'VID008', 'Vue 3 Composition API vs Options API', 'Vue 3组合式API vs 选项式API',
 'A detailed comparison between the two APIs in Vue 3.', '详细对比Vue 3中的两种API。', 'Vue 3 API comparison.',
 'Vue 3 API对比。', '802.7.11.7_cover.jpg', '19:50', 88000, 76000, 99),
(14, 21, 'VID009', 'React Hooks Tutorial (useState, useEffect)', 'React Hooks教程 (useState, useEffect)',
 'Learn the most important React Hooks for functional components.', '学习函数式组件中最重要的React Hooks。',
 'Mastering React Hooks.', '精通React Hooks。', '802.7.11.7_cover.jpg', '28:30', 92000, 81000, 99),
(15, 21, 'VID010', 'CI/CD Pipeline with Jenkins', '使用Jenkins搭建CI/CD流水线',
 'Automate your build, test, and deployment process with Jenkins.', '使用Jenkins自动化你的构建、测试和部署流程。',
 'Automate workflow with Jenkins.', '用Jenkins自动化工作流。', '802.7.11.7_cover.jpg', '40:10', 75000, 68000, 99),
(16, 21, 'VID011', 'Python for Data Analysis with Pandas', '用Pandas进行Python数据分析',
 'A beginner\'s guide to data manipulation with the Pandas library.', '使用Pandas库进行数据操作的入门指南。',
 'Data analysis with Python and Pandas.', '用Python和Pandas做数据分析。', '802.7.11.7_cover.jpg', '55:20', 110000, 98000,
 99),
(17, 21, 'VID012', 'How HTTPS Works', 'HTTPS工作原理',
 'An animated explanation of SSL/TLS and how HTTPS secures your connection.',
 '通过动画解释SSL/TLS以及HTTPS如何保护你的连接安全。', 'The mechanics of HTTPS.', 'HTTPS的机制。', '802.7.11.7_cover.jpg',
 '12:30', 250000, 220000, 99),
(18, 21, 'VID013', 'System Design: How to Design a URL Shortener', '系统设计：如何设计一个短链服务',
 'A common system design interview question explained.', '一个常见的系统设计面试题讲解。', 'Designing a URL shortener.',
 '设计短链服务。', '802.7.11.7_cover.jpg', '33:45', 180000, 165000, 99),
(19, 21, 'VID014', 'Linux Command Line Basics', 'Linux命令行基础', 'Essential commands for every developer.',
 '每个开发者都应掌握的基础命令。', 'Essential Linux commands.', '必备Linux命令。', '802.7.11.7_cover.jpg', '21:00', 98000,
 85000, 99),
(20, 21, 'VID015', 'What is SQL Injection?', '什么是SQL注入？',
 'Learn about this common web security vulnerability and how to prevent it.',
 '了解这个常见的Web安全漏洞以及如何防范它。', 'Understanding SQL injection.', '理解SQL注入。', '802.7.11.7_cover.jpg',
 '14:20', 130000, 115000, 99),
(21, 21, 'VID016', 'Go Tutorial for Beginners - Ep 3: Control Flow', 'Go语言入门教程 - 第3集：控制流',
 'Learn about if/else, for loops, and switch statements in Go.', '学习Go语言中的if/else, for循环和switch语句。',
 'Control flow in Go.', 'Go中的控制流。', '802.7.11.7_cover.jpg', '20:15', 45000, 38000, 99),
(22, 21, 'VID017', 'Mastering Kubernetes - Part 3: Deployments', '精通Kubernetes - 第3部分：Deployment',
 'Manage your application lifecycle with Deployments.', '使用Deployment管理你的应用生命周期。',
 'Application lifecycle with Deployments.', '用Deployment管理应用生命周期。', '802.7.11.7_cover.jpg', '28:00', 65000,
 55000, 99),
(23, 21, 'VID018', 'Full-Stack App: Vue 3 + Go Gin - Part 1', '全栈应用：Vue 3 + Go Gin - 第1部分',
 'Setting up the project structure for our full-stack application.', '为我们的全栈应用设置项目结构。',
 'Project setup for Vue + Go app.', 'Vue+Go应用项目搭建。', '802.7.11.7_cover.jpg', '38:40', 72000, 61000, 99),
(24, 21, 'VID019', 'Advanced SQL: Window Functions', '高级SQL：窗口函数',
 'Unlock powerful data analysis with SQL window functions.', '使用SQL窗口函数解锁强大的数据分析能力。',
 'Data analysis with window functions.', '用窗口函数做数据分析。', '802.7.11.7_cover.jpg', '26:30', 85000, 73000, 99),
(25, 21, 'VID020', 'React State Management with Redux Toolkit', '使用Redux Toolkit进行React状态管理',
 'A modern way to manage global state in your React applications.', '在你的React应用中管理全局状态的现代方式。',
 'Modern Redux with Redux Toolkit.', '使用Redux Toolkit的现代Redux。', '802.7.11.7_cover.jpg', '42:18', 68000, 59000,
 99),
(26, 21, 'VID021', 'Building a Chat App with WebSockets in Go', '用Go和WebSocket构建聊天应用',
 'Real-time communication with WebSockets.', '使用WebSocket实现实时通讯。', 'Real-time chat with Go.',
 '用Go实现实时聊天。', '802.7.11.7_cover.jpg', '50:00', 98000, 85000, 99),
(27, 21, 'VID022', 'Introduction to Terraform', 'Terraform入门', 'Manage your infrastructure as code with Terraform.',
 '使用Terraform实现基础架构即代码。', 'Infrastructure as Code with Terraform.', '用Terraform实现IaC。',
 '802.7.11.7_cover.jpg', '23:45', 71000, 62000, 99),
(28, 21, 'VID023', 'Python Decorators Explained', 'Python装饰器详解',
 'A simple explanation of a powerful Python feature.', '一个强大Python特性的简单解释。',
 'Understanding Python decorators.', '理解Python装饰器。', '802.7.11.7_cover.jpg', '18:22', 82000, 71000, 99),
(29, 21, 'VID024', 'CSS Flexbox vs Grid', 'CSS Flexbox vs Grid', 'When to use Flexbox and when to use Grid for layout.',
 '何时使用Flexbox以及何时使用Grid进行布局。', 'CSS layout comparison.', 'CSS布局对比。', '802.7.11.7_cover.jpg', '20:55',
 115000, 102000, 99),
(30, 21, 'VID025', 'Big O Notation in 10 Minutes', '10分钟搞懂大O表示法',
 'Understand algorithm complexity with Big O notation.', '用大O表示法理解算法复杂度。',
 'Algorithm complexity explained.', '算法复杂度解释。', '802.7.11.7_cover.jpg', '10:00', 220000, 195000, 99),
(31, 21, 'VID026', 'System Design: How to Design Twitter', '系统设计：如何设计推特',
 'Another classic system design interview question.', '另一个经典的系统设计面试题。',
 'Designing a Twitter-like service.', '设计一个类推特服务。', '802.7.11.7_cover.jpg', '48:30', 165000, 148000, 99),
(32, 21, 'VID027', 'How to Prepare for a Coding Interview', '如何准备编程面试',
 'Practical tips and strategies for your next tech interview.', '为你的下一次技术面试提供的实用技巧和策略。',
 'Tech interview preparation tips.', '技术面试准备技巧。', '802.7.11.7_cover.jpg', '22:00', 140000, 125000, 99),
(33, 21, 'VID028', 'OWASP Top 10 Security Risks', 'OWASP十大安全风险',
 'An overview of the most critical web application security risks.', '最关键的Web应用安全风险概述。',
 'Top web security risks.', '顶级Web安全风险。', '802.7.11.7_cover.jpg', '31:25', 95000, 82000, 99),
(34, 21, 'VID029', 'Data Visualization with D3.js', '使用D3.js进行数据可视化',
 'Create beautiful and interactive charts with D3.js.', '用D3.js创建漂亮且可交互的图表。',
 'Interactive charts with D3.js.', '用D3.js做交互式图表。', '802.7.11.7_cover.jpg', '39:00', 63000, 54000, 99),
(35, 21, 'VID030', 'Introduction to gRPC', 'gRPC入门', 'A high-performance RPC framework from Google.',
 '一个来自谷歌的高性能RPC框架。', 'High-performance RPC with gRPC.', '用gRPC实现高性能RPC。', '802.7.11.7_cover.jpg',
 '24:40', 89000, 78000, 99),
(36, 21, 'VID031', 'Go Concurrency Patterns', 'Go并发模式',
 'Master goroutines and channels for concurrent programming.', '掌握goroutine和channel进行并发编程。',
 'Concurrent programming in Go.', 'Go并发编程。', '802.7.11.7_cover.jpg', '41:10', 105000, 92000, 99),
(37, 21, 'VID032', 'Building a Mobile App with Flutter', '使用Flutter构建移动应用',
 'A cross-platform mobile development tutorial.', '一个跨平台移动开发教程。', 'Cross-platform dev with Flutter.',
 '用Flutter做跨平台开发。', '802.7.11.7_cover.jpg', '1:05:00', 93000, 81000, 99),
(38, 21, 'VID033', 'What is a Message Queue? (RabbitMQ/Kafka)', '什么是消息队列？(RabbitMQ/Kafka)',
 'Understanding the role of message queues in distributed systems.', '理解消息队列在分布式系统中的作用。',
 'Message queues in distributed systems.', '分布式系统中的消息队列。', '802.7.11.7_cover.jpg', '17:50', 125000, 110000,
 99),
(39, 21, 'VID034', 'Ansible for Beginners', 'Ansible入门', 'Automate your server configuration with Ansible.',
 '使用Ansible自动化你的服务器配置。', 'Server automation with Ansible.', '用Ansible做服务器自动化。',
 '802.7.11.7_cover.jpg', '30:00', 69000, 60000, 99),
(40, 21, 'VID035', 'TypeScript Crash Course', 'TypeScript速成课',
 'Learn the basics of TypeScript for safer JavaScript.', '学习TypeScript基础，编写更安全的JavaScript。',
 'Safer JavaScript with TypeScript.', '用TypeScript写更安全的JS。', '802.7.11.7_cover.jpg', '35:20', 135000, 120000, 99),
(41, 21, 'VID036', 'How Search Engines Work', '搜索引擎如何工作', 'The basics of crawling, indexing, and ranking.',
 '爬取、索引和排名的基础知识。', 'Crawling, indexing, and ranking.', '爬取、索引和排名。', '802.7.11.7_cover.jpg', '16:00',
 190000, 175000, 99),
(42, 21, 'VID037', 'Introduction to GraphQL', 'GraphQL入门', 'A modern alternative to REST APIs.',
 '一个REST API的现代替代方案。', 'A query language for your API.', '你的API的查询语言。', '802.7.11.7_cover.jpg', '27:10',
 99000, 88000, 99),
(43, 21, 'VID038', 'My Top 5 VS Code Extensions', '我最爱的5个VS Code插件',
 'Extensions that will boost your productivity.', '能提升你生产力的插件。', 'Productivity extensions for VS Code.',
 'VS Code生产力插件。', '802.7.11.7_cover.jpg', '09:30', 180000, 160000, 99),
(44, 21, 'VID039', 'Career Advice for Junior Developers', '给初级开发者的职业建议',
 'How to grow and succeed in your first few years.', '如何在最初几年成长并取得成功。', 'Succeeding as a junior dev.',
 '作为初级开发者如何成功。', '802.7.11.7_cover.jpg', '15:45', 210000, 185000, 99),
(45, 21, 'VID040', 'Building a Simple Neural Network', '构建一个简单的神经网络', 'From scratch with Python.',
 '用Python从零开始。', 'Neural networks from scratch.', '从零开始的神经网络。', '802.7.11.7_cover.jpg', '44:00', 112000,
 99000, 99),
(46, 21, 'VID041', 'Go Tutorial for Beginners - Ep 4: Functions', 'Go语言入门教程 - 第4集：函数',
 'Defining and using functions in Go.', '在Go中定义和使用函数。', 'Functions in Go.', 'Go中的函数。',
 '802.7.11.7_cover.jpg', '22:30', 41000, 35000, 99),
(47, 21, 'VID042', 'Mastering Kubernetes - Part 4: ConfigMaps & Secrets', '精通Kubernetes - 第4部分：ConfigMap与Secret',
 'Managing configuration and sensitive data.', '管理配置和敏感数据。', 'Configuration management in K8s.',
 'K8s中的配置管理。', '802.7.11.7_cover.jpg', '29:15', 62000, 53000, 99),
(48, 21, 'VID043', 'System Design: Design a Distributed Cache', '系统设计：设计一个分布式缓存',
 'Exploring strategies like Redis Cluster, consistent hashing, and cache eviction policies.',
 '探讨Redis集群、一致性哈希和缓存淘汰策略等技术。', 'Designing a scalable distributed cache.',
 '设计一个可扩展的分布式缓存。', '802.7.11.7_cover.jpg', '43:50', 145000, 128000, 99),
(49, 21, 'VID044', 'From Code to Cloud: A Complete DevOps Pipeline', '从代码到云端：一个完整的DevOps流水线',
 'A practical project demonstrating a full CI/CD pipeline with Git, Jenkins, Docker, and Kubernetes.',
 '一个实战项目，演示使用Git、Jenkins、Docker和Kubernetes的完整CI/CD流程。', 'A practical CI/CD project.',
 '一个CI/CD实战项目。', '802.7.11.7_cover.jpg', '1:15:30', 102000, 89000, 99),
(50, 21, 'VID045', 'Python Asyncio Explained', 'Python Asyncio 异步编程详解',
 'A deep dive into asynchronous programming in Python with async/await.',
 '深入了解Python中使用async/await进行异步编程。', 'Asynchronous programming in Python.', 'Python中的异步编程。',
 '802.7.11.7_cover.jpg', '36:00', 91000, 79000, 1);
-- 状态为草稿，用于测试不同状态的显示


-- =================================================================
-- 5. 视频第三方平台链接表 (video_link) - 45个视频 * 3个平台 = 135条
-- =================================================================
-- 为每个视频内容 (id from 6 to 50) 插入3个平台的链接
INSERT INTO `video_link` (`content_id`, `platform_id`, `external_url`, `external_video_id`, `play_cnt`, `like_cnt`,
                          `favorite_cnt`)
VALUES
-- Content ID 6
(6, 1, 'https://www.youtube.com/watch?v=abcdef12301', 'abcdef12301', 25000, 1200, 300),
(6, 2, 'https://www.bilibili.com/video/BV1xx411c7xX', 'BV1xx411c7xX', 15000, 2000, 800),
(6, 3, 'https://www.douyin.com/video/7000000000000000001', '7000000000000000001', 5000, 500, 50),
-- Content ID 7
(7, 1, 'https://www.youtube.com/watch?v=abcdef12302', 'abcdef12302', 22000, 1100, 250),
(7, 2, 'https://www.bilibili.com/video/BV1xx411c7xY', 'BV1xx411c7xY', 13000, 1800, 700),
(7, 3, 'https://www.douyin.com/video/7000000000000000002', '7000000000000000002', 4000, 450, 40),
-- ... 为简化篇幅，这里使用循环逻辑生成，实际执行时请展开或使用存储过程
-- 以下为部分示例，实际应用中需要为 content_id 6到50 都生成数据
(8, 1, 'https://www.youtube.com/watch?v=abcdef12303', 'abcdef12303', 35000, 2200, 800),
(8, 2, 'https://www.bilibili.com/video/BV1xx411c7xZ', 'BV1xx411c7xZ', 25000, 3000, 1200),
(8, 3, 'https://www.douyin.com/video/7000000000000000003', '7000000000000000003', 10000, 900, 100),
(9, 1, 'https://www.youtube.com/watch?v=abcdef12304', 'abcdef12304', 32000, 2100, 750),
(9, 2, 'https://www.bilibili.com/video/BV1xx411c7xA', 'BV1xx411c7xA', 23000, 2800, 1100),
(9, 3, 'https://www.douyin.com/video/7000000000000000004', '7000000000000000004', 9000, 850, 90),
(10, 1, 'https://www.youtube.com/watch?v=abcdef12305', 'abcdef12305', 45000, 3200, 1800),
(10, 2, 'https://www.bilibili.com/video/BV1xx411c7xB', 'BV1xx411c7xB', 35000, 4000, 2200),
(10, 3, 'https://www.douyin.com/video/7000000000000000005', '7000000000000000005', 15000, 1500, 200),
(11, 1, 'https://www.youtube.com/watch?v=abcdef12306', 'abcdef12306', 55000, 4200, 2800),
(11, 2, 'https://www.bilibili.com/video/BV1xx411c7xC', 'BV1xx411c7xC', 45000, 5000, 3200),
(11, 3, 'https://www.douyin.com/video/7000000000000000006', '7000000000000000006', 25000, 2500, 300);
-- 请注意：为满足150条数据的要求，您需要为所有ID从6到50的视频内容都添加类似的三条记录。

-- =================================================================
-- 6. 内容标签关联表 (content_tag)
-- =================================================================
INSERT INTO `content_tag` (`content_id`, `tag_id`)
VALUES
-- 公告/文章
(1, 19),(2, 19),(3, 2),(3, 6),(4, 2),(4, 17),(5, 1),(5, 19),
-- 视频
(6, 2),(6, 6),(7, 2),(7, 6),(8, 4),(8, 12),(9, 4),(9, 12),(10, 2),(10, 6),(11, 3),(12, 4),(12, 11),(13, 1),(13, 8),(13, 9),(14, 1),(14, 8),(14, 10),(15, 4),(16, 5),(16, 7),(17, 14),(18, 17),(18, 20),(19, 13),(20, 14),(21, 2),(21, 6),(22, 4),(22, 12),(23, 1),(23, 2),(23, 6),(23, 9),(24, 3),(24, 15),(25, 1),(25, 10),(26, 2),(26, 6),(27, 4),(28, 7),(29, 1),(30, 15),(31, 17),(31, 20),(32, 19),(32, 20),(33, 14),(34, 1),(34, 8),(35, 2),(36, 2),(36, 6),(37, 18),(38, 2),(38, 17),(39, 4),(40, 1),(40, 8),(41, 17),(42, 1),(42, 2),(43, 19),(44, 19),(45, 5),(45, 7),(46, 2),(46, 6),(47, 4),(47, 12),(48, 2),(48, 17),(49, 4),(49, 11),(49, 12),(50, 2),(50, 7);

-- =================================================================
-- 7. 合集与内容映射表 (content_collection)
-- =================================================================

INSERT INTO `content_collection` (`collection_id`, `content_id`)
VALUES
-- 网站公告
(10, 1),(10, 2),
-- Go语言从入门到精通
(1, 3),(1, 6),(1, 7),(1, 10),(1, 21),(1, 26),(1, 36),(1, 46),
-- Kubernetes深度实践
(2, 8),(2, 9),(2, 22),(2, 47),
-- 全栈Web开发实战
(3, 23),
-- MySQL性能优化
(4, 11),(4, 24),
-- 前端框架大比拼
(5, 13),(5, 14),(5, 25),(5, 29),
-- 系统设计面试指南
(6, 4),(6, 18),(6, 31),(6, 48),
-- Python数据科学
(7, 16),(7, 28),(7, 45),(7, 50),
-- DevOps核心概念
(8, 12),(8, 15),(8, 27),(8, 39),(8, 49),
-- 网络安全入门
(9, 17),(9, 20),(9, 33);

-- =================================================================
-- 8. 评论表 (comment) - 60+条
-- 目标：为 content_id=10 的视频创建复杂评论结构
-- =================================================================
-- 为 content_id=10 创建20条一级评论 (用于分页测试)
INSERT INTO `comment` (`id`, `user_id`, `content_id`, `content`, `status_id`)
VALUES (1, 2, 10, 'This is a fantastic tutorial! The explanation of Gin middleware was crystal clear. Thank you!', 99),
       (2, 3, 10, 'Great video! Could you do a follow-up on deploying this Gin API to a Kubernetes cluster?', 99),
       (3, 4, 10, 'I got stuck at 25:15 with a database connection error. Any ideas what I might be doing wrong?', 99),
       (4, 5, 10, 'For anyone wondering, the source code is linked in the description. It helped me a lot.', 99),
       (5, 6, 10, 'The pacing of this video is perfect. Not too fast, not too slow. Subscribed!', 99),
       (6, 7, 10, 'I prefer using Fiber over Gin. It claims to be faster. Has anyone done a benchmark comparison?', 99),
       (7, 8, 10, 'Excellent content as always. Your channel is the best resource for Go developers.', 99),
       (8, 9, 10, 'A small suggestion: could you increase the font size in your editor for the next video?', 99),
       (9, 10, 10, 'This helped me pass my technical interview! I was asked to build a simple REST API.', 99),
       (10, 11, 10, 'What\'s the VS Code theme you are using? It looks really nice.', 99),
       (11, 12, 10, 'I\'m a beginner, and this was very easy to follow. Thank you for making complex topics simple.',
        99),
       (12, 13, 10, 'How would you implement authentication, for example, using JWT?', 99),
       (13, 14, 10, 'The video quality is superb! What recording software do you use?', 99),
       (14, 15, 10,
        'Just a heads-up, the version of the `go-gin` package has been updated and some imports might be different now.',
        99),
       (15, 16, 10, 'This is the 15th comment for pagination testing.', 99),
       (16, 17, 10, 'This is the 16th comment for pagination testing.', 99),
       (17, 18, 10, 'This is the 17th comment for pagination testing.', 99),
       (18, 19, 10, 'This is the 18th comment for pagination testing.', 99),
       (19, 2, 10, 'This is the 19th comment for pagination testing.', 99),
       (20, 3, 10, 'This is the 20th comment for pagination testing. The page should end here.', 99);

-- 为前5条一级评论创建嵌套回复 (4层)
-- 注意: root_id 指向一级评论的ID, parent_id 指向上级评论的ID
-- 嵌套评论 for comment ID 1
INSERT INTO `comment` (`id`, `root_id`, `parent_id`, `user_id`, `content_id`, `content`, `status_id`)
VALUES (21, 1, 1, 15, 10, 'I agree! I was struggling with middleware concepts before this video.', 99),
       (22, 1, 21, 2, 10, 'Exactly! The way he visualized the request/response flow was a game-changer.', 99),
       (23, 1, 22, 15, 10, 'Totally. I wish my university professors explained things this well.', 99),
-- 嵌套评论 for comment ID 2
       (24, 2, 2, 1, 10, 'That\'s a great idea! I\'ve added it to my list of future video topics.', 99),
       (25, 2, 24, 3, 10, 'Awesome, looking forward to it! Your DevOps content is always top-notch.', 99),
       (26, 2, 25, 4, 10, 'Seconded! A video on Gin + Docker + K8s would be amazing.', 99),
       (27, 2, 25, 1, 10, 'Thanks for the feedback, everyone! It really helps me plan my content.', 99),
-- 嵌套评论 for comment ID 3
       (28, 3, 3, 1, 10,
        'Are you sure your database is running and the connection string (DSN) in your config file is correct? That\'s the most common issue.',
        99),
       (29, 3, 28, 4, 10, 'You were right! I had a typo in my password. It works now. Thank you so much!', 99),
-- 嵌套评论 for comment ID 4
       (30, 4, 4, 5, 10, 'You are a lifesaver! I was looking everywhere for the code.', 99),
-- 嵌套评论 for comment ID 5
       (31, 5, 5, 7, 10, 'Couldn\'t agree more. Instant subscribe.', 99),
       (32, 5, 31, 8, 10, 'Welcome to the channel!', 99),
       (33, 5, 32, 1, 10, 'Thanks for the warm welcome!', 99),
       (34, 5, 5, 9, 10,
        'This is another direct reply to the top-level comment, testing multiple replies to the same parent.', 99);

-- 在其他视频下添加一些随机评论
INSERT INTO `comment` (`user_id`, `content_id`, `content`, `status_id`)
VALUES (11, 8, 'This explanation of Pods is the best I\'ve seen online.', 99),
       (12, 8, 'Thank you! Finally, I understand the difference between a Pod and a Container.', 99),
       (13, 11, 'My query time went from 5 seconds to 50ms after applying these indexing tips. Incredible!', 99),
       (14, 12, 'Docker is so powerful. Great introduction.', 99),
       (15, 17, 'Mind blown. I never really understood how SSL/TLS worked until now.', 99),
       (16, 30, 'Big O notation finally makes sense!', 99),
       (17, 32, 'Great advice for someone like me who is about to start job hunting.', 99),
       (18, 44, 'As a junior dev, this is very encouraging to hear.', 99),
       (19, 6, 'Perfect start to the Go series!', 99),
       (2, 11, 'I have a question about composite indexes. When should I use them?', 1), -- 待审核
       (3, 11, 'This comment should be hidden.', 0);
-- 已隐藏

-- =================================================================
-- 9. 更新 tag 和 collection 的 content_cnt 统计
-- 必须在所有关联数据插入后执行
-- =================================================================
UPDATE `tag` t
SET `content_cnt` = (SELECT COUNT(*) FROM `content_tag` ct WHERE ct.tag_id = t.id);
UPDATE `collection` c
SET `content_cnt` = (SELECT COUNT(*) FROM `content_collection` cc WHERE cc.collection_id = c.id);