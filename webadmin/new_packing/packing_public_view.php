<?php
include_once('packing_common.inc.php');
if($_GET['return_message']=='') $_GET['return_message']='_';
include_once('templates/packing_header.tpl.php');

$container_list=packingFromPost($posted);

$friendly_member=array();
$container_products=array();
$selected_container=0;
$current_container=&$container_list[$selected_container];

foreach($container_list as $container)
	foreach($container->crate_list as $crate)
		$container_products[$crate->product_id]='Product ID: '.$crate->product_id;

$flashVars = array();
$flashVars['contnum'] = $selected_container+1;
$flashVars['conttot'] = count($container_list);
foreach($container_list as $key => $value) $flashVars['container' . ($key+1) ] = serialize($value);
$flashVars['shopping_cart'] = serialize($container_products);
$flashVars['member'] = serialize($friendly_member);
$flashVars['crating'] = ($container_list[$selected_container]) ? 'y' : 'n';
$flashVars['stacking'] = ($container_list[$selected_container]) ? 'y' : 'n';
$flashVars['custname'] = trim($_GLOBALS['staff']['first_name'] . ' ' . $_GLOBALS['staff']['last_name']);
if (is_object($current_container)) $flashVars['conttype'] = $current_container->getName();
foreach($flashVars as $key => $value) $flashVars[$key] = urlencode($key) . '=' . urlencode($value);
$flashVars = implode('&', $flashVars);

?>
<object type="application/x-shockwave-flash" data="https://<?= htmlspecialchars($_SERVER['HTTP_HOST']) ?>/webadmin/packing/packing_3d.swf" width="1000" height="675" codebase="https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0">
	<param name="movie" value="packing_3d.swf" />
	<param name="allowScriptAccess" value="sameDomain" />
	<param name="flashVars" value="<?= $flashVars ?>" />
	<img src="packing_2d.gif?packing_string=<?= htmlspecialchars(urlencode($flashVars)) ?>" />
</object>

<?php

include_once('templates/packing_footer.tpl.php');
?>