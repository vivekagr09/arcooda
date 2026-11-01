<!-- prevent_wanted.tpl -->

<?php
function prevent_wanted_row($count = '[ROW_COUNT]', $row_data = array())
{
	$name_key = 'prevent_wanted[' . $count . ']';
	$hidden = array($name_key . '[id]' => $row_data['id'],);
	ob_start();
?>
	<tr id="prevent_wanted_rowa[<?= $count
?>]"<?= (($count & 1) ? ' class="alt"' : '') . (($row_data['hidden']) ? ' style="display:none;"' : '') ?>>
		<td style="white-space:nowrap;">
			<?= formatHTMLInputs($hidden) ?>
			<a target="_blank" href="<?=htmlspecialchars("/webadmin/wanted/?action=edit&wanted_id=" . $row_data['id'])?>" onmouseover="<?=htmlspecialchars('tt_in(' . json_encode('<img src="' . htmlspecialchars($row_data['image_url']) . '" alt="' . htmlspecialchars($row_data['text']) . '" />') . ')')?>" onmouseout="tt_out()"><span name="<?= $name_key ?>[wanted_number]"><?= wantedFormatNumber($row_data['id']) ?></span></a>
		</td>
		<td style="width:100%;"><a target="_blank" href="<?= htmlspecialchars("/webadmin/wanted/?action=edit&wanted_id=" . $row_data['id']) ?>" onmouseover="<?=htmlspecialchars('tt_in(' . json_encode('<img src="' . htmlspecialchars($row_data['image_url']) . '" alt="' . htmlspecialchars($row_data['text']) . '" />') . ')')?>" onmouseout="tt_out()"><span name="<?= $name_key ?>[text]"><?= htmlspecialchars($row_data['text']) ?></span></a></td>
		<td><a href="#" onclick="return preventWantedDelete(<?= $count ?>)"><img src="/webadmin/images/icons/icon_remove.gif" alt="Remove" border="0" /></a></td>
	</tr>
<?php
	return ob_get_clean();
}
if (array_key_exists('prevent_wanted', $wanted))
{
?>
<script type="text/javascript">
//<![CDATA[
var prevent_wanted_count=<?= count($prevent_wanted) + 256 ?>;
function setWantedData(data)
{
	if(data.wanted_list!=null)
	{
		for(k in data.wanted_list) setWantedData(data.wanted_list[k]);
		return;
	}
	var row_id=preventWantedAddRow();
	preventWantedSetRow(row_id,data);
}
function preventWantedSort(column)
{
	var rows=[];
	var vals=[];
	$("[id^='prevent_wanted_rowa[']").each(function() {
		var row_id=$(this).attr("id").substr(20);
		rows.push(row_id);
		vals.push($("[name='prevent_wanted"+row_id+"["+column+"]']").text());
	});
	var reverse=true;
	var bubble=true;
	while(bubble)
	{
		bubble=false;
		for(var i=0;i<(rows.length-1);i++) for(var j=i+1;j<rows.length;j++) if(vals[i]>vals[j])
		{
			temp=rows[i]; rows[i]=rows[j]; rows[j]=temp;
			temp=vals[i]; vals[i]=vals[j]; vals[j]=temp;
			bubble=true;
			reverse=false;
		}
	}
	if(reverse) rows=rows.reverse();
	var table=$("#prevent_wanted_table");
	for(var i=0;i<rows.length;i++)
	{
		table.append($("[id='prevent_wanted_rowa"+rows[i]+"']"));
	}
	var count=0;
	$("[id^='prevent_wanted_rowa[']").each(function() {
		$("[name$='[rank]']",this).val(count++);
	});
	$("[id^='prevent_wanted_rowa']").removeClass("alt").filter(":odd").addClass("alt");
	return false;
}
function preventWantedDelete(row_id)
{
	$("[id='prevent_wanted_rowa["+row_id+"]']").remove();
	$("[id^='prevent_wanted_rowa']").removeClass("alt").filter(":odd").addClass("alt");
	return false;
}
function preventWantedAddRow()
{
	var row_string = <?=json_encode(prevent_wanted_row())?>;
	row_string=row_string.replace(/\[ROW_COUNT\]/g,prevent_wanted_count);
	$("#prevent_wanted_table").append(row_string);
	$("[id^='prevent_wanted_rowa']").removeClass("alt").filter(":odd").addClass("alt");
	prevent_wanted_count++;
	return prevent_wanted_count-1;
}
function preventWantedSetRow(row_id,data)
{
	$("[name='prevent_wanted["+row_id+"][id]']").val(data.id);
	$("[name='prevent_wanted["+row_id+"][wanted_number]']").text(data.id).unwrap().wrap('<a target="_blank" href="/webadmin/wanted/?action=edit&wanted_id=' + data.id + '" />');
	$("[name='prevent_wanted["+row_id+"][text]']").text(data.name).unwrap().wrap('<a target="_blank" href="/webadmin/wanted/?action=edit&wanted_id=' + data.id + '" />');
}

$(function() {
	$.getScript('/jquery/selectboxes&skipdeps.js');
	var list_storage={};
	var list_schedule=[];
	processSchedule=function() {
		for(i=0;i<list_schedule.length;i++)
		{
			var j=list_schedule[i];
			if(list_storage[j.rel]!==null)
			{
				var save_vals=$(j.target).val();
				$(j.target).empty();
				$(j.target).addOption('','');
				$(j.target).addOption(list_storage[j.rel]);
				$(j.target).val(save_vals);
				list_schedule.splice(i,1);
				i--;
			}
		}
	};
	$("select.on_demand").focus(function(event) {
		$(this).unbind("focus");
		var list_name=$(this).attr('rel');
		list_schedule.push({target:this,rel:list_name});
		if(list_storage[list_name]==null) $.getJSON('getoptionlist.json.php',{list:escape(list_name)},function(data) {
			list_storage[list_name]=data;
			processSchedule();
		});
		else processSchedule();
	});
	$('.prevent_wanted_add_button').css('display','').click(function() {
		var selected_list=[];
		$("[id^='prevent_wanted_rowa[']").each(function() {
			var row_id=$(this).attr("id").substr(20);
			selected_list.push($("[name='prevent_wanted" + row_id + "[id]']").val()+" "+$("[name='prevent_wanted"+row_id+"[sub_id]']").val());
		});
		window.open("/webadmin/wanted/?return_message=prevent_wanted&multiple=y&selected_list="+selected_list.join("|"));
		return false;
	});
});
//]]>
</script>

<?= formatHTMLTitle('Prevent these wanted from appearing on the same page', '<a href="#" class="prevent_wanted_add_button" style="display:none;color:#000;">Add Wanted(s)</a>') ?>

<div id="sectionPreventWanted">
	<table class="table_grid" id="prevent_wanted_table">
		<tr>
			<th><a href="#" onclick="return preventWantedSort('id')">ID</a></th>
			<th><a href="#" onclick="return preventWantedSort('text')">Name of Wanted</a></th>
			<th></th>
		</tr>
<?php
	$count = 0;
	$prevent_wanted_affected = array();
	$prevent_wanted = array();
	if ($wanted['prevent_wanted'] != '') $prevent_wanted = explode(',', $wanted['prevent_wanted']);
	$chunks = wantedChunk(wantedSearch(), 'prevent_wanted');
	if (is_array($chunks[$wanted['id']])) $prevent_wanted = array_merge($prevent_wanted, $chunks[$wanted['id']]);
	$prevent_wanted = array_unique($prevent_wanted);
	rsort($prevent_wanted);
	foreach($prevent_wanted as $prevent_wanted)
	{
		$prevent_wanted_affected[] = $prevent_wanted;
		$prevent_wanted = wantedFromDatabase($prevent_wanted);
		echo prevent_wanted_row($count++, $prevent_wanted, true);
	}
?>
	</table>
	<input type="hidden" name="prevent_wanted_affected" value="<?= implode(',', $prevent_wanted_affected) ?>" />
	<br />
</div>

<br />
<br />

<?php
}
?>

<!-- /prevent_wanted.tpl -->
