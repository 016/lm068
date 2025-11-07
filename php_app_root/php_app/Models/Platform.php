<?php

namespace App\Models;

use App\Core\Model;

class Platform extends Model
{
    protected static string $table = 'platform';
    protected $primaryKey = 'id';
    protected $fillable = [
        'default' => [
            'name', 'code', 'base_url'
        ]
    ];
    protected $timestamps = true;

    // 默认属性值
    protected array $defaults = [
        'name' => '',
        'code' => '',
        'base_url' => ''
    ];

    public function __construct()
    {
        parent::__construct();
        // 设置默认值
        $this->attributes = array_merge($this->defaults, $this->attributes);
    }

    /**
     * 定义验证规则
     * @param bool $isUpdate 是否为更新操作
     * @param string|null $scenario 场景名称，为null时使用当前场景
     * @return array 验证规则
     */
    public function rules(bool $isUpdate = false, ?string $scenario = null): array
    {
        return [
            'default' => [
                'name' => 'required|max:50|unique',
                'code' => 'required|max:50|unique',
                'base_url' => 'required|max:255'
            ]
        ];
    }

    /**
     * 获取字段标签
     * @return array 字段标签映射
     */
    protected function getFieldLabels(): array
    {
        return [
            'name' => '平台名称',
            'code' => '平台代码',
            'base_url' => '基础URL'
        ];
    }

    /**
     * 静态工厂方法 - 创建新Platform实例
     */
    public static function make(array $data = []): self
    {
        $instance = new static();
        $instance->fill($data);
        return $instance;
    }

    /**
     * 静态方法 - 通过ID查找
     */
    public static function findOrFail(int $id): self
    {
        $instance = new static();
        $found = $instance->find($id);
        if (!$found) {
            throw new \Exception("Platform with ID {$id} not found");
        }
        return $found;
    }

    /**
     * 静态方法 - 通过code查找
     */
    public static function findByCode(string $code): ?self
    {
        $instance = new static();
        $result = $instance->findOne(['code' => $code]);
        return $result;
    }
}
