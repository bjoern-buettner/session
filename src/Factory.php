<?php

declare(strict_types=1);

namespace Me\BjoernBuettner\Session;

class Factory
{
    public static function start(bool $isWriteable = false): void
    {
        $duration = (int) ($_ENV['SESSION_DURATION'] ?? 7200);
        $hasMemcached = extension_loaded('memcached') && ($_ENV['ENABLE_MEMCACHED'] ?? 'false') === 'true';
        session_set_save_handler(
            $hasMemcached ? new Memcache($duration) : new File($duration, $_ENV['SESSION_PATH'] ?? sys_get_temp_dir()),
            true
        );
        session_set_cookie_params([
            'lifetime' => $duration,
            'path' => '/',
            'domain' => $_SERVER['HTTP_HOST'],
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        session_start([
            'read_and_close' => !$isWriteable,
        ]);
    }
}
