<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header-inventory.inc.php';

$stock           = array();
$stock_additions = array();
?>

<script type="text/javascript">
//<![CDATA[
function validateInventoryForm(form)
{
    var pass = true;
    $(".required", form).each(function() {
        if ($(this).val() == "")
        {
            pass = false;
            $(this).css("background", "#faa").css("border", "2px solid #f00");
        }
        else $(this).css("background", "").css("border", "");
    });
    if (!pass)
    {
        alert("Please fill the details highlighted in red before continuing");
        return false;
    }

    var rows = [];
    $("[id^='stock_additions_rowa[']").each(function ()
    {
        rows.push($(this).attr("id").substr(20));
    });
    for (var i = 0;i < rows.length;i++)
    {
        var name_key = "stock_additions" + rows[i];
        weight = parseFloat($("[name='" + name_key + "[product_weight]']").val());
        if (isNaN(weight) || weight == 0.0)
        {
            alert("One of your products has undefined weight.\n\nPlease contact the product manager before proceeding");
            return false;
        }
    }

    return confirm("Are you sure you wish to enter this data into the system?\n\nThere will be no way to undo these changes");
    return true;
}
//]]>
</script>

<form action=".?action=do_add_inventory" method="post">
<?php
include __DIR__ . '/supplier_details.tpl.php';
include __DIR__ . '/stock_additions.tpl.php';
?>
<br />
<div style="text-align:center;"><input type="button" onclick="if(validateInventoryForm(this.form)) this.form.submit();" value="Add Inventory" /></div>
</form>
<?php

include_once __DIR__ . '/footer.inc.php';
