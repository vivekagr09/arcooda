<?php
/******************************************************************************
 ** Title.........:
 ** Version.......:
 ** Author........:    Bede Pavan <bpavan@highwaygroup.net>
 ** Filename......:
 ** Last changed..:    22/5/2003
 ** Notes.........:
 *****************************************************************************/

$section_code = "ALL_JOBS";
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

include __DIR__ . '/functions/quote_functions.inc.php';

if (!has_permission('ALL_JOBS', $allowed_sections)) {
    echo "<br><font color=\"#FF0000\" size=\"+1\">You do not have permission to access this section</font>\r\n";
    exit();
}
$user = ucwords(strtolower($user));

$script = 'jobs_admin_all.php';

$fields['user']           = "left";
$fields['open_jobs']      = 'center';
$fields['processed_jobs'] = 'center';

$actions[] = '<a href=\"jobs_admin.php?view_user=$view_user&query=$query\">View</a>';

$gets   = get_vars();
$action = $_REQUEST['action'];

$sql = "CREATE TEMPORARY TABLE IF NOT EXISTS min_max_job_ids (job_id INT(5),min_id INT(5),max_id INT(5))";
$DB->query($sql);

$sql = "INSERT INTO min_max_job_ids (job_id,min_id,max_id)
		SELECT job_id, MIN(id) AS min_id, MAX(id) AS max_id
		FROM jobs_details
		GROUP BY job_id";
$DB->query($sql);

?>
<!-- jobs admin -->
<script language="JavaScript">
<!--
function clearSearch()
{
	document.admin_form.pref_text.value = "";
	document.admin_form.pref_id.value = "";
	document.admin_form.pref_text.disabled = false;
}
-->
</script>
<form accept-charset="utf-8" action="<?=$script . $gets;?>" method="post" name="admin_form">
      <table width="100%" border="0" cellspacing="2" cellpadding="2">
        <tr>
          <td width="20%" class="pageMenu">Administrator Jobs</td>
		  <td width="80%" class="pageMenu">&nbsp;</td>
        </tr>
		<tr><td colspan="2">&nbsp;</td></tr>
      </table>
<!-- search box -->
      <p align="right">
        <input type="text" name="query" size="20" value="<?=htmlspecialchars(stripslashes($query));?>">
        <input type="submit" name="search" value="Search">
      </p>
<!-- //search box -->
<?php

function searchJobs($query)
{
    global $DB, $at, $fields, $order, $by;
    global $script, $andString, $user, $where;

    $query = str_replace(['(', ')', '+', '?', '[', ']', '$', '^'], '', $query);

    //keywords
    $arrKeywords = explode(" ", trim($query));

    //search query by key words
    if (!empty($query)) {
        $extra = " ";
    }

    for ($index = 0; $index < count($arrKeywords); $index++) {
        $word = trim(strtolower($arrKeywords[$index]));

        if (!empty($word)) {
            $extra .= " AND ";
        }

        if (!empty($word)) {
            $extra .= "(LOWER(job_id) REGEXP \"$word\")"; //just a normal regular exp. search
        }
    } //end search query keywords

    $statement = "SELECT DISTINCT `user` FROM `highwaygroup`.`admin_users`";

    //if max is not yet set
    if (!isset($max)) {
        $result = $DB->query($statement . $extra);
        $max    = $DB->num_rows($result);
    }

    $inc = 25;
    $at  = intval($at);
    if (!isset($order) || "" == $order) {
        $order = 'ASC';
    }

    if (!isset($by) || "" == $by) {
        $by = 'user';
    }

    $order_by = " ORDER BY $by $order";
    $limit    = " LIMIT $at, $inc ";
    $result   = $DB->query($statement . $extra . $order_by . $limit);

    show_links(get_links($max, "$script?query=" . urlencode($query) . "&max=$max&by=$by&order=$order", 25, 4));
    drawHeaderRow();
    $shaded = true;
    while ($row = $DB->fetch_array($result)) {
        showResult($row, $shaded = !$shaded);
    }
    drawHeaderEnd();
    show_links(get_links($max, "$script?query=" . urlencode($query) . "&max=$max&by=$by&order=$order", 25, 4));
}

function drawHeaderRow()
{
    global $fields, $order, $by, $query, $max, $at, $page, $script;

    reset($fields);
    echo "<br>";
    echo '<table align="center" cellspacing="1" cellpadding="5">';
    echo '<tr class="tableHeader">';
    for ($i = 0; $i < sizeof($fields); $i++) {
        $field        = key($fields);
        $fieldDisplay = $field;
        if ("staff" == $field) {
            $field = "user";
        }

        echo '<td align="center">';
        if ("open_jobs" != $field && "processed_jobs" != $field) {
            echo "<a href=\"$script?query=" . urlencode($query) . "&max=$max&at=$at&page=$page";
            echo "&by=$field&order=" . ("desc" == $order ? "asc" : "desc") . "\">";
            echo "<b>$fieldDisplay</b></a>\r\n";
        } else {
            echo $fieldDisplay;
        }

        if ($by == $field) {
            echo " <img src=\"" . $order . ".gif\" width=\"7\" height=\"7\" border=\"0\">";
        }

        echo "</td>\r\n";
        next($fields);
    }
    echo "<td>&nbsp;</td>\r\n";
    echo "</tr>\r\n";
}

function drawHeaderEnd()
{
    echo '</table>';
    echo "<br>";
}

function showResult($cols, $shaded)
{
    global $fields, $actions, $script, $table, $DB;

    reset($fields);
    $view_user = $cols['user'];
    $tr        = $shaded ? '<tr class="tableRowAlt">' : '<tr class="tableRow">';
    echo $tr;

    for ($i = 0; $i < sizeof($fields); $i++) {
        echo "<td align=\"" . $fields[key($fields)] . "\">";
        if (key($fields) == 'open_jobs') {
            $sql = "SELECT COUNT(*)
					FROM jobs j
					LEFT JOIN jobs_details jd ON jd.job_id = j.job_id
					LEFT JOIN min_max_job_ids mj ON mj.job_id = j.job_id
					WHERE ((jd.receiver='" . $cols['user'] . "' AND jd.id = mj.max_id)  OR (jd.sender='" . $cols['user'] . "' AND jd.id=mj.min_id)) AND j.status='open'";
            $temp = $DB->query_first($sql);
            echo $temp[0];
        } else if (key($fields) == 'processed_jobs') {
            $sql = "SELECT COUNT(*)
					FROM jobs j
					LEFT JOIN jobs_details jd ON jd.job_id = j.job_id
					LEFT JOIN min_max_job_ids mj ON mj.job_id = j.job_id
					WHERE (jd.receiver='" . $cols['user'] . "' AND jd.id != mj.max_id) OR jd.sender='" . $cols['user'] . "'";
            $temp = $DB->query_first($sql);
            echo $temp[0];
        } else {
            echo $cols[key($fields)];
        }

        echo "</td>\r\n";
        next($fields);
    }
    echo '<td nowrap>';
    for ($i = 0; $i < sizeof($actions); $i++) {
        if (0 != $i) {
            echo " | ";
        }

        $str = $actions[$i];
        eval("\$str = \"$str\";");
        echo "$str";
    }
    echo '</td>';
    echo "</tr>";
}
//do a query search
$query = htmlspecialchars($_REQUEST['query']);
searchJobs(addslashes($query));
$sql = "DROP TABLE IF EXISTS min_max_job_ids";
$DB->query($sql)
;?>
</form>
<!--//jobs admin -->
<?php
include_once __DIR__ . '/footer.inc.php';
