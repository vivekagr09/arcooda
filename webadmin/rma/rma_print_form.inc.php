<?php
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
$user     = "";

$today = getdate();
$month = $today['month'];
$mday  = $today['mday'];
$year  = $today['year'];
// echo "$month $mday, $year";

$script = 'rma_print_form.php';

$gets = get_vars();

?>

<form action="<?=$script . $gets?>" method="post">

<br /><br />
<table border="0" align="center" width="80%">
  <tr align="center">
    <td height="88" nowrap colspan="2" align="center"><b><font size="+2">COINOPEXPRESS Repair Report</font></b></td>
  </tr>
  <tr>
    <td height="48" nowrap><div align="left"><b>RMA#: <U><?= $id ?></U></b></div></td>
	<td height="48" nowrap><div align="right"><b>Date: <U><?= "$month $mday, $year" ?></U></b></div></td>
  </tr>
  <tr>
    <td height="48" colspan="2" align="left"><b>Name of Item: <U><?=$item?></U></b></td>
  </tr>
  <tr>
    <td height="48" colspan="2" align="left"><b>Serial#: <U><?=$serial?></U></b></td>
  </tr>
  <tr>
    <td height="48" colspan="2" align="left"><b>Fault: <U><?=$fault?></U></b></td>
  </tr>
  <tr>
    <td height="48" colspan="2" align="left"><b>Sent By: <U><?=$user?></U></b></td>
  </tr>
</table>
<br><br><br>