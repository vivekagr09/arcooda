<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

$inc = 10;
$at  = intval($at);

$days_before = 2;

$statement = "SELECT *, UNIX_TIMESTAMP(date_added) AS added,
					UNIX_TIMESTAMP(expiry_date) AS expiry
				FROM wanted
				WHERE queued = 'y'
				AND active = 'y'
				AND DATE_SUB( expiry_date, INTERVAL $days_before DAY )  < NOW()
				AND notified_key = ''
				LIMIT $at, $inc";

$result = $DB->query($statement);

if ($DB->num_rows($result) > 0) {
    while ($row = $DB->fetch_array($result)) {
        expiry_email($row);
    }
    refresh($at + $inc);
} else {
    echo 'Done.';
}
include '../footer.inc.php';

//===========================================================================
//
//
// PARAMS: n/a
// RETURN: n/a
//===========================================================================
function refresh($at)
{
?>
<script>

function go()
{
	window.open('wanted_expire_email.php?at=<?=$at?>&time=<?=time()?>','_self','toolbar=yes,location=yes,status=yes,menubar=yes,scrollbars=yes,resizable=yes');
}

setTimeout('go()', 5000);

</script>

<?php
}

//===========================================================================
//
//
// PARAMS: n/a
// RETURN: n/a
//===========================================================================
function expiry_email($data)
{
    global $DB;

    $key = md5($data['id'] . microtime());

    $url = 'http://' . $_SERVER['HTTP_HOST'] . '/extend_wanted_request.html?k=' . $key;
    $msg = '

Dear ' . $data['customer_name'] . '

Your request for a wanted item "' . $data['item'] . '" is about'
    . ' expire on ' . date('d F Y', $data['expiry']) . '. You may wish to extend the'
        . ' request for another 2 or 4 weeks by clicking or visiting the'
        . ' link below. If you do not want to extend your request, please'
        . ' ignore this email.

Please visit the following web address to extend your request;

' . $url . '

Best Regards

Coinopexpress
';

    $msg = wordwrap($msg);

    $mailed = mail($data['customer_email'], "Wanted Request About to Expire", $msg,
        "From: Coinopexpress <coe@coinopexpress.com>\r\n"
        . "Reply-To: coe@coinopexpress.com\r\n");

    echo '<li>Sending mail to ... ' . $data['customer_email'] . ' ... ';
    if ($mailed) {
        echo 'OK';
        $statement = "UPDATE wanted SET notified_key = '$key' WHERE id = '" . $data['id'] . "'";
        $DB->query($statement);
    } else {
        echo 'Failed!!!';
    }

    echo '</li>';
//    echo $url;

}
