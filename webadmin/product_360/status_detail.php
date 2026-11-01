<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;statusdetail&quot;);return false">Status Detail</a>')?>
<div id="statusdetail">

	<table class="table_fields">
         <tr>
            <th style="width:25%;">Status:</th>
            <td>
	              <select class="form-control col-sm-9" id="status" name="item[status]"  onchange="this.style.backgroundColor=((this.value==0)?'rgb(255 0 0)':'#00FF00');" style="width:90%;background:<?=(1 == $item['status']) ?  '#00FF00;':'rgb(255 0 0);';?>">
	                <?= formatHTMLOptions(['0'=>'In Active','1'=>'Active'], $item['status']) ?>
	            </select>
            </td>
        </tr>
        
	</table>
</div>
</br>
</br>