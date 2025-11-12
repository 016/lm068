<?php

namespace App\Core;

use App\Constants\ContentStatus;
use App\Core\Database;
use App\Core\Relations\BelongsTo;
use App\Core\Relations\HasMany;
use App\Core\Relations\HasOne;
use App\Core\Relations\Relation;
use App\Helpers\ClassHelper;
use App\Helpers\UrlHelper;
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

    // 关系数据存储
    protected array $relations = [];

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
     * ============================================
     * Active Record 查询方法 - 新增
     * ============================================
     */

    /**
     * 创建查询构建器
     */
    public static function query(): QueryBuilder
    {
        return new QueryBuilder(static::class);
    }

    /**
     * 查找单个记录 - 支持 ID 或条件数组
     */
    public static function find($idOrConditions)
    {
        if (is_numeric($idOrConditions) || is_string($idOrConditions)) {
            // 按 ID 查找
            return static::query()->where(['id' => $idOrConditions])->first();
        } elseif (is_array($idOrConditions)) {
            // 按条件查找
            return static::query()->where($idOrConditions)->first();
        }
        return null;
    }

    /**
     * 查找所有记录
     */
    public static function findAll(string|array $conditions = [], ?string $orderBy = null, ?int $limit = null, ?int $offset = 0): array
    {
        $query = static::query();

        if (!empty($conditions)) {
            if (is_array($conditions)) {
                $query->where($conditions);
            }else{
                $query->whereRaw($conditions);
            }
        }

        if ($orderBy) {
            $query->orderBy($orderBy);
        }

        if ($limit) {
            $query->limit($limit)->offset($offset);
        }

        $models = $query->all();

        return $models;
    }

    /**
     * where 条件查询
     */
    public static function where(array $conditions): QueryBuilder
    {
        return static::query()->where($conditions);
    }

    /**
     * ============================================
     * 关系定义方法 - 新增
     * ============================================
     */

    /**
     * 定义 HasOne 关系
     * @param string $related 关联模型类名
     * @param string|null $foreignKey 外键字段(默认: 当前模型名_id)
     * @param string|null $localKey 本地键(默认: id)
     */
    protected function hasOne(string $related, ?string $foreignKey = null, ?string $localKey = null): HasOne
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $localKey = $localKey ?: $this->primaryKey;

        return new HasOne($this, $related, $foreignKey, $localKey);
    }

    /**
     * 定义 HasMany 关系
     */
    protected function hasMany(string $related, ?string $foreignKey = null, ?string $localKey = null): HasMany
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $localKey = $localKey ?: $this->primaryKey;

        return new HasMany($this, $related, $foreignKey, $localKey);
    }

    /**
     * 定义 BelongsTo 关系
     * @param string $related 关联模型类名
     * @param string|null $foreignKey 外键字段
     * @param string|null $ownerKey 关联模型键(默认: id)
     */
    protected function belongsTo(string $related, ?string $foreignKey = null, ?string $ownerKey = null): BelongsTo
    {
        // 自动推断外键名: category_id
        if (!$foreignKey) {
            $relatedClass = ClassHelper::class_basename($related);
            $foreignKey = strtolower($relatedClass) . '_id';
        }

        $ownerKey = $ownerKey ?: 'id';

        return new BelongsTo($this, $related, $foreignKey, $ownerKey);
    }

    /**
     * 获取默认外键名
     */
    protected function getForeignKey(): string
    {
        $className = ClassHelper::class_basename(static::class);
        return strtolower($className) . '_id';
    }

    /**
     * ============================================
     * 魔术方法 - 支持关系访问
     * ============================================
     */

    /**
     * 魔术方法 - 获取属性或关系
     */
    public function __get(string $key)
    {
        // 先检查属性
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }

        // 检查是否已加载的关系
        if (array_key_exists($key, $this->relations)) {
            return $this->relations[$key];
        }

        // 尝试加载关系
        if (method_exists($this, $key)) {
            $relation = $this->$key();
            if ($relation instanceof Relation) {
                $this->relations[$key] = $relation->get();
                return $this->relations[$key];
            }
        }

        return null;
    }

    /**
     * 魔术方法 - 设置属性
     */
    public function __set(string $key, $value): void
    {
        // 如果是关系数据,存储到 relations
        if (method_exists($this, $key)) {
            $relation = $this->$key();
            if ($relation instanceof Relation) {
                $this->relations[$key] = $value;
                return;
            }
        }

        $this->attributes[$key] = $value;
    }

    /**
     * 魔术方法 - 检查属性是否存在
     */
    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]) || isset($this->relations[$key]);
    }

    /**
     * ============================================
     * Active Record 保存方法 - 增强
     * ============================================
     */

    /**
     * 保存模型(新增或更新) - 支持关系保存
     */
    public function save(): bool
    {
        // 验证数据
        $this->errors = [];
        if (!$this->validate() || !$this->beforeSave()) {
            return false;
        }

        try {
            $db = Database::getInstance();
            $db->beginTransaction();

            // 保存主模型
            if ($this->isNew()) {
                $data = $this->getAttributes();
                if ($this->timestamps) {
                    $data['created_at'] = date('Y-m-d H:i:s');
                    $data['updated_at'] = date('Y-m-d H:i:s');
                }

                $id = $db->insert(static::getTableName(), $this->filterFillable($data));
                $this->attributes[$this->primaryKey] = $id;
                $this->setNew(false);
            } else {
                $data = $this->getAttributes();
                if ($this->timestamps) {
                    $data['updated_at'] = date('Y-m-d H:i:s');
                }

                $db->update(
                    static::getTableName(),
                    $this->filterFillable($data),
                    "{$this->primaryKey} = :id",
                    ['id' => $this->attributes[$this->primaryKey]]
                );
            }

            // 保存关系数据
            $this->saveRelations();

            $db->commit();

            $this->afterSave();

            $this->setOriginal($this->attributes);
            return true;

        } catch (\Exception $e) {
            $db->rollback();
            $this->errors['general'] = $e->getMessage();
            return false;
        }
    }

    /**
     * 保存关系数据
     */
    protected function saveRelations(): void
    {
        foreach ($this->relations as $name => $related) {
            if (!method_exists($this, $name)) {
                continue;
            }

            $relation = $this->$name();

            if (!$relation instanceof Relation) {
                continue;
            }

            // HasMany 关系
            if ($relation instanceof HasMany) {
                if (is_array($related)) {
                    foreach ($related as $model) {
                        if ($model instanceof Model) {
                            $relation->save($model);
                        }
                    }
                }
            } else {
                // HasOne 和 BelongsTo
                if ($related instanceof Model) {
                    $relation->save($related);
                }
            }
        }
    }

    /**
     * 删除模型
     */
    public function delete(): bool
    {
        if ($this->isNew()) {
            return false;
        }

        try {
            $deleted = $this->db->delete(
                static::getTableName(),
                "{$this->primaryKey} = :id",
                ['id' => $this->attributes[$this->primaryKey]]
            );

            if ($deleted > 0) {
                $this->setNew(true);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            $this->errors['general'] = $e->getMessage();
            return false;
        }
    }

    /**
     * ============================================
     * 原有方法保持不变
     * ============================================
     */

    // 场景相关
    public function setScenario(string $scenario): self
    {
        $this->scenario = $scenario;
        return $this;
    }

    public function getScenario(): string
    {
        return $this->scenario;
    }

    public function rules(bool $isUpdate = false, ?string $scenario = null): array
    {
        return [];
    }

    protected function getRulesForScenario(bool $isUpdate = false): array
    {
        $allRules = $this->rules($isUpdate);

        if (empty($allRules)) {
            return [];
        }

        $firstKey = array_key_first($allRules);
        $firstElement = $allRules[$firstKey];

        if (is_array($firstElement) && isset($allRules['default']) && is_array($allRules['default'])) {
            return $allRules[$this->scenario] ?? [];
        }

        return $allRules;
    }

    // 数据填充
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

    protected function getFillableForScenario(): array
    {
        if (empty($this->fillable)) {
            return [];
        }

        $firstElement = reset($this->fillable);
        if (is_array($firstElement)) {
            return $this->fillable[$this->scenario] ?? [];
        }

        return $this->fillable;
    }

    // 属性访问
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setOriginal(array $data): void
    {
        $this->original = $data;
        $this->attributes = array_merge($this->attributes, $data);
    }

    public function isDirty(): bool
    {
        return $this->attributes !== $this->original;
    }

    public function isNew(): bool
    {
        return $this->isNew;
    }

    public function setNew(bool $isNew): void
    {
        $this->isNew = $isNew;
    }

    /**
     * 根据指定语言获取标题
     *
     * @param string|null $lang 语言代码 (zh/en), 为null时使用当前语言
     * @return string 对应语言的标题
     */
    public function getTitle(?string $lang = null): string
    {
        $lang = $lang ?? \App\Core\I18n::getCurrentLang();
        $title = $lang === 'zh' ? $this->name_cn : $this->name_en;

        // 如果指定语言的标题为空,降级到另一个语言
        if (empty($title)) {
            $title = $lang === 'zh' ? $this->name_en : $this->name_cn;
        }

        return $title ?? '';
    }

    /**
     * generate detail url for all model type.
     * @param string|null $targetLang
     * @param array $queryParams
     * @return string
     */
    public function generateDetailUrl(?string $targetLang = null, array $queryParams = []): string
    {
        //content_type to content-type
        $preFixTitle = str_replace('_', '-', static::$table);

        // 构建基础URL 前缀
        $urlPrefix = "/".$preFixTitle."/".HashId::encode($this->id)."/".UrlHelper::formatString($this->getTitle('en'));

        return UrlHelper::generateUri($urlPrefix, $targetLang, $queryParams);
    }


    // 验证
    public function validate(): bool
    {
        $this->errors = [];

        if (method_exists($this, 'rules')) {
            $rules = $this->getRulesForScenario(!$this->isNew());
            $excludeId = $this->isNew() ? null : $this->attributes[$this->primaryKey] ?? null;
            $this->errors = $this->validateRules($this->attributes, $rules, $excludeId);
        }

        return empty($this->errors);
    }

    protected function validateRules(array $data, array $rules, ?int $excludeId = null): array
    {
        // 保持原有验证逻辑
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

    protected function getFieldLabels(): array
    {
        return [];
    }

    protected function filterFillable(array $data): array
    {
        $fillableFields = $this->getFillableForScenario();

        if (empty($fillableFields)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($fillableFields));
    }

    public function beforeSave(): bool
    {
        return true;
    }

    public function afterSave()
    {
        //
    }

    // 保留原有静态方法
    public static function count(array $conditions = []): int
    {
        return static::query()->where($conditions)->count();
    }

    public static function exists(int $id): bool
    {
        return static::find($id) !== null;
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
    public static function loadList(null|string|array $conditions = [], ?array $fieldMapping = ['id'=>'id', 'text'=>'name_cn'], ?int $limit = null, ?int $offset = 0, ?string $orderBy = null): array
    {
        $models = static::findAll($conditions, $orderBy, $limit, $offset);

        $returnArray = [];
        foreach ($models as $oneModel) {
            $item = [];
            foreach ($fieldMapping as $outputKey => $sourceField) {

                $item[$outputKey] =  $oneModel->$sourceField ?? '';

                if ($outputKey === 'id'){
                    $item[$outputKey] = (int)$item[$outputKey];
                }
            }
            $returnArray[] = $item;
        }
        return $returnArray;
    }


    // 如果下面的方法和上面重复，则代表下面的方法为旧方法,保留只是为了兼容。
    // 原则上不在新代码中使用下面的代码

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
            'pv_cnt' => 'custom',
            'play_cnt' => 'custom'
        ];
    }

    /**
     * 根据过滤条件获取所有标签数据（不分页，用于JS处理）
     * 支持多字段搜索过滤，使用混合配置驱动模式
     */
    public static function findAllWithFilters(array $filters = []): array
    {
        $sqlBuilder = static::query();
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
            $sqlBuilder->whereRaw(implode(' AND ', $whereConditions), $params);
        }

        // 排序
        $sqlBuilder->orderBy($filters['order_by'] ?? 'id DESC');

        return $sqlBuilder->all();
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
            case 'pv_cnt':
                // 处理数量范围搜索，支持格式如 "5-10" 或 ">5" 或 "10"
                if (str_contains($value, '-')) {
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
                if (is_string($value) && str_contains($value, ',')) {
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
            // 1st normal update without pub_at check
            $placeholders = implode(',', array_fill(0, count($chunk), '?'));
            $sql = "UPDATE " . static::getTableName() . " SET status_id = ?, updated_at = NOW() WHERE id IN ({$placeholders})";

            $params = array_merge([$status], $chunk);
            // 累加每次成功更新的数量
            $returnCnt['changed'] += $db->execute($sql, $params);

            //2nd only update if pub_at is null
            //support content.pub_at auto update
            if (static::getTableName() == Content::getTableName() && $status == ContentStatus::PUBLISHED->value){
                $sql = "UPDATE " . static::getTableName() . " SET status_id = ?, updated_at = NOW(), pub_at = NOW() WHERE id IN ({$placeholders}) and pub_at IS NULL";
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