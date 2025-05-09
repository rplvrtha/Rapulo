<?php
namespace Rapulo\Core;

class Router {
    private $routes = [];
    private $middleware = [];
    private $basePath;

    public function __construct() {
        $this->basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    }

    public function get($path, $handler) {
        $this->addRoute('GET', $path, $handler);
    }

    public function post($path, $handler) {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute($method, $path, $handler) {
        $this->routes[] = [
            'method' => $method,
            'path' => $this->compilePath($path),
            'handler' => $handler,
            'middleware' => []
        ];
    }

    public function middleware($name, $middleware) {
        $this->middleware[$name] = $middleware;
    }

    public function group($middleware, $callback) {
        $prevMiddleware = $this->middleware;
        $currentMiddleware = [];
        foreach ((array)$middleware as $m) {
            $currentMiddleware[] = $m;
            $this->middleware[$m] = $this->middleware[$m] ?? [];
        }
        foreach ($this->routes as &$route) {
            $route['middleware'] = array_merge($route['middleware'], $currentMiddleware);
        }
        call_user_func($callback, $this);
        $this->middleware = $prevMiddleware;
    }

    private function compilePath($path) {
        $path = trim($path, '/');
        return preg_replace('#\{([a-zA-Z0-9_]+)\}#', '(?<>[^/]+)', $path);
    }

    public function dispatch() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = trim(str_replace($this->basePath, '', $uri), '/');
        $method = $_SERVER['REQUEST_METHOD'];

        if (isset($_GET['debug_routes']) && $_GET['debug_routes'] == 1) {
            $this->debugRoutes();
            exit;
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match('#^' . $route['path'] . '$#', $uri, $matches)) {
                $this->runMiddleware($route['middleware']);
                $this->callHandler($route['handler'], $matches);
                return;
            }
        }

        $this->logError("404 Not Found: $method $uri");
        http_response_code(404);
        echo '404 Not Found';
    }

    private function runMiddleware($middleware) {
        foreach ($middleware as $m) {
            if (isset($this->middleware[$m])) {
                $instance = new $this->middleware[$m]();
                $instance->handle();
            }
        }
    }

    private function callHandler($handler, $params) {
        try {
            if (is_callable($handler)) {
                call_user_func($handler, $params);
            } elseif (is_array($handler)) {
                [$controller, $method] = $handler;
                $instance = new $controller();
                $instance->$method($params);
            }
        } catch (\Exception $e) {
            $this->logError("Handler error: " . $e->getMessage());
            http_response_code(500);
            echo '500 Internal Server Error';
        }
    }

    private function debugRoutes() {
        echo "<h1>Registered Routes</h1><ul>";
        foreach ($this->routes as $route) {
            echo "<li>{$route['method']} /{$route['path']} (Middleware: " . implode(', ', $route['middleware']) . ")</li>";
        }
        echo "</ul>";
    }

    private function logError($message) {
        $logFile = __DIR__ . '/../../storage/logs/app.log';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] $message
", FILE_APPEND);
    }
}