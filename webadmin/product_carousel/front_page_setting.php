<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;frontpage&quot;);return false">Front Page</a>')?>
<div id="frontpage">

	<table class="table_fields">
		<tr>
			<th><span style="color:red;"><small>*</small></span>Title :</th>
			<td><input class="wide" type="text" name="item[home_title]"  value="<?= $item['home_title']?>" style="width:98%;" /></td>
		</tr>
		<tr>
			<?php $product_tooltip='<img src="images/carousel-x-radius-icon.png" style="max-width:96px;max-height:96px;position:absolute;left:8px;" /><br/><ul style="padding-left:128px;white-space:nowrap;min-height:96px;"><li>The carousel horizontal/x-axis radius in px.</li> </ul>';?>
			<th><span style="color:red;"><small>*</small></span>Carousel-X-Radius :  <i class="far fa-question-circle" onmouseover="<?php hecho('tt_in(' . jecho($product_tooltip, 1) . ');') ?>" onmouseout="tt_out();"></i></th>
			
			<td><input class="wide update_crausel" type="number" name="item[front_x_radious]"  value="<?= $item['front_x_radious']?>" style="width:98%;" max="1600" min="500" /></td>
		</tr>
		<tr>
			<?php $product_tooltip='<img src="images/carousel-y-radius-icon.png" style="max-width:96px;max-height:96px;position:absolute;left:8px;" /><br/><ul style="padding-left:128px;white-space:nowrap;min-height:96px;"><li>The carousel vertical/y-axis radius in px.</li> </ul>';?>
			<th><span style="color:red;"><small>*</small></span>Carousel-Y-Radius :  <i class="far fa-question-circle" onmouseover="<?php hecho('tt_in(' . jecho($product_tooltip, 1) . ');') ?>" onmouseout="tt_out();"></i></th>
			<td><input class="wide update_crausel" type="number" name="item[front_y_radious]"  value="<?= $item['front_y_radious']?>" style="width:98%;" max="100" min="0" /></td>
		</tr>
         <tr>
            <th style="width:25%;">Allow Front Page:</th>
            <td>
	              <select class="form-control col-sm-9" id="active_home_page" name="item[active_home_page]"  onchange="this.style.backgroundColor=((this.value=='n')?'rgb(255 0 0)':'#00FF00');" style="width:90%;background:<?=('y' == $item['active_home_page']) ?  '#00FF00;':'rgb(255 0 0);';?>">
	                <?= formatHTMLOptions(['n'=>'No','y'=>'Yes'], $item['active_home_page']) ?>
	            </select>
            </td>
        </tr>
        
	</table>
</div>
</br>
</br>