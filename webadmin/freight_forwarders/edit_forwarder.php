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

include_once 'forwarders_common.inc.php';

moduleLoad('template');

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
        $c = doAdd();
        break;
    case "do_edit":
        $c = doEdit();
        break;
    case "do_delete":
        redirect("/webadmin/freight_forwarders/forwarders_admin.php");
        echo "Doing DELETE";
        $c = do_delete();
        break;
    case "cancel_delete":
        redirect("/webadmin/freight_forwarders/forwarders_admin.php");
        break;
    case "delete_contact":
        delContact();
        break;
    case "delete_comment":
        delComment();
        break;
    case "delete_quote":
        delQuote();
        break;
    case "delete_booking":
        delBooking();
        break;
    default:
        echo "You have confused me.";
        echo "<BR> Action was: "+$_REQUEST['action'];
        return 0;
}
$c['header_links']['Add New Forwarder']   = "edit_forwarder.php?action=add";
$c['header_links']['List All Forwarders'] = "forwarders_admin.php?view=&query=&display_search_box=true";

include_once 'templates/forwarders_header.tpl.php';

$page = new Template("templates/extra/");
$page->set("content", $c['content']);
$page->set("title", $c['title']);
$page->set("headerlinks_array", $c['header_links']);
$page->set("linksperline", 3);
echo $page->fetch("body.tpl.php");

include_once 'templates/forwarders_footer.tpl.php';

// ADD Functions ______________________________________________________________
function showAdd()
{
    // Set some sane defaults then show edit form
    $forwarder_details['country']           = "US";
    $forwarder_details['warehouse_country'] = "US";

    $page = new Template("./templates/");
    $page->set('forwarder_details', $forwarder_details);
    $page->set('countries', getCountryList());
    $page->set('currencies', getCurrencyList());
    $page->set('add', 1);
    $c['content'] = $page->fetch("customer_edit.tpl.php");

    //Not sure what the hell this does, doubt I need it
    //$c['content'] .= "<pre>".print_r($member_details,1)."</pre>";

    return $c;
}

function doAdd()
{
    global $DB, $forwarder;

    $new_forwarder = $forwarder;
    $new_forwarder = array_intersect_key($new_forwarder, getTableFields('`highwaygroup`.freight_services'));
    $fields        = implode(',', array_keys($forwarder));
    $values        = str_replace("\n", "\\n", "'" . implode("','", array_values($forwarder)) . "'");

    $statement     = "INSERT INTO freight_services (" . $fields . ") VALUES (" . $values . ")";
    $insert_result = $DB->query($statement);

    //Get the newly inserted Forwarders id
    $id_statement = "SELECT id FROM freight_services WHERE name = '$forwarder[name]' AND phone = '$forwarder[phone]' AND website = '$forwarder[website]'";
    $result       = $DB->query($id_statement);

    if ($row = $DB->fetch_array($result)) {
        $forwarder_id = $row['id'];
    }

    //add the warehouses
    foreach ($_REQUEST['warehouse'] as $wh) {
        if (trim($wh['name']) != "" || "" != $wh['address'] || "" != $wh['phone'] || "" != $wh['fax']) {
            $wh_query = "INSERT INTO freight_services_warehouse SET
						name='{$wh['name']}',
						address='{$wh['address']}',
						address2='{$wh['address2']}',
						city='{$wh['city']}',
						state='{$wh['state']}',
						post_code='{$wh['post_code']}',
						phone='{$wh['phone']}',
						fax='{$wh['fax']}',
						country='{$wh['country']}',
						email='{$wh['email']}',
						website='{$wh['website']}',
						product_id='$forwarder_id'";
            $DB->query($wh_query);
        }
    }

    //add the contacts
    foreach ($_REQUEST['contact'] as $ca) {
        if (trim($ca['name']) != "" || trim($ca['email']) != "" || "" != $ca['title']) {
            $ca_query = "INSERT INTO freight_services_contacts SET
							name='{$ca['name']}',
							email='{$ca['email']}',
							title='{$ca['title']}',
							product_id='$forwarder_id'";
            //phone='{$ca['phone']}',
            $DB->query($ca_query);
        }
    }

    //add the notes
    foreach ($_REQUEST['comments'] as $cmt) {
        if (trim($cmt['comments']) != "" || "" != $cmt['comments']) {
            $cmt['date'] = substr($cmt['date'], 6, 4) . "-" . substr($cmt['date'], 3, 2) . "-" . substr($cmt['date'], 0, 2);
            $cmt_query   = "INSERT INTO freight_services_comments SET
							staff='{$cmt['staff']}',
							date='{$cmt['date']}',
							comments='{$cmt['comments']}',
							product_id='$forwarder_id'";
            $DB->query($cmt_query);
        }
    }

    foreach ($_REQUEST['bank'] as $bk) {
        if (trim($bk['details']) != "") {
            $bk_query = "INSERT INTO freight_services_bank SET
							product_id='$forwarder_id',
							details='{$bk['details']}'";
            $DB->query($bk_query);
        }
    }
}

// EDIT Functions _____________________________________________________________
function showEdit()
{
    global $DB;
    $id = $_REQUEST['id'];

    //Get and display forwarder info
    $statement = "SELECT * FROM freight_services WHERE id=$id";

    $result = $DB->query($statement);

    if ($row = $DB->fetch_array($result)) {
        $forwarder_details['name']      = stripslashes($row['name']);
        $forwarder_details['address']   = stripslashes($row['address']);
        $forwarder_details['address2']  = stripslashes($row['address2']);
        $forwarder_details['state']     = stripslashes($row['state']);
        $forwarder_details['city']      = $row['city'];
        $forwarder_details['post_code'] = $row['post_code'];
        $forwarder_details['country']   = $row['country'];
        $forwarder_details['phone']     = $row['phone'];
        $forwarder_details['fax']       = $row['fax'];
        $forwarder_details['mobile']    = $row['mobile'];
        $forwarder_details['email']     = $row['email'];
        $forwarder_details['website']   = $row['website'];
    }

    //Get and display warehouses
    $statement = "SELECT * FROM freight_services_warehouse WHERE product_id=$id ORDER BY id";

    $result = $DB->query($statement);

    $index = 1;
    while ($row = $DB->fetch_array($result)) {
        $warehouse[$index]['name']      = stripslashes($row['name']);
        $warehouse[$index]['address']   = stripslashes($row['address']);
        $warehouse[$index]['address2']  = stripslashes($row['address2']);
        $warehouse[$index]['city']      = $row['city'];
        $warehouse[$index]['state']     = $row['state'];
        $warehouse[$index]['post_code'] = $row['post_code'];
        $warehouse[$index]['country']   = $row['country'];
        $warehouse[$index]['phone']     = $row['phone'];
        $warehouse[$index]['fax']       = $row['fax'];
        $warehouse[$index]['mobile']    = $row['mobile'];
        $warehouse[$index]['email']     = $row['email'];
        $warehouse[$index]['website']   = $row['website'];
        $warehouse[$index]['id']        = $row['id'];
        $index++;
    }
    //Get and display contacts
    $statement = "SELECT * FROM freight_services_contacts WHERE product_id=$id ORDER BY id";

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
    $statement = "SELECT * FROM freight_services_bank WHERE product_id=$id ORDER BY id";

    $result = $DB->query($statement);

    $index = 1;
    while ($row = $DB->fetch_array($result)) {
        $bank[$index]['details'] = $row['details'];
        $bank[$index]['id']      = $row['id'];
        $index++;
    }

    //Get and display comments
    $statement = "SELECT * FROM freight_services_comments WHERE product_id=$id ORDER BY date DESC";

    $result = $DB->query($statement);

    $index = 1;

    while ($row = $DB->fetch_array($result)) {
        $comments[$index]['date']     = $row['date'];
        $comments[$index]['staff']    = $row['staff'];
        $comments[$index]['comments'] = $row['comments'];
        $comments[$index]['id']       = $row['id'];
        $index++;
    }

    //Get and display quotes
    $statement = "SELECT * FROM freight_services_quotes WHERE product_id=$id ORDER BY date DESC";

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
    $statement = "SELECT * FROM freight_services_quotes WHERE product_id=$id AND booking_confirmed = 'y' ORDER BY date DESC";

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
    $page->set('comments', $comments);
    $page->set('quote', $quote);
    $page->set('booking', $booking);
    $page->set('warehouse', $warehouse);
//    $page->set('countries', makeCountryArray());
    $c['content'] = $page->fetch("customer_edit.tpl.php");
    return $c;
}

function doEdit()
{
    global $DB, $forwarder;
    $id = $_REQUEST['editing_id'];

    $values        = array();
    $new_forwarder = $forwarder;
    $new_forwarder = array_intersect_key($new_forwarder, getTableFields('`highwaygroup`.freight_services'));
    foreach ($new_forwarder as $key => $value) {
        $values[] = $key . "=" . $DB->escape($value);
    }

    $values = implode(", ", $values);

    //Presently only modifies the forwarder details section of the page
    $statement     = "UPDATE freight_services SET " . $values . " WHERE id='$id'";
    $update_result = $DB->query($statement);

    //update the warehouses
    foreach ($_REQUEST['warehouse'] as $wh) {
        if (trim($wh['name']) != "" || "" != $wh['address'] || "" != $wh['phone'] || "" != $wh['fax']) {
            if (0 == $wh['id']) {
                $wh_query = "INSERT INTO freight_services_warehouse SET
							name='{$wh['name']}',
							address='{$wh['address']}',
							address2='{$wh['address2']}',
							city='{$wh['city']}',
							state='{$wh['state']}',
							post_code='{$wh['post_code']}',
							phone='{$wh['phone']}',
							fax='{$wh['fax']}',
							country='{$wh['country']}',
							email='{$wh['email']}',
							website='{$wh['website']}',
							product_id='$id'";
            } else {
                $wh_query = "UPDATE freight_services_warehouse SET
							name='{$wh['name']}',
							address='{$wh['address']}',
							address2='{$wh['address2']}',
							city='{$wh['city']}',
							state='{$wh['state']}',
							post_code='{$wh['post_code']}',
							phone='{$wh['phone']}',
							fax='{$wh['fax']}',
							country='{$wh['country']}',
							email='{$wh['email']}',
							website='{$wh['website']}',
							product_id='$id'
							WHERE id = '{$wh['id']}'";
            }
            $DB->query($wh_query);
        }
    }

    //update the contacts
    foreach ($_REQUEST['contact'] as $ca) {
        if (trim($ca['name']) != "" || trim($ca['email']) != "" || "" != $ca['title']) {
            if (0 == $ca['id']) {
                $ca_query = "INSERT INTO freight_services_contacts SET
							name='{$ca['name']}',
							email='{$ca['email']}',
							title='{$ca['title']}',
							product_id='$id'";
                //phone='{$ca['phone']}',
            } else {
                $ca_query = "UPDATE freight_services_contacts SET
								name='{$ca['name']}',
								email='{$ca['email']}',
								title='{$ca['title']}',
								product_id='$id'
								WHERE id = '{$ca['id']}'";
            }
            $DB->query($ca_query);
        }
    }
    //update the notes
    if (is_array($_REQUEST['comments'])) {
        foreach ($_REQUEST['comments'] as $cmt) {
            if (trim($cmt['comments']) != "" || "" != $cmt['comments']) {
                if (0 == $cmt['id']) {
                    $cmt['date'] = substr($cmt['date'], 6, 4) . "-" . substr($cmt['date'], 3, 2) . "-" . substr($cmt['date'], 0, 2);
                    $cmt_query   = "INSERT INTO freight_services_comments SET
								staff='{$cmt['staff']}',
								date='{$cmt['date']}',
								comments='{$cmt['comments']}',
								product_id='$id'";
                } else {
                    $cmt['date'] = substr($cmt['date'], 6, 4) . "-" . substr($cmt['date'], 3, 2) . "-" . substr($cmt['date'], 0, 2);
                    $cmt_query   = "UPDATE freight_services_comments SET
								staff='{$cmt['staff']}',
								date='{$cmt['date']}',
								comments='{$cmt['comments']}',
								product_id='$id'
								WHERE id = '{$cmt['id']}'";
                }
                $DB->query($cmt_query);
            }
        }
    }

    // update the bank details...
    foreach ($_REQUEST['bank'] as $bk) {
        if (trim($bk['details']) != "") {
            if (0 == $bk['id']) {
                $bk_query = "INSERT INTO freight_services_bank SET
							product_id='$id',
							details='{$bk['details']}'";
            } else {
                $bk_query = "UPDATE freight_services_bank SET
							product_id='$id',
							details='{$bk['details']}'
							WHERE id = '{$bk['id']}'";
            }
            $DB->query($bk_query);
        }
    }

   
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
    $statement = "DELETE FROM freight_services WHERE id = '$id'";
    $DB->query($statement);

    //Delete the forwarders warehouses
    $statement = "DELETE FROM freight_services_warehouse WHERE product_id = '$id'";
    $DB->query($statement);

    //Delete the forwarders contacts
    $statement = "DELETE FROM freight_services_contacts WHERE product_id = '$id'";
    $DB->query($statement);

    //Delete the forwarders comments
    $statement = "DELETE FROM freight_services_comments WHERE product_id = '$id'";
    $DB->query($statement);

    //Delete the forwarders quotes
    $statement = "DELETE FROM freight_services_quotes WHERE product_id = '$id'";
    $DB->query($statement);

    //Delete the forwarders bookings
    $statement = "DELETE FROM freight_services_bookings WHERE product_id = '$id'";
    $DB->query($statement);
}

function delContact()
{
    global $DB;

    $database_id = $_REQUEST['database_id'];
    $statement = "DELETE FROM freight_services_contacts WHERE id=$database_id";
    $DB->query($statement);
}

function delComment()
{
    global $DB;

    $database_id = $_REQUEST['database_id'];
    $statement = "DELETE FROM freight_services_comments WHERE id=$database_id";
    $DB->query($statement);
}

function delQuote()
{
    global $DB;

    $database_id = $_REQUEST['database_id'];
    $statement = "DELETE FROM freight_services_quotes WHERE id=$database_id";
    $DB->query($statement);
}

function delBooking()
{
    global $DB;

    $database_id = $_REQUEST['database_id'];
    $statement = "DELETE FROM freight_services_quotes WHERE id=$database_id";
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
