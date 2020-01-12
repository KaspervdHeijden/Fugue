<?php

declare(strict_types=1);

namespace Fugue\Configuration;

use IteratorAggregate;
use ArrayIterator;
use ArrayAccess;
use Traversable;
use Countable;

use function array_key_exists;
use function count;

final class SettingBranch implements ArrayAccess, IteratorAggregate, Countable
{
    /** @var mixed[] */
    private $branchData;

    /** @var string */
    private $path;

    public function __construct(string $path, array $branchData)
    {
        $this->branchData = $branchData;
        $this->path       = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->branchData);
    }

    public function offsetGet($offset)
    {
        return $this->branchData[$offset] ?? null;
    }

    public function offsetSet($offset, $value)
    {
        $this->branchData[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->branchData[$offset]);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->branchData);
    }

    public function count(): int
    {
        return count($this->branchData);
    }
}
