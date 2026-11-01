<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
$invoice_companies = invoiceGetOptionList('site_id');

//Quick Nav Table
$invoice_by_staff = array();
$invoice_actions  = invoiceGetOptionList("invoice_action");

//$invoice_by_action = array();
$totals_by_action = array();
$totals_by_site   = array();
$total_total      = 0;
$query            = $DB->query("SELECT `invoice_action`, `site_id`, COUNT(`invoice_id`) AS `invoices` FROM `highwaygroup`.`invoices` WHERE `invoice_id` IN(" . implode(', ', $invoice_list) . ") GROUP BY `invoice_action`, `site_id`");
while ($row = $DB->fetch_assoc($query)) {
    $invoice_by_action[$row['invoice_action']][$row['site_id']] = $row['invoices'];
    $totals_by_action[$row['invoice_action']] += $row['invoices'];
    $totals_by_site[$row['site_id']] += $row['invoices'];
    $total_total += $row['invoices'];
}
$DB->free_result($query);

$width = 100 / (count($totals_by_site) + 2);
ksort($totals_by_site);
$total_url           = $get_vars;
$total_url['action'] = 'list';
$total_url           = '.?' . http_build_query($total_url);
?>
<table class="wide spacetable" align="center">
	<tr>
		<th style="height:32px;width:<?=$width
?>%;"></th>
<?php
foreach ($totals_by_site as $site_id => $site_total) {
    $site_url            = $get_vars;
    $site_url['action']  = 'list';
    $site_url['site_id'] = $site_id;
    $site_url            = '.?' . http_build_query($site_url);
?>
		<th style="width:<?=$width
?>%;"><a href="<?=htmlspecialchars($site_url)?>"><?=htmlspecialchars((array_key_exists($site_id, $invoice_companies)) ? $invoice_companies[$site_id] : 'Other')?></a></th>
<?php
}
?>
		<th style="width:<?=$width
?>%;">TOTAL</th>
	</tr>
<?php
$alt = false;
//echo '<pre>'; print_r($invoice_by_action); die;
foreach ($invoice_by_action as $invoice_action => $by_site) {
    $staff_url                   = $get_vars;
    $staff_url['action']         = 'list';
    $staff_url['invoice_action'] = $invoice_action;
    $staff_url                   = '.?' . http_build_query($staff_url);
?>
	<tr<?=($alt = !$alt) ? '' : ' class="alt"'?>>
		<th style="height:32px;"><a href="<?=htmlspecialchars($staff_url)?>"><?=htmlspecialchars(('' == $invoice_action) ? 'Not Selected' : $invoice_actions[$invoice_action])?></a></th>
<?php
foreach ($totals_by_site as $site_id => $site_total) {
        $site_url                   = $get_vars;
        $site_url['action']         = 'list';
        $site_url['site_id']        = $site_id;
        $site_url['invoice_action'] = $invoice_action;
        $site_url                   = '.?' . http_build_query($site_url);
?>
		<td style="text-align:center;"><a href="<?=htmlspecialchars($site_url)?>"><?=intval($by_site[$site_id])?></a></td>
<?php
}
?>
		<td style="text-align:center;"><a href="<?=htmlspecialchars($staff_url)?>"><?=$totals_by_action[$invoice_action]?></a></td>
	</tr>
<?php
}
?>
	<tr<?=($alt = !$alt) ? '' : ' class="alt"'?>>
		<th style="height:32px;">TOTAL</th>
<?php
foreach ($totals_by_site as $site_id => $site_total) {
    $site_url            = $get_vars;
    $site_url['action']  = 'list';
    $site_url['site_id'] = $site_id;
    $site_url            = '.?' . http_build_query($site_url);
?>
		<td style="text-align:center;"><a href="<?=htmlspecialchars($site_url)?>"><?=$site_total?></a></td>
<?php
}
?>
		<td style="text-align:center;"><a href="<?=htmlspecialchars($total_url)?>"><?=intval($total_total)?></a></td>
	</tr>
</table>
<br />
<br />

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
