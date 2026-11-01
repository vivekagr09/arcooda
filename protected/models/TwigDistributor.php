<?php
namespace app\models;

use app\models\TwigImage;
use app\models\TwigProduct;
/*
block-brands.twig:
.image
.name
.url (.website)
*/

class TwigDistributor
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
    private $distributor = null;

    /**
     * @param string $field
     *
     * @return mixed
     */
    public function __get($field)
    {
        if (is_null($this->distributor)) {
            moduleLoad('distributor');

            if (function_exists('distributorFromDatabase')) {
                $this->distributor = distributorFromDatabase($this->id);
            }
        }

        return $this->distributor[$field] ?? null;
    }

    /**
     * @return string
     */
    public function __tostring()
    {
        return '[Distributor id:' . $this->id . ']';
    }

    /**
     * @param int[] $list
     *
     * @return TwigDistributor[]
     */
    public static function list($list) {
        foreach ($list as $k => $v) {
            $list[$k] = new TwigDistributor($v);
        }
        return $list;
    }

    /**
     * @param mixed[] $criteria
     *
     * @return TwigDistributor[]
     */
    public static function search($criteria = [])
    {
        global $_GLOBALS;

        if (array_key_exists('distributor', $_GLOBALS['criteria_options'])) {
            $criteria = array_merge(
                $_GLOBALS['criteria_options']['distributor'],
                $criteria
            );
        }
        moduleLoad('distributor');

        if (function_exists('distributorSearch')) {
            return self::list(distributorSearch($criteria));
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
                '/distributed-by/%s-%d.jpg',
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
        return $this->__get('company');
    }

    public function mapLink()
    {
        return $this->__get('map_link');
    }

    /**
     * @return string|null
     */
    public function url()
    {
        return $this->__get('web_page_url');
    }
    public function machine_product()
    {
        return $this->__get('machine_product');
    }
    public function game_product()
    {
        return $this->__get('game_product');
    }
    public function parts_product()
    {
        return $this->__get('parts_product');
    }
    public function merchandises_product()
    {
        return $this->__get('merchandises_product');
    }
    public function full_address()
    {
        return $this->__get('full_address');
    }
    public function country()
    {
        return $this->__get('country');
    }
     public function formated_address()
    {
        return $this->__get('formated_address');
    }
     public function category()
    {
        $category =  $this->__get('category');
        $category = json_decode($category);
        $result = [];
        $categoryArray = ['2'=>'Arcade Machines','3'=>'Game Software','1'=>'Arcade Parts','4'=>'Merchandises'];
        foreach ($category as $key => $cat) {
            $result[] = $categoryArray[$cat];
        }
        return  array_filter($result, 'boolval');
    }
    /**
     * @return string|null
     */
    public function image_file()
    {
        moduleLoad('distributor');

        // @todo $test = distributorGetOptionList('image_path') . '/' . $this->__get('item_image');
        $test = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/distributor/item_image/' . $this->__get('item_image');
        if (is_file($test)) {
            return $test;
        }

        return null;
    }
    public function map_image_file()
    {
        moduleLoad('distributor');

        // @todo $test = distributorGetOptionList('image_path') . '/' . $this->__get('item_image');
        $test = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/distributor/item_image/' . $this->__get('map_image');
        if (is_file($test)) {
            return $test;
        }

        return null;
    }
    public function getProduct()
    {
        $category =  $this->__get('category');
        $category = json_decode($category);
        $product = [];
        if($this->__get('machine_product') > 0 && in_array(2,$category)){
            $product[] = new TwigProduct($this->__get('machine_product'));
        }
        if($this->__get('game_product') > 0 && in_array(3,$category)){
            $product[] = new TwigProduct($this->__get('game_product'));
        }
        if($this->__get('parts_product') > 0 && in_array(1,$category)){
            $product[] = new TwigProduct($this->__get('parts_product'));
        }
        if($this->__get('merchandises_product') > 0 && in_array(4,$category)){
            $product[] = new TwigProduct($this->__get('merchandises_product'));
        }
        return $product;
    }
}
