<?php
$popup_window=(isset($_GET['return_message']))?true:false;

if(!$popup_window)
    include_once($_SERVER['DOCUMENT_ROOT'].'/webadmin/header.inc.php');
else
{
?>
<html>
<head>
<style type="text/css">
body,p,td
{
    font-family:Arial;
    font-size:12px;
}
a:hover
{
    color:#FF0000;
}
</style>
<link rel="stylesheet" type="text/css" href="/webadmin/admin.css" />
<script type="text/javascript" src="//code.jquery.com/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="/webadmin/jscript/common.php.js"></script>
<script type="text/javascript" src="/webadmin/jscript/tooltip.1.0.2.js"></script>
</head>
<?php
}
?>
<script>
var need_to_add_shipping=false;
var need_to_add_notify=false;
</script>
<style>
td.quarter { width:25%; }
td.left_input { width:20%; }
td.right_input { width:30%; }
td.right_input input, td.right_input textarea, td.right_input select { width:98%; }
</style>
<body onload="">

<link rel="stylesheet" type="text/css" href="/webadmin/survey/css/style.css" />

<div>
	<!-- Header -->
    <table cellspacing="2" cellpadding="2" style="width:100%;">
        <tr>
            <td class="pdx10" style="text-align:center;font-weight:bold;width:20%;background-color:#eeeeee;">
              Survey
            </td>

            <td class="pdx10 printHide" style="text-align:right;width:80%;background-color:#eeeeee;">
                <a href="index.php?action=add_survey">Add New</a> |
                <a href="index.php?action=view_survey">View All</a>
            </td>
        </tr>
    </table>
</div>


<div style="text-align:left;">
    <a href="#" onclick="javascript:toggleSection('sectionSearchMembers');return false;"><img name="imEx" src="/webadmin/images/triDown.gif" alt="Expand/Collapse All" border="0" height="16" width="16"> <b>Search Surveys</b></a>
    <br><br>
    <div id="sectionSearchMembers" class="child" style="display: none;">
<style type="text/css">
table.search_table { width:95%; margin:auto; border-collapse: collapse;}
table.search_table td { background-color:#FFFFCC; text-align:center; }
table.search_table td.left {border: none;text-align:left; padding-left:55px; width:35%; font-size:12px; text-align: right; }
</style>
<script type="text/JavaScript">
function toggleSiteSelected(site_id)
{
    var all_tick=(site_id=='');
    var elems=getElements('site_id[]');
    for(i=0;i<elems.length;i++)
    {
        if(elems[i].value=='') elems[i].checked=all_tick;
        else if(all_tick) elems[i].checked=false;
    }
}
</script>
<form action="index.php" method="post">
    <table class="search_table" style="border:1px solid #FF9900;">
        <tbody>
            <tr>
                <td colspan="2" style="padding-bottom:10px;">
                    <h2 style="color:#FF9900;margin:2px;">Survey Search</h2>
                </td>
            </tr>
            <tr>
                <td class="left">Survey Name :</td>
                <td><input type="text" style="width:97%;" name="search_for" value=""></td>
            </tr>
    
            <tr><td colspan="2"><input type="submit" value="Search"></td></tr>
        </tbody>
    </table>
</form>
</div>
    <div id="sectionSearchInvoices_hideNote" class="child"></div>
</div>