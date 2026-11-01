<?php
$title = 'Previewing Email';
include 'email2/include/header.inc';
if (!isset($DOCUMENT_ROOT)) global $DOCUMENT_ROOT;
$attachment_path = "$DOCUMENT_ROOT/newadmin/email2/attachments/";
include 'email2/uploader.php3';
include 'email2/include/word_wrap.inc';
if ($trade_specials == 1 || $classified == 1 || $wanted == 1) $show_USD_note = true;
include 'email2/email_contents.php';
if ($trade_news) $vars['trading_news'] = trading_news($tn_x);
if ($trade_specials) $vars['trading_specials'] = trading_specials($sp_x);
if ($classified) $vars['classified'] = classified($cl_x);
if ($wanted) $vars['wanted'] = wanted($wn_x);
if ($news) $vars['news'] = news($nw_x);
if ($products) $vars['products'] = products($pd_x);
if ($forum) $vars['forum'] = forum($fm_x);
if ($trade_shows) $vars['trade_shows'] = trade_shows($ts_x);
$vars['parsed_html'].= $vars['trading_news'];
$vars['parsed_html'].= $vars['trading_specials'];
$vars['parsed_html'].= $vars['classified'];
$vars['parsed_html'].= $vars['wanted'];
$vars['parsed_html'].= $vars['news'];
$vars['parsed_html'].= $vars['products'];
$vars['parsed_html'].= $vars['forum'];
$vars['parsed_html'].= $vars['trade_shows'];
$vars['parsed_html'].= html_footer();
$vars['parsed_text'] = trim(strip_tags($vars['parsed_html']));
$vars['text_message'] = trim(word_wrap(strip_tags($vars['html_message'])));
//$vars['parsed_text'] = eregi_replace("&nbsp;", "", $vars['parsed_text']);
$vars['parsed_text'] = preg_replace("/&nbsp;/i", "", $vars['parsed_text']);
$vars['parsed_text'] = preg_replace("/\s\s/", "\n", $vars['parsed_text']);
//$vars['parsed_text'] = eregi_replace("\n\n\n\n", "\n", $vars['parsed_text']);
$vars['parsed_text'] = str_replace("\n\n\n\n", "\n", $vars['parsed_text']);
//$vars['parsed_text'] = eregi_replace("\n\n\n", "\n", $vars['parsed_text']);
$vars['parsed_text'] = str_replace("\n\n\n", "\n", $vars['parsed_text']);
$vars['parsed_text'] = word_wrap($vars['parsed_text']);
$vars['parsed_html'] = html_header() . $vars['parsed_html'];
$vars['text_message'] = "If you experience difficulty in reading this email. \nPlease contact us at emails@highwaygames.com \n\n\n" . $vars['text_message'];
$vars['files'] = fileList_text();
$vars['last_activity'] = last_activity('Previewing Email');
set_vars($vars);
$db_vars = get_vars();
include 'email2/include/preview.inc';
?>
<head>
	<base href="http://www.highwaygames.com/newadmin/" target="_self">
</head>
<table width="95%" border="0" cellspacing="0" cellpadding="2" align="center"><tr bgcolor="#eeeeee"><td><font size="2" color="#000066"><b>Final Step</b> - The above is the email that will be sent. Clicking on &quot;Send&quot; will send the email, there is no going back after clicking send. Please allow the emails to finish sending.</font></td></tr></table>
<center><form action="email.php3?page=send" method="post">
	<input type="button" name="Submit" value="&lt;&lt; Back" onclick="history.back(-1)" class="button" />
	<input type="button" name="Submit2" value="Attachments" onclick="MM_openBrWindow('email2/attachment.php3', 'addatt', 'scrollbars=yes,resizable=yes,width=500,height=450')" class="button" />
	<input type="submit" name="Send" value="Send >>" class="button" />
</form></center>
<?php
include 'email2/include/footer.inc';
?>