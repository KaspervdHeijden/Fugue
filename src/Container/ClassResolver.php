<?php

declare(strict_types=1);

namespace Fugue\Container;

use Fugue\Collection\CollectionMap;
use ReflectionException;
use ReflectionMethod;
use ReflectionClass;

use function array_map;

final class ClassResolver
{
    /** @var ReflectionClass[][] */
    private $classCache = [];

    /**
     * @param string        $className
     * @param Container     $container
     * @param CollectionMap $classToObjects
     *
     * @return mixed        Instance of $className
     */
    public function resolve(
        string $className,
        Container $container,
        CollectionMap $classToObjects
    ) {
        $argumentClasses = $this->getArgumentClassesFromConstructor($className);
        $arguments       = array_map(
            function (ReflectionClass $reflectionClass) use ($container, $classToObjects) {
                $typeName = $reflectionClass->getName();

                if ($classToObjects->containsKey($typeName)) {
                    return $classToObjects[$typeName];
                } elseif ($container->isRegistered($typeName)) {
                    return $container->resolve($typeName);
                }

                throw CannotResolveClassException::forUnresolvedClass($typeName);
            },
            $argumentClasses
        );

        return new $className(...$arguments);
    }

    /**
     * @param string $className The name of the class to resolve the constructor arguments for.
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
