var instatus = true;	//determines if tooltip will also be displayed
			//in the status bar
var ttname = "tooltip"; //name of the tooltip div tag (used on next line)
var bcolor = "lightblue";	//background color of tooltip
var fcolor = "#000000";		//color of tooltip text
var opacity = 85;		//translucency of tooltip
var borderstyle = "solid";	//tooltip border style
var borderwidth = 1;		//tooltip border width
var bordercolor = "#000000";	//tooltip border color
var font = "MS Sans Serif";	//font family
var fontsize = 8;		//font size
//this is the div tag that holds the tooltip
document.write('<div style="color: '+fcolor+'; z-index: 2000; position: absolute; filter: alpha(opacity='+opacity+'); background-color: '+bcolor+'; border-style: '+borderstyle+'; border-width: '+borderwidth+'; border-color: '+bordercolor+'; font-family: '+font+'; font-size: '+fontsize+'; visibility: hidden;" id="'+ttname+'">');
document.write('Just an empty link!');
document.write('</div>');
function ToolTip() {
	e = event;
	es = event.srcElement;	//event source
	//if the event source has a tooltip set and you DID move the
	//mouse over a tag, then drag the tooltip
	if (es.tooltip != "" && es.tooltip != undefined) {
		document.getElementById(ttname).style.visibility = "hidden";
		document.getElementById(ttname).style.left = e.x+30;
		document.getElementById(ttname).style.top = e.y;
		document.getElementById(ttname).style.position = "absolute";
		document.getElementById(ttname).innerHTML = es.tooltip;
		if (instatus) {
			window.status = es.tooltip;
		}
	}
}
function hideToolTip() {
	//hide tooltip
	document.getElementById(ttname).style.visibility = "hidden";
	if (instatus) {
		window.status = "";
	}
}
document.onmousemove = ToolTip;
document.onmouseout = hideToolTip;