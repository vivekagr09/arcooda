<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('member'); //memberFromDatabase

$action = $posted['action'];
if (!isset($inc)) {
    include_once __DIR__ . '/header.inc.php';
}

/*************************************** Function Definitions *******************************************************/
/*
function doSearch($posted)
{
// now have to put the query in the proper format...
global $DB;
$criteria=array();
$link_sites=array('cnv','he','hg','car');
$site_id=$posted['site_id'];
if(!is_array($site_id)) $site_id=explode('|',$posted['site_id']);
if(is_array($site_id))
$results=array();

$sql=" SELECT p.id FROM `convert_convert`.products p";
$joins=array();

$fields=getTableFields('products');

$whereAND=array();
// Modified........Added { } at appropriate places....JS (01/Sep/06)
if(is_array($criteria))
{
foreach($criteria as $field=>$value)
if(isset($fields[$field]))
{
if(is_array($value))
{
if(count($value)>0)
{
$whereOR=array();
foreach($value as $sub_value)
$whereOR[]="    p.`".$field."`=".formatSQLString($sub_value);
$whereAND[]="    (".implode(" OR ",$whereOR).")";
}
else
return array();
}
else
$whereAND[]="    p.`".$field."`=".formatSQLString($value);
unset($fields[$field]);
}
}
elseif(is_string($criteria) && $criteria!='')
$criteria=array('any_text'=>$criteria);

$search_for=array();
if(is_array($criteria['any_text'])) $search_for=$criteria['any_text'];
if(is_string($criteria['any_text'])) $search_for=explode(' ',$criteria['any_text']);
if(count($search_for)>0)
{
//$searchBits=array('p.id', 'p.sup_code', 'p.product_name', 'p.short_desc', 'p.long_desc', 'p.product_details');
$searchBits=array('p.id', 'p.product_name', 'p.sup_code', 'p.sup_name');
foreach($search_for as $findme)
{
$whereOR=array();
if(trim($findme)!='') {
foreach($searchBits as $key_id=>$key) {
if($flags=='' || (($flags&(1<<$key_id))==(1<<$key_id)))
$whereOR[]=$key." LIKE '%".str_replace('%','\%',str_replace('_','\_',addslashes($findme)))."%'";
}
}
if(count($whereOR)>0) $whereAND[]="    (".implode(" OR ",$whereOR).")";
}
}
if($extra!='') $whereAND[]=$extra;
if($power_only) $whereAND[]="(p.type=22 OR p.type=25)";
if($active_only) $whereAND[]="p.active='y'";

if(count($whereAND)>0) $sql.=" WHERE ".implode(" AND ",$whereAND);

$sql.=" ORDER BY p.id ASC";

//echo $sql.'<br/>';
//die();
$query=$DB->query($sql);
while($row=$DB->fetch_array($query,MYSQLI_ASSOC))
$results[]=$row['id'];
$DB->free_result($query);
return $results;
}
 */

function do_search($query, $order_by = 'member_id', $order = 'desc', $site_id = 'all')
{
    display_search();
    global $posted, $DB;
    $member_list = array();
    $country     = $posted['country'];

    if ('' == $site_id) {
        $site_id = 'all';
    }

    if (!is_array($site_id)) {
        $site_id = explode('|', $site_id);
    }

    $page = $posted['page'];
    if ('' == $page) {
        $page = 0;
    }

    $limit = 100;
    $start = $page * $limit;

    // now the search function.....

    $sql        = "SELECT `m`.`member_id` FROM `highwaygroup`.`new_members` AS `m`";
    $searchBits = array('`m`.`first_name`', '`m`.`last_name`', '`m`.`company_name`', '`m`.`email_address`', '`m`.`address`', '`m`.`city`', '`m`.`state`', '`m`.`post_code`');
    $whereAND   = array();
    $whereOR    = array();
    foreach ($searchBits as $search) {
        $whereOR[] = $search . " LIKE " . formatSQLLikeString($query);
    }

    if (count($whereOR)) {
        $whereAND[] = " (" . implode(" OR ", $whereOR) . ")";
    }

    if (0 != $country) {
        $whereAND[] = "`m`.`country` = " . formatSQLString($country);
    }

    if (count($whereAND)) {
        $sql .= " WHERE " . implode(" AND ", $whereAND);
    }

    $sql .= " ORDER BY `m`.`member_id` ASC";
    $res = $DB->query($sql);
    while ($row = $DB->fetch_array($res, MYSQLI_ASSOC)) {
        $member_list[] = $row['member_id'];
    }

    echo formatHTMLTitle('Results');

    $sort_url = 'search2.php?action=do_search&query=' . $posted['query'];
    if (is_array($site_id)) {
        $temp = "&";
        foreach ($site_id as $key => $value) {
            $temp .= "site_id[$key]=$value&";
        }

        $sort_url .= $temp;
    }

    $o_member_list = $member_list;
    $member_list = array_chunk($member_list, $limit);
    $member_list = $member_list[$page];

    $edit_url           = $get_vars;
    $edit_url['action'] = 'edit';
    unset($edit_url['supplier_id']);
    $edit_url = '.?' . http_build_query($edit_url);

    if (count($member_list) > 0) {
?>
<table class="table_grid">
	<tr>
<?php
        $array = array(
            'date' => 'Date Of Order',
            'member_id' => 'ID',
            'company' => 'Company',
            'last_name' => 'Contact Name',
            'member_type' => 'Member Type',
            'orders' => 'Orders',
            'modified' => 'Last Contact',
        );
        foreach ($array as $key => $value) {
            $new_order = ($order_by == $key && 'asc' == $order) ? 'desc' : 'asc';
?>
		<th>
			<a href="<?=$sort_url?>&page=<?=$page?>&order_by=<?=$key?>&order=<?=$new_order?>"><?=$value?></a>
			<?=($order_by == $key) ? '<img src="../' . $order . '.gif">' : ''?>
		</th>
<?php
}
?>
		<th></th>
	</tr>
<?php
foreach ($member_list as $member_id) {
            $member = memberFromDatabase($member_id);
?>
	<tr<?=($alt = !$alt) ? '' : ' class="alt"'?> style="height:32px;">
		<td style="text-align:center;"><?=date('d/m/Y', $member_dates[$member_id]);?></td>
		<td style="text-align:center;"><?=substr('000000' . $member_id, -6)?></td>
		<td style="text-align:center;"><?=$member['company']?></td>
		<td style="text-align:center;"><?=$member['name'] . ' ' . $member['last_name']?></td>
		<td style="text-align:center;">
			<!--<table cellpadding=2><tr>-->
<?php
$mem_type = explode(",", $member['member_type']);
            foreach ($mem_type as $mem) {
                if ('' != $mem) {
?>
			<img src="images/<?=$mem?>.png">&nbsp;&nbsp;&nbsp;
<?php
}
            }
?>
			<!--</tr></table>-->
		</td>
		<td style="text-align:center;"><?=$member['total_orders']?></td>
		<td style="text-align:center;"><?=('0000-00-00' == $member['modified']) ? 'never' : date("d/m/y", strtotime($member['modified']))?></td>
		<td style="text-align:center;">
<?php
if (isset($posted['return_message'])) {
?>
			<a href=".?action=select&member_id=<?=$member_id?>&return_message=<?=urlencode($posted['return_message'])?>">Select</a>
			<a href="<?=$edit_url?>&member_id=<?=$member_id?>&return_message=<?=urlencode($posted['return_message'])?>">Edit</a>
<?php
} else {
?>
			<a href="<?=$edit_url?>&member_id=<?=$member_id?>" target="_blank">Edit</a>
<?php
}
?>
		</td>
	</tr>
<?php
}
?>
</table>
<?php
$items = count($member_list);
        $total = count($o_member_list);
        if ('alpha' == $by) {
            $limit = $total;
        }

        $url = "?action=do_search&query=$query&order_by=$order_by&order=$order&p&by=$by&alpha_value=$alpha_value&page=";
        echo '<br /><br /><center>' . formatHTMLPageLinks($url, $page, $items, $total, $limit, 'style="color:blue"') . '</center>';
    } else {
        echo "No Results were found.";
    }

}

function display_search()
{
    global $posted;
?>
<br />
<link type="text/css" rel="stylesheet" href="style.css" />
<form accept-charset="utf-8" autocomplete="off" method="get" action="search3.php">
<table class="table_search">
	<tr>
		<td colspan="2">
<?php
if ('' != $posted['return_message']) {
?>
<input type="hidden" name="return_message" value="<?php hecho($posted['return_message'])?>" />
<?php
}
?>
			<input type="hidden" name="action" value="do_search" />
			<div align="center"><strong><font color="#FF6600" size="4">Search By Region</font></strong></div>
		</td>
	</tr>
	<tr><td colspan="2">
		Enter Customer <b>Name</b>, <b>Company Name</b> or <b>Email</b><br />Then enter <b>Location</b>, <b>State</b>, <b>Post Code</b> and/or <b>Country</b>
	</td></tr>
	<tr>
		<td style="text-align:right"><b>Customer Info :</b></td>
		<td><input type="text" name="query" style="width:98%" size="30" value="<?php hecho($posted['query'])?>" /></td>
	</tr>
	<tr>
		<td style="text-align:right"><b>Suburb/City/Region :</b></td>
		<td><input type="text" name="city" style="width:98%" size="30" value="<?php hecho($posted['city'])?>" /></td>
	</tr>
	<tr>
		<td style="text-align:right"><b>State :</b></td>
		<td><input type="text" name="state" style="width:98%" size="30" value="<?php hecho($posted['state'])?>" /></td>
	</tr>
	<tr>
		<td style="text-align:right"><b>Post Code :</b></td>
		<td><input type="text" name="post_code" style="width:98%" size="30" value="<?php hecho($posted['post_code'])?>" /></td>
	</tr>
	<tr>
		<td style="text-align:right"><b>Country :</b></td>
		<td><select style="width:98%" name="country"><?=formatHTMLOptions(array(0 => ' ') + getCountryList(), $posted['country'])?></select></td>
	</tr>
	<tr>
		<td style="text-align:right"><b>Site :</b></td>
<?php
$selected = $_REQUEST['site_id'];
    if (is_array($selected)) {
        foreach ($selected as $key => $value) {
            $selected[$key] = trim($value);
        }
    }

    if (!is_array($selected)) {
        $selected = array();
    }

?>
		<td style="text-align:left;">
			<input type="checkbox" name="site_id[0]" value="0" <?=(in_array('all', $selected)) ? 'checked' : ''?>>All&nbsp;&nbsp;
			<input type="checkbox" name="site_id[1]" value="2" <?=(in_array('he', $selected)) ? 'checked' : ''?>>HE&nbsp;&nbsp;
			<input type="checkbox" name="site_id[2]" value="1" <?=(in_array('hg', $selected)) ? 'checked' : ''?>>HG&nbsp;&nbsp;
			<input type="checkbox" name="site_id[3]" value="9" <?=(in_array('cnv', $selected)) ? 'checked' : ''?>>Converters.tv&nbsp;&nbsp;
			<input type="checkbox" name="site_id[4]" value="16" <?=(in_array('car', $selected)) ? 'checked' : ''?>>Car Converters
		</td>
	</tr>
	<tr>
		<td colspan="2">

			<div align="center">

			</div>
		</td>
	</tr>
	<tr><td style="text-align:center" colspan="2"><input style="width:200px;" type="submit" value="Search" /></td></tr>
</table>
</form>
<?php
}

/************************************ Main: Switch-Case Scenario ****************************************************/
switch ($action) {
    case 'do_search':
        $query    = $posted['query'];
        $order_by = ($posted['order_by']) ? $posted['order_by'] : 'member_id';
        $order    = ($posted['order']) ? $posted['order'] : 'desc';
        $site_id  = $posted['site_id'];
        do_search($query, $order_by, $order, $site_id);
        //$action = 'search';
        break;
    case 'search':
    default:
        display_search();
}

if (!isset($inc)) {
    include_once __DIR__ . '/footer.inc.php';
}
