<?php

namespace SepMsi\Framework\Http;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
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

        $controllerInstance = new $controller($request);
        $response = $controllerInstance->$method($vars);

        if (!$response instanceof Response) {
            $response = $this->createResponse($response);
        }

        return $response;
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

    protected function createResponse($content, int $status = 200, array $headers = []): Response
    {
        if (is_array($content) || is_object($content)) {
            $content = json_encode($content);
            $headers['Content-Type'] = 'application/json';
        }

        return new Response($content, $status, $headers);
    }
}
