<?php

declare(strict_types=1);

namespace Fugue\HTTP\State;

final class RepositorySessionAdapter implements SessionAdapterInterface
{
    private SessionRepositoryInterface $sessionRepository;
    private string $userIdentifier;

    public function __construct(
        SessionRepositoryInterface $sessionRepository,
        string $userIdentifier
    ) {
        $this->sessionRepository = $sessionRepository;
        $this->userIdentifier    = $userIdentifier;
    }

    public function start(): void
    {
    }

    public function get(string $name)
    {
        $this->sessionRepository->getByName(
            $this->userIdentifier,
            $name
        );
    }

    public function set(string $name, $value): void
    {
        $this->sessionRepository->persist(
            $this->userIdentifier,
            $name,
            $value
        );
    }

    public function has(string $name): bool
    {
        return $this->sessionRepository->exists(
            $this->userIdentifier,
            $name
        );
    }

    public function unset(string $name): void
    {
        $this->sessionRepository->delete(
            $this->userIdentifier,
            $name
        );
    }

    public function clear(): void
    {
        $this->sessionRepository->clear(
            $this->userIdentifier
        );
    }

    public function close(): void
    {
    }
}
