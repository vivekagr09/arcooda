/*<?php
moduleLoad('invoice');

$charges = array();
if ($query = $DB->query(sprintf(
    "SELECT * FROM `arcooda_highwaygroup`.`charges` ORDER BY `charge_priority` ASC"
))) {
    while ($row = $DB->fetch_assoc($query)) {
        $charges[$row['charge_id']] = $row;
    }
    $DB->free_result($query);
}
?>*/
let Charges = {}/*<?php jecho($charges) ?>*/;

let CHARGE_PAYPAL = 1;
let CHARGE_SMALL_ORDER = 2;
let CHARGE_ADVANCEMENT = 3;
let CHARGE_COMMISSION = 4;
let CHARGE_CUSTOMS = 5;
let CHARGE_DISCOUNT2_5 = 6;
let CHARGE_DISCOUNT5 = 7;
let CHARGE_DISCOUNT10 = 8;
let CHARGE_DISCOUNT15 = 9;
let CHARGE_DISCOUNT20 = 10;
let CHARGE_DISCOUNT25 = 11;
let CHARGE_DISCOUNT30 = 12;
let CHARGE_COUPON = 13;
let CHARGE_DISCOUNT = 14;
let CHARGE_PACKING_AND_HANDLING = 15;
let CHARGE_SHIPPING_INSURANCE = 16;

function product_supplier_product_events(context) {
    //product_total_weight = (product_qty, product_weight, product_weight_inc) => {-1:"0.000", 0:weight, 1:bcadd(product_weight, bcmul(bcsub(product_qty, "1"), product_weight_inc, 3), 3)}[bccomp(product_qty, "1")];
    $(InvoiceProduct.selector(["product_qty", "product_weight", "product_weight_inc"]), context).change(function () {
        var row = InvoiceProduct.row(this);
        var product_qty = InvoiceProduct.get("product_qty", row);
        var comp = bccomp(product_qty, "1");
        if (comp < 0) return InvoiceProduct.set("product_total_weight", row, "0.000");
        var product_weight = InvoiceProduct.get("product_weight", row);
        if (comp == 0) return InvoiceProduct.set("product_total_weight", row, product_weight);
        var product_weight_inc = InvoiceProduct.get("product_weight_inc", row);
        InvoiceProduct.set("product_total_weight", row, bcadd(product_weight, bcmul(bcsub(product_qty, "1"), product_weight_inc, 3), 3));
    });

    //product_total_offer = (product_qty, product_offer, decimal) => bcmul(product_qty, product_offer, decimal);
    $(InvoiceProduct.selector(["product_qty", "product_offer", "decimal"]), context).change(function () {
        var row = InvoiceProduct.row(this);
        InvoiceProduct.set("product_total_offer", row, bcmul(
            InvoiceProduct.get("product_qty", row),
            InvoiceProduct.get("product_offer", row),
            InvoiceProduct.get("decimal", row)
        ));
    });

    //product_total_online = (product_qty, product_online, decimal) => bcmul(product_qty, product_online, decimal);
    $(InvoiceProduct.selector(["product_qty", "product_online", "decimal"]), context).change(function () {
        var row = InvoiceProduct.row(this);
        InvoiceProduct.set("product_total_online", row, bcmul(
            InvoiceProduct.get("product_qty", row),
            InvoiceProduct.get("product_online", row),
            InvoiceProduct.get("decimal", row)
        ));
    });

    //product_total_cost = (product_qty, product_cost, decimal) => bcmul(product_qty, product_cost, decimal);
    $(InvoiceProduct.selector(["product_qty", "product_cost", "decimal"]), context).change(function () {
        var row = InvoiceProduct.row(this);
        InvoiceProduct.set("product_total_cost", row, bcmul(
            InvoiceProduct.get("product_qty", row),
            InvoiceProduct.get("product_cost", row),
            InvoiceProduct.get("decimal", row)
        ));
    });

    //supplier_total_cost = (product_qty, supplier_cost, decimal) => bcmul(product_qty, supplier_cost, decimal);
    $(InvoiceProduct.selector(["product_qty", "supplier_cost", "decimal"]), context).change(function () {
        var row = InvoiceProduct.row(this);
        InvoiceProduct.set("supplier_total_cost", row, bcmul(
            InvoiceProduct.get("product_qty", row),
            InvoiceProduct.get("supplier_cost", row),
            InvoiceProduct.get("decimal", row)
        ));
    });

    //product_offer = (product_offerus, decimal) => convertCurrency(product_offerus, "USD", invoice_currency, decimal);
    $(InvoiceProduct.selector("product_offerus")).change(function () {
        //Don't bubble
        console.log(event);
        /*
        var row = InvoiceProduct.row(this);
        var decimal = InvoiceProduct.get("decimal", row);
        InvoiceProduct.set("product_offer", row, Invoice.fromCurrency($(this).val(), "USD", decimal);
        */
    });

    //product_offer = (product_offerau, decimal) => convertCurrency(product_offerau, "AUD", invoice_currency, decimal);
    $(InvoiceProduct.selector("product_offerau")).change(function () {
        //Don't bubble
        console.log(event);
        /*
        var row = InvoiceProduct.row(this);
        var decimal = InvoiceProduct.get("decimal", row);
        InvoiceProduct.set("product_offer", row, Invoice.fromCurrency($(this).val(), "AUD", decimal);
        */
    });

    //ship_weight = (product_total_weight) => sum(product_total_weight);
    $(InvoiceProduct.selector("product_total_weight"), context).change(function () {
        Invoice.set("ship_weight", InvoiceProduct.sum("product_total_weight", 3));
    });

    //products_offer = (product_total_offer) => sum(product_total_offer);
    $(InvoiceProduct.selector("product_total_offer"), context).change(function () {
        Invoice.set("products_offer", InvoiceProduct.sum("product_total_offer", 2));
    });

    //products_online = (product_total_online) => sum(product_total_online);
    $(InvoiceProduct.selector("product_total_online"), context).change(function () {
        Invoice.set("products_online", InvoiceProduct.sum("product_total_online", 2));
    });

    //products_cost = (product_total_cost) => sum(product_total_cost);
    $(InvoiceProduct.selector("product_total_cost"), context).change(function () {
        Invoice.set("products_cost", InvoiceProduct.sum("product_total_cost", 2));
    });

    //supplier_cost = (supplier_total_cost) => sum(supplier_total_cost);
    $(InvoiceProduct.selector("supplier_total_cost"), context).change(function () {
        Invoice.set("supplier_cost", InvoiceProduct.sum("supplier_total_cost", 2));
    });

    //product_costau  = (product_cost) => convertCurrency(product_cost, invoice_currency, "AUD");
    //product_costus  = (product_cost) => convertCurrency(product_cost, invoice_currency, "USD");
    //product_offerau = (product_offer) => convertCurrency(product_offer, invoice_currency, "AUD");
    //product_offerus = (product_offer) => convertCurrency(product_offer, invoice_currency, "USD");
    $(InvoiceProduct.selector(["product_cost", "product_offer"]), context).change(function () {
        var row = InvoiceProduct.row(this);
        var field = InvoiceProduct.field(this);
        InvoiceProduct.set(field + "us", row, Invoice.toCurrency($(this).val(), "USD", 4));
        InvoiceProduct.set(field + "au", row, Invoice.toCurrency($(this).val(), "AUD", 4));
    });
}

function product_supplier_charge_events(context) {
    //charge_total_offer = (charge_qty, charge_offer, decimal) => bcmul(charge_qty, charge_offer, decimal);
    $(InvoiceCharge.selector(["charge_qty", "charge_offer", "decimal"]), context).change(function () {
        var row = InvoiceCharge.row(this);
        InvoiceCharge.set("charge_total_offer", row, bcmul(
            InvoiceCharge.get("charge_qty", row),
            InvoiceCharge.get("charge_offer", row),
            InvoiceCharge.get("decimal", row)
        ));
    });

    //charge_total_online = (charge_qty, charge_online, decimal) => bcmul(charge_qty, charge_online, decimal);
    $(InvoiceCharge.selector(["charge_qty", "charge_online", "decimal"]), context).change(function () {
        var row = InvoiceCharge.row(this);
        InvoiceCharge.set("charge_total_online", row, bcmul(
            InvoiceCharge.get("charge_qty", row),
            InvoiceCharge.get("charge_online", row),
            InvoiceCharge.get("decimal", row)
        ));
    });

    //charge_total_cost = (charge_qty, charge_cost, decimal) => bcmul(charge_qty, charge_cost, decimal);
    $(InvoiceCharge.selector(["charge_qty", "charge_cost", "decimal"]), context).change(function () {
        var row = InvoiceCharge.row(this);
        InvoiceCharge.set("charge_total_cost", row, bcmul(
            InvoiceCharge.get("charge_qty", row),
            InvoiceCharge.get("charge_cost", row),
            InvoiceCharge.get("decimal", row)
        ));
    });

    //charge_offer = (charge_offerus, decimal) => convertCurrency(charge_offerus, "USD", invoice_currency, decimal);
    $(InvoiceCharge.selector("charge_offerus")).change(function () {
        //Don't bubble
        console.log(event);
        /*
        var row = InvoiceCharge.row(this);
        var decimal = InvoiceCharge.get("decimal", row);
        InvoiceCharge.set("charge_offer", row, Invoice.fromCurrency($(this).val(), "USD", decimal);
        */
    });

    //charge_offer = (charge_offerau, decimal) => convertCurrency(charge_offerau, "AUD", invoice_currency, decimal);
    $(InvoiceCharge.selector("charge_offerau")).change(function () {
        //Don't bubble
        console.log(event);
        /*
        var row = InvoiceCharge.row(this);
        var decimal = InvoiceCharge.get("decimal", row);
        InvoiceCharge.set("charge_offer", row, Invoice.fromCurrency($(this).val(), "AUD", decimal);
        */
    });

    //charges_offer = (charge_total_offer) => sum(charge_total_offer);
    $(InvoiceCharge.selector("charge_total_offer"), context).change(function () {
        Invoice.set("charges_offer", InvoiceCharge.sum("charge_total_offer", 2));
    });

    //charges_online = (charge_total_online) => sum(charge_total_online);
    $(InvoiceCharge.selector("charge_total_online"), context).change(function () {
        Invoice.set("charges_online", InvoiceCharge.sum("charge_total_online", 2));
    });

    //charges_cost = (charge_total_cost) => sum(charge_total_cost);
    $(InvoiceCharge.selector("charge_total_cost"), context).change(function () {
        Invoice.set("charges_cost", InvoiceCharge.sum("charge_total_cost", 2));
    });

    //charge_costau  = (charge_cost) => convertCurrency(charge_cost, invoice_currency, "AUD");
    //charge_costus  = (charge_cost) => convertCurrency(charge_cost, invoice_currency, "USD");
    //charge_offerau = (charge_offer) => convertCurrency(charge_offer, invoice_currency, "AUD");
    //charge_offerus = (charge_offer) => convertCurrency(charge_offer, invoice_currency, "USD");
    $(InvoiceCharge.selector(["charge_cost", "charge_offer"]), context).change(function () {
        var row = InvoiceCharge.row(this);
        var field = InvoiceCharge.field(this);
        InvoiceCharge.set(field + "us", row, Invoice.toCurrency($(this).val(), "USD", 4));
        InvoiceCharge.set(field + "au", row, Invoice.toCurrency($(this).val(), "AUD", 4));
    });
}

$(function () {
    product_supplier_product_events();
    product_supplier_charge_events();

    //supplier_ship_product_name = (ship_product_name) => ship_product_name;
    $(Invoice.selector("ship_product_name")).change(function () {
        Invoice.set("supplier_ship_product_name", $(this).val());
    });

    //ship_product_name = (supplier_ship_product_name) => supplier_ship_product_name;
    $(Invoice.selector("supplier_ship_product_name")).change(function () {
        //TODO: Don't bubble
        console.log(event);
        //Invoice.set("ship_product_name", $(this).val());
    });

    //supplier_ship_cost = (ship_cost) => ship_cost;
    $(Invoice.selector("ship_cost")).change(function () {
        Invoice.set("supplier_ship_cost", $(this).val());
    });

    //ship_cost = (supplier_ship_cost) => supplier_ship_cost;
    $(Invoice.selector("supplier_ship_cost")).change(function () {
        //TODO: Don't bubble
        console.log(event);
        //Invoice.set("ship_cost", $(this).val());
    });

    //invoice_cost = (products_cost, ship_cost, charges_cost) => products_cost + ship_cost + charges_cost;
    $(Invoice.selector(["products_cost", "ship_cost", "charges_cost"])).change(function () {
        Invoice.set("invoice_cost", bcadd(bcadd(Invoice.get("products_cost"), Invoice.get("ship_cost"), 2), Invoice.get("charges_cost"), 2));
    });

    //invoice_online = (products_online, ship_online, charges_online) => products_online + ship_online + charges_online;
    $(Invoice.selector(["products_online", "ship_online", "charges_online"])).change(function () {
        Invoice.set("invoice_online", bcadd(bcadd(Invoice.get("products_online"), Invoice.get("ship_online"), 2), Invoice.get("charges_online"), 2));
    });

    //invoice_offer = (products_offer, ship_offer, charges_offer) => products_offer + ship_offer + charges_offer;
    $(Invoice.selector(["products_offer", "ship_offer", "charges_offer"])).change(function () {
        Invoice.set("invoice_offer", bcadd(bcadd(Invoice.get("products_offer"), Invoice.get("ship_offer"), 2), Invoice.get("charges_offer"), 2));
    });

    //invoice_profit = (invoice_offer, invoice_cost) => invoice_offer - invoice_cost;
    $(Invoice.selector(["invoice_offer", "invoice_cost"])).change(function () {
        Invoice.set("invoice_profit", bcsub(Invoice.get("invoice_offer"), Invoice.get("invoice_cost"), 2));
    });

    //TODO: This needs to be un-anonymous, and split into product_supplier_charge_events()
    $([Invoice.selector(["products_offer", "ship_offer"]), InvoiceCharge.selector(["charge_id", "charge_qty", "charge_offer", "charge_cost"])].join(",")).change(function () {
        //TODO: Update charges that depend on sub-total
        var products_cost  = Invoice.get("products_cost");
        var products_offer = Invoice.get("products_offer");
        var ship_cost      = Invoice.get("ship_offer");
        var ship_offer     = Invoice.get("ship_offer");
        var running_cost   = products_cost;
        var running_offer  = products_offer;
        $(Charges).each(function() {
            if (this.priority == 7) {
                running_cost = bcadd(running_cost, ship_cost, 2);
                running_offer = bcadd(running_offer, ship_offer, 2);
            }
            var charge = this;
            $(InvoiceCharge.selector("charge_id") + "[value='" + this.id + "']").each(function() {
                var row = InvoiceCharge.row(this);
                var charge_qty = InvoiceCharge.get("charge_qty", row);
                var charge_offer = InvoiceCharge.get("charge_offer", row);
                var charge_cost = InvoiceCharge.get("charge_cost", row);
                //TODO: Calc the charge based on running_*
                InvoiceCharge.set("charge_offer", charge_offer);
                InvoiceCharge.set("charge_cost", charge_cost);
                running_cost = bcadd(running_cost, charge_cost, 2);
                running_offer = bcadd(running_offer, charge_offer, 2);
            });
        });
    });

    //charges_costau   = (charges_cost)   => convertCurrency(charges_cost, invoice_currency, "AUD");
    //charges_costus   = (charges_cost)   => convertCurrency(charges_cost, invoice_currency, "USD");
    //charges_offerau  = (charges_offer)  => convertCurrency(charges_offer, invoice_currency, "AUD");
    //charges_offerus  = (charges_offer)  => convertCurrency(charges_offer, invoice_currency, "USD");
    //invoice_costau   = (invoice_cost)   => convertCurrency(invoice_cost, invoice_currency, "AUD");
    //invoice_costus   = (invoice_cost)   => convertCurrency(invoice_cost, invoice_currency, "USD");
    //invoice_offerau  = (invoice_offer)  => convertCurrency(invoice_offer, invoice_currency, "AUD");
    //invoice_offerus  = (invoice_offer)  => convertCurrency(invoice_offer, invoice_currency, "USD");
    //invoice_profitau = (invoice_profit) => convertCurrency(invoice_profit, invoice_currency, "AUD");
    //invoice_profitus = (invoice_profit) => convertCurrency(invoice_profit, invoice_currency, "USD");
    //products_costau  = (products_cost)  => convertCurrency(products_cost, invoice_currency, "AUD");
    //products_costus  = (products_cost)  => convertCurrency(products_cost, invoice_currency, "USD");
    //products_offerau = (products_offer) => convertCurrency(products_offer, invoice_currency, "AUD");
    //products_offerus = (products_offer) => convertCurrency(products_offer, invoice_currency, "USD");
    //ship_costau      = (ship_cost)      => convertCurrency(ship_cost, invoice_currency, "AUD");
    //ship_costus      = (ship_cost)      => convertCurrency(ship_cost, invoice_currency, "USD");
    //ship_offerau     = (ship_offer)     => convertCurrency(ship_offer, invoice_currency, "AUD");
    //ship_offerus     = (ship_offer)     => convertCurrency(ship_offer, invoice_currency, "USD");
    $(Invoice.selector(["charges_cost", "charges_offer", "invoice_cost", "invoice_offer", "invoice_profit", "products_cost", "products_offer", "ship_cost", "ship_offer"]), context).change(function () {
        var field = Invoice.field(this);
        Invoice.set(field + "us", row, currency($(this).val(), "USD"));
        Invoice.set(field + "au", row, currency($(this).val(), "AUD"));
    });

    $(Invoice.selector("invoice_currency")).change(function() {
        //TODO: This could possibly update every single field, or it could update all the au/us fields
        //Maybe request the user input to decide
    });
});


//TODO: Here onward lies old code

var product_count = 0/*<?= max(100, count($invoice_products)) ?>*/;
var charge_count = 0/*<?= max(100, count($invoice_charges)) ?>*/;
var charge_names = 0/*<?php jecho(invoiceGetOptionList('charge_id'))?>*/;

function setChargeData(data, return_message) {
    if (return_message == 'add') {
        var target_id = String(charge_count);
        var site_id = getValue('site_id');
        var mappings = {
            1: { 1: 13, 2: 14, 9: 7, 16: 16, 11: 15 },
            2: { 11: 20 },
            3: { 1: 9, 2: 10, 9: 2, 16: 12, 11: 11 },
            4: { 11: 8 },
            5: { 11: 1 },
            6: { 11: 5 },
            7: { 11: 21 },
            10: { 11: data.charge_id }
        };
        var charge_id = 0;
        if (mappings[data.class_id] != null) {
            if (mappings[data.class_id][site_id] != null)
                charge_id = mappings[data.class_id][site_id];
            else
                charge_id = mappings[data.class_id][11];
        }
        var add_charge = true;
        if (charge_id == 1 && gstAdded) add_charge = false;
        if (charge_id == 1 && site_id == 20) add_charge = false;
        if (add_charge) {
            addCharge();
            if (charge_id == 1) gstAdded = true;
            setValue('charge_id[' + target_id + ']', charge_id);
            setValue('charge_product_id[' + target_id + ']', 'C' + charge_id);
            setValue('charge_name[' + target_id + ']', charge_names[charge_id]);
            chargeChange(charge_count);
        }
    }
}

function addCharge() {
    //global charge_count
    var currency = getValue('invoice_currency');
    if (currency == null || currency == '') {
        alert('You are unable to add charges until a currency is selected');
        var currencyElem = getElement('invoice_currency');
        if (currencyElem != null && currencyElem.focus != null) currencyElem.focus();
        return false;
    }
    var ccount = String(charge_count);

    $("#charges_table").append(/*<?= str_replace("[CHARGE_COUNT]", '" + ccount + "', jecho(charge_row('[CHARGE_COUNT]', array('charge_rank' => '[CHARGE_COUNT]'), $invoice['site_id']), 1)) ?>*/);

    product_supplier_charge_events($("#charges_row[TODO]"));

    charge_count++;
    //return charge_count - 1;
    return true;
}

function searchVoucher() {
    var member_id = getValue('member_id');
    url = '/webadmin/gift_voucher/?return_message=invoice&member_id=' + escape(member_id);
    if (member_id > 0) openWindow(url, '700', '600', true, 'scrollbars=1');
    else {
        alert('Please select a Member before adding a Gift Voucher');
        var elem = getElement('bill_name');
        if (elem.select) elem.select();
        if (elem.focus) elem.focus();
    }
}

function setVoucherData(data) {
    if (data != null && data['return_message'] == 'invoice') {
        var target_id = String(charge_count);
        var site_id = getValue('site_id');
        addCharge();
        var charge_id = 22;
        setValue('charge_id[' + target_id + ']', charge_id);
        setValue('charge_product_id[' + target_id + ']', 'C' + charge_id);
        setValue('charge_name[' + target_id + ']', 'Gift Voucher (' + data['formatted_id'] + ')');
        setValue('charge_cost[' + target_id + ']', '0.00');
        // charge offer
        var amount = convertCurrency(0 - data['amount'], data['currency'], getValue('invoice_currency'));
        var amountau = convertCurrency(0 - data['amount'], data['currency'], 'AUD');
        var amountus = convertCurrency(0 - data['amount'], data['currency'], 'USD');
        setValue('charge_offer[' + target_id + ']', amount);
        setValue('charge_online[' + target_id + ']', amount);

        // now have to update the voucher_user field....
        setValue('voucher_used', data['id']);

        productUpdateTotals();
    }
}

function addProduct() {
    var currency = getValue('invoice_currency');
    if (currency == null || currency == '') {
        alert('You are unable to add products until a currency is selected');
        var currencyElem = getElement('invoice_currency');
        if (currencyElem != null && currencyElem.focus != null) currencyElem.focus();
        return false;
    }
    var pcount = String(product_count);

    $("#product_table").append(/*<?= str_replace("[PRODUCT_COUNT]", '" + pcount + "', jecho(product_row('[PRODUCT_COUNT]', array('product_rank' => '[PRODUCT_COUNT]', 'generic' => 'n')), 1)) ?>*/);
    product_supplier_product_events($("#product_rowa[TODO]"));

    $("#supplier_table").append(/*<?= str_replace("[PRODUCT_COUNT]", '" + pcount + "', jecho(supplier_row('[PRODUCT_COUNT]', array('generic' => 'y')), 1)) ?>*/);
    product_supplier_product_events($("#supplier_rowa[TODO]"));

    product_count++;
    return product_count - 1;
}

function addGeneric() {
    //global product_count;
    var currency = getValue('invoice_currency');
    if (currency == null || currency == '') {
        alert('You are unable to add products until a currency is selected');
        var currencyElem = getElement('invoice_currency');
        if (currencyElem != null && currencyElem.focus != null) currencyElem.focus();
        return false;
    }
    var pcount = String(product_count);

    $("#product_table").append(/*<?= str_replace("[PRODUCT_COUNT]", '" + pcount + "', jecho(product_row('[PRODUCT_COUNT]', array('product_rank' => '[PRODUCT_COUNT]', 'product_qty' => '1', 'generic' => 'y')), 1)) ?>*/);
    product_supplier_product_events($("#product_rowa[TODO]"));

    $("#supplier_table").append(/*<?= str_replace("[PRODUCT_COUNT]", '" + pcount + "', jecho(supplier_row('[PRODUCT_COUNT]', array('generic' => 'y')), 1)) ?>*/);
    product_supplier_product_events($("#supplier_rowa[TODO]"));

    product_count++;
    return product_count - 1;
}

function addBulk() {
    var result = addGeneric();
    if (result != false) setValue('generic[' + result + ']', 'b');
    return result;
}

function deleteCharge(row) {
    var charge_id = getValue('charge_id[' + row + ']');
    if (charge_id == 1) gstAdded = false;
    var theRow = getElement("product_charge_rowa[" + row + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    var theRow = getElement("product_charge_rowb[" + row + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    chargeChange();
}

function deleteFreight() {
    setValue('ship_id', '');
    setValue('ship_product_id', '');
    setValue('ship_product_name', '');
    setValue('ship_courier_id', '');
    setValue('ship_notes', '');
    setValue('supplier_ship_notes', '');
    setValue('ship_offer', '');
    setValue('ship_online', '');
    setValue('ship_offerau', '');
    setValue('ship_offerus', '');
    var theRow = getElement("product_ship_rowa");
    if (theRow) theRow.style.display = 'none';
    var theRow = getElement("product_ship_rowb");
    if (theRow) theRow.style.display = 'none';
    var theRow = getElement("supplier_ship_rowa");
    if (theRow) theRow.style.display = 'none';
    shipChange();
}

function deleteProduct(row) {
    var theRow = getElement("product_rowa[" + row + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    theRow = getElement("product_rowb[" + row + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    theRow = getElement("supplier_rowa[" + row + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    theRow = getElement("supplier_rowb[" + row + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    theRow = getElement("supplier_rowc[" + row + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    theRow = getElement("supplier_rowd[" + row + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    theRow = getElement("supplier_rowe[" + row + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    theRow = getElement("supplier_rowf[" + row + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    theRow = getElement("supplier_rowg[" + row + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    theRow = getElement("supplier_rowh[" + row + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
    theRow = getElement("supplier_rowi[" + row + "]");
    if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);

    productChange();
}

function chargeChange(row, change_type) {
    var do_totals = false;

    do_totals = true;

    switch (change_type) {
        case 'pofferus':
            setValue("charge_offer[" + row + "]", convertCurrency(getValue("charge_offerus[" + row + "]"), "USD", getValue("invoice_currency")));
            break;
        case 'pofferau':
            setValue("charge_offer[" + row + "]", convertCurrency(getValue("charge_offerau[" + row + "]"), "AUD", getValue("invoice_currency")));
            break;
    }

    if (do_totals) productUpdateTotals();
}

function productChange(row, change_type) {
    var do_totals = false;
    var do_located = false;

    switch (change_type) {
        case 'pid':
            setValue('supplier_product_id[' + row + ']', getValue('product_id[' + row + ']'));
            setValue('supplier_sub_id[' + row + ']', getValue('sub_id[' + row + ']'));
            break;
        case 'pname':
            if (getValue("product_id[" + row + "]") < 1 || getValue("supplier_id[" + row + "]") < 1)
                setValue("supplier_product_name[" + row + "]", getValue("product_name[" + row + "]"));
            break;
        case 'pqty':
            setValue("supplier_product_qty[" + row + "]", getValue("product_qty[" + row + "]"));
            do_totals = true;
            break;
        case 'pcost':
            var supplier_currency = getValue("supplier_currency[" + row + "]");
            var invoice_currency = getValue("invoice_currency");
            var product_cost = getValue("product_cost[" + row + "]");
            setValue("supplier_product_cost[" + row + "]", product_cost);
            do_totals = true;
            break;
        case 'ponline':
            do_totals = true;
            break;
        case 'poffer':
            setValue('product_price_changed[' + row + ']', 'y');
            do_totals = true;
            break;
        case 'pofferus':
            setValue("product_offer[" + row + "]", convertCurrency(getValue("product_offerus[" + row + "]"), "USD", getValue("invoice_currency")));
            do_totals = true;
            break;
        case 'pofferau':
            setValue("product_offer[" + row + "]", convertCurrency(getValue("product_offerau[" + row + "]"), "AUD", getValue("invoice_currency")));
            do_totals = true;
            break;
        case 'sid':
            setValue('product_id[' + row + ']', getValue('supplier_product_id[' + row + ']'));
            setValue('sub_id[' + row + ']', getValue('supplier_sub_id[' + row + ']'));
            break;
        case 'sname':
            setValue("product_name[" + row + "]", getValue("supplier_product_name[" + row + "]"));
            break;
        case 'sfound':
            do_located = true;
            break;
        case 'sqty':
            setValue("product_qty[" + row + "]", getValue("supplier_product_qty[" + row + "]"));
            do_totals = true;
            break;
        case 'scost':
            var supplier_currency = getValue("supplier_currency[" + row + "]");
            var invoice_currency = getValue("invoice_currency");
            var supplier_product_cost = getValue("supplier_product_cost[" + row + "]");
            setValue("product_cost[" + row + "]", supplier_product_cost);
            do_totals = true;
            break;
        case 'ocost':
            var supplier_currency = getValue("supplier_currency[" + row + "]");
            var invoice_currency = getValue("invoice_currency");
            var supplier_cost = getValue("supplier_cost[" + row + "]");
            setValue("supplier_product_cost[" + row + "]", convertCurrency(supplier_cost, supplier_currency, invoice_currency));
            setValue("product_cost[" + row + "]", convertCurrency(supplier_cost, supplier_currency, invoice_currency));
            do_totals = true;
            break;
        case 'scostus':
            var supplier_currency = getValue("supplier_currency[" + row + "]");
            var invoice_currency = getValue("invoice_currency");
            var product_costus = Number(getValue("product_costus[" + row + "]"));
            setValue("supplier_product_cost[" + row + "]", convertCurrency(product_costus, "USD", invoice_currency));
            setValue("product_cost[" + row + "]", convertCurrency(product_costus, "USD", invoice_currency));
            do_totals = true;
            break;
        case 'scostau':
            var supplier_currency = getValue("supplier_currency[" + row + "]");
            var invoice_currency = getValue("invoice_currency");
            var product_costau = Number(getValue("product_costau[" + row + "]"));
            setValue("supplier_product_cost[" + row + "]", convertCurrency(product_costau, "AUD", invoice_currency));
            setValue("product_cost[" + row + "]", convertCurrency(product_costau, "AUD", invoice_currency));
            do_totals = true;
            break;
        default:
            do_totals = true;
            break;
    }

    setExtraSupplierData();
    if (do_totals) productUpdateTotals();
    if (do_located) productUpdateLocated();
}

function getProductRows() {
    var product_table = getElement("product_table");
    var product_rows = [];
    if (product_table != null) {
        var theRows = product_table.rows;
        for (var i = 0; i < theRows.length; i++)
            if (theRows[i].id.substr(0, 13) == 'product_rowa[')
                if (searchArray(theRows[i].id.substr(12), product_rows) == null)
                    product_rows[product_rows.length] = theRows[i].id.substr(12);
    }
    return product_rows;
}

function getChargeRows() {
    var charges_table = getElement("charges_table");
    var charge_rows = [];
    if (charges_table != null) {
        var theRows = charges_table.rows;
        for (var i = 0; i < theRows.length; i++)
            if (theRows[i].id.substr(0, 20) == 'product_charge_rowa[')
                if (searchArray(theRows[i].id.substr(19), charge_rows) == null)
                    charge_rows[charge_rows.length] = theRows[i].id.substr(19);
    }
    return charge_rows;
}

function rankProductsAlphabetically() {
    getElement('prodLink').innerHTML = 'Please Wait...';
    setTimeout("sortAlphabetically()", 500);
}

function sortAlphabetically() {
    // function to rank the products alphabetically....
    var product_rows = getProductRows();
    var ar = [];
    var first_prow = getElement('product_rowa' + product_rows[0]);
    var first_srow = getElement('supplier_rowa' + product_rows[0]);
    for (var i = 0; i < product_rows.length; i++) ar[i] = getValue("product_name" + product_rows[i]) + '^' + product_rows[i];
    ar.sort(); ar.reverse();

    // now have swap the rows around....
    for (i = 0; i < ar.length; i++) {
        var tmp = explode('^', ar[i]);
        //print_array(tmp);
        var source_row;
        var from_row = tmp[1];
        var target_table = getElement('product_table');//target_row.parentNode;
        setValue('product_rank' + from_row, ar.length - i);
        if (source_row = getElement("product_rowa" + from_row)) target_table.insertBefore(source_row, first_prow);
        var tmpp = source_row;
        if (source_row = getElement("product_rowb" + from_row)) target_table.insertBefore(source_row, first_prow);
        first_prow = tmpp;
        var target_table = getElement('supplier_table');//target_row.parentNode;
        if (source_row = getElement("supplier_rowa" + from_row)) target_table.insertBefore(source_row, first_srow);
        var tmps = source_row;
        if (source_row = getElement("supplier_rowb" + from_row)) target_table.insertBefore(source_row, first_srow);
        if (source_row = getElement("supplier_rowc" + from_row)) target_table.insertBefore(source_row, first_srow);
        if (source_row = getElement("supplier_rowd" + from_row)) target_table.insertBefore(source_row, first_srow);
        if (source_row = getElement("supplier_rowe" + from_row)) target_table.insertBefore(source_row, first_srow);
        if (source_row = getElement("supplier_rowf" + from_row)) target_table.insertBefore(source_row, first_srow);
        if (source_row = getElement("supplier_rowg" + from_row)) target_table.insertBefore(source_row, first_srow);
        if (source_row = getElement("supplier_rowh" + from_row)) target_table.insertBefore(source_row, first_srow);
        if (source_row = getElement("supplier_rowi" + from_row)) target_table.insertBefore(source_row, first_srow);
        first_srow = tmps;
    }
    getElement('prodLink').innerHTML = 'Product Name';
}

function rankProduct(row, direction) {

    var from_row = '[' + row + ']';
    var from_rank = Number(getValue("product_rank" + from_row));
    var swap_row = null;
    var swap_rank = null;
    var product_rows = getProductRows();
    for (var i = 0; i < product_rows.length; i++) {
        var check_row = product_rows[i];
        var check_rank = Number(getValue("product_rank" + check_row));
        if (check_row != from_row && ((direction < 0 && check_rank < from_rank && (swap_rank == null || check_rank > swap_rank)) || (direction > 0 && check_rank > from_rank && (swap_rank == null || check_rank < swap_rank)))) {
            swap_row = check_row;
            swap_rank = check_rank;
        }
    }
    if (swap_row != null) {
        setValue("product_rank" + from_row, swap_rank);
        setValue("product_rank" + swap_row, from_rank);
        if (direction > 0) {
            var temp = from_row;
            from_row = swap_row;
            swap_row = temp;
        }
        var source_row;
        var target_row = getElement("product_rowa" + swap_row);
        var target_table = target_row.parentNode;
        if (source_row = getElement("product_rowa" + from_row)) target_table.insertBefore(source_row, target_row);
        if (source_row = getElement("product_rowb" + from_row)) target_table.insertBefore(source_row, target_row);
        var target_row = getElement("supplier_rowa" + swap_row);
        var target_table = target_row.parentNode;
        if (source_row = getElement("supplier_rowa" + from_row)) target_table.insertBefore(source_row, target_row);
        if (source_row = getElement("supplier_rowb" + from_row)) target_table.insertBefore(source_row, target_row);
        if (source_row = getElement("supplier_rowc" + from_row)) target_table.insertBefore(source_row, target_row);
        if (source_row = getElement("supplier_rowd" + from_row)) target_table.insertBefore(source_row, target_row);
        if (source_row = getElement("supplier_rowe" + from_row)) target_table.insertBefore(source_row, target_row);
        if (source_row = getElement("supplier_rowf" + from_row)) target_table.insertBefore(source_row, target_row);
        if (source_row = getElement("supplier_rowg" + from_row)) target_table.insertBefore(source_row, target_row);
        if (source_row = getElement("supplier_rowh" + from_row)) target_table.insertBefore(source_row, target_row);
        if (source_row = getElement("supplier_rowi" + from_row)) target_table.insertBefore(source_row, target_row);
    }
}

function rankCharge(row, direction) {

    var from_row = '[' + row + ']';
    var from_rank = Number(getValue("charge_rank" + from_row));
    var swap_row = null;
    var swap_rank = null;
    var charge_rows = getChargeRows();
    for (var i = 0; i < charge_rows.length; i++) {
        var check_row = charge_rows[i];
        var check_rank = Number(getValue("charge_rank" + check_row));
        if (check_row != from_row && ((direction < 0 && check_rank < from_rank && (swap_rank == null || check_rank > swap_rank)) || (direction > 0 && check_rank > from_rank && (swap_rank == null || check_rank < swap_rank)))) {
            swap_row = check_row;
            swap_rank = check_rank;
        }
    }
    if (swap_row != null) {
        setValue("charge_rank" + from_row, swap_rank);
        setValue("charge_rank" + swap_row, from_rank);
        if (direction > 0) {
            var temp = from_row;
            from_row = swap_row;
            swap_row = temp;
        }
        var source_row;
        var target_row = getElement("product_charge_rowa" + swap_row);
        var target_table = target_row.parentNode;
        if (source_row = getElement("product_charge_rowa" + from_row)) target_table.insertBefore(source_row, target_row);
        if (source_row = getElement("product_charge_rowb" + from_row)) target_table.insertBefore(source_row, target_row);
    }
}

function productUpdateLocated() {
    var all_found = true;

    var supplier_rows = [];

    var theRows = getElement("supplier_table").rows;
    for (var i = 0; i < theRows.length; i++)
        if (theRows[i].id.substr(0, 14) == 'supplier_rowa[')
            if (searchArray(theRows[i].id.substr(13), supplier_rows) == null)
                supplier_rows[supplier_rows.length] = theRows[i].id.substr(13);

    for (var i = 0; i < supplier_rows.length; i++)
        if (getValue("supplier_found" + supplier_rows[i]) == 'n')
            all_found = false;

    setValue('products_located', (all_found) ? 'y' : 'n');
}

function shipChange(change_type) {
    var do_totals = false;

    switch (change_type) {
        case 'pname':
            break;
        case 'pqty':
            setValue("supplier_ship_qty", getValue("ship_qty"));
        case 'pcost':
        case 'ponline':
            do_totals = true;
            break;
        case 'poffer':
            setValue("ship_offerau", convertCurrency(getValue("ship_offer"), getValue("invoice_currency"), "AUD"));
            setValue("ship_offerus", convertCurrency(getValue("ship_offer"), getValue("invoice_currency"), "USD"));
            do_totals = true;
            break;
        case 'pofferus':
            setValue("ship_offer", convertCurrency(getValue("ship_offerus"), "USD", getValue("invoice_currency")));
            setValue("ship_offerau", convertCurrency(getValue("ship_offerus"), "USD", "AUD"));
            do_totals = true;
            break;
        case 'pofferau':
            setValue("ship_offer", convertCurrency(getValue("ship_offerau"), "AUD", getValue("invoice_currency")));
            setValue("ship_offerus", convertCurrency(getValue("ship_offerau"), "AUD", "USD"));
            do_totals = true;
            break;
        case 'sname':
            setValue("ship_product_name", getValue("supplier_ship_product_name"));
            break;
        case 'sqty':
            setValue("ship_qty", getValue("supplier_ship_qty"));
        case 'scost':
        case 'scostus':
        case 'scostau':
        default:
            do_totals = true;
            break;
    }

    if (do_totals) productUpdateTotals();
}

function productUpdateTotals() {
    var total_cost = 0.0;
    var total_costau = 0.0;
    var total_costus = 0.0;
    var total_offer = 0.0;
    var total_offerau = 0.0;
    var total_offerus = 0.0;
    var total_online = 0.0;

    var product_rows = [];
    var theRows = getElement("product_table").rows;
    for (var i = 0; i < theRows.length; i++) if (theRows[i].id.substr(0, 13) == 'product_rowa[' && searchArray(theRows[i].id.substr(12), product_rows) == null) product_rows.push(theRows[i].id.substr(12));

    var charge_rows = [];
    var theRows1 = getElement("charges_table").rows;
    for (var i = 0; i < theRows1.length; i++) if (theRows1[i].id.substr(0, 20) == 'product_charge_rowa[' && searchArray(theRows1[i].id.substr(19), charge_rows) == null) charge_rows.push(theRows1[i].id.substr(19));

    var in_cur = getValue('invoice_currency');

    for (var i = 0; i < product_rows.length; i++) {
        var rowNum = product_rows[i];
        var addMe = 0;
        var product_qty = Number(getValue("product_qty" + rowNum));
        var product_cost = Number(getValue("product_cost" + rowNum));
        var product_costau = Number(getValue("product_costau" + rowNum));
        var product_costus = Number(getValue("product_costus" + rowNum));
        var product_offer = Number(getValue("product_offer" + rowNum));
        var product_offerau = Number(getValue("product_offerau" + rowNum));
        var product_offerus = Number(getValue("product_offerus" + rowNum));
        var product_online = Number(getValue("product_online" + rowNum));
        addMe = product_qty * product_cost;
        if (!isNaN(addMe)) total_cost += addMe;
        addMe = product_qty * product_costau;
        if (!isNaN(addMe)) total_costau += addMe;
        addMe = product_qty * product_costus;
        if (!isNaN(addMe)) total_costus += addMe;
        addMe = product_qty * product_offer;
        if (!isNaN(addMe)) total_offer += addMe;
        addMe = product_qty * product_offerau;
        if (!isNaN(addMe)) total_offerau += addMe;
        addMe = product_qty * product_offerus;
        if (!isNaN(addMe)) total_offerus += addMe;
        addMe = product_qty * product_online;
        if (!isNaN(addMe)) total_online += addMe;
    }

/*<?php
$charges_amount   = array();
$charges_currency = array();
$charges_percent  = array();
$charges_rounding = array();
$charges_priority = array();
if ($query = $DB->query(sprintf(
    "SELECT * FROM `arcooda_highwaygroup`.`charges` ORDER BY `charge_priority` ASC"
))) {
    while ($row = $DB->fetch_assoc($query)) {
        $charges_amount[$row['charge_id']]   = $row['charge_amount'];
        $charges_currency[$row['charge_id']] = $row['charge_amount_currency'];
        $charges_percent[$row['charge_id']]  = $row['charge_percent'];
        $charges_rounding[$row['charge_id']] = $row['charge_rounding'];
        $charges_priority[$row['charge_id']] = intval($row['charge_priority']);
    }
    $DB->free_result($query);
}
?>*/
    var charges_ids = []/*<?php jecho(array_keys($charges_amount))?>*/;
    var charges_amount = []/*<?php jecho(array_values($charges_amount))?>*/;
    var charges_currency = []/*<?php jecho(array_values($charges_currency))?>*/;
    var charges_percent = []/*<?php jecho(array_values($charges_percent))?>*/;
    var charges_rounding = []/*<?php jecho(array_values($charges_rounding))?>*/;
    var charges_priority = []/*<?php jecho(array_values($charges_priority))?>*/;

    for (var j = 0; j < charges_ids.length; j++) if (charges_priority[j] <= 7) {
        for (var i = 0; i < charge_rows.length; i++) {
            var rowNum = charge_rows[i];
            var charge_id = Number(getValue('charge_id' + rowNum));
            if (charge_id == charges_ids[j]) {
                var addMe = 0;
                var amount = charges_amount[j];
                var currency = charges_currency[j];
                var percent = Number(charges_percent[j]) / 100.0;
                var rounding = Number(charges_rounding[j]);

                var charge_amount = charge_cost = '0.00';
                var charge_amount_au = '0.00';
                var charge_amount_us = '0.00';

                if (rounding == 0.0 && percent == 0.0 && amount == '0.00') {
/*<?php
if (moduleLoad("insurance") || function_exists("insuranceFromDatabase")) {
    $insurance = insuranceFromDatabase(1);
?>*/
                    if (charge_id == 21) {
                        var result_aud = "0.00";
                        var cover_amount = products_offerau;
                        //if (in_cur == "AUD") cover_amount = bcmul(cover_amount, "1.1", 2);
                        if (getValue("supply_country") == "AU" && getValue("bill_country") == "AU" && getValue("shipto_country[0]") == "AU") cover_amount = bcmul(cover_amount, "1.1", 2);

/*<?php
    switch ($insurance["pricing_type"]) {
        case 'brackets':
            foreach (insuranceRatesFromDatabase(1) as $rate) {
?>*/
                        if (bccomp(cover_amount, /*<?php jecho($rate["rate_start"])?>*/0, 2) > 0) result_aud = bcadd(result_aud, bcmul(bcsub((bccomp(cover_amount, /*<?php jecho($rate["rate_finish"])?>*/0, 2) < 0) ? cover_amount : /*<?php jecho($rate["rate_finish"])?>*/0, /*<?php jecho($rate["rate_start"])?>*/0, 2), /*<?php jecho(bcdiv($rate["rate_rate"], "100.0", 4))?>*/0, 2), 2);
/*<?php
            }
            break;
        default:
        case 'fixed':
?>*/
                        result_aud = bcmul(cover_amount, /*<?php jecho(bcdiv($rate["rate_rate"], "100.0", 4))?>*/0, 2);
/*<?php
            break;
    }
?>*/
                        setValue('charge_cost' + rowNum, convertCurrency(result_aud, "AUD", in_cur));
                        setValue('charge_offer' + rowNum, convertCurrency(result_aud, "AUD", in_cur));
                    }
/*<?php
}
?>*/

                    charge_cost = getValue('charge_cost' + rowNum);
                    charge_amount = getValue('charge_offer' + rowNum);
                    charge_amount_au = convertCurrency(charge_amount, in_cur, 'AUD');
                    charge_amount_us = convertCurrency(charge_amount, in_cur, 'USD');
                    setValue('charge_online' + rowNum, charge_amount);
                }
                else {
                    charge_amount = Number(convertCurrency(amount, currency, in_cur));

                    var temp = total_offer;
                    for (var k = 0; k < product_rows.length; k++) if (getValue("generic" + product_rows[k]) == "y" && getValue("generic_code" + product_rows[k]) == "GFR") temp -= Number(getValue("product_qty" + product_rows[k])) * Number(getValue("product_offer" + product_rows[k]));

                    charge_amount += Number(temp) * percent;

                    if (rounding != 0.0) charge_amount = (Math.ceil((temp + charge_amount) / rounding) * rounding) - temp;

                    charge_cost = charge_amount = convertCurrency(Number(charge_amount).toFixed(2), in_cur);
                    charge_amount_au = convertCurrency(charge_amount, in_cur, 'AUD');
                    charge_amount_us = convertCurrency(charge_amount, in_cur, 'USD');

                    if (charge_id == 18 || charge_id == 19 || charge_id == 34 || charge_id == 35 || charge_id == 36 || charge_id == 17) {
                        charge_cost = "0.00";
                    }

                    setValue('charge_cost' + rowNum, convertCurrency(charge_cost, in_cur));
                    setValue('charge_offer' + rowNum, convertCurrency(charge_amount, in_cur));
                    setValue('charge_online' + rowNum, convertCurrency(charge_amount, in_cur));
                }

                total_cost += Number(charge_cost);
                total_costau += Number(convertCurrency(charge_cost, in_cur, 'AUD'));
                total_costus += Number(convertCurrency(charge_cost, in_cur, 'USD'));
                total_offer += Number(charge_amount);
                total_offerau += Number(charge_amount_au);
                total_offerus += Number(charge_amount_us);
                total_online += Number(charge_amount);
            }
        }
    }

    var charges_offer = total_offer - products_offer;
    var charges_offerau = total_offerau - products_offerau;
    var charges_offerus = total_offerus - products_offerus;
    var charges_cost = total_cost - products_cost;
    var charges_costau = total_costau - products_costau;
    var charges_costus = total_costus - products_costus;

    if (getValue('ship_id') > 0) {
        addMe = Number(getValue('ship_cost'));
        if (!isNaN(addMe)) total_cost += addMe;
        addMe = Number(getValue('ship_costau'));
        if (!isNaN(addMe)) total_costau += addMe;
        addMe = Number(getValue('ship_costus'));
        if (!isNaN(addMe)) total_costus += addMe;
        addMe = Number(getValue('ship_offer'));
        if (!isNaN(addMe)) total_offer += addMe;
        addMe = Number(getValue('ship_offerau'));
        if (!isNaN(addMe)) total_offerau += addMe;
        addMe = Number(getValue('ship_offerus'));
        if (!isNaN(addMe)) total_offerus += addMe;
        addMe = Number(getValue('ship_online'));
        if (!isNaN(addMe)) total_online += addMe;
    }

    var ship_cost = total_cost - (products_cost + charges_cost);
    var ship_costau = total_costau - (products_costau + charges_costau);
    var ship_costus = total_costus - (products_costus + charges_costus);
    var ship_offer = total_offer - (products_offer + charges_offer);
    var ship_offerau = total_offerau - (products_offerau + charges_offerau);
    var ship_offerus = total_offerus - (products_offerus + charges_offerus);

    //Hack
    total_offerau = Number(total_offerau);
    total_offerus = Number(total_offerus);

    for (var j = 0; j < charges_ids.length; j++) if (charges_priority[j] > 7) {
        for (var i = 0; i < charge_rows.length; i++) {
            var rowNum = charge_rows[i];
            var charge_id = Number(getValue('charge_id' + rowNum));
            if (charge_id == charges_ids[j]) {
                var addMe = 0;
                var amount = charges_amount[j];
                var currency = charges_currency[j];
                var percent = Number(charges_percent[j]) / 100.0;
                var rounding = Number(charges_rounding[j]);

                var charge_amount = charge_cost = '0.00';
                var charge_amount_au = '0.00';
                var charge_amount_us = '0.00';

                if (rounding == 0.0 && percent == 0.0 && amount == '0.00') {
/*<?php
if (moduleLoad("insurance") || function_exists("insuranceFromDatabase")) {
    $insurance = insuranceFromDatabase(1);
?>*/
                    if (charge_id == 21) {
                        var result_aud = "0.00";
                        var cover_amount = products_offerau;
                        //if (in_cur == "AUD") cover_amount = bcmul(cover_amount, "1.1", 2);
                        if (getValue("supply_country") == "AU" && getValue("bill_country") == "AU" && getValue("shipto_country[0]") == "AU") cover_amount = bcmul(cover_amount, "1.1", 2);

/*<?php
    switch ($insurance["pricing_type"]) {
        case 'brackets':
            foreach (insuranceRatesFromDatabase(1) as $rate) {
?>*/
                        if (bccomp(cover_amount, /*<?php jecho($rate["rate_start"])?>*/0, 2) > 0) result_aud = bcadd(result_aud, bcmul(bcsub((bccomp(cover_amount, /*<?php jecho($rate["rate_finish"])?>*/0, 2) < 0) ? cover_amount : /*<?php jecho($rate["rate_finish"])?>*/0, /*<?php jecho($rate["rate_start"])?>*/0, 2), /*<?php jecho(bcdiv($rate["rate_rate"], "100.0", 4))?>*/0, 2), 2);
/*<?php
            }
            break;
        default:
        case 'fixed':
?>*/
                        result_aud = bcmul(cover_amount, /*<?php jecho(bcdiv($rate["rate_rate"], "100.0", 4))?>*/0, 2);
/*<?php
            break;
    }
?>*/
                        setValue('charge_cost' + rowNum, convertCurrency(result_aud, "AUD", in_cur));
                        setValue('charge_offer' + rowNum, convertCurrency(result_aud, "AUD", in_cur));
                    }
/*<?php
}
?>*/

                    charge_cost = getValue('charge_cost' + rowNum);
                    charge_amount = getValue('charge_offer' + rowNum);
                    charge_amount_au = convertCurrency(charge_amount, in_cur, 'AUD');
                    charge_amount_us = convertCurrency(charge_amount, in_cur, 'USD');
                    setValue('charge_online' + rowNum, charge_amount);
                }
                else {
                    charge_amount = Number(convertCurrency(amount, currency, in_cur));
                    charge_amount += Number(total_offer) * percent;

                    if (rounding != 0.0) charge_amount = (Math.ceil((total_offer + charge_amount) / rounding) * rounding) - total_offer;

                    charge_cost = charge_amount = convertCurrency(Number(charge_amount).toFixed(2), in_cur);
                    charge_amount_au = convertCurrency(charge_amount, in_cur, 'AUD');
                    charge_amount_us = convertCurrency(charge_amount, in_cur, 'USD');

                    setValue('charge_cost' + rowNum, convertCurrency(charge_cost, in_cur));
                    setValue('charge_offer' + rowNum, convertCurrency(charge_amount, in_cur));
                    setValue('charge_online' + rowNum, convertCurrency(charge_amount, in_cur));
                }

                total_cost += Number(charge_cost);
                total_costau += Number(convertCurrency(charge_cost, in_cur, 'AUD'));
                total_costus += Number(convertCurrency(charge_cost, in_cur, 'USD'));
                total_offer += Number(charge_amount);
                total_offerau += Number(charge_amount_au);
                total_offerus += Number(charge_amount_us);
                total_online += Number(charge_amount);
            }
        }
    }
}

function productShowOutlet() {
    var plug_id = Number(getValue('plug_id'));
    if (plug_id == null || plug_id < 1) return;
    var url = '/webadmin/world/plugs/show_image.php?id=' + plug_id;
    var features = 'scrollbars, resizable, height=400, width=600';
    window.open(url, 'powerplug_popup', features);
}

function searchProduct(row, searchFor) {
    var currency = getValue('invoice_currency');
    var site_id = getValue('site_id');
    var member_id = getValue('member_id');
    if (site_id == '1' || site_id == '2') site_id = '1|2';
    //var url='/webadmin/products/products_search.php?action=do_search';
    //url+='&query='+searchFor;
    var url = '/webadmin/products/?action=list&show_sites=n';
    url += '&return_message=select_' + row;
    url += '&search_for=' + escape(searchFor);
    if (currency != null) url += '&currency=' + currency;
    if (site_id != null) url += '&site_id=' + site_id;
    if (member_id != null) url += '&member_id=' + member_id;
    var features = 'scrollbars, resizable';//height=700, width=800
    window.open(url, 'product_popup', features);
}

function searchSupplier(row, searchFor) {
    var generic = getValue('generic[' + row + ']');
    var generic_code = getValue('generic_code[' + row + ']');
    var url = '/webadmin/suppliers/index.php?action=list';
    //if(generic=='y' && generic_code=='GFR') url='/webadmin/freight_forwarders/?action=list';
    if (generic == 'y' && generic_code == 'GFR') url = '/webadmin/forwarders/?action=list';
    url += '&return_message=select_' + row;
    if (searchFor != 'None') url += '&search_for=' + escape(searchFor);
    var features = 'scrollbars, resizable';//height=700, width=800
    window.open(url, 'Search', features);
}

function selectSupplier(row, supplier_id) {
    var url = '/webadmin/suppliers/index.php?action=select';
    url += '&return_message=select_' + row;
    url += '&supplier_id=' + supplier_id;
    var features = 'scrollbars, resizable';
    window.open(url, 'Search', features);
}

function searchDefaultSupplier(searchFor) {
    var url = '/webadmin/suppliers/index.php?action=list';
    url += '&return_message=default';
    if (searchFor != 'None') url += '&search_for=' + escape(searchFor);
    var features = 'scrollbars, resizable';//height=700, width=800
    window.open(url, 'Search', features);
}

function splitProduct(row) {
    var rowNum = '[' + row + ']';

    var product_qty = getValue('product_qty' + rowNum);
    if (product_qty == null || product_qty < 2) return;
    product_qty = Number(product_qty);

    var split_qty = 0;
    if (product_qty < 4) {
        split_qty = confirm('Are you sure you would like to split this product?');
        if (!split_qty) return;
        split_qty = 1;
    }
    else {
        split_qty = prompt('How many of this product would you like to split? (1 - ' + (product_qty - 1) + ')', '1');
        if (split_qty == null || split_qty < 1 || split_qty >= product_qty) return;
    }
    var new_product = product_count;
    addProduct();

    var fields = Array('product_cost', 'product_id', 'sub_id', 'product_name', 'product_notes', 'product_offer', 'product_offerau', 'product_offerus', 'product_online', 'product_weight', 'product_weight_inc', 'supplier_product_cost', 'supplier_cost_onload', 'supplier_cost_orig', 'supplier_cost_orig_curr', 'product_costau', 'product_costus', 'supplier_country', 'supplier_found', 'supplier_id', 'supplier_id_orig', 'supplier_name', 'supplier_notes', 'supplier_product_id', 'supplier_sub_id', 'supplier_product_name', 'supplier_product_qty');
    for (var i = 0; i < fields.length; i++) {
        var field = fields[i];
        setValue(field + '[' + new_product + ']', getValue(field + rowNum));
    }

    setValue('product_qty[' + new_product + ']', split_qty);
    setValue('supplier_product_qty[' + new_product + ']', split_qty);
    setValue('product_qty' + rowNum, product_qty - split_qty);
    setValue('supplier_product_qty' + rowNum, product_qty - split_qty);
}

function setBulkData(data) {
    if (data == null) return;

    if (data['return_message'] == 'add_bulk') {
        var row = addBulk();
        var currency = getValue('invoice_currency');

        var bulk_number = '000000' + data['bulk_id'];
        bulk_number = 'BO-' + bulk_number.substring(bulk_number.length - 4);

        setValue('product_id[' + row + ']', bulk_number);
        setValue('sub_id[' + row + ']', '0');
        setValue('product_name[' + row + ']', data['title']);
        setValue('product_qty[' + row + ']', '1');
        setValue('product_cost[' + row + ']', convertCurrency(data['bulk_cost'], 'AUD', currency));
        setValue('supplier_product_cost[' + row + ']', convertCurrency(data['bulk_cost'], 'AUD', currency));
        setValue('product_online[' + row + ']', convertCurrency(data['bulk_online'] / 1.1, 'AUD', currency));
        setValue('product_offer[' + row + ']', convertCurrency(data['bulk_offer'] / 1.1, 'AUD', currency));
        setValue('product_weight[' + row + ']', data['bulk_weight']);
        setValue('product_weight_inc[' + row + ']', data['bulk_weight']);
        setValue('product_warranty[' + row + ']', data['bulk_warranty']);
        setValue('product_condition[' + row + ']', data['bulk_condition']);
        setValue('product_terms[' + row + ']', data['bulk_terms']);
        setValue('product_voltage[' + row + ']', data['bulk_voltage']);
        setValue('generic_code[' + row + ']', bulk_number);
        setValue('product_price_changed[' + row + ']', 'n');
        if (data['bulk_notes'] != null) setValue('product_notes[' + row + ']', data['bulk_notes']);
        if (data['bulk_weight'] <= 0.0 && addMissingWeight != null) addMissingWeight(bulk_number, data['title'], data['bulk_url']);

        var data2 = data;
        data2['return_message'] = 'bulk_select_' + row;
        setSupplierData(data2);



        productChange(row);
    }
}

function setProductData(data) {
    if (data == null) return;

    if (data['return_message'].substring(0, 7) == 'select_') {
        var row = data['return_message'].substring(7);
		/*
		generic[100]
		generic_code[100]
		prod_count[100]
		product_cost[100]
		product_id[100]
		product_name[100]
		product_notes[100]
		product_offer[100]
		product_online[100]
		product_price_changed[100]
		product_qty[100]
		product_rank[100]
		product_updated[100]
		product_weight[100]
		product_weight_inc[100]
		sub_id[100]
		*/
        if (data['product_id'] == null) data['product_id'] = getValue('product_id[' + row + ']');
        if (data['sub_id'] == null) data['sub_id'] = 0;
        if (data['min_order'] == null) data['min_order'] = 1;
        if (data['product_cost'] == null) data['product_cost'] = '0.00';
        if (data['product_online'] == null) data['product_online'] = '0.00';
        if (data['product_offer'] == null) data['product_offer'] = '0.00';
        if (data['product_weight'] == null) data['product_weight'] = 0.0;
        if (data['product_weight_inc'] == null) data['product_weight_inc'] = 0.0;
        if (data['product_weight_inc'] == 0.0) data['product_weight_inc'] = data['product_weight'];
        if (data['product_notes'] == null) data['product_notes'] = '';
        if (data['product_condition'] == null) data['product_condition'] = 0;
        if (data['stock'] == null) data['stock'] = 'c';
        if (data['dPoints'] == null) data['dPoints'] = 2;
        if (data['section'] == null) data['section'] = 0;

        setValue('product_id[' + row + ']', data['product_id']);
        setValue('sub_id[' + row + ']', data['sub_id']);
        setValue('product_name[' + row + ']', data['product_name']);
        setValue('product_qty[' + row + ']', data['min_order']);
        setValue('product_condition[' + row + ']', data['product_condition']);
        setValue('product_cost[' + row + ']', data['product_cost']);
        setValue('product_notes[' + row + ']', data['product_notes']);
        setValue('product_offer[' + row + ']', data['product_offer']);
        setValue('product_online[' + row + ']', data['product_online']);
        setValue('product_section[' + row + ']', data['section']);
        setValue('decimal[' + row + ']', data['dPoints']);
        setValue('product_price_changed[' + row + ']', 'n');
        setValue('product_warranty[' + row + ']', data['warranty']);
        setValue('product_weight[' + row + ']', data['product_weight']);
        setValue('product_weight_inc[' + row + ']', data['product_weight_inc']);
        setValue('supplier_product_cost[' + row + ']', data['product_cost']);
        setValue('supplier_country[' + row + ']', data['location']);

        if (data['supplier'] == 0 && (data['stock'] == 's' || data['stock'] == 'ss')) {
            data['supplier_id'] = '660';
            data['supplier_name'] = 'Highway Entertainment Pty Ltd';
            //data['country']='AU';
            if (data['voltage'] != null) setValue('product_voltage[' + row + ']', data['voltage']);
            if (data['plug_type'] != null) setValue('product_plug[' + row + ']', data['plug_type']);
            if (data['condition'] != null) setValue('product_condition[' + row + ']', data['condition']);
            if (data['packing_terms'] != null) setValue('product_packing[' + row + ']', data['packing_terms']);
            if (data['ship_terms'] != null) setValue('product_terms[' + row + ']', data['ship_terms']);
        }
        setValue('supplier_product_qty[' + row + ']', data['min_order']);

        if (data['producttype_id'] != null) setValue('producttype_id', data['producttype_id']);

        if (data['product_weight'] <= 0.0 && addMissingWeight != null) addMissingWeight(data['product_id'], data['product_name'], data['product_url']);
    }

    setSupplierData(data);
    if (data['supplier'] != null) {
        if (typeof (data['supplier']) == "object")
            selectSupplier(row, data['supplier'][0]);
        else
            selectSupplier(row, data['supplier']);
    }

    productChange(row);
}

function setForwardersData(data) {
    if (data['return_message'].substring(0, 7) == 'select_') {
        var row = data['return_message'].substring(7);
        setValue('supplier_currency[' + row + ']', data['currency']);
        setValue('supplier_id[' + row + ']', data['id']);
        //setValue('supplier_product_id['+row+']',data['product_id']);
        //setValue('supplier_sub_id['+row+']',data['sub_id']);
        //setValue('supplier_product_name['+row+']',data['supplier_product_name']);
        setValue('supplier_name[' + row + ']', data['name']);
        setValue('supplier_country[' + row + ']', data['country']);
        setValue('supplier_found[' + row + ']', 'y');
        productChange(row, 'sfound');
    }
}

function setForwarderData(data) {
    if (data['return_message'].substring(0, 7) == 'select_') {
        var row = data['return_message'].substring(7);
        //setValue('supplier_currency['+row+']',data['cost_currency']);
        setValue('supplier_id[' + row + ']', data['id']);
        //setValue('supplier_product_id['+row+']',data['product_id']);
        //setValue('supplier_sub_id['+row+']',data['sub_id']);
        //setValue('supplier_product_name['+row+']',data['supplier_product_name']);
        setValue('supplier_name[' + row + ']', data['name']);
        setValue('supplier_country[' + row + ']', data['country']);
        setValue('supplier_found[' + row + ']', 'y');
        productChange(row, 'sfound');
    }
}

function setSupplierData(data) {
    if (data == null) return;

    if (data['return_message'] == 'default') {
        setValue('def_sup_name', data['supplier_name']);
        setValue('default_supplier', data['id']);
        if (data['warranty'] != null) setValue('def_warranty', data['warranty']);
        if (data['condition'] != null) setValue('def_condition', data['condition']);
        if (data['packing'] != null) setValue('def_packing', data['packing']);
        if (data['voltage'] != null) setValue('def_voltage', data['voltage']);
        if (data['plug'] != null) setValue('def_plug', data['plug']);
        if (data['terms'] != null) setValue('def_terms', data['terms']);
        if (data['currency'] != null) setValue('def_currency', data['currency']);
        if (data['country'] != null) {
            setValue('def_country', data['country']);
            setValue('supply_country', data['country']);
        }
    }

    if (data['return_message'].substring(0, 7) == 'select_') {
        var row = data['return_message'].substring(7);
		/*
		product_condition[100]
		product_packing[100]
		product_plug[100]
		product_terms[100]
		product_voltage[100]
		product_warranty[100]
		span_supplier_currency[100]
		supplier_country[100]
		supplier_currency[100]
		supplier_found[100]
		supplier_id[100]
		supplier_name[100]
		supplier_notes[100]
		supplier_product_cost[100]
		supplier_product_id[100]
		supplier_product_name[100]
		supplier_product_qty[100]
		supplier_sub_id[100]
		*/
        if (data['supplier'] != '' && data['supplier'] != null) data['supplier_id'] = data['supplier'];
        if (data['supplier_id'] == '' || data['supplier_id'] == null) data['supplier_id'] = data['id'];
        if (data['supplier_product_name'] == '' || data['supplier_product_name'] == null) data['supplier_product_name'] = data['product_name'];

        if (data['cost_currency'] != null) setValue('supplier_currency[' + row + ']', data['cost_currency']);
        if (data['supplier_id'] != null) setValue('supplier_id[' + row + ']', data['supplier_id']);
        if (data['product_id'] != null) setValue('supplier_product_id[' + row + ']', data['product_id']);
        if (data['sub_id'] != null) setValue('supplier_sub_id[' + row + ']', data['sub_id']);
        if (data['supplier_product_name'] != null) setValue('supplier_product_name[' + row + ']', data['supplier_product_name']);
        if (data['supplier_name'] != null) setValue('supplier_name[' + row + ']', data['supplier_name']);
        if (data['currency'] != null) {
            setValue('supplier_currency[' + row + ']', data['currency']);
            productChange(row, 'scost');
        }
        var supC = getValue('supplier_country[' + row + ']');
        if (data['country'] != null && (supC == '' || supC == 0)) setValue('supplier_country[' + row + ']', data['country']);
        // now update the default values if the section & the condition match
        var entered = false;
        var site_id = getValue('site_id');
        var section = getValue('product_section[' + row + ']');
        var condition = getValue('product_condition[' + row + ']');
        if (data['values'] != '' && data['values'] != null) {
            for (key in data['values']) {
                if (entered) break;
                var tmp = data['values'][key];
                if (site_id == tmp['company'] && section == tmp['section'] && condition == tmp['condition']) {
                    if (tmp['warranty'] != null) setValue('product_warranty[' + row + ']', tmp['warranty']);
                    if (tmp['voltage'] != null) setValue('product_voltage[' + row + ']', tmp['voltage']);
                    if (tmp['plug'] != null) setValue('product_plug[' + row + ']', tmp['plug']);
                    if (tmp['condition'] != null) setValue('product_condition[' + row + ']', tmp['condition']);
                    if (tmp['packing'] != null) setValue('product_packing[' + row + ']', tmp['packing']);
                    if (data['terms'] != null) setValue('product_terms[' + row + ']', tmp['terms']);
                    entered = true;
                }
            }
        }

        //now have to update the overall Packing/Shipping Terms


        if (data['qty'] != null && data['reserved'] != null)
            setValue('supplier_found[' + row + ']', ((data['qty'] - data['reserved'] > 0) ? ((data['stock'] == 's' || data['stock'] == 'ss') ? 's' : 'y') : 'n'));
        productChange(row, 'sfound');
    }

    if (data['return_message'].substring(0, 12) == 'bulk_select_') {
        var row = data['return_message'].substring(12);

        var bulk_number = '000000' + data['bulk_id'];
        bulk_number = 'BO-' + bulk_number.substring(bulk_number.length - 4);

        if (data['supplier_id'] == '' || data['supplier_id'] == null)
            data['supplier_id'] = data['id'];
        if (data['supplier_product_name'] == '' || data['supplier_product_name'] == null)
            data['supplier_product_name'] = data['product_name'];

        setValue('supplier_country[' + row + ']', data['bulk_country']);
        setValue('supplier_id[' + row + ']', data['bulk_supplier']);
        setValue('supplier_name[' + row + ']', data['bulk_supplier_name']);
        setValue('supplier_product_qty[' + row + ']', '1');
        setValue('supplier_currency[' + row + ']', 'AUD');
        setValue('supplier_product_id[' + row + ']', bulk_number);
        setValue('supplier_sub_id[' + row + ']', '');
        setValue('supplier_product_name[' + row + ']', data['title']);

        setValue('supplier_found[' + row + ']', 'y');
        productChange(row, 'sfound');
    }
}

function setExtraSupplierData() {
    var ids = getProductRows();
    var def_country = '';
    var pack = '';
    var terms = '';
    for (key in ids) {
        var tmp = getValue('supplier_country' + ids[key]);
        if (def_country == '' || tmp == def_country) def_country = tmp;
        else def_country = '';
        tmp = getValue('product_packing' + ids[key]);
        if (pack == '' || tmp == pack) pack = tmp;
        else pack = '';
        tmp = getValue('product_terms' + ids[key]);
        if (terms == '' || tmp == terms) terms = tmp;
        else terms = '';
    }
    var tm = getElement('terms_id');
    tm.value = terms; tm.style.backgroundColor = '#' + ((terms == 0) ? 'EE90A0' : 'FFFFFF');
    var dc = getElement('supply_country');
    dc.value = def_country; dc.style.backgroundColor = '#' + ((def_country == '') ? 'EE90A0' : 'FFFFFF');
    var pr = getElement('pack_requirement_id');
    pr.value = pack; pr.style.backgroundColor = '#' + ((pack == 0) ? 'EE90A0' : 'FFFFFF');
}

function updateAllSuppliers(supplier_name, obj) {
    var old_val = obj.value;
    obj.value = 'Please Wait...Updating';
    var supplier_id = getValue('default_supplier');
    var rows = getProductRows();
    for (key in rows) {
        var tmp = rows[key];
        setValue('supplier_name' + tmp, supplier_name);
        setValue('supplier_id' + tmp, supplier_id);
        var updateNotes = false;
        if (getValue('def_warranty') != 0) { setValue('product_warranty' + tmp, getValue('def_warranty')); updateNotes = true; }
        if (getValue('def_condition') != 0) { setValue('product_condition' + tmp, getValue('def_condition')); updateNotes = true; }
        if (getValue('def_voltage') != 0) { setValue('product_voltage' + tmp, getValue('def_voltage')); updateNotes = true; }
        if (getValue('def_plug') != 0) { setValue('product_plug' + tmp, getValue('def_plug')); updateNotes = true; }
        if (getValue('def_currency') != "") {
            setValue('supplier_currency' + tmp, getValue('def_currency'));
            productChange(tmp.substr(1, tmp.length - 2), 'scostau');
            updateNotes = true;
        }
        if (getValue('def_country') != "") {
            setValue('supplier_country' + tmp, getValue('def_country'));
            setValue('supply_country', getValue('def_country'));
        }
        if (getValue('def_packing') != 0) setValue('product_packing' + tmp, getValue('def_packing'));
        if (getValue('def_terms') != 0) setValue('product_terms' + tmp, getValue('def_terms'));
        if (updateNotes) updateProductNotes(tmp);
    }
    obj.value = old_val;
}

function updateProductNotes(prod_count) {
    var warranty = 0/*<?php jecho($warranty_terms)?>*/;
    var voltage = 0/*<?php jecho($voltage_terms)?>*/;
    var notes = [];
    var val1 = getValue('product_voltage' + prod_count);
    var val2 = getValue('product_plug' + prod_count);
    var val3 = getValue('product_warranty' + prod_count);
    var val4 = getValue('product_condition' + prod_count);
    var count = 0;
    if (val1 != 0) { notes[count] = 'Power Supply Voltage: ' + voltage[val1]; count++ }
    if (val2 != 0) { notes[count] = 'Power Adapter ID: ' + val2; count++; }
    if (val3 != 0) { notes[count] = 'Warranty: ' + warranty[val3]; count++; }
    if (val4 == 2) { notes[count] = 'Condition: Used'; count++; }
    if (getValue('product_updated' + prod_count) == 'n') setValue('product_notes' + prod_count, notes.join("\n"));
}

function actionPurchasePreview(theFrm) {
    var oldact = theFrm.action;
    var oldtrg = theFrm.target;
    theFrm.action = "invoice_purchase_preview.php";
    theFrm.target = "_blank";
    theFrm.submit();
    theFrm.action = oldact;
    theFrm.target = oldtrg;
}

function actionPurchaseOrder(theFrm) {
    //Do a check first
    var rows = getElement('product_table').rows;
    var ids = [];
    var count = 0;
    for (var i = 0; i < rows.length; i++) {
        var n = rows[i].id;
        if (n != null) {
            var t1 = n.split("[");
            var t2 = t1[1].split("]");
            if (!array_search(t2[0], ids))
                ids[count++] = t2[0];
        }
    }
    for (var i = 0; i < ids.length; i++)
        if (getValue('supplier_id[' + ids[i] + ']') == 0) {
            alert("One or more products do not have a supplier\n\nPlease correct this before trying again");
            return false;
        }

    var oldact = theFrm.action;
    theFrm.action = ".?action=do_purchase";
    theFrm.submit();
    theFrm.action = oldact;
}

function updateCostPrice(id, site_id, row_id) {
    var url = 'update_cost_price.php?product_id=' + id + '&site_id=' + site_id + '&return_message=' + row_id + '&currency=' + getValue('invoice_currency');
    if (confirm('This will update the cost price for ID#' + id + '. Continue??')) window.open(url, '', 'width=10,height=10');
}

function setCostPrice(data) {
    row_id = data['return_message'];
    setValue('supplier_product_cost[' + row_id + ']', data['product_cost']);
    productChange(row_id, 'scost');
    productChange(row_id, 'scostau');
}

function updateProductPrice(row) {
    var product_id = getValue('product_id[' + row + ']');
    var sub_id = getValue('sub_id[' + row + ']');
    var product_qty = getValue('product_qty[' + row + ']');
    var generic = getValue('generic[' + row + ']');
    var site_id = getValue('site_id');
    var member_id = getValue('member_id');
    var currency = getValue('invoice_currency');
    var url = 'invoice_update_price.php?product_id=' + product_id;
    if (sub_id != null) url += '&sub_id=' + sub_id;
    if (site_id != null) url += '&site_id=' + site_id;
    if (member_id != null) url += '&member_id=' + member_id;
    if (currency != null) url += '&currency=' + currency;
    if (product_qty != null) url += '&product_qty=' + product_qty;
    productChange(row, 'pqty');
/*<?php
if (9 != $_GLOBALS['site_settings']['site_id']) {
?>*/
    if (generic == 'n') {
        //now start of the AJAX script.
        if (window.XMLHttpRequest) { // Mozilla, Safari, ...
            http_request = new XMLHttpRequest();
            //if (http_request.overrideMimeType) http_request.overrideMimeType('text/xml');
            if (http_request.overrideMimeType) http_request.overrideMimeType('text/plain');
        }
        else if (window.ActiveXObject) { //IE
            try {
                http_request = new ActiveXObject("Msxml2.XMLHTTP");
            }
            catch (e) {
                try {
                    http_request = new ActiveXObject("Microsoft.XMLHTTP");
                }
                catch (e) { }
            }
        }
        if (!http_request) {
            alert('Giving up :( Cannot create an XMLHTTP instance');
            return false;
        }

        http_request.onreadystatechange = function () { updatePrices(http_request, row); };
        http_request.open('GET', url, true);
        http_request.send(null);
    }
/*<?php
}
?>*/
}

function updatePrices(http_request, row) {
    if (http_request.readyState == 4) {
        if (http_request.status == 200) {
            var prices = explode(', ', http_request.responseText);
            var price_changed = getValue('product_price_changed[' + row + ']');
            if (price_changed == 'n') {
                setValue('product_online[' + row + ']', prices[0]);
                setValue('product_offer[' + row + ']', prices[0]);
            }
            productUpdateTotals();
        }
        else alert('There was a problem with the request.');
    }

}

function makeProductIndividual(pcount) {
    var product_id = getValue('product_id[' + pcount + ']');
    var sub = getValue('sub_id[' + pcount + ']');
    var war = getValue('product_warranty[' + pcount + ']');
    var con = getValue('product_condition[' + pcount + ']');
    var vlt = getValue('product_voltage[' + pcount + ']');
    var pck = getValue('product_packing[' + pcount + ']');
    var trm = getValue('product_terms[' + pcount + ']');
    var cnt = getValue('supplier_country[' + pcount + ']');
    //var plg=getValue('product_plug['+pcount+']');
    var url = '/webadmin/products/products_add.php?link_id=' + product_id;
    if (sub != '') url += '&sub_id=' + sub;
    if (war != 0) url += '&warranty=' + war;
    if (vlt != 0) url += '&voltage=' + vlt;
    if (con != 0) url += '&condition=' + con;
    if (pck != 0) url += '&packing_terms=' + pck;
    if (trm != 0) url += '&ship_terms=' + trm;
    if (cnt != '') url += '&location=' + cnt;
    window.open(url);
}
