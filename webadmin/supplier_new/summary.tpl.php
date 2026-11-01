<!-- summary.tpl -->

<?php
?>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionMemberSummary\');">Supplier Summary</a>')?>

<div id="sectionMemberSummary">
	<table class="table_fields">
		<tr>
			<th style="width:18%;">Supplier ID :</th>
			<td style="width:32%;"><?php hecho($supplier['supplier_number']) ?></td>
			<th style="width:18%;">Last Purchase :</th>
			<td style="width:32%;"><?=$supplier['last_invoice_date']?></td>
		</tr>
		<tr>
			<th style="width:18%;">Company Name :</th>
			<td style="width:32%;"><?php hecho($supplier['company'])?> / <?php hecho($supplier['company_chinese']) ?></td>
			<th style="width:18%;">Total Purchase :</th>
			<td style="width:32%;"><a href="/webadmin/purchase_order/?action=list&amp;status=proforma|sales|processed|return|completed&amp;supplier_id=<?=$supplier_id?>" target="_blank"><?=$supplier['total_orders']?></a></td>
		</tr>
		<tr>
			<th style="width:18%;">Contact Person :</th>
			<td style="width:32%;"><?php hecho($supplier['full_name'])?> / <?php hecho($supplier['name_chinese'])?> <?php hecho($supplier['surname_chinese']) ?></td>
			<th style="width:18%;">Last Inquiry :</th>
			<td style="width:32%;"><?=$supplier['last_inquiry_date']?></td>
		</tr>
		<tr>
			<th style="width:18%;">Supplier to :</th>
			<td style="width:32%;">
			<?php
			foreach (explode(',', $supplier['supplier_type']) as $site_id) {
			    if ('' != $site_id) {
			?>	
							<img src="<?php hecho('/webadmin/images/sites/site_' . $site_id . '/logo_16.ico')?>" alt="<?php hecho($site_id)?>" />
			<?php
				}
			}
			?>
			</td>
			<th style="width:18%;">Total Inquiries :</th>
			<td style="width:32%;"><a href="/webadmin/purchase_order/?action=list&amp;status=inquiry|enquiry|closed|cancelled&amp;supplier_id=<?=$supplier_id?>" target="_blank"><?= $supplier['total_inquiries'] ?></a></td>
		</tr>
		<tr>
			<th style="width:18%;">Date Joined :</th>
			<td style="width:32%;"><?=(0 != $supplier['created_time']) ? date("d/m/Y", $supplier['created_time']) : 'N/A'?></td>
			<th style="width:18%;">Open Invoices :</th>
			<td style="width:32%;">
				<a href="/webadmin/purchase_order/?action=list&amp;status=sales|proforma|return&amp;supplier_id=<?=$supplier_id?>" target="_blank"><?= $supplier['open_invoices'] ?>
			</td>
		</tr>

		<tr>
				<th style="width:18%;">Status :</th>
			<td style="width:32%;">
			<?php
			if ('' == $supplier['site_id']) {
			    echo 'Inactive';
			} else{
			    echo 'Active';
			} 

			?>
						</td>
			<th style="width:18%;">Purchase Total :</th>
			<td style="width:32%;"><?=$supplier['invoice_purchase_total']?></td>
			
					</tr>
	</table>
	<br /><br />
</div>

<div style="background-color:#fff;text-align:center;padding:8px;display:none;" id="sectionMemberSummary_hideNote">Member Summary can be viewed by Clicking "Member Summary" above<br /></div>

<!-- /summary.tpl -->
