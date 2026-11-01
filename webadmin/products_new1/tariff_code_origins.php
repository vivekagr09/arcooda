<!-- website_page_layout.tpl -->



<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;tariff_code_origins&quot;);return false" class="">Tariff Code & Origins</a>')?>
<?php
    $harmonised_code_list   = invoiceGetOptionList('harmonised_code');
    $harmonised_code_level0 = array();
    $harmonised_code_level1 = array();
    $harmonised_code_level2 = array();
    foreach ($harmonised_code_list as $k => $v) {
        switch (strlen($k)) {
            case 2:
                $harmonised_code_level0[$k] = $k . ' - ' . $v;
                break;
            case 4:
                $harmonised_code_level1[$k] = $k . ' - ' . $v;
                break;
            default:
                $harmonised_code_level2[substr($k, 0, 2)][substr($k, 0, 4)][$k] = $k . ' - ' . $v;
                break;
        }
    }

    foreach ($harmonised_code_level2 as $k0 => $v0) {
        if (isset($harmonised_code_level0[$k0])) {
            unset($harmonised_code_level2[$k0]);
            $k0                          = $harmonised_code_level0[$k0];
            $harmonised_code_level2[$k0] = $v0;
            ksort($harmonised_code_level2);
        }
        foreach ($v0 as $k1 => $v1) {
            if (isset($harmonised_code_level1[$k1])) {
                unset($harmonised_code_level2[$k0][$k1]);
                $k1                               = $harmonised_code_level1[$k1];
                $harmonised_code_level2[$k0][$k1] = $v1;
                ksort($harmonised_code_level2[$k0]);
            }
        }
    }
    $harmonised_code_nest = $harmonised_code_level2;
    ?>
<div id="tariff_code_origins">
    <table class="wide">
    	<tr class="alt"><td style="text-align:center;"><span style="color:red;font-style:italic;font-size:8px;">Show on Invoice</span></td><td class="left_title">&nbsp;</td><td>&nbsp;</td></tr>
			<tr class="alt">
		        <td style="text-align:center;"><input type="checkbox" name="item[is_harmonised_code]" value="y" <?= $item['is_harmonised_code'] == 'y' ? 'checked' : '' ?>/></td>
		        <td class="left_title" style="text-align: right; width: 20%;">Harmonised Code :</td>
		        <td><select name="item[harmonised_code]" style="width:80%;">
		            <option value="">Not Selected</option>
		            <?=formatHTMLOptions($harmonised_code_nest, $item['harmonised_code'])?>
		        </select>
		        <a href="/webadmin/invoice/invoice_options.php?section=harmonised_code" target="_blank" class="addedit">Add / Edit</a>
		        </td>
		    </tr>
	    <tr class="alt">
	        <td style="text-align:center;"><input type="checkbox" name="item[is_origin_country]" value="y" <?= $item['is_origin_country'] == 'y' ? 'checked' : '' ?>/></td> 
	        <td class="left_title"  style="text-align: right; width: 20%;">Country Of Origin :</td>
	        <td><select name="item[origin_country]" style="width:80%;">
	            <option value="">Not Selected</option>
	            <?=formatHTMLOptions(invoiceGetOptionList('origin_country'), $item['origin_country'])?>
	        </select>
	        </td>
	    </tr>
	    <tr class="alt">
	        <td style="text-align:center;"><input type="checkbox" name="item[is_product_materials]" value="y" <?= $item['is_product_materials'] == 'y' ? 'checked' : '' ?>/></td> 
			<td class="left_title"  style="text-align: right; width: 20%;" >Materials :</td>
			<td><input type="text" name="item[product_materials]" value="<?=$item['product_materials']?>" class="intext" style="width:80%;"/></td>
		</tr>
    <tr class="alt">
        <td style="text-align:center;"><input type="checkbox" name="item[is_product_usage]" value="y" <?= $item['is_product_usage'] == 'y' ? 'checked' : '' ?>/></td> 
			<td class="left_title"  style="text-align: right; width: 20%;" >Usage :</td>
			<td><input type="text" name="item[product_usage]" value="<?=$item['product_usage']?>" class="intext" style="width:80%;"/></td>
		</tr>
</table>
</div>

<div style="color:#fff;text-align:center;padding:8px;display:none;" >
  Website Page Layout can be viewed by Clicking "Tariff Code & Origins" above
</div>

<!-- /website_page_layout.tpl -->
