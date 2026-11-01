<?php
include_once __DIR__ . '/common.inc.php';

$search = true;
include_once __DIR__ . '/templates/shopping_cart_header.tpl.php';

$posted = array_merge($_POST, $_GET);

//echo '<pre>Posted Array = ';print_r($posted);echo '</pre>';
$shopping_cart_id = intval($posted['shopping_cart_id']);
$shopping_cart    = shopping_cartFromDatabase($shopping_cart_id);
$get_vars = $posted;
if (count($get_vars) > 0) {
    foreach ($get_vars as $key => $value) {
        if (is_array($value)) {
            unset($get_vars[$key]);
        }
    }
}

$doedit_url           = $get_vars;
$doedit_url['action'] = 'do_edit';
$doedit_url           = '.?' . http_build_query($doedit_url);
if (shopping_cartExists($shopping_cart_id)) {
?>

<form action="<?=$doedit_url?>" method="post" onsubmit="return validate(this)">
<?php
include_once __DIR__ . '/templates/addedit_shopping_cart.tpl.php';
?>
</form>
<?php
} else {
    die("A Shopping Cart with that with that ID doesn't exist.");
}

include_once __DIR__ . '/templates/shopping_cart_footer.tpl.php';
