<?php
namespace app\models;

use app\models\TwigCartItem;
use app\models\TwigCurrency;
use app\models\TwigProduct;

class TwigCart
{
    private $_contents  = null;
    private $_to_delete = [];
    private $_dirty     = false;
    private $couponAmt  = 0;
    private $invoice_gifts;

    public function __construct($type = null)
    {
        if (!is_null($type)) {
            $this->load($type);
        }
    }
    public function addCoupon( $coupon = 0 )
    {
        
        $this->couponAmt = $coupon;
        //return true;
    }
    public function load($type='shopping')
    {
        include __DIR__ . '/../build_invoice.inc.php';
        $this->invoice_gifts = $invoice_gifts;
        if (!is_null($this->_contents)) {
            return true;
        }

        if (!moduleLoad('cart') || !function_exists('cartProductsFromDatabase')) {
            return false;
        }

        global $_GLOBALS;
        $this->_contents = cartProductsFromDatabase($_GLOBALS['member']['member_id'] ?? 0,$type);
        foreach ($this->_contents as &$v) {
            $v['product'] = new TwigProduct($v['product_id']);
        }
        return true;
    }

    public function save()
    {
        if (!$this->_dirty) {
            return true;
        }
        if (!moduleLoad('cart') || !function_exists('cartProductsToDatabase') || !function_exists('cartProductDelete')) {
            return false;
        }
        global $_GLOBALS;
        foreach ($this->_to_delete as $v) {
            cartProductDelete($_GLOBALS['member']['member_id'] ?? 0,$v);
        }
        $this->_contents  = cartProductsToDatabase($_GLOBALS['member']['member_id'] ?? 0, $this->_contents);
        $this->_to_delete = [];
        $this->_dirty     = false;
        return true;
    }

    public function insert($product_id, $qty, $extra = [])
    {
        global $_GLOBALS;

        $this->load();
        $this->_contents[] = [
            'site_id'     => $_GLOBALS['site_settings']['site_id'],
            'product_id'  => $product_id,
            'product_qty' => $qty,
        ] + $extra;
        $this->_dirty = true;
    }

    public function update($id, $qty, $extra = [])
    {
        $this->load();
        if (array_key_exists($id, $this->_contents)) {
            $this->_contents[$id] = [
                'product_qty' => $qty,
            ] + $extra + $this->_contents[$id];
            $this->_dirty = true;
            return true;
        }
        return false;
    }


    public function updateSave( $extra =[])
    {
        $this->load();
           
         return $this->_contents;
    }

    public function remove($id, $type = 'shopping')
    {
        $this->load($type);

        if (!array_key_exists($id, $this->_contents)) {
            return false;
        }

        $removedProductId = $this->_contents[$id]['product_id'];

        // mark main item for deletion
        $this->_to_delete[] = $id;
        unset($this->_contents[$id]);

        foreach ($this->_contents as $key => $item) {
            if (
                $item['is_free'] === 'y' &&
                $item['free_with_product'] == $removedProductId
            ) {
                $this->_to_delete[] = $key;
                unset($this->_contents[$key]);
            }
        }

        $this->_dirty = true;

        return true;
    }

    public function items($type='shopping')
    {
        $this->load($type);
        $result = [];
        foreach ($this->_contents as $k => $v) {
            $result[] = new TwigCartItem($v);
        }
        usort($result, function($a, $b) {
            return $b->priceNumeric() <=> $a->priceNumeric();
        });
        return $result;
    }

     public function Getcart($type='shopping')
    {
        $this->load($type);

       
        return $this->_contents;
    }

    private $sub_total = null;

    public function sub_total($type='shopping') {
        /*
        if (!is_null($this->sub_total)) {
            return $this->sub_total;
        }
        */

        //$sub_total = new TwigCurrency('0.00', 'USD', 2);
        $sub_total = 0.00;
        foreach ($this->items($type) as $v) {
            $sub_total = $sub_total + $v->total();
        }

        return $this->sub_total = number_format((float) $sub_total, 2, '.', '') + $this->couponAmt;
    }

    private $_total = null;
    public function total($type='shopping') {
        if (!is_null($this->_total)) {
            return $this->_total;
        }

        $total = $this->sub_total($type);
        return $this->_total = $total;
    }

    private $totals = null;

    public function totals($type='shopping') {
    
        /*
        if (!is_null($this->totals)) {
            return $this->totals;
        }
        */

        return $this->totals = [
            ["title" => "Subtotal", "value" => $this->sub_total($type)],
            //["title" => "DiscountedValue", "value" => $this->sub_total($type) - $this->couponAmt],
            //{"title": "Shipping", "value": 25, "shipping": true},
            //{"title": "Tax", "value": 0},
            ["title" => "Total", "value" => $this->total($type) . ' USD'],
            //["title" => "FinalTotal", "value" => $this->total($type) - $this->couponAmt . ' USD'],
        ];
    }

    public function checkout_totals() {

        $this->couponAmt=200;

        $this->_contents['coupon']='Danish';
        //$this->totals();
    }
    //  Added on 12th Jun 2023 by Swapnil
    public function FindProductID($id)
    {
        return $this->_contents[$id]['product_id'];
    }

    public function removeAllChilds($ChildIds)
    {
        $this->load($type);

        $NewArray=$this->_contents;
        $NewArray=array_values($NewArray);
        global $_GLOBALS;
        for($ik=0; $ik<count($ChildIds); $ik++){


    
               $key = array_search($ChildIds[$ik]['linking_id'], array_column($NewArray, 'product_id'));          
                   $this->_to_delete[] = $NewArray[$key]['id'];
      
     
            cartProductDelete($_GLOBALS['member']['member_id'] ?? 0,$NewArray[$key]['id']);
        }   
    }
     public function invoice_gifts(): array
    {
        return $this->invoice_gifts;
    }
    // 12th June 2023 code fix end
}
