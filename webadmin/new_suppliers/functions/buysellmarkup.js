/************************************************
 * Buy/Sell/Markup Dynamic Calculation JavaScript
 ************************************************
 * Author: Nathan Tomkins
 * Info: Just a few simple functions to go in onkeyup
 *			events to recalculate prices and such
 ************************************************/

function calcSell(stock_id)
{
	var buy = document.getElementById("stock["+stock_id+"][buy]").value;
	var sell_box = document.getElementById("stock["+stock_id+"][sell]");
	var markup = document.getElementById("stock["+stock_id+"][markup]").value;
	sell_box.value = (1 * buy) + (buy * markup / 100);
}

function calcMark(stock_id)
{
	var buy = document.getElementById("stock["+stock_id+"][buy]").value;
	var sell = document.getElementById("stock["+stock_id+"][sell]").value;
	var markup_box = document.getElementById("stock["+stock_id+"][markup]");
	markup_box.value = 100 * (sell - buy) / (buy);
}

function calcSellMark(stock_id)
{
	if(document.getElementById("stock["+stock_id+"][price_flux]").checked)
	{
		calcSell(stock_id);
	}
	else
	{
		calcMark(stock_id);
	}
}
