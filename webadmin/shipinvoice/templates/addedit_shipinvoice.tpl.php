<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
$form_action = 'list';
if ('edit' == $action) {
    $form_action = 'do_edit';
}

if ('add' == $action) {
    $form_action = 'do_add';
}
?>

<script type="text/javascript">
//<![CDATA[
function validateInvoice()
{
	return true;
}
function previewShipInvoice(id, theFrm, pdf)
{
	if(pdf==null) pdf=false;
	var oldact=theFrm.action;
	var oldtrg=theFrm.target;
	theFrm.action="/webadmin/shipinvoice/shipinvoice_preview.php?sinvoice_id="+id+((pdf)?'&format=pdf':'');
	theFrm.target="_blank";
	theFrm.submit();
	theFrm.action=oldact;
	theFrm.target=oldtrg;
}
//]]>
</script>

<form method="post" action=".?action=<?=$form_action?>">

<?php
// need to display the changes that were made in the last 5 mins or so.
if ('add' != $action) {
    $query = $DB->query(" SELECT UNIX_TIMESTAMP(message_date) AS message_time, message_type, message_notes FROM `highwaygroup`.`shipinvoice_messages` WHERE sinvoice_id=" . intval($sinvoice_id) . " AND message_date>" . formatSQLDateTime(time() - 255) . " AND message_sender=" . intval($staff_id) . " AND message_type=0 ORDER BY message_date DESC");
    if ($DB->num_rows($query)) {
?>
<div style="color:red;text-align:left;padding:5px;">Changes made in the last 5 mins :</div><br />
<?php
while ($message = $DB->fetch_assoc($query)) {
            $shade = substr('00' . dechex(min(255, (time() - $message['message_time']))), -2);
            $notes = explode(" ", $message['message_notes']);
            foreach ($notes as $key => $string) {
                $notes[$key] = trim(chunk_split($string, 80));
            }

            $notes = implode(" ", $notes);
?>
<div style="color:#FF<?=$shade . $shade?>;text-align:left;width:95%;">
	<li><?=nl2br(htmlspecialchars($notes))?></li>
</div>
<br /><br />
<?php
}
    }
}
?>

<?php
switch ($form_action) {
    case 'do_add':
    case 'do_edit':
        include __DIR__ . '/member_table.tpl.php';
        include __DIR__ . '/product_supplier.tpl.php';
        include __DIR__ . '/freight_table.tpl.php';
        include __DIR__ . '/payment_table.tpl.php';
        include __DIR__ . '/additional_table.tpl.php';
        include __DIR__ . '/shipinvoice_history.tpl.php';
        break;
}

?>
<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionShow\');">Show On Invoice</a>')?>
<div id="sectionShow">
<table class="wide" style="width:50%;">
	<tr style="background-color:transparent;">
		<td><input type="checkbox" name="show_order" value="y" <?=$sinvoice['show_order'] == 'y' ? 'checked' : ''?>/></td><td><b>Order Reference</b></td>
	</tr>
	<tr style="background-color:transparent;">
		<td><input type="checkbox" name="show_freight" value="y" <?=$sinvoice['show_freight'] == 'y' ? 'checked' : ''?>/></td><td><b>Courier/Freight Type</b></td>
	</tr>
	<tr style="background-color:transparent;">
		<td><input type="checkbox" name="show_freight_cost" value="y" <?=$sinvoice['show_freight_cost'] == 'y' ? 'checked' : ''?>/></td>
		<td><b>Courier/Freight Cost</b></td>
	</tr>
	<tr style="background-color:transparent;">
		<td><input type="checkbox" name="show_payment" value="y" <?=$sinvoice['show_payment'] == 'y' ? 'checked' : ''?>/></td><td><b>Payment Type</b></td>
	</tr>
	<tr style="background-color:transparent;">
		<td><input type="checkbox" name="show_payment_info" value="y" <?=$sinvoice['show_payment_info'] == 'y' ? 'checked' : ''?>/></td><td><b>Payment Information</b></td>
	</tr>
	<tr style="background-color:transparent;">
		<td><input type="checkbox" name="show_signature" value="y" <?=$sinvoice['show_signature'] == 'y' ? 'checked' : ''?>/></td><td><b>Show Signature</b></td>
	</tr>
	<tr style="background-color:transparent;">
		<td><input type="checkbox" name="show_stamp" value="y" <?=$sinvoice['show_stamp'] == 'y' ? 'checked' : ''?>/></td><td><b>Show Stamp</b></td>
	</tr>
	<tr style="background-color:transparent;">
		<td><input type="checkbox" name="is_product_harmonised_code" value="y" <?=$sinvoice['is_product_harmonised_code'] == 'y' ? 'checked' : ''?>/></td><td><b>Harmonised Code</b></td>
	</tr>
	<tr style="background-color:transparent;">
		<td><input type="checkbox" name="is_product_origin_country" value="y" <?=$sinvoice['is_product_origin_country'] == 'y' ? 'checked' : ''?>/></td><td><b>Country Of Origin</b></td>
	</tr>
	<tr style="background-color:transparent;">
		<td><input type="checkbox" name="is_product_materials" value="y" <?=$sinvoice['is_product_materials'] == 'y' ? 'checked' : ''?>/></td><td><b>Materials</b></td>
	</tr>
	<tr style="background-color:transparent;">
		<td><input type="checkbox" name="is_product_usage" value="y" <?=$sinvoice['is_product_usage'] == 'y' ? 'checked' : ''?>/></td><td><b>Usage</b></td>
	</tr>
</table>
</div>
<br /><br />
<br />
<p>Address<?= $sinvoice_addresses ?>: <select style="width:auto;" name="invoice_address">
    <option value="0"> Select Address</option>
    <?=formatHTMLOptions(getActiveInvoiceAddressList(), $sinvoice_addresses)?>
</select></p>
<br />
<br />
<?php
if ($sinvoice_id > 0) {
?>
<input type="hidden" name="sinvoice_id" value="<?=$sinvoice_id?>" />
<?php
}
?>
<input type="button" value="Submit Changes" onclick="javascript:if(validateInvoice()) this.form.submit();" />
<?php
if (shipinvoiceExists($sinvoice_id)) {
?>
<input type="button" value=" View Saved Invoice " onclick="window.open('.?action=view&sinvoice_id=<?=$sinvoice_id?>');" />
<input type="button" value="Print Shipping Label" onclick="window.open('.?action=shiplabel&sinvoice_id=<?=$sinvoice_id?>');" />
<?php
}
?>
<input type="button" value=" Preview Current Invoice " onclick="previewShipInvoice(<?=$sinvoice_id?>,this.form);" />
<input type="button" value=" Preview Current Invoice (PDF)" onclick="previewShipInvoice(<?=$sinvoice_id?>,this.form,true);" />
<?php
if ('Jwalit' == $_SERVER['REMOTE_USER'] || 'Daniel' == $_SERVER['REMOTE_USER']) {
?>
<input type="button" value="Form Preview" onclick="javascript:testPost(this.form);" />
<?php
}
?>
</form>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
