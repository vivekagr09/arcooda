<?php
global $_GLOBALS;
?>
<html>
 <head>
  <title>Printable Invoice</title>
  <style type="text/css">
   .left{border-left-width: 0px;}
   .right{border-right-width: 0px;}
   .bottom{border-bottom-width: 0px;}
   .top{border-top-width: 0px;}
   td {font-size:12px;font-family:arial;background-color:white;}
  </style>
 </head>
 <body>
<!--  <p align="center" style="color:red">NOTICE: Repairs are currently being done from off site. This may cause errors on this page and this page alone. Please report any errors outside of this page to Nathan Tomkins via ICQ</p> -->

  <table width="100%">
   <tr>
    <td width="20"></td>
    <td width="*">
     <font style="font-size:42px"><?=htmlspecialchars($_GLOBALS['site_settings']['site_name'])?></font><br />
44 Maitland Rd (Downstairs)<br />
Mayfield East, NSW 2304<br />
Australia
    </td>
    <td width="250" align="left">
     <h3 align="right">PURCHASE ORDER</h2>
     <table align="right" width="200" height="50" border="0" bgcolor="black" cellspacing="1" cellpadding="1">
      <tr>
       <td align="center">PURCHASE ORDER #:</td>
       <td align="center">DATE</td>
      </tr>
      <tr>
       <td align="center">PO#<?= sprintf("%06d", $porder_id); ?></td>
       <td align="center"><?= date("d/m/Y", $date); ?></td>
      </tr>
     </table>
    </td>
   </tr>
  </table>

  <br><br>

  <table>
   <tr>
    <td width="20"></td>
    <td width="100%">
     <table width="100%" border="0" bgcolor="black" cellspacing="1" cellpadding="1">
      <tr>
       <td align="left" width="50%">PURCHASE FROM:</td>
       <td align="left" width="50%">SHIP TO:</td>
      </tr>
      <tr>
       <td align="center">
        <?= $supplier['supplier_name'] ?><br />
	<?= nl2br($supplier['address']) ?><br />
	<?= $supplier['c_name'] ?><br />
	<br />
	Ph: <?= $supplier['phone'] ?><br />
	email: <?= $contact['contact_email'] ?>
       </td>
       <td align="center">
        <?= $dest['name'] ?><br /><br />
	<?= nl2br($dest['address']) ?>
       </td>
      </tr>
     </table>
    </td>
   <tr>
  </table>

  <br><br>

  <table width="100%" bgcolor="black" cellpadding="1" cellspacing="1" border="0">
   <tr>
    <td align="center">SUPPLIER CONTACT</td>
    <td align="center">PAYMENT TYPE</td>
    <td align="center">SHIP VIA</td>
    <td align="center">ACCOUNT #</td>
    <td align="center">ORDERED BY</td>
   </tr>
   <tr>
    <td align="center" height="40"><?= $contact['contact_name'] //Get User Name ?></td>
    <td align="center"><?= $payment_type //Get Payment Type ?></td>
    <td align="center"><?= $courier['courier_name'] //Get Courier Info ?></td>
    <td align="center"><?= $courier['account_no'] //Get Account Number ?></td>
    <td align="center"><?= $orderer['first_name'] . " " . $orderer['last_name'] //Get Ordered By ?></td>
   </tr>
  </table>

  <br><br>

<?php
// Order Summary Table ?>
  <table width="100%" height="250" border="1" bordercolor="black" cellpadding="0" cellspacing="0" bgcolor="black">
   <tr>
    <td width="10%" height="20" valign="top" align="center" style="border-top-width:0px; border-bottom-width:1px; border-left-width:0px;">ITEM ID</td>
    <td width="60%" height="20" valign="top" align="center" style="border-top-width:0px; border-bottom-width:1px; border-left-width:0px;">DESCRIPTION</td>
    <td width="10%" height="20" valign="top" align="center" style="border-top-width:0px; border-bottom-width:1px; border-left-width:0px;">QTY</td>
    <td width="10%" height="20" valign="top" align="center" style="border-top-width:0px; border-bottom-width:1px; border-left-width:0px;">PRICE EACH</td>
    <td width="10%" height="20" valign="top" align="center" style="border-top-width:0px; border-bottom-width:1px; border-left-width:0px; border-right-width:0px;">AMOUNT</td>
   </tr>

<?php
foreach($order_products as $p): // FOREACH over products ?>
   <tr>
    <td width="10%" valign="top" align="center" height="10" style="border-top-width:0px; border-bottom-width:0px; border-left-width:0px; border-right-width:1px;"><?= $p['id'] ? $p['id'] : "N/A" ?></td>
    <td width="60%" valign="top" align="left" style="border-top-width:0px; border-bottom-width:0px; border-left-width:0px; border-right-width:1px;"><?= htmlspecialchars($p['name']) ?></td>
    <td width="10%" valign="top" align="center" style="border-top-width:0px; border-bottom-width:0px; border-left-width:0px; border-right-width:1px;"><?= $p['qty'] ?></td>
    <td width="10%" valign="top" align="center" style="border-top-width:0px; border-bottom-width:0px; border-left-width:0px; border-right-width:1px;">$<?= sprintf("%01.2f", $p['price']) ?></td>
    <td width="10%" valign="top" align="center" style="border-top-width:0px; border-bottom-width:0px; border-left-width:0px; border-right-width:0px;">$<?php
$amount = sprintf("%01.2f",$p['qty'] * $p['price']); echo $amount; ?></td>
   </tr>
<?php
$subtotal += $amount; // Track Total Costs
endforeach; ?>

<?php
// Shipping (Courier) Costs ?>
   <tr>
    <td width="10%" valign="top" height="10" style="border-top-width:0px; border-bottom-width:0px; border-left-width:0px; border-right-width:1px;">&nbsp;</td>
    <td width="60%" valign="top" align="left" style="border-top-width:0px; border-bottom-width:0px; border-left-width:0px; border-right-width:1px;">Freight - <?= $courier['courier_name'] ?></td>
    <td width="10%" valign="top" align="center" style="border-top-width:0px; border-bottom-width:0px; border-left-width:0px; border-right-width:1px;">&nbsp;</td>
    <td width="10%" valign="top" align="center" style="border-top-width:0px; border-bottom-width:0px; border-left-width:0px; border-right-width:1px;">&nbsp;</td>
    <td width="10%" valign="top" align="center" style="border-top-width:0px; border-bottom-width:0px; border-left-width:0px; border-right-width:0px;">$<?= sprintf("%0.2f", $shipping_charge) ?></td>
   </tr>
<?php
$subtotal += $shipping_charge;?>

<?php
//TAX? ?>
<?php
if(isset($tax)): ?>
   <tr>
    <td width="10%" valign="top" height="10" style="border-top-width:0px; border-bottom-width:0px; border-left-width:0px; border-right-width:1px;">&nbsp;</td>
    <td width="60%" valign="top" align="left" style="border-top-width:0px; border-bottom-width:0px; border-left-width:0px; border-right-width:1px;">Tax</td>
    <td width="10%" valign="top" align="center" style="border-top-width:0px; border-bottom-width:0px; border-left-width:0px; border-right-width:1px;">&nbsp;</td>
    <td width="10%" valign="top" align="center" style="border-top-width:0px; border-bottom-width:0px; border-left-width:0px; border-right-width:1px;">&nbsp;</td>
    <td width="10%" valign="top" align="center" style="border-top-width:0px; border-bottom-width:0px; border-left-width:0px; border-right-width:0px;">$<?=$tax;?></td>
   </tr>
<?php
$subtotal += $quote['tax']; // Add TAX to total cost ?>
<?php
endif; ?>

   <tr>
    <td style="border-width:0;border-right-width:1px;" height="*">&nbsp;</td>
    <td style="border-width:0;border-right-width:1px;">&nbsp;</td>
    <td style="border-width:0;border-right-width:1px;">&nbsp;</td>
    <td style="border-width:0;border-right-width:1px;">&nbsp;</td>
    <td style="border-width:0;">&nbsp;</td>
   </tr>
   <tr>
    <td valign="top" align="left" rowspan="2" colspan="3" style="border-width:0;border-right-width:1px; border-top-width:1px;">
     &nbsp;&nbsp;*COMMENTS/NOTES: <br />
    <?= nl2br(stripslashes($order_notes)); ?>
    </td>
    <td height="40" align="center" style="border-width:0;border-right-width:1px; border-top-width:1px;">Total</td>
    <td height="40" align="center" style="border-width:0; border-top-width:1px;">$<?php
printf("%01.2f",$subtotal) ?></td>
   </tr>
   <tr>
    <td height="40" align="center" style="border-width:0;border-right-width:1px; border-top-width:1px;">Currency</td>
    <td height="40" align="center" style="border-width:0;border-right-width:0px; border-top-width:1px;"><?=$order_currency?></td>
   </tr>
  </table>
  <table width="100%">
   <tr>
    <td width="40"></td>
	<td>
	</td>
	<td>
	 <table width="100%" align="right" height="100">
	  <tr><td height="0" align="right"><b>FOR <?=htmlspecialchars(strtoupper($_GLOBALS['site_settings']['site_name']))?></b></td></tr>
	  <tr><td height="100%"></td></tr>
	  <tr><td height="0" align="right">
	    ................................................................................
	    <br>
		<b>Authorised Signatory</b>
	   </td></tr>
	  </tr>
	 </table>
	</td>
   </tr>
  </table>
 </body>
</html>
