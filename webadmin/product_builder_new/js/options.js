/**
 *	Module: Product Builder
 *
 *	options.js
 *
 *	This is javascript app for options section
 *
 *	@author Kelvin (khtat@highwaygroup.net)
 *	@since  25/01/2018
 */

/**
 *	This function is to bind sortable UI to option table
 */
function bindOptionTableSortable() {
	$('#option-table').sortable({
		containerSelector: "table.option-list-table",
		itemPath: "> tbody",
		itemSelector: "tr.option-list-row",
		placeholder: "<tr class='placeholder'/>",
		handle: "tr.option-list-row",

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
		    OptionEditor.reposition();
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
}

/**
 *	This function is to re initiate the option editor when the template is changed
 */
var newmachineid=100;
function changeTemplate(select) {
	// Initiate Option Editor
	$('#option-new-list').html('<div id="optionList"></div>');
	if (select.value == "standard") {
		var optionEditor = new OptionEditor('optionList',newmachineid);
		optionEditor.init("standard");
	}else if (select.value == "brand") {
		var optionEditor = new OptionEditor('optionList',newmachineid);
		optionEditor.init("standard");
	} else {
		var optionEditor = new OptionEditor('optionList',newmachineid);
		optionEditor.init("videofeat");
	}

	// Bind sortable ui
	bindOptionTableSortable();
}
function addnewbrand(){
	newmachineid++;
	var  macid='option-list'+newmachineid;
	$('#option-new-list').append('<div id="'+macid+'" style="margin-top: 15px;"></div>')
	var optionEditor = new OptionEditor(macid,newmachineid);

	// Get template
	var template = $('#option-template').val();
	console.log(template);
	if (template.length > 0) {
		optionEditor.init(template);
	} else {
		optionEditor.init("standard");
	}
}
var coloursarray = new Array();
$(document).ready(function(){
	// Initiate option editor
	$.ajax({
		url : "ajax/get_machine_colours.php",
		dataType : "json",
		success : function(r){
			coloursarray = r;
			var optionlength=$('.optionListClass').length;
			if(optionlength >0){
				for(var i=1; i<=optionlength; i++){
					var optionEditor = new OptionEditor('optionList'+i,i);
					var template = $('#option-template').val();
					if (template.length > 0) {
						optionEditor.init(template);
					} else {
						optionEditor.init("standard");
					}
					bindOptionTableSortable();
					}
			}else{
				var optionEditor = new OptionEditor('optionList',newmachineid);
				var template = $('#option-template').val();
				if (template.length > 0) {
					optionEditor.init(template);
				} else {
					optionEditor.init("standard");
				}
				bindOptionTableSortable();
			}
			
		}
	});
});
function serachlinkproduct(id) {
    var url = '/webadmin/products_new/master_products.php?';
    url += 'search_for=' + id;
    url += '&multiple=y';
    url += '&action=list';
    url += '&return_message=link_product';
    window.open(url, 'product_popup');
}
$('body').on('click', '.checkbox_select_default', function() {

	var tableid=$(this).attr('tableid');
	var ischecked=$(this).is(":checked");
	var inputs =document.querySelectorAll('.checkbox-class-'+tableid);
	  for (var i = 0; i < inputs.length; i++) {
	    inputs[i].checked = false;
	}
	if(ischecked){
		$(this).prop('checked', true);
	}else{
		$(this).prop('checked', false);
	}

})

