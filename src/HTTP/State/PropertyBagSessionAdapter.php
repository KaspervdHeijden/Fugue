<?php

declare(strict_types=1);

namespace Fugue\HTTP\State;

use Fugue\Collection\PropertyBag;

final class PropertyBagSessionAdapter implements SessionAdapterInterface
{
    private PropertyBag $sessionData;

    public function __construct(PropertyBag $sessionData)
    {
        $this->sessionData = $sessionData;
    }

    public function start(): void
    {
    }

    public function get(string $name)
    {
        return $this->sessionData[$name] ?? null;
    }

    public function set(string $name, $value): void
    {
        $this->sessionData[$name] = $value;
    }

    public function has(string $name): bool
    {
        return $this->sessionData->containsKey($name);
    }

    public function unset(string $name): void
    {
        unset($this->sessionData[$name]);
    }

    public function clear(): void
    {
        $this->sessionData->clear();
    }

    public function close(): void
    {
    }
}
