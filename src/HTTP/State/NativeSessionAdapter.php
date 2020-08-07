<?php

declare(strict_types=1);

namespace Fugue\HTTP\State;

use function session_write_close;
use function array_key_exists;
use function session_start;

final class NativeSessionAdapter implements SessionAdapterInterface
{
    private SessionSettings $settings;
    private bool $started;

    public function __construct(SessionSettings $settings)
    {
        $this->settings = $settings;
        $this->started  = false;
    }

    public function start(): void
    {
        if ($this->started) {
            return;
        }

        $settings = [
            'use_only_cookies' => (int)$this->settings->shouldUseOnlyCookies(),
            'use_cookies'      => (int)$this->settings->shouldUseCookies(),
            'cookie_httponly'  => (int)$this->settings->getHttpOnly(),
            'cookie_secure'    => (int)$this->settings->isSecure(),
            'cache_expire'     => $this->settings->getCacheExpire(),
            'use_trans_sid'    => 0,
        ];

        if ($this->settings->getTimeout() > 0) {
            $settings['gc_maxlifetime'] = $this->settings->getTimeout();
        }

        if ($this->settings->getName() !== '') {
            $settings['name'] = $this->settings->getName();
        }

        $this->started = (bool)session_start($settings);
    }

    public function get(string $name)
    {
        return $_SESSION[$name] ?? null;
    }

    public function set(string $name, $value): void
    {
        $_SESSION[$name] = $value;
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $_SESSION);
    }

    public function unset(string $name): void
    {
        unset($_SESSION[$name]);
    }

    public function clear(): void
    {
        $_SESSION = [];
    }

    public function close(): void
    {
        if ($this->started) {
            session_write_close();
        }
    }
}
