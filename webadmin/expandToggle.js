<!--

NS4 = (document.layers) ? 1 : 0;
IE4 = (document.all) ? 1 : 0;
ver4 = (NS4 || IE4) ? 1 : 0;

if (ver4) {
    with (document) {
        write("<STYLE TYPE='text/css'>");
        if (NS4) {
            write(".parent {position:absolute; visibility:visible}");
            write(".child {position:absolute; visibility:visible}");
            write(".regular {position:absolute; visibility:visible}")
        }
        else {
            write(".child {display:none}")
        }
        write("</STYLE>");
    }
}

isExpanded = false;

function getIndex(el) {
    ind = null;
    for (i=0; i<document.layers.length; i++) {
        whichEl = document.layers[i];
        if (whichEl.id == el) {
            ind = i;
            break;
        }
    }
    return ind;
}

function arrange() {
    nextY = document.layers[firstInd].pageY + document.layers[firstInd].document.height;
    for (i=firstInd+1; i<document.layers.length; i++) {
        whichEl = document.layers[i];
        if (whichEl.visibility != "hide") {
            whichEl.pageY = nextY;
            nextY += whichEl.document.height;
        }
    }
}

function initIt(){
    if (!ver4)
	{
		if(document.getElementsByTagName)
		{
			var theElems=document.getElementsByTagName("DIV");
			for(var i=0;i<theElems.length;i++)
				if(theElems[i].id.indexOf("Child")!=-1)
					theElems[i].style.display="none";
		}
		return;
	}
    if (NS4) {
        for (i=0; i<document.layers.length; i++) {
            whichEl = document.layers[i];
            if (whichEl.id.indexOf("Child") != -1) whichEl.visibility = "hide";
        }
        arrange();
    }
    else {
        divColl = document.all.tags("DIV");
        for (i=0; i<divColl.length; i++) {
            whichEl = divColl(i);
            if (whichEl.className == "child") whichEl.style.display = "none";
        }
    }
}

function expandIt(el) {
	if(!ver4)
	{
		var whichEl=document.getElementById(el+"Child");
		if (whichEl.style.display=="none")
			whichEl.style.display="inline";
		else
			whichEl.style.display="none";
		return;
	}

    if (IE4) {
        whichEl = eval(el + "Child");
        whichIm = event.srcElement;
        if (whichEl.style.display == "none") {
            whichEl.style.display = "block";
            whichIm.src = "triUp.gif";
        }
        else {
            whichEl.style.display = "none";
            whichIm.src = "triDown.gif";
        }
    }
    else {
        whichEl = eval("document." + el + "Child");
        whichIm = eval("document." + el + "Parent.document.images['imEx']");
        if (whichEl.visibility == "hide") {
            whichEl.visibility = "show";
            whichIm.src = "triUp.gif";
        }
        else {
            whichEl.visibility = "hide";
            whichIm.src = "triDown.gif";
        }
        arrange();
    }
}

function expandAll() {
    if (!ver4) return;
    newSrc = (isExpanded) ? "triDown.gif" : "triUp.gif";

    if (NS4) {
        document.images["imEx"].src = newSrc;
        for (i=firstInd; i<document.layers.length; i++) {
            whichEl = document.layers[i];
            if (whichEl.id.indexOf("Parent") != -1) {
                whichEl.document.images["imEx"].src = newSrc;
            }
            if (whichEl.id.indexOf("Child") != -1) {
                whichEl.visibility = (isExpanded) ? "hide" : "show";
            }
        }

        arrange();
        if (isExpanded) scrollTo(0,document.layers[firstInd].pageY);
    }
    else {
        divColl = document.all.tags("DIV");
        for (i=0; i<divColl.length; i++) {
            if (divColl(i).className == "child") {
                divColl(i).style.display = (isExpanded) ? "none" : "block";
            }
        }
        imColl = document.images.item("imEx");
        for (i=0; i<imColl.length; i++) {
            imColl(i).src = newSrc;
        }
    }

    isExpanded = !isExpanded;
}

onload = initIt;

//-->