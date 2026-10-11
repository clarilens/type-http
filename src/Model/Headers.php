<?php

declare(strict_types=1);

namespace Clarilens\Http\Model;

final readonly class Headers
{
    /** @var array<string, list<string>> */
    private array $values;

    /** @param array<string, string|list<string>> $headers */
    public function __construct(array $headers = [])
    {
        $values = [];
        foreach ($headers as $name => $items) {
            self::checkName($name);
            foreach (is_array($items) ? $items : [$items] as $item) {
                self::checkValue($item);
                $values[strtolower($name)][] = $item;
            }
        }
        $this->values = $values;
    }

    public function has(string $name): bool
    {
        self::checkName($name);
        return isset($this->values[strtolower($name)]);
    }

    /** @return list<string> */
    public function get(string $name): array
    {
        self::checkName($name);
        return $this->values[strtolower($name)] ?? [];
    }

    /** @return array<string, list<string>> */
    public function all(): array
    {
        return $this->values;
    }

    /** @param string|list<string> $values */
    public function with(string $name, string|array $values): self
    {
        self::checkName($name);
        $headers = $this->values;
        $headers[strtolower($name)] = is_array($values) ? $values : [$values];
        return new self($headers);
    }

    public function without(string $name): self
    {
        self::checkName($name);
        $headers = $this->values;
        unset($headers[strtolower($name)]);
        return new self($headers);
    }

    private static function checkName(string $name): void
    {
        if ($name === '' || !preg_match("/^[!#\$%&'*+.^_`|~0-9A-Za-z-]+$/D", $name)) {
            throw new \InvalidArgumentException('Invalid HTTP header name');
        }
    }

    private static function checkValue(string $value): void
    {
        if (preg_match('/[\x00-\x08\x0a-\x1f\x7f]/', $value)) {
            throw new \InvalidArgumentException('Invalid HTTP header value');
        }
    }
}
