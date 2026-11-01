<script type="text/javascript">
	function  productValidate() {
		var lfckv = document.getElementById("productinvoice").checked;
		if(lfckv){
			  if(!confirm("do you want to create an inquiry, this will make an inquiry and deactivate the wanted listing")) {
			    return false;
			  }
		}
		return true;
	}

</script>
<?php
switch ($action) {
    case 'add':
	    include __DIR__ . "/_wanted_item.tpl.php";
	    include __DIR__ . "/_wanted_customer.tpl.php";
	    include __DIR__ . "/_wanted_online_item.tpl.php";
	    include __DIR__ . "/_wanted_admin_notes.tpl.php";
	    include __DIR__ . "/_wanted_status.tpl.php";
    break;
    case 'edit':
      	include __DIR__ . "/_wanted_summery.tpl.php";
      	include __DIR__ . "/_wanted_item.tpl.php";
	    include __DIR__ . "/_wanted_customer.tpl.php";
	    include __DIR__ . "/_wanted_online_item.tpl.php";
	    include __DIR__ . "/_wanted_seller_offer.tpl.php";
	    include __DIR__ . "/_wanted_admin_notes.tpl.php";
	    include __DIR__ . "/_wanted_status.tpl.php";
	    break;
	default :
	echo 'invalid request';
	break;
}
?>

<center>
	<input type="hidden" name="id" value="<?php echo $wanted['id'] ?>">
	<input type="button" value="<?php echo ($add) ? 'Add' : 'Update' ?>"  onclick="if(productValidate()) this.form.submit();" />
</center>
