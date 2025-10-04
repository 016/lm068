<?php

namespace App\Core;

use App\Core\Model;
use App\Core\Config;

/**
 * 文件上传模型基类
 * 提供文件上传功能，支持图片和通用文件上传
 *
 * 使用方法：
 * 1. 子类继承此类
 * 2. 定义 $uploadableAttributes 属性，指定哪些字段支持上传
 * 3. 调用 load() 方法时自动处理文件上传
 */
abstract class UploadableModel extends Model
{
    /**
     * 可上传的属性配置
     * 格式: [
     *     'thumbnail' => [
     *         'type' => 'image',           // 文件类型: image, video, file
     *         'path_key' => 'thumbnails_path', // 配置文件中的路径键
     *         'required' => false,          // 是否必需
     *         'replace_old' => true,        // 是否替换旧文件（删除旧文件后再上传新文件）
     *     ]
     * ]
     */
    protected array $uploadableAttributes = [];

    /**
     * 上传文件的临时信息
     */
    protected array $uploadedFiles = [];

    /**
     * 处理文件上传
     *
     * @param array $files $_FILES 数组
     * @return bool 是否有文件上传成功
     */
    public function handleFileUploads(array $files): bool
    {
        $hasUploads = false;

        foreach ($this->uploadableAttributes as $attribute => $config) {
            if (isset($files[$attribute]) && $files[$attribute]['error'] === UPLOAD_ERR_OK) {
                // 先上传新文件
                $uploadedFile = $this->processFileUpload($files[$attribute], $config);

                if ($uploadedFile) {
                    $this->uploadedFiles[$attribute] = $uploadedFile;
                    $this->attributes[$attribute] = $uploadedFile['db_value'];

                    // 上传成功后，如果配置了替换旧文件，则删除旧文件
                    if (($config['replace_old'] ?? false) === true) {
                        $this->deleteOldFiles($attribute, $config);
                    }

                    $hasUploads = true;
                }
            }
        }

        return $hasUploads;
    }

    /**
     * 处理单个文件上传
     *
     * @param array $file $_FILES 中的单个文件信息
     * @param array $config 上传配置
     * @return array|null 上传结果信息
     */
    protected function processFileUpload(array $file, array $config): ?array
    {
        // 验证文件
        if (!$this->validateFile($file, $config)) {
            return null;
        }

        // 获取上传路径
        $uploadPath = $this->getUploadPath($config['path_key'] ?? 'base_path');

        // 确保目录存在
        if (!$this->ensureDirectoryExists($uploadPath)) {
            $this->errors['upload'] = "无法创建上传目录";
            return null;
        }

        // 生成新文件名（保持原扩展名）
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $newFileName = $this->generateFileName($extension);
        $targetPath = $uploadPath . $newFileName;

        // 移动上传文件
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            $this->errors['upload'] = "文件上传失败";
            return null;
        }

        // 返回上传信息
        return [
            'original_name' => $file['name'],
            'file_name' => $newFileName,
            'file_path' => $targetPath,
            'db_value' => $newFileName,  // 存储到数据库的值（只存文件名）
            'size' => $file['size'],
            'type' => $file['type']
        ];
    }

    /**
     * 验证上传文件
     *
     * @param array $file 文件信息
     * @param array $config 配置
     * @return bool 是否验证通过
     */
    protected function validateFile(array $file, array $config): bool
    {
        // 检查文件大小
        $maxSize = Config::get('upload.max_file_size', 10 * 1024 * 1024);
        if ($file['size'] > $maxSize) {
            $this->errors['upload'] = "文件大小超过限制";
            return false;
        }

        // 检查文件类型
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedTypes = $this->getAllowedTypes($config['type'] ?? 'image');

        if (!in_array($extension, $allowedTypes)) {
            $this->errors['upload'] = "不支持的文件类型: {$extension}";
            return false;
        }

        return true;
    }

    /**
     * 获取允许的文件类型
     *
     * @param string $type 文件类型: image, video, file
     * @return array 允许的扩展名数组
     */
    protected function getAllowedTypes(string $type): array
    {
        $configKey = match($type) {
            'image' => 'upload.allowed_image_types',
            'video' => 'upload.allowed_video_types',
            'file' => 'upload.allowed_file_types',
            default => 'upload.allowed_image_types'
        };

        return Config::get($configKey, ['jpg', 'jpeg', 'png']);
    }

    /**
     * 获取上传路径
     *
     * @param string $pathKey 配置键
     * @return string 完整上传路径
     */
    protected function getUploadPath(string $pathKey): string
    {
        $relativePath = Config::get("upload.{$pathKey}", '../public_resources/uploads/');
        $realPath = realpath($relativePath);
        if ($realPath && is_dir($realPath)) {
            $realPath .= DIRECTORY_SEPARATOR;
        }

        return $realPath;
    }

    /**
     * 确保目录存在
     *
     * @param string $path 目录路径
     * @return bool 是否成功
     */
    protected function ensureDirectoryExists(string $path): bool
    {
        if (!file_exists($path)) {
            return mkdir($path, 0755, true);
        }
        return is_dir($path) && is_writable($path);
    }

    /**
     * 生成唯一文件名
     *
     * @param string $extension 文件扩展名
     * @return string 新文件名
     */
    protected function generateFileName(string $extension): string
    {
        return uniqid('upload_', true) . '_' . time() . '.' . $extension;
    }

    /**
     * 获取文件URL
     *
     * @param string $attribute 属性名
     * @return string|null 文件完整URL
     */
    public function getFileUrl(string $attribute): ?string
    {
        $fileName = $this->attributes[$attribute] ?? null;

        if (!$fileName) {
            return null;
        }

        $baseUrl = Config::get('upload.base_url', '');

        // 如果已经是完整URL，直接返回
        if (str_starts_with($fileName, 'http://') || str_starts_with($fileName, 'https://')) {
            return $fileName;
        }

        // 获取对应的路径配置来构建URL
        $config = $this->uploadableAttributes[$attribute] ?? [];
        $pathKey = $config['path_key'] ?? 'base_path';

        // 根据路径键构建URL路径
        $urlPath = match($pathKey) {
            'thumbnails_path' => 'thumbnails/',
            'videos_preview_path' => 'videos_preview/',
            'avatars_path' => 'avatars/',
            'files_path' => 'files/',
            default => ''
        };

        return rtrim($baseUrl, '/') . '/' . $urlPath . $fileName;
    }

    /**
     * 删除旧文件（支持跨后缀删除）
     * 用于在上传新文件后清理旧文件
     * 注意：该方法在新文件上传成功后调用，此时 $this->attributes[$attribute] 已更新为新文件名
     *       需要从数据库读取旧文件名进行删除
     *
     * @param string $attribute 属性名
     * @param array $config 上传配置
     * @return bool 是否删除成功
     */
    protected function deleteOldFiles(string $attribute, array $config): bool
    {
        // 获取新上传的文件名（当前 attributes 中的值）
        $newFileName = $this->attributes[$attribute] ?? null;

        if (!$newFileName) {
            return false;
        }

        // 从数据库读取旧文件名
        $oldFileName = $this->getOldAttributeValue($attribute);

        // 如果没有旧文件，或新旧文件名相同，不需要删除
        if (!$oldFileName || $oldFileName === $newFileName) {
            return false;
        }

        $uploadPath = $this->getUploadPath($config['path_key'] ?? 'base_path');

        // 提取旧文件名主体（去除后缀）
        $fileBaseName = pathinfo($oldFileName, PATHINFO_FILENAME);

        // 使用 glob 查找所有匹配的旧文件（任意后缀）
        $pattern = $uploadPath . $fileBaseName . '.*';
        $matchedFiles = glob($pattern);

        $deleted = false;
        if ($matchedFiles) {
            foreach ($matchedFiles as $filePath) {
                $currentFileName = basename($filePath);

                // 跳过当前新上传的文件（避免误删）
                if ($currentFileName === $newFileName) {
                    continue;
                }

                if (file_exists($filePath) && is_file($filePath)) {
                    unlink($filePath);
                    $deleted = true;
                }
            }
        }

        return $deleted;
    }

    /**
     * 获取属性的旧值（从数据库读取）
     *
     * @param string $attribute 属性名
     * @return string|null 旧值
     */
    protected function getOldAttributeValue(string $attribute): ?string
    {
        // 如果是新记录（没有ID），则没有旧值
        if (!isset($this->attributes[$this->primaryKey]) || empty($this->attributes[$this->primaryKey])) {
            return null;
        }

        $id = $this->attributes[$this->primaryKey];
        $table = static::getTableName();
        $sql = "SELECT {$attribute} FROM {$table} WHERE {$this->primaryKey} = :id LIMIT 1";

        $result = $this->db->fetch($sql, ['id' => $id]);

        return $result[$attribute] ?? null;
    }

    /**
     * 删除文件
     *
     * @param string $attribute 属性名
     * @return bool 是否删除成功
     */
    public function deleteFile(string $attribute): bool
    {
        $fileName = $this->attributes[$attribute] ?? null;

        if (!$fileName) {
            return false;
        }

        $config = $this->uploadableAttributes[$attribute] ?? [];
        $uploadPath = $this->getUploadPath($config['path_key'] ?? 'base_path');
        $filePath = $uploadPath . $fileName;

        if (file_exists($filePath)) {
            return unlink($filePath);
        }

        return false;
    }

    /**
     * 重写 save 方法，在保存前处理文件URL
     */
    public function save(): bool
    {
        // 处理文件URL转换为数据库存储格式
        foreach ($this->uploadableAttributes as $attribute => $config) {
            if (isset($this->attributes[$attribute])) {
                // 如果是完整URL，提取文件名
                $value = $this->attributes[$attribute];
                if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
                    $this->attributes[$attribute] = basename($value);
                }
            }
        }

        return parent::save();
    }
}
