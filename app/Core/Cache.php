<?php
namespace Rapulo\Core;

class Cache {
    private static $storageDir;
    private static $cache = [];

    public static function init($dir) {
        if (!is_writable($dir)) {
            throw new \Exception("Cache directory '$dir' is not writable");
        }
        self::$storageDir = $dir;
    }

    public static function get($key, $default = null) {
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }

        $file = self::$storageDir . '/' . md5($key) . '.cache';
        if (file_exists($file)) {
            $data = @unserialize(file_get_contents($file));
            if ($data === false || !isset($data['expires']) || $data['expires'] < time()) {
                @unlink($file);
                return $default;
            }
            self::$cache[$key] = $data['value'];
            return $data['value'];
        }

        return $default;
    }

    public static function set($key, $value, $ttl = 3600) {
        self::$cache[$key] = $value;
        $file = self::$storageDir . '/' . md5($key) . '.cache';
        $data = ['value' => $value, 'expires' => time() + $ttl];
        if (!@file_put_contents($file, serialize($data))) {
            throw new \Exception("Failed to write cache to '$file'");
        }
    }
}