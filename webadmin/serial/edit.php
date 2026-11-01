<?php
/**
 * @var Serial_Handler $section_handler
 * @var string         $section_name
 * @var mixed[]        $posted
 */
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action   = 'edit';
$id       = intval($posted['id']);
$randumnumber=rand();
$item         = $section_handler->fromDatabase($id,$randumnumber);
$temp         = array_chunk($section_handler->commentSearch([$section_name . '_id' => $id, 'comment_type' => 'sql']), 5);
$item_changes = (count($temp))?reset($temp):$temp;
$item_lock    = $section_handler->getLock($id);

?>
<h2><?=htmlspecialchars(_('Edit Machine Serial Number'))?> <?=htmlspecialchars($item['serial_number'])?> - <?=date('d/m/Y', time())?></h2>
<?php

include __DIR__ . '/addedit.tpl.php';

include_once __DIR__ . '/footer.inc.php';
