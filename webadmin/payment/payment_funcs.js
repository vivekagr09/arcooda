function getElement(name)
{
	var elem=document.getElementById(name);
	if(elem) return elem;
	elem=document.getElementsByName(name);
	if(elem.length==0) return null;
	return elem[0];
}

function setValue(name,value)
{
	var elem=getElement(name);
	if(elem==null) return;
	if(elem.tagName=='SELECT')
		for(var i=0;i<elem.options.length;i++)
			if(elem.options[i].value==value)
			{
				elem.options[i].selected=true;
				elem.selectedIndex=i;
			}
			else
				elem.options[i].selected=false;
	if(elem.tagName=='SPAN' || elem.tagName=='DIV' || elem.tagName=='TD')
		elem.innerHTML=value;
	if(elem.value!=null) elem.value=value;
	else if(elem.innerHTML) elem.innerHTML=value;
}

function textareaAutoSize(theElem,stepSize,minSize,maxSize)
{
	if(theElem==null || theElem.rows==null || theElem.cols==null || theElem.value==null) return;
	if(stepSize==null || stepSize<1) stepSize=5;

	var rowContent=theElem.value.split('\n');
	var rowCount=rowContent.length;

	if(theElem.cols>0)
		for(i=0;i<rowContent.length;i++)
			rowCount+=Math.floor(rowContent[i].length/theElem.cols);

	rowCount=Math.floor(rowCount/stepSize)*stepSize+stepSize;

	if(minSize!=null && minSize>rowCount) rowCount=minSize;
	if(maxSize!=null && maxSize>minSize && maxSize<rowCount) rowCount=maxSize;

	theElem.rows=rowCount;
}

function getValue(name)
{
	var elem=getElement(name);
	if(elem==null) return null;
	if(elem.value!=null) return elem.value;
	if(elem.innerHTML!=null) return elem.innerHTML;
	return null;
}

function toggleSection(sectionName)
{
	var theSect=getElement(sectionName);
	var hideNote=getElement(sectionName+'_hideNote');
	if(theSect==null) return;
	if(theSect.style.display=='none')
	{
		theSect.style.display='inline';
		if(hideNote!=null) hideNote.style.display='none';
	}
	else
	{
		theSect.style.display='none';
		if(hideNote!=null) hideNote.style.display='inline';
	}
}
