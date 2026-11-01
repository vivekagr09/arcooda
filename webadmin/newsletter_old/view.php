<?php
include_once __DIR__ . '/common.inc.php';

$action   = 'view';
$id       = intval($posted['id']);
$staff_id = $_GLOBALS['staff']['id'];

$item = $section_handler->fromDatabase($id);

$replace = array(
    'first_name'    => $_GLOBALS['staff']['first_name'],
    'last_name'     => $_GLOBALS['staff']['last_name'],
    'email_address' => $_GLOBALS['staff']['email'],
    'tracker_key'   => '',
);
$replace['full_name']         = trim($replace['first_name'] . ' ' . $replace['last_name']);
$replace["name_url_encoded"]  = urlencode($replace["full_name"]);
$replace["email_url_encoded"] = urlencode($replace["email_address"]);

$newsletter_content = $item['description'];
//$newsletter_content = $section_handler->negotiateContent($newsletter_content, $replace);
$newsletter_content   = $section_handler->generateMailMerge($newsletter_content, $replace);
$newsletter_content   = $section_handler->generateMailMerge($newsletter_content);
$newsletter_alternate = $section_handler->generateAlternateText($newsletter_content);

if ('' != $newsletter_content) {
    ob_start();
?>
<div id="negotiation_menu" style="display:none;border:1px solid black;padding:12px;position:fixed;right:0;top:10%;font-weight:bold;background:#fff;">
	Content Negotiation:<br /><br />
</div>
<script type="text/javascript" src="/jquery/jquery.js"></script>
<script type="text/javascript">
//<![CDATA[
$(function() {
	var $negotiate_lists = $("negotiate_list");
	var negotiate_options = {};
	$negotiate_lists.each(function() {
		negotiate_options[$(this).attr("field")] = {};
	});
	for(var field_name in negotiate_options) $negotiate_lists.filter("[field='" + field_name + "']").children().each(function() {
		var field_value = $(this).attr('value');
		if (field_value) negotiate_options[field_name][field_value] = field_value;
	});

	var $negotiation_menu = $('#negotiation_menu');
	var $menu_table = $('<table></table>');
	var show_menu = false;
	for(var field_name in negotiate_options)
	{
		show_menu = true;
		var $menu_row = $("<tr><td>" + field_name + ":</td></tr>");
		var $menu_cell = $('<td></td>');
		var $menu_select = $('<select name="' + field_name + '"></select>');
		$menu_select.append('<option value="" selected="selected">Default</option>');
		for(var field_value in negotiate_options[field_name]) $menu_select.append('<option value="' + field_value + '">' + field_value + '</option>');
		$menu_table.append($menu_row.append($menu_cell.append($menu_select)));
		$menu_select.change(function() {
			var field_name = $(this).attr('name');
			var field_value = $(this).val();
			$negotiate_lists.filter("[field='" + field_name + "']").each(function() {
				var $children = $(this).children().hide();
				var selected_item = null;
				$children.filter("[default='default']").each(function() {
					selected_item = this;
				});
				$children.filter("[value='" + field_value + "']").each(function() {
					selected_item = this;
				});
				if (selected_item !== null) $(selected_item).show().children().hide().fadeIn();
			});
		}).change();
	}
	if(show_menu)
	{
		$negotiation_menu.append($menu_table);
		$negotiation_menu.slideDown();
	}
});
//]]>
</script>
<?php
if (false && ($pos1 = strpos($newsletter_content, "<!DOCTYPE")) !== false) {
        $pos2 = strpos($newsletter_content, ">", $pos1);
        ob_start();
?>
<!ENTITY % misc.inline "ins|del|script|negotiate_list">
<!ELEMENT negotiate_item (#PCDATA|a|b|i|negotiate_list|p|span)*>
<!ATTLIST negotiate_item default (default) #IMPLIED value CDATA #IMPLIED>
<!ELEMENT negotiate_list (negotiate_item)+>
<!ATTLIST negotiate_list field CDATA #IMPLIED>
<?php
$newsletter_content = substr($newsletter_content, 0, $pos2) . " [" . str_replace("\n", "", ob_get_clean()) . ']' . substr($newsletter_content, $pos2);
    }

    ob_get_clean();
    echo $newsletter_content;
    //echo str_replace('</body>', ob_get_clean() . '</body>', $newsletter_content);
}
//echo str_replace('[TRACKER_KEY]','',$item['description']);
?>