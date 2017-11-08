<?php

namespace Dewep\Http;

use Dewep\Http\Headers;
use FastRoute\RouteCollector;
use FastRoute\Dispatcher;
use Dewep\Exception\HttpExeption;

/**
 * Fast-Route
 *
 * @author Mikhail Knyazhev <markus621@gmail.com>
 */
class Route
{

    use \Dewep\Http\Traits\Http;

    protected $routes;
    protected $headers;
    protected $result;

    public function __construct(array $routes, Headers $headers)
    {
        $this->routes = $routes['routes'] ?? $routes ?? [];
        $this->headers = $headers;
    }

    public function set(string $path, string $methods, string $class)
    {
        $this->routes[$path][$methods] = $class;
    }

    public function bind()
    {
        $routes = $this->routes;
        $dispatcher = \FastRoute\simpleDispatcher(function(RouteCollector $r) use ($routes) {
            foreach ($routes as $uri => $route) {
                foreach ($route as $method => $handler) {
                    $method = explode(',', $method);
                    $r->addRoute($method, $uri, $handler);
                }
            }
        });

        $httpMethod = $this->headers->getServerParam(Headers::REQUEST_METHOD,
                'GET');
        $uri = $this->headers->getServerParam(Headers::REQUEST_URI, '/');

        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                throw new HttpExeption('Method not found', 404);
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new HttpExeption('Method not allowed', 405);
                break;
        }

        $this->result = $routeInfo;

        if (!empty($this->result[2])) {
            $this->result[2] = array_map([$this, 'normalizeKey'],
                    $this->result[2]);
        }

        return $this;
    }

    public function getAttribute($name, $default = null)
    {
        $name = $this->normalizeKey($name);
        return $this->result[2][$name] ?? $default;
    }

    public function setAttribute($name, $value)
    {
        $this->result[2][$this->normalizeKey($name)] = $value;
    }

    public function removeAttribute($name, $value)
    {
        unset($this->result[2][$this->normalizeKey($name)]);
    }

    public function getAttributes()
    {
        return $this->result[2] ?? [];
    }

    public function getHandler()
    {
        return $this->result[1] ?? function () {
            throw new HttpExeption('Handler is not found', 500);
        };
    }

}
