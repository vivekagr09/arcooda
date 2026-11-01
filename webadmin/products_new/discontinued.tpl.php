<script type="text/javascript">
    var product_recommended_count = <?=max(100, count($recommended_products ?? []))?>;
    function recommendedSearchProduct()
    {
        var name = getValue('recommended_product_search');
        window.open('/webadmin/products_new/?action=list&return_message=recommended_product&multiple=y&search_for=' + escape(name));
    }
    function recommendedDeleteProduct(row,id)
    {
        if (confirm('Are you sure you want to remove this product?')) {
            if(id)
            setValue("item_discontinued_recommended_delete", getValue("item_discontinued_recommended_delete") + "," + id);
            $("[id='recommended_product[" + row + "]']").detach();
        }
        
    }
    function setProductsRecommended(data)
    {
        for(var key in data) setProductRecommended(data[key]);
    }
    function setProductRecommended(data){
        var row_name = "recommended_product[" + product_recommended_count + "]";
        var html = '<li id="'+row_name+'">'+data["product_name"]+' <a href="#" onclick="recommendedDeleteProduct('+product_recommended_count+',0);return false">\
        <input type="hidden" name="'+row_name+'[recommended_id]" value="'+data["id"]+'">\
        <i class="bi bi-trash">Delete</i></a></li>';
        product_recommended_count++;
        $("#recommended_product_list").append(html);
    }

</script>

<tr id="discontinued-container" <?=('427' == $item['available']) ? '' : 'style="display:none"'?>>
    <th>Recommended product</th>
    <td>
        <div class="discontinued-data"  id="discontinued-data"> 
            <input type="text" name="recommended_product_search" width="40%">
            <input type="button" value="Search" onclick="recommendedSearchProduct()" />
             <input type="hidden" name="item_discontinued_recommended_delete" value="" />
            <br />
            <ul id="recommended_product_list">
                <?php
                     $count = 0;
                        foreach ($recommended_products as $recommended_product) {
                            $prod = productFromDatabase($recommended_product['recommended_id']);
                            $rowName = 'recommended_product['.$count.']';
                            echo '<li id="'.$rowName.'">'.$prod["product_name"].' <a href="#" onclick="recommendedDeleteProduct('.$count.','.$recommended_product["id"].');return false">
                                <input type="hidden" name="'.$rowName.'[recommended_id]" value="'.$prod["id"].'">
                                <input type="hidden" name="'.$rowName.'[id]" value="'.$recommended_product["id"].'">
                                <i class="bi bi-trash">Delete</i></a></li>';
                            $count++;
                        }
                ?>
            </ul>
        </div>
    </td>
</tr>