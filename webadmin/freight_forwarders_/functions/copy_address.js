/*************************
 * Copy From Above script
 * Filename: copy_address.js
 *************************
 * Author: Nathan Tomkins
 * This script contains the javascript magic for the "Copy from Above" link
 * in edit_member.php. It depends on trcloner.js, and although the idea is
 * fairly generic, this implementation is very specific.
 *************************/

function copy_address()
{
	var i = 0;
	var d = document;

	// See if we need a new row (i.e. first one isn't blank)
	if(d.getElementsByName("ship_addr["+i+"][company]")[0].value != "" &&
		d.getElementsByName("ship_addr["+i+"][phone]")[0].value != "" &&
		d.getElementsByName("ship_addr["+i+"][address_1]")[0].value != "" &&
		d.getElementsByName("ship_addr["+i+"][fax]")[0].value != "" &&
		d.getElementsByName("ship_addr["+i+"][mobile]")[0].value != "" &&
		d.getElementsByName("ship_addr["+i+"][city]")[0].value != "" &&
		d.getElementsByName("ship_addr["+i+"][state]")[0].value != "" &&
		d.getElementsByName("ship_addr["+i+"][zip]")[0].value != "" &&
		d.getElementsByName("ship_addr["+i+"][name]")[0].value != "" &&
		d.getElementsByName("ship_addr["+i+"][email]")[0].value != "")
	{
		i = cloneRow('clone_me');
	}

	// Do the copying.
	d.getElementsByName("ship_addr["+i+"][company]")[0].value = d.getElementsByName("member[company]")[0].value;
	d.getElementsByName("ship_addr["+i+"][phone]")[0].value = d.getElementsByName("member[phone]")[0].value;
	d.getElementsByName("ship_addr["+i+"][address_1]")[0].value = d.getElementsByName("member[address_1]")[0].value;
	d.getElementsByName("ship_addr["+i+"][fax]")[0].value = d.getElementsByName("member[fax]")[0].value;
	d.getElementsByName("ship_addr["+i+"][address_2]")[0].value = d.getElementsByName("member[address_2]")[0].value;
	d.getElementsByName("ship_addr["+i+"][mobile]")[0].value = d.getElementsByName("member[mobile]")[0].value;
	d.getElementsByName("ship_addr["+i+"][city]")[0].value = d.getElementsByName("member[city]")[0].value;
	d.getElementsByName("ship_addr["+i+"][state]")[0].value = d.getElementsByName("member[state]")[0].value;
	d.getElementsByName("ship_addr["+i+"][zip]")[0].value = d.getElementsByName("member[postcode]")[0].value;
	d.getElementsByName("ship_addr["+i+"][country]")[0].value = d.getElementsByName("member[country]")[0].value;
	d.getElementsByName("ship_addr["+i+"][name]")[0].value = d.getElementsByName("email[0][name]")[0].value;
	d.getElementsByName("ship_addr["+i+"][email]")[0].value = d.getElementsByName("email[0][email]")[0].value;
}
