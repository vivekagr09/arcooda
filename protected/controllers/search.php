<?php
namespace app\controllers;

use app\models\TwigCategory;
use app\models\TwigCurrency;
use app\models\TwigNews;
use app\models\TwigProduct;
use app\models\TwigDistributor;
//Expects:
// $action

switch ($action) {
    case 'category':
        moduleLoad('product'); //productFromDatabase

        global $_GLOBALS;
        if (!empty($_POST)) {
            $product_qty = 0;
            $product_id  = $_POST['product_id'];
            if (isset($_POST['product_qty']) && empty($_POST['product_qty']) === false) {
                $product_qty = intval($_POST['product_qty']);
            }
            if (productExists($product_id)) {
                $product               = productFromDatabase($product_id);
                $product['product_id'] = $product['id'];

                // Hack:
                // sub_id, back_qty, power_id, adapter_id not exist in product
                // But, used in cart. Throwing errors.
                $product['sub_id']     = 0;
                $product['back_qty']   = 0;
                $product['power_id']   = 0;
                $product['adapter_id'] = 0;

                if ('n' != $product['active']) {
                    $cart = $_GLOBALS['shopping_cart'];
                    if ($product_qty < 0) {
                        $cart = cartAddToContents($cart, $product, 0 - $product_qty);
                    }
                    if ($product_qty > 0) {
                        $cart = cartAddToContents($cart, $product, $product_qty);
                    }
                    if (0 == $product_qty) {
                        $cart = cartAddToContents($cart, $product, 1);
                    }
                    foreach ($cart as $cart_key => $cart_item) {
                        $min_order = productGetValue($cart_item['product_id'], 'min_order');
                        if ($cart_item['product_qty'] < $min_order) {
                            $cart_item['product_qty']       = $min_order;
                            $cart[$cart_key]['product_qty'] = $min_order;
                        }
                        $cart[$cart_key]['product_offer']    = (string) (new TwigCurrency($product['sales_price'], $product['sales_currency'], $product['precision']));
                        $cart[$cart_key]['product_offer_us'] = $cart[$cart_key]['product_offer'];
                        $cart[$cart_key]['offer_currency']   = 'USD';
                    }
                    $_GLOBALS['shopping_cart'] = $cart;
                    cartSaveContents($_GLOBALS['member']['id'] ?? 0, $cart);
                }
            }
            redirect($_SERVER['REQUEST_URI']);
            die();
        }
        $criteria = [];
        if (array_key_exists('id', $_GET)) {
            $criteria['id'] = $_GET['id'];
        }
        if (array_key_exists('slug', $_GET)) {
            $criteria['slug'] = $_GET['slug'];
        }
        if ($temp = TwigCategory::search($criteria)) {
            $category               = reset($temp);
            $search_category_parent = $category->parent_id();
            $slug                   = $category->slug();
            $criteria               = [];
            $current_manufacturer   = '';
            $layout                 = 'grid'; //grid, grid-with-features, list, table
            // $sort                   = 'Latest Items';
            $sort                   = 'Product Name';
            //$show    = 15;
            $show    = (isset($_GET['show']) && !empty($_GET['show'])) ? intval($_GET['show']) : 32;
            $page    = 0;
            // $sort_by = 'id';
            // $order   = 'desc';
            $sort_by = 'product_name';
            $order   = 'asc';
            if (isset($_GET['sort'])) {
                $sort = $_GET['sort'];

                switch ($sort) {
                    case 'Product Name':
                        $sort_by = 'product_name';
                        $order   = 'asc';
                        break;

                    case 'Lowest Price':
                        $sort_by = 'sales_price';
                        $order   = 'asc';
                        break;

                    case 'Highest Price':
                        $sort_by = 'sales_price';
                        $order   = 'desc';
                        break;

                    default:
                        $sort_by = 'id';
                        $order   = 'desc';
                        break;
                }
            }
            // Apply filters
            foreach ([
                's'                 => 'any_text',
                'cl'                => 'country_location',
                'm'                 => 'manufacturer',
                'c'                 => 'condition',
                'coin_operated'     => 'coin_operated',
                'product_supported' => 'product_supported',
                'categories'        => 'categories',
                'mtype'             => 'monitor_type',
                'mresolution'       => 'monitor_resolution',
                'mratio'            => 'monitor_ratio',
                'integrated_speakers'  => 'integrated_speakers'
            ] as $k1 => $v1) {
                $temp = [];
                if (array_key_exists($k1, $_REQUEST)) {
                    if (is_array($_REQUEST[$k1])) {
                        foreach ($_REQUEST[$k1] as $v2) {
                            $temp[] = $v2;
                        }
                    } else {
                        $temp[] = $_REQUEST[$k1];
                    }
                }
                if (count($temp)) {
                    $criteria[$v1] = $temp;
                }
            }
            if (!array_key_exists('discontinued', $_GET)) {
               $criteria['discontinued'] = 'n';
            }
            // Sub-type expansion
            $criteria['type'][] = $category->id;
            $temp               = TwigCategory::list($criteria['type']);
            while (count($temp)) {
                foreach (array_shift($temp)->children() as $v) {
                    $temp[]             = $v;
                    $criteria['type'][] = $v->id;
                }
            }
            $criteria['type'] = array_unique($criteria['type']);

            if (isset($criteria['categories'])) {
                $criteria['type'] = $criteria['categories'];
            }

            // Master results list
            if (isset($_REQUEST['colour']) && count($_REQUEST['colour'])) {
                $criteria['color'] = $_REQUEST['colour'];
            }
            $price_detail = TwigProduct::price_detail($criteria);
            $max_price    = (int) $price_detail['max_price'];
            $min_price    = (int) $price_detail['min_price'];
            $to_price     = $max_price;
            $from_price   = $min_price;
            if ($_REQUEST['min_price'] ?? false) {
                $from_price            = $_REQUEST['min_price'];
                $criteria['min_price'] = $_REQUEST['min_price'];
            }
            if ($_REQUEST['max_price'] ?? false) {
                $to_price              = $_REQUEST['max_price'];
                $criteria['max_price'] = $_REQUEST['max_price'];
            }
            $criteria['active']='y';
            $products = TwigProduct::search($criteria);

            // Apply Pagination
            $total = count($products);
            if($total > 0) {
                /*if (array_key_exists('sort', $_REQUEST)) {
                $sort_by = [][$_REQUEST['sort']] ?? $sort_by;
                $order   = [][$_REQUEST['sort']] ?? $order;
                }*/
                if ('id' !== $sort_by || 'desc' !== $order) {
                    foreach ($products as $k => $v) {
                        $products[$k] = $v->id;
                    }
                    $products = TwigProduct::list(productSort($products, $sort_by));                    
                }
                if ('asc' !== $order) {
                    $products = array_reverse($products);
                }
                if (array_key_exists('show', $_REQUEST)) {
                    $show = min(112, max(1, intval($_REQUEST['show'])));
                }
                $products = array_chunk($products, $show);
                $pages    = count($products);
                if ($pages) {
                    if (array_key_exists('page', $_REQUEST)) {
                        $page = min(count($products) - 1, max(0, intval($_REQUEST['page'])));
                    }
                    $products = $products[$page];
                }
                $page_url = $_REQUEST;
                unset($page_url['page']);
                asort($page_url);
                $page_url['page'] = '';
                $page_url         = $category->url() . '?' . http_build_query($page_url);
                $filters          = [];
                $items            = [];
                $filter_url       = $_REQUEST;
                unset($filter_url['page']);
                $filter_url   = '?' . http_build_query($filter_url);
                $sub_criteria = $criteria;
                $temp         = $category;
                while ($temp->parent) {
                    $temp                 = $temp->parent();
                    $sub_criteria['type'] = $temp->id;
                    array_unshift($items, [
                        "type"  => "parent",
                        "count" => count(TwigProduct::search($sub_criteria)), //Note: Not the right number
                        "name" => $temp->name(),
                        "url"   => relative_url($filter_url, $temp->url()),
                    ]);
                }
                $items[] = [
                    'id'    => $category->id,
                    "type"  => "current",
                    "count" => $total,
                    "name"  => $category->name(),
                    "url"   => relative_url($filter_url, $category->url()),
                ];
                foreach ($category->children() as $v) {
                    $sub_criteria['type'] = $v->id;
                    $temp                 = TwigProduct::search($sub_criteria); //Note: Not the right number
                    if ($temp) {
                        $items[] = [
                            'id'      => $v->id,
                            'checked' => (array_key_exists('categories', $criteria) && in_array($v->id, $criteria['categories'])),
                            "type"    => "child",
                            "count"   => count(TwigProduct::search($sub_criteria)), //Note: Not the right number
                            "name"    => $v->name(),
                            "url"     => relative_url($filter_url, $v->url()),
                        ];
                    }
                }
                if (count($items)) {
                    $filters[] = [
                        "title"   => "Categories",
                        "type"    => "categories",
                        "options" => ['items' => $items],
                    ];
                }
                 if($category->id == 305){ 
                        $items        = [];
                        $sub_criteria = $criteria;
                        unset($sub_criteria['monitor_type']);
                        $temp = productGroupCount(productSearch($sub_criteria), 'monitor_type');
                        foreach (productGetOptionList('monitor_type') as $k => $v) {
                            if (array_key_exists($k, $temp)) {
                                $items[] = [
                                    'id'       => $k,
                                    'title'    => $v,
                                    'count'    => $temp[$k],
                                    'checked'  => (array_key_exists('monitor_type', $criteria) && in_array($k, $criteria['monitor_type'])),
                                    'disabled' => false,
                                    'name'     => 'mtype[]',
                                    'value'    => $k,
                                ];
                            }
                        }
                        if (count($items)) {
                            $filters[] = [
                                "title"   => "Monitor Type",
                                "type"    => "checkbox",
                                "options" => ['items' => $items],
                            ];
                        }

                        // monitor_resolution

                        $items        = [];
                        $sub_criteria = $criteria;
                        unset($sub_criteria['monitor_resolution']);
                        $temp = productGroupCount(productSearch($sub_criteria), 'monitor_resolution');
                        foreach (productGetOptionList('monitor_resolution') as $k => $v) {
                            if (array_key_exists($k, $temp)) {
                                $items[] = [
                                    'id'       => $k,
                                    'title'    => $v,
                                    'count'    => $temp[$k],
                                    'checked'  => (array_key_exists('monitor_resolution', $criteria) && in_array($k, $criteria['monitor_resolution'])),
                                    'disabled' => false,
                                    'name'     => 'mresolution[]',
                                    'value'    => $k,
                                ];
                            }
                        }
                        if (count($items)) {
                            $filters[] = [
                                "title"   => "Monitor Resolution",
                                "type"    => "checkbox",
                                "options" => ['items' => $items],
                            ];
                        }
                        //monitor_ratio 
                         $items        = [];
                        $sub_criteria = $criteria;
                        unset($sub_criteria['monitor_ratio']);
                        $temp = productGroupCount(productSearch($sub_criteria), 'monitor_ratio');
                        foreach (productGetOptionList('monitor_ratio') as $k => $v) {
                            if (array_key_exists($k, $temp)) {
                                $items[] = [
                                    'id'       => $k,
                                    'title'    => $v,
                                    'count'    => $temp[$k],
                                    'checked'  => (array_key_exists('monitor_ratio', $criteria) && in_array($k, $criteria['monitor_ratio'])),
                                    'disabled' => false,
                                    'name'     => 'mratio[]',
                                    'value'    => $k,
                                ];
                            }
                        }
                        if (count($items)) {
                            $filters[] = [
                                "title"   => "Monitor Ratio",
                                "type"    => "checkbox",
                                "options" => ['items' => $items],
                            ];
                        }
                        //
                        $items        = [];
                        $sub_criteria = $criteria;
                        unset($sub_criteria['integrated_speakers']);
                        $temp = productGroupCount(productSearch($sub_criteria), 'integrated_speakers');
                        foreach (['yes'=>'Yes','no'=>'No'] as $k => $v) {
                            if (array_key_exists($k, $temp)) {
                                $items[] = [
                                    'id'       => $k,
                                    'title'    => $v,
                                    'count'    => $temp[$k],
                                    'checked'  => (array_key_exists('integrated_speakers', $criteria) && in_array($k, $criteria['integrated_speakers'])),
                                    'disabled' => false,
                                    'name'     => 'integrated_speakers[]',
                                    'value'    => $k,
                                ];
                            }
                        }
                         if (count($items)) {
                            $filters[] = [
                                "title"   => "Integrated Speakers",
                                "type"    => "checkbox",
                                "options" => ['items' => $items],
                            ];
                        }
                }
                // price filter
                //
                //if ($category->parent) {
                $items        = [];
                $sub_criteria = $criteria;
                unset($sub_criteria['country_location']);
                $temp = productGroupCount(productSearch($sub_criteria), 'country_location');
                foreach (productGetOptionList('country_location') as $k => $v) {
                    if (array_key_exists($k, $temp)) {
                        $items[] = [
                            'id'       => $k,
                            'title'    => $v,
                            'count'    => $temp[$k],
                            'checked'  => (array_key_exists('country_location', $criteria) && in_array($k, $criteria['country_location'])),
                            'disabled' => false,
                            'name'     => 'cl[]',
                            'value'    => $k,
                        ];
                    }
                }
                if (count($items)) {
                    $filters[] = [
                        "title"   => "Country",
                        "type"    => "checkbox",
                        "options" => ['items' => $items],
                    ];
                }
                $items        = [];
                $sub_criteria = $criteria;
                unset($sub_criteria['manufacturer']);
                $temp = productGroupCount(productSearch($sub_criteria), 'manufacturer');
                foreach (productGetOptionList('manufacturer') as $k => $v) {
                    if (array_key_exists($k, $temp)) {
                        $items[] = [
                            'id'       => $k,
                            'title'    => $v,
                            'count'    => $temp[$k],
                            'checked'  => (array_key_exists('manufacturer', $criteria) && in_array($k, $criteria['manufacturer'])),
                            'disabled' => false,
                            'name'     => 'm[]',
                            'value'    => $k,
                        ];

                        if (isset($criteria['manufacturer'])) {
                            if ($k == $criteria['manufacturer'][0]) {
                                $current_manufacturer = $v . ' ';
                            }
                        }
                    }
                }
                if (count($items)) {
                    $filters[] = [
                        "title"   => "Manufacturer",
                        "type"    => "checkbox",
                        "options" => ['items' => $items],
                    ];
                }
                if ('Merchandise' == $slug || 'Arcade_Parts' == $slug) {
                    $colour_array = (isset($_REQUEST['colour']) && count($_REQUEST['colour'])) ? $_REQUEST['colour'] : array();
                    $items        = [];
                    foreach (productcoloursfromfilter() as $k => $v) {
                        $items[] = [
                            'id'       => $v['id'],
                            'color'    => $v['color_val'],
                            'checked'  => in_array($v['id'], $colour_array),
                            'disabled' => false,
                            'value'    => $v['id'],
                        ];
                    }
                    if (count($items)) {
                        $filters[] = [
                            "title"   => "Colour",
                            "type"    => "color",
                            "options" => ['items' => $items],
                        ];
                    }
                }
                $items        = [];
                $sub_criteria = $criteria;
                unset($sub_criteria['condition']);
                $temp = productGroupCount(productSearch($sub_criteria), 'condition');
                foreach (productGetOptionList('condition') as $k => $v) {
                    if (array_key_exists($k, $temp)) {
                        $items[] = [
                            'id'       => $k,
                            'title'    => $v,
                            'count'    => $temp[$k],
                            'checked'  => (array_key_exists('condition', $criteria) && in_array($k, $criteria['condition'])),
                            'disabled' => false,
                            'name'     => 'c[]',
                            'value'    => $k,
                        ];
                    }
                }
                if (count($items)) {
                    $filters[] = [
                        "title"   => "Condition",
                        "type"    => "checkbox",
                        "options" => ['items' => $items],
                    ];
                }
                $items        = [];
                $sub_criteria = $criteria;
                unset($sub_criteria['product_supported']);
                $temp = productSearch($sub_criteria);
                foreach (productGetOptionList('product_supported') as $k => $v) {
                    if ($count=countProductSupported($temp,$k)) {
                        $items[] = [
                            'id'       => $k,
                            'title'    => $v,
                            'count'    => $count,
                            'checked'  => (array_key_exists('product_supported', $criteria) && in_array($k, $criteria['product_supported'])),
                            'disabled' => false,
                            'name'     => 'product_supported[]',
                            'value'    => $k,
                        ];
                    }
                }
                if (count($items)) {
                    $filters[] = [
                        "title"   => "Product Supported",
                        "type"    => "checkbox",
                        "options" => ['items' => $items],
                    ];
                }
                if($search_category_parent==2 || $search_category_parent==3){
                     $items        = [];
                    $sub_criteria = $criteria;
                    unset($sub_criteria['coin_operated']);
                    $temp = productGroupCount(productSearch($sub_criteria), 'coin_operated');
                    foreach (['y'=>'Yes','n'=>'No'] as $k => $v) {
                        if (array_key_exists($k, $temp)) {
                            $items[] = [
                                'id'       => $k,
                                'title'    => $v,
                                'count'    => $temp[$k],
                                'checked'  => (array_key_exists('coin_operated', $criteria) && in_array($k, $criteria['coin_operated'])),
                                'disabled' => false,
                                'name'     => 'coin_operated[]',
                                'value'    => $k,
                            ];
                        }
                    }
                    if (count($items)) {
                        $filters[] = [
                            "title"   => "Coin Operated",
                            "type"    => "checkbox",
                            "options" => ['items' => $items],
                        ];
                    }
                }
                
                //create price array for find max and min pric
                if (count($products)) {
                    $filters[] = [
                        "title"   => "Price (USD)",
                        "type"    => "price",
                        "options" => ['max' => $max_price, 'min' => $min_price, 'from' => $from_price, 'to' => $to_price],
                    ];
                }

                /*$items1[0]= ['title' => 'Show sold out products in search result', 'count' => '', 'checked' =>(!array_key_exists('discontinued', $_GET))?'':true,'disabled' =>'', 'name' => 'discontinued','value' => 'y'];
                $filters[] = [
                    "title"   => "Show sold out products in search result",
                    "type"    => "discontinued",
                    "options" => ['items' => $items1],
                ];*/
            }
            $product_series = array();
            $query = $DB->query('SELECT * FROM `product_series` order by `rank` DESC limit 4');
            while($row = $DB->fetch_assoc($query)) {
                $row['image_name']=basename($row['img']);
                $product_series[] = $row;
            }
            $DB->free_result($query);
            die($twig->load('search-category.twig')->render(['inputs' => [
                'page_title'       => $current_manufacturer . $category->title(),
                'page_description' => $category->description(),
                'page_keywords'    => $category->keywords(),
                'page_canonical'   => $category->url(),
                'page_breadcrumbs' => $category->breadcrumbs(),
                'category'         => $category,
                'slug'             => $slug,
                'products'         => $products,
                'layout'           => $layout,
                'sort'             => $sort,
                'show'             => $show,
                'page'             => $page,
                'pages'            => $pages,
                'total'            => $total,
                'page_url'         => $page_url,
                'filters'          => $filters,
                'product_series'   => $product_series,
                'latest_products'  => array_slice(TwigProduct::search(['type' => $category->id]), 0, 5),
                'featured_product'  => array_slice(TwigProduct::search(['type' => array_unique($criteria['type']),'feature' => 'y']), 0, 5),
                'title'            => $current_manufacturer . $category->name(),
            ]]));
        }
        break;

    case 'suggest':
        header('Access-Control-Allow-Origin: *');
        header("Content-type: application/json; charset=utf-8");
        $s = "";
        $critaria=[];
        if (isset($_GET['s'])) {
            $s = $_GET['s'];
            $s        = preg_split('/[^a-z0-9]+/i', $s);
            $critaria['any_text']=$s;
            //$critaria['discontinued']='n';
        }
        if (isset($_GET['id'])) {
            $critaria['type'][] = $_GET['id'];
            $temp               = TwigCategory::list($critaria['type']);
            while (count($temp)) {
                foreach (array_shift($temp)->children() as $v) {
                    $temp[]             = $v;
                    $critaria['type'][] = $v->id;
                }
            }
            $critaria['type'] = array_unique($critaria['type']);
            $critaria['discontinued'][]='y';

        }
        $products = TwigProduct::search($critaria);
        $data     = [];
        foreach (array_slice($products, 0, 5, true) as $p) {
            $data[] = [
                'id'   =>$p->id,
                'type'   =>$p->product_category_type(),
                'link'  => $p->url(),
                'image' => $p->dispalyimage(),
                'name'  => trim($p->name()),
                'price' => strval($p->price()),
            ];
        }
        die(json_encode([
            'data'  => $data,
            'total' => count($products),
        ]));

    case 'specials':
        moduleLoad('product');

        global $_GLOBALS;

        if (!empty($_POST)) {
            $product_qty = 0;
            $product_id  = $_POST['product_id'];
            if (isset($_POST['product_qty']) && empty($_POST['product_qty']) === false) {
                $product_qty = intval($_POST['product_qty']);
            }
            if (productExists($product_id)) {
                $product               = productFromDatabase($product_id);
                $product['product_id'] = $product['id'];

                // Hack:
                // sub_id, back_qty, power_id, adapter_id not exist in product
                // But, used in cart. Throwing errors.
                $product['sub_id']     = 0;
                $product['back_qty']   = 0;
                $product['power_id']   = 0;
                $product['adapter_id'] = 0;

                if ('n' != $product['active']) {
                    $cart = $_GLOBALS['shopping_cart'];
                    if ($product_qty < 0) {
                        $cart = cartAddToContents($cart, $product, 0 - $product_qty);
                    }
                    if ($product_qty > 0) {
                        $cart = cartAddToContents($cart, $product, $product_qty);
                    }
                    if (0 == $product_qty) {
                        $cart = cartAddToContents($cart, $product, 1);
                    }
                    foreach ($cart as $cart_key => $cart_item) {
                        $min_order = productGetValue($cart_item['product_id'], 'min_order');
                        if ($cart_item['product_qty'] < $min_order) {
                            $cart_item['product_qty']       = $min_order;
                            $cart[$cart_key]['product_qty'] = $min_order;
                        }
                        $cart[$cart_key]['product_offer']    = (string) (new TwigCurrency($product['sales_price'], $product['sales_currency'], $product['precision']));
                        $cart[$cart_key]['product_offer_us'] = $cart[$cart_key]['product_offer'];
                        $cart[$cart_key]['offer_currency']   = 'USD';
                    }
                    $_GLOBALS['shopping_cart'] = $cart;
                    cartSaveContents($_GLOBALS['member']['id'] ?? 0, $cart);
                }
            }
            redirect($_SERVER['REQUEST_URI']);
            die();
        }
        $criteria = ['special_flag' => 'y'];
        $layout   = 'grid'; //grid, grid-with-features, list, table
        // Apply filters
        foreach ([
            's'  => 'any_text',
            'cl' => 'country_location',
            'm'  => 'manufacturer',
            'c'  => 'condition',
        ] as $k1 => $v1) {
            if (array_key_exists($k1, $_REQUEST)) {
                if (is_array($_REQUEST[$k1])) {
                    foreach ($_REQUEST[$k1] as $v2) {
                        $criteria[$v1][] = $v2;
                    }
                } else {
                    $criteria[$v1][] = $_REQUEST[$k1];
                }
            }
        }
        if (array_key_exists('ct', $_REQUEST)) {
            //$criteria['type'][] = $_REQUEST['ct'];
            $temp               = TwigCategory::list($_REQUEST['ct']);
            while (count($temp)) {
                foreach (array_shift($temp)->children() as $v) {
                    $temp[]             = $v;
                    $criteria['type'][] = $v->id;
                }
            }
        }
       // print_r($criteria['type']); die;
        // Master results list
        $products = TwigProduct::search($criteria);
        // Apply sorting
        $sort = $_REQUEST['sort'] ?? 'Latest Items';
        switch ($sort) {
            case 'Product Name':
                foreach ($products as $k => $v) {
                    $products[$k] = $v->id;
                }
                $products = TwigProduct::list(productSort($products, 'product_name'));
                break;
            case 'Lowest Price':
                foreach ($products as $k => $v) {
                    $products[$k] = $v->id;
                }
                $products = TwigProduct::list(productSort($products, 'sales_price'));
                break;
            case 'Highest Price':
                foreach ($products as $k => $v) {
                    $products[$k] = $v->id;
                }
                $products = array_reverse(TwigProduct::list(productSort($products, 'sales_price')));
                break;
        }
        // Apply Pagination
        $show     = intval(max(16, min(112, $_REQUEST['show'] ?? 32)));
        $page     = intval(max(0, $_REQUEST['page'] ?? 0));
        $total    = count($products);
        $products = array_chunk($products, $show);
        $pages    = count($products);
        $page     = min($page, $pages - 1);
        $products = $products[$page] ?? [];
        $page_url = $_REQUEST;
        unset($page_url['page']);
        asort($page_url);
        $page_url['page'] = '';
        $page_url         = '/specials?' . http_build_query($page_url);

        // Build filters
        $filters = [];

        $filter_url = $_REQUEST;
        unset($filter_url['page']);
        $filter_url = '?' . http_build_query($filter_url);
        $category    = TwigCategory::search(['parent'=>0]);
        $items        = [];
        foreach ($category  as $k => $v) {
                $items[] = [
                    'title'    => $v->name(),
                    'checked'  => (array_key_exists('type', $criteria) && in_array($v->id, $_REQUEST['ct'])),
                    'disabled' => false,
                    'name'     => 'ct[]',
                    'value'    => $v->id,
                ];
        }
        if (count($items)) {
            $filters[] = [
                "title"   => "Category",
                "type"    => "checkbox",
                "options" => ['items' => $items],
            ];
        }
        $items        = [];

        $sub_criteria = $criteria;
        unset($sub_criteria['country_location']);
        $temp = productGroupCount(productSearch($sub_criteria), 'country_location');
        foreach (productGetOptionList('country_location') as $k => $v) {
            if (array_key_exists($k, $temp)) {
                $items[] = [
                    'title'    => $v,
                    'count'    => $temp[$k],
                    'checked'  => (array_key_exists('country_location', $criteria) && in_array($k, $criteria['country_location'])),
                    'disabled' => false,
                    'name'     => 'cl[]',
                    'value'    => $k,
                ];
            }
        }
        if (count($items)) {
            $filters[] = [
                "title"   => "Country",
                "type"    => "checkbox",
                "options" => ['items' => $items],
            ];
        }
        $items        = [];
        $sub_criteria = $criteria;
        unset($sub_criteria['manufacturer']);
        $temp = productGroupCount(productSearch($sub_criteria), 'manufacturer');
        foreach (productGetOptionList('manufacturer') as $k => $v) {
            if (array_key_exists($k, $temp)) {
                $items[] = [
                    'title'    => $v,
                    'count'    => $temp[$k],
                    'checked'  => (array_key_exists('manufacturer', $criteria) && in_array($k, $criteria['manufacturer'])),
                    'disabled' => false,
                    'name'     => 'm[]',
                    'value'    => $k,
                ];
            }
        }
        if (count($items)) {
            $filters[] = [
                "title"   => "Manufacturer",
                "type"    => "checkbox",
                "options" => ['items' => $items],
            ];
        }

        $items        = [];
        $sub_criteria = $criteria;
        unset($sub_criteria['condition']);
        $temp = productGroupCount(productSearch($sub_criteria), 'condition');
        foreach (productGetOptionList('condition') as $k => $v) {
            if (array_key_exists($k, $temp)) {
                $items[] = [
                    'title'    => $v,
                    'count'    => $temp[$k],
                    'checked'  => (array_key_exists('condition', $criteria) && in_array($k, $criteria['condition'])),
                    'disabled' => false,
                    'name'     => 'c[]',
                    'value'    => $k,
                ];
            }
        }
        if (count($items)) {
            $filters[] = [
                "title"   => "Condition",
                "type"    => "checkbox",
                "options" => ['items' => $items],
            ];
        }
        die($twig->load('search-specials.twig')->render(['inputs' => [
            'page_title'       => $_GLOBALS['site_options']['SPECIALS_META_TITLE'],
            'page_description' => $_GLOBALS['site_options']['SPECIALS_META_DESC'],
            'page_keywords'    => $_GLOBALS['site_options']['SPECIALS_META_KEYS'],
            'page_canonical'   => '/specials',
            'page_breadcrumbs' => [
                ['title' => 'Home', 'url' => '/'],
                ['title' => 'Specials', 'url' => '/specials'],
            ],
            'products'         => $products,
            'layout'           => $layout,
            'sort'             => $sort,
            'show'             => $show,
            'page'             => $page,
            'pages'            => $pages,
            'total'            => $total,
            'page_url'         => $page_url,
            'filters'          => $filters,
            'latest_products'  => array_slice(TwigProduct::search(['on_special' => 'y']), 0, 5),
            'featured_product'  => array_slice(TwigProduct::search(['feature' => 'y']), 0, 5),
            'title'            => 'Special Pricing',
        ]]));
    case 'parts':
        moduleLoad('product');

        if ($products = TwigProduct::search(['id' => $_GET['id']])) {
            $product  = reset($products);
            $layout   = 'grid';
            $criteria = [];
            foreach ([
                's'  => 'any_text',
                'cl' => 'country_location',
                'm'  => 'manufacturer',
                'c'  => 'condition',
            ] as $k1 => $v1) {
                if (array_key_exists($k1, $_REQUEST)) {
                    if (is_array($_REQUEST[$k1])) {
                        foreach ($_REQUEST[$k1] as $v2) {
                            $criteria[$v1][] = $v2;
                        }
                    } else {
                        $criteria[$v1][] = $_REQUEST[$k1];
                    }
                }
            }
            $products=$product->getMatchingMachine();
            $sort = $_REQUEST['sort'] ?? 'Latest Items';
            switch ($sort) {
                case 'Product Name':
                    foreach ($products as $k => $v) {
                        $products[$k] = $v->id;
                    }
                    $products = TwigProduct::list(productSort($products, 'product_name'));
                    break;
                case 'Lowest Price':
                    foreach ($products as $k => $v) {
                        $products[$k] = $v->id;
                    }
                    $products = TwigProduct::list(productSort($products, 'sales_price'));
                    break;
                case 'Highest Price':
                    foreach ($products as $k => $v) {
                        $products[$k] = $v->id;
                    }
                    $products = array_reverse(TwigProduct::list(productSort($products, 'sales_price')));
                    break;
            }
            // Apply Pagination
            $show     = intval(max(16, min(112, $_REQUEST['show'] ?? 32)));
            $page     = intval(max(0, $_REQUEST['page'] ?? 0));
            $total    = count($products);
            $products = array_chunk($products, $show);
            $pages    = count($products);
            $page     = min($page, $pages - 1);
            $products = $products[$page] ?? [];
            $page_url = $_REQUEST;
            unset($page_url['page']);
            asort($page_url);
            $page_url['page'] = '';
            $page_url         = $product->url() . http_build_query($page_url);

            // Build filters
            $filters = [];

            $filter_url = $_REQUEST;
            unset($filter_url['page']);
            $filter_url = '?' . http_build_query($filter_url);

            $items        = [];
            $sub_criteria = $criteria;
            unset($sub_criteria['country_location']);
            $temp = productGroupCount(productSearch($sub_criteria), 'country_location');
            foreach (productGetOptionList('country_location') as $k => $v) {
                if (array_key_exists($k, $temp)) {
                    $items[] = [
                        'title'    => $v,
                        'count'    => $temp[$k],
                        'checked'  => (array_key_exists('country_location', $criteria) && in_array($k, $criteria['country_location'])),
                        'disabled' => false,
                        'name'     => 'cl[]',
                        'value'    => $k,
                    ];
                }
            }
            if (count($items)) {
                $filters[] = [
                    "title"   => "Country",
                    "type"    => "checkbox",
                    "options" => ['items' => $items],
                ];
            }

            $items        = [];
            $sub_criteria = $criteria;
            unset($sub_criteria['manufacturer']);
            $temp = productGroupCount(productSearch($sub_criteria), 'manufacturer');
            foreach (productGetOptionList('manufacturer') as $k => $v) {
                if (array_key_exists($k, $temp)) {
                    $items[] = [
                        'title'    => $v,
                        'count'    => $temp[$k],
                        'checked'  => (array_key_exists('manufacturer', $criteria) && in_array($k, $criteria['manufacturer'])),
                        'disabled' => false,
                        'name'     => 'm[]',
                        'value'    => $k,
                    ];
                }
            }
            if (count($items)) {
                $filters[] = [
                    "title"   => "Manufacturer",
                    "type"    => "checkbox",
                    "options" => ['items' => $items],
                ];
            }

            $items        = [];
            $sub_criteria = $criteria;
            unset($sub_criteria['condition']);
            $temp = productGroupCount(productSearch($sub_criteria), 'condition');
            foreach (productGetOptionList('condition') as $k => $v) {
                if (array_key_exists($k, $temp)) {
                    $items[] = [
                        'title'    => $v,
                        'count'    => $temp[$k],
                        'checked'  => (array_key_exists('condition', $criteria) && in_array($k, $criteria['condition'])),
                        'disabled' => false,
                        'name'     => 'c[]',
                        'value'    => $k,
                    ];
                }
            }
            if (count($items)) {
                $filters[] = [
                    "title"   => "Condition",
                    "type"    => "checkbox",
                    "options" => ['items' => $items],
                ];
            }
            die($twig->load('search-specials.twig')->render(['inputs' => [
                'page_title'       => $_GLOBALS['site_options']['SPECIALS_META_TITLE'],
                'page_description' => $_GLOBALS['site_options']['SPECIALS_META_DESC'],
                'page_keywords'    => $_GLOBALS['site_options']['SPECIALS_META_KEYS'],
                'page_canonical'   => '',
                'page_breadcrumbs' => [],
                'product' => $product,
                'layout'           => $layout,
                'products'=>$products,
                'sort'             => $sort,
                'show'             => $show,
                'page'             => $page,
                'pages'            => $pages,
                'total'            => $total,
                'page_url'         => $page_url,
                'filters'          => $filters,
                'latest_products'  => array_slice(TwigProduct::search(['on_special' => 'y']), 0, 5),
                'title'            => $product->name().' Parts',
                ]]));

        }
        break;
    case 'index':
        moduleLoad('product');

        global $_GLOBALS;

        if (!empty($_POST)) {
            $product_qty = 0;
            $product_id  = $_POST['product_id'];
            if (isset($_POST['product_qty']) && empty($_POST['product_qty']) === false) {
                $product_qty = intval($_POST['product_qty']);
            }
            if (productExists($product_id)) {
                $product               = productFromDatabase($product_id);
                $product['product_id'] = $product['id'];

                // Hack:
                // sub_id, back_qty, power_id, adapter_id not exist in product
                // But, used in cart. Throwing errors.
                $product['sub_id']     = 0;
                $product['back_qty']   = 0;
                $product['power_id']   = 0;
                $product['adapter_id'] = 0;

                if ('n' != $product['active']) {
                    $cart = $_GLOBALS['shopping_cart'];
                    if ($product_qty < 0) {
                        $cart = cartAddToContents($cart, $product, 0 - $product_qty);
                    }
                    if ($product_qty > 0) {
                        $cart = cartAddToContents($cart, $product, $product_qty);
                    }
                    if (0 == $product_qty) {
                        $cart = cartAddToContents($cart, $product, 1);
                    }
                    foreach ($cart as $cart_key => $cart_item) {
                        $min_order = productGetValue($cart_item['product_id'], 'min_order');
                        if ($cart_item['product_qty'] < $min_order) {
                            $cart_item['product_qty']       = $min_order;
                            $cart[$cart_key]['product_qty'] = $min_order;
                        }
                        $cart[$cart_key]['product_offer']    = (string) (new TwigCurrency($product['sales_price'], $product['sales_currency'], $product['precision']));
                        $cart[$cart_key]['product_offer_us'] = $cart[$cart_key]['product_offer'];
                        $cart[$cart_key]['offer_currency']   = 'USD';
                    }
                    $_GLOBALS['shopping_cart'] = $cart;
                    cartSaveContents($_GLOBALS['member']['id'] ?? 0, $cart);
                }
            }
            redirect($_SERVER['REQUEST_URI']);
            die();
        }
        $s = "";
        if (isset($_GET['s'])) {
            $s = $_GET['s'];
        }

        $_s = $s;

        $s = preg_split('/[^a-z0-9]+/i', $s);

        $criteria = [
            'any_text' => $s,
        ];
        $criteria['discontinued'] = 'n';
        $layout = 'grid'; //grid, grid-with-features, list, table
        $sort   = 'price';
        //$show    = 15;
        $show    = (isset($_GET['show']) && !empty($_GET['show'])) ? intval($_GET['show']) : 16;
        $page    = 0;
        $sort_by = 'id';
        $order   = 'desc';

        // Master results list
        $products = TwigProduct::search($criteria);

        // Apply Pagination
        $total = count($products);
        if (array_key_exists('sort', $_REQUEST)) {
            $sort_by = [][$_REQUEST['sort']] ?? 'id';
            $order   = [][$_REQUEST['sort']] ?? 'desc';
        }
        if ('id' !== $sort_by || 'desc' !== $order) {
            foreach ($products as $k => $v) {
                $products[$k] = $v->id;
            }
            $products = TwigProduct::list(productSort($products, $sort_by));
            if ('asc' !== $order) {
                $products = array_reverse($products);
            }
        }
        if (array_key_exists('show', $_REQUEST)) {
            $show = min(112, max(1, intval($_REQUEST['show'])));
        }
        $products = array_chunk($products, $show);
        $pages    = count($products);
        if (array_key_exists('page', $_REQUEST)) {
            $page = min(count($products) - 1, max(0, intval($_REQUEST['page'])));
        }
        $products = $products[$page];

        $page_url = $_REQUEST;
        unset($page_url['page']);
        asort($page_url);
        $page_url['page'] = '';
        $page_url         = '/search?' . http_build_query($page_url);

        die($twig->load('search-index.twig')->render(['inputs' => [
            'page_title'       => ($_s) ? $_s : '[TODO]',
            'page_description' => ($_s) ? $_s : '[TODO]',
            'page_keywords'    => ($_s) ? $_s : '[TODO]',
            'page_canonical'   => ($_s) ? $_s : '[TODO]',
            'page_breadcrumbs' => [
                ['title' => 'Home', 'url' => '/'],
                ['title' => 'Search', 'url' => '/search'],
            ],
            'products'         => $products,
            'layout'           => $layout,
            'sort'             => $sort,
            'show'             => $show,
            'page'             => $page,
            'pages'            => $pages,
            'total'            => $total,
            'page_url'         => $page_url,
            'filters'          => [],
            'latest_products'  => [],
            'title'            => 'Search: ' . $_s,
        ]]));

    case 'news':
        $categories = [
            'Latest News' => [
                'title'       => 'Latest News at Arcooda',
                'name'        => 'Latest News',
                'url'         => '/latest-news',
                'description' => 'Latest News from Arcooda',
                'keywords'    => 'latest, news, arcooda, express',
            ],
            'Arcade Games' => [
                'title'       => 'Arcade Games News',
                'name'        => 'Arcade Games',
                'url'         => '/arcade-games-news/',
                'description' => 'Arcade Games news relating to arcade machines, ticket redemption and game manufacturers.',
                'keywords'    => 'arcade, games, news, machines, ticket, redemption',
            ],
            'Game Software' => [
                'title'       => 'Arcade Game Board News',
                'name'        => 'Game Software',
                'url'         => '/game-software-news/',
                'description' => 'Arcade Game board news discussing the latest amusement boards, kits, fishing games and sega systems.',
                'keywords'    => 'arcade, game, board, news, kits, fishing, system',
            ],
            'Arcade Parts' => [
                'title'       => 'Arcade Parts News',
                'name'        => 'Arcade Parts',
                'url'         => '/arcade-parts-news/',
                'description' => 'Arcade Game board news discussing the latest amusement boards, kits, fishing games and sega systems.',
                'keywords'    => 'arcade, game, board, news, kits, fishing, system',
            ],
            'Merchandise' => [
                'title'       => 'Merchandise News',
                'name'        => 'Merchandise',
                'url'         => '/merchandise-news/',
                'description' => 'Merchandise news related to plush toys and prizes for your crane and ticket redemption machines.',
                'keywords'    => 'merchandise, news, plush, toys, prizes',
            ],
        ];

        $filter = [];
        $filter['date_schedule'] = date('Y-m-d');
        if (isset($_GET['category']) && !empty($_GET['category'])) {
            $filter['category'][] = $_GET['category'];
        }
        // Apply filters
        foreach (['s' => 'any_text'] as $k1 => $v1) {
            $temp = [];
            if (array_key_exists($k1, $_REQUEST)) {
                if (is_array($_REQUEST[$k1])) {
                    foreach ($_REQUEST[$k1] as $v2) {
                        $temp[] = $v2;
                    }
                } else {
                    $temp[] = $_REQUEST[$k1];
                }
            }
            if (count($temp)) {
                $filter[$v1] = $temp;
            }
        }
        $limit            = 8;
        $offset           = max(0, intval($_REQUEST['page'] ?? 0) * $limit);
        $filter['limit']  = $limit;
        $filter['offset'] = $offset;
        //print_r($filter); die;
        $total_news       = TwigNews::totalnews($filter);
        if ($newses = TwigNews::search($filter)) {
            $pages    = ceil($total_news['total'] / $limit);
            $page     = 0;
            $page_url = $_REQUEST;
            unset($page_url['page']);
            asort($page_url);
            $page_url['page'] = '';
            $page_url         = '/arcade-news?' . http_build_query($page_url);
            if (isset($_GET['page']) && !empty($_GET['page'])) {
                $page = intval($_GET['page']);
            }

            $title       = $_GLOBALS['site_options']['NEWS_META_TITLE'] ?? 'Arcade News';
            $canonical   = '/arcade-news';
            $description = $_GLOBALS['site_options']['NEWS_META_DESC'];
            $keywords    = $_GLOBALS['site_options']['NEWS_META_KEYS'];
            $breadcrumbs = [
                ['title' => 'Home', 'url' => '/'],
                ['title' => 'Arcade News', 'url' => $canonical],
            ];

            if (array_key_exists('category', $_GET)) {
                if (array_key_exists($_GET['category'], $categories)) {
                    $row = $DB->query_first(sprintf(
                          "SELECT * FROM `news_category_meta` WHERE `name` = '%s' LIMIT 1",
                          $_GET['category']
                      ), MYSQLI_ASSOC);
                    $title       = $row['meta_title'] ?? $categories[$_GET['category']]['title'];
                    $canonical   = $categories[$_GET['category']]['url'];
                    $description = $row['meta_desc'] ?? $categories[$_GET['category']]['description'];
                    $keywords    = $row['meta_key'] ?? $categories[$_GET['category']]['keywords'];

                    $page_url = $_REQUEST;
                    unset($page_url['page']);
                    asort($page_url);
                    $page_url['page'] = '';
                    $page_url         = $canonical . '?' . http_build_query($page_url);
                } else {
                    //TODO: This is bad, displaying _GET content
                    $title       = $_GET['category'] . ' - ' . $title;
                    $canonical   = '/n/?category=' . urlencode($_GET['category']);
                    $description = '';
                    $keywords    = '';

                    $page_url = $_REQUEST;
                    unset($page_url['page']);
                    asort($page_url);
                    $page_url['page'] = '';
                    $page_url         = '/n/?' . http_build_query($page_url);
                }
                $breadcrumbs[] = ['title' => $_GET['category'], 'url' => $canonical];
            }

            die($twig->load('search-news.twig')->render(['inputs' => [
                'page_title'       => $title,
                'page_description' => $description,
                'page_keywords'    => $keywords,
                'page_canonical'   => $canonical,
                'page_breadcrumbs' => $breadcrumbs,

                'posts'            => $newses,
                'categories'       => $categories,
                'page'             => $page,
                'pages'            => $pages - 1,
                'page_url'         => $page_url,
                'action'           => $page_url,
                's'                => $_REQUEST['s'] ?? '',
            ]]));
        }
        break;
     case 'preorder':
            moduleLoad('product'); //productFromDatabase
            global $_GLOBALS;
            $criteria = [];
            $sort_by = 'product_name';
            $order   = 'asc';
            $show     = intval(max(16, min(112, $_REQUEST['show'] ?? 32)));
            if (isset($_GET['sort'])) {
                $sort = $_GET['sort'];

                switch ($sort) {
                    case 'Product Name':
                        $sort_by = 'product_name';
                        $order   = 'asc';
                        break;

                    case 'Lowest Price':
                        $sort_by = 'sales_price';
                        $order   = 'asc';
                        break;

                    case 'Highest Price':
                        $sort_by = 'sales_price';
                        $order   = 'desc';
                        break;

                    default:
                        $sort_by = 'id';
                        $order   = 'desc';
                        break;
                }
            }
            $pageTitle = 'Pre-Order Products';
            if($_GET['type'] == 'preorder'){
                $criteria['active']='y';
                $criteria['pre_order']='-1';
                $pageurl = '\pre-order-product';
                $breadcrumbs = [
                    ['title' => 'Home', 'url' => '/'],
                    ['title' => 'Pre Order', 'url' => '/pre-order-product'],
                ];
            }else if($_GET['type'] == 'special'){
                  $criteria = [
                    'active'    => 'y',
                    'special_flag' => 'y',
                    'special_expiry' => true
                ];
                $pageurl = '\specials-product';
                $breadcrumbs = [
                    ['title' => 'Home', 'url' => '/'],
                    ['title' => 'Specials', 'url' => '/specials-product'],
                ];
                 $pageTitle = 'Specials & Clearances Products';
            }else{
                $criteria = [
                    'active'    => 'y',
                    'factory_second' => 'yes'
                ];
                $pageurl = '\second-product';
                $breadcrumbs = [
                    ['title' => 'Home', 'url' => '/'],
                    ['title' => 'Second', 'url' => '/second-product'],
                ];
                $pageTitle = 'Second Products';
            }
            
            $products = TwigProduct::search($criteria);
            $total = count($products);
            if($total > 0) {
                if ('id' !== $sort_by || 'desc' !== $order) {
                    foreach ($products as $k => $v) {
                        $products[$k] = $v->id;
                    }
                                       
                }
                 $products = TwigProduct::list(productSort($products, $sort_by));
                if ('asc' !== $order) {
                    $products = array_reverse($products);
                }
                $products = array_chunk($products, $show);
                $pages    = count($products);
                $page    = 0;
                if ($pages) {
                    if (array_key_exists('page', $_REQUEST)) {
                        $page = min(count($products) - 1, max(0, intval($_REQUEST['page'])));
                    }
                    $products = $products[$page];
                }

                $page_url = $_REQUEST;
                unset($page_url['page']);
                asort($page_url);
                $page_url['page'] = '';
                $page_url         = $pageurl.'?' . http_build_query($page_url);
                $filters          = [];
                $items            = [];
                $filter_url       = $_REQUEST;
                unset($filter_url['page']);
                $filter_url   = '?' . http_build_query($filter_url);
            }
            $layout = 'grid';
            
            die($twig->load('search-pre-order-product.twig')->render(['inputs' => [
                'page_title'  => $pageTitle,
                'page_description' => $pageTitle,
                'page_keywords'    => $pageTitle,
                'page_canonical'   => $pageurl,
                'page_breadcrumbs' => $breadcrumbs,
                'products'         => $products,
                'layout'           => $layout,
                'sort'             => $sort,
                'show'             => $show,
                'page'             => $page,
                'pages'            => $pages,
                'total'            => $total,
                'page_url'         => $page_url,
                'filters'          => [],
                'title'            => $pageTitle,
            ]]));
        break;

        case 'discontinued':
            moduleLoad('product'); //productFromDatabase
            global $_GLOBALS;
            $criteria = [];
            $sort_by = 'product_name';
            $order   = 'asc';
            $show     = intval(max(16, min(112, $_REQUEST['show'] ?? 32)));
            if (isset($_GET['sort'])) {
                $sort = $_GET['sort'];

                switch ($sort) {
                    case 'Product Name':
                        $sort_by = 'product_name';
                        $order   = 'asc';
                        break;

                    case 'Lowest Price':
                        $sort_by = 'sales_price';
                        $order   = 'asc';
                        break;

                    case 'Highest Price':
                        $sort_by = 'sales_price';
                        $order   = 'desc';
                        break;

                    default:
                        $sort_by = 'id';
                        $order   = 'desc';
                        break;
                }
            }
            //$criteria['active']='y';
            $criteria['available'] = 427;
            $products = TwigProduct::search($criteria);
            $total = count($products);
            if($total > 0) {
                if ('id' !== $sort_by || 'desc' !== $order) {
                    foreach ($products as $k => $v) {
                        $products[$k] = $v->id;
                    }
                                       
                }
                 $products = TwigProduct::list(productSort($products, $sort_by));
                if ('asc' !== $order) {
                    $products = array_reverse($products);
                }
                $products = array_chunk($products, $show);
                $pages    = count($products);
                $page    = 0;
                if ($pages) {
                    if (array_key_exists('page', $_REQUEST)) {
                        $page = min(count($products) - 1, max(0, intval($_REQUEST['page'])));
                    }
                    $products = $products[$page];
                }

                $page_url = $_REQUEST;
                unset($page_url['page']);
                asort($page_url);
                $page_url['page'] = '';
                $page_url         = '\discontinued-product?' . http_build_query($page_url);
                $filters          = [];
                $items            = [];
                $filter_url       = $_REQUEST;
                unset($filter_url['page']);
                $filter_url   = '?' . http_build_query($filter_url);
            }
            $layout = 'list';
            $breadcrumbs = [
                ['title' => 'Home', 'url' => '/'],
                ['title' => 'Discontinued product', 'url' => '/discontinued-product'],
            ];
            die($twig->load('search-discontinued-product.twig')->render(['inputs' => [
                'page_title'  => 'Discontinued Products',
                'page_description' => 'Discontinued Products',
                'page_keywords'    =>'Discontinued Products',
                'page_canonical'   => '/discontinued-product',
                'page_breadcrumbs' => $breadcrumbs,
                'products'         => $products,
                'layout'           => $layout,
                'sort'             => $sort,
                'show'             => $show,
                'page'             => $page,
                'pages'            => $pages,
                'total'            => $total,
                'page_url'         => $page_url,
                'filters'          => [],
                'title'            => 'Discontinued Products',
            ]]));
        break;
        case 'distributor':
             global $_GLOBALS;
             $criteria = [];
             if(isset($_GET['category'])){
                $criteria['category'] = $_GET['category']; 
             }
             if(isset($_GET['country']) && !empty($_GET['country'])){
                $criteria['country_location'] = $_GET['country']; 
             }
             $show     = intval(max(16, min(112, $_REQUEST['show'] ?? 32)));
             $distributor = TwigDistributor::search($criteria);
             $total = count($distributor);
            if($total > 0) {
                if ('id' !== $sort_by || 'desc' !== $order) {
                    foreach ($distributor as $k => $v) {
                        $distributor[$k] = $v->id;
                    }
                                       
                }
                $distributor = TwigDistributor::list(distributorSort($distributor, $sort_by));
                if ('asc' !== $order) {
                    $distributor = array_reverse($distributor);
                }
                $distributor = array_chunk($distributor, $show);
                $pages    = count($distributor);
                $page    = 0;
                if ($pages) {
                    if (array_key_exists('page', $_REQUEST)) {
                        $page = min(count($distributor) - 1, max(0, intval($_REQUEST['page'])));
                    }
                    $distributor = $distributor[$page];
                }

                $page_url = $_REQUEST;
                unset($page_url['page']);
                asort($page_url);
                $page_url['page'] = '';
                $page_url         = '\discontinued-product?' . http_build_query($page_url);
                $filters          = [];
                $items            = [];
                $filter_url       = $_REQUEST;
                unset($filter_url['page']);
                $filter_url   = '?' . http_build_query($filter_url);
            }
            $layout = 'list';
            $breadcrumbs = [
                ['title' => 'Home', 'url' => '/'],
                ['title' => 'Distributors', 'url' => '/arcooda-distributor'],
            ];
            $query = $DB->query("SELECT country_location FROM `distributor` WHERE `active`='y' group by `country_location` ORDER BY country_location");
            $country_list =  getCountryList(false);
            $country = [];
            while($row = $DB->fetch_assoc($query)) {
                
                $country[$row['country_location']] = $country_list[$row['country_location']];
            }
            die($twig->load('search-arcooda-distributor.twig')->render(['inputs' => [
                'page_title'  => 'Distributors',
                'page_description' => 'Arcooda Distributors',
                'page_keywords'    =>'Distributor',
                'page_canonical'   => '/arcooda-distributor',
                'page_breadcrumbs' => $breadcrumbs,
                'distributor'         => $distributor,
                'layout'           => $layout,
                'sort'             => $sort,
                'show'             => $show,
                'page'             => $page,
                'pages'            => $pages,
                'total'            => $total,
                'page_url'         => $page_url,
                'country'          => $country,
                'filters'          => ['category'=>$_GET['category'] ?? 0 ,'country'=>$_GET['country'] ?? ''],
                'title'            => 'Arcooda Distributors',
            ]]));
        break;
}
if (!function_exists('formatSQLString')) {
    function formatSQLString($instring)
    {
        global $DB;
        $instring = str_replace("\r\n", "\n", $instring);
        $instring = str_replace("\n\r", "\n", $instring);
        $instring = str_replace("\r", "\n", $instring);
        $result   = $DB->escape_string($instring);
        return "'" . $result . "'";
    }
}
