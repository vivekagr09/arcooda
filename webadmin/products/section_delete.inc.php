<?php
	$item= $DB->query_first("SELECT * FROM $table WHERE $id_field = '$id'");     ?>

        <center>
          <table border=0>

<?php

	for ($i = 0; $i < count($item); $i++)
	{
		$key = key($item);

		if (!is_int($key))
		{
			echo '<tr bgcolor="'.(($i/2)%2 == 0?"#EEEEEE":"#FFFFFF").'">';
			echo '<td align="right"><b>'.$key.':</b></td>';
			echo '<td>'.stripslashes($item[$key]).'</td>';
			echo '</tr>';
		}
		next($item);
	}


?>

            <tr>
              <td colspan="2">
<?php
	$statement = "SELECT COUNT(*) as total FROM products p WHERE p.type = '$id'";

	$products_of_type = $DB->query_first($statement);

	if (intval($products_of_type['total']) <= 0)
	{

?>

			  <br><br>
                <center>
	    <input type="hidden" name="id" value="<?php echo  $id ?>">
		<input type="hidden" name="action" value="do_delete">
        <input type="submit" name="add" value="Yes">
        <input type="reset" name="clear" value="No" onClick="javascript:history.back()">
                </center>
<?php } else { ?>
			  <br><br>
                <center>
				There are <?php echo  $products_of_type['total']; ?> products belonging to this product type,<br> please delete those products first before deleting  this product type. <br>
				<br>
        <input type="reset" name="clear" value="OK" onClick="javascript:history.back()">

<?php } ?>
              </td>
            </tr>
          </table>
        </center>
        <p>
