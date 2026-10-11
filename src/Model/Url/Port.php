<?php

declare(strict_types=1);

namespace Clarilens\Http\Model\Url;

final readonly class Port
{
    public function __construct(
        public int $value,
    ) {
        if ($value < 1 || $value > 65535) {
            throw new \InvalidArgumentException('Port must be between 1 and 65535');
        }
    }

    public static function fromString(string $port): self
    {
        if (!preg_match('/^[0-9]{1,5}$/D', $port)) {
            throw new \InvalidArgumentException('Invalid port');
        }

        return new self((int) $port);
    }
}
