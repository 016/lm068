### MySQL 数据库操作规则
- 因为使用了 PDO::ATTR_EMULATE_PREPARES => false, 所以SQL语句中不允许出现同名占位符, 就算对应同一个参数, 也要严格使用不同的占位符
  - 正确做法 SELECT * FROM tag WHERE id = :id AND (name_cn LIKE :name_cn OR name_en LIKE :name_en) ORDER BY created_at DESC
  - 错误做法 SELECT * FROM tag WHERE id = :id AND (name_cn LIKE :name OR name_en LIKE :name) ORDER BY created_at DESC