<?php
namespace app\models;

use app\models\TwigProduct;
use app\cache\CacheService;

function categorySubCount($id)
{
    global $_GLOBALS;

    moduleLoad('category'); //categoryChunk, categoryQuery
    if (!function_exists('categoryChunk') || !function_exists('categoryQuery')) {
        return 0;
    }

    moduleLoad('product');
    if (!function_exists('productGroupCount') || !function_exists('productQuery')) {
        return 0;
    }

    static $category_counts = null;
    if (is_null($category_counts)) {
        $category_counts = productGroupCount(productQuery($_GLOBALS['criteria_options']['product']), 'type');
    }

    static $category_tree = null;
    if (is_null($category_tree)) {
        $category_tree = categoryChunk(categoryQuery($_GLOBALS['criteria_options']['category']), 'parent');
    }

    $total = $category_counts[$id] ?? 0;
    if (array_key_exists($id, $category_tree)) {
        foreach ($category_tree[$id] as $v) {
            $total += categorySubCount($v);
        }
    }
    /*
    private $_products = null;
    switch ($field) {
        case 'products':
            if (is_null($this->_products)) {
                $this->_products = TwigProduct::search(['type' => $this->id]);
            }
            return $this->_products;

        case 'category':
        case 'parent':
            return $this->_parent = $this->_parent ?: new TwigCategory($this->_category['parent']);

        case 'children':
            if (is_null($this->_children)) {
                $this->_children = self::search(['parent' => $this->id]);
            }
            return $this->_children;
    }
    */

    return $total;
}

class TwigCategory
{
    public $id;

    function __construct($id = 0)
    {
        $this->id = $id;
    }

    private $_category = null;
    function __get($field)
    {
       if (is_null($this->_category)) {
            moduleLoad('category'); //categoryFromDatabase
            if (function_exists('categoryFromDatabase')) {
                 $cacheService = new CacheService();
                 $this->_category = $cacheService->getData('product_types', $this->id);
                 if($this->_category == false){   
                    $this->_category = categoryFromDatabase($this->id);
                    $cacheService->updateData('product_types', $this->id,$this->_category);
                }
            }
        }

        return $this->_category[$field] ?? null;
    }

    function __tostring()
    {
        return $this->name();
    }


    public static function list($list)
    {
        foreach ($list as $k => $v) {
            $list[$k] = new TwigCategory($v);
        }
        return $list;
    }
    public static function search($criteria = [])
    {
        global $_GLOBALS;
        if (array_key_exists('category', $_GLOBALS['criteria_options'])) {
            $criteria = array_merge(
                $_GLOBALS['criteria_options']['category'],
                $criteria
            );
        }
        moduleLoad('category'); //categoryQuery, categorySearch
        if (function_exists('categorySearch') && function_exists('categoryQuery')) {
            return self::list(categorySearch(categoryQuery($criteria)));
        }

        return [];
    }


    public function title()
    {
        return trim($this->name());
    }
    public function slug()
    {
        return trim($this->slug);
    }
    public function description()
    {
        /*$desc = $this->meta_desc;
        if (empty($desc))*/ $desc = $this->description;
        return trim($desc);
    }
    public function keywords()
    {
        $keys = $this->meta_keywords;
        if (empty($keys)) {
            $keys = ucwords(implode(', ', preg_split('/[,\-\s]+/', $this->short_description)));
        }
        return $keys;
    }
    public function breadcrumbs()
    {
        if ($this->parent) {
            return array_merge(
                $this->parent()->breadcrumbs(),
                [['title' => $this->name(), 'url' => $this->url()]]
            );
        }
        return [
            ['title' => 'Home', 'url' => '/'],
            ['title' => $this->name(), 'url' => $this->url()],
        ];
    }


    public function url()
    {
        if ($this->parent) {
            $department = $this->parent();
            //while ($department->parent) $department = $department->parent();
            return $department->url() . sprintf(
                '/%s',
                urlencode($this->slug)
                /*
                '/%s-%d',
                urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $this->name), '-')),
                $this->id
                */
            );
        }
        return sprintf(
            '/buy/%s',
            urlencode($this->slug)
            /*
            '/c/%s-%d',
            urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $this->name), '-')),
            $this->id
            */
        );
    }
    public function parent_id(){
        if($this->parent){
            return $this->parent;
        }else{
            return $this->id;
        }
        
    }
    public function name() {
        $override_name = [
            1   => 'Arcade Parts',
            2   => 'Arcade Machines',
            3   => 'Game Software',
            4   => 'Merchandise',
            561 => 'Locks',
        ];
        if (array_key_exists($this->id, $override_name)) {
            return $override_name[$this->id];
        }
        return $this->name;
    }
    public function image() {
        return null;
    }



    public function products() {
        return categorySubCount($this->id);
    }

    private $_children = null;
    public function children()
    {
        if (is_null($this->_children)) {
            $this->_children = self::search(['parent' => $this->id]);
        }
        return $this->_children;
    }


    private $_parent   = null;
    public function parent()
    {
        if (is_null($this->_parent)) {
            $this->_parent = new TwigCategory($this->parent);
        }
        return $this->_parent;
    }

}
