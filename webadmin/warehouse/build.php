<style>
	ul.float_list li {
	    float: left;
	    display: block;
	    list-style: none;
	    width: 264px;
	    padding: 0px;
	    margin: 0;
	}
	ul.float_list {
	    display: block;
	    padding: 16px;
	    margin: 0;
	}
	ul.block_list {
    display: block;
    padding: 16px;
    margin: 0;
}
ul.block_list li {
    display: block;
    list-style: none;
    padding: 0;
    margin: 0;
}
</style>
<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';
global $_GLOBALS;
$country_list=array();
moduleLoad("member");
$partner_list=socialMediaPartnerSearch();
$partner_countries=socialMediaPartnerGroupCount($partner_list,'country');

foreach(memberGetOptionList('country') as $k=>$v) if($partner_countries[$k]>0) $country_list[$k]=$v;

$continent_list = array();
$query = $DB->query("SELECT * FROM `world`.`continent` ORDER BY `continent_name` ASC");
while($row = $DB->fetch_assoc($query)) $continent_list[strtoupper(trim($row["continent_id"]))] = trim($row["continent_name"]);
$DB->free_result($query);

$continent_to_country_list = array();
$query = $DB->query("SELECT * FROM `world`.`country_continent`");
while($row = $DB->fetch_assoc($query)) $continent_to_country_list[strtoupper(trim($row["continent_id"]))][] = strtoupper(trim($row["country_id"]));

$DB->free_result($query);
?>
<form  method="post" id="newsletter_form" action="sendupdate.php">
	<input type="hidden" name="<?php echo $section_name ?>[staff_id]" value="<?php echo $_GLOBALS['staff']['id'] ?>" />
<?php echo formatHTMLTitle('Member Type') ?>

<table class="table_fields">
	<tr>
		<th>Member Type</th>
		<td>
				<div><input type="radio" name="member_type" value="0">Include All</div>
				<?php foreach (socialMediaPartnerGetOptionList('member_type') as $key => $type) {
					echo '<div><input type="radio" name="member_type" value="'.$key .'">'.$type.'</div>';
				}
				?>
			</ul>
		</td>
	</tr>
</table>
<?php echo formatHTMLTitle('Category') ?>
<table class="table_fields">
	<tr>
		<th>Category</th>
		<td>	
				<ul class="block_list">
				<li><input type="radio" name="category" value="0">All</li>
				<?php 

				foreach (socialMediaPartnerGetOptionList('category') as $k => $v) { 
				?>
				    <li><input type="radio" id="item_category_<?=$k?>" name="category" value="<?=$k?>"><label for="item_category_<?=$k?>"><?=htmlspecialchars($v)?></label></li>
				<?php
				}
				?>
			</ul>
		</td>
	</tr>
</table>
<?php echo formatHTMLTitle('Country','<a href="#" onclick="return selectAll(\'country[]\')">Select All</a> <a href="#" onclick="return invertAll(\'country[]\')">Invert Selection</a>') ?>
	<table class="table_fields">
<?php
$temp = array();

foreach($continent_list as $k1 => $v1) if (array_key_exists($k1, $continent_to_country_list))
{
?>
		<tr>
			<th><?php hecho($v1) ?></th>
			<td><ul class="float_list">
<?php
	foreach($country_list as $k2 => $v2) 
		if (in_array($k2, $continent_to_country_list[$k1]))
	{
		$temp[] = $k2;
		$label = "continent_" . $k1 . "_country_" . $k2;
?>
				<li><input checked="checked" type="checkbox" id="<?php echo $label ?>" name="country[]" value="<?php echo $k2 ?>" /> <label for="<?php echo $label ?>"><?php echo formatHTMLSafe($v2) ?></label></li>
<?php
	}
?>
			</ul></td>
		</tr>
<?php
}
?>
		<tr>
			<th>Other</th>
			<td><ul class="float_list">
<?php
foreach($country_list as $k=>$v) if (!in_array($k, $temp))
{
	$label = "country_" . $k;
?>
				<li><input checked="checked" type="checkbox" id="<?php echo $label ?>" name="country[]" value="<?php echo $k ?>" /> <label for="<?php echo $label ?>"><?php echo formatHTMLSafe($v) ?></label></li>
<?php
}
?>
			</ul></td>
		</tr>
	</table>
	<div style="padding: 20px;width: 100%;text-align: center;">
		<input type="button" value="Create List" onclick="if(validateSectionForm()) sectionSubmitChanges()" />
	</div>
</form>
<script type="text/javascript">
	function invertAll(name)
{
	$(":checkbox[name='"+name+"']").each(function() {
		this.checked=!this.checked;
	});
	return false;
}
function selectAll(name)
{
	$(":checkbox[name='"+name+"']").attr('checked',true);
	return false;
}
function validateSectionForm()
{
	return true;
}
function sectionSubmitChanges()
{
	$('form#newsletter_form').eq(0).each(function() {this.submit();});
}
</script>