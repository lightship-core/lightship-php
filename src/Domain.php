<?php

namespace Khalyomede;

use Webmozart\Assert\Assert;

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
     * @param array<array<string, string>> $routes
     */
    public function setRoutes(array $routes): self
    {
        foreach ($routes as $route) {
            $this->addRoute($route);
        }

        return $this;
    }

    /**
     * @param array<string, string|array<string, array<string, string>>> $route
     */
    public function addRoute(array $route): self
    {
        Assert::keyExists($route, "path");
        Assert::string($route["path"]);
        Assert::notEmpty($route["path"]);

        if (isset($route["queries"])) {
            Assert::isArray($route["queries"]);

            assert(is_array($route["queries"]));

            foreach ($route["queries"] as $query) {
                Assert::keyExists($query, "key");
                Assert::keyExists($query, "value");
                Assert::string($query["key"]);
                Assert::string($query["value"]);
                Assert::notEmpty($query["key"]);
                Assert::notEmpty($query["value"]);
            }
        }

        $r = new Route();

        $r->setPath($route["path"])
            ->setQueries($route["queries"] ?? []);

        $this->routes[] = $r;

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
