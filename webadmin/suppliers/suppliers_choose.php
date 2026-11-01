<?php
return;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Choose a Payment Type</title>
<script>
function show(type)
{
	var elem = document.getElementById('show_select');
	try{
		elem.style.display = 'table-row';
	}
	catch(er){
		elem.style.display = 'inline';
	}
	var title = document.getElementsByName('title')[0];
	switch(type) {
		case '1':
			title.value="(Currency) Bank Payment";
			break;
		case '2':
			title.value="Paypal Payment";
			break;
		case '3':
			title.value="Western Union Payment";
			break;
	}

}

function addPayment()
{
	var opener = window.opener;
	var elem = document.getElementsByName('choose');
	var selected = false;
	var selectedValue = '';
	for(i=0;i<elem.length;i++){
		if(elem[i].checked){
			selected = true;
			selectedValue = elem[i].value;
			break;
		}
	}
	var title = document.getElementsByName('title')[0].value;
	if(!selected)
		alert('Please select a Payment Type');
	else if(title == '')
		alert('Please enter a title');
	else if(title.substring(0,10) == '(Currency)')
		alert('Please enter a currency for the Bank Payment');
	else{
		if(opener.addBank)
			opener.addBank(selectedValue, title);
		else
			alert('Cannot send info');
		window.close();
	}
}
</script>
</head>
<body style="background-color: #F0F4F9;">
<table style="width: 100%;align: left; font-family: Verdana;font-size: 12px;font-weight: normal;" border=0 cellpadding=2>
	<tr><td style="text-align: right; width: 40%"><input onclick="show('1')" type="radio" name="choose" value="b"></td><td style="text-align: left; width: 60%">Bank Payment</td></tr>
	<tr><td style="text-align: right; width: 40%"><input onclick="show('2')" type="radio" name="choose" value="p"></td><td style="text-align: left; width: 60%">PayPal </td></tr>
	<tr><td style="text-align: right; width: 40%"><input onclick="show('3')" type="radio" name="choose" value="w"></td><td style="text-align: left; width: 60%">Western Union </td></tr>

	<tr id="show_select" style="display: none">
		<td colspan=2 style="text-align: left;"><b>Title :</b><input style="width: 80%" type="text" name="title"></td>
	</tr>
	<tr><td colspan=2 align="center"><input type="button" value="Select" onclick="addPayment();"></td></tr>
</table>
</body>
</html>
