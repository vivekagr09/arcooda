<?php
namespace app\models;

use app\models\TwigImage;
use app\cache\CacheService;

/*
block-brands-carousel.twig:
.image
.name
.url (.website)
*/

class TwigBrand
{
    public $id;

    /**
     * @param int $id
     */
    public function __construct($id = 0)
    {
        $this->id = $id;
    }

    /** @var mixed[]|null */
    private $brand = null;

    /**
     * @param string $field
     *
     * @return mixed
     */
    public function __get($field)
    {
        if (is_null($this->brand)) {
            moduleLoad('brand');
            $cacheService = new CacheService();
            $this->brand = $cacheService->getData('brand', $this->id);
            if($this->product == false){    
                if (function_exists('brandFromDatabase')) {
                    $this->brand = brandFromDatabase($this->id);
                    $cacheService->updateData('brand', $this->id,$this->brand);
                }
            }
        }

        return $this->brand[$field] ?? null;
    }

    /**
     * @return string
     */
    public function __tostring()
    {
        return '[Brand id:' . $this->id . ']';
    }

    /**
     * @param int[] $list
     *
     * @return TwigBrand[]
     */
    public static function list($list) {
        foreach ($list as $k => $v) {
            $list[$k] = new TwigBrand($v);
        }
        return $list;
    }

    /**
     * @param mixed[] $criteria
     *
     * @return TwigBrand[]
     */
    public static function search($criteria = [])
    {
        global $_GLOBALS;

        if (array_key_exists('brand', $_GLOBALS['criteria_options'])) {
            $criteria = array_merge(
                $_GLOBALS['criteria_options']['brand'],
                $criteria
            );
        }
        moduleLoad('brand');

        if (function_exists('brandSearch')) {
            return self::list(brandSearch($criteria));
        }

        return array();
    }

    /**
     * @return string
     */
    public function alt()
    {
        if (!empty($name = $this->name())) {
            return 'Logo for ' . $name;
        }

        return '';
    }

    /**
     * @return TwigImage|null
     */
    public function image()
    {
        return is_file($this->image_file()) ? new TwigImage([
            'src' => sprintf(
                '/our-brand/%s-%d.jpg',
                urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $this->name()), '-')),
                $this->id
            ),
            'alt' => $this->alt(),
        ]) : null;
    }

    /**
     * @return string|null
     */
    public function name()
    {
        return $this->__get('name');
    }

    /**
     * @return string|null
     */
    public function url()
    {
        return $this->__get('website');
    }

    /**
     * @return string|null
     */
    public function image_file()
    {
        moduleLoad('brand');

        // @todo $test = brandGetOptionList('image_path') . '/' . $this->__get('img');
        $test = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/brand/img/' . $this->__get('img');
        if (is_file($test)) {
            return $test;
        }

        return null;
    }
}
