<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';

echo formatHTMLTitle('Product Ordered');
 $orderedArray  = array();
    if ($query = $DB->query(sprintf(
        "SELECT 
	    p.id,
	    p.product_name,
	    p.slug,
	    p.qty,
	    p.stock_type,
	    SUM(CASE WHEN inv.invoice_status = 'inquiry' THEN ip.product_qty ELSE 0 END) AS inquiry_total,
	    SUM(CASE WHEN inv.invoice_status IN ('proforma', 'sales', 'return') THEN ip.product_qty ELSE 0 END) AS invoice_total,
	    SUM(ip.product_qty) AS grand_total
	FROM arcooda_www.products AS p
	INNER JOIN highwaygroup.invoice_products AS ip 
	    ON ip.product_id = p.id
	INNER JOIN highwaygroup.invoices AS inv 
	    ON inv.invoice_id = ip.invoice_id
	WHERE inv.invoice_status IN ('inquiry', 'proforma', 'sales', 'return')
	AND inv.site_id = 39
	GROUP BY p.id, p.product_name, p.qty, p.stock_type"
    ))) {
        while ($row = $DB->fetch_assoc($query)) {
            $orderedArray[] = $row;
        }
        $DB->free_result($query);
    }

  $generic_query = "
		SELECT 
		    SUM(
		        CASE 
		            WHEN inv.invoice_status = 'inquiry' 
		            THEN 1 
		            ELSE 0 
		        END
		    ) AS inquiry_total,

		    SUM(
		        CASE 
		            WHEN inv.invoice_status IN ('proforma', 'sales', 'return') 
		            THEN 1 
		            ELSE 0 
		        END
		    ) AS invoice_total,

		    COUNT(*) AS grand_total

		FROM highwaygroup.invoice_products AS ip

		INNER JOIN highwaygroup.invoices AS inv 
		    ON inv.invoice_id = ip.invoice_id

		WHERE inv.invoice_status IN ('inquiry', 'proforma', 'sales', 'return')
		AND ip.generic_code IN ('GPA','GMA','GPC','GME')
		AND inv.site_id = 39
		";
    $generic_count = $DB->query_first($generic_query);
?>
<table class="wide spacetable table_grid" align="center">
	<tr>
		<th>Product Id</th>
		<th>Product Name</th>
		<th>Stock</th>
		<th>Inquiry</th>
		<th>Invoice</th>
		<th>Total Units</th>
	</tr>
	<?php 
	$alt = false;
	 foreach ($orderedArray as $order) { 
	 	$class = '';
	 	if ($alt = !$alt) {
	        $class = ' class="alt"';
	    }
	      $view_url = sprintf(
            '/buy/%s-%d',
            urlencode(preg_replace('/\s+/', '-', $order['product_name'])),
            $order['id']
        );
        if ('' !== $order['slug']) {
            $view_url = sprintf(
                '/buy/%s-%d',
                urlencode(preg_replace('/\s+/', '-', $order['slug'])),
                $order['id']
            );
        }
	 ?>
	 	<tr <?=$class?> onmouseover="this.style.backgroundColor='#ee90e629';" onmouseout="this.style.backgroundColor='';">
	 		<td style="text-align:center"><a href="/webadmin/products_new/edit.php?action=do_edit&id=<?=$order['id']?>" target="_blank"><?=$order['id']?></a></td>
	 		<td style="text-align:left"><a href="<?= $view_url ?>" target="_blank"><?=$order['product_name']?></a></td>
	 		<td style="text-align:center"><?=($order['stock_type'] != 'consignment') ? $order['qty']: '' ?></td>
	 		<td style="text-align:center"><a href="/webadmin/invoice/invoice_list.php?status=inquiry&site_id=39&prod_string=<?=$order['id']?>&referer=product_ordered"  target="_blank" ><?=$order['inquiry_total']?></a></td>
	 		<td style="text-align:center"><a href="/webadmin/invoice/invoice_list.php?status=proforma|sales|return&site_id=39&prod_string=<?=$order['id']?>&referer=product_ordered"  target="_blank" ><?=$order['invoice_total']?></a></td>
	 		<td style="text-align:center"><a href="/webadmin/invoice/invoice_list.php?status=inquiry|proforma|sales|return&site_id=39&prod_string=<?=$order['id']?>&referer=product_ordered"  target="_blank"><?=$order['grand_total']?></a></td>
	 	</tr>
	<?php } ?>
		<tr <?=$class?> onmouseover="this.style.backgroundColor='#ee90e629';" onmouseout="this.style.backgroundColor='';">
	 		<td style="text-align:center"></td>
	 		<td style="text-align:left">General Inquiries & Requests</td>
	 		<td></td>
	 		<td style="text-align:center"><a href="/webadmin/invoice/invoice_list.php?status=inquiry&site_id=39&generic_code=GPA|GMA|GPC|GME&referer=product_ordered"  target="_blank" ><?=$generic_count['inquiry_total']?></a></td>
	 		<td style="text-align:center"><a href="/webadmin/invoice/invoice_list.php?status=proforma|sales|return&site_id=39&generic_code=GPA|GMA|GPC|GME&referer=product_ordered"  target="_blank" ><?=$generic_count['invoice_total']?></a></td>
	 		<td style="text-align:center"><a href="/webadmin/invoice/invoice_list.php?status=inquiry|proforma|sales|return&site_id=39&generic_code=GPA|GMA|GPC|GME&referer=product_ordered"  target="_blank"><?=$generic_count['grand_total']?></a></td>
	 	</tr>
</table>
<table class="wide">
	<tbody><tr>
    <td style="background:#FAFAFA"><div style="text-align:center" bis_skin_checked="1">
		<input type="button" onclick="window.open(&quot;product_ordered_xlx.php?&quot;)" value="Export Excel">
</td></tr>
</tbody></table>