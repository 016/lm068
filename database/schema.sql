-- MySQL 5.7 标准数据库结构文件

CREATE DATABASE IF NOT EXISTS video_site;
USE video_site;

-- 用户表
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  status ENUM('active', 'banned') DEFAULT 'active', -- 用户状态，封停可用
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 视频表
CREATE TABLE videos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT, -- 支持markdown格式纯文本存储
  url VARCHAR(255) NOT NULL, -- 第三方视频链接
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  views INT DEFAULT 0
);

-- 评论表
CREATE TABLE comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  video_id INT NOT NULL,
  comment TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (video_id) REFERENCES videos(id)
);

-- 收藏表
CREATE TABLE favorites (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  video_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (video_id) REFERENCES videos(id)
);

-- 邮件订阅表
CREATE TABLE subscriptions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(100) NOT NULL UNIQUE,
  status ENUM('subscribed', 'unsubscribed') DEFAULT 'subscribed', -- 订阅状态，不直接删除，便于统计
  subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 视频标签表（支持多语言）
CREATE TABLE video_tags (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name_en VARCHAR(50) NOT NULL UNIQUE,
  name_cn VARCHAR(50) DEFAULT NULL,
  name_other VARCHAR(50) DEFAULT NULL -- 其他语言支持字段
);

-- 视频标签关联表
CREATE TABLE video_tag_map (
  video_id INT NOT NULL,
  tag_id INT NOT NULL,
  PRIMARY KEY (video_id, tag_id),
  FOREIGN KEY (video_id) REFERENCES videos(id),
  FOREIGN KEY (tag_id) REFERENCES video_tags(id)
);

-- 视频合集表（支持多语言）
CREATE TABLE video_collections (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name_en VARCHAR(255) NOT NULL,
  name_cn VARCHAR(255) DEFAULT NULL,
  name_other TEXT DEFAULT NULL,
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 合集与视频映射表
CREATE TABLE collection_videos (
  collection_id INT NOT NULL,
  video_id INT NOT NULL,
  PRIMARY KEY (collection_id, video_id),
  FOREIGN KEY (collection_id) REFERENCES video_collections(id),
  FOREIGN KEY (video_id) REFERENCES videos(id)
);