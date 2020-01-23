<?php

declare(strict_types=1);

namespace Fugue\Collection;

final class GenericList extends ArrayList
{
    /** @var string */
    private $class;

    public function __construct(string $class, iterable $elements = [])
    {
        $this->class = $class;
        parent::__construct($elements);
    }

    /**
     * @param mixed $value The value to check.
     * @return bool        TRUE if the value is OK, FALSE otherwise.
     */
    protected function checkValue($value): bool
    {
        return $value instanceof $this->class;
    }
}
