/**
 *	Module: Product Builder
 *	===============================================================
 *
 *	Option Editor UI
 *	------------------
 *
 *	This class create input fields for option editor
 *
 *	Option Editor has four type of inputs:
 *	1) Link Product : Get product from database. The
 *					  option key is 0.
 *	2) Link Text    : Show the option as text. The option
 *					  key is 1.
 *	3) Link Photo   : Upload custom photo and display it.
 *					  The option key is 2.
 *	4) Link Youtube : Have the youtube video in the option.
 *					  The option key is 3.
 *
 *	Option Editor has two type of template:
 *	1) Standard  : This template display the options horizontally
 *				   and it is horizontally scrollable. It has the
 *				   first three option links.
 *	2) Videofeat : This template display the options featured
 *				   videos. It has Link Youtube as an option.
 *	================================================================
 *	
 *
 *	@author  Kelvin (khtat@highwaygroup.net)
 *	@since   25/01/2018
 *	@version 1.0.0
 */

/**
 *	This is OptionEditor class. This will construct and initiate the object.
 *
 *	@param String element This is the ID of an element for editor.
 */
function OptionEditor(element) {
	this.element  = document.getElementById(element); 	// Option Editor Element
	this.ai       = 0;									// Auto increasement counter: Increase only
	this.count 	  = 0; 	 								// Non Auto increasement counter: Increase and decrease
	this.template = "standard";							// Template of options
	this.main 	  = "";									// Main content element
}

// Global static variables
OptionEditor.searchClickedId = 0; // ID of option where search button is clicked

// Global methods

/**
 * 	Method: reposition <static>
 *
 *	This method is to reposition all inputs.
 *	
 *	Precondition  : All input fields must have position input with the class name "options-pos-input".
 *	Postcondition : It will get all "options-pos-input" and re set the value from 1 to number of inputs.
 *
 *	@return void
 */
OptionEditor.reposition = function() {
	// Get position inputs
	var positionInput = document.getElementsByClassName("options-pos-input");

	for (var i = 0; i < positionInput.length; i++) {
		positionInput.item(i).value = i + 1;
	}
};

/**
 *	Method: init
 *
 *	This method is to initialise the option editor.
 *	This method will create functioning DOM Object and display it.
 *
 *	If there is no existing values, it will create new input fields with the default value according to
 *	template selected. Otherwise, it will load the existing values first then create new input field at
 *	the end.
 *	============================================================================================================
 *
 *	To load the initial or old values from HTML, the following format must be followed:
 *	
 * 	All inputs must be wrapped in the container with class name "option-init" with data attribute
 *	"data-option-key" which is to determine what option to load.
 *	For example: <div class="option-init" data-option-key"{OPTION_KEY}"> ... </div>
 *
 *	All inputs are in hidden input tag. The algorithm will read all those input values by accessing the wrapper
 *	and calling the relevent class name of the inputs. Therefore, all inputs have their own class name.
 *	For example: <input type="hidden" class="{CLASS_NAME}" value="{VALUE}" />
 *
 *	For textarea input, the value will be in textarea tag.
 *	For Example: <textarea class="{CLASS_NAME}">{VALUE}</textarea>
 *
 *	For link product
 *	-----------------
 *	There are three values to be read for this option:
 *	1) Product ID : This has the class name "product-id-input"
 *	2) Site ID    : This has the class name "site-id-input"
 *	3) Sub ID     : This has the class name "sub-id-input"
 *	
 *	For link text
 *	--------------
 *	There are two values to be read for this option
 *	1) Header : This has the class name "header-input"
 *	2) Text   : This has the class name "text-input"
 *
 *	For link photo
 *	---------------
 *	There are three values to be read for this option
 *	1) Header 	    : This has the class name "header-input"
 *	2) Image Source : This has the class name "src-input"
 *	3) Description  : This has the class name "description-input"
 *
 *	For link youtube
 *  -----------------
 *	There are four values to be read for this option
 *	1) Header 		: This has the class name "header-input"
 *	2) Youtube ID   : This has the class name "youtube-input"
 *	3) Description  : This has the class name "description-input"
 *	4) Info Link 	: This has the class name "info-link-input"
 *
 *	For Price and currency
 *	-----------------------
 *	There are four values to be read
 *	1) Price 	 : This has the class name "price-input"
 *	2) Currency  : This has the class name "currency-input"
 *	3) USD Price : This has the class name "usd-input"
 *	4) AUD Price : This has the class name "aud-input"
 *	
 *	============================================================================================================
 *	
 *	@param String template The template that editor is going to use
 *	
 *	@return void
 */
OptionEditor.prototype.init = function(template) {
	// Get self object
	var self = this;

	// Set template
	self.template = template;

	// Create new row for table title
	var tableTitle = document.createElement("tr");
	tableTitle.innerHTML = "<td></td><td></td><td></td><td><b>Price</b></td>";	// There are total four columns

	// Create thead and append table title row
	var thead = document.createElement("thead");
	thead.appendChild(tableTitle);
	
	// Create Tbody
	var tbody = document.createElement("tbody");

	// Get the containers of old input values
	var initItemInput = self.element.getElementsByClassName("option-init");

	// Load old values if exists
	while(initItemInput.length > 0) {
		// Get the option key of item input
		var optionKey = parseInt(initItemInput.item(0).getAttribute("data-option-key"));

		// Create new object to store item input data
		var initData = new Object();

		// Get input data for input field.
		// Object key varies according to option type
		switch(optionKey) {
			case 0 :
				initData.productId = initItemInput.item(0).getElementsByClassName("product-id-input")[0].value;
				initData.siteId    = initItemInput.item(0).getElementsByClassName("site-id-input")[0].value;
				initData.subId     = initItemInput.item(0).getElementsByClassName("sub-id-input")[0].value;
				break;

			case 1 :
				initData.header = initItemInput.item(0).getElementsByClassName("header-input")[0].value;
				initData.text   = initItemInput.item(0).getElementsByClassName("text-input")[0].value;
				break;

			case 2 :
				initData.header 	 = initItemInput.item(0).getElementsByClassName("header-input")[0].value;
				initData.src    	 = initItemInput.item(0).getElementsByClassName("src-input")[0].value;
				initData.description = initItemInput.item(0).getElementsByClassName("description-input")[0].value;
				break;

			case 3 :
				initData.header 	 = initItemInput.item(0).getElementsByClassName("header-input")[0].value;
				initData.youtube 	 = initItemInput.item(0).getElementsByClassName("youtube-input")[0].value;
				initData.description = initItemInput.item(0).getElementsByClassName("description-input")[0].value;
				initData.infoLink    = initItemInput.item(0).getElementsByClassName("info-link-input")[0].value;
				break;
		}

		// Get price
		initData.price 	  = initItemInput.item(0).getElementsByClassName("price-input")[0].value;
		initData.currency = initItemInput.item(0).getElementsByClassName("currency-input")[0].value;
		initData.usd      = initItemInput.item(0).getElementsByClassName("usd-input")[0].value;
		initData.aud 	  = initItemInput.item(0).getElementsByClassName("aud-input")[0].value;

		// Load all existing item input
		tbody.appendChild(self.loadItemInput(optionKey, initData));

		// Remove placeholders
		self.element.removeChild(initItemInput.item(0));
	}

	// Create new item input
	tbody.appendChild(self.getNewItemInput());
	self.main = tbody;

	// Create empty table
	var table = document.createElement("table");
	table.setAttribute("class", "form-table option-list-table");
	table.setAttribute("id", "option-table");
	table.appendChild(thead);
	table.appendChild(tbody);

	// Load editor
	if (self.element.hasChildNodes()) {
		self.element.replaceChild(table, self.element.childNodes[0]);
	} else {
		self.element.appendChild(table);
	}

	// Reposition inputs
	OptionEditor.reposition();
}

/**
 *	Method: getNewItemInput
 *
 *	This method is to create new input field.
 *
 *	The default for standard template is product input and 
 * 	for videofeat template is youtube input.
 *
 *	@return DOM Object This method return table row to be appended in the table.
 */
OptionEditor.prototype.getNewItemInput = function () {
	// Get self object
	var self = this;

	// Unique ID
	var uid = self.ai;

	// Create table row
	var row = document.createElement("tr");
	row.setAttribute("id", "option-list-row-" + uid);

	// Create first column for button
	var firstCol = document.createElement("td");

	// Create second column for link selection
	var secondCol = document.createElement("td");

	// Create thrid column for input fields
	var thirdCol = document.createElement("td");

	// Create fourth column for price input
	var fourthCol = document.createElement("td");

	// Create add button
	var addBtn = document.createElement("button");
	addBtn.setAttribute("type", "button");
	addBtn.innerHTML = "Add New";

	// Create remove button
	var removeBtn = document.createElement("button");
	removeBtn.setAttribute("type", "button");
	removeBtn.innerHTML = "Remove";

	// Add action for remove
	removeBtn.addEventListener("click", function() {
		row.parentNode.removeChild(row);
	});

	// Add action for add
	addBtn.addEventListener("click", function() {
		// Add class name for added input field
		row.setAttribute("class", "option-list-row");

		// Add new row
		self.main.appendChild(self.getNewItemInput());

		// Change add button to remove button
		this.parentNode.appendChild(removeBtn);
		this.parentNode.removeChild(this);

		// Reposition inputs
		OptionEditor.reposition();
	});

	// Get link option selection
	var linkOptionSelection = self.getLinkOptionSelction();

	// Get input field
	// Default for standard template: link product
	// Default for videofeat template: link youtube
	var inputField = "";

	// Get price field
	var priceField = "";

	// Generate input field and price field according to template
	switch(self.template) {
		case "videofeat":
			inputField = self.getYoutubeInput();
			priceField = self.getYoutubePriceInput();
			break;

		default :
			inputField = self.getProductInput();
			priceField = self.getProductPriceInput();
	}

	// Grid all components
	firstCol.appendChild(addBtn);
	secondCol.appendChild(linkOptionSelection);
	thirdCol.appendChild(inputField);
	fourthCol.appendChild(priceField);

	row.appendChild(firstCol);
	row.appendChild(secondCol);
	row.appendChild(thirdCol);
	row.appendChild(fourthCol);

	// Increase ai
	self.ai++;

	return row;
};

/**
 *  Method: loadItemInput
 *
 *	This method is to load existing or old input values.
 *	
 *	@param int 	  optionKey The option key to determin which option type
 *	@param Object initData	The object with all values for option key type options
 *
 *	@return DOM Object This method return the table row to be appended in table.
 */
OptionEditor.prototype.loadItemInput = function (optionKey, initData) {
	// Get self object
	var self = this;

	// Unique ID
	var uid = self.ai;

	// Parse option key to integer
	optionKey = parseInt(optionKey);

	// Create table row
	var row = document.createElement("tr");
	row.setAttribute("id", "option-list-row-" + uid);
	row.setAttribute("class", "option-list-row");

	// Create first column for button
	var firstCol = document.createElement("td");

	// Create second column for link selection
	var secondCol = document.createElement("td");

	// Create thrid column for input fields
	var thirdCol = document.createElement("td");

	// Create fourth column for price input
	var fourthCol = document.createElement("td");

	// Create remove button
	var removeBtn = document.createElement("button");
	removeBtn.setAttribute("type", "button");
	removeBtn.innerHTML = "Remove";

	// Add action for remove
	removeBtn.addEventListener("click", function() {
		row.parentNode.removeChild(row);
	});

	// Get link option selection
	var linkOptionSelection = self.getLinkOptionSelction();

	// Get input field
	// Default for standard template: link product
	// Default for videofeat template: link youtube
	var inputField = "";

	// Get price field
	var priceField = "";

	// Generate input field and price field according to template
	switch(optionKey) {
		case 0 :
			inputField = self.getProductInput(initData.productId, initData.siteId, initData.subId);
			priceField = self.getProductPriceInput(initData.price, initData.usd, initData.aud, initData.currency);
			break;

		case 1 :
			inputField = self.getTextInput(initData.header, initData.text);
			priceField = self.getTextPriceInput(initData.price, initData.usd, initData.aud, initData.currency);
			break;

		case 2 :
			inputField = self.getPhotoInput(initData.header, initData.src, initData.description);
			priceField = self.getPhotoPriceInput(initData.price, initData.usd, initData.aud, initData.currency);
			break;

		case 3 :
			inputField = self.getYoutubeInput(initData.header, initData.youtube, initData.description, initData.infoLink);
			priceField = self.getYoutubePriceInput(initData.price, initData.usd, initData.aud, initData.currency);
			break;
	}

	// Grid all components
	firstCol.appendChild(removeBtn);
	secondCol.appendChild(linkOptionSelection);
	thirdCol.appendChild(inputField);
	fourthCol.appendChild(priceField);

	row.appendChild(firstCol);
	row.appendChild(secondCol);
	row.appendChild(thirdCol);
	row.appendChild(fourthCol);

	// Increase ai
	self.ai++;

	return row;
};

/**
 * 	Method: getLinkOptionSelection
 *
 *	This method is to get all link option selection depending on selected templates
 *
 *	For videofeat template:
 *	1) Link Youtube
 *
 *	For standard template:
 *	1) Link Product
 *	2) Link Text
 *	3) Link Photo
 *
 *	@return DOM Object This method return option selector.
 */
OptionEditor.prototype.getLinkOptionSelction = function () {
	// Get self object
	var self = this;

	// Visible links
	var links = new Array();

	switch(self.template) {
		case "videofeat":
			links[3] = "Link Youtube";
			break;

		default :
			links[0] = "Link Product";
			links[1] = "Link Text";
			links[2] = "Link Photo";
	}

	// Create options selection
	var linkInput = document.createElement("select");
	linkInput.setAttribute("class", "option-input");

	for (var x in links) {
		var linkOption = document.createElement("option");
		linkOption.value = x;
		linkOption.innerHTML = links[x];
		linkInput.appendChild(linkOption);
	}

	// Add event on change
	linkInput.addEventListener("change", function() {
		var optionKey         = parseInt(this.value);
		var thisRow           = this.parentNode.parentNode;
		var oldInputFieldNode = thisRow.childNodes[2];
		var oldPriceFieldNode = thisRow.childNodes[3];
		var newInputFieldNode = document.createElement("td");
		var newPriceFieldNode = document.createElement("td");

		switch(optionKey) {
			case 0 :
				newInputFieldNode.appendChild(self.getProductInput());
				newPriceFieldNode.appendChild(self.getProductPriceInput());
				break;

			case 1 :
				newInputFieldNode.appendChild(self.getTextInput());
				newPriceFieldNode.appendChild(self.getTextPriceInput());
				break;

			case 2 :
				newInputFieldNode.appendChild(self.getPhotoInput());
				newPriceFieldNode.appendChild(self.getPhotoPriceInput());
				break;

			case 3 :
				newInputFieldNode.appendChild(self.getYoutubeInput());
				newPriceFieldNode.appendChild(self.getYoutubePriceInput());
				break;
		}

		thisRow.replaceChild(newInputFieldNode, oldInputFieldNode);
		thisRow.replaceChild(newPriceFieldNode, oldPriceFieldNode);

		// Reposition inputs
		OptionEditor.reposition();
	});

	return linkInput;
};

/**
 *  Method getProductInput
 *
 *	This method is to get the inputs for link product option
 *
 *	@param int product_id The ID of product in database
 *	@param int site_id    The ID of website
 *	@param int sub_id	  // Can someone find out what is it?
 *
 *	@return DOM Object The product input field
 */
OptionEditor.prototype.getProductInput = function (product_id, site_id, sub_id) {
	// Get self object
	var self = this;

	// Unique ID
	var uid = self.ai;

	// Initialise parameter values
	product_id = (typeof product_id == 'undefined') ? "" : product_id;
	site_id	   = (typeof site_id == 'undefined')    ? "" : site_id;
	sub_id     = (typeof sub_id == 'undefined')     ? "" : sub_id;

	// Create Text Input for product ID
	var productIdInput = document.createElement("input");
	productIdInput.setAttribute("class"			, "text-input option-val-input");
	productIdInput.setAttribute("type"			, "text");
	productIdInput.setAttribute("placeholder"	, "Product ID");
	productIdInput.setAttribute("id"			, "option-val-input-" + uid);
	productIdInput.setAttribute("name"			, "pb_options[options][0]["+ uid +"][option_val]");
	productIdInput.value = product_id;

	// Create hidden text input for site id
	var siteIdInput = document.createElement("input");
	siteIdInput.setAttribute("type", "hidden");
	siteIdInput.setAttribute("id"  , "site-id-input-" + uid);
	siteIdInput.setAttribute("name", "pb_options[options][0]["+ uid +"][site_id]");
	siteIdInput.value = site_id;

	// Create hidden text input for sub id
	var subIdInput = document.createElement("input");
	subIdInput.setAttribute("type", "hidden");
	subIdInput.setAttribute("id"  , "sub-id-input-" + uid);
	subIdInput.setAttribute("name", "pb_options[options][0]["+ uid +"][sub_id]");
	subIdInput.value = sub_id;

	// Create hidden text input for position
	var positionInput = document.createElement("input");
	positionInput.setAttribute("type"  , "hidden");
	positionInput.setAttribute("class" , "options-pos-input");
	positionInput.setAttribute("name"  , "pb_options[options][0]["+ uid +"][position]");
  
	// Create button for product ID Search
	var button = document.createElement("button");
	button.setAttribute("type", "button");
	button.setAttribute("style", "width: 100%");
	button.innerHTML = "Search";

	// Add action to search product from database
	button.addEventListener("click", function() {
		OptionEditor.searchClickedId = uid;
		window.open('/webadmin/products/products_list.php?return_message=add&search_for=' + productIdInput.value + '&category=&year_released=');
	});

	// Grid all components
	var table = document.createElement("table");
	var row   = document.createElement("tr");
	var fcol  = document.createElement("td");
	var scol  = document.createElement("td");

	fcol.appendChild(productIdInput);
	fcol.appendChild(siteIdInput);
	fcol.appendChild(subIdInput);
	fcol.appendChild(positionInput);

	scol.appendChild(button);

	row.appendChild(fcol);
	row.appendChild(scol);

	table.appendChild(row);
	table.style.border = "0";
	table.style.width = "100%";

	// Wrap the grid in container
	var container = document.createElement("div");
	container.appendChild(table);

	return container;
};

/**
 *	Method: getTextInput
 *
 *	This method is to get inputs for link text option
 *
 *	@param String header The title of text option
 *	@param String text   The text value 
 *
 *	@return DOM Object The text input field
 */
OptionEditor.prototype.getTextInput = function(header, text) {
	// Get self object
	var self = this;

	// Unique ID
	var uid = self.ai;

	// Initialise parameter values
	header = (typeof header == 'undefined') ? "" : header;
	text   = (typeof text == 'undefined')   ? "" : text;

	// Create textinput for header
	var headerInput = document.createElement("input");
	headerInput.setAttribute("class"		, "text-input option-val-input");
	headerInput.setAttribute("type"			, "text");
	headerInput.setAttribute("placeholder"	, "Title");
	headerInput.setAttribute("id"			, "header-val-input-" + uid);
	headerInput.setAttribute("name"			, "pb_options[options][1]["+ uid +"][header]");
	headerInput.value = header;

	// Create textarea for text
	var textInput = document.createElement("textarea");
	textInput.setAttribute("class" 	, "textarea-input");
	textInput.setAttribute("style"	, "width: 98%; resize: vertical; height: 70px; margin-top: 10px;");
	textInput.setAttribute("id"		, "option-val-input-" + self.ai);
	textInput.setAttribute("name"	, "pb_options[options][1]["+ self.ai +"][option_val]")
	textInput.innerHTML = text;

	// Create hidden text input for position
	var positionInput = document.createElement("input");
	positionInput.setAttribute("type"  , "hidden");
	positionInput.setAttribute("class" , "options-pos-input");
	positionInput.setAttribute("name"  , "pb_options[options][1]["+ uid +"][position]");

	// Wrap the components in container
	var container = document.createElement("div");
	container.appendChild(headerInput);
	container.appendChild(textInput);
	container.appendChild(positionInput);
	container.style.padding = "5px 0";

	return container;
};

/**
 *	Method: getPhotoInput
 *
 *	This method is to get inputs for link photo option
 *	
 *	This method is using ajax to upload photo.
 *
 *	@param String header 	  The title of photo option
 *	@param String src    	  The image source
 *	@param String description The description of photo option
 *
 *	@return DOM Object The photo input field
 */
OptionEditor.prototype.getPhotoInput = function(header, src, description) {
	// Get self object
	var self = this;

	// Unique ID
	var uid = self.ai;

	// Initialise parameter values
	header 		= (typeof header == 'undefined') 	  ? "" : header;
	src    		= (typeof src == 'undefined')    	  ? "" : src;
	description = (typeof description == 'undefined') ? "" : description;

	// Create textinput for header
	var headerInput = document.createElement("input");
	headerInput.setAttribute("class"		, "text-input option-val-input");
	headerInput.setAttribute("type"			, "text");
	headerInput.setAttribute("placeholder"	, "Title");
	headerInput.setAttribute("id"			, "header-val-input-" + uid);
	headerInput.setAttribute("name"			, "pb_options[options][2]["+ uid +"][header]");
	headerInput.value = header;

	// Create preview image
	var image = document.createElement("img");
	image.setAttribute("width"  , "100");
	image.setAttribute("height"	, "100");
	image.setAttribute("id"		, "photo-preview-" + uid);
	image.setAttribute("style"	, "margin: 10px 0; float: left;");

	// Preview image source
	if (src == "") {
		image.setAttribute("src", "http://via.placeholder.com/100x100");
	} else {
		image.setAttribute("src", window.location.protocol + "//" + window.location.host+"/images/builder//" + src);
	}

	// Create hidden text input for image source
	var srcInput = document.createElement("input");
	srcInput.setAttribute("type", "hidden");
	srcInput.setAttribute("id", "option-val-input-" + uid);
	srcInput.setAttribute("name", "pb_options[options][2]["+ uid +"][option_val]");
	srcInput.value = src;

	// Create textarea for description
	var descriptionInput = document.createElement("textarea");
	descriptionInput.setAttribute("class", "textarea-input");
	descriptionInput.setAttribute("style", "width: 98%; resize: vertical; height: 126px; margin: 10px 0;");
	descriptionInput.setAttribute("name" , "pb_options[options][2]["+ uid +"][photo_description]");
	descriptionInput.innerHTML = description;

	// Create hidden text input for position
	var positionInput = document.createElement("input");
	positionInput.setAttribute("type"  , "hidden");
	positionInput.setAttribute("class" , "options-pos-input");
	positionInput.setAttribute("name"  , "pb_options[options][2]["+ uid +"][position]");

	// Create button for search
	var searchBtn = document.createElement("button");
	searchBtn.setAttribute("type" , "button");
	searchBtn.setAttribute("style", "width: 100%; margin-bottom: 5px;");
	searchBtn.innerHTML = "Search";

	// Add action to search photo from database
	searchBtn.addEventListener("click", function() {
		OptionEditor.searchClickedId = uid;
		window.open('/webadmin/product_builder/photo_select.php?return_message=add');
	});

	// Create button for upload
	var uploadBtn = document.createElement("button");
	uploadBtn.setAttribute("type", "button");
	uploadBtn.setAttribute("style", "width: 100%;");
	uploadBtn.innerHTML = "Upload";

	// Add action to upload photo to database
	uploadBtn.addEventListener("click", function() {
		OptionEditor.searchClickedId = uid;

		// Create image input
		var imageInput = document.createElement("input");
		imageInput.setAttribute("type", "file");
		imageInput.setAttribute("name", "product_builder_img");
		imageInput.click();

		// Upload Image
		imageInput.addEventListener("change", function() {
			var file = imageInput.files[0];
			var upload = new Upload(file);

			if (upload.getType() != "image/jpeg" && upload.getType() != "image/png") {
				alert("Image file invalid. Image: JPEG, PNG");
			} else {
				upload.doUpload();
			}
		});
	});

	// Create progress bar
	var progressbar = document.createElement("div");
	progressbar.setAttribute("class", "progress-bar");

	// Create Progress bar wrapper
	var progressBarWrapper = document.createElement("div");
	progressBarWrapper.setAttribute("id", "progress-wrp");
	progressBarWrapper.setAttribute("style", "display: none");
	progressBarWrapper.appendChild(progressbar);

	// Grid all components
	var table = document.createElement("table");
	var row   = document.createElement("tr");
	var fcol  = document.createElement("td");
	var scol  = document.createElement("td");

	fcol.appendChild(headerInput);
	fcol.appendChild(srcInput);
	fcol.appendChild(descriptionInput);
	fcol.appendChild(positionInput);
	fcol.style.verticalAlign = "top";

	scol.appendChild(searchBtn);
	scol.appendChild(uploadBtn);
	scol.appendChild(progressBarWrapper);
	scol.appendChild(image);
	scol.style.verticalAlign = "top";
	scol.style.width 		 = "100px";

	row.appendChild(fcol);
	row.appendChild(scol);

	table.appendChild(row);
	table.style.border = "0";
	table.style.width  = "100%";

	// Wrapp the grid in container
	var container = document.createElement("div");
	container.appendChild(table);

	return container;
};

/**
 *	Method: getYoutubeInput
 *
 *	This method is to get input for link youtube option
 *
 *	@param String header 	  The title of youtube option
 *	@param String youtube     The Youtube ID
 *	@param String description The description of youtube option
 * 	@param String info_link   The link to the page for further reading
 *
 *	@return DOM Object the youtube input field
 */
OptionEditor.prototype.getYoutubeInput = function(header, youtube, description, info_link) {
	// Get self object
	var self = this;

	// Unique ID
	var uid = self.ai;

	// Initialise parameter values
	header 		= (typeof header == 'undefined') 	  ? "" : header;
	youtube    	= (typeof youtube == 'undefined')     ? "" : youtube;
	description = (typeof description == 'undefined') ? "" : description;
	info_link   = (typeof info_link == 'undefined')   ? "" : info_link;

	// Create textinput for header
	var headerInput = document.createElement("input");
	headerInput.setAttribute("class"		, "text-input option-val-input");
	headerInput.setAttribute("type"			, "text");
	headerInput.setAttribute("placeholder"	, "Title");
	headerInput.setAttribute("id"			, "header-val-input-" + uid);
	headerInput.setAttribute("name"			, "pb_options[options][3]["+ uid +"][header]");
	headerInput.value = header;

	// Create input for youtube id
	var youtubeInput = document.createElement("input");
	youtubeInput.setAttribute("class"		, "text-input option-val-input");
	youtubeInput.setAttribute("type"		, "text");
	youtubeInput.setAttribute("placeholder"	, "YouTube Video. Eg: T9Wwc5cWUkg");
	youtubeInput.setAttribute("id"			, "option-val-input-" + uid);
	youtubeInput.setAttribute("name"		, "pb_options[options][3]["+ uid +"][option_val]");
	youtubeInput.setAttribute("style"		, "margin: 10px 0;");
	youtubeInput.value = youtube;

	// Create textarea for description
	var descriptionInput = document.createElement("textarea");
	descriptionInput.setAttribute("class", "textarea-input");
	descriptionInput.setAttribute("style", "width: 98%; resize: vertical; height: 70px; margin-bottom: 10px");
	descriptionInput.setAttribute("name" , "pb_options[options][3]["+ uid +"][video_description]");
	descriptionInput.innerHTML = description;

	// Create input for info link
	var infoLinkInput = document.createElement("input");
	infoLinkInput.setAttribute("class"			, "text-input option-val-input");
	infoLinkInput.setAttribute("type"			, "text");
	infoLinkInput.setAttribute("placeholder"	, "Info Link");
	infoLinkInput.setAttribute("id"				, "infolink-val-input-" + uid);
	infoLinkInput.setAttribute("name"			, "pb_options[options][3]["+ uid +"][infolink]");
	infoLinkInput.value = info_link;

	// Create hidden text input for position
	var positionInput = document.createElement("input");
	positionInput.setAttribute("type"  , "hidden");
	positionInput.setAttribute("class" , "options-pos-input");
	positionInput.setAttribute("name"  , "pb_options[options][3]["+ uid +"][position]");

	// Wrap all components
	var container = document.createElement("div");
	container.appendChild(headerInput);
	container.appendChild(youtubeInput);
	container.appendChild(descriptionInput);
	container.appendChild(infoLinkInput);
	container.appendChild(positionInput);
	container.style.padding = "5px 0";

	return container;
};

/**
 *  Method: getProductPriceInput
 *	
 *	This method is to get price input for product option
 *
 *	@param float  price 	The price of the cost
 *	@param float  usd   	The price in USD
 *	@param float  aud   	The price in AUD
 *	@param String currency 	The currency used in the option
 *
 *	@return DOM Object The price input field
 */
OptionEditor.prototype.getProductPriceInput = function(price, usd, aud, currency) {
	return this.getPriceInput(0, price, usd, aud, currency);
};

/**
 *  Method: getTextPriceInput
 *
 *	This method is to get price input for text option

 *	@param float  price 	The price of the cost
 *	@param float  usd   	The price in USD
 *	@param float  aud   	The price in AUD
 *	@param String currency 	The currency used in the option
 *
 *	@return DOM Object The price input field
 */
OptionEditor.prototype.getTextPriceInput = function(price, usd, aud, currency) {
	return this.getPriceInput(1, price, usd, aud, currency);
};

/**
 *	Method: getPhotoPriceInput
 *
 *	This method is to get price input for photo option
 *
 *	@param float  price 	The price of the cost
 *	@param float  usd   	The price in USD
 *	@param float  aud   	The price in AUD
 *	@param String currency 	The currency used in the option
 *
 *	@return DOM Object The price input field
 */
OptionEditor.prototype.getPhotoPriceInput = function(price, usd, aud, currency) {
	return this.getPriceInput(2, price, usd, aud, currency);
};

/**
 *  Method: getYoutubePriceInput
 *
 *	This method is to get price input for youtube option
 *
 *	@param float  price 	The price of the cost
 *	@param float  usd   	The price in USD
 *	@param float  aud   	The price in AUD
 *	@param String currency 	The currency used in the option
 *
 *	@return DOM Object The price input field
 */
OptionEditor.prototype.getYoutubePriceInput = function(price, usd, aud, currency) {
	return this.getPriceInput(3, price, usd, aud, currency);
};

/**
 *  Method: getPriceInput
 *
 *	This method is to get price input
 *
 *  @param int    optionkey The option key to determine which option type
 *	@param float  price 	The price of the cost
 *	@param float  usd   	The price in USD
 *	@param float  aud   	The price in AUD
 *	@param String currency 	The currency used in the option
 *
 *	@return DOM Object The price input field
 */
OptionEditor.prototype.getPriceInput = function(optionKey, price, usd, aud, currency) {
	// Get self object
	var self = this;

	// Unique ID
	var uid = self.ai;

	// initialise parameter values
	optionKey = (typeof optionKey == 'undefined') ? "" : optionKey;
	price     = (typeof price == 'undefined')     ? "" : price;
	usd 	  = (typeof usd == 'undefined') 	  ? "" : usd;
	aud 	  = (typeof aud == 'undefined')		  ? "" : aud;
	currency  = (typeof currency == 'undefined')  ? "" : currency;

	// Create textinput for price
	var priceInput = document.createElement("input");
	priceInput.setAttribute("class" , "text-input");
	priceInput.setAttribute("id"	, "option-price-input-" + uid);
	priceInput.setAttribute("name"	, "pb_options[options]["+ optionKey +"]["+ uid +"][price]");
	priceInput.setAttribute("style"	, "width: 90px;");
	priceInput.value = price;

	// Create options selection for currency
	var currencyInput = document.createElement("select");
	currencyInput.setAttribute("id"		 	   , "option-currency-input-" + uid);
	currencyInput.setAttribute("name"	  	   , "pb_options[options]["+ optionKey +"]["+ uid +"][currency]");
	currencyInput.setAttribute("data-usd" 	   , usd);
	currencyInput.setAttribute("data-aud" 	   , aud);

	// Option for USD
	var usdCurrencyOption = document.createElement("option");
	usdCurrencyOption.value = "USD";
	usdCurrencyOption.innerHTML = "USD";

	// Option for AUD
	var audCurrencyOption = document.createElement("option");
	audCurrencyOption.value = "AUD";
	audCurrencyOption.innerHTML = "AUD";

	// Selected currency
	if (currency == "USD") {
		usdCurrencyOption.setAttribute("selected", "selected");
	} else {
		audCurrencyOption.setAttribute("selected", "selected");
	}

	// Add action to change price on currency changed
	currencyInput.addEventListener("change", function() {
		var usd 	 		= this.getAttribute("data-usd");
		var aud      		= this.getAttribute("data-aud");
		var currentCurrency = this.value;

		if (usd != "" && aud != "") {
			if (currentCurrency == "USD") {
				priceInput.value = usd;
			} else {
				priceInput.value = aud;
			}
		}
	});

	// Add options to selector
	currencyInput.appendChild(usdCurrencyOption);
	currencyInput.appendChild(audCurrencyOption);

	// Grid all components
	var table = document.createElement("table");
	var row   = document.createElement("tr");
	var fcol  = document.createElement("td");
	var scol  = document.createElement("td");

	fcol.appendChild(priceInput);
	fcol.style.textAlign = "center";

	scol.appendChild(currencyInput);

	row.appendChild(fcol);
	row.appendChild(scol);

	table.appendChild(row);
	table.style.border = "0";
	table.style.width  = "100%";

	// Wrapp the grid in container
	var container = document.createElement("div");
	container.appendChild(table);

	return container;
};

/**
 *  Public Function: setProductData
 *
 *	Set Product data from search
 *
 *	@param Object data The data of product from database
 *
 *	@return void
 */
function setProductData(data) {
	// Type of currencies
	currencies = ["USD", "AUD"];

	// Get price for different currencies
	var usd = (data['primary_currency'] == "USD") ? data['primary_price'] : data['product_offerus'];
	var aud = (data['primary_currency'] == "AUD" ) ? data['primary_price'] : data['product_offerau'];

	// Adjust the price input
	usd = (typeof usd == 'undefined') ? 0 : usd;
	aud = (typeof aud == 'undefined') ? 0 : aud;

	// Insert value from search to input field
	document.getElementById("option-val-input-" + OptionEditor.searchClickedId).value = data['product_id'];
	document.getElementById("option-currency-input-" + OptionEditor.searchClickedId).selectedIndex = currencies.indexOf(data['primary_currency']);
	document.getElementById("option-price-input-" + OptionEditor.searchClickedId).value = data['primary_price'];
	document.getElementById("site-id-input-" + OptionEditor.searchClickedId).value = data['site_id'];
	document.getElementById("sub-id-input-" + OptionEditor.searchClickedId).value = data['sub_id'];

	// Set currency selector attributes
	document.getElementById("option-currency-input-" + OptionEditor.searchClickedId).setAttribute("data-usd", usd);
	document.getElementById("option-currency-input-" + OptionEditor.searchClickedId).setAttribute("data-aud", aud);
}

/**
 *	Public Function: setPhotoData
 *
 *	Set photo data from search
 *
 *	@param String data The value of photo
 *
 *	@return void
 */
function setPhotoData(data) {
	document.getElementById("option-val-input-" + OptionEditor.searchClickedId).value = data;
	document.getElementById("photo-preview-" + OptionEditor.searchClickedId).setAttribute("src", window.location.protocol + "//" + window.location.host+"/images/builder//" + data);
}

/**
 *  Class: Upload
 *
 *	This class is to upload the file to database via ajax
 */
var Upload = function (file) {
    this.file = file;
};

/**
 *	Method: getType
 *
 *	This method is to get the type of file
 *
 *	@return String The type of file
 */
Upload.prototype.getType = function() {
    return this.file.type;
};

/**
 *	Method: getSize
 *
 *	This method is to get the size of file
 *
 *	@return int The size of file
 */
Upload.prototype.getSize = function() {
    return this.file.size;
};

/**
 *	Method: getName
 *
 *	This method is to get the name of file
 *
 *	@return String the name of file
 */
Upload.prototype.getName = function() {
    return this.file.name;
};

/**
 *	Method: doUpload
 *
 *	This method will send all data to /ajax/pb_upload_image.php via ajax connection
 *	to upload the selected image.
 */
Upload.prototype.doUpload = function () {
    var that = this;
    var formData = new FormData();

    // add assoc key values, this will be posts values
    formData.append("img", that.file);

    $.ajax({
        type: "POST",
        data: formData,
        url: "/webadmin/product_builder/ajax/pb_upload_image.php",
        xhr: function () {
            var myXhr = $.ajaxSettings.xhr();
            if (myXhr.upload) {
                myXhr.upload.addEventListener('progress', that.progressHandling, false);
            }
            return myXhr;
        },
        success: function (data) {
     		if (data == "FILE_EXT_ERROR") {
     			alert("File extenstion is invalid. Only JPEG or PNG is allowed.");
     		} else if (data == "UPLOAD_ERROR") {
     			alert("File upload failed. Please try again.");
     		} else {
            	document.getElementById("option-val-input-" + OptionEditor.searchClickedId).value = data;
            	document.getElementById("photo-preview-" + OptionEditor.searchClickedId).setAttribute("src", window.location.protocol + "//" + window.location.host+"/images/builder//" + data)
        	}
            $('#progress-wrp').hide();
        },
        error: function (error) {
            alert("Fail uploading image. Please try again.");
        },
        contentType: false,
        processData: false,
        cache: false
    });
};

/**
 *	Method: progressHandling
 *
 *	This method is to handle progress bar
 */
Upload.prototype.progressHandling = function (event) {
    var percent = 0;
    var position = event.loaded || event.position;
    var total = event.total;
    var progress_bar_id = "#progress-wrp";
   	$(progress_bar_id).show();
    if (event.lengthComputable) {
        percent = Math.ceil(position / total * 100);
    }
    // update progressbars classes so it fits your code
    $(progress_bar_id + " .progress-bar").css("width", +percent + "%");
};