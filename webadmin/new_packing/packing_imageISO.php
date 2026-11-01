<?php
require_once("packing_common.inc.php");

moduleLoad('invoice');

$error_list=array();
$font_file=$_SERVER['DOCUMENT_ROOT'] . '/webadmin/home_common/fonts/Arial.ttf';

//Set our viewport (for multi rendering)
$viewport=array('left'=>0,'right'=>640,'top'=>0,'bottom'=>480,'width'=>640,'height'=>480);

//Load all the post/get stuff
$posted = array_merge($_GET, $_POST);
foreach($viewport as $key=>$item)
	if(isset($posted['view_'.substr($key,0,1)]))
		$viewport[$key]=intval($posted['view_'.substr($key,0,1)]);
if(isset($posted['data']) && isset($posted['container_id']))
{
	$temp=unserialize(urldecode($posted['data']));
	$container=$temp[$posted['container_id']];
	unset($temp);
}
if(isset($posted['container']))
	$container=unserialize(urldecode($posted['container']));

$xvect=array(3.0,2.0);
$yvect=array(0.0,-2.0);
$zvect=array(-3.0,2.0);
$xrange=array(0.0,0.0);
$yrange=array(0.0,0.0);
for($c=0;$c<8;$c++)
{
	$xpos=(($c&1)==1)?$container->width:0.0;
	$ypos=(($c&2)==2)?$container->height:0.0;
	$zpos=(($c&4)==4)?$container->depth:0.0;

	$xrange[0]=min($xrange[0],$xvect[0]*$xpos+$yvect[0]*$ypos+$zvect[0]*$zpos);
	$xrange[1]=max($xrange[1],$xvect[0]*$xpos+$yvect[0]*$ypos+$zvect[0]*$zpos);
	$yrange[0]=min($yrange[0],$xvect[1]*$xpos+$yvect[1]*$ypos+$zvect[1]*$zpos);
	$yrange[1]=max($yrange[1],$xvect[1]*$xpos+$yvect[1]*$ypos+$zvect[1]*$zpos);
}
$xscale=($viewport['width']-1)/($xrange[1]-$xrange[0]);
$yscale=($viewport['height']-1)/($yrange[1]-$yrange[0]);

//Create the image
$img=imagecreatetruecolor($viewport['right']-$viewport['left'],$viewport['bottom']-$viewport['top']);
imageantialias($img,true);

//Build a palette
$palette=array();
$white=imagecolorallocate($img,255,255,255);
imagefilledrectangle($img,0,0,$viewport['right']-$viewport['left'],$viewport['bottom']-$viewport['top'],$white);
$black=imagecolorallocate($img,0,0,0);

$prod_col=array();
foreach($container->crate_list as $crate)
	$prod_col[$crate->packing_id]=0;
$col_angle=270.0/count($prod_col);
$col_count=0.0;
foreach($prod_col as $key=>$col)
{
	$col_count+=$col_angle;
	$col_cos=abs(cos($col_count));
	$col_sin=abs(sin($col_count));
	while($col_count>270.0) $col_count-=270.0;
	$red=$col_cos*255; $green=0; $blue=$col_sin*255;
	if($col_count>90) {$red=0; $green=$col_cos*255; $blue=$col_sin*255;}
	if($col_count>180) {$red=$col_sin*255; $green=$col_cos*255; $blue=0;}
	$prod_col[$key]=imagecolorallocate($img,$red,$green,$blue);
}

foreach($container->crate_list as $key=>$crate)
	$palette[$key]=$prod_col[$crate->packing_id];

$w=$container->width;
$h=$container->height;
$d=$container->depth;

$xorig=(0.0-$xrange[0])*$xscale;
$yorig=(0.0-$yrange[0])*$yscale;
$poly=array(
	$xorig+($w*$xvect[0])*$xscale,
	$yorig+($w*$xvect[1])*$yscale,
	$xorig+($w*$xvect[0]+$h*$yvect[0])*$xscale,
	$yorig+($w*$xvect[1]+$h*$yvect[1])*$yscale,
	$xorig+($h*$yvect[0])*$xscale,
	$yorig+($h*$yvect[1])*$yscale,
	$xorig+($h*$yvect[0]+$d*$zvect[0])*$xscale,
	$yorig+($h*$yvect[1]+$d*$zvect[1])*$yscale,
	$xorig+($d*$zvect[0])*$xscale,
	$yorig+($d*$zvect[1])*$yscale,
	$xorig+($d*$zvect[0]+$w*$xvect[0])*$xscale,
	$yorig+($d*$zvect[1]+$w*$xvect[1])*$yscale-2
);

imagepolygon($img,$poly,6,$black);
imageline($img,$xorig,$yorig,($w*$xvect[0]-$xrange[0])*$xscale,($w*$xvect[1]-$yrange[0])*$yscale,$black);
imageline($img,$xorig,$yorig,($h*$yvect[0]-$xrange[0])*$xscale,($h*$yvect[1]-$yrange[0])*$yscale,$black);
imageline($img,$xorig,$yorig,($d*$zvect[0]-$xrange[0])*$xscale,($d*$zvect[1]-$yrange[0])*$yscale,$black);

$sort=array();
foreach($container->crate_list as $key=>$crate)
	$sort[$key]=$crate->$x;
array_multisort($container->crate_list,$sort);

$sort=array();
foreach($container->crate_list as $key=>$crate)
	$sort[$key]=$crate->$z;
array_multisort($container->crate_list,$sort);

$sort=array();
foreach($container->crate_list as $key=>$crate)
	$sort[$key]=$crate->$y;
array_multisort($container->crate_list,$sort);

foreach($container->crate_list as $key=>$crate)
{
	$w=$crate->width;	$h=$crate->height;	$d=$crate->depth;
	$xorig=($crate->x*$xvect[0]+$crate->y*$yvect[0]+$crate->z*$zvect[0]-$xrange[0])*$xscale;
	$yorig=($crate->x*$xvect[1]+$crate->y*$yvect[1]+$crate->z*$zvect[1]-$yrange[0])*$yscale;
	$poly=array(
		$xorig+($w*$xvect[0])*$xscale,
		$yorig+($w*$xvect[1])*$yscale,
		$xorig+($w*$xvect[0]+$h*$yvect[0])*$xscale,
		$yorig+($w*$xvect[1]+$h*$yvect[1])*$yscale,
		$xorig+($h*$yvect[0])*$xscale,
		$yorig+($h*$yvect[1])*$yscale,
		$xorig+($h*$yvect[0]+$d*$zvect[0])*$xscale,
		$yorig+($h*$yvect[1]+$d*$zvect[1])*$yscale,
		$xorig+($d*$zvect[0])*$xscale,
		$yorig+($d*$zvect[1])*$yscale,
		$xorig+($d*$zvect[0]+$w*$xvect[0])*$xscale,
		$yorig+($d*$zvect[1]+$w*$xvect[1])*$yscale-1
	);
	imagefilledpolygon($img,$poly,6,$prod_col[$crate->packing_id]);
	imagepolygon($img,$poly,6,$black);
}

$link_list=array();
foreach($container->crate_list as $key=>$crate)
{
	if(isset($link_list[$crate->name]))
		$link_list[$crate->name][]=$key+1;
	else
	{
		$link=array($key+1);
		$link_list[$crate->name]=$link;
	}
}
$link_list=array_reverse($link_list);
$ypos=$viewport['height']-4;
foreach($link_list as $name=>$links)
{
	$ranges=convertListToRanges($links);
	$ids=array();
	foreach($ranges as $range)
		if($range[0]==$range[1])
			$ids[]='#'.$range[0];
		else
			$ids[]='#'.$range[0].'-'.$range[1];
	$ids=implode(',',$ids);
	imagefttext(
		$img,
		9.0,
		0.0,
		0,
		$ypos,
		$black,
		$font_file,
		$ids,
		array('linespacing' => 1.4)
	);
	imagefttext(
		$img,
		9.0,
		0.0,
		48,
		$ypos,
		$black,
		$font_file,
		' - ' . $name,
		array('linespacing' => 1.4)
	);
	$ypos-=10;
}

if(intval($_REQUEST['invoice_id'])>0)
{
	moduleLoad('invoice'); //invoiceFromDatabase, invoiceFormatNumber

	$details=invoiceFromDatabase($_REQUEST['invoice_id']);
	$ypos=0;

	$text=$details['customer_name'];
	$size=9;
	$dim = imageftbbox(
		$size,
		0.0,
		$font_file,
		$text,
		array('linespacing' => 1.4)
	);
	imagefttext(
		$img,
		$size,
		0.0,
		$viewport['width'] - $dim[4] - 1,
		$ypos - $dim[5],
		$black,
		$font_file,
		$text,
		array('linespacing' => 1.4)
	);
	$ypos+=10;

	if($details['invoice_status']=='Processed' || $details['invoice_status']=='Proforma')
	{
		$text='SI#'.substr('000000'.$details['invoice_id'],-6);
		if($details['revision_id']>0)
			$text.=chr($details['revision_id']+ord('A'));
	}
	else
		$text=invoiceFormatNumber($details['invoice_id']);
	$size=9;
	$dim = imageftbbox(
		$size,
		0.0,
		$font_file,
		$text,
		array('linespacing' => 1.4)
	);
	imagefttext(
		$img,
		$size,
		0.0,
		$viewport['width'] - $dim[4] - 1,
		$ypos - $dim[5],
		$black,
		$font_file,
		$text,
		array('linespacing' => 1.4)
	);
	$ypos+=10;

	$text=$container->name.' Container';
	$size=9;
	$dim = imageftbbox(
		$size,
		0.0,
		$font_file,
		$text,
		array('linespacing' => 1.4)
	);
	imagefttext(
		$img,
		$size,
		0.0,
		$viewport['width'] - $dim[4] - 1,
		$ypos - $dim[5],
		$black,
		$font_file,
		$text,
		array('linespacing' => 1.4)
	);
	$ypos+=10;
}

if(!isset($saveas_type)) $saveas_type='png';
switch($saveas_type)
{
case 'jpg':
case 'jpeg':
	header('Content-Type: image/jpeg');
	header('Content-Disposition: inline; filename=test.jpg');
	imagejpeg($img);
	break;
default:
	header('Content-Type: image/png');
	header('Content-Disposition: inline; filename="test.png"');
	imagepng($img);
}
if (function_exists('imagedestroy')) {
	imagedestroy($img);
}
$img = false;
