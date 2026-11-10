<?php
global $_GLOBALS;
global $DB;

?>
<table cellspacing="0" cellpadding="4" width="100%">
	<tr class="scellheader"><td class="collapseable" ondblclick="collapse(this);"><b><?=htmlspecialchars(_("Products NEW"))?></b></td></tr>
	<tr><td><ul class="blocklist">
<?php
if ($temp = $DB->query(sprintf(
    "SELECT `id`, `name` FROM `product_types` WHERE `deleted_at` IS NULL AND `parent` = 0 ORDER BY `ordering` ASC"
))) {
    while ($row = $DB->fetch_assoc($temp)) {
?>
        <li><a href="/webadmin/products_new/?section=<?=$row['id']?>"><?=htmlspecialchars(ucwords($row['name']))?></a></li>
<?php
    }
    $DB->free_result($temp);
}
?>
<br/>
<br/>
<li><a href="/webadmin/products_new">All Products</a></li>
    </ul></td></tr>
    <tr><td><ul class="blocklist">
<?php
if ($temp = $DB->query_first(sprintf(
    "SELECT COUNT(*) AS `total` FROM `products` AS `p` INNER JOIN `highwaygroup`.`currency` AS `c1` ON `c1`.`code` = `p`.`cost_currency` INNER JOIN `highwaygroup`.`currency` AS `c2` ON `c2`.`code` = `p`.`sales_currency` WHERE `p`.`active` = 'y' AND IF(`p`.`price_flux` = 'y', `p`.`markup` > -100.0 AND `p`.`markup` < 0.0, (`p`.`cost_price` * `c1`.`usfrom` > `p`.`sales_price` * `c2`.`usfrom`) AND `p`.`sales_price` > 0.0)"
), MYSQLI_ASSOC)) {
?>
        <li><a href="/webadmin/products_new/problems.php" class="unfinished"><?=htmlspecialchars(sprintf(_("Problem Products (%d)"), $temp['total']))?></a></li>
<?php
}

$temp = $_GLOBALS["criteria_options"]["products"];
unset($temp["active"]);
unset($temp["active_id"]);
$temp['alert_flag'] = 'y';
if ($temp = $DB->query_first(sprintf(
    "SELECT COUNT(*) AS `total` FROM `products` AS `p` INNER JOIN `highwaygroup`.`currency` AS `c1` ON `c1`.`code` = `p`.`cost_currency` INNER JOIN `highwaygroup`.`currency` AS `c2` ON `c2`.`code` = `p`.`sales_currency` WHERE `p`.`active` = 'y' AND IF(`p`.`price_flux` = 'y', `p`.`markup` > -100.0 AND `p`.`markup` < 0.0, (`p`.`cost_price` * `c1`.`usfrom` > `p`.`sales_price` * `c2`.`usfrom`) AND `p`.`sales_price` > 0.0)"
), MYSQLI_ASSOC)) {
?>
        <li><a href="/webadmin/products_new/?alert_flag=y"<?=($temp['total']) ? ' style="color:#f00;"' : ""?>><?=htmlspecialchars(_("To Order"))?> (<?=$temp['total']?>)</a></li>
<?php
}
?>
    </ul></td></tr>
	<tr><td><ul class="blocklist">
        <li><a href="/webadmin/products_new/?action=add_inventory"><?=htmlspecialchars(_("Add Inventory"))?></a></li>
        <li><a href="/webadmin/products_new/?action=list_inventory"><?=htmlspecialchars(_("List Added Inventory"))?></a></li>
        <li><a href="/webadmin/warehouse_transfer/"><?=htmlspecialchars(_("Transfer Inventory"))?></a></li>
        <li><a href="/webadmin/warehouse_transfer/list.php"><?=htmlspecialchars(_("List Transferred Inventory"))?></a></li>
        <li><a href="/webadmin/warehouse/?action=list"><?=htmlspecialchars(_("Inventory Warehouse"))?></a></li>
    </ul></td></tr>

    <tr><td><ul class="blocklist">
<?php
if ($temp = $DB->query_first(sprintf(
    "SELECT COUNT(*) AS `total` FROM `products` WHERE `feature` = 'y' AND `active` = 'y' AND (`featured_expiry` = '0000-00-00 00:00:00' OR `featured_expiry` >= %s)",
    $DB->escape(date('Y-m-d 00:00:00'))
), MYSQLI_ASSOC)) {
?>
        <li><a href="/webadmin/products_new/featured.php?"><?=htmlspecialchars(_("Featured Products"))?> (<?=$temp['total']?>)</a></li>
<?php
}
?>
        <li><a href="/webadmin/products_new/problems.php"><?=htmlspecialchars(_("Problem Products"))?></a></li>
    </ul></td></tr>

	<tr><td><ul class="blocklist">
        <li><a href="/webadmin/products_new/?action=search"><?=htmlspecialchars(_("Search Products"))?></a></li>
    </ul></td></tr>
    <tr><td><ul class="blocklist">
        <li><a href="/webadmin/product_supported/"><?=htmlspecialchars(_(" Products Supported"))?></a></li>
    </ul></td></tr>
    <tr><td><ul class="blocklist">
        <li><a href="/webadmin/product_series/"><?=htmlspecialchars(_(" Products Series"))?></a></li>
    </ul></td></tr>
    <tr><td><ul class="blocklist">
        <li><a href="/webadmin/harmonised_code/"><?=htmlspecialchars(_(" Harmonised Code"))?></a></li>
    </ul></td></tr><br>
    <tr><td><ul class="blocklist">
        <li><a href="/webadmin/clearance/index.php"><?=htmlspecialchars(_(" Clearance Products"))?></a></li>
    </ul></td></tr><br>
</table>
