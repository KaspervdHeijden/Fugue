<?php

declare(strict_types=1);

namespace Fugue\Container;

final class ContainerDefinition
{
    /** @var mixed */
    private $definition;
    private string $name;
    private int $type;

    private function __construct(
        string $name,
        int $type,
        $definition
    ) {
        $this->definition = $definition;
        $this->name       = $name;
        $this->type       = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getDefinition()
    {
        return $this->definition;
    }

    public static function singleton(
        string $name,
        callable $definition
    ): self {
        return new self(
            $name,
            Container::TYPE_SINGLETON,
            $definition
        );
    }

    /**
     * Returns a raw Container value definition.
     *
     * @param string $name      The name for the service.
     * @param mixed $definition The service/object to register.
     *
     * @return static           The created container definition.
     */
    public static function raw(
        string $name,
        $definition
    ): self {
        return new self(
            $name,
            Container::TYPE_RAW,
            $definition
        );
    }

    public static function factory(
        string $name,
        callable $definition
    ): self {
        return new self(
            $name,
            Container::TYPE_FACTORY,
            $definition
        );
    }
}
