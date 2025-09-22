<?php
namespace App\Cache;

/**
 * Simple file-based caching system.
 *
 */
class Cache {
    private string $cacheDir;
    private int $ttl; // Time-to-live in seconds

    /**
     * Constructor
     *
     * @param string $cacheDir Directory where cache files are stored
     * @param int    $ttl      Time-to-live for cached entries in seconds
     */
    public function __construct(string $cacheDir = __DIR__ . '/../cache', int $ttl = 10) {
        $this->cacheDir = $cacheDir;
        $this->ttl = $ttl;

        // Ensure the cache directory exists
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    /**
     * Generate the full file path for a given cache key
     *
     * @param string $key
     * @return string
     */
    private function getFile(string $key): string {
        return $this->cacheDir . '/' . md5($key) . '.cache';
    }

    /**
     * Store data in the cache
     *
     * @param string $key
     * @param mixed  $data
     */
    public function set(string $key, mixed $data): void {
        $payload = [
            'expires' => time() + $this->ttl,
            'data'    => $data
        ];

        file_put_contents($this->getFile($key), serialize($payload));
    }

    /**
     * Retrieve data from cache
     *
     * @param string $key
     * @return mixed|null Returns the cached data or null if expired/not found
     */
    public function get(string $key): mixed {
        $file = $this->getFile($key);

        if (!file_exists($file)) {
            return null;
        }

        $payload = @unserialize(file_get_contents($file));

        // Remove expired cache file
        if (!$payload || !isset($payload['expires']) || $payload['expires'] < time()) {
            @unlink($file);
            return null;
        }

        return $payload['data'];
    }

    /**
     * Delete all expired cache files
     *
     */
    public function deleteExpiredCachedFiles(): void {
        foreach (glob($this->cacheDir . '/*.cache') as $file) {
            $payload = @unserialize(file_get_contents($file));
            if ($payload && isset($payload['expires']) && $payload['expires'] < time()) {
                @unlink($file);
            }
        }
    }
}