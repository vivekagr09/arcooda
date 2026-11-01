<?php
/**
 * @var Serial_Handler $section_handler
 * @var string         $section_name
 * @var string         $action
 * @var int            $id
 * @var mixed[]        $item
 * @var mixed[]|false  $item_lock
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?php
global $_GLOBALS;
$staff_list = $section_handler->getOptionList('staff_id');
$form_action = 'list';
if ('edit' == $action) {
    $form_action = 'do_edit';
}

if ('add' == $action) {
    $form_action = 'do_add';
}

if ('view' == $action) {
    $form_action = 'view';
}

?>

<script type="text/javascript">
//<![CDATA[
function validateSectionForm() {
	return true;
}
function sectionSubmitChanges() {
	$("#<?=$section_name?>_form").eq(0).each(function () {
		this.submit();
	});
}
//]]>
</script>

<form id="<?=htmlspecialchars($section_name)?>_form" method="post" action=".?action=<?=htmlspecialchars($form_action)?>">
	<input type="hidden" name="<?=htmlspecialchars($section_name)?>[id]" value="<?=$id?>" />
<?php
if ('view' != $action) {
    // need to display the changes that were made in the last 5 mins or so.
    if ('add' != $action) {
        $recent_list = $section_handler->commentSearch([$section_name . '_id' => $id, 'comment_type' => 'sql'], time() - 255);
        if (count($recent_list)) {
?>
<div style="color:red;text-align:left;padding:5px"><?=htmlspecialchars(sprintf(ngettext(
    'Changes made in the last minute',
    'Changes made in the last %d minutes',
    5
), 5))?>:</div><br />
<?php
            foreach ($recent_list as $message_id) {
                $message = $section_handler->commentFromDatabase($message_id);
                $shade   = substr('00' . dechex(min(255, (time() - $message['comment_time']))), -2);
                $notes   = explode(' ', $message['comment_text']);
                foreach ($notes as $key => $string) {
                    $notes[$key] = trim(chunk_split($string, 80));
                }

                $notes = implode(' ', $notes);
?>
<div style="color:#FF<?=$shade . $shade?>;text-align:left">
	<li><?=nl2br(htmlspecialchars($notes))?></li>
</div>
<br /><br />
<?php
            }
        }
    }
?>

<?php
    if ($item_lock['staff_id'] != $_GLOBALS['staff']['id'] && 'add' != $action) {
?>
<div style="border:1px solid black;font-weight:bold;background:pink;text-align:center;padding:8px">
<?php
        if (false === $item_lock) {
            echo htmlspecialchars(sprintf(
                _('This %s is not locked to you.'),
                $section_name
            ));
            echo ' ';
            echo htmlspecialchars(_('You cannot submit any changes.'));
        } else {
            $time_to_live = $item_lock['expiry_time'] - time();
            echo htmlspecialchars(sprintf(
                _('This %s is currently locked to %s.'),
                $section_name,
                $staff_list[$item_lock['staff_id']]
            ));
            echo ' ';
            echo htmlspecialchars(sprintf(
                _('Please wait for %d Minutes %d Seconds and try again.'),
                intval($time_to_live / 60),
                $time_to_live % 60
            ));
        }
?>
</div>
<br />
<br />
<?php
}
}
switch ($form_action) {
    case 'view':
    case 'do_edit':
        include __DIR__ . '/details.tpl.php';
        include __DIR__ . '/changes.tpl.php';
        break;

    case 'do_add':
        include __DIR__ . '/details.tpl.php';
        break;

    default:
        break;
}

if ('view' != $action) {
?>
<div style="text-align:center">
	<input type="button" value="<?=htmlspecialchars(_('Save Changes'))?>"<?=($item_lock['staff_id'] == $_GLOBALS['staff']['id'] || 'add' == $action) ? ' onclick="if(validateSectionForm()) sectionSubmitChanges()"' : ' disabled="disabled"'?> />
</div>
<?php
}
?>

</form>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
