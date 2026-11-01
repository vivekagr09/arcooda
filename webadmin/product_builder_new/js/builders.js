/**
 *	Module: Product Builder
 *
 *	builders.js
 *
 *	Builder UI and functions
 *
 *	@author Kelvin (khtat@highwaygroup.net)
 *	@since  29/12/2017
 */

var machinesUID = 0;	// Machine DOM Unique ID
var optionClickedMachineID = 0;	// The machine DOM unique ID that has been clicked
var optionUID = 0;	// Option DOM Unique ID

var subdomain = [
	"https://www.oceankingarcade.com/"
];

// Get the categories from database via ajax
var categories = new Array();
$.ajax({
	url : "ajax/get_machine_categories.php",
	dataType : "json",
	success : function(r){
		categories = r;
	}
});

/**
 *	Add the website link when the link is selected
 */
function addWebsite(option) {
	var website = option.value;

	var name = document.getElementById("builder-name-input").value;
	if (name.length > 0) {
		name = name.toLowerCase();
		name = name.replace(/\s+/g, "-");

		if (subdomain.indexOf(website) != -1) {
			website = website.replace("https://www", "http://builder") + name;
		} else {
			website += "product-builder/" + name;
		}
	}

	if (website.length > 0) {
		var row = document.createElement("tr");

		var websiteTd = document.createElement("td");
		var websiteVal = document.createElement("input");
		websiteVal.setAttribute("type", "text");
		websiteVal.setAttribute("name", "pb_builder[websites][]");
		websiteVal.setAttribute("class", "text-input");
		websiteVal.value = website;
		websiteTd.appendChild(websiteVal);

		var removeBtnTd = document.createElement("td");
		removeBtn = document.createElement("button");
		removeBtn.setAttribute("type", "button");
		removeBtn.setAttribute("class", "pb-website-remove-btn");
		removeBtn.addEventListener("click", function(){
			removeWebsite(this);
		});
		removeBtn.innerHTML = "Remove";
		removeBtnTd.appendChild(removeBtn);

		row.appendChild(websiteTd);
		row.appendChild(removeBtnTd);

		document.getElementById("websites-display").appendChild(row);
	}
}

/**
 *	Remove the website link
 */
function removeWebsite(btn) {
	var row = btn.parentNode.parentNode;
	row.parentNode.removeChild(row);
}

/**
 *	Remove the option
 */
function removeOption(link) {
	var row = link.parentNode.parentNode;
	row.parentNode.removeChild(row);
}

/**
 *	Reposition the option
 */
function optionReposition(machineID) {
	// Get the number of options listing
	var posHolder = $('#builder-options-table-'+ machineID +' span.position-number');

	// Re arrange it in Ascending order from 1
	for(var i = 0; i < posHolder.length; i++) {
		posHolder[i].nextSibling.value = i + 1;
		posHolder[i].innerText = i + 1;
	}
}
function  categoryReposition(){
	// Get the number of options listing
	var posHolder = $('#categoy-shortable-table span.position-number-cat');

	// Re arrange it in Ascending order from 1
	for(var i = 0; i < posHolder.length; i++) {
		posHolder[i].nextSibling.value = i + 1;
	}
}
/**
 *	Set option data when options are selected from option selection page
 */
function setOptionData(data) {
	// Get the number of options listing
	var posHolder = $('#builder-options-table-'+ optionClickedMachineID +' span.position-number');

	// Calculate the DOM unique id for option
	if (posHolder.length > 0 && optionUID == 0) {
		optionUID = parseInt(posHolder[posHolder.length - 1].innerText);
	}

	// Check if the selected option already exists
	for(x in data) {
		var exists = false;

		if (posHolder.length > 0) {
			for(var i = 0; i < posHolder.length; i++) {
				if (posHolder[i].nextSibling.nextSibling.value == data[x][0]) {
					exists = true;
					break;
				}
			}
		}

		// Add the option if not exists
		if (exists == false) {

			var html = "<tr>";
			html += "<td>";
			html += "<span class='position-number'>"+ optionUID +"</span>";
			html += "<input type='hidden' name='pb_builder[machines]["+ optionClickedMachineID +"][options]["+ (optionUID) +"][position]' value='"+ optionUID +"'/>";
			html += "<input type='hidden' name='pb_builder[machines]["+ optionClickedMachineID +"][options]["+ (optionUID) +"][id]' value='"+ data[x][0] +"'/>";
			html += "<input type='hidden' name='pb_builder[machines]["+ optionClickedMachineID +"][options]["+ (optionUID) +"][name]' value='"+ data[x][1] +"'/>";
			html += "</td>";
			html += "<td><div class='drag-icon'></div></td>";
			html += "<td>"+ data[x][1] +"</td>";
			html += "<td><input type='number' name='pb_builder[machines]["+ optionClickedMachineID +"][options]["+ (optionUID) +"][qty]' class='text-inpue' style='width:50px;'></td>";
			html += "<td><a class='builde-options-remove-btn sort-link' href='#' onclick='removeOption(this); return false;'>Remove</a></td>";
			html += "</tr>";

			$('#builder-options-table-' + optionClickedMachineID).append(html);
			$('#builder-options-table-'+ optionClickedMachineID +' .no-option-text').remove();

			// Reposition the option
			optionReposition(optionClickedMachineID);

			// Get new option DOM Unique ID
			optionUID++;
		}
	}
}

/**
 *	Set the product data from product selection page
 */
function dispalyselecteddata(dataarray){
    dataarray.item_list.forEach(setProductData);
} 
function setProductData(data) {
	// For Debugging
	var selected_category=new  Array();
	var inputElements = document.getElementsByClassName('cat-checkbox');
	for(var i=0; inputElements[i]; ++i){
	      if(inputElements[i].checked){
	           selected_category.push(inputElements[i].value);
	      }
	}
	// Get the machine table
	var machinesTable = $('.machines-table');

	// Calculate the DOM Unique ID for machine table
	if (machinesTable.length > 0 && machinesUID == 0) {
		machinesUID = machinesTable.length;
	}

	var html = "<table class='form-table machines-table' id='machines-table-"+ machinesUID +"' style='margin-bottom: 50px;' data-machinesUID='"+ machinesUID +"'>";
	
	// Machine Name
	html += "<tr>";
	html += "<td class='left-title'>Machine Name</td>";
	html += "<td>";
	html += "<table style='width: 100%;'>";
	html += "<tr>";
	html += "<td>";
	html += "<input type='text' name='pb_builder[machines]["+ machinesUID +"][name]' class='text-input' value='"+ data['product_name'] +"' />";
	html += "<input type='hidden' name='pb_builder[machines]["+ machinesUID+"][product_id]' value='"+ data['product_id'] +"' />";
	html += "<input type='hidden' name='pb_builder[machines]["+ machinesUID +"][site_id]' value='"+ data['site_id'] +"' />";
	html += "<input type='hidden' name='pb_builder[machines]["+ machinesUID +"][sub_id]' value='"+ data['sub_id'] +"'/>";
	html += "</td>";
	html += "<td><button type='button' onclick='return deleteMachine("+ machinesUID +");'>Delete</button></td>";
	html += "</tr>";
	html += "</table>";
	html += "</td>";
	html += "</tr>";

	html += "<tr>";
	html += "<td class='left-title'>Machine Id</td>";
	html += "<td><a href='/webadmin/products_new/edit.php?action=edit&id="+ data['product_id'] +"' target='_blank'>"+ data['product_id'] +"</a></td>";
	html += "</tr>";
	// machine category
	html += "<tr>";
	html += "<td class='left-title'>Category</td>";
	html += "<td>";
	html += "<select name='pb_builder[machines]["+ machinesUID +"][category]'>";
	html += "<option value=''>-- Select Category --</option>";
	for (var x in categories) {
		if(selected_category.includes(categories[x]['id'])){
		html += "<option value='"+ categories[x]['id'] +"'>"+ categories[x]['name'] +"</option>";
		}
	}
	html += "</select>"
	html += "</td>";
	html += "</tr>";

	// machine position
	html += "<tr>";
	html += "<td class='left-title'>Position to be displayed</td>";
	html += "<td><input type='number' name='pb_builder[machines]["+ machinesUID +"][position]' class='text-input' value='0' style='width: 70px; margin-left: 2px;'/></td>";
	html += "</tr>";

	// Images
	html += "<tr>";
	html += "<td class='left-title'>Image</td>";
	html += "<td>";
	html += "<img src='"+ data['thumb_url'] +"' width='96' height='96' style='margin-left: 2px' id='image_machine_"+machinesUID+"'/>";
	html += "<div><input type='file' machine_id='"+machinesUID+"' class='machine_image_file' name='machine_image["+ machinesUID +"]' style='psdding:5px;max-height:96px;' accept='image/*'' /></div>";
	html += "<td><input type='hidden' name='pb_builder[machines]["+ machinesUID +"][image]' value='"+ data['thumb_url'] +"'/></td>";
	html += "</td>";
	html += "</tr>";

	// Price
	var priceUSD = 0;
	var priceAUD = 0;
	var priceValue = 0;
	var selectUSD = "";
	var selectAUD = "";

	if (data['primary_currency'] == 'USD') {
		if (data.hasOwnProperty('product_offerus')) {
			priceUSD = data['product_offerus'];
		} else {
			priceUSD = data['primary_price'];
		}

		if (data.hasOwnProperty('product_offerau')) {
			priceAUD = data['product_offerau'];
		} else {
			priceAUD = 0;
		}

		priceValue = priceUSD;
		selectUSD = "selected";
	} else {
		if (data.hasOwnProperty('product_offerau')) {
			priceAUD = data['product_offerau'];
		} else {
			priceAUD = data['primary_price'];
		}

		if (data.hasOwnProperty('product_offerus')) {
			priceUSD = data['product_offerus'];
		} else {
			priceUSD = 0;
		}

		priceValue = priceAUD;
		selectAUD = "selected";
	}

	html += "<tr>";
	html += "<td class='left-title'>Price</td>";
	html += "<td>";
	html += "<input type='text' name='pb_builder[machines]["+ machinesUID +"][price]' class='text-input' value='"+ priceValue +"' style='width:100px; margin-left: 2px;' data-usd='"+ priceUSD +"' data-aud='"+ priceAUD +"'/>";
	html += "<select name='pb_builder[machines]["+ machinesUID +"][currency]' style='height: 21px; margin-left: 2px;' onchange='machinesChangeCurrency(this);'>";
	html += "<option value='USD' "+ selectUSD +">USD</option>";
	html += "<option value='AUD' "+ selectAUD +">AUD</option>";
	html += "</select>";
	html += "</td>";
	html += "</tr>";

	html += "<tr>";
	html += "<td class='left-title'>Price</td>";
	html += "<td>";
	html += "<table class='clr'>";
	html += "<tr>";
	html += "<td>";
	html += "<input type='checkbox' name='pb_builder[machines]["+ machinesUID +"][delivery_pickup]'/>&nbsp;<span>Pickup</span>";
	html += "</td>";
	html += "<td>";
	html += "<input type='number' name='pb_builder[machines]["+ machinesUID +"][delivery_pickup_price]' class='text-input' style='width:100px; margin-left: 2px;' placeholder='Price' />";
	html += "</td>";
	html += "</tr>";

	html += "<tr>";
	html += "<td>";
	html += "<input type='checkbox' name='pb_builder[machines]["+ machinesUID +"][delivery_sea]'/>&nbsp;<span>Sea Freight</span>";
	html += "</td>";
	html += "<td>";
	html += "<input type='number' name='pb_builder[machines]["+ machinesUID +"][delivery_sea_price]' class='text-input' style='width:100px; margin-left: 2px;' placeholder='Price' />";
	html += "</td>";
	html += "</tr>";

	html += "<tr>";
	html += "<td>";
	html += "<input type='checkbox' name='pb_builder[machines]["+ machinesUID +"][delivery_air]'/>&nbsp;<span>Air Freight</span>";
	html += "</td>";
	html += "<td>";
	html += "<input type='number' name='pb_builder[machines]["+ machinesUID +"][delivery_air_price]' class='text-input' style='width:100px; margin-left: 2px;' placeholder='Price' />";
	html += "</td>";
	html += "</tr>";
	html += "</table>";
	html += "</td>";
	html += "</tr>";

	// Description
	html += "<tr>";
	html += "<td class='left-title'>Description</td>";
	html += "<td>";
	html += "<textarea name='pb_builder[machines]["+ machinesUID +"][description]' class='textarea-input description' style='resize: vertical; width: 100%; height: 150px; font-family: Arial;'>";
	html += data['product_details'];
	html += "</textarea>";
	html += "</td>";
	html += "</tr>";

	// Description
	html += "<tr>";
	html += "<td class='left-title'>Special Notes</td>";
	html += "<td>";
	html += "<textarea name='pb_builder[machines]["+ machinesUID +"][special_notes]' class='textarea-input' style='resize: vertical; width: 100%; height: 150px; font-family: Arial;'>";
	html += "</textarea>";
	html += "</td>";
	html += "</tr>";

	// Options
	html += "<tr>";
	html += "<td class='left-title'>Options</td>";
	html += "<td><a href='#' style='margin: 0 5px; color: #000;' onclick='return chooseOptions("+ machinesUID +");'><i>Add Options</i></a></td>";
	html += "</tr>";

	html += "<tr>";
	html += "<td colspan='2'>";
	html += "<table class='view-table builder-options-table' id='builder-options-table-"+ machinesUID +"' style='width: 100%'>";
	html += "<thead>";
	html += "<tr>";
	html += "<th style='width: 100px;'>Position</th>";
	html += "<th style='width: 20px;'></th>";
	html += "<th>Option Title</th>";
	html += "<th>Qty</th>";
	html += "<th style='width: 100px;'></th>";
	html += "</tr>";
	html += "</thead>";

	html += "<tr class='no-option-text'>";
	html += "<td colspan='5'><i>No Options Added</i></td>";
	html += "</tr>";
	html += "</table>";
	html += "</td>";
	html += "</tr>";

	html += "</table>";

	// Load the selected machine
	$('#machines-loader').prepend(html);

	// Get current machine DOM Unique ID
	var _machinesUID = machinesUID;

	// Make the option table in that machine sortable
	$('#builder-options-table-' + machinesUID).sortable({
		containerSelector: "table",
		itemPath: "> tbody",
		itemSelector: "tr",
		placeholder: "<tr class='placeholder'/>",
		handle: ".drag-icon",

		// animation on drop
		onDrop: function  ($item, container, _super) {
		    var $clonedItem = $('<li/>').css({height: 0});
		    $item.before($clonedItem);
		    $clonedItem.animate({'height': $item.height()});

		    $item.animate($clonedItem.position(), function  () {
		      	$clonedItem.detach();
		      	_super($item, container);
		    });

		    // Change the position input
		    optionReposition(_machinesUID);
		},

		// set $item relative to cursor position
		onDragStart: function ($item, container, _super) {
		    var offset = $item.offset(),
		        pointer = container.rootGroup.pointer;

		    adjustment = {
		    	left: pointer.left - offset.left,
		      	top: pointer.top - offset.top
		    };

		    _super($item, container);
		},

		onDrag: function ($item, position) {
		    $item.css({
		    	left: position.left - adjustment.left,
		      	top: position.top - adjustment.top
			});
		}
	});
	machinesUID++;
	$(".machine_image_file").change(function() {
        var file = this.files[0];
        var machineid =$(this).attr('machine_id');
        var reader = new FileReader();
        reader.onload = function (e) {
            $("#image_machine_" + machineid).attr("src", e.target.result);
            //$(ProductScreenshot.selector("image", row)).toggle(false);
        };
        reader.readAsDataURL(file);
    });
}

/**
 *	Change the currency
 */
function machinesChangeCurrency(cur) {
	var priceInput = cur.previousSibling;
	if (cur.value == "USD") {
		priceInput.value = priceInput.getAttribute("data-usd");
	} else {
		priceInput.value = priceInput.getAttribute("data-aud");
	}
}

/**
 *	Open the option selection page
 */
function chooseOptions(machineID) {
	optionClickedMachineID = machineID;
	window.open('/webadmin/product_builder_new/options_select.php?return_message=add');
	return false;
}

/**
 *	Delete the selected machine
 */
function deleteMachine(machineID) {
	$('#machines-table-' + machineID).remove();
}

$(document).ready(function(){

	var textPlaceholder = "";

	$('body').on("focus", "textarea.description", function(){
		if ($(this).hasClass("placeholder")) {
			$(this).removeClass("placeholder");
			textPlaceholder = $(this).val();
			$(this).val("");
		}
	});

	$('body').on("blur", "textarea.description", function(){
		if ($(this).val().length == 0) {
			$(this).addClass("placeholder");
			$(this).val(textPlaceholder);
		}
	});

	// Sortable builder option table in machine table
	$('.builder-options-table').sortable({
		containerSelector: "table",
		itemPath: "> tbody",
		itemSelector: "tr",
		placeholder: "<tr class='placeholder'/>",
		handle: ".drag-icon",

		// animation on drop
		onDrop: function  ($item, container, _super) {
		    var $clonedItem = $('<li/>').css({height: 0});
		    $item.before($clonedItem);
		    $clonedItem.animate({'height': $item.height()});

		    $item.animate($clonedItem.position(), function  () {
		      	$clonedItem.detach();
		      	_super($item, container);
		    });

		    // Change the position input
		    var _machinesUID = container.el.attr("data-machinesUID");
		    optionReposition(_machinesUID);
		},

		// set $item relative to cursor position
		onDragStart: function ($item, container, _super) {
		    var offset = $item.offset(),
		        pointer = container.rootGroup.pointer;

		    adjustment = {
		    	left: pointer.left - offset.left,
		      	top: pointer.top - offset.top
		    };

		    _super($item, container);
		},

		onDrag: function ($item, position) {
		    $item.css({
		    	left: position.left - adjustment.left,
		      	top: position.top - adjustment.top
			});
		}
	});
	$('.builder-options-table-cat').sortable({
		containerSelector: "table",
		itemPath: "> tbody",
		itemSelector: "tr",
		placeholder: "<tr class='placeholder'/>",
		handle: ".drag-icon",

		// animation on drop
		onDrop: function  ($item, container, _super) {
		    var $clonedItem = $('<li/>').css({height: 0});
		    $item.before($clonedItem);
		    $clonedItem.animate({'height': $item.height()});

		    $item.animate($clonedItem.position(), function  () {
		      	$clonedItem.detach();
		      	_super($item, container);
		    });

		    categoryReposition();
		},

		// set $item relative to cursor position
		onDragStart: function ($item, container, _super) {
		    var offset = $item.offset(),
		        pointer = container.rootGroup.pointer;

		    adjustment = {
		    	left: pointer.left - offset.left,
		      	top: pointer.top - offset.top
		    };

		    _super($item, container);
		},

		onDrag: function ($item, position) {
		    $item.css({
		    	left: position.left - adjustment.left,
		      	top: position.top - adjustment.top
			});
		}
	});
	$(".machine_image_file").change(function() {
        var file = this.files[0];
        var machineid =$(this).attr('machine_id');
        var reader = new FileReader();
        reader.onload = function (e) {
            $("#image_machine_" + machineid).attr("src", e.target.result);
            //$(ProductScreenshot.selector("image", row)).toggle(false);
        };
        reader.readAsDataURL(file);
    });
	// make sure to uncheck all other categories of builder when the category All is checked
	/*$('body').on('click', '.cat-checkbox', function(){
		if ($(this).is(":checked")) {
			var v = $(this).val();

			if (v == 1) {
				$('.cat-checkbox:checked').not(this).attr("checked", false);
			} else {
				if ($('.cat-checkbox[value="1"]').length > 0) {
					$('.cat-checkbox[value="1"]').attr("checked", false);
				}
			}
		}
	});*/
});