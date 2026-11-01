<!-- _product_purchase_summary.tpl -->

<?php
// print_r($item); die;
$summary = productPurchaseSummary($item['id']);

?>

<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;purchaseSummary&quot;);return false">Purchase Summary</a>')?>

<div id="purchaseSummary">
    <table class="table_grid">
        <tr>
            <th style="<?=$style?>" colspan="8"><strong style="margin-right:20px;">Current Average Cost</strong><input type="text" value="<?= productAverageCostPriceSummery($item['id']) ?>" readonly><span style="margin-left:10px;">USD</span></th>
        </tr>
        <tr style="<?=('' != $style) ? $style : 'background:#E3EBF7'?>">
            <td width="8%"><b>Date</b></td>
            <td width="8%"><b>Qty</b></td>
            <td width="10%"><b>Unit Cost (USD)</b></td>
            <td width="10%"><b>Unit Freight Cost (USD)</b></td>
            <td width="10%"><b>Unit Total Cost (USD)</b></td>
            <!-- <td width="10%"><b>Total Order Cost (USD)</b></td> -->
            <td width="14%"><b>Supplier</b></td>
            <td width="30%"><b>Comment</b></td>
        </tr>
        <?php
        $count = 0;
        foreach ($summary as $sum) {
            $total_cost = convertCurrency($sum['unit_cost'],$sum['unit_currency'],'USD') + convertCurrency($sum['unit_freight'],$sum['unit_currency'],'USD');
            $total      = $total_cost * $sum['movement'];
            $unit_total_cost = $sum['unit_cost']+$sum['unit_freight'];
            $unit_total_cost = convertCurrency($unit_total_cost, $sum['unit_currency'], 'USD');
        ?>
            <tr class= "<?=($count % 2 == 1) ? 'alt' : ''?> <?= ($count >= 10) ? ' row-purchase-hide-check hide-row':''?>">
                <td style="text-align:center"><?=date('d/m/Y', strtotime($sum['created_at']))?></td>
                <td style="text-align:center"><?=$sum['movement']?></td>
                <td style="text-align:center">$<?=number_format((float)  convertCurrency($sum['unit_cost'], $sum['unit_currency'], 'USD'), 2, '.', '')?></td>
                <td style="text-align:center">$<?=number_format((float) convertCurrency($sum['unit_freight'], $sum['unit_currency'], 'USD'), 2, '.', '')?></td>
                <td style="text-align: center">$<?=number_format($total, 2, '.', '')?></td>
                <td style="text-align:center"><?= $supplier_list[$sum['supplier_id']] ?></td>
                <td style="text-align:center"><?= $sum['comments'] ?></td>
            </tr>
        <?php
        $count++;
        }
        ?>
    </table>
    <?php if ($count > 10 ) { ?>
    <div style="text-align:center">
        <br />
        <button id="showPurchaseinvoice" type="button">View All (<?= $count ?>)</button>
    </div>
    <?php } ?>
    <br /><br />
</div>
<script type="text/javascript">
var htmltext='';
$('#showPurchaseinvoice').click(function(){
    if($('.row-purchase-hide-check').hasClass('hide-row')){
        $('.row-purchase-hide-check').removeClass('hide-row');
         htmltext=$('#showPurchaseinvoice').html();
        $('#showPurchaseinvoice').html('Show Less');
    }else{
        $('#showPurchaseinvoice').html(htmltext);
        $('.row-purchase-hide-check').addClass('hide-row');
    }
});
</script>
<!-- /_product_purchase_summary.tpl -->
