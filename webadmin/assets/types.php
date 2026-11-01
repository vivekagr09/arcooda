<?php
/**
 * @var string $section_singular
 */
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$type_url = $_GET;
unset($type_url['asset_type']);
$type_url = '.?' . http_build_query($type_url) . '&asset_type=';

$types_list = assetTableInfoFromType('', true);
?>
<br />

<?=formatHTMLTitle($section_singular . ' Types')?>
<p>Please choose an asset type to continue</p>
<table>
<?php
foreach ($types_list as $type => $description) {
?>
	<tr><td><a href="<?=$type_url . $type?>"><?=$description?></a></td></tr>
<?php
}
?>
</table>
<?php

include_once __DIR__ . '/footer.inc.php';
