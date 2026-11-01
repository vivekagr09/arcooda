<?php

namespace Studio\Base;

class Language
{
    public $language;
    public $basedir;
    public $file;
    public $dir;
    public $google;
    public $locale;
    public $shortLocale;
    public $name;
    public $countriesLocale;
    public $translations = array();
    public $loadedFiles = array();

    private $languages = [];
    private $shortLanguages = [];

    public function __construct($basedir, $name = '') {
        $this->basedir = $basedir;
        if ($name == '') {
            $this->load();
            $this->read();
            $this->verify();
        }
        else {
            $this->file = $this->basedir . "/" . $name . "/";
            if (!file_exists($this->file)) {
                die("Load translation file failed: {$this->language}");
            }

            $this->read();
        }
    }

    protected function load() {
        $this->loadLanguages();
        $this->language = $this->getLanguage();
        $this->file = $this->basedir . "/" . $this->language . "/";

        if (!file_exists($this->file)) {
            if (!isset($_SESSION['language'])) die("Load default translation file failed: {$this->language}");
            unset($_SESSION['language']);
            $this->load();
        }
    }

    public function read() {
        foreach (scandir($this->file) as $file) {
            if (substr($file, 0, 1) == ".") continue;
            if (strtolower(substr($file, -5)) !== ".json") continue;
            if (!is_file($this->file . $file)) continue;

            $data = str_replace("\r\n", "\n", file_get_contents($this->file . $file));
            $data = trim(str_replace("\n", "", $data));
            $data = preg_replace('/\s+/', ' ', $data);

            $items = json_decode($data, true);

            if (!is_array($items)) {
                die("Invalid language file: {$this->file}{$file}");
            }

            foreach ($items as $in => $out) {
                if (!isset($this->translations[$in]))
                    $this->translations[$in] = $out;
            }

            $this->loadedFiles[] = $file;
        }
    }

    public function verify() {
        global $studio;

        $row = array_key_exists($this->language, $this->languages) ? $this->languages[$this->language] : null;

        if (!$row) {
            $q = $studio->sql->query("SELECT * FROM languages WHERE locale = '{$this->language}'");

            if ($q->num_rows == 0) {
                $this->translations = array();
                if (isset($_SESSION['language'])) {
                    unset($_SESSION['language']);
                    $this->load();
                    $this->read();
                    return $this->verify();
                }
                else {
                    $studio->showFatalError("Language system corrupted: please ensure the default language is properly configured. (Try running diagnostics)");
                }
            }

            if (!($row = $q->fetch_array())) {
                $studio->showFatalError("Language with locale '{$this->language}' not found in database!");
            }
        }

        $this->dir = $row['dir'];
        $this->google = $row['google'];
        $this->locale = $this->shortLocale = $row['locale'];
        $this->name = $row['name'];
        $this->countriesLocale = $row['countries'];

        if (strpos($this->locale, '-') !== false) {
            $parts = explode('-', $this->locale);
            $this->shortLocale = $parts[0];
        }
    }

    public function loadLanguages() {
        global $studio;
        // $studio = new Studio();
        $q = $studio->sql->query("SELECT * FROM languages ORDER BY id ASC");

        $this->languages = [];
        $this->shortLanguages = [];

        while ($row = $q->fetch_array()) {
            $languages[strtolower($row['locale'])] = $row;

            if (strpos($row['locale'], '-') !== false) {
                $parts = explode('-', $row['locale']);
                $short = strtolower($parts[0]);

                if (!array_key_exists($short, $this->shortLanguages)) {
                    $this->shortLanguages[$short] = $row;
                }
            }
        }
    }

    public function getLanguage() {
        global $studio;

        if (isset($_GET['setlang'])) {
            $setlang = $_GET['setlang'];

            if (array_key_exists($setlang, $this->languages)) {
                $_SESSION['language'] = $setlang;
            }
            else {
                $p = $studio->sql->prepare("SELECT * FROM languages WHERE locale = ?");
                $p->bind_param("s", $setlang);
                $p->execute();
                $p->store_result();

                if ($p->num_rows == 1) {
                    $_SESSION['language'] = $setlang;
                }
            }

            $currentLink = $_SERVER['REQUEST_URI'];
            $arguments = $studio->permalinks->getQueryArguments();

            if (($index = strpos($currentLink, '?')) !== false) {
                $currentLink = substr($currentLink, 0, $index);
            }

            if (isset($arguments['setlang'])) {
                unset($arguments['setlang']);
            }

            $newLink = $currentLink;

            if (count($arguments) > 0) {
                $newLink .= '?' . http_build_query($arguments);
            }

            header("Location: $newLink");
            die;
        }

        if (isset($_SESSION['language'])) {
            return $_SESSION['language'];
        }

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $header = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $values = explode(',', $header);
            $extracted = [];

            // Parse language values
            foreach ($values as $value) {
                $value = trim($value);

                if (preg_match('/^([a-z]{2})(?:-([a-zA-Z]+))?(?:;q=([0-9.]+))?$/', $value, $matches)) {
                    $shortLocale = $matches[1];
                    $extraLocale = isset($matches[2]) ? $matches[2] : '';
                    $qFactor = isset($matches[3]) ? $matches[3] : '';

                    $extracted[] = [
                        'locale' => !empty($extraLocale) ? ($shortLocale . '-' . $extraLocale) : '',
                        'shortLocale' => $shortLocale,
                        'weight' => is_numeric($qFactor) ? floatval($qFactor) : 1
                    ];
                }
            }

            // Sort by weight
            static::sort($extracted, function($a, $b) {
                if($a['weight'] === $b['weight']) return 0;
                return ($a['weight'] > $b['weight']) ? -1 : 1;
            });

            // Match the exact locale first
            foreach ($extracted as $language) {
                $locale = $language['locale'];
                if (array_key_exists($locale, $this->languages)) {
                    return $this->languages[$locale]['locale'];
                }
            }

            // Match the short locale next
            foreach ($extracted as $language) {
                $locale = $language['shortLocale'];

                if (array_key_exists($locale, $this->shortLanguages)) {
                    return $this->shortLanguages[$locale]['locale'];
                }
            }
        }

        return $studio->getopt("default-language");
    }

    public function getCountries() {
        global $studio;

        $path = $studio->bindir . '/countries/' . $this->countriesLocale . '.json';
        $contents = json_decode(file_get_contents($path), true);

        return $contents['countries'];
    }

    public function translate($phrase) {
        if (isset($this->translations[$phrase])) return $this->translations[$phrase];
        return $phrase;
    }

    private static function sort(&$array, $cmp_function) {
        if(count($array) < 2) {
            return;
        }
        $halfway = count($array) / 2;
        $array1 = array_slice($array, 0, $halfway, TRUE);
        $array2 = array_slice($array, $halfway, NULL, TRUE);

        static::sort($array1, $cmp_function);
        static::sort($array2, $cmp_function);

        if(call_user_func($cmp_function, end($array1), reset($array2)) < 1) {
            $array = $array1 + $array2;
            return;
        }
        $array = array();
        reset($array1);
        reset($array2);
        while(current($array1) && current($array2)) {
            if(call_user_func($cmp_function, current($array1), current($array2)) < 1) {
                $array[key($array1)] = current($array1);
                next($array1);
            } else {
                $array[key($array2)] = current($array2);
                next($array2);
            }
        }
        while(current($array1)) {
            $array[key($array1)] = current($array1);
            next($array1);
        }
        while(current($array2)) {
            $array[key($array2)] = current($array2);
            next($array2);
        }
        return;
    }
}

?>
