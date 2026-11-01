<!-- status.tpl -->
<?= formatHTMLTitle('Contractor Details') ?>
<?php $contractor_type=explode(',',$contractor['flag']);?>
<table class="table_fields">
    <tr>
        <th>Service Performance/10 :</th>
        <td><select name="contractor[performance]">
            <?= formatHTMLOptions(contractorGetOptionList('performance'), $contractor['performance']) ?>
        </select></td>
    </tr>
    <tr>
        <th>Category (<small><i><a href="options.php?section=contractor_category" target="_blank">Add</a></i></small>) :</th>
        <td><select name="contractor[contractor_category]">
            <?= formatHTMLOptions(contractorGetOptionList('contractor_category'), $contractor['contractor_category']) ?>
        </select></td>
    </tr>
     <tr>
        <th>Cost :</th>
        <td>
            <input class="intext" type="number" min="0" name="contractor[cost]" value="<?php hecho($contractor['cost'])?>" style="width:20%"/>
            <select name="contractor[currency]" style="width:20%">
            <?= formatHTMLOptions(contractorGetOptionList('currency'), $contractor['currency']) ?>
            </select>
            <input class="intext" type="text"  name="contractor[comments]" value="<?php hecho($contractor['comments'])?>" style="width:56%;" placeholder="comments"/> 
        </td> 
    </tr>
    <tr>
        <th></th>
        <td>
            <input  type="checkbox"  name="contractor[tax]" value="y" <?= ($contractor['tax']=='y')?'checked':''?>/> Above price includes tax
            
        </td> 
    </tr>
    <tr>
        <th>Flags :</th>
        <td>
			<input type="checkbox" id="unreliable" name="contractor[flag][]" value="unreliable" <?= (in_array('unreliable', $contractor_type)) ? ' checked="checked"' : '' ?> /> <label for="unreliable">Unreliable</label>&nbsp;&nbsp;
			<input type="checkbox" id="expensive" name="contractor[flag][]" value="expensive"<?= (in_array('expensive', $contractor_type)) ? ' checked="checked"' : '' ?> /> <label for="expensive">Expensive</label>&nbsp;&nbsp;
			<input type="checkbox" id="bad_experience" name="contractor[flag][]" value="bad_experience"<?= (in_array('bad_experience', $contractor_type)) ? ' checked="checked"' : '' ?> /> <label for="bad_experience">Bad Experience</label>&nbsp;&nbsp;
			<input type="checkbox" id="supplier_section" name="contractor[flag][]" value="supplier_section"<?= (in_array('supplier_section', $contractor_type)) ? ' checked="checked"' : '' ?> /> <label for="supplier_section">Supplier Section</label>&nbsp;&nbsp;
		</td>
</table>
<br /><br />

<!-- /status.tpl -->
