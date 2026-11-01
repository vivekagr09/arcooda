<?php
/**
 * @var Serial_Handler $section_handler
 */
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;

include_once __DIR__ . '/header.inc.php';
$randumnumber=rand();
$action = 'add';
$id     = 0;

$item      = $section_handler->FromDatabase(0,$randumnumber);
$item_lock = false;

?>
<h2><?=htmlspecialchars(_('Add Machine Serial Number'))?> - <?=date('d/m/Y', time())?></h2>
<?php
include __DIR__ . '/addedit.tpl.php';

include_once __DIR__ . '/footer.inc.php';
