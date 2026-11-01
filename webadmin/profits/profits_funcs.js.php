<?php
header('Content-Type: text/javascript');
?>
function getElement(name)
{
	var elem=document.getElementById(name);
	if (elem) return elem;
	elem=document.getElementsByName(name);
	if (elem.length>0) return elem[0];
	if (document.all!=null)
	{
		for (var key in document.all)
		{
			if (document.all[key].id==name) return document.all[key];
			if (document.all[key].name==name) return document.all[key];
		}
	}
	return null;
}

function toggleSection(sectionName)
{
	var theSect=getElement(sectionName);
	var hideNote=getElement(sectionName+'_hideNote');
	if (theSect==null) return;
	if (theSect.style.display=='none')
	{
		theSect.style.display='';
		if (hideNote!=null) hideNote.style.display='none';
	}
	else
	{
		theSect.style.display='none';
		if (hideNote!=null) hideNote.style.display='';
	}
}
