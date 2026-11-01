/**
 *	Project: Product Builder
 *
 *	app.js
 *
 *	Product builder website UI.
 *	Store the value selected by user on client storage.
 *	Each page transition is done by Ajax.
 *
 *	Framework: Jquery
 *
 *	Libraries: 
 *	- slick: For horizontal page scrolling for items
 *
 *	@author Kelvin (khtat@highwaygroup.net)
 *	@since  01/02/2018
 */

/**
 *	Global Variables
 */
var machineKey = -1;		  			// Selected machine key
var step = 0;			  			// Step of selection
var voltage = 0;			  			// voltage selected
var options = new Array(); 			// Store all selected items
var prices = new Array(); 			// Store all prices of each item
var shipping = "";		  			// Shipping method
var preference = 0;			  			// The last step
var currency = 0;			  			// The currency for this builder
var ajaxUrl = "ajax/request.php";	// Url for ajax request

$(document).ready(function () {
	/**
	 *	Button Selector
	 *
	 *	It selects and deselects the items.
	 *
	 *	Color Code:
	 *	------------------
	 *	#888888 When deselected
	 *	#FFB500 When selected
	 */
	$("body").on("click", ".button-selector", function (event) {
		// Deselect all button except the current selected one
		$(".button-selector").not(this).each(function () {
			if ($(this).hasClass("selected")) {
				$(this).removeClass("selected");
				$(this).find(".option-price").css("color", "#888888");
				$(this).find(".option-units").css("color", "#888888");
			}
		});

		if ($(this).hasClass("selected")) {	// Deselect this item
			$(this).removeClass("selected");
			$(this).find(".option-price").css("color", "#888888");
			$(this).find(".option-units").css("color", "#888888");
		} else {	// Select this item
			$(this).addClass("selected");
			$(this).find(".option-price").css("color", "#FFB500");
			$(this).find(".option-units").css("color", "#FFB500");
		}
	});

	/**
	 *	Button Checkbox
	 *
	 *	It check the selected items.
	 */
	$("body").on("click", ".button-checkbox", function (event) {
		// name of the checkbox
		var name = $(this).attr("data-name");

		// Uncheck all checkbox with this name except the current selected one
		$(".button-checkbox[data-name='" + name + "']").not(this).each(function () {
			if ($(this).hasClass("selected")) {
				$(this).removeClass("selected");
			}
		});

		if ($(this).hasClass("selected")) {	// Uncheck
			$(this).removeClass("selected");
		} else {	// Check
			$(this).addClass("selected");
		}
	});

	/**
	 *	Machine Selector
	 *
	 *	It sets machineKey of selected item
	 */
	$("body").on("click", ".machine-selector", function () {
		// Get button
		var btn = $(this).children().children(".option-btn-wrapper").children();

		// As default, deselected all machines
		$(".option-btn-radio.option-btn-radio-selected").removeClass("option-btn-radio-selected");

		if ($(this).hasClass("selected")) {	// Select
			// Get thumbnail
			var thumb = btn.attr("data-machine-thumb");

			// Get price
			var price = btn.attr("data-machine-price");

			// Get machine name
			var name = btn.attr("data-machine-name");

			// Set machine key
			machineKey = btn.attr("data-machine-key");

			// Set currency
			currency = btn.attr("data-machine-currency")

			// Image preview
			$("#preview-img").attr("src", thumb);

			// Update price
			updateTotalPriceDisplay(parseFloat(getTotalPrice()) + parseFloat(price));

			// Show footer
			showFooter();

			// selected
			btn.addClass("option-btn-radio-selected");

		} else {	// Deselect
			// Hide footer
			hideFooter();

			// Deselected
			btn.removeClass("option-btn-radio-selected");
		}
	});

	/**
	 *	Voltage Selector
	 *
	 *	It sets voltage value
	 */
	$("body").on("click", ".voltage-selector", function () {
		// As default deselect all voltage
		$(".voltage-selector[data-name='voltage']").find(".option-btn-radio.option-btn-radio-selected").removeClass("option-btn-radio-selected");

		// Get button
		var btn = $(this).find(".option-btn-radio");

		if ($(this).hasClass("selected")) {	// Select
			// Set voltage value
			voltage = btn.attr("data-value");

			// selected
			btn.addClass("option-btn-radio-selected");
		} else {	// Deselect
			// Reset voltage value
			voltage = 0;

			// Deselected
			btn.removeClass("option-btn-radio-selected");
		}
	});

	/**
	 *	Shipping method selector
	 *
	 *	It set the shpping method
	 */
	$("body").on("click", ".shipping-selector", function () {
		// As default, deselect all shipping methods
		$(".shipping-selector[data-name='shipping']").find(".option-btn-radio.option-btn-radio-selected").removeClass("option-btn-radio-selected");

		// Get button
		var btn = $(this).find(".option-btn-radio");

		//	Get price
		var price = btn.attr("data-price");

		if ($(this).hasClass("selected")) {	// Select
			// Set shipping method
			shipping = btn.attr("data-value");

			// Update price
			updateTotalPriceDisplay(parseFloat(getTotalPrice()) + parseFloat(price));

			// Selected
			btn.addClass("option-btn-radio-selected");
		} else {	// Deselect
			//Reset shipping method
			shipping = "";

			// Reset total price
			resetTotalPriceDisplay();

			// Deselected
			btn.removeClass("option-btn-radio-selected");
		}
	});

	/**
	 *	Option selector
	 *
	 *	It selects items and stores the selected items
	 */
	$("body").on("click", ".option-select", function () {
		// Id of this option item
		var id = getUniqueId();

		// Get button
		var btn = $(this).children().children(".option-btn-wrapper").children(".option-btn-radio");

		// Check if this option is radio selection type
		var radioType = (btn.length > 0) ? true : false;

		if (!radioType) {
			btn = $(this);
		} else {
			$(".option-btn-radio.option-btn-radio-selected").removeClass("option-btn-radio-selected");
		}

		if ($(this).hasClass("selected")) {	// Select
			// Get array key
			var option_arraykey = btn.attr("data-option-key");

			// Get option key
			var option_key = btn.attr("data-option-optionkey");

			// Get image
			var option_img = btn.attr("data-option-img");

			// Get name
			var option_name = btn.attr("data-option-name");

			// Get price
			var option_price = btn.attr("data-option-price");

			// Get currency
			var option_currency = btn.attr("data-option-currency");

			// Get quantity
			var option_qty = btn.attr("data-option-qty");

			// Update price
			updateTotalPriceDisplay(parseFloat(getTotalPrice()) + parseFloat(option_price));

			// Preview option item
			itemChainDisplay(option_name, option_img, id);

			// Add this option item
			options[id] = option_arraykey;

			if (radioType) {
				btn.addClass("option-btn-radio-selected");
			}
		} else {	// Deselect
			// Remove preview option item
			removePreviewFromItemChain(id);

			// Reset total price display
			resetTotalPriceDisplay();

			// Reset options
			options.splice(id, 1);

			if (radioType) {
				btn.removeClass("option-btn-radio-selected");
			}
		}
	});

	/**
	 *	Item Description
	 *
	 *	It display description when mouse is hovering the item.
	 */

	// Move description as mouse move
	$("body").on("mousemove", ".option-select, .machine-selector", function (event) {
		// Get axis
		var x = event.pageX;
		var y = event.pageY + 10;

		// Adjust axis when text touch the screen edge.
		if ($(window).width() - x < 270) {
			x = x - 260;
		}

		// Move the description
		$(".desc-overlay").css({
			"top": y + "px",
			"left": x + "px"
		});

	});

	// Display description when mouse is entered
	// Description will only be displayed for the item with option-container-border class
	$("body").on("mouseenter", ".option-select, .machine-selector", function (event) {
		if ($(this).hasClass("option-container-border")) {
			// Get axis
			var x = event.pageX;
			var y = event.pageY;

			// Build description overlay
			var html = "<div class='desc-overlay' style='top:" + y + "px; left:" + x + "px;'>";
			html += $(this).children().children(".desc-wrapper").html();
			html += "</div>";

			// Display it
			$("body").append(html);
		}
	});

	// Remove description when mouse left
	$("body").on("mouseleave", ".option-select, .machine-selector", function (event) {
		$(".desc-overlay").remove();
	});

	/**
	 *	Option list link
	 *
	 *	It redirects to the selected option selection page
	 */
	$("body").on("click", ".option-list-link", function () {
		// Get option key
		var optionKey = $(this).attr("href").substring(1, 2);

		// Update Step
		step = parseInt(optionKey) + 1;

		// Get builder id
		var id = $(this).attr("data-id");

		// Get URL
		var url = $(this).attr("data-url");

		// Check if the next option is clicked without selecting the current item
		if (optionKey != options.length && options.hasOwnProperty(optionKey) == false) {
			popupMessage("Opps!", "Please choose the item.");
		} else {
			// Selected link
			$(".option-list-link[href='#" + optionKey + "']").addClass("selected");

			// Remove images from item chain
			for (var i = optionKey; i < options.length; i++) {
				removePreviewFromItemChain(i);
			}

			// Reset options
			options.splice(optionKey);

			// Reset final step variables
			voltage = 0;
			shipping = "";

			// Update total price
			updateTotalPriceDisplay(prices[optionKey]);

			// Set total price
			setTotalPrice();

			//
			// Ajax Request
			//

			// Data to be sent
			var data = {
				machine_key: parseInt(machineKey) + 1,	// Add extra '1' because '0' represent false
				option_key: parseInt(optionKey) + 1,  // Add extra '1' because '0' represent false
				builder_id: id,
				summary: options,
				voltage: voltage,
				shipping: shipping,
				total_price: getTotalPrice()
			};

			$.ajax({
				type: "GET",
				url: url + ajaxUrl,
				dataType: "JSON",
				data: data,
				beforeSend: function () {
					// Requesting page
					ajaxLoading(null);
				},
				success: function (result) {
					// Page loaded
					ajaxLoaded(null, null);

					// load page
					$("#options-step").show().html(result['html']);

					// Update or load option list link
					updateOptionListLink(result['option_list'], id, url);

					// Determine if it's final step
					if (result['preference'] == 1) {
						preference = 1;
					} else {
						preference = 0;
					}

					// Show item selection footer
					itemSelectionFooter();
				}
			});
		}
	});

	/**
	 *	Next Step
	 *
	 *	It request next page and load it
	 */
	$("body").on("click", "#next-step-btn", function () {
		// Get button
		var btn = $(this);

		// Get builder id
		var id = btn.attr("data-builder-id");

		// Get url
		var url = btn.attr("data-url");

		// Get website
		var website = btn.attr("data-website");

		// Get option key
		// Option key is based on step.
		var optionKey = step;

		// Check the steps
		if (step == 0 && (machineKey == -1 || $(".machine-selector.selected").length == 0)) {	// Machine hasn't been selected yet
			popupMessage("Opps!", "Please choose the Cabinet.");
		} else {
			if (step > 0 && preference == 0 && (options.hasOwnProperty(getUniqueId()) == false || $(".option-select.selected").length == 0)) {	// Item hasn't been selected yet
				popupMessage("Opps!", "Please choose the item.")
			} else if (preference > 0 && (voltage == 0 || $(".voltage-selector.selected").length == 0)) {	// Voltage has been selected
				popupMessage("Opps!", "Please choose voltage.");
			} else if (preference > 0 && $(".shipping-selector").length > 0 && (shipping == "" || $(".shipping-selector.selected").length == 0)) {	// Shipping method hasn't been selected yet
				popupMessage("Opps!", "Please choose shipping method");
			} else {
				//	Set total price
				setTotalPrice();

				// Store this item price
				prices[step] = getTotalPrice();

				// Hide main page title
				hideMainPageTitle();

				//
				// Ajax request
				//

				// Data to be sent
				var data = {
					machine_key: parseInt(machineKey) + 1,	// Add extra '1' because '0' represent false
					option_key: parseInt(optionKey) + 1,  // Add extra '1' because '0' represent false
					builder_id: id,
					summary: options,
					voltage: voltage,
					shipping: shipping,
					website: website,
					total_price: getTotalPrice()
				};

				$.ajax({
					type: "GET",
					url: url + ajaxUrl,
					dataType: "JSON",
					data: data,
					beforeSend: function () {
						// Requesting page
						ajaxLoading(btn);
					},
					success: function (result) {
						// Page request successful
						ajaxLoaded(btn, "NEXT");

						// Hide machine selection page
						$("#machine-step").hide();

						// Load option items
						$("#options-step").show().html(result["html"]);

						// Update or load option list
						updateOptionListLink(result["option_list"], id, url);

						// If steps are finished
						if (result["done"] == 1) {
							// Display the final step footer
							finalStepFooter();
						} else {
							// Display normal footer
							itemSelectionFooter();
						}

						// Determine if this is the last step
						if (result['preference'] == 1) {
							preference = 1;
						} else {
							preference = 0;
						}

						// Clean the garbage
						clearTransition();
					}
				});

				// Increasement the step
				step++;
			}
		}
	});

	/**
	 *	Previous Step
	 *
	 *	it request the previouse page and load it
	 */
	$("body").on("click", "#previous-step-btn", function () {
		// Get button
		var btn = $(this);

		// Get builder id
		var id = btn.attr("data-builder-id");

		// Get url
		var url = btn.attr("data-url");

		// Get website
		var website = btn.attr("data-website");

		// Get previouse page price
		var price = prices[getUniqueId() - 1];

		// Reset all selection in previouse page
		resetSelection();

		// Check if the step is the first step
		if (step == 1) {	// Machine selection
			// Reset machine key
			machineKey = -1;

			// Show machine selection page
			$("#machine-step").show();

			// Clear all option items on options page
			$("#options-step").html("").hide();

			// Show the title
			showMainPageTitle();

			// Show machine selection footer
			machineSelectionFooter();

			// Hide footer
			hideFooter();

			// Hide option list on the top
			$("#options-list").hide();

			// Reset options
			if (options.hasOwnProperty(getUniqueId())) {
				options.splice(getUniqueId(), 1);
			}

			// Clear the price
			clearPrice();
		} else {	// Back to option selection
			// Get option key
			var optionKey = parseInt(getUniqueId());

			// Reset options and preview image
			if (preference == 0) {	// Not from final step
				if (options.hasOwnProperty(optionKey)) {	// Item value from the selection page before previouse button is clicked
					options.splice(optionKey, 1);
				}

				if (options.hasOwnProperty(optionKey - 1)) {	// Item value from the current page
					options.splice(optionKey - 1, 1);
				}

				// Remove preview image of the step before previouse button is clicked
				removePreviewFromItemChain(optionKey);

				// remove preview image of the current step
				removePreviewFromItemChain(optionKey - 1);

			} else {	// From final step
				if (options.hasOwnProperty(optionKey - 1)) {	// Item value before the last step
					options.splice(optionKey - 1, 1);
				}

				// Remove preview image before the final step
				removePreviewFromItemChain(optionKey - 1);
			}

			// 
			// Ajax request
			//

			// Data to be sent
			var data = {
				machine_key: parseInt(machineKey) + 1,	// Add extra '1' because '0' represent false
				option_key: parseInt(optionKey),
				builder_id: id,
				summary: options,
				voltage: voltage,
				shipping: shipping,
				website: website,
				total_price: price
			};

			$.ajax({
				type: "GET",
				url: url + ajaxUrl,
				dataType: "JSON",
				data: data,
				beforeSend: function () {
					// Requesting page
					ajaxLoading(btn);
				},
				success: function (result) {
					// Page request successful
					ajaxLoaded(btn, "PREVIOUS");

					// Load the page
					$("#options-step").html(result['html']);

					// Update or load option list link
					updateOptionListLink(result['option_list'], id, url);

					// Determine if it's final step
					if (result["preference"] == 1) {
						// Reset final step variables
						preference = 1;
						voltage = 0;
						shipping = "";
					} else {
						preference = 0;
					}

					// Display item selection footer
					itemSelectionFooter();

					// Clean the garbage
					clearTransition();
				}
			});

			// Update total price
			updateTotalPriceDisplay(price);

			// Set total price
			setTotalPrice();
		}

		// Decrease step
		step--;
	});

	/**
	 *	Textare placeholder
	 */

	// To hold placeholder text
	var textPlaceholder = "";

	$("body").on("focus", "textarea.textarea-input", function () {
		if ($(this).hasClass("placeholder")) {
			textPlaceholder = $(this).val();
			$(this).removeClass("placeholder").val("");
		}

		$(this).parent().css("border-color", "#FFB500");
	});

	$("body").on("blur", "textarea-input", function () {
		if ($(this).val().length == 0) {
			$(this).addClass("placeholder").val(textPlaceholder);
		}

		$(this).parent().removeAttr("style");
	});
});

/**
 *	Function: updateTotalPrice
 *
 *	This function will display the total price. The currency of total price
 *	is based on machine currency.
 *
 *	Thie price here is not final price yet as the price might be changed when
 *	the item selection is cancelled or selecte another item.
 */
function updateTotalPriceDisplay(price) {
	$("#total-price").text(price);
	$("#currency").text(currency)
}

/**
 *	Function: resetTotalPriceDisplay
 *
 *	This function will display the final selected total price.
 */
function resetTotalPriceDisplay() {
	$("#total-price").text(getTotalPrice());
}

/**
 *	Function: getTotalPrice
 *
 *	This function get the final selected total price
 */
function getTotalPrice() {
	return $("#total-price").attr("data-price");
}

/**
 *	Function: setTotalPrice
 *
 *	This function set the temporary total price to final total price when
 *	the selection is confirmed
 */
function setTotalPrice() {
	$("#total-price").attr("data-price", $("#total-price").text());
}

/**
 *	Function: clearPrice
 *
 *	This function clear all total price
 */
function clearPrice() {
	$("#total-price").attr("data-price", 0).text("0");
	prices = new Array();
}

/**
 *	Function: showFooter
 */
function showFooter() {
	$("#footer-container").show();
}

/**
 *	Function: hideFooter
 */
function hideFooter() {
	$("#footer-container").hide();
}

/**
 *	Function: showNextButton
 */
function showNextButton() {
	$("#next-step-btn").show();
}

/**
 *	Function: showPreviousButton
 */
function showPreviousButton() {
	$("#previous-step-btn").show();
}

/**
 *	Function: hideNextButton
 */
function hideNextButton() {
	$("#next-step-btn").hide();
}

/**
 *	Function: hidePreviousButton
 */
function hidePreviousButton() {
	$("#previous-step-btn").hide();
}

/**
 *	Function: showMainPageTitle
 */
function showMainPageTitle() {
	$("#main-page-title").show();
}

/**
 *	Function: hideMainPageTitle
 */
function hideMainPageTitle() {
	$("#main-page-title").hide();
}

/**
 *	Function: itemChainDisplay
 *
 *	This function add the preview image in preview image chain
 *
 *	@param String name The name of the item
 *	@param String img  The source of the preview image
 *	@param int    id   The uniquie id for preview image
 */
function itemChainDisplay(name, img, id) {
	var img = "<img src='" + img + "' width='80' height='80' alt='" + name + " Preview' id='item-chain-" + id + "' />";

	if ($("#item-chain-" + id).length > 0) {	// If this image preview already exists
		$("#item-chain-" + id).replaceWith(img);
	} else {	// New preview
		$("#preview-img-wrapper").append(img);
	}
}

/**
 *	Function: removePreviewFromItemChain
 *
 *	This function remove the item by unique id from preview
 *	image chain
 *
 *	@param int id The Unique ID of the preview image
 */
function removePreviewFromItemChain(id) {
	if ($("#item-chain-" + id).length > 0) {
		$("#item-chain-" + id).remove();
	}
}

/**
 *	Function: getUniqueId
 *
 *	This function get the unique id. The unique id
 *	is based on the step of the selection.
 *
 *	Range of Unique Id = [0, ~]
 *
 *	The begining of item selection page is step 1.
 *
 *	Therefore, substract 1 from step to produce uniquie id.
 *
 *	@return int The unique ID
 */
function getUniqueId() {
	return parseInt(step) - 1;
}

/**
 *	Function: ajaxLoading
 *
 *	This function is to display loading UI when ajax is requesting
 *	the page
 *
 *	Use NULL for the parameter if there is no button
 *
 *	@param DOM Object elem The button DOM Object 
 */
function ajaxLoading(elem) {
	// Check if elem is null
	if (elem != null) {
		// Loading text
		$(elem).text("LOADING");

		// Disable button
		$(elem).attr("disabled", "disabled");
	}

	// Show loading overlay
	$("#loading-overlay").css("display", "table");

	// Disable scroll when loading
	$("body").css("overflow", "hidden");
}

/**
 *	Function: ajaxLoaded
 *
 *	This function remove the loading UI when the ajax request is done
 *
 *	use NULL for both parameters if no parameters are passing
 *
 *	@param DOM Object elem  The button DOM Object
 *	@param String     label The label to be displayed on button
 */
function ajaxLoaded(elem, label) {
	// Check if elem and label are null
	if (elem != null && label != null) {
		// Reset the text
		$(elem).text(label);

		// Enable button
		$(elem).removeAttr("disabled");
	}

	// Hide loading overlay
	$("#loading-overlay").hide();

	// Enable scroll
	$("body").removeAttr("style");
}

/**
 *	Function: updateOptionListLink
 *
 *	This function load option list link on the top if not yet loaded or update it if
 *	it's been loaded.
 *
 *	@param Array  optionList List of options items name
 *	@param String builderId  ID of builder
 *	@param String url        URL of this website
 */
function updateOptionListLink(optionList, builderId, url) {
	var html = "<ul class='clr' id='options-list'>";
	for (var x in optionList) {
		var selected = (x == getUniqueId()) ? "selected" : "";
		html += "<li class='grid-inline-top'>";
		html += "<a href='#" + x + "' class='option-list-link " + selected + "' data-id='" + builderId + "' data-url='" + url + "'>";
		html += "<span>" + optionList[x] + "</span>";
		html += "</a>";
		html += "</li>";
	}
	html += "</ul>";

	$("#options-list").replaceWith(html);
}

/**
 *	Function: finalStepFooter
 *
 *	This function display and hide all necessary componentes on footer for final step
 */
function finalStepFooter() {
	// Hide all preview images
	$("#preview-img-container").hide();

	// Hide total price
	$("#total-price-container").hide();

	// Display summary footer
	$("#summary-footer").show();

	// Show previouse step button
	showPreviousButton();

	// Hide next step button
	hideNextButton();
}

/**
 *	Function: itemSelectionFooter
 *
 *	This function display and hide all necessary components on footer for item selection page
 */
function itemSelectionFooter() {
	// Show all preview images
	$("#preview-img-container").show();

	// Show total price
	$("#total-price-container").show();

	// Hide summary footer
	$("#summary-footer").hide();

	// Show previous step button
	showPreviousButton();

	// Show next step button
	showNextButton();
}

/**
 *	Function: machineSelectionFooter
 *
 *	This function display and hide all necessary components on footer for machine selection page
 */
function machineSelectionFooter() {
	// Show all previouse images
	$("#preview-img-container").show();

	// Show total price
	$("#total-price-container").show();

	// Hide summary footer
	$("#summary-footer").hide();

	// Hide previouse step button
	hidePreviousButton();

	// Show next step button
	showNextButton();
}

/**
 *	Function: clearTransition
 *
 *	To clearn anything which is stuck during the page transition
 */
function clearTransition() {
	// Remove description overlay if it's stuck
	if ($(".desc-overlay").length > 0) {
		$(".desc-overlay").remove();
	}
}

/**
 *	Function: resetSelection
 *
 *	This function reset all button selection
 */
function resetSelection() {
	$(".button-selector.selected").children().children(".option-btn-wrapper").children(".option-btn-radio").removeClass("option-btn-radio-selected");
	$(".button-selector.selected").find(".option-price").css("color", "#888888");
	$(".button-selector.selected").removeClass("selected");
	$(".button-selector.selected").removeClass("selected");
}

/**
 *	Function: sendInquiry
 *	
 *	This function validate the form and send an inquiry if valid.
 *	Ajax is used for submitting the form
 */
function sendInquiry(form) {
	var fname = form.fname;
	var lname = form.lname;
	var email = form.email;
	var country = form.country;
	var mail_list = (form.join_mailing_list.checked) ? "checked" : "unchecked";
	//var send_copy = (form.send_copy.checked) ? "checked" : "unchecked";
	var send_copy = "unchecked";
	var comments = form.comments;
	var price = form.price.value;
	var currency = form.currency.value;
	var sub_id = form.sub_id.value;

	var machine_name = form.machine_name.value;
	var machine_id = form.machine_id.value;

	var items = new Array();

	// Get all items
	$('.summary-items-input').each(function () {
		items.push($(this).val());
	});

	var errorMessages = new Array();

	if (fname.value.length == 0) {
		errorMessages.push("First Name is missing.");
	}

	if (lname.value.length == 0) {
		errorMessages.push("Last Name is missing.");
	}

	if (email.value.length == 0) {
		errorMessages.push("Email is missing.");
	} else if (ValidateEmail(email.value) == false) {
		errorMessages.push("Email is invalid.");
	}

	if (country.value.length == 0) {
		errorMessages.push("Country is missing.");
	}

	if ($(comments).hasClass("placeholder")) {
		errorMessages.push("Please enter the inquiry you want to make in comments.")
	}

	if (errorMessages.length > 0) {
		var html = "<ul class='clr' id='err-list'>";

		for (var x in errorMessages) {
			html += "<li>" + errorMessages[x] + "</li>";
		}

		html += "</ul>";

		$('#err').html(html).show();
	} else {
		var url = form.getAttribute("action");
		var type = form.getAttribute("method");

		var data = {
			fname: fname.value,
			lname: lname.value,
			email: email.value,
			country: country.value,
			mail_list: mail_list,
			send_copy: send_copy,
			comments: comments.value,
			machine_name: machine_name,
			machine_id: machine_id,
			items: items,
			currency: currency,
			price: price,
			sub_id: sub_id
		};

		$.ajax({
			url: url,
			type: type,
			data: data,
			beforeSend: function () {
				$('#send-btn').attr("disabled", "disabled").val("SENDING...");
			},
			success: function (result) {
				$("#inquiry_form").children(".sent-notification").show();
				$('#inquiry_form').children("form").remove();
				//$('#options-list, #preview-img-container, #total-price-container, #summary-footer, #button-wrapper').remove();
				$('#options-list, #footer-container').remove();
			}
		});
	}

	return false;
}

/**
 *	Function: ValidateEmail
 */
function ValidateEmail(mail) {
	if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail)) {
		return (true)
	}

	return (false)
}

/**
 *	Function: popupMessage
 *
 *	This function display the popup message.
 *
 *	@param String title   The title to be displayed
 *	@param String message The message to be displayed
 */
function popupMessage(title, message) {
	// Create popup overlay
	var popupOverlay = $($.parseHTML("<div></div>")).css({
		"position": "fixed",
		"top": "0",
		"left": "0",
		"width": "100%",
		"height": "100%",
		"background": "rgba(0,0,0,0.6)",
		"z-index": "999"
	});

	// Create popup window
	var popupWindow = $($.parseHTML("<div></div>")).css({
		"width": "450px",
		"background": "#ffffff",
		"margin": "100px auto",
		"border-radius": "10px",
		"-moz-border-radius": "10px",
		"-webkit-border-radius": "10px",
		"text-align": "center"
	});

	// Create popup title
	var popupTitle = $($.parseHTML("<div></div>"))
		.css({
			"padding": "20px 10px",
			"border-bottom": "1px solid #e0e0e0"
		})
		.append($($.parseHTML("<h2></h2>"))
			.css({
				"margin": "0"
			})
			.text(title)
		)
		;

	// Create popup message content
	var popupContent = $($.parseHTML("<div></div>"))
		.css({
			"padding": "30px 10px"
		})
		.append($($.parseHTML("<p></p>"))
			.text(message)
		)
		;

	// Create popup button
	var popupOkayButton = $($.parseHTML("<div></div>"))
		.css({
			"padding-bottom": "20px"
		})
		.append($($.parseHTML("<button></button>"))
			.css({
				"width": "250px",
				"height": "50px"
			})
			.addClass("blue-btn")
			.text("Okay")
		)
		;

	// Add action
	popupOkayButton.click(function () {
		popupOverlay.remove();
	});

	// Display popup message
	$("body").append(
		popupOverlay.append(
			popupWindow
				.append(popupTitle)
				.append(popupContent)
				.append(popupOkayButton)
		)
	);
}