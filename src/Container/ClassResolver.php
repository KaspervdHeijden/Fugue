<?php

declare(strict_types=1);

namespace Fugue\Container;

use Fugue\Collection\ArrayMap;
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
     * @param ArrayMap  $classToObjects
     *
     * @return mixed    Instance of $className
     */
    public function resolve(
        string $className,
        Container $container,
        ArrayMap $classToObjects
    ) {
        $argumentClasses = $this->getArgumentClassesFromConstructor($className);
        $objectsToLoad   = array_map(
            static function (ReflectionClass $reflectionClass) use ($classToObjects): string {
                $typeName = $reflectionClass->getName();
                if ($classToObjects->containsKey($typeName)) {
                    return $classToObjects[$typeName];
                }

                return $typeName;
            },
            $argumentClasses
        );

        $arguments = array_map(
            function ($objectToLoad) use ($container, $classToObjects) {
                if (! is_string($objectToLoad)) {
                    return $objectToLoad;
                } elseif ($container->isRegistered($objectToLoad)) {
                    return $container->resolve($objectToLoad);
                } else {
                    return $this->resolve($objectToLoad, $container, $classToObjects);
                }
            },
            $objectsToLoad
        );

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
            $classes    = [];

            foreach ($parameters as $parameter) {
                $class = $parameter->getClass();
                if ($class instanceof ReflectionClass) {
                    $classes[] = $class;
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

            $this->classCache[$className] = $classes;
            return $classes;
        } catch (ReflectionException $reflectionException) {
            throw InvalidClassException::forClassName($className);
        }
    }
}
