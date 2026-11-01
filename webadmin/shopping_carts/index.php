<?php
/**
 * @var mixed[] $posted
 */
include_once __DIR__ . '/common.inc.php';

$action = $posted['action'];

/**
 * @param mixed[] $posted
 *
 * @return mixed[]
 */
function shopping_cartFromPost($posted)
{
    $shopping_cart_id           = $posted['shopping_cart_id'];
    $result                     = shopping_cartFromDatabase($shopping_cart_id);
    $result['shopping_cart_id'] = intval($shopping_cart_id);
    foreach ($result as $key => $value) {
        if (array_key_exists($key, $posted) && !is_array($posted[$key])) {
            $result[$key] = trim($posted[$key]);
        }
    }

    if ($shopping_cart_id <= 0) {
        unset($result['shopping_cart_id']);
        $result['create_time'] = time();
        $result['create_date'] = formatDateTime();
    }
    return $result;
}

/**
 * @param mixed[] $posted
 *
 * @return mixed[][]
 */
function shopping_cartProductsFromPost($posted)
{
    global $DB;
    $products = array();

    $fields = $DB->table_defaults("`highwaygroup`.`shopping_cart_products`");

    $shopping_cart_id = intval($posted['shopping_cart_id']);
    if (shopping_cartExists($shopping_cart_id)) {
        $products = shopping_cartProductsFromDatabase($shopping_cart_id);
    }

    if (isset($posted['product_affected'])) {
        foreach (explode(',', $posted['product_affected']) as $key1) {
            foreach ($products as $key2 => $product) {
                if ($product['prod_count'] == $key1) {
                    unset($products[$key2]);
                }
            }
        }
    }

    if (isset($posted['prod_count'])) {
        foreach ($posted['prod_count'] as $key1) {
            foreach ($products as $key2 => $product) {
                if ($product['prod_count'] == $key1) {
                    unset($products[$key2]);
                }
            }
        }
    }

    if (isset($posted['product_id'])) {
        foreach ($posted['product_id'] as $key1 => $product_id) {
            $product                     = $fields;
            $product['shopping_cart_id'] = $posted['shopping_cart_id'];
            foreach ($fields as $key2 => $value) {
                if (isset($posted[$key2][$key1])) {
                    $product[$key2] = $posted[$key2][$key1];
                }
            }

            $products[] = $product;
        }
    }
    return $products;
}

//Update Actions
switch ($action) {
    case 'do_add':
        unset($posted['shopping_cart']['shopping_cart_id']);
    case 'do_edit':
        $shopping_cart_id  = $posted['shopping_cart']['shopping_cart_id'];
        $is_new            = !shopping_cartExists($shopping_cart_id);
        $old_shopping_cart = shopping_cartFromDatabase($shopping_cart_id);
        $new_shopping_cart = shopping_cartFromPost($posted['shopping_cart']);

        if (0 == $new_shopping_cart['time_added']) {
            $new_shopping_cart['time_added'] = time();
        }

        $new_shopping_cart['last_change_time'] = time();
        $new_shopping_cart['last_change']      = formatDateTime();

        $shopping_cart_id           = shopping_cartToDatabase($new_shopping_cart);
        $posted['shopping_cart_id'] = $shopping_cart_id;

        $action = 'edit';
        break;
}

$get_vars  = $posted;
$keep_only = array('order_by', 'search_for', 'page_id', 'order', 'shopping_cart_id');
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value) || !in_array($key, $keep_only)) {
            unset($get_vars[$key]);
        }
    }
}

//Redirect Actions
switch ($action) {
    case 'add':
        unset($get_vars['shopping_cart_id']);
        redirect('shopping_cart_add.php?' . http_build_query($get_vars));
        die();

    case 'edit':
        redirect('shopping_cart_edit.php?' . http_build_query($get_vars));
        die();

    case 'view':
        redirect('shopping_cart_view.php?' . http_build_query($get_vars));
        die();

    case 'delete':
        redirect('shopping_cart_delete.php?' . http_build_query($get_vars));
        die();

    case 'search':
        redirect('shopping_cart_search.php?' . http_build_query($get_vars));
        die();

    default:
    case 'list':
        redirect('shopping_cart_list.php?' . http_build_query($get_vars));
        die();
}
