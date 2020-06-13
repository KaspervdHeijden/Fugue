<?php

declare(strict_types=1);

namespace Fugue\HTTP\State;

use function session_write_close;
use function array_key_exists;
use function session_start;

final class NativeSessionAdapter implements SessionAdapterInterface
{
    private bool $started = false;

    public function start(SessionSettings $settings): void
    {
        if ($this->started) {
            return;
        }

        $sessionSessions = [
            'use_only_cookies' => (int)$settings->shouldUseOnlyCookies(),
            'use_cookies'      => (int)$settings->shouldUseCookies(),
            'cookie_httponly'  => (int)$settings->getHttpOnly(),
            'cookie_secure'    => (int)$settings->isSecure(),
            'cache_expire'     => $settings->getCacheExpire(),
            'use_trans_sid'    => 0,
        ];

        if ($settings->getTimeout() > 0) {
            $sessionSessions['gc_maxlifetime'] = $settings->getTimeout();
        }

        if ($settings->getName() !== '') {
            $sessionSessions['name'] = $settings->getName();
        }

        $this->started = (bool)session_start($sessionSessions);
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
