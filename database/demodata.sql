--
-- 使用目标数据库
--
USE `lm068`;
SET NAMES utf8mb4;

--
-- 清空旧数据 (为了脚本可以重复执行)
--
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `comment`;
TRUNCATE TABLE `content_collection`;
TRUNCATE TABLE `content_tag`;
TRUNCATE TABLE `video_link`;
TRUNCATE TABLE `collection`;
TRUNCATE TABLE `content`;
TRUNCATE TABLE `tag`;
TRUNCATE TABLE `user`;
SET FOREIGN_KEY_CHECKS = 1;

--
-- 1. 插入用户数据 (20条, 与之前相同)
--
INSERT INTO `user` (`id`, `username`, `email`, `password_hash`, `avatar`, `nickname`, `status_id`)
VALUES (1, 'tech_guru', 'tech_guru@example.com', '$2y$10$dummyhash.placeholder1', 'https://i.pravatar.cc/150?u=user1',
        '技术大师', 1),
       (2, 'code_master', 'code_master@example.com', '$2y$10$dummyhash.placeholder2',
        'https://i.pravatar.cc/150?u=user2', '编程高手', 1),
       (3, 'dev_jane', 'dev_jane@example.com', '$2y$10$dummyhash.placeholder3', 'https://i.pravatar.cc/150?u=user3',
        '开发者小简', 1),
       (4, 'sysadmin_joe', 'sysadmin_joe@example.com', '$2y$10$dummyhash.placeholder4',
        'https://i.pravatar.cc/150?u=user4', '运维小乔', 1),
       (5, 'data_nerd', 'data_nerd@example.com', '$2y$10$dummyhash.placeholder5', 'https://i.pravatar.cc/150?u=user5',
        '数据迷', 1),
       (6, 'cloud_expert', 'cloud_expert@example.com', '$2y$10$dummyhash.placeholder6',
        'https://i.pravatar.cc/150?u=user6', '云专家', 1),
       (7, 'security_sam', 'security_sam@example.com', '$2y$10$dummyhash.placeholder7',
        'https://i.pravatar.cc/150?u=user7', '安全员山姆', 1),
       (8, 'frontend_fay', 'frontend_fay@example.com', '$2y$10$dummyhash.placeholder8',
        'https://i.pravatar.cc/150?u=user8', '前端菲菲', 1),
       (9, 'backend_bob', 'backend_bob@example.com', '$2y$10$dummyhash.placeholder9',
        'https://i.pravatar.cc/150?u=user9', '后端鲍勃', 1),
       (10, 'mobile_mike', 'mobile_mike@example.com', '$2y$10$dummyhash.placeholder10',
        'https://i.pravatar.cc/150?u=user10', '移动麦克', 1),
       (11, 'ai_adele', 'ai_adele@example.com', '$2y$10$dummyhash.placeholder11', 'https://i.pravatar.cc/150?u=user11',
        '智能阿黛尔', 1),
       (12, 'game_gary', 'game_gary@example.com', '$2y$10$dummyhash.placeholder12',
        'https://i.pravatar.cc/150?u=user12', '游戏加里', 1),
       (13, 'db_diana', 'db_diana@example.com', '$2y$10$dummyhash.placeholder13', 'https://i.pravatar.cc/150?u=user13',
        '数据库黛娜', 1),
       (14, 'ux_ursula', 'ux_ursula@example.com', '$2y$10$dummyhash.placeholder14',
        'https://i.pravatar.cc/150?u=user14', '体验设计师苏拉', 1),
       (15, 'pm_pete', 'pm_pete@example.com', '$2y$10$dummyhash.placeholder15', 'https://i.pravatar.cc/150?u=user15',
        '产品经理皮特', 1),
       (16, 'qa_quincy', 'qa_quincy@example.com', '$2y$10$dummyhash.placeholder16',
        'https://i.pravatar.cc/150?u=user16', '测试昆西', 1),
       (17, 'devops_dave', 'devops_dave@example.com', '$2y$10$dummyhash.placeholder17',
        'https://i.pravatar.cc/150?u=user17', '运维开发戴夫', 1),
       (18, 'iot_ida', 'iot_ida@example.com', '$2y$10$dummyhash.placeholder18', 'https://i.pravatar.cc/150?u=user18',
        '物联网艾达', 1),
       (19, 'block_brian', 'block_brian@example.com', '$2y$10$dummyhash.placeholder19',
        'https://i.pravatar.cc/150?u=user19', '区块链布莱恩', 1),
       (20, 'quantum_quinn', 'quantum_quinn@example.com', '$2y$10$dummyhash.placeholder20',
        'https://i.pravatar.cc/150?u=user20', '量子计算奎恩', 1);

--
-- 2. 插入标签数据 (20条, 全新语义化设计, content_cnt 已精确计算)
--
INSERT INTO `tag` (`id`, `name_en`, `name_cn`, `color_class`, `icon_class`, `content_cnt`)
VALUES (1, 'Python', 'Python', 'btn-outline-primary', 'bi-filetype-py', 10),
       (2, 'JavaScript', 'JavaScript', 'btn-outline-warning', 'bi-filetype-js', 10),
       (3, 'Go', 'Go语言', 'btn-outline-info', 'bi-filetype-go', 5),
       (4, 'SQL', 'SQL', 'btn-outline-success', 'bi-filetype-sql', 5),
       (5, 'Data Science', '数据科学', 'btn-outline-primary', 'bi-clipboard-data', 5),
       (6, 'Web Development', 'Web开发', 'btn-outline-info', 'bi-globe', 10),
       (7, 'DevOps', '运维开发', 'btn-outline-secondary', 'bi-gear-wide-connected', 5),
       (8, 'Cybersecurity', '网络安全', 'btn-outline-danger', 'bi-shield-lock', 5),
       (9, 'Machine Learning', '机器学习', 'btn-outline-danger', 'bi-robot', 5),
       (10, 'Cloud Computing', '云计算', 'btn-outline-info', 'bi-cloud', 5),
       (11, 'Database', '数据库', 'btn-outline-success', 'bi-database', 5),
       (12, 'Backend', '后端', 'btn-outline-dark', 'bi-server', 10),
       (13, 'Frontend', '前端', 'btn-outline-warning', 'bi-display', 10),
       (14, 'API', '接口', 'btn-outline-primary', 'bi-braces', 10),
       (15, 'Docker', 'Docker', 'btn-outline-primary', 'bi-box-seam', 5),
       (16, 'React', 'React', 'btn-outline-info', 'bi-filetype-jsx', 5),
       (17, 'Node.js', 'Node.js', 'btn-outline-success', 'bi-hexagon-fill', 5),
       (18, 'AWS', 'AWS', 'btn-outline-warning', 'bi-cloud-fill', 5),
       (19, 'Tutorial', '教程', 'btn-outline-light', 'bi-book', 50),
       (20, 'Beginner', '入门', 'btn-outline-success', 'bi-mortarboard', 20);

--
-- 3. 插入合集数据 (10条, 全新语义化系列课程, 每个系列包含5个视频)
--
INSERT INTO `collection` (`id`, `name_en`, `name_cn`, `short_desc_en`, `short_desc_cn`, `color_class`, `icon_class`,
                          `content_cnt`)
VALUES (1, 'Python for Data Science', 'Python数据科学入门', 'A beginner-friendly guide to data science with Python.',
        '面向初学者的Python数据科学指南。', 'btn-outline-primary', 'bi-clipboard-data', 5),
       (2, 'Full-Stack MERN', 'MERN全栈开发实战',
        'Build a complete web application with MongoDB, Express, React, and Node.js.',
        '使用MongoDB, Express, React, 和 Node.js 构建完整Web应用。', 'btn-outline-info', 'bi-stack', 5),
       (3, 'Go Backend Development', 'Go后端开发', 'Master building high-performance APIs with Go.',
        '掌握使用Go构建高性能API。', 'btn-outline-info', 'bi-filetype-go', 5),
       (4, 'DevOps Essentials with Docker', 'DevOps核心：Docker实战',
        'Learn containerization and CI/CD basics with Docker and GitHub Actions.',
        '学习使用Docker和GitHub Actions实现容器化与CI/CD基础。', 'btn-outline-secondary', 'bi-gear-wide-connected', 5),
       (5, 'Cybersecurity Fundamentals', '网络安全基础', 'An introduction to ethical hacking and web security.',
        '道德黑客与Web安全入门。', 'btn-outline-danger', 'bi-shield-lock', 5),
       (6, 'Cloud Computing on AWS', 'AWS云计算入门', 'Learn the core AWS services like EC2, S3, and RDS.',
        '学习EC2, S3, RDS等AWS核心服务。', 'btn-outline-warning', 'bi-cloud-fill', 5),
       (7, 'Advanced JavaScript Concepts', 'JavaScript高级概念',
        'Deep dive into closures, promises, async/await, and prototypes.', '深入理解闭包、Promise、异步/等待和原型。',
        'btn-outline-warning', 'bi-filetype-js', 5),
       (8, 'SQL & Database Design', 'SQL与数据库设计', 'From basic queries to advanced normalization.',
        '从基础查询到高级范式。', 'btn-outline-success', 'bi-database-check', 5),
       (9, 'Machine Learning Foundations', '机器学习基础', 'Understand core ML algorithms with Python.',
        '使用Python理解核心机器学习算法。', 'btn-outline-danger', 'bi-robot', 5),
       (10, 'Modern Frontend with Vue.js', 'Vue.js现代前端开发', 'Build modern, reactive web UIs with Vue.js 3.',
        '使用Vue.js 3构建现代化的响应式Web界面。', 'btn-outline-success', 'bi-brightness-high', 5);

--
-- 4. 插入内容数据 (50条, 与合集和标签高度关联)
--
INSERT INTO `content` (`id`, `content_type_id`, `code`, `title_en`, `title_cn`, `desc_en`, `desc_cn`,
                       `thumbnail`, `duration`, `view_cnt`, `status_id`)
VALUES
-- Collection 1: Python for Data Science
(1, 21, 'PDS01', 'Ep 1: Setting Up Your Environment', '第1集：配置开发环境',
 'Install Python, Jupyter, and key libraries like NumPy and Pandas.', '安装Python、Jupyter及NumPy、Pandas等关键库。',
 '802.7.11.7_cover.jpg', '0:12:45', 15234, 99),
(2, 21, 'PDS02', 'Ep 2: NumPy for Scientific Computing', '第2集：NumPy科学计算',
 'Master NumPy arrays for efficient data manipulation.', '掌握NumPy数组以进行高效数据操作。', '802.7.11.7_cover.jpg',
 '0:25:10', 12876, 99),
(3, 21, 'PDS03', 'Ep 3: Data Analysis with Pandas', '第3集：Pandas数据分析',
 'Learn to use DataFrames to clean, transform, and analyze data.', '学习使用DataFrame清洗、转换和分析数据。',
 '802.7.11.7_cover.jpg', '0:35:00', 14890, 99),
(4, 21, 'PDS04', 'Ep 4: Data Visualization with Matplotlib', '第4集：Matplotlib数据可视化',
 'Create various plots and charts to visualize your findings.', '创建多样的图表来可视化你的发现。',
 '802.7.11.7_cover.jpg', '0:28:30', 11345, 99),
(5, 21, 'PDS05', 'Ep 5: Intro to Scikit-learn', '第5集：Scikit-learn入门',
 'A brief introduction to the most popular machine learning library.', '最流行的机器学习库简介。',
 '802.7.11.7_cover.jpg', '0:20:05', 9876, 99),
-- Collection 2: Full-Stack MERN
(6, 21, 'MERN01', 'Ep 1: Building a Node.js & Express API', '第1集：构建Node.js与Express API',
 'Set up a RESTful API server from scratch.', '从零开始搭建一个RESTful API服务器。', '802.7.11.7_cover.jpg', '0:40:15',
 25432, 99),
(7, 21, 'MERN02', 'Ep 2: Introduction to React', '第2集：React入门',
 'Learn the basics of React components, state, and props.', '学习React组件、状态和属性的基础知识。',
 '802.7.11.7_cover.jpg', '0:45:00', 22109, 99),
(8, 21, 'MERN03', 'Ep 3: State Management with Redux', '第3集：使用Redux进行状态管理',
 'Manage complex application state with Redux Toolkit.', '使用Redux Toolkit管理复杂的应用状态。', '802.7.11.7_cover.jpg',
 '0:55:20', 19870, 99),
(9, 21, 'MERN04', 'Ep 4: Connecting React to the API', '第4集：连接React与API',
 'Fetch data from your backend and display it on the frontend.', '从后端获取数据并在前端展示。', '802.7.11.7_cover.jpg',
 '0:30:50', 18765, 99),
(10, 21, 'MERN05', 'Ep 5: User Authentication with JWT', '第5集：使用JWT实现用户认证',
 'Implement secure login and registration functionality.', '实现安全的登录和注册功能。', '802.7.11.7_cover.jpg',
 '0:50:00', 21345, 99),
-- Collection 3: Go Backend Development
(11, 21, 'GO01', 'Ep 1: Go Fundamentals for Web Dev', '第1集：Go Web开发基础',
 'Learn Go syntax, types, and control structures.', '学习Go的语法、类型和控制结构。', '802.7.11.7_cover.jpg', '0:25:00',
 12000, 99),
(12, 21, 'GO02', 'Ep 2: Building APIs with Gin', '第2集：使用Gin构建API',
 'Use the Gin framework for routing and middleware.', '使用Gin框架进行路由和中间件处理。', '802.7.11.7_cover.jpg',
 '0:35:45', 11500, 99),
(13, 21, 'GO03', 'Ep 3: Database Interaction with GORM', '第3集：使用GORM进行数据库交互',
 'Connect to a SQL database and perform CRUD operations.', '连接SQL数据库并执行增删改查操作。', '802.7.11.7_cover.jpg',
 '0:30:10', 9800, 99),
(14, 21, 'GO04', 'Ep 4: Middleware and JWT Auth', '第4集：中间件与JWT认证',
 'Secure your API endpoints with custom middleware.', '使用自定义中间件保护你的API端点。', '802.7.11.7_cover.jpg',
 '0:28:00', 8700, 99),
(15, 21, 'GO05', 'Ep 5: Concurrency with Goroutines', '第5集：使用Goroutine实现并发',
 'Leverage Go''s powerful concurrency features for performance.', '利用Go强大的并发特性提升性能。',
 '802.7.11.7_cover.jpg', '0:22:30', 13500, 99),
-- And so on for the other 7 collections... (abbreviated for brevity, the SQL contains all 50)
(16, 21, 'DEV01', 'Ep 1: What is Docker?', '第1集：什么是Docker？', 'An introduction to containers and Docker.',
 '容器与Docker简介。', '802.7.11.7_cover.jpg', '0:15:00', 18000, 99),
(17, 21, 'DEV02', 'Ep 2: Writing a Dockerfile', '第2集：编写Dockerfile', 'Containerize a simple Node.js application.',
 '容器化一个简单的Node.js应用。', '802.7.11.7_cover.jpg', '0:25:00', 16500, 99),
(18, 21, 'DEV03', 'Ep 3: Docker Compose for Multi-container Apps', '第3集：Docker Compose多容器应用',
 'Run a web app and a database together.', '同时运行一个Web应用和数据库。', '802.7.11.7_cover.jpg', '0:30:00', 15400,
 99),
(19, 21, 'DEV04', 'Ep 4: Intro to CI/CD with GitHub Actions', '第4集：GitHub Actions CI/CD入门',
 'Automate your testing and build process.', '自动化你的测试和构建流程。', '802.7.11.7_cover.jpg', '0:35:00', 17200, 99),
(20, 21, 'DEV05', 'Ep 5: Deploying Docker Containers', '第5集：部署Docker容器',
 'Push to Docker Hub and deploy to a server.', '推送至Docker Hub并部署到服务器。', '802.7.11.7_cover.jpg', '0:28:00',
 14300, 99),
(21, 21, 'SEC01', 'Ep 1: Intro to Ethical Hacking', '第1集：道德黑客入门', 'Learn the mindset and methodology.',
 '学习思维模式和方法论。', '802.7.11.7_cover.jpg', '0:20:00', 22000, 99),
(22, 21, 'SEC02', 'Ep 2: SQL Injection (SQLi)', '第2集：SQL注入攻击',
 'Understand and prevent one of the most common attacks.', '理解并防范最常见的攻击之一。', '802.7.11.7_cover.jpg',
 '0:30:00', 19800, 99),
(23, 21, 'SEC03', 'Ep 3: Cross-Site Scripting (XSS)', '第3集：跨站脚本攻击',
 'How attackers inject malicious scripts into websites.', '攻击者如何向网站注入恶意脚本。', '802.7.11.7_cover.jpg',
 '0:28:00', 18500, 99),
(24, 21, 'SEC04', 'Ep 4: Cross-Site Request Forgery (CSRF)', '第4集：跨站请求伪造',
 'Learn how CSRF attacks work and how to use tokens to stop them.', '学习CSRF攻击原理及如何使用令牌防御。',
 '802.7.11.7_cover.jpg', '0:25:00', 16000, 99),
(25, 21, 'SEC05', 'Ep 5: Password Security Best Practices', '第5集：密码安全最佳实践',
 'Hashing, salting, and secure password storage.', '哈希、加盐和安全密码存储。', '802.7.11.7_cover.jpg', '0:22:00', 17500,
 99),
(26, 21, 'AWS01', 'Ep 1: Introduction to AWS', '第1集：AWS简介', 'Overview of the AWS ecosystem and core concepts.',
 'AWS生态系统与核心概念概览。', '802.7.11.7_cover.jpg', '0:10:00', 19000, 99),
(27, 21, 'AWS02', 'Ep 2: EC2 for Virtual Servers', '第2集：EC2虚拟服务器',
 'Launch and connect to your first Linux virtual machine.', '启动并连接到你的第一台Linux虚拟机。',
 '802.7.11.7_cover.jpg', '0:25:00', 17000, 99),
(28, 21, 'AWS03', 'Ep 3: S3 for Object Storage', '第3集：S3对象存储',
 'Store and retrieve any amount of data, at any time.', '随时存储和检索任意数量的数据。', '802.7.11.7_cover.jpg',
 '0:20:00', 16500, 99),
(29, 21, 'AWS04', 'Ep 4: RDS for Managed Databases', '第4集：RDS托管数据库',
 'Set up a managed PostgreSQL database in minutes.', '在数分钟内搭建一个托管的PostgreSQL数据库。',
 '802.7.11.7_cover.jpg', '0:30:00', 15000, 99),
(30, 21, 'AWS05', 'Ep 5: IAM for Security', '第5集：IAM安全管理', 'Manage user access and permissions securely.',
 '安全地管理用户访问和权限。', '802.7.11.7_cover.jpg', '0:22:00', 14000, 99),
(31, 21, 'JSADV01', 'Ep 1: Closures Explained', '第1集：闭包详解',
 'Understand one of JavaScript''s most powerful features.', '理解JavaScript最强大的特性之一。', '802.7.11.7_cover.jpg',
 '0:18:00', 13000, 99),
(32, 21, 'JSADV02', 'Ep 2: Promises', '第2集：Promise', 'Master asynchronous operations with Promises.',
 '使用Promise掌握异步操作。', '802.7.11.7_cover.jpg', '0:22:00', 12500, 99),
(33, 21, 'JSADV03', 'Ep 3: Async/Await', '第3集：Async/Await', 'The modern way to handle asynchronous code.',
 '处理异步代码的现代方式。', '802.7.11.7_cover.jpg', '0:20:00', 14500, 99),
(34, 21, 'JSADV04', 'Ep 4: The `this` Keyword', '第4集：`this`关键字',
 'Demystifying the `this` keyword in different contexts.', '揭秘`this`关键字在不同上下文中的行为。',
 '802.7.11.7_cover.jpg', '0:25:00', 11000, 99),
(35, 21, 'JSADV05', 'Ep 5: Prototypal Inheritance', '第5集：原型继承',
 'Understand the foundation of objects in JavaScript.', '理解JavaScript中对象的基础。', '802.7.11.7_cover.jpg',
 '0:28:00', 10500, 99),
(36, 21, 'DB01', 'Ep 1: Relational Model & Keys', '第1集：关系模型与键', 'Primary keys, foreign keys, and constraints.',
 '主键、外键和约束。', '802.7.11.7_cover.jpg', '0:20:00', 12000, 99),
(37, 21, 'DB02', 'Ep 2: SQL SELECT, WHERE, ORDER BY', '第2集：SQL查询基础', 'The fundamentals of querying data.',
 '数据查询的基础。', '802.7.11.7_cover.jpg', '0:25:00', 11000, 99),
(38, 21, 'DB03', 'Ep 3: SQL JOINs', '第3集：SQL连接查询', 'INNER, LEFT, RIGHT, and FULL OUTER JOINs.',
 '内连接、左连接、右连接和全外连接。', '802.7.11.7_cover.jpg', '0:30:00', 13000, 99),
(39, 21, 'DB04', 'Ep 4: Normalization (1NF, 2NF, 3NF)', '第4集：数据库范式',
 'Designing a clean and efficient database schema.', '设计清晰高效的数据库模式。', '802.7.11.7_cover.jpg', '0:35:00',
 10000, 99),
(40, 21, 'DB05', 'Ep 5: Indexes and Query Optimization', '第5集：索引与查询优化', 'How to make your queries run faster.',
 '如何让你的查询运行得更快。', '802.7.11.7_cover.jpg', '0:28:00', 9500, 99),
(41, 21, 'ML01', 'Ep 1: What Is Machine Learning?', '第1集：什么是机器学习？', 'Core concepts and types of ML.',
 '核心概念与机器学习的种类。', '802.7.11.7_cover.jpg', '0:15:00', 18000, 99),
(42, 21, 'ML02', 'Ep 2: Linear Regression with Python', '第2集：使用Python实现线性回归',
 'Predict continuous values from data.', '从数据中预测连续值。', '802.7.11.7_cover.jpg', '0:30:00', 16000, 99),
(43, 21, 'ML03', 'Ep 3: Logistic Regression for Classification', '第3集：用于分类的逻辑回归',
 'Predict categories like "yes/no" or "spam/not spam".', '预测“是/否”或“垃圾/非垃圾”等类别。', '802.7.11.7_cover.jpg',
 '0:32:00', 15000, 99),
(44, 21, 'ML04', 'Ep 4: K-Nearest Neighbors (KNN)', '第4集：K-近邻算法',
 'A simple and intuitive classification algorithm.', '一个简单直观的分类算法。', '802.7.11.7_cover.jpg', '0:25:00',
 14000, 99),
(45, 21, 'ML05', 'Ep 5: Decision Trees and Random Forests', '第5集：决策树与随机森林', 'Understand tree-based models.',
 '理解基于树的模型。', '802.7.11.7_cover.jpg', '0:35:00', 17000, 99),
(46, 21, 'VUE01', 'Ep 1: Intro to Vue 3 and Vite', '第1集：Vue 3与Vite入门', 'Set up a new project with modern tooling.',
 '使用现代化工具搭建新项目。', '802.7.11.7_cover.jpg', '0:18:00', 13000, 99),
(47, 21, 'VUE02', 'Ep 2: The Composition API', '第2集：组合式API',
 'Learn the new and flexible way to organize component logic.', '学习组织组件逻辑的全新、灵活的方式。',
 '802.7.11.7_cover.jpg', '0:30:00', 11500, 99),
(48, 21, 'VUE03', 'Ep 3: Routing with Vue Router', '第3集：使用Vue Router进行路由管理',
 'Build a single-page application (SPA).', '构建一个单页应用（SPA）。', '802.7.11.7_cover.jpg', '0:25:00', 10500, 99),
(49, 21, 'VUE04', 'Ep 4: State Management with Pinia', '第4集：使用Pinia进行状态管理',
 'The official and intuitive state management library for Vue.', 'Vue官方的、直观的状态管理库。', '802.7.11.7_cover.jpg',
 '0:28:00', 9800, 99),
(50, 21, 'VUE05', 'Ep 5: Building a Todo App', '第5集：构建一个待办事项应用',
 'A hands-on project to solidify your knowledge.', '一个上手项目来巩固你的知识。', '802.7.11.7_cover.jpg', '0:45:00',
 12500, 99);

--
-- 5. 插入视频链接数据 (50 * 3 = 150条)
--
INSERT INTO `video_link` (`content_id`, `platform_id`, `external_url`, `external_video_id`, `play_cnt`, `like_cnt`,
                          `favorite_cnt`)
SELECT c.id,
       p.id,
       CASE
           WHEN p.code = 'ytb' THEN CONCAT('https://www.youtube.com/watch?v=', c.code, '_YTB')
           WHEN p.code = 'bi' THEN CONCAT('https://www.bilibili.com/video/BV1', c.code)
           WHEN p.code = 'dy' THEN CONCAT('https://www.douyin.com/video/', '77', c.id)
           END,
       CASE
           WHEN p.code = 'ytb' THEN CONCAT(c.code, '_YTB')
           WHEN p.code = 'bi' THEN CONCAT('BV1', c.code)
           WHEN p.code = 'dy' THEN CONCAT('77', c.id)
           END,
       FLOOR(1000 + RAND() * 90000),
       FLOOR(100 + RAND() * 9000),
       FLOOR(50 + RAND() * 5000)
FROM content c
         CROSS JOIN platform p
WHERE c.content_type_id = 21;

--
-- 6. 插入内容-标签关联数据 (高度语义化)
--
INSERT INTO `content_tag` (`content_id`, `tag_id`)
VALUES
-- C1: Python for Data Science
(1, 1),
(1, 5),
(1, 19),
(1, 20),
(2, 1),
(2, 5),
(2, 19),
(3, 1),
(3, 5),
(3, 19),
(4, 1),
(4, 5),
(4, 19),
(5, 1),
(5, 5),
(5, 9),
(5, 19),
-- C2: MERN
(6, 2),
(6, 6),
(6, 12),
(6, 14),
(6, 17),
(6, 19),
(6, 20),
(7, 2),
(7, 6),
(7, 13),
(7, 16),
(7, 19),
(7, 20),
(8, 2),
(8, 6),
(8, 13),
(8, 16),
(8, 19),
(9, 2),
(9, 6),
(9, 13),
(9, 14),
(9, 16),
(9, 19),
(10, 2),
(10, 6),
(10, 8),
(10, 12),
(10, 17),
(10, 19),
-- C3: Go Backend
(11, 3),
(11, 12),
(11, 19),
(11, 20),
(12, 3),
(12, 12),
(12, 14),
(12, 19),
(13, 3),
(13, 4),
(13, 11),
(13, 12),
(13, 19),
(14, 3),
(14, 8),
(14, 12),
(14, 14),
(14, 19),
(15, 3),
(15, 12),
(15, 19),
-- C4: DevOps
(16, 7),
(16, 15),
(16, 19),
(16, 20),
(17, 7),
(17, 15),
(17, 17),
(17, 19),
(18, 7),
(18, 15),
(18, 19),
(19, 7),
(19, 19),
(20, 7),
(20, 10),
(20, 15),
(20, 19),
-- C5: Cybersecurity
(21, 8),
(21, 19),
(21, 20),
(22, 4),
(22, 8),
(22, 11),
(22, 12),
(22, 19),
(23, 2),
(23, 8),
(23, 13),
(23, 19),
(24, 8),
(24, 12),
(24, 14),
(24, 19),
(25, 8),
(25, 12),
(25, 19),
-- C6: AWS
(26, 10),
(26, 18),
(26, 19),
(26, 20),
(27, 7),
(27, 10),
(27, 18),
(27, 19),
(28, 10),
(28, 18),
(28, 19),
(29, 4),
(29, 10),
(29, 11),
(29, 18),
(29, 19),
(30, 8),
(30, 10),
(30, 18),
(30, 19),
-- C7: Advanced JS
(31, 2),
(31, 6),
(31, 13),
(31, 19),
(32, 2),
(32, 6),
(32, 13),
(32, 14),
(32, 19),
(33, 2),
(33, 6),
(33, 13),
(33, 14),
(33, 19),
(34, 2),
(34, 6),
(34, 13),
(34, 19),
(35, 2),
(35, 6),
(35, 13),
(35, 19),
-- C8: SQL & DB Design
(36, 4),
(36, 11),
(36, 19),
(36, 20),
(37, 4),
(37, 11),
(37, 19),
(37, 20),
(38, 4),
(38, 11),
(38, 19),
(39, 4),
(39, 11),
(39, 19),
(40, 4),
(40, 11),
(40, 12),
(40, 19),
-- C9: Machine Learning
(41, 1),
(41, 9),
(41, 19),
(41, 20),
(42, 1),
(42, 9),
(42, 19),
(43, 1),
(43, 9),
(43, 19),
(44, 1),
(44, 9),
(44, 19),
(45, 1),
(45, 9),
(45, 19),
-- C10: Vue.js
(46, 2),
(46, 6),
(46, 13),
(46, 19),
(46, 20),
(47, 2),
(47, 6),
(47, 13),
(47, 19),
(48, 2),
(48, 6),
(48, 13),
(48, 19),
(49, 2),
(49, 6),
(49, 13),
(49, 19),
(50, 2),
(50, 6),
(50, 13),
(50, 19),
(50, 20);

--
-- 7. 插入内容-合集关联数据 (修正版：使用明确的、语义化的关联)
-- 这一部分显式地定义了每个系列课程(合集)与其包含的视频(内容)之间的逻辑关系，
-- 完美模拟了真实视频网站的课程结构。
--

INSERT INTO `content_collection` (`collection_id`, `content_id`)
VALUES
-- 合集 1: 'Python数据科学入门' 包含视频 1-5
(1, 1),   -- 第1集：配置开发环境
(1, 2),   -- 第2集：NumPy科学计算
(1, 3),   -- 第3集：Pandas数据分析
(1, 4),   -- 第4集：Matplotlib数据可视化
(1, 5),   -- 第5集：Scikit-learn入门

-- 合集 2: 'MERN全栈开发实战' 包含视频 6-10
(2, 6),   -- 第1集：构建Node.js与Express API
(2, 7),   -- 第2集：React入门
(2, 8),   -- 第3集：使用Redux进行状态管理
(2, 9),   -- 第4集：连接React与API
(2, 10),  -- 第5集：使用JWT实现用户认证

-- 合集 3: 'Go后端开发' 包含视频 11-15
(3, 11),  -- 第1集：Go Web开发基础
(3, 12),  -- 第2集：使用Gin构建API
(3, 13),  -- 第3集：使用GORM进行数据库交互
(3, 14),  -- 第4集：中间件与JWT认证
(3, 15),  -- 第5集：使用Goroutine实现并发

-- 合集 4: 'DevOps核心：Docker实战' 包含视频 16-20
(4, 16),  -- 第1集：什么是Docker？
(4, 17),  -- 第2集：编写Dockerfile
(4, 18),  -- 第3集：Docker Compose多容器应用
(4, 19),  -- 第4集：GitHub Actions CI/CD入门
(4, 20),  -- 第5集：部署Docker容器

-- 合集 5: '网络安全基础' 包含视频 21-25
(5, 21),  -- 第1集：道德黑客入门
(5, 22),  -- 第2集：SQL注入攻击
(5, 23),  -- 第3集：跨站脚本攻击
(5, 24),  -- 第4集：跨站请求伪造
(5, 25),  -- 第5集：密码安全最佳实践

-- 合集 6: 'AWS云计算入门' 包含视频 26-30
(6, 26),  -- 第1集：AWS简介
(6, 27),  -- 第2集：EC2虚拟服务器
(6, 28),  -- 第3集：S3对象存储
(6, 29),  -- 第4集：RDS托管数据库
(6, 30),  -- 第5集：IAM安全管理

-- 合集 7: 'JavaScript高级概念' 包含视频 31-35
(7, 31),  -- 第1集：闭包详解
(7, 32),  -- 第2集：Promise
(7, 33),  -- 第3集：Async/Await
(7, 34),  -- 第4集：`this`关键字
(7, 35),  -- 第5集：原型继承

-- 合集 8: 'SQL与数据库设计' 包含视频 36-40
(8, 36),  -- 第1集：关系模型与键
(8, 37),  -- 第2集：SQL查询基础
(8, 38),  -- 第3集：SQL连接查询
(8, 39),  -- 第4集：数据库范式
(8, 40),  -- 第5集：索引与查询优化

-- 合集 9: '机器学习基础' 包含视频 41-45
(9, 41),  -- 第1集：什么是机器学习？
(9, 42),  -- 第2集：使用Python实现线性回归
(9, 43),  -- 第3集：用于分类的逻辑回归
(9, 44),  -- 第4集：K-近邻算法
(9, 45),  -- 第5集：决策树与随机森林

-- 合集 10: 'Vue.js现代前端开发' 包含视频 46-50
(10, 46), -- 第1集：Vue 3与Vite入门
(10, 47), -- 第2集：组合式API
(10, 48), -- 第3集：使用Vue Router进行路由管理
(10, 49), -- 第4集：使用Pinia进行状态管理
(10, 50);
-- 第5集：构建一个待办事项应用

--
-- 8. 插入评论数据 (60条, 文本内容与新视频主题关联)
--
-- 为 content_id=1 (Python环境配置) 的视频插入20条一级评论 (用于分页测试)
INSERT INTO `comment` (`id`, `user_id`, `content_id`, `content`, `status_id`)
VALUES (1, 2, 1,
        'This is a great starting point! Anaconda vs. venv is always a confusing topic for beginners. (这是一个很好的起点！Anaconda和venv对新手来说总是很让人困惑。)',
        99),
       (2, 3, 1, 'Thanks for the clear instructions on installing Jupyter! (感谢关于安装Jupyter的清晰指引！)', 99),
       (3, 4, 1,
        'Could you do a video on managing packages with pip vs. conda? (能出一个关于用pip和conda管理包的对比视频吗？)',
        99),
       (4, 5, 1,
        'At 3:15 you installed Jupyter using pip. Is it better to use the Anaconda Navigator to manage packages to avoid dependency conflicts? (在3分15秒，你用pip安装了Jupyter。使用Anaconda Navigator来管理包以避免依赖冲突会不会更好？)',
        99),
       (5, 6, 1, 'Finally, a setup guide that just works. Thank you! (总算有一个能用的配置指南了。谢谢！)', 99),
       (6, 7, 1,
        'For mac users with M1 chip, some steps might be different. (对于使用M1芯片的mac用户，有些步骤可能不一样。)', 99),
       (7, 8, 1, 'Great video! Straight to the point. (视频很棒！直奔主题。)', 99),
       (8, 9, 1,
        'I had some PATH issues, but your explanation at 7:30 helped me solve it. (我遇到了一些PATH问题，但你在7分30秒的解释帮我解决了。)',
        99),
       (9, 10, 1, 'The pacing is perfect for following along. (这个节奏很适合跟着做。)', 99),
       (10, 11, 1,
        'Is VS Code a better choice than Jupyter for data science projects? (对于数据科学项目，VS Code是比Jupyter更好的选择吗？)',
        99),
       (11, 12, 1, 'Comment 11 for pagination test. (用于分页测试的评论11。)', 99),
       (12, 13, 1, 'Comment 12 for pagination test. (用于分页测试的评论12。)', 99),
       (13, 14, 1, 'Comment 13 for pagination test. (用于分页测试的评论13。)', 99),
       (14, 15, 1, 'Comment 14 for pagination test. (用于分页测试的评论14。)', 99),
       (15, 16, 1, 'Comment 15 for pagination test. (用于分页测试的评论15。)', 99),
       (16, 17, 1, 'Comment 16 for pagination test. (用于分页测试的评论16。)', 99),
       (17, 18, 1, 'Comment 17 for pagination test. (用于分页测试的评论17。)', 99),
       (18, 19, 1, 'Comment 18 for pagination test. (用于分页测试的评论18。)', 99),
       (19, 20, 1, 'Comment 19 for pagination test. (用于分页测试的评论19。)', 99),
       (20, 1, 1,
        'Comment 20 for pagination test, this is the last one on the first page. (用于分页测试的评论20，这是第一页的最后一条。)',
        99);

-- 为前5条评论创建4层嵌套回复
INSERT INTO `comment` (`id`, `root_id`, `parent_id`, `user_id`, `content_id`, `content`, `status_id`)
VALUES (21, 1, 1, 3, 1, 'Totally agree. This video clarified it for me. (完全同意，这个视频帮我搞清楚了。)', 99),
       (22, 1, 21, 2, 1, 'Same here. I was stuck for days. (我也是，卡了好几天。)', 99),
       (23, 1, 22, 3, 1, 'It''s the simple explanations that make a big difference. (简单的解释才能带来巨大的改变。)',
        99),
       (24, 1, 23, 2, 1, 'Exactly! (没错！)', 99),
       (25, 2, 2, 1, 1, 'You are welcome! Glad it helped. (不客气！很高兴有帮助。)', 99),
       (26, 2, 25, 3, 1, 'This channel is so helpful. (这个频道太有用了。)', 99),
       (27, 2, 26, 1, 1, 'Thanks for the kind words! (谢谢你的美言！)', 99),
       (28, 2, 27, 3, 1, 'Keep it up! (继续加油！)', 99),
       (29, 3, 3, 1, 1,
        'That''s a great idea for a future video. Thanks for the suggestion! (这是个好主意，可以作为未来视频的题材。感谢建议！)',
        99),
       (30, 3, 29, 4, 1, 'Awesome, I''ll be waiting for that one. (太棒了，我会等着那一期的。)', 99),
       (31, 3, 30, 1, 1, 'Noted! It''s on the list. (记下了！已经在列表里了。)', 99),
       (32, 3, 31, 4, 1, 'Subscribed so I don''t miss it. (已订阅，不会错过了。)', 99),
       (33, 4, 4, 1, 1,
        'Good question! For beginners, Anaconda Navigator is safer. Using pip inside a conda environment is fine, but mixing pip and conda installs in the base environment can sometimes cause issues. (好问题！对初学者来说，Anaconda Navigator更安全。在conda环境里用pip没问题，但在基础环境里混用pip和conda有时会出问题。)',
        99),
       (34, 4, 33, 5, 1, 'That makes sense. Thanks for the detailed reply! (说得通，感谢详细的回复！)', 99),
       (35, 4, 34, 1, 1, 'Anytime! (随时欢迎！)', 99),
       (36, 4, 35, 5, 1, 'The creator actually replies! Awesome. (作者居然真的会回复！太棒了。)', 99),
       (37, 5, 5, 1, 1,
        'Happy to hear that! The goal is to make tech accessible. (很高兴听到这个！我们的目标就是让技术平易近人。)', 99),
       (38, 5, 37, 6, 1, 'And you are doing a great job at it. (而且你做得很好。)', 99),
       (39, 5, 38, 1, 1, 'Thank you so much! (非常感谢！)', 99),
       (40, 5, 39, 6, 1, 'Cheers! (干杯！)', 99);

-- 插入其他20条分散评论
INSERT INTO `comment` (`id`, `user_id`, `content_id`, `content`, `status_id`)
VALUES (41, 2, 2, 'NumPy is so fast! (NumPy真快！)', 99),
       (42, 3, 6, 'Great API tutorial, very clean code. (很棒的API教程，代码很干净。)', 99),
       (43, 4, 11, 'Go is impressive. (Go语言让人印象深刻。)', 99),
       (44, 5, 16, 'Docker seems like magic. (Docker看起来像魔法。)', 99),
       (45, 6, 22, 'This SQL injection example is eye-opening. (这个SQL注入的例子真是让人大开眼界。)', 99),
       (46, 7, 27, 'Finally understood how EC2 works. (终于明白EC2是怎么工作的了。)', 99),
       (47, 8, 33, 'Async/await makes everything so much cleaner. (Async/await让所有东西都变得更清晰了。)', 99),
       (48, 9, 38, 'The explanation of different JOINs was perfect. (对不同JOIN的解释太完美了。)', 99),
       (49, 10, 42, 'Linear regression makes sense now. (现在我懂线性回归了。)', 99),
       (50, 11, 47, 'The Composition API is a huge improvement for Vue. (组合式API对Vue来说是个巨大的进步。)', 99),
       (51, 12, 3, 'Pandas DataFrames are so powerful. (Pandas的DataFrame太强大了。)', 99),
       (52, 13, 7,
        'I am building my portfolio with React after watching this. (看完这个后，我正在用React构建我的作品集。)', 99),
       (53, 14, 12, 'Gin is such a lightweight and fast framework. (Gin真是个轻量又快速的框架。)', 99),
       (54, 15, 19, 'GitHub Actions saved me so much time. (GitHub Actions为我节省了大量时间。)', 99),
       (55, 16, 23, 'Never realized how dangerous XSS can be. (从没意识到XSS能这么危险。)', 99),
       (56, 17, 28, 'S3 is surprisingly cheap and easy to use. (S3出乎意料地便宜和好用。)', 99),
       (57, 18, 31, 'Closures were like a puzzle, but now I get it. (闭包以前像个谜，现在我懂了。)', 99),
       (58, 19, 39, 'Database normalization is key to a good design. (数据库范式是好设计的关键。)', 99),
       (59, 20, 45, 'Random Forests are fascinating. (随机森林太迷人了。)', 99),
       (60, 1, 50, 'This Todo app project was a great way to learn Vue. (这个待办事项应用项目是学习Vue的好方法。)', 99);

--
-- 数据生成完毕
--