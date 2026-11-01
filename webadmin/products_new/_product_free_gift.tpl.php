<?php
function product_free_gift_row($count = "[ROW_COUNT]", $item = [])
{
    $row_name = "product_free_gift[" . $count . "]";
    $hidden = [
        $row_name . "[id]" => $item["id"] ?? "",
        $row_name . "[product_id]" => $item["product_id"] ?? "",
        $row_name . "[color_id]" => $item["color_id"] ?? "",
        $row_name . "[sub_id]" => $item["sub_id"] ?? "",
        $row_name . "[currency]" => $item["currency"] ?? "",
        $row_name . "[precision]" => $item["precision"] ?? "",
        $row_name . "[generic_code]" => $item["generic_code"] ?? "",
    ];
    ob_start();
?>
<tr id="product_free_gift_rowa[<?= $count ?>]" class="promotion_gift_row">
    <td style="white-space:nowrap;">
        <?= str_replace("\n", "", formatHTMLInputs($hidden)) ?>
        <input name="<?= $row_name ?>[linked_id]" type="text" value="<?= htmlspecialchars($item["linked_id"] ?? "") ?>" size="5" maxlength="8" />
    </td>
    <td><input name="<?= $row_name ?>[name]" type="text" value="<?= htmlspecialchars($item["name"] ?? "") ?>" style="width:96%;" /></td>
    <td><input name="<?= $row_name ?>[qty]" type="text" min="1" step="1" value="<?= htmlspecialchars($item["qty"] ?? "") ?>" size="3" /></td>
    <td><input name="<?= $row_name ?>[cost]" type="text" min="0.00" step="0.01" value="<?= htmlspecialchars(round($item["cost"], 2) ?? "") ?>" size="6" readonly style="border: none;text-align: center;"/></td>
    <td><input name="<?= $row_name ?>[online]" type="text" min="0.00" step="0.01" value="<?= htmlspecialchars(round($item["online"], 2) ?? "") ?>" size="6" readonly style="border: none;width: 96%;text-align: center;"/></td>
    <td><input name="<?= $row_name ?>[price]" type="text" min="0.00" step="0.01" value="<?= htmlspecialchars(round($item["price"], 2) ?? "") ?>" size="6" /></td>
    <td style="text-align:center;vertical-align:middle;"><input name="<?= $row_name ?>[generic]" type="checkbox" value="<?= "y" == $item["generic"] ? "n" : $item["generic"] ?>"<?= "y" == $item["generic"] ? "" : ' checked="checked"' ?> /></td>
    <td style="vertical-align:middle;"><a href="#" onclick="freeDeleteGift(<?= $count ?>);return false"><i class="bi bi-trash">Delete</i></a></td>
</tr>
<?php return ob_get_clean();
}  ?>



<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;free_gifts_product&quot;);return false">Free Gift</a>', '<a href="#" onclick="freeGiftSearchProduct();return false">Add New</a>')?>
    <table class="table_fields">
        <tr>
            <th style="width:25%;">Free Gifts :</th>
            <td>
                <!-- <input type="checkbox"  name="item[free_gifts_product]" value="n" > -->
                 <input type="checkbox"  name="item[free_gifts_product]" value="y" <?= ($item['free_gifts_product']=='y')?'checked':''; ?> >
            </td>
        </tr>
    </table>  
    <!-- <table class="table_grid" id="product_free_gift_table" <?=('y' == $item['free_gifts_product']) ? '' : 'style="display:none"'?>> -->
    <table class="table_grid" id="product_free_gift_table" <?= (('y' == $item['free_gifts_product'] || !empty($product_free_gift)) ? '' : 'style="display:none"') ?>>
        <tr>
            <th style="width:5%">PID</th>
            <th style="width:30%">Product Name</th>
            <th style="width:10%">Qty.</th>
            <th style="width:10%">Cost Price (USD)</th>
            <th style="width:15%">Online Price (USD)</th>
            <th style="width:10%">Offer Price (USD)</th>
            <th style="width:5%">Link Stock</th>
            <th style="width:5%"></th>
        </tr>
        <?php
        $count = 1;
        $gift_sum = 0;
        $gift_currency = '';
        if(isset($product_free_gift)) {
            foreach ($product_free_gift as $gift) {
                switch ($gift["generic"]) {
                    case "n":
                        if (
                            $linked = productFromDatabase(
                                productStrJoin($gift["linked_id"], $gift["linked_sub_id"])
                            )
                        ) {
                            $gift["precision"] = "y" == $linked["decimal_points"] ? 4 : 2;
                        }
                        break;

                    case "b":
                        if (
                            function_exists("bulkFromDatabase") &&
                            ($linked = bulkFromDatabase($gift["linked_id"]))
                        ) {
                            $gift["cost"] = $linked["cost"] ?? $gift["cost"];
                            $gift["online"] = $linked["online"] ?? $gift["online"];
                            $gift["price"] = $linked["price"] ?? $gift["price"];
                            $gift["currency"] = $linked["currency"] ?? $gift["currency"];
                        }
                        break;
                }

                $gift["price"] = bcadd($gift["price"], "0.0000", $gift["precision"]);
                $gift_sum      = bcadd(bcmul($gift["online"],$gift["qty"],2),$gift_sum,2);
                $gift_currency = $gift["currency"];
                echo product_free_gift_row($count++, $gift);
            }
        }
        ?>
    </table>
    <input type="hidden" name="product_free_gift_deleted" value="" />
<table class="table_fields">
        <tr>
            <th style="width:25%;">Promotion Expiry :</th>
            <td> <input type="date" name="item[gift_promotion_expiry_date]" value="<?php hecho((is_null($item['gift_promotion_expiry_date'])) ? '' : substr($item['gift_promotion_expiry_date'], 0, 10))?>"/>
                        <button onclick="Product.set(&quot;gift_promotion_expiry_date&quot;, &quot;&quot;);return false"><i class="fas fa-trash-alt"></i></button></td>
        </tr>
    </table>
<script type="text/javascript">
//<![CDATA[
var product_free_gift_count = <?=max(100, count($product_free_gift))?>;
        function productFreeAddGift()
        {
            $('#product_free_gift_table').show();
            $('input[name="item[free_gifts_product]"]').prop('checked', true).val('y'); 
            $("#product_free_gift_table").append(<?= strtr(
            json_encode(
                product_free_gift_row("[ROW_COUNT]", [
                    "currency" => "",
                    "generic" => "n",
                    "precision" => "2",
                    "price" => "0.00",
                    "qty" => "1",
                ]) ?:
                ""
            ) ?:
            "",
            [
                "[ROW_COUNT]" => '" + product_free_gift_count + "',
            ]
        ) ?>);
            product_free_gift_count++;
        }
        function freeGiftSearchProduct()
        {
            window.open('/webadmin/products_new/?action=list&return_message=add_free_gift&multiple=y&search_for=');
        }
        function freeDeleteGift(row)
        {
            if (confirm('Are you sure you want to remove this gift?')) $("[id='product_free_gift_rowa[" + row + "]']").detach();
        }
        function freeGiftProductData2(data)
        {
              console.log(data);
                var currency = "USD";
                var row_name = "product_free_gift[" + product_free_gift_count + "]";
                productFreeAddGift();
                $("[name='" + row_name + "[cost]']").val(convertCurrency(data["consignment_cost_price"], data["consignment_cost_currency"], currency));
                $("[name='" + row_name + "[currency]']").val(currency);
                $("[name='" + row_name + "[name]']").val(data["product_name"]);
                $("[name='" + row_name + "[online]']").val(convertCurrency(data["consignment_sales_price"], data["consignment_sales_currency"], currency));
                $("[name='" + row_name + "[linked_id]']").val(data["id"]);
                $("[name='" + row_name + "[color_id]']").val(data["color_id"]);
        }
        function setfreeGiftProductsAddData(data)
        {
            for(var key in data) freeGiftProductData2(data[key]);
        }

</script>
<!-- <script type="text/javascript">
    $('input[name="item[free_gifts_product]"]').on('change', function() {
        if ($('input[name="item[free_gifts_product]"]').is(':checked')) {
            $('#product_free_gift_table').show();
        } else {
            $('#product_free_gift_table').hide();
        }
    });
</script> -->
<script type="text/javascript">
    $('input[name="item[free_gifts_product]"]').on('change', function() {
        if ($('input[name="item[free_gifts_product]"]').is(':checked')) {
            $('#product_free_gift_table').show();
            $(this).val('y');
        } else {
            if (product_free_gift_count == <?=max(100, count($product_free_gift))?>) {
                $('#product_free_gift_table').hide();
            }
            //$('#product_free_gift_table').hide();
            $(this).val('n'); 
        }
    });
</script>
<!-- /_machine_linked.tpl -->
