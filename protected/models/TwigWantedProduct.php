<?php
namespace app\models;

use app\models\TwigWantedProduct;
use app\models\TwigProduct;
use app\models\TwigCategory;
class TwigWantedProduct
{
    public $id;

    function __construct($id = 0)
    {
        $this->id = $id;
    }

    private $_wanted = null;
    function __get($field)
    {
        if (is_null($this->_wanted)) {
            moduleLoad('wanted');
            $this->_wanted = wantedFromDatabase($this->id);
        }
        if (is_array($this->_wanted) && array_key_exists($field, $this->_wanted)) {
            return $this->_wanted[$field];
        }
        return null;
    }

    public static function list($list)
    {
        foreach ($list as $k => $v) {
            $list[$k] = new TwigWantedProduct($v);
        }
        return $list;
    }
    public static function search($criteria = [])
    {
        global $_GLOBALS;

        //TODO: Can't search by ext_fields, or id ext_field doesn't exists yet?
        if (array_key_exists('id', $criteria)) $criteria['id'] = $criteria['id'];

        moduleLoad('wanted');
        
        $result = self::list(wantedSearch($criteria));

        //TODO: Default order should be id DESC
        $result = array_reverse($result);

        return $result;
    }
      /**
     * @var ?TwigProduct $_product
     */
    private $_product;

    /**
     * @return TwigCategory
     */
    public function product()
    {
        if (!is_null($this->_product)) {
            return $this->_product;
        }
        if($this->__get('online_item_id')){
            $this->_product = new TwigProduct($this->__get('online_item_id'));
        }
        
        return $this->_product;
    }
    public function images()
    {
       if($this->__get('online_item_id')){
        return $this->product()->images();
       }else{
         if($this->__get('image')){
            $images=[];
            $images[]= '/images/img_wanted/'.$this->__get('image').'';
            return $images;
         }
       }
    }
    public function item()
    {
        return $this->__get('online_item');
    }
    public function description()
    {
        return $this->__get('online_description');
    }
    public function categorytype()
    {
        if($this->__get('online_type')){
            $title = '';
            $test  = trim(sprintf('%s', $this->category()->title()));
            if (strlen($test) < 52) {
                return $test;
            }
            return $title;
        }
        return false;
        
    }
    private $_category;

    public function category()
    {
        if (!is_null($this->_category)) {
            return $this->_category;
        }

        $this->_category = new TwigCategory($this->__get('online_type'));

        return $this->_category;
    }
    public function manufacturer(){
        if($this->__get('online_item_id')){
            return $this->product()->manufacturer();
        }
        return false;
    }
    public function location(){
        $country_location_list = getCountryList();
        if (array_key_exists($this->__get('online_pref_location'), $country_location_list)) {
            return explode('-', $country_location_list[$this->__get('online_pref_location')])[0];
        }
        return '';
    }
    public function qty_needed(){
        return $this->__get('online_qty_needed');
    }
    public function online_id(){
        return $this->__get('online_item_id');
    }
    public function wanted_id(){
        return $this->__get('id');
    }
   
}
