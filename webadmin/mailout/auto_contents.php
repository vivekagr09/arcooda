<?php
/***********************************************************************
 ** Title.........:
 ** Version.......:
 ** Author........:    Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
 ** Filename......:
 ** Last changed..:
 ** Notes.........:
 ***********************************************************************/

/**
 * @param string  $file
 * @param mixed[] $params
 * @param bool    $plaintext
 *
 * @return string
 */
function get_auto_contents($file, $params, $plaintext = false)
{
    $parameters = "";
    if ($plaintext) {
        $params['plain'] = '1';
    }

    if (is_array($params)) {
        $parameters = "?" . http_build_query($params);
    }

    return file_get_contents("https://www.coinopexpress.com/auto_contents/" . $file . ".php" . $parameters);
}

/**
 * @param bool $plain
 *
 * @return string
 */
function msg_header($plain = false)
{
    global $title, $newTitle;
    if ($plain) {
        return '';
    }

    if (trim($newTitle) != "") {
        $title = $newTitle;
    }

    ob_start();
?>
<table border="0" cellspacing="0" cellpadding="5"  bgcolor="#000099" align="center" width="100%"><tr><td><table border="0" cellspacing="0" cellpadding="5"  bgcolor="#FFFFFF" align="center" width="100%">
	<tr><td><table width="100%" border="0" cellspacing="0" cellpadding="3"><tr bgcolor="#FFFFFF">
		<td width="30%" align="left"><a href="https://www.coinopexpress.com/"><img src="https://www.coinopexpress.com/img_coe/coinopexpress_weblogo.gif" alt="Coinop Express" width="241" height="55" border="0" /></a></td>
		<td width="50%" align="center" nowrap="nowrap"><font color="#000099" size="+3"><b><?php echo $title ?></b></font></td>
		<td width="20%" align="right" nowrap="nowrap"><font color="#000099" size="-1"><?php echo date('D, F d, Y') ?></font></td>
	</tr></table></td></tr>
	<tr><td><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td>
<?php
    return ob_get_clean();
}

/**
 * @param bool $plain
 *
 * @return string
 */
function msg_footer($plain = false)
{
    if ($plain) {
        return 'Notes: All prices quoted are in US dollars, unless specified otherwise.';
    }

    ob_start();
?>
	</td></tr></table></td></tr>
	<tr><td><table width="100%" border="0" bgcolor="#000099" cellspacing="0" cellpadding="2">
		<tr align="center"><td><a href="https://www.arcooda.com/"><img src="https://www.arcooda.com/img_coe/logo_coinop_footer.gif" alt="Coinop Express" width="270" height="30" border="0" /></a></td></tr>
		<tr align="center"><td><font color="#FFFFFF" size="-1">Phone: (+61) 02 4968 9313</font></td></tr>
		<tr align="center"><td><font color="#FFFFFF" size="-1">Fax: (+61) 02 4968 9314</font></td></tr>
		<tr align="center"><td><font size="-1"><a href="mailto:sales@arcooda.com" style="color:#77f;">sales@arcooda.com</a></font></td></tr>
		<tr align="center"><td><font size="-1">&nbsp;</font></td></tr>
		<tr align="center"><td><font color="#FFFFFF" size="-1">We respect your right to decline these emails, please <a href="https://www.arcooda.com/messages/unsubscribe.html" target="_blank" style="color:#77f;">Click Here</a> if you wish to unsubscribe.</font></td></tr>
	</table></td></tr>
</table></td></tr></table>
<?php
    return ob_get_clean();
}
