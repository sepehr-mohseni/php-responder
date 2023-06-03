<?php

namespace App\Controllers;

use SepMsi\Framework\Http\Request;
use SepMsi\Framework\Http\Response;

class HomeController
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index(): Response
    {
        $content = "<h1>Hello World</h1>";
        return new Response($content);
    }

    public function show(int $id): Response
    {
        $content = "<h1>This is article {$id}</h1>";
        return new Response($content);
    }

    public function store(array $args): Response
    {
        $title = $args['body']['title'] ?? '';
        $content = $args['body']['content'] ?? '';

        // Perform validation or other processing on the input data

        return new Response("Received title: $title");
    }
}
