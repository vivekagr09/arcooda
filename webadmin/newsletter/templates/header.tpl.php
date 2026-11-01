<?php
$popup_window = (isset($_GET['return_message'])) ? true : false;
if (!$popup_window) include_once ($_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php');
else
{
	if (!headers_sent()) header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title></title>
	<style type="text/css">
body,p,td
{
	font-family:Arial;
	font-size:12px;
}
a:hover
{
	color:#FF0000;
}
	</style>
	<script type="text/javascript" src="//code.jquery.com/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="/phpjs/arrays&amp;strings.js"></script>
	<script type="text/javascript" src="/webadmin/jscript/common.php.js" defer="defer"></script>
	<script type="text/javascript" src="/webadmin/jscript/tooltip.1.0.2.js"></script>
</head>
<body>
<?php
}
?>
<script type="text/javascript" src="/webadmin/jscript/common.php.js"></script>
<link rel="stylesheet" type="text/css" href="style.css" />

<?php
$create_url = array('action' => 'build');
if (isset($posted['site_id'])) $create_url['site_id'] = $posted['site_id'];
if ($_GLOBALS['site_options']['site_id'] != 11) $create_url['site_id'] = $_GLOBALS['site_options']['site_id'];
$create_url = 'index.php?' . http_build_query($create_url);
?>

<div class="newsletter_content">
	<table cellspacing="2" cellpadding="2" style="width:100%;">
		<tr>
			<td style="text-align:center;font-weight:bold;width:20%;background-color:#eeeeee;">Newsletters</td>
			<td style="text-align:right;width:80%;background-color:#eeeeee;"><a href="<?php echo formatHTMLSafe($create_url) ?>">Create New Newsletter</a> | <a href="index.php?action=list">List All Newsletters</a></td>
		</tr>
	</table>

	<div align="center">
		<br />

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
//<![CDATA[
//google.load('visualization', '1', {'packages':['corechart']});	//Note: Piechart seems to reload and conflict this module
google.load('visualization', '1', {'packages':['piechart','geomap']});
$(function() {
	if(!$.tb_show)
	{
		$.ajaxSetup({cache:true});
		$.getScript('/js/thickbox.js');
	}
});
//]]>
</script>
