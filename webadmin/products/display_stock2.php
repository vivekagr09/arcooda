<?php
include_once __DIR__ . '/common.inc.php';
?>
<!-- This file retrieves information from a specific stock item from the database
     and displays it's information in the calling page(edit_stock_form.php).
-->
<?php

    //$id is POSTed to the page
    $id = $_GET['id'];
	//Get all the fields to update from the old database
	$result = $DB->query("SELECT * FROM stock WHERE id ='$id'");

	if ($row = $DB->fetch_array($result))
	{
        $company_stock = $row['company_stock'];
        $cid = $row['coe_id'];
		$product_name = $row['product_name'];
        //$product_name = trim(addslashes($product_name));
        $product_catagory = $row['product_catagory'];
        $date_received = $row['date_received'];
        //Convert the date from the internal Y-m-d format to display in d-m-Y format
		//if($date_received != "0000-0-00")
        $date_received = date("d-m-Y", strtotime($date_received));

        //$markup = $row['markup'];
        $samplestock = $row['samplestock'];
        $active = $row['active'];
        //$price_flux = $row['price_flux'];
   		$type = $row['product_type'];
   		$weight = $row['weight'];
		$total_weight = $row['total_weight'];
   		$condition = $row['condition'];
   		$storage = $row['storage'];
   		$country_location = $row['country_location'];
   		$supplier = $row['supplier'];
   		$unit_price = $row['unit_price'];
		$cost_currency = $row['cost_currency'];
   		//$sales_price = $row['sales_price'];
		//$sales_currency = $row['sales_currency'];
		$customer_name=$row['customer_name'];
		$invoice_ref=$row['invoice_ref'];
		$qty = $row['qty'];
        $active = $row['active'];
		//new fields
		$floor_location = $row['floor_location'];
		$product_details = $row['product_details']; //Add in code to enter this later
		$product_details = str_replace("\n","\\n",$product_details);
        $product_details = str_replace("\r","\\n",$product_details);
		$stock_cod = $row['stock_cod'];
		$purchase_no = $row['purchase_no'];
		$sample_qty = $row['sample_qty'];
		$stock_qty = $row['stock_qty'];
		$order_qty = $row['order_qty'];

		//Update to read from products table
		$new_result=$DB->Query(
			" SELECT".
			"	p.weight,".
			"	p.cost_price,".
			"	p.cost_currency".
			" FROM products p".
			" WHERE id=".intval($cid)
		);
		if($new_row=$DB->fetch_array($result))
		{
			$weight=$row['weight'];
			$unit_price=$row['cost_price'];
			$cost_currency=$row['cost_currency'];
		}
		$supplier_result=$DB->query_first("SELECT supplier_id FROM supplier_product WHERE product_id=".intval($cid));
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

		//Add delay to wait till parent form loads
//		print "while(document.getElementsByName('data3[active]').length==0) doEvents();";

		//Set the Purchase Order Number
        print "document.getElementsByName('data3[purchase_no]')[0].value='$purchase_no'\n";
		//print "document.write('Purchase order no set<BR>')\n";

		//Set the stock COD
        print "document.getElementsByName('data3[stock_cod]')[0].value='$stock_cod'\n";
		//print "document.write('Stock COD set<BR>')\n";

		//Set the Product Details
        print "document.getElementsByName('data3[product_details]')[0].value='$product_details'\n";
		//print "document.write('Product Details set<BR>')\n";

		//Set the floor location
        print "document.getElementsByName('data3[floor_location]')[0].value='$floor_location'\n";
		//print "document.write('Floor location set<BR>')\n";

        //Set the COE ID
        print "document.getElementsByName('data3[coe_id]')[0].value='$cid'\n";
		//print "document.write('COE id set<BR>')\n";

		//Set the product name field
   		print "document.getElementsByName('data3[product_name]')[0].value='$product_name'\n";
		//print "document.write('Product name set<BR>')\n";

        //Get the products admin notes
        $result = $DB->query("SELECT note, user, date_added, UNIX_TIMESTAMP(date_added) AS time_added FROM stock_admin_notes WHERE stock_id = '$id'");
        print "removeOldNotes()\n";
        while($row = $DB->fetch_array($result))
        {
            $note = $row['note'];
			$note = nl2br($note);
			$note = str_replace("\n","\\n",$note);
            $note = str_replace("\r","\\n",$note);
            $userName = $row['user'];
            $date = date('d M, Y',$row['time_added']);
            //Set the admin notes note field
            print "addOldAdminNote('$userName', '$date', '$note')\n";
        }
		print "document.write('Admin notes set<BR>')\n";

        //Get the product categories/quantities
        $result = $DB->query("SELECT colour, quantity FROM stock_colour_qty WHERE stock_id='$id'");
        print "deleteAllColourQty();\n";
        while($row=$DB->fetch_array($result))
        {
            //Add the colour
            print "addColourQty('".$row['colour']."', '".$row['quantity']."');\n";
        }
		print "document.write('Colour Quantities Set<br />');\n";

		//Get the product dimensions quantities
        $result = $DB->query("SELECT dimensions, qty FROM stock_dimensions_qty WHERE stock_id='$id'");
        print "deleteAllProductSize();\n";
        while($row=$DB->fetch_array($result))
        {
            //Add the colour
            print "addProductSize('".$row['dimensions']."', '".$row['qty']."');\n";
        }
		print "document.write('Dimensions Quantities Set<br />');\n";

		//Set the selected catagory item
		print "counter = 0;\n";
        print "escape = false;\n";
		print "size = document.getElementsByName('data3[product_catagory]')[0].options.length\n";
        print "while(counter < size && escape == false)\n";
		print "{\n";
		print "  if(document.getElementsByName('data3[product_catagory]')[0].options[counter].value == $product_catagory)\n";
		print "  {\n";
		print "    document.getElementsByName('data3[product_catagory]')[0].selectedIndex=counter\n";
        print "    escape = true;\n";
		print "  }\n";
		print "  counter++\n";
		print "}\n";
		//End loop

		//print "document.write('Catagory set<BR>')\n";

		//Set the selected type item
		print "counter = 0;\n";
        print "escape = false;\n";
		print "size = document.getElementsByName('data3[product_type]')[0].options.length\n";
        print "while(counter < size && escape == false)\n";;
		print "{\n";
		print "  if(document.getElementsByName('data3[product_type]')[0].options[counter].value == $type)\n";
		print "  {\n";
		print "    document.getElementsByName('data3[product_type]')[0].selectedIndex=counter\n";
        print "    escape = true;\n";
        print "  }\n";
		print "  counter++\n";
		print "}\n";
		//End loop

		//Set the weight field
   		print "document.getElementsByName('data3[weight]')[0].value='$weight'\n";

		//Set the total weight field
   		print "document.getElementsByName('data3[total_weight]')[0].value='$total_weight'\n";

		//Set the selected condition item
		print "counter = 0;\n";
        print "escape = false;\n";
		print "size = document.getElementsByName('data3[condition]')[0].options.length\n";
        print "while(counter < size && escape == false)\n";
		print "{\n";
		print "  if(document.getElementsByName('data3[condition]')[0].options[counter].value == $condition)\n";
		print "  {\n";
		print "    document.getElementsByName('data3[condition]')[0].selectedIndex=counter\n";
        print "    escape = true;\n";
        print "  }\n";
		print "  counter++\n";
		print "}\n";
		//End loop

     	//Set the selected storage item
		print "counter = 0;\n";
        print "escape = false;\n";
		print "size = document.getElementsByName('data3[storage]')[0].options.length\n";
        print "while(counter < size && escape == false)\n";
		print "{\n";
		print "  if(document.getElementsByName('data3[storage]')[0].options[counter].value == $storage)\n";
		print "  {\n";
		print "    document.getElementsByName('data3[storage]')[0].selectedIndex=counter\n";
        print "    escape = true;\n";
        print "  }\n";
		print "  counter++\n";
		print "}\n";


		//Set the selected location item
		print "counter = 0;\n";
        print "escape = false;\n";
		print "size = document.getElementsByName('data3[country_location]')[0].options.length\n";
        print "while(counter < size && escape == false)\n";
		print "{\n";
		print "  if(document.getElementsByName('data3[country_location]')[0].options[counter].value == $country_location)\n";
		print "  {\n";
		print "    document.getElementsByName('data3[country_location]')[0].selectedIndex=counter\n";
        print "    escape = true;\n";
        print "  }\n";
		print "  counter++\n";
		print "}\n";

   		//Set the selected supplier item
		print "counter = 0;\n";
        print "escape = false;\n";
		print "size = document.getElementsByName('data3[supplier]')[0].options.length\n";
        print "while(counter < size && escape == false)\n";
		print "{\n";
		print "  if(document.getElementsByName('data3[supplier]')[0].options[counter].value == $supplier)\n";
		print "  {\n";
		print "    document.getElementsByName('data3[supplier]')[0].selectedIndex=counter\n";
        print "    escape = true;\n";
        print "  }\n";
		print "  counter++\n";
		print "}\n";

		//Set the cost price field
   		print "document.getElementsByName('data3[unit_price]')[0].value='$unit_price'\n";//@

		//Set the selected cost currency item
		print "counter = 0;\n";
        print "escape = false;\n";
		print "size = document.getElementsByName('data3[cost_currency]')[0].options.length\n";
        print "while(counter < size && escape == false)\n";
		print "{\n";
		print "  if(document.getElementsByName('data3[cost_currency]')[0].options[counter].value == '$cost_currency')\n";
		print "  {\n";
		print "    document.getElementsByName('data3[cost_currency]')[0].selectedIndex=counter\n";
        print "    escape = true;\n";
        print "  }\n";
		print "  counter++\n";
		print "}\n";
		//End loop

   		print "document.getElementsByName('data3[customer_name]')[0].value='$customer_name'\n";//@
   		print "document.getElementsByName('data3[invoice_ref]')[0].value='$invoice_ref'\n";//@

		//Set the quantity field
   		//print "document.getElementsByName('data3[qty]')[0].value='$qty'\n";//@

        //Set the selected company stock item
		print "counter = 0;\n";
        print "escape = false;\n";
		print "size = document.getElementsByName('data3[company_stock]')[0].options.length\n";
        print "while(counter < size && escape == false)\n";
		print "{\n";
		print "  if(document.getElementsByName('data3[company_stock]')[0].options[counter].value == '$company_stock')\n";
		print "  {\n";
		print "    document.getElementsByName('data3[company_stock]')[0].selectedIndex=counter\n";
        print "    escape = true;\n";
        print "  }\n";
		print "  counter++\n";
		print "}\n";
		//End loop

        //Set the date received value
        print "document.getElementsByName('data3[date_received]')[0].value='$date_received'\n";

        //Set the sample qty
		print "document.getElementsByName('data3[sample_qty]')[0].value='$sample_qty'\n";
		//Set the stock qty
		print "document.getElementsByName('data3[stock_qty]')[0].value='$stock_qty'\n";
		//Set the order qty
		print "document.getElementsByName('data3[order_qty]')[0].value='$order_qty'\n";

        //Set the selected active item
		print "counter = 0;\n";
        print "escape = false;\n";
		print "size = document.getElementsByName('data3[active]')[0].options.length\n";
        print "while(counter < size && escape == false)\n";
		print "{\n";
		print "  if(document.getElementsByName('data3[active]')[0].options[counter].value == '$active')\n";
		print "  {\n";
		print "    document.getElementsByName('data3[active]')[0].selectedIndex=counter\n";
        print "    escape = true;\n";
        print "  }\n";
		print "  counter++\n";
		print "}\n";
		//End loop

        //Set the selected price flux item
		//print "counter = 0;\n";
        //print "escape = false;\n";
		//print "size = document.getElementsByName('data3[price_flux]')[0].options.length\n";
        //print "while(counter < size && escape == false)\n";
		//print "{\n";
		//print "  if(document.getElementsByName('data3[price_flux]')[0].options[counter].value == '$price_flux')\n";
		//print "  {\n";
		//print "    document.getElementsByName('data3[price_flux]')[0].selectedIndex=counter\n";
        //print "    escape = true;\n";
        //print "  }\n";
		//print "  counter++\n";
		//print "}\n";
		//End loop

        //Set the selected active item
		print "counter = 0;\n";
        print "escape = false;\n";
		print "size = document.getElementsByName('data3[active]')[0].options.length\n";
        print "while(counter < size && escape == false)\n";
		print "{\n";
		print "  if(document.getElementsByName('data3[active]')[0].options[counter].value == '$active')\n";
		print "  {\n";
		print "    document.getElementsByName('data3[active]')[0].selectedIndex=counter\n";
        print "    escape = true;\n";
        print "  }\n";
		print "  counter++\n";
		print "}\n";
		//End loop

        //Update the approx price
        print "refreshCosts();\n";

   		//This line would Close the window
//   		print "	window.close();";
   		print "</script>\n";
		mysqli_close();
	}
	else
	{
		echo 'Could not find any entries in the database with id: ';
		echo $_GET["id"];
		print "<script>\n";
   		//print "	window.close()";
   		print "</script>\n";
		mysqli_close();
	}
?>
