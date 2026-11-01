<!-- summary.tpl.php -->

<?php
moduleLoad('invoice'); //invoiceSort, invoiceSearch, invoiceGetValue, invoiceSumValue, invoiceFormatNumber

$last_inquiry_date = 'Never';
$inquiry_list      = invoiceSort(invoiceSearch(array(
	//@todo looks wrong
	'id'             => $id,
	'invoice_status' => array('enquiry', 'inquiry', 'closed', 'cancelled'),
)), 'open_date');
if (count($inquiry_list)) {
	$last_inquiry_date = date('d/m/Y', invoiceGetValue(end($inquiry_list), 'open_time'));
}

$last_invoice_date = 'Never';
$invoice_list      = invoiceSort(invoiceSearch(array(
	//@todo looks wrong
	'id'             => $id,
	'invoice_status' => array('proforma', 'sales', 'processed'),
)), 'open_date');
if (count($invoice_list)) {
	$last_invoice_date = date('d/m/Y', invoiceGetValue(end($invoice_list), 'open_time'));
}

$invoice_purchase_total = convertCurrency(invoiceSumValue($invoice_list,'invoice_offerus'),'USD');

$signup_country = getCountryInfo($weeklyspecial['signup_IP']);
if($signup_country['a2']=='') $signup_country['a2']='zz';
?>

<?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionWeeklySpecialSummary\');">WeeklySpecial Summary</a>') ?>

<div id="sectionWeeklySpecialSummary">
	<table class="wide">
		<tr>
			<td class="left_title" style="width:18%;">WeeklySpecial ID :</td>
			<td style="width:32%;"><?= $section_handler->formatNumber($id) ?></td>
			<td class="left_title" style="width:18%;">Last Order</td>
			<td style="width:32%;"><?= $last_invoice_date ?></td>
		</tr>
		<tr>
			<td class="left_title" style="width:18%;">Company Name :</td>
			<td style="width:32%;"><?= htmlspecialchars($weeklyspecial['company']) ?></td>
			<td class="left_title" style="width:18%;">Total Orders</td>
			<td style="width:32%;"><a href="/webadmin/invoice/?action=list&amp;status=proforma|sales|processed&amp;id=<?= $id ?>" target="_blank"><?= count($invoice_list) ?></a></td>
		</tr>
		<tr>
			<td class="left_title" style="width:18%;">Contact Person :</td>
			<td style="width:32%;"><?= htmlspecialchars($weeklyspecial['full_name']) ?></td>
			<td class="left_title" style="width:18%;">Last Inquiry</td>
			<td style="width:32%;"><?= $last_inquiry_date ?></td>
		</tr>
		<tr>
			<td class="left_title" style="width:18%;">WeeklySpecial Type :</td>
			<td style="width:32%;">
<?php
foreach(explode(',',$weeklyspecial['weeklyspecial_type']) as $site_id) if($site_id!='')
{
?>
				<img src="<?= htmlspecialchars('/webadmin/images/sites/site_'.$site_id.'/logo_16.png') ?>" alt="<?= htmlspecialchars($websites_list[$site_id]) ?>" />
				<?= ($weeklyspecial_discounts[$site_id]>0)?sprintf('%d%%',$weeklyspecial_discounts[$site_id]):'' ?>
<?php
}
?>
			</td>
			<td class="left_title" style="width:18%;">Total Inquiries</td>
			<td style="width:32%;"><a href="/webadmin/invoice/?action=list&amp;status=enquiry|inquiry|closed|cancelled&amp;id=<?= $id ?>" target="_blank"><?= count($inquiry_list) ?></a></td>
		</tr>
		<tr>
			<td class="left_title" style="width:18%;">Date Joined :</td>
			<td style="width:32%;"><?= ($weeklyspecial['created_time']!=0)?date("d/m/Y",$weeklyspecial['created_time']):'N/A' ?></td>
			<td class="left_title" style="width:18%;">Last Login</td>
			<td style="width:32%;"><?= $weeklyspecial_logins['last_login'] ?></td>
		</tr>
		<tr>
			<td class="left_title" style="width:18%;">Signup IP Address :</td>
			<td style="width:32%;">
				<a href="https://www.ip2location.com/<?= $weeklyspecial['signup_IP'] ?>" target="_blank"><?= htmlspecialchars($weeklyspecial['signup_IP']) ?></a>
<?php
if($signup_country['a2'] != '--' && $signup_country['a2'] != 'zz')
{
?>
				<img src="/webadmin/images/flags/<?= strtolower($signup_country['a2']) ?>.gif" alt="<?= htmlspecialchars($signup_country['country']) ?>" title="<?= htmlspecialchars($signup_country['country']) ?>" />
<?php
}
if (in_array(strtolower($weeklyspecial['browser']), array('ie', 'firefox', 'opera', 'safari'))) {
?>
				<img src="/webadmin/images/browsers/<?= strtolower($weeklyspecial['browser']) ?>.svg" width="16" height="16" alt="<?= $weeklyspecial['browser'] ?>" title="<?= $weeklyspecial['b_version'].' - '.$weeklyspecial['user_agent'] ?>" />
<?php
}
?>
			</td>
			<td class="left_title" style="width:18%;">Total Logins</td>
			<td style="width:32%;"><?= $weeklyspecial_logins['total_logins'] ?></td>
		</tr>
		<tr>
			<td class="left_title" style="width:18%;" rowspan="3">Open Invoices :</td>
			<td style="width:32%;" rowspan="3">
<?php
if(count($invoice_list))
{
	$open=array();
	foreach($invoice_list as $invoice_id) $open[]='<a href="/webadmin/invoice/?action=edit&amp;invoice_id='.$invoice_id.'" target="_blank">'.htmlspecialchars(invoiceFormatNumber($invoice_id)).'</a>';
	echo implode(', ',$open);
}
else echo 'None';
?>
			</td>
			<td class="left_title" style="width:18%;">Purchase Total</td>
			<td style="width:32%;">$<?= $invoice_purchase_total ?> USD</td>
		</tr>
		<tr>
			<td class="left_title" style="width:18%;">Outstanding Payment</td>
			<td style="background-color:yellow;width:32%;"></td>
		</tr>
		<tr>
			<td class="left_title" style="width:18%;">Status</td>
			<td style="width:32%;">
<?php
if($weeklyspecial['site_id']=='') echo 'Inactive';
elseif($weeklyspecial['site_id']==$weeklyspecial['weeklyspecial_type']) echo 'Active';
else echo 'Semi-Active';
?>
			</td>
		</tr>
	</table>
	<br /><br />
</div>

<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionWeeklySpecialSummary_hideNote">WeeklySpecial Summary can be viewed by Clicking "WeeklySpecial Summary" above<br /></div>

<!-- /summary.tpl.php -->
