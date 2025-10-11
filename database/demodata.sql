好的，作为一名专业的IT人员，我将根据您的DDL和要求，为您生成一套完整、合理且具有关联性的MySQL测试数据。

这套数据模拟了一个科技类视频网站的运营情况，包含了用户、内容（视频、文章、公告）、标签、合集、多平台链接以及评论等核心模块。数据之间有丰富的关联，例如：用户发表了评论，视频被打上了多个标签，多个视频组成了一个合集系列等。

请将以下SQL语句在您的`lm068`数据库中执行即可。

```sql
-- =================================================================
-- IT-Professional Generated Demo Data for lm068
-- 数据库: 科技类视频网站
-- =================================================================

-- 清空旧数据 (可选, 执行前请确保了解其影响)
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `user`;
TRUNCATE TABLE `tag`;
TRUNCATE TABLE `collection`;
TRUNCATE TABLE `content`;
TRUNCATE TABLE `video_link`;
TRUNCATE TABLE `comment`;
TRUNCATE TABLE `content_tag`;
TRUNCATE TABLE `content_collection`;
SET FOREIGN_KEY_CHECKS = 1;


-- 1. 插入用户数据 (20条)
-- ------------------------------------------------------------------
INSERT INTO `user` (`id`, `username`, `email`, `password_hash`, `avatar`, `nickname`, `status_id`) VALUES
(1, 'tech_guru', 'tech_guru@example.com', 'hashed_password_placeholder', 'https://i.pravatar.cc/150?u=tech_guru', '技术大师', 1),
(2, 'code_ninja', 'code_ninja@example.com', 'hashed_password_placeholder', 'https://i.pravatar.cc/150?u=code_ninja', '代码忍者', 1),
(3, 'dev_jane', 'dev_jane@example.com', 'hashed_password_placeholder', 'https://i.pravatar.cc/150?u=dev_jane', '开发者小简', 1),
(4, 'data_king', 'data_king@example.com', 'hashed_password_placeholder', 'https://i.pravatar.cc/150?u=data_king', '数据之王', 1),
(5, 'cyber_sec', 'cyber_sec@example.com', 'hashed_password_placeholder', 'https://i.pravatar.cc/150?u=cyber_sec', '网络安全员', 1),
(6, 'product_pro', 'product_pro@example.com', 'hashed_password_placeholder', 'https://i.pravatar.cc/150?u=product_pro', '产品经理Pro', 1),
(7, 'ai_explorer', 'ai_explorer@example.com', 'hashed_password_placeholder', 'https://i.pravatar.cc/150?u=ai_explorer', 'AI探索者', 1),
(8, 'cloud_master', 'cloud_master@example.com', 'hashed_password_placeholder', 'https://i.pravatar.cc/150?u=cloud_master', '云服务专家', 1),
(9, 'frontend_fan', 'frontend_fan@example.com', 'hashed_password_placeholder', 'https://i.pravatar.cc/150?u=frontend_fan', '前端爱好者', 1),
(10, 'backend_boss', 'backend_boss@example.com', 'hashed_password_placeholder', 'https://i.pravatar.cc/150?u=backend_boss', '后端大佬', 1),
(11, 'tester_tom', 'tester_tom@example.com', 'hashed_password_placeholder', 'https://i.pravatar.cc/150?u=tester_tom', '测试员汤姆', 1),
(12, 'devops_dave', 'devops_dave@example.com', 'hashed_password_placeholder', 'https://i.pravatar.cc/150?u=devops_dave', '运维戴夫', 1),
(13, 'ui_unicorn', 'ui_unicorn@example.com', 'hashed_password_placeholder', 'https://i.pravatar.cc/150?u=ui_unicorn', 'UI独角兽', 1),
(14, 'sql_sorcerer', 'sql_sorcerer@example.com', 'hashed_password_placeholder', 'https://i.pravatar.cc/150?u=sql_sorcerer', 'SQL魔法师', 1),
(15, 'algo_ace', 'algo_ace@example.com', 'hashed_password_placeholder', 'https://i.pravatar.cc/150?u=algo_ace', '算法王牌', 1),
(16, 'geek_girl', 'geek_girl@example.com', 'hashed_password_placeholder', 'https://i.pravatar.cc/150?u=geek_girl', '极客女孩', 1),
(17, 'mobile_mike', 'mobile_mike@example.com', 'hashed_password_placeholder', 'https://i.pravatar.cc/150?u=mobile_mike', '移动开发麦克', 1),
(18, 'banned_user', 'banned_user@example.com', 'hashed_password_placeholder', 'https://i.pravatar.cc/150?u=banned_user', '违规用户', 0),
(19, 'learner_lucy', 'learner_lucy@example.com', 'hashed_password_placeholder', 'https://i.pravatar.cc/150?u=learner_lucy', '学习者露西', 1),
(20, 'watcher_will', 'watcher_will@example.com', 'hashed_password_placeholder', 'https://i.pravatar.cc/150?u=watcher_will', '观察家威尔', 1);


-- 2. 插入标签数据 (20条)
-- ------------------------------------------------------------------
INSERT INTO `tag` (`id`, `name_en`, `name_cn`, `color_class`, `icon_class`) VALUES
(1, 'Python', 'Python', 'bg-blue-500', 'icon-python'),
(2, 'JavaScript', 'JavaScript', 'bg-yellow-500', 'icon-js'),
(3, 'Go', 'Go语言', 'bg-cyan-500', 'icon-go'),
(4, 'AI', '人工智能', 'bg-purple-500', 'icon-ai'),
(5, 'Machine Learning', '机器学习', 'bg-purple-600', 'icon-ml'),
(6, 'Web Development', 'Web开发', 'bg-orange-500', 'icon-web'),
(7, 'DevOps', 'DevOps', 'bg-gray-500', 'icon-devops'),
(8, 'Cloud Computing', '云计算', 'bg-sky-500', 'icon-cloud'),
(9, 'Cybersecurity', '网络安全', 'bg-red-500', 'icon-security'),
(10, 'React', 'React框架', 'bg-blue-400', 'icon-react'),
(11, 'Vue', 'Vue框架', 'bg-green-500', 'icon-vue'),
(12, 'Database', '数据库', 'bg-indigo-500', 'icon-db'),
(13, 'Hardware Review', '硬件评测', 'bg-slate-600', 'icon-hardware'),
(14, 'Tutorial', '入门教程', 'bg-emerald-500', 'icon-tutorial'),
(15, 'Project Demo', '项目实战', 'bg-rose-500', 'icon-project'),
(16, 'Tech News', '科技新闻', 'bg-amber-500', 'icon-news'),
(17, 'Career', '职业发展', 'bg-teal-500', 'icon-career'),
(18, 'Docker', 'Docker', 'bg-sky-600', 'icon-docker'),
(19, 'Kubernetes', 'Kubernetes', 'bg-blue-700', 'icon-k8s'),
(20, 'Rust', 'Rust语言', 'bg-orange-700', 'icon-rust');


-- 3. 插入合集数据 (10条)
-- ------------------------------------------------------------------
INSERT INTO `collection` (`id`, `name_en`, `name_cn`, `short_desc_cn`) VALUES
(1, 'Python from Zero to Hero', 'Python入门到精通', '一个完整的Python学习路径系列'),
(2, 'Full-Stack with React & Node', 'React全栈开发实战', '使用React和Node.js构建现代化应用'),
(3, 'AI Frontier Insights', 'AI前沿观察', '追踪人工智能领域的最新突破和趋势'),
(4, 'DevOps Best Practices', 'DevOps最佳实践', '涵盖CI/CD、监控、自动化等核心实践'),
(5, 'Hardware Unboxing & Reviews', '硬件评测系列', '最新最热的科技产品开箱与深度评测'),
(6, 'Cybersecurity Fundamentals', '网络安全基础', '为初学者介绍网络安全的核心概念和技术'),
(7, 'Go Concurrency Programming', 'Go语言并发编程', '深入探索Go语言强大的并发模型'),
(8, 'Deep Dive into Vue.js 3', 'Vue.js 3 深度解析', '全面解析Vue 3的新特性和内部原理'),
(9, 'Weekly Tech Roundup', '每周科技快讯', '每周五更新，总结本周科技界的大事件'),
(10, 'Algorithms & Data Structures', '算法与数据结构', '用动画和实例生动讲解核心算法');


-- 4. 插入内容数据 (30条)
-- 类型: 1-公告, 11-文章, 21-视频
-- 状态: 1-草稿, 91-待发布, 99-已发布
-- ------------------------------------------------------------------
INSERT INTO `content` (`id`, `content_type_id`, `title_en`, `title_cn`, `short_desc_cn`, `thumbnail`, `duration`, `pv_cnt`, `view_cnt`, `status_id`) VALUES
-- 公告 (1条)
(1, 1, 'Website v2.0 Launch Announcement', '网站v2.0版本上线公告', '我们很高兴地宣布，网站2.0版本正式上线，带来了全新的UI和功能！', 'https://example.com/thumbnails/announce_v2.jpg', NULL, 1500, 800, 99),
-- 文章 (4条)
(2, 11, 'Understanding HTTP/3 In-Depth', '深入理解HTTP/3', 'HTTP/3是下一代Web协议，本文将带你深入了解其核心QUIC协议和优势。', 'https://example.com/thumbnails/article_http3.jpg', NULL, 8000, 4500, 99),
(3, 11, 'Top 10 VSCode Extensions for 2024', '2024年最佳VSCode插件Top10', '工欲善其事，必先利其器。这10款插件将极大提升你的开发效率。', 'https://example.com/thumbnails/article_vscode.jpg', NULL, 12000, 7800, 99),
(4, 11, 'Is Rust the Future of System Programming?', 'Rust是系统编程的未来吗？', '探讨Rust语言的所有权、借用检查等特性为何让它成为C++的有力竞争者。', 'https://example.com/thumbnails/article_rust.jpg', NULL, 6500, 3200, 99),
(5, 11, 'My Career Path as a Software Engineer', '我的软件工程师职业规划之路', '分享从初级工程师到技术专家的成长心得与建议。', 'https://example.com/thumbnails/article_career.jpg', NULL, 9500, 6000, 99),
-- 视频 (25条)
(6, 21, 'Python Web Scraper Tutorial: E-commerce Data', 'Python爬虫实战：抓取电商网站数据', '手把手教你用Requests和BeautifulSoup库，从零开始编写一个Python网络爬虫。', 'https://example.com/thumbnails/video_py_scraper.jpg', '0:25:40', 55000, 28000, 99),
(7, 21, 'Building a Full-Stack App with React & Express', '用React和Express构建一个全栈应用', '项目实战课程，学习如何结合React前端与Node.js Express后端。', 'https://example.com/thumbnails/video_react_express.jpg', '1:10:22', 78000, 45000, 99),
(8, 21, 'What is GPT-4? An AI Revolution', 'GPT-4是什么？一场AI革命', '通俗易懂地解释GPT-4的工作原理及其对未来的深远影响。', 'https://example.com/thumbnails/video_gpt4.jpg', '0:18:15', 150000, 98000, 99),
(9, 21, 'CI/CD Pipeline with GitLab and Docker', '使用GitLab和Docker搭建CI/CD流水线', 'DevOps核心技能，学习如何自动化你的代码构建、测试和部署流程。', 'https://example.com/thumbnails/video_cicd.jpg', '0:45:05', 42000, 21000, 99),
(10, 21, 'Apple M3 Chip Macbook Pro In-depth Review', '苹果M3芯片Macbook Pro深度评测', '性能、功耗、游戏、生产力全方位评测，M3芯片真的值得升级吗？', 'https://example.com/thumbnails/video_m3_review.jpg', '0:22:50', 95000, 65000, 99),
(11, 21, 'SQL Injection 101: How to Hack and Defend', 'SQL注入入门：从攻击到防御', '演示常见的SQL注入攻击手法，并讲解如何在代码层面进行有效防御。', 'https://example.com/thumbnails/video_sql_inject.jpg', '0:33:18', 61000, 33000, 99),
(12, 21, 'Go Goroutines Explained Simply', 'Go并发编程：轻松理解Goroutine', '深入浅出讲解Go语言的核心并发特性——Goroutine和Channel。', 'https://example.com/thumbnails/video_goroutine.jpg', '0:28:30', 38000, 19000, 99),
(13, 21, 'Vue 3 Composition API vs Options API', 'Vue 3深度解析：Composition API vs Options API', '对比分析Vue 3两种API的优缺点及适用场景，助你写出更优雅的代码。', 'https://example.com/thumbnails/video_vue3_api.jpg', '0:21:00', 31000, 17000, 99),
(14, 21, 'Tech News Roundup: AI Chips War', '每周科技快讯：AI芯片战争升级', '本周焦点：Nvidia, AMD, Intel在AI芯片领域的最新动态和未来布局。', 'https://example.com/thumbnails/video_news_chips.jpg', '0:12:35', 25000, 15000, 99),
(15, 21, 'Animated Guide to Quick Sort Algorithm', '动画详解快速排序算法', '通过生动的动画，让你彻底搞懂经典排序算法“快速排序”的原理和实现。', 'https://example.com/thumbnails/video_quicksort.jpg', '0:15:20', 88000, 52000, 99),
(16, 21, 'Python Crash Course for Beginners', 'Python快速入门教程 (上)', '面向零基础学习者，从变量、数据类型到循环控制，快速上手Python。', 'https://example.com/thumbnails/video_py_crash_1.jpg', '0:55:10', 120000, 75000, 99),
(17, 21, 'Python Functions and Modules', 'Python快速入门教程 (下)', '深入讲解函数、模块和包，构建结构化的Python应用。', 'https://example.com/thumbnails/video_py_crash_2.jpg', '0:48:00', 90000, 58000, 99),
(18, 21, 'Building a Blog with Vue.js and Firebase', 'Vue.js 3项目：从零搭建个人博客', '一个完整的Vue项目，学习组件化开发、路由和状态管理。', 'https://example.com/thumbnails/video_vue_blog.jpg', '1:30:00', 45000, 29000, 99),
(19, 21, 'Intro to Machine Learning with Scikit-learn', '机器学习入门：使用Scikit-learn', '介绍机器学习基本概念，并使用Python的Scikit-learn库解决一个分类问题。', 'https://example.com/thumbnails/video_ml_sklearn.jpg', '0:38:45', 51000, 26000, 99),
(20, 21, 'Deploying Your App on AWS EC2', '手把手教你将应用部署到AWS EC2', '从创建实例、配置环境到上线Web应用，完整的云计算入门实践。', 'https://example.com/thumbnails/video_aws_ec2.jpg', '0:40:10', 36000, 18000, 99),
(21, 21, 'How HTTPS Works (SSL/TLS Explained)', 'HTTPS是如何工作的？(SSL/TLS证书详解)', '揭秘浏览器地址栏的小绿锁，讲解非对称加密、证书颁发机构等网络安全知识。', 'https://example.com/thumbnails/video_https.jpg', '0:19:55', 72000, 41000, 99),
(22, 21, 'Building a Real-time Chat App with Go', 'Go语言实战：构建实时聊天应用', '利用Go的并发能力和WebSocket技术，打造一个高性能的在线聊天室。', 'https://example.com/thumbnails/video_go_chat.jpg', '1:05:00', 29000, 15000, 99),
(23, 21, 'Nvidia RTX 4090 vs AMD RX 7900XTX', '显卡对决：Nvidia RTX 4090 vs AMD RX 7900XTX', '旗舰显卡的巅峰对决，游戏性能、生产力、光追表现全面对比。', 'https://example.com/thumbnails/video_gpu_battle.jpg', '0:17:30', 110000, 72000, 99),
(24, 21, 'Tech News Roundup: The Metaverse Reality', '每周科技快讯：元宇宙的现实与幻想', '探讨本周关于元宇宙的最新进展，是未来还是泡沫？', 'https://example.com/thumbnails/video_news_meta.jpg', '0:14:00', 18000, 9000, 99),
(25, 21, 'Binary Search Algorithm Explained', '算法动画：二分查找法', '在有序数组中进行高效查找的必备算法，包教包会。', 'https://example.com/thumbnails/video_binary_search.jpg', '0:09:45', 98000, 61000, 99),
(26, 21, 'Setup a Kubernetes Cluster on Local Machine', '在本地搭建Kubernetes集群 (K8s教程)', '使用Minikube或Kind，在你的电脑上快速搭建一个用于学习和测试的K8s环境。', 'https://example.com/thumbnails/video_k8s_local.jpg', '0:35:15', 33000, 16000, 99),
(27, 21, 'Understanding Docker Containers', 'Docker核心概念：容器化技术详解', '什么是容器？它和虚拟机有什么区别？本视频将为你解答。', 'https://example.com/thumbnails/video_docker_intro.jpg', '0:20:00', 65000, 39000, 99),
(28, 21, 'Create a REST API with Go and Gin', '使用Go和Gin框架创建REST API', '学习如何使用流行的Gin框架快速开发高性能的Web API。', 'https://example.com/thumbnails/video_go_gin.jpg', '0:50:30', 22000, 11000, 91),
(29, 21, 'React State Management: Context vs Redux', 'React状态管理：Context还是Redux？', '分析React内置Context API和第三方库Redux的差异，帮你做出正确的技术选型。', 'https://example.com/thumbnails/video_react_state.jpg', '0:24:00', 48000, 27000, 99),
(30, 21, '[Draft] Unboxing the new Raspberry Pi 5', '[草稿] 树莓派5开箱初体验', '这是一个草稿视频，内容还未完成。', 'https://example.com/thumbnails/video_rpi5_draft.jpg', '0:00:00', 0, 0, 1);


-- 5. 插入视频平台链接 (为每个视频内容生成3个平台的链接)
-- ------------------------------------------------------------------
-- 注意: content_id 从 6 到 30 是视频类型
INSERT INTO `video_link` (`content_id`, `platform_id`, `external_url`, `external_video_id`, `play_cnt`, `like_cnt`, `favorite_cnt`, `comment_cnt`) VALUES
-- Content ID: 6
(6, 1, 'https://www.youtube.com/watch?v=vid006A', 'vid006A', 25000, 2200, 800, 150),
(6, 2, 'https://www.bilibili.com/video/BV006A', 'BV006A', 28000, 4000, 5500, 300),
(6, 3, 'https://www.douyin.com/video/700006A', '700006A', 150000, 8000, 0, 450),
-- Content ID: 7
(7, 1, 'https://www.youtube.com/watch?v=vid007B', 'vid007B', 38000, 3500, 1200, 200),
(7, 2, 'https://www.bilibili.com/video/BV007B', 'BV007B', 40000, 6000, 7000, 400),
(7, 3, 'https://www.douyin.com/video/700007B', '700007B', 200000, 12000, 0, 600),
-- Content ID: 8
(8, 1, 'https://www.youtube.com/watch?v=vid008C', 'vid008C', 90000, 8000, 3000, 500),
(8, 2, 'https://www.bilibili.com/video/BV008C', 'BV008C', 60000, 10000, 12000, 800),
(8, 3, 'https://www.douyin.com/video/700008C', '700008C', 450000, 25000, 0, 1200),
-- ... (为简洁，此处省略了 content_id 9-28 的数据，您可以按此模式填充)
-- 简单填充剩余部分
(9, 1, 'https://www.youtube.com/watch?v=vid009D', 'vid009D', 20000, 1800, 700, 100),
(9, 2, 'https://www.bilibili.com/video/BV009D', 'BV009D', 22000, 3500, 4000, 250),
(9, 3, 'https://www.douyin.com/video/700009D', '700009D', 100000, 5000, 0, 300),
(10, 1, 'https://www.youtube.com/watch?v=vid010E', 'vid010E', 50000, 4500, 2000, 400),
(10, 2, 'https://www.bilibili.com/video/BV010E', 'BV010E', 45000, 7000, 8000, 600),
(10, 3, 'https://www.douyin.com/video/700010E', '700010E', 300000, 15000, 0, 900),
(11, 1, 'https://www.youtube.com/watch?v=vid011F', 'vid011F', 30000, 2800, 1100, 180),
(11, 2, 'https://www.bilibili.com/video/BV011F', 'BV011F', 31000, 5000, 6000, 350),
(11, 3, 'https://www.douyin.com/video/700011F', '700011F', 180000, 9000, 0, 500),
(12, 1, 'https://www.youtube.com/watch?v=vid012G', 'vid012G', 18000, 1500, 600, 90),
(12, 2, 'https://www.bilibili.com/video/BV012G', 'BV012G', 20000, 3000, 3500, 200),
(12, 3, 'https://www.douyin.com/video/700012G', '700012G', 90000, 4000, 0, 250),
(13, 1, 'https://www.youtube.com/watch?v=vid013H', 'vid013H', 15000, 1300, 500, 80),
(13, 2, 'https://www.bilibili.com/video/BV013H', 'BV013H', 16000, 2500, 3000, 180),
(13, 3, 'https://www.douyin.com/video/700013H', '700013H', 80000, 3500, 0, 220),
(14, 1, 'https://www.youtube.com/watch?v=vid014I', 'vid014I', 12000, 1000, 400, 70),
(14, 2, 'https://www.bilibili.com/video/BV014I', 'BV014I', 13000, 2000, 2500, 150),
(14, 3, 'https://www.douyin.com/video/700014I', '700014I', 70000, 3000, 0, 200),
(15, 1, 'https://www.youtube.com/watch?v=vid015J', 'vid015J', 40000, 3800, 1500, 250),
(15, 2, 'https://www.bilibili.com/video/BV015J', 'BV015J', 48000, 6500, 7500, 450),
(15, 3, 'https://www.douyin.com/video/700015J', '700015J', 250000, 13000, 0, 700),
(16, 1, 'https://www.youtube.com/watch?v=vid016K', 'vid016K', 60000, 5500, 2500, 450),
(16, 2, 'https://www.bilibili.com/video/BV016K', 'BV016K', 60000, 8000, 9000, 650),
(16, 3, 'https://www.douyin.com/video/700016K', '700016K', 350000, 18000, 0, 1000),
(17, 1, 'https://www.youtube.com/watch?v=vid017L', 'vid017L', 45000, 4000, 1800, 350),
(17, 2, 'https://www.bilibili.com/video/BV017L', 'BV017L', 45000, 6000, 7000, 550),
(17, 3, 'https://www.douyin.com/video/700017L', '700017L', 280000, 14000, 0, 800),
(18, 1, 'https://www.youtube.com/watch?v=vid018M', 'vid018M', 22000, 2000, 900, 160),
(18, 2, 'https://www.bilibili.com/video/BV018M', 'BV018M', 23000, 3800, 4500, 280),
(18, 3, 'https://www.douyin.com/video/700018M', '700018M', 120000, 6000, 0, 350),
(19, 1, 'https://www.youtube.com/watch?v=vid019N', 'vid019N', 25000, 2300, 1000, 170),
(19, 2, 'https://www.bilibili.com/video/BV019N', 'BV019N', 26000, 4200, 5000, 300),
(19, 3, 'https://www.douyin.com/video/700019N', '700019N', 140000, 7000, 0, 400),
(20, 1, 'https://www.youtube.com/watch?v=vid020O', 'vid020O', 17000, 1600, 700, 110),
(20, 2, 'https://www.bilibili.com/video/BV020O', 'BV020O', 19000, 3000, 3500, 220),
(20, 3, 'https://www.douyin.com/video/700020O', '700020O', 100000, 5000, 0, 300),
(21, 1, 'https://www.youtube.com/watch?v=vid021P', 'vid021P', 35000, 3200, 1300, 220),
(21, 2, 'https://www.bilibili.com/video/BV021P', 'BV021P', 37000, 5500, 6500, 400),
(21, 3, 'https://www.douyin.com/video/700021P', '700021P', 200000, 10000, 0, 550),
(22, 1, 'https://www.youtube.com/watch?v=vid022Q', 'vid022Q', 14000, 1200, 500, 90),
(22, 2, 'https://www.bilibili.com/video/BV022Q', 'BV022Q', 15000, 2500, 3000, 180),
(22, 3, 'https://www.douyin.com/video/700022Q', '700022Q', 80000, 4000, 0, 250),
(23, 1, 'https://www.youtube.com/watch?v=vid023R', 'vid023R', 55000, 5000, 2200, 450),
(23, 2, 'https://www.bilibili.com/video/BV023R', 'BV023R', 55000, 7500, 8500, 650),
(23, 3, 'https://www.douyin.com/video/700023R', '700023R', 320000, 16000, 0, 1000),
(24, 1, 'https://www.youtube.com/watch?v=vid024S', 'vid024S', 9000, 800, 300, 60),
(24, 2, 'https://www.bilibili.com/video/BV024S', 'BV024S', 9000, 1500, 2000, 120),
(24, 3, 'https://www.douyin.com/video/700024S', '700024S', 50000, 2500, 0, 150),
(25, 1, 'https://www.youtube.com/watch?v=vid025T', 'vid025T', 48000, 4500, 1900, 300),
(25, 2, 'https://www.bilibili.com/video/BV025T', 'BV025T', 50000, 7000, 8000, 500),
(25, 3, 'https://www.douyin.com/video/700025T', '700025T', 280000, 14000, 0, 800),
(26, 1, 'https://www.youtube.com/watch?v=vid026U', 'vid026U', 16000, 1400, 600, 100),
(26, 2, 'https://www.bilibili.com/video/BV026U', 'BV026U', 17000, 2800, 3200, 200),
(26, 3, 'https://www.douyin.com/video/700026U', '700026U', 90000, 4500, 0, 280),
(27, 1, 'https://www.youtube.com/watch?v=vid027V', 'vid027V', 32000, 3000, 1200, 200),
(27, 2, 'https://www.bilibili.com/video/BV027V', 'BV027V', 33000, 5000, 6000, 380),
(27, 3, 'https://www.douyin.com/video/700027V', '700027V', 180000, 9000, 0, 500),
(28, 1, 'https://www.youtube.com/watch?v=vid028W', 'vid028W', 10000, 900, 400, 80),
(28, 2, 'https://www.bilibili.com/video/BV028W', 'BV028W', 12000, 2000, 2500, 150),
(28, 3, 'https://www.douyin.com/video/700028W', '700028W', 60000, 3000, 0, 180),
(29, 1, 'https://www.youtube.com/watch?v=vid029X', 'vid029X', 23000, 2100, 900, 160),
(29, 2, 'https://www.bilibili.com/video/BV029X', 'BV029X', 25000, 4000, 4800, 280),
(29, 3, 'https://www.douyin.com/video/700029X', '700029X', 130000, 6500, 0, 380);

-- 6. 插入评论数据 (30条, 包含回复)
-- status_id: 1-待审核, 99-审核通过
-- ------------------------------------------------------------------
INSERT INTO `comment` (`id`, `root_id`, `parent_id`, `user_id`, `content_id`, `content`, `status_id`) VALUES
-- 根评论
(1, NULL, NULL, 2, 6, '讲得太好了，思路清晰，感谢UP主！', 99),
(2, NULL, NULL, 9, 6, '跟着做了一遍，成功了！期待更多这样的教程。', 99),
(3, NULL, NULL, 4, 8, '震撼！AI的发展速度真的超乎想象。', 99),
(4, NULL, NULL, 10, 7, '这个项目对我启发很大，正好在学React和Node。', 99),
(5, NULL, NULL, 13, 10, '评测非常客观，数据详实，已三连！', 99),
(6, NULL, NULL, 5, 11, '网络安全教育真的太重要了，支持！', 99),
(7, NULL, NULL, 15, 15, '这个动画做得太棒了，秒懂快排！', 99),
(8, NULL, NULL, 1, 16, '非常适合新手的Python教程，已收藏。', 99),
(9, NULL, NULL, 17, 20, '部署到云服务器一直是我头疼的问题，这个视频帮大忙了。', 99),
(10, NULL, NULL, 12, 9, '作为DevOps工程师，感觉这个流程很实用。', 99),
(11, NULL, NULL, 3, 2, 'HTTP/3确实是未来趋势，学习了。', 99),
(12, NULL, NULL, 8, 20, '除了AWS，后续能出一些Azure或者GCP的教程吗？', 99),
(13, NULL, NULL, 19, 17, '老师，Python面向对象什么时候讲呀？', 99),
(14, NULL, NULL, 7, 19, '很好的机器学习入门视频！', 99),
(15, NULL, NULL, 16, 23, '4090性能还是猛啊，就是价格...看看就好。', 99),
(16, NULL, NULL, 1, 3, '里面推荐的GitLens插件我一直在用，神器！', 99),
(17, NULL, NULL, 20, 21, '原来HTTPS是这么回事，涨知识了。', 99),
(18, NULL, NULL, 14, 11, '请问，如何防御二阶注入呢？', 99),
(19, NULL, NULL, 6, 4, 'Rust的学习曲线确实陡峭，但内存安全太香了。', 99),
(20, NULL, NULL, 11, 29, '对于中小型项目，感觉Context API已经够用了。', 99),
-- 回复
(21, 1, 1, 1, 6, '不客气，大家喜欢就好！', 99),
(22, 3, 3, 7, 8, '是啊，我们正处在一个伟大的变革时代。', 99),
(23, 5, 5, 1, 10, '感谢支持！M3系列确实是生产力工具的又一次飞跃。', 99),
(24, 7, 7, 15, 15, '同意，好算法配好动画，学习效率加倍！', 99),
(25, 13, 13, 1, 17, '面向对象部分已经在计划中了，下下期更新！', 99),
(26, 18, 18, 5, 11, '二阶注入通常需要对所有外部输入做严格的清理和验证，并且使用参数化查询。单纯的转义可能不够。', 99),
(27, 20, 20, 9, 29, '是的，技术选型要看场景，不能一概而论。Redux在大规模复杂应用中还是有它的优势。', 99),
(28, 1, 2, 19, 6, '附议，老师的教程是我看过最清晰的。', 99),
(29, 18, 26, 14, 11, '明白了，多谢解答！', 99),
(30, NULL, NULL, 2, 28, '这个视频什么时候发布呀，好期待！', 1); -- 待审核评论


-- 7. 插入内容-标签关联数据
-- ------------------------------------------------------------------
INSERT INTO `content_tag` (`content_id`, `tag_id`) VALUES
(2, 6), (2, 9), -- HTTP/3: Web开发, 网络安全
(3, 6), (3, 17), -- VSCode: Web开发, 职业发展
(4, 20), (4, 17), -- Rust: Rust, 职业发展
(5, 17), -- Career Path: 职业发展
(6, 1), (6, 14), (6, 15), -- Python Scraper: Python, 教程, 实战
(7, 2), (7, 6), (7, 10), (7, 15), -- React Full-Stack: JS, Web, React, 实战
(8, 4), (8, 5), (8, 16), -- GPT-4: AI, ML, 新闻
(9, 7), (9, 18), (9, 19), -- CI/CD: DevOps, Docker, K8s
(10, 13), (10, 16), -- M3 Review: 硬件评测, 新闻
(11, 9), (11, 12), (11, 14), -- SQL Injection: 安全, 数据库, 教程
(12, 3), (12, 14), -- Goroutines: Go, 教程
(13, 11), (13, 6), -- Vue API: Vue, Web
(14, 16), (14, 4), -- Tech News AI: 新闻, AI
(15, 15), -- Quick Sort: 实战
(16, 1), (16, 14), -- Python Crash Course 1: Python, 教程
(17, 1), (17, 14), -- Python Crash Course 2: Python, 教程
(18, 11), (18, 15), -- Vue Blog: Vue, 实战
(19, 4), (19, 5), (19, 1), -- ML Intro: AI, ML, Python
(20, 8), (20, 7), -- AWS EC2: 云计算, DevOps
(21, 9), (21, 6), -- HTTPS: 安全, Web
(22, 3), (22, 15), -- Go Chat: Go, 实战
(23, 13), -- GPU Battle: 硬件评测
(24, 16), -- Tech News Metaverse: 新闻
(25, 15), -- Binary Search: 实战
(26, 7), (26, 19), (26, 14), -- K8s Local: DevOps, K8s, 教程
(27, 7), (27, 18), (27, 14), -- Docker Intro: DevOps, Docker, 教程
(28, 3), (28, 15), -- Go Gin API: Go, 实战
(29, 10), (29, 6); -- React State: React, Web


-- 8. 插入内容-合集关联数据
-- ------------------------------------------------------------------
INSERT INTO `content_collection` (`content_id`, `collection_id`) VALUES
(6, 1), (16, 1), (17, 1), (19, 1), -- Python入门到精通
(7, 2), (18, 2), (29, 2), -- React全栈开发实战
(8, 3), (19, 3), -- AI前沿观察
(9, 4), (20, 4), (26, 4), (27, 4), -- DevOps最佳实践
(10, 5), (23, 5), -- 硬件评测系列
(11, 6), (21, 6), -- 网络安全基础
(12, 7), (22, 7), (28, 7), -- Go语言并发编程
(13, 8), (18, 8), -- Vue.js 3 深度解析
(14, 9), (24, 9), -- 每周科技快讯
(15, 10), (25, 10); -- 算法与数据结构

-- =================================================================
-- Demo Data Generation Complete
-- =================================================================
```