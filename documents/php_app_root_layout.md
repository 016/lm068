#### php_app_root 目录结构
/php_app_root
├── php_app/            # 主PHP应用 (code), 内部结构见 documents/php_app_layout.md
└── public_resources/   # 独立的资源目录, 安全隔离
└── uploads/            # re.domain.com 指向这里
├── avatars/
├── thumbnails/
└── videos_preview/