-- MySQL 5.7+ 标准数据库结构文件
CREATE DATABASE IF NOT EXISTS `lm068` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `lm068`;

-- 用户表
CREATE TABLE `user` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `avatar` VARCHAR(255) COMMENT '用户头像URL',
  `nickname` VARCHAR(100) COMMENT '用户昵称',
  `status_id` TINYINT UNSIGNED DEFAULT 1 COMMENT '用户状态: 0=不可用/封停, 1=可用/正常',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_username` (`username`),
  UNIQUE KEY `uk_email` (`email`)
) ENGINE=InnoDB;

-- 内容主表 (包含: 网站公告, 一般文章, 视频, 共三类内容，用content_type_id区分)
CREATE TABLE `content` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `content_type_id` TINYINT UNSIGNED NOT NULL COMMENT '内容类型: 1-网站公告, 11-一般文章, 21-视频',
  `author` VARCHAR(255) DEFAULT 'DP' COMMENT '作者名称',
  `code` VARCHAR(50) COMMENT '内部管理代码',
  `title_en` VARCHAR(255) NOT NULL COMMENT '英文标题',
  `title_cn` VARCHAR(255) NOT NULL COMMENT '中文标题',
  `short_desc_en` VARCHAR(300) COMMENT '英文简介',
  `short_desc_cn` VARCHAR(300) COMMENT '中文简介',
  `desc_en` TEXT COMMENT '英文描述/内容, 支持markdown格式纯文本存储',
  `desc_cn` TEXT COMMENT '中文描述/内容, 支持markdown格式纯文本存储',
  `sum_en` TEXT COMMENT '英文 AI总结内容, 支持markdown格式纯文本存储',
  `sum_cn` TEXT COMMENT '中文 AI总结内容, 支持markdown格式纯文本存储',
  `thumbnail` VARCHAR(255) COMMENT '缩略图URL',
  `duration` MEDIUMINT UNSIGNED COMMENT '视频时长秒数 (仅视频类型使用)',
  `pv_cnt` BIGINT UNSIGNED DEFAULT 0 COMMENT '网站内PV计数',
  `view_cnt` BIGINT UNSIGNED DEFAULT 0 COMMENT '总观看/阅读次数',
  `status_id` TINYINT UNSIGNED DEFAULT 1 COMMENT '状态: 0-隐藏, 1-草稿, 11-创意, 18-脚本开, 19-脚本完, 21-开拍, 29-拍完, 31-开剪, 39-剪完, 91-待发布, 99-已发布',
  `pub_at` DATETIME COMMENT '发布时间',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_status_id` (`status_id`),
  INDEX `idx_content_type_id` (`content_type_id`)
) ENGINE=InnoDB;

-- 内容 PV 每日统计表 (主要查询表)
CREATE TABLE `content_pv_daily` (
    `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `content_id` INT UNSIGNED NOT NULL COMMENT '关联内容ID',
    `stat_date` DATE NOT NULL COMMENT '统计日期',
    `pv_count` BIGINT UNSIGNED DEFAULT 0 COMMENT '当日新增PV数',
    `uv_count` BIGINT UNSIGNED DEFAULT 0 COMMENT '当日独立访客数',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_content_date` (`content_id`, `stat_date`),
    INDEX `idx_stat_date` (`stat_date`),
    FOREIGN KEY (`content_id`) REFERENCES `content`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='内容每日PV统计表';

-- 原始 PV 访问日志表 (用于实时记录和重算)
CREATE TABLE `content_pv_log` (
  `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `content_id` INT UNSIGNED NOT NULL COMMENT '关联内容ID',
  `user_id` INT UNSIGNED COMMENT '用户ID(可选,用于UV统计), 当前未启用用户系统',
  `ip_address` BINARY(16) COMMENT 'IPv4/IPv6 地址(可选,用于UV统计) use INET6_ATON()',
  `accessed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '访问时间',

  -- 不保存完整 user_agent，而是解析后的结构化字段. 保留设计，当前程序不使用
  `device_type` TINYINT COMMENT '1-Desktop, 2-Mobile, 3-Tablet, 4-Bot',
  `os_family` VARCHAR(20) COMMENT 'Windows, iOS, Android, Linux, Mac',
  `browser_family` VARCHAR(20) COMMENT 'Chrome, Safari, Firefox, Edge',
  `is_bot` BOOLEAN DEFAULT 0 COMMENT '是否爬虫',

  INDEX `idx_accessed_at` (`accessed_at`)
) ENGINE=InnoDB COMMENT='内容PV原始访问日志';

-- 视频第三方平台表
CREATE TABLE `platform` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL COMMENT '平台名称',
  `code` VARCHAR(50) NOT NULL COMMENT '平台内部代码, 用于项目内部通讯',
  `base_url` VARCHAR(255) NOT NULL COMMENT '基础url用于类似数据抓取',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_code` (`code`),
  UNIQUE KEY `uk_name` (`name`)
) ENGINE=InnoDB;

-- 插入默认第三方平台数据
INSERT INTO `platform` (`name`, `code`, `base_url`) VALUES 
('Youtube', 'ytb', ''), 
('BiliBili', 'bi', ''), 
('DouYin', 'dy', '');

-- 视频第三方平台链接表
CREATE TABLE `video_link` (
    `id` INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `content_id` INT UNSIGNED NOT NULL COMMENT '关联内容ID (content_type_id 应为视频类型)',
    `platform_id` INT UNSIGNED NOT NULL COMMENT '关联平台表ID',
    `external_url` VARCHAR(500) NOT NULL COMMENT '第三方视频链接',
    `external_video_id` VARCHAR(200) NOT NULL COMMENT '第三方平台视频URI里的ID',
    `play_cnt` BIGINT UNSIGNED DEFAULT 0 COMMENT '播放数',
    `like_cnt` BIGINT UNSIGNED DEFAULT 0 COMMENT '点赞数',
    `favorite_cnt` BIGINT UNSIGNED DEFAULT 0 COMMENT '收藏数',
    `download_cnt` BIGINT UNSIGNED DEFAULT 0 COMMENT '下载数',
    `comment_cnt` BIGINT UNSIGNED DEFAULT 0 COMMENT '评论数',
    `share_cnt` BIGINT UNSIGNED DEFAULT 0 COMMENT '分享数',
    `status_id` TINYINT UNSIGNED DEFAULT 1 COMMENT '状态: 1-正常, 0-失效',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_content_id` (`content_id`),
    UNIQUE KEY `uk_platform_external_id` (`platform_id`, `external_video_id`),
    FOREIGN KEY (`content_id`) REFERENCES `content`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`platform_id`) REFERENCES `platform`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 视频数据抓取日志表
CREATE TABLE `video_stats_log` (
    `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `video_link_id` INT UNSIGNED NOT NULL COMMENT '关联video_links表ID',
    `content_id` INT UNSIGNED NOT NULL COMMENT '关联内容ID (content_type_id 应为视频类型)',
    `platform_id` INT UNSIGNED NOT NULL COMMENT '关联平台表ID',
    `play_cnt` BIGINT UNSIGNED DEFAULT 0 COMMENT '播放数',
    `like_cnt` BIGINT UNSIGNED DEFAULT 0 COMMENT '点赞数',
    `favorite_cnt` BIGINT UNSIGNED DEFAULT 0 COMMENT '收藏数',
    `download_cnt` BIGINT UNSIGNED DEFAULT 0 COMMENT '下载数',
    `comment_cnt` BIGINT UNSIGNED DEFAULT 0 COMMENT '评论数',
    `share_cnt` BIGINT UNSIGNED DEFAULT 0 COMMENT '分享数',
    `status_id` TINYINT UNSIGNED DEFAULT 1 COMMENT '状态: 0-失败, 1-新任务, 11-进行中, 99-已完成',
    `collected_at` DATETIME NOT NULL COMMENT '数据采集时间',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_status_id` (`status_id`),
    INDEX `idx_link_collected` (`video_link_id`, `collected_at`),
    FOREIGN KEY (`video_link_id`) REFERENCES `video_link`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`platform_id`) REFERENCES `platform`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 评论表
CREATE TABLE `comment` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `root_id` INT UNSIGNED DEFAULT NULL COMMENT '根评论ID, 用于快速查询整个评论树',
  `parent_id` INT UNSIGNED DEFAULT NULL COMMENT '父评论ID, 支持回复功能',
  `user_id` INT UNSIGNED NOT NULL,
  `content_id` INT UNSIGNED NOT NULL COMMENT '关联内容ID',
  `content` TEXT NOT NULL,
  `status_id` TINYINT UNSIGNED DEFAULT 1 COMMENT '状态: 0-已隐藏, 1-待审核, 99-审核通过',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_user_id` (`user_id`),
  INDEX `content_user` (`content_id`, `user_id`),
  INDEX `idx_root_id` (`root_id`),
  INDEX `content_root` (`content_id`, `root_id`),
  INDEX `idx_parent_id` (`parent_id`),
  FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`content_id`) REFERENCES `content`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`root_id`) REFERENCES `comment`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`parent_id`) REFERENCES `comment`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 收藏表
CREATE TABLE `favorite` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `content_id` INT UNSIGNED NOT NULL COMMENT '关联内容ID',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_user_content` (`user_id`, `content_id`),
  INDEX `content_id` (`content_id`), 
  FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`content_id`) REFERENCES `content`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 邮件订阅表
CREATE TABLE `subscription` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(200) NOT NULL,
  `status_id` TINYINT UNSIGNED DEFAULT 1 COMMENT '订阅状态: 0=取消订阅, 1=已订阅',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_email` (`email`)
) ENGINE=InnoDB;

-- 内容标签表
CREATE TABLE `tag` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name_en` VARCHAR(50) NOT NULL,
  `name_cn` VARCHAR(50) NOT NULL,
  `short_desc_en` VARCHAR(100) DEFAULT '',
  `short_desc_cn` VARCHAR(100) DEFAULT '',
  `desc_en` VARCHAR(500),
  `desc_cn` VARCHAR(500),
  `color_class` VARCHAR(50) DEFAULT NULL COMMENT '颜色样式类, 为 btn-outline-primary 这种bootstrap 5.3 默认类',
  `icon_class` VARCHAR(50) DEFAULT NULL COMMENT '图标样式类, 为 bi-book 这种bootstrap icon 默认类',
  `content_cnt` INT UNSIGNED DEFAULT 0 COMMENT '关联内容数量',
  `published_content_cnt` INT UNSIGNED DEFAULT 0 COMMENT '关联已发布内容数量',
  `status_id` TINYINT UNSIGNED DEFAULT 1 COMMENT '状态: 1-启用, 0-禁用',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_name_cn` (`name_cn`)
) ENGINE=InnoDB;

-- 内容标签关联表
CREATE TABLE `content_tag` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `content_id` INT UNSIGNED NOT NULL COMMENT '关联内容ID',
  `tag_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_content_tag` (`content_id`, `tag_id`),
  INDEX `idx_tag_id` (`tag_id`),
  FOREIGN KEY (`content_id`) REFERENCES `content`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`tag_id`) REFERENCES `tag`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 内容合集表
CREATE TABLE `collection` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name_en` VARCHAR(50) NOT NULL,
  `name_cn` VARCHAR(50) NOT NULL,
  `short_desc_en` VARCHAR(100) DEFAULT '',
  `short_desc_cn` VARCHAR(100) DEFAULT '',
  `desc_en` VARCHAR(500),
  `desc_cn` VARCHAR(500),
  `color_class` VARCHAR(50) DEFAULT NULL COMMENT '颜色样式类, 为 btn-outline-primary 这种bootstrap 5.3 默认类',
  `icon_class` VARCHAR(50) DEFAULT NULL COMMENT '图标样式类, 为 bi-book 这种bootstrap icon 默认类',
  `content_cnt` INT UNSIGNED DEFAULT 0 COMMENT '关联内容数量',
  `published_content_cnt` INT UNSIGNED DEFAULT 0 COMMENT '关联已发布内容数量',
  `status_id` TINYINT UNSIGNED DEFAULT 1 COMMENT '状态: 1-启用, 0-禁用',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_name_cn` (`name_cn`)
) ENGINE=InnoDB;

-- 合集与内容映射表
CREATE TABLE `content_collection` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `collection_id` INT UNSIGNED NOT NULL,
  `content_id` INT UNSIGNED NOT NULL COMMENT '关联内容ID',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_collection_content` (`collection_id`, `content_id`),
  INDEX `content_id` (`content_id`),
  FOREIGN KEY (`collection_id`) REFERENCES `collection`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`content_id`) REFERENCES `content`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 后台管理员表
CREATE TABLE `admin_user` (
    `id` INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `email` VARCHAR(100),
    `real_name` VARCHAR(50),
    `avatar` VARCHAR(255) COMMENT '管理员头像',
    `phone` VARCHAR(20),
    `status_id` TINYINT UNSIGNED DEFAULT 1 COMMENT '状态: 1-启用, 0-禁用',
    `role_id` TINYINT UNSIGNED NOT NULL COMMENT '角色权限id: 1-普通管理员, 99-超级管理员',
    `last_login_time` DATETIME,
    `last_login_ip` VARCHAR(45),
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_username` (`username`),
    UNIQUE KEY `uk_email` (`email`)
) ENGINE=InnoDB;

-- 插入默认第三方平台数据
INSERT INTO `admin_user` (`username`, `password_hash`, `role_id`) VALUES
('admin', '$2y$10$4x1JLpPnxgaErapg4Bg4wOyAlZjJF4lzLkbbAgVXWynmCAODx0OyC', 99);