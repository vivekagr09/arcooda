<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;adminnotes&quot;);return false">Admin Notes</a>')?>
<div id="adminnotes">

	<table class="table_fields">
		 <tr>
            <th style="width:25%;">Admin Notes:</th>
            <td>
            	<textarea name="item[admin_notes]" rows="10"><?= $wanted['admin_notes']?></textarea>
	            
            </td>
        </tr>
         
        
	</table>
</div>