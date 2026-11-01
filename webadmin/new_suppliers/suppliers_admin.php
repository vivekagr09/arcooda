<?php
/***********************
 * Suppliers Admin Core Logic
 * Filename: suppliers_admin.php
 ***********************
 * Purpose:
 *    Do all the relevant stuff for administering a list of suppliers
 * Configuration Variables used:
 *    $config['db_prefix']
 *    $config['template_path']
 * Functions defined:
 *    checkIDGetSupplier() -    Check to see if $_REQUEST['id'] exists and is valid
 *                Returns the supplier array if it's ok, will also
 *                grab a lock on the record.
 *    genTypeSQL() -    Insert appropriate arguments into a string for doing inserts
 *            on the suppliers_supplytypes table.
 ***********************/

include_once __DIR__ . '/common.inc.php';

moduleLoad('template');

include_once "spo_config.php";

include_once "functions/country.inc.php"; // Country Functions (requires DB)
include_once "functions/lock.inc.php"; // DB Locking (requires DB)
include_once "functions/menu.inc.php"; // Menu updater (requires DB)
include_once "functions/user.inc.php"; // User Functions (requires DB)

$user_details = getUserByServer($config['db_prefix']);

// Handle request for action
switch ($_REQUEST['action']) {
    case "add":
        $content = addSupplierJS();
        break;
    case "edit":
        $content = editSupplier();
        break;
    case "delete":
        $content = deleteSupplier();
        break;
    case "do_add":
        $content = doAddSupplier();
        break;
    case "do_addJS":
        $content = doAddSupplierJS();
        break;
    case "do_edit":
        $content = doEditSupplierJS();
        break;
    case "list":
        $content = listSuppliers();
        break;
    case "details":
        $content = supplierDetails();
        die();
    case "delete_contact":
        $contact = delete_con();
        break;
    default:
        break;
}

// Top Bar links
if ("delete_contact" != $action) {
    $content['header_links']['Add Supplier']       = "suppliers_admin.php?action=add";
    $content['header_links']['Search Suppliers']   = "suppliers_admin.php?action=list";
    $content['header_links']['View All Suppliers'] = "suppliers_admin.php?action=list";
    $contect['body']['bgcolor']                    = "#FFFFFF";

    $content['counts'] = menuCounters($config['db_prefix']);
    outputHTML($content, $msg, $config['template_path']);
}

// List Suppliers Function
// Draws a nicely formatted list of suppliers
function listSuppliers()
{
    // init
    global $DB, $config;
    $dbp       = $config['db_prefix'];
    $suppliers = array();

    $country_list = getCountryList(false);

    // List length variables
    if ($_REQUEST['per_page']) {
        $per_page = $_REQUEST['per_page'];
    } else {
        $per_page = 50;
    }
    $start = $_REQUEST['page'] * $per_page;

    // Data grabbing
    $supp_sql = "SELECT * FROM " . $dbp . "suppliers ORDER BY supplier_name";

    // Total number of items
    $DB->query($supp_sql);
    $total_items = $DB->num_rows();

    $supp_sql .= " LIMIT $start, $per_page";
    $DB->query($supp_sql);

    while ($supplier = $DB->fetch_array()) {
        // Get country name
        $supplier['country_name'] = $country_list[$supplier['country_code']] ?? $supplier['country_code'];

        // Find All P/O for this supplier
        $po_sql = "SELECT COUNT(supplier_id) as x FROM " . $dbp . "purchase_order
				WHERE supplier_id='" . $supplier['supplier_id'] . "'";
        $po_count             = $DB->query_first($po_sql);
        $supplier['po_count'] = $po_count['x'];

        // Find All Open P/O for this supplier
        $po_sql = "SELECT COUNT(supplier_id) as x FROM " . $dbp . "purchase_order
				WHERE supplier_id='" . $supplier['supplier_id'] . "'
					AND status='1' AND checked='0'";
        $po_count                  = $DB->query_first($po_sql);
        $supplier['open_po_count'] = $po_count['x'];

        // Find newest P/O for this supplier
        $po_sql = "SELECT MAX(creation_date) as x FROM " . $dbp . "purchase_order
				WHERE supplier_id='" . $supplier['supplier_id'] . "'";
        $last_po             = $DB->query_first($po_sql);
        $supplier['last_po'] = $last_po['x'];

        // Save all this detail
        $suppliers[] = $supplier;
    }

    // Do Template
    $page = new Template($config['template_path']);
    $page->set("suppliers", $suppliers);

    // Page limiting stuff
    $url_args  = "?action=list";
    $page_last = $start + $per_page;
    if ($page_last > $total_items) {
        $page_last = $total_items;
    }
    $page->set("page_first", $start + 1);
    $page->set("page_last", $page_last);
    $page->set("total_items", $total_items);
    $page->set("page_links", makePageLimitLinks($url_args, $total_items, $_REQUEST['page'], $per_page));

    $ret['content'] = $page->fetch("suppliers_front.tpl.php");
    $ret['msg']     = "";
    $ret['header']  = "Suppliers Summary";
    $ret['title']   = "Suppliers";
    return $ret;
}

// Add Supplier Function
// Shows the add form.
function addSupplier()
{
    global $config;
    // Do Template
    $page = new Template($config['template_path']);
    $page->set("mode", "add");
    $page->set("form_dest", "?action=do_add");
    $page->set("countries", makeCountryArray());
    $ret['content'] = $page->fetch("edit_supplier.tpl.php");
    $ret['msg']     = "";
    $ret['header']  = "Add New Supplier";
    $ret['title']   = "Suppliers";
    return $ret;
}
// Function added: 22/09/05 - Jwalit Shah
// Displays the new template
function addSupplierJS()
{
    global $config;
    // Do Template
    $page = new Template($config['template_path']);
    $page->set("mode", "add");
    $page->set("form_dest", "?action=do_addJS");
    $page->set("countries", makeCountryArray());
    $page->set("admin_notes", array());
    $ret['content'] = $page->fetch("edit_supplier.tplJS.php");
    $ret['msg']     = "";
    $ret['header']  = "Add New Supplier";
    $ret['title']   = "Suppliers";
    return $ret;
}

// Edit Supplier Function
// Locks the record, grabs the data, and shows the edit form.
function editSupplier()
{
    // init
    global $DB, $config;
    $dbp = $config['db_prefix'];
    $id  = $_REQUEST['id'];

    $supplier = checkIDGetSupplier();
    if (!$supplier) {
        $ret        = listSuppliers();
        $ret['msg'] = "Valid ID not supplied!";
        return $ret;
    }

    // Supply types
    $supplier['mach'] = "";
    $supplier['kits'] = "";
    $supplier['part'] = "";
    $supplier['merc'] = "";
    $supplier['serv'] = "";

    $type_sql = "SELECT * FROM " . $dbp . "suppliers_supplytypes WHERE supplier_id='$id'";
    $DB->query($type_sql);
    while ($type = $DB->fetch_array()) {
        switch ($type['supply_type']) {
            case 1:
                $supplier['mach'] = " checked=\"checked\"";
                break;
            case 2:
                $supplier['kits'] = " checked=\"checked\"";
                break;
            case 3:
                $supplier['part'] = " checked=\"checked\"";
                break;
            case 4:
                $supplier['merc'] = " checked=\"checked\"";
                break;
            case 5:
                $supplier['serv'] = " checked=\"checked\"";
                break;
        }
    }

    // Contacts
    $supplier['contacts'] = array();
    $contact_sql          = "SELECT * FROM " . $dbp . "suppliers_contacts WHERE supplier_id='$id'";
    $DB->query($contact_sql);
    while ($contact = $DB->fetch_array()) {
        $supplier['contacts'][] = $contact;
    }

    // warehouse address
    $supplier['warehouse'] = array();
    $ware_sql              = "SELECT * FROM " . $dbp . "suppliers_warehouse WHERE supplier_id='$id'";
    $DB->query($ware_sql);
    while ($warehouse = $DB->fetch_array()) {
        $supplier['warehouse'][] = $warehouse;
    }

    // payment methods
    $supplier['payments'] = array();
    $pay_sql              = "SELECT * FROM " . $dbp . "suppliers_payment WHERE supplier_id='$id'";
    $DB->query($pay_sql);
    while ($pay = $DB->fetch_array()) {
        $supplier['payments'][] = $pay;
    }

    // Open P/O Summary
    $po_summary = array();
    $pos_sql    = "SELECT
				po.porder_id, po.creation_date, CONCAT_WS(' ', u.first_name, u.last_name) AS user_name, sc.contact_name
			FROM
				{$dbp}purchase_order AS po
			INNER JOIN
				{$dbp}suppliers_contacts AS sc
				ON po.supplier_contact=sc.contact_id
			INNER JOIN
				`highwaygroup`.`users` AS `u`
				ON po.orderer_user_id=u.id
			WHERE
				po.supplier_id='$id' AND
				po.status='1'";
    $DB->query($pos_sql);
    while ($pos = $DB->fetch_array()) {
        $po_summary[] = $pos;
    }

    // Get Admin Notes
    $admin_notes   = array();
    $get_notes_sql = "SELECT * FROM {$dbp}suppliers_adminnotes WHERE supplier_id='$id' ORDER BY date DESC";
    $DB->query($get_notes_sql);
    while ($note = $DB->fetch_array()) {
        $note['user']  = getUserByID($note['user_id'], $dbp);
        $admin_notes[] = $note;
    }

    // Do Template
    $page = new Template($config['template_path']);
    $page->set("mode", "edit");
    $page->set("form_dest", "?action=do_edit");
    $page->set("supplier", $supplier);
    $page->set("countries", makeCountryArray());
    $page->set("po_summary", $po_summary);
    $page->set("admin_notes", $admin_notes);
    $ret['content'] = $page->fetch("edit_supplier.tplJS.php");
    $ret['msg']     = "";
    $ret['header']  = "Edit Supplier";
    $ret['title']   = "Suppliers";
    return $ret;
}

// Do Add Supplier Function
// Takes data posted by the add supplier form and stores it in the DB
function doAddSupplier()
{
    // init
    global $DB, $config;
    $dbp = $config['db_prefix'];

    // Take info from addSupplier and stick it where it is supposed to go
    // Supplier details
    $supplier_sql = "INSERT INTO " . $dbp . "suppliers SET
						supplier_name='" . $_REQUEST['supplier_name'] . "',
						address='" . $_REQUEST['address'] . "',
						factory_name='" . $_REQUEST['factory_name'] . "',
						factory_address='" . $_REQUEST['factory_address'] . "',
						doffice_name='" . $_REQUEST['doffice_name'] . "',
						doffice_address='" . $_REQUEST['doffice_address'] . "',
						supplier_name_ch='" . $_REQUEST['supplier_name_ch'] . "',
						address_ch='" . $_REQUEST['address_ch'] . "',
						factory_name_ch='" . $_REQUEST['factory_name_ch'] . "',
						factory_address_ch='" . $_REQUEST['factory_address_ch'] . "',
						doffice_name_ch='" . $_REQUEST['doffice_name_ch'] . "',
						doffice_address_ch='" . $_REQUEST['doffice_address_ch'] . "',
						country_code='" . $_REQUEST['country'] . "',
						phone_cc='" . $_REQUEST['phone_cc'] . "',
						phone_ac='" . $_REQUEST['phone_ac'] . "',
						phone='" . $_REQUEST['phone'] . "',
						fax_cc='" . $_REQUEST['fax_cc'] . "',
						fax_ac='" . $_REQUEST['fax_ac'] . "',
						fax='" . $_REQUEST['fax'] . "',
						factory_phone_cc='" . $_REQUEST['factory_phone_cc'] . "',
						factory_phone_ac='" . $_REQUEST['factory_phone_ac'] . "',
						factory_phone='" . $_REQUEST['factory_phone'] . "',
						factory_fax_cc='" . $_REQUEST['factory_fax_cc'] . "',
						factory_fax_ac='" . $_REQUEST['factory_fax_ac'] . "',
						factory_fax='" . $_REQUEST['factory_fax'] . "',
						doffice_phone_cc='" . $_REQUEST['doffice_phone_cc'] . "',
						doffice_phone_ac='" . $_REQUEST['doffice_phone_ac'] . "',
						doffice_phone='" . $_REQUEST['doffice_phone'] . "',
						doffice_fax_cc='" . $_REQUEST['doffice_fax_cc'] . "',
						doffice_fax_ac='" . $_REQUEST['doffice_fax_ac'] . "',
						doffice_fax='" . $_REQUEST['doffice_fax'] . "',
						website='" . (("http://" == $_REQUEST['website']) ? "" : $_REQUEST['website']) . "',
						website2='" . (("http://" == $_REQUEST['website2']) ? "" : $_REQUEST['website2']) . "',
						email='" . $_REQUEST['email'] . "',
						business_nature='" . $_REQUEST['business_nature'] . "',
						bank_detail='" . $_REQUEST['bank_detail'] . "',
						where_to_find='" . $_REQUEST['where_to_find'] . "',
						created_by='" . $GLOBALS['user_details']['id'] . "',
						active='" . $_REQUEST['active'] . "'";
    $DB->query($supplier_sql);
    $insert_id = $DB->insert_id();

    // Supply types
    if ("on" == $_REQUEST['mach']) {
        $DB->query(genTypeSQL(1, $insert_id, $dbp));
    }
    if ("on" == $_REQUEST['kits']) {
        $DB->query(genTypeSQL(2, $insert_id, $dbp));
    }
    if ("on" == $_REQUEST['part']) {
        $DB->query(genTypeSQL(3, $insert_id, $dbp));
    }
    if ("on" == $_REQUEST['merc']) {
        $DB->query(genTypeSQL(4, $insert_id, $dbp));
    }
    if ("on" == $_REQUEST['serv']) {
        $DB->query(genTypeSQL(5, $insert_id, $dbp));
    }

    // Contacts (should be only one)
    if (trim($_REQUEST['contact_name'])) {
        // Only add him if he's there ;)
        $contacts_sql = "INSERT INTO " . $dbp . "suppliers_contacts SET
					supplier_id='$insert_id',
					contact_name='" . $_REQUEST['contact_name'] . "',
					contact_phone='XXXXXX',
					contact_email='" . $_REQUEST['contact_email'] . "',
					contact_position='" . $_REQUEST['contact_position'] . "'";
        $DB->query($contacts_sql);
        if (trim($_REQUEST['contact2_name'])) {
            $contacts_sql = "INSERT INTO " . $dbp . "suppliers_contacts SET
						supplier_id='$insert_id',
						contact_name='" . $_REQUEST['contact2_name'] . "',
						contact_phone='XXXXXX',
						contact_email='" . $_REQUEST['contact2_email'] . "',
						contact_position='" . $_REQUEST['contact2_position'] . "'";
            $DB->query($contacts_sql);
        }
    }

    if ($_REQUEST['new_note']) {
        $note_sql = "INSERT INTO {$dbp}suppliers_adminnotes SET
				supplier_id='$insert_id',
				user_id='{$GLOBALS['user_details']['id']}',
				date=UNIX_TIMESTAMP(NOW()),
				note='{$_REQUEST['new_note']}'";
        $DB->query($note_sql);
    }

    $ret        = listSuppliers();
    $ret['msg'] = "New Supplier Added";
    return $ret;
}

// Function added: 4/10/05 - Jwalit Shah
// add function for the new template
function doAddSupplierJS()
{
    // init
    global $DB, $config, $contacts;
    $dbp = $config['db_prefix'];

    // Take info from addSupplier and stick it where it is supposed to go
    // Supplier details
    $supplier_sql = "INSERT INTO " . $dbp . "suppliers SET
						supplier_name='" . $_REQUEST['supplier_name'] . "',
						address='" . $_REQUEST['address'] . "',
						address2='" . $_REQUEST['address2'] . "',
						city='" . $_REQUEST['city'] . "',
						pcode='" . $_REQUEST['zip'] . "',
						factory_name='" . $_REQUEST['company'] . "',
						supplier_name_ch='" . $_REQUEST['supplier_name_ch'] . "',
						address_ch='" . $_REQUEST['address_ch'] . "',
						address2_ch='" . $_REQUEST['address2_ch'] . "',
						city_ch='" . $_REQUEST['city_ch'] . "',
						factory_name_ch='" . $_REQUEST['company_ch'] . "',
						phone='" . $_REQUEST['phone'] . "',
						fax='" . $_REQUEST['fax'] . "',
						website='" . (("http://" == $_REQUEST['website']) ? "" : $_REQUEST['website']) . "',
						email='" . $_REQUEST['sup_email'] . "',
						where_to_find='" . $_REQUEST['where_to_find'] . "',
						created_by='" . $GLOBALS['user_details']['id'] . "',
						country_code='" . $_REQUEST['country'] . "',
						active='" . $_REQUEST['active'] . "'";
    //echo 'Supplier qry = '.$supplier_sql.'<br>';
    $DB->query($supplier_sql);
    $insert_id = $DB->insert_id();

    // Supply types
    if ("on" == $_REQUEST['mach']) {
        $DB->query(genTypeSQL(1, $insert_id, $dbp));
    }
    if ("on" == $_REQUEST['kits']) {
        $DB->query(genTypeSQL(2, $insert_id, $dbp));
    }
    if ("on" == $_REQUEST['part']) {
        $DB->query(genTypeSQL(3, $insert_id, $dbp));
    }
    if ("on" == $_REQUEST['merc']) {
        $DB->query(genTypeSQL(4, $insert_id, $dbp));
    }
    if ("on" == $_REQUEST['serv']) {
        $DB->query(genTypeSQL(5, $insert_id, $dbp));
    }

    $contacts    = $_REQUEST['contacts']; // total contacts
    $w_address   = $_REQUEST['w_address']; // total warehouse address
    $pay_methods = $_REQUEST['pay_method']; // total payment methods

    /*echo 'Total contacts = '.$contacts.'<br>';
    echo 'Total W_address = '.$w_address.'<br>';
    echo 'Total pay_methods = '.$pay_methods.'<br>';*/// Just for test purposes...

    // contacts
    global $contact;
    for ($i = 0; $i < $contacts; $i++) {
        $name  = $contact[$i]['name'];
        $phone = $contact[$i]['phone'];
        $email = $contact[$i]['email'];
        $title = $contact[$i]['title'];
        if ("" != $name) {
            // only enter if the name is present...
            $contacts_sql = "INSERT INTO " . $dbp . "suppliers_contacts SET supplier_id='$insert_id', contact_name='$name', contact_phone='$phone',  contact_email='$email', contact_position='$title'";
            //echo 'Contact query = '.$contacts_sql.'<br>';
            $DB->query($contacts_sql);
        }
    }

    // payment methods
    global $pay;
    for ($i = 0; $i < $pay_methods; $i++) {
        $type    = $pay[$i]['type'];
        $details = $pay[$i]['details'];
        $notes   = $pay[$i]['notes'];
        if ("" != $details) {
            $pay_sql = "INSERT INTO " . $dbp . "suppliers_payment SET supplier_id='$insert_id', type='$type', details='$details', notes='$notes'";
            //echo 'Payment query = '.$pay_sql.'<br>';
            $DB->query($pay_sql);
        }
    }

    // warehouse addresses
    global $warehouse;
    for ($i = 0; $i < $w_address; $i++) {
        $comp     = $warehouse[$i]['company'];
        $addr     = $warehouse[$i]['address'];
        $addr2    = $warehouse[$i]['address2'];
        $city     = $warehouse[$i]['city'];
        $zip      = $warehouse[$i]['zip'];
        $country  = $warehouse[$i]['country'];
        $comments = $warehouse[$i]['comments'];
        $phone    = $warehouse[$i]['phone'];
        $fax      = $warehouse[$i]['fax'];
        $website  = $warehouse[$i]['website'];
        $email    = $warehouse[$i]['email'];

        if ("" != $country) {
            $ware_sql = "INSERT INTO " . $dbp . "suppliers_warehouse SET supplier_id='$insert_id', company='$comp', address='$addr', address2='$addr2', city='$city', zip='$zip', country='$country', comments='$comments', phone='$phone', fax='$fax', website='$website', email='$email' ";
            //echo 'Warehouse query = '.$ware_sql.'<br>';
            $DB->query($ware_sql);
        }
    }

    if ($_REQUEST['new_note']) {
        $note_sql = "INSERT INTO {$dbp}suppliers_adminnotes SET
				supplier_id='$insert_id',
				user_id='{$GLOBALS['user_details']['id']}',
				date=UNIX_TIMESTAMP(NOW()),
				note='{$_REQUEST['new_note']}'";
        $DB->query($note_sql);
    }

    $ret        = listSuppliers();
    $ret['msg'] = "New Supplier Added";
    return $ret;
}

// Do Edit Supplier Function
// Takes data posted by the edit supplier form and updates the DB
/*function doEditSupplier()
{
// init
global $DB, $config;
$dbp = $config['db_prefix'];
$id = $_REQUEST['id'];

$supplier = checkIDGetSupplier();
if(!$supplier)
{
$ret = listSuppliers();
$ret['msg'] = "Valid ID not supplied!";
return $ret;
}

// See if an update has been made, do DB stuff if necessary
// TODO: JS in appropriate template
if($_REQUEST['supplier_updated'])
{
$up_supplier_sql = "UPDATE " . $dbp . "suppliers SET
supplier_name='" . $_REQUEST['supplier_name'] . "',
address='" . $_REQUEST['address'] . "',
factory_name='" . $_REQUEST['factory_name'] . "',
factory_address='" . $_REQUEST['factory_address'] . "',
doffice_name='" . $_REQUEST['doffice_name'] . "',
doffice_address='" . $_REQUEST['doffice_address'] . "',
supplier_name_ch='" . $_REQUEST['supplier_name_ch'] . "',
address_ch='" . $_REQUEST['address_ch'] . "',
factory_name_ch='" . $_REQUEST['factory_name_ch'] . "',
factory_address_ch='" . $_REQUEST['factory_address_ch'] . "',
doffice_name_ch='" . $_REQUEST['doffice_name_ch'] . "',
doffice_address_ch='" . $_REQUEST['doffice_address_ch'] . "',
country_code='" . $_REQUEST['country'] . "',
phone_cc='" . $_REQUEST['phone_cc'] . "',
phone_ac='" . $_REQUEST['phone_ac'] . "',
phone='" . $_REQUEST['phone'] . "',
fax_cc='" . $_REQUEST['fax_cc'] . "',
fax_ac='" . $_REQUEST['fax_ac'] . "',
fax='" . $_REQUEST['fax'] . "',
factory_phone_cc='" . $_REQUEST['factory_phone_cc'] . "',
factory_phone_ac='" . $_REQUEST['factory_phone_ac'] . "',
factory_phone='" . $_REQUEST['factory_phone'] . "',
factory_fax_cc='" . $_REQUEST['factory_fax_cc'] . "',
factory_fax_ac='" . $_REQUEST['factory_fax_ac'] . "',
factory_fax='" . $_REQUEST['factory_fax'] . "',
doffice_phone_cc='" . $_REQUEST['doffice_phone_cc'] . "',
doffice_phone_ac='" . $_REQUEST['doffice_phone_ac'] . "',
doffice_phone='" . $_REQUEST['doffice_phone'] . "',
doffice_fax_cc='" . $_REQUEST['doffice_fax_cc'] . "',
doffice_fax_ac='" . $_REQUEST['doffice_fax_ac'] . "',
doffice_fax='" . $_REQUEST['doffice_fax'] . "',
website='" . (($_REQUEST['website']=="http://")?"":$_REQUEST['website']) . "',
website2='" . (($_REQUEST['website2']=="http://")?"":$_REQUEST['website2']) . "',
email='" . $_REQUEST['email'] . "',
business_nature='" . $_REQUEST['business_nature'] . "',
bank_detail='" . $_REQUEST['bank_detail'] . "',
where_to_find='" . $_REQUEST['where_to_find'] . "',
active='" . $_REQUEST['active'] . "'
WHERE
supplier_id='$id'";
$DB->query($up_supplier_sql);
}
if($_REQUEST['contacts_updated'])
{
$up_contacts_sql = "    UPDATE " . $dbp . "suppliers_contacts SET
contact_email='" . $_REQUEST['contact_email'] . "'
WHERE
contact_email='" . $_REQUEST['edit_contact'] . "'";
$DB->query($up_contacts_sql);
}
if($_REQUEST['types_updated'])
{
// Delete all types from DB, then re-insert appropriate ones
$cleartype_sql = "DELETE FROM " . $dbp . "suppliers_supplytypes WHERE supplier_id='$id'";
$DB->query($cleartype_sql);

if($_REQUEST['mach'] == "on")
{
$DB->query(genTypeSQL(1, $id, $dbp));
}
if($_REQUEST['kits'] == "on")
{
$DB->query(genTypeSQL(2, $id, $dbp));
}
if($_REQUEST['part'] == "on")
{
$DB->query(genTypeSQL(3, $id, $dbp));
}
if($_REQUEST['merc'] == "on")
{
$DB->query(genTypeSQL(4, $id, $dbp));
}
if($_REQUEST['serv'] == "on")
{
$DB->query(genTypeSQL(5, $id, $dbp));
}

}

if($_REQUEST['new_note'])
{
$note_sql = "INSERT INTO {$dbp}suppliers_adminnotes SET
supplier_id='$id',
user_id='{$GLOBALS['user_details']['id']}',
date=UNIX_TIMESTAMP(NOW()),
note='{$_REQUEST['new_note']}'";
$DB->query($note_sql);
}

unlock($dbp . "suppliers", array("supplier_id" => $id));
$ret = listSuppliers();
$ret['msg'] = "Supplier $id Updated.";
return $ret;
}
 */

// New Edit Function: Written by Jwalit Shah
function doEditSupplierJS()
{
    // init
    global $DB, $config;
    $dbp = $config['db_prefix'];
    $id  = $_REQUEST['id'];

    $supplier = checkIDGetSupplier();
    if (!$supplier) {
        $ret        = listSuppliers();
        $ret['msg'] = "Valid ID not supplied!";
        return $ret;
    }

    // See if an update has been made, do DB stuff if necessary
    // TODO: JS in appropriate template
    $up_supplier_sql = "UPDATE " . $dbp . "suppliers SET
					supplier_name='" . $_REQUEST['supplier_name'] . "',
					address='" . $_REQUEST['address'] . "',
					address2='" . $_REQUEST['address2'] . "',
					city='" . $_REQUEST['city'] . "',
					pcode='" . $_REQUEST['zip'] . "',
					factory_name='" . $_REQUEST['company'] . "',
					supplier_name_ch='" . $_REQUEST['supplier_name_ch'] . "',
					address_ch='" . $_REQUEST['address_ch'] . "',
					address2_ch='" . $_REQUEST['address2_ch'] . "',
					city_ch='" . $_REQUEST['city_ch'] . "',
					pcode_ch='" . $_REQUEST['zip_ch'] . "',
					factory_name_ch='" . $_REQUEST['company_ch'] . "',
					phone='" . $_REQUEST['phone'] . "',
					fax='" . $_REQUEST['fax'] . "',
					website='" . (("http://" == $_REQUEST['website']) ? "" : $_REQUEST['website']) . "',
					email='" . $_REQUEST['sup_email'] . "',
					where_to_find='" . $_REQUEST['where_to_find'] . "',
					created_by='" . $GLOBALS['user_details']['id'] . "',
					country_code='" . $_REQUEST['country'] . "',
					active='" . $_REQUEST['active'] . "'
				WHERE
					supplier_id='$id'";
    $DB->query($up_supplier_sql);

    $contacts    = $_REQUEST['contacts']; // total contacts
    $w_address   = $_REQUEST['w_address']; // total warehouse address
    $pay_methods = $_REQUEST['pay_method']; // total payment methods

    global $contact;
    for ($i = 0; $i < $contacts; $i++) {
        $name  = $contact[$i]['name'];
        $phone = $contact[$i]['phone'];
        $email = $contact[$i]['email'];
        $title = $contact[$i]['title'];
        $c_id  = $contact[$i]['id'];
        if ("" == $c_id) {
            // ie a new entry..
            if ("" != $name) {
                $contacts_sql = "INSERT INTO " . $dbp . "suppliers_contacts SET supplier_id='$id', contact_name='$name', contact_phone='$phone',  contact_email='$email', contact_position='$title'";
                $DB->query($contacts_sql);
            }
        } else {
            // update the entry regardless....
            if ("" != $name) {
                $sql = "UPDATE " . $dbp . "suppliers_contacts SET contact_name='$name', contact_phone='$phone',  contact_email='$email', contact_position='$title' WHERE contact_id = '$c_id'";
                $DB->query($sql);
            }
        }
    }

    global $warehouse;
    for ($i = 0; $i < $w_address; $i++) {
        $comp     = $warehouse[$i]['company'];
        $addr     = $warehouse[$i]['address'];
        $addr2    = $warehouse[$i]['address2'];
        $city     = $warehouse[$i]['city'];
        $zip      = $warehouse[$i]['zip'];
        $country  = $warehouse[$i]['country'];
        $comments = $warehouse[$i]['comments'];
        $phone    = $warehouse[$i]['phone'];
        $fax      = $warehouse[$i]['fax'];
        $website  = $warehouse[$i]['website'];
        $email    = $warehouse[$i]['email'];
        $w_id     = $warehouse[$i]['id'];
        if (trim($w_id) == "") {
            // ie a new entry..
            if ("" != $addr) {
                $ware_sql = "INSERT INTO " . $dbp . "suppliers_warehouse SET supplier_id='$id', company='$comp', address='$addr', address2='$addr2', city='$city', zip='$zip', country='$country', comments='$comments', phone='$phone', fax='$fax', website='$website', email='$email' ";
                $DB->query($ware_sql);
            }
        } else {
            // update the entry regardless....
            if ("" != $addr) {
                $ware_sql = "UPDATE " . $dbp . "suppliers_warehouse SET company='$comp', address='$addr', address2='$addr2', city='$city', zip='$zip', country='$country', comments='$comments', phone='$phone', fax='$fax', website='$website', email='$email' WHERE id = '$w_id'";
                $DB->query($ware_sql);
            }
        }
    }

    global $pay;
    for ($i = 0; $i < $pay_methods; $i++) {
        $type    = $pay[$i]['type'];
        $details = $pay[$i]['details'];
        $notes   = $pay[$i]['notes'];
        $p_id    = $pay[$i]['id'];
        if ("" == $p_id) {
            // ie a new entry
            if ("" != $details) {
                $pay_sql = "INSERT INTO " . $dbp . "suppliers_payment SET supplier_id='$id', type='$type', details='$details', notes='$notes'";
                $DB->query($pay_sql);
            }
        } else {
            // update the entry regardless...
            if ("" != $details) {
                $pay_sql = "UPDATE " . $dbp . "suppliers_payment SET type='$type', details='$details', notes='$notes' WHERE id = '$p_id'";
                $DB->query($pay_sql);
            }
        }
    }
    // Delete all types from DB, then re-insert appropriate ones
    $cleartype_sql = "DELETE FROM " . $dbp . "suppliers_supplytypes WHERE supplier_id='$id'";
    $DB->query($cleartype_sql);

    if ("on" == $_REQUEST['mach']) {
        $DB->query(genTypeSQL(1, $id, $dbp));
    }
    if ("on" == $_REQUEST['kits']) {
        $DB->query(genTypeSQL(2, $id, $dbp));
    }
    if ("on" == $_REQUEST['part']) {
        $DB->query(genTypeSQL(3, $id, $dbp));
    }
    if ("on" == $_REQUEST['merc']) {
        $DB->query(genTypeSQL(4, $id, $dbp));
    }
    if ("on" == $_REQUEST['serv']) {
        $DB->query(genTypeSQL(5, $id, $dbp));
    }

    if ($_REQUEST['new_note']) {
        $note_sql = "INSERT INTO {$dbp}suppliers_adminnotes SET
				supplier_id='$id',
				user_id='{$GLOBALS['user_details']['id']}',
				date=UNIX_TIMESTAMP(NOW()),
				note='{$_REQUEST['new_note']}'";
        $DB->query($note_sql);
    }

    unlock($dbp . "suppliers", array("supplier_id" => $id));
    $ret        = listSuppliers();
    $ret['msg'] = "Supplier $id Updated.";
    return $ret;
}

// Do Delete Supplier Function
// Deletes a supplier if confirmed via deleteSupplier()
function doDeleteSupplier()
{
    // init
    global $DB, $config;
    $dbp = $config['db_prefix'];
    $id  = $_REQUEST['id'];

    $supplier = checkIDGetSupplier();
    if (!$supplier) {
        $ret        = listSuppliers();
        $ret['msg'] = "Valid ID not supplied!";
        return $ret;
    }

    $del_supplier_sql = "DELETE FROM " . $dbp . "suppliers WHERE supplier_id='$id'";
    $del_contacts_sql = "DELETE FROM " . $dbp . "suppliers_contacts WHERE supplier_id='$id'";
    $del_types_sql    = "DELETE FROM " . $dbp . "suppliers_supplytypes WHERE supplier_id='$id'";

    $DB->query($del_supplier_sql);
    $DB->query($del_contacts_sql);
    $DB->query($del_types_sql);

    // TODO: Delete purchase orders too
    $del_porder_sql  = "DELETE FROM " . $dbp . "purchase_order WHERE supplier_id='$id'";
    $del_porderd_sql = "DELETE FROM " . $dbp . "purchase_order_details WHERE stuff";

    // TODO: Clean everything up ;)

    unlock($dbp . "suppliers", array("supplier_id" => $id));
    $ret        = listSuppliers();
    $ret['msg'] = "Supplier $id Deleted.";
    return $ret;
}

function delete_con()
{
    global $DB;
    $type  = $_REQUEST['type'];
    $id    = $_REQUEST['id'];
    $table = "new_suppliers_$type";

    //echo 'Type = '.$type.' & id = '.$id.' & table = '.$table.'<br><br>';

    // now delete the id from the table..
    $sql = "DELETE FROM `$table` WHERE ";
    if ('contacts' == $type) {
        $sql .= "`contact_id` = '$id'";
    } else if ('payment' == $type) {
        $sql .= "id = '$id'";
    }

    //echo "SQL query:<br>$sql";
    if ($DB->query($sql)) {
        if ('contacts' == $type) {
            echo 'Contact successfully deleted from the database.<br><br><div align="left"><a href="javascript:window.close()">Close Window</a></div>';
        } else {
            echo 'Payment method successfully deleted from the database.<br><br><div align="left"><a href="javascript:window.close()">Close Window</a></div>';
        }
    } else {
        $ret['msg'] = mysqli_error();
    }

    return $ret;
}

function supplierDetails()
{
    global $DB, $config;
    $dbp = $config['db_prefix'];

    // Resolve Supplier from name if no ID
    if (!$_REQUEST['id']) {
    }

    if (!array_key_exists("id", $_REQUEST)) {
        // We have no ID
        return false;
    } else {
        $id = $_REQUEST['id'];
    }

    // Grab Supplier info from DB
    $supplier_sql = "SELECT * FROM " . $dbp . "suppliers WHERE supplier_id='$id'";
    $DB->query($supplier_sql);
    $supplier = $DB->fetch_array();
    if (!$DB->num_rows()) {
        // If no rows, we have a bad ID
        return false;
    }

    // Do Template
    $page = new Template($config['template_path']);
    $page->set("s", $supplier);
//    echo "<pre>".print_r($supplier,1)."</pre>";
    echo $page->fetch("supplier_short_details.tpl.php");
    return true;
}

// Check to see if $_REQUEST['id'] exists and is valid
// Returns the supplier array if it's ok
function checkIDGetSupplier()
{
    // init
    global $DB, $config;
    $dbp = $config['db_prefix'];

    if (!array_key_exists("id", $_REQUEST)) {
        // We have no ID
        return false;
    } else {
        $id = $_REQUEST['id'];
    }

    if (locked($dbp . "suppliers", array("supplier_id" => $id), true)) {
        if (!my_lock($dbp . "suppliers", array("supplier_id" => $id))) {
            return false;
        }
    }
    lock($dbp . "suppliers", array("supplier_id" => $id));

    // Grab Supplier info from DB
    $supplier_sql = "SELECT * FROM " . $dbp . "suppliers WHERE supplier_id='$id'";
    $DB->query($supplier_sql);
    $supplier = $DB->fetch_array();
    if (!$DB->num_rows()) {
        // If no rows, we have a bad ID
        return false;
    }

    return $supplier;
}

function genTypeSQL($typecode, $id, $dbp)
{
    return "INSERT INTO " . $dbp . "suppliers_supplytypes SET
			supplier_id='$id',
			supply_type='$typecode'";
}
