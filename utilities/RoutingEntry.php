<?php

class RoutingEntry
{
    private array $method;
    private string $endpoint;
    private array $permissions;
    private string $action;

    public function __construct(array|string $method, string $endpoint, array|string|null $permissions, callable $action)
    {
        $this->method = is_string($method) ? [$method] : $method;
        $this->endpoint = preg_replace('/\/+/', '/', $endpoint);
        $this->endpoint = preg_replace('/\/+$/', '', $this->endpoint);
        $this->permissions = is_null($permissions) ? [] : (is_string($permissions) ? [$permissions] : $permissions);
        $this->action = $action;
    }

    public function getMethod(): array
    {
        return $this->method;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function getAction(): callable
    {
        return $this->action;
    }

    public function isValid(array &$matches): bool
    {
        $found = false;
        $http_method = $_SERVER['REQUEST_METHOD'];
        foreach ($this->method as $method) {
            if ($method === $http_method) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            return false;
        }
        $http_uri = $_SERVER['REQUEST_URI'];
        $pos = strpos($http_uri, '?');
        if (is_int($pos)) {
            $http_uri = substr($http_uri, 0, $pos);
        }

        $http_uri = preg_replace('/\/+/', '/', $http_uri);
        $http_uri = preg_replace('/\/+$/', '', $http_uri);

        $ex_endpoint = explode('/', $this->endpoint);
        $ex_http_uri = explode('/', $http_uri);
        if (count($ex_endpoint) != count($ex_http_uri)) {
            return false;
        }
        for ($i = 1; $i < count($ex_endpoint); $i++) {
            $end_p = $ex_endpoint[$i];
            $http_u = $ex_http_uri[$i];
            if ($end_p[0] == '%') {
                $matches[substr($end_p, 1)] = $http_u;
            } elseif ($end_p != $http_u) {
                return false;
            }
        }
        return true;
    }
}