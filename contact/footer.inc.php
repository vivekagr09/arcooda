<?php
$title = 'Arcooda';
$body = ob_get_clean();

$split = explode('<title>', $body);
if (count($split) > 1) {
    $title = explode('</title>', $split[1]);
    $title = $title[0];
}

$split = explode('<body>', $body);
if (count($split) > 1) {
    $body = explode('</body>', $split[1]);
    $body = $body[0];
}


?><!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8" />
    <title><?= $title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="format-detection" content="telephone=no" />
    <link rel="icon" type="image/png" href="/img/favicon.png" />

    <!-- fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,400i,500,500i,700,700i" />

    <!-- css -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" integrity="sha256-UhQQ4fxEeABh4JrcmAJ1+16id/1dnlOEVCFOxDef9Lw=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.3/photoswipe.min.css" integrity="sha256-LWdHSKWG7zv3DTpee8YAgoTfkj3gNkfauF624h4P2Nw=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.3/default-skin/default-skin.min.css" integrity="sha256-Q9bBMw/rHRRag46GDWY84J3elDNc8JJjKXL9tIC4oe8=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" integrity="sha256-FdatTf20PQr/rWg+cAKfl6j4/IY3oohFAJ7gVC3M34E=" crossorigin="anonymous" />

    <link rel="stylesheet" href="/css/style.css" />
    <link rel="stylesheet" href="/css/style.header-classic-variant-one.css" media="(min-width: 1200px)" />
    <link rel="stylesheet" href="/css/style.mobile-header-variant-one.css" media="(max-width: 1199px)" />

    <!-- font - fontawesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.1/css/all.min.css" integrity="sha256-7rF6RaSKyh16288E3hVdzQtHyzatA2MQRGu0cf6pqqM=" crossorigin="anonymous" />


</head>
<body>

<!-- site -->
<div class="site">

    <!-- site__body -->
    <div class="site__body">
        <?= $body ?>
    </div>
    <!-- site__body / end -->

</div>
<!-- site / end -->

<!-- scripts -->
<script src="https://code.jquery.com/jquery-3.4.0.min.js" integrity="sha256-BJeo0qm959uMBGb65z40ejJYGSgR7REI4+CW1fNKwOg=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js" integrity="sha256-pTxD+DSzIwmwhOqTFN+DB+nHjO4iAsbgfyFq5K5bcE0=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/13.1.4/nouislider.min.js" integrity="sha256-6NGkvvdjRehdAHFa9mUmcVBX8gSDO1CFTnfNiLMrPNY=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.3/photoswipe.min.js" integrity="sha256-ePwmChbbvXbsO02lbM3HoHbSHTHFAeChekF1xKJdleo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.3/photoswipe-ui-default.min.js" integrity="sha256-UKkzOn/w1mBxRmLLGrSeyB4e1xbrp4xylgAWb3M42pU=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js" integrity="sha256-d/edyIFneUo3SvmaFnf96hRcVBcyaOy96iMkPez1kaU=" crossorigin="anonymous"></script>

<!-- script for ReCaptch  -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<!-- end  -->

<script src="/js/number.js"></script>
<script src="/js/main.js"></script>

<?php
/*
<!-- livezilla.net PLACE SOMEWHERE IN BODY -->
<script type="text/javascript" id="65c5cdd476e0c1760e7ddff667572bb1" src="https://chat.highwaygroup.net/script.php?id=65c5cdd476e0c1760e7ddff667572bb1"></script>
<!-- livezilla.net PLACE SOMEWHERE IN BODY -->
*/
?>

</body>
</html>
