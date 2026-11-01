<?php
include_once __DIR__ . '/common.inc.php';

	//Get all the fields to update from the old database
	$result = $DB->query("SELECT product_name, type, storage, country_location, `condition`, weight, preferred_supplier, cost_price, cost_currency, sales_price, sales_currency, qty, active FROM products WHERE id ='{$_GET['id']}'");

	if ($row = $DB->fetch_array($result))
	{
		$product_name = $row['product_name'];
        $product_name = trim(addslashes($product_name));
   		$type = $row['type'];
   		$weight = $row['weight'];
   		$condition = $row['condition'];
   		$storage = $row['storage'];
   		$country_location = $row['country_location'];
   		$supplier = $row['preferred_supplier'];
   		$cost_price = $row['cost_price'];
		$cost_currency = $row['cost_currency'];
   		//$sales_price = $row['sales_price'];
		//$sales_currency = $row['sales_currency'];
   		$qty = $row['qty'];
        $active = $row['active'];

		$supplier_result=$DB->query_first("SELECT supplier_id FROM supplier_product WHERE product_id=".intval($_GET['id']));
		if($supplier_result['supplier_id']>0) $supplier=$supplier_result['supplier_id'];

		//Determine the product catagory by it's type, uses parent field in product types and assumes that the parents correspond to
		//the id's in stock_catagories
		$result = $DB->query("SELECT parent, name FROM product_types WHERE id = {$type}");
		if($row = $DB->fetch_array($result))
		{
			$catagory = $row['parent'];
		}
		else
		{
		}

		//Start printing out javascript
   		print "<script>\n";
   		//! denotes field that is temporary, * denotes a drop down that needs to be set to selected, and @ denotes a number

		//Set the product name field
   		print "window.opener.document.getElementsByName('data3[product_name]')[0].value='$product_name'\n";

        //Get the products admin notes
        //At the moment it's disabled
        $result = $DB->query("SELECT note, user, date_added, UNIX_TIMESTAMP(date_added) AS time_added FROM product_admin_notes WHERE product_id = '{$_GET['id']}'");
        //print "window.opener.removeOldNotes()\n";

		// Reset admin notes table
		print "window.opener.resetAdminNotesTable()\n";

		while($row = $DB->fetch_array($result))
        {
            $note = $row['note'];
			$note = nl2br($note);
            $note = str_replace("\n","\\n",$note);
            $note = str_replace("\r","\\n",$note);
			$note = str_replace("'", "\\'",$note);
            $userName = $row['user'];
            $date = date('d M, Y',$row['time_added']);
            //Set the admin notes note field
            print "window.opener.addOldAdminNote('$userName', '$date', '$note')\n";
        }


		//Set the selected catagory item
		print "counter = 0;\n";
        print "escape = false;\n";
		print "size = window.opener.document.getElementsByName('data3[product_catagory]')[0].options.length\n";
		print "while(counter < size && escape == false)\n";
		print "{\n";
		print "  if(window.opener.document.getElementsByName('data3[product_catagory]')[0].options[counter].value == '$catagory')\n";
		print "  {\n";
		print "    window.opener.document.getElementsByName('data3[product_catagory]')[0].selectedIndex=counter\n";
		print "    escape = true;\n";
		print "  }\n";
		print "  counter++\n";
		print "}\n";
		//End loop

		//Set the selected type item
		print "counter = 0;\n";
        print "escape = false;\n";
		print "size = window.opener.document.getElementsByName('data3[product_type]')[0].options.length\n";
		print "while(counter < size && escape == false)\n";;
		print "{\n";
		print "  if(window.opener.document.getElementsByName('data3[product_type]')[0].options[counter].value == '$type')\n";
		print "  {\n";
		print "    window.opener.document.getElementsByName('data3[product_type]')[0].selectedIndex=counter\n";
        print "    escape = true;\n";
        print "  }\n";
		print "  counter++\n";
		print "}\n";
		//End loop

		//Set the weight field
   		print "window.opener.document.getElementsByName('data3[weight]')[0].value='$weight'\n";

		//Set the selected condition item
		print "counter = 0;\n";
        print "escape = false;\n";
		print "size = window.opener.document.getElementsByName('data3[condition]')[0].options.length\n";
        print "while(counter < size && escape == false)\n";
		print "{\n";
		print "  if(window.opener.document.getElementsByName('data3[condition]')[0].options[counter].value == '$condition')\n";
		print "  {\n";
		print "    window.opener.document.getElementsByName('data3[condition]')[0].selectedIndex=counter\n";
        print "    escape = true;\n";
        print "  }\n";
		print "  counter++\n";
		print "}\n";
		//End loop

     	//Set the selected storage item
		print "counter = 0;\n";
        print "escape = false;\n";
		print "size = window.opener.document.getElementsByName('data3[storage]')[0].options.length\n";
        print "while(counter < size)\n";
		print "{\n";
		print "  if(window.opener.document.getElementsByName('data3[storage]')[0].options[counter].value == '$storage')\n";
		print "  {\n";
		print "    window.opener.document.getElementsByName('data3[storage]')[0].selectedIndex=counter\n";
        print "    escape = true;\n";
        print "  }\n";
		print "  counter++\n";
		print "}\n";


		//Set the selected location item
		print "counter = 0;\n";
        print "escape = false;\n";
		print "size = window.opener.document.getElementsByName('data3[country_location]')[0].options.length\n";
        print "while(counter < size && escape == false)\n";
		print "{\n";
		print "  if(window.opener.document.getElementsByName('data3[country_location]')[0].options[counter].value == '$country_location')\n";
		print "  {\n";
		print "    window.opener.document.getElementsByName('data3[country_location]')[0].selectedIndex=counter\n";
        print "    escape = true;\n";
        print "  }\n";
		print "  counter++\n";
		print "}\n";

   		//Set the selected supplier item
		print "counter = 0;\n";
        print "escape = false;\n";
		print "size = window.opener.document.getElementsByName('data3[supplier]')[0].options.length\n";
        print "while(counter < size && escape == false)\n";
		print "{\n";
		print "  if(window.opener.document.getElementsByName('data3[supplier]')[0].options[counter].value == $supplier)\n";
		print "  {\n";
		print "    window.opener.document.getElementsByName('data3[supplier]')[0].selectedIndex=counter\n";
        print "    escape = true;\n";
        print "  }\n";
		print "  counter++\n";
		print "}\n";

		//Set the unit price field
   		print "window.opener.document.getElementsByName('data3[unit_price]')[0].value='$cost_price'\n";//@

		//Set the selected cost currency item
		print "counter = 0;\n";
        print "escape = false;\n";
		print "size = window.opener.document.getElementsByName('data3[cost_currency]')[0].options.length\n";
        print "while(counter < size && escape == false)\n";
		print "{\n";
		print "  if(window.opener.document.getElementsByName('data3[cost_currency]')[0].options[counter].value == '$cost_currency')\n";
		print "  {\n";
		print "    window.opener.document.getElementsByName('data3[cost_currency]')[0].selectedIndex=counter\n";
        print "    escape = true;\n";
        print "  }\n";
		print "  counter++\n";
		print "}\n";
		//End loop

		//Set the sales price field
   		//print "window.opener.document.getElementsByName('data3[sales_price]')[0].value='$sales_price'\n";//@

		//Set the selected sales currency item
		//print "counter = 0;\n";
        //print "escape = false;\n";
		//print "size = window.opener.document.getElementsByName('data3[sales_currency]')[0].options.length\n";
        //print "while(counter < size && escape == false)\n";
		//print "{\n";
		//print "  if(window.opener.document.getElementsByName('data3[sales_currency]')[0].options[counter].value == '$sales_currency')\n";
		//print "  {\n";
		//print "    window.opener.document.getElementsByName('data3[sales_currency]')[0].selectedIndex=counter\n";
        //print "    escape = true;\n";
        //print "  }\n";
		//print "  counter++\n";
		//print "}\n";
		//End loop

		//Set the selected active item
		print "counter = 0;\n";
        print "escape = false;\n";
		print "size = window.opener.document.getElementsByName('data3[active]')[0].options.length\n";
        print "while(counter < size && escape == false)\n";
		print "{\n";
		print "  if(window.opener.document.getElementsByName('data3[active]')[0].options[counter].value == '$active')\n";
		print "  {\n";
		print "    window.opener.document.getElementsByName('data3[active]')[0].selectedIndex=counter\n";
        print "    escape = true;\n";
        print "  }\n";
		print "  counter++\n";
		print "}\n";
		//End loop

		//Set the quantity field
//   		print "window.opener.document.getElementsByName('data3[qty]')[0].value='$qty'\n";//@

        //Update the approx price
        print "window.opener.refreshCosts();\n";
   		//This line would Close the window
  		print "	window.close();\n";
   		print "</script>\n";

	}
	else
	{
		echo 'Could not find any products in the database with coeid: ';
		echo $_GET["id"];
		print "<script>\n";
   	    //print "window.close();\n";
   		print "</script>\n";
	}


?>
