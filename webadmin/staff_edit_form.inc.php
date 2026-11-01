<?php

$item = $DB->query_first("SELECT * FROM $table WHERE $id_field = '$id'");
$sig  = $DB->query_first("SELECT * FROM sigs WHERE user_name = '" . $item['user'] . "'");
?>

<center>
	<table border="0" cellpadding="2">

		<tr><td colspan="2"><?=$error_msg;?>&nbsp;</td></tr>

		<tr><td align="center"><h4>Details For : <?=$item['user'];?></h4></td></tr>

<?php
for ($i = 0; $i < count($fields); $i++) {
    $key = key($fields);
    $field_name = ucwords(str_replace('_', ' ', $key));
    //make sure not to include editing of payrate by users
    if (has_permission('USERS', $allowed_sections)) {
        if ('company_id' == $key) {
?>
		<tr>
			<td><b>Company</b></td>
			<td>
				<input name="field_data[company_id]" type="radio" value=""<?=(0 == $item[$key]) ? ' checked="checked"' : '';?> /> None
				<input name="field_data[company_id]" type="radio" value="3"<?=(3 == $item[$key]) ? ' checked="checked"' : '';?> /> Hong Kong
				<input name="field_data[company_id]" type="radio" value="15"<?=(15 == $item[$key]) ? ' checked="checked"' : '';?> /> China
			</td>
		</tr>
<?php
} elseif ('job_type' == $key) {
?>
		<tr>
			<td><b>Commission</b></td>
			<td>
				<input name="field_data[job_type]" type="radio" value=""<?=('' == $item[$key]) ? ' checked="checked"' : '';?> /> None
				<input name="field_data[job_type]" type="radio" value="salesman"<?=('salesman' == $item[$key]) ? ' checked="checked"' : '';?> /> Salesman
				<input name="field_data[job_type]" type="radio" value="supply"<?=('supply' == $item[$key]) ? ' checked="checked"' : '';?> /> Supply
				<input name="field_data[job_type]" type="radio" value="support"<?=('support' == $item[$key]) ? ' checked="checked"' : '';?> /> Support
			</td>
		</tr>
<?php
} elseif ($key != $id_field && "user" != $key && "date_added" != $key) {
?>
		<tr>
			<td><b><?=$field_name;?>:</b></td>
			<td><input type="text" name="field_data[<?=$key;?>]" size="20" value="<?=$item[$key];?>" style="width:400px;" /></td>
		</tr>
<?php
}
    } else {
        if ('company_id' == $key) {
        } elseif ('job_type' == $key) {
        } elseif ($key != $id_field && "user" != $key && "date_added" != $key) {
?>
		<tr>
			<td><b><?=$field_name;?>:</b></td>
			<td><input type="text" name="field_data[<?=$key;?>]" size="20" value="<?=$item[$key];?>" style="width:400px;" /></td>
		</tr>
<?php
}
    }
    next($fields);
}
reset($fields);

if (has_permission('USERS', $allowed_sections)) {
    $product_types = [];
    $types_checked = [];

    $sort_by    = [1 => 3, 1, 2, 4];
    $order_list = [];
    foreach ($product_types as $type_id => $value) {
        if (isset($sort_by[$type_id])) {
            $order_list[$type_id] = $sort_by[$type_id];
        } else {
            $order_list[$type_id] = 5;
        }
    }

    asort($order_list);
}
?>

		<tr>
			<td><b>Current Signature:</b></td>
			<td><input type="text" name="old_signature" id="old_signature" size="20" value="<?=$sig['sig_image'];?>" style="width:300px" readonly="readonly" /><input type="button" value="Remove" onclick="javascript:this.form.old_signature.value='';" style="width:100px;" /></td>
		</tr>

		<tr>
			<td><b>New Signature:</b></td>
			<td>
				<input type="hidden" name="MAX_FILE_SIZE" value="<?=htmlspecialchars(convertSize(ini_get("upload_max_filesize")))?>" />
				<input type="file" name="new_signature" size="20" />
			</td>
		</tr>

		<tr>
			<td colspan="2">
				<center>
					<input type="hidden" name="id" value="<?=$id;?>" />
					<input type="hidden" name="action" value="do_edit" />
					<input type="submit" name="add" value="Update" />
					<input type="reset" name="clear" value="Clear" />
				</center>
			</td>
		</tr>
	</table>
</center>
<p>
