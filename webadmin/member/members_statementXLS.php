<?php
/**
 * @var mixed[] $posted
 */
include_once __DIR__ . '/common.inc.php';

global $DB;

moduleLoad('invoice'); //invoiceProductsFromDatabase, invoiceSort, invoiceSearch, invoiceFromDatabase, invoiceFormatNumber
moduleLoad('writeexcel');

$member_id=intval($posted['member_id']);
$member=memberFromDatabase($member_id);
$member_invoices=array_reverse(invoiceSort(invoiceSearch(array('member_id'=>$member_id, 'invoice_status'=>array('sales','proforma','processed'))), 'open_date'));

//-----------------------------------------------------------------------------
/**
 * Old Stuff
 */
$invoice_cache=array();
foreach($member_invoices as $id) $invoice_cache[$id]=invoiceFromDatabase($id);

$use_cur='USD';
if($member['country']=='AU') $use_cur='AUD';

$invoice_count=count($member_invoices);

$payments = array();
if ($query = $DB->query(sprintf(
	"SELECT *, UNIX_TIMESTAMP(`payment_date`) AS `payment_time` FROM `highwaygroup`.`invoice_payments` WHERE `invoice_id` IN(%s) ORDER BY `payment_date` DESC",
	implode(', ', $member_invoices)
))) {
	while ($row = $DB->fetch_assoc($query)) {
		$payments[] = $row;
	}
	$DB->free_result($query);
}

$alltogether=array();
foreach($member_invoices as $invoice_id)
{
	$tmp=$invoice_cache[$invoice_id];
	$alltogether[]=$tmp['open_time'];
}
foreach($payments as $row) $alltogether[]=$row['payment_time'];
arsort($alltogether,SORT_NUMERIC);
$last_update=reset($alltogether);

$balance=$sale=$payby=0;
foreach($alltogether as $key=>$time)
{
	$payment=$invoice=array();
	if($key<$invoice_count)
	{
		$invoice_id=$member_invoices[$key];
		$invoice=$invoice_cache[$invoice_id];
		$pay=$invoice['invoice_offerau'];
		if($use_cur!='AUD') $pay=convertCurrency($invoice['invoice_offer'],$invoice['invoice_currency'],$use_cur);
		$balance+=$pay;
		$sale+=$pay;
	}
	else
	{
		$nkey=$key-$invoice_count;
		$payment=$payments[$nkey];
		$invoice_id=$payment['invoice_id'];
		$pay=$payment['payment_amountau'];
		if($pay=='' || $use_cur!='AUD') $pay=convertCurrency($payment['payment_amount'], $payment['payment_currency'], $use_cur);
		$balance-=$pay;
		$payby+=$pay;
	}
}
$sale=convertCurrency($sale,$use_cur);
$payby=convertCurrency($payby,$use_cur);
$balance=convertCurrency($balance,$use_cur);

$display_rows=array();
$start=6;
$rolling_balance=$balance;
foreach($alltogether as $key=>$time)
{
	$is_invoice=false;
	$payment=$invoice=array();
	if($key<$invoice_count)
	{
		$invoice_id=$member_invoices[$key];
		$invoice=$invoice_cache[$invoice_id];
		$pay=$invoice['invoice_offerau'];
		if($use_cur!='AUD') $pay=convertCurrency($invoice['invoice_offer'],$invoice['invoice_currency'],$use_cur);
		$new_balance=$rolling_balance-$pay;
		$is_invoice=true;
	}
	else
	{
		$nkey=$key-$invoice_count;
		$payment=$payments[$nkey];
		$invoice_id=$payment['invoice_id'];
		$pay=$payment['payment_amountau'];
		if($pay=='' || $use_cur!='AUD') $pay=convertCurrency($payment['payment_amount'], $payment['payment_currency'], $use_cur);
		$new_balance=$rolling_balance+$pay;
	}
	$invoice=$invoice_cache[$invoice_id];
	$invoice_currency=$invoice['invoice_currency'];
	$symbol=getCurrencySymbol($invoice_currency,'UTF8');
	$invoice_products=invoiceProductsFromDatabase($invoice_id);

	$products_tooltip=array();
	foreach($invoice_products as $product)
		$products_tooltip[]=$product['product_qty'].' x '.' # '.$product['product_id'].': '.$product['product_name'].' @ '.convertCurrency($product['product_offer'],$invoice_currency).' = '.$symbol. convertCurrency($product['product_offer']*$product['product_qty'], $invoice_currency).' '.$invoice_currency;

	$ship_offer=convertCurrency($invoice['ship_offer'],$invoice['invoice_currency']);
	if($invoice['ship_product_name']!='')
		$products_tooltip[]='1 x Freight: '.$invoice['ship_product_name'].' @ '.$ship_offer.' = '.$symbol.$ship_offer.' '.$invoice_currency;
	$products_tooltip=implode("\n",$products_tooltip);
	if($is_invoice) $payment['payment_text']=$products_tooltip;
	else $payment['payment_text']=reset(explode("\n",$payment['payment_text']));

	$invoice_number=invoiceFormatNumber($invoice_id,0,$invoice['site_id']);
	$rolling_balance=convertCurrency($rolling_balance,$use_cur);

	$display_rows[] = array(
		'date'          => date(_('d/m/Y'), $time),
		'reference'     => $invoice_number,
		'item'          => trim($payment['payment_text']),
		'pay_by_hg'     => ((!$is_invoice) ? $pay : ''),
		'pay_by_member' => (($is_invoice) ? $pay : ''),
		'balance'       => $rolling_balance,
		'remarks'       => '',
	);
	$start++;
	$rolling_balance=$new_balance;
}
//-----------------------------------------------------------------------------

$fname = tempnam("/tmp", "member_statement_".$member_id.".xls");
$workbook = new writeexcel_workbook($fname);

$fmt_title       = $workbook->addformat(array('bold'=>1,));
$fmt_subtitle    = $workbook->addformat(array('color'=>10,));
$fmt_subsubtitle = $workbook->addformat(array());
$fmt_border      = $workbook->addformat(array());
$fmt_border->set_bottom(5);
$fmt_normal = $workbook->addformat(array('valign'=>'vcenter','align'=>'center',));
$fmt_text   = $workbook->addformat(array('align'=>'left',));
$fmt_text->set_text_wrap(1);
$fmt_header           = $workbook->addformat(array('bold'=>1,'align'=>'center',));
$fmt_number           = $workbook->addformat(array('align'=>'right',));
$fmt_highlight_number = $workbook->addformat(array('align'=>'right','bg_color'=>'10',));

include __DIR__ . '/statement.xls.tpl.php';

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"member_statement_".$member_id.".xls\"");

$workbook->close();

header("Content-Length: ".filesize($fname));
readfile($fname);
unlink($fname);
?>