<?php

declare(strict_types=1);

namespace Middleware;

use Opulence\Routing\Matchers\RouteMatcher;
use Opulence\Routing\Matchers\RouteNotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RouteMiddleware implements MiddlewareInterface
{
    private $route;

    public function __construct($route)
    {
        $this->route = $route;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Find a matching route
        try {
            $routeMatcher = new RouteMatcher($this->route->createRegexes());
            $matchedRoute = $routeMatcher->match(
                $_SERVER['REQUEST_METHOD'],
                $_SERVER['HTTP_HOST'],
                $_SERVER['REQUEST_URI']
            );
            if ($matchedRoute->getAction()->getClosure()) {
                $closure = $matchedRoute->getAction()->getClosure();
                $response = $closure($request);
            } else {
                $controllerName = $matchedRoute->getAction()->getClassName();
                $methodName = $matchedRoute->getAction()->getMethodName();
                $response = call_user_func([new $controllerName(), $methodName], $request);
            }
        } catch (RouteNotFoundException $ex) {
            throw $ex;
        }

        return $response;
    }
}
