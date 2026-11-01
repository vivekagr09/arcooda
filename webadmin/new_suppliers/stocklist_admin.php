<?php
/***********************
 * Stocklist Administration Page
 * Filename: stocklist_admin.php
 ***********************
 * Purpose:
 *    Main logic for dealing with Stocklists from suppliers
 * Configuration Variables used:
 *    $config['db_prefix']
 *    $config['template_path']
 * Functions defined:
 *    retrieveStockList($supplier_id)                    - Returns an array with all stock details for the given supplier
 *    updateStockListItem($stock_item, $supplier_id)    - Does either an insert or an update on the stocklist, depending on
 *                                                        whether the product already exists.
 ***********************/

include_once __DIR__ . '/common.inc.php';

moduleLoad('template');

// All the setupness
require_once "spo_config.php";

require_once "functions/currency.inc.php"; // Currency Functions (requires DB)
require_once "functions/country.inc.php"; // Country Functions (requires DB)
require_once "functions/shipping.inc.php"; // Shipping Functions (requires DB)
require_once "functions/user.inc.php"; // User Functions (requires DB)
require_once "functions/supplier_contact.inc.php"; // Supplier Magic (Requires DB)
require_once "functions/menu.inc.php"; // Menu updater (requires DB)

// Function select switch
switch ($_REQUEST['action']) {
    case "add":
        $content = addStocklist();
        break;
    case "edit":
        $content = editStocklist();
        break;
    case "delete_item":
        $content = delStocklistItem();
        break;
    case "do_add":
        $content = doAddStocklist();
        break;
    case "do_edit":
        $content = doEditStocklist();
        break;
    case "do_delete_item":
        $content = doDelStocklistItem();
        break;
    case "search":
        $content = searchStocklist();
        break;
    default:
        if ($_REQUEST['activesubmit']) {
            fixActives();
        }

        $content = listStock();
        break;
}

$content['header_links']['Add Stock']      = "stocklist_admin.php?action=add" . ($_REQUEST['supplier_id'] ? "&amp;supplier_id={$_REQUEST['supplier_id']}" : "");
$content['header_links']['Search Stock']   = "stocklist_admin.php?action=search";
$content['header_links']['View All Stock'] = "stocklist_admin.php?action=list";

outputHTML($content, $msg, $config['template_path']);

// LIST stocklist functions_______________________________________________
function listStock()
{
    // init
    global $config, $DB;
    $dbp = $config['db_prefix'];

    // List length variables
    if ($_REQUEST['per_page']) {
        $per_page = $_REQUEST['per_page'];
    } else {
        $per_page = 50;
    }
    $start = $_REQUEST['page'] * $per_page;

    $country_list = getCountryList(false);

    $search_sql = sprintf(
        "SELECT LTRIM(`product_name`) AS `product_name`, `product_id`, `buy_price`, `sell_price`, `percent_markup`, `last_update`, `s`.`active`, `s`.`price_flux`, `sup`.`supplier_id`, `supplier_name`, `supplier_name_ch`, `pt`.`parent`, `ppt`.`description` AS `product_category`, `s`.`location` FROM %sstocklist AS `s` INNER JOIN %ssuppliers AS `sup` ON `s`.`supplier_id` = `sup`.`supplier_id` INNER JOIN `products` AS `p` ON `s`.`product_id` = `p`.`id` INNER JOIN `product_types` AS `pt` ON `p`.`type` = `pt`.`id` INNER JOIN `product_types` AS `ppt` ON `pt`.`parent` = `ppt`.`id`",
        $dbp,
        $dbp
    );

    if ($_REQUEST['searching']) {
        $search_sql .= " WHERE (`supplier_name` LIKE '%{$_REQUEST['sname']}%' OR `supplier_name_ch` LIKE '%{$_REQUEST['sname']}%') AND `product_name` LIKE '%{$_REQUEST['pname']}%'";
        if ($_REQUEST['country']) {
            $search_sql .= " AND `s`.`location` = '{$_REQUEST['country']}'";
        }
        if ($_REQUEST['category']) {
            $search_sql .= " AND `pt`.`parent` = '{$_REQUEST['category']}'";
        }

        $search_args = "?searching={$_REQUEST['searching']}&amp;country={$_REQUEST['country']}&amp;category={$_REQUEST['category']}";
        $search_args .= ($_REQUEST['sname']) ? "&amp;sname={$_REQUEST['sname']}" : "";
        $search_args .= ($_REQUEST['pname']) ? "&amp;pname={$_REQUEST['pname']}" : "";
    }

    // Total number of items
    $DB->query($search_sql);
    $total_items = $DB->num_rows();

    // More searching
    $search_sql .= " ORDER BY `product_name` LIMIT $start, $per_page";

    // DB Magic
    $results   = array();
    $resultset = $DB->query($search_sql);
    if ($DB->num_rows()) {
        while ($r = $DB->fetch_assoc($resultset)) {
            $r['location'] = $country_list[$r['location']] ?? $r['location'];

            // 'Movement' calculation
            $r['movement'] = calcMovement($r['buy_price'], $r['product_id'], $r['supplier_id']);

            /* Chopped for price_flux modifications
            // Calculate % markup if it's not there
            if($r['sell_price'] && !$r['percent_markup'])
            {
            $r['percent_markup'] = 100 * ($r['sell_price'] - $r['buy_price']) / $r['buy_price'];
            $r['pm_notset'] = true;
            }
            // Calculate Sell Price if it's not there
            if($r['percent_markup'] && !$r['sell_price'])
            {
            $r['sell_price'] = ($r['percent_markup'] * $r['buy_price'] / 100) + $r['buy_price'];
            $r['sp_notset'] = true;
            }
             */

            if ($r['price_flux']) {
                // Price Flux, calculate sell price with markup
                $r['sell_price'] = ($r['percent_markup'] * $r['buy_price'] / 100) + $r['buy_price'];
                $r['sp_notset']  = true;
            } else {
                // No price flux, calculate markup from sell price
                $r['percent_markup'] = 100 * ($r['sell_price'] - $r['buy_price']) / $r['buy_price'];
                $r['pm_notset']      = true;
            }

            $results[] = $r;
        }
    } else {
        if ($_REQUEST['searching']) {
            $ret        = searchStocklist();
            $ret['msg'] = errorStringHTML("No Results!");
        } else {
            $ret['msg'] = errorStringHTML("No Stocklists available.");
        }
        return $ret;
    }

    // Misc calculations
    $page_last = $start + $per_page;
    if ($page_last > $total_items) {
        $page_last = $total_items;
    }

    // Do Template
    $page = new Template($config['template_path']);
    $page->set("search_results", $results);
    $page->set("page_first", $start + 1);
    $page->set("page_last", $page_last);
    $page->set("total_items", $total_items);
    $page->set("page_links", makePageLimitLinks($search_args, $total_items, $_REQUEST['page'], $per_page));
    $page->set("show_search", $_REQUEST['searching']);
    $page->set("countries", makeCountryArray());
    $page->set("categories", makeProductCategoryArray());
    $ret['content'] = $page->fetch("stocklist_search.tpl.php");
    $ret['title']   = $_REQUEST['searching'] ? "Search Results" : "All Stock";

    return $ret;
}

// ADD stocklist functions________________________________________________
function addStocklist()
{
    global $config;

    if ($_REQUEST['currency']) {
        $defcurrency = $_REQUEST['currency'];
    } else {
        // Supplier Default Currency here
        $defcurrency = "USD";
    }
    if ($_REQUEST['location']) {
        $defcountry = $_REQUEST['location'];
    } else {
        // Supplier Default Country here
        $defcountry = "US";
    }

    // Do Template
    $page = new Template($config['template_path']);
    $page->set("countries", makeCountryArray());
    $page->set("currencies", makeCurrencyArray());
    $page->set("suppliers", listSuppliers());
    $page->set("sid", $_REQUEST['supplier_id']);
    $page->set("stock", $_REQUEST['stock']); // For reposting data
    $page->set("defcurrency", $defcurrency);
    $page->set("defcountry", $defcountry);
    $page->set("terms", makeTermsArray($GLOBALS['config']['db_prefix']));
    $ret['content'] = $page->fetch("stocklist_add.tpl.php");
    $ret['msg']     = "";
    $ret['title']   = "Add Stocklist";
    return $ret;
}

function doAddStocklist()
{
    // init
    global $DB, $config;
    $dbp = $config['db_prefix'];

    if ($_REQUEST['supplier_id']) {
        // Check for dups
        for ($a = 0; $a < count($_REQUEST['stock']); $a++) {
            for ($b = $a + 1; $b < count($_REQUEST['stock']); $b++) {
                if (($_REQUEST['stock'][$a]['id'] == $_REQUEST['stock'][$b]['id']) && $_REQUEST['stock'][$b]['id']) {
                    //Items Are The Same
                    $ret        = addStocklist();
                    $ret['msg'] = errorStringHTML("Don't duplicate products.");
                    return $ret;
                }
            }
        }
        // Loop over stocklist entries
        foreach ($_REQUEST['stock'] as $s) {
            // Sanity Check product
            // is ID set?
            if ($s['id']) {
                // is name set?
                if ($s['name']) {
                    $pnr = $DB->query_first("SELECT product_name FROM products WHERE id='{$s['id']}'");
                    if (strtolower(trim($pnr['product_name'])) != strtolower(trim($s['name']))) {
                        // Problem!
                        $ret        = addStocklist();
                        $ret['msg'] = errorStringHTML("Error: Name/ID mismatch - {$s['id']}");
                        return $ret;
                    }
                } else {
                    // TODO: search! if one match return it, else error
                    $ret        = addStocklist();
                    $ret['msg'] = errorStringHTML("Error: ID ({$s['id']}) with no name!");
                    return $ret;
                }
            } else {
                // check name
                if ($s['name']) {
                    // if set, TODO:search -> if one result prompt, else error
                    $ret        = addStocklist();
                    $ret['msg'] = errorStringHTML("Product name ({$s['name']}) with no ID!");
                    return $ret;
                } else {
                    continue;
                }
                // Name & ID not set, ignoring this entry!
            }
            /* Obsoleted by price_flux
            // check that [sell] and [markup] are not both set
            if(!((floatval($s['sell'])) XOR (floatval($s['markup']))))
            {
            $ret = addStocklist();
            $ret['msg'] = errorStringHTML("For any product you may set either sell price OR markup but NOT both!");
            return $ret;
            }
             */
            // check that [buy] is set
            if (!floatval($s['buy'])) {
                $ret        = addStocklist();
                $ret['msg'] = errorStringHTML("Please set a buy price for product {$s['id']}.");
                return $ret;
            }

            // SQL Magic
            $s['location'] = $_REQUEST['location'];
            $s['currency'] = $_REQUEST['currency'];
            $s['terms']    = $_REQUEST['terms'];
            updateStockListItem($s, $_REQUEST['supplier_id']);
        }
    } else {
        // This shouldn't happen, unless someone is being silly
        $ret        = addStocklist();
        $ret['msg'] = errorStringHTML("No Supplier ID set. Either no suppliers are active, or you're being silly.");
        return $ret;
    }

    return listStock();
}

// EDIT stocklist functions_______________________________________________
function editStocklist($post_error = false)
{
    // init
    global $DB, $config;
    $dbp   = $config['db_prefix'];
    $stock = array();

    // List length variables
    if ($_REQUEST['per_page']) {
        $per_page = $_REQUEST['per_page'];
    } else {
        $per_page = 50;
    }
    $start = $_REQUEST['page'] * $per_page;

    if ($_REQUEST['supplier_id']) {
        // Grab supplier name
        $supp = $DB->query_first("SELECT supplier_name, supplier_name_ch FROM {$dbp}suppliers WHERE supplier_id='{$_REQUEST['supplier_id']}'");
        // retrieve SL details for this supplier
        $s['supplier_name']    = $supp['supplier_name'];
        $s['supplier_name_ch'] = $supp['supplier_name_ch'];
    } else {
        die("Can't edit a supplier's stockist if you don't tell me who it is! supplier_id=??? Go away!");
    }

    $country_list = getCountryList(false);

    // Do database query - grab details from stocklist table
    $slsql = sprintf(
        "SELECT `s`.*, LTRIM(`p`.`product_name`) AS `product_name`, `pt`.`parent`, `s`.`location`, `st`.`term` FROM %sstocklist AS `s` INNER JOIN `products` AS `p` ON `s`.`product_id` = `p`.`id` INNER JOIN `product_types` AS `pt` ON `p`.`type` = `pt`.`id` LEFT JOIN %sshipping_terms AS `st` ON `s`.`terms_id` = `st`.`term_id` WHERE `supplier_id` = %d ORDER BY `p`.product_name",
        $dbp,
        $dbp,
        $_REQUEST['supplier_id']
    );
    $DB->query($slsql);
    $total_items = $DB->num_rows();
    $slsql .= " LIMIT $start,$per_page";
    $DB->query($slsql);
    while ($slitem = $DB->fetch_array()) {
        $slitem['location_name'] = $country_list[$slitem['location']] ?? $slitem['location'];

        $itemcount++;

        // 'Movement' calculation
        $slitem['movement'] = calcMovement($slitem['buy_price'], $slitem['product_id'], $_REQUEST['supplier_id']);

        // work out sell/markup
        if ($slitem['price_flux']) {
            $slitem['sell_price'] = ($slitem['percent_markup'] * $slitem['buy_price'] / 100) + $slitem['buy_price'];
        } else {
            $slitem['percent_markup'] = 100 * ($slitem['sell_price'] - $slitem['buy_price']) / $slitem['buy_price'];
        }

        // Check repost values
        if ($post_error) {
            if ($slitem['buy_price'] != $_REQUEST['stock'][$itemcount]['buy']) {
                $slitem['buy_price'] = $_REQUEST['stock'][$itemcount]['buy'];
            }
            if ($slitem['sell_price'] != $_REQUEST['stock'][$itemcount]['sell']) {
                $slitem['sell_price'] = $_REQUEST['stock'][$itemcount]['sell'];
            }
            if ($slitem['percent_markup'] != $_REQUEST['stock'][$itemcount]['markup']) {
                $slitem['percent_markup'] = $_REQUEST['stock'][$itemcount]['markup'];
            }
            if ($slitem['qty'] != $_REQUEST['stock'][$itemcount]['qty']) {
                $slitem['qty'] = $_REQUEST['stock'][$itemcount]['qty'];
            }
            if ($slitem['notes'] != $_REQUEST['stock'][$itemcount]['notes']) {
                $slitem['notes'] = $_REQUEST['stock'][$itemcount]['notes'];
            }
        }

        // Add to stocklist array
        $stock[] = $slitem;
    }

    // Misc calculations
    $page_last = $start + $per_page;
    if ($page_last > $total_items) {
        $page_last = $total_items;
    }

    // Pass SL details to template
    $page = new Template($config['template_path']);
    $page->set("stock", $stock);
    $page->set("supplier", $s);
    $page->set("page_first", $start + 1);
    $page->set("page_last", $page_last);
    $page->set("total_items", $total_items);
    $page->set("page_links", makePageLimitLinks("?action=edit&amp;supplier_id={$_REQUEST['supplier_id']}", $total_items, $_REQUEST['page'], $per_page));
    if ($_REQUEST['view']) {
        die($page->fetch("stocklist_print.tpl.php"));
    } else {
        $ret['content'] = $page->fetch("stocklist_edit.tpl.php");
    }
    $ret['title'] = "Edit Stocklist";
    return $ret;
}

function doEditStocklist()
{
    // init
    global $config;
    $dbp = $config['db_prefix'];
    $i   = 0;

    if ($_REQUEST['supplier_id']) {
        // Loop over stocklist entries
        foreach ($_REQUEST['stock'] as $s) {
            if ($s['update']) {
                // Sanity Check product
                /* Obsoleted by price_flux stuff
                // check that [sell] and [markup] are not both set
                if(!((floatval($s['sell'])) XOR (floatval($s['markup']))))
                {
                $ret = editStocklist(true);    // true signifies error. hooray!
                $ret['msg'] = errorStringHTML("For any product you may set either sell price OR markup but NOT both!");
                return $ret;
                }
                 */
                // check that [buy] is set
                if (!floatval($s['buy'])) {
                    $ret        = editStocklist(true); // true signifies error. hooray!
                    $ret['msg'] = errorStringHTML("Please set a buy price for product {$s['id']}.");
                    return $ret;
                }

                $i++;

                // SQL Magic
                updateStockListItem($s, $_REQUEST['supplier_id']);
            }
        }
    } else {
        // This shouldn't happen, unless someone is being silly
        $ret        = editStocklist(true); // true signifies error. hooray!
        $ret['msg'] = errorStringHTML("No Supplier ID set. Either no suppliers are active, or you're being silly.");
        return $ret;
    }

    $ret        = editStocklist();
    $ret['msg'] = "$i products updated.";
    return $ret;
}

function fixActives()
{
    foreach ($_REQUEST['mod'] as $k => $m) {
        if ($m) {
            updateActive($_REQUEST['pid'][$k], $_REQUEST['sid'][$k], $_REQUEST['act'][$k]);
        }
    }
}

// DELETE stocklist item functions________________________________________
function delStocklistItem()
{
    // init
    global $config, $DB;
    $dbp = $config['db_prefix'];

    // Check that this item exists
    $DB->query("SELECT product_id FROM {$dbp}stocklist WHERE supplier_id='{$_REQUEST['supplier_id']}' AND product_id='{$_REQUEST['product_id']}'");
    if (!$DB->num_rows()) {
        if ("edit" == $_REQUEST['from']) {
            $ret = editStocklist();
        } else {
            $ret = listStock();
        }
        $ret['msg'] = errorStringHTML("Selected item does not exist.");
        return $ret;
    }

    // Find names
    $pnr  = $DB->query_first("SELECT product_name FROM products WHERE id='{$_REQUEST['product_id']}'");
    $supp = $DB->query_first("SELECT supplier_name, supplier_name_ch FROM {$dbp}suppliers WHERE supplier_id='{$_REQUEST['supplier_id']}'");
    // Do Template
    $page = new Template($config['template_path']);
    $page->set("pid", $_REQUEST['product_id']);
    $page->set("sid", $_REQUEST['supplier_id']);
    $page->set("supplier_name", $supp[0]);
    $page->set("supplier_name_ch", $supp[1]);
    $page->set("product_name", $pnr[0]);
    $page->set("from", $_REQUEST['from']);
    $ret['content'] = $page->fetch("stocklist_delconfirm.tpl.php");
    $ret['title']   = "Stock Delete Confirmation";
    return $ret;
}

function doDelStocklistItem()
{
    // init
    global $config, $DB;
    $dbp = $config['db_prefix'];

    if ($_REQUEST['go']) {
        // Do delete
        $dele_sql = "DELETE FROM {$dbp}stocklist WHERE supplier_id='{$_REQUEST['supplier_id']}' AND product_id='{$_REQUEST['product_id']}'";
        $DB->query($dele_sql);
    }

    // Figure out where to return to
    if ("edit" == $_REQUEST['from']) {
        $ret = editStocklist();
    } else {
        $ret = listStock();
    }

    if ($_REQUEST['go']) {
        // Notify of deletion
        $ret['msg'] = "Item Deleted.";
    }

    return $ret;
}

// SEARCH stocklist functions_____________________________________________
function searchStocklist()
{
    global $config;

    $page = new Template($config['template_path']);
    $page->set("countries", makeCountryArray());
    $page->set("categories", makeProductCategoryArray());
    $page->set("show_search", true);
    $page->set("search_results", array());
    $ret['content'] = $page->fetch("stocklist_search.tpl.php");
    $ret['title']   = "Search Stocklist";
    return $ret;
}

// Helper Functions_______________________________________________________
function updateStockListItem($stock_item, $supplier_id)
{
    /* Updates an entry in the stocklist */
    // init
    global $DB, $config;
    $dbp = $config['db_prefix'];

    // Check to see if item-supplier combo exists
    $chexql = "SELECT * FROM {$dbp}stocklist WHERE product_id='{$stock_item['id']}' AND supplier_id='$supplier_id'";
    $DB->query($chexql);
    if ($DB->num_rows()) {
        // Item exists, do the updation

        // Record old data
        $old_data = $DB->fetch_array();
        // If last entry is the same, don't update
        $chk = "SELECT * FROM {$dbp}stocklist_history
				WHERE product_id='{$old_data['product_id']}' AND supplier_id='{$old_data['supplier_id']}'
				ORDER BY date DESC";
        $prev_hist = $DB->query_first($chk);
        if ($old_data['buy_price'] != $stock_item['buy'] ||
            $old_data['sell_price'] != $stock_item['sell'] ||
            $old_data['markup'] != $stock_item['markup'] ||
            $old_data['price_flux'] != $stock_item['price_flux']) {
            $hist_sql = "INSERT INTO {$dbp}stocklist_history
							SET
								product_id='{$old_data['product_id']}',
								supplier_id='{$old_data['supplier_id']}',
								buy_price='{$old_data['buy_price']}',
								date='{$old_data['last_update']}',
								qty='{$old_data['qty']}'";
            $DB->query($hist_sql);
        }

        // Do the update
        $update_sql = "UPDATE {$dbp}stocklist
						SET
							active='{$stock_item['active']}',
							price_flux='{$stock_item['price_flux']}',
							last_update=UNIX_TIMESTAMP(NOW()),
							qty='{$stock_item['qty']}',
							notes='{$stock_item['notes']}',
							buy_price='{$stock_item['buy']}',
							sell_price='{$stock_item['sell']}',
							percent_markup='{$stock_item['markup']}'";
        if (isset($stock_item['location'])) {
            // These bits won't be set if sent from the edit form
            $update_sql .= ",
							location='{$stock_item['location']}',
							currency='{$stock_item['currency']}',
							terms_id='{$stock_item['terms']}',
							notes='{$stock_item['notes']}'";
        }
        $update_sql .= "
						WHERE
							product_id='{$stock_item['id']}' AND supplier_id='$supplier_id'";
        $DB->query($update_sql);
    } else {
        // Item does not exist, insert it
        // "Initial" history not required, it gets saved at first update
        $insert_sql = "INSERT INTO {$dbp}stocklist
						SET
							product_id='{$stock_item['id']}',
							supplier_id='$supplier_id',
							active='{$stock_item['active']}',
							price_flux='{$stock_item['price_flux']}',
							last_update=UNIX_TIMESTAMP(NOW()),
							qty='{$stock_item['qty']}',
							buy_price='{$stock_item['buy']}',
							sell_price='{$stock_item['sell']}',
							currency='{$stock_item['currency']}',
							percent_markup='{$stock_item['markup']}',
							location='{$stock_item['location']}',
							terms_id='{$stock_item['terms']}',
							notes='{$stock_item['notes']}'";
        $insert_qry = $DB->query($insert_sql);
    }
}

function updateActive($pid, $sid, $active)
{
    /* Sets the "Active" flag on a stocklist item */
    // init
    global $DB, $config;
    $dbp = $config['db_prefix'];

    // Check to see if item-supplier combo exists
    $chexql = "SELECT * FROM {$dbp}stocklist WHERE product_id='$pid' AND supplier_id='$sid'";
    $DB->query($chexql);
    if ($DB->num_rows()) {
        // Do the update
        $active_sql = "UPDATE {$dbp}stocklist SET active='$active' WHERE product_id='$pid' AND supplier_id='$sid'";
        $DB->query($active_sql);

        return true;
    }
    return false;
}

function calcMovement($curr_price, $product_id, $supplier_id)
{
    // init
    global $DB, $config;
    $dbp = $config['db_prefix'];

    $last_entry = $DB->query_first("SELECT * FROM {$dbp}stocklist_history
									WHERE product_id='{$product_id}' AND supplier_id='$supplier_id'
									ORDER BY date DESC");

    if ($last_entry) {
        return (($curr_price - $last_entry['buy_price']) / $last_entry['buy_price']) * 100;
    } else {
        return 0;
    }
}

// Extras______________________________________________________________________
function makeProductCategoryArray()
{
    global $DB;
    $categ_sql = "SELECT * FROM product_types WHERE parent='0'";
    $DB->query($categ_sql);
    while ($c = $DB->fetch_array()) {
        $categories[] = $c;
    }
    return $categories;
}

function errorStringHTML($string)
{
    return "<span style=\"color: red\">ERROR: $string</span><br />\r\n<br />\r\n";
}
