<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/webadmin/common.inc.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/webadmin/header.inc.php";
	global $DB;
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $DB->query("UPDATE `site_front_page` SET `content` = '".$_POST['data1']['content']."' ,`font_color` = '".$_POST['data1']['text_color']."',`background_colour` = '".$_POST['data1']['background_color']."',`active` = '".$_POST['data1']['active']."' WHERE `type` = 'header' LIMIT 1");
}
$data1 = $DB->query_first("SELECT * FROM `site_front_page` WHERE `type` = 'header'  LIMIT 1", MYSQLI_ASSOC);
?>
<script type="text/javascript">
//<![CDATA[
function link_insert(name) {
    var theElem = document.getElementsByName(name);
    if (theElem.length == 0) return;
    theElem = theElem[0];
    if (theElem.selectionStart != null && theElem.selectionEnd != null) {
        var a = theElem.selectionStart;
        var b = theElem.selectionEnd;
        theElem.value = theElem.value.substring(0, a) + '<a href="http://www.WEBSITE" target="_blank">' + theElem.value.substring(a, b) + '</a>' + theElem.value.substring(b);
        theElem.selectionStart = a + 20;
        theElem.selectionEnd = a + 27;
        theElem.focus();
    }
    else {
        var repltext = null;
        var seltext = (document.all) ? document.selection.createRange() : document.getSelection();
        var selit = (document.all) ? document.selection.createRange().text : document.getSelection();
        if (seltext != null && selit.length >= 1)
            seltext.text = '<a href="http://www.WEBSITE" target="_blank">' + seltext.text + '</a>';
        else
            theElem.value += '<a href="http://www.WEBSITE" target="_blank">LINK</a>';
    }
}
function generic_insert(name, tag) {
    var theElem = document.getElementsByName(name);
    if (theElem.length == 0) return;
    theElem = theElem[0];
    if (theElem.selectionStart != null && theElem.selectionEnd != null) {
        var a = theElem.selectionStart;
        var b = theElem.selectionEnd;
        theElem.value = theElem.value.substring(0, a) + '<' + tag + '>' + theElem.value.substring(a, b) + '</' + tag + '>' + theElem.value.substring(b);
        theElem.selectionStart = a + 2 + tag.length;
        theElem.selectionEnd = 2 + tag.length + b;
        theElem.focus();
    }
    else {
        var repltext = null;
        var seltext = (document.all) ? document.selection.createRange() : document.getSelection();
        var selit = (document.all) ? document.selection.createRange().text : document.getSelection();
        if (seltext != null && selit.length >= 1)
            seltext.text = '<' + tag + '>' + seltext.text + '</' + tag + '>';
        else
            theElem.value += '<' + tag + '>BOLDTEXT</' + tag + '>';
    }
}
function str_replace(str_normal, str_find, str_replace) {
    // case sensitive function at the moment...
    return (str_normal.split(str_find)).join(str_replace);
}
function updateTags() {

  var text=getValue('data1[content]');
  var textcolour=getValue('data1[text_color]');
  var backcolour=getValue('data1[background_color]');
  	console.log(textcolour);
  	console.log(backcolour);
  $('#displayexample').html(text);
  $('#displayexample').css("background-color", backcolour);
  $('#displayexample').css("color", textcolour);
}
function my_trim(array) {
    for (i = 0; i < array.length; i++) {
        //alert(array[i]);
        var word = array[i] + "";
        array[i] = word.replace(" ", "");
    }
    //alert(array);
    return array;
}
function getElement(name) {
    var theElem = document.getElementById(name);
    if (theElem != null) return theElem;
    var theElems = document.getElementsByName(name);
    if (theElems.length > 0) return theElems[0];
    return null;
}
function toggleSection(sectionName) {
    var theSect = getElement(sectionName);
    if (theSect == null) return;
    if (theSect.style.display == 'none')
        theSect.style.display = 'inline';
    else
        theSect.style.display = 'none';
}
//]]>
</script>
<link rel="stylesheet" type="text/css" href="style.css" />

<div class="news">
<form accept-charset="utf-8" action="" method="post">
<div align="center">
<div class="flagHeader" style="width:95%;align:center"><span><a href="javascript:toggleSection('sectionDesc');">TOP HEADER TEXT</a></span></div>
</div><br />
<div id="sectionDesc">
	<table class="wide" style="width:95%" align="center">
		<tr>
			<td style="width:15%" class="left_title">Title:</td>
			<td style="width:85%">
				<?php $onchange = ('' == $data1['news_id']) ? 'javascript: updateTags();' : ''?>
				<?php $desc     = ('' == $data1['content']) ? '' : str_replace('<', '&lt;', str_replace('<br />', '', $data1['content']))?>
				<textarea name="data1[content]" style="width:600px;height:192px;" onchange="<?=$onchange?>"><?=trim($desc)?></textarea>
				<br />
				<input type="button" value="Insert Link" onclick="javascript:link_insert('data1[content]');" />
				<input type="button" value="Bold" onclick="javascript:generic_insert('data1[content]','b');" />
				<input type="button" value="Italics" onclick="javascript:generic_insert('data1[content]','i');" />
				<input type="button" value="Underline" onclick="javascript:generic_insert('data1[content]','u');" />
				<!-- <input type="checkbox" name="data1[short_desc_raw]" />Save as Raw HTML -->
			</td>
		</tr>
		<br/><br/>
		<tr>
			<td style="width:15%" class="left_title"> Colour</td>
			<td style="width:85%;text-align:left" class="">
			

				<span style="margin-left: 39px;">Text Colour <input type="color" name="data1[text_color]" onchange="updateTags()" value="<?= $data1['font_color'] ?>"></span>
				<span style="margin-left: 39px;">BackGround Colour <input type="color" name="data1[background_color]" onchange="updateTags()" value="<?= $data1['background_colour'] ?>"></span>
			</td>
		</tr>
		
	</table>
	<table class="wide" style="width:95%;border:none;height: 50px;" align="center">
		<tr>
			<td style="width:15%" class="left_title"> Example</td>
			<?php 

				$style="padding: 20px;font-size: 20px;font-family: auto;color:".$data1['font_color'].";background-color:".$data1['background_colour'].";";
			?>
			<td style="width:85%;text-align:left" class="">
				<p id="displayexample" style="<?= $style ?>"><?= $data1['content']?></p>
			</td>
		</tr>
	</table>
</div>

<br />


<br />
<div align="center">
<div class="flagHeader" style="width:95%;align:center"><span><a href="javascript:toggleSection('sectionSta');">Status</a></span></div>
</div><br />
<div id="sectionSta">
	<table class="wide" style="width: 95%" align="center">
	<tr>
		<td class="left_title">Active :</td>
		<td><select name="data1[active]" style="background: <?=('n' == $data1['active']) ? 'red' : 'green'?>;" onchange="updateSelect(this);return false;">
			<option style="background: green;" value="y"<?=('y' == $data1['active']) ? ' selected="selected"' : ''?>>Yes</option>
			<option style="background:red" value="n"<?=('n' == $data1['active']) ? ' selected="selected"' : ''?>>No</option>
		</select></td>
	</tr>
	</table>
</div>
<br />
<div align="center"><input type="submit" value="Edit" style="width:100px"/> </div>
</form>
</table>
</div>
