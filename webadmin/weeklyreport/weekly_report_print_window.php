<?php
include_once __DIR__ . '/common.inc.php';

include 'weekly_report_email.php';
include 'weekly_report_date_functions.inc.php';

$ids                   = $_GET['ids'];
$start                 = startOfWeek($somedate);
$startyear             = substr($start, 0, 4);
$startmonth            = substr($start, 5, 2);
$startday              = substr($start, 8, 2);
$startdate             = $startday . "/" . $startmonth . "/" . $startyear;
$end                   = endOfWeek($somedate);
$endyear               = substr($end, 0, 4);
$endmonth              = substr($end, 5, 2);
$endday                = substr($end, 8, 2);
$enddate               = $endday . "/" . $endmonth . "/" . $endyear;
$fancydate             = $startdate . ' - ' . $enddate;
$fields['id']          = 'left';
$fields['user']        = 'left';
$fields['date']        = 'left';
$fields['Title']       = 'left';
$fields['Description'] = 'left';
$toname                = $DB->query_first("SELECT * FROM `highwaygroup`.`websites` WHERE `id` = '" . $to . "'");
$fromname              = $DB->query_first("SELECT * FROM `highwaygroup`.`websites` WHERE `id` = '" . $from . "'");

?>
<html>
<head>
<title>Weekly <?=$fromname['website']?> Report for Work On <?=$toname['website']?></title>
<style type="text/css">
td.white	{ color:#000; background:#ccc; }
</style>
<script type="text/javascript">
//<![CDATA[
function printpage()
{
	if (window.print) window.print();
	else alert('To print this page, select Print Frame from the file menu.');
}
function doPrint()
{
	if (confirm("Click OK to print this document")) printpage();
}
function selectAll(formObj, isInverse)
{
	for (var i=0;i < formObj.length;i++)
	{
		fldObj = formObj.elements[i];
		if (fldObj.type == 'checkbox')
		{
			if(isInverse) fldObj.checked = (fldObj.checked) ? false : true;
			else fldObj.checked = true;
		}
	}
}
//]]>
</script>
</head>
<body>
<?php
function drawreport()
{
    global $DB, $ids, $fields;
    $statement = "SELECT *, DATE_FORMAT(`date`, '%Y%m%d') AS `date` FROM `highwaygroup`.`weekly_report` WHERE `id` IN(" . $ids . ")";
    if (!isset($order)) {
        $order = 'asc';
    }

    if (!isset($by)) {
        $by = 'date';
    }

    $order_by = " ORDER BY $by $order";
    $result   = $DB->query($statement . $order_by);
    draw_header_row2();
    $shaded = true;
    while ($row = $DB->fetch_array($result)) {
        echo show_result_string($row, $shaded = !$shaded);
    }
    draw_header_end2();
}
function draw_header_row2()
{
    global $somedate, $from, $to, $toname, $fromname, $fancydate;
    $start      = startOfWeek($somedate);
    $startyear  = substr($start, 0, 4);
    $startmonth = substr($start, 5, 2);
    $startday   = substr($start, 8, 2);
    $startdate  = $startday . "/" . $startmonth . "/" . $startyear;
    $end        = endOfWeek($somedate);
    $endyear    = substr($end, 0, 4);
    $endmonth   = substr($end, 5, 2);
    $endday     = substr($end, 8, 2);
    $enddate    = $endday . "/" . $endmonth . "/" . $endyear;
    echo "<h2><u><center> Weekly " . $fromname['website'] . " Report For " . $toname['website'] . "</u></center></h2>";
    if ('' == $somedate) {
        $today = getdate();
        $month = $today['month'];
        $mday  = $today['mday'];
        $year  = $today['year'];
        $today = $mday . "/" . $month . "/" . $year;
        echo "<h3>Mixed week report of work done for " . $fromname['website'] . "</h3>";
        //echo "<h3> Report created on the $today </h3>";

    } else {
        echo "<h3><center>" . $fancydate . "</center></h3>";
        //echo "<h3><center> Report created on the $today </center></h3>";

    }
    echo "<form>";
    echo "<table width='500px' align='center' cellpadding='0' cellspacing='0' border='1' name='listTable' id='listTable'>";
}
function draw_header_end2()
{
    echo '</table>';
    echo "<br>";
    echo "</form>";
}
function show_result_string($cols, $shaded)
{
    global $fields, $actions, $script;
    global $order, $by, $query, $max, $at, $page, $user;
    //$string = '';
    reset($fields);
    $id = $cols[key($fields)];
    //echo "<tr>";
    for ($i = 0; $i < sizeof($fields); $i++) {
        $field = key($fields);
        if ('id' != $field) {
            if ('user' == $field) {
                $string .= "<table align='center' width='475px' cellpadding='0' cellspacing='0' border='1'>";
                $string .= "<td>";
                $string .= "<table align='center' width='125px' cellpadding='6' cellspacing='0' border='0'>";
                $string .= "<td valign='top' align='center' class='white'><b>";
                $output = stripslashes(niceusername($cols[key($fields)]));
                $output = nl2br($output);
                $string .= $output . "</b></td>\r\n";
                $string .= "</table>";
            }
            if ('Title' == $field) {
                $string .= "<td>";
                $string .= "<table align='center' width='475px' cellpadding='6' cellspacing='0' border='0'>";
                $string .= "<td class='white' align='center'><b>";
                $output = stripslashes($cols[key($fields)]);
                $output = nl2br($output);
                $string .= $output . "</b></td>\r\n";
                $string .= "</table>";
                $string .= "</td>";
                $string .= "</table>";
            }
            if ('Description' == $field) {
                $string .= "<td>";
                $string .= "<table width='607px' align='center' cellpadding='6' cellspacing='0' border='1'><tr >";
                $string .= "<td align='Left' colspan='2' >";
                $output = stripslashes($cols[key($fields)]);
                $output = nl2br($output);
                $string .= $output . '<p><br></td>';
                $string .= "</tr></table>";
                //echo "<tr><td colspan='2' >&nbsp;</td></tr>\r\n";
                $string .= "</td>";
            }
        }
        next($fields);
    }
    return $string;
}
function listemails()
{
    global $DB, $email, $from, $to, $somedate, $ids, $fancydate;
    echo '<table align="center"><form action="weekly_report_print_window.php?email=' . $email . '&from=' . $from . '&to=' . $to . '&somedate=' . $somedate . '&ids=' . $ids . '&action=email&fancydate=' . $fancydate . '" method="post">';

    $section_id = 0;
    if ($row = $DB->query_first(sprintf(
        "SELECT `section_id` FROM `highwaygroup`.`admin_sections` WHERE `sect_key` = 'REPORT' LIMIT 1"
    ), MYSQLI_ASSOC)) {
        $section_id = $row['section_id'];
    }

    if ($result = $DB->query(sprintf(
        "SELECT DISTINCT `user` FROM `highwaygroup`.`admin_users` WHERE `section_id` = %d",
        $section_id
    ))) {
        while ($row = $DB->fetch_assoc($result)) {
            echo '<tr><td align="center">';
            $name = $DB->query_first("SELECT `user` FROM `highwaygroup`.`users` WHERE `user` = '" . $row['user'] . "'");
            echo "<INPUT TYPE='CHECKBOX' Name=namearray[] Value='" . $name['user'] . "'>" . niceusername($row['user']) . "</input>";
            //echo "<INPUT TYPE='CHECKBOX' value='namearray[".$name['user']."][".$name['email']."]'>".niceusername($row['user'])."</input>";
            echo '</td><tr>';
        }
        $DB->free_result($result);
    }
    echo '<tr><td align="center"><input type="submit" value="Email to Selected"></input></td></tr>';
    echo '</table></form>';
}
if ('email' == $action) {
    emailreports($ids, $fancydate, $from, $to, $namearray);
} else {
    if ('' != $ids) {
        if ($email) {
            listemails();
        }
        drawreport();
    } else {
        echo "You must select items to before they can be printed";
    }
}
?>
<?php
if (!isset($action) && !$email) {
    echo '<script defer LANGUAGE="JavaScript">doPrint();selectAll(document.forms[0],0)</script>';
}
if ($email && !isset($action)) {
    echo '<script defer LANGUAGE="JavaScript">selectAll(document.forms[0],0)</script>';
}
?>
</body>
</html>