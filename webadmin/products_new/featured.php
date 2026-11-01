<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

//$criteria             = $_GLOBALS["criteria_options"]["products"];
$criteria1['active'] = 'y';
$criteria1['feature']='y';
$item_query           = productQuery($criteria1);
$today = date("'Y-m-d 00:00:00'");
$item_query=$item_query. " AND (`p`.`featured_expiry` ='0000-00-00 00:00:00' OR `p`.`featured_expiry` >= " . $today . ")";
$item_list            = array_reverse(productSearch($item_query));
?>

<script type="text/javascript">
//<![CDATA[
function featured_validate(target) {
    return true;
}
//]]>
</script>

<h2>Edit Featured Products</h2>
<?php
if ($_GET['status'] == 1) {
?>
<div id="warning" style="text-align: center;color: black;margin: 0px 32px;margin-bottom: 5px;background: lightgreen;font-weight: 600;">Featured list has been updated.</div>
<br /><br />
<?php
}
?>
<form action="." method="post">
    <input type="hidden" name="action" value="do_featured" />
<?php
include __DIR__ . '/featured_products.tpl.php';
?>
    <div style="text-align:center;">
        <input type="button" value="Save Changes" id="featured_save" onclick="if (featured_validate(this.form)) this.form.submit();" />
    </div>
</form>
<script>
    // Get a reference to the warning div and close button
    var warningDiv = document.getElementById("warning");
    var closeButton = document.getElementById("closeButton");

    // Function to hide the warning div after 10 seconds
    function hideWarning() {
        warningDiv.style.display = "none";
    }

    // Set a timeout to hide the warning div after 10 seconds
    var timeoutID = setTimeout(hideWarning, 10000);

    // // Add event listener to close button to hide the warning div immediately when clicked
    // closeButton.addEventListener("click", function() {
    //     clearTimeout(timeoutID); // Clear the timeout
    //     hideWarning(); // Hide the warning div immediately
    // });
</script>
<?php

include_once __DIR__ . '/footer.inc.php';
