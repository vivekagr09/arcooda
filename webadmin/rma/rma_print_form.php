<?php
include_once __DIR__ . '/common.inc.php';

$warranty = $_POST['warranty'] ?? null;

//This is the form for a print_version of an RMA
//echo "Ready to print current RMA. Today is ";

//search statement
$statement = "SELECT * FROM rma_service WHERE id='$id'";

//echo "Id_selected: $id";
$result   = $DB->query($statement);
$rma      = $DB->fetch_array($result);
$customer = $rma['customer'];
$cust_ref = $rma['cust_ref'];
$item     = $rma['item'];
$serial   = $rma['serial'];
$fault    = trim($rma['fault']);
//echo $fault;
$priority = $rma['priority'];
$sent_to  = $rma['sent_to'];
$status   = $rma['status'];
$comments = $rma['comments'];

$user       = $rma['added_by'];
$statement2 = "SELECT `first_name`, `last_name` FROM `highwaygroup`.`users` WHERE `user` = '" . $user . "'";
$result2    = $DB->query_first($statement2);
if (!empty($result2['first_name'])) {
    $user = $result2['first_name'] . ' ' . $result2['last_name'];
}

$today = getdate();
$month = $today['month'];
$mday  = $today['mday'];
$year  = $today['year'];
// echo "$month $mday, $year";

$script = 'rma_print_form.php';

$gets = get_vars();
?>
<html>
<head>
<script type="text/javascript">
//<![CDATA[
function printpage()
{
    if (window.print) {
        window.print();
    } else {
        alert('To print this page, select Print Frame from the file menu.');
    }
}
function doPrint()
{
    if (confirm("Click OK to print this document")) {
        printpage();
    }
}
//]]>
</script>
</head>
<body onload="doPrint();">
<form action="<?=$script . $gets?>" method="post">

<table border="0" align="center" width="100%" cellpadding="4" cellspacing="2">
  <tr align="center">
    <td nowrap colspan="2" align="center"><b><font size="+4" face="Arial">ARCOODA</font><br>
	                        <font face="Times New Romans">
44 Maitland Rd (Downstairs)<br />
Mayfield East, NSW 2304, Australia<br />
Tel: (+61) 02 4968 9313 Fax: (+61) 02 4968 9314<br />
                               Web: <a href="https://www.arcooda.com/">www.arcooda.com</a>, Email: <a href="mailto:sales@arcooda.com">sales@arcooda.com</a></font><br>
	<hr>
	</td>
  </tr>
  <tr>
  	<td align="left"><font size="+2"><b>REPAIR REPORT</b></font></td>
  </tr>
</table>
<br>

<table border="1" align="center" width="100%" cellpadding="4" cellspacing="0">
  <tr>
    <td align="left" height="30" width="50%" ><b>RMA#: <?= $id ?></b></td>
	<td align="right" height="30"><b>Date: <?=date("F d, Y", strtotime($rma['date_added']))?></b></td>
  </tr>
</table>
<br><br>
<?php
if ('on' == $details && 'on' != $warranty) {?>
<table border="1" align="center" width="100%" cellpadding="4" cellspacing="0">
  <tr>
    <td height="30"  align="left"><b>Customer Name: <?=$rma['customer']?></b></td>
	<td height="30" align="left"><b>Inv No: <?=$rma['invoice_no']?></b></td>
  </tr>
  <tr>
    <td height="30"  align="left"><b>Phone: <?=$rma['phone_no']?></b></td>
	<td height="30" align="left"><b>Priority: <?=$rma['priority']?></b></td>
  </tr>
</table>
<br><br>
<?php
}?>
<table border="1" align="center" width="100%" cellpadding="4" cellspacing="0">
  <tr>
    <td height="30"  align="left" width="20%"><b>Name of Item:</b></td>
	<td height="30" align="left"><b><?=$item?>&nbsp;</b></td>
  </tr>
  <tr>
    <td height="30"  align="left"><b>Serial#: </b></td>
	<td height="30" align="left"><b><?=$serial?>&nbsp;</b></td>
  </tr>
  <tr>
    <td height="100"  align="left"><b>Fault: </b></td>
	<td height="100" align="left"><b><?=$fault?>&nbsp;</b></td>
  </tr>
  <tr>
    <td height="30"  align="left"><b>Sent By: </b></td>
	<td height="30" align="left"><b><?=$user?>&nbsp;</b></td>
  </tr>
</table>

<br><br>
<div><font size="+2"><b>TO BE COMPLETED BY <?=strtoupper($sent_to)?><b></font><div>
<br><br>
<table border="1" align="center" width="100%" cellpadding="4" cellspacing="0">
<?php
if ('on' == $handle) {?>
  <tr>
    <td height="200"  align="left" width="20%"><b>Repair Notes:</b></td>
	<td height="200" align="left">&nbsp; </td>
  </tr>
  <tr>
    <td height="30"  align="left"><b>Date Completed: </b></td>
	<td height="30" align="left">&nbsp;</td>
  </tr>
<?php
}?>
  <tr>
    <td height="30"  align="left"><b>Repaired By: </b></td>
	<td height="30" align="left">&nbsp; </td>
  </tr>
  <?php if ('on' == $repair && 'on' != $warranty) {?>
  <tr>
    <td height="30"  align="left"><b>Repair Charges: </b></td>
	<td height="30" align="left">&nbsp; </td>
  </tr>
  <?php }?>
  <?php if ('on' == $reference && 'on' != $warranty) {?>
  <tr>
    <td height="30"  align="left"><b>Reference Invoice #: </b></td>
	<td height="30" align="left">&nbsp;</td>
  </tr>
  <?php }?>
  <?php if ('on' == $warranty) {?>
  <tr>
    <td height="30"  align="left"><b>Comment: </b></td>
	<td height="30" align="left">This is a warranty repair</td>
  </tr>
  <?php }?>

</table>

<?php
if ('on' == $_POST['comments']) {
?>
<br><br>
<table border="1" align="center" width="100%" cellpadding="4" cellspacing="0">
 <tr>
  <td colspan="3" align="center"><b>Comments</b></td>
 </tr>
 <tr>
  <td><b>Date Added</b></td>
  <td><b>User</b></td>
  <td><b>Comments</b></td>
 </tr>
<?php
    //Gets Comments From Database
    if ($data = $DB->query(sprintf(
        "SELECT * FROM `rma_comments` WHERE `rma` = %d",
        $id
    ))) {
        //Displays Them
        while ($row = $DB->fetch_assoc($data)) {
?>

 <tr>
  <td><b><?=date("d-m-Y", strtotime($row["date"]))?></b></td>
  <td><b><?=$row["person"]?></b></td>
  <td><b><?=nl2br($row["comment"])?></b></td>
 </tr>
<?php
        }
        $DB->free_result($data);
    }
?>
</table>
<?php
}
?>
</form>

</body>
</html>