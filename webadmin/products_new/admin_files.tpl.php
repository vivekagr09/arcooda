<!-- Section : Manuals -->

<script type="text/javascript">
//<![CDATA[
function deleteAdminFiles(id){
    var theRow = getElement("admin_files_rowa[" + id + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    setValue("admin_files_deleted", getValue("admin_files_deleted") + "," + id);
}
var admin_files_count = <?=max(100, count($admin_files))?>;
function adminFileAdd(data) { 
    var id=data.id;
    var description=data.description;
    var filename=data.file_orig;
    var staff=data.staff;
    var size=data.size;
    var filetype=data.filetype;
    var active=data.active;
    var alink = ((active=='y')?'de':'')+'activate_mm';
    var name = ((active=='y')?'De-':'')+'Activate';
    var classdis =(admin_files_count%2==1)?'class="alt"':'';
    var activeurl = '<a href="/webadmin/products_new/?&action='+alink+'&return_url='+"<?=urlencode($_SERVER['REQUEST_URI']) ?>"+' &admin_file_id='+id+'">'+name+'</a>';
    if(data.edit==0){
        var imagetext='<img src="addAdminFiles.php?action=view&main_id='+id+'" width="100">';
        if (filetype == 'pdf' || filetype == 'pptx'){
            imagetext='<img src="/images/pdf_new.png" width="100" style="width:96px; height:96px;background:white;">';
        }else if(filetype == 'xlsx'){
            imagetext='<img src="/images/xls_new.png" width="100" style="width:96px; height:96px;background:white;">';
        }else if(filetype == 'docx' || filetype == 'doc'){
            imagetext='<img src="/images/doc_new.png" width="100" style="width:96px; height:96px;background:white;">';
        }else{

        }
        
        $('#admin_files').append('<tr  '+classdis+' style="text-align: center" name="admin_files_rowa['+id+']" id="admin_files_rowa['+id+']">\
            <td align="center" style="padding:10px;"><b><a href="addAdminFiles.php?action=view&main_id='+id+'" target="_blank">'+imagetext+'</a></b></td><td align="center"><a href="addAdminFiles.php?action=view&main_id='+id+'" target="_blank"><b>'+filename+'</b></a></td><td align="center"><textarea rows="3" name="admin_file['+id+'][description]" value="'+description+'" style="float: right; width: 90%;">'+description+'</textarea><input type="hidden" value="'+id+'" name="admin_file['+id+'][id]"></td> <td align="center"><b>'+filetype+'</b></td>\
            <td align="center"><b>'+size+'</b></td>\
            <td align="center"><b>'+staff+'</b></td>\
            <td align="center"><b><?= date(_('d/m/Y'), time()) ?></b></td>\
            <td align="center"><input type="button" value="Delete" onclick="deleteAdminFiles('+id+')"></td>\
        </tr>');
        admin_files_count++;
    }else{
       window.location.reload();
    }
}

</script>
<div align="center">
    <?= formatHTMLTitle('<a href="javascript:toggleSection(\'admin_files_section\');">Product Admin Files & Screenshots</a>');?>
</div>
<div id="admin_files_section">
<table class="wide" align="center" style="width:95%;border:1px solid #CCCCCC" name="admin_files" id="admin_files" cellpadding=3>
    <tr style="background-color:#E3EBF7; font-size:11pt">
        <td width="20%" align="center" nowrap></td>
        <td width="10%" align="center" nowrap><b>Document Name</b></td>
        <td width="35%" align="center" nowrap><b>Notes/Description</b></td>
        <td width="5%" align="center" nowrap><b>File Type</b></td>
        <td width="5%" align="center" nowrap><b>Size</b></td>
        <td width="10%" align="center" nowrap><b>Uploaded By</b></td>
        <td width="5%" align="center" nowrap><b>Date Added</b></td>
        <!-- <td width="5%" align="center" nowrap><b>Active</b></td> -->
        <td width="5%" align="center" nowrap>&nbsp;</td>
    </tr>
<?php
    $fileArray = [];
    foreach($admin_files as $file){
        $fileArray[$file['type']][] = $file;
    }
    $count = 0;
    if(count($fileArray) > 0) {
        $staff_list = productGetOptionList('staff_id');
        foreach($fileArray as $name => $filedata) {
            echo '<tr style="background-color: pink;line-height: 20px;"><td colspan="8"><b style="font-size: 16px;">'.$name.'<b></td></tr>';
        foreach($filedata as $files) {
            $desc = ($files['description'] != '') ? $files['description'] : $files['file_original'];
            $alink = (($files['active']=='y')?'de':'').'activate_mm';
            $name = (($files['active']=='y')?'De-':'').'Activate';
            $active = '<a href="/webadmin/products_new/?&action='.$alink.'&return_url='.urlencode($_SERVER['REQUEST_URI']).'&manual_id='.$files['id'].'">'.$name.'</a>';
?>
    <tr <?= ($count%2==1)?'class="alt"':''?> style="text-align: center" name="admin_files_rowa[<?= $files['id']?>]" id="admin_files_rowa[<?= $files['id']?>]">
        <td align="center" style="padding:10px;"><b><a href="addAdminFiles.php?action=view&main_id=<?= $files['id']?>" target="_blank">
            <?php 
            if($files['filetype'] == 'pdf' or $files['filetype'] == 'pptx'){
                echo '<img  src="/images/pdf_new.jpg" width="100" style="width:96px; height:96px;background:white;">';
            }else if($files['filetype'] == 'xlsx' ){
                echo '<img  src="/images/xls_new.png" width="100" style="width:96px; height:96px;background:white;">';
            }else if($files['filetype'] == 'docx' or $files['filetype'] == 'doc'){
                 echo '<img  src="/images/doc_new.png" width="100" style="width:96px; height:96px;background:white;">';
            }else if($files['filetype'] == 'bin' or $files['filetype'] == 'bin'){
                echo '<img  src="/images/bin-file-icon.png" width="100" style="width:86px; height:86px;background:white;">';
            }else{
                 echo '<img src="addAdminFiles.php?action=view&main_id='.$files['id'].'" width="100" style="width:96px; height:96px;">';
             }
            ?>
           

        </a></b>
        </td>
        <td align="center"><a href="addAdminFiles.php?action=view&main_id=<?= $files['id']?>" target="_blank"><b><?= $files['file_original']?></b></a></td>
        <td align="center">    
            <textarea  name="admin_file[<?php echo $files['id'] ?>][description]" value="<?= $desc?>" style="float: right;
    width: 90%;" rows="3"><?= $desc?></textarea></td> 
    <td align="center"><b><?= strtoupper($files['filetype'])?></b>
        <input type="hidden" value="<?= $files['id']?>" name="admin_file[<?php echo $files['id'] ?>][id]">
    </td>
        <td align="center"><b><?= formatFileSize($files['size'])?></b></td>
        <td align="center"><b><?= $staff_list[$files['updated_by']] ?></b></td>
        <td align="center"><b><?= date("d/m/y",strtotime($files['attach_date']))?></b></td>
       <!--  <td><b><?= $active?></b></td> -->
        <td align="center">
             <input type="button" value="Edit"  onclick="window.open('addAdminFiles.php?action=edit&id=<?= $id?>&ss_id=<?= $files['id']?>','','width=700,height=500')" style="margin-bottom: 10px;">
            
            <input type="button" value="Delete" onclick="deleteAdminFiles(<?= $files['id']?>)">

        </td>
    </tr>
    <?php $count++;}} }?>
   
</table>
<table> <tr style="background-color: rgb(238, 238, 224);" class="alt">
        <td>
              <input type="hidden" name="admin_files_deleted" value="" />
                <center><input type="button" value="Add Admin File/Screenshot" onclick="window.open('addAdminFiles.php?action=add&id=<?= $id?>','','width=700,height=500')" style="margin-top:10px;"/></center>
                <center><h4 style="color:red">This Section is for Admin use only. Images/Other files uploaded here are not visible to customers</h4></center>
        </td>
    </tr></table>


<!-- Section : Manuals: End -->