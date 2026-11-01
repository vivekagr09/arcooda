$(document).ready(function(){

	// Centralise survey title
	/*$("#logo").on("load", function(){
		$("#survey-title").css("margin-left", "-"+ $("#logo").width() +"px");
	}); */
	
	// Hide and seek trick
	$(".survey-question-container").hide();
	$(".survey-question-container").first().show();

	// Cover all first
	$("#loading-page").remove();
	$("#main-container").removeClass("d-none");

	// Form check highlight
	$(".form-check-input").on("click", function(){
		var wrapper = $(this).parent();
		var container = wrapper.parent();
		var type = $(this).attr("type");

		if (type == "checkbox") {
			if (wrapper.hasClass("form-checked")) {
				wrapper.removeClass("form-checked");
				
				if (wrapper.find(".choice-open-input").length > 0) {
					$(".choice-open-input").hide();
				}

			} else {
				wrapper.addClass("form-checked");
				if (wrapper.find(".choice-open-input").length > 0) {
					$(".choice-open-input").show().focus();
				}
			}
		} else {
			if (!wrapper.hasClass("form-checked")) {
				if (container.find(".form-checked").length > 0) {
					container.find(".form-checked").each(function(){
						$(this).removeClass("form-checked");
					});

					container.find(".choice-open-input").each(function(){
						$(this).hide();	
					});
				}

				wrapper.addClass("form-checked");

				if (wrapper.find(".choice-open-input").length > 0) {
					$(".choice-open-input").show().focus();
				}
			}
		}
	});

	$(window).on("resize", function(){
		if ($(".choice-open-input").length > 0) {
			$(".choice-open-input").each(function(){
				var label = $(this).parent();
				var labelWidth = label.width();
				
				var span = $(this).prev();
				var spanWidth = span.width();
	
				var inputWidth = labelWidth - spanWidth - 5;
				$(this).css("width", inputWidth + "px");

			});
		}
	});

});

function prevOf(q) {
	var container = $("#survey-question-container-" + q);
	container.hide();
	container.prev().show();
}

function nextOf(q, open) {
	var error = false;
	var container = $("#survey-question-container-" + q);

	if (open) {
		container.find("textarea.required").each(function() {
			if ($(this).val().length == 0) {
				container.find(".alert").text("Please answer the question to proceed to next question.");
				error = true;
			} else if ($(this).val().length > 2000) {
				container.find(".alert").text("The answer is too long.");
			}
		});
	} else {
		if (container.find("input.required").length > 0) {
			var checkedInputs = container.find("input.required:checked");
			if (checkedInputs.length == 0) {
				container.find(".alert").text("Please answer the question to proceed to next question.");
				error = true;
			} else if (checkedInputs.next(".form-check-label").children(".choice-open-input").val() > 2000) {
				container.find(".alert").text("The answer is too long.");
				error = true;
			}
		}
	}

	if (error == true) {
		if (container.find(".alert").hasClass("d-none")) {
			container.find(".alert").removeClass('d-none');
		}
	} else {
		if (!container.find(".alert").hasClass("d-none")) {
			container.find(".alert").addClass("d-none");
		}

		container.hide();
		container.next().show();

		if (container.next().find(".choice-open-input").length > 0) {
			container.next().find(".choice-open-input").each(function(){
				var label = $(this).parent();
				var labelWidth = label.width();
				
				var span = $(this).prev();
				var spanWidth = span.width();
	
				var inputWidth = labelWidth - spanWidth - 5;
				$(this).css("width", inputWidth + "px");
			});
		}
	}
}