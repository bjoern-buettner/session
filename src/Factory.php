<?php

declare(strict_types=1);

namespace Me\BjoernBuettner\Session;

class Factory
{
    public function __construct(
        private readonly array $environment
    ) {
    }

    /**
     * @deprecated Use constructor and init() instead
     */
    public static function start(bool $isWriteable = false): void
    {
        (new self($_ENV))->init($isWriteable);
    }
    public function init(bool $isWriteable = false): void
    {
        $duration = (int) $this->get('SESSION_DURATION', '7200');
        $hasMemcached = extension_loaded('memcached') && $this->get('ENABLE_MEMCACHED', 'false') === 'true';
        $path = $this->get('SESSION_PATH', sys_get_temp_dir());
        session_set_save_handler(
            $hasMemcached ? new Memcache($duration) : new File($duration, $path),
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
    private function get(string $key, string $default): string
    {
        if (!isset($this->environment[$key])) {
            return $default;
        }
        return $this->environment[$key] ?: $default;
    }
}
