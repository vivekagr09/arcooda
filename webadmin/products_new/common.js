let Product = {
    field: function (elem) {
        return $(elem).attr("name").split("[")[1].split("]")[0];
    },
    selector: function (field) {
        if (Array.isArray(field)) {
            var result = [];
            while (field.length) {
                result.push(this.selector(field.pop()));
            }
            return result.join(",");
        }
        return "[name='item[" + field + "]']";
    },
    get: function (field) {
        return $(this.selector(field)).val();
    },
    set: function (field, value, noBubble) {
        var $elem = $(this.selector(field));
        $elem.text(value);
        if (value !== $elem.val()) {
            if (noBubble) $elem.val(value);
            else $elem.val(value).change();
        }
    }
};
let ProductDimension = {
    field: function (elem) {
        return $(elem).attr("name").split("[")[1].split("]")[0];
    },
    selector: function (field) {
        if (Array.isArray(field)) {
            var result = [];
            while (field.length) {
                result.push(this.selector(field.pop()));
            }
            return result.join(",");
        }
        return "[name='item_dimension[" + field + "]']";
    },
    get: function (field) {
        return $(this.selector(field)).val();
    },
    set: function (field, value, noBubble) {
        var $elem = $(this.selector(field));
        if (value !== $elem.val()) {
            if (noBubble) $elem.val(value);
            else $elem.val(value).change();
        }
    }
};
let ProductPacking = {
    row: function (elem) {
        return $(elem)[0].name.split("[")[1].split("]")[0];
    },
    rows: function () {
        var result = [];
        var self = this;
        $(this.selector("id")).each(function() {
            result.push(self.row(this));
        });
        return result;
    },
    field: function (elem) {
        return $(elem).attr("name").split("[")[2].split("]")[0];
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
            //console.log("[name^='item_packings['][name$='][" + field + "]']");
            //return "[name^='item_packings['][name$='][" + field + "]']";
            return "[name^='item_packings[0][" + field + "]']";
        }
        return "[name='item_packings[" + row + "][" + field + "]']";
    },
    get: function (field, row) {
        return $(this.selector(field, row)).val();
    },
    set: function (field, row, value, noBubble) {
        var $elem = $(this.selector(field, row));
        if (value !== $elem.val()) {
            if (noBubble) $elem.val(value);
            else $elem.val(value).change();
        }
    },
    sum: function (field, scale) {
        var total = bcadd("0", "0", scale);
        $(this.selector(field)).each(function () {
            total = bcadd(total, $(this).val(), scale);
        });
        return total;
    }
};
