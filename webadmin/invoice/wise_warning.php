<?php
include_once __DIR__ . '/common.inc.php';

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

<div class="container m-4"><div class="card" style="--bs-card-bg:#eee"><div class="card-body">
    <div class="d-flex">
        <h2 class="card-title"><?=htmlspecialchars($payment_method['payment_method_name'])?></h2>
        <div class="d-flex ms-auto">
            <button type="button" class="btn btn-close" aria-label="Close Window"></button>
        </div>
    </div>
    <p>Wise Only accepts SWIFT payments from these countries:</p>
    <dl>
        <dt>Americas</dt>
        <dd>Canada, United States</dd>

        <dt>Asia</dt>
        <dd>China, Hong Kong, India, Japan, Philippines, Singapore, Taiwan, United Arab Emirates</dd>

        <dt>Oceania</dt>
        <dd>Australia, New Zealand</dd>

        <dt>Europe</dt>
        <dd>Austria, Belgium, Bulgaria, Croatia, Czech Republic, Denmark, Estonia, Finland, France, Germany, Gibraltar, Greece, Guernsey, Hungary, Iceland, Ireland, Isle of Man, Italy, Jersey, Latvia, Liechtenstein, Luxembourg, Malta, Netherlands, Norway, Poland, Portugal, Romania, Slovakia, Slovenia, Spain, Sweden, Switzerland, United Kingdom</dd>
    </dl>
    <p class="text-danger">If country is not displayed, DO NOT use Wise as a payment method as funds will not be received and customer will need to wait up to 7 days to receive refund.</p>
</div></div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js" integrity="sha384-Xe+8cL9oJa6tN/veChSP7q+mnSPaj5Bcu9mPX5F5xIGE0DVittaqT5lorf0EI7Vk" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.min.js" integrity="sha384-ODmDIVzN+pFdexxHEHFBQH3/9/vQ9uori45z4JjnFsRydbmQbmL5t1tQ0culUzyK" crossorigin="anonymous"></script>
        <script type="text/javascript">
//<![CDATA[
$(function() {
    $(".btn-close").click(function() {
        window.close();
    });
});
//]]>
        </script>
    </body>
</html>
