<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, callable $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function patch(string $path, callable $handler): void
    {
        $this->addRoute('PATCH', $path, $handler);
    }

    public function delete(string $path, callable $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute(string $method, string $path, callable $handler): void
    {
        $this->routes[] = [
            'method'  => strtoupper($method),
            'path'    => $path,
            'handler' => $handler,
        ];
    }

    public function dispatch(Request $request, Response $response): void
    {
        $method = $request->getMethod();
        $path   = $request->getPath();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->matchPath($route['path'], $path);
            if ($params !== null) {
                $request->Params($params);
                call_user_func($route['handler'], $request, $response);
                return;
            }
        }

        $response->notFound('Route introuvable');
    }

    // Converts /api/posts/:id  →  regex with named groups
    // Returns extracted params array on match, null otherwise
    private function matchPath(string $routePath, string $requestPath): ?array
    {
        $pattern = preg_replace(
            '#:([a-zA-Z_][a-zA-Z0-9_]*)#',
            '(?P<$1>[^/]+)',
            $routePath
        );
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $requestPath, $matches)) {
            // Keep only named (string-keyed) captures
            return array_filter($matches, fn($k) => !is_int($k), ARRAY_FILTER_USE_KEY);
        }

        return null;
    }
}
