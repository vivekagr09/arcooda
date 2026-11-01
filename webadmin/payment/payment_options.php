<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$action            = $posted['action'];
$site_id           = $posted['site_id'];
$payment_method_id = $posted['id'];
$distinctList      = paymentMethodsDistinct();
//$sites=array(1=>'Highwaygames', 2=>'Highway Entertainment', 9=>'Converters.tv', 11=>'Highwaygroup', 16=>'Car Converters');
$sites = getWebsitesList();
switch ($action) {
    case 'do_edit':
        paymentMethodToDatabase($posted);
        $action = 'edit';
        break;
}
?>
<script type="text/javascript" src="payment_funcs.js"></script>
<link rel="stylesheet" type="text/css" href="/webadmin/payment/style.css" />
<div class="payment_content" style="padding-left:10px;">
<br /><br />
<?php
switch ($action) {
    case 'edit':
        include 'templates/edit_payment_options.tpl.php';
        break;
    case 'list':
    default:
        include 'templates/payment_optionlist.php';
}
?>

</div>

<?php

include_once __DIR__ . '/footer.inc.php';
