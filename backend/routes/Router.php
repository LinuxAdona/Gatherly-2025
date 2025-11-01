<?php

/**
 * Router
 * Handles API routing and request dispatching
 */

class Router
{

    private $routes = [];
    private $notFoundCallback;

    /**
     * Add GET route
     */
    public function get($path, $callback)
    {
        $this->addRoute('GET', $path, $callback);
    }

    /**
     * Add POST route
     */
    public function post($path, $callback)
    {
        $this->addRoute('POST', $path, $callback);
    }

    /**
     * Add PUT route
     */
    public function put($path, $callback)
    {
        $this->addRoute('PUT', $path, $callback);
    }

    /**
     * Add DELETE route
     */
    public function delete($path, $callback)
    {
        $this->addRoute('DELETE', $path, $callback);
    }

    /**
     * Add route to routes array
     */
    private function addRoute($method, $path, $callback)
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback
        ];
    }

    /**
     * Set 404 not found callback
     */
    public function notFound($callback)
    {
        $this->notFoundCallback = $callback;
    }

    /**
     * Run the router
     */
    public function run()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = $_SERVER['REQUEST_URI'];

        // Remove query string from URI
        $requestUri = strtok($requestUri, '?');

        // Remove base path (if running in subdirectory)
        $basePath = '/Gatherly-2025/backend';
        if (strpos($requestUri, $basePath) === 0) {
            $requestUri = substr($requestUri, strlen($basePath));
        }

        // Ensure URI starts with /
        if (substr($requestUri, 0, 1) !== '/') {
            $requestUri = '/' . $requestUri;
        }

        // Find matching route
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod) {
                $pattern = $this->convertPathToRegex($route['path']);

                if (preg_match($pattern, $requestUri, $matches)) {
                    array_shift($matches); // Remove full match

                    // Call the callback with parameters
                    call_user_func_array($route['callback'], $matches);
                    return;
                }
            }
        }

        // No route found - call 404 handler
        if ($this->notFoundCallback) {
            call_user_func($this->notFoundCallback);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Route not found']);
        }
    }

    /**
     * Convert path to regex pattern
     * Converts /api/venues/:id to regex pattern
     */
    private function convertPathToRegex($path)
    {
        // Escape forward slashes
        $pattern = preg_replace('/\//', '\\/', $path);

        // Convert :param to named capture groups
        $pattern = preg_replace('/:([a-zA-Z0-9_]+)/', '([a-zA-Z0-9_-]+)', $pattern);

        return '/^' . $pattern . '$/';
    }

    /**
     * Get request body as array
     */
    public static function getRequestBody()
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    /**
     * Get query parameters
     */
    public static function getQueryParams()
    {
        return $_GET;
    }
}
