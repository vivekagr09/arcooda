<html><head><title>Post Test</title></head>
<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/webadmin/common.inc.php');
?>
<style>
pre {background-color:#6688BB;}
</style>
<body>

<?php
foreach(array('post'=>&$_POST, 'get'=>&$_GET, 'cookie'=>&$_COOKIE, 'session'=>&$_SESSION, 'server'=>&$_SERVER, 'request'=>&$_REQUEST) as $key=>$row)
{
?>
Received the following <?= $key ?> messages:<br />
<pre><?=htmlspecialchars(json_encode($row, JSON_PRETTY_PRINT))?></pre>
<br />
<?php
}
?>
</body>
</html>
