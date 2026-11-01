<?php
include_once __DIR__ . '/query_products.inc.php';

function summarizeProduct($product_id, $currency = 'USD')
{
    //Wrapper function
    return productFromDatabase($product_id, 3);
}
