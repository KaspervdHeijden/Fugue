<?php

declare(strict_types=1);

namespace Fugue\Persistence\Database\ORM;

use InvalidArgumentException;
use ReflectionNamedType;
use ReflectionException;
use ReflectionMethod;
use ReflectionClass;
use LogicException;
use ReflectionType;

use function ucfirst;

final class ReflectionClassMapper implements RecordMapperInterface
{
    private ReflectionClass $reflection;

    public function __construct(string $className)
    {
        if ($className === '') {
            throw new InvalidArgumentException('Classname should not be empty');
        }

        try {
            $reflection  = new ReflectionClass($className);
            $constructor = $reflection->getConstructor();

            if (
                $constructor instanceof ReflectionMethod &&
                $constructor->getNumberOfRequiredParameters() > 0
            ) {
                throw new LogicException(
                    "Cannot instantiate a new '{$className}' because it requires arguments"
                );
            }

            $this->reflection = $reflection;
        } catch (ReflectionException $reflectionException) {
            throw new InvalidArgumentException(
                "Could not load class '{$className}'",
                (int)$reflectionException->getCode(),
                $reflectionException
            );
        }
    }

    private function shouldSet(mixed $value, ?ReflectionType $type): bool
    {
        if (! $type instanceof ReflectionType) {
            return true;
        }

        if ($value === null && ! $type->allowsNull()) {
            return false;
        }

        return true;
    }

    private function cast(mixed $value, ?ReflectionType $type): mixed
    {
        if (! $type instanceof ReflectionNamedType) {
            return $value;
        }

        switch ($type->getName()) {
            case 'int':
                return (int)$value;
            case 'bool':
                return (bool)$value;
            case 'float':
                return (float)$value;
            case 'string':
                return (string)$value;
            case 'array':
                return (array)$value;
            default:
                return $value;
        }
    }

    private function setProperty(
        object $instance,
        string $propertyName,
        mixed $value
    ): void {
        $setter = 'set' . ucfirst($propertyName);
        if (! $this->reflection->hasMethod($setter)) {
            if ($this->reflection->hasProperty($propertyName)) {
                /** @noinspection PhpUnhandledExceptionInspection */
                $type = $this->reflection->getProperty($propertyName)->getType();

                if ($this->shouldSet($value, $type)) {
                    $instance->$propertyName = $this->cast($value, $type);
                }
            }

            return;
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        $method = $this->reflection->getMethod($setter);
        if ($method->getNumberOfRequiredParameters() !== 1) {
            return;
        }

        $params = $method->getParameters();
        $type   = $params[0]->getType();

        if ($this->shouldSet($value, $type)) {
            $instance->$setter($this->cast($value, $type));
        }
    }

    public function arrayToObject(array $record): object
    {
        $className = $this->reflection->getName();
        $instance  = new $className();

        foreach ($record as $property => $value) {
            $this->setProperty($instance, $property, $value);
        }

        return $instance;
    }
}
