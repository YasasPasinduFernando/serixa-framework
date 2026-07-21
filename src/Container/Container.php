<?php

declare(strict_types=1);

namespace Serixa\Container;

use Serixa\Exception\ContainerException;

/**
 * Lightweight, PSR-inspired service container (no facades, no auto-wiring magic beyond constructors).
 *
 * Eventually replaces process-wide static managers for Theme/State when apps opt in.
 */
final class Container
{
    /** @var array<string, callable(self): mixed> */
    private array $bindings = [];

    /** @var array<string, mixed> */
    private array $instances = [];

    /** @var array<string, true> */
    private array $singletons = [];

    /**
     * Bind an abstract to a factory (new instance each resolve unless singleton).
     *
     * @param callable(self): mixed $factory
     */
    public function bind(string $abstract, callable $factory): void
    {
        $this->bindings[$abstract] = $factory;
        unset($this->instances[$abstract], $this->singletons[$abstract]);
    }

    /**
     * Bind an abstract as a shared singleton factory.
     *
     * @param callable(self): mixed $factory
     */
    public function singleton(string $abstract, callable $factory): void
    {
        $this->bindings[$abstract] = $factory;
        $this->singletons[$abstract] = true;
        unset($this->instances[$abstract]);
    }

    /**
     * Store a concrete instance.
     */
    public function instance(string $abstract, mixed $instance): void
    {
        $this->instances[$abstract] = $instance;
        $this->singletons[$abstract] = true;
    }

    /**
     * Resolve a binding or autowire a concrete class via constructor injection.
     *
     * @template T of object
     * @param class-string<T>|string $abstract
     * @return ($abstract is class-string<T> ? T : mixed)
     */
    public function resolve(string $abstract): mixed
    {
        if (array_key_exists($abstract, $this->instances)) {
            return $this->instances[$abstract];
        }

        if (isset($this->bindings[$abstract])) {
            $concrete = ($this->bindings[$abstract])($this);

            if (isset($this->singletons[$abstract])) {
                $this->instances[$abstract] = $concrete;
            }

            return $concrete;
        }

        if (! class_exists($abstract)) {
            throw new ContainerException(sprintf('Cannot resolve "%s".', $abstract));
        }

        return $this->build($abstract);
    }

    public function has(string $abstract): bool
    {
        return array_key_exists($abstract, $this->instances)
            || isset($this->bindings[$abstract])
            || class_exists($abstract);
    }

    /**
     * @param class-string $class
     */
    private function build(string $class): object
    {
        $reflection = new \ReflectionClass($class);

        if (! $reflection->isInstantiable()) {
            throw new ContainerException(sprintf('Class "%s" is not instantiable.', $class));
        }

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return $reflection->newInstance();
        }

        $parameters = [];

        foreach ($constructor->getParameters() as $parameter) {
            $parameters[] = $this->resolveParameter($parameter);
        }

        return $reflection->newInstanceArgs($parameters);
    }

    private function resolveParameter(\ReflectionParameter $parameter): mixed
    {
        $type = $parameter->getType();

        if ($type instanceof \ReflectionNamedType && ! $type->isBuiltin()) {
            return $this->resolve($type->getName());
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        if ($parameter->allowsNull()) {
            return null;
        }

        throw new ContainerException(sprintf(
            'Unable to resolve parameter $%s of %s.',
            $parameter->getName(),
            $parameter->getDeclaringClass()?->getName() ?? 'unknown',
        ));
    }
}
