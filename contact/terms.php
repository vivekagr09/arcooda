<?php 
include_once __DIR__ . '/common.inc.php';
include __DIR__ . '/../protected/build_invoice.inc.php';
  $row = $DB->query_first("SELECT * FROM terms_condition_checkout WHERE id=1 LIMIT 1");
/*echo '<pre>'; print_r($invoice); 
echo '<pre>'; print_r($invoice_products);
echo '<pre>'; print_r($invoice_charges);  
echo '<pre>'; print_r($invoice_courier_quotes);die;*/
// COde updated by swapnil to get the custom terms
// $result = [];
// $data = [];
// $query  = $DB->query("Select * from `arcooda_www`.`products` where id=".$invoice_products[0]['product_id']);
// while ($row = $DB->fetch_assoc($query)) {
// 	$result[] = $row;
// }
// if(count($result) > 0)
// {
// 	$query = $DB->query("Select * from `highwaygroup`.`terms_condition_custom_templatess` where id=".$result[0]['terms_condition_custom']);
// 	while ($row = $DB->fetch_assoc($query)) {
// 		$data[] = $row;
// 	}
// }
//end

?>
<html>
	<header>
		<title>Terms & Condition</title>
		  <link rel="stylesheet" href="/css/style.min.css" />
		  <style type="text/css">
		  	li{
		  		font-size: 16px;
		  	}
		  </style>
	</header>
	<body> 
		<table class="wide" style="border-collapse:collapse;border-style:none;">

			<tbody>
			<?php 
				if(count($invoice_products) == 1 && $invoice_products[0]["product"]->terms_condition_custom() > 0)
				{
					foreach($invoice_products as $product){
						$product_id=$product["product"]->terms_condition_custom();
						if( $product_id && $product_id > 0){
							$row = $DB->query_first("SELECT * FROM terms_condition_custom_templates WHERE id=".$product_id." LIMIT 1",MYSQLI_ASSOC);
								if($row){ ?>
										<tr>
										<td style="font-size:11px;color:#000;vertical-align:top;">	
											<div class="checkout_body" style="padding-bottom: 20px;">
												<div class="default_window">
													<div class="window_border">
														<div class="window_client">
															<h3><?= $row['template_name']?></h3>
															<div style="font-size: 16px;"><?= $row['template_description']?></div>
														</div>
													</div>
												</div>
											</div>
										</td>
									</tr>
								<?php }
						}
					}
				} else {
			?>
				<tr>
					<td style="font-size:11px;color:#000;vertical-align:top;">

						<div class="checkout_body">

							<div class="default_window">
								<div class="window_border">

									<div class="window_client">

										<?= $row['header_text']?>
										<h3>Shipping/Insurance :</h3>
										<!-- Code Updated by Swapnil on 19th July -->
										<ul>
											<?php if ($_GET['ship_insurance']=='n' || (($_GET['ship_insurance']=='o' && $_GET['terms_id'] == 0))) { ?>
												<li>You have selected Shipping <span style="color:red;font-weight:600;">without insurance</span>. Arcooda shall not be liable for any loss or damage to the goods during transit.</li>
                                            <?php } elseif(($_GET['ship_insurance']=='o' && $_GET['terms_id'] == 4) || $_GET['ship_insurance'] == 'y') { ?>
												<li>You have selected Shipping <span style="color:green;font-weight:600;">with insurance</span>. If you do not receive goods within the estimated Transit time quoted during the Checkout, please contact us immediately, so we can contact the freight company and get details regarding the status of the delivery. If you receive damaged goods, please photograph the damage and retain all relevant documentation so that an insurance claim can be made against the freight forwarders and contact us immediately as an insurance claim has to be put ASAP, usually within 24-48 hours from the time the goods were delivered.</li>
											
                                            <?php } else{ ?>

											<?php } ?>
										</ul>
										<!-- end -->
											<!-- <?php if($invoice['ship_insurance']=='n'){?>
											<?= $row['without_insurance'] ?>
											<?php }else{ ?>
												<?= $row['with_insurance'] ?>

											<?php } ?> -->

										<h3>Taxes :</h3>
											<?= $row['texes'] ?>
										<h3>Customs Entry  :</h3>
											<ul>
												<li>
													You understand that all shipments must comply with your country’s customs laws. Arcooda is not responsible for any delays, inspections, fees, refusals, or other issues caused by customs.
												</li>
												<li>
													You agree to indemnify and hold Arcooda harmless from any claims, costs, or penalties arising from customs delays, clearance issues, or incorrect information you provide.
												</li>
												<li>
													If the goods are refused entry into your country, you are responsible for all return shipping charges back to our warehouse.
												</li>
											</ul>
										<h3>Warranty :</h3>
										<ul>
											<?php
												$html .='<ul style="margin-top:10px;">';
												foreach($invoice_products as $product){ 
													$html .='<li><span style="color:blue;font-weight:600">'.$product["product"]->name().'</span> : <b>'.(($product["product"]->Warranty() == 'No Warranty') ? '<span style="color:red;font-weight:600;">'.$product["product"]->Warranty().'</span>' : '<span style="color:green;font-weight:600;">'.$product["product"]->Warranty()).'</span>' .'</b></li></br>';
												 } 
												$html .='</ul>';
											?>
											<?= str_replace('[PRODUCTS]',$html,$row['warranty'] ) ?>
										</ul>

										<h3>Returns/Exchange :</h3>
											<?= $row['return_policy'] ?>
										

									</div>
								</div>
							</div>
						</div>


					</td>
				</tr>
				<?php foreach($invoice_products as $product){
					$product_id=$product["product"]->terms_condition_custom();
					if( $product_id && $product_id > 0){
						$row = $DB->query_first("SELECT * FROM terms_condition_custom_templates WHERE id=".$product_id." LIMIT 1",MYSQLI_ASSOC);
							if($row){ ?>
									<tr>
									<td style="font-size:11px;color:#000;vertical-align:top;">	
										<div class="checkout_body" style="padding-bottom: 20px;">
											<div class="default_window">
												<div class="window_border">
													<div class="window_client">
														<h3><?= $product["product"]->name().'-'.$row['template_name']?></h3>
														<div style="font-size: 16px;"><?= $row['template_description']?></div>
													</div>
												</div>
											</div>
										</div>
									</td>
								</tr>
							<?php }
					}
				}
			}?>
			</tbody>
		</table>
	</body>
</html>