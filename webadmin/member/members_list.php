<?php
/**
 * @var mixed[] $posted
 * @var mixed[] $get_vars
 */
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;
global $DB;

include_once __DIR__ . '/header.inc.php';

$order    = (array_key_exists('order', $posted) && 'asc' == $posted['order']) ? 'asc' : 'desc';
$order_by = (array_key_exists('order_by', $posted)) ? strtolower($posted['order_by']) : 'member_id';
$page_id  = (array_key_exists('page_id', $posted)) ? max(0, intval($posted['page_id'])) : 0;

$criteria = array();
foreach (array('member_id', 'country', 'status', 'flag', 'account_type', 'mailing_list', 'reseller', 'partner', 'best_described', 'currency', 'products_per_page', 'news_per_page', 'mailing_products', 'last_initial', 'city', 'site_id', 'spammer', 'signup_IP') as $k) {
    if (array_key_exists($k, $posted)) {
        if (is_array($posted[$k])) {
            $criteria[$k] = $posted[$k];
        } else {
            $criteria[$k] = preg_split("/[^a-zA-Z0-9@_\\.]+/", $posted[$k]);
        }

    }
}

if (array_key_exists('search_for', $posted) && !empty($posted['search_for'])) {
    $criteria['any_text'] = explode(' ', $posted['search_for']);
}

/*
// now have to sort out the site_id array - mainly for the search part..
if(is_array($posted['site_id'])) foreach($posted['site_id'] as $key=>$tmp)
if($tmp=='') unset($posted['site_id'][$key]);

if($posted['site_id']!='')
{
if(!is_array($posted['site_id']) && $posted['site_id']!='') $criteria['site_id']=explode('|',$posted['site_id']);
elseif(count($posted['site_id'])) $criteria['site_id']=$posted['site_id'];
}
 */

$sub_criteria = $criteria;
unset($sub_criteria["site_id"]);
$member_list = memberSearch($sub_criteria);
if (count($member_list) > 0) {
    $list_by1 = 'site_id';
    $list_by2 = null;
?>
<div style="text-align:left;">
	<a href="#" onclick="toggleSection('sectionViewByWebsite');return false"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>List by Website</b></a>
	<br /><br />
	<div id="sectionViewByWebsite" class="child">
<?php
include __DIR__ . '/templates/navigate.tpl.php';
?>
	</div>
</div>
<script type="text/javascript">
//<![CDATA[
$(function() {
	toggleSection('sectionViewByWebsite');
});
//]]>
</script>
<?php
}

$sub_criteria = $criteria;
unset($sub_criteria["country"]);
$member_list = memberSearch($sub_criteria);
if (count($member_list) > 0) {
    $list_by1 = 'country';
    $list_by2 = null;
?>
<div style="text-align:left;">
	<a href="#" onclick="toggleSection('sectionViewByCountry');return false"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>List by Country</b></a>
	<br /><br />
	<div id="sectionViewByCountry" class="child">
<?php
include __DIR__ . '/templates/navigate.tpl.php';
?>
	</div>
</div>
<script type="text/javascript">
//<![CDATA[
$(function() {
	toggleSection('sectionViewByCountry');
});
//]]>
</script>
<?php
}

?>
<div style="text-align:left;">
	<a href="#" onclick="toggleSection('sectionSearchMembers');return false"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16" /> <b>Search Members</b></a>
	<br /><br />
	<div id="sectionSearchMembers" class="child" style="<?=(isset($posted['search_for'])) ? '' : 'display:none'?>">
<?php
include __DIR__ . '/templates/search_members.tpl.php';
?>
	</div>
	<div id="sectionSearchInvoices_hideNote" class="child"></div>
</div>
<?php

$items_per_page = 50;
if ($_GLOBALS['staff']['members_per_page'] > 0) {
    $items_per_page = $_GLOBALS['staff']['members_per_page'];
}

//Show By Country
$sub_criteria = $criteria;
unset($sub_criteria['country']);
$member_list = memberSearch($criteria);

if (array_key_exists('any_text', $criteria) && is_array($criteria["any_text"]) && count($criteria["any_text"])) {
    $whereAND = array();
    foreach ($criteria["any_text"] as $word) {
        if (trim($word) != "") {
            $whereOR = array();
            foreach (array("`email`") as $field) {
                $whereOR[] = $field . " LIKE " . formatSQLLikeString($word);
            }

            $whereAND[] = "(" . implode(" OR ", $whereOR) . ")";
            unset($whereOR);
        }
    }

    if (count($whereAND)) {
        if ($query = $DB->query(sprintf(
            "SELECT `member_id` FROM `highwaygroup`.`new_members_contacts` WHERE %s",
            implode(" AND ", $whereAND)
        ))) {
            while ($row = $DB->fetch_assoc($query)) {
                $member_list[] = $row["member_id"];
            }
            $DB->free_result($query);
        }

        $member_list = array_unique($member_list);
    }
    unset($whereAND);
}

if (array_key_exists('last_initial', $posted) && !empty($posted['last_initial'])) {
    $order_by = 'last_name';
    $order    = 'asc';
}
$member_list = memberSort($member_list, $order_by);
if ('desc' == $order) {
    $member_list = array_reverse($member_list);
}

$count_total   = count($member_list);
$member_list   = array_chunk($member_list, $items_per_page);
$count_pages   = count($member_list);
$page_id       = max(0, min($count_pages - 1, $page_id));
if (array_key_exists($page_id, $member_list)) {
    $member_list = $member_list[$page_id];
} else {
    $member_list = array();
}
$count_display = count($member_list);

$add_url           = $get_vars;
$add_url['action'] = 'add';
$add_url           = '.?' . http_build_query($add_url);

?>
<?=formatHTMLTitle('Members', '<i><a style="color:black;" href="' . htmlspecialchars($add_url) . '">Add New Member</a></i>')?>
<?php

if ($count_total > 0) {
    $page_url = $get_vars;
    unset($page_url['page_id']);
    $page_url   = '?' . http_build_query($page_url);
    $page_links = formatHTMLPageLinks($page_url . '&page_id=', $page_id, $count_display, $count_total, $items_per_page);

    $select_url = $get_vars;
    unset($select_url['action']);
    $select_url = 'members_select.php?' . http_build_query($select_url);
?>
<br />
<form action="<?=htmlspecialchars($select_url)?>" method="post">
<?php
include __DIR__ . '/templates/list_members.tpl.php';

    $initial_url           = $get_vars;
    $initial_url['action'] = 'list';
    unset($initial_url['page_id']);
    unset($initial_url['last_initial']);
    $initial_url = '.?' . http_build_query($initial_url) . '&last_initial=';
?>
</form>
<table class="wide">
	<tr><td style="background:#FAFAFA"><div style="text-align:center">
		<b>By Last Name :</b>
<?php
for ($i = 'a';'z' != $i; $i++) {
        if ($posted['last_initial'] == $i) {
?>
		<span style="background-color:#7FFFD4"><?=strtoupper($i)?></span>
<?php
} else {
?>
		<a href="<?=htmlspecialchars($initial_url . $i)?>"><?=strtoupper($i)?></a>
<?php
}
    }
?>
	</div></td></tr>
	<tr><td style="background:#FAFAFA"><div style="text-align:center">
		<?=$page_links?>
	</div></td></tr>
	<tr><td style="background:#FAFAFA"><div style="text-align:center">
		<input type="button" onclick="<?=htmlspecialchars('window.open(' . json_encode('members_list_xls.php?' . http_build_query($_GET)) . ')')?>" value="Export Excel" />
	</div></td></tr>
</table>
<br />
<?php
if ($popup_window) {
?>
<input type="button" onclick="window.close();" value="Close Window" />
<?php
}
} else {
?>
No results were found
<?php
}

include_once __DIR__ . '/footer.inc.php';
