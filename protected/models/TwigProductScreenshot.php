<?php
namespace app\models;

//moduleLoad('product');
use app\models\TwigImage;
use app\models\TwigProduct;
use app\cache\CacheService;

class TwigProductScreenshot
{
    /** @var int */
    public $id;

    /**
     * @param int $id
     */
    public function __construct($id = 0)
    {
        $this->id = $id;
    }

    /** @var mixed[]|null */
    private $screenshot;

    /**
     * @param string $field
     *
     * @return mixed
     */
    public function __get($field)
    {
        if (!is_null($this->screenshot)) {
            return $this->screenshot[$field] ?? null;
        }

        $cacheService = new CacheService();
        $this->screenshot = $cacheService->getData('product_screenshots', $this->id);
        if($this->screenshot == false){  
            $this->screenshot = productScreenshotFromDatabase($this->id);
            $cacheService->updateData('product_screenshots', $this->id,$this->screenshot);
        }

        if (is_null($this->screenshot)) {
            return null;
        }

        return $this->screenshot[$field] ?? null;
    }

    /**
     * @return string
     */
    public function __tostring()
    {
        return (string)($this->image() ?? $this->__get('caption'));
    }

    /** @var TwigProduct? */
    private $product;

    /**
     * @return TwigProduct?
     */
    public function product()
    {
        if (!is_null($this->product)) {
            return $this->product;
        }

        if ($products = TwigProduct::search(['id' => $this->__get('id')])) {
            $this->product = reset($products);
        }

        return $this->product;
    }

    /**
     * @param int[] $list
     *
     * @return TwigProductScreenshot[]
     */
    public static function list($list)
    {
        $result = [];
        foreach ($list as $k => $v) {
            $result[$k] = new TwigProductScreenshot($v);
        }

        return $result;
    }

    /**
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return TwigProductScreenshot[]
     */
    public static function search($eq = [], $lt = [], $gt = [], $sort_by = null)
    {
        global $_GLOBALS;

        moduleLoad('product'); //productScreenshotQuery, productScreenshotSearch, productScreenshotSort

        if (array_key_exists('product', $_GLOBALS['criteria_options'])) {
            $eq = array_merge(
                $_GLOBALS['criteria_options']['product'],
                $eq
            );
        }

        $screenshot_list   = productScreenshotSearch($eq);
        $screenshot_sorted = productScreenshotSort($screenshot_list, [
        ][$sort_by] ?? 'ss_id');

        return static::list($screenshot_sorted);
    }

    /**
     * @return TwigImage|null
     */
    public function image()
    {
        moduleLoad('product');

        if (!is_null($test = $this->image_file())) {
            return new TwigImage([
                'src'      => sprintf(
                    '/ss/%s-%d.jpg',
                    static::createslug($this->__get('caption')),
                    $this->id
                ),
                'alt'      => $this->__get('caption'),
                'src_file' => $test,
            ]);
        }

        return null;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private static function createslug($name)
    {
        return trim(preg_replace('/[^A-Za-z0-9]+/', '-', $name), '-');
    }

    public function image_file()
    {
        moduleLoad('product');

        $test = $this->image_path() . $this->__get('filename');
        if (is_file($test)) {
            return $test;
        }

        return null;
    }

    /**
     * Path to the image files, including trailing slash
     *
     * @return string
     */
    public static function image_path()
    {
        return productGetOptionList('screenshot_path') . '/';
    }
}
