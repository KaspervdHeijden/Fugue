<?php

declare(strict_types=1);

namespace Fugue\HTTP\State;

use Fugue\Configuration\Config;
use InvalidArgumentException;

final class SessionAdapterFactory
{
    private const SETTING_NAME = 'session.identifier';

    /**
     * @var string The native PHP session implementation identifier.
     */
    public const SESSION_ADAPTER_NATIVE = 'native';

    /**
     * @var string The empty session implementation identifier.
     */
    public const SESSION_ADAPTER_EMPTY = 'empty';

    /**
     * @var string The system default session identifier.
     */
    public const SESSION_ADAPTER_DEFAULT = 'default';

    /** @var Config */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Gets a SessionAdapterInterface from an identifier.
     *
     * @param string $identifier       The identifier to get the SessionAdapterInterface for.
     * @return SessionAdapterInterface The SessionAdapterInterface.
     */
    public function getSessionAdapterFromIdentifier(string $identifier): SessionAdapterInterface
    {
        switch ($identifier) {
            case self::SESSION_ADAPTER_NATIVE:
                return new NativeSessionAdapter();
            case self::SESSION_ADAPTER_EMPTY:
                return new EmptySessionAdapter();
            case self::SESSION_ADAPTER_DEFAULT:
                $defaultIdentifier = $this->config->getValue(self::SETTING_NAME);
                return $this->getSessionAdapterFromIdentifier($defaultIdentifier);
            default:
                throw new InvalidArgumentException(
                    "Identifier {$identifier} not recognized."
                );
        }
    }
}
