<?php

declare(strict_types=1);

namespace Me\BjoernBuettner\Session;

class File extends Base
{
    public function __construct(private int $duration, private string $root)
    {
        if (!is_dir($root)) {
            mkdir($root, 0700, true);
        }
    }

    public function close(): bool
    {
        return true;
    }

    public function destroy(string $id): bool
    {
        $file = $this->root . DIRECTORY_SEPARATOR. $this->getIPKey() . $id . '.session';
        if (!is_file($file)) {
            return true;
        }
        return unlink($file);
    }

    public function gc(int $max_lifetime): int|false
    {
        register_shutdown_function(function () {
            $files = glob($this->root . DIRECTORY_SEPARATOR . '*.session');
            foreach ($files as $file) {
                if (filemtime($file) + $this->duration < time() && is_file($file)) {
                    unlink($file);
                }
            }
        });
        return 0;
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function read(string $id): string|false
    {
        $file = $this->root . DIRECTORY_SEPARATOR. $this->getIPKey() . $id . '.session';
        if (is_file($file)) {
            return file_get_contents($file);
        }
        return false;
    }

    public function write(string $id, string $data): bool
    {
        $file = $this->root . DIRECTORY_SEPARATOR . $this->getIPKey() . $id . '.session';
        return strlen($data) === file_put_contents($file, $data);
    }

    public function updateTimestamp(string $id, string $data): bool
    {
        return touch($this->root . DIRECTORY_SEPARATOR . $this->getIPKey() . $id . '.session');
    }
}
