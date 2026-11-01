<?php
/**
 *     Project: Product Builder
 *
 *    index.php
 *
 *    This page is preview page for product builder.
 *
 *    @author Kelvin (khtat@highwaygroup.net)
 *    @since  18 JAN 2018
 */

// To detect mobile
include_once __DIR__ . "/inc/Mobile_Detect.php";
$detect = new Mobile_Detect();
if ($detect->isMobile()) {
    $redirect = str_replace("builder", "builder-mobile", $_SERVER['REDIRECT_SCRIPT_URI']);
    header("Location: " . $redirect);
    exit();
}
include_once __DIR__ . "/inc/common.inc.php";
// Global variables
$title            = ""; // Title of the page
$continueShopping = ""; // URL for continue shopping
$website          = ""; // The  website of current builder page
$logo             = ""; // Logo of the current builder page
$cat              = ""; // Category of builder
$url              = ""; // URL
// Find out if it's https or http
$isSecure = false;
if (isset($_SERVER['HTTPS']) && 'on' == $_SERVER['HTTPS']) {
    $isSecure = true;
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO'] || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && 'on' == $_SERVER['HTTP_X_FORWARDED_SSL']) {
    $isSecure = true;
}
$REQUEST_PROTOCOL = $isSecure ? 'https' : 'http';

$logo = "/brand.png";
$url  = "/builder/";
if (isset($_GET['preview'])) {
    $url = "/webadmin/product_builder/builder/";
}

// Get product builder module
$pb = new ProductBuilder($DB);
// Preview and live page
if (isset($_GET['preview'])) {
    // Preview the builder from admin
    if (isset($_GET['builder_id']) && !empty($_GET['builder_id'])) {
        $id = $_GET['builder_id'];
    } else {
        die("No preview to show");
    }

    if (isset($_GET['cat']) && !empty($_GET['cat'])) {
        $cat = $_GET['cat'];
    }

    // Build the query for preview
    $website = "?builder_id={$_GET['builder_id']}&preview";

    $title = " | Preview";
} else {
    // Live builder page

    // Get the current accessed website uri
    $builder_name = "";

    // Get builder name
    if (isset($_GET['name']) && !empty($_GET['name'])) {
        $builder_name = $_GET['name'];
    }

    // Get builder categories
    if (isset($_GET['cat']) && !empty($_GET['cat'])) {
        $cat = $_GET['cat'];
    }

    // Build query for this website
    $website = $url . $builder_name;

    // To search builder, use actual desktop url
    $_website = $website;

    // Get the builder id by uri
    $id = $pb->getBuilderIdByWebsite($_website);
    // If ID not found
    if (null == $id) {
        include $_SERVER['DOCUMENT_ROOT'] . "/messages/not_found.html";
        die();
    }
}

// Builder
$builder = $pb->getBuilderById($id);
// Check if builder is active
if (!isset($_GET['preview']) && 0 == $builder['status']) {
    include $_SERVER['DOCUMENT_ROOT'] . "/messages/not_found.html";
    die();
}

// If builder id does not exsit
if (count($builder) == 0) {
    include $_SERVER['DOCUMENT_ROOT'] . "/messages/not_found.html";
    die();
}

// Builder machines
$machines = $builder['machines'];
// Page title
$title = $builder['name'];
?>
<!DOCTYPE html>

<html>
<head>



	<title><?=$title?></title>
	<link href="<?=$url?>css/style.css" type="text/css" rel="stylesheet" />
	<link rel="stylesheet" type="text/css" href="<?=$url?>js/slick/slick.css"/>
  	<link rel="stylesheet" type="text/css" href="<?=$url?>js/slick/slick-theme.css"/>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<body id="app-body">
	<!-- Header -->
	<div id="header-container">
		<!--
		<div id="logo-wrapper"><img src="<?=$logo?>" alt="logo" height="37" /></div>
		<div id="builder-title-wrapper">
			<h1 class="clr" id="builder-title"><?=strtoupper($builder['name'])?></h1>
		</div>
		-->
		<div id="builder-title-wrapper" class="align-middle">
        <a href="/"><div id="logo-wrapper" class="grid-inline-top"><img src="<?=$logo?>" alt="Logo" height="37" /></div></a>

			<h1 class="clr" id="builder-title" style="width: 80%;"><?=strtoupper($builder['name'])?></h1>
<?php

if (1 != $builder['category'][0] && "" != $cat) {
    // Show the category selected
?>
			<h4 style='margin: 10px 0;'><?php echo strtoupper($pb->getCategoryNameById($cat)); ?></h4>
<?php
}
?>
			<ul class="clr" id="options-list" style="display: none;" data-url="<?=$url?>"></ul>
<?php
if ("" == $cat && 1 != $builder['category'][0]) {
    // Title for category selection
?>
			<div id="main-page-title" class="page-title" style="padding: 25px 0 0 0; display: none;"><h3 class="clr">Choose Category</h3></div>
<?php
} else {
    // Title for cabinet selection
?>
			<div id="main-page-title" class="page-title" style="padding: 25px 0 0 0;"><h3 class="clr">Choose Cabinets</h3></div>
<?php
}
?>
		</div>

		<!--div id="step-display" class="grid-inline-top"><span id="step-label">STEP</span><span id="current-step">1</span>/<span id="total-step">5</span></div-->
	</div>
	<!-- #Header -->

	<!-- page title -->
	<div id="main-content">
<?php
if ("" == $cat && 1 != $builder['category'][0]) {
?>
		<script type="text/javascript">
//<![CDATA[
document.getElementById('app-body').style.overflow = "hidden";
//]]>
		</script>
		<div id="start-image-container" style="cursor: pointer;" onclick="this.style.display='none'; document.getElementById('main-page-title').style.display='block'; document.getElementById('category-step').removeAttribute('style');"><img src="/webadmin/product_builder/builder/graphic/start-image.png" style="width:100%;" /></div>
		<div id="category-step" style="visibility: hidden;"><?php include __DIR__ . '/templates/category.tpl.php';?></div>
<?php
} else {
?>
		<div id="machine-step"><?php include __DIR__ . '/templates/first_v2.tpl.php';?></div>
<?php
}
?>
		<div id="options-step" style="display: flex;"></div>
	</div>
	<!-- #Main Content -->

	<!-- Fotoer -->
	<div id="footer-container">
		<!-- Preview Image -->
		<div class="align-middle grid-inline-mid" id="preview-img-container"><div id="preview-img-wrapper" class="grid-pdx10"><img src="http://via.placeholder.com/80x80" width="80" height="80" alt="Product Preview" id="preview-img" /></div></div>
		<!-- #Preview Image -->

		<div class="grid-inline-mid" id="total-price-container"><div id="total-price-wrapper" class="grid-pdx10 grid-inline-mid">
			<span style="color: #5D7C89; font-size: 18px;">Total</span><br />
			$<span id="total-price" data-price='0'>0</span><span id="currency" class="font-12">AUD</span>
		</div></div>

		<div class="grid-inline-mid" id="summary-footer" style="padding: 25px 10px; display: none;">
			<h3 class="clr" style="color: #5D7C89;">YOUR BUILD</h3>
			<h2 class="clr">SUMMARY</h2>
		</div>

		<!-- Buttons -->
		<div class="align-middle grid-inline-mid" style="padding: 25px; float: right;" id="button-wrapper">
			<div class="grid-inline-mid"><button type="button" class="blue-btn" id="previous-step-btn" data-builder-id="<?=$id?>" data-website="<?=$website?>" data-url="<?=$url?>">PREVIOUS</button></div>
			<div class="grid-inline-mid"><button type="button" class="blue-btn" id="next-step-btn" data-builder-id="<?=$id?>" data-website="<?=$website?>" data-url="<?=$url?>">NEXT</button></div>
		</div>

	</div>
	<!-- #Footer -->

	<div id="loading-overlay">
		<div id="loading-gif-wrapper"><div id="loading-gif"></div></div>
	</div>

	<script type="text/javascript" src="<?=$url?>js/slick/slick.min.js"></script>
	<script src="<?=$url?>js/app.js" type="text/javascript"></script>
</body>
</html>