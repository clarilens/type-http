<?php

declare(strict_types=1);

namespace Clarilens\Http\Model\Url;

use Clarilens\Http\Model\Scheme;

final readonly class Url
{
    public function __construct(
        public Scheme $scheme,
        public Host $host,
        public ?Port $port = null,
        public ?string $user = null,
        public ?string $password = null,
        public ?string $path = null,
        public ?string $query = null,
        public ?string $fragment = null,
    ) {
        if ($this->password !== null && $this->user === null) {
            throw new \InvalidArgumentException(
                'Password cannot be specified without user',
            );
        }

        if ($path !== null && $path !== '' && !str_starts_with($path, '/')) {
            throw new \InvalidArgumentException('URL path must begin with /');
        }

        foreach (['user' => $user, 'password' => $password, 'path' => $path, 'query' => $query, 'fragment' => $fragment] as $part => $value) {
            if ($value !== null && (preg_match('/[\x00-\x20\x7f-\xff]/', $value) || preg_match('/%(?![0-9a-f]{2})/i', $value))) {
                throw new \InvalidArgumentException("Invalid URL $part");
            }
        }
        if ($user !== null && strpbrk($user, ':@/?#') !== false) {
            throw new \InvalidArgumentException('Invalid URL user');
        }
        if ($password !== null && strpbrk($password, '@/?#') !== false) {
            throw new \InvalidArgumentException('Invalid URL password');
        }
        if ($path !== null && strpbrk($path, '?#') !== false) {
            throw new \InvalidArgumentException('Invalid URL path');
        }
        if ($query !== null && str_contains($query, '#')) {
            throw new \InvalidArgumentException('Invalid URL query');
        }
    }

    public static function fromString(string $url): self
    {
        $parsed = parse_url($url);

        if ($parsed === false) {
            throw new \InvalidArgumentException('Invalid URL');
        }

        if (!isset($parsed['scheme'])) {
            throw new \InvalidArgumentException('URL scheme is required');
        }

        if (!isset($parsed['host'])) {
            throw new \InvalidArgumentException('URL host is required');
        }

        return new self(
            scheme: Scheme::from(strtolower($parsed['scheme'])),
            host: Host::fromString($parsed['host']),
            port: isset($parsed['port'])
                ? new Port($parsed['port'])
                : null,
            user: $parsed['user'] ?? null,
            password: $parsed['pass'] ?? null,
            path: $parsed['path'] ?? null,
            query: $parsed['query'] ?? null,
            fragment: $parsed['fragment'] ?? null,
        );
    }

    public function __toString(): string
    {
        $authority = '';

        if ($this->user !== null) {
            $authority .= $this->user;

            if ($this->password !== null) {
                $authority .= ':' . $this->password;
            }

            $authority .= '@';
        }

        $authority .= $this->host->value;

        if ($this->port !== null) {
            $authority .= ':' . $this->port->value;
        }

        $url = sprintf(
            '%s://%s',
            $this->scheme->value,
            $authority,
        );

        if ($this->path !== null) {
            $url .= $this->path;
        }

        if ($this->query !== null) {
            $url .= '?' . $this->query;
        }

        if ($this->fragment !== null) {
            $url .= '#' . $this->fragment;
        }

        return $url;
    }
}
