<?php
/**
 * @var string[] $allowed_sections
 * @var mixed[]  $staff
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionPersonalDetails\');">Personal Details</a>')?>

<div id="sectionPersonalDetails">

<script type="text/javascript">
function onchangeName()
{
	var fromEmail=getValue('email');
	var lastName=getValue('last_name');
	var firstName=getValue('first_name');
	if (fromEmail!='' || lastName=='' || firstName=='') return;

	setValue('email',(firstName.charAt(0)+lastName).toLowerCase()+'@highwaygroup.org');
}

function icqSearch()
{
	window.open('staff_icqsearch.php?first_name=' + escape(getValue('first_name')) + '&last_name=' + escape(getValue('last_name')) + '&email=' + escape(getValue('email')));
}
</script>

<input type="hidden" name="staff_id" value="<?=$staff['staff_id']?>" />

<?php
if (in_array('USERS',$allowed_sections))
{
?>
<table class="wide">
	<tr>
		<td class="left_title" style="width:25%;">Last Name</td>
		<td style="width:25%;"><input class="intext" name="last_name" type="text" onchange="javascript:onchangeName();" value="<?=htmlspecialchars($staff['last_name'])?>" /></td>
		<td class="left_title" style="width:25%;">Photo</td>
		<td style="width:25%;">
			<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
			<input class="intext" type="file" name="new_photo" />
		</td>
	</tr>
	<tr>
		<td class="left_title">First Name</td>
		<td><input class="intext" name="first_name" type="text" onchange="javascript:onchangeName();" value="<?=htmlspecialchars($staff['first_name'])?>" /></td>
		<td class="left_title" rowspan="2">Signature<br /><span style="font-size:xx-small;color:red;">(Please resize to 210x70)</span></td>
		<td rowspan="2">
			<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
			<input class="intext" type="file" name="new_sig" />
		</td>
	</tr>
	<tr>
		<td class="left_title">Address</td>
		<td><input class="intext" name="address" type="text" value="<?=htmlspecialchars($staff['address'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Post Code</td>
		<td><input class="intext" name="post_code" type="text" value="<?=htmlspecialchars($staff['post_code'])?>" /></td>
		<td rowspan="10" style="text-align:center;vertical-align:middle;">
<?php
$file=$_SERVER['DOCUMENT_ROOT'].'/images/staff/'.$staff['photo'];
if ($staff['photo']!='' && is_file($file))
{
	$image_info=getimagesize($file);
	$scale=96.0/max(96,max($image_info[0],$image_info[1]));
?>
			<a href="/images/staff/<?=$staff['photo'] ?>" target="_blank"><img name="img_photo" style="width:<?=intval($image_info[0]*$scale) ?>px;height:<?=intval($image_info[1]*$scale) ?>px;" src="/images/staff/<?=$staff['photo']?>" border="0" /></a>
<?php
}
?>
		</td>
		<td rowspan="10" style="text-align:center;vertical-align:middle;">
<?php
$file=$_SERVER['DOCUMENT_ROOT'].'/images/sigs/'.$staff['signature'];
if ($staff['signature']!='' && is_file($file))
{
	$image_info=getimagesize($file);
	$scale=min(210.0/max(210,$image_info[0]),70.0/max(70,$image_info[1]));
?>
			<a href="/images/sigs/<?=$staff['signature'] ?>" target="_blank"><img name="img_photo" style="width:<?=intval($image_info[0]*$scale) ?>px;height:<?=intval($image_info[1]*$scale) ?>px;" src="/images/sigs/<?=$staff['signature']?>" border="0" /></a>
<?php
}
?>
		</td>
	</tr>
	<tr>
		<td class="left_title">Country</td>
		<td><select name="country">
			<option value=""></option>
			<?=formatHTMLOptions(getCountryList(),$staff['country'])?>
		</select></td>
	</tr>
	<tr>
		<td class="left_title">Date Of Birth (YYYY-MM-DD)</td>
		<td><input type="date" name="birth_date" value="<?=($staff['birth_time'] != 0) ? date('Y-m-d', $staff['birth_time']) : ''?>" /></td>
	</tr>
	<tr>
		<td class="left_title" style="height:1px;width:25%;">Home Phone</td>
		<td style="height:1px;width:25%;"><input class="intext" name="home_phone" type="text" value="<?=htmlspecialchars($staff['home_phone'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title" style="height:1px;width:25%;">Mobile Phone</td>
		<td style="height:1px;width:25%;"><input class="intext" name="mobile" type="text" value="<?=htmlspecialchars($staff['mobile'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title" style="height:1px;width:25%;">Fax Number</td>
		<td style="height:1px;width:25%;"><input class="intext" name="fax" type="text" value="<?=htmlspecialchars($staff['fax'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title" style="width:25%;">Email - Work</td>
		<td style="width:25%;"><input class="intext" name="email" type="text" value="<?=htmlspecialchars($staff['email'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title">Email - Home</td>
		<td><input class="intext" name="home_email" type="text" value="<?=htmlspecialchars($staff['home_email'])?>" /></td>
	</tr>
	<tr>
		<!-- <a href="javascript:void(0);" onclick="javascript:icqSearch();"> -->
		<td class="left_title">ICQ# - Work</td>
		<td><input class="intext" name="icq_no" type="text" value="<?=htmlspecialchars($staff['icq_no'])?>" /></td>
	</tr>
	<tr>
		<td class="left_title" style="height:auto;"></td>
		<td style="height:auto;"></td>
	</tr>
</table>
<?php
}
else
{
?>
<table style="width:50%;">
	<tr>
		<td class="left_title" style="width:50%;">Last Name</td>
		<td style="width:50%;"><?=htmlspecialchars($staff['last_name'])?></td>
	</tr>
	<tr>
		<td class="left_title">First Name</td>
		<td><?=htmlspecialchars($staff['first_name'])?></td>
	</tr>
	<tr>
		<td class="left_title">Address</td>
		<td><?=htmlspecialchars($staff['address'])?></td>
	</tr>
	<tr>
		<td class="left_title">Post Code</td>
		<td><?=htmlspecialchars($staff['post_code'])?></td>
	</tr>
	<tr>
		<td class="left_title">Country</td>
		<td>
<?php
	$country_list=getCountryList();
	echo $country_list[$staff['country']];
?>
		</td>
	</tr>
	<tr>
		<td class="left_title">Home Phone</td>
		<td><?=htmlspecialchars($staff['home_phone'])?></td>
	</tr>
	<tr>
		<td class="left_title">Mobile Phone</td>
		<td><?=htmlspecialchars($staff['mobile'])?></td>
	</tr>
	<tr>
		<td class="left_title">Fax Number</td>
		<td><?=htmlspecialchars($staff['fax'])?></td>
	</tr>
	<tr>
		<td class="left_title">Email - Work</td>
		<td><?=htmlspecialchars($staff['email'])?></td>
	</tr>
	<tr>
		<td class="left_title">Email - Home</td>
		<td><?=htmlspecialchars($staff['home_email'])?></td>
	</tr>
	<tr>
		<td class="left_title">ICQ# - Work</td>
		<td><?=htmlspecialchars($staff['icq_no'])?></td>
	</tr>
</table>
<?php
}
?>
<br /><br />
</div>
<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionPersonalDetails_hideNote">
Personal Details can be viewed by Clicking "Personal Details" above
<br />
</div>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
