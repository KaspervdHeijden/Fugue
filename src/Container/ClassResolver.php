<?php

declare(strict_types=1);

namespace Fugue\Container;

use Fugue\Collection\CollectionList;
use Fugue\Collection\CollectionMap;
use Fugue\Caching\CacheInterface;
use ReflectionException;
use ReflectionMethod;
use ReflectionClass;

final class ClassResolver
{
    private CacheInterface $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function resolve(
        string $className,
        Container $container,
        CollectionMap $classToObjects
    ) {
        $reflectionClasses = $this->getArgumentClassesFromConstructorWithCache($className);
        $arguments         = $reflectionClasses->map(
            static function (ReflectionClass $reflectionClass) use ($container, $classToObjects) {
                $typeName = $reflectionClass->getName();

                if ($classToObjects->containsKey($typeName)) {
                    return $classToObjects[$typeName];
                } elseif ($container->isRegistered($typeName)) {
                    return $container->resolve($typeName);
                }

                throw CannotResolveClassException::forUnresolvedClass($typeName);
            }
        );

        return new $className(...$arguments);
    }

    private function getArgumentClassesFromConstructorWithCache(string $className): CollectionList
    {
        if ($this->cache->hasEntry($className)) {
            return $this->cache->retrieve($className);
        }

        $classes = $this->getArgumentClassesFromConstructor($className);
        $this->cache->store($className, $classes);

        return $classes;
    }

    private function getArgumentClassesFromConstructor(string $className): CollectionList
    {
        try {
            $classes     = new CollectionList([], ReflectionClass::class);
            $reflection  = new ReflectionClass($className);
            $constructor = $reflection->getConstructor();

            if (! $constructor instanceof ReflectionMethod) {
                return $classes;
            }

            $parameters = $constructor->getParameters();
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

            return $classes;
        } catch (ReflectionException $reflectionException) {
            throw InvalidClassException::forClassName($className);
        }
    }
}
