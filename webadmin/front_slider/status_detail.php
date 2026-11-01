<?=formatHTMLTitle('<a href="#" onclick="toggleSection(&quot;statusdetail&quot;);return false">Status Detail</a>')?>
<div id="statusdetail">

	<table class="table_fields">
		<tr>
            <th style="width:25%;">Default:</th>
            <td>
	              <select class="form-control col-sm-9" id="default" name="item[default]"  onchange="this.style.backgroundColor=((this.default=='n')?'rgb(255 0 0)':'#00FF00');" style="width:90%;background:<?=('y' == $item['default']) ?  '#00FF00;':'rgb(255 0 0);';?>">
	                <?= formatHTMLOptions(['n'=>'No','y'=>'Yes'], $item['default']) ?>
	            </select>
            </td>
        </tr>
         <tr>
            <th style="width:25%;">Status:</th>
            <td>
	              <select class="form-control col-sm-9" id="status" name="item[status]"  onchange="this.style.backgroundColor=((this.value=='n')?'rgb(255 0 0)':'#00FF00');" style="width:90%;background:<?=('y' == $item['status']) ?  '#00FF00;':'rgb(255 0 0);';?>">
	                <?= formatHTMLOptions(['n'=>'In Active','y'=>'Active'], $item['status']) ?>
	            </select>
            </td>
        </tr>
        
	</table>
</div>
</br>
</br>