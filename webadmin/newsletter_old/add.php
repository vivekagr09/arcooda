<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action   = 'add';
$id       = 0;
$staff_id = $_GLOBALS['staff']['id'];
$site_id  = $_GLOBALS['site_settings']['site_id'];

$item = array(
    'subscr_list' => $posted['subscr_list'],
);
$item_lock     = false;
$item_comments = array();
$item_settings = array();

$styles_list = $section_handler->styleFromDatabase(array());

foreach (array('country', 'best_described', 'subscr_list') as $k) {
    if (is_array($posted[$k])) {
        $item[$k] = implode(',', $posted[$k]);
    } elseif (isset($posted[$k])) {
        $item[$k] = $posted[$k];
    }

}
foreach (explode(',', $item['subscr_list']) as $subscr) {
    $k = explode('_', $subscr);
    $v = array_pop($k);
    $k = implode('_', $k);
    switch ($k) {
        case 'site':
            $item['style_id'] = 0;
            $item['site_id']  = intval($v);
            break;
        case 'style':
            $item['style_id'] = intval($v);
            $item['site_id']  = $styles_list[$item['style_id']]['site_id'];
            $item['subject']  = str_replace('[CURRENT_DATE]', date('jS F, Y'), $styles_list[$item['style_id']]['subject']);
            break;
    }
}
if (3 != $site_id && $item['site_id'] != $site_id) {
    $item['style_id'] = 0;
    $item['site_id']  = $site_id;
    $item['subject']  = '';
}

$copy_id = intval($posted['id']);
if (0 != $copy_id) {
    $item = $section_handler->fromDatabase($copy_id);
    unset($item['id']);
    unset($item['newsletter_id']);
    $item_settings = unserialize($item['settings']);
    foreach ($item_settings as $k1 => $v1) {
        if (is_array($v1) && is_array($v1['items'])) {
            $item_settings[$k1]['qty'] = count($v1['items']);
            unset($item_settings[$k1]['items']);
        }
    }
}

$member_list = $section_handler->generateMemberList($item);

?>
<h2>Create New Newsletter - <?=date('d/m/Y', time())?></h2>
<p>Found <?=count($member_list)?> members</p>
<?php

include __DIR__ . '/addedit.tpl.php';

include_once __DIR__ . '/footer.inc.php';
