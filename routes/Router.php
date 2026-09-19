<?php
class Router {
    private $routes = [];

    public function get($path, $controller, $method){
        $this->routes['GET'][$path] = [$controller, $method];
    }

    public function post($path, $controller, $method){
        $this->routes['POST'][$path] = [$controller, $method];
    }

    public function dispatch($uri, $httpMethod){
        $uri = strtok($uri, '?');
        $uri = rtrim($uri, '/') ?: '/';

        $routes = $this->routes[$httpMethod] ?? [];

        // Exact match
        if (isset($routes[$uri])) {
            [$controller, $method] = $routes[$uri];
            (new $controller())->$method();
            return;
        }

        // Pattern match with parameters
        foreach ($routes as $pattern => $handler) {
            $regex = preg_replace('/\{[^}]+\}/', '([^/]+)', $pattern);
            $regex = '#^' . $regex . '$#';
            if (preg_match($regex, $uri, $matches)) {
                array_shift($matches);
                [$controller, $method] = $handler;
                (new $controller())->$method(...$matches);
                return;
            }
        }

        http_response_code(404);
        require BASE_PATH . '/app/views/errors/404.php';
    }
}
