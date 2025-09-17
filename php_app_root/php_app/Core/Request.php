<?php

namespace App\Core;

class Request
{
    private $uri;
    private $method;
    private $params;
    private $query;
    private $body;
    private $headers;

    public function __construct()
    {
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->params = [];
        $this->query = $_GET;
        $this->body = $_POST;
        $this->headers = getallheaders() ?: [];
    }

    public function getUri(): string
    {
        $scriptPath = $_SERVER['SCRIPT_NAME'];

        // 从请求路径中移除脚本路径部分
        if (str_starts_with($this->uri, $scriptPath)) {
            $this->uri = substr($this->uri, strlen($scriptPath));
        }


        return $this->uri;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getParam(string $key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }

    public function getQuery(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->query;
        }
        return $this->query[$key] ?? $default;
    }

    public function getBody(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->body;
        }
        return $this->body[$key] ?? $default;
    }

    public function getInput(string $key, $default = null)
    {
        return $this->getBody($key) ?? $this->getQuery($key) ?? $default;
    }

    public function getHeader(string $key): ?string
    {
        return $this->headers[$key] ?? null;
    }

    public function isGet(): bool
    {
        return $this->method === 'GET';
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    public function isPut(): bool
    {
        return $this->method === 'PUT';
    }

    public function isDelete(): bool
    {
        return $this->method === 'DELETE';
    }

    public function isAjax(): bool
    {
        return strtolower($this->getHeader('X-Requested-With') ?? '') === 'xmlhttprequest';
    }

    public function get(?string $key = null, $default = null)
    {
        return $this->getQuery($key, $default);
    }

    public function post(?string $key = null, $default = null)
    {
        return $this->getBody($key, $default);
    }

    public function getIp(): string
    {
        // 优先检查代理头
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // 可能包含多个IP，取第一个
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
            return $_SERVER['HTTP_X_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
            return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED'])) {
            return $_SERVER['HTTP_FORWARDED'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        }
    }
}