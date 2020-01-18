<?php

declare(strict_types=1);

namespace Fugue\Container;

final class ContainerDefinition
{
    /** @var string */
    private $name;

    /** @var mixed */
    private $definition;

    /** @var int */
    private $type;

    private function __construct(string $name, $definition, int $type)
    {
        $this->definition = $definition;
        $this->name       = $name;
        $this->type       = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDefinition()
    {
        return $this->definition;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public static function singleton(string $name, callable $definition): self
    {
        return new self($name, $definition, Container::TYPE_SINGLETON);
    }

    public static function raw(string $name, $definition): self
    {
        return new self($name, $definition, Container::TYPE_RAW);
    }

    public static function factory(string $name, callable $definition): self
    {
        return new self($name, $definition, Container::TYPE_FACTORY);
    }
}
