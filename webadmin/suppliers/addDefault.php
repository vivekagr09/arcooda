<?php
return;

include_once('suppliers_common.php');

moduleLoad('invoice'); //invoiceGetOptionList

$company=array(1=>'Highway Games', 2=>'Highway Entertainment', 9=>'Converters.tv', 11=>'Highway Group', 16=>'Car Converters');
//$condition=array(1=>'Brand New', 2=>'Used');
$condition=invoiceGetOptionList('condition');
?>
<html>
<head>
	<title>Add Supplier Default Values</title>
	<link rel="stylesheet" type="text/css" href="style.css" />
	<script type="text/javascript" src="/webadmin/jscript/common.php.js" defer="defer"></script>
	<script>
	function checkSection()
	{
		getElement('section_row').style.display=(getValue('company')==2)?'':'none';
	}
	function submit()
	{
		var company=getValue('company');
		var section=getValue('section');
		if(section==null) section=0;
		var condition=getValue('condition');
		if(company==0)
		{
			alert('Please Select a Company');
			return false;
		}
		if(company == 2 && section==0)
		{
			alert('Please select a select a Section');
			return false;
		}
		if(condition==0)
		{
			alert('Please select a Condition');
			return false;
		}
		if(window.opener != null && window.opener.addSupplierValues != null)
			window.opener.addSupplierValues(company, section, condition);
		else
			alert('Unable to send data back.');
		window.close();
	}
	</script>
</head>
<body>
	<div class="supplier_content" style="width:100%;">
	<table class="wide" style="width:98%;text-align: center" align="center" cellpadding=0 cellspacing=0>
		<tr>
			<td class="left_title" style="width:30%;">Company :</td>
			<td><select name="company" onchange="checkSection()"><option value="0"></option><?=formatHTMLOptions($company)?></select>
		</tr>
		<tr id="section_row" style="display:none">
			<td class="left_title" style="width:30%;">Section :</td>
			<td><select name="section"><option value="0"></option><?=formatHTMLOptions(getProductSections())?></select>
		</tr>
		<tr>
			<td class="left_title" style="width:30%;">Condition :</td>
			<td><select name="condition"><option value="0"></option><?=formatHTMLOptions($condition)?></select>
		</tr>
	</table>
	<center><input type="button" value=" Add Default Values" onclick="submit();" /></center>
	</div>
</body>





