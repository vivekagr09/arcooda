<?php
/* Modifed: 1/05/06 @ 9:10 AM by Jwalit Shah
/* Added Checks to make sure the ID exists. Avoids the PHP errors, caused when the ID didn't exists
/* Changed the for loop, whick used key() and next() method to foreach loop, avoids PHP errors.
*/
	show_locked_msg($table, $id);

	$item= $DB->query_first("SELECT * FROM stock WHERE $id_field = '$id'");     ?>

	<center>
	<?php if($item) { ?>
	<h2 style="color:red">Do you want to delete this Item?</h2>
	<?php }
	else {
?>
	<h2 style="color:red">The ID entered doesn't exist!!</h2>
	<?php } ?>
	<table border=0>


<?php

	$i = 0;
	if(is_array($item)) {
		foreach($item as $key=>$value)
		//for ($i = 0; $i < count($item); $i++)
		{
			//$key = key($item);

			if (!is_int($key))
			{
				echo '<tr bgcolor="'.(($i/2)%2 == 0?"#EEEEEE":"#FFFFFF").'">';
				echo '<td align="right"><b>'.$key.':</b></td>';
				echo '<td>'.stripslashes($value).'</td>';
				echo '</tr>';
			}
			$i++;
			//next($item);
		}
	}


?>

            <tr>
              <td colspan="2">
			  <br><br>
                <center>
		<?php if($item) { ?>
	    <input type="hidden" name="id" value="<?php echo  $id ?>">
		<input type="hidden" name="action" value="do_delete">
        <input type="submit" name="add" value="Yes">
        <input type="reset" name="clear" value="No" onClick="javascript:history.back()">
		<?php }?>
                </center>
              </td>
            </tr>
          </table>
        </center>
        <p>
