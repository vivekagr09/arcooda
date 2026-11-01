let Distributor = {
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
    set: function (field, value, bubble) {
        bubble    = (typeof bubble !== 'undefined') ? bubble : true;
        var $elem = $(this.selector(field));
        $elem.text(value);
        if (value !== $elem.val()) {
            if (bubble) {
                $elem.val(value).change();
            } else {
                $elem.val(value);
            }
        }
    }
};


let DistributorContact = {
    field: function (elem) {
        return $(elem).attr("name").split("[")[2].split("]")[0];
    },
    row: function (elem) {
        return $(elem).attr("name").split("[")[1].split("]")[0];
    },
    rows: function () {
        var result = [];
        var self   = this;
        $(this.selector("id")).each(function() {
            result.push(self.row(this));
        });
        return result;
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
            return "[name^='item_contacts['][name$='][" + field + "]']";
        }
        return "[name='item_contacts[" + row + "][" + field + "]']";
    },
    get: function (field, row) {
        if (Array.isArray(row)) {
            var result = {};
            while (row.length) {
                var row_id = row.pop();
                result[row_id] = this.get(field, row_id);
            }
            return result;
        }
        if (row === undefined) {
            return this.get(field, this.rows());
        }
        return $(this.selector(field, row)).val();
    },
    set: function (field, row, value, bubble) {
        bubble    = (typeof bubble !== 'undefined') ? bubble : true;
        var $elem = $(this.selector(field, row));
        $elem.text(value);
        if (value !== $elem.val()) {
            if (bubble) {
                $elem.val(value).change();
            } else {
                $elem.val(value);
            }
        }
    },
    delete: function (row) {
        $("[name^='item_contacts_row'][name$='[" + row + "]']").detach();
    }
};
