<?php
namespace app\models;

use app\models\TwigImage;
use app\cache\CacheService;

class TwigSlider
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
            $this->_icon = $cacheService->getData('front_slider', $this->id);
            if($this->_icon == false){  
                $this->_icon = $DB->query_first(sprintf(
                    "SELECT * FROM `front_slider` WHERE `id` = %d LIMIT 1",
                    $this->id
                ), MYSQLI_ASSOC);
                $cacheService->updateData('front_slider', $this->id,$this->_icon);
            }
        }
        return $this->_icon[$field] ?? null;
    }

    public function __tostring()
    {
        return '[Icon id:' . $this->id . ']';
    }

    public static function list($list) {
        foreach ($list as $k => $v) {
            $list[$k] = new TwigSlider($v);
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
        die("Not Implemented");
    }
    public function page_height(){
        return $this->page_height;
    }
    public function getAllImages()
    {
        global $DB;
        $query  = $DB->query("SELECT * FROM `front_slider_images` WHERE active='y' AND  `slider_id` =".$this->id. " ORDER BY `rank`");
        $result = [];
        while ($row = $DB->fetch_assoc($query)) {

            $result[] =[
                        'src'      => sprintf('/sliderimage/%s-%d.jpg', urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $row['title']), '-')), $row['id'] ),
                        'alt'      => $row['title'],
                        'src_file' => self::image_path() . $row['name'],
                        'caption'=>$row['caption']
                    ];
        }

        return $result;
    }
    public static function getDefaultSlider()
    {
        global $DB;
        $result  = $DB->query_first("SELECT id FROM `front_slider` WHERE `status` = 'y' AND `default`='y' limit 1");
        return new TwigSlider($result['id']);;
    }
    public static function getRandomSlider()
    {
        global $DB;
        $result  = $DB->query_first("SELECT id FROM `front_slider`  WHERE `status` = 'y' ORDER BY RAND() LIMIT 1");
        return new TwigSlider($result['id']);
    }
    public function title()
    {
        return trim($this->name());
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
            return [
                'src'      => $test,
                'alt'      => $this->name(),
                'src_file' => self::image_path() . $this->img,
            ];
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
        global $_GLOBALS;
        return sprintf(
            '/icon/%s-%d',
            urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $this->name), '-')),
            $this->id
        );
    }
}
