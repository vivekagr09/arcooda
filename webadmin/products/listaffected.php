<?php
include_once __DIR__ . '/common.inc.php';

global $DB;

//First run step=1 to insert into query_recovery, then step=2 to find results
$step = 2;

if (1 == $step || 'true' == $_GET['reset']) {
    $DB->query("TRUNCATE TABLE `query_recovery`");
    $DB->query("INSERT INTO `query_recovery` (`log_date`, `query`) SELECT `log_date`, `query` FROM `query_log` WHERE TRIM(`query`) LIKE 'UPDATE `products` SET%'");
}

if ($_POST['new_cost'] > 0 && '' != $_POST['new_currency']) {
    $DB->query(sprintf(
        "UPDATE `products` SET `cost_price` = %s, `cost_currency` = %s WHERE `id` = %d LIMIT 1",
        formatSQLString($_POST['new_cost']),
        formatSQLString($_POST['new_currency']),
        $_POST['product_id']
    ));
    $counter = $_POST['counter'];
} else {
    $counter = $_POST['counter'] + 1;
}

//Make a list of bugged products
$products_list = array();
if ($query = $DB->query(sprintf(
    "SELECT `query` FROM `query_recovery` WHERE `query` LIKE 'UPDATE `products` SET `weight` = 0, `cost_price` = 0, `cost_currency` = \\'%%\\'%% WHERE `id` = % LIMIT 1'"
))) {
    while ($row = $DB->fetch_assoc($query)) {
        $product_id                 = intval(substr($row['query'], strrpos($row['query'], ' ') + 2, -1));
        $products_list[$product_id] = $product_id;
    }
    $DB->free_result($query);
}

//Make sure they haven't already been fixed
foreach ($products_list as $product_id) {
    if ($row = $DB->query_first(sprintf(
        "SELECT `weight`, `cost_price`, `cost_currency` FROM `products` WHERE `id` = %d LIMIT 1",
        $product_id
    ), MYSQLI_ASSOC)) {
        if (0 != $row['weight'] || 0 != $row['cost_price']) {
            unset($products_list[$product_id]);
        }
    }
}

asort($products_list);
$chunks = array_chunk($products_list, 5);
foreach ($chunks as $key => $item) {
    $chunks[$key] = implode(", ", $item);
}

//echo implode("<br />\n",$chunks);
//echo "p.id=".implode(" OR p.id=",$products_list);
//die();

$product_id = reset($products_list);
for ($i = 0; $i < $counter; $i++) {
    $product_id = next($products_list);
}

echo '<h1>' . $product_id . '</h1>';

if ($row = $DB->query_first(sprintf(
    "SELECT `weight`, `cost_price`, `cost_currency` FROM `products` WHERE `id` = %d LIMIT 1",
    $product_id
), MYSQLI_ASSOC)) {
    echo $row['cost_price'] . ' ' . $row['cost_currency'] . '<br />';
}

$fix_array = array('cost_price');

$fix_search    = array(' weight ', ' cost_price ', ' cost_currency ');
$fix_highlight = array();
foreach ($fix_search as $key => $fix) {
    $fix_highlight[$key] = '<br /><b>' . $fix . '</b>';
}

foreach ($fix_array as $fix) {
    if ($query = $DB->query(sprintf(
        "SELECT * FROM `query_recovery` WHERE TRIM(`query`) LIKE %s ORDER BY `log_date` DESC LIMIT 1,10",
        formatSQLString('%' . $fix . '%' . $product_id . '%')
    ))) {
        while ($row = $DB->fetch_assoc($query)) {
            $oldsql = $row['query'];
            echo $row['log_date'] . ' - ' . $row['user_id'] . '<br />' . str_replace($fix_search, $fix_highlight, $oldsql) . "<br /><br />\n";
        }
        $DB->free_result($query);
    }
}
?>

<table align="center" style="border:1px solid #EEEEEE;">
<form action="" method="post">
<input type="hidden" name="product_id" value="<?php echo $product_id ?>" />
<input type="hidden" name="counter" value="<?php echo $counter ?>" />
	<tr><td>cost_price:</td><td><input name="new_cost" type="text" value="" /></td></tr>
	<tr><td>cost_currency:</td><td><input name="new_currency" type="text" value="" /></td></tr>
	<tr><td colspan="2" align="center"><input type="submit" /></td></tr>
</form>
</table>
