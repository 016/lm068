<?php

namespace App\Models;

use App\Core\Model;
use App\Constants\TagStatus;
use App\Interfaces\HasStatuses;

class Tag extends Model implements HasStatuses
{

    protected static string $table = 'tag';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name_en', 'name_cn', 'short_desc_en', 'short_desc_cn', 
        'desc_en', 'desc_cn', 'color_class', 'icon_class', 
        'content_cnt', 'status_id'
    ];
    protected $timestamps = true;

    // 默认属性值
    protected array $defaults = [
        'name_cn' => '',
        'name_en' => '',
        'short_desc_cn' => '',
        'short_desc_en' => '',
        'desc_cn' => '',
        'desc_en' => '',
        'color_class' => 'btn-outline-primary',
        'icon_class' => 'bi-tag',
        'content_cnt' => 0,
        'status_id' => 1
    ];

    public function __construct()
    {
        parent::__construct();
        // 设置默认值
        $this->attributes = array_merge($this->defaults, $this->attributes);
    }

    /**
     * 实现接口方法，返回对应的状态枚举类
     */
    public static function getStatusEnum(): string
    {
        return TagStatus::class;
    }

    /**
     * 定义验证规则
     * @param bool $isUpdate 是否为更新操作
     * @return array 验证规则
     */
    public function rules(bool $isUpdate = false): array
    {
        return [
            'name_cn' => 'required|max:50|unique',
            'name_en' => 'required|max:50|unique',
            'short_desc_cn' => 'max:100',
            'short_desc_en' => 'max:100',
            'desc_cn' => 'max:500',
            'desc_en' => 'max:500',
            'color_class' => 'max:50',
            'icon_class' => 'max:50',
            'status_id' => 'numeric'
        ];
    }

    /**
     * 获取字段标签
     * @return array 字段标签映射
     */
    protected function getFieldLabels(): array
    {
        return [
            'name_cn' => '中文名称',
            'name_en' => '英文名称',
            'short_desc_cn' => '中文简介',
            'short_desc_en' => '英文简介',
            'desc_cn' => '中文描述',
            'desc_en' => '英文描述',
            'color_class' => '颜色样式',
            'icon_class' => '图标样式',
            'status_id' => '状态'
        ];
    }

    /**
     * 获取显示名称（优先中文）
     */
    public function getDisplayName(): string
    {
        return $this->name_cn ?: $this->name_en;
    }

    /**
     * 获取显示描述（优先中文）
     */
    public function getDisplayDescription(): string
    {
        return $this->desc_cn ?: $this->desc_en;
    }

    /**
     * 根据指定语言获取名称
     *
     * @param string|null $lang 语言代码 (zh/en), 为null时使用当前语言
     * @return string 对应语言的名称
     */
    public function getName(?string $lang = null): string
    {
        $lang = $lang ?? \App\Core\I18n::getCurrentLang();
        $name = $lang === 'zh' ? $this->name_cn : $this->name_en;

        // 如果指定语言的名称为空,降级到另一个语言
        if (empty($name)) {
            $name = $lang === 'zh' ? $this->name_en : $this->name_cn;
        }

        return $name ?? '';
    }

    /**
     * 根据指定语言获取简介
     *
     * @param string|null $lang 语言代码 (zh/en), 为null时使用当前语言
     * @return string 对应语言的简介
     */
    public function getShortDescription(?string $lang = null): string
    {
        $lang = $lang ?? \App\Core\I18n::getCurrentLang();
        $shortDesc = $lang === 'zh' ? $this->short_desc_cn : $this->short_desc_en;

        // 如果指定语言的简介为空,降级到另一个语言
        if (empty($shortDesc)) {
            $shortDesc = $lang === 'zh' ? $this->short_desc_en : $this->short_desc_cn;
        }

        return $shortDesc ?? '';
    }

    /**
     * 根据指定语言获取描述
     *
     * @param string|null $lang 语言代码 (zh/en), 为null时使用当前语言
     * @return string 对应语言的描述
     */
    public function getDescription(?string $lang = null): string
    {
        $lang = $lang ?? \App\Core\I18n::getCurrentLang();
        $desc = $lang === 'zh' ? $this->desc_cn : $this->desc_en;

        // 如果指定语言的描述为空,降级到另一个语言
        if (empty($desc)) {
            $desc = $lang === 'zh' ? $this->desc_en : $this->desc_cn;
        }

        return $desc ?? '';
    }

    /**
     * 获取状态标签
     */
    public function getStatusLabel(): string
    {
        if (isset($this->status_id)) {
            $status = TagStatus::tryFrom($this->status_id);
            return $status ? $status->label() : '未知状态';
        }
        return '未设置';
    }

    /**
     * 检查是否启用
     */
    public function isEnabled(): bool
    {
        return $this->status_id === TagStatus::ENABLED->value;
    }

    /**
     * 静态工厂方法 - 创建新Tag实例
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
            throw new \Exception("Tag with ID {$id} not found");
        }
        return $found;
    }

    public function getStats(): array
    {

        $sql = "SELECT 
                    COUNT(*) as total_tags,
                    SUM(CASE WHEN status_id = :active_status THEN 1 ELSE 0 END) as active_tags,
                    SUM(CASE WHEN status_id = :inactive_status THEN 1 ELSE 0 END) as inactive_tags,
                    SUM(content_cnt) as total_content_associations
                FROM ".static::getTableName();

        
        $result = $this->db->fetch($sql, [
            'active_status' => TagStatus::ENABLED->value,
            'inactive_status' => TagStatus::DISABLED->value
        ]);
        
        return [
            'total_tags' => (int)$result['total_tags'],
            'active_tags' => (int)$result['active_tags'],
            'inactive_tags' => (int)$result['inactive_tags'],
            'total_content_associations' => (int)$result['total_content_associations']
        ];
    }

    public function getRelatedContent(int $tagId): array
    {
        $sql = "SELECT c.id, c.title_cn, c.title_en, c.content_type_id, c.status_id, c.view_cnt, c.thumbnail
                FROM content c
                INNER JOIN content_tag ct ON c.id = ct.content_id  
                WHERE ct.tag_id = :tag_id
                ORDER BY c.created_at DESC";
        
        return $this->db->fetchAll($sql, ['tag_id' => $tagId]);
    }

    public function updateContentCount(int $tagId): bool
    {
        $table = static::getTableName();

        $sql = "UPDATE {$table} 
                SET content_cnt = (
                    SELECT COUNT(*) 
                    FROM content_tag 
                    WHERE tag_id = :tag_id1
                )
                WHERE id = :tag_id2";

        $this->db->query($sql, ['tag_id1' => $tagId, 'tag_id2' => $tagId]);
        return true;
    }

    public function attachContent(int $tagId, int $contentId): bool
    {
        $sql = "INSERT IGNORE INTO content_tag (tag_id, content_id) VALUES (:tag_id, :content_id)";
        $this->db->query($sql, ['tag_id' => $tagId, 'content_id' => $contentId]);
        
        $this->updateContentCount($tagId);
        return true;
    }

    public function detachContent(int $tagId, int $contentId): bool
    {
        $sql = "DELETE FROM content_tag WHERE tag_id = :tag_id AND content_id = :content_id";
        $this->db->query($sql, ['tag_id' => $tagId, 'content_id' => $contentId]);
        
        $this->updateContentCount($tagId);
        return true;
    }

    public function syncContentAssociations(int $tagId, array $contentIds): bool
    {
        $this->db->beginTransaction();
        
        try {
            $this->db->query("DELETE FROM content_tag WHERE tag_id = :tag_id", ['tag_id' => $tagId]);
            
            foreach ($contentIds as $contentId) {
                $this->db->query(
                    "INSERT INTO content_tag (tag_id, content_id) VALUES (:tag_id, :content_id)",
                    ['tag_id' => $tagId, 'content_id' => $contentId]
                );
            }

            $this->updateContentCount($tagId);
            $this->db->commit();
            
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * 重写父类方法，为Tag模型准备CSV导入数据
     *
     * @param array $csvRowData CSV行数据
     * @return array 处理后的数据
     */
    public function prepareBulkImportData(array $csvRowData): array
    {
        return [
            'name_cn' => $csvRowData['name_cn'] ?? '',
            'name_en' => $csvRowData['name_en'] ?? '',
            'short_desc_cn' => $csvRowData['short_desc_cn'] ?? '',
            'short_desc_en' => $csvRowData['short_desc_en'] ?? '',
            'desc_cn' => $csvRowData['desc_cn'] ?? '',
            'desc_en' => $csvRowData['desc_en'] ?? '',
            'color_class' => $csvRowData['color_class'] ?? 'btn-outline-primary',
            'icon_class' => $csvRowData['icon_class'] ?? 'bi-tag',
            'status_id' => isset($csvRowData['status_id']) ? (int)$csvRowData['status_id'] : TagStatus::ENABLED->value,
            'content_cnt' => 0
        ];
    }

    /**
     * 获取所有启用的标签（用于前端筛选）
     *
     * @return array
     */
    public static function getEnabledTags(): array
    {
        $db = \App\Core\Database::getInstance();
        $sql = "SELECT * FROM " . static::getTableName() . " WHERE status_id = :status_id ORDER BY name_cn";
        return $db->fetchAll($sql, ['status_id' => TagStatus::ENABLED->value]);
    }

}