<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
<style>
    .highlight-option {
        color: red;
        font-weight: bold;
    }
</style>
<?php
    $harmonised_code_list   = invoiceGetOptionList('harmonised_code_with_type');
    $harmonised_code_level0 = array();
    $harmonised_code_level1 = array();
    $harmonised_code_level2 = array();
    foreach ($harmonised_code_list as $k => $v) {
        switch (strlen($k)) {
            case 2:
                $harmonised_code_level0[$k] = $v['product_type']. ' '. $k . ' - ' . $v['title'];
                break;
            case 4:
                $harmonised_code_level1[$k] = $v['product_type']. ' '. $k . ' - ' . $v['title'];
                break;
            default:
                $harmonised_code_level2[substr($k, 0, 2)][substr($k, 0, 4)][$k] = (($v['product_type'] != null)?('<span style="color:red">'.$v['product_type']. '</span> '):''). $k . ' - ' . $v['title'];
                break;
        }
    }
    foreach ($harmonised_code_level2 as $k0 => $v0) {
        if (isset($harmonised_code_level0[$k0])) {
            unset($harmonised_code_level2[$k0]);
            $k0  = $harmonised_code_level0[$k0];
            $harmonised_code_level2[$k0] = $v0;
            ksort($harmonised_code_level2);
        }
        foreach ($v0 as $k1 => $v1) {
            if (isset($harmonised_code_level1[$k1])) {
                unset($harmonised_code_level2[$k0][$k1]);
                $k1 = $harmonised_code_level1[$k1];
                $harmonised_code_level2[$k0][$k1] = $v1;
                ksort($harmonised_code_level2[$k0]);
            }
        }
    }
    $harmonised_code_nest = $harmonised_code_level2;
    ?>
<?=formatHTMLTitle('<a href="javascript:toggleSection(\'sectionAdditional\');">Additional Details</a>')?>

<div id="sectionAdditional">
<table class="wide">
	<tr class="alt">
		<td style="width:10%;">&nbsp;</td>    
		<td class="left_title" style="width:25%">Gross Weight (kgs) :</td>
		<td><input style="width:98%;" type="text" name="ship_weight" onchange="getElement('ship_tail_gate_tr').style.display=(Number(this.value)>25)?'':'none'" value="<?=sprintf('%.2f', $sinvoice['ship_weight'])?>"/></td>
	</tr>
	<tr class="alt">
		<td style="width:10%;">&nbsp;</td>    
		<td class="left_title">Gross Weight (cm<sup>3</sup>) :</td>
		<td><input style="width:98%;" type="text" name="ship_cubic" value="<?=sprintf('%.2f', $sinvoice['ship_cubic'])?>"/></td>
	</tr>
	<tr class="alt">
		<td style="width:10%;">&nbsp;</td>    
		<td class="left_title">Order Reference :</td>
		<td><input style="width:98%;" type="text" name="proforma_purchaseorder" value="<?=$sinvoice['proforma_purchaseorder']?>"/></td>
	</tr>
	<tr class="alt">
		<td style="width:10%;">&nbsp;</td>    
		<td class="left_title">Invoice Comments/Remarks :</td>
		<td><textarea name="invoice_comments" rows="<?=max(2, count(explode("\n", $sinvoice['invoice_comments'])))?>" onkeyup="javascript:textareaAutoSize(this,1,2);" style="width:98%;"><?=htmlspecialchars($sinvoice['invoice_comments'])?></textarea></td>
	</tr>
	<tr class="alt"><td style="text-align:center;"><span style="color:red;font-style:italic;font-size:8px;">Show on Invoice</span></td><td class="left_title">&nbsp;</td><td>&nbsp;</td></tr>
	<tr class="alt">
        <td style="text-align:center;"><input type="checkbox" name="is_harmonised_code" value="y" <?= $sinvoice['is_harmonised_code'] == 'y' ? 'checked' : '' ?>/></td>
        <td class="left_title">Harmonised Code <?= $sinvoice['harmonised_code'] ?? $harmonisedCode ?></td>
        <td><select name="harmonised_code" style="width:80%;">
            <option value="">Not Selected</option>
            <?=formatHTMLOptions($harmonised_code_nest, !empty(trim($sinvoice['harmonised_code'] ?? '')) ? $sinvoice['harmonised_code'] : $harmonisedCode )?>
        </select>
        <a href="/webadmin/invoice/invoice_options.php?section=harmonised_code" target="_blank" class="addedit">Add / Edit</a>
        </td>
    </tr>
    <tr class="alt">
        <td style="text-align:center;"><input type="checkbox" name="is_origin_country" value="y" <?= $sinvoice['is_origin_country'] == 'y' ? 'checked' : '' ?>/></td> 
        <td class="left_title">Country Of Origin</td>
        <td><select name="origin_country" style="width:80%;">
            <option value="">Not Selected</option>
            <?=formatHTMLOptions(invoiceGetOptionList('origin_country'), !empty(trim($sinvoice['origin_country'] ?? '')) ? $sinvoice['origin_country'] : $countryOfOrigion )?>
        </select>
        </td>
    </tr>
    <tr class="alt">
        <td style="text-align:center;"><input type="checkbox" name="is_invoice_materials" value="y" <?= $sinvoice['is_invoice_materials'] == 'y' ? 'checked' : '' ?>/></td> 
		<td class="left_title">Materials</td>
		<td><input type="text" name="invoice_materials" value="<?=!empty(trim($sinvoice['invoice_materials'] ?? '')) ? $sinvoice['invoice_materials'] : $productMaterials ?>" class="intext" style="width:80%;"/></td>
	</tr>
    <tr class="alt">
        <td style="text-align:center;"><input type="checkbox" name="is_invoice_usage" value="y" <?= $sinvoice['is_invoice_usage'] == 'y' ? 'checked' : '' ?>/></td> 
		<td class="left_title">Usage</td>
		<td><input type="text" name="invoice_usage" value="<?=!empty(trim($sinvoice['invoice_usage'] ?? '')) ? $sinvoice['invoice_usage'] : $productusage ?>" class="intext" style="width:80%;"/></td>
	</tr>
</table>
</div>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
