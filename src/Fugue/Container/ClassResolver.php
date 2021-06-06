<?php

declare(strict_types=1);

namespace Fugue\Container;

use Fugue\Collection\CollectionList;
use Fugue\Caching\CacheInterface;
use ReflectionNamedType;
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

    public function resolve(string $className, Container $container): mixed
    {
        $arguments = $this->getArgumentClassesFromConstructorWithCache($className)
                          ->map(
                              static function (string $typeName) use ($container): mixed {
                                    if ($container->isRegistered($typeName)) {
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
            $reflection  = new ReflectionClass($className);
            $classes     = CollectionList::forString([]);
            $constructor = $reflection->getConstructor();

            if (! $constructor instanceof ReflectionMethod) {
                return $classes;
            }

            $parameters = $constructor->getParameters();
            foreach ($parameters as $parameter) {
                $class = $parameter->getType();
                if ($class instanceof ReflectionNamedType) {
                    $classes[] = $class->getName();
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
            throw InvalidClassException::forClassName(
                $className,
                $reflectionException
            );
        }
    }
}
