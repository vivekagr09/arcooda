<?php
include_once('packing_common.inc.php');
if($_GET['return_message']=='') $_GET['return_message']='_';
include_once('templates/packing_header.tpl.php');

if(!isset($posted['angle_x'])) $posted['angle_x']=30;
if(!isset($posted['angle_y'])) $posted['angle_y']=315;
if(!isset($posted['angle_z'])) $posted['angle_z']=0;
?>
<script type="text/javascript">
var theTimer;
function refreshDisplay()
{
	clearTimeout(theTimer);
	theTimer=setTimeout('document.forms[0].submit()',1000);
}
function loadPreset(x,y,z)
{
	setValue('angle_x',x);
	setValue('angle_y',y);
	setValue('angle_z',z);
	refreshDisplay();
}
function adjustPreset(x,y,z)
{
	setValue('angle_x',(Number(getValue('angle_x'))+x+360)%360);
	setValue('angle_y',(Number(getValue('angle_y'))+y+360)%360);
	setValue('angle_z',(Number(getValue('angle_z'))+z+360)%360);
	refreshDisplay();
}
function saveImage(theFrm)
{
	var oldact=theFrm.action;
	theFrm.action='/webadmin/packing/packing_image3d.jpg?disposition=attachment';
	theFrm.submit();
	theFrm.action=oldact;
}
</script>

<div align="center"><iframe name="viewport" style="width:676px;height:516px;padding:0px;border:1px solid #CCCCCC;margin:0px;outline:0px;"></iframe></div>

<br />

<table width="528" align="center">
	<form accept-charset="utf-8" target="viewport" method="post" onsubmit="javascript:return false;" action="packing_image3d.jpg">
<?php
	$hidden=$posted;
	unset($hidden['angle_x']);
	unset($hidden['angle_y']);
	unset($hidden['angle_z']);
?>
		<?= formatHTMLInputs($hidden) ?>
		<input type="hidden" name="view_w" value="512" />
		<input type="hidden" name="view_r" value="512" />
		<input type="hidden" name="view_h" value="384" />
		<input type="hidden" name="view_b" value="384" />
		<tr>
			<td style="text-align:center;">
				<input type="button" value="Perspective" class="large" onclick="javascript:loadPreset(30,315,0);" /><br />
				<input type="button" value="Above" onclick="javascript:loadPreset(90,0,0);" /><input type="button" value="Below" onclick="javascript:loadPreset(270,0,0);" /><br />
				<input type="button" value="Front" onclick="javascript:loadPreset(0,270,0);" /><input type="button" value="Behind" onclick="javascript:loadPreset(0,90,0);" /><br />
				<input type="button" value="Left" onclick="javascript:loadPreset(0,0,0);" /><input type="button" value="Right" onclick="javascript:loadPreset(0,180,0);" /><br />
			</td>

			<td style="text-align:center;">
				<button onclick="javascript:adjustPreset(0,0,5);"><img src="/webadmin/images/roll-left.gif" /></button><button onclick="javascript:adjustPreset(5,0,0);"><img src="/webadmin/images/pitch-up.gif" /></button><button onclick="javascript:adjustPreset(0,0,-5);"><img src="/webadmin/images/roll-right.gif" /></button><br />
				<button onclick="javascript:adjustPreset(0,5,0);"><img src="/webadmin/images/turn-left.gif" /></button><button onclick="javascript:adjustPreset(-5,0,0);"><img src="/webadmin/images/pitch-down.gif" /></button><button onclick="javascript:adjustPreset(0,-5,0);"><img src="/webadmin/images/turn-right.gif" /></button><br />
				<input type="button" style="width:auto" value="Save Image As" onclick="javascript:saveImage(this.form);">
			</td>

			<td style="text-align:center;">
				X Angle: <input class="small" type="text" style="text-align:right;" name="angle_x" value="<?= $posted['angle_x'] ?>" /><br />
				Y Angle: <input class="small" type="text" style="text-align:right;" name="angle_y" value="<?= $posted['angle_y'] ?>" /><br />
				Z Angle: <input class="small" type="text" style="text-align:right;" name="angle_z" value="<?= $posted['angle_z'] ?>" /><br />
				<input class="large" type="submit" value="Display" onclick="javascript:refreshDisplay();" /><br />
			</td>
		</tr>
	</form>
</table>

<script type="text/javascript">
try
{
	document.forms[0].submit();
}
catch(er)
{
}
</script>

<?php
include_once('templates/packing_footer.tpl.php');
?>