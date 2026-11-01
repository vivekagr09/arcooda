<!-- insurance_addedit.tpl.php -->

<?php
$use_custom=false;
if($insurance['customer_charge']==0.0) $use_custom=true;
?>

<script type="text/javascript">
//<![CDATA[
function validateInsurance()
{
	return true;
}
//]]>
</script>

<?php
if($action!='add')
{
	$changes=array();
	//$query=$DB->query("SELECT UNIX_TIMESTAMP(message_date) AS message_time, message_type_id, message_title, message_notes FROM highwaygroup.invoice_messages WHERE invoice_id=".intval($invoice_id)." AND message_date>".formatSQLDateTime(time()-255)." AND message_sender=".intval($staff_id)." ORDER BY message_date DESC");
	//while($row=$DB->fetch_assoc($query)) $changes[]=$row;
	if(count($changes))
	{
?>
<div style="color:red;text-align:left;padding:5px;">Changes made in the last 5 mins :</div>
<?php
		foreach($changes as $message)
		{
			$shade=substr('00'.dechex(min(255,(time()-$message['message_time']))),-2);
			$notes=explode(" ",$message['message_notes']);
			foreach($notes as $key=>$string)
				$notes[$key]=trim(chunk_split($string,80));
			$notes=implode(" ",$notes);
?>
<div style="color:#FF<?php echo $shade.$shade ?>;text-align:left;width:95%;">
	<li><?php hecho($message['message_title']) ?> : <?php echo nl2br(hecho($notes, 1)) ?></li>
</div>
<br /><br />
<?php
		}
	}
}

/*
if($invoice_lock['staff_id']!=$staff_id && $action!='add')
{
?>
<table class="wide" align="center" style="border:1px solid black;">
	<tr><td style="font-weight:bold;background-color:pink;text-align:center;padding:8px 8px 8px 8px;">
<?php
	if($invoice_lock===false)
		echo 'This insurance is not locked to you; You cannot submit any changes.';
	else
	{
		$time_to_live=$invoice_lock['expiry_time']-$_SERVER['REQUEST_TIME'];
		echo 'This insurance is currently locked to '.$all_staff_list[$invoice_lock['staff_id']].', please wait for '.intval($time_to_live/60).' Minutes '.($time_to_live%60).' Seconds and try again.';
	}
?>
	</td></tr>
</table>
<br /><br />
<?php
}
*/

?>
<form method="post" action="index.php?action=do_edit">

<?php echo formatHTMLTitle('<a href="javascript:toggleSection(\'sectionCosting\');">Costing</a>') ?>
<div id="sectionCosting">

<table class="wide">
	<tr>
		<td class="left_title" style="width:25%;">Start Date</td>
		<td><input name="contract_date_from" style="width:236px;" value="" /><input style="width:20px;" value="V" onclick="gfPop.fPopCalendar(getElement('contract_date_from'))" type="button" /></td>
	</tr>
	<tr>
		<td class="left_title">End Date</td>
		<td><input name="contract_date_till" style="width:236px;" value="" /><input style="width:20px;" value="V" onclick="gfPop.fPopCalendar(getElement('contract_date_till'))" type="button" /></td>
	</tr>
	<tr>
		<td class="left_title">Premium</td>
		<td><input name="insurance_contract[contract_premium]" style="text-align:right;" type="text" value="" /></td>
	</tr>
</table>
<br />
<br />
<?php
if(count($insurance_contracts))
{
?>
<table class="wide">
	<tr>
		<th>Date From</th>
		<th>Date Till</th>
		<th>Premium</th>
	</tr>
<?php
	foreach($insurance_contracts as $contract)
	{
?>
	<tr>
		<td style="text-align:center;"><?php echo date('d/m/Y',$contract['contract_commence_time']) ?></td>
		<td style="text-align:center;"><?php echo date('d/m/Y',$contract['contract_complete_time']) ?></td>
		<td style="text-align:center;">$<?php echo convertCurrency($contract['contract_premium'],'AUD') ?></td>
	</tr>
<?php
	}
?>
</table>
<br />
<br />
<?php
}
?>
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionCosting_hideNote">
	Costing can be viewed by Clicking "Costing" above<br />
</div>

<!--
<table class="wide">
	<tr>
		<td class="left_title" style="width:25%;">Yearly Insurance Premium</td>
		<td><input style="text-align:right;" type="text" name="insurance[yearly_premium]" value="<?php echo floatval($insurance['yearly_premium']) ?>" onchange="setValue('insurance[weekly_premium]',convertCurrency(this.value*7/365.25,'AUD'))" /></td>
	</tr>
	<tr>
		<td class="left_title">Weekly Insurance Premium</td>
		<td><input style="text-align:right;" type="text" name="insurance[weekly_premium]" value="<?php echo floatval($insurance['weekly_premium']) ?>" onchange="setValue('insurance[yearly_premium]',convertCurrency(this.value*365.25/7,'AUD'))" /></td>
	</tr>
</table>
<br />
<br />
-->

<?php
function insurance_rate_row($rcount='[RATE_COUNT]',$rate=array())
{
	ob_start();
	$hidden=array();
?>
<tr id="insurance_rate_rowa[<?php echo $rcount ?>]">
	<td>$<?php echo str_replace("\n","",formatHTMLInputs($hidden)) ?><input style="text-align:right;" type="text" name="insurance_rate[<?php echo $rcount ?>][rate_start]" value="<?php echo $rate['rate_start'] ?>" /> AUD</td>
	<td>To</td>
	<td>$<input style="text-align:right;" type="text" name="insurance_rate[<?php echo $rcount ?>][rate_finish]" value="<?php echo $rate['rate_finish'] ?>" /> AUD</td>
	<td><input style="text-align:right;" type="text" name="insurance_rate[<?php echo $rcount ?>][rate_rate]" value="<?php echo $rate['rate_rate'] ?>" />%</td>
	<td><input type="button" value="Delete" onclick="deleteRate(<?php echo $rcount ?>)" /></td>
</tr>
<?php
	return ob_get_clean();
}
?>

<script type="text/javascript">
var rate_count=<?php echo max(100,count($insurance_rates)) ?>;

function addRate()
{
	var rcount=String(rate_count);
	var rate_table=getElement("rate_table");
	if(rate_table!=null)
	{
		var newRow1=addTableRow(rate_table,'insurance_rate_rowa['+rcount+']');
<?php
$count=0;
foreach(explode("\n",insurance_rate_row('[RATE_COUNT]',array('rate_rank'=>'[RATE_COUNT]'))) as $row)
{
	$row=trim($row);
	if(substr($row,0,3)=='<tr')
		$count++;
	elseif (substr($row, 0, 4) == '<td>') echo "addTableCell(newRow" . $count . ", " . str_replace("[RATE_COUNT]", '" + rcount + "', jecho(substr($row, 4, -5), 1)) . ");\n";
}
?>
	}
	rate_count++;
}

function deleteRate(row)
{
	var theRow=getElement("insurance_rate_rowa["+row+"]");
	if(theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
}

function onchangePricing()
{
	var new_value='';
	var elems=getElements('insurance[pricing_type]');
	for(var i=0;i<elems.length;i++)
		if(elems[i].checked==true)
			new_value=elems[i].value;
	if(new_value!='fixed') setValue('insurance[customer_charge]','');
	toggleSection('pricing_fixed',(new_value=='fixed'));
	toggleSection('pricing_brackets',(new_value=='brackets'));
	toggleSection('pricing_courier',(new_value=='courier'));
	toggleSection('pricing_freight',(new_value=='freight'));
}
</script>

<?php
$rates_affected=array();
foreach($insurance_rates as $rate)
	$rates_affected[]=$rate['rate_count'];
?>
<input type="hidden" name="rates_affected" value="<?php echo implode(',',$rates_affected) ?>" />

<?php echo formatHTMLTitle('<a href="javascript:toggleSection(\'sectionCustomerPricing\');">Customer Pricing</a>') ?>
<div id="sectionCustomerPricing">

<table style="width:auto;">
	<tr><td><input name="insurance[pricing_type]" type="radio" onclick="onchangePricing();" value="fixed"<?php echo ($insurance['pricing_type']!='fixed')?'':' checked="checked"' ?> /> Fixed % Throughout</td></tr>
	<tr><td><input name="insurance[pricing_type]" type="radio" onclick="onchangePricing();" value="brackets"<?php echo ($insurance['pricing_type']!='brackets')?'':' checked="checked"' ?> /> Value Based %</td></tr>
	<tr><td><input name="insurance[pricing_type]" type="radio" onclick="onchangePricing();" value="courier"<?php echo ($insurance['pricing_type']!='courier')?'':' checked="checked"' ?> /> Courier Based %</td></tr>
	<tr><td><input name="insurance[pricing_type]" type="radio" onclick="onchangePricing();" value="freight"<?php echo ($insurance['pricing_type']!='freight')?'':' checked="checked"' ?> /> Freight Type %</td></tr>
</table>
<br />
<br />

<div id="pricing_fixed"<?php echo ($insurance['pricing_type']!='fixed')?' style="display:none;"':'' ?>>
<table class="wide">
	<tr>
		<td class="left_title" style="width:25%;">Insurance to Charge Customer</td>
		<td><input type="text" style="text-align:right;" name="insurance[customer_charge]" value="<?php echo floatval($insurance['customer_charge']) ?>" />%</td>
	</tr>
</table>
<br />
<br />
</div>

<div id="pricing_brackets"<?php echo ($insurance['pricing_type']!='brackets')?' style="display:none;"':'' ?>>
<table class="wide" id="rate_table">
	<tr>
		<th>From</th>
		<th></th>
		<th>To</th>
		<th>Rate</th>
		<th></th>
	</tr>
<?php
$rcount=1;
foreach($insurance_rates as $rate)
	echo insurance_rate_row($rcount++,$rate);
?>
</table>
<br />
<br />
</div>

<div id="pricing_courier"<?php echo ($insurance['pricing_type']!='courier')?' style="display:none;"':'' ?>>
<table class="wide">
	<tr>
		<th>Freight ID</th>
		<th>Name of Freight</th>
		<th>% Insurance</th>
	</tr>
<?php
$freight_list=getAllFreightServices();
asort($freight_list);
foreach($freight_list as $key=>$value)
{
?>
	<tr>
		<td style="text-align:center;"><?php echo intval($key) ?></td>
		<td style="text-align:center;"><?php hecho($value) ?></td>
		<td style="text-align:center;"><input type="text" class="unfinished" /></td>
	</tr>
<?php
}
?>
</table>
<br />
<br />
</div>

<div id="pricing_freight"<?php echo ($insurance['pricing_type']!='freight')?' style="display:none;"':'' ?>>
<table class="wide">
	<tr>
		<td class="left_title">Courier</td>
		<td><input type="text" class="unfinished" />%</td>
	</tr>
	<tr>
		<td class="left_title">Truck</td>
		<td><input type="text" class="unfinished" />%</td>
	</tr>
	<tr>
		<td class="left_title">Sea Cargo</td>
		<td><input type="text" class="unfinished" />%</td>
	</tr>
	<tr>
		<td class="left_title">Air Cargo</td>
		<td><input type="text" class="unfinished" />%</td>
	</tr>
</table>
<br />
<br />
<table class="wide">
	<tr>
		<td>If value of shipment over $<input type="text" class="unfinished" />, insurance charge <input type="text" class="unfinished" />%</td>
	</tr>
</table>
<br />
<br />
</div>
</div>

<input type="button" value="Submit Changes"<?php echo ($invoice_lock['staff_id']==$staff_id || $action=='add' || true)?' onclick="javascript:if(validateInsurance()) this.form.submit();"':' disabled="disabled"' ?> />
<input type="button" value="Print" onclick="javascript:window.print();" />

</form>

<!-- /insurance_addedit.tpl.php -->