<!-- summary.tpl -->

<?php
?>

<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectioncontractorSummary\');">Contractor Summary</a>')?>

<div id="sectioncontractorSummary">
	<table class="table_fields">
		<tr>
			<th style="width:18%;">Contractor ID :</th>
			<td style="width:32%;"><?php hecho($contractor['contractor_number']) ?></td>
			<th style="width:18%;">Date Joined :</th>
			<td style="width:32%;"><?=($contractor['created']) ? date("d/m/Y", strtotime($contractor['created'])) : 'N/A'?></td>
		</tr>
		<tr>
			<th style="width:18%;">Company Name :</th>
			<td style="width:32%;"><?php hecho($contractor['company'])?></td>
			<th style="width:18%;">Last Updated :</th>
			<td style="width:32%;">
			<?=($contractor['modified']) ? date("d/m/Y", strtotime($contractor['modified'])) : 'N/A'?>
			</td>
			
		</tr>
		<tr>
			<th style="width:18%;">Contact Person :</th>
			<td style="width:32%;"><?php echo $contractor['name']. '  '.$contractor['surname']; ?></td>
			<th style="width:18%;">Rating :</th>
			<td style="width:32%;"><?=$contractor['performance']?></td>
			
		</tr>

	
		<tr>
			<th style="width:18%;">Category :</th>
			<td style="width:32%;"><?= contractorGetOptionList('contractor_category')[$contractor['contractor_category']]?></td>
			<th style="width:18%;">Flags :</th>
			<td style="width:32%;"><?php $data = explode(',', $contractor['flag']);
				$finalstr='';
				foreach ($data as $key => $value) {
					$finalstr.= ucfirst(str_replace('_', ' ', $value)). ', ' ;
				}
				echo rtrim($finalstr, ', ');
			?></td>
			
			
		</tr>
		<tr>
			<th style="width:18%;">Cost :</th>
			<td style="width:32%;"><?= $contractor['cost'].' '.$contractor['currency']?></td>
			<th style="width:18%;">Status :</th>
			<td style="width:32%;">
			<?php
			if (0 == $contractor['status']) {
			    echo 'Inactive';
			}else {
			    echo 'Active';
			}

			?>
			</td>
			
		</tr>
	</table>
	<br /><br />
</div>

<div style="background-color:#fff;text-align:center;padding:8px;display:none;" id="sectioncontractorSummary_hideNote">contractor Summary can be viewed by Clicking "contractor Summary" above<br /></div>

<!-- /summary.tpl -->
