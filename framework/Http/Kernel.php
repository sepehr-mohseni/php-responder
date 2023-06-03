<?php

namespace SepMsi\Framework\Http;

use FastRoute\RouteCollector;
use FastRoute\Dispatcher;
use function FastRoute\simpleDispatcher;

class Kernel
{
    public function handle(Request $request): Response
    {
        $dispatcher = simpleDispatcher(function (RouteCollector $routeCollector) {
            $routes = include BASE_PATH . '/routes/web.php';
            foreach ($routes as $route) {
                $routeCollector->addRoute(...$route);
            }
        });

        $httpMethod = $request->getMethod();
        $uri = $request->getPathInfo();
        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

        $status = $routeInfo[0];
        if ($status === Dispatcher::NOT_FOUND) {
            return $this->handleNotFound();
        } elseif ($status === Dispatcher::METHOD_NOT_ALLOWED) {
            return $this->handleMethodNotAllowed($routeInfo[1]);
        }
        [$status, [$controller, $method], $vars] = $routeInfo;

        if ($httpMethod === 'POST') {
            $vars['body'] = $request->getPostData();
        }
        return (new $controller())->$method($vars);
    }

    protected function handleNotFound(): Response
    {
        return new Response('404 Not Found', 404);
    }

    protected function handleMethodNotAllowed(array $allowedMethods): Response
    {
        $allowedMethodsString = implode(', ', $allowedMethods);
        return new Response('405 Method Not Allowed. Allowed methods: ' . $allowedMethodsString, 405);
    }
}
