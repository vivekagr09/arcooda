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
function changeTemplate(select) {
	// Initiate Option Editor
	if (select.value == "standard") {
		var optionEditor = new OptionEditor('optionList');
		optionEditor.init("standard");
	} else {
		var optionEditor = new OptionEditor('optionList');
		optionEditor.init("videofeat");
	}

	// Bind sortable ui
	bindOptionTableSortable();
}

$(document).ready(function(){
	// Initiate option editor
	var optionEditor = new OptionEditor('optionList');

	// Get template
	var template = $('#option-template').text();
	if (template.length > 0) {
		optionEditor.init(template);
	} else {
		optionEditor.init("standard");
	}

	// Bind sortable ui
	bindOptionTableSortable();
	
});

function validatedefault(){
	 if (typeof $('.option-val-checkbox:checkbox:checked').val()=='undefined') {
        alert('Please select default value');
        return false;
    }
    return true;
}
