<?php

declare(strict_types=1);

namespace Fugue\HTTP\State;

use function array_key_exists;
use function session_start;

final class NativeSessionAdapter implements SessionAdapterInterface
{
    public function start(array $settings): void
    {
        $sessionSessions = [
            'cache_expire'     => 240,
            'use_only_cookies' => 1,
            'cookie_httponly'  => 1,
            'use_cookies'      => 1,
            'use_trans_sid'    => 0,
        ];

        if (isset($settings['secure']) && $settings['secure']) {
            $sessionSessions['cookie_secure'] = 1;
        }

        if (isset($settings['timeout']) && (int)$settings['timeout'] > 0) {
            $sessionSessions['gc_maxlifetime'] = (int)$settings['timeout'];
        }

        if (isset($settings['name'])) {
            $sessionSessions['name'] = (string)$settings['name'];
        }

        session_start($sessionSessions);
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
}
