<?php
include_once 'common.inc.php';

$file='newsletters/config_'.intval($posted['style_id']).'.tpl.php';
if(is_file($file)) include $file;
else
{
?>
<!-- config_<?php echo intval($posted['style_id']) ?>.tpl -->

No configuration currently available for this template
<br /><br />

<!-- /config_<?php echo intval($posted['style_id']) ?>.tpl -->
<?php
}
?>