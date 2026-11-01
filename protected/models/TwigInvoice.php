<?php
namespace app\models;

use app\models\TwigCurrency;
use app\models\TwigDate;
use app\cache\CacheService;

/*
.id
.currency
.date
.status
*/

class TwigInvoice
{
    public $id;
    private $invoice_frieight_tracking_service;
    public function __construct($id = 0)
    {
        $this->id = $id;
        $this->invoice_frieight_tracking_service = invoiceGetOptionList('freight_tracking');
    }

    private $_invoice = null;
    public function __get($field)
    {
        if (is_null($this->_invoice)) {
            moduleLoad('invoice'); //invoiceFromDatabase
            $cacheService = new CacheService();
            $this->_invoice = $cacheService->getData('invoice', $this->id);
            if($this->_invoice == false){   
                $this->_invoice = invoiceFromDatabase($this->id);
                $cacheService->updateData('invoice', $this->id,$this->_invoice);
            }
        }
        if (is_array($this->_invoice) && array_key_exists($field, $this->_invoice)) {
            return $this->_invoice[$field];
        }
        return null;
    }

    public function __tostring()
    {
        return $this->invoice_number;
    }

    public static function list($list) {
        foreach ($list as $k => $v) {
            $list[$k] = new TwigInvoice($v);
        }
        return $list;
    }
    public static function search($criteria = [])
    {
        global $_GLOBALS;
        if (array_key_exists('invoice', $_GLOBALS['criteria_options'])) {
            $criteria = array_merge(
                $_GLOBALS['criteria_options']['invoice'],
                $criteria
            );
        }
        moduleLoad('invoice'); //invoiceSearch
        //Note: Return ORDER BY invoice_id DESC
        return array_reverse(self::list(invoiceSearch($criteria)));
    }

    public function number()
    {
        return $this->invoice_number;
    }
    public function invoice_id()
    {
        return $this->id;
    }
    public function member_id()
    {
        return $this->member_id;
    }
    public function rand_num()
    {
        return $this->rand_num;
    }
    public function currency()
    {
        return $this->invoice_currency;
    }
    public function payment_method()
    {
        return $this->paymenttype_id;
    }
    public function date()
    {
        return new TwigDate(strtotime($this->open_date));
    }
    public function shipping_address(){
        return [
            'ship_company'=>$this->ship_company,
            'ship_name'=>$this->ship_name,
            'ship_address'=>$this->ship_address,
            'ship_city'=>$this->ship_city,
            'ship_state'=>$this->ship_state,
            'ship_postcode'=>$this->ship_postcode,
            'ship_country'=>$this->ship_country,
            'ship_phone'=>$this->ship_phone,
            'ship_email'=>$this->ship_email
        ];
    }
    public function billing_address(){
        return [
            'bill_company'=>$this->bill_company,
            'bill_name'=>$this->bill_name,
            'bill_address'=>$this->bill_address,
            'bill_city'=>$this->bill_city,
            'bill_state'=>$this->bill_state,
            'bill_postcode'=>$this->bill_postcode,
            'bill_country'=>$this->bill_country,
            'bill_phone'=>$this->bill_phone,
            'bill_email'=>$this->bill_email
        ];
    }
    public function status()
    {
        return [
            'working'   => 'Open',
            'feedback'    => 'Open',
            'nointerest'     => 'Closed',
            'payment'  => 'Awaiting Payment',
            'approval' => 'Awaiting Shipping',
            'shipping' => 'Awaiting Shipping',
            'shipped'    => 'Shipped',
            'shipped-loan'    => 'Shipped',
            'processed' => 'Processed',
            'cancelled'     => 'Closed',
            'nohelp'  => 'Closed',
            'returned' => 'Open',
            'sent_factory' => 'Open',
            'prepare_factory' => 'Open',
            'prepare_customer' => 'Open',
            'sent_customer' => 'Open',
            'return_completed' => 'Processed',
            'return_cancelled' => 'Closed',
        ][$this->invoice_action];
    }
    public function tracking_number(){
        return $this->proforma_tracknumber;
    }
    public function invoicestatus(){
        return $this->invoice_status;
    }
    public function invoicetype(){
        return $this->invoice_type;
    }
    public function display_to_customer(){
        return $this->display_to_customer;
    }
    public function display_to_customer_all(){
        moduleLoad('invoice');
        return invoiceDashboardHistoryNotesFromDatabase($this->id);
    }
    /** @var TwigCurrency? */
    private $total = null;

    /**
     * @return TwigCurrency
     */
    public function total()
    {
        if (!is_null($this->total)) {
            return $this->total;
        }

        return $this->total = (new TwigCurrency(
            $this->__get('invoice_offer'),
            $this->__get('invoice_currency'),
            2
        ))->setAlternate('Pending');
    }

    public function url()
    {
        return sprintf('/account/invoice?invoice_id=%d', $this->id);
    }

    /** @var TwigCurrency? */
    private $subtotal = null;

    /**
     * @return TwigCurrency
     */
    public function subtotal()
    {
        if (!is_null($this->subtotal)) {
            return $this->subtotal;
        }

        return $this->subtotal = (new TwigCurrency(
            $this->__get('products_offer'),
            $this->__get('invoice_currency'),
            2
        ))->setAlternate('Pending');
    }

    public function shipping_cost()
    {
        return '$'.$this->ship_offer;
    }
    public function invoiceProduct($invoice_id){
        moduleLoad('invoice'); //invoiceProductsFromDatabase

        return invoiceProductsFromDatabase($invoice_id);
    }

    public function handler_note()
    {
        return $this->__get('handler_note');
    }
    public function fright_tracking_service()
    {
        if($this->__get('fright_tracking_service') > 0){
            return $this->invoice_frieight_tracking_service[$this->__get('fright_tracking_service')] ?? null;
        }
        return null;
    }
    public function frieght_traking_url()
    {
        return $this->__get('frieght_traking_url');
    }
}
