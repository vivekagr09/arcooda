<?php
/******************************************************************************
 ** Title.........:    Attachment HTML
 ** Version.......:    1.0
 ** Author........:    Xiang Wei ZHUO <c9807224@studentmail.newcastle.edu.au>
 ** Filename......:    attachment.inc
 ** Last changed..:    14/12/2000
 ** Notes.........:    The HTML for the attachment page
 *****************************************************************************/

?><!DOCTYPE html>
<html>
	<head>
		<title>Attachments</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<style type="text/css">
/*<![CDATA[*/
body,p,td {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:12px}
.td_options {color:#fff;font-size:12px}
a:hover {color:#f00}
/*]]>*/
		</style>
	</head>
	<!-- header -->
	<body text="#000000" style="background:#fff;"><form action="<?=$_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data" method="post"><table width="100%" cellspacing="0" cellpadding="5" style="border-style:none;">
		<tr><td style="background:#eee;"><b>Step 1</b> - Select a file to attach</td></tr>
		<tr><td><div style="text-align:center;">
			<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
			<input type="file" name="the_file" size="30" class="button" />
		</div></td></tr>
		<tr><td style="background:#eee;"><b>Step 2</b> - Click on &quot;Attach&quot; to include the file in the Email</td></tr>
		<tr><td><table cellpadding="5" cellspacing="0" style="text-align:center;"><tr>
			<td style="text-align:center;" valign="middle">
				<input type="submit" name="task" value="Attach" class="button" />
				<br />
				<br />
				<input type="submit" name="task" value="Remove" class="button" />
			</td>
			<td><select name="files" size="5" class="textbox">
				<option value="">-- Attachment --</option>
<?php
fileOptionsList();
?>
			</select></td>
		</tr></table></td></tr>
		<tr><td style="background:#eee;"><b>Step 3</b> - When ready click on &quot;Done&quot; below</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><div style="text-align:center;"><input type="button" name="done" value="Done" onclick="self.close()" class="button" /></div></td></tr>
	</table></form></body>
</html>