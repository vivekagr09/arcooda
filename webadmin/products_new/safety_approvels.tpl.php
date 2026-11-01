<!-- Section : Manuals -->

<script type="text/javascript">
//<![CDATA[
function deleteManual(id){
    var theRow = getElement("item_manuals_rowa[" + id + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    setValue("item_manuals_deleted", getValue("item_manuals_deleted") + "," + id);
}
var item_manuals_count = <?=max(100, count($manuals))?>;
function manualAdd(data) { 
    var id=data.id;
    var description=data.description;
    var filename=data.filename;
    var staff=data.staff;
    var size=data.size;
    var filetype=data.filetype;
    var active=data.active;
    var alink = ((active=='y')?'de':'')+'activate_mm';
    var name = ((active=='y')?'De-':'')+'Activate';
    var classdis =(item_manuals_count%2==1)?'class="alt"':'';
    var activeurl = '<a href="/webadmin/products_new/?&action='+alink+'&return_url='+"<?=urlencode($_SERVER['REQUEST_URI']) ?>"+' &manual_id='+id+'">'+name+'</a>';
    $('#manuals').append('<tr  '+classdis+' style="text-align: center" name="item_manuals_rowa['+id+']" id="item_manuals_rowa['+id+']">\
        <td align="left"><b><a href="addManuals.php?action=view&man_id='+id+'" target="_blank">'+description+'</a></b><input type="text" name="item_manual['+id+'][description]" value="'+description+'" style="float: right; width: 50%;">        <input type="hidden" value="'+id+'" name="item_manual['+id+'][id]"></td> <td><b>'+filetype+'</b></td>\
        <td><b>'+size+'</b></td>\
        <td><b>'+staff+'</b></td>\
        <td><b><?= date(_('d/m/Y'), time()) ?></b></td>\
        <td><b>'+activeurl+'</b></td>\
        <td><input type="button" value="Delete" onclick="deleteManual('+id+')"></td>\
    </tr>');
    item_manuals_count++;
}

</script>
<div align="center">
    <?= formatHTMLTitle('<a href="javascript:toggleSection(\'sectionMan\');">Product Safety Approvals</a>');?>
</div>
<div id="sectionMan">
    <?php 
    $approvels_array=[];
    if($item['approvals']){
        $approvels_array=explode(',', $item['approvals']);
    }

    ?>
<table class="table_fields">
    <tr>
        <th>Product Approvals:</th>
        <td>
            <ul style="list-style:none;">
                <li style="text-align: center; display: inline-block; vertical-align: top; margin: 10px 5px;">
                    <input type="checkbox" name="item[approvals][]" value="RCM" <?= (in_array('RCM', $approvels_array))?'checked':''?>> RCM
                </li>
                <li style="text-align: center; display: inline-block; vertical-align: top; margin: 10px 5px;">
                    <input type="checkbox" name="item[approvals][]" value="EMC" <?= (in_array('EMC', $approvels_array))?'checked':''?>> EMC
                </li>
                <li style="text-align: center; display: inline-block; vertical-align: top; margin: 10px 5px;">
                    <input type="checkbox" name="item[approvals][]" value="ROHS" <?= (in_array('ROHS', $approvels_array))?'checked':''?>> ROHS
                </li>
                <li style="text-align: center; display: inline-block; vertical-align: top; margin: 10px 5px;">
                    <input type="checkbox" name="item[approvals][]" value="CE" <?= (in_array('CE', $approvels_array))?'checked':''?>> CE
                </li>
                <li style="text-align: center; display: inline-block; vertical-align: top; margin: 10px 5px;">
                    <input type="checkbox" name="item[approvals][]" value="UL" <?= (in_array('UL', $approvels_array))?'checked':''?>> UL
                </li>
                <li style="text-align: center; display: inline-block; vertical-align: top; margin: 10px 5px;">
                    <input type="checkbox" name="item[approvals][]" value="FCC" <?= (in_array('FCC', $approvels_array))?'checked':''?>> FCC
                </li>
                <li style="text-align: center; display: inline-block; vertical-align: top; margin: 10px 5px;">
                    <input type="checkbox" name="item[approvals][]" value="CCC" <?= (in_array('CCC', $approvels_array))?'checked':''?>> CCC
                </li>
            </ul>
        </td>
    </tr>
</table>
<table class="wide" align="center" style="width:95%;border:1px solid #CCCCCC" name="manuals" id="manuals" cellpadding=3>

    <tr>
        <th colspan="7" align="center" class="box"><strong>Certificate And Documents</strong></td>
    </tr>
    <tr style="background-color:#E3EBF7; font-size:11pt">
        <td width="30%" align="center" nowrap><b>Document Name</b></td>
        <td width="35%" align="center" nowrap><b>Notes/Description</b></td>
        <td width="5%" align="center" nowrap><b>File Type</b></td>
        <td width="5%" align="center" nowrap><b>Size</b></td>
        <td width="10%" align="center" nowrap><b>Uploaded By</b></td>
        <td width="5%" align="center" nowrap><b>Date Added</b></td>
        <td width="5%" align="center" nowrap><b>Active</b></td>
        <td width="5%" align="center" nowrap>&nbsp;</td>
    </tr>
<?php
    $count = 0;
    if(count($manuals) > 0) {
        foreach($manuals as $manual) {
            $desc = ($manual['description'] != '') ? $manual['description'] : $manual['file_original'];
            $alink = (($manual['active']=='y')?'de':'').'activate_mm';
            $name = (($manual['active']=='y')?'De-':'').'Activate';
            $active = '<a href="/webadmin/products_new/?&action='.$alink.'&return_url='.urlencode($_SERVER['REQUEST_URI']).'&manual_id='.$manual['id'].'">'.$name.'</a>';
?>
    <tr <?= ($count%2==1)?'class="alt"':''?> style="text-align: center" name="item_manuals_rowa[<?= $manual['id']?>]" id="item_manuals_rowa[<?= $manual['id']?>]">
        <td align="left"><b><a href="addManuals.php?action=view&man_id=<?= $manual['id']?>" target="_blank"><?= $manual['file_original'] ?></a></b>
        </td>
        <td align="left">    
            <input type="text" name="item_manual[<?php echo $manual['id'] ?>][description]" value="<?= $desc?>" style="float: right;
    width: 50%;"></td> 
    <td><b><?= strtoupper($manual['filetype'])?></b>
        <input type="hidden" value="<?= $manual['id']?>" name="item_manual[<?php echo $manual['id'] ?>][id]">
    </td>
        <td><b><?= formatFileSize($manual['size'])?></b></td>
        <td><b><?= $manual['user']?></b></td>
        <td><b><?= date("d/m/y",strtotime($manual['attach_date']))?></b></td>
        <td><b><?= $active?></b></td>
        <td><input type="button" value="Delete" onclick="deleteManual(<?= $manual['id']?>)"></td>
    </tr>
    <?php $count++;}}?>
</table>
    <input type="hidden" name="item_manuals_deleted" value="" />
<center><input type="button" value="Add Documents" onclick="window.open('addManuals.php?action=add&id=<?= $id?>','','width=700,height=150')" /></center>
<table class="table_fields">
    <tr>
        <th> Safety Approval Notes:</th>
        <td>
 <?=formatHTMLTextarea("item[product_safety_notes]", $item["product_safety_notes"], true, "", 10)?><br />
</td>
</tr>
</table>
</div><br />

<!-- Section : Manuals: End -->