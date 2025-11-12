## MVC架构规则
- 使用 MVC架构
- 遵循 MVC 原则, 进行必要的继承, 以优化代码架构
- MVC 标准流程, 使用经典MVC流程, 实现Active Record模式，支持属性访问和错误管理
  - 以create tag 举例
    1. $tag = new Tag();
    2. 把 $tag 传递到 view 实现渲染。
    3. view 渲染时使用 $tag 的一些入属性，比如说字数限制标题等常见功能。
    4. 表单提交时 post 回 create action。对 POST 的数值进行提取并填充回 $tag。
    5. 使用 Tag 的 validate 对提取的 post 数值进行验证。
    6. 如果验证失败，使用 $tag->errors 返回给 view, view 向用户渲染错误, 允许再次编辑。
    7. 重复提交，直至验证通过。写入数据库。完成后续逻辑.
- Model
  - 建立 model 和基础 model
  - 在 view 和 controller 中按标准使用 model
  - 新建 model 时需要为 model 的属性和关系。引入 IDE 支持的变量注释。
- View
  - view 文件存放在 php_app_root/php_app/Views 文件夹下
    - 管理后端存放在 php_app_root/php_app/Views/backend
    - 用户前端存放在 php_app_root/php_app/Views/frontend
  - 布局内容存放在 对应 layouts 文件夹内, 在无约定的情况下, 优先使用layouts内的布局文件
    - layouts/main.php 为默认布局文件, 无特殊指定时优先使用该布局
    - 在使用布局的前提下, 只需要渲染 <main> 标签内的内容即可, 其他可复用的公共元素内容已存放在布局文件内, 不需要重复渲染
  - backend form page
    - create and edit form page 相同的表单部分使用 _form.php 文件来实现共享