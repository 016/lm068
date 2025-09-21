<?php

namespace App\Constants;

/**
 * 全局常量使用示例
 * 
 * 这个文件展示了如何使用定义的枚举常量来替换硬编码的数字
 */
class ConstantsDemo
{
    public function statusExamples(): void
    {
        echo "=== Status 枚举使用示例 ===\n";
        
        // 替换原来的硬编码: status_id = 1
        $activeStatus = Status::ACTIVE;
        echo "活跃状态值: " . $activeStatus->value . "\n";
        echo "活跃状态标签: " . $activeStatus->label() . "\n";
        echo "是否活跃: " . ($activeStatus->isActive() ? '是' : '否') . "\n";
        
        // 替换原来的硬编码: status_id = 0
        $inactiveStatus = Status::INACTIVE;
        echo "非活跃状态值: " . $inactiveStatus->value . "\n";
        echo "非活跃状态标签: " . $inactiveStatus->label() . "\n";
        
        // 从字符串创建状态
        $statusFromString = Status::fromString('1');
        echo "从字符串'1'创建的状态: " . $statusFromString?->label() . "\n";
        
        // 获取所有状态选项（用于下拉菜单）
        $allStatuses = Status::getAllValues();
        echo "所有状态选项: " . json_encode($allStatuses, JSON_UNESCAPED_UNICODE) . "\n\n";
    }

    public function contentStatusExamples(): void
    {
        echo "=== ContentStatus 枚举使用示例 ===\n";
        
        // 替换原来的硬编码: 'status_id' => [21, 29, 31, 39, 91, 99]
        $visibleStatuses = ContentStatus::getVisibleStatuses();
        echo "可见内容状态: " . implode(', ', $visibleStatuses) . "\n";
        
        // 检查状态类型
        $publishedStatus = ContentStatus::PUBLISHED;
        echo "已发布状态值: " . $publishedStatus->value . "\n";
        echo "已发布状态标签: " . $publishedStatus->label() . "\n";
        echo "是否已发布: " . ($publishedStatus->isPublished() ? '是' : '否') . "\n";
        echo "CSS类名: " . $publishedStatus->statusClass() . "\n";
        
        // 获取所有已发布状态
        $publishedStatuses = ContentStatus::getPublishedStatuses();
        echo "已发布状态列表: " . implode(', ', $publishedStatuses) . "\n\n";
    }

    public function httpStatusExamples(): void
    {
        echo "=== HttpStatus 枚举使用示例 ===\n";
        
        // 替换原来的硬编码: 200, 302
        $okStatus = HttpStatus::OK;
        echo "成功状态码: " . $okStatus->value . "\n";
        echo "状态消息: " . $okStatus->message() . "\n";
        echo "是否成功: " . ($okStatus->isSuccess() ? '是' : '否') . "\n";
        
        $redirectStatus = HttpStatus::FOUND;
        echo "重定向状态码: " . $redirectStatus->value . "\n";
        echo "是否重定向: " . ($redirectStatus->isRedirection() ? '是' : '否') . "\n\n";
    }

    public function controllerUsageExample(): void
    {
        echo "=== 在Controller中的使用示例 ===\n";
        
        echo "// 旧的写法:\n";
        echo "// \$this->json(['message' => 'Success'], 200);\n";
        echo "// \$this->redirect('/dashboard', 302);\n\n";
        
        echo "// 新的写法:\n";
        echo "// \$this->json(['message' => 'Success'], HttpStatus::OK->value);\n";
        echo "// \$this->redirect('/dashboard', HttpStatus::FOUND->value);\n\n";
    }

    public function modelUsageExample(): void
    {
        echo "=== 在Model中的使用示例 ===\n";
        
        echo "// 旧的写法:\n";
        echo "// \$this->findAll(['status_id' => 1], \$limit, \$offset);\n";
        echo "// WHERE status_id = 1\n\n";
        
        echo "// 新的写法:\n";
        echo "// \$this->findAll(['status_id' => Status::ACTIVE->value], \$limit, \$offset);\n";
        echo "// WHERE status_id = " . Status::ACTIVE->value . "\n\n";
        
        echo "// 查询可见内容:\n";
        echo "// WHERE status_id IN (" . implode(', ', ContentStatus::getVisibleStatuses()) . ")\n\n";
    }

    public function viewUsageExample(): void
    {
        echo "=== 在View中的使用示例 ===\n";
        
        echo "// 旧的写法:\n";
        echo "// <option value=\"1\">显示</option>\n";
        echo "// <option value=\"0\">隐藏</option>\n\n";
        
        echo "// 新的写法:\n";
        $statuses = Status::getAllValues();
        foreach ($statuses as $value => $label) {
            echo "// <option value=\"{$value}\">{$label}</option>\n";
        }
        echo "\n";
    }

    public function runAllExamples(): void
    {
        $this->statusExamples();
        $this->contentStatusExamples();
        $this->httpStatusExamples();
        $this->controllerUsageExample();
        $this->modelUsageExample();
        $this->viewUsageExample();
    }
}

// 如果直接运行此文件，执行示例
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $demo = new ConstantsDemo();
    $demo->runAllExamples();
}