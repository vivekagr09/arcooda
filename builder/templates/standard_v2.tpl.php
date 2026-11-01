<?php
/**
 * Project: Product Builder
 *
 * standard_v2.tpl.php
 *
 * Standard template for product builder.
 *
 * This template need options variable from parent
 * Options data can be optain from ProductBuilder Module.
 *
 * @author Kelvin (khtat@highwaygroup.net)
 * @since  03 JAN 2018
 */

moduleLoad('product'); //productFromDatabase

$name = $options['name'];
$qty  = $options['qty'];
$col  = $options['column'];

//@fixme /images/builder/... needs to be relocated
$imageurl = '/images/builder/';

$optionData = $pb->getOptionById($options['id']);

// Sort options
$optionList = $pb->sortOptionsByPosition($optionData['options']);

?>
<div style="padding: 25px 0; width: 75%; margin: auto; border-left: 1px solid #f0f0f0; border-right: 1px solid #f0f0f0;" class="align-middle" id="options-slider" data-col="<?=$col?>">
	<!--div class="page-title"><h2 class="clr">Choose <?php echo ucwords($name); ?></h2></div-->
<?php
$defaultkey = array_search('y',array_column($optionList, 'default'));
$defaultkey=($defaultkey===false)?0:($defaultkey+1);
$default_color_array=[];
foreach ($optionList as $itemKey => $item) {
    $total_price = $item['price'] * $qty;
    if($item['default_color']>0 && $item['option_key']==0){
        $default_color_array[$optionKey.'_'.$itemKey]=['key'=>$optionKey,'row'=>$itemKey,'value'=>$item['default_color']];
    }
    $ea = ($item['price'] > 0) ? "(x{$qty} units)" : "";
    $price=$item['price'];
    if($defaultkey){
        $price=$item['price']-$optionList[$defaultkey]['price'];
        if($price<0){
            $price='- $'.abs($price);
        }else{
            $price='$'.abs($price);
        }
    }else{
         $price='$'.abs($price);
    }
    //echo "<td style='width:252px;' class='align-middle'>";
    $priceused=0;
    if($defaultkey>0){
        if($itemKey != $defaultkey){
            $priceused=$item['price']-$optionList[$defaultkey]['price'];
        }else{
            $priceused=0;
        }
    }else{
        $priceused=$item['price'];
    }
    $total_price = $priceused * $qty;
    if(isset($_GET['option_color'][$optionKey.'_'.$itemKey])){
        $item['default_color']=$_GET['option_color'][$optionKey.'_'.$itemKey]['value'];
    }
    switch ($item['option_key']) {
        case 0:
            $productData = productFromDatabase($item['product_id'], $item['site_id']);
            $color_data=getcolorhtmlforbuilderoption($item['color'],$itemKey,$optionKey,$item['product_id'],$productData['product_name'],$item['default_color']);
            $selector_class=($color_data!='' && $item['default_color']==0)?'':'button-selector';
            $img = str_replace('thumb', 'large', $productData['thumb_url']);
            echo "<div class='grid-inline-top align-middle option-items' style='width: 300px; margin: 10px; outline: none;'>";
            echo "<div class='option-select ".$selector_class." option-container-border' id='option-default-selector-".$itemKey."'>";
            echo "<div class='option-container'>";
            echo "<div class='option-name-wrapper'>";
            echo "<h4 class='option-image' id='option-productname-".$itemKey."'>{$productData['product_name']}</h4>";
            echo "</div>";

            echo "<div class='option-image-wrapper'>";
            echo "<img src='{$img}' width='246' height='250' class='option-image' id='option-image-".$itemKey."'/>";
            echo $color_data;
            echo "</div>"; // END option-ima
                //(($defaultkey)? ($item['price']-$optionList[$defaultkey]['price']):$item['price'])
                echo "<div class='align-middle' style=".(($itemKey == $defaultkey)?'display:none;':'').">";
                echo "<span class='option-price'>".$price."</span><span class='option-price option-price-currency' style='font-size: 10PX;'>{$item['currency']}</span><span class='option-units'> {$ea}</span>";
                echo "</div>";
            echo "<div class='desc-wrapper' style='display: none;'>";
            echo "<p class='clr option-desc'>".preg_replace("/\r\n|\r|\n/", '<br/>', $productData['long_desc'])."</p>";
            echo "</div>";
           

            echo "<div class='option-btn-wrapper'>";
            echo "<div class='option-btn-radio' data-option-key='{$itemKey}' data-option-optionkey='{$item['option_key']}' data-option-img='{$productData['thumb_url']}' data-option-currency='{$item['currency']}' data-option-price='{$total_price}' data-option-itemKey='{$itemKey}' data-option-name='{$productData['product_name']}' data-option-qty='{$qty}' data-default-key='{$defaultkey}'></div>";
            
            echo "</div>"; // end option-btn-wrapper
            echo "</div>";
            echo "</div>";
            echo "</div>";
            break;

        case 1:
            echo "<div class='grid-inline-top align-middle option-items' style='width: 300px; margin: 10px; outline: none;'>";
            echo "<div class='option-select button-selector option-container-border' id='option-default-selector-".$itemKey."'>";
            echo "<div class='option-container'>";
            echo "<div class='option-name-wrapper'><h4>{$item['header']}</h4></div>";

            echo "<div class='option-image-wrapper'>";
            echo "<p class='clr' style='padding: 10px;'>{$item['text']}</p>";
            echo "</div>";

            if($itemKey != $defaultkey){
                echo "<div class='align-middle'>";
                echo "<span class='option-price'>".$price."</span><span class='option-price option-price-currency' style='font-size: 10PX;'>{$item['currency']}</span><span class='option-units'> {$ea}</span>";
                echo "</div>";
            }

            echo "<div class='option-btn-wrapper'>";
            echo "<div class='option-btn-radio' data-option-key='{$itemKey}' data-option-optionkey='{$item['option_key']}' data-option-img='graphic/text.png' data-option-currency='{$item['currency']}' data-option-price='{$total_price}' data-option-itemKey='{$itemKey}' data-option-name='{$item['header']}' data-option-qty='{$qty}' data-default-key='{$defaultkey}'></div>";
                echo "</div>"; // end option-btn-wrapper
            echo "</div>";
            echo "</div>";
            echo "</div>";
            break;

        case 2:
            echo "<div class='grid-inline-top align-middle option-items' style='width: 300px; margin: 10px; outline: none;'>";
            echo "<div class='option-select button-selector option-container-border' id='option-default-selector-".$itemKey."'>";
            echo "<div class='option-container'>";
            echo "<div class='option-name-wrapper'>";
            echo "<h4>{$item['header']}</h4>";
            echo "</div>";

            echo "<div class='option-image-wrapper'>";
            echo "<img src='{$imageurl}{$item['img']}.{$item['ext']}' width='246' height='250' class='option-image' />";

            echo "</div>"; // END option-image-wrapper

            if($itemKey != $defaultkey){
                echo "<div class='align-middle'>";
                echo "<span class='option-price'>".$price."</span><span class='option-price option-price-currency' style='font-size: 10PX;'>{$item['currency']}</span><span class='option-units'> {$ea}</span>";
                echo "</div>";
            }

            echo "<div class='desc-wrapper' style='display: none;'>";
            echo "<p class='clr option-desc'>".preg_replace("/\r\n|\r|\n/", '<br/>', $item['description'])."</p>";
            echo "</div>";

            echo "<div class='option-btn-wrapper'>";
            echo "<div class='option-btn-radio' data-option-key='{$itemKey}' data-option-optionkey='{$item['option_key']}' data-option-img='{$imageurl}{$item['img']}.{$item['ext']}' data-option-currency='{$item['currency']}' data-option-price='{$total_price}' data-option-itemKey='{$itemKey}' data-option-name='{$item['header']}' data-option-qty='{$qty}' data-default-key='{$defaultkey}'></div>";
            //echo "<button type='button' class='option-select blue-btn button-selector' data-option-key='{$optionKey}' data-option-optionkey='{$item['option_key']}' data-option-img='{$productData['thumb_url']}' data-option-currency='{$item['currency']}' data-option-price='{$total_price}' data-option-itemKey='{$itemKey}' data-option-name='{$productData['product_name']}' data-option-qty='{$qty}'>SELECT</button>";
            echo "</div>"; // end option-btn-wrapper
            echo "</div>";
            echo "</div>";
            echo "</div>";
            break;
    }

}

?>
<?php 
 /*   function getcolorhtmlforbuilderoption($color,$row,$keydata){
            if(count(explode(',', $color)) && $color){
            $html .='<table class="table_fields" style="width: 100%; margin-top: 1px;">
            <tr>
                <td>
                    <ul style="list-style:none;">';
                    
                    foreach (explode(',', $color) as $key => $colr) {
                        $color_detail=getProductColourById($colr);
                        $html =  $html. '<li style="text-align: center; display: inline-block; vertical-align: top; margin: 1px 1px;">
                            <div class="cat action" >
                            <input type="checkbox" value="'.$color_detail['id'].'" name="option_color['.$keydata.']['.$row.'][]" style="width: 24px; height: 24px;" class="color_checkbox" data-key="'.$keydata.'" data-row="'.$row.'">
                            <div style="padding:12px;background:'.$color_detail["color_val"].'">
                                </div>
                                </div>
                        </li>';
                }
                $html .='</ul> </td> </tr> </table>';
            return $html;
            }
        return '';
    }*/
    function getcolorhtmlforbuilderoption($color,$row,$keydata,$product_id,$productname,$default_color){
            if(count(explode(',', $color)) && $color){
            $html ='<div class="input-radio-color">
                    <div class="input-radio-color__list">';
                    
                    foreach (explode(',', $color) as $key => $colr) {
                        $color_detail=getProductColourById($colr);
                        $html =  $html. '<label class="input-radio-color__item" style="color:'.$color_detail["color_val"].';margin: 0 auto;" data-toggle="tooltip" title="'.$color_detail["colour"].'" data-original-title="'.$color_detail["colour"].'" ><input type="radio"  value="'.$color_detail['id'].'" name="option_color['.$keydata.']['.$row.'][]" data-key="'.$keydata.'" data-row="'.$row.'" data-productid= "'.$product_id.'" data-produc-name= "'.$productname.'" data-color-name= "'.$color_detail["colour"].'" class="color_checkbox" '.(($default_color==$colr)?"checked":"").'> <span></span></label>';
                }
                $html .='</div>
                </div>';
            return $html;
            }
        return '';
    }
?>
</div>

<script type="text/javascript">
//<![CDATA[
$(document).ready(function () {
    $('#options-slider').slick({
        centerMode: false,
        infinite: false,
        centerPadding: '60px',
        slidesToShow: 4,
        arrows: true
    });
});
//]]>
</script>