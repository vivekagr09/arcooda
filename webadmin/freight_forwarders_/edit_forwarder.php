<?php
/********************************
 * Edit Member
 ********************************
 * A refresh of the Member/Customer section's edit page
 * and support code.
 ********************************
 * Author: Nathan Tomkins
 * Created: 20050412
 ********************************/

include_once __DIR__ . '/common.inc.php';

moduleLoad('template');

include_once "../new_suppliers/functions/country.inc.php";
include_once "../new_suppliers/functions/currency.inc.php";
include_once "../new_suppliers/functions/user.inc.php";

switch ($_REQUEST['action']) {
    case "add":
        $c          = showAdd();
        $c['title'] = "Add New Forwarder";
        break;
    case "edit":
        $c          = showEdit();
        $c['title'] = "Edit Forwarder";
        break;
    case "delete":
        $c = request_delete();
        break;
    case "do_add":
        //header("Location: http://{$_SERVER['HTTP_HOST']}/webadmin/freight_forwarders/forwarders_admin.php");
        $c = doAdd();
        break;
    case "do_edit":
        //header("Location: http://{$_SERVER['HTTP_HOST']}/webadmin/freight_forwarders/forwarders_admin.php"); 
        $c = doEdit();
        break;
    case "do_delete":
        header("Location: http://{$_SERVER['HTTP_HOST']}/webadmin/freight_forwarders/forwarders_admin.php");
        echo "Doing DELETE";
        $c = do_delete();
        break;
    case "cancel_delete":
        header("Location: http://{$_SERVER['HTTP_HOST']}/webadmin/freight_forwarders/forwarders_admin.php");

    case "delete_contact":
        delContact();
        //return 0;
        break;
    case "delete_comment":
        delComment();
        //return 0;
        break;
    case "delete_quote":
        delQuote();
        //return 0;
        break;
    case "delete_booking":
        delBooking();
        //return 0;
        break;
    //case "find_company":
    //    findCompany();
    //    return 0;
    default:
        echo "You have confused me.";
        echo "<BR> Action was: "+$_REQUEST['action'];
        return 0;
}

$c['header_links']['Add New Forwarder']   = "edit_forwarder.php?action=add";
$c['header_links']['List All Forwarders'] = "forwarders_admin.php?view=&query=&display_search_box=true";

//Locks temporarily disabled, this is unnecessary
//if($c['locked'])
//{
//    $c['content'] = "<h3 style=\"color:red\">Member {$_REQUEST['member_id']} is Locked.</h3><p>Please try again later.</p>";
//}

//$page = new Template("../new_suppliers/templates/");
$page = new Template("templates/extra/");
$page->set("content", $c['content']);
$page->set("title", $c['title']);
$page->set("headerlinks_array", $c['header_links']);
$page->set("linksperline", 3);
echo $page->fetch("body.tpl.php");

// ADD Functions ______________________________________________________________
function showAdd()
{
    // Set some sane defaults then show edit form
    $forwarder_details['country']           = "US";
    $forwarder_details['warehouse_country'] = "US";
    //$forwarder_details['email_addr'][0]['not_set'] = 1;
    //$forwarder_details['email_addr'][1]['not_set'] = 1;
    //$forwarder_details['ship_addr'][0]['not_set'] = 1;
    //$forwarder_details['ship_addr'][0]['country'] = "US";
    //$forwarder_details['freight_forwarders'][0]['not_set'] = 1;
    //$forwarder_details['freight_forwarders'][0]['country'] = "US";
    //$forwarder_details['courier_accounts'][0] = array("foo");    // One set of courier account boxes
    //$forwarder_details['prefs']['currency'] = "USD";
    //$forwarder_details['prefs']['per_page'] = 25;

    $page = new Template("./templates/");
    $page->set('forwarder_details', $forwarder_details);
    $page->set('countries', makeCountryArray());
    $page->set('currencies', makeCurrencyArray());
    $page->set('add', 1);
    $c['content'] = $page->fetch("customer_edit.tpl.php");

    //Not sure what the hell this does, doubt I need it
    //$c['content'] .= "<pre>".print_r($member_details,1)."</pre>";

    return $c;
}

function doAdd()
{
    global $DB, $forwarder;

    $statement     = "INSERT INTO `coinop`.`freight_forwarders` (" . get_fields($_POST['forwarder']) . ") VALUES (" . get_values($_POST['forwarder']) . ")";
    $insert_result = $DB->query($statement);

    //Get the newly insterted Forwarders id
    $id_statement = "SELECT id FROM `coinop`.`freight_forwarders` WHERE name = '$forwarder[name]' AND phone = '$forwarder[phone]' AND website = '$forwarder[website]'";
    $result       = $DB->query($id_statement);

    if ($row = $DB->fetch_array($result)) {
        $forwarder_id = $row['id'];
    }

    //add the warehouses
    foreach ($_REQUEST['warehouse'] as $wh) {
        if (trim($wh['name']) != "" || "" != $wh['address'] || "" != $wh['phone'] || "" != $wh['fax']) {
            $wh_query = "INSERT INTO `coinop`.`freight_forwarders_warehouses` SET
						name='{$wh['name']}',
						address='{$wh['address']}',
						address2='{$wh['address2']}',
						city='{$wh['city']}',
						state='{$wh['state']}',
						zip='{$wh['zip']}',
						phone='{$wh['phone']}',
						fax='{$wh['fax']}',
						country='{$wh['country']}',
						email='{$wh['email']}',
						website='{$wh['website']}',
						forwarder_id='$forwarder_id'";
            $DB->query($wh_query);
        }
    }

    //add the contacts
    foreach ($_REQUEST['contact'] as $ca) {
        if (trim($ca['name']) != "" || trim($ca['email']) != "" || "" != $ca['title']) {
            $ca_query = "INSERT INTO `coinop`.`freight_forwarders_contacts` SET
							name='{$ca['name']}',
							email='{$ca['email']}',
							title='{$ca['title']}',
							forwarder_id='$forwarder_id'";
            //phone='{$ca['phone']}',
            $DB->query($ca_query);
        }
    }

    //add the notes
    foreach ($_REQUEST['comment'] as $cmt) {
        if (trim($cmt['comment']) != "" || "" != $cmt['comment']) {
            $cmt['date'] = substr($cmt['date'], 6, 4) . "-" . substr($cmt['date'], 3, 2) . "-" . substr($cmt['date'], 0, 2);
            $cmt_query   = "INSERT INTO `coinop`.`freight_forwarders_comments` SET
							staff='{$cmt['staff']}',
							date='{$cmt['date']}',
							comment='{$cmt['comment']}',
							forwarder_id='$forwarder_id'";
            $DB->query($cmt_query);
        }
    }

    foreach ($_REQUEST['bank'] as $bk) {
        if (trim($bk['details']) != "") {
            $bk_query = "INSERT INTO `coinop`.`freight_forwarders_bank` SET
							forwarder_id='$forwarder_id',
							details='{$bk['details']}'";
            $DB->query($bk_query);
        }
    }
    /*
    <html><head></head><body>
    <script type="text/javascript">
    //<![CDATA[
var new_suppliers = <?=json_encode(getFreightForwarderList())?>;
if (window.opener.updateSuppliers) {
    window.opener.updateSuppliers(new_suppliers);
}
//]]>
    </script>
    </body></html>*/

    //add the quotes
    /*foreach($_REQUEST['quote'] as $qt)
{
if(trim($qt['customer_name']) != "" || trim($qt['details']) != "" || $qt['name'] != "" || $qt['qt_no'] != "" || $qt['sea_air'] != "" || $qt['price'] != "" || $qt['status'] != "")
{
$qt['date'] = substr($qt['date'],6,4)."-".substr($qt['date'],3,2)."-".substr($qt['date'],0,2);
$qt_query = "INSERT INTO `coinop`.`freight_forwarders_quotes` SET
date='{$qt['date']}',
customer_name='{$qt['customer_name']}',
qt_no='{$qt['qt_no']}',
sea_air='{$qt['sea_air']}',
details='{$qt['details']}',
price='{$qt['price']}',
status='{$qt['status']}',
forwarder_id='$forwarder_id'";
$DB->query($qt_query);
}
}

//add the bookings
foreach($_REQUEST['booking'] as $bo)
{
if(trim($bo['customer_name']) != "" || trim($bo['status']) != "" || $bo['coe_id'] != "" || $bo['price'] != "" || $bo['tracking_no'] != "" || $bo['paid'] != "")
{
$bo['date'] = substr($bo['date'],6,4)."-".substr($bo['date'],3,2)."-".substr($bo['date'],0,2);
$bo_query = "INSERT INTO `coinop`.`freight_forwarders_bookings` SET
date='{$bo['date']}',
customer_name='{$bo['customer_name']}',
coe_id='{$bo['coe_id']}',
price='{$bo['price']}',
tracking_no='{$bo['tracking_no']}',
status='{$bo['status']}',
paid='{$bo['paid']}',
forwarder_id='$forwarder_id'";
$DB->query($bo_query);
}
}*/
}

// EDIT Functions _____________________________________________________________
function showEdit()
{
    global $DB;
    $id = $_REQUEST['id'];

    //Get and display forwarder info
    $statement = "SELECT * FROM `coinop`.`freight_forwarders` WHERE id=$id";

    $result = $DB->query($statement);

    if ($row = $DB->fetch_array($result)) {
        $forwarder_details['name']     = stripslashes($row['name']);
        $forwarder_details['address']  = stripslashes($row['address']);
        $forwarder_details['address2'] = stripslashes($row['address2']);
        $forwarder_details['state']    = stripslashes($row['state']);
        $forwarder_details['city']     = $row['city'];
        $forwarder_details['zip']      = $row['zip'];
        $forwarder_details['country']  = $row['country'];
        $forwarder_details['phone']    = $row['phone'];
        $forwarder_details['fax']      = $row['fax'];
        $forwarder_details['mobile']   = $row['mobile'];
        $forwarder_details['email']    = $row['email'];
        $forwarder_details['website']  = $row['website'];
    }

    //Get and display warehouses
    $statement = "SELECT * FROM `coinop`.`freight_forwarders_warehouses` WHERE forwarder_id=$id ORDER BY id";

    $result = $DB->query($statement);

    $index = 1;
    while ($row = $DB->fetch_array($result)) {
        $warehouse[$index]['name']     = stripslashes($row['name']);
        $warehouse[$index]['address']  = stripslashes($row['address']);
        $warehouse[$index]['address2'] = stripslashes($row['address2']);
        $warehouse[$index]['city']     = $row['city'];
        $warehouse[$index]['state']    = $row['state'];
        $warehouse[$index]['zip']      = $row['zip'];
        $warehouse[$index]['country']  = $row['country'];
        $warehouse[$index]['phone']    = $row['phone'];
        $warehouse[$index]['fax']      = $row['fax'];
        $warehouse[$index]['mobile']   = $row['mobile'];
        $warehouse[$index]['email']    = $row['email'];
        $warehouse[$index]['website']  = $row['website'];
        $warehouse[$index]['id']       = $row['id'];
        $index++;
    }
    //Get and display contacts
    $statement = "SELECT * FROM `coinop`.`freight_forwarders_contacts` WHERE forwarder_id=$id ORDER BY id";

    $result = $DB->query($statement);

    $index = 1;
    while ($row = $DB->fetch_array($result)) {
        $contact[$index]['name']  = $row['name'];
        $contact[$index]['title'] = $row['title'];
        $contact[$index]['email'] = $row['email'];
        //$contact[$index]['phone'] = $row['phone'];
        $contact[$index]['id'] = $row['id'];
        $index++;
    }

    //Get The Bank Account Details
    $statement = "SELECT * FROM `coinop`.`freight_forwarders_bank` WHERE forwarder_id=$id ORDER BY id";

    $result = $DB->query($statement);

    $index = 1;
    while ($row = $DB->fetch_array($result)) {
        $bank[$index]['details'] = $row['details'];
        $bank[$index]['id']      = $row['id'];
        $index++;
    }

    //Get and display comments
    $statement = "SELECT * FROM `coinop`.`freight_forwarders_comments` WHERE forwarder_id=$id ORDER BY date DESC";

    $result = $DB->query($statement);

    $index = 1;

    while ($row = $DB->fetch_array($result)) {
        $comment[$index]['date']    = $row['date'];
        $comment[$index]['staff']   = $row['staff'];
        $comment[$index]['comment'] = $row['comment'];
        $comment[$index]['id']      = $row['id'];
        $index++;
    }

    //Get and display quotes
    $statement = "SELECT * FROM `coinop`.`freight_forwarders_quotes` WHERE forwarder_id=$id ORDER BY date DESC";

    $result = $DB->query($statement);

    $index = 1;

    while ($row = $DB->fetch_array($result)) {
        $quote[$index]['date']          = $row['date'];
        $quote[$index]['customer_name'] = $row['customer_name'];
        $quote[$index]['customer_id']   = $row['customer_id'];
        $quote[$index]['qt_no']         = $row['qt_no'];
        $quote[$index]['sea_air']       = $row['sea_air'];
        $quote[$index]['details']       = $row['details'];
        $quote[$index]['price']         = $row['price'];
        $quote[$index]['status']        = $row['status'];
        $quote[$index]['id']            = $row['id'];
        $index++;
    }

    //Get and display bookings
    $statement = "SELECT * FROM `coinop`.`freight_forwarders_bookings` WHERE forwarder_id=$id ORDER BY date DESC";

    $result = $DB->query($statement);

    $index = 1;

    while ($row = $DB->fetch_array($result)) {
        $booking[$index]['date']          = $row['date'];
        $booking[$index]['customer_name'] = $row['customer_name'];
        $booking[$index]['coe_id']        = $row['coe_id'];
        $booking[$index]['price']         = $row['price'];
        $booking[$index]['tracking_no']   = $row['tracking_no'];
        $booking[$index]['status']        = $row['status'];
        $booking[$index]['paid']          = $row['paid'];
        $booking[$index]['id']            = $row['id'];
        $index++;
    }
    $page = new Template("./templates/");
    $page->set('forwarder_details', $forwarder_details);
    $page->set('contact', $contact);
    $page->set('bank', $bank);
    $page->set('comment', $comment);
    $page->set('quote', $quote);
    $page->set('booking', $booking);
    $page->set('warehouse', $warehouse);
    $page->set('countries', makeCountryArray());
    $c['content'] = $page->fetch("customer_edit.tpl.php");
    return $c;
}

function doEdit()
{
    global $DB, $forwarder;
    $forwarder=
    $id = $_REQUEST['editing_id'];
    //Presently only modifies the forwarder details section of the page
    $statement     = "UPDATE `coinop`.`freight_forwarders` SET " . get_set_values($_POST['forwarder']) . " WHERE id = '$id'";
    $update_result = $DB->query($statement);

    //update the warehouses
    foreach ($_REQUEST['warehouse'] as $wh) {
        //$test = "INSERT INTO freight_forwarders_warehouses SET name = '{$wh['name']}', address = '{$wh['id']}'";
        //$DB->query($test);
        if (trim($wh['name']) != "" || "" != $wh['address'] || "" != $wh['phone'] || "" != $wh['fax']) {
            if (0 == $wh['id']) {
                $wh_query = "INSERT INTO `coinop`.`freight_forwarders_warehouses` SET
							name='{$wh['name']}',
							address='{$wh['address']}',
							address2='{$wh['address2']}',
							city='{$wh['city']}',
							state='{$wh['state']}',
							zip='{$wh['zip']}',
							phone='{$wh['phone']}',
							fax='{$wh['fax']}',
							country='{$wh['country']}',
							email='{$wh['email']}',
							website='{$wh['website']}',
							forwarder_id='$id'";
            } else {
                $wh_query = "UPDATE `coinop`.`freight_forwarders_warehouses` SET
							name='{$wh['name']}',
							address='{$wh['address']}',
							address2='{$wh['address2']}',
							city='{$wh['city']}',
							state='{$wh['state']}',
							zip='{$wh['zip']}',
							phone='{$wh['phone']}',
							fax='{$wh['fax']}',
							country='{$wh['country']}',
							email='{$wh['email']}',
							website='{$wh['website']}',
							forwarder_id='$id'
							WHERE id = '{$wh['id']}'";
            }
            $DB->query($wh_query);
        }
    }

    //update the contacts
    foreach ($_REQUEST['contact'] as $ca) {
        if (trim($ca['name']) != "" || trim($ca['email']) != "" || "" != $ca['title']) {
            if (0 == $ca['id']) {
                $ca_query = "INSERT INTO `coinop`.`freight_forwarders_contacts` SET
							name='{$ca['name']}',
							email='{$ca['email']}',
							title='{$ca['title']}',
							forwarder_id='$id'";
                //phone='{$ca['phone']}',
            } else {
                $ca_query = "UPDATE `coinop`.`freight_forwarders_contacts` SET
								name='{$ca['name']}',
								email='{$ca['email']}',
								title='{$ca['title']}',
								forwarder_id='$id'
								WHERE id = '{$ca['id']}'";
            }
            $DB->query($ca_query);
        }
    }
    //update the notes
    foreach ($_REQUEST['comment'] as $cmt) {
        if (trim($cmt['comment']) != "" || "" != $cmt['comment']) {
            if (0 == $cmt['id']) {
                $cmt['date'] = substr($cmt['date'], 6, 4) . "-" . substr($cmt['date'], 3, 2) . "-" . substr($cmt['date'], 0, 2);
                $cmt_query   = "INSERT INTO `coinop`.`freight_forwarders_comments` SET
								staff='{$cmt['staff']}',
								date='{$cmt['date']}',
								comment='{$cmt['comment']}',
								forwarder_id='$id'";
            } else {
                $cmt['date'] = substr($cmt['date'], 6, 4) . "-" . substr($cmt['date'], 3, 2) . "-" . substr($cmt['date'], 0, 2);
                $cmt_query   = "UPDATE `coinop`.`freight_forwarders_comments` SET
								staff='{$cmt['staff']}',
								date='{$cmt['date']}',
								comment='{$cmt['comment']}',
								forwarder_id='$id'
								WHERE id = '{$cmt['id']}'";
            }
            $DB->query($cmt_query);
        }
    }

    // update the bank details...
    foreach ($_REQUEST['bank'] as $bk) {
        if (trim($bk['details']) != "") {
            if (0 == $bk['id']) {
                $bk_query = "INSERT INTO `coinop`.`freight_forwarders_bank` SET
							forwarder_id='$id',
							details='{$bk['details']}'";
            } else {
                $bk_query = "UPDATE `coinop`.`freight_forwarders_bank` SET
							forwarder_id='$id',
							details='{$bk['details']}'
							WHERE id = '{$bk['id']}'";
            }
            $DB->query($bk_query);
        }
    }

    //update the quotes
    /*foreach($_REQUEST['quote'] as $qt)
    {
    if(trim($qt['customer_name']) != "" || trim($qt['details']) != "" || $qt['name'] != "" || $qt['qt_no'] != "" || $qt['sea_air'] != "" || $qt['price'] != "" || $qt['status'] != "")
    {
    if($qt['id'] == 0)
    {
    $qt['date'] = substr($qt['date'],6,4)."-".substr($qt['date'],3,2)."-".substr($qt['date'],0,2);
    $qt_query = "INSERT INTO `coinop`.`freight_forwarders_quotes` SET
    date='{$qt['date']}',
    customer_name='{$qt['customer_name']}',
    qt_no='{$qt['qt_no']}',
    sea_air='{$qt['sea_air']}',
    details='{$qt['details']}',
    price='{$qt['price']}',
    status='{$qt['status']}',
    forwarder_id='$id'";
    }
    else
    {
    $qt_query = "UPDATE `coinop`.`freight_forwarders_quotes` SET
    date='{$qt['date']}',
    customer_name='{$qt['customer_name']}',
    qt_no='{$qt['qt_no']}',
    sea_air='{$qt['sea_air']}',
    details='{$qt['details']}',
    price='{$qt['price']}',
    status='{$qt['status']}',
    forwarder_id='$id'
    WHERE id = '{$qt['id']}'";
    }
    $DB->query($qt_query);
    }
    }

    //update the bookings
    foreach($_REQUEST['booking'] as $bo)
    {
    if(trim($bo['customer_name']) != "" || trim($bo['status']) != "" || $bo['coe_id'] != "" || $bo['price'] != "" || $bo['tracking_no'] != "" || $bo['paid'] != "")
    {
    if($bo['id'] == 0)
    {
    $bo['date'] = substr($bo['date'],6,4)."-".substr($bo['date'],3,2)."-".substr($bo['date'],0,2);
    $bo_query = "INSERT INTO `coinop`.`freight_forwarders_bookings` SET
    date='{$bo['date']}',
    customer_name='{$bo['customer_name']}',
    coe_id='{$bo['coe_id']}',
    price='{$bo['price']}',
    tracking_no='{$bo['tracking_no']}',
    status='{$bo['status']}',
    paid='{$bo['paid']}',
    forwarder_id='$id'";
    }
    else
    {
    $bo_query = "UPDATE `coinop`.`freight_forwarders_bookings` SET
    date='{$bo['date']}',
    customer_name='{$bo['customer_name']}',
    coe_id='{$bo['coe_id']}',
    price='{$bo['price']}',
    tracking_no='{$bo['tracking_no']}',
    status='{$bo['status']}',
    paid='{$bo['paid']}',
    forwarder_id='$id'
    WHERE id = '{$bo['id']}'";
    }
    $DB->query($bo_query);
    }
    }*/

    //$page = new Template("./templates/");
    //$page->set('content', "<pre>".print_r($_REQUEST,1)."</pre>");
    //echo $page->fetch("blank.tpl.php");
}

// DELETE Functions ___________________________________________________________
function request_delete()
{
    global $DB;
    $id = $_REQUEST['id'];

    // Dig up pertinent info

    // Display some confirmation
    $c['content'] = "<script type=\"text/javascript\">";
    $c['content'] .= "if(confirm(\"Do you wish to delete this forwarder and all associated details?\"))";
    $c['content'] .= "{";
    $c['content'] .= "document.location.href=\"" . basename($_SERVER['PHP_SELF']) . "?action=do_delete&id=$id\";";
    $c['content'] .= "}";
    $c['content'] .= "else{";
    $c['content'] .= "document.location.href=\"" . basename($_SERVER['PHP_SELF']) . "?action=cancel_delete&id=$id\";";
    $c['content'] .= "}";
    $c['content'] .= "</script>";
    $c['content'] .= "<a href=\"" . basename($_SERVER['PHP_SELF']) . "?action=do_delete&amp;id=$id\">Delete Forwarder $id</a>";

    return $c;
}

function do_delete()
{
    global $DB;
    $id = $_REQUEST['id'];

    //Delete the forwarder
    $statement = "DELETE FROM `coinop`.`freight_forwarders` WHERE id = '$id'";
    $DB->query($statement);

    //Delete the forwarders warehouses
    $statement = "DELETE FROM `coinop`.`freight_forwarders_warehouses` WHERE forwarder_id = '$id'";
    $DB->query($statement);

    //Delete the forwarders contacts
    $statement = "DELETE FROM `coinop`.`freight_forwarders_contacts` WHERE forwarder_id = '$id'";
    $DB->query($statement);

    //Delete the forwarders comments
    $statement = "DELETE FROM `coinop`.`freight_forwarders_comments` WHERE forwarder_id = '$id'";
    $DB->query($statement);

    //Delete the forwarders quotes
    $statement = "DELETE FROM `coinop`.`freight_forwarders_quotes` WHERE forwarder_id = '$id'";
    $DB->query($statement);

    //Delete the forwarders bookings
    $statement = "DELETE FROM `coinop`.`freight_forwarders_bookings` WHERE forwarder_id = '$id'";
    $DB->query($statement);
}

function delContact()
{
    global $DB;

    $database_id = $_REQUEST['database_id'];
    //if($x) $inject = "alert(\"An error occured.\");";
    $statement = "DELETE FROM `coinop`.`freight_forwarders_contacts` WHERE id=$database_id";
    //echo $statement;
    //writeJSIFrameDeleter($inject, $_REQUEST['database_id']);
    $DB->query($statement);
}

function delComment()
{
    global $DB;

    $database_id = $_REQUEST['database_id'];
    //if($x) $inject = "alert(\"An error occured.\");";
    $statement = "DELETE FROM `coinop`.`freight_forwarders_comments` WHERE id=$database_id";
    //echo $statement;
    //writeJSIFrameDeleter($inject, $_REQUEST['database_id']);
    $DB->query($statement);
}

function delQuote()
{
    global $DB;

    $database_id = $_REQUEST['database_id'];
    //if($x) $inject = "alert(\"An error occured.\");";
    $statement = "DELETE FROM `coinop`.`freight_forwarders_quotes` WHERE id=$database_id";
    //echo $statement;
    //writeJSIFrameDeleter($inject, $_REQUEST['database_id']);
    $DB->query($statement);
}

function delBooking()
{
    global $DB;

    $database_id = $_REQUEST['database_id'];
    //if($x) $inject = "alert(\"An error occured.\");";
    $statement = "DELETE FROM `coinop`.`freight_forwarders_bookings` WHERE id=$database_id";
    //echo $statement;
    //writeJSIFrameDeleter($inject, $_REQUEST['database_id']);
    $DB->query($statement);
}

function findCompany()
{
    /* Function called from OnChange event of "company" textbox in members add/edit form. */
    global $DB;

    // DB Query to find if there's any existing companies with the same name as what just got entered.
    /*$tmp = $DB->query_first("SELECT COUNT(*) AS `compcount` FROM `highwaygroup`.`new_members` WHERE `company_name` = '{$_REQUEST['company']}'");
if($tmp['compcount']) $inject = "parent.document.getElementById('cewarn').style.display='';
parent.document.getElementById('nocewarn').style.display='none';";
writeJSIFrameDeleter($inject, $_REQUEST['company']);*/
}

function writeJSIFrameDeleter($inject, $id)
{
    /* What follows is a funky bit of JS manipulation =]
     * The script assumes it's loaded in a "dynamic" iframe attached to the <body> tag of another document.
     * The net effect of this is to remove the iframe from it's parent frame after doing all it's work =D
     */
?>
	<html><body><script type="text/javascript">
<?php
if ($inject) {
        echo $inject;
    }
?>
	var obj = parent.document.getElementById("tempif<?=$id?>")
	obj.parentNode.removeChild(obj);
	</script></body></html>
<?php
}
?>
