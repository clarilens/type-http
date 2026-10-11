<?php

declare(strict_types=1);

namespace Clarilens\Http\Model\Url;

final readonly class Host
{
    public bool $isIp;

    public function __construct(public string $value)
    {
        $address = $value;
        if (str_starts_with($value, '[') && str_ends_with($value, ']')) {
            $address = substr($value, 1, -1);
            if (filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) {
                throw new \InvalidArgumentException('Invalid IPv6 host');
            }
        } elseif (str_contains($value, ':')) {
            throw new \InvalidArgumentException('IPv6 host must be bracketed');
        }

        $this->isIp = filter_var($address, FILTER_VALIDATE_IP) !== false;
        if ($this->isIp) {
            return;
        }

        if (strlen($value) > 253 || !preg_match('/^[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?(?:\.[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?)*\.?$/iD', $value)) {
            throw new \InvalidArgumentException('Invalid host');
        }
    }

    public static function fromString(string $host): self
    {
        return new self($host);
    }
}
