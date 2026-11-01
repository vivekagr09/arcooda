<?php
include_once 'insurance_common.inc.php';
include_once 'templates/insurance_header.tpl.php';

$from_time=mktime(0,0,0,7,1,2003);
$to_time=$_SERVER['REQUEST_TIME'];
$grouped_by='monthly';

if(isset($posted['start_time'])) $from_time=intval($posted['start_time']);
if(isset($posted['end_time'])) $to_time=intval($posted['end_time']);
if(isset($posted['group_by'])) $grouped_by=$posted['group_by'];

if($to_time<$from_time) $to_time=$from_time+1;

if($posted['year']>0)
{
	$from_time=mktime(0,0,0,7,1,intval($posted['year']));
	$to_time=mktime(0,0,0,7,1,intval($posted['year'])+1);
}
if($posted['month']>0)
{
	$from_time=mktime(0,0,0,intval($posted['month'])+6,1,intval($posted['year']));
	$to_time=mktime(0,0,0,intval($posted['month'])+7,1,intval($posted['year']));
}
if($posted['quarter']>0)
{
	$from_time=mktime(0,0,0,$posted['quarter']*3+4,1,intval($posted['year']));
	$to_time=mktime(0,0,0,$posted['quarter']*3+7,1,intval($posted['year']));
}

$show_groups=array();
$package_list=array();

//Main split into tables
switch($grouped_by)
{
default:
case 'monthly':
case 'quarterly':
case 'weekly':
	if(date('m',$from_time)<7)
		$time1=mktime(0,0,0,7,1,date('Y',$from_time)-1);
	else
		$time1=mktime(0,0,0,7,1,date('Y',$from_time));

	while($time1<$to_time)
	{
		$time2=mktime(0,0,0,7,1,date('Y',$time1)+1);
		$show_groups[]=array('from_time'=>$time1,'to_time'=>$time2);
		$time1=$time2;
	}
	$show_groups=array_reverse($show_groups);
	break;
case 'yearly':
	if(date('m',$from_time)<7)
		$from_time=mktime(0,0,0,7,1,date('Y',$from_time)-1);
	else
		$from_time=mktime(0,0,0,7,1,date('Y',$from_time));
	if(date('m',$to_time)<7)
		$to_time=mktime(0,0,0,7,1,date('Y',$to_time));
	else
		$to_time=mktime(0,0,0,7,1,date('Y',$to_time)+1);
	$show_groups[]=array('from_time'=>$from_time,'to_time'=>$to_time);
	break;
}

/*
switch($grouped_by)
{
case 'quarterly':
	$group_url=$get_vars;
	$group_url['action']='quarter_summary';
	unset($group_url['start_time']);
	$group_url='index.php?'.http_build_query($group_url).'&start_time=';
	break;
default:
case 'monthly':
case 'weekly':
case 'yearly':
	$group_url=$get_vars;
	$group_url['action']='payment_summary';
	unset($group_url['start_time']);
	$group_url='index.php?'.http_build_query($group_url).'&start_time=';
	break;
}
*/

foreach($show_groups as $date_range)
{
	$package_list=array();

	$counter=$date_range['from_time'];
	while($counter<$date_range['to_time'])
	{
		switch($grouped_by)
		{
		default:
		case 'weekly':		$counter2=mktime(0,0,0,date('m',$counter),date('d',$counter)+7,date('Y',$counter));	break;
		case 'monthly':		$counter2=mktime(0,0,0,date('m',$counter)+1,1,date('Y',$counter));	break;
		case 'quarterly':	$counter2=mktime(0,0,0,date('m',$counter)+3,1,date('Y',$counter));	break;
		case 'yearly':		$counter2=mktime(0,0,0,7,1,date('Y',$counter)+1);	break;
		}
		if($counter2>$date_range['to_time']) $counter2=$date_range['to_time'];
		$package_list[$counter]=array('from_time'=>$counter, 'to_time'=>$counter2, 'contract_premium'=>0.0, 'uptake_amount'=>0.0, 'uptake_qty'=>0, 'invoices_qty'=>0);
		$counter=$counter2;
	}
	$package_list=array_reverse($package_list,true);

	$invoice_criteria=array('invoice_status'=>array('processed'));
	//$invoice_criteria=array();

	foreach(insuranceContractSearch(array(),$date_range['from_time'],$date_range['to_time']) as $contract_id)
	{
		$contract=insuranceContractFromDatabase($contract_id);
		$time_full=$contract['contract_complete_time']-$contract['contract_commence_time'];

		foreach($package_list as $key=>$package)
		{
			$time_end=min(min($contract['contract_complete_time'],$package['to_time']),$date_range['to_time']);
			$time_start=max(max($contract['contract_commence_time'],$package['from_time']),$date_range['from_time']);
			$time_part=max(0,$time_end-$time_start);
			$date_percent=0.0;
			if($time_full!=0) $date_percent=$time_part/($time_full+0.0);
			$package_list[$key]['contract_premium']+=$contract['contract_premium']*$date_percent;
		}
	}

	foreach(invoiceSearch($invoice_criteria,$date_range['from_time'],$date_range['to_time']) as $invoice_id)
	{
		$invoice=array('open_time'=>invoiceGetValue($invoice_id,'open_time'));
		foreach($package_list as $key=>$package)
			if($package['from_time']<=$invoice['open_time'] && $package['to_time']>$invoice['open_time'])
				$package_list[$key]['invoices_qty']++;
	}

	foreach(invoiceSearch(array('terms_id'=>'4')+$invoice_criteria,$date_range['from_time'],$date_range['to_time']) as $invoice_id)
	{
		$invoice=array('open_time'=>invoiceGetValue($invoice_id,'open_time'));
		foreach($package_list as $key=>$package)
			if($package['from_time']<=$invoice['open_time'] && $package['to_time']>$invoice['open_time'])
			{
				foreach(invoiceChargesFromDatabase($invoice_id) as $charge)
					if($charge['charge_id']==21)
						$package_list[$key]['uptake_amount']+=$charge['charge_offerau'];
				$package_list[$key]['uptake_qty']++;
			}
	}

	foreach(insuranceClaimSearch(array(),$date_range['from_time'],$date_range['to_time']) as $claim_id)
	{
		$claim=insuranceClaimFromDatabase($claim_id);
		foreach($package_list as $key=>$package)
			if($package['from_time']<=$claim['claim_time'] && $package['to_time']>$claim['claim_time'])
				$package_list[$key]['claims_qty']++;
	}

	$sect_id='sectionInsurances'.$date_range['from_time'];
	if(count($package_list)>0)
	{
?>
<?php echo formatHTMLTitle('<a href="javascript:toggleSection(\''.$sect_id.'\');">Insurance Summaries</a>', date('Y',$date_range['from_time']). '-'. date('Y',$date_range['to_time']). ((time()<$date_range['to_time'])?' Current Year':'')) ?><br />

<div id="<?php echo $sect_id ?>">
<?php
	include 'templates/insurance_summarylist.tpl.php';
?>
</div>

<br /><br />
<?php
	}
}


if($posted['year']!='' && ($posted['month']!='' || $grouped_by=='weekly') && false)
{
	$page_url=$get_vars;
	unset($page_url['month']);
	$page_url='insurance_summaries.php?'.http_build_query($page_url).'&month=';
	$legend=array(1=>'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun');
?>
<div>
	Showing Month :
	<?php echo formatHTMLChunkLinks($page_url,$posted['year'],$legend,$legend) ?>
</div>
<br />
<?php
}

$page_url=$get_vars;
unset($page_url['year']);
$page_url='insurance_summaries.php?'.http_build_query($page_url).'&year=';
$legend=array();
foreach(range(2003,date('Y')) as $year)
	$legend[$year]=$year;
?>
<div>
	Showing Year :
	<?php echo formatHTMLChunkLinks($page_url,$posted['year'],$legend) ?>
</div>
<br />
<?php

$export_url=$get_vars;
$export_url='insurance_summary_xls.php?'.http_build_query($export_url);

?>
<input type="button" value="Export To Excel" onclick="document.location=<?php echo formatSQLString($export_url) ?>" />
<?php

include_once 'templates/insurance_footer.tpl.php';
?>