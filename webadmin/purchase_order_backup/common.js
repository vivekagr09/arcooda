function searchArray(needle, haystack) {
    if (needle == null || haystack == null) return null;
    for (var i = 0; i < haystack.length; i++) if (needle == haystack[i]) return i;
    return null;
}

function updateSelect(elem) {
    if (elem) elem.style.backgroundColor = (elem.value == "y") ? "#00FF00" : "#FF0000";
}

let Invoice = {
    field: function (elem) {
        return $(elem).attr("name").split("[")[0];
    },
    selector: function (field) {
        if (Array.isArray(field)) {
            var result = [];
            while (field.length) {
                result.push(this.selector(field.pop()));
            }
            return result.join(",");
        }
        return "[name='" + field + "']";
    },
    get: function (field) {
        return $(this.selector(field)).val();
    },
    set: function (field, value) {
        var $elem = $(this.selector(field));
        $elem.text(value);
        if (value !== $elem.val()) $elem.val(value).change();
    },
    toCurrency: function (value, dst, decimal) {
        var src = Invoice.get("invoice_currency");
        if (decimal === undefined) {
            decimal = 2;
            if ($.inArray(dst, ["ADP", "BIF", "BYR", "CLF", "CLP", "DJF", "ESP", "GNF", "ITL", "JPY", "KMF", "KRW", "LUF", "MGF", "PYG", "RWF", "TRL", "VUV", "XAF", "XOF", "XPF"]) >= 0) decimal = 0;
            if ($.inArray(dst, ["MRO", "MGA"]) >= 0) decimal = 1;
            if ($.inArray(dst, ["BHD", "IQD", "JOD", "KWD", "LYD", "OMR", "TND"]) >= 0) decimal = 3;
        }
        return convertCurrency(value, src, dst, false, decimal);
    },
    fromCurrency: function (value, src, decimal) {
        var dst = Invoice.get("invoice_currency");
        if (decimal === undefined) {
            decimal = 2;
            if ($.inArray(dst, ["ADP", "BIF", "BYR", "CLF", "CLP", "DJF", "ESP", "GNF", "ITL", "JPY", "KMF", "KRW", "LUF", "MGF", "PYG", "RWF", "TRL", "VUV", "XAF", "XOF", "XPF"]) >= 0) decimal = 0;
            if ($.inArray(dst, ["MRO", "MGA"]) >= 0) decimal = 1;
            if ($.inArray(dst, ["BHD", "IQD", "JOD", "KWD", "LYD", "OMR", "TND"]) >= 0) decimal = 3;
        }
        return convertCurrency(value, src, dst, false, decimal);
    }
};

let InvoiceCharge = {
    row: function (elem) {
        return $(elem).attr("name").split("[")[1].split("]")[0];
    },
    rows: function () {
        var result = [];
        $(this.selector("charge_count")).each(function() {
            result.push(this.row(this));
        });
        return result;
    },
    field: function (elem) {
        //return $(elem).attr("name").split("[")[2].split("]")[0];
        return $(elem).attr("name").split("[")[0];
    },
    selector: function (field, row) {
        if (Array.isArray(field)) {
            var result = [];
            while (field.length) {
                result.push(this.selector(field.pop(), row));
            }
            return result.join(",");
        }
        if (Array.isArray(row)) {
            var result = [];
            while (row.length) {
                result.push(this.selector(field, row.pop()));
            }
            return result.join(",");
        }
        if (row === undefined) {
            //return "[name^='invoice_charges['][name$='][" + field + "]']";
            return "[name^='" + field + "[']";
        }
        //return "[name='invoice_charges[" + row + "][" + field + "]']";
        return "[name='" + field + "[" + row + "]']";
    },
    get: function (field, row) {
        return $(this.selector(field, row)).val();
    },
    set: function (field, row, value) {
        var $elem = $(this.selector(field, row));
        if (value !== $elem.val()) $elem.val(value).change();
    },
    sum: function (field, scale) {
        var total = bcadd("0", "0", scale);
        $(this.selector(field)).each(function () {
            total = bcadd(total, $(this).val(), scale);
        });
        return total;
    },
    allMatch: function (field, val) {
        var result = true;
        $(this.selector(field)).each(function () {
            return result = result && ($(this).val() == val);
        });
        return result;
    },
    someMatch: function (field, val) {
        var result = false;
        $(this.selector(field)).each(function () {
            return !(result = result || ($(this).val() == val));
        });
        return result;
    }
};

let InvoiceProduct = {
    row: function (elem) {
        return $(elem)[0].name.split("[")[1].split("]")[0];
    },
    rows: function () {
        var result = [];
        $(this.selector("prod_count")).each(function() {
            result.push(this.row(this));
        });
        return result;
    },
    field: function (name) {
        //return $(elem).attr("name").split("[")[2].split("]")[0];
        return $(elem).attr("name").split("[")[0];
    },
    selector: function (field, row) {
        if (Array.isArray(field)) {
            var result = [];
            while (field.length) {
                result.push(this.selector(field.pop(), row));
            }
            return result.join(",");
        }
        if (Array.isArray(row)) {
            var result = [];
            while (row.length) {
                result.push(this.selector(field, row.pop()));
            }
            return result.join(",");
        }
        if (row === undefined) {
            //return "[name^='invoice_products['][name$='][" + field + "]']";
            return "[name^='" + field + "[']";
        }
        //return "[name='invoice_products[" + row + "][" + field + "]']";
        return "[name='" + field + "[" + row + "]']";
    },
    get: function (field, row) {
        return $(this.selector(field, row)).val();
    },
    set: function (field, row, value) {
        var $elem = $(this.selector(field, row));
        if (value !== $elem.val()) $elem.val(value).change();
    },
    sum: function (field, scale) {
        var total = bcadd("0", "0", scale);
        $(this.selector(field)).each(function () {
            total = bcadd(total, $(this).val(), scale);
        });
        return total;
    },
    and: function (field, val) {
        var result = true;
        $(this.selector(field)).each(function () {
            return result = result && ($(this).val() == val);
        });
        return result;
    },
    or: function (field, val) {
        var result = false;
        $(this.selector(field)).each(function () {
            return !(result = result || ($(this).val() == val));
        });
        return result;
    }
};

/*<?php/*
let InvoiceAsset = {
    row: function (elem) {
        return $(elem)[0].name.split("[")[1].split("]")[0];
    },
    rows: function () {
        var result = [];
        $(this.selector("asset_rank")).each(function() {
            result.push(this.row(this));
        });
        return result;
    },
    field: function (elem) {
        //return $(elem).attr("name").split("[")[2].split("]")[0];
        return $(elem).attr("name").split("[")[0];
    },
    selector: function (field, row) {
        if (Array.isArray(field)) {
            var result = [];
            while (field.length) {
                result.push(this.selector(field.pop(), row));
            }
            return result.join(",");
        }
        if (Array.isArray(row)) {
            var result = [];
            while (row.length) {
                result.push(this.selector(field, row.pop()));
            }
            return result.join(",");
        }
        if (row === undefined) {
            //return "[name^='invoice_assets['][name$='][" + field + "]']";
            return "[name^='" + field + "[']";
        }
        //return "[name='invoice_assets[" + row + "][" + field + "]']";
        return "[name='" + field + "[" + row + "]']";
    },
    get: function (field, row) {
        return $(this.selector(field, row)).val();
    },
    set: function (field, row, value) {
        var $elem = $(this.selector(field, row));
        if (value !== $elem.val()) $elem.val(value).change();
    },
    sum: function (field, scale) {
        var total = bcadd("0", "0", scale);
        $(this.selector(field)).each(function () {
            total = bcadd(total, $(this).val(), scale);
        });
        return total;
    }
};

let InvoiceCourierQuote = {
    row: function (elem) {
        return $(elem)[0].name.split("[")[1].split("]")[0];
    },
    rows: function () {
        var result = [];
        $(this.selector("quote_count")).each(function() {
            result.push(this.row(this));
        });
        return result;
    },
    field: function (name) {
        //return $(elem).attr("name").split("[")[2].split("]")[0];
        return $(elem).attr("name").split("[")[0];
    },
    selector: function (field, row) {
        if (Array.isArray(field)) {
            var result = [];
            while (field.length) {
                result.push(this.selector(field.pop(), row));
            }
            return result.join(",");
        }
        if (Array.isArray(row)) {
            var result = [];
            while (row.length) {
                result.push(this.selector(field, row.pop()));
            }
            return result.join(",");
        }
        if (row === undefined) {
            //return "[name^='invoice_courier_quotes['][name$='][" + field + "]']";
            return "[name^='" + field + "[']";
        }
        //return "[name='invoice_courier_quotes[" + row + "][" + field + "]']";
        return "[name='" + field + "[" + row + "]']";
    },
    get: function (field, row) {
        return $(this.selector(field, row)).val();
    },
    set: function (field, row, value) {
        var $elem = $(this.selector(field, row));
        if (value !== $elem.val()) $elem.val(value).change();
    },
    sum: function (field, scale) {
        var total = bcadd("0", "0", scale);
        $(this.selector(field)).each(function () {
            total = bcadd(total, $(this).val(), scale);
        });
        return total;
    }
};
let InvoiceDecline = {
    row: function (elem) {
        return $(elem)[0].name.split("[")[1].split("]")[0];
    },
    rows: function () {
        var result = [];
        $(this.selector("id")).each(function() {
            result.push(this.row(this));
        });
        return result;
    },
    field: function (name) {
        //return $(elem).attr("name").split("[")[2].split("]")[0];
        return $(elem).attr("name").split("[")[0];
    },
    selector: function (field, row) {
        if (Array.isArray(field)) {
            var result = [];
            while (field.length) {
                result.push(this.selector(field.pop(), row));
            }
            return result.join(",");
        }
        if (Array.isArray(row)) {
            var result = [];
            while (row.length) {
                result.push(this.selector(field, row.pop()));
            }
            return result.join(",");
        }
        if (row === undefined) {
            //return "[name^='invoice_declines['][name$='][" + field + "]']";
            return "[name^='" + field + "[']";
        }
        //return "[name='invoice_declines[" + row + "][" + field + "]']";
        return "[name='" + field + "[" + row + "]']";
    },
    get: function (field, row) {
        return $(this.selector(field, row)).val();
    },
    set: function (field, row, value) {
        var $elem = $(this.selector(field, row));
        if (value !== $elem.val()) $elem.val(value).change();
    },
    sum: function (field, scale) {
        var total = bcadd("0", "0", scale);
        $(this.selector(field)).each(function () {
            total = bcadd(total, $(this).val(), scale);
        });
        return total;
    }
};
let InvoiceJob = {
    row: function (elem) {
        return $(elem)[0].name.split("[")[1].split("]")[0];
    },
    rows: function () {
        var result = [];
        $(this.selector("job_id")).each(function() {
            result.push(this.row(this));
        });
        return result;
    },
    field: function (name) {
        //return $(elem).attr("name").split("[")[2].split("]")[0];
        return $(elem).attr("name").split("[")[0];
    },
    selector: function (field, row) {
        if (Array.isArray(field)) {
            var result = [];
            while (field.length) {
                result.push(this.selector(field.pop(), row));
            }
            return result.join(",");
        }
        if (Array.isArray(row)) {
            var result = [];
            while (row.length) {
                result.push(this.selector(field, row.pop()));
            }
            return result.join(",");
        }
        if (row === undefined) {
            //return "[name^='invoice_jobs['][name$='][" + field + "]']";
            return "[name^='" + field + "[']";
        }
        //return "[name='invoice_jobs[" + row + "][" + field + "]']";
        return "[name='" + field + "[" + row + "]']";
    },
    get: function (field, row) {
        return $(this.selector(field, row)).val();
    },
    set: function (field, row, value) {
        var $elem = $(this.selector(field, row));
        if (value !== $elem.val()) $elem.val(value).change();
    },
    sum: function (field, scale) {
        var total = bcadd("0", "0", scale);
        $(this.selector(field)).each(function () {
            total = bcadd(total, $(this).val(), scale);
        });
        return total;
    }
};
let InvoiceLock = {
    row: function (elem) {
        return $(elem)[0].name.split("[")[1].split("]")[0];
    },
    rows: function () {
        var result = [];
        $(this.selector("lock_id")).each(function() {
            result.push(this.row(this));
        });
        return result;
    },
    field: function (name) {
        //return $(elem).attr("name").split("[")[2].split("]")[0];
        return $(elem).attr("name").split("[")[0];
    },
    selector: function (field, row) {
        if (Array.isArray(field)) {
            var result = [];
            while (field.length) {
                result.push(this.selector(field.pop(), row));
            }
            return result.join(",");
        }
        if (Array.isArray(row)) {
            var result = [];
            while (row.length) {
                result.push(this.selector(field, row.pop()));
            }
            return result.join(",");
        }
        if (row === undefined) {
            //return "[name^='invoice_locks['][name$='][" + field + "]']";
            return "[name^='" + field + "[']";
        }
        //return "[name='invoice_locks[" + row + "][" + field + "]']";
        return "[name='" + field + "[" + row + "]']";
    },
    get: function (field, row) {
        return $(this.selector(field, row)).val();
    },
    set: function (field, row, value) {
        var $elem = $(this.selector(field, row));
        if (value !== $elem.val()) $elem.val(value).change();
    },
    sum: function (field, scale) {
        var total = bcadd("0", "0", scale);
        $(this.selector(field)).each(function () {
            total = bcadd(total, $(this).val(), scale);
        });
        return total;
    }
};
let InvoiceMessage = {
    row: function (elem) {
        return $(elem)[0].name.split("[")[1].split("]")[0];
    },
    rows: function () {
        var result = [];
        $(this.selector("message_id")).each(function() {
            result.push(this.row(this));
        });
        return result;
    },
    field: function (name) {
        //return $(elem).attr("name").split("[")[2].split("]")[0];
        return $(elem).attr("name").split("[")[0];
    },
    selector: function (field, row) {
        if (Array.isArray(field)) {
            var result = [];
            while (field.length) {
                result.push(this.selector(field.pop(), row));
            }
            return result.join(",");
        }
        if (Array.isArray(row)) {
            var result = [];
            while (row.length) {
                result.push(this.selector(field, row.pop()));
            }
            return result.join(",");
        }
        if (row === undefined) {
            //return "[name^='invoice_messages['][name$='][" + field + "]']";
            return "[name^='" + field + "[']";
        }
        //return "[name='invoice_messages[" + row + "][" + field + "]']";
        return "[name='" + field + "[" + row + "]']";
    },
    get: function (field, row) {
        return $(this.selector(field, row)).val();
    },
    set: function (field, row, value) {
        var $elem = $(this.selector(field, row));
        if (value !== $elem.val()) $elem.val(value).change();
    },
    sum: function (field, scale) {
        var total = bcadd("0", "0", scale);
        $(this.selector(field)).each(function () {
            total = bcadd(total, $(this).val(), scale);
        });
        return total;
    }
};

let InvoicePayment = {
    row: function (elem) {
        return $(elem)[0].name.split("[")[1].split("]")[0];
    },
    rows: function () {
        var result = [];
        $(this.selector("payment_count")).each(function() {
            result.push(this.row(this));
        });
        return result;
    },
    field: function (name) {
        //return $(elem).attr("name").split("[")[2].split("]")[0];
        return $(elem).attr("name").split("[")[0];
    },
    selector: function (field, row) {
        if (Array.isArray(field)) {
            var result = [];
            while (field.length) {
                result.push(this.selector(field.pop(), row));
            }
            return result.join(",");
        }
        if (Array.isArray(row)) {
            var result = [];
            while (row.length) {
                result.push(this.selector(field, row.pop()));
            }
            return result.join(",");
        }
        if (row === undefined) {
            //return "[name^='invoice_payments['][name$='][" + field + "]']";
            return "[name^='" + field + "[']";
        }
        //return "[name='invoice_payments[" + row + "][" + field + "]']";
        return "[name='" + field + "[" + row + "]']";
    },
    get: function (field, row) {
        return $(this.selector(field, row)).val();
    },
    set: function (field, row, value) {
        var $elem = $(this.selector(field, row));
        if (value !== $elem.val()) $elem.val(value).change();
    },
    sum: function (field, scale) {
        var total = bcadd("0", "0", scale);
        $(this.selector(field)).each(function () {
            total = bcadd(total, $(this).val(), scale);
        });
        return total;
    }
};
*///?>
