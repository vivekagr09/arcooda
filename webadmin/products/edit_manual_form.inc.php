<br /><br />
<script>
function getElement(name) {
	var elem=document.getElementById(name);
	if(elem) return elem;
	elem=document.getElementsByName(name);
	if(elem.length==0) return null;
	return elem[0];
}
function setValue(name,value) {
	var elem=getElement(name);
//	var uni_elem=getElement('unicode_elem');
//	if(uni_elem!=null && uni_elem.innerHTML!=null)
//	{
//		uni_elem.innerHTML=value;
//		value=uni_elem.innerHTML;
//	}
	if(elem==null) return;
	switch(elem.tagName) {
	case "INPUT":
		if(elem.type=='checkbox' && (value==true || value==false))
			elem.checked=value;
		else
			elem.value=value;
		break;
	case "TEXTAREA":
//		if(elem.innerHTML!=null)
//			elem.innerHTML=value;
//		else
			elem.value=value;
	break;
	case "SPAN":		elem.innerHTML=value;	break;
	case "DIV":			elem.innerHTML=value;	break;
	case "SELECT":
		elem.value=value;
		for(var i=0;i<elem.options.length;i++)
			if(elem.options[i].value==value) {
				elem.options[i].selected=true;
				elem.selectedIndex=i;
			} else elem.options[i].selected=false;
		break;
	default:
		if(elem.value) elem.value=value;
		else if(elem.innerHTML) elem.innerHTML=value;
	}
	return;
}
function display_result(id,product_name)
{
	setValue('product_id',id);
	setValue('product_name',product_name);
}
</script>
<?php
if(isset($id))
	$row = $DB->query_first("SELECT * FROM product_attachments pa, products p WHERE p.id=pa.product_id AND attachment_id = '$id'");

?>
<script>
<?php echo (isset($id))?'document.title="Edit Manual";':'document.title="Add Manual";'?>
function search_product()
{
	var obj = getElement('product_id');
	//alert(document.forms[0].site.length);
	var radio_checked = false;
	for(i=0;i<document.forms[0].site.length;i++)
	{
		if(document.forms[0].site[i].checked)
			radio_checked = true;
	}
	if(obj.value == ''){
		alert('Please Enter a Product Name/ID');
	}
	else if(!radio_checked)
		alert('Please select a Site you want to search from');
	else
		window.open('attachment_admin.php?action=product_search&search='+obj.value,'','width=700,height=700,resizable=1,scrollbars=1');
	//toolbar=0, , location=0, statusbar=0, menubar=0, resizable=1, width=450, height=200'
}
function delete_product()
{
	getElement('action').value = 'do_delete';
	if(confirm('Are You Sure you want to delete this Manual for this product'))
		document.forms[0].submit();
}
</script>
<form enctype="multipart/form-data" action="" method="post" name="add_edit_form">
<table border="0" align="center" cellpadding="4" cellspacing="0">
	<tr>
		<td class="left">Product Name/ID</td>
		<td class="right">
			<input type="hidden" name="orig_product_id" value="<?php echo $row['product_id']?>">
			<input type="text" name="product_id" style="width:300px" value="<?php echo $row['product_id']?>">&nbsp;
			<input type="button" value="Search" onclick="javascript:search_product();">&nbsp;
			<input type="radio" name="site" value="coe" checked>COE <input type="radio" name="site" value="hgr" disabled>HGR
		</td>
	</tr>
	<tr>
		<td class="left">&nbsp;</td>
		<td class="right"><input type="text" name="product_name" style="width:300px" value="<?php echo $row['product_name']?>" readonly>
	</tr>
	<tr><td colspan=2>&nbsp;</td></tr>
	<tr>
		<td class="left">Type Of Manual</td>
		<td class="right">
			<select style="width:300px" name="manual_type">
				<option value="">None Selected</option>
<?php
				$array = array('coe'=>'COE Manual','hgr'=>'HG Manual','fm'=>'Factory Manual','wd'=>'Wiring Diagram/Schematics','sm'=>'Service Manual','dss'=>'Dip Switch Settings','oth'=>'Other');
				echo formatHTMLOptions($array,$row['manual_type']);
?>
			</select>
		</td>
	</tr>
	<tr><td colspan=2>&nbsp;</td></tr>
	<tr>
		<td class="left">File</td>
		<td class="right">
			<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
			<input type="file" style="width:383px" name="image_file" /><br />
			<small><b><i><span style="color:red">NB: Max File Size: 10 MB</span></i></b></small>
		</td>
	</tr>
	<tr>
		<td class="left">&nbsp;</td>
		<td class="right"><input type="text" style="width:300px" value="<?php echo $row['file_name']?>" readonly>&nbsp;<?php echo (isset($id)) ? '<a href="viewimage.php?product_id='.$row['product_id'].'&attachment_id='.$id.'" target="_blank">View</a>' : ''?><br />
		<small><b><i><span style="color:red">NB: Uploading new manual will overwrite existing one</span></i></b></small>
	</tr>
	<tr><td colspan=2>&nbsp;</td></tr>
	<tr>
		<td class="left">Notes</td>
		<td class="right"><input type="text" value="<?php echo $row['description']?>" style="width:300px" name="image_desc"></td>
	</tr>
	<tr><td colspan=2>&nbsp;</td></tr>
	<tr>
		<td nowrap>&nbsp;</td>
		<td>
			<p>
			<?php if($id != ''){?>
				<input type="hidden" name="attachment_id" value="<?php echo $id ?>">
				<input type="hidden" name="action" value="do_edit">
				<input type="submit" name="edit" value="Update">
				<input type="button" name="delete" value="Delete" onclick="javascript:delete_product()">
				<input type="reset" name="clear" value="Clear">
			<?php }
			else{
?>
			<input type="hidden" name="action" value="do_add">
			<input type="submit" name="add" value="Add">
			<input type="reset" name="clear" value="Clear">
			<?php }?>
			</p>
		</td>
	</tr>
</table>
<?php if($action == 'add' && $prod_id != "")
{
?>
<script>
	display_result('<?php echo $prod_id?>','<?php echo $prod_name?>');
</script>
<?php
}
?>