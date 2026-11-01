<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionOrderInfo\');">Order Information</a>')?>
<div id="sectionOrderInfo">

<table class="wide">
	<tr>
		<td class="left_title">Goods Needed By</td>
		<td>
			<?php 
				$needbybcolor='';
				$needbybtext='';
				if(!empty($invoice['needby_date']) && $invoice['needby_date'] != '0000-00-00 00:00:00')
				{
					if(date('Y-m-d h:i:s') < $invoice['needby_date']){
						$needbybcolor='background-color:pink;';
					}else{
						$needbybcolor='background-color:red;';
						$needbybtext='<span style="color:red;font-size:15px;">Shipping date has expired - deadline has been missed </span>';
					}
				}
			?>
			<input name="needby_date" id="needby_date" type="text" size="24" readonly="readonly" value="<?=$invoice['needby_date']?>" style="<?= $needbybcolor ?>"/>
			<input type="button" value=" V " onclick="javascript:gfPop.fPopCalendar(getElement('needby_date'));" />
			<input type="button" value="Clear" onclick="javascript:setValue('needby_date','');" />
			<div><?= $needbybtext ?></div>
		</td>
	</tr>
	<tr>
		<td class="left_title">Customer Reference Number</td>
		<td><input type="text" name="proforma_purchaseorder" value="<?=$invoice['proforma_purchaseorder']?>" class="intext" /></td>
	</tr>
</table>
<br /><br />
</div>
<div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionOrderInfo_hideNote">
Order Information can be viewed by Clicking "Order Information" above
<br />
</div>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
