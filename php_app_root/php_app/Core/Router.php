<?php

namespace App\Core;

class Router
{
    private $routes = [];
    private $notFound = null;

    public function get(string $pattern, $handler): void
    {
        $this->routes['GET'][$pattern] = $handler;
    }

    public function post(string $pattern, $handler): void
    {
        $this->routes['POST'][$pattern] = $handler;
    }

    public function put(string $pattern, $handler): void
    {
        $this->routes['PUT'][$pattern] = $handler;
    }

    public function delete(string $pattern, $handler): void
    {
        $this->routes['DELETE'][$pattern] = $handler;
    }

    public function notFound($handler): void
    {
        $this->notFound = $handler;
    }

    public function resolve(Request $request)
    {
        $method = $request->getMethod();
        $uri = $request->getUri();

        if (!isset($this->routes[$method])) {
            return $this->executeNotFound();
        }

        foreach ($this->routes[$method] as $pattern => $handler) {
            $params = $this->matchRoute($pattern, $uri);
            if ($params !== false) {
                $request->setParams($params);
                return $this->executeHandler($handler, $request);
            }
        }

        return $this->executeNotFound();
    }

    private function matchRoute(string $pattern, string $uri): array|false
    {
        // Convert route pattern to regex
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $pattern);
        $pattern = str_replace('/', '\/', $pattern);
        $pattern = '/^' . $pattern . '$/';

        if (preg_match($pattern, $uri, $matches)) {
            array_shift($matches); // Remove full match
            return $matches;
        }

        return false;
    }

    private function executeHandler($handler, Request $request)
    {
        if (is_callable($handler)) {
            return call_user_func($handler, $request);
        }

        if (is_string($handler)) {
            list($controller, $method) = explode('@', $handler);

            // Add namespace prefix
            $controllerClass = 'App\\Controllers\\' . $controller;

            if (!class_exists($controllerClass)) {
                throw new \Exception("Controller {$controllerClass} not found");
            }

            $controllerInstance = new $controllerClass();

            if (!method_exists($controllerInstance, $method)) {
                throw new \Exception("Method {$method} not found in {$controllerClass}");
            }

            // 执行 before action 过滤器
            if (method_exists($controllerInstance, 'runBeforeActionFilters')) {
                $filterResult = $controllerInstance->runBeforeActionFilters($method);

                // 如果过滤器返回 false, 中断执行
                if ($filterResult === false) {
                    return false;
                }
            }

            return call_user_func([$controllerInstance, $method], $request);
        }

        throw new \Exception("Invalid route handler");
    }

    private function executeNotFound()
    {
        if ($this->notFound && is_callable($this->notFound)) {
            return call_user_func($this->notFound);
        }

        http_response_code(404);
        echo "404 - Page Not Found";
        return false;
    }
}