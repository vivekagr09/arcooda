<!-- addedit.tpl -->

<?php
/**
 * Inputs:
 * - $item
 * - $item_admin_notes
 * - $item_bulk_pricing
 * - $item_bulk_weights
 */
?>

<script type="text/javascript">
    
//<![CDATA[
function checkMetaTags() {
    var title = getElement('item[meta_title]');
    var keys = getElement('item[meta_keywords]');
    var desc = getElement('item[meta_description]');
    var url = getElement('item[slug]');
    if (title && keys && desc && url) {
        if (title.value == '' || keys.value == '' || desc.value == '' || url.value == '') {
            alert('All META Tags and URL Keywords fields are required. Please enter all of them');
            if (title.select) title.select();
            if (title.focus) title.focus();
            return false;
        }
    }
    return true;
}

function checkCountryLocation() {
    var elem2 = getElement('products[country_location]');
    if (elem2.value == '') {
        alert('Please select a country location');
        if (elem2.focus != null) elem2.focus();
        if (elem2.select != null) elem2.select();
        return false;
    }
    return checkMetaTags();
}

function checkCategorySelected() {
    if (Product.get("type") == "0") {
        alert('Please select a Product Category');
        $(Product.selector("type")).select();
        return false;
    }
    return checkCountryLocation();
}

function checkSupplierAdded() {
    var link_id = getValue('products[link_id]');
    if (link_id == 0) {
        var temp = getElement('supplier_text[0]');
        var elem = getElement('suppliers[0][supplier]');
        var elem2 = getElement('suppliers[0][supplier_name]');
        if (elem && elem2) {
            if (elem.value == '' || elem2.value == '') {
                alert('Please select a supplier. Cannot add/edit product without a supplier');
                if (temp.select) temp.select();
                if (temp.focus) temp.focus();
                return false;
            }
        }
    }
    return checkCategorySelected();
}

function productValidate() {
    var bulk_rows = [];
    if (typeof ProductBulkPricing != "undefined") {
        bulk_rows = ProductBulkPricing.rows();
    }

    var bulk_weights = [];
    if (typeof ProductBulkWeight != "undefined") {
        bulk_weights = ProductBulkWeight.rows();
    }

    var weights = Array();
    for (i = 0; i < bulk_weights.length; i++) {
        var qty = ProductBulkWeight.get("qty", bulk_weights[i]);
        if (!in_array(qty, weights)) weights[weights.length] = qty;
    }
    // now search through the bulk_rows to see if they have a corresponding weight added..
    for (i = 0; i < bulk_rows.length; i++) {
        var qty = ProductBulkPricing.get("qty", bulk_rows[i]);
        if (!in_array(qty, weights)) {
            alert('No Bulk Carton Weight found for Bulk Qty of ' + qty + ".\nPlease enter the qty before continuing.");
            //return false;
        }
    }
    //now have to check if the prices are entered in the inventory...
    var movement = getValue('inventory[movement]');
    if (movement != '') {
        var price = getValue('inventory[unit_value]');
        if (price == '') {
            var add_stock = getValue('add_stock');
            alert('Please fill in the ' + ((add_stock == 'true') ? 'cost' : 'sale') + ' price in the Inventory');
            return false;
        }
    }
    if (Product.get("type") == "0") {
        alert('Please select a Product Category');
        $(Product.selector("type")).select();
        return false;
    }
     if (Product.get("section") == "0") {
        alert('Please select a Product Section');
        $(Product.selector("section")).select();
        return false;
    }
    if (typeof $('input[name="item[stock_type]"]:checked').val() == "undefined") {
        alert('Please select a Stock type');
        return false;
    }
     // if(parseInt(ProductPacking.sum("weight",0))==0 && Product.get("active")=='y'){
    //     alert('Sorry, you cannot activate this product as its missing the shipping weight & size');
    //     return false;
    // }
    // updated on 23 jan 2024
    var checkbox = document.getElementById('item[bypass_freight]');
    if (checkbox.checked) {

    } else {
        if(ProductPacking.sum("weight",2)==0.00 && Product.get("active")=='y'){
            alert('Sorry, you cannot activate this product as its missing the shipping weight & size');
            return false;
        }
    }
    var selectbox=document.getElementsByClassName('mark_as_default');
    var returnvalue=(selectbox.length>0)?false:true;
    for(var i=0;i<selectbox.length;i++)
    {
          if(selectbox[i].value=='y'){
            returnvalue=true;
            break;
          }  
    }
    if(returnvalue==false){
        alert('Please select default sub product');
    }
    return returnvalue;
    if (checkSupplierAdded()) {
        var stock_type = getValue('item[stock_type]');
        var linked_id = getValue('item[link_id]');
        if ((stock_type == 'stock_for_sale' || stock_type == '') && linked_id == 0) openWindow('problem.php', 600, 200, true);
        else return true;
    }
    return false;
}

function str_repeat(i, m) {
    for (var o = []; m > 0; o[--m] = i);
    return (o.join(''));
}

function is_array(mixed_var) {
    var key = '';
    var getFuncName = function(fn) {
        var name = (/\W*function\s+([\w\$]+)\s*\(/).exec(fn);
        if (!name) return '(Anonymous)';
        return name[1];
    };
    if (!mixed_var) return false;
    if (typeof mixed_var === 'object') {
        if (mixed_var.hasOwnProperty)
            for (key in mixed_var)
                if (false === mixed_var.hasOwnProperty(key)) return false;
        return true;
    }
    return false;
}

function formatURLSafe(intext) {
    return intext.toLowerCase().replace(/[^a-z0-9_]+/, "_");
}

function sprintf() {
    var i = 0,
        a, f = arguments[i++],
        o = [],
        m, p, c, x;
    while (f) {
        if (m = /^[^\x25]+/.exec(f)) o.push(m[0]);
        else if (m = /^\x25{2}/.exec(f)) o.push('%');
        else if (m = /^\x25(?:(\d+)\$)?(\+)?(0|'[^$])?(-)?(\d+)?(?:\.(\d+))?([b-fosuxX])/.exec(f)) {
            if (((a = arguments[m[1] || i++]) == null) || (a == undefined)) throw ("Too few arguments.");
            if (/[^s]/.test(m[7]) && (typeof(a) != 'number')) throw ("Expecting number but found " + typeof(a));
            switch (m[7]) {
                case 'b':
                    a = a.toString(2);
                    break;
                case 'c':
                    a = String.fromCharCode(a);
                    break;
                case 'd':
                    a = parseInt(a);
                    break;
                case 'e':
                    a = m[6] ? a.toExponential(m[6]) : a.toExponential();
                    break;
                case 'f':
                    a = m[6] ? parseFloat(a).toFixed(m[6]) : parseFloat(a);
                    break;
                case 'o':
                    a = a.toString(8);
                    break;
                case 's':
                    a = ((a = String(a)) && m[6] ? a.substring(0, m[6]) : a);
                    break;
                case 'u':
                    a = Math.abs(a);
                    break;
                case 'x':
                    a = a.toString(16);
                    break;
                case 'X':
                    a = a.toString(16).toUpperCase();
                    break;
            }
            a = (/[def]/.test(m[7]) && m[2] && a > 0 ? '+' + a : a);
            c = m[3] ? m[3] == '0' ? '0' : m[3].charAt(1) : ' ';
            x = m[5] - String(a).length;
            p = m[5] ? str_repeat(c, x) : '';
            o.push(m[4] ? a + p : p + a);
        } else throw ("Huh ?!");
        f = f.substring(m[0].length);
    }
    return o.join('');
}
//]]>
</script>

<?php

$recent_changes = productCommentSearch(productCommentQuery(
    array('product_id' => $item['id'], 'comment_type' => 'sql'),
    array(),
    array('created_at' => formatDateTime(time() - 255))
));
if (count($recent_changes)) {
?>
<div style="color:#f00;text-align:left;padding:5px;">Changes made in the last 5 mins :</div><br />
<ul style="width:95%;text-align:left;margin:auto;">
<?php
foreach ($recent_changes as $change_id) {
        $change = productCommentFromDatabase($change_id);
?>
    <li style="color:#f00;"><?php hecho(wordwrap($change['comments'], 80, "\n", true))?></li>
<?php
}
?>
</ul>
<br /><br />
<?php
}

if ($item_lock['staff_id'] != $_GLOBALS['staff']['id'] && 'add' != $action) {
?>
<div style="width:95%;margin:auto;border:1px solid black;font-weight:bold;background:pink;text-align:center;padding:8px 0;">
<?php
if (is_null($item_lock)) {
        echo 'This Product is not locked to you; You cannot submit any changes.';
    } else {
        $time_to_live = $item_lock['expiry_time'] - time();
        echo 'This Product is currently locked to ' . $all_staff_list[$item_lock['staff_id']] . ', please wait for ' . intval($time_to_live / 60) . ' Minutes ' . ($time_to_live % 60) . ' Seconds and try again.';
    }
?>
</div>
<br /><br />
<?php
}
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
switch ($action) {
    case 'add':
        //include __DIR__ . "/_product_section.tpl.php";
        include __DIR__ . "/_product_item.tpl.php";
        include __DIR__ . "/_product_monitor_options.tpl.php";
        //include __DIR__ . "/_machine_linked.tpl.php";
        include __DIR__ . "/add-on-product.php";
        include __DIR__ . "/monitor-bracket-option.php";
        include __DIR__ . "/_manufacturer.tpl.php";
        //include __DIR__ . "/_factory_meta_tags.tpl.php";
        include __DIR__ . "/_product_reference.tpl.php";
        include __DIR__ . "/_product_suppliers.tpl.php";
        //include __DIR__ . "/_linked_products.tpl.php";
        include __DIR__ . "/_product_description.tpl.php";
        include __DIR__ . "/safety_approvels.tpl.php";
        include __DIR__ . "/_movies.tpl.php";
        include __DIR__ . "/_meta_tags.tpl.php";
        include __DIR__ . "/products_seo.tpl.php";
        include __DIR__ . "/_product_screenshots.tpl.php";
        include __DIR__ . "/_product_supported_tpl.php";
        include __DIR__ . "/_weight_shipping.tpl.php";
        include __DIR__ . "/_container_packing.tpl.php";
        include __DIR__ . "/_bulk_weights.tpl.php";
        include __DIR__ . "/_message_to_staff.tpl.php";
        include __DIR__ . "/_admin_notes.tpl.php";
        include __DIR__ . "/_inventory.tpl.php";
        include __DIR__ . "/_pricing.tpl.php";
        include __DIR__ . "/_bulk_pricing.tpl.php";
        include __DIR__ . "/_icons.tpl.php";
        include __DIR__ . "/_statistics.tpl.php";
        include __DIR__ . "/_customer_feedback.tpl.php";
        include __DIR__ . "/_last_5_changes.tpl.php";
        include __DIR__ . "/_product_terms_condition.tpl.php";
        include __DIR__ . "/_product_status.tpl.php";
        break;

    case 'edit':
        include __DIR__ . "/_product_summary.tpl.php";
        include __DIR__ . "/_product_item.tpl.php";
        include __DIR__ . "/_product_monitor_options.tpl.php";
        if($item['section']==1 && $item['type']==578)
        include __DIR__ . "/custom_sticker.php";
        if($item['section']==2)
        include __DIR__ . "/sub_product_model.php";
        include __DIR__ . "/add-on-product.php";
        include __DIR__ . "/monitor-bracket-option.php";
        include __DIR__ . "/_product_free_gift.tpl.php";
        include __DIR__ . "/_machine_linked.tpl.php";
        include __DIR__ .  "/upgrade_linking.tpl.php";
        include __DIR__ . "/_manufacturer.tpl.php";
        include __DIR__ . "/product_brochure_images.php";
        
        //include __DIR__ . "/_factory_meta_tags.tpl.php";
        include __DIR__ . "/_product_reference.tpl.php";
        //include __DIR__ . "/_product_section.tpl.php";
        include __DIR__ . "/_product_suppliers.tpl.php";
        include __DIR__ . "/_linked_products.tpl.php";
        include __DIR__ . "/_product_description.tpl.php";
        include __DIR__ . "/tariff_code_origins.php";
        include __DIR__ . "/safety_approvels.tpl.php";
        if($item['section']==2)
        include __DIR__ . "/_product_specification.tpl.php";
        // if($item['section']==2 || $item['section']==3){
        echo '<div style="background-color: #f7d8d8;">';
        include __DIR__ . "/machine_owner_section.tpl.php";
        include __DIR__ . "/machine_serial_number.tpl.php";
        include __DIR__ . "/manuals.tpl.php";
        include __DIR__ . "/firmware.tpl.php";
        include __DIR__ . "/links.tpl.php";
        include __DIR__ . "/member_news.php";
        echo '</div>';
        // }
        include __DIR__ . "/_product_review.tpl.php";
        include __DIR__ . "/website_page_layout.tpl.php";
        include __DIR__ . "/_movies.tpl.php";
        include __DIR__ . "/_meta_tags.tpl.php";
        include __DIR__ . "/products_seo.tpl.php";
        include __DIR__ . "/_product_screenshots.tpl.php";
        include __DIR__ . "/_product_supported_tpl.php";
        include __DIR__ . "/_weight_shipping.tpl.php";
        include __DIR__ . "/_container_packing.tpl.php";
        include __DIR__ . "/_bulk_weights.tpl.php";
        include __DIR__ . "/_message_to_staff.tpl.php";
        include __DIR__ . "/_admin_notes.tpl.php";
        include __DIR__ . "/admin_files.tpl.php";
        include __DIR__ . "/_inventory.tpl.php";
        include __DIR__ . "/_product_colour_images.tpl.php";
        if($item['section']==1 && $item['type']==578 && count($art_work))
        include __DIR__ . "/art_work_summary.php";
        if($item['stock_type']=='consignment')
            include __DIR__ . "/consigment_pricing.tpl.php";
        else
        include __DIR__ . "/_pricing.tpl.php";
        include __DIR__ . "/_bulk_pricing.tpl.php";
        include __DIR__ . "/_icons.tpl.php";
        include __DIR__ . "/_statistics.tpl.php";
        include __DIR__ . "/_customer_feedback.tpl.php";
        include __DIR__ . "/_last_5_changes.tpl.php";
        include __DIR__ . "/_product_terms_condition.tpl.php";
        include __DIR__ . "/_product_status.tpl.php";
        break;

    default:
        break;
}
?>

<div style="text-align:center;width:95%;margin:auto;">
    <input type="button" value="Submit Changes"<?=(1 || 'add' == $action) ? ' onclick="if(productValidate()) this.form.submit();"' : ' disabled="disabled"'?> />
<?php
if ($item['id']) {
?>
    <!-- <input type="button" value="Copy Product" onclick="window.open('.?action=do_copy&id=<?=$item['id']?>');" style="float:right;" class="unfinished" /> -->
    <input type="button" value="Copy Product" onclick="if(confirm('Are you sure you want to copy this product?')) { window.open('.?action=do_copy&id=<?=$item['id']?>'); }"  style="float:right;" class="unfinished" />
<?php
}
?>
</div>
<br />
<br />

<!-- /addedit.tpl -->
