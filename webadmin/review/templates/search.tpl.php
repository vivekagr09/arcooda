<?php
	$rating_list = [
		5=>"5 Stars Rating",
		4=> "4 Stars Rating",
		3=> "3 Stars Rating",
		2=> "2 Stars Rating",
		1=> "1 Stars Rating"
	];

	$bw = [
		'1' => 'Best',
		'2' => 'Worst',
	];


?>
<table align="center" style="border:1px solid #FF9900;width:95%;background-color: #FFFFCC;">
	<form accept-charset="utf-8" action="" method="GET">
	<tr style="background-color: #FFFFCC;"><td colspan="2" style="text-align:center;background-color:#FFFFCC;"><h2 style="color:#FF9900;">Search Ratings</h2></td></tr>
    <tr style="background-color: #FFFFCC;">
		<td style="text-align:right;background-color:#FFFFCC;width:40%;">Browse by Category: </td>
		<td style="text-align:left;background-color:#FFFFCC;width:60%;"><select style="width:256px;" name="category">
			<option value="" selected>All</option>
			 <?=formatHTMLOptions(productGetOptionList('section'), $_GET['category'])?>
		</select></td>
	</tr>
    <tr style="background-color: #FFFFCC;">
		<td style="text-align:right;background-color:#FFFFCC;width:40%;">Product Rating: </td>
		<td style="text-align:left;background-color:#FFFFCC;width:60%;">
			<select style="width:256px;" name="rating">
				<option value="" selected>All</option>
		<?=formatHTMLOptions($rating_list,$_GET['rating'])?>
		</select></td>
	</tr>
    <tr style="background-color: #FFFFCC;">
		<td style="text-align:right;background-color:#FFFFCC;width:40%;">Best/Worst Rating: </td>
		<td style="text-align:left;background-color:#FFFFCC;width:60%;">
			<select style="width:256px;" name="ranking">
				<option value="" >All</option>
				<?=formatHTMLOptions($bw,$_GET['ranking'])?>
			</select>
		</td>
	</tr>
	<tr style="background-color: #FFFFCC;">
		<td style="text-align:right;background-color:#FFFFCC;width:40%;">Product Name/ID: </td>
		<td style="text-align:left;background-color:#FFFFCC;width:60%;"><input style="width:250px;" type="text" name="search_for" value="<?php hecho($posted['search_for']) ?>" /></td>
	</tr>
	<tr style="background-color: #FFFFCC;"><td colspan="2" style="text-align:center;background-color:#FFFFCC;"><input type="submit" value="Search" /></td></tr>
	</form>
</table>
