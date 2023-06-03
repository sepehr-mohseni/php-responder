<?php

namespace SepMsi\Framework\Http;

class Response
{
    protected array $contentTypes = [
        'html' => 'text/html; charset=utf-8',
        'json' => 'application/json; charset=utf-8',
    ];

    protected array $statusTexts = [
        200 => 'OK',
        404 => 'Not Found',
    ];

    protected array $cookies = [];

    public function __construct(
        protected ?string $content = '',
        protected int $status = 200,
        protected array $headers = []
    ) {
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function setStatusCode(int $status): void
    {
        $this->status = $status;
    }

    public function setHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    public function setCookie(string $name, string $value, int $expires = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httpOnly = true): void
    {
        $this->cookies[$name] = compact('value', 'expires', 'path', 'domain', 'secure', 'httpOnly');
    }

    public function send(): void
    {
        $this->sendHeaders();
        $this->sendContent();
        $this->sendCookies();
    }

    protected function sendHeaders(): void
    {
        if (headers_sent()) {
            return;
        }

        $statusText = $this->statusTexts[$this->status] ?? 'Unknown Status';
        header("HTTP/1.1 {$this->status} {$statusText}");

        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        $contentType = $this->detectContentType();
        header("Content-Type: {$contentType}");
    }

    protected function sendContent(): void
    {
        echo $this->content;
    }

    protected function sendCookies(): void
    {
        foreach ($this->cookies as $name => $properties) {
            setcookie($name, $properties['value'], $properties['expires'], $properties['path'], $properties['domain'], $properties['secure'], $properties['httpOnly']);
        }
    }

    protected function detectContentType(): string
    {
        $extension = pathinfo($this->content, PATHINFO_EXTENSION);
        if ($extension === 'json') {
            return $this->contentTypes['json'];
        }

        return $this->contentTypes['html'];
    }
}
