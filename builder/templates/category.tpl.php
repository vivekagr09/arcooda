<?php
/**
 * 	Project: Product Builder
 *
 *	category.tpl.php
 *
 *	This template is to display the category of machines
 *
 *	@author Kelvin (khtat@highwaygroup.net)
 *	@since  03 JAN 2018
 */

// Get categories
$categories = $builder['category'];

//@fixme /images/builder/... needs to be relocated
$imageurl = '/images/builder/';
?>

<div style="padding: 25px 0; width: 75%; margin: auto;" id="machine-slider">
	
	<?php
	foreach($categories as $cat) {
		$name = $pb->getCategoryNameById($cat);
		$image = $pb->getCategoryImageById($cat);
		// Url for redirection
		$r_url = $website;
		$r_url .= (isset($_GET['preview'])) ? "&cat={$cat}" : "/{$cat}";

		echo "<div class='grid-inline-top align-middle cat' style=' width: 270px; margin: 10px; outline: none;' onclick='redirect(\"{$r_url}\");'>";
		echo "<div class='cat-selector button-selector option-container-border'>";
		echo "<div class='option-container'>";

		echo "<div class='option-name-wrapper'>";
		echo "<h4>{$name}</h4>";
		echo "</div>";

		echo "<div class='option-image-wrapper'>";
		echo "<img src='{$imageurl}{$image}' width='250' height='250' class='option-image' />";
		echo "</div>"; // END option-image-wrapper

		echo "<div style='padding:20px 0;'></div>";
		/*echo "<div class='option-btn-wrapper'>";
		echo "<div class='option-btn-radio'></div>";
		echo "</div>"; // end option-btn-wrapper*/

		echo "</div>";
		echo "</div>";
		echo "</div>";
	}
	?>

</div>

<script type="text/javascript">
	function redirect($link) {
		window.location.href=$link;
	}

	$(document).ready(function(){
		$('#machine-slider').slick({
		  centerMode: false,
		  infinite: false,
		  centerPadding: '60px',
		  slidesToShow: 4,
		  arrows:true,
		});
	});
</script>