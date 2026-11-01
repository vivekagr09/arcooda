<script src="functions/trcloner.js" type="text/javascript"></script>
<script src="functions/copy_address.js" type="text/javascript"></script>
<script src="functions/delete_contact2.js" type="text/javascript"></script>
<script>
function testpost(theFrm) {
	var oldact=theFrm.action;
	var oldtrg=theFrm.target;
	theFrm.action="developer/testpost.php";
	theFrm.target="_blank";
	theFrm.submit();
	theFrm.action=oldact;
	theFrm.target=oldtrg;
}
</script>
<?php
// Product Type Image Array -- UGLY HACK.
$pt = array(0 => "/webadmin/images/section/other.jpg",
			1 => "/webadmin/images/section/parts.jpg",
			2 => "/webadmin/images/section/machines.jpg",
			3 => "/webadmin/images/section/pcbs.jpg",
			4 => "/webadmin/images/section/merchandises.jpg")
?>

<?php
//Sets the items $id value if there is none
	if(!$_REQUEST['id'])
		$id = 0;
	else
		$id = $_REQUEST['id'];
?>
<style type="text/css">
.in
{
	width:300px;
}
.in2
{
	width:100%;
}
.qt_bk
{
	padding:16px 16px 16px 16px;
	border:1px solid #DDDDDD;
}

</style>
<div id="content">
	<!--<h2><a href="popup_help.html" target="_BLANK">HELP</a></h2>-->
	<form accept-charset="utf-8" autocomplete="off" method="post" id="test" action="edit_forwarder.php">
		<input type="hidden" name="action" value="do_<?= $add?"add":"edit" ?>" />

<!--_________________________Freight Forwarder Office Details____________________-->
		<div class="flagHeader"><span>Freight Forwarder Office Details</span></div>
		<br />
		<table class="table">
			<tr>
				<td class="left_title">Company Name :</td>
				<td class="in"><input type="text" class="in" name="forwarder[name]" value="<?= $forwarder_details['name']; ?>"></td>
				<td class="left_title">Phone :</td>
				<td class="in"><input type="text" class="in" name="forwarder[phone]" value="<?= $forwarder_details['phone']; ?>"></td>
			</tr>
			<tr>
				<td class="left_title">Address :</td>
				<td class="in"><input type="text" class="in" name="forwarder[address]" value="<?= $forwarder_details['address']; ?>"></td>
				<td class="left_title">Fax :</td>
				<td class="in"><input type="text" class="in" name="forwarder[fax]" value="<?= $forwarder_details['fax']; ?>"></td>
			</tr>
			<tr>
				<td class="left_title">&nbsp;</td>
				<td class="in"><input type="text" class="in" name="forwarder[address2]" value="<?= $forwarder_details['address2']; ?>"></td>
				<td class="left_title">Mobile :</td>
				<td class="in"><input type="text" class="in" name="forwarder[mobile]" value="<?= $forwarder_details['mobile']; ?>"></td>
			</tr>
			<tr>
				<td class="left_title">City :</td>
				<td class="in"><input type="text" class="in" name="forwarder[city]" value="<?= $forwarder_details['city']; ?>"></td>
				<td class="left_title">&nbsp;</td><td class="in">&nbsp;</td>
			</tr>
			<tr>
				<td class="left_title">State :</td>
				<td class="in"><input type="text" class="in" name="forwarder[state]" value="<?= $forwarder_details['state']; ?>"></td>
				<td class="left_title">&nbsp;</td><td class="in">&nbsp;</td>
			</tr>
			<tr>
				<td class="left_title">Post Code :</td>
				<td class="in"><input type="text" class="in" name="forwarder[zip]" value="<?= $forwarder_details['zip']; ?>"></td>
				<td class="left_title">Email :</td>
				<td class="in"><input type="text" class="in" name="forwarder[email]" value="<?= htmlspecialchars($forwarder_details['email']) ?>"></td>
			</tr>
				<td class="left_title">Country :</td>
				<td>
					<select name="forwarder[country]" class="in">
<?php
foreach($countries as $c): ?>
						<option value="<?= trim($c['code']); ?>"<?= ($c['code'] == $forwarder_details['country'])?" selected=\"selected\"":""; ?>><?= trim($c['name']); ?></option><?= "\r\n"; ?>
<?php
endforeach ?>
					</select>
				</td>
				<td class="left_title">Website :</td>
				<td class="in"><input type="text" class="in" name="forwarder[website]" value="<?= $forwarder_details['website']; ?>"></td>
			</tr>
		</table>
		<br />
		<br />

<!--________________________Freight Forwarder Warehouse Details________________________-->

		<div class="flagHeader"><span>Freight Forwarder Warehouse Details</span>
		<small><i><a style="color: blue" href="#" onclick="javascript:
		document.getElementsByName('warehouse[1][name]')[0].value = document.getElementsByName('forwarder[name]')[0].value;
		document.getElementsByName('warehouse[1][address]')[0].value = document.getElementsByName('forwarder[address]')[0].value;
		document.getElementsByName('warehouse[1][address2]')[0].value = document.getElementsByName('forwarder[address2]')[0].value;
		document.getElementsByName('warehouse[1][city]')[0].value = document.getElementsByName('forwarder[city]')[0].value;
		document.getElementsByName('warehouse[1][state]')[0].value = document.getElementsByName('forwarder[state]')[0].value;
		document.getElementsByName('warehouse[1][zip]')[0].value = document.getElementsByName('forwarder[zip]')[0].value;
		document.getElementsByName('warehouse[1][country]')[0].value = document.getElementsByName('forwarder[country]')[0].value;
		document.getElementsByName('warehouse[1][phone]')[0].value = document.getElementsByName('forwarder[phone]')[0].value;
		document.getElementsByName('warehouse[1][fax]')[0].value = document.getElementsByName('forwarder[fax]')[0].value;
		document.getElementsByName('warehouse[1][mobile]')[0].value = document.getElementsByName('forwarder[mobile]')[0].value;
		document.getElementsByName('warehouse[1][email]')[0].value = document.getElementsByName('forwarder[email]')[0].value;
		document.getElementsByName('warehouse[1][website]')[0].value = document.getElementsByName('forwarder[website]')[0].value;
		return false;">Copy from Above</a></small> |
		<small><a style="color:blue" href="#" onclick="javascript: cloneRow('warehouse_row'); return false;">Add New Address</a></small></i>
		</div>

		<br />

		<table id="warehouses">
		<tr class="hidden" id="warehouse_row">
			<td>
			<table id="warehouse_table" class="table">
				<tr>
					<td class="left_title">Company Name :</td>
					<td class="in"><input type="text" class="in" name="warehouse[0][name]" value=""><input type="hidden" name="warehouse[0][id]" value="0"></td>
					<td class="left_title">Phone :</td>
					<td class="in"><input type="text" class="in" name="warehouse[0][phone]" value=""></td>
				</tr>
				<tr>
					<td class="left_title">Address :</td>
					<td class="in"><input type="text" class="intext" name="warehouse[0][address]" value=""></td>
					<td class="left_title">Fax :</td>
					<td class="in"><input type="text" class="in" name="warehouse[0][fax]" value=""></td>
				</tr>
				<tr>
					<td class="left_title">&nbsp;</td>
					<td class="in"><input type="text" class="in" name="warehouse[0][address2]" value=""></td>
					<td class="left_title">Mobile :</td>
					<td class="in"><input type="text" class="in" name="warehouse[0][mobile]" value=""></td>
				</tr>
				<tr>
					<td class="left_title">City :</td>
					<td class="in"><input type="text" class="in" name="warehouse[0][city]" value=""></td>
					<td class="left_title">&nbsp;</td><td class="in">&nbsp;</td>
				</tr>
				<tr>
					<td class="left_title">State :</td>
					<td class="in"><input type="text" class="in" name="warehouse[0][state]" value=""></td>
					<td class="left_title">&nbsp;</td><td class="in">&nbsp;</td>
				</tr>
				<tr>
					<td class="left_title">Post Code :</td>
					<td class="in"><input type="text" class="in" name="warehouse[0][zip]" value=""></td>
					<td class="left_title">Email :</td>
					<td class="in"><input type="text" class="in" name="warehouse[0][email]" value=""></td>
				</tr>
				<tr>
					<td class="left_title">Country :</td>
					<td>
						<select name="warehouse[0][country]" class="in">
<?php
foreach($countries as $c): ?>
							<option value="<?= trim($c['code']); ?>"<?= ($c['code'] == $warehouse[0]['country'])?" selected=\"selected\"":""; ?>><?= trim($c['name']); ?></option><?= "\r\n"; ?>
<?php
endforeach ?>
						</select>
					</td>
					<td class="left_title">Website :</td>
					<td class="in"><input type="text" class="in" name="warehouse[0][website]" value=""></td>
				</tr>
			</table>
			</td>
			</tr>

<?php
//If there is another warehouse, fill in the new warehouse
				if(is_array($warehouse) && count($warehouse)>0)
				{
					$counter = 1;
					while($counter <= count($warehouse))
					{
					//Insert code to output contact cells correctly here
?>
							<tr id="warehouse_row_<?= $counter; ?>">
							<td>
							<table id="warehouse_table_<?= $counter; ?>" class="table">
								<tr>
									<td class="left_title">Company Name :</td>
									<td class="in"><input type="text" class="in" name="warehouse[<?= $counter; ?>][name]" value="<?= $warehouse[$counter]['name']; ?>">
										<input type="hidden" name="warehouse[<?= $counter; ?>][id]" value="<?= $warehouse[$counter]['id']; ?>">
									</td>
									<td class="left_title">Phone :</td>
									<td class="in"><input type="text" class="in" name="warehouse[<?=$counter?>][phone]" value="<?= $warehouse[$counter]['phone']; ?>"></td>
								</tr>
								<tr>
									<td class="left_title">Address :</td>
									<td class="in">
										<input type="text" class="in" name="warehouse[<?= $counter; ?>][address]" value="<?= $warehouse[$counter]['address']; ?>">
									</td>
									<td class="left_title">Fax :</td>
									<td class="in"><input type="text" class="in" name="warehouse[<?=$counter?>][fax]" value="<?= $warehouse[$counter]['fax']; ?>"></td>
								</tr>
								<tr>
									<td class="left_title">&nbsp;</td>
									<td class="in">
										<input type="text" class="in" name="warehouse[<?= $counter; ?>][address2]" value="<?= $warehouse[$counter]['address2']; ?>">
									</td>
									<td class="left_title">Mobile :</td>
									<td class="in"><input type="text" class="in" name="warehouse[<?=$counter?>][mobile]" value="<?=$warehouse[$counter]['mobile']?>"></td>
								</tr>
								<tr>
									<td class="left_title">City :</td>
									<td class="in"><input type="text" class="in" name="warehouse[<?=$counter?>][city]" value="<?=$warehouse[$counter]['city']?>"></td>
									<td class="left_title">&nbsp;</td><td class="in">&nbsp;</td>
								</tr>
								<tr>
									<td class="left_title">State :</td>
									<td class="in"><input type="text" class="in" name="warehouse[<?=$counter?>][state]" value="<?=$warehouse[$counter]['state']?>"></td>
									<td class="left_title">&nbsp;</td><td class="in">&nbsp;</td>
								</tr>
								<tr>
									<td class="left_title">Post Code :</td>
									<td class="in"><input type="text" class="in" name="warehouse[<?=$counter?>][zip]" value="<?=$warehouse[$counter]['zip']?>"></td>
									<td class="left_title">Email :</td>
									<td class="in"><input type="text" class="in" name="warehouse[<?=$counter?>][email]" value="<?=$warehouse[$counter]['email']?>"></td>
								</tr>
								<tr>
									<td class="left_title">Country :</td>
									<td class="in">
										<select name="warehouse[<?= $counter; ?>][country]" class="in">
<?php
foreach($countries as $c): ?>
											<option value="<?= trim($c['code']); ?>"<?= ($c['code'] == $warehouse[$counter]['country'])?" selected=\"selected\"":""; ?>><?= trim($c['name']); ?></option><?= "\r\n"; ?>
<?php
endforeach ?>
										</select>
									</td>
									<td class="left_title">Website :</td>
									<td class="in"><input type="text" class="in" name="warehouse[<?=$counter?>][website]" value="<?=$warehouse[$counter]['website']?>"></td>
								</tr>
							</table>
							</td>
							</tr>

<?php
					$counter++;
					}
				}
				else
				{ //Default showing an empty warehouse
?>
						<tr id="warehouse_row_1">
							<td>
								<table id="warehouse_table" class="table">
									<tr>
										<td class="left_title">Company Name :</td>
										<td class="in"><input type="text" class="in" name="warehouse[1][name]" value="<?= $warehouse[$counter]['name']; ?>">
											<input type="hidden" name="warehouse[1][id]" value="<?= $warehouse[$counter]['id']; ?>">
										</td>
										<td class="left_title">Phone :</td>
										<td class="in"><input type="text" class="in" name="warehouse[1][phone]" value="<?= $warehouse[$counter]['phone']; ?>"></td>
									</tr>
									<tr>
										<td class="left_title">Address :</td>
										<td class="in">
											<input type="text" class="in" name="warehouse[1][address]" value="<?= $warehouse[$counter]['address']; ?>">
										</td>
										<td class="left_title">Fax :</td>
										<td class="in"><input type="text" class="in" name="warehouse[1][fax]" value="<?= $warehouse[$counter]['fax']; ?>"></td>
									</tr>
									<tr>
										<td class="left_title">&nbsp;</td>
										<td class="in">
											<input type="text" class="in" name="warehouse[1][address2]" value="<?= $warehouse[$counter]['address2']; ?>">
										</td>
										<td class="left_title">Mobile :</td>
										<td class="in"><input type="text" class="in" name="warehouse[1][mobile]" value="<?=$warehouse[$counter]['mobile']?>"></td>
									</tr>
									<tr>
										<td class="left_title">City :</td>
										<td class="in"><input type="text" class="in" name="warehouse[1][city]" value="<?=$warehouse[$counter]['city']?>"></td>
										<td class="left_title">&nbsp;</td><td class="in">&nbsp;</td>
									</tr>
									<tr>
										<td class="left_title">State :</td>
										<td class="in"><input type="text" class="in" name="warehouse[1][state]" value="<?=$warehouse[$counter]['state']?>"></td>
										<td class="left_title">&nbsp;</td><td class="in">&nbsp;</td>
									</tr>
									<tr>
										<td class="left_title">Post Code :</td>
										<td class="in"><input type="text" class="in" name="warehouse[1][zip]" value="<?=$warehouse[$counter]['zip']?>"></td>
										<td class="left_title">Email :</td>
										<td class="in"><input type="text" class="in" name="warehouse[1][email]" value="<?=$warehouse[$counter]['email']?>"></td>
									</tr>
									<tr>
										<td class="left_title">Country :</td>
										<td class="in">
											<select name="warehouse[1][country]" class="in">
<?php
foreach($countries as $c): ?>
												<option value="<?= trim($c['code']); ?>"<?= ($c['code'] == $warehouse[0]['country'])?" selected=\"selected\"":""; ?>><?= trim($c['name']); ?></option><?= "\r\n"; ?>
<?php
endforeach ?>
											</select>
										</td>
										<td class="left_title">Website :</td>
										<td class="in"><input type="text" class="in" name="warehouse[1][website]" value="<?=$warehouse[$counter]['website']?>"></td>
									</tr>
								</table>
							</td>
						</tr>
<?php
				}?>

		</table>
		<br />
		<br />

<!--________________________________Contacts Section___________________________________________-->
		<div class="flagHeader"><span>Contact People for this Forwarder</span>&nbsp;<i><small><a style="color: blue" href="#" onclick="javascript: cloneRow('contact_row'); return false;">Add New Contact</a></small></i>
		<!--<a href="#" onclick="javascript: cloneRow('contact_row'); return false;">Add New Contact</a>-->
		</div>
		<br />
		<!--<table class="wide vbox">-->
		<table id="contacts">
			<tr id="contact_row" class="hidden">
				<td>
					<table class="table">
						<tr>
							<td class="left_title">Contact Name :</td>
							<td>
								<input type="text" class="in2" name="contact[0][name]" value=""/>
								<input type="hidden" name="contact[0][id]" value="0" />
							</td>
							<td class="left_title" rowspan="3" style="text-align:center;"><center>
								<a href = "#" onclick="javascript: delContact(0); return false;">Delete</a>
							</center></td>
						</tr>
						<tr>
							<td class="left_title">Position :</td>
							<td><input type="text" class="in2" name="contact[0][title]" value=""/></td>
							<td class="left_title">&nbsp;</td>
						</tr>
						<tr>
							<td class="left_title">Email Address :</td>
							<td><input type="text" class="in2" name="contact[0][email]" value="" /></td>
							<td class="left_title">&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
<?php
//If there is another contact, fill in the new contact
if(is_array($contact) && count($contact)>0)
{
	$counter = 1;
	while($counter <= count($contact))
	{
		//Insert code to output contact cells correctly here
?>
			<tr id="contact_row_<?= $counter; ?>">
				<td>
					<table class="table">
						<tr>
							<td class="left_title">Contact Name :</td>
							<td>
								<input type="text" class="in2" name="contact[<?= $counter; ?>][name]" value="<?= $contact[$counter]['name']; ?>" />
								<input type="hidden" name="contact[<?= $counter; ?>][id]" value="<?= $contact[$counter]['id']; ?>" />
							</td>
							<td class="left_title" rowspan="3" style="text-align:center;">
								<center><a href="#" onclick="javascript:delContact(<?= $counter ?>,<?= $id ?>,<?= $contact[$counter]['id'] ?>);return false;">Delete</a></center>
							</td>
							<td class="left_title">&nbsp;</td>
						</tr>
						<tr>
							<td class="left_title">Position :</td>
							<td><input type="text" class="in2" name="contact[<?= $counter; ?>][title]" value="<?= $contact[$counter]['title'];?>" /></td>
							<td class="left_title">&nbsp;</td>
						</tr>
						<tr>
							<td class="left_title">Email Address :</td>
							<td><input type="text" class="in2" name="contact[<?= $counter; ?>][email]" value="<?= htmlspecialchars($contact[$counter]['email']);?>" /></td>
							<td class="left_title">&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
<?php
		$counter++;
	}
}
else
{
	if($add)
	{
?>
			<tr id="contact_row_1" > <!-- class=\"alt\"" : ""; ? -->
				<td>
					<table class="table">
						<tr>
							<td class="left_title">Contact Name :</td>
							<td>
								<input type="text" class="in2" name="contact[0][name]" value=""/>
								<input type="hidden" name="contact[0][id]" value="0"/>
							</td>
							<td class="left_title" rowspan="3" style="text-align:center;"><center>
								<a href = "#" onclick="javascript: delContact(1,0,0); return false;">Delete</a>
							</center></td>
							<td class="left_title">&nbsp;</td>
						</tr>
						<tr>
							<td class="left_title">Position :</td>
							<td><input type="text" class="in2" name="contact[0][title]" value=""/></td>
							<td class="left_title">&nbsp;</td>
						</tr>
						<tr>
							<td class="left_title">Email Address :</td>
							<td><input type="text" class="in2" name="contact[0][email]" value="" /></td>
							<td class="left_title">&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
<?php
	}
}
?>
		</table>
		<br />
		<br />

<!--_________________________________End Contacts Section______________________________-->
<!--________________________________Bank Accounts___________________________________________-->
		<div class="flagHeader"><span>Bank Accounts</span>&nbsp;<i><small><a style="color: blue" href="#" onclick="javascript: cloneRow('bank_row'); return false;">Add New Account Details</a></small></i></div>
		<br />

		<table>
			<tr id="bank_row" class="hidden">
				<td colspan=2><input type="hidden" name="bank[0][id]" value="0"><textarea name="bank[0][details]" rows=10 cols=15 style="width:100%"></textarea></td>
			</tr>
<?php
if(is_array($bank) && count($bank)>0)
{
	$counter=1;
	while($counter<=count($bank))
	{
?>
			<tr id="bank_row_<?= $counter; ?>">
				<td colspan=2><input type="hidden" name="bank[<?=$counter?>][id]" value="<?=$bank[$counter]['id']?>"><textarea name="bank[<?=$counter?>][details]" rows=10 cols=15 style="width:100%"><?=$bank[$counter]['details']?></textarea></td>
			</tr>
<?php
		$counter++;
	}
}
else
{
	if($add)
	{
?>
			<tr id="bank_row_1">
				<td colspan=2><input type="hidden" name="bank[1][id]" value="0" /><textarea name="bank[1][details]" rows=10 cols=15 style="width:100%"></textarea></td>
			</tr>
<?php
	}
}
?>
		</table>
		<br />
		<br />
<!--_________________________________End Bank Section______________________________-->
<!-- ________________________________Quotes Section ___________________________________-->
<?php
if(is_array($quote) && count($quote)>0)
{
?>
		<div class="flagHeader"><span>Quotes with this Forwarder</span><!--style="background-color:yellow;"-->
		<!--<a href="#" onclick="javascript: var rID = cloneRow('quote_row'); rID = 'quote[' + rID + '][date]'; document.getElementsByName(rID)[0].value = '<?= date('d-m-Y'); ?>'; return false;">Add New Quote</a>-->
		</div>
		<br />
		<!--<table class="wide vbox">-->
		<table cellpadding="3" cellspacing="0" class="table">
			<tr>
				<th width="15%">Date</th>
				<th width="21%">Customer Name</th>
				<th width="6%">Qt#</th>
				<th width="3%">Sea/Air</th>
				<th width="25%">Details</th>
				<th width="10%">Price</th>
				<th width="20%">Status</th>
				<!--<th>&nbsp;</th>-->
			</tr>
			<!--<tr id="quote_row" class="hidden">
				<td>
					<input type="text" readonly="readonly" class="intext" name="quote[0][date]" value="" />
					<input type="hidden" name="quote[0][id]" value="0" />
				</td>
				<td><input type="text" readonly="readonly" class="intext" name="quote[0][customer_name]" value="" /></td>
				<td><input type="text" readonly="readonly" class="intext" name="quote[0][qt_no]" value="" /></td>
				<td><input type="text" readonly="readonly" class="intext" name="quote[0][sea_air]" value="" /></td>
				<td><input type="text" readonly="readonly" class="intext" name="quote[0][details]" value="" /></td>
				<td><input type="text" readonly="readonly" class="intext" name="quote[0][price]" value="" /></td>
				<td><input type="text" readonly="readonly" class="intext" name="quote[0][status]" value="" /></td>
				<!--<td><a href = "#" onclick="javascript: delQuote(0); return false;">Delete</a></td>-->
			<!--</tr> -->
<?php
	//If there is another comment, fill in the new contact
	if(is_array($quote) && count($quote)>0)
	{
		$counter = 1;
		while($counter <= count($quote))
		{
			if($counter%2==0)
				echo '<tr class = "alt" id = "quote_row_'.$counter.'">';
			else
				echo '<tr id = "quote_row_'.$counter.'">';
?>
					<!--<tr id="quote_row_<?= $counter; ?>">-->
						<td align="center" class="qt_bk"><?= date("d-m-Y", strtotime($quote[$counter]['date'])); ?>
						<td align="center" class="qt_bk"><a href="/webadmin/member/edit_member.php?action=edit&amp;member_id=<?=$quote[$counter]['customer_id']?>" target="_blank"><?=htmlspecialchars($quote[$counter]['customer_name'])?></a></td>
						<td align="center" class="qt_bk"><a href="/webadmin/invoice/?action=edit&amp;invoice_id=<?= $quote[$counter]['qt_no']; ?>" target="blank">QT#<?= $quote[$counter]['qt_no']; ?></a></td>
						<td align="center" class="qt_bk"><?= $quote[$counter]['sea_air']; ?></td>
						<td align="center" class="qt_bk"><?= $quote[$counter]['details']; ?></td>
						<td align="center" class="qt_bk"><?= $quote[$counter]['price']; ?></td>
						<td align="center" class="qt_bk"><?= $quote[$counter]['status']; ?></td>
						<!--<td><a href = "#" onclick="javascript: delQuote(<?= $counter; ?>, <?= $id; ?>, <?= $quote[$counter]['id']; ?>); return false;">Delete</a></td>-->
						<!--<td><a href = "#" >Delete</a></td>-->
					</tr>
<?php
			$counter++;
		}
	}
?>
			</tr>
		</table>
		<br />
		<br />
<?php
}
?>
<!--_________________________________End Quotes Section _______________________________-->
<!-- ________________________________Bookings Section ___________________________________-->
<?php
if(is_array($booking) && count($booking)>0)
{
?>
		<div class="flagHeader"><span>Bookings with this Forwarder</span><!--style="background-color:yellow;"-->
		<!--<a href="#" onclick="javascript: var rID = cloneRow('booking_row'); rID = 'booking[' + rID + '][date]'; document.getElementsByName(rID)[0].value = '<?= date('d-m-Y'); ?>'; return false;">Add New Booking</a>-->
		</div>
		<br />
		<!--<table class="wide vbox">-->
		<table cellpadding="3" cellspacing="1" class="table">
			<tr>
				<th width="15%">Date</th>
				<th width="24%">Customer Name</th>
				<th width="11%">COE ID</th>
				<th width="10%">Tracking #</th>
				<th width="10%">Price</th>
				<th width="20%">Status</th>
				<th width="10%">Paid</th>
				<!--<th>&nbsp;</th>-->
			</tr>
			<!--<tr id="booking_row" class="hidden">
				<td>
					<input type="text" readonly="readonly" class="intext" name="booking[0][date]" value="" />
					<input type="hidden" name="booking[0][id]" value="0" />
				</td>
				<td><input type="text" readonly="readonly" class="intext" name="booking[0][customer_name]" value="" /></td>
				<td><input type="text" readonly="readonly" class="intext" name="booking[0][coe_id]" value="" /></td>
				<td><input type="text" readonly="readonly" class="intext" name="booking[0][price]" value="" /></td>
				<td><input type="text" readonly="readonly" class="intext" name="booking[0][tracking_no]" value="" /></td>
				<td><input type="text" readonly="readonly" class="intext" name="booking[0][status]" value="" /></td>
				<td><input type="text" readonly="readonly" class="intext" name="booking[0][paid]" value="" /></td>
				<!--<td><a href = "#" onclick="javascript: delBooking(0); return false;">Delete</a></td>
			</tr>-->
<?php
	//If there is another comment, fill in the new contact
	if(is_array($booking) && count($booking)>0)
	{
		$counter = 1;
		while($counter <= count($booking))
		{
			if($counter%2==0)
				echo '<tr class = "alt" id = "booking_row_'.$counter.'">';
			else
				echo '<tr id = "booking_row_'.$counter.'">';
?>
							<td align="center" class="qt_bk"><?= date("d-m-Y", strtotime($booking[$counter]['date'])) ?></td>
							<td align="center" class="qt_bk"><?= $booking[$counter]['customer_name'] ?></td>
							<td align="center" class="qt_bk"><?= $booking[$counter]['coe_id'] ?></td>
							<td align="center" class="qt_bk"><?= $booking[$counter]['tracking_no'] ?></td>
							<td align="center" class="qt_bk"><?= $booking[$counter]['price'] ?></td>
							<td align="center" class="qt_bk"><?= $booking[$counter]['status'] ?></td>
							<td align="center" class="qt_bk"><?= $booking[$counter]['paid'] ?></td>
						</tr>
<?php
			$counter++;
		}
	}
?>
				</tr>
		</table>
		<br />
		<br />
<?php
}
?>
<!--_________________________________End Bookings Section _______________________________-->
<!-- ________________________________Notes Section ___________________________________-->
		<div class="flagHeader"><span>Notes about this Forwarder</span></div>
		<br />
		<table class="table">
			<tr>
				<th width="95%">Comment</th>
				<!--<th><a href="#" onclick="javascript:  var rID = cloneRow('comment_row'); var dID = 'comment[' + rID + '][date]'; document.getElementsByName(dID)[0].value = '<?= date('d-m-Y'); ?>'; var uID = 'comment[' + rID + '][staff]'; document.getElementsByName(uID)[0].value = '<?= get_user(); ?>'; return false;">Add</a></th>-->
			</tr>
			<tr id="comment_row" class="hidden">
				<input type="hidden" name="comment[0][date]" value="" />
				<input type="hidden" name="comment[0][id]" value="0" />
				<input type="hidden" name="comment[0][staff]" value="<?= get_user(); ?>" />
				<td><textarea rows = "15" name = "comment[0][comment]" style="width:100%"></textarea></td>
				<!--<td><a href = "#" onclick="javascript: delComment(0); return false;">Delete</a></td>-->
			</tr>
			<tr id="comment_row_1" >
				<input type="hidden" name="comment[1][date]" value="<?= date('d-m-Y');; ?>" />
				<input type="hidden" name="comment[1][id]" value="0" />
				<input type="hidden" name="comment[1][staff]" value="<?= get_user(); ?>" />
				<td><textarea rows = "15" name = "comment[1][comment]" style="width:100%"></textarea></td>
				<!--<td><a href = "#" onclick="javascript: delComment(1, 0, 0); return false;">Delete</a></td>-->
			</tr>
			</table>
			<br />
			<br />
			<!-- Old notes display-->
<?php
					if(is_array($comment) && count($comment)>0)
					{ ?>
						<table width="500" border="0" cellspacing="0" cellpadding="2" align="center" style="border-collapse: collapse; border: 1px solid #CCCCCC">
							<tr>
								<th colspan="3" class="bottomLeftRight">Admin Notes</th>
							</tr>
							<tr class = "alt">
								<td width="10%" align="center" nowrap class="bottomLeft"><strong>Date Added</strong></td>
								<td width="8%" align="center" nowrap class="bottomLeft"><strong>Staff</strong></td>
								<td width="82%" align="center" class="bottomLeftRight"><strong>Notes</strong></td>
							</tr>
<?php
$n_comment = $comment;
						if(count($comment) > 5){
							$comment = array_chunk($comment,5);
							$n_comment = $comment[0];
						}
						$counter = 0;
						foreach($n_comment as $comm)
						{
							//while($counter <= count($comment))
							if($counter%2==1)
							{
								echo '<tr class = "alt">';
							}
							else
							{
								echo '<tr>';
							}
							//echo "<tr bgcolor=\"".($counter%2==1?"#EEEEEE":"#FFFFFF")."\">\n";
							echo "<td class=\"bottomLeft\" valign=\"top\" nowrap align=\"center\" style=\"padding:16px 16px 16px 16px;border:1px solid #DDDDDD;\">".date('d M, Y', strtotime($comm['date']))."</td>\n";
							echo "<td class=\"bottomLeft\" valign=\"top\" nowrap align=\"center\" style=\"padding:16px 16px 16px 16px;border:1px solid #DDDDDD;\">".$comm['staff']."&nbsp;</td>\n";
							echo "<td class=\"bottomLeftRight\" style=\"padding:16px 16px 16px 16px;border:1px solid #DDDDDD;\">".nl2br(stripslashes($comm['comment']))."</td>\n";
							echo "</tr>\n";
							$counter++;
						}?>
<?php
if(count($comment) > 5) { ?>
							<tr><td colspan=3 align="center"><input type="button" value="View All (<?=count($comment)?>)" onclick="return false"></td></tr>
<?php
} ?>
						</table>
						<br />
						<br />
<?php
}?>
		<!--</table>
		<br />
		<br />-->
<!--_________________________________End Notes Section _______________________________-->
		<input type="hidden" name="action" value="do_<?= $add?"add":"edit" ?>">
		<input type="hidden" name="editing_id" value="<?= $id ?>">
		<p align="center"><input type="submit" style="align: center;" name="submit" value="<?= $add?"Add Forwarder":"Edit Forwarder" ?>"></p>
		<br />
	</form>
	<br />
</div>
