<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('member'); //memberSearch, memberGroupCount, memberGetOptionList

include_once __DIR__ . '/header.inc.php';

$criteria = $_GLOBALS['criteria_options']['member'];
if (isset($posted['site_id'])) {
    $criteria['site_id'] = $posted['site_id'];
}

if (3 != $_GLOBALS['site_settings']['site_id']) {
    $criteria['site_id'] == $_GLOBALS['site_settings']['site_id'];
}

$member_list = memberSearch($criteria);

if (isset($criteria['site_id'])) {
    $temp1 = array();
    $temp2 = '=' . intval($criteria['site_id']);
    if (is_array($criteria['site_id'])) {
        $temp2 = ' IN(' . implode(', ', $criteria['site_id']);
    }

    $query = $DB->query("SELECT `ml`.`member_id` FROM `newsletter_styles` AS `ms` INNER JOIN `highwaygroup`.`new_members_mailing_list` AS `ml` ON `ml`.`style_id` = `ms`.`style_id` INNER JOIN `highwaygroup`.`new_members` AS `m` ON `m`.`member_id` = `ml`.`member_id` WHERE `ml`.`active` = 'y' AND `ms`.`site_id`" . $temp2);
    while ($row = $DB->fetch_assoc($query)) {
        $temp1[] = $row['member_id'];
    }

    $DB->free_result($query);
    $member_list = array_unique(array_merge($member_list, $temp1));
}

$member_sites     = memberGroupCount($member_list, 'site_id');
$member_countries = memberGroupCount($member_list, 'country');

$site_list = array();
foreach (memberGetOptionList('site_id') as $k => $v) {
    if ($member_sites[$k] > 0) {
        $site_list[$k] = $v;
    }
}

$country_list = array();
foreach (memberGetOptionList('country') as $k => $v) {
    if ($member_countries[$k] > 0) {
        $country_list[$k] = $v;
    }
}

if (isset($posted['site_id'])) {
    $site_list = array($posted['site_id'] => $site_list[$posted['site_id']]);
}

if (3 != $_GLOBALS['site_settings']['site_id']) {
    $site_list = array($_GLOBALS['site_settings']['site_id'] => $site_list[$_GLOBALS['site_settings']['site_id']]);
}

$style_list = array();
$query      = $DB->query("SELECT style_id,site_id,style_description FROM newsletter_styles");
while ($row = $DB->fetch_assoc($query)) {
    if (isset($site_list[$row['site_id']])) {
        $style_list[$row['style_id']] = $row['style_description'];
    }
}

$DB->free_result($query);

?>
<h2>Create Newsletter</h2>

<script type="text/javascript">
//<![CDATA[
function validateSectionForm()
{
	return true;
}
function sectionSubmitChanges()
{
	$('form#<?=$section_name?>_form').eq(0).each(function() {this.submit();});
}
function sectionSendPreview()
{
	$('form#<?=$section_name?>_form').eq(0).each(function() {var action=this.action;var target=this.target;this.action='index.php?action=do_preview';this.target='_blank';this.submit();this.action=action;this.target=target;});
}
function sectionSendNewsletter()
{
	$('form#<?=$section_name?>_form').eq(0).attr('action','index.php?action=do_newsletter').each(function() {this.submit();});
}
function invertAll(name)
{
	$(":checkbox[name='"+name+"']").each(function() {
		this.checked=!this.checked;
	});
	return false;
}
function selectAll(name)
{
	$(":checkbox[name='"+name+"']").attr('checked',true);
	return false;
}
//]]>
</script>

<form accept-charset="utf-8" id="<?=$section_name?>_form" method="post" action="add.php">
	<input type="hidden" name="<?=$section_name?>[staff_id]" value="<?=$_GLOBALS['staff']['id']?>" />

	<?=formatHTMLTitle('Mailing List')?>
	<table class="table_fields"><tr><td><ul class="block_list">
<?php
foreach ($site_list as $site_id => $site_name) {
    $label = "site_id_" . $site_id;
?>
		<li><input<?=(count($style_list) > 1 && count($site_list) == 1) ? ' checked="checked"' : ''?> type="radio" id="<?=$label?>" name="subscr_list" value="site_<?=$site_id?>" /> <label for="<?=$label?>">Include All Active &quot;<?php hecho($site_name)?>&quot; Members</label></li>
<?php
}
foreach ($style_list as $style_id => $style_name) {
    $label = "style_id_" . $style_id;
?>
		<li><input<?=(count($style_list) <= 1) ? ' checked="checked"' : ''?> type="radio" id="<?=$label?>" name="subscr_list" value="style_<?=$style_id?>" /> <label for="<?=$label?>">Include Only &quot;<?php hecho($style_name)?>&quot; Members</label></li>
<?php
}
?>
	</ul></td></tr></table>

	<?=formatHTMLTitle('Best Described', '<a href="#" onclick="return selectAll(\'best_described[]\')">Select All</a> <a href="#" onclick="return invertAll(\'best_described[]\')">Invert Selection</a>')?>
	<table class="table_fields"><tr><td><ul class="float_list">
<?php
foreach (memberGetOptionList('best_described') as $k => $v) {
    $label = "best_described_" . $k;
?>
		<li><input checked="checked" type="checkbox" id="<?=$label?>" name="best_described[]" value="<?=$k?>" /> <label for="<?=$label?>"><?php hecho($v)?></label></li>
<?php
}
?>
	</ul></td></tr></table>

	<?=formatHTMLTitle('Country', '<a href="#" onclick="return selectAll(\'country[]\')">Select All</a> <a href="#" onclick="return invertAll(\'country[]\')">Invert Selection</a>')?>
	<table class="table_fields"><tr><td><ul>
<?php
foreach (getCountryListNested() as $k1 => $v1) {
    if (is_array($v1)) {
        printf(
            '<li>%s<ul>',
            hecho($k1, 1)
        );
        foreach ($v1 as $k2 => $v2) {
            if (is_array($v2)) {
                printf(
                    '<li>%s<ul>',
                    hecho($k2, 1)
                );
                foreach ($v2 as $k3 => $v3) {
                    printf(
                        '<li style="display:inline-block;min-width:384px;"><input type="checkbox" id="%s" name="country[]" value="%s" /> <label for="%1$s">%s</label></li>',
                        hecho("country_" . $k3, 1),
                        hecho($k3, 1),
                        hecho($v3, 1)
                    );
                }

                printf('</ul></li>');
            } else {
                printf(
                    '<li style="display:inline-block;min-width:384px;"><input type="checkbox" id="%s" name="country[]" value="%s" /> <label for="%1$s">%s</label></li>',
                    hecho("country_" . $k2, 1),
                    hecho($k2, 1),
                    hecho($v2, 1)
                );
            }

        }
        echo '</ul></li>';
    } else {
        printf(
            '<li style="display:inline-block;min-width:384px;"><input type="checkbox" id="%s" name="country[]" value="%s" /> <label for="%1$s">%s</label></li>',
            hecho("country_" . $k1, 1),
            hecho($k1, 1),
            hecho($v1, 1)
        );
    }

}
/*
foreach($country_list as $k=>$v)
{
$label="country_" . $k;
?>
<li><input checked="checked" type="checkbox" id="<?= $label ?>" name="country[]" value="<?= $k ?>" /> <label for="<?= $label ?>"><?php hecho($v) ?></label></li>
<?php
}
 */
?>
	</ul></td></tr></table>

	<input type="button" value="Create List" onclick="if(validateSectionForm()) sectionSubmitChanges()" />
</form>

<?php

include_once __DIR__ . '/footer.inc.php';
