<?php

namespace App\Core;

use App\Constants\ContentStatus;
use App\Core\Database;
use App\Models\Content;

abstract class Model
{
    protected $db;
    protected static string $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $timestamps = true;
    public $isNew = true;

    // Scenario 场景支持
    protected string $scenario = 'default';

    // Active Record 属性
    protected array $attributes = [];
    protected array $original = [];
    public array $errors = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * 获取当前模型的表名
     */
    public static function getTableName(): string
    {
        return static::$table;
    }

    public function getPrimaryKey(): string
    {
        return $this->attributes[$this->primaryKey] ?? '';
    }

    /**
     * 设置场景
     * @param string $scenario 场景名称
     * @return self
     */
    public function setScenario(string $scenario): self
    {
        $this->scenario = $scenario;
        return $this;
    }

    /**
     * 获取当前场景
     * @return string
     */
    public function getScenario(): string
    {
        return $this->scenario;
    }

    /**
     * 定义验证规则
     * @param bool $isUpdate 是否为更新操作
     * @param string|null $scenario 场景名称, 为null时使用当前场景
     * @return array 验证规则
     */
    public function rules(bool $isUpdate = false, ?string $scenario = null): array
    {
        $rules = [
        ];

        return $rules;
    }

    /**
     * 获取当前场景的验证规则
     * @param bool $isUpdate 是否为更新操作
     * @return array
     */
    protected function getRulesForScenario(bool $isUpdate = false): array
    {
        // 获取所有规则
        $allRules = $this->rules($isUpdate);

        // 如果规则为空，返回空数组
        if (empty($allRules)) {
            return [];
        }

        // 检查是否为二维数组结构（场景化规则）
        $firstKey = array_key_first($allRules);
        $firstElement = $allRules[$firstKey];

        // 如果第一个元素是数组且包含规则数组，说明是场景化结构
        if (is_array($firstElement) && isset($allRules['default']) && is_array($allRules['default'])) {
            // 二维数组模式：根据场景返回对应的规则
            return $allRules[$this->scenario] ?? [];
        }

        // 一维数组模式（向后兼容）：直接返回所有规则
        return $allRules;
    }

    /**
     * 静态方法 - 查找所有记录，支持查询条件和输出格式化
     * 
     * @param array $conditions 查询条件数组 
     * @param callable|array|null $formatter 输出格式化：数组表示字段名筛选，callable表示格式化函数
     * @return array 格式化后的数组结果
     */
    public static function findAll(array $conditions = [], ?string $orderBy = null, ?int $limit = null, ?int $offset = 0, callable|array|null $formatter = null): array
    {
        $db = Database::getInstance();
        $table = static::getTableName();
        
        $sql = "SELECT * FROM {$table}";
        $params = [];

        // 处理查询条件
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $field => $value) {
                if (is_array($value)) {
                    $placeholders = implode(',', array_fill(0, count($value), '?'));
                    $whereClause[] = "{$field} IN ({$placeholders})";
                    $params = array_merge($params, $value);
                } else {
                    $whereClause[] = "{$field} = :{$field}";
                    $params[$field] = $value;
                }
            }
            $sql .= " WHERE " . implode(" AND ", $whereClause);
        }

        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }

        if ($limit) {
            $sql .= " LIMIT {$limit}";
            if ($offset > 0) {
                $sql .= " OFFSET {$offset}";
            }
        }

        // 执行查询
        $results = $db->fetchAll($sql, $params);

        // 应用格式化
        if ($formatter !== null) {
            if (is_array($formatter)) {
                // 字段名数组模式：筛选指定字段
                $results = array_map(function($row) use ($formatter) {
                    return array_intersect_key($row, array_flip($formatter));
                }, $results);
            } elseif (is_callable($formatter)) {
                // 格式化函数模式：使用自定义格式化函数
                $results = array_map($formatter, $results);
            }
        }

        return $results;
    }

    /**
     * 魔术方法 - 获取属性
     */
    public function __get(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * 魔术方法 - 设置属性
     */
    public function __set(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * 魔术方法 - 检查属性是否存在
     */
    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    /**
     * 填充模型属性
     */
    public function fill(array $data): self
    {
        $fillableFields = $this->getFillableForScenario();

        foreach ($data as $key => $value) {
            if (in_array($key, $fillableFields) || empty($fillableFields)) {
                $this->attributes[$key] = $value;
            }
        }
        return $this;
    }

    /**
     * 获取当前场景的可填充字段
     * @return array
     */
    protected function getFillableForScenario(): array
    {
        // 如果 fillable 为空，返回空数组
        if (empty($this->fillable)) {
            return [];
        }

        // 检查 fillable 是否为二维数组
        $firstElement = reset($this->fillable);
        if (is_array($firstElement)) {
            // 二维数组模式：根据场景返回对应的fillable
            return $this->fillable[$this->scenario] ?? [];
        }

        // 一维数组模式（向后兼容）：直接返回fillable
        return $this->fillable;
    }

    /**
     * 获取所有属性
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * 设置原始数据（用于追踪变更）
     */
    public function setOriginal(array $data): void
    {
        $this->original = $data;
        $this->attributes = array_merge($this->attributes, $data);
    }

    /**
     * 检查模型是否已修改
     */
    public function isDirty(): bool
    {
        return $this->attributes !== $this->original;
    }

    /**
     * 检查是否为新记录
     * @return bool 是否为新记录
     */
    public function isNew(): bool
    {
        return $this->isNew;
    }

    /**
     * 设置记录状态
     * @param bool $isNew 是否为新记录
     * @return void
     */
    public function setNew(bool $isNew): void
    {
        $this->isNew = $isNew;
    }

    public static function find(int $id): ?self
    {
        $db = Database::getInstance();
        $sql = "SELECT * FROM " . static::getTableName() . " WHERE id = :id LIMIT 1";
        $result = $db->fetch($sql, ['id' => $id]);
        
        if ($result) {
            $instance = new static();
            $instance->setOriginal($result);
            $instance->setNew(false);
            return $instance;
        }
        
        return null;
    }

    /**
     * 静态方法 - 查找单条记录，支持查询条件和输出格式化
     *
     * @param array $conditions 查询条件数组
     * @param int|null $offset 结果集的偏移量
     * @param string|null $orderBy 排序方式
     * @param callable|array|null $formatter 输出格式化：数组表示字段名筛选，callable表示格式化函数
     * @return array|null 格式化后的单条记录数组，未找到则返回 null
     */
    public static function findOne(array $conditions = [], ?string $orderBy = null, ?int $offset = 0, callable|array|null $formatter = null): ?array
    {
        $db = Database::getInstance();
        $table = static::getTableName();

        $sql = "SELECT * FROM {$table}";
        $params = [];

        // 处理查询条件
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $field => $value) {
                if (is_array($value)) {
                    $placeholders = implode(',', array_fill(0, count($value), '?'));
                    $whereClause[] = "{$field} IN ({$placeholders})";
                    $params = array_merge($params, $value);
                } else {
                    $whereClause[] = "{$field} = :{$field}";
                    $params[$field] = $value;
                }
            }
            $sql .= " WHERE " . implode(" AND ", $whereClause);
        }

        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }

        // 强制限制只查找一条记录
        $sql .= " LIMIT 1";
        if ($offset > 0) {
            $sql .= " OFFSET {$offset}";
        }

        // 执行查询，获取单条记录
        $result = $db->fetch($sql, $params);

        // 如果没有找到记录，直接返回 null
        if (!$result) {
            return null;
        }

        // 应用格式化
        if ($formatter !== null) {
            if (is_array($formatter)) {
                // 字段名数组模式：筛选指定字段
                $result = array_intersect_key($result, array_flip($formatter));
            } elseif (is_callable($formatter)) {
                // 格式化函数模式：使用自定义格式化函数
                $result = $formatter($result);
            }
        }

        return $result;
    }

    /**
     * 根据中文名或英文名查找标签
     * @param string $nameCn 中文名称
     * @param string $nameEn 英文名称
     * @return array|null 标签数据或null
     */
    public static function findByName(string $nameCn, string $nameEn): ?array
    {
        $db = Database::getInstance();
        $sql = "SELECT * FROM " . static::getTableName() . " WHERE name_cn = :name_cn OR name_en = :name_en LIMIT 1";
        $params = [
            'name_cn' => $nameCn,
            'name_en' => $nameEn
        ];

        $result = $db->fetch($sql, $params);
        
        return $result ?: null;
    }

    /**
     * 获取字段搜索策略配置
     * 子类可以重写此方法来自定义字段搜索行为
     * 
     * @return array 字段搜索策略配置
     */
    protected static function getFieldSearchStrategies(): array
    {
        return [
            'id' => 'exact',
            'status_id' => 'exact',
            'code' => 'like',
            'name' => 'bilingual_like',
            'title' => 'bilingual_like',
            'short_desc' => 'bilingual_like',
            'desc' => 'bilingual_like',
            'icon_class' => 'like',
            'content_cnt' => 'custom',
            'view_cnt' => 'custom',
            'play_cnt' => 'custom'
        ];
    }

    /**
     * 根据过滤条件获取所有标签数据（不分页，用于JS处理）
     * 支持多字段搜索过滤，使用混合配置驱动模式
     */
    public static function findAllWithFilters(array $filters = []): array
    {
        $db = Database::getInstance();
        $sql = "SELECT * FROM " . static::getTableName();
        $params = [];
        $whereConditions = [];

        // 处理搜索条件
        if (!empty($filters)) {
            $fieldStrategies = static::getFieldSearchStrategies();
            
            foreach ($filters as $field => $value) {
                if (empty($value) && $value !== '0') {
                    continue; // skip for empty null or empty string
                }

                // 获取字段策略，默认为 'auto'
                $strategy = $fieldStrategies[$field] ?? 'auto';
                
                if ($strategy === 'custom') {
                    // 处理自定义复杂逻辑
                    static::handleCustomFieldFilter($field, $value, $whereConditions, $params);
                } else {
                    // 处理标准策略
                    static::handleStandardFieldFilter($field, $value, $strategy, $whereConditions, $params);
                }
            }
        }

        if (!empty($whereConditions)) {
            $sql .= " WHERE " . implode(" AND ", $whereConditions);
        }

        // 排序
        $orderBy = $filters['order_by'] ?? 'id DESC';
        $sql .= " ORDER BY {$orderBy}";

        return $db->fetchAll($sql, $params);
    }

    /**
     * for quick get model list for multi-select init.
     *  force format to [[id, text], ... ]
     * @param array $conditions 查询条件
     * @param array $fieldMapping 字段映射配置，例如 ['id'=>'id', 'text'=>'name_cn']
     * @param int|null $limit 限制数量
     * @param int|null $offset 偏移量
     * @param string|null $orderBy 排序规则
     * @return array 格式化后的数组
     */
    public static function loadList(?array $conditions = [], ?array $fieldMapping = ['id'=>'id', 'text'=>'name_cn'], ?int $limit = null, ?int $offset = 0, ?string $orderBy = null): array
    {
        $models = static::findAll($conditions, $orderBy, $limit, $offset);

        $returnArray = [];
        foreach ($models as $oneModel) {
            $item = [];
            foreach ($fieldMapping as $outputKey => $sourceField) {

                $item[$outputKey] =  $oneModel[$sourceField] ?? '';

                if ($outputKey === 'id'){
                    $item[$outputKey] = (int)$item[$outputKey];
                }
            }
            $returnArray[] = $item;
        }
        return $returnArray;
    }

    /**
     * 处理自定义字段过滤逻辑
     * 子类可以重写此方法来处理特定字段的复杂逻辑
     *   - range number,  like 5-10, > < = >= <= 5
     * 
     * @param string $field 字段名
     * @param mixed $value 搜索值
     * @param array &$whereConditions WHERE条件数组
     * @param array &$params 参数数组
     */
    protected static function handleCustomFieldFilter(string $field, $value, array &$whereConditions, array &$params): void
    {
        switch ($field) {
            case 'content_cnt':
            case 'play_cnt':
            case 'view_cnt':
                // 处理数量范围搜索，支持格式如 "5-10" 或 ">5" 或 "10"
                if (strpos($value, '-') !== false) {
                    $range = explode('-', $value);
                    if (count($range) === 2 && is_numeric($range[0]) && is_numeric($range[1])) {
                        $whereConditions[] = "{$field} BETWEEN :cnt_min AND :cnt_max";
                        $params['cnt_min'] = (int)$range[0];
                        $params['cnt_max'] = (int)$range[1];
                    }
                } elseif (preg_match('/^([><=]+)(\d+)$/', $value, $matches)) {
                    $operator = $matches[1];
                    $number = (int)$matches[2];
                    if (in_array($operator, ['>', '<', '>=', '<=', '='])) {
                        $whereConditions[] = "{$field} {$operator} :cnt_value";
                        $params['cnt_value'] = $number;
                    }
                } elseif (is_numeric($value)) {
                    $whereConditions[] = "{$field} = :cnt_exact";
                    $params['cnt_exact'] = (int)$value;
                }
                break;
        }
    }

    /**
     * 处理标准字段过滤逻辑
     *
     * @param string $field 字段名
     * @param mixed $value 搜索值
     * @param string $strategy 搜索策略
     * @param array &$whereConditions WHERE条件数组
     * @param array &$params 参数数组
     */
    protected static function handleStandardFieldFilter(string $field, $value, string $strategy, array &$whereConditions, array &$params): void
    {
        $paramKey = "search_{$field}";

        switch ($strategy) {
            case 'exact':
                // 检查值是否包含逗号（多选的情况）
                if (is_string($value) && strpos($value, ',') !== false) {
                    // 多选情况：使用 IN 查询
                    $ids = array_map('trim', explode(',', $value));
                    $ids = array_filter($ids, function($id) {
                        return $id !== '' && is_numeric($id);
                    });
                    $ids = array_map('intval', $ids);

                    if (!empty($ids)) {
                        $placeholders = [];
                        foreach ($ids as $index => $id) {
                            $placeholder = "{$paramKey}_{$index}";
                            $placeholders[] = ":{$placeholder}";
                            $params[$placeholder] = $id;
                        }
                        $whereConditions[] = "{$field} IN (" . implode(', ', $placeholders) . ")";
                    }
                } else {
                    // 单选情况：使用 = 查询
                    $whereConditions[] = "{$field} = :{$paramKey}";
                    $params[$paramKey] = is_numeric($value) ? (int)$value : $value;
                }
                break;

            case 'like':
                $whereConditions[] = "{$field} LIKE :{$paramKey}";
                $params[$paramKey] = "%{$value}%";
                break;

            case 'bilingual_like':
                // 双语模糊搜索 (中英文字段)
                $whereConditions[] = "({$field}_cn LIKE :{$paramKey}_cn OR {$field}_en LIKE :{$paramKey}_en)";
                $params["{$paramKey}_cn"] = "%{$value}%";
                $params["{$paramKey}_en"] = "%{$value}%";

                break;

            case 'auto':
            default:
                // 默认规则：数字用 =，字符串用 LIKE
                if (is_numeric($value)) {
                    $whereConditions[] = "{$field} = :{$paramKey}";
                    $params[$paramKey] = (int)$value;
                } else {
                    $whereConditions[] = "{$field} LIKE :{$paramKey}";
                    $params[$paramKey] = "%{$value}%";
                }
                break;
        }
    }


    public function create(array $data): int
    {
        $data = $this->filterFillable($data);

        if ($this->timestamps) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $id = $this->db->insert(static::getTableName(), $data);
        
        if ($id > 0) {
            $this->setNew(false);
        }
        
        return $id;
    }

    public function update(int $id, array $data): bool
    {
        $data = $this->filterFillable($data);

        if (!$this->beforeSave()){
            return false;
        }

        if ($this->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $updated = $this->db->update(
            static::getTableName(),
            $data,
            "{$this->primaryKey} = :id",
            ['id' => $id]
        );

        return $updated > 0;
    }

    public function delete(int $id): bool
    {
        $deleted = $this->db->delete(
            static::getTableName(),
            "{$this->primaryKey} = :id",
            ['id' => $id]
        );

        return $deleted > 0;
    }

    public static function count(array $conditions = []): int
    {
        $db = Database::getInstance();
        $sql = "SELECT COUNT(*) as count FROM " . static::getTableName();
        $params = [];

        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $field => $value) {
                $whereClause[] = "{$field} = :{$field}";
                $params[$field] = $value;
            }
            $sql .= " WHERE " . implode(" AND ", $whereClause);
        }

        $result = $db->fetch($sql, $params);
        return (int)$result['count'];
    }

    public static function exists(int $id): bool
    {
        $db = Database::getInstance();
        $sql = "SELECT 1 FROM " . static::getTableName() . " WHERE id = :id LIMIT 1";
        return (bool)$db->fetch($sql, ['id' => $id]);
    }

    public function query(string $sql, array $params = []): \PDOStatement
    {
        return $this->db->query($sql, $params);
    }

    protected function filterFillable(array $data): array
    {
        $fillableFields = $this->getFillableForScenario();

        if (empty($fillableFields)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($fillableFields));
    }

    /**
     * always run b4 save to db.
     * @return bool
     */
    public function beforeSave(): bool
    {
        // add b4 save code, change bool return.
        return true;
    }

    /**
     * 保存模型（新增或更新）
     */
    public function save(): bool
    {
        // 验证数据
        $this->errors = [];
        if (!$this->validate() || !$this->beforeSave()) {
            return false;
        }

        try {
            if ($this->isNew()) {
                $data = $this->getAttributes();
                if ($this->timestamps) {
                    $data['created_at'] = date('Y-m-d H:i:s');
                    $data['updated_at'] = date('Y-m-d H:i:s');
                }
                
                $id = $this->db->insert(static::getTableName(), $this->filterFillable($data));
                $this->attributes[$this->primaryKey] = $id;
                $this->setNew(false);
                $this->setOriginal($this->attributes);
            } else {
                $data = $this->getAttributes();
                if ($this->timestamps) {
                    $data['updated_at'] = date('Y-m-d H:i:s');
                }
                
                $this->db->update(
                    static::getTableName(),
                    $this->filterFillable($data),
                    "{$this->primaryKey} = :id",
                    ['id' => $this->attributes[$this->primaryKey]]
                );
                $this->setOriginal($this->attributes);
            }
            return true;
        } catch (\Exception $e) {
            $this->errors['general'] = $e->getMessage();
            return false;
        }
    }

    /**
     * 验证模型数据
     */
    public function validate(): bool
    {
        $this->errors = [];

        // 子类可以重写此方法实现具体验证逻辑
        if (method_exists($this, 'rules')) {
            $rules = $this->getRulesForScenario(!$this->isNew());
            $excludeId = $this->isNew() ? null : $this->attributes[$this->primaryKey] ?? null;
            $this->errors = $this->validateRules($this->attributes, $rules, $excludeId);
        }

        return empty($this->errors);
    }

    /**
     * 执行验证规则
     * @param array $data 数据
     * @param array $rules 规则
     * @param ?int $excludeId 排除的ID
     * @return array 错误信息
     */
    protected function validateRules(array $data, array $rules, ?int $excludeId = null): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleSet) {
            $value = $data[$field] ?? null;
            $fieldRules = explode('|', $ruleSet);

            foreach ($fieldRules as $rule) {
                $ruleParts = explode(':', $rule);
                $ruleName = $ruleParts[0];
                $ruleParam = $ruleParts[1] ?? null;

                switch ($ruleName) {
                    case 'required':
                        if (empty($value) && $value !== '0' && $value !== 0) {
                            $errors[$field] = $this->getErrorMessage($field, 'required');
                        }
                        break;

                    case 'max':
                        if (!empty($value) && mb_strlen($value) > (int)$ruleParam) {
                            $errors[$field] = $this->getErrorMessage($field, 'max', $ruleParam);
                        }
                        break;

                    case 'min':
                        if (!empty($value) && mb_strlen($value) < (int)$ruleParam) {
                            $errors[$field] = $this->getErrorMessage($field, 'min', $ruleParam);
                        }
                        break;

                    case 'unique':
                        if (!empty($value)) {
                            $table = $ruleParam ?: static::getTableName();
                            $exists = $this->checkUnique($field, $value, $table, $excludeId);
                            if ($exists) {
                                $errors[$field] = $this->getErrorMessage($field, 'unique');
                            }
                        }
                        break;

                    case 'numeric':
                        if (!empty($value) && !is_numeric($value)) {
                            $errors[$field] = $this->getErrorMessage($field, 'numeric');
                        }
                        break;
                }
            }
        }

        return $errors;
    }

    /**
     * 检查字段值是否唯一
     * @param string $field 字段名
     * @param mixed $value 值
     * @param string $table 表名
     * @param ?int $excludeId 排除的ID
     * @return bool 是否存在重复
     */
    protected function checkUnique(string $field, $value, string $table, ?int $excludeId = null): bool
    {
        $sql = "SELECT 1 FROM {$table} WHERE {$field} = :value";
        $params = ['value' => $value];

        if ($excludeId !== null) {
            $sql .= " AND {$this->primaryKey} != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $sql .= " LIMIT 1";

        return (bool)$this->db->fetch($sql, $params);
    }

    /**
     * 获取错误信息
     * @param string $field 字段名
     * @param string $rule 规则名
     * @param ?string $param 参数
     * @return string 错误信息
     */
    protected function getErrorMessage(string $field, string $rule, ?string $param = null): string
    {
        $fieldLabels = $this->getFieldLabels();
        $fieldLabel = $fieldLabels[$field] ?? $field;

        $messages = [
            'required' => "{$fieldLabel}不能为空",
            'max' => "{$fieldLabel}不能超过{$param}个字符",
            'min' => "{$fieldLabel}不能少于{$param}个字符",
            'unique' => "{$fieldLabel}已存在",
            'numeric' => "{$fieldLabel}必须是数字"
        ];

        return $messages[$rule] ?? "{$fieldLabel}验证失败";
    }

    /**
     * 获取字段标签，子类可重写
     * @return array 字段标签映射
     */
    protected function getFieldLabels(): array
    {
        return [];
    }



    //// bulk actions
    //bulk update
    public static function bulkUpdateStatus(array $targetIds, int $status): array
    {
        $db = Database::getInstance();
        $returnCnt = ['total'=>count($targetIds), 'changed'=>0, 'fail'=>0];
        if (empty($targetIds)) {
            return $returnCnt;
        }

        // 分批执行，保证性能. 定义每个批次的大小，例如 1000
        $chunkSize = 1000;
        $tagIdChunks = array_chunk($targetIds, $chunkSize);

        foreach ($tagIdChunks as $chunk) {
            $placeholders = implode(',', array_fill(0, count($chunk), '?'));
            $sql = "UPDATE " . static::getTableName() . " SET status_id = ?, updated_at = NOW() WHERE id IN ({$placeholders})";

            //support content.pub_at auto update
            if (static::getTableName() == Content::getTableName() && $status == ContentStatus::PUBLISHED->value){
                $sql = "UPDATE " . static::getTableName() . " SET status_id = ?, updated_at = NOW(), pub_at = NOW() WHERE id IN ({$placeholders})";
            }

            $params = array_merge([$status], $chunk);

            // 累加每次成功更新的数量
            $returnCnt['changed'] += $db->execute($sql, $params);
        }


        $returnCnt['fail'] = $returnCnt['total'] - $returnCnt['changed'];

        return $returnCnt;
    }

    //bulk delete
    public static function bulkDelete(array $targetIds): array
    {
        $db = Database::getInstance();
        $returnCnt = ['total'=>count($targetIds), 'changed'=>0, 'fail'=>0];
        if (empty($targetIds)) {
            return $returnCnt;
        }


        $db->beginTransaction();

        try {
            // 分批执行，保证性能. 定义每个批次的大小，例如 1000
            $chunkSize = 1000;
            $tagIdChunks = array_chunk($targetIds, $chunkSize);

            foreach ($tagIdChunks as $chunk) {
                $placeholders = implode(',', array_fill(0, count($chunk), '?'));

                $returnCnt['changed'] += $db->execute("DELETE FROM " . static::getTableName() . " WHERE id IN ({$placeholders})", $chunk);
            }

            $db->commit();

            $returnCnt['fail'] = $returnCnt['total'] - $returnCnt['changed'];
            return $returnCnt;

        } catch (\Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    /**
     * 准备CSV导入数据 - 子类可重写来自定义数据准备逻辑
     * 
     * @param array $csvRowData CSV行数据
     * @return array 处理后的数据
     */
    public function prepareBulkImportData(array $csvRowData): array
    {
        // 默认实现，子类可以重写
        return $csvRowData;
    }

    /**
     * 验证CSV导入数据 - 基于现有的validate方法
     * 
     * @param array $data 要验证的数据
     * @return bool 是否验证通过
     */
    public function validateBulkImportData(array $data): bool
    {
        $errors = $this->validate($data, false);
        return empty($errors);
    }

    /**
     * 检查导入数据是否重复 - 子类可重写
     * 
     * @param array $data 导入数据
     * @return bool 是否重复
     */
    public function isDuplicateImportData(array $data): bool
    {
        // 默认实现，基于name_cn和name_en检查
        if (isset($data['name_cn']) && isset($data['name_en'])) {
            return (bool)$this->findByName($data['name_cn'], $data['name_en']);
        }
        return false;
    }

    /**
     * 批量导入单条记录 - 通用方法
     *
     * @param array $csvRowData CSV行数据
     * @return bool 是否成功
     */
    public function importSingleRecord(array $csvRowData): bool
    {
        try {
            // 1. 准备数据
            $data = $this->prepareBulkImportData($csvRowData);

            // 2. 验证数据
            if (!$this->validateBulkImportData($data)) {
                return false;
            }

            // 3. 检查重复
            if ($this->isDuplicateImportData($data)) {
                return false; // 跳过重复数据
            }

            // 4. 创建记录
            $this->create($data);
            return true;

        } catch (\Exception $e) {
            error_log("Import single record error: " . $e->getMessage());
            return false;
        }
    }
}