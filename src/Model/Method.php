<?php

declare(strict_types=1);

namespace Clarilens\Http\Model;

final readonly class Method
{
    public function __construct(public string $value)
    {
        if ($value === '' || !preg_match("/^[!#\$%&'*+.^_`|~0-9A-Za-z-]+$/D", $value)) {
            throw new \InvalidArgumentException('Invalid HTTP method');
        }
    }

    public static function from(HTTPMethod|string $method): self
    {
        return new self($method instanceof HTTPMethod ? $method->value : $method);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
