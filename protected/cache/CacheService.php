<?php
namespace app\cache;

class CacheService
{
    // Path to cache directory
    private $cacheDir = '/home/arcooda/public_html/protected/cache/mysqlcache';

    /**
     * Get data from cache by table name and ID.
     */
    public function getData($table, $id)
    {
        $cacheFile = $this->getCacheFilePath($table,$id);

        // Check if cache file exists
        if (file_exists($cacheFile)) {
            $data = json_decode(file_get_contents($cacheFile), true);

            // Check if the specific record exists in cache
            if (isset($data[$id])) {
                return $data[$id];
            }
        }

        return false;
    }

    /**
     * Update or add data in cache for a specific table and ID.
     */
    public function updateData($table, $id, $newData)
    {
        $cacheFile = $this->getCacheFilePath($table,$id);
        // Load existing cache data or initialize as empty array
        $data = file_exists($cacheFile) ? json_decode(file_get_contents($cacheFile), true) : [];

        // Update the specific record in the array
        $data[$id] = $newData;

        // Save the updated data back to the cache file
        file_put_contents($cacheFile, json_encode($data));
    }

    /**
     * Delete data from cache for a specific table and ID.
     */
    public function deleteData($table, $id)
    {
        $cacheFile = $this->getCacheFilePath($table,$id);
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
    }

    /**
     * Get the full path to the cache file for a table.
     */
    private function getCacheFilePath($table,$id)
    {
        return "{$this->cacheDir}/{$table}_{$id}_cache.json";
    }

    /**
     * Simulate a database fetch operation (replace with real database query).
     */
    private function fetchFromDatabase($table, $id)
    {
        // This is a placeholder. Replace it with actual database fetch logic.
        return [
            'id' => $id,
            'name' => "Sample Name for {$table} {$id}",
            'email' => "sample{$id}@example.com"
        ];
    }
}

?>
