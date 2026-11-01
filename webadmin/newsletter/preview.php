<?php
include_once __DIR__ . '/common.inc.php';
error_reporting(E_ALL);
ini_set('display_errors', '1');
moduleLoad('phpmailer');
use PHPMailer\PHPMailer\PHPMailer;

$action             = 'preview';
$item               = $section_handler->fromPost($posted['newsletter']);
$temp               = $section_handler->styleFromDatabase($item['style_id']);
$item['site_id']    = $temp['site_id'];
$item['sender']     = $temp['sender'];
$item['time_added'] = $_SERVER['REQUEST_TIME'];
if (isset($posted['newsletter_settings'])) {
    $item['settings'] = serialize($posted['newsletter_settings']);
}

$item_settings = unserialize($item['settings']);

$file = 'newsletters/style_' . $item['style_id'] . '.tpl.php';
if ("Daniel" == $_COOKIE["REMOTE_USER"] && is_file('newsletters/style_' . $item['style_id'] . '_new.tpl.php')) {
    $file = 'newsletters/style_' . $item['style_id'] . '_new.tpl.php';
}

if (is_file($file)) {
    ob_start();
    $_STACK[] = $item;
    include $file;
    $item                = array_pop($_STACK);
    $item['description'] = $section_handler->generateMailMerge(ob_get_clean(), array(
        'staff_name' => $_GLOBALS['staff']['first_name'],
    ));
}

$replace = array(
    'first_name'    => $_GLOBALS['staff']['first_name'],
    'last_name'     => $_GLOBALS['staff']['last_name'],
    'email_address' => $_GLOBALS['staff']['email'],
    'tracker_key'   => '',
);

//-----------------------------------------------------------------------------
/*
require_once('/home/common/3rdparty/tcpdf/tcpdf.php');
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 061');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 061', PDF_HEADER_STRING);
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->setLanguageArray($l);
$pdf->SetFont('helvetica', '', 10);
$pdf->AddPage();
$newsletter_content=end(preg_split('/<body[^>]*>/', $newsletter_content));
$newsletter_content=trim(reset(preg_split('/<\\/body>/', $newsletter_content)));
$newsletter_content=preg_replace('/ (src)="http:\\/\\/www\\./',' $1="http://',$newsletter_content);
$newsletter_content=preg_replace('/(font-size:[0-9]+)/','$1px',$newsletter_content);
$pdf->writeHTML($newsletter_content, true, false, true, false, '');
$pdf->lastPage();
$pdf->Output();
die();
 */
//-----------------------------------------------------------------------------

if ('' != $posted['send_copy']) {
	$htmlContent =  ('' != $item['description'])?$item['description']:$item['short_desc'];
	$domain = 'https://www.arcooda.com';
	$newsletter_content = addDomainToImageSrc($htmlContent, $domain);
    $newsletter_content   = $section_handler->negotiateContent($newsletter_content, $replace);

    $newsletter_content   = $section_handler->generateMailMerge($newsletter_content, $replace);
    $newsletter_content   = $section_handler->generateMailMerge($newsletter_content);
    $newsletter_alternate = $section_handler->generateAlternateText($newsletter_content);

    $site_list = getWebsitesList();
    $from_name = $site_list[$_GLOBALS['site_settings']['site_id']];
    if ('' != $site_list[$item['site_id']]) {
        $from_name = $site_list[$item['site_id']];
    }
    $mailer           = new PHPMailer();
    $mailer->From     = $item['sender'] ?? 'sales@arcooda.com';
    $mailer->FromName = $from_name;
    $mailer->Sender   = 'no-reply@highwaygroup.net';
    $mailer->CharSet  = 'utf-8';
    $mailer->Subject  = $item['subject'];
    $mailer->isHTML(true);
    $mailer->AddAddress($_GLOBALS['staff']['email'], trim($_GLOBALS['staff']['first_name'] . ' ' . $_GLOBALS['staff']['last_name']));
    // $mailer->AddAddress('testingsyrus@yopmail.com');
    $mailer->AddAddress('vivek@yopmail.com','vivek agrawal');

    $mailer->AddCustomHeader('Precedence: bulk');
    if (preg_match("@href=\"([^\"]+unsubscribe[^\"]+)\"@", $newsletter_content, $m)) {
        $mailer->AddCustomHeader("List-Unsubscribe: <" . $m[1] . ">");
    }
    $mailer->Body    = $newsletter_content;
    $mailer->AltBody = $newsletter_alternate;
    $mailer->Send();
    $mailer->ClearAllRecipients();
    $mailer->ClearCustomHeaders();
}
if ('' != $item['short_desc'] || '' != $item['description']) {
    $newsletter_content = ('' != $item['description'])?$item['description']:$item['short_desc'];
    $newsletter_content = $section_handler->generateMailMerge($newsletter_content, $replace);
    $newsletter_content = $section_handler->generateMailMerge($newsletter_content);

    ob_start();
    ?>
<div id="negotiation_menu" style="display:none;border:1px solid black;padding:12px;position:fixed;right:0;top:10%;font-weight:bold;background:#fff;">
	Content Negotiation:<br /><br />
</div>
<script type="text/javascript" src="//code.jquery.com/jquery-1.4.2.min.js"></script>
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
echo str_replace('</body>', ob_get_clean() . '</body>', $newsletter_content);
} else {
    die('Template does not exist');
}
function addDomainToImageSrc($html, $domain) {
    return preg_replace_callback(
        '/<img[^>]+src=["\'](?!http|https|\/\/)([^"\']+)["\']/i',
        function($matches) use ($domain) {
            return str_replace($matches[1], $domain . '/' . ltrim($matches[1], '/'), $matches[0]);
        },
        $html
    );
}
?>