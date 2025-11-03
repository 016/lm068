-- 数据库迁移文件: 为 admin_user 表添加 remember_token 字段
-- 用于实现"记住我"功能的持久化登录
-- 创建日期: 2025-11-04
-- 说明: 此字段存储哈希后的记住我 token，用于浏览器关闭后的自动登录

USE `lm068`;

-- 检查字段是否已存在，避免重复添加
ALTER TABLE `admin_user`
ADD COLUMN IF NOT EXISTS `remember_token` VARCHAR(64) DEFAULT NULL COMMENT '记住我功能的token(SHA256哈希后存储)' AFTER `last_login_ip`;

-- 为 remember_token 字段添加索引，提高查询性能
ALTER TABLE `admin_user`
ADD INDEX IF NOT EXISTS `idx_remember_token` (`remember_token`);

-- 验证字段是否添加成功
SELECT
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT,
    COLUMN_COMMENT
FROM
    INFORMATION_SCHEMA.COLUMNS
WHERE
    TABLE_SCHEMA = 'lm068'
    AND TABLE_NAME = 'admin_user'
    AND COLUMN_NAME = 'remember_token';
