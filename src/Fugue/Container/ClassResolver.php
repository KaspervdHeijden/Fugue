<?php

declare(strict_types=1);

namespace Fugue\Container;

use Fugue\Collection\CollectionList;
use Fugue\Caching\CacheInterface;
use ReflectionNamedType;
use ReflectionException;
use ReflectionParameter;
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
                              static function (ReflectionParameter $parameter) use ($container, $className): mixed {
                                  /** @var ReflectionNamedType $type */
                                  $type     = $parameter->getType();
                                  $typeName = $type->getName();

                                  if ($container->isRegistered($typeName)) {
                                      return $container->resolve($typeName);
                                  }

                                  if ($parameter->isOptional()) {
                                      return null;
                                  }

                                  throw CannotResolveClassException::forUnresolvedClass($typeName, $className);
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
            $paramList   = CollectionList::forType(ReflectionParameter::class);
            $reflection  = new ReflectionClass($className);
            $constructor = $reflection->getConstructor();

            if (! $constructor instanceof ReflectionMethod) {
                return $paramList;
            }

            $parameters = $constructor->getParameters();
            foreach ($parameters as $parameter) {
                if ($parameter->getType() instanceof ReflectionNamedType) {
                    $paramList[] = $parameter;
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

            return $paramList;
        } catch (ReflectionException $reflectionException) {
            throw InvalidClassException::forClassName(
                $className,
                $reflectionException
            );
        }
    }
}
