<?php
global $_GLOBALS;

?>
<html>
<head>
<style type="text/css">
/*<![CDATA[*/
body,p,td{font-family:Arial;font-size:12px}
a:hover{color:red}
/*]]>*/
</style>
</head>
<body>
<table style="width:100%;height:100%;" cellpadding="0" cellspacing="0">
	<tr>
		<td style="text-align:center;vertical-align:top;">

			<table style="width:100%;background-color:#ccc;">
				<tr>
					<td style="text-align:center"><b><font size="5"><?=htmlspecialchars($_GLOBALS["site_settings"]["site_name"])?> Admin</font></b></td>
					<td style="width:1%;text-align:right;white-space:nowrap;">
						<input type="button" style="font-size:10px;width:32px;" value="Opt"	onclick="window.open('/webadmin/staff/?action=options&amp;return_message=options_change');" />
						<input type="button" style="font-size:10px;width:32px;" value="SJ" onclick="window.open('/webadmin/softjob/?action=add&amp;refer=true');" />
					</td>
				</tr>
			</table>
