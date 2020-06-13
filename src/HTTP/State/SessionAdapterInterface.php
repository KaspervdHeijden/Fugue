<?php

declare(strict_types=1);

namespace Fugue\HTTP\State;

interface SessionAdapterInterface
{
    /**
     * Starts the session. Should only be called if the
     * session has not been started before.
     *
     * @param SessionSettings $settings Session config settings.
     */
    public function start(SessionSettings $settings): void;

    /**
     * Gets a session property.
     *
     * @param string $name The session variable name.
     * @return mixed       The value associated with $name, or null if not defined.
     */
    public function get(string $name);

    /**
     * Sets a session property.
     *
     * @param string $name  The variable name.
     * @param mixed  $value The value to store.
     */
    public function set(string $name, $value): void;

    /**
     * Checks if a session property exists.
     *
     * @param string $name The name of the property to check.
     * @return bool        TRUE if the value exists, FALSE otherwise.
     */
    public function has(string $name): bool;

    /**
     * Unset a session property.
     *
     * @param string $name The variable name to unset.
     */
    public function unset(string $name): void;

    /**
     * Clears all session data.
     */
    public function clear(): void;

    /**
     * Closes the session.
     */
    public function close(): void;
}
