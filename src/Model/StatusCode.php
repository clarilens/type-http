<?php

declare(strict_types=1);

namespace Clarilens\Http\Model;

final readonly class StatusCode
{
    public int $value;

    public function __construct(HTTPStatus|int $value)
    {
        if ($value instanceof HTTPStatus) {
            $value = $value->value;
        }

        if ($value < 100 || $value > 599) {
            throw new \InvalidArgumentException('HTTP status must be between 100 and 599');
        }

        $this->value = $value;
    }

    public static function from(HTTPStatus|int $status): self
    {
        return new self($status);
    }

    public function isSuccessful(): bool
    {
        return $this->value >= 200 && $this->value < 300;
    }
}
