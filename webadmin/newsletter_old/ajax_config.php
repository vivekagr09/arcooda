<?php
include_once __DIR__ . '/common.inc.php';

$file = __DIR__ . '/newsletters/config_' . intval($posted['style_id']) . '.tpl.php';
if (is_file($file)) {
    include $file;
} else {
?>
<!-- config_<?=intval($posted['style_id'])?>.tpl -->

No configuration currently available for this template
<br /><br />

<!-- /config_<?=intval($posted['style_id'])?>.tpl -->
<?php
}
