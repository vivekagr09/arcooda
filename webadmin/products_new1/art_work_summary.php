<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;art_work_summary&quot;);return false">Art Work Summary</a>')?>
<div id="art_work_summary">
	<table class="table_fields"  style="margin-bottom: 10px;width: 50%">
		<tr>
			<td style="width:4%; background: #eed; color: #000; font-weight: bold; text-align: right; vertical-align: middle;"></td>
			<th style="text-align: left;">Name #</th>
			<th style="text-align: center;">Cost USD</th>
			<th style="text-align: center;">Sell USD</th>
		</tr>
		<?php  
		$total_art_costprice=0;
		$total_art_sellprice=0;
		$countkey=0;
		foreach ($art_work as $data) { 
		$total_art_costprice=$total_art_costprice+$data['price_usd'];
		$total_art_sellprice=$total_art_sellprice+$data['sell_price'];          ?>
		<tr <?=(($countkey & 1) ? ' class="alt"' : "")?>>
			<td style="width:20px;"> <?= $data['id']?></td>
	        <td> <?= $data['name']?></td>
	        <td style="text-align: center;">
	        	<?= $data['price_usd']?>
	        </td>
	        <td style="text-align: center;">
	        	<?= $data['sell_price']?>
	        </td>
	    </tr>
	<?php $countkey++; } ?>
		<tr>
			<td></td>
	        <td style="text-align: right;"> <b>Total:</b></td>
	        <td style="text-align: center;">
	        	<?= $total_art_costprice ?>
	        </td>
	        <td style="text-align: center;">
	        	<?= $total_art_sellprice ?>
	        </td>
	    </tr>
	</table>
</div>