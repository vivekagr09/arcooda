<?php
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;
global $DB;

include __DIR__ . '/coinop_report_date_functions.inc.php';
?>
<html>
<head>
<title>
	Coinop Express Report to Highwaygames
</title>
<style type="text/css">
	td.white { color:#000000; background:#CCCCCC}
</style>
<script LANGUAGE="JavaScript">
//<![CDATA[
function printpage() {
    if (window.print) {
        window.print();
    } else {
        alert('To print this page, select Print Frame from the file menu.');
    }
}
function doPrint() {
    if (confirm("Click OK to print this document")) {
        printpage();
    }
}
//]]>
</script>
</head>
<body>

<?php
$ids = $_GET['ids'];
if ('' != $ids) {
    $fields['id']          = 'left';
    $fields['user']        = 'left';
    $fields['date']        = 'left';
    $fields['Title']       = 'left';
    $fields['Description'] = 'left';
    $statement             = "SELECT * FROM coinop_report WHERE id IN(" . $ids . ")";

    //if max is not yet set
    if (!isset($max)) {
        $result = $DB->query($statement);
        $max    = $DB->num_rows($result);
    }
    $inc = 25;
    $at  = intval($_REQUEST['at']);

    if (!isset($order)) {
        $order = 'asc';
    }

    if (!isset($by)) {
        $by = 'date';
    }

    $order_by = " ORDER BY $by $order";
    $limit    = " LIMIT $at, $inc ";

    //echo $statement.$extra;
    $result = $DB->query($statement . $order_by . $limit);

    //show_links2(get_links($max, "$script?$date=".urlencode($query)."&max=$max&by=$by&order=$order",25,4));

    draw_header_row2();
    $shaded = true;
    while ($row = $DB->fetch_array($result)) {
        show_result2($row, $shaded = !$shaded);
    }
    draw_header_end2();
} else {
    echo "You must select items to before they can be printed";
}

/**
 * @return void
 */
function draw_header_row2()
{
    global $somedate;
    $start      = startOfWeek($somedate);
    $startyear  = substr($start, 0, 4);
    $startmonth = substr($start, 5, 2);
    $startday   = substr($start, 8, 2);
    $startdate  = $startday . "/" . $startmonth . "/" . $startyear;

    $end      = endOfWeek($somedate);
    $endyear  = substr($end, 0, 4);
    $endmonth = substr($end, 5, 2);
    $endday   = substr($end, 8, 2);
    $enddate  = $endday . "/" . $endmonth . "/" . $endyear;
    echo "<h2><u><center> Coinopexpress Report to Highwaygames </u></center></h2>";

    if ('' == $somedate) {
        $today = getdate();
        $month = $today['month'];
        $mday  = $today['mday'];
        $year  = $today['year'];
        $today = $mday . "/" . $month . "/" . $year;

        echo "<h3>Mixed week report of work done for Highwaygames </h3>";
        //echo "<h3> Report created on the $today </h3>";

    } else {
        $today = getdate();
        $month = $today['month'];
        $mday  = $today['mday'];
        $year  = $today['year'];
        $today = $mday . "/" . $month . "/" . $year;
        echo "<h3><center> $startdate	-	 $enddate </center></h3>";
        //echo "<h3><center> Report created on the $today </center></h3>";
    }
    echo "<form accept-charset=\"utf-8\" autocomplete=\"off\" method=\"post\">";
    echo "<table width='500px' align='center' cellpadding='0' cellspacing='0' border='1' name='listTable' id='listTable'>";
}

/**
 * @return void
 */
function draw_header_end2()
{
    echo '</table>';
    echo "<br>";
    echo "</form>";
}

/**
 * @param string[] $cols
 * @param bool     $shaded
 *
 * @return void
 */
function show_result2($cols, $shaded)
{
    global $fields, $actions, $script;
    global $order, $by, $query, $max, $at, $page, $user;

    reset($fields);
    $id = $cols[key($fields)];
    //echo "<tr>";
    for ($i = 0; $i < sizeof($fields); $i++) {
        $field = key($fields);
        if ('id' != $field) {
            if ('user' == $field) {
                echo "<table align='center' width='475px' cellpadding='0' cellspacing='0' border='1'>";
                echo "<td>";
                echo "<table align='center' width='125px' cellpadding='6' cellspacing='0' border='0'>";
                echo "<td valign='top' align='center' class='white'><b>";
                $output = stripslashes($cols[key($fields)]);
                $output = str_replace("\n", "<br />", $output);
                echo $output . "</b></td>\r\n";
                echo "</table>";
            }
            if ('Title' == $field) {
                echo "<td>";
                echo "<table align='center' width='475px' cellpadding='6' cellspacing='0' border='0'>";
                echo "<td class='white' align='center'><b>";
                $output = stripslashes($cols[key($fields)]);
                $output = str_replace("\n", "<br />", $output);
                echo $output . "</b></td>\r\n";
                echo "</table>";
                echo "</td>";
                echo "</table>";
            }
            if ('Description' == $field) {
                echo "<td>";
                echo "<table width='607px' align='center' cellpadding='6' cellspacing='0' border='1'><tr >";
                echo "<td align='Left' colspan='2' >";
                $output = stripslashes($cols[key($fields)]);
                $output = str_replace("\n", "<br />", $output);
                echo $output . '<p><br></td>';
                echo "</tr></table>";
                //echo "<tr><td colspan='2' >&nbsp;</td></tr>\r\n";
                echo "</td>";
            }

        }

        next($fields);
    }
}
?>
</body>
<script language="javascript">
	doPrint();
</script>
</html>