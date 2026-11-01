<?php
namespace app\models;

use app\models\TwigCart;
use app\models\TwigCategory;
use app\models\TwigCurrency;
use app\models\TwigPage;
use app\models\TwigProduct;
use app\models\TwigUser;
//use cebe\markdown\GithubMarkdown;
//use cebe\markdown\Markdown;
//use cebe\markdown\MarkdownExtra;
//use Michelf\Markdown;
//use Michelf\MarkdownExtra;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use Parsedown;
use ParsedownExtra;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\SandboxExtension;
use Twig\Sandbox\SecurityPolicy;
use Twig\TwigFilter;
use Twig\TwigFunction;

class MyTwig
{
    /**
     * @var ?\Twig\Environment $_twig
     */
    private $_twig = null;

    public function __construct()
    {
        $twig = new Environment(new FilesystemLoader(__DIR__ . '/../views'), [
            'debug'      => true,
            'autoescape' => false,

            //'cache' => __DIR__ . '/../../tmp',
            //'debug' => false,
        ]);

        $twig->addFunction(new TwigFunction('classnames', [$this, 'func_classnames']));
        $twig->addFunction(new TwigFunction('data', [$this, 'func_data']));
        $twig->addFunction(new TwigFunction('image', [$this, 'func_image']));
        $twig->addFunction(new TwigFunction('svg', [$this, 'func_svg']));
        $twig->addFunction(new TwigFunction('svg_inline', [$this, 'func_svg_inline']));
        $twig->addFunction(new TwigFunction('socials', [$this, 'func_socials']));
        $twig->addFunction(new TwigFunction('countries', [$this, 'func_countries']));
        $twig->addFunction(new TwigFunction('currencies', [$this, 'func_currencies']));
        $twig->addFunction(new TwigFunction('user', [$this, 'func_user']));
        $twig->addFunction(new TwigFunction('pref', [$this, 'func_pref']));
        $twig->addFunction(new TwigFunction('cart', [$this, 'func_cart']));
        $twig->addFunction(new TwigFunction('quote_cart', [$this, 'func_quote_cart']));
        $twig->addFilter(new TwigFilter('currency', [$this, 'func_currency']));
        $twig->addFilter(new TwigFilter('markdown', [$this, 'func_markdown']));
        $twig->addFunction(new TwigFunction('markdown', [$this, 'func_markdown']));

        $twig->addFilter(new TwigFilter('truncate', [$this, 'func_truncate']));
        $twig->addFunction(new TwigFunction('truncate', [$this, 'func_truncate']));
        $twig->addFunction(new TwigFunction('product_det', [$this, 'func_product_det']));
        $twig->addFunction(new TwigFunction('header_text', [$this, 'func_header_text']));
        $twig->addFunction(new TwigFunction('season_graphics', [$this, 'func_season_graphics']));
        $twig->addFunction(new TwigFunction('comWisCount', [$this, 'func_comWisCount']));
        $twig->addFunction(new TwigFunction('getvideoid', [$this, 'func_getvideoid']));
        $twig->addFunction(new TwigFunction('getusercurrency', [$this, 'GetUserCurrency']));
        $twig->addFunction(new TwigFunction('getsocialIcon', [$this, 'func_getsocialIcon']));
        $twig->addFunction(new TwigFunction('getmembercurrencyconverter', [$this, 'func_getmembercurrencyconverter']));
        $twig->addFunction(new TwigFunction('allowInquirySectionOnMobile', [$this, 'func_allowInquirySectionOnMobile']));
        $twig->addFunction(new TwigFunction('getvisitorcountry', [$this, 'func_getvisitorcountry']));
        $twig->addFunction(new TwigFunction('getOwnerSection', [$this, 'func_getOwnerSection']));
        $twig->addFunction(new TwigFunction('checkOnlineDisplayToCustomer', [$this, 'func_checkOnlineDisplayToCustomer']));
        $twig->addFunction(new TwigFunction('getCountryName', [$this, 'func_getCountryName']));
        $twig->addFilter(new TwigFilter('getCountryName', [$this, 'func_getCountryName']));
        $twig->addFunction(new TwigFunction('preOrderProduct', [$this, 'func_preOrderProduct']));
        $twig->addFunction(new TwigFunction('salesProduct', [$this, 'func_SalesProduct']));
        $twig->addFunction(new TwigFunction('secondProduct', [$this, 'func_SecondProduct']));
        $twig->addFunction(new TwigFunction('isClearanceAllow', [$this, 'func_isClearanceAllow']));
        /**
         * Apply security policy to debug template features
         */
        /*
        $twig->addExtension(new SandboxExtension(new SecurityPolicy(
            ['spaceless', 'set', 'include', 'for', 'if', 'block', 'from', 'macro'],
            ['default', 'escape', 'slice', 'batch', 'currency', 'first'],
            [],
            [],
            ['classnames', 'data', 'image', 'svg', 'range']
        ), true));
        */

        $this->_twig = $twig;
    }

    /**
     * @param  string $file
     * @return \Twig\TemplateWrapper
     */
    public function load($file)
    {
        return $this->_twig->load($file);
    }

    /**
     * classnames - just combines a list of css class names
     *
     * @param  mixed $args,...
     *
     * @return string
     */
    public function func_classnames(...$args)
    {
        $result = [];
        foreach ($args as $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($v2) {
                        $result[] = $k2;
                    }
                }
            }
            if (is_string($v)) {
                $result[] = $v;
            }
        }
        return trim(implode(' ', array_unique($result)));
    }
     public function func_getvideoid($link){
        $video_id = explode("?v=", $link);
        if (empty($video_id[1]))
        $video_id = explode("/v/", $link);
        $video_id = explode("&", $video_id[1]); // Deleting any other params
        $video_id = $video_id[0];
        if($video_id)
        return $video_id;
        else
        return $link;
    }
    /**
     * data - fetches data for the template
     *
     * @param  string $file
     *
     * @return mixed
     */
    public function func_data($file)
    {
        global $_GLOBALS,$DB;
        $data = [];

        if ('language-list.json' == $file) {
            //$data = getLanguageList();
            $data = [
                'EN' => 'English',
                //'CN' => 'Chinese',
            ];
        } elseif ('header-main-menu.json' == $file) {
            $query = $DB->query(sprintf(
                "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s WHERE `status`='y' %s",
                '`front_top_menu`',
                'ORDER BY `rank` ASC'
            ));
            while ($row = $DB->fetch_assoc($query)) {
                     $data[] = ['title' => $row['title'], 'url' => $row['url'], 'external' => $row['navigation']];
            }
        } elseif ('shop-products.json' == $file && moduleLoad('product')) {
            $data = TwigProduct::search();
        } else if ('header-departments.json' == $file) {
            $data = [];
            foreach (TwigCategory::search(['parent' => 0]) as $v1) {
                if ($v1->products()) {
                //if (categorySubCount($v1->id)) {
                    $department = [
                        'title'   => $v1->name(),
                        'url'     => $v1->url(),
                        'submenu' => [
                            'type'    => 'megamenu', //menu, megamenu
                            'size'    => 'xl', //sm, nl, md, lg, xl
                            'columns' => [],
                        ],
                    ];
                    if ($v1->image()) {
                        $department['submenu']['image'] = $v1->image();
                    }

                    $chunks       = [];
                    $chunks_sizes = [];
                    foreach (TwigCategory::search(['parent' => $v1->id]) as $v2) {
                        if ($v2->products()) {
                        //if (categorySubCount($v2->id)) {
                            $links = [
                                'title' => $v2->name(),
                                'url'   => $v2->url(),
                            ];

                            $sub_links = [];
                            foreach (TwigCategory::search(['parent' => $v2->id]) as $v3) {
                                if ($v3->products()) {
                                //if (categorySubCount($v3->id)) {
                                    $sub_links[] = [
                                        'title' => $v3->name(),
                                        'url'   => $v3->view_url(),
                                    ];
                                }
                            }

                            if (count($sub_links)) {
                                $links['links'] = $sub_links;
                            }

                            $chunks[]       = $links;
                            $chunks_sizes[] = count($sub_links) + 1;
                        }
                    }

                    $split_size = (array_sum($chunks_sizes) + 2) / 3;
                    $count      = 0;
                    $links      = [];
                    foreach ($chunks as $k2 => $v2) {
                        if ($chunks_sizes[$k2] + $count <= $split_size) {
                            $links[] = $v2;
                            $count += $chunks_sizes[$k2];
                        } else {
                            $department['submenu']['columns'][] = [
                                'size'  => '4',
                                'items' => [
                                    [
                                        'type'  => 'links',
                                        'links' => $links,
                                    ],
                                ],
                            ];
                            $links = [$v2];
                            $count = $chunks_sizes[$k2];
                        }
                    }
                    if ($count) {
                        $department['submenu']['columns'][] = [
                            'size'  => '4',
                            'items' => [
                                [
                                    'type'  => 'links',
                                    'links' => $links,
                                ],
                            ],
                        ];
                    }

                    $data[] = $department;
                }
            }
        } elseif ('footer-links.json' == $file) {
            $data  = [];
            $pages = TwigPage::search(['name' => 'public_footer']);
            if (count($pages)) {
                $data = reset($pages)->children();
            }
        } else {
            $data = json_decode(file_get_contents(__DIR__ . '/../data/' . $file), true);
        }

        if ('mobile-menu.json' == $file) {
            moduleLoad('category'); //categoryIsParent
            $data['items'][4]['items'] = [];
            foreach (TwigCategory::search(['parent' => 0]) as $v1) {
                if ($v1->products()) {
                //if (categorySubCount($v1->id)) {
                    $items = [];
                    foreach (TwigCategory::search(['parent' => $v1->id]) as $v2) {
                        if ($v2->products()) {
                        //if (categorySubCount($v2->id)) {
                            $items[] = [
                                'title' => $v2->name(),
                                'url'   => $v2->url(),
                            ];
                        }
                    }

                    $data['items'][4]['items'][] = [
                        'title' => $v1->name(),
                        'url'   => $v1->url(),
                        'panel' => [
                            'title' => $v1->name(),
                            'items' => [['type' => 'links', 'items' => $items]],
                        ],
                    ];
                }
            }

            // $data['items'][4]['items'][] = [
            //                 'title' => 'News',
            //                 'url'   => '/arcade-news',
            //                 'type' => 'links'
            // ];
            // $data['items'][4]['items'][] = [
            //                 'title' => 'Product Builder',
            //                 'url'   => '/product-builder.html',
            //                 'type' => 'links'
            // ];
            // $data['items'][4]['items'][] = [
            //                 'title' => 'jobs',
            //                 'url'   => 'https://www.highwaygroup.net/jobs/',
            //                 'type' => 'links'
            // ];
            // $data['items'][4]['items'][] = [
            //                 'title' => 'Join us',
            //                 'url'   => '/join',
            //                 'type' => 'links'
            // ];
            // $data['items'][4]['items'][] = [
            //                 'title' => 'Contact us',
            //                 'url'   => '/contact-us',
            //                 'type' => 'links'
            // ];

            // Code updated by swapnil

            $query = $DB->query(sprintf(
                "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s WHERE `status`='y' %s",
                '`front_top_menu`',
                'ORDER BY `rank` ASC'
            ));

            while ($row = $DB->fetch_assoc($query)) {
                if(categoryIsParent($row['id']))
                {
                    if($row['title'] == 'Product Builder' || $row['title'] == 'Jobs')
                    {
                        $data['items'][4]['items'][] = [
                            'title' => $row['title'],
                            'url'   => $row['url'],
                            'type' => 'links'
                        ];
                    }

                } else {
                    if($row['title'] != 'Merchandise')
                    {
                        $data['items'][4]['items'][] = [
                            'title' => $row['title'],
                            'url'   => $row['url'],
                            'type' => 'links'
                        ];
                    }
                    
                }
                
            }
            //end
            unset($data['items'][1]);
            unset($data['items'][0]);
        }

        return $data;
    }

    /**
     * image - returns the correct url for an image
     *
     * @param string  $image
     * @param mixed[] $options
     *
     * @return string
     */
    public function func_image($image, $options = [])
    {
        global $_GLOBALS;

        $options = array_merge([
            'path' => $image,
            'size' => null,
            'webp' => false,
        ], $options);

        if (strpos($image, ':') !== false || substr($image, 0, 1) === '/') {
            if (!is_null($options['size'])) {
                $params = [];
                if (!is_array($options['size'])) {
                    $params['size'] = $options['size'];
                } else {
                    if (is_numeric($options['size'][0])) {
                        $params['width'] = $options['size'][0];
                    }
                    if (is_numeric($options['size'][1])) {
                        $params['height'] = $options['size'][1];
                    }
                }
                //$params[rand()]=rand().time();
                return relative_url('?' . http_build_query($params), $image);
            }

            if (!is_null($options['size']) && (
                (is_array($options['size']) && $options['size'][0] <= 96 && $options['size'][1] <= 96) ||
                $options['size'] <= 96
            )) {
                $image = str_replace('large', 'thumb', $image);
            }
            return $image;
        }

        $filename = explode('.', substr($image, 7));
        $ext      = array_pop($filename);
        $filename = implode('.', $filename);

        if ($filename) {
            $newFilename = '/img/' . $filename;
            if ($options['size']) {
                if (is_array($options['size'])) {
                    $newFilename .= '-' . $options['size'][0] . 'x' . $options['size'][1];
                } else {
                    $newFilename .= '-' . $options['size'] . 'x' . $options['size'];
                }
            }
            $newFilename .= '.' . $ext;

            return $newFilename;
        } else {
            return $_GLOBALS['site_options']['DEFAULT_IMAGE'];
        }
    }

    /**
     * svg - returns the html for a svg image
     *
     * @param string $file
     *
     * @return string
     */
    public function func_svg($file)
    {
        $content = file_get_contents(__DIR__ . '/../../svg/' . $file);
        //Note: external svg won't inherit the fade colors...
        //return $content;
        if (preg_match('/width="([0-9]+)(px)?"/', $content, $w) && preg_match('/height="([0-9]+)(px)?"/', $content, $h)) {
            return '<img src="/svg/' . $file . '" style="vertical-align:top;" width="' . $w[1] . '" height="' . $h[1] . '" />';
        } else {
            return '<img src="/svg/' . $file . '" style="vertical-align:top;" />';
        }
    }

    /**
     * @param  string $file
     * @return string
     */
    public function func_svg_inline($file)
    {
        $content = file_get_contents(__DIR__ . '/../../svg/' . $file);
        return $content;
    }

    /**
     * currency - formats a number according to currency
     *
     * @param string|TwigCurrency $value
     * @param string $currency
     *
     * @return string
     */
    public static function func_currency($value)
    {
        $value = preg_replace('/(\\d)(\\d{3}\\.)/', '$1,$2', $value) ?? $value;
        $value = preg_replace('/(\\d)(\\d{3}\\,)/', '$1,$2', $value) ?? $value;
        $value = preg_replace('/\\.00\\b/', '', $value) ?? $value;

        return $value;
    }

    /**
     * markdown
     *
     * @param string $text
     *
     * @return string
     */
    public function func_markdown($text)
    {
        switch ($_GET['test'] ?? 'parsedownextra') {
            default:
            case 'parsedownextra': //Nicest <dl>, anchors, iframes
                $parsedown = ParsedownExtra::instance();
                return '<div class="markdown">' . $parsedown->setBreaksEnabled(true)->text($text) . '</div>';

            case 'markdownextra2': //Highlights <dd>, Anchors, iframes
                return '<div class="markdown">' . \Michelf\MarkdownExtra::defaultTransform($text) . '</div>';

            case 'parsedown': //Ignores <dl>, no anchors, iframes
                $parsedown = Parsedown::instance();
                return '<div class="markdown">' . $parsedown->setBreaksEnabled(true)->text($text) . '</div>';

            case 'markdown2': //No Anchors, iframes
                return '<div class="markdown">' . \Michelf\Markdown::defaultTransform($text) . '</div>';

            case 'markdownextra': //Has anchors, iframes
                $parser = new \cebe\markdown\MarkdownExtra();
                return '<div class="markdown">' . $parser->parse($text) . '</div>';

            case 'markdowngfc': //No anchors, iframes
                $parser = new \cebe\markdown\GithubMarkdown();
                return '<div class="markdown">' . $parser->parse($text) . '</div>';

            case 'markdown': //No anchors, iframes
                $parser = new \cebe\markdown\Markdown();
                return '<div class="markdown">' . $parser->parse($text) . '</div>';

            case 'commonmark': //No Anchors, iframes
                $converter = new CommonMarkConverter();
                return '<div class="markdown">' . $converter->convertToHtml($text) . '</div>';

            case 'commonmarkgfc': //No Anchors, no iframes
                $converter = new GithubFlavoredMarkdownConverter();
                return '<div class="markdown">' . $converter->convertToHtml($text) . '</div>';

            case 'pre':
                return '<div class="markdown">' . nl2br(htmlspecialchars($text)) . '</div>';
        }
    }

    /**
     * truncate
     *
     * @param string $text
     * @param int    $length
     * @param string $ellipse
     *
     * @return string
     */
    public function func_truncate($text, $length = 80, $ellipse = '...')
    {
        if (strlen($text) > $length) {
            return substr($text, 0, $length - strlen($ellipse)) . $ellipse;
        }
        return $text;
    }

    /*
    public function func_socials()
    {
        global $DB;
        $social = [];
        $query  = $DB->query('SELECT * FROM `social` WHERE `link` != ""');
        while ($row = $DB->fetch_assoc($query)) {
            $social[$row['name']] = $row;
        }
        $DB->free_result($query);
        return $social;
    }
    */
    public function func_header_text()
    {
        global $DB;
        $data=$DB->query_first("SELECT * FROM `site_front_page` WHERE `type` = 'header' AND `active`='y'  LIMIT 1", MYSQLI_ASSOC);
        if($data){
            return $data;
        }
        return false;
    }
    public function func_season_graphics(){
        global $DB;
        $date   = formatSQLDate();
        $result = [];
        $data=$DB->query_first("SELECT * FROM `season_graphics` WHERE $date between `start_date` and `end_date` AND `status`='y' ORDER BY created_at desc LIMIT 1", MYSQLI_ASSOC);
        $result['header_graphic']=false;
        $result['menu_graphic']=false;
        $result['footer_graphic']=false;
        if($data){
            $header_graphic=json_decode($data['header_graphic']);
            $result['header_graphic']=(is_array($header_graphic))?$header_graphic[array_rand($header_graphic,1)]:false;
            $menu_graphic=json_decode($data['menu_graphic']);
            $result['menu_graphic']=(is_array($menu_graphic))?$menu_graphic[array_rand($menu_graphic,1)]:false;
            $footer_graphic=json_decode($data['footer_graphic']);
            $result['footer_graphic']=(is_array($footer_graphic))?$footer_graphic[array_rand($footer_graphic,1)]:false;

        }
        return $result;
    }
    /**
     * @var ?array{code: string, name: string}[] $_func_countries
     */
    private $_func_countries = null;

    /**
     * @return array{code: string, name: string}[]
     */
    public function func_countries()
    {
        if (!is_null($this->_func_countries)) {
            return $this->_func_countries;
        }

        $this->_func_countries = [];
        foreach (getCountryList() as $k => $v) {
            $this->_func_countries[$k] = ['code' => $k, 'name' => explode('-',$v)[0]];
        }
        return $this->_func_countries;
    }

    /**
     * @var ?array{code: string, name: string}[] $_func_currencies
     */
    private $_func_currencies = null;

    /**
     * @return array{code: string, name: string}[]
     */
    public function func_currencies()
    {
        if (!is_null($this->_func_currencies)) {
            return $this->_func_currencies;
        }

        $this->_func_currencies = [];
        foreach (getCurrencyList() as $k => $v) {
            $this->_func_currencies[$k] = ['code' => $k, 'name' => $v];
        }

        return $this->_func_currencies;
    }

    /**
     * @var ?TwigUser $_func_user
     */
    private $_func_user = null;

    /**
     * @return ?TwigUser
     */
    public function func_user()
    {
        if (!is_null($this->_func_user)) {
            return $this->_func_user;
        }

        global $_GLOBALS;
        if (array_key_exists('member', $_GLOBALS)) {
            $this->_func_user = new TwigUser($_GLOBALS['member']['member_id']);
        }

        return $this->_func_user;
    }

    public function func_pref()
    {
        global $_GLOBALS;

        return $_GLOBALS['member']['mailing_list'] ?? 'n';
    }

    public function func_cart()
    {
        return new TwigCart('shopping');
        global $_GLOBALS;

        $data = [
            'items'          => [],
            'total'          => new TwigCurrency('0.00', 'USD', 2),
            'compareAtTotal' => new TwigCurrency('0.00', 'USD', 2),
        ];

        if (!isset($_GLOBALS['shopping_cart'])) {
            return $data;
        }

        foreach ($_GLOBALS['shopping_cart'] as $key => $v) {
            //$product         = TwigProduct::search(['id' => $item['product_id']]);
            $product = new TwigProduct($v['product_id']);
            $item    = [
                'product'        => $product,
                'qty'            => $v['product_qty'],
                'key'            => $key,
                'department'     => $product->base_type,
                'compareAtPrice' => $product->compareAtPrice(),
                'price'          => $product->price(),
                'compareAtTotal' => null,
                'total'          => null,
            ];
            if (!is_null($item['price'])) {
                $item['total'] = $item['price']->multiplied($item['qty']);
                $data['total'] = $data['total']->added($item['total']);
            } else {
                $data['total'] = new TwigCurrency();
            }
            if (!is_null($item['compareAtPrice'])) {
                $item['compareAtTotal'] = $item['compareAtPrice']->multiplied($item['qty']);
                $data['compareAtTotal']->added($item['compareAtTotal']);
            } else {
                $data['compareAtTotal'] = $data['compareAtTotal'] = new TwigCurrency();
            }
            $data['items'][] = $item;
        }

        $data['total']->setPrecision(2);
        $data['compareAtTotal']->setPrecision(2);

        return $data;
    }
    public function func_quote_cart()
    {
        return new TwigCart('quote');
        global $_GLOBALS;

        $data = [
            'items'          => [],
            'total'          => new TwigCurrency('0.00', 'USD', 2),
            'compareAtTotal' => new TwigCurrency('0.00', 'USD', 2),
        ];

        if (!isset($_GLOBALS['quote_cart'])) {
            return $data;
        }

        foreach ($_GLOBALS['quote_cart'] as $key => $v) {
            //$product         = TwigProduct::search(['id' => $item['product_id']]);
            $product = new TwigProduct($v['product_id']);
            $item    = [
                'product'        => $product,
                'qty'            => $v['product_qty'],
                'key'            => $key,
                'department'     => $product->base_type,
                'compareAtPrice' => $product->compareAtPrice(),
                'price'          => $product->price(),
                'compareAtTotal' => null,
                'total'          => null,
            ];
            if (!is_null($item['price'])) {
                $item['total'] = $item['price']->multiplied($item['qty']);
                $data['total'] = $data['total']->added($item['total']);
            } else {
                $data['total'] = new TwigCurrency();
            }
            if (!is_null($item['compareAtPrice'])) {
                $item['compareAtTotal'] = $item['compareAtPrice']->multiplied($item['qty']);
                $data['compareAtTotal']->added($item['compareAtTotal']);
            } else {
                $data['compareAtTotal'] = $data['compareAtTotal'] = new TwigCurrency();
            }
            $data['items'][] = $item;
        }

        $data['total']->setPrecision(2);
        $data['compareAtTotal']->setPrecision(2);
        return $data;
    }
    public function func_product_det($id){
        return new TwigProduct($id);
    }

    /**
     * @return array{compare:int,wishlist:int}
     */
    public static function func_comWisCount()
    {
        global $DB;
        global $_GLOBALS;

        $visitor_id = cartGetVisitorID();

        $result = [
            'compare'  => 0,
            'wishlist' => 0,
        ];

        if ($row = $DB->query_first(sprintf(
            "SELECT COUNT(`id`) AS `total` FROM compare_product WHERE `visitor_id` = %d LIMIT 1",
            $visitor_id
        ), MYSQLI_ASSOC)) {
            $result['compare'] = $row['total'];
        }

        //FIXME: visitor/member can't use the same table => id conflicts
        //$visitor_id = (isset($_GLOBALS['member']['member_id'])) ? $_GLOBALS['member']['member_id'] : cartGetVisitorID();

        if ($row = $DB->query_first(sprintf(
            "SELECT COUNT(*) AS `total` FROM wishlist_product WHERE `visitor_id` = %d LIMIT 1",
            $visitor_id
        ), MYSQLI_ASSOC)) {
            $result['wishlist'] = $row['total'];
        }

        return $result;
    }

    /**
     * @deprecated Use TwigCurrency or convertCurrency
     *
     * @return string
     */
    public static function func_getmembercurrencyconverter()
    {
        global $_GLOBALS;
        global $DB;

        if ($row = $DB->query_first(sprintf(
            "SELECT * FROM `highwaygroup`.`currency` where `code` = %s LIMIT 1",
            $DB->escape($_GLOBALS['member']['currency'] ?? 'USD')
        ), MYSQLI_ASSOC)) {
            return $row['usto'];
        }

        return '1.000000';
    }

    public static function func_getsocialIcon()
    {
        global $DB;

        $social = [];
        if ($query  = $DB->query(sprintf(
            "SELECT * FROM `social` WHERE `status` = 1 ORDER BY `rank` ASC"
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $social[$row['name']]['id']        = $row['id'];
                $social[$row['name']]['name']      = $row['name'];
                $social[$row['name']]['title']     = $row['title'];
                $social[$row['name']]['link']      = $row['link'];
                $social[$row['name']]['fafaicon']  = $row['fafaicon'];
                $social[$row['name']]['classname'] = $row['classname'];
            }
            $DB->free_result($query);
        }

        return $social;
    }

    public static function GetUserCurrency()
    {
        global $_GLOBALS;

        return $_GLOBALS['member']['currency'] ?? 'USD';
    }
    public function func_allowInquirySectionOnMobile(){
        $parsed_url = parse_url('https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
       
        return ($parsed_url['path']=='/' || $parsed_url['path']=='/contact-us')?1:0;
    }

    public function func_getvisitorcountry(){
        global $_GLOBALS;

        if (isset($_GLOBALS['member']) && isset($_GLOBALS['member']['ship_addr']) && is_array($_GLOBALS['member']['ship_addr'])) {
            $ship_addr = $_GLOBALS['member']['ship_addr'];
            $default_country = null;
        
            foreach ($ship_addr as $address) {
                if (isset($address['default']) && $address['default'] === 'y') {
                    $default_country = $address['country'];
                    break;
                }
            }
        
            if ($default_country !== null) {
                return $default_country;
            }
        }

        $ip  = getClientIP();
        $country = getCountryInfo($ip);
        return $country['a2'] ?? 'USA';
    }

    public function func_getOwnerSection(){
        global $_GLOBALS;
        global $DB;

        $products = [];

        if (array_key_exists('member', $_GLOBALS)) {
            if ($query = $DB->query(sprintf(
                "SELECT * FROM `highwaygroup`.`serial_numbers` WHERE `member_id` = %d GROUP BY `product_id`",
                $_GLOBALS['member']['id']
            ))) {
                while ($row = $DB->fetch_assoc($query)) {
                    $product = TwigProduct::search(['id' => $row['product_id']]);
                    if(count($product) > 0) {
                        $products[] = $product[0];
                    }
                }
                $DB->free_result($query);
            }
        }
        
        return $products;

    }

    public function func_checkOnlineDisplayToCustomer(){
        global $_GLOBALS;
        global $DB;
        $returnData = false;
         if (array_key_exists('member', $_GLOBALS)) {
             if ($row = $DB->query_first(sprintf(
                "SELECT count(invoice_id) as total FROM `highwaygroup`.`invoices` where `member_id` = %d AND `is_display_customer`=1 LIMIT 1",
                 $_GLOBALS['member']['id']
            ), MYSQLI_ASSOC)) {
                $returnData =  $row['total'];
            }
            $parsed_url = parse_url('https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
            if ($parsed_url['path'] ==  '/account/dashboard') {
               $DB->query(sprintf(
                    "UPDATE   `highwaygroup`.`invoices` set `is_display_customer`=0 where `member_id` = %d AND `is_display_customer`=1",
                     $_GLOBALS['member']['id']
                )); 
            }
         }
        return $returnData;
    }
    public function func_getCountryName($countryCode){
        return getCountryName($countryCode);
    }
    public function func_preOrderProduct()
    {
        static $cachedpreOrderProducts = null;

        // If already fetched once, return cached result
        if ($cachedpreOrderProducts !== null) {
            return $cachedpreOrderProducts;
        }

        // Otherwise fetch and store
        $criteria = [
            'active'    => 'y',
            'pre_order' => '-1'
        ];

        $cachedpreOrderProducts = TwigProduct::search($criteria);

        return $cachedpreOrderProducts;
    }

    public function func_SalesProduct()
    {
        static $cachedSalesProducts = null;

        // If already fetched once, return cached result
        if ($cachedSalesProducts !== null) {
            return $cachedSalesProducts;
        }

        // Otherwise fetch and store
        $criteria = [
            'active'    => 'y',
            'special_flag' => 'y',
            'special_expiry' => true
        ];

        $cachedSalesProducts = TwigProduct::search($criteria);

        return $cachedSalesProducts;
    }

     public function func_SecondProduct()
    {
        static $cachedSecondProducts = null;

        // If already fetched once, return cached result
        if ($cachedSecondProducts !== null) {
            return $cachedSecondProducts;
        }

        // Otherwise fetch and store
        $criteria = [
            'active'    => 'y',
            'factory_second' => 'yes'
        ];

        $cachedSecondProducts = TwigProduct::search($criteria);

        return $cachedSecondProducts;
    }

    public function func_isClearanceAllow(){
        global $DB;
         $item = $DB->query_first("SELECT active FROM `clearance_items` WHERE `id` = 1  LIMIT 1", MYSQLI_ASSOC);
        return  $item['active'] == 'y' ? true : false;
    }

}
