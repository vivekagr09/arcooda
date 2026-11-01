<?php
/******************************************************************************
 ** Title.........:
 ** Version.......:
 ** Author........:    Bede Pavan <bpavan@highwaygroup.net>
 ** Filename......:
 ** Last changed..:    20/5/2003
 ** Notes.........:
 *****************************************************************************/

$section_code = "ALL_JOBS";
include_once __DIR__ . '/common.inc.php';

moduleLoad('invoice'); //invoiceGetValue

include_once __DIR__ . '/header.inc.php';

include __DIR__ . '/functions/quote_functions.inc.php';

if ("" != $_GET['view_user'] && has_permission('ALL_JOBS', $allowed_sections)) {
    $anotherUser = true;
    $user        = $_GET['view_user'];
}
$user   = ucwords(strtolower($user));
$gets   = get_vars();
$action = $_REQUEST['action'];
$script = 'jobs_admin.php';
$type   = $_GET['type'];

switch ($action) {
    case "do_new_message":
        $errorMsg = validateNewMessage();
        if ($errorMsg) {
            $includeEditForm = true;
        }
        // include __DIR__ . '/jobs_edit_form.inc.php';
        else {
            doNewMessage();
        }

        break;
    case "do_add":
        $errorMsg = validateAdd();
        if ($errorMsg) {
            $includeAddForm = true;
        }
        // include __DIR__ . '/jobs_add_form.inc.php';
        else {
            doAdd();
        }

        break;
    case "do_close_job":
        doCloseJob();
        break;
    case "do_update":
        doUpdate();
        break;
}

$sql = "CREATE TEMPORARY TABLE IF NOT EXISTS min_max_job_ids (job_id INT(5),min_id INT(5),max_id INT(5))";
$DB->query($sql);

$sql = "INSERT INTO min_max_job_ids (job_id,min_id,max_id)
		SELECT job_id, MIN(id) AS min_id, MAX(id) AS max_id
		FROM jobs_details
		GROUP BY job_id";
$DB->query($sql);

// COUNT THE NUMBER OF OPEN JOBS THAT THE USER CURRENTLY HAS WITH THEM
// INCLUDING JOBS THAT THEY CREATED WHICH HAVEN'T CLOSED
$sql = "SELECT COUNT(*)
		FROM jobs j
		LEFT JOIN jobs_details jd ON jd.job_id = j.job_id
		LEFT JOIN min_max_job_ids mj ON mj.job_id = j.job_id
		WHERE ((jd.receiver='$user' AND jd.id = mj.max_id)  OR (jd.sender='$user' AND jd.id=mj.min_id)) AND j.status='open'";
$temp    = $DB->query_first($sql);
$numOpen = intval($temp[0]);

// COUNT THE NUMBER OF JOBS THAT THE USER SENT AND RECEIVED THAT AREN'T CURRENTLY WITH THEM
$sql = "SELECT COUNT(*)
		FROM jobs j
		LEFT JOIN jobs_details jd ON jd.job_id = j.job_id
		LEFT JOIN min_max_job_ids mj ON mj.job_id = j.job_id
		WHERE (jd.receiver='$user' AND jd.id != mj.max_id) OR jd.sender='$user'";
$temp      = $DB->query_first($sql);
$numClosed = intval($temp[0]);

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
          <td class="pageMenu" width="20%">
			<div align="center">
			  <?=$user . "'s " . ucwords($type) . " Jobs";?>
			</div>
		  </td>
          <td class="pageMenu" width="80%">
            <div align="right">
			<a href="<?=$script;?>?action=add">Add New Job</a> |
			<a href="<?=$script;?>?type=open<?=$anotherUser == true ? "&view_user=$user" : "";?>">Open Jobs(<?=$numOpen;?>)</a> |
			<a href="<?=$script;?>?type=closed<?=$anotherUser == true ? "&view_user=$user" : "";?>">Processed Jobs(<?=$numClosed;?>)</a>
			</div>
          </td>
        </tr>
		<tr><td colspan="2">&nbsp;</td></tr>
      </table><br>
<!-- search box

  <p align="right">
    <input type="text" name="query" size="20" value="<?=htmlspecialchars(stripslashes($query));?>">
    <input type="submit" name="search" value="Search">
   </p>
//search box -->
<?php
if (trim($query) == '') {
    switch ($action) {
        case "add":
            include __DIR__ . '/jobs_add_form.inc.php';
            break;
        case "edit":
            include __DIR__ . '/jobs_edit_form.inc.php';
            break;
    }
    if ($includeAddForm) {
        include __DIR__ . '/jobs_add_form.inc.php';
    } else if ($includeEditForm) {
        include __DIR__ . '/jobs_edit_form.inc.php';
    }

}

if (!isset($_REQUEST['action']) || (isset($_REQUEST['action']) && "" == $_REQUEST['action'])) {
    $actions[] = '<a href=\"$script?action=edit&type=' . $_GET[type] . '&id=$jobID&query=$query&max=$max&by=$by&order=$order&at=$at&page=$page\">View/Edit</a>';
    if ("open" == $type) {
        showOpenJobs();
    } else if ("closed" == $type) {
        showClosedJobs();
    } else {
        showOpenJobs();
        showClosedJobs();
    }
    $sql = "DROP TABLE IF EXISTS min_max_job_ids";
    $DB->query($sql);
}

function showOpenJobs()
{
    global $DB, $user, $query;

//    $fields['job_id'] = "center";
    //    $fields['id'] = "center";
    $fields['date_added'] = 'center';
    $fields['priority']   = 'center';
    $fields['topic']      = 'center';
    $fields['quote_id']   = 'center';
    $fields['started_by'] = 'center';
    $fields['sender']     = 'center';
    $fields['receiver']   = 'center';
    $fields['status']     = 'center';
    $fields['days']       = 'center';
    $fields['days_open']  = 'center';
    $GLOBALS['fields']    = $fields;

    // GET OPEN JOBS THAT THE USER CURRENTLY HAS WITH THEM
    // INCLUDING JOBS THAT THEY CREATED WHICH HAVEN'T CLOSED
    $sql = "SELECT *, TO_DAYS(NOW()) - TO_DAYS(jd.date_added) AS days
			FROM jobs j
			LEFT JOIN jobs_details jd ON jd.job_id = j.job_id
			LEFT JOIN jobs_priority jp ON jd.priority_id = jp.priority_id
			LEFT JOIN min_max_job_ids mj ON mj.job_id = j.job_id
			WHERE ((jd.receiver='$user' AND jd.id = mj.max_id) OR (jd.sender='$user' AND jd.id=mj.min_id))
			AND j.status='open'";

    showTitleStr("Open Jobs", "center", "left", 90);
    $query = htmlspecialchars($_REQUEST['query']);
    searchJobs($sql, addslashes($query));
}

function showClosedJobs()
{
    global $DB, $user, $query;

//    $fields['job_id'] = "center";
    //    $fields['id'] = "center";
    $fields['date_added'] = 'center';
    $fields['topic']      = 'center';
    $fields['quote_id']   = 'center';
    $fields['started_by'] = 'center';
    $fields['sender']     = 'center';
    $fields['receiver']   = 'center';
    $fields['status']     = 'center';
    $fields['days']       = 'center';
    $fields['days_open']  = 'center';
    $GLOBALS['fields']    = $fields;

    // GET JOBS THAT THE USER SENT AND RECEIVED THAT AREN'T CURRENTLY WITH THEM
    $sql = "SELECT *, TO_DAYS(jd.date_completed) - TO_DAYS(jd.date_added) AS days
			FROM jobs j
			LEFT JOIN jobs_details jd ON jd.job_id = j.job_id
			LEFT JOIN jobs_priority jp ON jd.priority_id = jp.priority_id
			LEFT JOIN min_max_job_ids mj ON mj.job_id = j.job_id
			WHERE (jd.receiver='$user' AND jd.id != mj.max_id) OR jd.sender='$user'";

    showTitleStr("Processed Jobs", "center", "left", 90);
    $query = htmlspecialchars($_REQUEST['query']);
    searchJobs($sql, addslashes($query));
}

function searchJobs($sql, $query)
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
            $extra .= "(LOWER(jd.job_id) REGEXP \"$word\")"; //just a normal regular exp. search
        }
    } //end search query keywords

    $statement = $sql;

    //if max is not yet set
    if (!isset($max)) {
        $result = $DB->query($statement . $extra);
        $max    = $DB->num_rows($result);
    }

    $inc = 25;
    $at  = intval($at);
    if (!isset($order) || "" == $order) {
        $order = 'DESC';
    }

    if (!isset($by) || "" == $by) {
        $by = 'jd.date_added';
    }

    $order_by = " ORDER BY $by $order";
    $limit    = " LIMIT $at, $inc ";
    $result   = $DB->query($statement . $extra . $order_by . $limit);

//    show_links(get_links($max, "$script?type=".$_GET['type']."&query=".urlencode($query)."&max=$max&by=$by&order=$order",25,4));
    if (!$DB->num_rows($result) > 0) {
        echo "<div align=\"center\"><b>None</b></div>\r\n";
    } else {
        drawHeaderRow();
        $shaded = true;
        while ($row = $DB->fetch_array($result)) {
            showResult($row, $shaded = !$shaded);
        }
        drawHeaderEnd();
    }
//    show_links(get_links($max, "$script?type=".$_GET['type']."&query=".urlencode($query)."&max=$max&by=$by&order=$order",25,4));
}

function drawHeaderRow()
{
    global $fields, $order, $by, $query, $max, $at, $page, $script;

    reset($fields);
    echo "<br>";
    echo '<table align="center" cellspacing="1" cellpadding="5">';
    echo '<tr bgcolor="#000066">';
    for ($i = 0; $i < sizeof($fields); $i++) {
        $field        = key($fields);
        $fieldDisplay = $field;
//        if($field == "creator")
        //            $fieldDisplay = "fromuser";
        echo '<td align="center" class="tableHeader">';
        if (!("days_open" == $field || "started_by" == $field)) {
            echo "<a href=\"$script?type=" . $_GET['type'] . "&query=" . urlencode($query) . "&max=$max&at=$at&page=$page";
            echo "&by=$field&order=" . ("desc" == $order ? "asc" : "desc") . '">' . $fieldDisplay . '</a>';
        } else {
            echo $fieldDisplay;
        }

        if ($by == $field) {
            echo '  <img src="' . $order . '.gif" width="7" height="7" border="0">  ';
        }

        echo "</td>\r\n";
        next($fields);
    }
    echo "<td class=\"tableHeader\">&nbsp;</td>";
    echo "</tr>";
}

function drawHeaderEnd()
{
    echo '</table>';
    echo "<br>";
}

function showResult($cols, $shaded)
{
    global $fields, $actions, $script, $table, $DB, $user;

    reset($fields);
    $jobID = $cols['id'];
//    $locked = locked($table, $jobID);
    //    if ($locked)
    //        echo '<tr bgcolor="pink">';
    //    else

    $class = $shaded ? 'class="tableRowAlt"' : 'class="tableRow"';
    echo "<tr $class>\r\n";

    for ($i = 0; $i < sizeof($fields); $i++) {
        if (key($fields) == "priority") {
            if (intval($cols['priority_id']) == 3) {
                $color = "bgcolor=\"#FFFF00\"";
            } else if (intval($cols['priority_id']) == 2) {
                $color = "bgcolor=\"#FF8000\"";
            } else if (intval($cols['priority_id']) == 1) {
                $color = "bgcolor=\"#FF0000\"";
            }

        }
        echo "<td align=\"" . $fields[key($fields)] . "\" $color>";
        if (key($fields) == 'quote_id') {
            if ($cols['quote_id'] > 0) {
                if (function_exists('invoiceGetValue')) {
                    echo invoiceGetValue($cols['quote_id'], 'invoice_number') ?? ("Q#" . padQuoteNum($cols[key($fields)]));
                } else {
                    echo "Q#" . padQuoteNum($cols[key($fields)]);
                }
            } else {
                echo "N/A";
            }
        } else if (key($fields) == 'started_by') {
            $sql = "SELECT jd.sender FROM jobs_details jd
					LEFT JOIN min_max_job_ids mj ON jd.job_id=mj.job_id
					WHERE jd.job_id='" . $cols['job_id'] . "' AND jd.id=mj.min_id";
            $temp = $DB->query_first($sql);
            echo $temp['sender'];
        } else if (key($fields) == 'days_open') {
            if ('open' == $cols['status']) {
                $sql = "SELECT TO_DAYS(NOW()) - TO_DAYS(date_added) AS days_open
					FROM jobs
					WHERE job_id='" . $cols['job_id'] . "'";
                $result = $DB->query_first($sql);
                if (is_numeric($result['days_open'])) {
                    echo $result['days_open'];
                } else {
                    echo "NA";
                }

            } else {
                $sql = "SELECT TO_DAYS(date_completed) - TO_DAYS(date_added) AS days_open
					FROM jobs
					WHERE job_id='" . $cols['job_id'] . "'";
                $result = $DB->query_first($sql);
                if (is_numeric($result['days_open'])) {
                    echo $result['days_open'];
                } else {
                    echo "NA";
                }

            }
        } else if (key($fields) == 'date_added') {
            echo formatDate(strtotime($cols['date_added']));
        } else {
            echo $cols[key($fields)];
        }

        echo "</td>\r\n";
        $color = "";
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

function doAdd()
{
    global $DB, $jobs, $user;

    $jobs     = add_slashes_array($jobs);
    $user     = ucwords(strtolower($user));
    $receiver = ucwords(strtolower($jobs['receiver']));

    if (-1 == $jobs['quote_id'] && -1 == $jobs['invoice_id']) {
        $qid = 'NULL';
    } else if (-1 != $jobs['quote_id'] && -1 == $jobs['invoice_id']) {
        $qid = $jobs['quote_id'];
    } else if (-1 == $jobs['quote_id'] && -1 != $jobs['invoice_id']) {
        $qid = $jobs['invoice_id'];
    }

    $timestamp = time();
    $sql       = "INSERT INTO jobs (quote_id,topic,status,date_added) VALUES($qid,'" . $jobs['topic'] . "','open',FROM_UNIXTIME($timestamp))";
    $DB->query($sql);

    $jobID = $DB->query_first("SELECT MAX(job_id) FROM jobs");
    $jobID = $jobID[0];

    $sql = "INSERT INTO jobs_details (job_id,priority_id,sender,receiver,description,date_added)
			VALUES ($jobID," . $jobs['priority_id'] . ",'$user','$receiver','" . $jobs['description'] . "',FROM_UNIXTIME($timestamp))";
    $DB->query($sql);

    echo "<div><font color=\"#FF000\"><b>New Job created.</b></font></div>\r\n";
}

function validateAdd()
{
    $errorMsg = "";
    $jobs     = $_POST['jobs'];
//    echo "QID: ".$jobs['quote_id']." - IID: ".$jobs['invoice_id']."<br>";
    if ("" == $jobs['receiver']) {
        $errorMsg .= "<li>Please select a person to assign this job to.</li>\r\n";
    }

    if (intval($jobs['quote_id']) != -1 && intval($jobs['invoice_id']) != -1) {
        $errorMsg .= "<li>Please select a Quote ID OR an Invoice ID and NOT both.</li>\r\n";
    }

    if (trim($jobs['topic']) == "") {
        $errorMsg .= "<li>Please enter a Title.</li>\r\n";
    }

    if (trim($jobs['description']) == "") {
        $errorMsg .= "<li>Please enter a Description.</li>\r\n";
    }

    return $errorMsg;
}

function doUpdate()
{
    global $DB, $jobs, $id;

    $sql = "UPDATE jobs_details SET description='" . addslashes($jobs['description']) . "' WHERE id='$id'";
    $DB->query($sql);
    echo "<div><font color=\"#FF0000\"><b>Job Updated.</b></font></div>\r\n";
}

function validateNewMessage()
{
    $errorMsg = "";
    $jobs     = $_POST['jobs'];
    if ("" == $jobs['receiver']) {
        $errorMsg .= "<li>Please select a Recipient.</li>\r\n";
    }

    if (trim($jobs['description']) == "") {
        $errorMsg .= "<li>Please enter a Description.</li>\r\n";
    }

    return $errorMsg;
}

function doNewMessage()
{
    global $DB, $jobs, $id;

    $sql = "UPDATE jobs_details SET date_completed=NOW() WHERE id='$id'";
    $DB->query($sql);

    $sql = "INSERT INTO jobs_details (job_id,priority_id,sender,receiver,description,date_added)
			VALUES (" . $jobs['job_id'] . "," . $jobs['priority_id'] . ",'" . ucwords(strtolower($jobs['sender'])) .
    "','" . $jobs['receiver'] . "','" . addslashes($jobs['description']) . "',NOW())";
    $DB->query($sql);

    echo "<div><font color=\"#FF000\"><b>New Message Created.</b></font></div>\r\n";
}

function doCloseJob()
{
    global $DB, $jobs, $id;

    $closedMsg      = "User " . ucwords(strtolower($jobs['sender'])) . ", has now closed your job.";
    $temp           = $DB->query_first("SELECT sender FROM jobs_details WHERE job_id='" . $jobs['job_id'] . "' ORDER BY id");
    $originalSender = $temp['sender'];

    // SEND CLOSING MESSAGE TO JOB CREATOR IF CREATOR DIDN'T CLOSE THE JOB THEMSELVES
    //    echo "ORIG = $originalSender : SENDER = ".$jobs['sender']."<br>";
    if (strcasecmp($originalSender, $jobs['sender']) != 0) {
        $sql = "INSERT INTO jobs_details (job_id,priority_id,sender,receiver,description,date_added)
				VALUES (" . $jobs['job_id'] . ",3,'" . ucwords(strtolower($jobs['sender'])) .
            "','$originalSender','$closedMsg',NOW())";
        $DB->query($sql);
    }
    $timestamp = time();
    $DB->query("UPDATE jobs_details SET date_completed=FROM_UNIXTIME($timestamp) WHERE id='$id'");
    $DB->query("UPDATE jobs SET status='closed',date_completed=FROM_UNIXTIME($timestamp) WHERE job_id=" . $jobs['job_id']);

    echo "<div><font color=\"#FF000\"><b>Job ID: " . $jobs['job_id'] . " Closed.</b></font></div>\r\n";
}
?>
</form>
<!--//jobs admin -->
<?php
include_once __DIR__ . '/footer.inc.php';
