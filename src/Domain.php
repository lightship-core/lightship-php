<?php

namespace Khalyomede;

class Domain
{
    private string $base;

    /**
     * @var array<Route>
     */
    private array $routes;

    public function __construct()
    {
        $this->base = "";
        $this->routes = [];
    }

    public function setBase(string $base): self
    {
        $this->base = $base;

        return $this;
    }

    /**
     * @param array<Route> $routes
     */
    public function setRoutes(array $routes): self
    {
        foreach ($routes as $route) {
            $this->addRoute($route);
        }

        return $this;
    }

    public function addRoute(Route $route): self
    {
        $this->routes[] = $route;

        return $this;
    }

    /**
     * @return array<Route>
     */
    public function routes(): array
    {
        return $this->routes;
    }

    public function base(): string
    {
        return $this->base;
    }
}
