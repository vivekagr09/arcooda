<?php
/***********************
 * General Variables Admin Core Logic
 * Filename: stuff_admin.php
 ***********************
 * Purpose:
 *    Do all the relevant stuff for administering some random elements like couriers,
 *    shipping addresses, etc.
 * Configuration Variables used:
 *    $config['db_prefix']
 *    $config['template_path']
 * Functions defined:
 *    NONE
 ***********************
 * TODO:
 *    Locking?
 ***********************/
include_once __DIR__ . '/common.inc.php';

moduleLoad('template');

include_once "spo_config.php";

include_once "functions/lock.inc.php"; // DB Locking (requires DB)
include_once "functions/menu.inc.php"; // Menu updater (requires DB)

// Handle request for action
switch ($_REQUEST['action']) {
    case "update":
        $content = updateItems();
        break;
    case "add":
        $content = showEditForm();
        break;
    case "edit":
        $content = showEditForm();
        break;
    case "del":
        $content = deleteItem();
        break;
    case "list":
        $content = listItems();
        break;
    default:
        break;
}

$content['header_links']['Add Shipping Destination']       = "stuff_admin.php?section=dest&amp;action=add";
$content['header_links']['View All Shipping Destinations'] = "stuff_admin.php?section=dest&amp;action=list";
$content['header_links']['Add Shipping Term']              = "stuff_admin.php?section=term&amp;action=add";
$content['header_links']['View All Shipping Terms']        = "stuff_admin.php?section=term&amp;action=list";
$content['header_links']['Add Payment Method']             = "stuff_admin.php?section=paym&amp;action=add";
$content['header_links']['View All Payment Methods']       = "stuff_admin.php?section=paym&amp;action=list";
$content['linksperline']                                   = 2;

$content['counts'] = menuCounters($config['db_prefix']);
outputHTML($content, $msg, $config['template_path']);

function listItems()
{
    // init
    global $DB, $config;
    $dbp        = $config['db_prefix'];
    $dests      = array();
    $terms      = array();
    $paymethods = array();
    $page       = new Template($config['template_path']);

    switch ($_REQUEST['section']) {
        case "dest":
            // Get Address Details
            $address_sql = "SELECT * FROM {$dbp}ship_destinations";
            $DB->query($address_sql);
            while ($a = $DB->fetch_array()) {
                $dests[] = $a;
            }
            $page->set("dests", $dests);
            $ret['content'] = $page->fetch("stuff_edit_dest.tpl.php");
            $ret['title']   = "Shipping Destinations";
            break;

        case "term":
            // Get Shipping Terms
            $term_sql = "SELECT * FROM {$dbp}shipping_terms";
            $DB->query($term_sql);
            while ($t = $DB->fetch_array()) {
                $terms[] = $t;
            }
            $page->set("terms", $terms);
            $ret['content'] = $page->fetch("stuff_edit_term.tpl.php");
            $ret['title']   = "Shipping Terms";
            break;

        case "paym":
            // Get Payment Methods
            $paym_sql = "SELECT * FROM {$dbp}payment_methods";
            $DB->query($paym_sql);
            while ($p = $DB->fetch_array()) {
                $paymethods[] = $p;
            }
            $page->set("paymethods", $paymethods);
            $ret['content'] = $page->fetch("stuff_edit_paym.tpl.php");
            $ret['title']   = "Payment Methods";
            break;
    }

    // Do Template
    $ret['msg'] = "";
    return $ret;
}

function updateItems()
{
    // init
    global $DB, $config;
    $dbp = $config['db_prefix'];

    switch ($_REQUEST['section']) {
        case "dest":
            // Do Magic Updates: Shipping Addresses
            if (count($_POST['shipdest'])) {
                foreach ($_POST['shipdest'] as $d) {
                    if ($d['modified']) {
                        if ("" == $d['name']) {
                            $msg .= "Removing Destination {$d['dest_id']}... ";
                            $cdelete_sql = "DELETE FROM {$dbp}ship_destinations WHERE dest_id='{$d['dest_id']}'";
                            $DB->query($cdelete_sql);
                            $msg .= "Done<br />\r\n";
                        } else {
                            $msg .= "Updating Destination ID {$d['dest_id']}... ";
                            $dupdate_sql = "UPDATE {$dbp}ship_destinations SET
								name='{$d['name']}',
								address='{$d['address']}'
							WHERE dest_id='{$d['dest_id']}'";
                            $DB->query($dupdate_sql);
                            $msg .= "Done<br />";
                        }
                    }
                }
            }

            break;

        case "term":
            // Do Magic Updates: Shipping Terms
            if (count($_POST['terms'])) {
                foreach ($_POST['terms'] as $t) {
                    if ($t['modified']) {
                        if ("" == $t['term']) {
                            $msg .= "Removing Shipping Term {$t['term_id']}... ";
                            $cdelete_sql = "DELETE FROM {$dbp}shipping_terms WHERE term_id='{$t['term_id']}'";
                            $DB->query($cdelete_sql);
                            $msg .= "Done<br />\r\n";
                        } else {
                            $msg .= "Updating Shipping Term {$t['term_id']}... ";
                            $tupdate_sql = "UPDATE {$dbp}shipping_terms SET
										term='{$t['term']}'
									WHERE term_id='{$t['term_id']}'";
                            $DB->query($tupdate_sql);
                            $msg .= "Done<br />";
                        }
                    }
                }
            }

            break;

        case "paym":
            // Do Magic Updates: Payment Methods
            if (count($_POST['paymethods'])) {
                foreach ($_POST['paymethods'] as $p) {
                    if ($p['modified']) {
                        if ("" == $p['text']) {
                            $msg .= "Removing Mayment Method {$p['paymethod_id']}... ";
                            $cdelete_sql = "DELETE FROM {$dbp}payment_methods WHERE paymethod_id='{$p['paymethod_id']}'";
                            $DB->query($cdelete_sql);
                            $msg .= "Done<br />\r\n";
                        } else {
                            $msg .= "Updating paymethod ID {$p['paymethod_id']}... ";
                            $pupdate_sql = "UPDATE {$dbp}payment_methods SET
										text='{$p['text']}'
									WHERE paymethod_id='{$p['paymethod_id']}'";
                            $DB->query($pupdate_sql);
                            $msg .= "Done<br />";
                        }
                    }
                }
            }

            break;
    }

    switch ($_REQUEST['section']) {
        case "dest":
            // Do Magic Add: New Shipping Address
            if (!(
                ("[new]" == $_POST['newdest']['name']) ||
                ("" == $_POST['newdest']['name']) ||
                ("[new]" == $_POST['newdest']['address'])
            )) {
                $msg .= "Adding new Shipping Address... ";
                $newd_sql = "INSERT INTO {$dbp}ship_destinations SET
					name='{$_POST['newdest']['name']}',
					address='{$_POST['newdest']['address']}'";
                $DB->query($newd_sql);
                $msg .= "Done<br />\r\n";
            }
            break;

        case "term":
            // Do Magic Add: New Shipping Term
            if (("[new]" != $_POST['newterm']['term']) && ("" != $_POST['newterm']['term'])) {
                $msg .= "Adding new Term... ";
                $newt_sql = "INSERT INTO {$dbp}shipping_terms SET
					term='{$_POST['newterm']['term']}'";
                $DB->query($newt_sql);
                $msg .= "Done<br />\r\n";
            }
            break;

        case "paym":
            // Do Magic Add: New Payment Method
            if (("[new]" != $_POST['newpaymethod']['text']) && ("" != $_POST['newpaymethod']['text'])) {
                $msg .= "Adding new Payment Method... ";
                $newp_sql = "INSERT INTO {$dbp}payment_methods SET
					text='{$_POST['newpaymethod']['text']}'";
                $DB->query($newp_sql);
                $msg .= "Done<br />\r\n";
            }
            break;
    }

    $ret        = listItems();
    $ret['msg'] = $msg;
    return $ret;
}

function showEditForm()
{
    // init
    global $DB, $config;
    $dbp  = $config['db_prefix'];
    $page = new Template($config['template_path']);

    switch ($_REQUEST['section']) {
        case "dest":
            if ($_REQUEST['id']) {
                // Editing existing item
                // Get Address Details
                $address_sql = "SELECT * FROM {$dbp}ship_destinations WHERE dest_id={$_REQUEST['id']}";
                $address     = $DB->query_first($address_sql);
                $page->set("d", $address);
                $ret['content'] = $page->fetch("stuff_medit_dest.tpl.php");
            } else {
                // Adding new item
                $ret['content'] .= $page->fetch("stuff_add_dest.tpl.php");
            }
            break;
        case "term":
            if ($_REQUEST['id']) {
                // Editing existing item
                $term_sql = "SELECT * FROM {$dbp}shipping_terms WHERE term_id='{$_REQUEST['id']}'";
                $term     = $DB->query_first($term_sql);
                $page->set("t", $term);
                $ret['content'] = $page->fetch("stuff_medit_term.tpl.php");
            } else {
                // Adding new item
                $ret['content'] .= $page->fetch("stuff_add_term.tpl.php");
            }
            break;
        case "paym":
            if ($_REQUEST['id']) {
                // Editing existing item
                $paym_sql = "SELECT * FROM {$dbp}payment_methods WHERE paymethod_id='{$_REQUEST['id']}'";
                $paym     = $DB->query_first($paym_sql);
                $page->set("p", $paym);
                $ret['content'] = $page->fetch("stuff_medit_paym.tpl.php");
            } else {
                // Adding new item
                $ret['content'] .= $page->fetch("stuff_add_paym.tpl.php");
            }
            break;
    }

    return $ret;
}

function deleteItem()
{
    // init
    global $DB, $config;
    $dbp  = $config['db_prefix'];
    $page = new Template($config['template_path']);

    if ($_REQUEST['id']) {
        $me = array();
        switch ($_REQUEST['section']) {
            case "dest":
                $me['modified']       = true;
                $me['dest_id']        = $_REQUEST['id'];
                $me['name']           = "";
                $_POST['shipdest'][0] = $me;
                break;
            case "term":
                $me['modified']    = true;
                $me['term_id']     = $_REQUEST['id'];
                $me['term']        = "";
                $_POST['terms'][0] = $me;
                break;
            case "paym":
                $me['modified']         = true;
                $me['paymethod_id']     = $_REQUEST['id'];
                $me['text']             = "";
                $_POST['paymethods'][0] = $me;
                break;
        }
        return updateItems();
    }
}
