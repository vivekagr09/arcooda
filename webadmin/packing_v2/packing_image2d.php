<?php
include_once("packing_common.inc.php");

$font_file='/home/common/fonts/Arial.ttf';

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
	$total_products = count($palette);
	$hue_step = 360 / max($total_products, 1); // Ensure division is valid
	foreach($palette as $key=>$col)
	{
		$hue = (crc32($key) % 360); // Assign a unique hue to  
	    $saturation = 0.8; // Keep colors vivid
	    $lightness = 0.6; // Adjust brightness

	    // Convert HSL to RGB
	    list($red, $green, $blue) = hslToRgb($hue, $saturation, $lightness);

		$palette[$key]=imagecolorallocate($img, $red, $green, $blue);
		$col_count++; // Move to the next hue step
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
	imagettftext($img,$textSize,0,8,$height+24,$black,$font_file,$text);

	$text=$invoice['bill_name'].' '.date('d-m-Y',$_SERVER['REQUEST_TIME']);
	$dimensions=imagettfbbox($textSize,0,$font_file,$text);
	$lineWidth=$dimensions[2]-$dimensions[0];
	imagettftext($img,$textSize,0,($width-$lineWidth)/2,$height+24,$black,$font_file,$text);

	$text=$container->getName();
	$dimensions=imagettfbbox($textSize,0,$font_file,$text);
	$lineWidth=$dimensions[2]-$dimensions[0];
	imagettftext($img,$textSize,0,$width-$lineWidth-8,$height+24,$black,$font_file,$text);
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
imagedestroy($img);

function printWordWrapCentered(&$image,$x,$y,$maxWidth,$maxHeight,$font,$color,$text,$textSize)
{
	$lineHeight=$textSize;
	$words=preg_split('/\s/',$text);
	$line='';
	foreach($words as $word)
	{
		$word=trim($word);

		$dimensions=imagettfbbox($textSize,0,$font,$word);
		$wordWidth=$dimensions[2]-$dimensions[0];
		while($wordWidth>=$maxWidth && strlen($word)>3)
		{
			$word=substr($word,0,-4).'...';
			$dimensions=imagettfbbox($textSize,0,$font,$word);
			$wordWidth=$dimensions[2]-$dimensions[0];
		}
		while($wordWidth>=$maxWidth && strlen($word)>0)
		{
			$word=substr($word,0,-1);
			$dimensions=imagettfbbox($textSize,0,$font,$word);
			$wordWidth=$dimensions[2]-$dimensions[0];
		}

		$propose=($line=='')?$word:($line.' '.$word);
		$dimensions=imagettfbbox($textSize,0,$font,$propose);
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
		$dimensions=imagettfbbox($textSize,0,$font,$line);
		$lineWidth=$dimensions[2]-$dimensions[0];
		imagettftext($image,$textSize,0,$x+($maxWidth-$lineWidth)/2,$y+=$lineHeight,$color,$font,$line);
	}
}

function hslToRgb($h, $s, $l) {
    $c = (1 - abs(2 * $l - 1)) * $s;
    $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
    $m = $l - ($c / 2);

    $r = $g = $b = 0;
    if ($h < 60) {
        $r = $c; $g = $x; $b = 0;
    } elseif ($h < 120) {
        $r = $x; $g = $c; $b = 0;
    } elseif ($h < 180) {
        $r = 0; $g = $c; $b = $x;
    } elseif ($h < 240) {
        $r = 0; $g = $x; $b = $c;
    } elseif ($h < 300) {
        $r = $x; $g = 0; $b = $c;
    } else {
        $r = $c; $g = 0; $b = $x;
    }

    return [
        round(($r + $m) * 255), 
        round(($g + $m) * 255), 
        round(($b + $m) * 255)
    ];
}
?>