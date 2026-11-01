<?php
namespace app\models;

use app\models\TwigInvoice;
use app\cache\CacheService;

/*
account.twig
.email
.inquiries
.orders
.prefs
  .currency
  .mailing_list
  .products_per_page
.ship_addr
  .id
  .company
  .name
  .address
  .address_2
  .city
  .state
  .zip
  .country
  .phone
  .mobile
  .fax
  .email
  .address_full

cart-checkout.twig
.ship_addr
  .id
  .contact
  .email
  .address_full
.notify_parties
.contacts
  .id
  .default
  .email

cart-index.twig
.ship_addr

freight-calculator.twig
.country

product-page.twig
.country
.wholesale

product-tabs.twig
.news_notification

header.twig
.name
.surname
.email
*/

class TwigUser
{
    /** @var int $id */
    public $id;

    /**
     * @param int $id
     */
    public function __construct($id = null)
    {
        global $_GLOBALS;

        if (is_null($id)) {
            $id = $_GLOBALS['member']['id'] ?? 0;
        }

        $this->id = $id;
    }

    /** @var mixed[]|null */
    private $member;

    /**
     * @param string $field
     *
     * @return mixed
     */
    public function __get($field)
    {
        if (!is_null($this->member)) {
            return $this->member[$field] ?? null;
        }

        global $_GLOBALS;

        if ($this->id == ($_GLOBALS['member']['id'] ?? 0)) {
            $this->member = $_GLOBALS['member'];
        } else {
            moduleLoad('member');
             if (function_exists('memberFromDatabase')) {
                $cacheService = new CacheService();
                 $this->member = $cacheService->getData('new_members', $this->id);
                if($this->member == false){    
                    $this->member = memberFromDatabase($this->id);
                    $cacheService->updateData('new_members', $this->id,$this->member);
                }
            }
        }

        return $this->member[$field] ?? null;
    }

    /**
     * @return string
     */
    public function __tostring()
    {
        return $this->__get('name');
    }

    /** @var array{
     *     id      : int,
     *     default : string,
     *     email   : string,
     * }[] */
    private $contacts;

    /**
     * @return array{
     *     id      : int,
     *     default : string,
     *     email   : string,
     * }[]
     */
    public function contacts()
    {
        if (!is_null($this->contacts)) {
            return $this->contacts;
        }

        $this->contacts = [];

        moduleLoad('member'); //memberContactsFromDatabase
        if (!function_exists('memberContactsFromDatabase')) {
            return $this->contacts;
        }

        foreach (memberContactsFromDatabase($this->id) as $v) {
            $this->contacts[] = [
                'id'      => $v['id'],
                'default' => $v['main'],
                'email'   => $v['email'],
                'name'    => $v['name'],
                'last_name' => $v['last_name'],
            ];
        }

        return $this->contacts;
    }

    /**
     * @return string
     */
    public function country()
    {
        return $this->__get('country');
    }

    /**
     * @return string
     */
    public function email()
    {
        return $this->__get('email_address');
    }

    /** @var TwigInvoice[]|null */
    private $inquiries;

    /**
     * @return TwigInvoice[]
     */
    public function inquiries()
    {
        if (!is_null($this->inquiries)) {
            return $this->inquiries;
        }

        $this->inquiries = TwigInvoice::search([
            'member_id'      => $this->id,
            'invoice_status' => ['inquiry', 'enquiry'],
        ]);

        return $this->inquiries;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->__get('first_name') ?? $this->__get('name');
    }

    /**
     * @return string
     */
    public function news_notification()
    {
        return $this->__get('news_notification');
    }

    /** @var mixed[][] */
    private $notify_parties = null;

    /**
     * @return mixed[][]
     */
    public function notify_parties()
    {
        if (!is_null($this->notify_parties)) {
            return $this->notify_parties;
        }

        $this->notify_parties = [];

        moduleLoad('member'); //memberNotifyPartiesFromDatabase
        if (!function_exists('memberNotifyPartiesFromDatabase')) {
            return $this->notify_parties;
        }

        $this->notify_parties = memberNotifyPartiesFromDatabase($this->id);

        return $this->notify_parties;
    }

    /** @var TwigInvoice[]|null */
    private $orders;

    /**
     * @return TwigInvoice[]
     */
    public function orders()
    {
        if (!is_null($this->orders)) {
            return $this->orders;
        }

        $this->orders = TwigInvoice::search([
            'member_id'      => $this->id,
            'invoice_status' => ['proforma', 'sales', 'processed', 'cancelled'],
        ]);

        return $this->orders;
    }

    /** @var array{
     *   currency          : string,
     *   mailing_list      : string,
     *   products_per_page : int,
     * }|null */
    private $prefs;

    /**
     * @return array{
     *   currency          : string,
     *   mailing_list      : string,
     *   products_per_page : int,
     * }
     */
    public function prefs()
    {
        if (!is_null($this->prefs)) {
            return $this->prefs;
        }

        global $_GLOBALS;

        $this->prefs = [
            'currency'          => $this->__get('currency'),
            'mailing_list'      => 'n',
            'products_per_page' => 30,
        ];

        moduleLoad('member'); //memberPreferencesFromDatabase
        if (function_exists('memberPreferencesFromDatabase')) {
            $prefs = memberPreferencesFromDatabase($this->id);
            if (is_array($prefs) && array_key_exists($_GLOBALS['site_settings']['site_id'], $prefs)) {
                $this->prefs = $prefs[$_GLOBALS['site_settings']['site_id']];
            }
        }

        return $this->prefs;
    }

    /** @var array{
     *    id           : int,
     *    company      : string,
     *    contact      : string,
     *    name         : string,
     *    address      : string,
     *    address_2    : string,
     *    city         : string,
     *    state        : string,
     *    zip          : string,
     *    country      : string,
     *    phone        : string,
     *    mobile       : string,
     *    fax          : string,
     *    email        : string,
     *    address_full : string,
     * }[]|null */
    private $ship_addr;

    /**
     * @return mixed[][]
     */
    public function ship_addr()
    {
        if (!is_null($this->ship_addr)) {
            return $this->ship_addr;
        }

        $this->ship_addr = [];

        moduleLoad('member'); //memberShippingAddressesFromDatabase
        if (!function_exists('memberShippingAddressesFromDatabase')) {
            return $this->ship_addr;
        }

        foreach (memberShippingAddressesFromDatabase($this->id) as $v) {
            $this->ship_addr[] = [
                'id'           => $v['id'],
                'company'      => $v['company_name'],
                'contact'      => trim($v['contact'] . ' ' . $v['contact_last']),
                'name'         => trim($v['contact'] . ' ' . $v['contact_last']),
                'address'      => $v['address'],
                'address_2'    => $v['address_2'],
                'city'         => $v['city'],
                'state'        => $v['state'],
                'zip'          => $v['post_code'],
                'country'      => $v['country'],
                'phone'        => $v['phone_no'],
                'mobile'       => $v['mobile_no'],
                'fax'          => $v['fax_no'],
                'email'        => $v['email'],
                'address_full' => $v['address_full'],
            ];
        }

        return $this->ship_addr;
    }

    /**
     * @return string
     */
    public function surname()
    {
        return $this->__get('last_name') ?? $this->__get('surname');
    }

    /**
     * @return string
     */
    public function wholesale()
    {
        return $this->__get('wholesale');
    }


    //------- unused from here?

    /**
     * @return string
     */
    public function address()
    {
        return trim($this->__get('address') . "\n" . $this->__get('address_2'));
    }

    /**
     * @return string
     */
    public function city()
    {
        return $this->__get('city');
    }

    /**
     * @return string
     */
    public function company()
    {
        return $this->__get('company_name');
    }

    /**
     * @return string
     */
    public function fax()
    {
        return $this->__get('fax_no');
    }

    /**
     * @return string
     */
    public function mobile()
    {
        return $this->__get('mobile_no');
    }

    /**
     * @return string
     */
    public function phone()
    {
        return $this->__get('phone_no');
    }

    /**
     * @return string
     */
    public function state()
    {
        return $this->__get('state');
    }

    /**
     * @return string
     */
    public function url()
    {
        return $this->__get('website');
    }

    /**
     * @return string
     */
    public function zip()
    {
        return $this->zip;
    }
    
    public function avatar_image()
    {
        return $this->__get('avatar_image');
    }
}
