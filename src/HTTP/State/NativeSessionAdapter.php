<?php

declare(strict_types=1);

namespace Fugue\HTTP\State;

use function session_write_close;
use function array_key_exists;
use function session_start;

final class NativeSessionAdapter implements SessionAdapterInterface
{
    /** @var bool */
    private $started = false;

    public function start(array $settings): void
    {
        if ($this->started) {
            return;
        }

        $sessionSessions = [
            'cache_expire'     => 240,
            'use_only_cookies' => 1,
            'cookie_httponly'  => 1,
            'use_cookies'      => 1,
            'use_trans_sid'    => 0,
        ];

        if (isset($settings['secure']) && (bool)$settings['secure']) {
            $sessionSessions['cookie_secure'] = 1;
        }

        if (isset($settings['timeout']) && (int)$settings['timeout'] > 0) {
            $sessionSessions['gc_maxlifetime'] = (int)$settings['timeout'];
        }

        if (isset($settings['name']) && (string)$settings['name'] !== '') {
            $sessionSessions['name'] = (string)$settings['name'];
        }

        $this->started = (bool)session_start($sessionSessions);;
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
