<?php
/**
 * Copyright (c) 2003 Brian E. Lozier (brian@massassi.net)
 *
 * set_vars() method contributed by Ricardo Garcia (Thanks!)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */

class Template
{
    public $vars; /// Holds all the template variables
    public $path; /// Path to the templates
    public $method;

    /**
     * Constructor
     *
     * @param string $path the path to the templates
     *
     * @return void
     */
    public function __construct($path = null, $method = null)
    {
        $this->path   = $path;
        $this->vars   = array();
        $this->method = $method;
    }

    /**
     * Set the path to the template files.
     *
     * @param string $path path to template files
     *
     * @return void
     */
    public function set_path($path)
    {
        $this->path = $path;
    }

    /**
     * Set a template variable.
     *
     * @param string $name name of the variable to set
     * @param mixed $value the value of the variable
     *
     * @return void
     */
    public function set($name, $value)
    {
        $this->vars[$name] = $value;
    }

    /**
     * Set a bunch of variables at once using an associative array.
     *
     * @param array $vars array of vars to set
     * @param bool $clear whether to completely overwrite the existing vars
     *
     * @return void
     */
    public function set_vars($vars, $clear = false)
    {
        if ($clear) {
            $this->vars = $vars;
        } elseif (is_array($vars)) {
            $this->vars = array_merge($this->vars, $vars);
        }
    }

    /**
     * Open, parse, and return the template file.
     *
     * @param string string the template file name
     *
     * @return string
     */
    public function fetch($file)
    {
        global $DB;

        if (!is_null($this->path)) {
            $file = $this->path . $file;
        }

        extract($this->vars); // Extract the vars to local namespace
        ob_start(); // Start output buffering
        include $file; // Include the file
        $contents = ob_get_contents(); // Get the contents of the buffer
        ob_end_clean(); // End buffering and discard
        return $contents; // Return the contents
    }

    public $contents = '';
    public $output = '';
    public function set_method($method)
    {
        $this->method = $method;
    }
    public function set_contents($contents)
    {
        $this->contents = $contents;
    }
    public function apply($method = null)
    {
        if ($method === null) {
            $method = $this->method;
        }

        $output = '';
        switch ($method) {
            default:
            case 'str_replace':
                $output = str_replace(array_keys($this->vars), array_values($this->vars), $this->contents);
                break;
            case 'str_ireplace':
                $output = str_ireplace(array_keys($this->vars), array_values($this->vars), $this->contents);
                break;
            case 'preg_replace':
                $output = preg_replace(array_keys($this->vars), array_values($this->vars), $this->contents);
                break;
        }
        $this->output = $output;
        return $output;
    }
    public function fetch_cache($file = null)
    {
        return $this->output;
    }
}

/**
 * An extension to Template that provides automatic caching of
 * template contents.
 */
class CachedTemplate extends Template
{
    public $cache_id;
    public $expire;
    public $cached;

    /**
     * Constructor.
     *
     * @param string $path path to template files
     * @param string $cache_id unique cache identifier
     * @param int $expire number of seconds the cache will live
     *
     * @return void
     */
    public function __construct($path, $cache_id = null, $expire = 900)
    {
        parent::__construct($path);
        $this->cache_id = $cache_id ? sys_get_temp_dir() . '/' . md5($cache_id) : $cache_id;
        $this->expire   = $expire;
    }

    /**
     * Test to see whether the currently loaded cache_id has a valid
     * corrosponding cache file.
     *
     * @return bool
     */
    public function is_cached()
    {
        if ($this->cached) {
            return true;
        }

        // Passed a cache_id?
        if (!$this->cache_id) {
            return false;
        }

        // Cache file exists?
        if (!file_exists($this->cache_id)) {
            return false;
        }

        // Can get the time of the file?
        if (!($mtime = filemtime($this->cache_id))) {
            return false;
        }

        // Cache expired?
        if (($mtime + $this->expire) < time()) {
            @unlink($this->cache_id);
            return false;
        } else {
            /**
             * Cache the results of this is_cached() call.  Why?  So
             * we don't have to double the overhead for each template.
             * If we didn't cache, it would be hitting the file system
             * twice as much (file_exists() & filemtime() [twice each]).
             */
            $this->cached = true;
            return true;
        }
    }

    /**
     * This function returns a cached copy of a template (if it exists),
     * otherwise, it parses it as normal and caches the content.
     *
     * @param $file string the template file
     *
     * @return string
     */
    public function fetch_cache($file = null)
    {
        if (is_null($file)) {
            return $this->output;
        }

        if ($this->is_cached()) {
            $fp       = @fopen($this->cache_id, 'r');
            $contents = fread($fp, filesize($this->cache_id));
            fclose($fp);
            return $contents;
        } else {
            $contents = $this->fetch($file);

            // Write the cache
            if ($fp = @fopen($this->cache_id, 'w')) {
                fwrite($fp, $contents);
                fclose($fp);
            } else {
                die('Unable to write cache.');
            }

            return $contents;
        }
    }
}
