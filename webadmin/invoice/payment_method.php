<?php
include_once __DIR__ . '/common.inc.php';

global $_GLOBALS;
global $DB;

$invoice_id     = null;
$site_id        = $_GLOBALS['site_settings']['site_id'];
$paymenttype_id = null;
$member_id      = null;

if (array_key_exists('invoice_id', $_GET) && 0 != $_GET['invoice_id']) {
    $invoice_id = intval($_GET['invoice_id']);
    moduleLoad('invoice');
    $invoice        = invoiceFromDatabase($invoice_id);
    $site_id        = intval($invoice['site_id']);
    $paymenttype_id = $invoice['paymenttype_id'];
    $member_id      = intval($invoice['member_id']);
}
if (array_key_exists('site_id', $_GET) && 0 != $_GET['site_id']) {
    $site_id = intval($_GET['site_id']);
}
if (array_key_exists('paymenttype_id', $_GET)) {
    $paymenttype_id = $_GET['paymenttype_id'];
}
if (array_key_exists('member_id', $_GET) && 0 != $_GET['member_id']) {
    $member_id = intval($_GET['member_id']);
}

$paymenttype_list = invoiceGetOptionList('paymenttype_id');

if (!function_exists('invoicePaymentMethodFromDatabase')) {
    /**
     * @param string $payment_method_id
     * @param int    $site_id
     *
     * @return mixed[]|null
     */
    function invoicePaymentMethodFromDatabase($payment_method_id, $site_id = 0)
    {
        global $DB;

        if (0 != $site_id && $row = $DB->query_first(sprintf(
            "SELECT * FROM `highwaygroup`.`payment_methods` WHERE `site_id` = %d AND `payment_method_id` = %s LIMIT 1",
            $site_id,
            $DB->escape($payment_method_id)
        ), MYSQLI_ASSOC)) {
            return $row;
        }

        if (0 != $site_id && $row = $DB->query_first(sprintf(
            "SELECT * FROM `highwaygroup`.`payment_methods` WHERE `site_id` = 0 AND `payment_method_id` = %s LIMIT 1",
            $DB->escape($payment_method_id)
        ), MYSQLI_ASSOC)) {
            return $row;
        }

        return $DB->query_first(sprintf(
            "SELECT * FROM `highwaygroup`.`payment_methods` WHERE `site_id` = 11 AND `payment_method_id` = %s LIMIT 1",
            $DB->escape($payment_method_id)
        ), MYSQLI_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css" />
    </head>
	<body style="--bs-body-color:#444">

<div class="container"><div class="row row-cols-1 row-cols-lg-2 g-4">
<?php
function paymentMethodCard($payment_method, $invoice_id = null, $member_id = null)
{
    ob_start();
?>
    <div class="col"><div class="card" style="--bs-card-bg:#eee"><div class="card-body">
        <div class="d-flex">
            <h2 class="card-title"><?=htmlspecialchars($payment_method['payment_method_name'])?></h2>
            <div class="d-flex ms-auto">
                <button type="button" class="btn btn-clipboard" aria-label="Copy to clipboard">
                    <i class="bi bi-clipboard"></i>
                </button>
            </div>
        </div>
        <table class="table">
<?php
    foreach (explode("\n", $payment_method["payment_method_description"]) as $description) {
        if ('' != trim($description)) {
            $split = explode(":", $description, 2);
            switch (trim(strtolower($split[0]))) {
                case 'beneficiary bank name':
                    //no break
                case 'bank':
                    $bank_logo = sprintf(
                        'images/%s_logo.png',
                        preg_replace("/[\\0- ]+/", "_", trim(strtolower($split[1])))
                    );
                    if (is_file(__DIR__ . '/' . $bank_logo)) {
?>
            <tr><td colspan="2" style="text-align:center;"><img src="<?=htmlspecialchars($bank_logo)?>" alt="" title="<?=htmlspecialchars($split[1])?>" /></td></tr>
<?php
                    }
                    //no break
                default:
                    if (count($split) > 1) {
?>
            <tr>
                <th style="width:33%"><?=htmlspecialchars(trim($split[0]))?>:</th>
                <td><?=htmlspecialchars(trim($split[1]))?></td>
            </tr>
<?php
                    } else {
?>
            <tr><td colspan="2"><?=htmlspecialchars(trim($split[0]))?></td></tr>
<?php
                    }
                    break;
            }
        }
    }
    if ('BPAY' == $payment_method['payment_method_id']) {
        moduleLoad('bpay');
?>
            <tr>
                <th style="width:33%">Biller Code:</th>
                <td>424606</td>
            </tr>
<?php
        if (0 != $member_id && function_exists('bpayGenerateNumber')) {
?>
            <tr>
                <th style="width:33%">Reference:</th>
                <td><?=htmlspecialchars(str_replace("\r\n", " ", chunk_split(bpayGenerateNumber($member_id, $invoice_id), 5)))?></td>
            </tr>
<?php
        }
    } elseif (0 != $invoice_id && false !== strpos($payment_method['payment_method_id'], 'BANK')) {
?>
            <tr>
                <th style="width:33%">Reference:</th>
                <td><?=htmlspecialchars(invoiceGetValue($invoice_id, 'invoice_number'))?></td>
            </tr>
<?php
    }
?>
        </table>
    </div></div></div>
<?php
    return ob_get_clean();
}

if (array_key_exists($paymenttype_id, $paymenttype_list)) {
    $payment_method = invoicePaymentMethodFromDatabase($paymenttype_id, $site_id);
    echo paymentMethodCard($payment_method, $invoice_id, $member_id);
} else {
    foreach ($paymenttype_list as $paymenttype_id => $title) {
        $payment_method = invoicePaymentMethodFromDatabase($paymenttype_id, $site_id);
        echo paymentMethodCard($payment_method, $invoice_id, $member_id);
    }
}
?>
</div></div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js" integrity="sha384-Xe+8cL9oJa6tN/veChSP7q+mnSPaj5Bcu9mPX5F5xIGE0DVittaqT5lorf0EI7Vk" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.min.js" integrity="sha384-ODmDIVzN+pFdexxHEHFBQH3/9/vQ9uori45z4JjnFsRydbmQbmL5t1tQ0culUzyK" crossorigin="anonymous"></script>
        <script type="text/javascript">
//<![CDATA[
$(function() {
    $(".btn-clipboard").click(function() {
        var src = $(this).closest(".card")[0];
        var aux = document.createElement("div");
        aux.setAttribute("contentEditable", true);
        aux.innerHTML = src.innerHTML;
        aux.setAttribute("onfocus", "document.execCommand('selectAll', false, null)");
        src.appendChild(aux);
        aux.focus();
        document.execCommand("copy");
        src.removeChild(aux);
    });
});
//]]>
        </script>
    </body>
</html>
