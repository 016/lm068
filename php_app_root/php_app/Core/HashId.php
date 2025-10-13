<?php

namespace App\Core;

/**
 * HashId类 - 用于将数字ID编码为短字符串，并可解码回数字ID
 *
 * 使用Base62编码（0-9, a-z, A-Z）结合简单混淆算法
 * 目的：隐藏URL中的纯数字ID，提高安全性和美观性
 */
class HashId
{
    // Base62字符集
    private const CHARSET = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    // 盐值 - 用于混淆，可以在配置文件中设置
    private string $salt;

    // 最小长度 - 生成的hash最小长度
    private int $minLength;

    /**
     * 构造函数
     *
     * @param string $salt 盐值，用于混淆ID
     * @param int $minLength 最小长度，默认为6
     */
    public function __construct(string $salt = 'lm068_video_site_2025', int $minLength = 6)
    {
        $this->salt = $salt;
        $this->minLength = max(4, $minLength);
    }

    /**
     * 编码ID为hash字符串
     *
     * @param int $id 要编码的数字ID
     * @return string 编码后的hash字符串
     */
    public function encode(int $id): string
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException('ID must be a positive integer');
        }

        // 第一步：添加混淆
        $obfuscated = $this->obfuscate($id);

        // 第二步：Base62编码
        $encoded = $this->base62Encode($obfuscated);

        // 第三步：填充到最小长度
        $encoded = $this->pad($encoded);

        return $encoded;
    }

    /**
     * 解码hash字符串为ID
     *
     * @param string $hash 要解码的hash字符串
     * @return int|null 解码后的ID，失败返回null
     */
    public function decode(string $hash): ?int
    {
        if (empty($hash)) {
            return null;
        }

        try {
            // 第一步：Base62解码
            $obfuscated = $this->base62Decode($hash);

            if ($obfuscated === null) {
                return null;
            }

            // 第二步：去除混淆
            $id = $this->deobfuscate($obfuscated);

            // 验证ID的合理性
            if ($id <= 0 || $id > PHP_INT_MAX) {
                return null;
            }

            return $id;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * 混淆ID
     * 使用简单的数学运算和盐值进行混淆
     *
     * @param int $id 原始ID
     * @return int 混淆后的数字
     */
    private function obfuscate(int $id): int
    {
        // 使用盐值生成一个数字因子
        $saltHash = crc32($this->salt);

        // 混淆算法：(ID * 质数 + 偏移量) XOR 盐值哈希
        $prime = 1000003; // 一个大质数
        $offset = 987654321;

        $obfuscated = ($id * $prime + $offset) ^ $saltHash;

        return abs($obfuscated);
    }

    /**
     * 去除混淆
     *
     * @param int $obfuscated 混淆后的数字
     * @return int 原始ID
     */
    private function deobfuscate(int $obfuscated): int
    {
        $saltHash = crc32($this->salt);
        $prime = 1000003;
        $offset = 987654321;

        // 反向运算
        $temp = $obfuscated ^ $saltHash;
        $id = ($temp - $offset) / $prime;

        return (int)round($id);
    }

    /**
     * Base62编码
     *
     * @param int $number 要编码的数字
     * @return string Base62字符串
     */
    private function base62Encode(int $number): string
    {
        if ($number === 0) {
            return self::CHARSET[0];
        }

        $base = strlen(self::CHARSET);
        $result = '';

        while ($number > 0) {
            $remainder = $number % $base;
            $result = self::CHARSET[$remainder] . $result;
            $number = (int)($number / $base);
        }

        return $result;
    }

    /**
     * Base62解码
     *
     * @param string $string Base62字符串
     * @return int|null 解码后的数字，失败返回null
     */
    private function base62Decode(string $string): ?int
    {
        $base = strlen(self::CHARSET);
        $result = 0;
        $length = strlen($string);

        for ($i = 0; $i < $length; $i++) {
            $char = $string[$i];
            $position = strpos(self::CHARSET, $char);

            if ($position === false) {
                return null; // 包含非法字符
            }

            $result = $result * $base + $position;
        }

        return $result;
    }

    /**
     * 填充字符串到最小长度
     *
     * @param string $string 原始字符串
     * @return string 填充后的字符串
     */
    private function pad(string $string): string
    {
        if (strlen($string) >= $this->minLength) {
            return $string;
        }

        // 使用盐值生成填充字符
        $saltHash = md5($this->salt . $string);
        $paddingChars = '';

        for ($i = 0; $i < strlen($saltHash); $i++) {
            $index = ord($saltHash[$i]) % strlen(self::CHARSET);
            $paddingChars .= self::CHARSET[$index];
        }

        // 添加填充，但保持可解码性
        // 在字符串前后添加特定模式的填充
        $paddingNeeded = $this->minLength - strlen($string);
        $padding = substr($paddingChars, 0, $paddingNeeded);

        return $string . $padding;
    }

    /**
     * 获取单例实例
     *
     * @return self
     */
    public static function getInstance(): self
    {
        static $instance = null;

        if ($instance === null) {
            // 从配置文件读取盐值和最小长度
            $config = Config::get('hashid', []);
            $salt = $config['salt'] ?? 'lm068_video_site_2025';
            $minLength = $config['min_length'] ?? 6;

            $instance = new self($salt, $minLength);
        }

        return $instance;
    }
}
