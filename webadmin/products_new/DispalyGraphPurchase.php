<?php
include_once __DIR__ . '/common.inc.php';

$supplier_id  = $posted["supplier_id"];
$product_id = $posted['product_id'];
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
        <script src="https://cdn.anychart.com/releases/8.10.0/js/anychart-base.min.js"></script>
    </head>
    <body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
    	<?php 
    		 $item_history=getSupplierPurchaseHistory($supplier_id,$product_id);
    		 if(count($item_history)>1){
    	?>
    	<div id="containerchart" style="height: 500px;"></div>
    	 <script type="text/javascript">
                var dataval=JSON.parse('<?php echo json_encode($item_history); ?>');
                console.log('test',dataval);
                // create a data set on our data
                var dataSet = anychart.data.set(dataval);

                var seriesData = dataSet.mapAs({ x: 0, value: 1 });

                // create a line chart
                var chart = anychart.line();
                chart.title('Purchase History');

                // set the y axis title
                chart.yAxis().title('Cost (USD)');
                var lineChart = chart.line(seriesData);

                // set the container id for the line chart
                chart.container("containerchart");

                // draw the line chart
                chart.draw();
            </script>
    	<?php }  ?>
    </body>
</html>