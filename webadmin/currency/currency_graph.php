<?php
include_once('currency_common.inc.php');

$currency_id1='AUD';
$currency_id2='USD';

if($posted['currency_to']!='')
	$currency_id1=strtoupper($posted['currency_to']);
if($posted['currency_from']!='')
	$currency_id2=strtoupper($posted['currency_from']);

$currency_history1=currencyHistoryFromDatabase($currency_id1);
$currency_history2=currencyHistoryFromDatabase($currency_id2);

if($currency_id1=='USD') $currency_history1[]=array('change_time'=>0,'usto'=>1.0);
if($currency_id2=='USD') $currency_history2[]=array('change_time'=>0,'usto'=>1.0);

if(count($currency_history1)<1 || count($currency_history2)<1)
{
	header('HTTP/1.0 404 Not Found');
	echo 'No Data Found for selected currencies';
	die();
}

$graph_dates=array();
for($i=0;$i<256;$i++)
	$graph_dates[]=date('Ymd', mktime(0, 0, 0, date('m',time()), date('d',time())-$i, date('Y',time())));
$graph_dates=array_reverse($graph_dates);

$graph_data1=array();
foreach($currency_history1 as $history)
	foreach($graph_dates as $date)
		if(date('Ymd',$history['change_time'])<=$date)
			$graph_data1[$date]=$history['usto'];

$graph_data2=array();
foreach($currency_history2 as $history)
	foreach($graph_dates as $date)
		if(date('Ymd',$history['change_time'])<=$date)
			$graph_data2[$date]=$history['usto'];

$scaled_data=array();
foreach($graph_dates as $date)
{
	$scaled=0.0;
	if($graph_data2[$date]!=0.0 && $graph_data1[$date]!=0.0)
	{
		$scaled=$graph_data1[$date]/$graph_data2[$date];
		$scaled_data[$date]=$scaled;
	}
}

$min_scale=$max_scale=reset($scaled_data);
foreach($scaled_data as $value)
{
	$min_scale=min($min_scale,$value);
	$max_scale=max($max_scale,$value);
}

$x_scale=2;
$width=count($scaled_data)*$x_scale;
$height=256;
$img=imagecreate($width,$height);
$col_white=imagecolorallocate($img,255,255,255);
$col_black=imagecolorallocate($img,0,0,0);
$col_blue=imagecolorallocate($img,0,0,255);

$min_scale=max(0.0,min($max_scale-0.5,$min_scale));
$min_scale=0.0;
$max_scale=$max_scale*1.5;
$max_scale=max(1.5,$max_scale);
$y_scale=$height/($max_scale-$min_scale);

imageline($img,0,$height-(0-$min_scale)*$y_scale,$width,$height-(0-$min_scale)*$y_scale,$col_blue);
imageline($img,0,$height-(1-$min_scale)*$y_scale,$width,$height-(1-$min_scale)*$y_scale,$col_blue);

$x=0;
$y=reset($scaled_data);
foreach($scaled_data as $value)
{
	$new_x=$x+$x_scale;
	$new_y=$value;
	imageline($img,$x,$height-($y-$min_scale)*$y_scale,$new_x,$height-($new_y-$min_scale)*$y_scale,$col_black);
	$x=$new_x;
	$y=$new_y;
}

imagerectangle($img,0,0,$width-1,$height-1,$col_blue);

header('Content-Type: image/png');
imagepng($img);
?>