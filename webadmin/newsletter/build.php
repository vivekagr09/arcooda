<?php
include_once(dirname(__FILE__) . "/common.inc.php");
include_once('templates/header.tpl.php');

$criteria=$_GLOBALS['criteria_options']['member'];
if(isset($posted['site_id'])) $criteria['site_id']=$posted['site_id'];
if(!in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43))) $criteria['site_id']=$_GLOBALS['site_settings']['site_id'];
$member_list=memberSearch($criteria);

if(isset($criteria['site_id']))
{
	$temp1=array();
	$temp2='=' . intval($criteria['site_id']);
	if(is_array($criteria['site_id'])) $temp2=' IN('.implode(', ',$criteria['site_id']);
	$query=$DB->query("SELECT ml.member_id FROM highwaygroup.newsletter_styles ms INNER JOIN highwaygroup.new_members_mailing_list ml ON ml.style_id=ms.style_id INNER JOIN highwaygroup.new_members m ON m.member_id=ml.member_id WHERE ml.active='y' AND ms.site_id" . $temp2);
	while($row = $DB->fetch_assoc($query)) $temp1[]=$row['member_id'];
	$DB->free_result($query);
	$member_list = array_unique(array_merge($member_list,$temp1));
}

$member_sites=memberGroupCount($member_list,'site_id');
$member_countries=memberGroupCount($member_list,'country');

$site_list=array();
foreach(memberGetOptionList('site_id') as $k=>$v) if($member_sites[$k]>0) $site_list[$k]=$v;

$country_list=array();
foreach(memberGetOptionList('country') as $k=>$v) if($member_countries[$k]>0) $country_list[$k]=$v;

$continent_list = array();
$query = $DB->query("SELECT * FROM `world`.`continent` ORDER BY `continent_name` ASC");
while($row = $DB->fetch_assoc($query)) $continent_list[strtoupper(trim($row["continent_id"]))] = trim($row["continent_name"]);
$DB->free_result($query);

$continent_to_country_list = array();
$query = $DB->query("SELECT * FROM `world`.`country_continent`");
while($row = $DB->fetch_assoc($query)) $continent_to_country_list[strtoupper(trim($row["continent_id"]))][] = strtoupper(trim($row["country_id"]));
$DB->free_result($query);

$site_list=array($_GLOBALS['site_settings']['site_id']=>$site_list[$_GLOBALS['site_settings']['site_id']]);
if(!in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43))) $site_list=array($_GLOBALS['site_settings']['site_id']=>$site_list[$_GLOBALS['site_settings']['site_id']]);

$style_list=array();
$style_to_site_list = array();
$query=$DB->query("SELECT style_id,site_id,style_description FROM highwaygroup.newsletter_styles");
while($row=$DB->fetch_assoc($query))
{
	$style_to_site_list[$row["style_id"]] = $row["site_id"];
	if(isset($site_list[$row['site_id']])) $style_list[$row['style_id']]=$row['style_description'];
}
$DB->free_result($query);

$criteria = $_GLOBALS['criteria_options']['invoice'];
if (isset($posted['site_id'])) $criteria['site_id'] = $posted['site_id'];
if (!in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43))) $criteria['site_id'] = $_GLOBALS['site_settings']['site_id'];
$criteria["invoice_status"] = "processed";
$invoice_list = invoiceGroupCount(invoiceSearch($criteria, strtotime("-1 year")), array("site_id", "member_id"));

$sales_list = array();
foreach(memberGetOptionList('site_id') as $k => $v) if (array_key_exists($k, $invoice_list)) $sales_list[$k] = $v;

$distributor_count=$DB->query_first("SELECT count(`id`) as total  FROM distributor Where `active`='y'");
?>
<h2>Create Newsletter</h2>

<script type="text/javascript">
//<![CDATA[
function validateSectionForm()
{
	return true;
}
function sectionSubmitChanges()
{
	$('form#<?php echo $section_name ?>_form').eq(0).each(function() {this.submit();});
}
function sectionSendPreview()
{
	$('form#<?php echo $section_name ?>_form').eq(0).each(function() {var action=this.action;var target=this.target;this.action='index.php?action=do_preview';this.target='_blank';this.submit();this.action=action;this.target=target;});
}
function sectionSendNewsletter()
{
	$('form#<?php echo $section_name ?>_form').eq(0).attr('action','index.php?action=do_newsletter').each(function() {this.submit();});
}
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
$(function() {
	$("select[name=\"site_id\"]").change(function() {
		$("[name='subscr_list']").closest("li").stop().hide();
		$("." + $(this).val()).stop().show();
	}).change();
});
//]]>
</script>

<form id="<?php echo $section_name ?>_form" method="post" action="add.php">
	<input type="hidden" name="<?php echo $section_name ?>[staff_id]" value="<?php echo $_GLOBALS['staff']['id'] ?>" />

	<?php echo formatHTMLTitle('Mailing List') ?>
	<table class="table_fields">
<?php
if (in_array($_GLOBALS["site_settings"]["site_id"], array(11, 43)))
{
?>
		<tr>
			<th>Website</th>
			<td><select name="site_id">
				<option value="site_id_0">All</option>
<?php
foreach(memberGetOptionList('site_id') as $k => $v) if (array_key_exists($k, $site_list) || array_key_exists($k, $sales_list))
{
?>
				<option value="site_id_<?php echo $k ?>"><?php hecho($v) ?></option>
<?php
}
?>
			</select></td>
		</tr>
<?php
}
?>
		<tr>
			<th>Member Type</th>
			<td><ul class="block_list">
<?php
foreach($site_list as $site_id=>$site_name)
{
	$label="site_id_" . $site_id;
?>
				<li class="site_id_0 site_id_<?php echo $site_id ?>"><input<?php echo (count($style_list)>1 && count($site_list)==1)?' checked="checked"':'' ?> type="radio" id="<?php echo $label ?>" name="subscr_list" value="site_<?php echo $site_id ?>" /> <label for="<?php echo $label ?>">Include All Active &quot;<?php echo formatHTMLSafe($site_name) ?>&quot; Members</label></li>
<?php
}
foreach($style_list as $style_id=>$style_name)
{
	$label="style_id_" . $style_id;
	;
?>
				<li class="site_id_0<?php echo ($style_to_site_list[$style_id] != "") ? (" site_id_" . implode(" site_id_", explode(",", $style_to_site_list[$style_id]))) : "" ?>"><input<?php echo (count($style_list)<=1)?' checked="checked"':'' ?> type="radio" id="<?php echo $label ?>" name="subscr_list" value="style_<?php echo $style_id ?>" /> <label for="<?php echo $label ?>">Include Only &quot;<?php echo formatHTMLSafe($style_name) ?>&quot; Members</label></li>
<?php
}
foreach($sales_list as $sales_id => $sales_name)
{
	$label = "sales_id_" . $sales_id;
?>
				<li class="site_id_0 site_id_<?php echo $sales_id ?>"><input type="radio" id="<?php echo $label ?>" name="subscr_list" value="sales_<?php echo $sales_id ?>" /> <label for="<?php echo $label ?>">Include Only &quot;<?php echo formatHTMLSafe($sales_name) ?>&quot; Members who purchased in last 12 months</label></li>
<?php
}
if($distributor_count > 0) {
	$label = "distributor_" . $_GLOBALS['site_settings']['site_id'];
?>
				<li class="site_id_0"><input type="radio" id="<?php echo $label ?>" name="subscr_list" value="distributor_<?php echo $_GLOBALS['site_settings']['site_id'] ?>" /> <label for="<?php echo $label ?>">Include Only Arcooda Distributor</label></li>

				<li class="site_id_0"><input type="radio" id="<?php echo "agents_" . $_GLOBALS['site_settings']['site_id']; ?>" name="subscr_list" value="agents_<?php echo $_GLOBALS['site_settings']['site_id'] ?>" /> <label for="<?php echo "agents_" . $_GLOBALS['site_settings']['site_id']; ?>">Include Only Arcooda Agents</label></li>
				<li class="site_id_0"><input type="radio" id="<?php echo "distagents_" . $_GLOBALS['site_settings']['site_id']; ?>" name="subscr_list" value="distagents_<?php echo $_GLOBALS['site_settings']['site_id'] ?>" /> <label for="<?php echo "distagents_" . $_GLOBALS['site_settings']['site_id']; ?>">Include All Distributors & Agents</label></li>
<?php
}
?>
			</ul></td>
		</tr>
	</table>

	<?php echo formatHTMLTitle('Best Described','<a href="#" onclick="return selectAll(\'best_described[]\')">Select All</a> <a href="#" onclick="return invertAll(\'best_described[]\')">Invert Selection</a>') ?>
	<table class="table_fields"><tr><td><ul class="float_list">
<?php
foreach(memberGetOptionList('best_described') as $k=>$v)
{
	$label="best_described_" . $k;
?>
		<li><input checked="checked" type="checkbox" id="<?php echo $label ?>" name="address_type[]" value="<?php echo $k ?>" /> <label for="<?php echo $label ?>"><?php echo formatHTMLSafe($v) ?></label></li>
<?php
}
?>
	</ul></td></tr></table>

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
	foreach($country_list as $k2 => $v2) if (in_array($k2, $continent_to_country_list[$k1]))
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

	<input type="button" value="Create List" onclick="if(validateSectionForm()) sectionSubmitChanges()" />
</form>

<?php
include_once('templates/footer.tpl.php');
?>