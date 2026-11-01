/**
 * 	Project: Product Builder
 *
 *	app.js
 *
 *	Basic UI for product builder.
 *	This app store the value selected by user.
 *	The page transition is done by requesting the template using Ajax.
 *
 *	@author Kelvin (khtat@highwaygroup.net)
 *	@since  09 JAN 2018
 */

var machineKey = -1;					 // Selected machine key
var step = 0;							 // Step of selection
var voltage = 0;						 // Voltage selected
var options = new Array();				 // Option selected
var ajaxRequestUrl = "/builder/ajax/request.html"; // link for ajax request
var prices = new Array();				 // prices chain of each step
var shipping = "";						 // Shipping method
var preference = 0;

$(document).ready(function(){
	/**
	 *	Show summary bubble
	 */
	$("#preview-img").on("mouseenter", function() {
		$('#summary-bubble-wrapper').show();
	});

	$("#summary-bubble-wrapper").on("mouseleave", function(){
		$(this).hide();
	})

	/**
	 *	Button Selector
	 */
	$("body").on("click", ".button-selector", function(event){
		
		$(".button-selector").not(this).each(function(){
			if ($(this).hasClass("selected")) {
				$(this).removeClass("selected");
				$(this).find('.option-price').css("color", "#888888");
				$(this).find('.option-units').css("color", "#888888");
			}
		});

		if ($(this).hasClass("selected")) {
			$(this).removeClass("selected");
			$(this).find('.option-price').css("color", "#888888");
			$(this).find('.option-units').css("color", "#888888");
		} else {
			$(this).addClass("selected");
			$(this).find('.option-price').css("color", "#FFB500");
			$(this).find('.option-units').css("color", "#FFB500");
		}
	});

	/**
	 *	Button Checkbox
	 */
	$("body").on("click", ".button-checkbox", function(event){
		var name = $(this).attr("data-name");
		$(".button-checkbox[data-name='"+ name +"']").not(this).each(function() {
			if ($(this).hasClass("selected")) {
				$(this).removeClass("selected");
			}
		});

		if ($(this).hasClass("selected")) {
			$(this).removeClass("selected");
		} else {
			$(this).addClass("selected");
		}
	});

	/**
	 *	Load option details in standard
	 */
	$("body").on("click", ".option-standard-details", function(){
		var content = $(this).children(".option-standard-details-content").html();

		$('#option-details-content-loader').html(content);
		$('#option-details-popup-bg').show();
	});

	$("body").on("click", "#option-details-popup-bg", function(event){
		if ($(event.target).is('#option-details-popup-bg')) {
			$('#option-details-popup-bg').hide();
		}

	});

	$("body").on("click", "#option-details-popup-cancel", function(){
		$("#option-details-popup-bg").hide();
	});

	/**
	 *	Select machine
	 */
	$("body").on("click", ".machine-selector", function(){
		btn = $(this).children().children(".option-btn-wrapper").children();

		$('.option-btn-radio.option-btn-radio-selected').removeClass("option-btn-radio-selected");

		if ($(this).hasClass("selected")) {
			var thumb = btn.attr("data-machine-thumb");
			var price = btn.attr("data-machine-price");
			var currency = btn.attr("data-machine-currency");
			var name = btn.attr("data-machine-name");
			machineKey = btn.attr("data-machine-key");

			$('#total-price').text(price);
			$('#currency').text(currency);
			$('#preview-img, #summary-machine-img').attr("src", thumb);

			var largeImg = thumb.replace("thumb", "large");
			$('#machine-image').attr("src", largeImg);

			$('#summary-machine-name').text(name);
			$('#summary-machine-price').text(currency + "$ " + price);

			$('#footer-container').show();

			btn.addClass("option-btn-radio-selected");

		} else {
			$('#footer-container').hide();

			btn.removeClass("option-btn-radio-selected");

		}
	});

	/**
	 *	Select Voltage
	 */
	$("body").on("click", ".voltage-selector", function(){
		$(".voltage-selector[data-name='voltage']").find('.option-btn-radio.option-btn-radio-selected').removeClass("option-btn-radio-selected");

		var btn = $(this).find('.option-btn-radio');

		if ($(this).hasClass("selected")) {
			voltage = btn.attr("data-value");
			btn.addClass("option-btn-radio-selected");
			//$('#summary-machine-voltage').text("Power: " + voltage + "V");
		} else {
			voltage = 0;
			//$('#summary-machine-voltage').text("");
			btn.removeClass("option-btn-radio-selected");
		}
	});

	/**
	 *	Shipping method button
	 */

	$("body").on("click", ".shipping-selector", function(){
		$('.shipping-selector[data-name="shipping"').find('.option-btn-radio.option-btn-radio-selected').removeClass("option-btn-radio-selected");
		var btn = $(this).find('.option-btn-radio');

		var delivery_price = btn.attr("data-price");

		if ($(this).hasClass("selected")) {
			shipping = btn.attr("data-value");
			$("#total-price").text(parseInt($("#total-price").attr("data-price")) + parseInt(delivery_price));
			btn.addClass("option-btn-radio-selected");
		} else {
			shipping = "";
			$("#total-price").text($("#total-price").attr('data-price'));
			btn.removeClass("option-btn-radio-selected");
		}
	});


	/**
	 *	Summary for item selected
	 */
	function itemSummaryHtml(name, img, price, id, qty) {
		var html = "<div class='summary-item-wrapper' id='summary-item-wrapper-"+ id +"'>";
		html += "<table class='clr'>";
		html += "<tr>";
		html += "<td>";
		html += "<img src='"+ img +"' width='80' height='80' alt='Item Preview' />";
		html += "</td>";
		html += "<td style='padding-left: 10px; vertical-align: top;'>";
		html += "<span class='font-14'><b>"+ name +"</b></span><br>";
		html += "<span class='font-12'>Qty: "+ qty +"</span><br>";
		html += "<span class='font-12' style='color: #808080;'>"+ price +"</span>";
		html += "</td>";
		html += "</tr>";
		html += "</table>";
		html += "</div>";

		return html;
	}

	/**
	 *	Item preview
	 */
	function itemChainHtml(name, img, price, id, qty) {
		var html = "<img src='"+ img +"' width='80' height='80' alt='"+ name +" Preview' id='item-chain-"+ id +"' />";

		return html;
	}

	/**
	 *	Select Item
	 */

	$("body").on("click", ".option-select", function(){
		var summary_id = parseInt(step) - 2 + 1;
		var btn = $(this).children().children(".option-btn-wrapper").children(".option-btn-radio");

		var radioType = (btn.length > 0) ? true : false;

		if (!radioType) {
			btn = $(this);
		} else {
			$('.option-btn-radio.option-btn-radio-selected').removeClass("option-btn-radio-selected");
		}
		
		if ($(this).hasClass("selected")) {
			var option_arraykey = btn.attr("data-option-key");
			var option_key = btn.attr("data-option-optionkey");
			var option_img = btn.attr("data-option-img");
			var option_name = btn.attr("data-option-name");
			var option_price = parseInt(btn.attr("data-option-price"));
			var option_currency = btn.attr("data-option-currency");
			var option_qty = btn.attr("data-option-qty");

			var total_price = parseInt($('#total-price').attr("data-price"));
			
			$('#total-price').html(total_price + option_price);

			var item = itemChainHtml(option_name, option_img, option_currency + "$ " + option_price, summary_id, option_qty);

			if ($("#item-chain-" + summary_id).length > 0) {
				$("#item-chain-" + summary_id).replaceWith(item);
			} else {
				$("#preview-img-wrapper").append(item);
			}

			options[summary_id] = option_arraykey;

			if (radioType) {
				btn.addClass("option-btn-radio-selected");
			}

		} else {

			if ($("#item-chain-" + summary_id).length > 0) {
				$("#item-chain-" + summary_id).remove();
			}

			options.splice(summary_id, 1);

			$("#total-price").html($("#total-price").attr("data-price"));

			if (radioType) {
				btn.removeClass("option-btn-radio-selected");
			}
		}

		console.log(options);
		console.log(step);
	});

	/**
	 *	Item Description
	 */
	$('body').on("mousemove", ".option-select, .machine-selector", function(event){
		var x = event.pageX;
		var y = event.pageY + 10;

		if ($(window).width() - x < 270) {
			x = x - 260;
		}

		$('.desc-overlay').css({
			"top" : y + "px",
			"left" : x + "px"
		});
		
	});

	$('body').on("mouseenter", ".option-select, .machine-selector", function(event){
		if ($(this).hasClass("option-container-border")) {
			var x = event.pageX;
			var y = event.pageY;

			var html = "<div class='desc-overlay' style='top: "+ y +"px; left: "+ x +"px;'>";
			html += $(this).children().children(".desc-wrapper").html();
			html += "</div>";

			$('body').append(html);
		}

	});

	$('body').on("mouseleave", ".option-select, .machine-selector", function(){
		$('.desc-overlay').remove();
	});

	/**
	 *	Option list link
	 */
	$('body').on("click", ".option-list-link", function(){
		var id = $(this).attr("href").substring(1, 2);
		var builder_id = $(this).attr("data-id");
		var url = $(this).attr("data-url");

		//preference = 0;
		voltage = 0;
		shipping = "";

		if (id != options.length && options.hasOwnProperty(id) == false) {
			alert("Please choose the item.");
		} else {
			$(this).removeClass("selected");
			$(".option-list-link[href='#"+ id +"']").addClass("selected");

			for (var i = id; i < options.length; i++) {
				if ($("#item-chain-" + i).length > 0) {
					$("#item-chain-" + i).remove();
				}
			}

			step = parseInt(id) + 2 - 1;
			// Options selection
			var optionKey = id;	// Option key

			options.splice(id);

			$.ajax({
				type: "GET",
				url : url + "ajax/request.html",
				dataType : "JSON",
				data : {machine_key : parseInt(machineKey) + 1, option_key : parseInt(optionKey) + 1, builder_id: builder_id},
				beforeSend : function() {
					requestLoading();
				},
				success : function(result){
					requestLoaded();

					$('#voltage-step').hide();
					$('#options-step').show();
					$('#options-step').html(result['html']);

					// Update step
					$('#current-step').text(step - 1 + 1);
					$('#total-step').text(result['total']);
					$('#step-display').show();

					$('#next-step-btn').show();
					$('#previous-step-btn').show();

					if (result['done'] == 1) {
						$('#step-display').hide();
					}

					var html = "<ul class='clr' id='options-list'>";
					for (var x in result['option_list']) {
						var selected = (x == options.length) ? "selected" : "";
						html += "<li class='grid-inline-top'><a href='#"+ x +"' class='option-list-link "+ selected +"' data-id='"+ result['builder_id'] +"' data-url='"+ url +"'><span>";
						html += result["option_list"][x];
						html += "</span></a></li>";
					}

					html += "</ul>";

					if (result['preference'] == 1) {
						preference = 1;
					} else {
						preference = 0;
					}

					$('#options-list').replaceWith(html);
				}
			});

			$('#total-price').attr("data-price", prices[step]);;
		}
	});

	/**
	 *	Next Step
	 */
	$("body").on("click", "#next-step-btn", function(){

		var id = $(this).attr("data-builder-id");
		var button = $(this);
		var url = $(this).attr("data-url");
		var website = $(this).attr("data-website");

		if (step == 0 && (machineKey == -1 || $('.machine-selector.selected').length == 0)) {
			alert("Please choose the Cabinet.");
		} else {
			if (step > 0 && preference == 0 && (options.hasOwnProperty(step - 2 + 1) == false || $('.option-select.selected').length == 0)) { // Check if item is selected
				alert("Please choose the item.");
			} else if (preference > 0 && (voltage == 0 || $('.voltage-selector.selected').length == 0)) {
				alert("Please choose voltage.");
			} else if (preference > 0 && $(".shipping-selector").length > 0 && (shipping = "" || $('.shipping-selector.selected').length == 0)) {
				alert("Please choose shipping method.");
			} else {
				// Options selection
				var optionKey = parseInt(step) - 1 + 1;	// Option key

				$.ajax({
					type: "GET",
					url : url + "ajax/request.html",
					dataType : "JSON",
					data : {machine_key : parseInt(machineKey) + 1, option_key : parseInt(optionKey) + 1, builder_id: id, summary: options, voltage: voltage, shipping: shipping, website: website},
					beforeSend : function(){
						button.text("LOADING");
						button.attr("disabled", "disabled");

						requestLoading();
					},
					success : function(result){
						requestLoaded();

						$('#machine-step').hide();
						$('#options-step').show();
						$('#options-step').html(result['html']);
						$('#previous-step-btn').show();

						// Update step
						$('#current-step').text(step - 1 + 1);
						$('#total-step').text(result['total']);
						$('#step-display').show();

						if (result['done'] == 1) {
							$('#step-display').hide();
						}

						$('#main-page-title').hide();

						var html = "<ul class='clr' id='options-list'>";
						for (var x in result['option_list']) {
							var selected = (x == step - 1) ? "selected" : "";
							html += "<li class='grid-inline-top'><a href='#"+ x +"' class='option-list-link "+ selected +"' data-id='"+ result['builder_id'] +"' data-url='"+ url +"'><span>";
							html += result["option_list"][x];
							html += "</span></a></li>";
						}

						html += "</ul>";

						$('#options-list').replaceWith(html);

						if (result['done'] == 1) {
							$('#preview-img-container').hide();
							$('#total-price-container').hide();
							$('#preview-sep').hide();
							$('#summary-footer').show();
							$('#next-step-btn').hide();
							$('#previous-step-btn').show();

							if ($('#summary-price-display').length > 0) {
								$('#summary-price-display').text($("#total-price").text());
							}

							if ($('#total-price-input').length > 0) {
								$('#total-price-input').val($('#total-price').text());
							}
						}

						if (result['preference'] == 1) {
							preference = 1;
						} else {
							preference = 0;
						}

						// Remove if description overlay is stuck
						if ($('.desc-overlay').length > 0) {
							$('.desc-overlay').remove();
						}

						button.text("NEXT");
						button.removeAttr("disabled");
					}
				});

				$('#total-price').attr("data-price", $('#total-price').text());

				prices[step] = $('#total-price').attr("data-price");

				step++;
			}
		}
	});

	/**
	 *	Previous Step
	 */
	$("body").on("click", "#previous-step-btn", function(){

		var id = $(this).attr('data-builder-id');
		var price = prices[step - 2 + 1];
		var button = $(this);
		var url = $(this).attr("data-url");

		$('.button-selector.selected').children().children(".option-btn-wrapper").children(".option-btn-radio").removeClass("option-btn-radio-selected");
		$('.button-selector.selected').find(".option-price").css("color", "#888888");
		$('.button-selector.selected').removeClass('selected');
		$('.button-checkbox.selected').removeClass('selected');

		$('#preview-img-container').show();
		$('#total-price-container').show();
		$('#preview-sep').show();
		$('#next-step-btn').show();
		$('#summary-footer').hide();

		switch(step) {
			case 1:
				// Going back to machine selection page from voltage selection
				$('#machine-step').show();
				$('#options-step').html("").hide();
				$('#main-page-title').children().text("Choose Cabinet");

				$('#footer-container').hide();
				
				machineKey = -1;	// Reset machine key

				step--;

				$('#previous-step-btn').hide();

				if (options.hasOwnProperty(parseInt(step) - 2 + 1)) {
					options.splice(parseInt(step) - 2 + 1, 1); // If the value is in options, delete it
				}

				$('#step-display').hide();

				$('#options-list').hide();
				break;

			default : 

				// Options selection
				var optionKey = parseInt(step) - 2 + 1;	// Option key

				if (preference == 0) {
					if (options.hasOwnProperty(optionKey)) { // Item value from the selection page before previous button is clicked
						options.splice(optionKey, 1);	// if the value is in options, delete it
					}

					if (options.hasOwnProperty(optionKey - 1)) {	// Item value from the current page
						options.splice(optionKey - 1, 1);	// if the value is in options, delete it
					}
				} else {
					if (options.hasOwnProperty(step - 2)) { // Item value from the selection page before previous button is clicked
						options.splice(step - 2, 1);	// if the value is in options, delete it
					}
				}

				$.ajax({
					type: "GET",
					url : url + "ajax/request.html",
					dataType : "JSON",
					data : {machine_key : parseInt(machineKey) + 1, option_key : parseInt(optionKey), builder_id: id},
					beforeSend : function(){
						button.text("LOADING");
						button.attr("disabled", "disabled");

						requestLoading();
					},
					success : function(result){
						requestLoaded();

						$('#options-step').html(result['html']);

						// Update step
						$('#current-step').text(step - 1 + 1);
						$('#total-step').text(result['total']);
						$('#step-display').show();

						var html = "<ul class='clr' id='options-list'>";
						for (var x in result['option_list']) {
							var selected = (x == step - 1) ? "selected" : "";
							html += "<li class='grid-inline-top'><a href='#"+ x +"' class='option-list-link "+ selected +"' data-id='"+ result['builder_id'] +"' data-url='"+ url +"'><span>";
							html += result["option_list"][x];
							html += "</span></a></li>";
						}

						html += "</ul>";

						$('#options-list').replaceWith(html);

						// Remove if description overlay is stuck
						if ($('.desc-overlay').length > 0) {
							$('.desc-overlay').remove();
						}

						if (result['preference'] == 1) {
							preference = 1;
						} else {
							preference = 0;
						}

						button.text("PREVIOUS");
						button.removeAttr("disabled");


					}
				});

				// }

				var summary_id = parseInt(step) - 2 + 1;

				if (preference == 0) {
					if ($('#item-chain-' + summary_id).length > 0) {	// Remove the preview of the item from the page before the previous button is click
						$('#item-chain-' + summary_id).remove();
					}

					if ($('#item-chain-' + (summary_id - 1)).length > 0) { // Remove the preview of the item from the current page
						$('#item-chain-' + (summary_id - 1)).remove();
					}
				} else {
					if ($('#item-chain-' + (step - 2)).length > 0) {
						$('#item-chain-' + (step - 2)).remove();
					}
				}

				// update the total price
				$('#total-price').text(price).attr("data-price", price);
				step--; 
		}

		preference = 0;
		voltage = 0;
		shipping = "";

	});

	var textPlaceholder = "";

	$('body').on("focus", "textarea.textarea-input", function(){
		if ($(this).hasClass("placeholder")) {
			$(this).removeClass("placeholder");
			textPlaceholder = $(this).val();
			$(this).val("");
		}

		$(this).parent().css("border-color", "#FFB500");
	});

	$('body').on("blur", "textarea.textarea-input", function(){
		if ($(this).val().length == 0) {
			$(this).addClass("placeholder");
			$(this).val(textPlaceholder);
		}

		$(this).parent().removeAttr("style");
	});
});

/**
 *	loading for page request
 */
function requestLoading() {
	$('#loading-overlay').css("display", "table");
	$('body').css("overflow", "hidden");
}

function requestLoaded() {
	$('#loading-overlay').hide();
	$('body').removeAttr("style");
}

/**
 *	Send Inquiry
 */
function sendInquiry(form) {
	var fname = form.fname;
	var lname = form.lname;
	var email = form.email;
	var country = form.country;
	var mail_list = (form.join_mailing_list.checked) ? "checked" : "unchecked";
	//var send_copy = (form.send_copy.checked) ? "checked" : "unchecked";
	var send_copy = "unchecked";
	var comments  = form.comments;
	var price = form.price.value;
	var currency = form.currency.value;
	var sub_id = form.sub_id.value;

	var machine_name = form.machine_name.value;
	var machine_id = form.machine_id.value;

	var items = new Array();

	$('.summary-items-input').each(function(){
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

		for(var x in errorMessages) {
			html += "<li>" + errorMessages[x] + "</li>";
		}

		html += "</ul>";

		$('#err').html(html).show();
	} else {
		var url = form.getAttribute("action");
		var type = form.getAttribute("method");

		var data = {
			fname : fname.value,
			lname : lname.value,
			email : email.value,
			country : country.value,
			mail_list : mail_list,
			send_copy : send_copy,
			comments : comments.value,
			machine_name : machine_name,
			machine_id : machine_id,
			items : items,
			currency : currency,
			price : price,
			sub_id : sub_id
		};

		$.ajax({
			url : url,
			type : type,
			data : data,
			beforeSend : function() {
				$('#send-btn').attr("disabled", "disabled").val("SENDING...");
			},
			success : function(result) {
				$("#inquiry_form").children(".sent-notification").show();
				$('#inquiry_form').children("form").remove();
				//$('#options-list, #preview-img-container, #total-price-container, #summary-footer, #button-wrapper').remove();
				$('#options-list, #footer-container').remove();

				console.log(result);
			}
		});
	}

	return false;
}

function ValidateEmail(mail) {  
	if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail)) {  
		return (true)  
  	}

	return (false)  
}