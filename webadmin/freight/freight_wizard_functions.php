<?php
	function deleteService()
	{
		removeServiceTable();
		removeFromTables();
	}

	function removeFromTables()
	{
		global $DB;
		$prop_id = $_REQUEST["courier"];
		$statement = "DELETE FROM shipping_countries WHERE prop_id = '$prop_id';";
		$DB->query($statement);
		$statement = "DELETE FROM shipping_services WHERE prop_id = '$prop_id';";
		$DB->query($statement);
	}

	function removeServiceTable()
	{
		global $DB;
		$prop_id = $_REQUEST["courier"];
		$table_name = "shipping_";
		$table_name .= $prop_id;

		$statement = "DROP TABLE IF EXISTS $table_name;";
		$DB->query($statement);
	}
	function addServiceTable($prop_id)
	{
		global $DB;
		$zones = $GLOBALS['zoneNameArray'];

		$table_name = "shipping_";
		$table_name .= $prop_id;

		//sql statement
		$statement = "CREATE TABLE ";
		$statement .= $table_name;
		$statement .= " (ship_id int(11) NOT NULL auto_increment, shipping_weight float(10,2) NOT NULL default '0.0',";
		for ($i = 0; $i < count($zones) ; $i++)
		{
			$zone_name = $zones[$i];
			$statement .= $zone_name;
			$statement .= " float(10,2) NOT NULL default '0.0',";
			//echo $statement;
		}

		$statement .=   "PRIMARY KEY (ship_id));";
		$DB->query($statement);
	}

	function addService()
	{
		global $DB;

		$name = stripslashes($_REQUEST["name"]);
		$accountno = $_REQUEST["accountno"];
		$base_currency = $_REQUEST["base_currency"];
		$customer_discount = $_REQUEST["customer_discount"];
		$store_discount = $_REQUEST["store_discount"];
		$source = $_REQUEST["source"];
		$handling = $_REQUEST["handling"];
		$hndasperc = $_REQUEST["hndasperc"];

			$statement = "INSERT IGNORE INTO shipping_services (name, accountno, base_currency, customer_discount, store_discount, handling, hndasperc, source)";

			$statement .= " VALUES ('".addslashes($name)."',  '".$accountno."',  '".$base_currency."', '".$customer_discount."', '".$store_discount."', '".$handling."', '".$hndasperc."', '".$source."');";

			$DB->query($statement);

			$prop_id = $DB->query("SELECT * FROM shipping_services ORDER BY prop_id DESC;");
			$row = $DB->fetch_array($prop_id);
			$prop_id = $row[prop_id];
			addServiceTable($prop_id);


			$country = $DB->query("SELECT * FROM shipping_location WHERE code = '$source';");
			$row = $DB->fetch_array($country);
			$country = $row[name];
			echo "<center><b>Successfully added Service to courier list</b>";
			echo "<p>";
			echo "This procedure has setup the structure ready to place in values.";
			echo "<br>";
			echo "Please fill in the Pricing Table and the Destination Table to make it work";
			echo "<p>";
			echo "It will appear as ";
			echo $name;
			echo "	-	";
			echo $country;
			echo " in the drop down boxes</center>";

			echo "<center><p><a href='generate.php?prop_id=$prop_id'> Click here to automatically generate some weights in the Pricing Table</a> ";
			echo "</center>";
	}
	function generate($prop_id, $number, $starting, $incrementby)
	{
		global $DB;
		$statement = "DELETE FROM shipping_".$prop_id." WHERE ship_id > 0;";
		$DB->query($statement);
		for($i = 0; $i < $number; $i++)
		{
			$statement = "INSERT IGNORE INTO shipping_".$prop_id." (shipping_weight) VALUES (".$starting.");";
			$DB->query($statement);
			$starting = $starting + $incrementby;
		}
		echo "<center>".$number." values created successfully.";

		echo "<p><a href='pricing_admin.php?table=shipping_".$prop_id."&query=&name_id=".$prop_id."'> Take a look at the generated values </a>";

		echo "<p><a href='AdminMenu.php'>Click to Go Back to the Admin Menu</a></center>";

	}
?>