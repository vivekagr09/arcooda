<?php
include_once __DIR__ . '/common.inc.php';

global $DB;
global $_GLOBALS;

$etime = strtotime("+1 week");

/**
 * Declare some default variables
 */
$cache_file = '';
$ext = 'jpg';
$height = 256;
$map_id = 0;
$plug_id = 0;
$size_str = '';
$source_file = '';
$thumb = false;
$watermark = false;
$watermark_file = "";
$width = 256;

/**
 * Remove the watermarks when this image is displayed in webadmin
 */
if (strpos($_SERVER['HTTP_REFERER'], '/webadmin/') && $_GLOBALS['staff']['id'] != 0)
{
	$watermark = false;
	$watermark_file = '';
}

/**
 * Trim any query or fragment
 */
$url = explode('#', $_SERVER['REQUEST_URI']);
$url = explode('?', reset($url));
$url = reset($url);

/**
 * Build some small regular expressions to match the url
 */
$reg_div = '([_\-\/])';
$reg_sep = '([_\-])';
$reg_ext = '(png|jpg|jpeg|gif|svg)';
$reg_siz = '(0x[0-9a-f]{1,3}|sml|tiny|touch)';
$reg_map = '([0-9]{1,10})';
$reg_plug = '([0-9]{1,10})';

/**
 * Match the url to expected formats
 */
$matches = array();
if (preg_match('/' . $reg_div . 'map' . $reg_sep . $reg_map . $reg_sep . $reg_siz . '\.' . $reg_ext . '$/', $url, $matches))
{
	$match = true;
	$map_id = $matches[3];
	$size_str = $matches[5];
	$ext = $matches[6];
}
elseif (preg_match('/' . $reg_div . 'plug' . $reg_sep . $reg_plug . $reg_sep . $reg_siz . '\.' . $reg_ext . '$/', $url, $matches))
{
	$match = true;
	$plug_id = $matches[3];
	$size_str = $matches[5];
	$ext = $matches[6];
}
elseif (preg_match('/' . $reg_div . 'map' . $reg_sep . $reg_map . '\.' . $reg_ext . '$/', $url, $matches))
{
	$match = true;
	$map_id = $matches[3];
	$ext = $matches[4];
}
elseif (preg_match('/' . $reg_div . 'plug' . $reg_sep . $reg_plug . '\.' . $reg_ext . '$/', $url, $matches))
{
	$match = true;
	$plug_id = $matches[3];
	$ext = $matches[4];
}

/**
 * Calculate the size
 */
if ($size_str != '')
{
	if (preg_match('/^0x([a-f0-9]+)$/', $size_str, $matches))
	{
		$width = $height = hexdec($matches[1]);
		$thumb = ($width < 97);
	}
	elseif ($size_str == 'tiny')
	{
		$thumb = true;
		$width = $height = 32;
	}
	elseif ($size_str == 'touch')
	{
		$thumb = true;
		$width = $height = 64;
	}
	elseif ($size_str == 'sml')
	{
		$thumb = true;
		$width = $height = 128;
	}
	else
	{
		include $_SERVER['DOCUMENT_ROOT'] . '/messages/file_not_found.html';
		die();
	}
}
if ($width < 16 || $height < 16)
{
	include $_SERVER['DOCUMENT_ROOT'] . '/messages/file_not_found.html';
	die();
}

/**
 * Test for a valid screenshot
 */
if ($map_id != 0)
{
	$temp = $DB->query_first(sprintf(
		"SELECT `file_path` FROM `world`.`map` WHERE `id` = %d LIMIT 1",
		$map_id
	), MYSQLI_ASSOC);
	$source_file = $temp["file_path"];
	$cache_file = $_SERVER['DOCUMENT_ROOT'] . '/cache/world/maps/' . $map_id . '_' . $width . 'x' . $height . (($watermark) ? '_w' : '') . '.' . $ext;
	if ($size_str == '') $width = $height = 512;
}

/**
 * Test for a valid product
 */
elseif ($plug_id != 0)
{
	$temp = $DB->query_first(sprintf(
		"SELECT `file_path` FROM `world`.`plug_types` WHERE `id` = %d LIMIT 1",
		$plug_id
	), MYSQLI_ASSOC);
	if ($temp) $source_file = $temp["file_path"];
	else
	{
		$temp = $DB->query_first(sprintf(
			"SELECT `plug_path` FROM `world`.`country_plugs` WHERE `id` = %d LIMIT 1",
			$plug_id
		), MYSQLI_ASSOC);
		$source_file = $temp["plug_path"];
	}
	$cache_file = $_SERVER['DOCUMENT_ROOT'] . '/cache/world/plugs/' . $plug_id . '_' . $width . 'x' . $height . (($watermark) ? '_w' : '') . '.' . $ext;
	if ($size_str == '') $width = $height = 512;
}

/**
 * Failed to match an appropriate source
 */
else
{
	include $_SERVER['DOCUMENT_ROOT'] . '/messages/file_not_found.html';
	die();
}

/**
 * Check if the source file exists
 */
if ($source_file == '' || !file_exists($source_file) || !is_file($source_file) || filesize($source_file) == 0)
{
	include ($_SERVER['DOCUMENT_ROOT'] . '/messages/file_not_found.html');
	die();
}

/**
 * Test times of related files and calculate the last changed time
 */
$cache_time = time();
$script_time = filemtime(__FILE__);
$source_time = filemtime($source_file);
$watermark_time = 0;
if ($cache_file != '' && is_file($cache_file)) $cache_time = filemtime($cache_file);
if ($watermark_file != '' && is_file($watermark_file)) $watermark_time = filemtime($watermark_file);
$want_time = max(max($source_time, $watermark_time), $script_time);
$cache_etag = '"' . md5($cache_file . $want_time) . '"';

/**
 * Test if browser has already cached the current expected image
 */
$if_modified_since = preg_replace('/;.*$/', '', $_SERVER['HTTP_IF_MODIFIED_SINCE']);
if ($if_modified_since == gmdate(DATE_RFC1123, $want_time) || $_SERVER['HTTP_IF_NONE_MATCH'] == $cache_etag)
{
	header("HTTP/1.0 304 Not Modified");
	header('ETag: ' . $cache_etag);
	die();
}

if ($cache_file != '')
{
	/**
	 * Every now and then, try to improve on the file size
	 */
	$shrink_size=false;
	if ($ext=='jpg' && is_file($cache_file) && filesize($cache_file)>25600 && substr(time(),-1)=='0')
	{
		$cache_time=0;
		$shrink_size=true;
	}

	if ($cache_time != $want_time)
	{
		list($orig_width, $orig_height) = getimagesize($source_file);
		if ($orig_width > $orig_height)
		{
			$new_width = $width;
			$new_height = ($width / $orig_width) * $orig_height;
			$x_off = 0;
			$y_off = intval(($height - $new_height) / 2);
		}
		else
		{
			$new_width = ($height / $orig_height) * $orig_width;
			$new_height = $height;
			$x_off = intval(($width - $new_width) / 2);
			$y_off = 0;
		}
		$resize_cmd = ' -resize ' . $width . 'x' . $height;
		$repage_cmd = ' -repage ' . $width . 'x' . $height . '+' . $x_off . '+' . $y_off;
		$profile_cmd = ' -profile ' . escapeshellarg($_SERVER['DOCUMENT_ROOT'] . '/copyright.8bim');
		$watermark_cmd = '';
		if ($watermark) $watermark_cmd = ' ' . escapeshellarg($watermark_file) . ' ' . $resize_cmd;

		$old_quality = 0;
		$quality = 90;

		/**
		 * Optionally, determine the best quality for a 25kb file
		 */
		if ($shrink_size)
		{
			$quality = 100;
			$step = 50;
			$sizes = array();
			while($step>0 && $quality<=100 && $quality>0)
			{
				system('/usr/bin/convert -interlace Plane -quality ' . intval($quality) . ' xc:white ' . escapeshellarg($source_file) . $resize_cmd . $repage_cmd . $watermark_cmd . ' -flatten' . $profile_cmd . ' ' . escapeshellarg($cache_file) . ' 2>&1');
				clearstatcache();
				$sizes[filesize($cache_file)][intval($quality)]=intval($quality);
				$old_quality=intval($quality);
				if(filesize($cache_file)>25600) $quality-=$step;
				else $quality+=$step;
				$step/=2;
				if($old_quality == intval($quality)) $step = 0;
			}
			foreach($sizes as $k=>$v) if($k>25600) unset($sizes[$k]);
			ksort($sizes);
			$sizes=end($sizes);
			ksort($sizes);
			$quality=end($sizes);
		}

		if ($old_quality != $quality)
		{
			system('/usr/bin/convert -interlace Plane -quality ' . $quality . ' xc:white ' . escapeshellarg($source_file) . $resize_cmd . $repage_cmd . $watermark_cmd . ' -flatten' . $profile_cmd . ' ' . escapeshellarg($cache_file) . ' 2>&1');
		}
		touch($cache_file, $want_time);
	}
	header('Date: ' . gmdate(DATE_RFC1123, time()));
	header('Last-Modified: ' . gmdate(DATE_RFC1123, $want_time));
	header('Content-Length: ' . filesize($cache_file));
	header('Content-Type: ' . mime_content_type($cache_file));
	header('ETag: ' . $cache_etag);
	header('Expires: ' . gmdate(DATE_RFC1123, $etime));
	header('Cache-Control: public');
	header('Image-Quality: ' . $quality);
	readfile($cache_file);
	die();
}
/**
 * TODO: If no cache_file specified, but source_file specified, try on-the-fly here
 */
/*
elseif(!$watermark)
{
	header('Date: ' . gmdate(DATE_RFC1123, time()));
	header('Last-Modified: ' . gmdate(DATE_RFC1123, $want_time));
	header('Content-Type: image/' . $ext);
	header('ETag: ' . $cache_etag);
	header('Expires: ' . gmdate(DATE_RFC1123, gmmktime(gmdate('H') + 24)));
	system('/usr/bin/convert -interlace Plane -quality 90 xc:white ' . escapeshellarg($source_file) . $resize_cmd . ' -repage ' . $width . 'x' . $height . '+' . $x_off . '+' . $y_off . (($watermark) ? (' ' . escapeshellarg($watermark_file) . $resize_cmd) : '') . ' -flatten -profile ' . escapeshellarg($_SERVER['DOCUMENT_ROOT'] . '/copyright.8bim') . ' ' . escapeshellarg($cache_file) . ' 2>&1');
	die();
}
*/

include $_SERVER['DOCUMENT_ROOT'] . '/messages/file_not_found.html';
die();
?>