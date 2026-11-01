<?php
include_once('packing_common.inc.php');

moduleLoad('product'); //productFromDatabase


$container_list=packingFromPost($posted);
include 'templates/packing_error_list.tpl.php';
$counter=0;
foreach($container_list as $key=>$container)
{
	$counter++;
	$width=intval($container->getWidth()/3);
	$scale=$width/$container->getWidth();
	$height=$container->getDepth()*$scale;
?>
<div class="summary-table" style="width:95%;border:1px #ccc solid;padding:10px 0px;margin-top:10px;">
	<p>Container <?= $counter ?> (<?= $container->getName() ?>):</p>
<div style="display:flex;width:100%">
	<div style="width:20%;">
		<table>
		  <tr>
            <th colspan="2">Summary</th>
          </tr>
	      <tr>
                <td>Total Packages</td>
                <td>18</td>
           </tr>
		   <tr>
                <td>Cargo Volume</td>
                <td>20.9</td>
            </tr>
			<tr>
                <td>Cargo Weight</td>
                <td>2470</td>
            </tr>
        </table>
    </div>
	<div style="width:30%;">
		<div>
		<img border="0" src="https://www.arcooda.com/webadmin/packing_v2/packing_image2d.png?imgstring=<?= urlencode(packingContainerToImgString($container)) ?>&width=<?= $width ?>" usemap="#container_map<?= $counter ?>" /><br />
		<map id="container_map<?= $counter ?>" name="container_map<?= $counter ?>">
           <?php
	         $container_space=$container->getVolume();
	         $container_free=$container_space;
	         $product_cache=array();
	         foreach($container->crate_list as $crate)
	          {
		        $container_free-=$crate->getVolume();
		        $x1=$crate->getX()*$scale;
		        $y1=$crate->getZ()*$scale;
		        $x2=$x1+$crate->getWidth()*$scale-1;
		        $y2=$y1+$crate->getDepth()*$scale-1;
                $product_id=$crate->getProductID();
		        $site_id=$crate->getSiteID();
		        $product=array();
		          if(array_key_exists($product_id,$product_cache))
			          $product=$product_cache[$product_id];
		            elseif($product_id>0)
		              {
			            $product=productFromDatabase($product_id,$site_id);
			            $product_cache[$product_id]=$product;
		               }
            ?>
			<area href="<?= $product['product_url'] ?>" target="_blank" shape="rect" coords="<?= $x1 ?>,<?= $y1 ?>,<?= $x2 ?>,<?= $y2 ?>" onmouseover="<?=htmlspecialchars('tt_in(' . json_encode(htmlspecialchars($crate->getName() . ' - ' . $product_id)) . ')')?>" onmouseout="tt_out()" />
           <?php
	          }
            ?>
		</map>
		</div>
	</div>
	<div style="width:20%;">
		<form accept-charset="utf-8" target="_blank" method="post" style="display:inline;">
		<textarea style="display:none;" name="container"><?= htmlspecialchars(base64_encode(gzcompress(serialize($container)))) ?></textarea>
			<table style="width:100%;border:none;">
				<tr>
					<td><input type="button" value="Save Image As" onclick="javascript:form.action='packing_image2d.png?disposition=attachment';form.submit();" /></td>
					<td><input type="button" value="Zoom Image" onclick="javascript:form.action='packing_image2d.png';form.submit();" /></td>
                </tr>
                <tr>
	               <td><input type="button" value="Show Blocks" onclick="javascript:form.action='packing_view3d.php';form.submit();" /></td>
	               <td><input type="button" value="View 3D" onclick="javascript:form.action='packing_view3d.php';form.submit();" /></td>
                </tr>
            </table>
		</form>
	</div>
	<div style="width:30%;">
		<table>
		<tr>
           <th>Space <br/> Available</th>
           <th>Cubic M3 <br/> Available</th>
		   <th>Weight Limit <br/> Available:</th>
        </tr>
        <tr>
           <td style="text-align:center;"><?= sprintf('%.2f',$container_free*100.0/$container_space) ?>%</td>
	       <td style="text-align:center;">4.2</td>
	       <td style="text-align:center;">3924 Kg</td>
        </tr>
        </table>
	</div>
</div>
</div>

<!-- <table class="wide">
	<tr><th>Container <?= $counter ?> (<?= $container->getName() ?>):</th></tr>
	<tr>
		<td>
			<table>
				<tr>
					<td>Total Packeges</td>
					<td>18</td>
				</tr>
				<tr>
					<td>Cargo Volume</td>
					<td>20.9 </td>
				</tr>
				<tr>
					<td>Cargo Weight</td>
					<td>2470</td>
				</tr>
			</table>
		</td>
		<td style="text-align:center;">
		<img border="0" src="https://www.arcooda.com/webadmin/packing_v2/packing_image2d.png?imgstring=<?= urlencode(packingContainerToImgString($container)) ?>&width=<?= $width ?>" usemap="#container_map<?= $counter ?>" /><br />
		<map id="container_map<?= $counter ?>" name="container_map<?= $counter ?>">
<?php
	$container_space=$container->getVolume();
	$container_free=$container_space;
	$product_cache=array();
	foreach($container->crate_list as $crate)
	{
		$container_free-=$crate->getVolume();
		$x1=$crate->getX()*$scale;
		$y1=$crate->getZ()*$scale;
		$x2=$x1+$crate->getWidth()*$scale-1;
		$y2=$y1+$crate->getDepth()*$scale-1;

		$product_id=$crate->getProductID();
		$site_id=$crate->getSiteID();
		$product=array();
		if(array_key_exists($product_id,$product_cache))
			$product=$product_cache[$product_id];
		elseif($product_id>0)
		{
			$product=productFromDatabase($product_id,$site_id);
			$product_cache[$product_id]=$product;
		}
?>
			<area href="<?= $product['product_url'] ?>" target="_blank" shape="rect" coords="<?= $x1 ?>,<?= $y1 ?>,<?= $x2 ?>,<?= $y2 ?>" onmouseover="<?=htmlspecialchars('tt_in(' . json_encode(htmlspecialchars($crate->getName() . ' - ' . $product_id)) . ')')?>" onmouseout="tt_out()" />
<?php
	}
?>
		</map>
	</td>
	<td class="left_title" style="text-align:center;">
		<form accept-charset="utf-8" target="_blank" method="post" style="display:inline;">
			<textarea style="display:none;" name="container"><?= htmlspecialchars(base64_encode(gzcompress(serialize($container)))) ?></textarea>
			<input type="button" value="Save Image As" onclick="javascript:form.action='packing_image2d.png?disposition=attachment';form.submit();" />
			<input type="button" value="Zoom Image" onclick="javascript:form.action='packing_image2d.png';form.submit();" />
			<br\>
			<input type="button" value="Show Blocks" onclick="javascript:form.action='packing_view3d.php';form.submit();" />
			<input type="button" value="View 3D" onclick="javascript:form.action='packing_view3d.php';form.submit();" />
		</form>
	</td>
	<td>
		Container Space Available = <?= sprintf('%.2f',$container_free*100.0/$container_space) ?>%<br />
		Width = <?= $container->getWidth() ?>cm,
		Depth = <?= $container->getDepth() ?>cm,
		Height = <?= ($container->getHeight()>0)?$container->getHeight():$container->maximal['y'] ?>cm
	</td>
</tr>
</table> -->
<br />
<br />
<?php
}

include_once('templates/packing_footer.tpl.php');
?>
