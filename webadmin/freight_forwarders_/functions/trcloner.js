/******************************
 * JavaScript Table Row Cloner
 ******************************
 * Author: Nathan Tomkins
 * Email: ntomkins@highwaygroup.net
 * Last Modified: 20050124
 ******************************
 * This set of functions provides a method for dynamic extending of tables.
 * To use: - call cloneRow('foo', 'bar') to copy the row with id foo to the table with id bar.
 *         - call cloneRow('foo') to copy the row with id foo to the end of the same table.
 *         - modify processCellContents() and processCellDOM() if you want to do some other kind of processing ;]
 * Things to remember: if you have a javascript function requiring an integer id passed in which must be updated,
 *						either write it as (0) or change the process* functions.
 ******************************/

var next_row_id = 1;

/**
 *  function: cloneRow
 * arguments: rowid: ID of an existing <tr> element (name will not work)
 *            tableid: (optional) where you want to copy the row to
 *                     defaults to parent table of rowid if not specified
 *   effects: Copies the <tr> to the end of the table, adjusts formatting,
 *            and runs a processing function on the contents of the <td>'s in the row
 *   returns: nothing
 *     notes: Assumes that you'll want to be copying into the last tbody of the destination table ;]
 *      bugs: IE doesn't seem to populate newRow.cells[] until it has been attached to a table...
 *            which means that I have to attach it first. This means you can see the modifications
 *            happenning if you're quick. So, if .cells[] is empty, I attach the row ASAP, else I
 *            wait until *after* I fiddle with the cells. Fucking Stupid Browsers.
 */
function cloneRow(rowid, tableid)
{
	var row_added_already = false;
	// Find some elements
	var oldRow = document.getElementById(rowid);
	if(tableid)
	{
		var table = document.getElementById(tableid);
		var tbody = table.tBodies[table.tBodies.length - 1];
	}
	else
	{
		var tbody = oldRow.parentNode;
	}

	// Copy <TR>
	var newRow = oldRow.cloneNode(true);

	// Find a free ID
	while(document.getElementById(oldRow.id + "_" + next_row_id)) next_row_id++;

	if(!newRow.cells.length)
	{	// Deal with IE being broken =|
		// Put row in table
		row_added_already = true;
		tbody.appendChild(newRow);
	}

	newRow.id = oldRow.id + "_" + next_row_id;

	// Process cell contents
	for(i = 0; i < newRow.cells.length; i++)
	{
		// Run cell's HTML content through a string processor
		newRow.cells[i].innerHTML = processCellContents(newRow.cells[i].innerHTML);

		// Run cell's children through a DOM processor (i.e. for trimming attributes)
		processCellDOM(newRow.cells[i]);
	}

	// Update next row counter
	next_row_id++;

	if(!row_added_already)
	{
		// Put row in table
		tbody.appendChild(newRow);
	}

	// Set Alternate Row Highlighting
	if(getPrevSib(newRow).className != "alt")
	{
		newRow.className = "alt";
	}
	else
	{
		newRow.className = "";
	}

	return next_row_id - 1;
}

/**
 *  function: processCellContents
 * arguments: String to process
 *   returns: Processed string
 *   effects: Replaces [0] and (0) with appropriate IDs
 */
function processCellContents(cell_contents)
{
	var search_for = "[0]";
	while(cell_contents.indexOf(search_for) != -1)
	{
		var before = cell_contents.substring(0, cell_contents.indexOf(search_for));
		var after = cell_contents.substring(cell_contents.indexOf(search_for) + search_for.length, cell_contents.length);
		cell_contents = before + "[" + next_row_id + "]" + after;
	}

	var search_for = "(0)";
	while(cell_contents.indexOf(search_for) != -1)
	{
		var before = cell_contents.substring(0, cell_contents.indexOf(search_for));
		var after = cell_contents.substring(cell_contents.indexOf(search_for) + search_for.length, cell_contents.length);
		cell_contents = before + "(" + next_row_id + ")" + after;
	}
	return cell_contents;
}

/**
 *  function: processCellDOM
 * arguments: HTML Element to process (can be used recursively)
 *   returns: nothing
 *   effects: blanks the value= tag on any input-text or input-hidden
 *            unticks any input-checkbox
 */
function processCellDOM(cell)
{
	for(var i = 0; i < cell.childNodes.length; i++)
	{
		var child = cell.childNodes[i];
		if(child.childNodes.length) processCellDOM(child);
		if(child.value && (child.type == "text")) child.value = "";
		if(child.value && (child.type == "hidden")) child.value = "";
		if(child.checked && (child.type == "checkbox")) child.checked = "";
	}
}

/**
 *  function: getPrevSib
 * arguments: a DOM element
 *   returns: closest previous sibling that IS NOT a text node
 *     notes: this is to deal with browsers that treat whitespace as sigificant in the DOM tree (like Mozilla)
 */
function getPrevSib(e)
{
	if(e.previousSibling.nodeType == 3)
	{
		return getPrevSib(e.previousSibling);
	}
	else
	{
		return e.previousSibling;
	}
}
