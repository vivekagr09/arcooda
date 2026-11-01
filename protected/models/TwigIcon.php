<?php
namespace app\models;

use app\models\TwigImage;
use app\cache\CacheService;

/*
block-brands.twig:
.image
.url
.name
 */

/*
Example: { // Example
"id" : 232
"img" : /img_manufacturers/12341234.jpg
"name" : "Sega"
"url" : /products/machines/crane_machines/24_inch_Toy_Story_Single_Claw_Catcher_9254.htm
"show_front" : yes
}
 */

class TwigIcon
{
    public $id;

    public function __construct($id = 0)
    {
        $this->id = $id;
    }

    /**
     * Path to the image files, including trailing slash
     *
     * @return string
     */
    private static function image_path()
    {
        return $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/icon/image/';
    }

    private $_icon = null;
    public function __get($field)
    {
        global $DB;
        if (is_null($this->_icon)) {
            $cacheService = new CacheService();
            $this->_icon = $cacheService->getData('icons', $this->id);
             if($this->_icon == false){  
                $this->_icon = $DB->query_first(sprintf(
                    "SELECT * FROM `icons` WHERE `id` = %d LIMIT 1",
                    $this->id
                ), MYSQLI_ASSOC);
                $cacheService->updateData('icons', $this->id,$this->_icon);
            }
        }

        //TODO: Remove this when admin is saving properly
        $this->_icon['img'] = basename($this->_icon['img']);

        $this->_icon['image_url'] = sprintf(
            '/icon/%s-%d.jpg',
            urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $this->_icon['name']), '-')),
            $this->id
        );

        if (!is_file(self::image_path() . $this->_icon['img'])) {
            $this->_icon['img']       = '';
            $this->_icon['image_url'] = '';
        }

        return $this->_icon[$field] ?? null;
    }

    public function __tostring()
    {
        return '[Icon id:' . $this->id . ']';
    }

    public static function list($list) {
        foreach ($list as $k => $v) {
            $list[$k] = new TwigIcon($v);
        }
        return $list;
    }
    public static function search($criteria = [])
    {
        if (array_key_exists('id', $criteria)) {
            if (is_array($criteria['id'])) {
                return self::list($criteria['id']);
            }
            return self::list([$criteria['id']]);
        }
        /*
        global $_GLOBALS;
        if (array_key_exists('icon', $_GLOBALS['criteria_options'])) {
        $criteria = array_merge(
        $_GLOBALS['criteria_options']['icon'],
        $criteria
        );
        }
        moduleLoad('icon');
        return self::list(iconSearch($criteria));
         */
        die("Not Implemented");
    }

    public static function getAllToShow()
    {
        global $DB;
        $query  = $DB->query("SELECT * FROM `icons` WHERE `show_front` = 'y'");
        $result = [];
        while ($row = $DB->fetch_assoc($query)) {
            $result[] = $row['id'];
        }

        return self::list($result);
    }

    public function title()
    {
        return trim($this->name());
    }

    public function description()
    {
        $desc = $this->text;
        if (empty($desc)) {
            $desc = $this->name;
        }
        return trim($desc);
    }

    public function descriptionval()
    {
        $desc = $this->description;
        if (empty($desc)) {
            $desc = '';
        }

        return trim($desc);
    }

    public function keywords()
    {
        $keys = ucwords(implode(', ', preg_split('/[,\-\s]+/', $this->text)));
        return $keys;
    }

    public function breadcrumbs()
    {
        return [
            ['title' => 'Home', 'url' => '/'],
            ['title' => $this->name(), 'url' => $this->url()],
        ];
    }

    public function image()
    {
        $test = $this->__get('image_url') ?? '';
        if ('' !== $test) {
            return new TwigImage([
                'src'      => $test,
                'alt'      => $this->name(),
                'src_file' => self::image_path() . $this->icon['img'],
            ]);
        }

        return null;
    }

    public function name()
    {
        return $this->name;
    }

    public function text()
    {
        return $this->text;
    }

    public function url()
    {
        return sprintf(
            '/icon/%s-%d',
            urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $this->name), '-')),
            $this->id
        );
    }
}
