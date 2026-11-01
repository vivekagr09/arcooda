<?php
global $_GLOBALS;

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/header.inc.php';

?>
<link rel="stylesheet" type="text/css" href="style.css" />

<script type="text/javascript" src="profits_funcs.js.php"></script>

<div class="profits_content">
	<table cellspacing="2" cellpadding="2" style="width:100%;">
		<tr>
			<td style="width:30%;background:#eee;text-align:center"><b><?=htmlspecialchars(_('Profit/Sales Summary'))?></b></td>
			<td style="width:70%;background:#eee;text-align:right">
            </td>
		</tr>
	</table>

	<div style="text-align:center">
		<br />

<?php
if (11 == $_GLOBALS['site_settings']['site_id']) {
    $site_url = $_GET;
    unset($site_url["site_id"]);
    $site_url['site_id'] = '';
    $site_url            = "?" . http_build_query($site_url);

    $content = sprintf('<ul style="column-width:192px;list-style:none;margin:0;padding:0;">' . "\n");
    foreach (invoiceGetOptionList('site_id') as $k => $v) {
        $content .= sprintf(
            '    <li%s><a href="%s%s">%s</a></li>' . "\n",
            (($_GET['site_id'] ?? '') == $k) ? ' style="background:#ffa;"' : '',
            htmlspecialchars($site_url),
            urlencode($k),
            htmlspecialchars($v)
        );
    }
    $content .= sprintf(
        '    <li%s><a href="%s">%s</a></li>' . "\n" . '</ul>',
        ('' == ($_GET['site_id'] ?? '')) ? ' style="background:#ffa;"' : '',
        htmlspecialchars($site_url),
        htmlspecialchars(_('All Sites'))
    );
    echo formatHTMLFilterBox(_('View by Site'), $content, array_key_exists('site_id', $_GET));
}
