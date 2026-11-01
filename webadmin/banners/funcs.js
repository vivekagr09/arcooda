function resize(theElem, stepSize, minSize, maxSize) {
	if (theElem == null || theElem.rows == null || theElem.cols == null || theElem.value == null) return;
	if (stepSize == null || stepSize < 1) stepSize = 2;
	if (maxSize == null || maxSize < 1) maxSize = 100;
	if (minSize == null || minSize < 1) minSize = 2;

	var rowContent = theElem.value.split('\n');
	var rowCount = rowContent.length;

	if (theElem.cols > 0)
		for (i = 0; i < rowContent.length; i++)
			rowCount += Math.floor(rowContent[i].length / theElem.cols);

	rowCount = Math.floor(rowCount / stepSize) * stepSize + stepSize;

	if (minSize != null && minSize > rowCount) rowCount = minSize;
	if (maxSize != null && maxSize > minSize && maxSize < rowCount) rowCount = maxSize;

	theElem.rows = rowCount;
}

function getElement(name) {
	var theElem = document.getElementById(name);
	if (theElem != null) return theElem;
	var theElems = document.getElementsByName(name);
	if (theElems.length > 0) return theElems[0];
	return null;
}

function getValue(name) {
	var theElem = getElement(name);
	if (theElem == null) return null;
	return theElem.value;
}

function setValue(name, value) {
	var elem = getElement(name);
	if (elem == null) return;
	if (elem.tagName == 'SELECT')
		for (var i = 0; i < elem.options.length; i++)
			if (elem.options[i].value == value) {
				elem.options[i].selected = true;
				elem.selectedIndex = i;
			}
			else
				elem.options[i].selected = false;
	if (elem.tagName == 'SPAN' || elem.tagName == 'DIV')
		elem.innerHTML = value;
	if (elem.value != null) elem.value = value;
	else if (elem.innerHTML) elem.innerHTML = value;
}

function addTableRow(theTable, newName, rownum) {
	if (rownum == null) rownum = theTable.rows.length;
	var newRow = theTable.insertRow(rownum);
	newRow.name = newName;
	newRow.id = newName;
	return newRow;
}

function addTableCell(tableRow, innerStuff) {
	var newCol = document.createElement('td');
	newCol.innerHTML = innerStuff;
	tableRow.appendChild(newCol);
	return newCol;
}