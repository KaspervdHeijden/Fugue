<?php

declare(strict_types=1);

namespace Fugue\HTTP\State;

final class RepositorySessionAdapter implements SessionAdapterInterface
{
    /** @var SessionRepositoryInterface */
    private $sessionRepository;

    public function __construct(SessionRepositoryInterface $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
    }

    public function start(SessionSettings $settings): void
    {
    }

    public function get(string $name)
    {
        $this->sessionRepository->getByName($name);
    }

    public function set(string $name, $value): void
    {
        $this->sessionRepository->persist($name, $value);
    }

    public function has(string $name): bool
    {
        $this->sessionRepository->exists($name);
    }

    public function unset(string $name): void
    {
        $this->sessionRepository->delete($name);
    }

    public function clear(): void
    {
        $this->sessionRepository->clear();
    }

    public function close(): void
    {
    }
}
