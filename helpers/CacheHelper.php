<?php

namespace helpers;
use Predis\Client;

class CacheHelper
{
    /** @var string */
    private string $cacheDir = __DIR__ . '/../storage/cache/';

    /** @var string */
    private string $cacheFile = '';

    /**
     * CacheHelper constructor.
     *
     * @param string $fileName The file name (or key) to use for caching.
     */
    public function __construct(string $fileName)
    {
        // Use a custom extension (.cache) to indicate this is a cache file
        $this->cacheFile = $this->cacheDir . $fileName . '.cache';

        // Create the cache directory if it doesn't exist
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    /**
     * Write data to the cache file as a serialized array.
     *
     * @param mixed $data The data to cache.
     */
    public function writeCache($data): void
    {
        // Serialize the data to store it as a string
        $serializedData = serialize($data);

        // Write the serialized data to the cache file (file is created automatically if it doesn't exist)
        file_put_contents($this->cacheFile, $serializedData);
    }

    /**
     * Read data from the cache file.
     *
     * @return mixed|null Returns the unserialized data, or null if the cache file does not exist.
     */
    public function readCache()
    {
        if (file_exists($this->cacheFile)) {
            $cachedData = file_get_contents($this->cacheFile);
            return unserialize($cachedData);
        }

        return null;
    }

    /**
     * Clear the cache by deleting the cache file.
     */
    public function clearCache(): void
    {
        if (file_exists($this->cacheFile)) {
            unlink($this->cacheFile);
        }
    }

    /**
     * Delete all cache files in the cache directory
     */
    public function clearAllCaches(): void
    {
        // Scan the cache directory for files
        $files = glob($this->cacheDir . '*.cache');

        // Iterate through all the cache files and delete them
        foreach ($files as $file) {
            unlink($file);
        }
    }
}
