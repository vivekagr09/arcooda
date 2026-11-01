<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';
?>
<link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/webfonts/fa-solid-900.woff2" as="font" type="font/woff2" crossorigin>

<?php
$url = 'https://www.arcooda.com/admin-arcooda-monitor-comparison-table.html?' . http_build_query($_GET);

$homepage = file_get_contents($url);
echo $homepage; 
?>
