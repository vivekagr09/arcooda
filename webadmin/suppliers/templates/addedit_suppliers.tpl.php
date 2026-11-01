<?php

// NEVER EVER DO THIS AGAIN
// return;

//$sql=" SELECT UNIX_TIMESTAMP(date) AS message_time, query FROM suppliers_changes WHERE supplier_id=".intval($supplier_id)." AND `date`>".formatSQLDateTime(time()-255)." ORDER BY date DESC";
//$query=$DB->query($sql);
//if($DB->num_rows($query))
if (false) {
?>
<div style="color:red;text-align:left;padding:5px;">Changes made in the last 5 mins :</div><br />
<?php
    while ($message = $DB->fetch_assoc($query)) {
        $shade = substr('00' . dechex(min(255, (time() - $message['message_time']))), -2);
?>
<div style="color:#FF<?=$shade . $shade?>;text-align:left;width:95%;">
	<li><?=nl2br(htmlspecialchars($message['query']))?></li>
</div>
<br />
<?php
    }
}
?>
<input type="hidden" value="do_edit" name="action" />
<link rel="stylesheet" type="text/css" href="style.css" />

<style type="text/css">
.in
{
	width:300px;
}
.in2
{
	width:100%;
}

</style>
<?php
$country_list  = getCountryList();
$currency_list = getCurrencyList();

$dis_name = in_array('supp', explode('|', $posted['type'])) ? 'Supplier' : 'Manufacturer';

$hg_only    = array('about', 'meta', 'email');
$not_hg     = array('orders', 'bank', 'shipping');
$add_array  = array('about', 'meta', 'details', 'warehouse', 'shipping', 'contacts', 'bank', 'comments', 'values', 'for');
$edit_array = array('summary', 'about', 'meta', 'details', 'warehouse', 'shipping', 'contacts', 'attachments', 'message', 'bank', 'orders', 'comments', 'values', 'for');
$array      = ($add) ? $add_array : $edit_array;
foreach ($array as $table) {
    $include = true;
    if (1 != $posted['site_id'] && in_array($table, $hg_only)) {
        $include = false;
    }

    if (1 == $posted['site_id'] && in_array($table, $not_hg)) {
        $include = false;
    }

    if ($include) {
        include "suppliers_$table.tpl.php";
    }

}
?>
<div align="center"><input type="submit" value="<?=($add) ? 'Add' : 'Update'?>" style="width:100px"/></div>