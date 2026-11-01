<?php
include_once __DIR__ . '/common.inc.php';

moduleLoad('member'); //memberFromDatabase

switch ($_REQUEST['action']) {
    case 'select':
        showSelectMember();
        break;
    default:
        showSearchMember();
        break;
}
return;

function show_links($links)
{
    $result = '';
    if (!count($links)) {
        return;
    }

    $result .= '<b>Showing : ' . $links['start_from'] . ' - ' . $links['ends_at'] . ' of ' . $links['max'] . '</b>';
    if ($links['url_prev_page']) {
        $result .= ' | <a href="' . $links['url_prev_page'] . '">&lt;&lt;</a>';
    }

    foreach ($links['links'] as $link) {
        if ($link['url']) {
            $result .= ' | <a href="' . $link['url'] . '">' . $link['start'] . ' - ' . $link['end'] . '</a>';
        } else {
            $result .= ' | ' . $link['start'] . ' - ' . $link['end'];
        }

    }
    if ($links['url_next_page']) {
        $result .= ' | <a href="' . $links['url_next_page'] . '">&gt;&gt;</a> ';
    }

    return $result;
}

function showSearchMember()
{
    global $DB, $myurl;

    //Compatibility
    if (isset($_GET['for'])) {
        $_GET['search_for'] = $_GET['for'];
    }

    $get_original = $_GET;

    $getvals = $get_original;
    unset($getvals['orderby']);
    switch ($getvals['desc']) {
        case 'DESC':$getvals['desc'] = 'ASC';
            $ord_img                     = '<i class="fas fa-sort-down"></i>';
            break;
        case 'ASC':unset($getvals['desc']);
            $ord_img = '<i class="fas fa-sort-up"></i>';
            break;
        default:$getvals['desc'] = 'DESC';
            $ord_img                 = '';
            break;
    }
    $sortbyurl = "?" . http_build_query($getvals) . "&orderby=";

    $getvals = $get_original;
    unset($getvals['page']);
    $newpageurl = "?" . http_build_query($getvals) . "&page=";

    $itemsperpage = 50;
    $page         = intval($get_original['page']);
    $search_for   = $get_original['search_for'];
    $desc         = strtoupper($get_original['desc']);
    $orderby      = $get_original['orderby'];
    if ('' == $orderby) {
        $orderby = 'member_id';
        $desc    = 'DESC';
    }

    //$member_list=searchMembers($search_for,false,true);
    $member_list = searchMembers2($search_for, $orderby);
    if ('DESC' == $desc) {
        $member_list = array_reverse($member_list);
    }

    $total_items = count($member_list);
    $member_list = array_chunk($member_list, $itemsperpage);
    $total_pages = count($member_list);
    $member_list = $member_list[$page];
    $itemsonpage = count($member_list);
    $firstitem   = $page * $itemsperpage;

    $page_list = array();
    for ($i = 1; $i <= $total_pages; $i++) {
        $page_list[$i - 1] = $i;
    }

    $detail_list = array();
    if (count($member_list) > 0) {
        foreach ($member_list as $member_id) {
            $row = memberFromDatabase($member_id, false);
            if ('' == $row['company']) {
                $row['company'] = $row['name'];
            }

            if ('' == $row['name']) {
                $row['name'] = $row['company'];
            }

            $detail_list[] = $row;
        }
    }
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
    	<link type="text/css" rel="stylesheet" href="style.css" />
    	<script type="text/javascript">
//<![CDATA[
function getElement(name)
{
	var elem=document.getElementById(name);
	if(elem) return elem;
	elem=document.getElementsByName(name);
	if(elem.length==0) return null;
	return elem[0];
}
function setValue(name,value)
{
	var elem=getElement(name);
	if(elem==null) return;
	elem.value=value;
}
//]]>
    	</script>
    </head>
    <body style="text-align:center">

<form accept-charset="utf-8" method="post" action="<?= $myurl ?>?for=<?= $product_id ?>">
<input type="hidden" name="member_id" value="0" />
<input type="hidden" name="action" value="select" />

<div class="invoice_content">
<table class="wide">
	<tr>
		<th><a href="<?= $sortbyurl ?>member_id">ID</a></th>
		<th><a href="<?= $sortbyurl ?>name">Name</a></th>
		<th><a href="<?= $sortbyurl ?>email_address">Primary eMail</a></th>
		<th>&nbsp;</th>
	</tr>
<?php
if (count($detail_list) > 0) {
        foreach ($detail_list as $item) {
?>
	<tr<?= ($alt = !$alt) ? '' : ' class="alt"' ?> style="font-size:12px;">
		<td style="text-align:right"><?= $item['member_id'] ?></td>
		<td style="text-align:left"><?= $item['name'] ?></td>
		<td style="text-align:left"><?= $item['email_address'] ?></td>
		<td style="text-align:center"><input type="button" value="Select" onclick="setValue('member_id','<?= $item['member_id'] ?>');form.submit();" /></td>
	</tr>
<?php
}
    }
?>
	<tr>
		<td colspan="4" class="left_title" style="font-size:12px;">Page:
			<select onchange="document.location='<?= $newpageurl ?>'+this.value;" style="width:auto;">
				<?= formatHTMLOptions($page_list, $page) ?>
			</select>
			of <?= $total_pages ?>
		</td>
	</tr>

</table>
</div>
</form>

    </body>
</html>
<?php
}

function showSelectMember()
{
    $country_list = getCountryList(false);

    $member_id = intval($_GET['member_id']);
    if (0 == $member_id) {
        $member_id = intval($_POST['member_id']);
    }

    $row = memberFromDatabase($member_id, true, true);

    if ('' == $row['company']) {
        $row['company'] = $row['name'];
    }

    if ('' == $row['name']) {
        $row['name'] = $row['company'];
    }

    $ship_address = array();
    if (count($row['ship_addr']) > 0) {
        foreach ($row['ship_addr'] as $sid => $addr) {
            $addr['country_full'] = $country_list[$addr['country']] ?? $addr['country'];
            $ship_address[]       = $addr;
        }
    }
    $row['ship_addr'] = $ship_address;
    $couriers         = array();
    if (count($row['courier_accounts']) > 0) {
        $match_array = array('courier_acc_id' => 'id', 'member_id' => '', 'courier_id' => '', 'account_number' => 'account_num', 'notes' => '');
        foreach ($row['courier_accounts'] as $courier) {
            //need to put the contacts in the proper format..
            $temp = array();
            foreach ($match_array as $from => $to) {
                if ('' == $to) {
                    $to = $from;
                }

                $temp[$to] = $courier[$from];
            }
            $temp['member_courier'] = 'y';
            $temp['courier_name']   = getPersonalCourierName($courier['courier_id']);
            $couriers[]             = $temp;
        }
        unset($row['courier_accounts']);
    }
    $row['couriers'] = $couriers;

    $row['country_full'] = $country_list[$row['country']] ?? $row['country'];

?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
        <script type="text/javascript">
//<![CDATA[
var data = <?=json_encode($row)?>;
if(window.opener.setMemberData)
	window.opener.setMemberData(data);
else
	alert('Unable to send customer to inquiry form');
window.close();
//]]>
        </script>
    </head>
    <body>
    </body>
</html>
<?php
}
