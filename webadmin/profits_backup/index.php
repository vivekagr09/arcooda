<?php
/**
 * @var mixed[] $posted
 */
include_once __DIR__ . '/common.inc.php';

$action = $posted['action'];

//Processing Actions..
switch ($action) {
    case 'break_even_update':
        profitUpdateBreakEvens($posted);
        $action = 'break_even';
        break;
}

$get_vars  = $posted;
$keep_only = array('type', 'site_id', 'page', 'start_time', 'end_time', 'year', 'month', 'top', 'sale_type', 'view', 'print');
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (!in_array($key, $keep_only)) {
            unset($get_vars[$key]);
        }
    }
}

//Redirect Actions.
switch ($action) {
    case 'sales_search':
        redirect('profits_sales_search.php?' . http_build_query($get_vars));
        die();

    case 'products_sales':
        redirect('profits_product_sales.php?' . http_build_query($get_vars));
        die();

    case 'products_views':
        redirect('profits_product_views.php?' . http_build_query($get_vars));
        die();

    case 'freight_sales':
        redirect('profits_freight_sales.php?' . http_build_query($get_vars));
        die();

    case 'country_sales':
        redirect('profits_country_sales.php?' . http_build_query($get_vars));
        die();

    case 'payment_types':
        redirect('profits_credit_sales.php?' . http_build_query($get_vars));
        die();

    case 'customer_sales':
        redirect('profits_customer_sales.php?' . http_build_query($get_vars));
        die();

    case 'break_even':
        redirect('profits_break_even.php?' . http_build_query($get_vars));
        die();

    case 'summary':
        redirect('profits_summary.php?' . http_build_query($get_vars));
        die();

    case 'best_products':
        unset($get_vars['type']);
        redirect('profits_products.php?type=best&' . http_build_query($get_vars));
        die();

    case 'worst_products':
        unset($get_vars['type']);
        redirect('profits_products.php?type=worst&' . http_build_query($get_vars));
        die();

    case 'country':
        redirect('profits_country.php?' . http_build_query($get_vars));
        die();

    case 'customers':
        redirect('profits_customers.php?' . http_build_query($get_vars));
        die();

    case 'sales':
    default:
        redirect('profits_sales.php?' . http_build_query($get_vars));
        die();
}
