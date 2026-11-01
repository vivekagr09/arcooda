<!-- machine_serial_number.tpl -->

<?php 
moduleLoad('invoice'); //invoiceFromDatabase
moduleLoad('member'); //memberFromDatabase

$section_name='productserail';
function item_serial_row($count = "[ROW_COUNT]", $row_data = array())
{
    static $alt = false;

    $section_name = 'productserail';
    $name_key = $section_name . "[" . $count . "]";
    $hidden   = array(
        $name_key . "[id]"   => $row_data["id"],
        $name_key . "[serial_id]" => $row_data["serial_id"],
        $name_key . "[product_id]" => $row_data["product_id"],
        $name_key . "[invoice_id]" => $row_data["invoice_id"],
        $name_key . "[member_id]" => $row_data["member_id"],
    );
    ob_start();
?>
	<tr id="item_serail_rowa[<?=$count?>]"<?=(($alt = !$alt) ? ' class="alt"' : "")?>>
		<td style="white-space:nowrap;">
				<?=formatHTMLInputs($hidden)?>
			<?= sprintf('SN#00%s', $row_data['serial_id']);?>
		</td>
		<td style="white-space:nowrap;" id="member_id_<?=$count?>">
		
			<?php if($row_data['member_id']){
				$member = memberFromDatabase($row_data['member_id']);
				echo "<a href='/webadmin/member/?action=edit&amp;member_id=".$row_data['member_id']."' target='_blank'>".$member['first_name'].' '.$member['last_name']."</a>";
			}?>
		</td>
		<td style="white-space:nowrap;">
			<?php if($row_data['invoice_id']){
				 $invoice = invoiceFromDatabase($row_data['invoice_id']);
				echo "<a href='/webadmin/invoice/invoice_view.php?invoice_id=".$row_data['invoice_id']."' target='_blank'>".$invoice['invoice_number']."</a>";
			}?>
		</td>
		<td style="white-space:nowrap;">
			<?= ($row_data['created'])?date('m/d/Y',strtotime($row_data['created'])):'Now'?>
		</td>
		<td>
			<a style="cursor:pointer;" onclick="return serachserialmember(<?=$count?>)">Link</a>
			<a style="cursor:pointer;" onclick="return item_serialno_delete(<?=$count?>,<?= $row_data['id'] ?>)">Remove</a>
		</td>
	</tr>
<?php
return ob_get_clean();
}

$randumnumber=rand();
?>
<script type="text/javascript">
	var currentserialno=0
	var serial_count="<?= count($serail_number)+1;?>";
	function addserial_number(){
		var row_string = <?=json_encode(item_serial_row('[SERIAL_LINKING_COUNT]', ['product_id'=>$item['id'],'serial_id'=> '[SERIAL_ID]','id'=> '[ROW_ID]']))?>;
	    	row_string = row_string.replace(/\[SERIAL_LINKING_COUNT\]/g, serial_count);
			$.ajax({
				url:     "/ajax/getNewSerialNumber.php",
				type:    "post",
				dataType: "json",
				data:    { reserve_by: "<?= $randumnumber?>" },
				success: function (serialdata) {
					var next_id=serialdata.serial_id;
					var type=serialdata.type;
					var rowid=serialdata.id;
					if(type=='new'){
						if (parseInt(currentserialno) >= next_id){
					    	next_id = parseInt(currentserialno) + 1;
					    	currentserialno=next_id;
					    } 
					}
					row_string = row_string.replace(/\[SERIAL_ID\]/g, next_id);
					row_string = row_string.replace(/\[ROW_ID\]/g, rowid);
					$("#item_serial_table").append(row_string);
				}
			});
	    serial_count++;
	}
	function item_serialno_delete(id,dataid) {
	    if (confirm("Are you sure you want to delete  this serial no ??")) {
	        var theRow = getElement("item_serail_rowa["+id+"]");
	        if (theRow) theRow.parentNode.deleteRow(theRow.sectionRowIndex);
	        if(dataid>0)
	        setValue("item_sereal_id_deleted", getValue("item_sereal_id_deleted") + "," + dataid);
	    }
	}
	function serachserialmember(retunmsg){
		
		var url = "/webadmin/member/list.php?return_message="+retunmsg
	    url += "&search_for=";
	    window.open(url);
	}
	function setMemberData(data){
		if(data.return_message){
			setValue('productserail['+data.return_message+'][member_id]',data.member_id);
			$('#member_id_'+data.return_message).html('<a href="/webadmin/member/?action=edit&amp;member_id='+data.id+'" target="_blank">'+data.first_name+' '+data.last_name+'</a>');

		}
	}

</script>
<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;machine_serial_number&quot;);return false">Machine Serial Number</a>','<a stylr="cursor:pointer;" onclick="addserial_number();">Add</a>')?>

<div id="machine_serial_number">
	<div style="text-align: left; width: 95%; margin-left: auto; margin-right: auto; color: red; font-size: 20px;"> 
		<p style="font-size: 20px;padding: 5px;">Total Units Made : <?= count($serail_number)?> </p>
		<?php 
			$units_registerd=0;
			foreach ($serail_number as $key => $data) {
				if($data['member_id']){
					$units_registerd++;
				}
			}
		?>
		<p style="font-size: 20px;padding: 5px;">Total Units Registered : <?= $units_registerd?> </p>
	</div>
    <table class="table_grid" id="item_serial_table">
		<tr>
			<th>Serial #</th>
			<th >Registered Owner</th>
			<th>Invoice</th>
			<th>Date</th>
			<th></th>
		</tr>
<?php
$count    = 0;
    $affected = array();

    foreach ($serail_number as $links) {

        echo item_serial_row($count++, $links);
    }
?>
	</table>
	<input type="hidden" name="item_sereal_id_deleted" value="" />
</div>

<div style="color:#fff;text-align:center;padding:8px;display:none;" id="sectionItem_hideNote">
   We will work on it further<br />
</div>

<!-- /machine_serial_number.tpl -->
