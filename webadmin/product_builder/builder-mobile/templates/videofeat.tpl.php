<?php
/**
 *     Project: Product Builder
 *
 *    videofeat.tpl.php
 *
 *    Video feature template for product builder. This template uses youtube as
 *    a video to be displayed.
 *
 *    This template need options variables from parent.
 *    Options data can be optain from ProductBuilder Module.
 *
 *    @author Kelvin (khtat@highwaygroup.net)
 *    @since  15/02/2018
 */

$name = $options['name'];
$qty  = $options['qty'];

$optionData = $pb->getOptionById($options['id']);

// Sort options
$optionList = $pb->sortOptionsByPrice($optionData['options']);

?>
<div style="padding: 25px 0; width: 90%; margin: auto;" class="align-middle" id="videofeat-slider">
	<!--div class="page-title"><h2 class="clr">Choose <?php echo ucwords($name); ?></h2></div-->

	<?php
    $imageurl=(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") .$_SERVER['HTTP_HOST'].'/images/builder//';
foreach ($optionList as $itemKey => $item) {
    $total_price = $item['price'] * $qty;
    echo "<div class='grid-inline-top align-middle' style='margin: 0 10px; outline: none;'>";

    echo "<div class='option-container-border'>";

    echo "<div class='youtube-video-wrapper'>";
    if ("" == $item['youtube']) {
        echo "<div style='text-align: center; width: 100%;'>";
        echo "<img src='https://www.highwaygroup.net/images/builder/b0d1e258c463e29846bc34c0e2f6d9ad.png' width='70%' alt='No Game Board' title='No Game Board' style='margin:auto;'/>";
        echo "</div>";

        $previewImg = "https://www.highwaygroup.net/images/builder/b0d1e258c463e29846bc34c0e2f6d9ad.png";
    } else {
        echo "<iframe width='100%'src='https://www.youtube.com/embed/{$item['youtube']}?rel=0&amp;showinfo=0' frameborder='0' gesture='media' allow='encrypted-media' allowfullscreen></iframe>";
        $previewImg = "https://img.youtube.com/vi/{$item['youtube']}/0.jpg";
    }

    echo "</div>";

    echo "<h3 class='clr grid-pdx10'>{$item['header']}</h3>";
    echo "<div class='grid-pdx10'>";
    echo "<p class='clr description' style='text-align: left; font-size: 14px; min-height: 150px;'>";
    echo $item['description'];
    echo ("" == $item['infolink']) ? "" : " <a target='_blank' href='{$item['infolink']}' style='font-size: 12px;'>More Info</a>";
    echo "</p>";
    echo "</div>";

    echo "<div class='select-btn-wrapper'>";
    echo "<button style='width: 90%; margin: 10px 0;' class='option-select mob-org-select-btn button-selector' type='button' data-option-key='{$itemKey}' data-option-optionkey='3' data-option-name='{$item['header']}' data-option-img='$previewImg' data-option-price='{$total_price}' data-option-currency='{$item['currency']}' data-option-qty='{$qty}'>";

    echo "SELECT";
    echo " (<span>$" . $item['price'] . "</span><span style='font-size: 10px;'>{$item['currency']}</span>)";

    echo "</button>";
    echo "</div>";

    echo "</div>";

    echo "</div>";
}
?>
</div>

<script type="text/javascript">

	$(document).ready(function(){
		$('#videofeat-slider').slick({
		  centerMode: false,
		  infinite: false,
		  centerPadding: '60px',
		  slidesToShow: 1,
		  arrows:true
		});
	});

</script>