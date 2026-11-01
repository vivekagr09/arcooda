<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('member'); // memberFromDatabase

include_once __DIR__ . '/header.inc.php';

$invoice = array();
if (0 != $posted['invoice_id']) {
    $invoice                   = invoiceFromDatabase($posted['invoice_id']);
    $invoice['invoice_number'] = invoiceFormatNumber($invoice['invoice_id'], $invoice['revision_id'], $invoice['site_id']);
}
if (0 != $posted['member_id'] && function_exists('memberFromDatabase')) {
    $member                  = memberFromDatabase($posted['member_id']);
    $invoice['bill_company'] = $member['company_name'];
    $invoice['bill_name']    = trim($member['first_name'] . ' ' . $member['last_name']);
}
?>
<script type="text/javascript">
//<![CDATA[
function typeOfCard(number)
{
	var firstNumber=number.substring(0,1);
	var firstThreeNumbers=number.substring(0,3);
	if(firstNumber==4) return "VISA";
	var firstTwoNumbers=number.substring(0,2);
	if(firstTwoNumbers>50 && firstTwoNumbers<56) return "MASTERCARD";
	if(firstTwoNumbers==34 || firstTwoNumbers==37) return "AMEX";
	var firstFourNumbers=number.substring(0,4);
	if(firstFourNumbers==6011) return "DISCOVER";
}

function isValidCreditCard(number)
{
	if(number.length<12) return false;
	var total=0;
	var flag=0;
	for(var i=(number.length-1);i>=0;i--)
	{
		if(flag==1)
		{
			var digits=number.charAt(i)*2;
			if(digits>9) digits-=9;
			total+=digits;
			flag=0;
		}
		else
		{
			total=total+parseInt(number.charAt(i));
			flag=1;
		}
	}
	return ((total%10)==0);
}

function validate(frm)
{
	if(getValue('invoice[bill_company]')=="")
	{
		alert("Please Enter Your Company Name");
		return false;
	}
	if(getValue('invoice[bill_name]')=="")
	{
		alert("Please Enter Your Name");
		return false;
	}
	if(getValue('invoice[invoice_number]')=="")
	{
		alert("Please Enter Your Invoice Reference");
		return false;
	}
	if(getValue('invoice[bill_email]')=="")
	{
		alert("Please Enter Your Email Address");
		return false;
	}
	if(getValue('payment[amount]')=="")
	{
		alert("Please Enter The Amount You Are Paying");
		return false;
	}
	if(getValue('payment[expire_month]')=="" || getValue('payment[expire_year]')=="")
	{
		alert("Please Enter An Expiration Date");
		return false;
	}
	if(!isValidCreditCard(getValue('payment[card_number]')))
		if(!confirm("The card number doesn't appear to be valid.\nContinue anyway?"))
			return false;
	if(getValue('payment[security_code]')=="")
		if(!confirm("You did not enter a security code.\nContinue anyway?"))
			return false;
	return true;
}

function numeric_filter(elem)
{
	var source=elem.value;
	var result='';
	for(var i=0;i<source.length;i++)
		if(source.charAt(i)>='0' && source.charAt(i)<='9')
			result+=source.charAt(i);
	elem.value=result;
}
//]]>
</script>

<h1>Credit Card Payment</h1>

<?=formatHTMLTitle('Payment Details')?>

<form action="payment_process.php" method="post" onsubmit="return validate(this)">
<table class="wide">
	<tr>
		<td class="left_title" style="width:25%;">Company Name:</td>
		<td><input type="text" name="invoice[bill_company]" value="<?=htmlspecialchars($invoice['bill_company'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Your Name:</td>
		<td><input type="text" name="invoice[bill_name]" value="<?=htmlspecialchars($invoice['bill_name'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Reference:</td>
		<td><input type="text" name="invoice[invoice_number]" value="<?=htmlspecialchars($invoice['invoice_number'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Email Address:</td>
		<td><input type="text" name="invoice[bill_email]" value="<?=htmlspecialchars($invoice['bill_email'])?>" /></td>
	</tr>
</table>
<br />
<table class="wide">
	<tr>
		<td class="left_title" style="width:25%;">Payment (AUD):</td>
		<td><input type="text" name="payment[amount]" onchange="if(this.value.substr(0,1)=='$' || this.value.substr(0,1)=='-') this.value=this.value.substr(1);" value="" onchange="numeric_filter(this)" /></td>
	</tr>
	<tr>
		<td class="left_title">Card Number:</td>
		<td><input type="text" name="payment[card_number]" value="" onchange="numeric_filter(this)" /></td>
	</tr>
	<tr>
		<td class="left_title">Expiration (Month/Year):</td>
		<td>
			<input type="text" name="payment[expire_month]" size="2" maxlength="2" value="" onchange="numeric_filter(this)" />
			/
			<input type="text" name="payment[expire_year]" size="2" maxlength="2" value="" onchange="numeric_filter(this)" />
		</td>
	</tr>
	<tr>
		<td class="left_title">Security Code:</td>
		<td><input type="text" name="payment[security_code]" size="4" maxlength="4" value="" onchange="numeric_filter(this)" /></td>
	</tr>
</table>
<br />
<br />

<input type="submit" value="Make Payment" />

</form>
<?php

include_once __DIR__ . '/footer.inc.php';
