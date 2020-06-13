<?php

declare(strict_types=1);

namespace Fugue\Container;

use Fugue\Collection\CollectionMap;
use Fugue\Caching\CacheInterface;
use ReflectionException;
use ReflectionMethod;
use ReflectionClass;

use function array_map;

final class ClassResolver
{
    private CacheInterface $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

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
            static function (ReflectionClass $reflectionClass) use ($container, $classToObjects) {
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
     * @return ReflectionClass[]
     */
    private function getArgumentClassesFromConstructor(string $className): array
    {
        if ($this->cache->hasValueForKey($className)) {
            return $this->cache->retrieve($className);
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

            $this->cache->store($className, $classes);
            return $classes;
        } catch (ReflectionException $reflectionException) {
            throw InvalidClassException::forClassName($className);
        }
    }
}
