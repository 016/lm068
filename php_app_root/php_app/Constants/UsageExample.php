<?php

/**
 * 实际代码改造示例
 * 
 * 展示如何将现有代码中的硬编码数字替换为枚举常量
 */

// 引入常量类
use App\Constants\Status;
use App\Constants\ContentStatus;
use App\Constants\HttpStatus;

echo "=== 实际代码改造示例 ===\n\n";

echo "1. Controller 中的改造示例:\n";
echo "=====================================\n";

echo "// 旧代码 (TagController.php:65):\n";
echo "// 'status_id' => [21, 29, 31, 39, 91, 99]\n\n";

echo "// 新代码:\n";
echo "// 'status_id' => ContentStatus::getVisibleStatuses()\n";
echo "// 实际值: [" . implode(', ', ContentStatus::getVisibleStatuses()) . "]\n\n";

echo "// 旧代码 (TagController.php:108):\n";
echo "// 'status_id' => (int)(\$request->post('status_id') ?? 0)\n\n";

echo "// 新代码:\n";
echo "// 'status_id' => (int)(\$request->post('status_id') ?? Status::INACTIVE->value)\n";
echo "// 默认值: " . Status::INACTIVE->value . " (" . Status::INACTIVE->label() . ")\n\n";

echo "2. Model 中的改造示例:\n";
echo "=====================================\n";

echo "// 旧代码 (User.php:29):\n";
echo "// return \$this->findAll(['status_id' => 1], \$limit, \$offset, 'created_at DESC');\n\n";

echo "// 新代码:\n";
echo "// return \$this->findAll(['status_id' => Status::ACTIVE->value], \$limit, \$offset, 'created_at DESC');\n";
echo "// 查询条件: status_id = " . Status::ACTIVE->value . " (" . Status::ACTIVE->label() . ")\n\n";

echo "// 旧代码 (AdminUser.php:17):\n";
echo "// WHERE username = :username AND status_id = 1\n\n";

echo "// 新代码:\n";
echo "// WHERE username = :username AND status_id = " . Status::ACTIVE->value . "\n";
echo "// 或者在准备语句中使用: ['status_id' => Status::ACTIVE->value]\n\n";

echo "3. View 中的改造示例:\n";
echo "=====================================\n";

echo "// 旧代码 (tags/index.php:259-260):\n";
echo "// <option value=\"1\">显示</option>\n";
echo "// <option value=\"0\">隐藏</option>\n\n";

echo "// 新代码:\n";
$statusOptions = Status::getAllValues();
foreach ($statusOptions as $value => $label) {
    echo "// <option value=\"{$value}\">{$label}</option>\n";
}
echo "\n";

echo "4. HTTP 状态码改造示例:\n";
echo "=====================================\n";

echo "// 旧代码 (Controller.php:38):\n";
echo "// protected function json(array \$data, int \$statusCode = 200): void\n\n";

echo "// 新代码:\n";
echo "// protected function json(array \$data, int \$statusCode = HttpStatus::OK->value): void\n";
echo "// 默认值: " . HttpStatus::OK->value . " (" . HttpStatus::OK->message() . ")\n\n";

echo "// 旧代码 (Controller.php:45):\n";
echo "// protected function redirect(string \$url, int \$statusCode = 302): void\n\n";

echo "// 新代码:\n";
echo "// protected function redirect(string \$url, int \$statusCode = HttpStatus::FOUND->value): void\n";
echo "// 默认值: " . HttpStatus::FOUND->value . " (" . HttpStatus::FOUND->message() . ")\n\n";

echo "5. 动态状态检查示例:\n";
echo "=====================================\n";

echo "// 检查内容是否可发布:\n";
$contentStatus = ContentStatus::PUBLISHED;
echo "// if (\$contentStatus->isPublished()) { ... }\n";
echo "// 状态 " . $contentStatus->value . " (" . $contentStatus->label() . ") 是否已发布: " . ($contentStatus->isPublished() ? '是' : '否') . "\n\n";

echo "// 检查HTTP状态是否为错误:\n";
$httpStatus = HttpStatus::NOT_FOUND;
echo "// if (\$httpStatus->isError()) { ... }\n";
echo "// 状态 " . $httpStatus->value . " (" . $httpStatus->message() . ") 是否为错误: " . ($httpStatus->isError() ? '是' : '否') . "\n\n";

echo "6. 实际使用场景:\n";
echo "=====================================\n";

echo "// 在数据验证中使用:\n";
echo "if (!in_array(\$statusId, ContentStatus::getVisibleStatuses())) {\n";
echo "    throw new InvalidArgumentException('无效的状态ID');\n";
echo "}\n\n";

echo "// 在数据库查询中使用:\n";
echo "\$publishedContent = \$this->findAll([\n";
echo "    'status_id' => ContentStatus::getPublishedStatuses()\n";
echo "]);\n\n";

echo "// 在API响应中使用:\n";
echo "\$this->json([\n";
echo "    'success' => true,\n";
echo "    'data' => \$results\n";
echo "], HttpStatus::OK->value);\n\n";

echo "7. 配置数组示例:\n";
echo "=====================================\n";

echo "// 创建状态映射配置:\n";
echo "\$statusConfig = [\n";
foreach (Status::cases() as $status) {
    echo "    '{$status->name}' => [\n";
    echo "        'value' => {$status->value},\n";
    echo "        'label' => '{$status->label()}',\n";
    echo "        'english' => '{$status->englishLabel()}'\n";
    echo "    ],\n";
}
echo "];\n\n";

echo "完成！现在你可以在整个项目中使用这些常量来替代硬编码的数字。\n";
echo "这样做的好处:\n";
echo "1. 代码更加可读和可维护\n";
echo "2. 类型安全，IDE可以提供自动完成\n";
echo "3. 集中管理常量，便于修改\n";
echo "4. 提供丰富的辅助方法\n";
echo "5. 支持国际化标签\n";