<?php
global $_GLOBALS;
global $DB;

$payment_methods_distinct = array();
$payment_methods_by_site  = array();
if ($query = $DB->query(sprintf(
    "SELECT * FROM `highwaygroup`.`payment_methods`%s",
    (11 != $_GLOBALS['site_settings']['site_id']) ? sprintf(
        " WHERE `site_id` = 0 OR `site_id` = %d",
        $_GLOBALS['site_settings']['site_id']
    ) : ""
))) {
    while ($row = $DB->fetch_assoc($query)) {
        $payment_methods_distinct[$row['payment_method_id']]                 = $row['payment_method_id'];
        $payment_methods_by_site[$row['site_id']][$row['payment_method_id']] = $row;
    }
    $DB->free_result($query);
}

$site_list    = getWebsitesList();
$site_list[0] = 'All Websites';
$site_list    = array_intersect_key($site_list, $payment_methods_by_site);

uasort($payment_methods_distinct, function ($a, $b) {
    return strcmp(paymentName($a), paymentName($b));
});

$payment_method_public = array(
    'y' => 'Yes',
    'n' => 'No',
);
?>
<table class="wide" style="border:1px solid #CCCCCC" border="1" bordercolor="#CCCCCC" align="center">
	<tr>
		<td style="background-color:#EEEEEE;text-align:center" rowspan="2" valign="middle"><b>Payment Type</b></td>
<?php
foreach ($site_list as $site_id => $site_name) {
?>
		<th colspan="4"><?=htmlspecialchars($site_name)?></th>
<?php
}
?>
	</tr>
	<tr style="text-align:center">
<?php
$alt = false;
foreach ($site_list as $site_id => $site_name) {
    $style = ($alt = !$alt) ? 'class="left_title" style="text-align:center"' : "";
?>
		<td <?=$style?>><b>Payment Details</b></td>
        <td <?=$style?>><b>Payment Method Active</b></td>
		<td <?=$style?>><b>Accepted Currencies</b></td>
        <td <?=$style?>>&nbsp;</td>
<?php
}
?>
	</tr>
<?php
$tr_count = 0;
foreach ($payment_methods_distinct as $payment_method_id) {
?>
	<tr style="background-color:#<?=($tr_count % 2) ? 'F6F6F6' : 'FFFFFF'?>">
		<td style="text-align:center"><b><?=paymentName($payment_method_id)?></b></td>
<?php
$count = 0;
    foreach ($site_list as $site_id => $site_name) {
        if (array_key_exists($payment_method_id, $payment_methods_by_site[$site_id])) {
            $method = $payment_methods_by_site[$site_id][$payment_method_id];
?>
		<td style="padding:8px;white-space:nowrap"><?=nl2br(htmlentities($method['payment_method_description']))?></td>
		<td style="padding:8px;text-align:center"><?=nl2br(htmlentities($payment_method_public[$method['payment_method_public']]))?></td>
		<td style="padding:8px;text-align:center"><?=nl2br(htmlentities($method['accepted_currencies']))?></td>
		<td style="padding:8px;text-align:center"><a style="color:blue" href="payment_options.php?action=edit&amp;id=<?=$payment_method_id?>&amp;site_id=<?=$site_id?>">EDIT</a></td>
<?php
} else {
?>
        <td style="background:#fee;text-align:center" colspan="4"><a class="unfinished" style="color:blue" href="payment_options.php?action=add&amp;id=<?=$payment_method_id?>&amp;site_id=<?=$site_id?>">ADD<!-- (<?=htmlspecialchars($site_name)?> - <?=paymentName($payment_method_id)?>)--></a></td>
<?php
}
        $count++;
    }
?>
	</tr>
<?php
$tr_count++;
}
?>
</table>
