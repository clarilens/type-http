<?php

declare(strict_types=1);

namespace Clarilens\Http\Model;

use Clarilens\Http\Model\Url\Url;

final readonly class Request
{
    public Method $method;
    public Headers $headers;

    public function __construct(
        Method|HTTPMethod|string $method,
        public Url $url,
        ?Headers $headers = null,
        public ?string $body = null,
    ) {
        $this->method = $method instanceof Method ? $method : Method::from($method);
        $this->headers = $headers ?? new Headers();
    }

    /** @return array{string, string, array{headers: array<string, list<string>>, body?: string, http_errors: false}} */
    public function toGuzzleArguments(): array
    {
        $arguments = $this->clientArguments();
        $arguments[2]['http_errors'] = false;
        return $arguments;
    }

    /** @return array{string, string, array{headers: array<string, list<string>>, body?: string}} */
    public function toSymfonyArguments(): array
    {
        return $this->clientArguments();
    }

    /** @return array{string, string, array{headers: array<string, list<string>>, body?: string}} */
    private function clientArguments(): array
    {
        $options = ['headers' => $this->headers->all()];
        if ($this->body !== null) {
            $options['body'] = $this->body;
        }

        return [$this->method->value, (string) $this->url, $options];
    }
}
