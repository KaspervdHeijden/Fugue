<?php

declare(strict_types=1);

namespace Fugue\Container;

use Fugue\Collection\Map;
use ReflectionException;
use ReflectionMethod;
use ReflectionClass;

use function array_map;

final class ClassResolver
{
    /** @var ReflectionClass[][] */
    private $classCache = [];

    /**
     * @param string    $className
     * @param Container $container
     * @param Map       $classToObjects
     *
     * @return mixed    Instance of $className
     */
    public function resolve(
        string $className,
        Container $container,
        Map $classToObjects
    ) {
        $argumentClasses = $this->getArgumentClassesFromConstructor($className);
        $objectsToLoad   = array_map(
            static function (ReflectionClass $reflectionClass) use ($classToObjects): string {
                $typeName = $reflectionClass->getName();
                if (isset($classToObjects[$typeName])) {
                    return $classToObjects[$typeName];
                }

                return $typeName;
            },
            $argumentClasses
        );

        $arguments = [];
        foreach ($objectsToLoad as $objectToLoad) {
            if (isset($container[$objectToLoad])) {
                $arguments[] = $container[$objectToLoad];
            } else {
                $arguments[] = $this->resolve($objectToLoad, $container, $classToObjects);
            }
        }

        return new $className(...$arguments);
    }

    /**
     * @param string $className
     * @return ReflectionClass[]
     */
    private function getArgumentClassesFromConstructor(string $className): array
    {
        if (isset($this->classCache[$className])) {
            return $this->classCache[$className];
        }

        try {
            $reflection  = new ReflectionClass($className);
            $constructor = $reflection->getConstructor();
            if (! $constructor instanceof ReflectionMethod) {
                return [];
            }

            $parameters = $constructor->getParameters();
            $names      = [];

            foreach ($parameters as $parameter) {
                $class = $parameter->getClass();
                if ($class instanceof ReflectionClass) {
                    $names[] = $class;
                    continue;
                }

                if ($parameter->isOptional()) {
                    continue;
                }

                throw CannotResolveClassException::forConstructorParameter(
                    $parameter->getName(),
                    $className
                );
            }

            $this->classCache[$className] = $names;
            return $names;
        } catch (ReflectionException $reflectionException) {
            throw InvalidClassException::forClassName($className);
        }
    }
}
