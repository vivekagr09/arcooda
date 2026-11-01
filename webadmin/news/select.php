<?php
include_once dirname(__FILE__) . "/common.inc.php";
$news_id = intval($posted['news_id']);
$return_message = $posted['return_message'];
$news = $NH->FromDatabase($news_id);
$news['return_message'] = $return_message;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title></title>
	<script type="text/javascript">
//<![CDATA[
if (window.opener != null && window.opener.setNewsData != null)
{
	window.opener.setNewsData(<?php jecho($news) ?>);
	window.close();
}
else alert('Cannot send information, please follow proper steps');
//]]>
	</script>
</head>
<body></body>
</html>