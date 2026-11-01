<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>META tags - HELP</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<?php
$action=$_REQUEST['action'];?>
<body bgcolor="#FFFFFF" text="#000000" leftmargin="30" topmargin="30" marginwidth="30" marginheight="30">
	<p><font color="#000066" size="3" face="Verdana, Arial, Helvetica, sans-serif"><b>META Tags</b></font></p>
	<p>
		<font face="Verdana, Arial, Helvetica, sans-serif" size="2">
			META Tags are basically information stored in a web page that is used by the search engines to rank the pages. It is usually information that the human visitor is not concerned with. There are three main tags used by the search engines.
		</font>
	</p>
	<?php if($action=='title'){?>
	<p><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>The TITLE Tag</b></font></p>
	<p>
		<font face="Verdana, Arial, Helvetica, sans-serif" size="2">
			The Title tag theocratically isn't a META tag, but it is very important for the search engines. The text that is entered in the Title tag is one of the most important factors used by search engines to rank the web page. In addition, all major web-crawlers will use the text of the title tag as the text in the search engine listing.
		</font>
	</p>
	<?php }
	else if($action=='desc'){?>
	<p><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>The META Description Tag</b></font></p>
	<p>
		<font face="Verdana, Arial, Helvetica, sans-serif" size="2">
			The text in the description tag is usually displayed underneath the title in the search engines, giving the visitor a small idea about the web page, (generally 200-250 characters from this tag are indexed by the web-crawlers, but a smaller amount maybe displayed)
		</font>
	</p>
	<?php }
	else if($action=='url'){?>
	<p><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>URL Keywords</b></font></p>
	<p>
		<font face="Verdana, Arial, Helvetica, sans-serif" size="2">
			These are the keywords that are used in the product url. Typically 3-4 keywords should be used in the url to optimise the keywords on the page.
		</font>
	</p>
	<?php }
	else{?>
	<p><font face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>The META Keywords Tag</b></font></p>
	<p>
		<font face="Verdana, Arial, Helvetica, sans-serif" size="2">
			The Keywords tag allows you to enter some additional information, which is used by the web-crawlers to index your web page. It is recommended that you use around 20-25 keywords for this tag.
		</font>
	</p>
	<?php }?>
	<p>
		<font face="Verdana, Arial, Helvetica, sans-serif" size="1">
			<i>NB: Web-Crawlers is a piece of software that collects documents from the web to build a searchable index for a search engine.</i>
		</font>
	</p>
	<div align="center">
		<input type="button" name="button" value="close" onclick="javascript: window.close()">
	</div>
</body>
</html>