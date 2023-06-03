<?php

namespace App\Controllers;

use SepMsi\Framework\Http\Request;
use SepMsi\Framework\Http\Response;

class HomeController
{
    public function index(): Response
    {
        $content = "<h1>Hello World</h1>";
        return new Response($content);
    }

    public function show(int $id): Response
    {
        $content = "<h1>this is article {$id}</h1>";
        return new Response($content);
    }

    public function store(array $args): Response
    {
        $title = $args['body']['title'] ?? '';
        $content = $args['body']['content'] ?? '';
        return new Response($title);
    }
}