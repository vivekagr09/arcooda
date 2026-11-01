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
	$invoice=invoiceFromDatabase($posted['invoice_id']);

//Set our viewport (for multi rendering)
$viewport=array('left'=>0,'right'=>640,'top'=>0,'bottom'=>480,'width'=>640,'height'=>480);

if(!isset($posted['angle_x'])) $posted['angle_x']=225;
if(!isset($posted['angle_y'])) $posted['angle_y']=225;
if(!isset($posted['angle_z'])) $posted['angle_z']=0;

//Build a world transformation
$world=	matrixMultiply(
			matrixMultiply(
				matrixOffset($container->getWidth()/-2,$container->getDepth()/-2,$container->getHeight()/-2),
				matrixScale(0.5,-0.5,0.5)
			),
			matrixMultiply(
				matrixRotate($posted['angle_x'],$posted['angle_y'],$posted['angle_z']),
				matrixOffset($viewport['width']/2-$viewport['left'],$viewport['height']/2-$viewport['top'],0)
			)
		);

//Create the image
//$img=imagecreatetruecolor($viewport['right']-$viewport['left'],$viewport['bottom']-$viewport['top']);
$img=imagecreate($viewport['right']-$viewport['left'],$viewport['bottom']-$viewport['top']);
imageantialias($img,true);

//Build a palette
$palette=array();
$white=imagecolorallocate($img,255,255,255);
imagefilledrectangle($img,0,0,$viewport['right']-$viewport['left'],$viewport['bottom']-$viewport['top'],$white);
$black=imagecolorallocate($img,0,0,0);

$prod_col=array();
foreach($container->crate_list as $crate)
	$prod_col[$crate->getProductID()]=0;
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
	$palette[$key]=$prod_col[$crate->getProductID()];
$palette[]=$black;


//Render the container
imageRenderWire(
	$img,
	crateToVertexList(
		array(0,0,0,1),
		array($container->getWidth(),$container->getHeight(),$container->getDepth(),1)
	),
	$world,
	$black,
	1
);


//Build a list of surfaces using the crates
$surface_list=array();
foreach($container->crate_list as $key=>$crate)
{
	$newbox=crateToVertexList(
		array($crate->getX(),$crate->getY(),$crate->getZ(),1),
		array($crate->getX()+$crate->getWidth(),$crate->getY()+$crate->getHeight(),$crate->getZ()+$crate->getDepth(),1)
	);
	foreach($newbox as $surface)
	{
		$surface[]=$key;
		$surface_list[]=$surface;
	}
}

//Render the crates
imageRenderFilled($img,$surface_list,$world,$palette);

$link_list=array();
foreach($container->crate_list as $key=>$crate)
{
	if(isset($link_list[$crate->getName()]))
		$link_list[$crate->getName()][]=$key+1;
	else
	{
		$link=array($key+1);
		$link_list[$crate->getName()]=$link;
	}
}
$link_list=array_reverse($link_list);
$ypos=$viewport['height']-12;
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
		8,
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
		56,
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
	include_once($_SERVER['DOCUMENT_ROOT'].'/webadmin/invoice/functions/query_invoice.inc.php');
	$details=invoiceFromDatabase($_REQUEST['invoice_id']);
	$ypos=16;

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
		$viewport['width'] - $dim[4] - 9,
		$ypos,
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
		$viewport['width'] - $dim[4] - 9,
		$ypos,
		$black,
		$font_file,
		$text,
		array('linespacing' => 1.4)
	);
	$ypos+=10;

	$text=$container->getName().' Container '.(isset($container_id)?"($container_id)":'');
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
		$viewport['width'] - $dim[4] - 9,
		$ypos,
		$black,
		$font_file,
		$text,
		array('linespacing' => 1.4)
	);
	$ypos+=10;

	$text=' Date: '.date("d/m/y",time());
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
		$viewport['width'] - $dim[4] - 9,
		$ypos,
		$black,
		$font_file,
		$text,
		array('linespacing' => 1.4)
	);
	$ypos+=10;
}

$disposition='inline';
if($posted['disposition']!='') $disposition=$posted['disposition'];

//Display any errors, or the image
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
		header('Content-Disposition: '.$disposition.'; filename="'.str_replace("#","",(($details['invoice_status']=='Proforma') ? formatProformaNumber($details['invoice_id']) : invoiceFormatNumber($details['invoice_id']))).'_'.$details['customer_name'].'_3D_'.$container_id.'.jpg"');
		imagejpeg($img);
		break;
	default:
		header('Content-Type: image/png');
		header('Content-Disposition: '.$disposition.'; filename="'.str_replace("#","",(($details['invoice_status']=='Proforma') ? formatProformaNumber($details['invoice_id']) : invoiceFormatNumber($details['invoice_id']))).'_'.$details['customer_name'].'_3D_'.$container_id.'.png"');
		imagepng($img);
	}
	if (function_exists('imagedestroy')) {
		imagedestroy($img);
	}
	$img = false;
}
die();




function printWordWrapped(&$image,$x,$y,$maxWidth,$maxHeight,$font,$color,$text,$textSize)
{
	$lineHeight=$textSize;
	$words=explode(' ',$text);
	$line='';
	foreach($words as $word)
	{
		$word=trim($word);
		$propose=($line=='')?$word:($line.' '.$word);
		$dimensions = imageftbbox(
			$textSize,
			0.0,
			$font,
			$propose,
			array('linespacing' => 1.4)
		);
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
		imagefttext(
			$image,
			$textSize,
			0.0,
			$x,
			$y += $lineHeight,
			$color,
			$font,
			$line,
			array('linespacing' => 1.4)
		);
}
function imageRenderWire($img,$surface_list,$transform,$color,$faces=3)
{
	foreach($surface_list as $surface)
	{
		$polygon=array();
		foreach($surface as $vertex)
		{
			$temp=vertexMultiply($vertex,$transform);
			$polygon[]=$temp[0];
			$polygon[]=$temp[1];
		}
		$visibility=($polygon[0]-$polygon[2])*($polygon[5]-$polygon[3])
					-($polygon[1]-$polygon[3])*($polygon[4]-$polygon[2]);
		if((($faces&1)==1 && $visibility>=0) || (($faces&2)==2 && $visibility<=0))
			imagepolygon($img,$polygon,count($surface),$color);
	}
}

function imageRenderFilled($img,$surface_list,$world,$color,$faces=2)
{
	global $font_file;

	if(!is_array($color)) $color=array($color);
	$shades=count($color);

	$polygon_list=array();

	foreach($surface_list as $surface)
	{
		$col=array_pop($surface);
		$polygon=array();
		foreach($surface as $vertex)
			$polygon[]=vertexMultiply($vertex,$world);
		$polygon[]=$col;
		$visibility=($polygon[0][0]-$polygon[1][0])*($polygon[2][1]-$polygon[1][1])
					-($polygon[0][1]-$polygon[1][1])*($polygon[2][0]-$polygon[1][0]);
		if((($faces&1)==1 && $visibility>=0) || (($faces&2)==2 && $visibility<=0))
			$polygon_list[]=$polygon;
	}

	//Sort the polygons by distance
	$dist_list=array();
	foreach($polygon_list as $polygon)
	{
		$col=array_pop($polygon);
		$dist=0;
		foreach($polygon as $v)
			$dist+=$v[2];
		$dist_list[]=$dist/count($polygon);
	}
	array_multisort($dist_list,$polygon_list);
	$min_dist=$dist_list[0];
	$max_dist=$dist_list[count($dist_list)-1];

	$outline_color=reset(array_reverse($color));

	//Draw the leftover polygons
	foreach($polygon_list as $polygon)
	{
		$fill_color=array_pop($polygon);
		$temp=array();
		$dist=0;
		foreach($polygon as $v)
		{
			$temp[]=$v[0];
			$temp[]=$v[1];
			$dist+=$v[2];
		}
		$dist/=count($polygon);

//		$fill_color=intval(($dist-$min_dist)*($shades-1)/($max_dist-$min_dist))+1;
//		if($fill_color>=$shades) $fill_color=$shades-1;
		imagefilledpolygon($img,$temp,count($polygon),$color[$fill_color]);
		imagepolygon($img,$temp,count($polygon),$outline_color);

		$xpos=0;
		$ypos=0;
		foreach($temp as $key=>$pos)
			if(($key&1)==1) $ypos+=$pos;
			else $xpos+=$pos;
		$xpos/=count($polygon);
		$ypos/=count($polygon);
		$dim = imageftbbox(
			9.0,
			0.0,
			$font_file,
			'#' . ($fill_color + 1),
			array('linespacing' => 1.4)
		);
		imagefttext(
			$img,
			9.0,
			0.0,
			$xpos - ($dim[2] - $dim[0]) / 2,
			$ypos - ($dim[5] - $dim[1]) / 2,
			$color[count($color) - 1],
			$font_file,
			'#' . ($fill_color + 1),
			array('linespacing' => 1.4)
		);
	}
}

function crateToVertexList($c1,$c2)
{
	$result=array();

	$vertices=array();
	$vertex=$c1;		$vertices[]=$vertex;
	$vertex[0]=$c2[0];	$vertices[]=$vertex;
	$vertex[1]=$c2[1];	$vertices[]=$vertex;
	$vertex[0]=$c1[0];	$vertices[]=$vertex;
	$result[]=$vertices;

	$vertices=array();
	$vertex=$c2;		$vertices[]=$vertex;
	$vertex[1]=$c1[1];	$vertices[]=$vertex;
	$vertex[0]=$c1[0];	$vertices[]=$vertex;
	$vertex[1]=$c2[1];	$vertices[]=$vertex;
	$result[]=$vertices;

	$vertices=array();
	$vertex=$c1;		$vertices[]=$vertex;
	$vertex[1]=$c2[1];	$vertices[]=$vertex;
	$vertex[2]=$c2[2];	$vertices[]=$vertex;
	$vertex[1]=$c1[1];	$vertices[]=$vertex;
	$result[]=$vertices;

	$vertices=array();
	$vertex=$c2;		$vertices[]=$vertex;
	$vertex[2]=$c1[2];	$vertices[]=$vertex;
	$vertex[1]=$c1[1];	$vertices[]=$vertex;
	$vertex[2]=$c2[2];	$vertices[]=$vertex;
	$result[]=$vertices;

	$vertices=array();
	$vertex=$c1;		$vertices[]=$vertex;
	$vertex[2]=$c2[2];	$vertices[]=$vertex;
	$vertex[0]=$c2[0];	$vertices[]=$vertex;
	$vertex[2]=$c1[2];	$vertices[]=$vertex;
	$result[]=$vertices;

	$vertices=array();
	$vertex=$c2;		$vertices[]=$vertex;
	$vertex[0]=$c1[0];	$vertices[]=$vertex;
	$vertex[2]=$c1[2];	$vertices[]=$vertex;
	$vertex[0]=$c2[0];	$vertices[]=$vertex;
	$result[]=$vertices;

	return $result;
}

function vertexMultiply($v,$A)
{
	$b=array(0,0,0,0);
	for($x=0;$x<4;$x++)
		for($y=0;$y<4;$y++)
			$b[$x]+=$A[$y][$x]*$v[$y];
	if($b[3]!=0 && $b[3]!=1)
		for($x=0;$x<4;$x++)
			$b[$x]/=$b[3];
	return $b;
}

function matrixMultiply(&$A,&$B)
{
	$C=matrixIdentity();
	for($x=0;$x<4;$x++)
		for($y=0;$y<4;$y++)
		{
			$sum=0;
			for($z=0;$z<4;$z++)
				$sum+=$A[$y][$z]*$B[$z][$x];
			$C[$y][$x]=$sum;
		}
	return $C;
}

function matrixScale($scalex,$scaley,$scalez)
{
	$C=matrixIdentity();
	$C[0][0]=$scalex;
	$C[1][1]=$scaley;
	$C[2][2]=$scalez;
	return $C;
}

function matrixRotate($x,$y,$z)
{
	$A=matrixIdentity();
	if($y!=0)
	{
		$y*=M_PI/180.0;
		$B=matrixIdentity();
		$B[0][0]=cos($y);
		$B[2][0]=sin($y);
		$B[0][2]=0-sin($y);
		$B[2][2]=cos($y);
		$A=matrixMultiply($A,$B);
	}
	if($x!=0)
	{
		$x*=M_PI/180.0;
		$B=matrixIdentity();
		$B[1][1]=cos($x);
		$B[2][1]=sin($x);
		$B[1][2]=0-sin($x);
		$B[2][2]=cos($x);
		$A=matrixMultiply($A,$B);
	}
	if($z!=0)
	{
		$z*=M_PI/180.0;
		$B=matrixIdentity();
		$B[0][0]=cos($z);
		$B[1][0]=sin($z);
		$B[0][1]=0-sin($z);
		$B[1][1]=cos($z);
		$A=matrixMultiply($A,$B);
	}
	return $A;
}

function matrixOffset($x,$y,$z)
{
	return array(array(1,0,0,0),array(0,1,0,0),array(0,0,1,0),array($x,$y,$z,1));
}

function matrixIdentity()
{
	return array(array(1,0,0,0),array(0,1,0,0),array(0,0,1,0),array(0,0,0,1));
}
