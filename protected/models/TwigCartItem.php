<?php
namespace app\models;

use app\models\TwigProduct;
use app\models\TwigCurrency;
class TwigCartItem
{
    private $_params = null;
    private $_subProduct = null;
    function __construct($params = null) {
        $this->_params = $params;
        if($params['sub_product_id']){
             $this->_subProduct=$this->getSubProduct();
        }
    }

    function id() {
        return $this->_params['id'];
    }
    function product_id() {
        return $this->_params['product_id'];
    }
    function colour_id() {
        return $this->_params['colour_id'];
    }
    function is_free() {
        return $this->_params['is_free'] == 'y' ? true : false;;
    }
    function colorstock(){
        return !!(!$this->_params['product']->when_sold_out($this->_params['colour_id']) || $this->_params['product']->stockavailable($this->_params['product_id'], $this->_params['colour_id']) >= $this->_params['product']->minOrder());
    }
    function subProductId() {
        return $this->_params['sub_product_id'];
    }
    function warranty(){
        return $this->_params['product']->Warranty();
    }
    function location(){
        return $this->_params['product']->location();
    }
    function availability(){
        return $this->_params['product']->availability();
    }
     public function deposit_needed(){
        return $this->_params['product']->deposit_needed();
    }
    public function full_payment(){
        return $this->_params['product']->full_payment();
    }
    public function pre_order_close_date(){
        return $this->_params['product']->pre_order_close_date();
    }
    /**
     * @deprecated use arrival_date
     *
     * @return string
     */
    public function arival_date()
    {
        return $this->arrival_date();
    }

    /**
     * @return string
     */
    public function arrival_date()
    {
        return $this->_params['product']->arrival_date();
    }

    public function compareAtPriceValue(){
        return $this->_params['product']->compareAtPriceValue();
    }
    function discount(){
        if($this->_params['product']->compareAtPrice()){
            return (($this->_params['product']->arcooda_discount())?'Member Discount':'Discount').' : Save $'. $this->_params['product']->discount(). ' USD';
        }else{
            return null;
        }
    }
    function colour_name(){
        return $this->_params['product']->Singlecolor_name($this->_params['colour_id']);
    }
    function colour_code(){
        return $this->_params['product']->Singlecolor_code($this->_params['colour_id']);
    }

    function image() {
        $subproid=($this->subProductId())?$this->subProductId():0;
        return $this->_params['product']->images($this->_params['colour_id'],$subproid)[0] ?? null;
    }

    function name() {
        return $this->_params['product']->nameWithoutSubpro().(($this->_subProduct)?('-'.ucfirst($this->_subProduct['name'])):'');
    }
    function url() {
        return $this->_params['product']->url();
    }

    function options() {
        $result = [];
        return $result;
    }

    function product() {
        return new TwigProduct($this->_params['id']);
    }

    function rating() {
        return null;
    }

    function quantity() {
        return $this->_params['product_qty'];
    }

    function compareAtPrice() {
        if($this->is_free()){
            return '$ 0.00';
        }
        return '$'.number_format($this->_params['product']->compareAtPriceValue(), 2, '.', ',');
    }
    function getSubProduct(){
        global $DB, $_GLOBALS;
        $row = $DB->query_first(sprintf("SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s  Where `%s` = %d AND `deleted_at` IS NULL LIMIT 1",
            '`product_sub_module`',
            'id',
            $this->subProductId(),
        ));
        return $row;

    }
    function price() {
        if($this->is_free()){
            return '$0.00';
        }
        return $this->_params['product']->price();   
    }
    function priceValue() {
        return '$'.number_format($this->_params['product']->priceValue(), 2, '.', ',');   
    }
    function priceNumeric() {
        if($this->is_free()){
            return 0.00;
        }
        return number_format($this->_params['product']->priceValue(), 2, '.', ',');   
    }
    function compareAtTotal() {
        return $this->compareAtPrice()->multiplied($this->quantity());
    }

    function total() {
        /*
        return $this->price()->multiplied($this->quantity());
        */
         $quantity = $this->quantity();
        if($this->is_free()){
            $quantity =  $quantity -1 ;
        }
        $total=$this->_params['product']->priceValue() * $quantity;
        return number_format((float) $total, 2, '.', '');
    }
}
