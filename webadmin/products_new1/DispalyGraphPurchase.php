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
    <!-- <body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
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
    </body> -->
    <body style="text-align:center;vertical-align:middle;width:100%;height:100%;">
        <?php 
            $item_history = getSupplierPurchaseHistory($supplier_id, $product_id);
            if(count($item_history) > 1){
        ?>
        <div id="containerchart" style="height: 500px;"></div>
        <script type="text/javascript">
            var dataval = JSON.parse('<?php echo json_encode($item_history); ?>');
            console.log('test', dataval);

            // Prepare a data object for multiple series
            var dataSet = [];
            var seriesMapping = {};

            dataval.forEach(function(entry) {
                var date = entry[0];
                var value = entry[1];

                if (!seriesMapping[date]) {
                    seriesMapping[date] = [];
                }
                seriesMapping[date].push(parseFloat(value));
            });

            // Convert the mapped data into a format suitable for AnyChart
            for (var date in seriesMapping) {
                if (seriesMapping[date].length === 1) {
                    dataSet.push([date, seriesMapping[date][0]]);
                } else {
                    seriesMapping[date].forEach(function(value, index) {
                        var seriesName = "Value " + (index + 1);
                        if (!window[seriesName]) {
                            window[seriesName] = anychart.data.set([]);
                        }
                        window[seriesName].append([date, value]);
                    });
                }
            }

            // Create a line chart
            var chart = anychart.line();
            chart.title('Purchase History');

            // Set the y-axis title
            chart.yAxis().title('Cost (USD)');

            // If there's only one series per date, plot it directly
            if (dataSet.length > 0) {
                var seriesData = anychart.data.set(dataSet);
                var lineChart = chart.line(seriesData.mapAs({ x: 0, value: 1 }));
            }

            // Plot additional series for the same date values
            for (var i = 1; i <= Object.keys(window).filter(key => key.startsWith("Value ")).length; i++) {
                var seriesName = "Value " + i;
                if (window[seriesName] && window[seriesName].mapAs) {
                    var series = chart.line(window[seriesName].mapAs({ x: 0, value: 1 }));
                    series.name(seriesName);
                }
            }

            // Set the container id for the line chart
            chart.container("containerchart");

            // Draw the line chart
            chart.draw();
        </script>
        <?php }  ?>
    </body>

</html>