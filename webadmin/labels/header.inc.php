<?php
include_once("../header.inc.php");
if(!function_exists('formatHTMLTitle'))
{
	function formatHTMLTitle($title)
	{
		return "<br />\n<div class=\"flagHeader\"><span>".$title."</span></div>\n<br />\n";
	}
}
?>
<p align="right">Product Labels: <a href="index.php">Start a New List</a></p>

<script type="text/javascript">
function getElement(name)
{
	var elem=document.getElementById(name);
	if(elem) return elem;
	elem=document.getElementsByName(name);
	if(elem.length==0) return null;
	return elem[0];
}

function toggleSection(sectionName)
{
	var theSect=getElement(sectionName);
	if(theSect==null) return;
	if(theSect.style.display=='none')
		theSect.style.display='inline';
	else
		theSect.style.display='none';
}
</script>

<div id="labcontent" align="center">
