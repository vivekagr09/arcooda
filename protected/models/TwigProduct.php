<?php
namespace app\models;

//moduleLoad('product');
use app\models\TwigBloags;
use app\models\TwigCategory;
use app\models\TwigCurrency;
use app\models\TwigIcon;
use app\models\TwigImage;
use app\models\TwigMonitorOptions;
use app\cache\CacheService;

/*
 * @implements ITwigShopProduct
 */
class TwigProduct
{
    private const SECTION_ARCADE_PARTS    = 1;
    private const SECTION_ARCADE_MACHINES = 2;

    private const CATEGORY_ARTWORK        = 578;
    private const CATEGORY_CONTROL_PANELS = 579;

    /** @var int */
    public $id;

    public $todayDate ;

    /**
     * @param int $id
     */
    public function __construct($id = 0)
    {
        $this->todayDate = date('Y-m-d');
        $this->id = $id;
    }

    /** @var mixed[]|null */
    private $product;

    /**
     * @param string $field
     *
     * @return mixed
     */
    public function __get($field)
    {
        if (!is_null($this->product)) {
            return $this->product[$field] ?? null;
        }

        moduleLoad('product'); //productFromDatabase, productGetOptionList

        $cacheService = new CacheService();
        $this->product = $cacheService->getData('products', $this->id);
        if(1){
            $this->product = productFromDatabase($this->id);
            $cacheService->updateData('products', $this->id,$this->product);
        }

        if (is_null($this->product)) {
            return null;
        }

        //TODO: Remove this when admin is saving properly
        $this->product['image'] = basename($this->product['image']);

        if (!is_file(productGetOptionList('image_path') . '/' . $this->product['image'])) {
            $this->product['image'] = '';
        }

        return $this->product[$field] ?? null;
    }

    /**
     * @return string
     */
    public function __tostring()
    {
        return $this->name();
    }

    /**
     * Implemented for ITwigShopProduct
     *
     * @return string
     */
    public function id()
    {
        return strval($this->id);
    }

    /**
     * Implemented for ITwigShopProduct
     *
     * @return string
     */
    public function name()
    {
        $name = $this->__get('name');

        if (self::SECTION_ARCADE_MACHINES == $this->__get('section')) {
            if ($this->isProductAvailableByRule()) {
                $subproducts = $this->subproducts();
                if (count($subproducts)) {
                    $defaultkey = ($_GET['subproduct'] ?? false) ? $_GET['subproduct'] : (array_search('y', array_column($subproducts, 'default', 'id')));
                    $name       = trim($name) . ' ' . trim($subproducts[$defaultkey]['name']);
                }
            }
        }

        return trim($name);
    }

    /**
     * Implemented for ITwigShopProduct
     *
     * @return string
     */
    public function sku()
    {
        return $this->__get('product_number');
    }

    /**
     * Implemented for ITwigShopProduct
     *
     * @return TwigCurrency
     */
    public function price()
    {
        // price_online must be enabled
        if ('y' != $this->__get('price_online')) {
            return new TwigCurrency();
        }

        // must have a valid sale price
        if (bccomp('0.0000', $this->__get('secondary_price'), 4) == 0) {
            return new TwigCurrency();
        }

        return new TwigCurrency(
            $this->__get('secondary_price'),
            $this->__get('secondary_currency'),
            $this->__get('precision')
        );
    }

    /**
     * Implemented for ITwigShopProduct
     *
     * @return TwigCurrency|null
     */
    public function compareAtPrice(): ?TwigCurrency
    {
        // price_online must be enabled
        if ('y' != $this->__get('price_online')) {
            return null;
        }

        // price must be different from retail
        if ($this->__get('primary_currency') == $this->__get('secondary_currency') &&
            0 == bccomp(
                $this->__get('primary_price'),
                $this->__get('secondary_price'),
                2
            )
        ) {
            return null;
        }

        // must have a valid retail price
        if (0 == bccomp($this->__get('primary_price'), '0.0000', $this->__get('precision'))) {
            return null;
        }

        // must have a valid sale price
        if (0 == bccomp($this->__get('secondary_price'), '0.0000', $this->__get('precision'))) {
            return null;
        }

        return new TwigCurrency(
            $this->__get('primary_price'),
            $this->__get('primary_currency'),
            $this->__get('precision')
        );
    }

    /** @var TwigImage[]|null */
    private $images;

    /**
     * Implemented for ITwigShopProduct
     *
     * @param int $colour_idval
     *
     * @return TwigImage[]
     */
    public function images($colour_idval = 0, $subproid = 0)
    {
        /*
        if (!is_null($this->images)) {
            return $this->images;
        }
        */

        moduleLoad('product'); //productColorImagesFromDatabase, productGetOptionList, productScreenshotsFromDatabase, productArtWorkFromDatabase

        $this->images = [];

        $colour = explode(',', $this->__get('colour'));
        if (count($colour) > 1) {
            $color_id = $this->isradiochecked();
            // to check images is available for that colour otherwise we will display default images
            $color_id = ($colour_idval > 0) ? $colour_idval : $color_id;
            foreach (productColorImagesFromDatabase($this->id, $color_id) as $v) {
                $this->images[] = new TwigImage([
                    'src'      => sprintf(
                        '/colourimages/%s/%s-%d.jpg',
                        self::createslug($this->__get('name')),
                        self::createslug($v['caption']),
                        $v['id']
                    ),
                    'alt'      => $v['caption'],
                    'src_file' => productGetOptionList('screenshot_path') . $v['image'],
                ]);
            }
        }

        if (self::SECTION_ARCADE_MACHINES == $this->__get('section')) {
            if ($this->isProductAvailableByRule()) {
                $subproducts = $this->subproducts();
                if (count($subproducts)) {
                    if ($subproid) {
                        $defaultkey = array_search($subproid, array_column($subproducts, 'id', 'id'));
                    } else {
                        $defaultkey = ($_GET['subproduct'] ?? false) ? $_GET['subproduct'] : (array_search('y', array_column($subproducts, 'default', 'id')));
                    }
                    $dir = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/product/art_work/';
                    if ('' !== $subproducts[$defaultkey]['product_image'] && file_exists($dir . $subproducts[$defaultkey]['product_image'])) {
                        $this->images[] = new TwigImage([
                            'src'      => '/artimage/' . $subproducts[$defaultkey]['product_image'],
                            'alt'      => $subproducts[$defaultkey]['name'],
                            'src_file' => $dir . $subproducts[$defaultkey]['product_image'],
                        ]);
                    }
                }
            }
        }

        $test = $this->image();
        if (!is_null($test)) {
            $this->images[] = $test;
        }

        foreach (productScreenshotsFromDatabase($this->id) as $v) {
            if($v['active']=='y'){
                if (!empty($v['image_url'] ?? '') && file_exists(productGetOptionList('screenshot_path') . $v['filename'])) {
                    $this->images[] = new TwigImage([
                        'src'      => sprintf(
                            '/ss/%s-%d.jpg',
                            self::createslug((!empty($v['caption'])) ? $v['caption'] : $this->__get('name')),
                            $v['ss_id']
                        ),
                        'alt'      => $v['caption'],
                        'src_file' => productGetOptionList('screenshot_path') . $v['filename'],
                    ]);
                } else {
                    $this->images[] = new TwigImage([
                        'src'      => '',
                        'alt'      => 'default image',
                        'src_file' => '',
                    ]);
                }
            }
        }

        if (self::SECTION_ARCADE_PARTS == $this->__get('section') && self::CATEGORY_ARTWORK == $this->__get('type')) {
            $art_product = productArtWorkFromDatabase($this->id);
            if (count($art_product)) {
                $dir = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/product/art_work/';
                foreach ($art_product as $key => $artpeo) {
                    if ('' !== $artpeo['product_image'] && file_exists($dir . $artpeo['product_image'])) {
                        $this->images[] = new TwigImage([
                            'src'      => '/artimage/' . $artpeo['product_image'],
                            'alt'      => $artpeo['name'],
                            'src_file' => $dir . $artpeo['product_image'],
                        ]);
                    }
                }
            }
        }

        return $this->images;
    }

    /** @var string[]|null */
    private $badges;

    /**
     * Implemented for ITwigShopProduct
     *
     * @return string[]
     */
    public function badges()
    {
        global $_GLOBALS;

        if (!is_null($this->badges)) {
            return $this->badges;
        }

        $this->badges = [];

        if ($this->__get('primary_price') != $this->__get('secondary_price') || 'y' == $this->__get('on_special')) {
            $this->badges[] = $this->getDiscountBadges();
        }

        $days = ($_GLOBALS['site_options']['DAYS_PRODUCT_TO_SHOW_ICON'] > 0) ? $_GLOBALS['site_options']['DAYS_PRODUCT_TO_SHOW_ICON'] : 7;
        if ($this->__get('time_added') + $days * 24 * 60 * 60 >= time()) {
            $this->badges[] = 'new';
        }
        if ($this->__get('available') < 0 && $this->arrival_date > date('Y-m-d') && $this->__get('bonus') == 'y') {
            $this->badges[] = 'Bonus';
        }
        return $this->badges;
    }

    public function getDiscountBadges(){
        if($this->arcooda_discount()){
            return 'VIP MEMBER';
        }elseif($this->pre_offer_discount()){
            return 'PRE ORDER';
        }else{
            return 'sale';
        }
    }

     public function getDiscountType(){
        if($this->arcooda_discount()){
            return 'Vip Member';
        }elseif($this->pre_offer_discount()){
            return 'Pre Order';
        }elseif($this->offer_discount()){
            return 'Offer';
        }else{
            return '';
        }
    }
    /**
     * Implemented for ITwigShopProduct
     *
     * @return int
     */
    public function rating()
    {
        moduleLoad('product');

        return getProductAverageRating($this->id);
    }

    /**
     * Get total review count
     *
     * @return int
     */
    public function totalRatings()
    {
        moduleLoad('product');

        return countProductReviews($this->id);
    }

    /**
     * Implemented for ITwigShopProduct
     *
     * @return int
     */
    public function totalReviews()
    {
        moduleLoad('product');
        
        return getProductReviews($this->id);
    }

    /**
     * Implemented for ITwigShopProduct
     *
     * @return int
     */
    public function reviews()
    {
        return 0;
    }

    /** @var string|null */
    private $availability;

    /**
     * Implemented for ITwigShopProduct
     *
     * @return ?string
     */
    public function availability(): ?string
    {
        if (!is_null($this->availability)) {
            return $this->availability;
        }

        $colour = explode(',', $this->__get('colour'));
        if (count($colour) > 1) {
            return null;
        }

        moduleLoad('product'); //productGetOptionList

        $available_list = productGetOptionList('available');

        $this->availability = $available_list[$this->__get('available')] ?? 'Please Ask';

        if ($this->__get('available') < 0 && $this->arrival_date > date('Y-m-d')) {
            $this->availability = 'Pre Order';
        }

        return $this->availability;
    }
     /**
     * Implemented for ITwigShopProduct
     *
     * @return ?string
     */
    public function availabilityColor($colorId): ?string
    {

        moduleLoad('product'); //productGetOptionList

        $available_list = productGetOptionList('available');
        $availability=inventoryStockGetValue($this->id,$colorId,'available');
        $colorAvailibility = $available_list[$availability] ?? 'Please Ask';
        if ($availability < 0 && $this->arrival_date > date('Y-m-d')) {
            $colorAvailibility = 'Pre Order';
        }

        return $colorAvailibility;
    }
    
     /**
     * Implemented for ITwigShopProduct
     *
     * @return ?string
     */
    public function availabilityColorForAnyStock(): ?string
    {
        $in_stock = false;
        $available_list = productGetOptionList('available');
        foreach ($this->color() as $key => $value) {
            $availability=inventoryStockGetValue($this->id,$value['id'],'available');
           // echo $availability.','; 
            $colorAvailibility = $available_list[$availability] ?? 'Please Ask';
            if(in_array($colorAvailibility, ['In Stock', 'Stock'])){
                $in_stock = true;
                break;
            }
        }
        return $in_stock;
    }
    /** @var mixed[]|null */
    private $features;

    /**
     * Implemented for ITwigShopProduct
     *
     * @return mixed[]
     */
    public function features()
    {
        if (!is_null($this->features)) {
            return $this->features;
        }

        $this->features = [];

        if (bccomp($this->__get('weight'), '0.000', 3)) {
            $this->features['weight'] = $this->__get('weight');
        }

        return $this->features;
    }

    /**
     * @return string
     */
    public function discount()
    {
        return $this->compareAtPriceValue() - $this->priceValue();
    }

    /**
     * @deprecated use arcooda_discount
     *
     * @return string
     */
    public function arcoda_discount()
    {
        return $this->arcooda_discount();
    }

    /**
     * @fixme replace arcoda with arcooda
     *
     * @return string
     */
    public function pre_offer_discount()
    {
        return $this->__get('pre_offer_discount');
    }

    public function arcooda_discount()
    {
        return $this->__get('arcoda_discount');
    }
    /**
     * @return string
     */
    public function offer_discount()
    {
        return $this->__get('offer_discount');
    }

    /**
     * @deprecated use getLinkedProducts
     *
     * @return mixed[][]
     */
    public function getLinkedProduct()
    {
        return $this->getLinkedProducts();
    }

    /**
     * @return mixed[][]
     */
    public function getLinkedProducts()
    {
        moduleLoad('product');

        $result = [];

        if (self::SECTION_ARCADE_MACHINES != $this->__get('section')) {
            return $result;
        }

        $linked_products = [];
        foreach (productGenericsFromDatabase($this->id, '`products_upgrade_linking`', 'id', 'product_id', 'ORDER BY `rank` ASC') as $v) {
            if ('y' == $v['show_front']) {
                $linked_products[] = $v['linking_id'];
                $linked_products[$v['linking_id']]['price'] = $v['offer_price'];
            }
        }

        if (empty($linked_products)) {
            return $result;
        }

        foreach (TwigProduct::search(['id' => $linked_products]) as $v) {
            $result[] = [
                'id'       => $v->__get('id'),
                'name'     => $v->__get('name'),
                'price'    => number_format((float) (($linked_products[$v->__get('id')]['price']) ? $linked_products[$v->__get('id')]['price'] : $v->__get('secondary_price')), 2, '.', ''),
                'discount' => ($v->__get('offer_price')) ? ($v->__get('primary_price') - $v->__get('offer_price')) : $v->discount(),
                'product'  => $v,
            ];
        }

        return $result;
    }

    /**
     * @return TwigCurrency|null
     */
    public function special_order_fee()
    {
        if ('y' == $this->__get('additional_fees') && $this->__get('forwarder_fee') > 0) {
            return new TwigCurrency($this->__get('forwarder_fee'), $this->__get('forwarder_fee_currency'), $this->__get('precision'));

        }
        return null;
    }

    /**
     * @return string
     */
    public function discontinued()
    {
        return $this->__get('discontinued');
    }

    /**
     * @param int[] $list
     *
     * @return TwigProduct[]
     */
    public static function list($list)
    {
        $result = [];
        foreach ($list as $k => $v) {
            $result[$k] = new TwigProduct($v);
        }

        return $result;
    }

    /**
     * @param mixed[] $criteria
     *
     * @return array{
     *     min_price:string,
     *     max_price:string,
     * }
     */
    public static function price_detail($criteria = [])
    {
        global $_GLOBALS;

        moduleLoad('product'); //productpricerange

        if (array_key_exists('product', $_GLOBALS['criteria_options'])) {
            $criteria = array_merge(
                $_GLOBALS['criteria_options']['product'],
                $criteria
            );
        }

        return productpricerange($criteria);
    }

    /**
     * @param mixed[] $eq
     * @param mixed[] $lt
     * @param mixed[] $gt
     *
     * @return TwigProduct[]
     */
    public static function search($eq = [], $lt = [], $gt = [], $sort_by = null,$offset = 0 ,$limit = 0)
    {
        global $_GLOBALS;

        moduleLoad('product'); //productQuery, productSearch, productSort

        if (array_key_exists('product', $_GLOBALS['criteria_options'])) {
            $eq = array_merge(
                $_GLOBALS['criteria_options']['product'],
                $eq
            );
        }

        /*
        $product_query  = productQuery($eq, $lt, $gt);
        $product_list   = productSearch($product_query);
        */
        $product_list   = productSearch($eq,$offset,$limit);
        $product_sorted = productSort($product_list, [
        ][$sort_by] ?? 'date_added');

        return static::list($product_sorted);
    }

    /**
     * @return string
     */
    public function title()
    {
        $title = trim($this->name());
        $test  = trim(sprintf('%s - %s', $title, $this->category()->title()));
        if (strlen($test) < 52) {
            return $test;
        }
        return $title;
    }

    /**
     * @return string
     */
    public function description()
    {
        /*
        $desc = $this->__get('meta_desc');
        if (empty($desc)) {
            $desc = $this->__get('short_desc');
        }
        if (empty($desc)) {
        */
            $desc = preg_replace("/\r\n|\r|\n/", '<br/>', $this->__get('long_desc'));
        /*
        }
        */

        return trim($desc);
    }

    /**
     * @deprecated use excerpt()
     *
     * @return string
     */
    public function sortdescription()
    {
        return $this->excerpt();
    }

    /**
     * @return string
     */
    public function keywords()
    {
        $keys = $this->__get('meta_keywords');
        if (empty($keys)) {
            $keys = ucwords(implode(', ', preg_split('/[,\-\s]+/', $this->__get('short_desc'))));
        }
        return $keys;
    }

    /**
     * @return array{title:string, url:string}[]
     */
    public function breadcrumbs()
    {
        return array_merge(
            $this->category()->breadcrumbs(),
            [['title' => $this->name(), 'url' => $this->url()]]
        );
    }

    /**
     * @return string
     */
    private function categoryUrl()
    {
        return $this->category()->url();
    }

    /**
     * @deprecated use image()
     *
     * @return TwigImage|null The relative url of the image
     */
    public function dispalyimage()
    {
        return $this->__get('image_url') ?? '';
    }

    /**
     * @return TwigImage|null
     */
    public function image()
    {
        moduleLoad('product');

        $test = $this->__get('image_url') ?? '';
        if ('' !== $test) {
            return new TwigImage([
                'src'      => $test,
                'alt'      => $this->name(),
                'src_file' => productGetOptionList('image_path') . '/' . $this->__get('image'),
            ]);
        }

        return null;
    }

    /**
     * @return TwigImage|null The relative url of the image
     */
    public function thumb()
    {
        moduleLoad('product');

        $test = $this->__get('thumb_url') ?? '';
        if ('' !== $test) {
            return new TwigImage([
                'src' => $test,
                'alt' => $this->name(),
            ]);
        }

        $test = $this->__get('image_url') ?? '';
        if ('' !== $test) {
            return new TwigImage([
                'src'      => $test,
                'alt'      => $this->name(),
                'src_file' => productGetOptionList('image_path') . '/' . $this->__get('image'),
            ]);
        }

        return null;
    }

    /**
     * @return string
     */
    public function url()
    {
        return sprintf(
            '/buy/%s-%d',
            urlencode($this->__get('slug') ?: urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $this->__get('name')), '-'))),
            $this->id
        );
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

    /**
     * @return bool
     */
    public function is_product_owner()
    {
        global $_GLOBALS;
        global $DB;

        if (!array_key_exists('member', $_GLOBALS)) {
            return false;
        }

        if ($row = $DB->query_first(sprintf(
            "SELECT COUNT(*) AS `total` FROM `highwaygroup`.`serial_numbers` WHERE `member_id` = %d AND `product_id` = %d LIMIT 1",
            $_GLOBALS['member']['id'],
            $this->id
        ), MYSQLI_ASSOC)) {
            if ($row['total'] > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @deprecated use is_rule_available_for_restricted_user
     *
     * @return bool
     */
    private function is_rule_available_for_ristricted_user()
    {
        return $this->is_rule_available_for_restricted_user();
    }

    /**
     * @fixme Replace availabe with available
     *
     * @return bool
     */
    private function is_rule_available_for_restricted_user()
    {
        global $_GLOBALS;
        global $DB;

        if (!array_key_exists('member', $_GLOBALS)) {
            return false;
        }

        if ($row = $DB->query_first(sprintf(
            "SELECT COUNT(*) AS `total` FROM `highwaygroup`.`serial_numbers` WHERE `member_id` = %d AND FIND_IN_SET(`product_id`, %s) LIMIT 1",
            $_GLOBALS['member']['id'],
            $DB->escape($this->__get('product_availabe_for'))
        ), MYSQLI_ASSOC)) {
            if ($row['total'] > 0) {
                return true;
            }
        }

        return false;
    }

    /** @var string[]|null */
    private $owner_serials;

    /**
     * @deprecated use owner_product_serial_number()
     *
     * @return string[]
     */
    public function owner_product_serial_numebr()
    {
        return $this->owner_product_serial_number();
    }

    /**
     * @return string[]
     */
    public function owner_product_serial_number()
    {
        global $_GLOBALS;
        global $DB;

        if (!is_null($this->owner_serials)) {
            return $this->owner_serials;
        }

        $this->owner_serials = [];

        if (!array_key_exists('member', $_GLOBALS)) {
            return $this->owner_serials;
        }

        if ($query = $DB->query(sprintf(
            "SELECT `serial_id` FROM `highwaygroup`.`serial_numbers` WHERE `member_id` = %d AND `product_id` = %d",
            $_GLOBALS['member']['id'],
            $this->id
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $this->owner_serials[] = chunk_split($row['serial_id'], 1, ' ');
            }
            $DB->free_result($query);
        }

        return $this->owner_serials;
    }

    /** @var mixed[]|null */
    private $subproducts;

    /**
     * @return mixed[][]
     */
    private function subproducts()
    {
        moduleLoad('product'); //subProductFromDatabase

        if (is_null($this->subproducts)) {
            $this->subproducts = subProductFromDatabase($this->id);
        }

        return $this->subproducts;
    }

    /**
     * @return string
     */
    public function nameWithoutSubpro()
    {

        if($this->__get('name')==null){
            return '';
        }
        return $this->__get('name');
    }

    /**
     * @return string
     */
    public function includedItem()
    {

        if($this->__get('included_item')==null){

            return '';
        }
        return $this->__get('included_item');
    }

    /** @var mixed[][]|null */
    private $display_artworks;

    /**
     * @return mixed[][]
     */
    public function displayArtWork()
    {
        moduleLoad('product');

        if (!is_null($this->display_artworks)) {
            return $this->display_artworks;
        }

        $this->display_artworks = [];

        if (self::SECTION_ARCADE_PARTS == $this->__get('section') && self::CATEGORY_CONTROL_PANELS == $this->__get('type')) {
            $this->display_artworks = productArtWorkFromDatabase($this->id);
        }

        return $this->display_artworks;
    }

    /** @var string[]|null */
    private $included_artworks;

    /**
     * @return string
     */
    public function includedArtWork()
    {
        moduleLoad('product');

        if (!is_null($this->included_artworks)) {
            return implode("\n", $this->included_artworks);
        }

        $this->included_artworks = [];

        if (self::SECTION_ARCADE_PARTS == $this->__get('section') && self::CATEGORY_ARTWORK == $this->__get('type')) {
            $material_list = productGetOptionList('material');
            foreach (productArtWorkFromDatabase($this->id) as $artwork) {
                $this->included_artworks[] = sprintf(
                    " - 1 x %s (%s)",
                    $artwork['name'],
                    $material_list[$artwork['material']] ?? ''
                );
            }
        }

        return implode("\n", $this->included_artworks);
    }

    /**
     * @param int|string $key
     *
     * @return string
     */
    public function includedSubProduct($key)
    {
        moduleLoad('product'); //productFromDatabase

        $data = "";
        if (self::SECTION_ARCADE_MACHINES == $this->__get('section')) {
            $subproducts = $this->subproducts();
            if (count($subproducts)) {
                if ($subproducts[$key]['option_list']) {
                    foreach (explode(',', $subproducts[$key]['option_list']) as $proid) {
                        $prod = productFromDatabase($proid);
                        $data = $data . "\n - " . $prod['product_name'] . " - #" . $proid;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @param int|null $product_id
     *
     * @return mixed[]
     */
    public function subProductDiscount($product_id = null)
    {
        $subproducts = $this->subproducts();
        $response    = [];
        if (count($subproducts)) {
            if ($product_id) {
                $defaultkey = $product_id;
            } else {
                $defaultkey = (isset($_GET['subproduct'])) ? $_GET['subproduct'] : (array_search('y', array_column($subproducts, 'default', 'id')));
            }
            $product = $subproducts[$defaultkey];
            if ('y' == $product['special_flag'] && date('Y-m-d', strtotime($product['special_expiry']) <= date('Y-m-d')) && $product['sell_price'] > $product['special_priceus']) {
                $response['discount_price'] = $product['special_priceus'];
                $response['price']          = $product['sell_price'];
                $response['discount']       = $product['sell_price'] - $product['special_priceus'];
                $response['msg']            = 'Discount';
            } elseif (self::is_arcooda_customer() && $product['sell_price'] > $product['sales_price_arcooda']) {
                $response['discount_price'] = $product['sales_price_arcooda'];
                $response['price']          = $product['sell_price'];
                $response['discount']       = $product['sell_price'] - $product['sales_price_arcooda'];
                $response['msg']            = 'Member Discount';
            }
        }

        return $response;
    }

    /**
     * @fixme Replace availabe with available
     *
     * @return string
     */
    public function registeredMachineForRule()
    {
        moduleLoad('product'); //productFromDatabase

        $data = "";
        if ($this->__get('product_availabe_for')) {
            foreach (explode(',', $this->__get('product_availabe_for')) as $proid) {
                $prod = productFromDatabase($proid);
                $url  = sprintf(
                    '/buy/%s-%d',
                    urlencode($this->__get('slug') ?: urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $prod['product_name']), '-'))),
                    $prod['id']
                );
                $data = $data . "\n - <a href='" . $url . "' target='_blank'>" . $prod['product_name'] . "</a>"; // - #".$proid;
            }
        }

        return $data;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function subproductDescription($key)
    {
        $subproducts = $this->subproducts();

        return $subproducts[$key]['description'];
    }

    /**
     * @return mixed[]
     */
    public function color()
    {
        moduleLoad('product');

        $color_array    = [];
        $colour         = explode(',', $this->__get('colour'));
        $in_stock       = [];
        $out_stock      = [];
        $inactive_stock = [];
        if (count($colour) > 1) {
            foreach ($colour as $key => $value) {
                $color_data          = productGenericFromDatabase($value, 'product_colour');
                $stock               = inventoryStockGetValue($this->id, $value, 'qty');
                $sold_out            = inventoryStockGetValue($this->id, $value, 'when_sold_out');
                $color_data['stock'] = $stock;
                if ($stock > $this->__get('min_order')) {
                    if ('active' == $sold_out) {
                        array_push($in_stock, $color_data);
                    } else {
                        array_push($inactive_stock, $color_data);
                    }
                } else {
                    if ('active' == $sold_out) {
                        array_push($out_stock, $color_data);
                    } else {
                        array_push($inactive_stock, $color_data);
                    }
                }
            }
            $color_array = array_merge($color_array, $in_stock);
            $color_array = array_merge($color_array, $out_stock);
            $color_array = array_merge($color_array, $inactive_stock);
        }
        return $color_array;
    }

    /**
     * @param int $id
     *
     * @return mixed[]
     */
    public static function Singlecolor($id)
    {
        moduleLoad('product'); //productGenericFromDatabase

        return productGenericFromDatabase($id, 'product_colour')['color_val'];
    }

    /**
     * @param int $id
     *
     * @return mixed[]
     */
    public function Singlecolor_name($id)
    {
        moduleLoad('product');

        return productGenericFromDatabase($id, 'product_colour')['colour'];
    }

    /**
     * @param int $id
     *
     * @return mixed[]
     */
    public function Singlecolor_code($id)
    {
        moduleLoad('product');

        return productGenericFromDatabase($id, 'product_colour')['code3'];
    }

    /**
     * @return mixed[][]
     */
    public function product_manual()
    {
        moduleLoad('product'); //productManualsFromDatabase

        return productManualsFromDatabase($this->id);
    }

    /**
     * @return mixed[][]
     */
    public function product_firmware()
    {
        moduleLoad('product'); //productFirmwareFromDatabase

        return productFirmwareFromDatabase($this->id);
    }

    /**
     * @return mixed[][]
     */
    public function product_links()
    {
        global $DB;

        $result = array();
        $query  = $DB->query("SELECT *, UNIX_TIMESTAMP(`created`) AS `created_time` FROM `product_links` WHERE `product_id` = " . intval($this->id) . " AND `active`='y' ORDER BY `rank` ASC");
        while ($row = $DB->fetch_assoc($query)) {
            $result[$row["id"]] = $row;
        }
        $DB->free_result($query);

        return $result;
    }

    /**
     * @return string
     */
    public function getfiletype($file)
    {
        $file_ext = substr($file, strrpos($file, '.'));
        if (substr($file_ext, 0, 1) == '.') {
            $file_ext = substr($file_ext, 1);
        }
        return $file_ext;
    }

    /**
     * TODO: Remove the float conversion, and use locale-correct formatting
     *
     * @return string
     */
    public function priceValue()
    {
        if ('n' == $this->__get('price_online')) {
            return '0.00';
        }

        return number_format((float) $this->__get('secondary_price'), 2, '.', '');
    }

    /**
     * @return string
     */
    public function sales_price_arcooda()
    {
        return number_format((float) ($this->__get('secondary_price') < $this->__get('sales_price_arcooda')) ? $this->__get('secondary_price') : $this->__get('sales_price_arcooda'), 2, '.', '');
    }

    /**
     * @return string
     */
    public function sales_price_distributor()
    {
        return number_format((float) ($this->__get('secondary_price') < $this->__get('sales_price_distributor')) ? $this->__get('secondary_price') : $this->__get('sales_price_distributor'), 2, '.', '');
    }

    /**
     * @return string
     */
    public function sales_price_container()
    {
        return number_format((float) ($this->__get('secondary_price') < $this->__get('sales_price_container')) ? $this->__get('secondary_price') : $this->__get('sales_price_container'), 2, '.', '');
    }

    /**
     * @param float $price
     * @param int   $qun
     *
     * @return string
     */
    public function priceTotal($price, $qun)
    {
        $totalprice = $price * $qun;
        return number_format((float) $totalprice, 2, '.', '');
    }

    public function priceMultiplied($qty = 1)
    {
        return $this->price()->multiplied($qty);
    }

    /**
     * @return string
     */
    public function excerpt()
    {
        return $this->__get('short_desc');
    }

    /**
     * @return string
     */
    public function section()
    {
        return $this->__get('section');
    }

    /**
     * @return string
     */
    public function coin_operated()
    {
        return ($this->__get('coin_operated') == 'y') ? 'Yes' : 'No';
    }

    /**
     * @return mixed[]|int|null
     */
    public function product_series()
    {
        if ($this->__get('product_series')) {
            return getproductSeriesByid($this->__get('product_series'));
        }
        return $this->__get('product_series');
    }

    /**
     * @return string
     */
    public function details()
    {
        return $this->__get('product_details');
    }


    /** @var ?TwigCategory */
    private $category;

    /**
     * @return TwigCategory
     */
    public function category()
    {
        if (!is_null($this->category)) {
            return $this->category;
        }

        $this->category = new TwigCategory($this->__get('type'));

        return $this->category;
    }

    /**
     * @return ?TwigCurrency
     */
    public function compareAtPriceValue()
    {
        if ('y' == $this->__get('price_online') && bccomp($this->__get('primary_price'), '0.0000', $this->__get('precision')) && bccomp($this->__get('secondary_price'), '0.0000', $this->__get('precision')) && (bccomp($this->__get('primary_price'), $this->__get('secondary_price'), $this->__get('precision')) || $this->__get('primary_currency') != $this->__get('secondary_currency'))) {
            $price = new TwigCurrency($this->__get('primary_price'), $this->__get('primary_currency'), $this->__get('precision'));
            return $price->getAmount();
        }

        return 0;
    }

    /**
     * @return mixed
     */
    public function isradiochecked()
    {
        //moduleLoad('inventory'); //inventoryStockGetValue

        $colour = explode(',', $this->__get('colour'));
        if (isset($_GET['colorid']) && !empty($_GET['colorid'])) {
            $stock   = inventoryStockGetValue($this->id, $_GET['colorid'], 'qty');
            $soldout = inventoryStockGetValue($this->id, $_GET['colorid'], 'when_sold_out');
            if ($stock > $this->__get('min_order') || 'active' == $soldout) {
                return $_GET['colorid'];
            } else {
                foreach ($colour as $k => $v) {
                    $stock   = inventoryStockGetValue($this->id, $v, 'qty');
                    $soldout = inventoryStockGetValue($this->id, $v, 'when_sold_out');
                    if ($stock > $this->__get('min_order') || 'active' == $soldout) {
                        return $colour[$k];
                    }
                }
            }
        } else {
            foreach ($colour as $k => $v) {
                $stock   = inventoryStockGetValue($this->id, $v, 'qty');
                $soldout = inventoryStockGetValue($this->id, $v, 'when_sold_out');
                if ($stock > $this->__get('min_order') || 'active' == $soldout) {
                    return $colour[$k];
                }
            }
        }
    }

    /**
     * @param int $product_id
     * @param int $colour_id
     *
     * @return int
     */
    public function stockavailable($product_id, $colour_id)
    {
        if ($colour_id) {
            return inventoryStockGetValue($product_id, $colour_id, 'qty');
        } else {
            return $this->__get('qty');
        }

    }

    /**
     * Get linked machine value
     * 
     * @return TwigProduct[]
     */
    public function getMatchingMachine()
    {
        moduleLoad('product'); //productPartsofFromDatabase

        $linked_products = [];
        foreach (productPartsofFromDatabase($this->id) as $product) {
            $linked_products[] = $product['partof_id'];
        }

        if (empty($linked_products)) {
            return [];
        }

        return static::search(['id' => $linked_products]);
    }

    /**
     * @return TwigProduct[]
     */
    public function getUpgradeLinking()
    {
        moduleLoad('product'); //productUpgradeLinkingFromDatabase

        $linked_products = [];
        foreach (productUpgradeLinkingFromDatabase($this->id) as $product) {
            $linked_products[] = $product['linking_id'];
        }

        if (empty($linked_products)) {
            return [];
        }

        return static::search(['id' => $linked_products]);
    }

    /**
     * @deprecated use getproductbrochure
     *
     * @return mixed[][]
     */
    public function getproductbroucher()
    {
        return $this->getProductBrochures();
    }

    /**
     * @return mixed[][]
     */
    public function getProductBrochures()
    {
        moduleLoad('product'); //productBrochureFromDatabase

        $brochures = [];

        foreach (productBrochureFromDatabase($this->id) as $bros) {
            $dir  = $_SERVER['DOCUMENT_ROOT'] . '/webadmin/assets/icon/image/';
            $file = $dir . $bros['name'];
            if (file_exists($file)) {
                $brochures[] = $bros;
            }
        }

        return $brochures;
    }

    /** @var TwigImage[]|null */
    private $images_category;

    /**
     * @return TwigImage[]
     */
    public function imagescategory()
    {
        if (!is_null($this->images_category)) {
            return $this->images_category;
        }

        moduleLoad('product'); //productScreenshotsFromDatabase

        $this->images_category = [];

        $test = $this->image();
        if (!is_null($test)) {
            $this->images_category[] = $test;
        }

        foreach (productScreenshotsFromDatabase($this->id) as $v) {
            if (!empty($v['image_url'] ?? '')) {
                $this->images_category[] = new TwigImage([
                    'src'      => '/ss/image-' . $v['ss_id'] . '.jpg',
                    'alt'      => $v['caption'],
                    'src_file' => productGetOptionList('screenshot_path') . $v['filename'],
                ]);
            }
        }

        return $this->images_category;
    }

    /**
     * @return mixed[][]
     */
    public function recommendedproduct()
    {
        moduleLoad('product'); //productRecommendedFromDatabase

        $recommended = [];
        foreach (productRecommendedFromDatabase($this->id) as $v) {
            $recommended[] = new TwigProduct($v['related_id']);
        }

        return $recommended;
    }

    /**
     * @return mixed[]|null
     */
    public function getproductBuilder()
    {
        if ($this->__get('product_builder')) {
            moduleLoad('product'); //getBuilderById

            $builder = getBuilderById($this->__get('product_builder'), $this->id);
            return $builder;
        }

        return null;
    }

    /**
     * @return string
     */
    public function getBuilderurl()
    {
        $isSecure = false;
        if (isset($_SERVER['HTTPS']) && 'on' == $_SERVER['HTTPS']) {
            $isSecure = true;
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO'] || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && 'on' == $_SERVER['HTTP_X_FORWARDED_SSL']) {
            $isSecure = true;
        }
        return '/builder/';
    }

    /**
     * @return string
     */
    private function machineColorOptions()
    {
        moduleLoad('product');

        $colours = explode(',', $this->__get('machine_colours'));
        if (count($colours)) {
            $colorhtml = '<div style="display: inline-block;">';
            foreach ($colours as $key => $color) {
                $colorcode = productGenericFromDatabase($color, 'product_colour');
                $colorhtml = $colorhtml . '<span style="padding:12px;display: inherit;margin-left:2px;width: 25px;background:' . $colorcode["color_val"] . '" data-toggle="tooltip" data-placement="top" title="' . $colorcode["colour"] . '"></span>';
            }
            return $colorhtml . '</div>';
        }

        return 'No Any color available';
    }

    /** @var ?string[] */
    private $_dimen;

    /**
     * @return ?string
     */
    public function depth()
    {
        moduleLoad('product');

        if (is_null($this->_dimen)) {
            moduleLoad('product'); //productDimensionFromDatabase

            $this->_dimen = productDimensionFromDatabase($this->id);
            if (is_array(reset($this->_dimen))) {
                $this->_dimen = reset($this->_dimen);
            }
        }
        return $this->_dimen['depth'] ?? null;
    }

    /**
     * @param int $id
     *
     * @return string
     */
    private static function getMonitorPartNumbers($id)
    {
        moduleLoad('product'); //productMonitorPartLinkingFromDatabase

        $data = productMonitorPartLinkingFromDatabase($id);
        if (count($data)) {
            $linked_product = '';
            foreach ($data as $key => $value) {
                $products = static::search(['id' => $value['linking_id']]);
                if (count($products)) {
                    $product        = reset($products);
                    $linked_product = $linked_product . '<a href="' . $product->url() . '" target="_blank">#' . $product->id . '</a>, ';
                }
            }
            return rtrim($linked_product, ', ');
        }

        return '';
    }

    /**
     * @return array{
     *     '20ft':string|null,
     *     '40ft':string|null
     * }|false
     */
    private function getContainerQuantity()
    {
        moduleLoad('product'); //productContainersFromDatabase

        $container_quan = productContainersFromDatabase($this->id);
        if (count($container_quan)) {
            return [
                '20ft' => ('real' == $container_quan[$this->id]['contload_20ft_type']) ? ((int) $container_quan[$this->id]['contload_20ft_qty']) : null,
                '40ft' => ('real' == $container_quan[$this->id]['contload_40ft_type']) ? ((int) $container_quan[$this->id]['contload_40ft_qty']) : null,
            ];
        }

        return false;
    }

    /**
     * @return mixed[][]
     */
    private function getPackingdetail()
    {
        moduleLoad('product'); //productPackingsFromDatabase

        $result = [];

        foreach (productPackingsFromDatabase($this->id) as $key => $pack) {
            $result[] = [
                'name'  => ucfirst($pack['name']) . ' Details',
                'value' => [
                    [
                        "name"   => "Depth",
                        "value"  => (bccomp($pack['depth'], '0.0', 1)) ? (number_format((float) $pack['depth'], 2, '.', '') . ' cm') : 'N/A',
                        "value2" => (bccomp($pack['depth'], '0.0', 1)) ? (bcmul($pack['depth'], '0.393701', 2) . ' in') : 'N/A',
                    ],
                    [
                        "name"   => "Width",
                        "value"  => (bccomp($pack['width'], '0.0', 1)) ? (number_format((float) $pack['width'], 2, '.', '') . ' cm') : 'N/A',
                        "value2" => (bccomp($pack['width'], '0.0', 1)) ? (bcmul($pack['width'], '0.393701', 2) . ' in') : 'N/A',
                    ],
                    [
                        "name"   => "Height",
                        "value"  => (bccomp($pack['height'], '0.0', 1)) ? (number_format((float) $pack['height'], 2, '.', '') . ' cm') : 'N/A',
                        "value2" => (bccomp($pack['height'], '0.0', 1)) ? (bcmul($pack['height'], '0.393701', 2) . ' in') : 'N/A',
                    ],
                    [
                        "name"   => "Weight",
                        "value"  => (preg_match('/^([1-9][0-9.]+|0\.[0-9]*[1-9][0-9]*)$/', $pack['weight'])) ? (number_format((float) $pack['weight'], 2, '.', '') . ' kg') : (number_format((float) $pack['weight'], 2, '.', '') ?: 'N/A'),
                        "value2" => (bccomp($pack['weight'], '0.0', 3)) ? (bcmul($pack['weight'], '2.20462', 2) . ' lb') : 'N/A',
                    ],
                ],
            ];
        }

        return $result;
    }

    /**
     * @return ?string
     */
    public function height()
    {
        if (is_null($this->_dimen)) {
            moduleLoad('product'); //productDimensionFromDatabase

            $this->_dimen = productDimensionFromDatabase($this->id);
            if (is_array(reset($this->_dimen))) {
                $this->_dimen = reset($this->_dimen);
            }
        }

        return $this->_dimen['height'] ?? null;
    }

    /**
     * @return ?string
     */
    public function width()
    {
        if (is_null($this->_dimen)) {
            moduleLoad('product'); //productDimensionFromDatabase

            $this->_dimen = productDimensionFromDatabase($this->id);
            if (is_array(reset($this->_dimen))) {
                $this->_dimen = reset($this->_dimen);
            }
        }

        return $this->_dimen['width'] ?? null;
    }

    /**
     * @return string
     */
    public function item_weight()
    {
        if (is_null($this->_dimen)) {
            moduleLoad('product'); //productDimensionFromDatabase

            $this->_dimen = productDimensionFromDatabase($this->id);
            if (is_array(reset($this->_dimen))) {
                $this->_dimen = reset($this->_dimen);
            }
        }

        return number_format((float) $this->_dimen['weight'], 2, '.', '');
    }

    /**
     * @return string|null
     */
    public function mass()
    {
        moduleLoad('product'); //productDimensionFromDatabase

        $weight = $this->__get('weight');
        if (preg_match('/^([1-9][0-9.]+|0\.[0-9]*[1-9][0-9]*)$/', $weight)) {
            return $weight;
        }

        if (preg_match('/[^0-9.]/', $weight)) {
            return $weight;
        }

        if (is_null($this->_dimen)) {
            $this->_dimen = productDimensionFromDatabase($this->id);
            if (is_array(reset($this->_dimen))) {
                $this->_dimen = reset($this->_dimen);
            }

        }
        return $this->_dimen['weight'] ?? null;
    }
       /**
     * @return string|null
     */
    public function mass_packing()
    {
        moduleLoad('product'); //productDimensionFromDatabase
        if (function_exists('productPackingFromDatabase')) {
            $packing = productPackingFromDatabase($this->id);
            if (count($packing) && !is_array(reset($packing))) {
                return $packing['weight'];
            }
        }
        $weight = $this->__get('weight');
        if (preg_match('/^([1-9][0-9.]+|0\.[0-9]*[1-9][0-9]*)$/', $weight)) {
            return $weight;
        }

        if (preg_match('/[^0-9.]/', $weight)) {
            return $weight;
        }

        if (is_null($this->_dimen)) {
            $this->_dimen = productDimensionFromDatabase($this->id);
            if (is_array(reset($this->_dimen))) {
                $this->_dimen = reset($this->_dimen);
            }

        }
        return $this->_dimen['weight'] ?? null;
    }
    /**
     * @return string
     */
    private function safetyvalue()
    {
        $data = '<ul>';
        foreach (explode(',', $this->__get('approvals')) as $key => $value) {
            $data .= "<li>" . $value . "</li>";
        }
        $data .= '</ul>';

        return $data;
    }

    /**
     * @param int    $id
     * @param string $table
     *
     * @return string
     */
    private static function getvaluefromtable($id, $table)
    {
        return (string)getValueFromTableById($id, $table);
    }

    /**
     * Get monitor count for compare product
     *
     * @return int
     */
    public function compare_product_monitor_val()
    {
        global $_GLOBALS;
        global $DB;

        //moduleLoad('cart'); //cartGetVisitorID
        moduleLoad('product'); //getcompareproduct

        $criteria = [];
        if (array_key_exists('product', $_GLOBALS['criteria_options'])) {
            $criteria = array_merge(
                $_GLOBALS['criteria_options']['product'],
                $criteria
            );
        }
        $visitor_id = cartGetVisitorID();

        moduleLoad('product');
        if (count($compare_product = getcompareproduct($criteria, $visitor_id))) {
            if ($row = $DB->query_first(sprintf(
                "SELECT COUNT(`product_id`) AS `count` FROM `products_monitors` WHERE `deleted_at` IS NULL AND `product_id` IN(%s) GROUP BY `product_id` ORDER BY `count` DESC LIMIT 1",
                implode(', ', $compare_product)
            ), MYSQLI_ASSOC)) {
                return $row['count'];
            }
        }

        return 0;
    }

    /**
     * @return int
     */
    private static function compare_product_dimensions_val()
    {
        global $_GLOBALS;
        global $DB;

        //moduleLoad('cart'); //cartGetVisitorID
        moduleLoad('product'); //getcompareproduct

        $criteria = [];
        if (array_key_exists('product', $_GLOBALS['criteria_options'])) {
            $criteria = array_merge(
                $_GLOBALS['criteria_options']['product'],
                $criteria
            );
        }

        $visitor_id = cartGetVisitorID();

        if (count($compare_product = getcompareproduct($criteria, $visitor_id))) {
            if ($row = $DB->query_first(sprintf(
                "SELECT COUNT(`product_id`) AS `count` FROM `product_packing` WHERE `deleted_at` IS NULL AND `product_id` IN(%s) GROUP BY `product_id` ORDER BY `count` DESC LIMIT 1",
                implode(", ", $compare_product)
            ), MYSQLI_ASSOC)) {
                return $row['count'];
            }
        }

        return 0;
    }

    /**
     * @fixme Replace dimentions with dimensions
     *
     * @return mixed[]
     */
    public function comparePageDetail()
    {
        //moduleLoad('cart'); //cartGetVisitorID
        moduleLoad('product'); //productPackingsFromDatabase, productContainersFromDatabase, productMonitorFromDatabase

        $data          = [];
        $monitor_array = [];
        $cabinet_array = [
            ['name' => 'Cabinet Material', 'value' => []],
            ['name' => 'Control Panel Material', 'value' => []],
            ['name' => 'LED Lighting', 'value' => []],
            ['name' => 'Joystick Brand', 'value' => []],
            ['name' => 'Button Brand', 'value' => []],
            ['name' => 'Colour Options', 'value' => []],
            ['name' => 'Colour Notes', 'value' => []],
        ];
        $dimensions = [
            ['name' => 'Unboxed', 'data' => [['name' => 'Depth', 'value' => []], ['name' => 'Width', 'value' => []], ['name' => 'Height', 'value' => []], ['name' => 'Weight', 'value' => []]]],
        ];

        $container = [
            ['name' => '20ft Container Loading', 'value' => []],
            ['name' => '40ft Container Loading', 'value' => []],
        ];
        $visitor_id       = cartGetVisitorID();
        $compare_products = static::getcompareproduct($visitor_id);
        if (count($compare_products) > 0) {
            $highest_monitor_count = $this->compare_product_monitor_val();
            if ($highest_monitor_count > 0) {
                for ($key = 0; $key < $highest_monitor_count; $key++) {
                    $monitor_array[$key] = [
                        ['name' => 'Monitor Size #' . ($key + 1), 'value' => []],
                        ['name' => 'Monitor Specifications', 'value' => []],
                        ['name' => 'Touchscreen', 'value' => []],
                        ['name' => 'Monitor Options', 'value' => []],
                    ];
                }
                foreach ($compare_products as $key => $product) {
                    $count        = 0;
                    $monitor_data = productMonitorFromDatabase($product->id);
                    if (count($monitor_data) > 0) {
                        foreach ($monitor_data as $key => $monitor) {
                            array_push($monitor_array[$count][0]['value'], (($monitor['monitor_size'] > 0) ? self::getvaluefromtable($monitor['monitor_size'], '`product_monitor_size`') : 'N/A'));
                            array_push($monitor_array[$count][1]['value'], (($monitor['monitor_specifications'] > 0) ? self::getvaluefromtable($monitor['monitor_specifications'], '`product_monitor_specifications`') : 'N/A'));
                            array_push($monitor_array[$count][2]['value'], (($monitor['monitor_touchscreen'] > 0) ? self::getvaluefromtable($monitor['monitor_touchscreen'], '`product_monitor_touchscreen`') : 'N/A'));
                            array_push($monitor_array[$count][3]['value'], (($monitor['monitor_other_options'] > 0) ? self::getvaluefromtable($monitor['monitor_other_options'], '`product_monitor_other_options`') : 'N/A'));
                            $count++;
                        }
                        if (count($monitor_array) > $count) {
                            for ($key = $count; $key < count($monitor_array); $key++) {
                                array_push($monitor_array[$key][0]['value'], 'N/A');
                                array_push($monitor_array[$key][1]['value'], 'N/A');
                                array_push($monitor_array[$key][2]['value'], 'N/A');
                                array_push($monitor_array[$key][3]['value'], 'N/A');
                            }
                        }
                    } else {
                        foreach ($monitor_array as $key => $monitor) {
                            foreach ($monitor as $keyd => $val) {
                                array_push($monitor_array[$key][$keyd]['value'], 'N/A');
                            }
                        }
                    }
                }
            }
        }
        $data['monitor'] = $monitor_array;
        foreach ($compare_products as $key => $product) {
            array_push($cabinet_array[0]['value'], (($product->__get('cabinet_material') > 0) ? self::getvaluefromtable($product->__get('cabinet_material'), '`product_cabinet_material`') : 'N/A'));
            array_push($cabinet_array[1]['value'], (($product->__get('cabinet_panel_material') > 0) ? self::getvaluefromtable($product->__get('cabinet_panel_material'), '`product_cabinet_panel_material`') : 'N/A'));
            array_push($cabinet_array[2]['value'], (($product->__get('cabinet_lighting') > 0) ? self::getvaluefromtable($product->__get('cabinet_lighting'), '`product_cabinet_lighting`') : 'N/A'));
            array_push($cabinet_array[3]['value'], (($product->__get('cabinet_joystick_brand') > 0) ? self::getvaluefromtable($product->__get('cabinet_joystick_brand'), '`product_cabinet_joystick_brand`') : 'N/A'));
            array_push($cabinet_array[4]['value'], (($product->__get('cabinet_button_brand') > 0) ? self::getvaluefromtable($product->__get('cabinet_button_brand'), '`product_cabinet_button_brand`') : 'N/A'));
            array_push($cabinet_array[5]['value'], $product->machineColorOptions());
            array_push($cabinet_array[6]['value'], (!empty($product->__get('color_comment')) ? $product->__get('color_comment') : 'N/A'));
            array_push($dimensions[0]['data'][0]['value'], (bccomp($product->depth(), '0.0', 1)) ? ($product->depth() . ' cm / ' . (bcmul($product->depth(), '0.393701', 2) . ' in')) : 'N/A');
            array_push($dimensions[0]['data'][1]['value'], (bccomp($product->width(), '0.0', 1)) ? ($product->width() . ' cm / ' . (bcmul($product->width(), '0.393701', 2) . ' in')) : 'N/A');
            array_push($dimensions[0]['data'][2]['value'], (bccomp($product->height(), '0.0', 1)) ? ($product->height() . ' cm / ' . (bcmul($product->height(), '0.393701', 2) . ' in')) : 'N/A');
            array_push($dimensions[0]['data'][3]['value'], (preg_match('/^([1-9][0-9.]+|0\.[0-9]*[1-9][0-9]*)$/', $product->item_weight())) ? ($product->item_weight() . ' kg / ' . (bcmul($this->item_weight(), '2.20462', 2) . ' lb')) : ($product->item_weight() ?: 'N/A'));
            $container_quan = productContainersFromDatabase($product->id);
            if (count($container_quan)) {
                array_push($container[0]['value'], (('real' == $container_quan[$product->id]['contload_20ft_type']) ? ((int) $container_quan[$product->id]['contload_20ft_qty']) : 'N/A'));
                array_push($container[1]['value'], (('real' == $container_quan[$product->id]['contload_40ft_type']) ? ((int) $container_quan[$product->id]['contload_40ft_qty']) : 'N/A'));
            } else {
                array_push($container[0]['value'], 'N/A');
                array_push($container[1]['value'], 'N/A');
            }
        }
        $highest_dimensions_count = self::compare_product_dimensions_val();
        if ($highest_dimensions_count > 0) {
            for ($key = 0; $key < $highest_dimensions_count; $key++) {
                array_push($dimensions, ['name' => 'Boxed Betails #' . ($key + 1), 'data' => [['name' => 'Depth', 'value' => []], ['name' => 'Width', 'value' => []], ['name' => 'Height', 'value' => []], ['name' => 'Weight', 'value' => []]]]);
            }

            foreach ($compare_products as $key => $product) {
                $count     = 1;
                $packaging = productPackingsFromDatabase($product->id);

                if (count($packaging)) {
                    foreach ($packaging as $key => $pack) {
                        array_push($dimensions[$count]['data'][0]['value'], (bccomp($pack['depth'], '0.0', 1)) ? ($pack['depth'] . ' cm / ' . (bcmul($pack['depth'], '0.393701', 2) . ' in')) : 'N/A');
                        array_push($dimensions[$count]['data'][1]['value'], (bccomp($pack['height'], '0.0', 1)) ? ($pack['height'] . ' cm / ' . (bcmul($pack['height'], '0.393701', 2) . ' in')) : 'N/A');
                        array_push($dimensions[$count]['data'][2]['value'], (bccomp($pack['height'], '0.0', 1)) ? ($pack['height'] . ' cm / ' . (bcmul($pack['height'], '0.393701', 2) . ' in')) : 'N/A');
                        array_push($dimensions[$count]['data'][3]['value'], (preg_match('/^([1-9][0-9.]+|0\.[0-9]*[1-9][0-9]*)$/', $pack['weight'])) ? ($pack['weight'] . ' kg / ' . (bcmul($pack['weight'], '2.20462', 2) . ' lb')) : ($pack['weight'] ?: 'N/A'));
                        $count++;
                    }
                    if ($highest_dimensions_count >= $count) {
                        for ($key = $count; $key <= $highest_dimensions_count; $key++) {
                            array_push($dimensions[$key]['data'][0]['value'], 'N/A');
                            array_push($dimensions[$key]['data'][1]['value'], 'N/A');
                            array_push($dimensions[$key]['data'][2]['value'], 'N/A');
                            array_push($dimensions[$key]['data'][3]['value'], 'N/A');
                        }
                    }
                } else {
                    for ($key = 1; $key <= $highest_dimensions_count; $key++) {
                        array_push($dimensions[$key]['data'][0]['value'], 'N/A');
                        array_push($dimensions[$key]['data'][1]['value'], 'N/A');
                        array_push($dimensions[$key]['data'][2]['value'], 'N/A');
                        array_push($dimensions[$key]['data'][3]['value'], 'N/A');
                    }
                }
            }
        }
        $data['cabinet']    = $cabinet_array;
        $data['dimentions'] = $dimensions;
        $data['container']  = $container;

        return $data;
    }

    /** @var mixed[]|null */
    private $monitor;

    /**
     * @return mixed[]|null
     */
    private function monitor()
    {
        moduleLoad('product'); //productMonitorFromDatabase

        if (is_null($this->monitor)) {
            $this->monitor = productMonitorFromDatabase($this->id);
        }
        return $this->monitor;
    }

    /**
     * @return mixed[][]
     */
    private function monitor_detail()
    {
        $array_data = [];
        $count      = 1;
        foreach ($this->monitor() as $key => $monitor) {
            if ($monitor['monitor_size'] > 0 || $monitor['monitor_specifications'] > 0 || $monitor['monitor_touchscreen'] > 0 || $monitor['monitor_other_options'] > 0 || self::getMonitorPartNumbers($monitor['id'])) {
                $array_data[] = [
                    'name'  => 'Monitor #' . $count,
                    'value' => [
                        ['name' => 'Monitor Size', 'value' => (($monitor['monitor_size'] > 0) ? self::getvaluefromtable($monitor['monitor_size'], '`product_monitor_size`') : ''), 'link' => null],
                        ['name' => 'Monitor Specifications', 'value' => (($monitor['monitor_specifications'] > 0) ? self::getvaluefromtable($monitor['monitor_specifications'], '`product_monitor_specifications`') : ''), 'link' => null],
                        ['name' => 'Touchscreen', 'value' => (($monitor['monitor_touchscreen'] > 0) ? self::getvaluefromtable($monitor['monitor_touchscreen'], '`product_monitor_touchscreen`') : ''), 'link' => null],
                        ['name' => 'Other Monitor Options Available', 'value' => (($monitor['monitor_other_options'] > 0) ? self::getvaluefromtable($monitor['monitor_other_options'], '`product_monitor_other_options`') : ''), 'link' => null],
                        ['name' => 'Monitor Part Number', 'value' => self::getMonitorPartNumbers($monitor['id']), 'link' => null],
                    ],
                ];
                $count++;
            }
        }

        return $array_data;
    }

    /** @var mixed[][]|null */
    private $specification;

    /**
     * @return mixed[][]
     */
    public function specification()
    {
        if (!is_null($this->specification)) {
            return $this->specification;
        }

        $this->specification[] = [
            'name'     => 'Product Information',
            'type'     => 'table',
            'features' => [
                ['name' => 'Manufacturer', 'value' => $this->manufacturer(), 'link' => $this->categoryUrl() . '?m=' . $this->manufacturer_id()],
                ['name' => 'Manufacturer Part No', 'value' => $this->man_code(), 'link' => null],
                ['name' => 'Product Type', 'value' => $this->product_type(), 'link' => null],
                ['name' => 'Condition', 'value' => $this->condition(), 'link' => null],
                ['name' => 'Product Category', 'value' => $this->category(), 'link' => $this->categoryUrl()],
                ['name' => 'Location', 'value' => $this->location(), 'link' => $this->categoryUrl() . '?cl[]=' . $this->locationId()],
            ],
        ];
        if (self::SECTION_ARCADE_MACHINES == $this->__get('section')) {
            if (count($this->monitor())) {
                $this->specification[] = [
                    'name'     => 'Monitor Information',
                    'type'     => 'tablemultiplerow',
                    'features' => $this->monitor_detail(),
                ];
            }
            if ($this->__get('cabinet_material') > 0 || $this->__get('cabinet_panel_material') > 0 || $this->__get('cabinet_lighting') > 0 || $this->__get('cabinet_joystick_brand') > 0 || $this->__get('cabinet_button_brand')) {
                $this->specification[] = [
                    'name'     => 'Cabinet Information',
                    'type'     => 'table',
                    'features' => [
                        [
                            'name'  => 'Cabinet Material',
                            'value' => ($this->__get('cabinet_material') > 0) ? self::getvaluefromtable($this->__get('cabinet_material'), '`product_cabinet_material`') : '',
                            'link'  => null,
                        ],
                        [
                            'name'  => 'Control Panel Material',
                            'value' => ($this->__get('cabinet_panel_material') > 0) ? self::getvaluefromtable($this->__get('cabinet_panel_material'), '`product_cabinet_panel_material`') : '',
                            'link'  => null,
                        ],
                        [
                            'name'  => 'LED Cabinet Lighting',
                            'value' => ($this->__get('cabinet_lighting') > 0) ? self::getvaluefromtable($this->__get('cabinet_lighting'), '`product_cabinet_lighting`') : '',
                            'link'  => null,
                        ],
                        [
                            'name'  => 'Joystick Brand',
                            'value' => ($this->__get('cabinet_joystick_brand') > 0) ? self::getvaluefromtable($this->__get('cabinet_joystick_brand'), '`product_cabinet_joystick_brand`') : '',
                            'link'  => null,
                        ],
                        [
                            'name'  => 'Button Brand',
                            'value' => ($this->__get('cabinet_button_brand') > 0) ? self::getvaluefromtable($this->__get('cabinet_button_brand'), '`product_cabinet_button_brand`') : '',
                            'link'  => null,
                        ],
                    ],
                ];
            }
            if ($this->__get('machine_colours')) {
                $this->specification[] = [
                    'name'     => 'Colour',
                    'type'     => 'table',
                    'features' => [
                        ['name' => 'Color Options', 'value' => $this->machineColorOptions(), 'link' => null],
                        ['name' => 'Color Notes', 'value' => $this->__get('color_comment'), 'link' => null],
                    ],
                ];
            }
        }
        if ($this->__get('product_details')) {
            $this->specification[] = [
                'name'  => 'Detailed Specifications',
                'type'  => 'list',
                'value' => $this->__get('product_details'),
            ];
        }
        if ($this->__get('approvals')) {
            $this->specification[] = [
                'name'  => 'Safety Approvals & Certificates',
                'type'  => 'list',
                'value' => $this->safetyvalue(),
            ];
        }

        $this->specification[] = [
            'name'     => 'Dimensions, Weight and Container Packing',
            'type'     => 'multitable',
            'features' => [
                [
                    [
                        'name'  => 'Unboxed Details',
                        'value' => [
                            [
                                "name"   => "Depth",
                                "value"  => (bccomp($this->depth(), '0.0', 1)) ? ($this->depth() . ' cm') : '',
                                "value2" => (bccomp($this->depth(), '0.0', 1)) ? (bcmul($this->depth(), '0.393701', 2) . ' in') : '',
                            ],
                            [
                                "name"   => "Width",
                                "value"  => (bccomp($this->width(), '0.0', 1)) ? ($this->width() . ' cm') : '',
                                "value2" => (bccomp($this->width(), '0.0', 1)) ? (bcmul($this->width(), '0.393701', 2) . ' in') : '',
                            ],
                            [
                                "name"   => "Height",
                                "value"  => (bccomp($this->height(), '0.0', 1)) ? ($this->height() . ' cm') : '',
                                "value2" => (bccomp($this->height(), '0.0', 1)) ? (bcmul($this->height(), '0.393701', 2) . ' in') : '',
                            ],
                            [
                                "name"   => "Weight",
                                "value"  => (preg_match('/^([1-9][0-9.]+|0\.[0-9]*[1-9][0-9]*)$/', $this->item_weight())) ? ($this->item_weight() . ' kg') : ($this->item_weight() ?: ''),
                                "value2" => (bccomp($this->item_weight(), '0.0', 3)) ? (bcmul($this->item_weight(), '2.20462', 2) . ' lb') : '',
                            ],
                        ],
                    ],
                ],
                $this->getPackingdetail(),
            ],
        ];
        if ($this->__get('weight') > 0) {
            $this->specification[] = [
                'name'  => '',
                'type'  => 'text',
                'value' => 'Shipping Weight = '.$this->__get('weight').' kgs',
            ];
        }
        if ($this->__get('packing_text')) {
            $this->specification[] = [
                'name'  => '',
                'type'  => 'text',
                'value' => $this->__get('packing_text'),
            ];
        }
       
        if ($this->getContainerQuantity()['20ft'] || $this->getContainerQuantity()['40ft']) {
            $this->specification[] = [
                'name'     => 'Container Packing Quantities',
                'type'     => 'table',
                'features' => [
                    ['name' => '20ft Container', 'value' => $this->getContainerQuantity()['20ft'], 'link' => null],
                    ['name' => '40ft Container', 'value' => $this->getContainerQuantity()['40ft'], 'link' => null],
                ],
            ];
        }
        if ($this->__get('container_text')) {
            $this->specification[] = [
                'name'  => '',
                'type'  => 'text',
                'value' => $this->__get('container_text'),
            ];
        }

        return $this->specification;
    }

    /**
     * @return TwigProduct[]
     */
    public function related()
    {
        $related_pro= static::search(['type' => $this->__get('type')]);
        if(count($related_pro)<5){
            $up_linking=self::getUpgradeLinking();
            if(is_array($up_linking) && count($up_linking)>1){
                $related_pro=array_merge($related_pro,$up_linking);
            }
            
        }
        if(count($related_pro)<5){
            $machine_link=self::getMatchingMachine();
            if(is_array($machine_link) && count($machine_link)>0)
            $related_pro=array_merge($related_pro,$machine_link);
        }
        return   $related_pro;   
    }

    /**
     * @return TwigProduct[]
     */
    public function related_product()
    {
        moduleLoad('product'); //productRelatedFromDatabase

        $related_product = [];
        foreach (productRelatedFromDatabase($this->id) as $product) {
            if (count($results = static::search(['id' => $product['related_id']]))) {
                $related_product[] = reset($results);
            }
        }

        return $related_product;
    }

    /**
     * @return string
     */
    public function location()
    {
        moduleLoad('product'); //productGetOptionList

        $country_location_list = productGetOptionList('country_location');

        return $country_location_list[$this->__get('country_location')] ?? '';
    }

    /**
     * @return string
     */
    public function Warranty()
    {
        moduleLoad('product'); //productGetOptionList

        $warranty = productGetOptionList('warranty');
        if (array_key_exists($this->__get('warranty'), $warranty)) {
            return $warranty[$this->__get('warranty')];
        }
        return '';
    }

    /**
     * @return string
     */
    public function locationId()
    {
        return $this->__get('country_location');
    }

    /**
     * @return mixed[][]
     */
    public function wholesaleprice()
    {
        moduleLoad('product'); //productBulkPricingFromDatabase

        return productBulkPricingFromDatabase($this->id);
    }

    /**
     * @return string
     */
    public function manufacturer()
    {
        moduleLoad('product'); //productGetOptionList

        $manufacturer_list = productGetOptionList('manufacturer');

        return $manufacturer_list[$this->__get('manufacturer')] ?? '';
    }

    /**
     * @return int
     */
    public function manufacturer_id()
    {
        return $this->__get('manufacturer');
    }

    /**
     * @return string
     */
    public function notes()
    {
        return $this->__get('notes');
    }

    /**
     * @return string
     */
    public function style()
    {
        moduleLoad('product'); //productGetOptionList

        $style_list = productGetOptionList('style');

        return $style_list[$this->style] ?? '';
    }

    /**
     * @return TwigIcon[]
     */
    public function icons()
    {
        if ('' == $this->__get('icons')) {
            return [];
        }

        $data = [];
        foreach (explode(',', $this->__get('icons')) as $icon) {
            $data[] = new TwigIcon($icon);
        }

        return $data;
    }

    /**
     * @deprecated use getBlog
     *
     * @return TwigBloags[]
     */
    public function getBloags()
    {
        return $this->getBlogs();
    }

    /**
     * @fixme Replace TwigBloags with TwigBlog
     *
     * @return TwigBloags[]
     */
    public function getBlogs()
    {
        return TwigBloags::getProductBlogs($this->id);
    }

    /**
     * @return string
     */
    public function colour()
    {
        return $this->__get('colour');
    }

    /**
     * @return string
     */
    public function weight()
    {
        return $this->__get('weight');
    }

    /**
     * @return string
     */
    public function condition()
    {
        return (1 == $this->__get('condition')) ? 'New' : 'Used';
    }

    /**
     * @return string
     */
    public function type()
    {
        return $this->__get('base_type_name');
    }

    /**
     * @return string
     */
    public function allow_invoice()
    {
        return $this->__get('allow_invoice');
    }

    /**
     * @return int
     */
    public function typeId()
    {
        return $this->__get('base_type');
    }

    /**
     * @return int
     */
    public function product_category_type()
    {
        return $this->__get('type');
    }

    /**
     * @return bool
     */
    public function soldout()
    {
        return ('active' == $this->__get('when_sold_out')) ? false : true;
    }

    /**
     * @param int $colour_id
     *
     * @return bool
     */
    public function when_sold_out($colour_id)
    {
        //moduleLoad('inventory'); //inventoryStockGetValue

        //echo 'sdfdsfdsfd'.$colour_id; die;
        $sold_out = inventoryStockGetValue($this->id, $colour_id, 'when_sold_out');
        return ('active' == $sold_out) ? false : true;
    }

    /**
     * @fixme This should probably be moved to TwigMember/TwigVisitor
     *
     * @param int $visitor_id
     *
     * @return TwigProduct[]
     */
    public static function getcompareproduct($visitor_id)
    {
        global $_GLOBALS;

        moduleLoad('product'); //getcompareproduct

        $criteria = [];
        if (array_key_exists('product', $_GLOBALS['criteria_options'])) {
            $criteria = array_merge(
                $_GLOBALS['criteria_options']['product'],
                $criteria
            );
        }

        return static::list(getcompareproduct($criteria, $visitor_id));
    }

    /**
     * @fixme This should probably be moved to TwigMember/TwigVisitor
     *
     * @param int $visitor_id
     *
     * @return TwigProduct[]
     */
    public static function getwishlistproduct($visitor_id)
    {
        global $_GLOBALS;

        moduleLoad('product'); //getwishlistproduct

        $criteria = [];
        if (array_key_exists('product', $_GLOBALS['criteria_options'])) {
            $criteria = array_merge(
                $_GLOBALS['criteria_options']['product'],
                $criteria
            );
        }

        return static::list(getwishlistproduct($criteria, $visitor_id));
    }

    /**
     * @return mixed[]
     */
    public function videos()
    {
        global $DB;

        $data = [];
        if ($sql = $DB->query("SELECT * FROM `product_videos` WHERE `product_id` = " . $this->id."  AND `active`='y' ORDER BY `rank` ASC")) {
            while ($row = $DB->fetch_array($sql)) {
                $data[] = $row;
            }
            $DB->free_result($sql);
        }

        return $data;
    }

    /**
     * @return int
     */
    public function getsliderheight()
    {
        global $DB;

        $slider_height = 300;

        if ($this->__get('product_slider')) {
            $data = $DB->query_first("SELECT page_height FROM `product_page_slider` WHERE  `id` = " . $this->__get('product_slider'), MYSQLI_ASSOC);
            if ($data) {
                $slider_height = $data['page_height'];
            }
        }

        return $slider_height;
    }

    /**
     * @return mixed[][]
     */
    public function getproductslider()
    {
        global $DB;

        $slider = [];

        if ($this->__get('product_slider')) {
            if ($query = $DB->query(sprintf(
                "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s  Where `%s` = %d ORDER BY `rank` ASC",
                '`product_page_slider_images`',
                'slider_id',
                $this->__get('product_slider')
            ))) {
                while ($row = $DB->fetch_assoc($query)) {
                    $slider[] = $row;
                }
                $DB->free_result($query);
            }
        }

        return $slider;
    }

    /**
     * @return mixed[][]
     */
    public function getproductcarouselimages()
    {
        global $DB;

        $carousel = [];
        if ($this->__get('product_carousel')) {
            if ($query = $DB->query(sprintf(
                "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s  Where `%s` = %d ORDER BY `rank` ASC",
                '`product_page_carousel_images`',
                'crousel_id',
                $this->__get('product_carousel')
            ))) {
                while ($row = $DB->fetch_assoc($query)) {
                    $carousel[] = $row;
                }
                $DB->free_result($query);
            }
        }

        return $carousel;
    }

    /**
     * @return mixed[]|null
     */
    public function getproductcarousel()
    {
        global $DB;

        if ($this->__get('product_carousel')) {
            if ($row = $DB->query_first(sprintf(
                "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s WHERE `%s` = %d LIMIT 1",
                '`product_page_carousel`',
                'id',
                 $this->__get('product_carousel')
            ), MYSQLI_ASSOC)) {
                return $row;
            }
        }

        return null;
    }

    /*
    public function get360View()
    {
        if ($this->__get('360_view')) {
            return view360GenericsFromDatabase(
                $this->__get('360_view'),
                "`360_images`",
                "id",
                "view_id",
                "ORDER BY `rank` ASC"
            );
        } else {
            return [];
        }
    }
    public function get360marker()
    {
        if ($this->__get('360_view')){
            return view360getActiveMarker(
                $this->__get('360_view'),
                "`360_marker`",
                "id",
                "view_id",
                "ORDER BY `id` ASC"
            );
        }else{
            return [];
        }
    }
    public function removedoublecode($data)
    {
        return str_replace('""', '', $data);
    }
    public function get360setting()
    {
        if($this->__get('360_view')){
            return view360GenericFromDatabase(
                1,
                "`360_view_setting`"
            );
        }else{
            return [];
        }
    }
    public function markerlocation($marker_id, $image_id)
    {
        return view360MarkerLocation($marker_id, $image_id);
    }
    */

    /**
     * @return mixed[][]
     */
    public function productSupported()
    {
        global $DB;

        $supported = [];
        if ($this->__get('product_supported')) {
            $supported_array = explode(',', $this->__get('product_supported'));
            foreach ($supported_array as $key => $support) {
                $data = $DB->query_first("SELECT * FROM `product_supported` WHERE `active`='y' AND `id` = " . $support, MYSQLI_ASSOC);
                if ($data) {
                    $supported[] = $data;
                }
            }
        }

        return $supported;
    }

    /**
     * @return int
     */
    public function minOrder()
    {
        return $this->__get('min_order');
    }

    /**
     * @return int
     */
    public function product_type()
    {
        return ($this->__get('product_type_con')) ? $this->__get('product_type_con') : 0;
    }

    /**
     * @return int
     */
    public function man_code()
    {
        return ($this->__get('man_code')) ? $this->__get('man_code') : 0;
    }

    /**
     * @return int
     */
    public function get360viewID()
    {
        return $this->__get('360_view');
    }

    /*
    public function get360SideImages(){
        global $DB;

        if($this->__get('360_view')){
            $view= view360GenericFromDatabase($this->__get('360_view'),'`360_view`');
            if($view['side_images']){
                $images=$DB->query_first(sprintf(
                    "SELECT * FROM %s WHERE `%s` = %d LIMIT 1",
                    "`360_side_images`",
                    "id",
                    $view['side_images']
                ), MYSQLI_ASSOC);
                $imagedtata=[];
                $imagedata['image_left']=basename($images['img_left']);
                $imagedata['image_right']=basename($images['img_right']);
                return $imagedata;
            }else{
                return [];
            }
        }else{
            return [];
        }
    }
    */

    /**
     * @return bool
     */
    public function is_product_compare()
    {
        global $DB;

        //moduleLoad('cart'); //cartGetVisitorID

        return !!$DB->query_first(sprintf(
            "SELECT `id` FROM `compare_product` WHERE `visitor_id` = %d AND `product_id` = %d LIMIT 1",
            cartGetVisitorID(),
            $this->id
        ), MYSQLI_ASSOC);
    }

    /**
     * @return string
     */
    public function encryptid()
    {
        return md5(strval($this->id));
    }

    /**
     * @return bool
     */
    public function is_product_wishlist()
    {
        global $DB;

        //moduleLoad('cart'); //cartGetVisitorID

        return !!$DB->query_first(sprintf(
            "SELECT `id` FROM  `wishlist_product` WHERE `visitor_id` = %d AND `product_id` = %d LIMIT 1",
            cartGetVisitorID(),
            $this->id
        ), MYSQLI_ASSOC);
    }

    /**
     * @return int
     */
    public function getblogscount()
    {
        global $DB;

        //@todo blogCount(blogQuery(['status' => 'y', 'products' => (string)$this->id]));
        if ($row = $DB->query_first(sprintf(
            "SELECT COUNT(*) AS `total` FROM %s WHERE `status` = 'y' AND FIND_IN_SET('%d', `products`)",
            "`blogs`",
            $this->id
        ), MYSQLI_ASSOC)) {
            return $row['total'];
        }

        return 0;
    }

    /**
     * @return string
     */
    public function orderRule()
    {
        return $this->__get('order_rule');
    }
    public function terms_condition_custom()
    {
        return $this->__get('terms_condition_custom');
    }
    /**
     * @return string
     */
    public function deposit_needed()
    {
        return $this->__get('deposit_needed');
    }

    /**
     * @deprecated use arrival_date()
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
        $arrival_date = $this->__get('arrival_date');

        if (in_array($arrival_date, [null, '0000-00-00 00:00:00', '0000-00-00', ''])) {
            return null;
        }

        return date('jS F, Y', strtotime($arrival_date));
    }

    /**
     * @deprecated use is_arcooda_customer
     *
     * @return bool
     */
    private static function is_arcoda_customer()
    {
        return self::is_arcooda_customer();
    }

    /**
     * @return bool
     */
    private static function is_arcooda_customer()
    {
        global $_GLOBALS;
        global $DB;

        if (!array_key_exists('member', $_GLOBALS)) {
            return false;
        }

        if ($row = $DB->query_first(sprintf(
            "SELECT COUNT(*) AS `total` FROM `highwaygroup`.`serial_numbers` WHERE `member_id` = %d LIMIT 1",
            $_GLOBALS['member']['id']
        ), MYSQLI_ASSOC)) {
            if ($row['total'] > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @deprecated Replace Prooduct with Product
     *
     * @return bool
     */
    public function isProoductAvailableByRule()
    {
        return $this->isProductAvailableByRule();
    }

    /**
     * @return bool
     */
    public function isProductAvailableByRule()
    {
        $response = false;
        switch ($this->orderRule()) {
            case 'inquiry_for_everyone':
                $response = true;
                break;

            case 'invoice_for_everyone':
                $response = true;
                break;

            case 'invoice_only_for_restricted_owners':
                $response = $this->is_rule_available_for_restricted_user();
                break;

            case 'invoice_only_for_arcooda_machine_owners':
                $response = self::is_arcooda_customer();
                break;

            default:
                $response = true;
                break;
        }
        return $response;
    }

    /**
     * @param mixed[] $product
     *
     * @return string
     */
    private static function SubProductPrice($product)
    {
        if (self::is_arcooda_customer()) {
            return $product['sales_price_arcooda'];
        }

        return $product['sell_price'];
    }

    /**
     * @param mixed[] $product
     *
     * @return string
     */
    public function SubProductSalesPrice($product)
    {
        $response = self::SubProductPrice($product);
        if ('y' == $product['special_flag'] && date('Y-m-d', strtotime($product['special_expiry']) <= date('Y-m-d')) && $response > $product['special_priceus']) {
            $response = $product['special_priceus'];
        }
        return $response;
    }

    /**
     * @return mixed[][]
     */
    public function getSubProduct()
    {
        $return_data = [];
        if (self::SECTION_ARCADE_MACHINES == $this->__get('section')) {
            if ($this->isProductAvailableByRule()) {
                $subproducts = $this->subproducts();
                if (count($subproducts)) {
                    $defaultkey = ($_GET['subproduct'] ?? false) ? $_GET['subproduct'] : (array_search('y', array_column($subproducts, 'default', 'id')));
                    foreach ($subproducts as $key => $pro) {
                        $temp                      = [];
                        $temp['id']                = $pro['id'];
                        $temp['name']              = ucfirst($pro['name']);
                        $temp['default']           = $defaultkey;
                        $temp['price']             = self::SubProductPrice($pro);
                        $temp['sales_price']       = $this->SubProductSalesPrice($pro);
                        $temp['sale_discount_msg'] = (self::is_arcooda_customer()) ? 'Member Discount' : 'Discount';
                        array_push($return_data, $temp);
                    }
                }
            }
        }
        return $return_data;
    }

    /**
     * @return mixed[][]
     */
    public function getSubProductIncluded()
    {
        $return_data = [];
        if (self::SECTION_ARCADE_MACHINES == $this->__get('section')) {
            $subproducts = $this->subproducts();
            if (count($subproducts)) {
                $defaultkey = ($_GET['subproduct'] ?? false) ? $_GET['subproduct'] : (array_search('y', array_column($subproducts, 'default', 'id')));
                foreach ($subproducts as $key => $pro) {
                    $temp                      = [];
                    $temp['id']                = $pro['id'];
                    $temp['name']              = ucfirst($pro['name']);
                    $temp['default']           = $defaultkey;
                    $temp['price']             = self::SubProductPrice($pro);
                    $temp['sales_price']       = $this->SubProductSalesPrice($pro);
                    $temp['sale_discount_msg'] = (self::is_arcooda_customer()) ? 'Member Discount' : 'Discount';
                    array_push($return_data, $temp);
                }
            }
        }
        return $return_data;
    }

    public function image_file()
    {
        moduleLoad('product');

        $test = productGetOptionList('image_path') . '/' . $this->__get('image');
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
        return $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/product/image/';
    }

    /**
     * Path to the thumb files, including trailing slash
     *
     * @return string
     */
    public static function thumb_path()
    {
        return $_SERVER['DOCUMENT_ROOT'] . '/webadmin/uploads/product/thumb/';
    }

    /** @var mixed[]|null */
    private $_product;

    /** @var mixed[]|null */
    private $_monitor;

    public function parturl()
    {
        return sprintf(
            '/parts/%s-%d',
            urlencode($this->__get('slug') ?: urlencode(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $this->__get('name')), '-'))),
            $this->id
        );
    }

    /**
     * TODO: Remove the float conversion, and use locale-correct formatting
     * TODO: Fix typo in function name
     *
     * @param string $price
     *
     * @return string
     */
    public function getbulckprice($price)
    {
        return number_format((float) $price, 2, '.', '');
    }

    public function checkpreorder()
    {
        if($this->__get('available') < 0 && $this->arrival_date > date('Y-m-d')){
                return 'Pre Order';
            }else{
                return null;
            }
    }

    public function getvideoid($link){
        $video_id = explode("?v=", $link);
        if (empty($video_id[1]))
        $video_id = explode("/v/", $link);
        $video_id = explode("&", $video_id[1]); // Deleting any other params
        $video_id = $video_id[0];
        return $video_id;
    }

    public function full_payment()
    {
        return $this->full_payment;
    }

     public function cubic_weight()
    {
        return $this->cubic_weight;
    }
     /**
     * @fixme replace arcoda with arcooda
     *
     * @return string
     */
    public function bypass_freight()
    {
        return $this->__get('bypass_freight');
    }

    public function get_product_coupon(){
        global $_GLOBALS,$DB;
        $coupon=array();
        if ($query = $DB->query(sprintf(
                "SELECT count(*) as `count`, `value`,`coupon_type`,`product_id`,`section`, `active` , `description`, `name`, `expiry_date` FROM `highwaygroup`.`gift_coupon`  WHERE `product_id` =  %s AND (`expiry_date` = '0000-00-00 00:00:00' OR `expiry_date` > NOW()) AND `show_coupon` = '1' AND `active` = 'y' AND (`site_id` = '' OR `site_id` = %d)",
                $this->__get('id'),
                $_GLOBALS['site_settings']['site_id']
            ))) {
                while ($row = $DB->fetch_assoc($query)) { 
                    if($row['count']>0 && $row['active']=='y'){
                        switch($row['coupon_type']){
                            case 2: //Individual Product
                            moduleLoad('product'); //productFromDatabase
                            $invproducts=array_column($invoice_products,'product_id');
                            if(!in_array($row['product_id'], $invproducts)){
                                $product               = productFromDatabase($row['product_id']);
                                // $coupon['description'] = 'Please add Product <a href="'.$product['product_url'].'" style="color:red;text-decoration:underline;">'.$product['product_name'].'</a> in your cart to apply this coupon';
                                $coupon[]              = $row;
                                break;
                            }
                        }
                     }
                }
            }
        //     echo "<pre>";
        // print_r($coupon); die;
        return $coupon;
    }

    public function faq() {
        global $DB;
    
        $result = array();
        
        // Safely cast $this->id to an integer to prevent SQL injection
        $id = intval($this->id);

        // Modify the query to use FIND_IN_SET for both `productid` and `softwareid`
        $query = $DB->query(
            "SELECT * FROM `faq` 
             WHERE (FIND_IN_SET($id, `productid`) OR FIND_IN_SET($id, `softwareid`)) 
             AND `status` = 'y' 
             AND `permission` = 'owner' 
             ORDER BY `rank` ASC"
        );
    
        while ($row = $DB->fetch_assoc($query)) {
            $result[$row["category"]][$row["id"]] = $row;
        }
        $DB->free_result($query);
        return $result;
    }
    public function faqForEveryOne() {
        global $DB;
    
        $result = array();
        
        // Safely cast $this->id to an integer to prevent SQL injection
        $id = intval($this->id);

        // Modify the query to use FIND_IN_SET for both `productid` and `softwareid`
        $query = $DB->query(
            "SELECT * FROM `faq` 
             WHERE (FIND_IN_SET($id, `productid`) OR FIND_IN_SET($id, `softwareid`)) 
             AND `status` = 'y' 
             AND `permission` = 'every' 
             ORDER BY `rank` ASC"
        );
    
        while ($row = $DB->fetch_assoc($query)) {
            $result[$row["category"]][$row["id"]] = $row;
        }
        $DB->free_result($query);
        return $result;
    }
    public function faqCategory() {
        global $DB;
    
        $result = array();
        
        // Safely cast $this->id to an integer to prevent SQL injection
        $id = intval($this->id);

        // Modify the query to use FIND_IN_SET for both `productid` and `softwareid`
        $query = $DB->query(
            "SELECT * FROM `faq_category` 
             ORDER BY `rank` ASC"
        );
        while ($row = $DB->fetch_assoc($query)) {
            $result[$row["id"]] = $row['name'];
        }
        $DB->free_result($query);
        return $result;
    }

    public function isReviewExist()
    {
        moduleLoad('product');
        return isProductReviewExist($this->id);
    }

    public function isProductPurchased()
    {
        global $DB,$_GLOBALS;
        if ($row = $DB->query_first(sprintf(
            "SELECT count(products.product_id) as total from highwaygroup.invoice_products AS products INNER JOIN highwaygroup.invoices AS invoice ON products.invoice_id = invoice.invoice_id  WHERE invoice.invoice_status IN ('processed', 'completed') AND invoice.member_id = %d AND  products.product_id = %d LIMIT 1",
            $_GLOBALS['member']['id'],
            $this->id
        ), MYSQLI_ASSOC)) {
            if ($row['total'] > 0) {
                return true;
            }
        }
       return false;
    }

    public function getSupportLinks(){
        moduleLoad('product');

        return getEveryoneSupportLinks($this->id);
    }

    public function getGiftProducts()
    {
        moduleLoad('product');

        $result = [];
        $gift_products = [];
        $gift_product_search = [];
         if(!$this->__get('available') < 0 || $this->arrival_date < date('Y-m-d') || $this->free_gifts() == 'n' ||  $this->bonus() == 'n'){
            if(($this->gift_promotion_expiry_date >= date('Y-m-d') || $this->gift_promotion_expiry_date == NULL) && $this->free_gifts_product == "y") {
                    foreach (productGenericsFromDatabase($this->id, '`products_free_gifts`', 'id', 'product_id', 'ORDER BY `online` DESC') as $v) {
                            $result[] = [
                                'id'       => $v['linked_id'],
                                'name'     => $v['name'],
                                'color_id' => $v['color_id'],
                                // 'price'    => number_format((float)($v['price']) , 2, '.', ''),
                               'price'    => number_format((float) (isset($v['online']) ? $v['online'] : $v['secondary_price']), 2, '.', ''),
                                'cost' => number_format((float)($v['cost']), 2, '.', ''),
                                'qty'  => $v['qty'],
                                // 'offer'  => $v['offer'],
                                 'offer'  => $v['price'],
                                'product'  => new TwigProduct($v['linked_id']),
                            ];
                    }
            }else{
              return $result;  
            }
            
        }else{
            foreach (productGenericsFromDatabase($this->id, '`products_gifts`', 'id', 'product_id', 'ORDER BY `online` DESC') as $v) {
                     $result[] = [
                        'id'       => $v['linked_id'],
                        'name'     => $v['name'],
                        'color_id' => $v['color_id'],
                        //'price'    => number_format((float)($v['price']) , 2, '.', ''),
                       'price'    => number_format((float) (isset($v['online']) ? $v['online'] : $v['secondary_price']), 2, '.', ''),
                        'cost' => number_format((float)($v['cost']), 2, '.', ''),
                        'qty'  => $v['qty'],
                        //'offer'  => $v['offer'],
                        'offer'  => $v['price'],
                        'product'  => new TwigProduct($v['linked_id']),
                    ];
            }
        }
        if (empty($result)) {
            return $result;
        }

        /*foreach (TwigProduct::search(['id' => $gift_product_search]) as $v) {
            $result[] = [
                'id'       => $v->__get('id'),
                'name'     => $v->__get('name'),
                'price'    => number_format((float) (($gift_products[$v->__get('id')]['price']) ? $gift_products[$v->__get('id')]['price'] : $v->__get('secondary_price')), 2, '.', ''),
                'cost' => number_format((float)($gift_products[$v->__get('id')]['cost']), 2, '.', ''),
                'qty'  => $gift_products[$v->__get('id')]['qty'],
                'offer'  => $gift_products[$v->__get('id')]['offer'],
                'product'  => $v,
            ];
        }*/
        usort($result, function ($a, $b) {
            return $b['price'] <=> $a['price'];
        });
        return $result;
    }
    public function pre_order_close_date()
    {
        return ($this->__get('pre_order_close_date') != '0000-00-00')?$this->__get('pre_order_close_date'):NULL ;
    }
     public function promotion_gift_close_date()
    {
        return ($this->__get('gift_promotion_expiry_date') != '0000-00-00') ? $this->__get('gift_promotion_expiry_date'):NULL ;
    }
    public function is_promotion_gift_available()
    {
        $returnval = false;
        if($this->__get('available') < 0 && $this->pre_order_close_date() >= date('Y-m-d') && (($this->free_gifts() == 'y' &&  $this->bonus() == 'y') || $this->bonus_discount() == 'y' || $this->free_freight() == 'y') ){
            return  true;
        }
        return ($this->__get('free_gifts_product') == 'y' && ($this->promotion_gift_close_date() >= $this->todayDate || $this->__get('gift_promotion_expiry_date') == '0000-00-00') ) ? true:false;
    }
    public function bonus()
    {
        return ($this->__get('bonus') == 'y' && ($this->pre_order_close_date() >= $this->todayDate || $this->pre_order_close_date() == NULL) && $this->checkpreorder() == 'Pre Order') ? 'y':'n';
    }
    public function free_gifts()
    {
        return ($this->__get('free_gifts') == 'y' && ($this->pre_order_close_date() >= $this->todayDate || $this->pre_order_close_date() == NULL) ) ? 'y':(($this->__get('free_gifts_product') == 'y' && ($this->promotion_gift_close_date() > $this->todayDate || $this->__get('gift_promotion_expiry_date') == '0000-00-00') )?'y':'n');
    }
    public function free_freight()
    {
        return ($this->__get('free_freight') == 'y' && ($this->pre_order_close_date() >= $this->todayDate || $this->pre_order_close_date() == NULL) ) ? 'y':'n';
    }
    public function display_comment()
    {
        return ($this->__get('display_comment') == 'y' && ($this->pre_order_close_date() >= $this->todayDate || $this->pre_order_close_date() == NULL) ) ? 'y':'n';
    }
    public function display_comment_description()
    {
        return $this->__get('display_comment_description');
    }

    public function bonus_discount()
    {
       return ($this->bonus() == 'y' &&  $this->__get('bonus_discount') == 'y') ? 'y':'n';
    }
    public function bonus_discount_amount()
    {
       return $this->__get('bonus_discount_amount');
    }
    public function active()
    {
       return $this->__get('active');
    }
    public function monitor_type()
    {
       return $this->__get('monitor_type');
    }
    public function monitor_resolution()
    {
       return $this->__get('monitor_resolution');
    }
    public function monitor_ratio()
    {
       return $this->__get('monitor_ratio');
    }
    public function getAddOnProducts(){
        $returnPro = [];
        foreach (productAddonsFromDatabase($this->id) as $item){
            $productObject = new TwigProduct($item['related_id']);
            if($productObject->active() == 'y'){
                $returnPro[] = [
                    'id' => $item['id'],
                    'addontext' => $item['addontext'],
                    'is_free' => $item['is_free'],
                    'product' => $productObject
                ];
            }  

        }
        usort($returnPro, function ($a, $b) {
            return ($b['is_free'] === 'y') <=> ($a['is_free'] === 'y');
        });
        return $returnPro; 
    }
    
    private $monitoOptions;
    public function monitoOptions()
    {
         if (!is_null($this->monitoOptions)) {
            return $this->monitoOptions;
        }

        $this->monitoOptions = new TwigMonitorOptions($this->id);
        return $this->monitoOptions;
    }

    public function productGetOptionListData($option){
        return productGetOptionList($option);
    }

    public function factory_second()
    {
        return $this->__get('factory_second'); 
    }
    public function factory_second_description()
    {
        return $this->__get('factory_second_description'); 
    }
    public function integrated_speakers()
    {
        return $this->__get('integrated_speakers'); 
    }
    public function speakers_support_type()
    {
        return $this->__get('speakers_support_type'); 
    }
    public function integrated_speakers_comment()
    {
        return $this->__get('integrated_speakers_comment'); 
    }
    private $images_second_image;
    public function productSecondImage()
    {
        if (!is_null($this->images_second_image)) {
            return $this->images_second_image;
        }

        moduleLoad('product'); //productScreenshotsFromDatabase

        $this->images_second_image = [];

        foreach (productSecondScreenshotsFromDatabase($this->id) as $v) {
            if (!empty($v['image_url'] ?? '')) {
                $this->images_second_image[] = new TwigImage([
                    'src'      => '/ss/image-' . $v['ss_id'] . '.jpg',
                    'alt'      => $v['caption'],
                    'src_file' => productGetOptionList('screenshot_path') . $v['filename'],
                ]);
            }
        }

        return $this->images_second_image;
    }

    public function getMonitorBracket(){
        global $DB;
    
        $returnPro = array();
        
        // Safely cast $this->id to an integer to prevent SQL injection
        $id = intval($this->id);
        // Modify the query to use FIND_IN_SET for both `productid` and `softwareid`
        $query = $DB->query(
            "SELECT * FROM `add_on_bracket` 
             WHERE  product_id = ".$this->id." AND `deleted_at` IS NULL
             ORDER BY `main` ASC"
        );
    
        while ($row = $DB->fetch_assoc($query)) {
            $productObject = new TwigProduct($row['related_id']);
            if($productObject->active() == 'y'){
                $returnPro[] = [
                    'id' => $row['id'],
                    'addontext' => $row['addontext'],
                    'is_free' => $row['is_free'],
                    'main' => $row['main'],
                    'product' => $productObject
                ];
            } 
        }
        $DB->free_result($query);
        return $returnPro;
    }

    public function getbracketdiscount(){
          return [
            'backet_discunt_active' => $this->backet_discunt_active,
            'backet_discunt_enable' => $this->backet_discunt_enable,
            'backet_discunt_value' => $this->backet_discunt_value,
            'bracket_discount_main' => $this->bracket_discount_main

          ] ;
    }
}
