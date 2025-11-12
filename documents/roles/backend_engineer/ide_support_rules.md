### IDE引入指南
- 请依据数据库 DDL 的字段定义，在生成 Model 和 View 层代码时，自动为相关的类属性,关系与变量添加 PHPDoc 类型注释。
- View中使用的变量(包括 Controller 和 Model), 需要引入变量对应的源，方便实现 IDE 提示
- Model中的关系需要指定具体目标关系的Model名称，具体在DDL里可以查找到
- PHPDoc 定义格式见 "IDE引入指南Demo"
```IDE引入指南Demo
// View demo
/**
 * @var $this \App\Controllers\Frontend\ContentController //$this->funcName() will auto work in IDE
 * @var $contents \App\Models\Content[]
 * @var $content \App\Models\Content //$content->id will auto work in IDE
 */
 
// Model demo
/**
 * @property int $id 内容ID
 * @property int $content_type_id 内容类型
 * @property-read ContentType $contentType 所属内容类型
 * @property-read Comment[] $comments 评论
 */
```