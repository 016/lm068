### URI设计/使用规范
- 关于URL, 已经通过3级域名实现了前后端使用不同的域名, 在生成uri的时候请生成正确的path
  - www.a.com 已指向 php_app_root/php_app/public_frontend
  - admin.a.com 已指向 php_app_root/php_app/public_backend
- 如果需要对 URI 中的 ID 进行encode/decode，已经实现了 hashId class 可以直接使用
  - php_app_root/php_app/Core/HashId.php
- backend
  - list page 使用 index关键词
  - create page 使用 create关键词
    - 直接post到create 处理完以后跳转回index
  - update page 使用 update关键词
    - 直接post到update 处理完以后跳转回index
  - view page 使用 view关键词
  - 其他要求
    - 后台页面所有功能, 需要使用反馈的时候, 均使用定义的notification进行反馈

- frontend
  - list page 使用 index关键词
  - detail page 使用 view关键词