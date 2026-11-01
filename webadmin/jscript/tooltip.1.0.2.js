/***************************************************************************
 *                            tooltip.1.0.2.js
 *                            -------------------
 *   begin                : November 23, 2005
 *   copyright            : (C) 2005 BSG Online Games
 *	 author:			  : Bryan Wiebe
 *   email                : freelance@bsgonlinegames.com
 *
 *   version: 1.0.2
 *
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *	 https://www.gnu.org/licenses/gpl.txt
 *
 ***************************************************************************/

/* Directions:
 *
 *       To create a new tooltip simply add this to your <a href
 *       onMouseOver="tt_in("Your text", OffsetX, OffsetY)"
 *       onMouseOut="tt_out()"
*/

/* Customizable Variables: */
var tt_divx, tt_divy, tt_on = false, tt_offx = 0, tt_offy = 0, tt_rightpad = 0, tt_bottompad = 0, tt_delay = 250, tt_outdelay = 5;
var tt_cellpadding = 4, tt_maxwidth = 400, tt_maxheight = 400;
var tt_border = 1, tt_borderstyle = 'solid', tt_background = '#FFFFE1', tt_color = '#000000', tt_bordercolor = '#000000';

/* Do not edit the following */
var mouse_x = 0, mouse_y = 0;
var tt_id = null, tt_data = '';
var tt_this_offx = tt_offx, tt_this_offy = tt_offy;

function tt_in(data, x, y) {

	if (tt_data != data) {

		tt_data = data;
		tt_id = tt_getId();

		tt_capture();
		if (x != null) { tt_this_offx = x; } else { tt_this_offx = tt_offx; }
		if (y != null) { tt_this_offy = y; } else { tt_this_offy = tt_offy; }

		tt_id.innerHTML = '<table cellpadding="' + tt_cellpadding + '" cellspacing="0" class="tooltip"><tr><td>' + data + '</td></tr></table>';
		tt_on = true;
		tt_divx = tt_id.offsetWidth;
		tt_divy = tt_id.offsetHeight;
		if (tt_divx > tt_maxwidth) { tt_id.style.width = tt_maxwidth + 'px'; tt_divx = tt_maxwidth; }
		if (tt_divx > tt_maxheight) { tt_id.style.height = tt_maxheight + 'px'; tt_divx = tt_maxheight; }
		tt_move(mouse_x, mouse_y);
		setTimeout('tt_show()', tt_delay);

	} else {

		tt_on = true;

	}

}

function tt_out() {

	tt_id = tt_getId();
	tt_on = false;

	setTimeout('tt_delayout()', tt_outdelay);

}

function tt_delayout() {

	if (tt_on == false) {

		tt_release();
		tt_data = '';

		tt_id.innerHTML = '';
		tt_id.style.visibility = 'hidden';
		tt_id.style.width = null;
		tt_id.style.height = null;

	}

}

function tt_show() {

	if (tt_on) {
		tt_id = tt_getId();
		tt_id.style.visibility = 'visible';
	}
}

function tt_getId() {
	return document.getElementById('tooltip');
}

function tt_release() {

	if (document.releaseEvents) {
		document.releaseEvents(Event.MOUSEMOVE);
	}
	document.onmousemove = null;
}
function tt_capture() {
	if (document.captureEvents) {
		document.captureEvents(Event.MOUSEMOVE);
	}
	document.onmousemove = tt_mousemove;
}

function tt_mousemove(e) {

	if (tt_on) {

		if (!e) {
			e = window.event;
		}

		mouse_x = e.clientX;
		mouse_y = e.clientY;
		if( typeof( e.pageX ) == 'number' ) {
			page_x = e.pageX;
			page_y = e.pageY;
		} else {
			page_x = e.clientX + document.body.scrollLeft;
			page_y = e.clientY + document.body.scrollTop;
		}
		tt_move(page_x + 10 + tt_this_offx, page_y + 10 + tt_this_offy);

	}

}

function tt_move(x, y) {

	realx = mouse_x + 10 + tt_this_offx;
	realy = mouse_y + 10 + tt_this_offy;
	tt_id = tt_getId();

	if ( typeof (window.innerWidth) == 'number' ) {
		screenx = window.innerWidth;
		screeny = window.innerHeight;
	} else {
		screenx = document.body.offsetWidth;
		screeny = document.body.offsetHeight;
	}
	screenx = screenx - tt_rightpad;
	screeny = screeny - tt_bottompad;

	if (realx + tt_divx >= screenx) {
		x = Math.max(x - 10 - tt_this_offx - tt_divx, 0);
	}
	if (realy + tt_divy >= screeny) {
		y = Math.max(y - 10 - tt_this_offy - tt_divy, 0);
	}

	tt_id.style.top = (y) + 'px';
	tt_id.style.left = (x) + 'px';

}


document.write('<div id="tooltip" style="position: absolute; visibility: hidden; z-index: 99;"></div><style type="text/css">.tooltip { background-color: ' + tt_background + '; color: ' + tt_color + '; border-color: ' + tt_bordercolor + '; border-width: ' + tt_border + 'px; border-style: ' + tt_borderstyle + '; }</style>');