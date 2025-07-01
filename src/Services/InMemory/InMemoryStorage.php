<?php

namespace App\Services\InMemory;

class InMemoryStorage
{
    private static array $storage = [];
    private static array $expirations = [];

    /**
     * Set a value in memory with optional timeout (seconds).
     */
    public static function set(string $key, mixed $value, ?int $ttlSeconds = null): void
    {
        self::$storage[$key] = $value;
        if ($ttlSeconds !== null) {
            self::$expirations[$key] = time() + $ttlSeconds;
        } else {
            unset(self::$expirations[$key]);
        }
    }

    /**
     * Get a value from memory. Returns $default if not found or expired.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (!array_key_exists($key, self::$storage)) {
            return null;
        }
        if (isset(self::$expirations[$key]) && time() > self::$expirations[$key]) {
            self::delete($key);
            return null;
        }
        return self::$storage[$key];
    }

    /**
     * Check if a key exists in memory and is not expired.
     */
    public static function has(string $key): bool
    {
        if (!array_key_exists($key, self::$storage)) {
            return false;
        }
        if (isset(self::$expirations[$key]) && time() > self::$expirations[$key]) {
            self::delete($key);
            return false;
        }
        return true;
    }

    /**
     * Remove a value from memory.
     */
    public static function delete(string $key): void
    {
        unset(self::$storage[$key], self::$expirations[$key]);
    }

    /**
     * Clear all in-memory data.
     */
    public static function clear(): void
    {
        self::$storage = [];
        self::$expirations = [];
    }
}