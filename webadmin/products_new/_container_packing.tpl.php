<!-- container_packing.tpl -->

<?php
/**
 * Product Container Packing template
 *
 * Inputs:
 * - $action
 * - $item
 * - - id
 * - $item_containers[]
 * - - id
 * - - product_id
 * - - contload_20ft_qty
 * - - contload_20ft_type
 * - - contload_40ft_qty
 * - - contload_40ft_type
 * - - freeload_20ft_qty
 * - - freeload_20ft_type
 * - - freeload_40ft_qty
 * - - freeload_40ft_type
 *
 * Overwrites:
 * - $temp
 * - $temp2
 */
if (!isset($item_containers) || !is_array($item_containers)) {
    return;
}

?>

<script type="text/javascript">
//<![CDATA[
function calcHeight()
{
	var the_frame=getElement("the_iframe");
	if(the_frame==null) return;
	var the_height=the_frame.contentWindow.document.body.scrollHeight;
	if(the_height<50) the_height=50;
	the_frame.style.height=the_height+15;
}
//Function to calculate how many of the product will
//fit in a container using crating dimensions
function updateCratingApprox()
{
	var theWidths=ProductPacking.sum("width", 3);
	var theDepths=ProductPacking.sum("depth", 3);
	var theArea=0.0;
    theArea=Number(theWidths*theDepths);
	/*for(var i=0;i<theWidths.length;i++)
		theArea+=Number(theWidths[i].value*theDepths[i].value);*/
	if(theArea==0.0) return;

	//20ft Container
	var flag=false;
	var theOutput=getElement('item_containers[0][contload_20ft_qty]');
	var theRadio=document.getElementsByName('item_containers[0][contload_20ft_type]');
	for(var i=0;i<theRadio.length;i++)
		if(theRadio[i].value=='approx' && theRadio[i].checked!=false)
			flag=true;
	if(flag) theOutput.value=Math.round((235.0*590.0)/theArea);

	//40ft Container
	var flag=false;
	var theOutput=getElement('item_containers[0][contload_40ft_qty]');
	var theRadio=document.getElementsByName('item_containers[0][contload_40ft_type]');
	for(var i=0;i<theRadio.length;i++)
		if(theRadio[i].value=='approx' && theRadio[i].checked!=false)
			flag=true;
	if(flag) theOutput.value=Math.round((235.0*1201.0)/theArea);
}
//Function to calculate how many of the product will
//fit in a container using without crating dimensions
function updateFreeApprox()
{
	var theWidth=getElement('item_dimension[width]').value;
	var theDepth=getElement('item_dimension[depth]').value;
	var theArea=Number(theWidth*theDepth);
	if(theArea==0.0) return;

	//20ft Container
	var flag=false;
	var theOutput=getElement('item_containers[0][freeload_20ft_qty]');
	var theRadio=document.getElementsByName('item_containers[0][freeload_20ft_type]');
	for(var i=0;i<theRadio.length;i++)
		if(theRadio[i].value=='approx' && theRadio[i].checked!=false)
			flag=true;
	if(flag) theOutput.value=Math.round((235.0*590.0)/theArea);

	//40ft Container
	var flag=false;
	var theOutput=getElement('item_containers[0][freeload_40ft_qty]');
	var theRadio=document.getElementsByName('item_containers[0][freeload_40ft_type]');
	for(var i=0;i<theRadio.length;i++)
		if(theRadio[i].value=='approx' && theRadio[i].checked!=false)
			flag=true;
	if(flag) theOutput.value=Math.round((235.0*1201.0)/theArea);
}
$(function() {
    updateFreeApprox();
    updateCratingApprox();
    //setTimeout("calcHeight()", 2700);
});
//]]>
</script>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionContainerPacking&quot;);return false">Container Packing</a>')?>

<div id="sectionContainerPacking">
    <table class="table_fields">
<?php

$temp = reset($item_containers);
$temp2 = 'item_containers[0]';
?>
        <tr>
            <th>20ft Container Qty (without crating) :</th>
            <td class="right">
                <input type="hidden" name="<?= $temp2 ?>[id]" value="<?= $temp['id'] ?>" />
                <input type="hidden" name="<?= $temp2 ?>[product_id]" value="<?= $temp['product_id'] ?>" />
                <input type="text" name="<?= $temp2 ?>[freeload_20ft_qty]" value="<?php echo $temp['freeload_20ft_qty'] ?>" />
                <b>Approx</b><input type="radio" name="<?= $temp2 ?>[freeload_20ft_type]" value="approx"<?php echo ('real' != $temp['freeload_20ft_type']) ? ' checked="checked"' : '' ?> />
                <b>Real</b><input type="radio" name="<?= $temp2 ?>[freeload_20ft_type]" value="real"<?php echo ('real' != $temp['freeload_20ft_type']) ? '' : ' checked="checked"' ?> />
            </td>
        </tr>
        <tr>
            <th>20ft Container Qty (with crating) :</th>
            <td class="right">
                <input type="text" name="<?= $temp2 ?>[contload_20ft_qty]" value="<?php echo $temp['contload_20ft_qty'] ?>" />
                <b>Approx</b><input type="radio" name="<?= $temp2 ?>[contload_20ft_type]" value="approx"<?php echo ('real' != $temp['contload_20ft_type']) ? ' checked="checked"' : '' ?> />
                <b>Real</b><input type="radio" name="<?= $temp2 ?>[contload_20ft_type]" value="real"<?php echo ('real' != $temp['contload_20ft_type']) ? '' : ' checked="checked"' ?> />
                <a class="unfinished" href="#">View</a>
            </td>
        </tr>
        <tr>
            <th>40ft Container Qty (without crating) :</th>
            <td class="right">
                <input type="text" name="<?= $temp2 ?>[freeload_40ft_qty]" value="<?php echo $temp['freeload_40ft_qty'] ?>" />
                <b>Approx</b><input type="radio" name="<?= $temp2 ?>[freeload_40ft_type]" value="approx"<?php echo ('real' != $temp['freeload_40ft_type']) ? ' checked="checked"' : '' ?> />
                <b>Real</b><input type="radio" name="<?= $temp2 ?>[freeload_40ft_type]" value="real"<?php echo ('real' != $temp['freeload_40ft_type']) ? '' : ' checked="checked"' ?> />
            </td>
        </tr>
        <tr>
            <th>40ft Container Qty (with crating) :</th>
            <td class="right">
                <input type="text" name="<?= $temp2 ?>[contload_40ft_qty]" value="<?php echo $temp['contload_40ft_qty'] ?>" />
                <b>Approx</b><input type="radio" name="<?= $temp2 ?>[contload_40ft_type]" value="approx"<?php echo ('real' != $temp['contload_40ft_type']) ? ' checked="checked"' : '' ?> />
                <b>Real</b><input type="radio" name="<?= $temp2 ?>[contload_40ft_type]" value="real"<?php echo ('real' != $temp['contload_40ft_type']) ? '' : ' checked="checked"' ?> />
                <a class="unfinished" href="#">View</a>
            </td>
        </tr>
        <tr>
            <th>Online Notes :</th>
            <td>
                <textarea name="item[container_text]" id="item[container_text]" cols="75" rows="2"><?= $item['container_text'] ?></textarea>
            </td>
        </tr>
    </table>
    <br />
    <br />
<?php
unset($temp);
unset($temp2);

if ('add' !== $action) {
?>
    <div style="width:95%;margin:auto;">
        Without Crating:<br />
        <iframe name="the_iframe1" src="/webadmin/packing/packingsearch.php?product_id=<?= $item['id'] ?>&amp;product_qty=1&amp;with_crating=0" style="width:100%" onload="javascript:calcHeight();"></iframe>
    </div>
    <br />
    <br />

    <div style="width:95%;margin:auto;">
        With Crating:
        <iframe name="the_iframe2" src="/webadmin/packing/packingsearch.php?product_id=<?= $item['id'] ?>&amp;product_qty=1&amp;with_crating=1" style="width:100%" onload="javascript:calcHeight();"></iframe>
    </div>
    <br />
    <br />
<?php
}

?>
</div>

<div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionContainerPacking_hideNote">
    Container Packing can be viewed by Clicking "Container Packing" above<br />
</div>

<!-- /container_packing.tpl -->
