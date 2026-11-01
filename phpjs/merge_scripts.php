<?php
$file = "phpjs.full.min.js";
$mtime = filemtime($file);
$mtime = min($mtime, $_SERVER['REQUEST_TIME']);
$etag = '"' . md5($file . $mtime) . '"';
if (array_key_exists("HTTP_IF_MODIFIED_SINCE", $_SERVER) && preg_replace('/;.*$/', '', $_SERVER['HTTP_IF_MODIFIED_SINCE']) == gmdate(DATE_RFC1123, $mtime) || $_SERVER['HTTP_IF_NONE_MATCH'] == $etag)
{
	header($_SERVER['SERVER_PROTOCOL'] . " 304 Not Modified");
	header('ETag: ' . $etag);
	die();
}

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
{
	$file .= ".gz";
	header("Content-Encoding: gzip");
}

header('Content-Type: text/javascript');
header('Content-Length: ' . filesize($file));
header('Expires: ' . gmdate(DATE_RFC1123, gmmktime(gmdate('H')+24)));
header('Last-Modified: ' . gmdate(DATE_RFC1123, $mtime));
header('ETag: ' . $etag);
header('Date: ' . gmdate(DATE_RFC1123,$_SERVER['REQUEST_TIME']));
readfile($file);

die();

$skip_dependancies=false;

function dependant_sort($a,$b)
{
	$t1=explode('.',basename($a));
	$t2=explode('.',basename($b));
	while(count($t1) && count($t2))
	{
		$c=strcmp(array_shift($t1),array_shift($t2));
		if($c!=0) return $c;
	}
	if(count($t2)==0) return 1;
	if(count($t1)==0) return -1;
	return 0;
}

/**
 * Find all the available files
 *
 * We do this to make sure we only ever allow inclusion of files within this folder
 */
$available=array();
$temp=array(dirname(__FILE__).'/*');
while(count($temp)) foreach(glob(array_shift($temp)) as $found)
{
	if(substr(basename($found),0,1)=='.') { /* Ignore Hidden */ }
	elseif(is_link($found)) { /* Ignore Links */ }
	elseif(is_dir($found)) $temp[]=$found.'/*';
	elseif(is_file($found) && substr($found,-7)=='.min.js') $available[]=substr($found,0,-7);
	elseif(is_file($found) && substr($found,-3)=='.js') $available[]=substr($found,0,-3);
	unset($found);
}
unset($temp);
$available=array_unique($available);
usort($available,'dependant_sort');
//var_dump($available); die();

/**
 * Fetch the basenames of the available files
 *
 * To speed up the lookup for requested files
 */
$basenames=array();
foreach($available as $k=>$v)
{
	$basenames[$k]=basename($v);
	unset($k);
	unset($v);
}
//var_dump($basenames); die();

/**
 * Lookup the requested files
 */
$requested=array();
foreach(explode('&',end(explode('/',$_SERVER['REQUEST_URI']))) as $i)
{
	if(substr($i,-3)=='.js') $i=substr($i,0,-3);
	if(substr($i,-4)=='.min') $i=substr($i,0,-4);
	if(substr($i,0,6)=='phpjs.') $i=substr($i,6);
	if($i=='skip_deps') $skip_dependancies=true;
	if($i=='phpjs')
	{
		$key=array_search($i,$basenames);
		if($key!==FALSE) $requested[$key]=$key;
	}
	else
	{
		$i=str_replace('__WILDCARD__','[^.]*',preg_quote(str_replace('*','__WILDCARD__',$i)));
		foreach($basenames as $k=>$v)
		{
			if(preg_match('/^phpjs\.'.$i.'$/',$v)) $requested[$k]=$k;
			unset($k);
			unset($v);
		}
		unset($i);
	}
}
//var_dump($requested); die();

/**
 * Add dependancies
 *
 * All phpjs files should have dependancy names within their name,
 * ie. phpjs.ui.accordian.js depends on phpjs.ui.js and phpjs.js etc.
 */
if(!$skip_dependancies)
{
	foreach($requested as $k)
	{
		$temp=explode('.',$basenames[$k]);
		while(count($temp)>1)
		{
			array_pop($temp);
			$key=array_search(implode('.',$temp),$basenames);
			if($key!==FALSE) $requested[$key]=$key;
			unset($key);
		}
		unset($temp);
		unset($k);
	}
}
//var_dump($requested); die();

/**
 * Handle file not found
 */
if(count($requested)==0)
{
	header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
	die();
}

/**
 * Sort the requested files
 */
sort($requested);

/**
 * Build a list of include files
 *
 * Prefer minified files over normal, but only if they have the same content
 */
$fetch=array();
foreach($requested as $k)
{
	$file_name1=$available[$k].'.js';
	$file_name2=$available[$k].'.min.js';
	if(is_file($file_name1))
	{
		if(is_file($file_name2) && filemtime($file_name2)>=filemtime($file_name1)) $fetch[]=$file_name2;
		else $fetch[]=$file_name1;
	}
	else $fetch[]=$file_name2;
}
//var_dump($fetch); die();

//Get the modification date and e-tag
$mtime=filemtime(__FILE__);
foreach($fetch as $i) $mtime=max($mtime,filemtime($i));
$mtime=min($mtime,$_SERVER['REQUEST_TIME']);
$etag='"'.md5(implode('&',$fetch).$mtime).'"';

//Test the modification date
if(preg_replace('/;.*$/','',$_SERVER['HTTP_IF_MODIFIED_SINCE'])==gmdate(DATE_RFC1123,$mtime) || $_SERVER['HTTP_IF_NONE_MATCH']==$etag)
{
	header($_SERVER['SERVER_PROTOCOL']." 304 Not Modified");
	header('ETag: '.$etag);
	die();
}

//Fetch all the scripts
ob_start();
foreach($fetch as $file)
{
	echo "/* START: ".basename($file)." */\n";
	readfile($file);
	echo "\n";
	echo "/* END: ".basename($file)." */\n";
}
$ob=ob_get_clean();

//Shrink the file further
//$ob=preg_replace('/\/\/.*\n/',"\n",$ob);			//No inline comments
//$ob=preg_replace('/(\/\*[\s\S]*?\*\/)/','',$ob);	//No star comments
//$ob=preg_replace('/\t/',' ',$ob);					//No tabs
//$ob=preg_replace('/\s*\:\s*/',':',$ob);				//No spacing before : and no spacing after
//$ob=preg_replace('/\s*\,\s*/',',',$ob);			//No spacing before , and single spacing after
//$ob=preg_replace('/\s*;\s*/','; ',$ob);				//No spacing before ; and no spacing after
//$ob=preg_replace('/\s*\{\s*/',' { ',$ob);			//Single spacing before { and no spacing after
//$ob=preg_replace('/\s*\}\s*/'," }\n",$ob);			//No spacing before } and newline after
//$temp=''; while($temp!=$ob=preg_replace('/  /',' ',$ob)) $temp=$ob;		//No double spaces
//$temp=''; while($temp!=$ob=preg_replace('/\s*\n/',"\n",$ob)) $temp=$ob;	//No blank lines

//Output the result
if(!headers_sent())
{
	header('Content-Type: text/javascript');
	header('Content-Length: '.strlen($ob));
	header('Expires: '.gmdate(DATE_RFC1123,gmmktime(gmdate('H')+24)));
	header('Last-Modified: '.gmdate(DATE_RFC1123,$mtime));
	header('ETag: '.$etag);
	header('Date: '.gmdate(DATE_RFC1123,$_SERVER['REQUEST_TIME']));
}
echo $ob;
