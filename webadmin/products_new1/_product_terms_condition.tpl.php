<!-- _product_terms_condition.tpl -->

<?php
/**
 * Terms and Condition Template
 *
 * Inputs:
 * - $custom_terms_condition
 */

$terms_condition = productGetOptionList('custom_terms_condition');
?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;sectionMessage&quot;);return false">Custom Terms & Condition</a>')?>
<script type="text/javascript">
    $(document).ready(function(){
        $("input[name='item[terms_condition_default]']").click(function(){
            if($(this).val()==0){
                $('#terms_condition_custom_div').show();
            }else{
                $('#terms_condition_custom_div').val('');
                $('#terms_condition_custom_div').hide();
            }
        });
    })
   
</script>
<div id="sectionMessage">
    <table class="table_fields">
        <!-- <tr>
            <th>Terms & Condition - Custom (<small><i><a href="/webadmin/custom-terms-condition/add.php" target="_blank">Add</a></i></small>)</th>
            <td><select name="item[terms_condition_custom]">
                <option value=""></option>
                <?=formatHTMLOptions($terms_condition,$item['terms_condition_custom'])?>
            </select></td>
        </tr> -->
        <tr>
			<th>Terms & Condition - Custom (<small><i><a href="/webadmin/custom-terms-condition/add.php" target="_blank">Add</a></i></small>)</th>
			<td>
				<input type="radio" id="terms_condition_default" name="item[terms_condition_default]" value="1" <?=("1" == $item['terms_condition_default'] || $item['terms_condition_default']==NULL) ? 'checked' : ''?>>
				<label for="terms_condition_default">Standard Terms & Condition <a href="/webadmin/options/terms_condition.php" target="_blank" style="text-decoration:none;"><small style="color:#f00;margin-left:10px;font-weight:600;"><i>(view)</i></small></a></label>
			</td>
        </tr>
        <tr>
            <th></th>
            <td style="display: flex;">
                <input type="radio" id="terms_condition_custom" name="item[terms_condition_default]" value="0" <?=("0" == $item['terms_condition_default']) ? 'checked' : ''?>>
                <label for="terms_condition_custom" style="margin-left:4px;">Custom Terms & Condition</label>
    				<select name="item[terms_condition_custom]" style="width:50%;margin-left:10px;display:<?=("0" == $item['terms_condition_default']) ? 'block' : 'none'?>" id="terms_condition_custom_div">
                        <option value=""></option>
                        <?=formatHTMLOptions($terms_condition,$item['terms_condition_custom'])?>
                    </select>
            </td>
        </tr>
    </table>
    <br /><br />
</div>

<!-- /_product_terms_condition.tpl -->
