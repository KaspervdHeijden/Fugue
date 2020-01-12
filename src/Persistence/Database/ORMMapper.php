<?php

declare(strict_types=1);

namespace Fugue\Persistence\Database;

use InvalidArgumentException;
use ReflectionException;
use ReflectionMethod;
use ReflectionClass;
use LogicException;
use ReflectionType;

use function ucfirst;

final class ORMMapper
{
    /** @var ReflectionClass */
    private $reflection;

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
                    "Cannot instantiate a new {$className} because it requires arguments"
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

    private function shouldSet($value, ?ReflectionType $type): bool
    {
        if (! $type instanceof ReflectionType) {
            return true;
        }

        if ($value === null && ! $type->allowsNull()) {
            return false;
        }

        return true;
    }

    /**
     * @param mixed|null          $value
     * @param ReflectionType|null $type
     *
     * @return mixed|null         The casted value.
     */
    private function cast($value, ?ReflectionType $type)
    {
        if (! $type instanceof ReflectionType) {
            return $value;
        }

        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
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

    /**
     * @param object     $instance      The instance to set the property on.
     * @param string     $propertyName  The name of the property to set.
     * @param mixed|null $value         The value to set.
     *
     * @throws ReflectionException      Shouldn't really.
     */
    private function setProperty($instance, string $propertyName, $value): void
    {
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
        $params = $method->getParameters();

        if ($method->getNumberOfRequiredParameters() !== 1) {
            return;
        }

        $type = $params[0]->getType();
        if ($this->shouldSet($value, $type)) {
            $instance->$setter($this->cast($value, $type));
        }
    }

    public function recordToObjectInstance(array $record)
    {
        $className = $this->reflection->getName();
        $instance  = new $className();

        foreach ($record as $property => $value) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->setProperty($instance, $property, $value);
        }

        return $instance;
    }
}
