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
<?=formatHTMLTitle('<a href="javascript:toggleSection(\'invoicecomment\');">Invoice Comments/Notes</a>')?>
<div id="invoicecomment">

<table class="wide">
    <tr class="alt">
        <td style="width:10%;">&nbsp;</td>    
		<td class="left_title" style="width:15%;">Invoice Comments/Notes<br /><span style="font-size:9px;color:red;font-weight:normal;">Displayed on Invoice</span></td>
		<td colspan="2"><textarea name="invoice_comments" rows="<?=max(10, count(explode("\n", trim($invoice['invoice_comments']))))?>" onkeyup="javascript:textareaAutoSize(this,1,10);"><?=htmlspecialchars(trim($invoice['invoice_comments']))?></textarea></td>
	</tr>
    <tr class="alt"><td style="text-align:center;"><span style="color:red;font-style:italic;font-size:8px;">Show on Invoice</span></td><td class="left_title">&nbsp;</td><td>&nbsp;</td></tr>
	<tr class="alt">
        <td style="text-align:center;"><input type="checkbox" name="is_harmonised_code" value="y" <?= $invoice['is_harmonised_code'] == 'y' ? 'checked' : '' ?>/></td>
        <td class="left_title">Harmonised Code</td>
        <td><select name="harmonised_code" style="width:80%;">
            <option value="">Not Selected</option>
            <?=formatHTMLOptions($harmonised_code_nest, $invoice['harmonised_code'])?>
        </select>
        <a href="invoice_options.php?section=harmonised_code" target="_blank" class="addedit">Add / Edit</a>
        </td>
    </tr>
    <tr class="alt">
        <td style="text-align:center;"><input type="checkbox" name="is_origin_country" value="y" <?= $invoice['is_origin_country'] == 'y' ? 'checked' : '' ?>/></td> 
        <td class="left_title">Country Of Origin</td>
        <td><select name="origin_country" style="width:80%;">
            <option value="">Not Selected</option>
            <?=formatHTMLOptions(invoiceGetOptionList('origin_country'), $invoice['origin_country'])?>
        </select>
        </td>
    </tr>
    <tr class="alt">
        <td style="text-align:center;"><input type="checkbox" name="is_invoice_materials" value="y" <?= $invoice['is_invoice_materials'] == 'y' ? 'checked' : '' ?>/></td> 
		<td class="left_title">Materials</td>
		<td><input type="text" name="invoice_materials" value="<?=$invoice['invoice_materials']?>" class="intext" style="width:80%;"/></td>
	</tr>
    <tr class="alt">
        <td style="text-align:center;"><input type="checkbox" name="is_invoice_usage" value="y" <?= $invoice['is_invoice_usage'] == 'y' ? 'checked' : '' ?>/></td> 
		<td class="left_title">Usage</td>
		<td><input type="text" name="invoice_usage" value="<?=$invoice['invoice_usage']?>" class="intext" style="width:80%;"/></td>
	</tr>
</table>
<br /><br />
</div>
<!-- <div style="background-color:white;text-align:center;padding:8px 8px 8px 8px;display:none;" id="sectionOrderInfo_hideNote">
Order Information can be viewed by Clicking "Order Information" above
<br />
</div> -->

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
