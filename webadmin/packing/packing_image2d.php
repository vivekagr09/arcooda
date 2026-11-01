<?php
include_once("packing_common.inc.php");

moduleLoad('invoice'); //invoiceFromDatabase, invoiceFormatNumber

$font_file=$_SERVER['DOCUMENT_ROOT'] . '/webadmin/home_common/fonts/Arial.ttf';

$width=($posted['width']>0)?$posted['width']:640;
$scale=1.0;
$height=480;
$crate_list=array();
$invoice=array();
$real_width=0;
$real_height=0;

if($posted['imgstring']!='')
{
	$container=packingImgStringToContainer($posted['imgstring']);
	$scale=($width+0.0)/$container->getWidth();
	$height=$container->getDepth()*$scale;
	$crate_list=$container->crate_list;
	if($container->getHeight()==0)
	{
		$real_width=($container->getWidth()-$container->maximal['y'])*$scale;
		$real_height=($container->getDepth()-$container->maximal['y'])*$scale;
	}
}
if($posted['container']!='')
{
	$container=unserialize(gzuncompress(base64_decode($posted['container'])));
	$scale=($width+0.0)/$container->getWidth();
	$height=$container->getDepth()*$scale;
	$crate_list=$container->crate_list;
	if($container->getHeight()==0)
	{
		$real_width=($container->getWidth()-$container->maximal['y'])*$scale;
		$real_height=($container->getDepth()-$container->maximal['y'])*$scale;
	}
}
elseif($posted['product_id']>0)
{
	$site_id=$posted['site_id'];
	$product_id=$posted['product_id'];
//	$crate_list=....;
}

if($posted['invoice_id']>0)
{
	$invoice=invoiceFromDatabase($posted['invoice_id']);
	$invoice_number=invoiceFormatNumber($invoice['invoice_id'],$invoice['revision_id'],$invoice['site_id']);
	$container_list=packingFromPost($posted);
	$container=$container_list[intval($posted['container_id'])];
	$scale=($width+0.0)/$container->getWidth();
	$height=$container->getDepth()*$scale;
	$crate_list=$container->crate_list;
	if($container->getHeight()==0)
	{
		$real_width=($container->getWidth()-$container->maximal['y'])*$scale;
		$real_height=($container->getDepth()-$container->maximal['y'])*$scale;
	}
}

$filename=formatFileSafe($invoice_number.' '.$invoice['bill_name'].' '.$container->getName());
$disposition='inline';
if($posted['disposition']!='') $disposition=$posted['disposition'];

if($_SERVER['REDIRECT_FILE_TYPE']=='map')
{
	header('Content-Type: text/xml');
	header('Content-Disposition: '.$disposition.'; filename="'.urlencode($filename.'.map').'"');
?>
<map id="crate_map" name="crate_map">
<?php
if(count($crate_list)>0)
{
	foreach($crate_list as $crate)
	{
		$x1=$crate->x*$scale;
		$y1=$crate->z*$scale;
		$x2=$x1+$crate->width*$scale-1;
		$y2=$y1+$crate->depth*$scale-1;
?>
<area shape="rect" coords="<?= intval($x1) ?>,<?= intval($y1) ?>,<?= intval($x2) ?>,<?= intval($y2) ?>" />
<?php
	}
}
?>
</map>
<?php
	die();
}

$img=imagecreate($width,$height+32);

$white=imagecolorallocate($img,255,255,255);
$black=imagecolorallocate($img,0,0,0);
imagesetstyle($img,array($black,$white));

imagefilledrectangle($img,0,0,$width-1,$height+31,$white);
if($real_width>0 && $real_height>0)
	imagerectangle($img,0,0,$real_width-1,$real_height-1,IMG_COLOR_STYLED);
imagerectangle($img,0,0,$width-1,$height-1,$black);

if(count($crate_list)>0)
{
	$palette=array();
	foreach($crate_list as $crate)
		$palette[$crate->product_id]=0;

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

	//Sort crate list by tops
	$sort=array();
	foreach($crate_list as $key=>$crate)
		$sort[$key]=$crate->y+$crate->height;
	array_multisort($sort,SORT_ASC,$crate_list);

	foreach($crate_list as $crate)
	{
		$x1=$crate->x*$scale;
		$y1=$crate->z*$scale;
		$x2=$x1+$crate->width*$scale-1;
		$y2=$y1+$crate->depth*$scale-1;
		imagefilledrectangle($img,$x1,$y1,$x2,$y2,$palette[$crate->product_id]);
		imagerectangle($img,$x1,$y1,$x2,$y2,$black);
		printWordWrapCentered($img,$x1+1,$y1+1,$x2-$x1,$y2-$y1,$font_file,$black,$crate->name,9);
	}
}

if($invoice['invoice_id']>0)
{
	$textSize=16;
	imagefttext(
		$img,
		$textSize,
		0.0,
		8,
		$height + 24,
		$black,
		$font_file,
		$text,
		array('linespacing' => 1.4)
	);

	$text=$invoice['bill_name'].' '.date('d-m-Y',time());
	$dimensions = imageftbbox(
		$textSize,
		0.0,
		$font_file,
		$text,
		array('linespacing' => 1.4)
	);
	$lineWidth=$dimensions[2]-$dimensions[0];
	imagefttext(
		$img,
		$textSize,
		0.0,
		($width - $lineWidth) / 2,
		$height + 24,
		$black,
		$font_file,
		$text,
		array('linespacing' => 1.4)
	);

	$text=$container->getName();
	$dimensions = imageftbbox(
		$textSize,
		0.0,
		$font_file,
		$text,
		array('linespacing' => 1.4)
	);
	$lineWidth=$dimensions[2]-$dimensions[0];
	imagefttext(
		$img,
		$textSize,
		0.0,
		$width - $lineWidth - 8,
		$height + 24,
		$black,
		$font_file,
		$text,
		array('linespacing' => 1.4)
	);
}

switch($_SERVER['REDIRECT_FILE_TYPE'])
{
case 'jpg':
case 'jpeg':
	header('Content-Type: image/jpeg');
	header('Content-Disposition: '.$disposition.'; filename="'.urlencode($filename.'.jpg').'"');
	imagejpeg($img);
	break;
case 'gif':
	header('Content-Type: image/gif');
	header('Content-Disposition: '.$disposition.'; filename="'.urlencode($filename.'.gif').'"');
	imagegif($img);
	break;
default:
case 'png':
	header('Content-Type: image/png');
	header('Content-Disposition: '.$disposition.'; filename="'.urlencode($filename.'.png').'"');
	imagepng($img);
	break;
}
if (function_exists('imagedestroy')) {
	imagedestroy($img);
}
$img = false;

function printWordWrapCentered(&$image,$x,$y,$maxWidth,$maxHeight,$font,$color,$text,$textSize)
{
	$lineHeight=$textSize;
	$words=preg_split('/\s/',$text);
	$line='';
	foreach($words as $word)
	{
		$word=trim($word);

		$dimensions = imageftbbox(
			$textSize,
			0.0,
			$font,
			$word,
			array('linespacing' => 1.4)
		);
		$wordWidth=$dimensions[2]-$dimensions[0];
		while($wordWidth>=$maxWidth && strlen($word)>3)
		{
			$word=substr($word,0,-4).'...';
			$dimensions = imageftbbox(
				$textSize,
				0.0,
				$font,
				$word,
				array('linespacing' => 1.4)
			);
			$wordWidth=$dimensions[2]-$dimensions[0];
		}
		while($wordWidth>=$maxWidth && strlen($word)>0)
		{
			$word=substr($word,0,-1);
			$dimensions = imageftbbox(
				$textSize,
				0.0,
				$font,
				$word,
				array('linespacing' => 1.4)
			);
			$wordWidth=$dimensions[2]-$dimensions[0];
		}

		$propose=($line=='')?$word:($line.' '.$word);
		$dimensions = imageftbbox(
			$textSize,
			0.0,
			$font,
			$propose,
			array('linespacing' => 1.4)
		);
		$lineWidth=$dimensions[2]-$dimensions[0];
		$lineHeight=max($lineHeight,$dimensions[1]-$dimensions[7]);
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

	$y+=($maxHeight-count($lines)*$lineHeight)/2;
	foreach($lines as $line)
	{
		$dimensions = imageftbbox(
			$textSize,
			0.0,
			$font,
			$line,
			array('linespacing' => 1.4)
		);
		$lineWidth=$dimensions[2]-$dimensions[0];
		imagefttext(
			$image,
			$textSize,
			0.0,
			$x + ($maxWidth - $lineWidth) / 2,
			$y += $lineHeight,
			$color,
			$font,
			$line,
			array('linespacing' => 1.4)
		);
	}
}
