<br><br>
	<table align="center" cellpadding="3" cellspacing="1" width="60%">
		<tr>
			<td class="left" width="40%">
				Full Name:
			</td>
			<td class="right" width="60%">
				<input type="text" name="name" style="width:190px" tooltip="First off we need a name for the service, this is the name that a customer will see.">
			</td>
		</tr>
		<tr>
			<td class="left" width="40%">
				Account Number:
			</td>
			<td class="right" width="60%">
				<input type="text" name="accountno" style="width:190px" tooltip="Enter the account number for this service">
			</td>
		</tr>
		<tr>
			<td class="left">
				Base Currency:
			</td>
			<td class="right">
					<select name="base_currency" style="width:190px" tooltip="This allows the program to know in which currency the pricing information was given and therefore do coversions and so forth">
						<?=formatHTMLOptions(getCurrencyList(false), 'USD')?>
						</select>
			</td>
		</tr>
		<tr>
			<td class="left">
				Discounts to Customer:
			</td>
			<td class="right">
				<input type="text" name="customer_discount" size="3" value="0" tooltip="Enter in any discounts that are apply to the customers price eg. '30' = 30% OR '-30' = '-30%'">%
			</td>
		</tr>
		<tr>
			<td class="left">
				Our Discounts:
			</td>
			<td class="right">
				<input type="text" name="store_discount" size="3" value="0" tooltip="Enter in any discounts that apply to the store price eg. '40' = 40%  OR '-40' = '-40%'">%
			</td>
		</tr>
		<tr>
			<td class="left">
				Handling:
			</td>
			<td class="right">
				<input type="text" name="handling" size="3" value="0" tooltip="This value is added or subtracted from the customer price">
			</td>
		</tr>
		<tr>
			<td class="left">
				Handling as a percentage:
			</td>
			<td class="right">
				<input type="text" name="hndasperc" size="3" value="0" tooltip="This value is a percentage increase of decrease to the customer price">%
			</td>
		</tr>
		<tr>
			<td class="left">
				Source:
			</td>
			<td class="right">
					<select name="source" style="width:190px" tooltip="Select the location where the service picks up the goods, this allows the system to distinguish between having Fedex out of Newcastle and Fedex out of Hong Kong.
">
<?php
							$result = $DB->query("SELECT * FROM shipping_location ORDER BY name ASC");
							while ($row = $DB->fetch_array($result))
							{
									echo "<option value='";
									echo $row[code];
									echo "'>";
									echo $row[name];
									echo "</option>";
							}
?>
					</select>
			</td>
		</tr>
		<tr>
			<td class="left">
				Number of Zones:
			</td>
			<td class="right">
				<input type="text" name="numZones" size="3" tooltip="This part is critical to the operation of the Freight Calculator, without which it could not identify which country belongs to which zone, Think carefully how many different zones there are typically there are between 6-9, this can be usually be found on the pricing sheet and appears as something like Zone A, Zone B, Zone C. In this case it would be 3">
			</td>
		</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<td colspan='2' align='center'>
			<input type="hidden" name="step" value="1">
			<input type="submit" name="Submit" value="Next" tooltip='This button takes us to the next step in adding a service'>
		</td>
	</tr>
	</table>