<?php
require_once("packing_common.php");

$font_file='/home/common/fonts/Arial.ttf';
$error_list=array();
//$error_list[]="Debug Mode";

$container_list=array();

$posted=undo_magicquotes($_REQUEST);

$viewport=array('left'=>0,'right'=>0,'top'=>0,'bottom'=>0);
foreach($viewport as $key=>$item)
	if(isset($posted['view_'.substr($key,0,1)]))
		$viewport[$key]=intval($posted['view_'.substr($key,0,1)]);

if(isset($posted['data']))
{
	$posted['data']=urldecode($posted['data']);
	$container_list=unserialize($posted['data']);
}
$width=isset($posted['width'])?$posted['width']:640;
if(isset($posted['con_str']))
{
	$container=new Container();
	$container->fromImgString($posted['con_str']);
	foreach($container->crate_list as $key=>$crate)
	{
		$row=$DB->query_first("SELECT product_name FROM products WHERE id=".$crate->packing_id);
		$container->crate_list[$key]->name=$row['product_name'];
	}
}
else
{
	$container_id=isset($posted['container_id'])?$posted['container_id']:0;
	$container=reset($container_list);
	for($i=0;$i<$container_id;$i++)
		$container=next($container_list);
}
if(isset($posted['container']))
	$container=unserialize(urldecode($posted['container']));

$crate_list=$container->crate_list;

$scale=$width/$container->width;
$height=$container->depth*$scale;
$img=imagecreate($width,$height);

$white=imagecolorallocate($img,255,255,255);
$black=imagecolorallocate($img,0,0,0);

imagerectangle($img,0,0,$width-1,$height-1,$black);

if(count($crate_list)>0)
{
	$palette=array();
	foreach($crate_list as $crate)
		$palette[$crate->packing_id]=0;

	$col_angle=270.0/count($palette);
	$col_count=0.0;

	foreach($palette as $key=>$col)
	{
		$col_count+=$col_angle;
		$col_cos=abs(cos($col_count));
		$col_sin=abs(sin($col_count));

		while($col_count>270.0) $col_count-=270.0;
		$red=$col_cos*255; $green=0; $blue=$col_sin*255;
		if($col_count>90) {$red=0; $green=$col_cos*255; $blue=$col_sin*255;}
		if($col_count>180) {$red=$col_sin*255; $green=$col_cos*255; $blue=0;}

		$palette[$key]=imagecolorallocate($img,$red,$green,$blue);
	}

	$drawn=array();
	$ypos=4;
	foreach($crate_list as $crate)
	{
		if(!isset($drawn[$crate->packing_id]))
		{
			$drawn[$crate->packing_id]=true;
			imagefilledrectangle($img,4,$ypos,20,$ypos+16,$palette[$crate->packing_id]);
			imagerectangle($img,4,$ypos,20,$ypos+16,$black);
			imagettftext($img,9,0,28,$ypos+13,$black,$font_file,$crate->name);
			$ypos+=16;
		}
	}
}

if(count($error_list)>0)
{
	echo nl2br(print_r($error_list,1));
}
else
{
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
		header('Content-Disposition: inline; filename=test.png');
		imagepng($img);
	}
	imagedestroy($img);
}

function printWordWrapped(&$image,$x,$y,$maxWidth,$maxHeight,$font,$color,$text,$textSize)
{
	$lineHeight=$textSize;
	$words=explode(' ',$text);
	$line='';
	foreach($words as $word)
	{
		$word=trim($word);
		$propose=($line=='')?$word:($line.' '.$word);
		$dimensions=imagettfbbox($textSize,0,$font,$propose);
		$lineHeight=max($lineHeight,$dimensions[1]-$dimensions[7]);
		$lineWidth=$dimensions[2]-$dimensions[0];
		if($lineWidth>$maxWidth)
		{
			$lines[]=$line;
			$line=$word;
		}
		else
			$line=$propose;
	}
	$lines[]=$line;

	foreach($lines as $key=>$row)
		if(trim($row)=='') unset($lines[$key]);
	if((count($lines)*$lineHeight)>$maxHeight)
		$lines=array_slice($lines,0,floor($maxHeight/$lineHeight));
	foreach($lines as $line)
		imagettftext($image,$textSize,0,$x,$y+=$lineHeight,$color,$font,$line);
}
?>