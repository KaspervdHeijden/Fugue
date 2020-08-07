<?php

declare(strict_types=1);

namespace Fugue\HTTP\State;

interface SessionRepositoryInterface
{
    /**
     * Gets a value by name.
     *
     * @param string $userIdentifier The user identifier.
     * @param string $name           The name to load the value for.
     *
     * @return mixed                 The value for the given name.
     */
    public function getByName(string $userIdentifier, string $name);

    /**
     * Persists a value.
     *
     * @param string $userIdentifier The user identifier.
     * @param string $name           The name for the value to persist.
     *
     * @return mixed                 The value to persist.
     */
    public function persist(string $userIdentifier, string $name, $value): void;

    /**
     * Checks if a value is set for the given name.
     *
     * @param string $userIdentifier The user identifier.
     * @param string $name           The name to test the existence for.
     *
     * @return bool                  TRUE if a value exist for the give name, FALSE otherwise.
     */
    public function exists(string $userIdentifier, string $name): bool;

    /**
     * Removes a value for the given name.
     *
     * @param string $userIdentifier The user identifier.
     * @param string $name           The name to delete.
     */
    public function delete(string $userIdentifier, string $name): void;

    /**
     * Clears all values.
     *
     * @param string $userIdentifier The user identifier.
     */
    public function clear(string $userIdentifier): void;
}
