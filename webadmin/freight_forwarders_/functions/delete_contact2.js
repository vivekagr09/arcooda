/****************
 * Delete Contact
 ****************
 * Author: Nathan Tomkins
 * Notes: This script removes a table row, then calls
 *			a PHP script to remove the corresponding entry
 *			from the database.
 ****************/
 //Modification by Daniel Kehl
 //Adjusted some properties to make it apply better to the Freight Forwarders section, and added some extra functions

function delContact(contact_row_id, forwarder_id, database_id)
{
	var orow = "contact_row";
	var question = "Are you sure you wish to delete this contact?";
	var row_id = orow + "_" + contact_row_id;

	if(confirm(question))
	{
		var row = document.getElementById(row_id);
		if(!row) row = document.getElementById(orow);
		var tbody = row.parentNode;
		//if(tbody.rows.length > 3)
		//if(tbody.rows.length > 3)
		//{
			//if the forwarder_id is 0, the row just has to be removed, otherwise it needs to be removed and deleted from the database
			// Normal case
			//var email_id = document.getElementsByName("contact[" + contact_row_id + "][contact_id]")[0].value;
			//if(email_id)
			//{	// If there's an email_id, this contact is in the DB. Otherwise it's a new one and can just be tossed.
			//	var dif = document.createElement("IFRAME");
			//	dif.id = "tempif" + email_id;
			//	dif.src = "edit_forwarder.php?action=delete_contact&contact_id=" + email_id;
			//	document.getElementsByTagName("body")[0].appendChild(dif);
			//}
			if(forwarder_id != 0)
			{
				var dif = document.createElement("IFRAME");
				dif.id = "tempif" + forwarder_id;
				dif.src = "edit_forwarder.php?action=delete_contact&database_id=" + database_id;
				document.getElementsByTagName("body")[0].appendChild(dif);
				document.getElementsByTagName("body")[0].removeChild(dif);
			}

			//This will need to be midified later along with the others to check if it needs to be removed from the database
			tbody.removeChild(row);
		//}
		//else
		//{	// Only one contact left.
		  	//alert("At least one set of contact details is required.");
		//}
	}
}

function delComment(comment_row_id, forwarder_id, database_id)
{
	var orow = "comment_row";
	var question = "Are you sure you wish to delete this comment?";
	var row_id = orow + "_" + comment_row_id;

	if(confirm(question))
	{
		var row = document.getElementById(row_id);
		if(!row) row = document.getElementById(orow);
		var tbody = row.parentNode;
		//if(tbody.rows.length > 3)
		//if(tbody.rows.length > 2)
		//{	// Normal case
			//var email_id = document.getElementsByName("email[" + contact_row_id + "][email_id]")[0].value;

			//if(email_id)
			//{	// If there's an email_id, this contact is in the DB. Otherwise it's a new one and can just be tossed.
			//	var dif = document.createElement("IFRAME");
			//	dif.id = "tempif" + email_id;
			//	dif.src = "edit_member.php?action=delete_contact&contact_id=" + email_id;
			//	document.getElementsByTagName("body")[0].appendChild(dif);
			//}
			if(forwarder_id != 0)
			{
				var dif = document.createElement("IFRAME");
				dif.id = "tempif" + forwarder_id;
				dif.src = "edit_forwarder.php?action=delete_comment&database_id=" + database_id;
				document.getElementsByTagName("body")[0].appendChild(dif);
				document.getElementsByTagName("body")[0].removeChild(dif);
			}

			tbody.removeChild(row);
		//}
		//else
		//{	// Only one contact left.
			//alert("At least one set of contact details is required.");
		//}
	}
}

function delQuote(quote_row_id, forwarder_id, database_id)
{
	var orow = "quote_row";
	var question = "Are you sure you wish to delete this quote?";
	var row_id = orow + "_" + quote_row_id;

	if(confirm(question))
	{
		var row = document.getElementById(row_id);
		if(!row) row = document.getElementById(orow);
		var tbody = row.parentNode;
		//if(tbody.rows.length > 3)
		//if(tbody.rows.length > 2)
		//{	// Normal case
			//var email_id = document.getElementsByName("email[" + contact_row_id + "][email_id]")[0].value;

			//if(email_id)
			//{	// If there's an email_id, this contact is in the DB. Otherwise it's a new one and can just be tossed.
			//	var dif = document.createElement("IFRAME");
			//	dif.id = "tempif" + email_id;
			//	dif.src = "edit_member.php?action=delete_contact&contact_id=" + email_id;
			//	document.getElementsByTagName("body")[0].appendChild(dif);
			//}
			if(forwarder_id != 0)
			{
				var dif = document.createElement("IFRAME");
				dif.id = "tempif" + forwarder_id;
				dif.src = "edit_forwarder.php?action=delete_quote&database_id=" + database_id;
				document.getElementsByTagName("body")[0].appendChild(dif);
				document.getElementsByTagName("body")[0].removeChild(dif);
			}

			tbody.removeChild(row);
		//}
		//else
		//{	// Only one contact left.
			//alert("At least one set of contact details is required.");
		//}
	}
}

function delBooking(booking_row_id, forwarder_id, database_id)
{
	var orow = "booking_row";
	var question = "Are you sure you wish to delete this booking?";
	var row_id = orow + "_" + booking_row_id;

	if(confirm(question))
	{
		var row = document.getElementById(row_id);
		if(!row) row = document.getElementById(orow);
		var tbody = row.parentNode;
		//if(tbody.rows.length > 3)
		//if(tbody.rows.length > 2)
		//{	// Normal case
			//var email_id = document.getElementsByName("email[" + contact_row_id + "][email_id]")[0].value;

			//if(email_id)
			//{	// If there's an email_id, this contact is in the DB. Otherwise it's a new one and can just be tossed.
			//	var dif = document.createElement("IFRAME");
			//	dif.id = "tempif" + email_id;
			//	dif.src = "edit_member.php?action=delete_contact&contact_id=" + email_id;
			//	document.getElementsByTagName("body")[0].appendChild(dif);
			//}
			if(forwarder_id != 0)
			{
				var dif = document.createElement("IFRAME");
				dif.id = "tempif" + forwarder_id;
				dif.src = "edit_forwarder.php?action=delete_booking&database_id=" + database_id;
				document.getElementsByTagName("body")[0].appendChild(dif);
				document.getElementsByTagName("body")[0].removeChild(dif);
			}

			tbody.removeChild(row);
		//}
		//else
		//{	// Only one contact left.
			//alert("At least one set of contact details is required.");
		//}
	}
}

function doCompanyDupCheck()
{
	var company = document.getElementsByName("member[company]")[0].value;
	var dif = document.createElement("IFRAME");
	dif.id = "tempif" + company;
	dif.src = "edit_member.php?action=find_company&company=" + company;
	document.getElementsByTagName("body")[0].appendChild(dif);
}
