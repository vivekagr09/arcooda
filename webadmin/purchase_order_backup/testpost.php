<?php
include_once __DIR__ . "/common.inc.php";
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Post Test</title>
		<style type="text/css">
/*<![CDATA[*/
pre{background-color:#68b}
/*]]>*/
		</style>
	</head>
	<body>

<?php
foreach (array('post' => &$_POST, 'get' => &$_GET, 'cookie' => &$_COOKIE, 'session' => &$_SESSION, 'server' => &$_SERVER, 'request' => &$_REQUEST) as $key => $row) {
?>
Received the following <?=$key?> messages:<br />
<pre><?=htmlspecialchars(json_encode($row, JSON_PRETTY_PRINT))?></pre>
<br />
<?php
}
?>

	</body>
</html>
