<link rel="stylesheet" type="text/css" href="<?= base_url('assets/integration/prism/css.css') ?>?v=<?= av() ?>">
<script type="text/javascript" src="<?= base_url('assets/integration/prism/js.js') ?>"></script>

<?php 
function funConvertToCode($text,$lan){
	$text = implode("\n", $text);
	$text = htmlentities($text);
	$text = '<pre class="language-'.$lan.'"><code class="language-'.$lan.'">'.$text.'</code></pre>';
	return $text;
}

$base_url  = base_url();
?>

<script type="text/javascript" src="<?= base_url('assets/plugins/html2canvas/html2canvas.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/plugins/html2canvas/jspdf.debug.js') ?>"></script>

<script type="text/javascript">
	function download(ele){
		$(".no-pdf").hide();
		$(".btn-export-pdf").btn("loading");

		var HTML_Width = $(ele).width();
		var HTML_Height = $(ele).height();

		var top_left_margin = 15;
		var PDF_Width = HTML_Width+(top_left_margin*2);
		var PDF_Height = (PDF_Width*1.5)+(top_left_margin*2);
		var canvas_image_width = HTML_Width;
		var canvas_image_height = HTML_Height;

		var totalPDFPages = Math.ceil(HTML_Height/PDF_Height)-1;

		html2canvas($(ele)[0],{allowTaint:true}).then(function(canvas) {
			canvas.getContext('2d');
			
			var imgData = canvas.toDataURL("image/jpeg", 1.0);
			var pdf = new jsPDF('p', 'pt',  [PDF_Width, PDF_Height]);
		    pdf.addImage(imgData, 'JPG', top_left_margin, top_left_margin,canvas_image_width,canvas_image_height);
			
			for (var i = 1; i <= totalPDFPages; i++) { 
				pdf.addPage(PDF_Width, PDF_Height);
				pdf.addImage(imgData, 'JPG', top_left_margin, -(PDF_Height*i)+(top_left_margin*4),canvas_image_width,canvas_image_height);
			}
			
		    pdf.save("<?= __('admin.payment_api_documentation') ?>.pdf");

		    $(".no-pdf").show();
		    $(".btn-export-pdf").btn("reset");
        });
	}
</script>

<div class="row" id="page-doc">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h5 class="pull-left"><?= __( 'admin.integration_of' ) ?><?= isset($toolsname) ? $toolsname : $module['name']; ?></h5>
				<div class="pull-right">
					<?php if($module_key == 'affiliate_register_api'){ ?>
	    				<button type="button" onclick="download('#page-doc')" class="btn btn-export-pdf btn-primary btn-sm"><?= __( 'admin.download_as_pdf' ) ?></button>
	    			<?php } ?>
	    		</div>
			</div>

			<div class="card-body">
				<div class="integration-modules-ins">
					<?= $views ?>

					<?php if($module_key == 'affiliate_register_api'){ ?>
						<div id="affiliate_register_api">
							<p><?= __( 'admin.this_page_contains_api_' ) ?></p>

							<p class="text-info"><?= __( 'admin.download_postman_example' ) ?> <a target="_blank" href="<?= base_url('assets/Affiliate-Pro.postman_collection.json') ?>">Affiliate-Pro.postman_collection.json</a>. <?= __( 'admin.how_to_import_postman' ) ?> <a href="https://learning.postman.com/docs/running-collections/working-with-data-files/" target="_target" ><i class="fa fa-external-link"></i></a></p>
							<p class="text-info"><?= __( 'admin.download_php_boot_example' ) ?> <a download target="_blank" href="<?= base_url('assets/register-api-example.zip') ?>"> <?= __( 'admin.download' ) ?> </a></p>


							<h5 class="mt-5"><?= __( 'admin.get_custom_registration_field' ) ?></h5>
							<hr>
							<p><?= __( 'admin.get_custom_registration_field_2' ) ?></p>


							<h6><?= __('admin.uri') ?></h6>
							<?php
								$code = array();
								$code[] = 'GET '. base_url('/api/register_custom_field');
								echo funConvertToCode($code,'html');
							?>

							<h6 class="mt-3"><?= __( 'admin.exmple_resopnse' ) ?></h6>
							<?php
								$code = array();
								$code[] = '{';
								$code[] = '    "fields": [';
								$code[] = '        {';
								$code[] = '            "type": "select",';
								$code[] = '            "required": false,';
								$code[] = '            "label": "Select",';
								$code[] = '            "className": "form-control",';
								$code[] = '            "name": "custom_select-1594271473044",';
								$code[] = '            "min": "",';
								$code[] = '            "max": "",';
								$code[] = '            "maxlength": "",';
								$code[] = '            "values": [';
								$code[] = '                {';
								$code[] = '                    "label": "Option 1",';
								$code[] = '                    "value": "option-1",';
								$code[] = '                    "selected": "true"';
								$code[] = '                },';
								$code[] = '                {';
								$code[] = '                    "label": "Option 2",';
								$code[] = '                    "value": "option-2"';
								$code[] = '                },';
								$code[] = '                {';
								$code[] = '                    "label": "Option 3",';
								$code[] = '                    "value": "option-3"';
								$code[] = '                }';
								$code[] = '            ],';
								$code[] = '            "mobile_validation": false';
								$code[] = '        },';
								$code[] = '        {';
								$code[] = '            "type": "text",';
								$code[] = '            "required": true,';
								$code[] = '            "label": "Custom FIeld",';
								$code[] = '            "className": "form-control",';
								$code[] = '            "name": "custom_text-1594269069679",';
								$code[] = '            "min": "",';
								$code[] = '            "max": "",';
								$code[] = '            "maxlength": "",';
								$code[] = '            "values": null,';
								$code[] = '            "mobile_validation": false';
								$code[] = '        }';
								$code[] = '    ]';
								$code[] = '}';
								echo funConvertToCode($code,'javascript');
							?>



							<h5 class="mt-5"><?= __( 'admin.create_affiliate_registration' ) ?></h5>
							<hr>
							<p><?= __( 'admin.create_affiliate_registration_api_info' ) ?></p>


							<h6><?= __('admin.uri') ?></h6>
							<?php
								$code = array();
								$code[] = 'POST '. base_url('user/registarion');
								echo funConvertToCode($code,'html');
							?>

							<h6 class="mt-5"><?= __( 'admin.request_body' ) ?></h6>
							<table class="table-inverse table">
								<thead>
									<tr>
										<th width="200px"><?= __( 'admin.field' ) ?></th>
										<th width="100px"><?= __( 'admin.type' ) ?></th>
										<th><?= __( 'admin.description' ) ?></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<th><?= __('admin.first_name') ?></th>
										<th><?= __('admin.string') ?></th>
										<td><?= __( 'admin.the_first_name_of_desc' ) ?></td>
									</tr>

									<tr>
										<th><?= __('admin.last_name') ?></th>
										<th><?= __('admin.string') ?></th>
										<td><?= __( 'admin.the_users_last_name_desc' ) ?></td>
									</tr>

									<tr>
										<th><?= __('admin.email') ?></th>
										<th><?= __('admin.email') ?></th>
										<td><?= __( 'admin.the_users_email_adress_desc' ) ?></td>
									</tr>

									<tr>
										<th><?= __('admin.username') ?></th>
										<th><?= __('admin.string') ?></th>
										<td><?= __( 'admin.the_username_of_desc' ) ?></td>
									</tr>

									<tr>
										<th><?= __('admin.password') ?></th>
										<th><?= __('admin.string') ?></th>
										<td><?= __( 'admin.the_users_password_desc' ) ?></td>
									</tr>
									<?php 
										foreach ($customField as $key => $value) { 
											if($value['type'] == 'header') continue; 
									?>
										<tr>
											<th><?= $value['name'] ?></th>
											<th><?= __('admin.custom_field') ?></th>
											<td><?= __('admin.the_custom_fields') ?><b><?= $value['label'] ?></b></td>
										</tr>
									<?php } ?>

									<tr>
										<th><?= __('admin.terms') ?></th>
										<th><?= __('admin.boolean') ?></th>
										<td><?= __( 'admin.accept_terms_and_cond' ) ?></td>
									</tr>
								</tbody>
							</table>


							<h6 class="mt-5"><?= __( 'admin.example_request_json' ) ?></h6>

							<?php
								$code = array();
								$code[] = "{";
								$code[] = "	'firstname':'Keri',";
								$code[] = "	'lastname':'Taylor',";
								$code[] = "	'username':'taylor.keri',";
								$code[] = "	'email':'taylor.keri@gmail.com',";
								$code[] = "	'password':'password',";
								$code[] = "	'terms':'true',";
								$code[] = "}";
								echo funConvertToCode($code,'javascript');
							?>

							<h6 class="mt-5"><?= __( 'admin.response' ) ?></h6>
							<p><?= __( 'admin.response_desc' ) ?></p>

							<b class="mt-4"><?= __( 'admin.erros' ) ?></b>
							<p><?= __( 'admin.error_desc' ) ?></p>

							<b class="mt-4"><?= __( 'admin.success' ) ?></b>
							<p><?= __( 'admin.success_desc' ) ?></p>

						</div>
					<?php } ?>

					<?php if($module_key == 'wp_forms'){ ?>
						<h2><?= __( 'admin.wpfroms_plugin_integration' ) ?></h2>
						<div>
							<ol class="installed-step">
								<li><?= __( 'admin.login_to_your_wp' ) ?></li>
								<li><?= __( 'admin.go_to_plugin_wp' ) ?></li>
								<li><?= __( 'admin.instal_new_plaugin_wp' ) ?></li>
								<li><?= __( 'admin.create_thank_you_pahe' ) ?></li>
								<li><?= __( 'admin.go_to_wp_form_edit_desc' ) ?></li>
								<li><?= __( 'admin.create_your_banner_in_aff' ) ?></li>
								<li><?= __( 'admin.after_finish_step_6_wp' ) ?>
									<?= __( 'admin.example_here' ) ?> 
									<button class="btn_info" data-toggle="modal" data-target="#myModal"><i class="fa fa-info"></i></button>
								</li>
								<li><?= __( 'admin.thats_it_wpform_aff' ) ?></li>
							</ol>
							<?php
								$code = array();
								$code[] = '<script type="text/javascript" src="'. $base_url .'integration/general_integration"></script>';
								$code[] = '<script type="text/javascript">';
								$code[] = '	AffTracker.setWebsiteUrl( "WebsiteUrl" );';
								$code[] = '	AffTracker.createAction( "actionCode" )';
								$code[] = '</script>';
								
								echo funConvertToCode($code,'html');
							?>

							<p>
								<h6><?= __( 'admin.all_possible_tracking_parameter' ) ?>s</h6>
								<div class="well">
									<strong>websiteUrl</strong>       : <?= __( 'admin.website_root_url' ) ?> <br>
									<strong>actionCode</strong>       : <?= __( 'admin.action_code_you_have_desc' ) ?><br>
								</div>
							</p>

							<h6><?= __( 'admin.available_action_code_is_here' ) ?></h6>
							<ul>
								<?php foreach ($action_codes as $key => $value) { ?>
									<li> <?= $value['action_code'] ?> </li>
								<?php } ?>
							</ul>
						</div>
					<?php } ?>
					
					<?php if($module_key == 'woocommerce'){ ?>
						<p><?= __( 'admin.integrate_aff_script_download_woocomm_module_here' ) ?> <a href="<?= base_url('integration/download_plugin/woocommerce') ?>"><?= __( 'admin.wordpress_module' ) ?></a> <?= __( 'admin.and_follow_following_steps' ) ?></p>
						<hr>

						<ol class="installed-step">
							<li><?= __( 'admin.login_to_your_wp' ) ?></li>
							<li><?= __( 'admin.go_to_plugin_wp' ) ?></li>
							<li><?= __( 'admin.upload_new_plugin_zip_instr' ) ?></li>
							<li><?= __( 'admin.install_the_plugin_instr' ) ?></li>
							<li><?= __( 'admin.activate_the_plugin_instr' ) ?></li>
						</ol>
					<?php } ?>

					<?php if($module_key == 'wp_show_affiliate_id'){ ?>
						<p><?= __( 'admin.integrate_aff_plugin_download_woocomm_module_here' ) ?> <a href="<?= base_url('integration/download_plugin/show_affiliate_id') ?>"><?= __( 'admin.wordpress_module' ) ?></a> <?= __( 'admin.and_follow_following_steps' ) ?></p>
						<hr>

						<ol class="installed-step">
							<li><?= __( 'admin.login_to_your_wp' ) ?></li>
							<li><?= __( 'admin.go_to_plugin_wp' ) ?></li>
							<li><?= __( 'admin.upload_new_plugin_zip_instr' ) ?></li>
							<li><?= __( 'admin.install_the_plugin_instr' ) ?></li>
							<li><?= __( 'admin.activate_plugin_you_completed_show_aff_id' ) ?></li>
						</ol>
					<?php } ?>

					<?php if($module_key == 'show_affiliate_id'){ ?>
						<p><?= __( 'admin.if_you_want_to_show_affid_in_ex_desc' ) ?></p>
						<hr>


						<?php
							$code = array();
							$code[] = '<script type="text/javascript" src="'. base_url('integration/show_affiliate_id') .'"></script>';
							$code[] = '<script type="text/javascript">';
							$code[] = '	var af_df_setting = {';
							$code[] = '	  position:\'bottom\',';
							$code[] = '	  text:\'Affiliate ID is {id}\',';
							$code[] = '	}';
							$code[] = '</script>';
							echo funConvertToCode($code,'html');
						?>

						<br>
						<b><?= __( 'admin.where' ) ?>:</b>
						<ul>
							<li><strong><?= __( 'admin.position' ) ?></strong> = <?= __( 'admin.default_is' ) ?> <?= __( 'admin.bottom' ) ?>
								<ol>
									<li><?= __( 'admin.bottom' ) ?> </li>
									<li><?= __( 'admin.top' ) ?></li>
									<li><?= __( 'admin.left' ) ?></li>
									<li><?= __( 'admin.right' ) ?></li>
									<li><?= __( 'admin.top_left' ) ?></li>
									<li><?= __( 'admin.top_right' ) ?></li>
									<li><?= __( 'admin.bottom_left' ) ?></li>
									<li><?= __( 'admin.bottom_right' ) ?></li>
								</ol>
							</li>
							<li><strong><?= __( 'admin.text' ) ?></strong> = <?= __( 'admin.you_can_cust_txt_line' ) ?></li>
						</ul> 
						
					<?php } ?>

					<?php if($module_key == 'postback'){ ?>
						<p><?= __( 'admin.int_instr_postback_p1' ) ?></p>

						<p><?= __( 'admin.int_instr_postback_p2' ) ?></p>

						<b><?= __( 'admin.int_instr_postback_p3' ) ?></b>

						<?php
							$code = array();
							$code[] = 'https://www.domain.co.nz/?city={city}&regionCode={regionCode}&regionName={regionName}&countryCode={countryCode}&countryName={countryName}&continentName={continenName}&timezone={timezone}&currencyCode={currencyCode}&currencySymbol={currencySymbol}&ip={ip}&type={type}&id={id}&custom_field1={custom_field1}&custom_field2={custom_field2}';
							echo funConvertToCode($code,'html');
						?>

						<br>
						<b><?= __( 'admin.where' ) ?>:</b>
						<ul>
							<li>{city} - <?= __('admin.city_name') ?></li>
							<li>{regionCode} - <?= __('admin.region_code') ?></li>
							<li>{regionName} - <?= __('admin.region_name') ?></li>
							<li>{countryCode} - <?= __('admin.country_code') ?></li>
							<li>{countryName} - <?= __('admin.continent_name') ?></li>
							<li>{continentName} - <?= __('admin.continent_name') ?></li>
							<li>{timezone} - <?= __('admin.time_zone') ?></li>
							<li>{currencyCode} - <?= __('admin.currency_code') ?></li>
							<li>{currencySymbol} - <?= __('admin.currency_symbol') ?></li>
							<li>{ip} - <?= __('admin.ip_address') ?></li>
							<li>{type} - <?= __('admin.type') ?> (action, general_click, product_click, sale) </li>
							<li>{id} - <?= __('admin.if_its') ?> type=sale <?= __('admin.than') ?> saleid_param_info <?= __('admin.otherwise_its') ?> clickid_param_info</li>
							<li>{custom_field1} - <?= __('admin.custom_field') ?> 1</li>
							<li>{custom_field2} - <?= __('admin.custom_field') ?> 2</li>
						</ul>

						<br>
						
						<b><?= __( 'admin.destination_example' ) ?>:</b>
						<p><?= __( 'admin.advertisers_website_destination' ) ?></p>
						<?php
							$code = array();
							$code[] = 'https://www.domain.co.nz/?city=New York&regionCode=NY&regionName=New York&countryCode=US&countryName=United States&continentName=NA&timezone=North America&currencyCode=$&currencySymbol=USD&ip=170.171.1.24&type=general_click&id=1542';
							echo funConvertToCode($code,'html');
						?>

					<?php } ?>

					<?php if($module_key == 'php_api_library'){ ?>
						<p><?= __( 'admin.for_tracking_order_u_can_use_php' ) ?></p>

						<h2><?= __( 'admin.cmn_tracking_script' ) ?></h2>
						<div>
							<p><?= __( 'admin.add_script_in_footer_instr' ) ?></p>
							<?php
							$code = array();
							$code[] = '<script type="text/javascript" src="'. $base_url .'integration/general_integration"></script>';
							echo funConvertToCode($code,'html');
							?>
						</div>
						<br><hr>

						
						<h2><?= __( 'admin.tracking_order' ) ?></h2>
						<p><?= __( 'admin.add_this_code_on' ) ?></p>
						<p><?= __( 'admin.include_php_lib_in_ur_code_inst' ) ?> <a href="<?= base_url('integration/download_plugin/php_api_library') ?>"><?= __( 'admin.download' ) ?></a></p>
						<?php
							$code = array();
							$code[] = '<?php';
							$code[] = 'require "affiliatepro.php";';
							$code[] = '';
							$code[] = '$tracking = new AffiliatePro();';
							$code[] = '$tracking->orderId = "OrderId";';
							$code[] = '$tracking->orderCurrency = "OrderCurrency";';
							$code[] = '$tracking->orderTotal = "OrderTotal";';
							$code[] = '$tracking->productIds = array("product_id1", "product_id1", "...");';
							$code[] = '$tracking->websiteUrl = "https://www.abc.com";';
							$code[] = '';
							$code[] = '//set custom value';
							$code[] = '$tracking->setData("custom_data_1","value");';
							$code[] = '$tracking->setData("custom_data_2","value");';
							$code[] = '$tracking->setData("custom_data_...","value");';
							$code[] = '';
							$code[] = '//place order in affiliate script';
							$code[] = '$tracking->placeOrder();';
							echo funConvertToCode($code,'php');
						?>

						<div class="well">
							<strong>websiteUrl</strong>    : <?= __( 'admin.website_root_url' ) ?> <br>
							<strong>orderId</strong>       : <?= __( 'admin.unique_orer_id' ) ?> <br>
							<strong>orderCurrency</strong> : <?= __( 'admin.currency_symbol_of_order' ) ?> <br>
							<strong>orderTotal</strong>    : <?= __( 'admin.total_amt_of_order' ) ?> <br>
							<strong>productIds</strong>    : <?= __( 'admin.product_ids_of_order' ) ?> <br>
						</div>


					<?php } ?>

							<?php if($module_key == 'wp_user_register'){ ?>
								<p><?= __( 'admin.wp_woocom_bridge_pugin_download' ) ?>
									<a href="<?= base_url('integration/download_plugin/wp_user_register') ?>"><?= __( 'admin.download_plugin' ) ?></a> <?= __( 'admin.and_folow_the_following_steps' ) ?> <?= __('admin.steps') ?>.</p>
									<hr>

									<ol class="installed-step">
										<li><?= __( 'admin.download_wp_plugin_from_this_page' ) ?></li>
										<li><?= __( 'admin.login_to_your_wp' ) ?></li>
										<li><?= __( 'admin.go_to_plugin_wp' ) ?></li>
										<li><?= __( 'admin.upload_new_plugin_zip_instr' ) ?></li>
										<li><?= __( 'admin.install_the_plugin_instr' ) ?></li>
										<li><?= __( 'admin.activate_affiliate_plugin' ) ?></li>
										<li><?= __( 'admin.goto_wp_menu_and_you_see_plugin_name' ) ?> 
											<button class="btn_info" data-toggle="modal" data-target="#myModal_bridge"><i class="fa fa-info"></i></button></li>
											<li><?= __( 'admin.you_can_set_register_only_wp_reg_instr' ) ?></li>
										</ol>
							<?php } ?>

								<?php if($module_key == 'bigcommerce'){ ?>
								
									<p><?= __( 'admin.integrate_aff_scr_big_commerce' ) ?>
									<hr>

									<ol class="installed-step">
										<li>><?= __( 'admin.login_to_bigc_dashboard' ) ?></li>
										<li>><?= __( 'admin.from_left_side_panel_open' ) ?> <code class="code_"><?= __( 'admin.store_front' ) ?> -> <?= __( 'admin.script_manager' ) ?></code></li>
										<li>
											><?= __( 'admin.create_new_script' ) ?>

											<ol class="installed-step">
												<div class="step"><b>><?= __( 'admin.name_of_script' ) ?> : </b> <?= __('admin.affiliate_script') ?> </div>
												<div class="step"><b>><?= __( 'admin.description' ) ?> : </b>  <?= __('admin.affiliate_tracking_code') ?> </div>
												<div class="step"><b>><?= __( 'admin.location_on_page' ) ?> : </b>  <?= __('admin.footer') ?> </div>
												<div class="step"><b>><?= __( 'admin.select_pages_where' ) ?> : </b> <?= __('admin.all_pages') ?> </div>
												<div class="step"><b>><?= __( 'admin.script_type' ) ?> : </b> <?= __('admin.script') ?> </div>
												<div class="step"><b>><?= __( 'admin.script_content' ) ?> : </b> </div>

											<?php
												$code = array();
												$code[] = '<script type="text/javascript">';
												$code[] = '	if("{{ page_type }}" == "product"){';
												$code[] = '		{{ inject "data" product }}';
												$code[] = '		var productData = JSON.parse({{jsContext}});';
												$code[] = '		AffTracker.setWebsiteUrl(window.location.hostname);';
												$code[] = '		AffTracker.productClick( productData["data"]["id"] );';
												$code[] = '	}';
												$code[] = '	';
												$code[] = '	if("{{ page_type }}" == "orderconfirmation"){';
												$code[] = '		fetch("/api/storefront/order/{{checkout.order.id}}", {credentials: "include"})';
												$code[] = '		.then(function(response) {';
												$code[] = '			return response.json();';
												$code[] = '		})';
												$code[] = '		.then(function(orderDetails) {';
												$code[] = '			var product_ids = "";';
												$code[] = '			orderDetails.lineItems.physicalItems.forEach(function(j){';
												$code[] = '			 	product_ids += product_ids ? "," + j["productId"] : j["productId"]';
												$code[] = '			})';
												$code[] = '			AffTracker.setWebsiteUrl(window.location.hostname);';
												$code[] = '			';
												$code[] = '			AffTracker.setData( "custom_data_1", "value" );';
												$code[] = '			AffTracker.setData( "custom_data_2", "value" );';
												$code[] = '			AffTracker.setData( "custom_data_...", "value" );';
												$code[] = '			';
												$code[] = '			AffTracker.add_order({';
												$code[] = '				order_id       : "{{checkout.order.id}}",';
												$code[] = '				order_currency : orderDetails.currency.code,';
												$code[] = '				order_total    : orderDetails.orderAmount,';
												$code[] = '				product_ids    : product_ids,';
												$code[] = '			})';
												$code[] = '		});';
												$code[] = '	}';
												$code[] = '</script>';
												
												echo funConvertToCode($code,'html');
											?>
											</ol>
										</li>

										<li>
											<?= __('admin.create_new_script') ?>
											<ol class="installed-step">
												<div class="step"><b><?= __( 'admin.name_of_script' ) ?> : </b>  <?= __('admin.affiliate_script') ?> </div>
												<div class="step"><b><?= __( 'admin.description' ) ?> : </b>  <?= __('admin.affiliate_tracking_code_helper') ?> </div>
												<div class="step"><b><?= __( 'admin.location_on_page' ) ?> : </b> <?= __('admin.head') ?> </div>
												<div class="step"><b>><?= __( 'admin.select_pages_where' ) ?> : </b> <?= __('admin.all_pages') ?> </div>
												<div class="step"><b>><?= __( 'admin.script_type' ) ?> : </b> <?= __('admin.script') ?> </div>
												<div class="step"><b>><?= __( 'admin.script_content' ) ?> : </b> </div>

												<?php
												$code = array('<script type="text/javascript">');
												$bigcommercejs = file_get_contents(base_url('integration/bigcommerce'));
												$stringArr = explode("\n", $bigcommercejs);
												$code  = array_merge($code, $stringArr);
												$code[] = '</script>';
												echo funConvertToCode($code,'html');
												?>
											</ol>
										</li>
										<li><?= __( 'admin.congrats_for_installed_aff_pro' ) ?></li>
									</ol>
								<?php } ?>

								<?php if($module_key == 'prestashop'){ ?>
									<p><?= __( 'admin.int_aff_prestashop_download_from_here' ) ?> <a href="<?= base_url('integration/download_plugin/prestashop') ?>"><?= __( 'admin.prestashop_module' ) ?></a> <?= __( 'admin.and_follow_following_steps' ) ?></p>
									
									<ol class="installed-step">
										<li><?= __( 'admin.log_in_to_prestashop' ) ?>.</li>
										<li><?= __( 'admin.prestashop_int_step_1' ) ?></li>
										<li><?= __( 'admin.prestashop_int_step_2' ) ?></li>
										<li><?= __( 'admin.prestashop_int_step_3' ) ?></li>
										<li><?= __( 'admin.prestashop_int_step_4' ) ?></li>
										<li><?= __( 'admin.prestashop_int_step_5' ) ?></li>
										<li><?= __( 'admin.prestashop_int_step_6' ) ?></li>
									</ol>
								<?php } ?>

								<?php if($module_key == 'xcart'){ ?>
									<p><?= __('admin.integrate_affiliate_script_into_xcart') ?></p>
									
									<ol class="installed-step">
										<li>
											<?= __('admin.open_file' ) ?><code class="code_">/skins/customer/header/parts/script_config.twig</code><?= __('admin.add_following_code_at_the_end_file') ?> 

											<?php
												$code = array();
												$code[] = '<script type="text/javascript" src="'. base_url('integration/xcart') .'"></script>';
												echo funConvertToCode($code,'html');
											?>
											
										</li>
										<li>
											<?= __('admin.open_file') ?><code class="code_">/classes/XLite/Controller/Customer/Product.php</code><?= __('admin.add_following_code_before' ) ?> <code class="code_">parent::handleRequest();</code> <?= __('admin.line') ?>
											
											<?php
											$code = array();
											$code[] = '/* AFFILIATE PRO integration */';
											$code[] = '	$ipaddress = "";';
											$code[] = '	if (getenv("HTTP_CLIENT_IP")) $ipaddress           = getenv("HTTP_CLIENT_IP");';
											$code[] = '	else if(getenv("HTTP_X_FORWARDED_FOR")) $ipaddress = getenv("HTTP_X_FORWARDED_FOR");';
											$code[] = '	else if(getenv("HTTP_X_FORWARDED")) $ipaddress     = getenv("HTTP_X_FORWARDED");';
											$code[] = '	else if(getenv("HTTP_FORWARDED_FOR")) $ipaddress   = getenv("HTTP_FORWARDED_FOR");';
											$code[] = '	else if(getenv("HTTP_FORWARDED")) $ipaddress       = getenv("HTTP_FORWARDED");';
											$code[] = '	else if(getenv("REMOTE_ADDR")) $ipaddress          = getenv("REMOTE_ADDR");';
											$code[] = '	else $ipaddress                                    = "UNKNOWN";';
											$code[] = '	$affliate_cookie = (isset($_GET["af_id"]) ? $_GET["af_id"] : (isset($_COOKIE["af_id"]) ? $_COOKIE["af_id"] : "") ); ';
											$code[] = '	$protocol = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? "https" : "http");';
											$code[] = '	$base_url = $protocol . "://" . $_SERVER["HTTP_HOST"];';
											$code[] = '	$complete_url =   $base_url . $_SERVER["REQUEST_URI"];';
											$code[] = '	$affiliateData = array(';
											$code[] = '	    "product_id"       => $this->getProduct()->getId(),';
											$code[] = '	    "af_id"            => $affliate_cookie,';
											$code[] = '	    "ip"               => $ipaddress,';
											$code[] = '	    "base_url"         => base64_encode($base_url),';
											$code[] = '	    "script_name"      => "xcart",';
											$code[] = '	    "current_page_url" => base64_encode($complete_url),';
											$code[] = '	);';
											$code[] = '	$context_options = stream_context_create(array(';
											$code[] = '	    "http"=>array(';
											$code[] = '	        "method"=>"GET",';
											$code[] = '	        "header"=> "User-Agent: ". (isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : ""),';
											$code[] = '	    )';
											$code[] = '	)); ';
											$code[] = '	file_get_contents("'. base_url('integration/addClick') .'?".http_build_query($affiliateData), false, $context_options);';
											$code[] = '/* end of AFFILIATE PRO integration */';
											
											echo funConvertToCode($code,'php');
											?>
											
										</li>
										<li>
											<?= __('admin.open_file') ?><code class="code_">/classes/XLite/Controller/Customer/CheckoutSuccess.php</code><?= __('admin.add_following_code_before') ?><code class="code_">parent::handleRequest();</code> <?= __('admin.line') ?>
											
											<?php
											$code = array();
											$code[] = '/* AFFILIATE PRO integration */';
											$code[] = '    $ipaddress = "";';
											$code[] = '';
											$code[] = '    if (getenv("HTTP_CLIENT_IP")) $ipaddress = getenv("HTTP_CLIENT_IP");';
											$code[] = '    else if(getenv("HTTP_X_FORWARDED_FOR")) $ipaddress = getenv("HTTP_X_FORWARDED_FOR");';
											$code[] = '    else if(getenv("HTTP_X_FORWARDED")) $ipaddress = getenv("HTTP_X_FORWARDED");';
											$code[] = '    else if(getenv("HTTP_FORWARDED_FOR")) $ipaddress = getenv("HTTP_FORWARDED_FOR");';
											$code[] = '    else if(getenv("HTTP_FORWARDED")) $ipaddress = getenv("HTTP_FORWARDED");';
											$code[] = '    else if(getenv("REMOTE_ADDR")) $ipaddress = getenv("REMOTE_ADDR");';
											$code[] = '    else $ipaddress = "UNKNOWN";';
											$code[] = '';
											$code[] = '    $protocol = ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? "https" : "http");';
											$code[] = '    $base_url = $protocol . "://" . $_SERVER["HTTP_HOST"];';
											$code[] = '    $affliate_cookie = (isset($_GET["af_id"]) ? $_GET["af_id"] : (isset($_COOKIE["af_id"]) ? $_COOKIE["af_id"] : "") ); ';
											$code[] = '';
											$code[] = '    $affiliateData = array(';
											$code[] = '        "order_id"       => $this->getOrder()->getOrderNumber(),';
											$code[] = '        "order_currency" => $this->getOrder()->getCurrency()->getCurrencySymbol(false),';
											$code[] = '        "order_total"    => $this->getOrder()->getPaidTotal(),';
											$code[] = '        "product_ids"    => array(),';
											$code[] = '        "af_id"          => $affliate_cookie,';
											$code[] = '        "ip"             => $ipaddress,';
											$code[] = '        "base_url"       => base64_encode($base_url),';
											$code[] = '        "script_name"    => "xcart",';
											$code[] = '    );';
											$code[] = '';
											$code[] = '    foreach ($this->getOrder()->getItems() as $item) { $affiliateData["product_ids"][] = $item->getItemId(); }';
											$code[] = '';
											$code[] = '    $context_options = stream_context_create(array(';
											$code[] = '        "http" => array(';
											$code[] = '          "method" => "GET",';
											$code[] = '          "header" => "User-Agent: ". (isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : ""),';
											$code[] = '        )';
											$code[] = '    ));';
											$code[] = '';
											$code[] = '    file_get_contents("'. base_url('integration/addOrder') .'?".http_build_query($affiliateData), false, $context_options);';
											$code[] = '/* end of AFFILIATE PRO integration */';
											
											echo funConvertToCode($code,'php');
											?>
											
										</li>
										<li> <?= __('admin.clear_files_cache') ?>
											<ol class="installed-step">
												<li><?= __('admin.goto_admin_dashboard') ?></li>
												<li><?= __('admin.click_on') ?><b><?= __('admin.system_tool') ?></b> <?= __('admin.from_left_menu') ?></li>
												<li><?= __('admin.click_on') ?><b><?= __('admin.cache_management') ?></b> <?= __('admin.in_system_tool_menu') ?></li>
												<li><?= __('admin.click_on_start_button_in') ?><b><?= __('admin.redeploy_the_store') ?></b> <?= __('admin.section') ?></li>
											</ol>
										</li>
										<li><?= ('admin.you_have_completed_affiliate_pro_module_install') ?></li>
									</ol>
								<?php } ?>

								<?php if($module_key == 'zencart'){ ?>
									<p><?= __('admin.integrate_affiliate_script_into_zencart') ?></p>

									<div class="alert alert-info">
										<?= __('admin.for_find_your_temlate_directory_name_go') ?><code class="code_"><?= __('admin.admin_tools_template_selection') ?></code>
										<?= __('admin.you_can_see_your_template_directory_name') ?> <b>your_template_directory</b>
									</div>
									<ol class="installed-step">
										<li>
											<?= __('admin.open_file' ) ?> <code class="code_">/includes/templates/your_template_directory/common/html_header.php</code> <?= __('admin.add_following_code_at_the_end_file') ?>

											<?php
											$code = array();
											$code[] = '<script type="text/javascript" src="'. base_url('integration/zencart') .'"></script>';
											echo funConvertToCode($code,'html');
											?>
											
										</li>
										<li>
											<?= __('admin.open_file' ) ?> <code class="code_">/includes/templates/your_template_directory/templates/tpl_product_info_display.php</code> <?= __('admin.add_following_code_after_at_the_end_file') ?>
											
											<?php
											$code = array();
											$code[] = '<?php';
											$code[] = '	/* AFFILIATE PRO integration */';
											$code[] = '	$ipaddress = "";';
											$code[] = '	if (getenv("HTTP_CLIENT_IP")) $ipaddress           = getenv("HTTP_CLIENT_IP");';
											$code[] = '	else if(getenv("HTTP_X_FORWARDED_FOR")) $ipaddress = getenv("HTTP_X_FORWARDED_FOR");';
											$code[] = '	else if(getenv("HTTP_X_FORWARDED")) $ipaddress     = getenv("HTTP_X_FORWARDED");';
											$code[] = '	else if(getenv("HTTP_FORWARDED_FOR")) $ipaddress   = getenv("HTTP_FORWARDED_FOR");';
											$code[] = '	else if(getenv("HTTP_FORWARDED")) $ipaddress       = getenv("HTTP_FORWARDED");';
											$code[] = '	else if(getenv("REMOTE_ADDR")) $ipaddress          = getenv("REMOTE_ADDR");';
											$code[] = '	else $ipaddress                                    = "UNKNOWN";';
											$code[] = '';
											$code[] = '	$affliate_cookie = (isset($_GET["af_id"]) ? $_GET["af_id"] : (isset($_COOKIE["af_id"]) ? $_COOKIE["af_id"] : "") ); ';
											$code[] = '	$protocol = ((isset($_SERVER[\'HTTPS\']) && $_SERVER[\'HTTPS\'] == "on") ? "https" : "http");';
											$code[] = '	$base_url = $protocol . "://" . $_SERVER[\'HTTP_HOST\'];';
											$code[] = '	$complete_url =   $base_url . $_SERVER["REQUEST_URI"];';
											$code[] = '';
											$code[] = '	$affiliateData = array(';
											$code[] = '		"product_id"       => $products_id_current,';
											$code[] = '		"af_id"            => $affliate_cookie,';
											$code[] = '		"ip"               => $ipaddress,';
											$code[] = '		"base_url"         => base64_encode(HTTP_SERVER.DIR_WS_CATALOG),';
											$code[] = '		"script_name"      => "zencart",';
											$code[] = '		"current_page_url" => base64_encode($complete_url),';
											$code[] = '	);';
											$code[] = '';
											$code[] = '	$context_options = stream_context_create(array(';
											$code[] = '		"http"=>array(';
											$code[] = '			"method"=>"GET",';
											$code[] = '			"header"=> "User-Agent: ". (isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : ""),';
											$code[] = '		)';
											$code[] = '	)); ';
											$code[] = '	';
											$code[] = '	file_get_contents("'. base_url('integration/addClick') .'?".http_build_query($affiliateData), false, $context_options);';
											$code[] = '	/* end of AFFILIATE PRO integration */';
											$code[] = '?>';
											echo funConvertToCode($code,'php');
											?>
											
										</li>
										<li>
											<?= __('admin.open_file' ) ?> <code class="code_">/includes/templates/your_template_directory/templates/tpl_checkout_success_default.php</code> <?= __('admin.add_following_code_at_the_end_file') ?>

											<div class="alert alert-info"><?= ('admin.if_you_can_not_find_file_search_inside') ?> <b>template_default</b> <?= __('admin.folder') ?></div>

											<?php
											$code = array();
											$code[] = '<?php';
											$code[] = '/* AFFILIATE PRO integration */';
											$code[] = '	$ipaddress = "";';
											$code[] = '	if (getenv("HTTP_CLIENT_IP")) $ipaddress = getenv("HTTP_CLIENT_IP");';
											$code[] = '	else if(getenv("HTTP_X_FORWARDED_FOR")) $ipaddress = getenv("HTTP_X_FORWARDED_FOR");';
											$code[] = '	else if(getenv("HTTP_X_FORWARDED")) $ipaddress = getenv("HTTP_X_FORWARDED");';
											$code[] = '	else if(getenv("HTTP_FORWARDED_FOR")) $ipaddress = getenv("HTTP_FORWARDED_FOR");';
											$code[] = '	else if(getenv("HTTP_FORWARDED")) $ipaddress = getenv("HTTP_FORWARDED");';
											$code[] = '	else if(getenv("REMOTE_ADDR")) $ipaddress = getenv("REMOTE_ADDR");';
											$code[] = '	else $ipaddress = "UNKNOWN";';
											$code[] = '';
											$code[] = '	$protocol = ((isset($_SERVER[\'HTTPS\']) && $_SERVER[\'HTTPS\'] == "on") ? "https" : "http");';
											$code[] = '	$base_url = $protocol . "://" . $_SERVER[\'HTTP_HOST\'];';
											$code[] = '	$complete_url =   $base_url . $_SERVER["REQUEST_URI"];';
											$code[] = '';
											$code[] = '	$affliate_cookie = (isset($_GET["af_id"]) ? $_GET["af_id"] : (isset($_COOKIE["af_id"]) ? $_COOKIE["af_id"] : "") ); ';
											$code[] = '';
											$code[] = '	$affiliateData = array(';
											$code[] = '		"order_id"       => $orders->fields[\'orders_id\'],';
											$code[] = '		"order_currency" => $order->info[\'currency\'],';
											$code[] = '		"order_total"    => $order->info[\'total\'],';
											$code[] = '		"product_ids"    => array(),';
											$code[] = '		"af_id"          => $affliate_cookie,';
											$code[] = '		"ip"             => $ipaddress,';
											$code[] = '		"base_url"       => base64_encode($base_url),';
											$code[] = '		"script_name"    => "zencart",';
											$code[] = '	);';
											$code[] = '';
											$code[] = '	foreach ($order->products as $item) { $affiliateData["product_ids"][] = $item["id"]; }';
											$code[] = '	';
											$code[] = '    $context_options = stream_context_create(array(';
											$code[] = '        "http" => array(';
											$code[] = '          "method" => "GET",';
											$code[] = '          "header" => "User-Agent: ". (isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : ""),';
											$code[] = '        )';
											$code[] = '    ));';
											$code[] = '	';
											$code[] = '    file_get_contents("'. base_url('integration/addOrder') .'?".http_build_query($affiliateData), false, $context_options);';
											$code[] = '/* end of AFFILIATE PRO integration */';
											$code[] = '?>';
											echo funConvertToCode($code,'php');
											?>
											
										</li>
										<li><?= __('admin.you_have_completed_affiliate_pro_module_install') ?></li>
									</ol>
								<?php } ?>

								<?php if($module_key == 'oscommerce'){ ?>
									<p><?= __('admin.integrate_affiliate_script_into_oscommerce') ?></p>

									<ol class="installed-step">
										<li>
											<?= __('admin.open_file' ) ?> <code class="code_">/includes/template_top.php</code> <?= __('admin.add_following_code_at_the_end_file') ?>
											
											<?php
											$code = array();
											$code[] = '<script type="text/javascript" src="'. base_url('integration/oscommerce') .'"></script>';
											echo funConvertToCode($code,'html');
											?>
											
										</li>
										<li>
											<?= __('admin.open_file' ) ?> <code class="code_">product_info.php</code> <?= __('admin.add_following_code_after') ?> <code class="code_">$product_info = tep_db_fetch_array($product_info_query);</code> <?= __('admin.this_line') ?> (<?= __('admin.around') ?> 42 <?= __('admin.line') ?>)
											
											<?php
											$code = array();
											$code[] = '/* AFFILIATE PRO integration */';
											$code[] = '	$ipaddress = "";';
											$code[] = '	if (getenv("HTTP_CLIENT_IP")) $ipaddress           = getenv("HTTP_CLIENT_IP");';
											$code[] = '	else if(getenv("HTTP_X_FORWARDED_FOR")) $ipaddress = getenv("HTTP_X_FORWARDED_FOR");';
											$code[] = '	else if(getenv("HTTP_X_FORWARDED")) $ipaddress     = getenv("HTTP_X_FORWARDED");';
											$code[] = '	else if(getenv("HTTP_FORWARDED_FOR")) $ipaddress   = getenv("HTTP_FORWARDED_FOR");';
											$code[] = '	else if(getenv("HTTP_FORWARDED")) $ipaddress       = getenv("HTTP_FORWARDED");';
											$code[] = '	else if(getenv("REMOTE_ADDR")) $ipaddress          = getenv("REMOTE_ADDR");';
											$code[] = '	else $ipaddress                                    = "UNKNOWN";';
											$code[] = '	';
											$code[] = '	$affliate_cookie = (isset($_GET["af_id"]) ? $_GET["af_id"] : (isset($_COOKIE["af_id"]) ? $_COOKIE["af_id"] : "") ); ';
											$code[] = '	$current_url = tep_href_link(FILENAME_PRODUCT_INFO, "products_id=" . $product_info["products_id"]);';
											$code[] = '	';
											$code[] = '	$affiliateData = array(';
											$code[] = '		"product_id"       => $product_info["product_id"],';
											$code[] = '		"af_id"            => $affliate_cookie,';
											$code[] = '		"ip"               => $ipaddress,';
											$code[] = '		"base_url"         => base64_encode(tep_href_link(FILENAME_DEFAULT)),';
											$code[] = '		"script_name"      => "oscommerce",';
											$code[] = '		"current_page_url" => base64_encode($current_url),';
											$code[] = '	);';
											$code[] = '	';
											$code[] = '	$context_options = stream_context_create(array(';
											$code[] = '		"http"=>array(';
											$code[] = '			"method"=>"GET",';
											$code[] = '			"header"=> "User-Agent: ". (isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : ""),';
											$code[] = '		)';
											$code[] = '	)); ';
											$code[] = '	';
											$code[] = '	file_get_contents("'. base_url('integration/addClick') .'?".http_build_query($affiliateData), false, $context_options);';
											$code[] = '/* end of AFFILIATE PRO integration */';
											echo funConvertToCode($code,'php');
											?>
											
										</li>
										<li>
											<?= __('admin.open_file' ) ?> <code class="code_">checkout_success.php</code> <?= __('admin.add_following_code_after') ?> <code class="code_">$orders = tep_db_fetch_array($orders_query);</code> <?= __('admin.this_line') ?> (<?= __('admin.around') ?> 27 <?= __('admin.line') ?>)
											
											<?php
											$code = array();
											$code[] = '/* AFFILIATE PRO integration */';
											$code[] = '    require(DIR_WS_CLASSES . "order.php");';
											$code[] = '    $_order = new order($orders["orders_id"]);';
											$code[] = '	';
											$code[] = '    $ipaddress = "";';
											$code[] = '    if (getenv("HTTP_CLIENT_IP")) $ipaddress = getenv("HTTP_CLIENT_IP");';
											$code[] = '    else if(getenv("HTTP_X_FORWARDED_FOR")) $ipaddress = getenv("HTTP_X_FORWARDED_FOR");';
											$code[] = '    else if(getenv("HTTP_X_FORWARDED")) $ipaddress = getenv("HTTP_X_FORWARDED");';
											$code[] = '    else if(getenv("HTTP_FORWARDED_FOR")) $ipaddress = getenv("HTTP_FORWARDED_FOR");';
											$code[] = '    else if(getenv("HTTP_FORWARDED")) $ipaddress = getenv("HTTP_FORWARDED");';
											$code[] = '    else if(getenv("REMOTE_ADDR")) $ipaddress = getenv("REMOTE_ADDR");';
											$code[] = '    else $ipaddress = "UNKNOWN";';
											$code[] = '	';
											$code[] = '    $affliate_cookie = (isset($_GET["af_id"]) ? $_GET["af_id"] : (isset($_COOKIE["af_id"]) ? $_COOKIE["af_id"] : "") ); ';
											$code[] = '    $affiliateData = array(';
											$code[] = '      "order_id"       => $orders["orders_id"],';
											$code[] = '      "order_currency" => $_order->info["currency"],';
											$code[] = '      "order_total"    => preg_replace(\'/[^\d\.]/\', "", $_order->info["total"]),';
											$code[] = '      "product_ids"    => array(),';
											$code[] = '      "af_id"          => $affliate_cookie,';
											$code[] = '      "ip"             => $ipaddress,';
											$code[] = '      "base_url"       => base64_encode(tep_href_link(FILENAME_DEFAULT)),';
											$code[] = '      "script_name"    => "oscommerce",';
											$code[] = '    );';
											$code[] = '	';
											$code[] = '    foreach ($_order->products as $item) { $affiliateData["product_ids"][] = $item["id"]; }';
											$code[] = '	';
											$code[] = '    $context_options = stream_context_create(array(';
											$code[] = '        "http" => array(';
											$code[] = '          "method" => "GET",';
											$code[] = '          "header" => "User-Agent: ". (isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : ""),';
											$code[] = '        )';
											$code[] = '    ));';
											$code[] = '	';
											$code[] = ' file_get_contents("'. base_url('integration/addOrder') .'?".http_build_query($affiliateData), false, $context_options);';
											$code[] = '/* end of AFFILIATE PRO integration */';
											echo funConvertToCode($code,'php');
											?>
											
										</li>
										<li><?= __('admin.you_have_completed_affiliate_pro_module_install') ?></li>
									</ol>
								<?php } ?>

								<?php if($module_key == 'paypal'){ ?>
									<p><?= __('admin.paypal_express_checkout_integrates_using_ipn_callback') ?></p>
									
									<ol class="installed-step">
										<li><?= __('admin.paypal_button') ?>
											<ol>
												<li><?= __('admin.now_add_the_following_code_into_every_paypal_button_form') ?></li>
												<li>
													<?php
													$code = array();
													$code[] = '<input type="hidden" name="custom" value="custom=your_custom_value_here&af_id=<?= $_COOKIE[\'af_id\'] ?>" />';
													echo funConvertToCode($code,'html');
													?>
													
												</li>
											</ol>
										</li>
										<li><?= __('admin.integration') ?> 
											<ol>
												<li><?= __('admin.ipn_callback_paypal_processing_script_place_code') ?></li>
												<li>
													<?php
													$code = array();

													$code[] = '/* AFFILIATE PRO integration */';
													$code[] = '	parse_str($_POST["custom"],$_CUSTOM);';
													$code[] = '	$_POST["custom"] = $_CUSTOM["custom"];';
													$code[] = '	$ch = curl_init();';
													$code[] = '	curl_setopt($ch, CURLOPT_URL, "'. base_url('integration/addOrderPaypal') .'");';
													$code[] = '	curl_setopt($ch, CURLOPT_POST, 1);';
													$code[] = '	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);';
													$code[] = '	curl_setopt($ch, CURLOPT_POSTFIELDS, array(';
													$code[] = '		"post"           => json_encode($_POST),';
													$code[] = '		"af_id"          => $_CUSTOM["af_id"],';
													$code[] = '		"order_id"       => "YOUR_ORDER_ID",';
													$code[] = '		"product_ids"    => "PRODUCTS_ID",';
													$code[] = '		"base_url"       => base64_encode("YOUR_WEBSITE_URL"),';
													$code[] = '	));';
													$code[] = '	curl_exec($ch);';
													$code[] = '/* end of AFFILIATE PRO integration */';

													echo funConvertToCode($code,'php');
													?>
													<p>
														<h6><?= __('admin.all_possible_tracking_parameter') ?></h6>
														<div class="well">
															<strong>YOUR_WEBSITE_URL</strong> : <?= __('admin.website_root_url') ?><br>
															<strong>YOUR_ORDER_ID</strong>    : <?= __('admin.unique_orer_id') ?><br>
															<strong>PRODUCTS_ID</strong>      : <?= __('admin.pro_ids_of_order_com_separated') ?><br>
														</div>
													</p>
												</li>
											</ol>
										</li>
										<li><?= __('admin.you_have_completed_affiliate_pro_module_install') ?> </li>
									</ol>
								<?php } ?>

								<?php if($module_key == 'magento'){ ?>
									<div role="tabpanel">
										<ul class="nav nav-pills" role="tablist">
											<li role="presentation" class="nav-item">
												<a href="#magento-1" class="nav-link active" aria-controls="magento-1" role="tab" data-bs-toggle="tab">Magento 1</a>
											</li>
											<li role="presentation" class="nav-item">
												<a href="#magento2" class="nav-link" aria-controls="magento2" role="tab" data-bs-toggle="tab">Magento 2</a>
											</li>
										</ul>
										
										<br>
										<div class="tab-content">
											<div role="tabpanel" class="tab-pane active" id="magento-1">
												<p><?= __('admin.integrate_affiliate_magento_1_download_magento_module') ?><a href="<?= base_url('integration/download_plugin/magento/1') ?>"><?= __('admin.magento_module') ?></a> <?= __('admin.and_follow_following_steps').__('admin.or_check') ?> <a target='_blank' href="https://docs.mageplaza.com/kb/installation.html"><?= __('admin.official_document') ?></a></p>
												
												<ol class="installed-step">
													<li><?= __('admin.extract_download_zip_file') ?></li>
													
													<li><?= __('admin.upload_app_folder_to_root_folder_of_your_magento_store') ?></li>
													
													<li><?= __('admin.check_current_backup_your_site') ?><code class="code_"><?= __('admin.system_tools_backup') ?></code>. <?= __('admin.this_will_be_usefull_in_case') ?></li>
													
													<li><?= __('admin.disable_compilations_via') ?><code class="code_"><?= __('admin.system_tools_compilations') ?></code></li>
													
													<li><?= __('admin.clear_cache_by_going_into') ?><code class="code_"><?= __('admin.system_cache_management') ?></code>, <?= __('admin.selecting_all_file_in_list_refresh_option_dropdown_menu') ?></li>
													
													<li><?= __('admin.activate_extension') ?><code class="code_"><?= __('admin.system_configuration') ?></code> 
														<p><?= __('admin.click_on') ?> <b><?= __('admin.advanced') ?></b> <?= __('admin.menu_from_left_panel') ?></p>
														<p><?= __('admin.advanced') ?> <b>AffiliatePro_Magento1</b> <?= __('admin.and_enable_it') ?></p>
													</li>
												</ol>
											</div>
											<div role="tabpanel" class="tab-pane" id="magento2">
												<p><?= __('admin.integrate_affiliate_magento_download_magento_module') ?><a href="<?= base_url('integration/download_plugin/magento') ?>"><?= __('admin.magento_module') ?></a><?= __('admin.and_follow_following_steps'). __('admin.or_check') ?><a target='_blank' href="https://docs.mageplaza.com/kb/installation.html"><?= __('admin.official_document') ?></a></p>
												
												<ol class="installed-step">
													<li><?= __('admin.extract_download_zip_file') ?></li>

													<li><?= __('admin.upload_affiliatepro_folder_to') ?> <code class="code_">/app/code/</code> <?= __('admin.folder_in_your_magento_store') ?></li>
													<li>
														<b> <?= __('admin.run_command_using_php') ?></b>
														<ul>
															<li><?= __('admin.create') ?> <code class="code_">cmd.php</code> <?= __('admin.file_into_magento_root_folder') ?> </li>

															<li><?= __('admin.add_following_content_to_cmd_file') ?>
																<?php
																$code = array();
																$code[] = '<?php';
																$code[] = '	exec("php bin/magento setup:upgrade",$o);';
																$code[] = '	exec("php bin/magento setup:static-content:deploy",$o);';
																$code[] = '	echo "Module installed successfully";';
																
																echo funConvertToCode($code,'php');
																?>
															</li>
															<li><?= __('admin.open_cmd_file_into_browser_using_following_url') ?>
																<code class="code_">http://url_of_magento_store/cmd.php</code>
															</li>
														</ul>
													</li>
												</ol>
											</div>
										</div>
									</div>
								<?php } ?>

								<?php if($module_key == 'opencart'){ ?>
									<p><?= __('admin.integrate_affiliate_script_into_opencart_download_opencart_extension') ?></p>

									<br>
									<table class="ml-4">
										<tr>
											<td><?= __('admin.for_opencart_version_1564_to_2200') ?> </td>
											<td><a href="<?= base_url('integration/download_plugin/opencart/1') ?>"><?= __('admin.download') ?></a></td>
										</tr>
										<tr>
											<td><?= __('admin.for_opencart_version_2300_to_3011') ?> </td>
											<td><a href="<?= base_url('integration/download_plugin/opencart/2') ?>"><?= __('admin.download') ?></a></td>
										</tr>
									</table>

									<br>
									
									<ol class="installed-step">
										<li><?= __('admin.lets_start_logging_store_admin_panel') ?> <code class="code_"><?= __('admin.extensions_extension_installer') ?></code></li>

										<li><?= __('admin.click_upload_button_dialog_box_open') ?></li>

										<li><?= __('admin.locate_installation_zip_file_extension') ?></li>

										<li><?= __('admin.after_clicking_ok_extension_uploaded') ?></li>

										<li><?= __('admin.your_module_should_visible') ?> <code class="code_"><?= __('admin.extensions_modules') ?></code>. <?= __('admin.locating_in_module_list_click_install_button') ?></li>

										<li><?= __('admin.final_step_installation_process_apply_changes') ?> <code class="code_"><?= __('admin.extensions_modifications') ?></code> <?= __('admin.click_refresh_sign_right_corner') ?></li>
									</ol>
								<?php } ?>

								
								<?php if(in_array($module_key, array('general_integration','laravel','codeigniter','cakephp'))){ ?>
									<h2><?= __('admin.cmn_tracking_script') ?></h2>
									<div>
										<p><?= __('admin.add_script_in_footer_instr') ?></p>
										<?php
										$code = array();
										$code[] = '<script type="text/javascript" src="'. $base_url .'integration/general_integration"></script>';
										echo funConvertToCode($code,'html');
										?>
									</div>
									<br><hr>
									
									<h2><?= __('admin.general_click_tracking') ?></h2>
									<div>
										<p><?= __('admin.use_follow_code_to_track_general_cicks') ?></p>

										<?php
										$code = array();
										$code[] = '<script type="text/javascript" src="'. $base_url .'integration/general_integration"></script>';
										$code[] = '<script type="text/javascript">';
										$code[] = '	AffTracker.setWebsiteUrl( "WebsiteUrl" );';
										$code[] = '	AffTracker.generalClick( "general_code" );';
										$code[] = '</script>';
										
										echo funConvertToCode($code,'html');
										?>

										<p>
											<h6><?= __('admin.all_possible_tracking_para') ?></h6>
											<div class="well">
												<strong>WebsiteUrl</strong>       : <?= __('admin.website_root_url') ?> <br>
												<strong>general_code</strong> : <?= __('admin.unique_code_of_general_click_val_msg') ?>
											</div>
										</p>

										<h6><?= __('admin.available_general_click_code_is_here') ?></h6>
										<ul>
											<?php foreach ($general_codes as $key => $value) { ?>
												<li> <?= $value['general_code'] ?> </li>
											<?php } ?>
										</ul>

									</div>
									<br><hr>
									
									<h2><?= __('admin.cpa_cost_per_action') ?></h2>
									<div>
										<p><?= __('admin.action_like_registration_section_per_action_comissions') ?></p>

										<p><?= __('admin.under_integrations_integration_tools_create_new_ads') ?></p>

										<p><?= __('admin.last_step_insert_javascript_tracking_code') ?></p>

										<p><?= __('admin.example_in_case_registration_action_user_register') ?></p>

										<?php
										$code = array();
										$code[] = '<script type="text/javascript" src="'. $base_url .'integration/general_integration"></script>';
										$code[] = '<script type="text/javascript">';
										$code[] = '	AffTracker.setWebsiteUrl( "WebsiteUrl" );';
										$code[] = '	AffTracker.createAction( "actionCode" )';
										$code[] = '</script>';
										
										echo funConvertToCode($code,'html');
										?>

										<p>
											<h6><?= __('admin.all_possible_tracking_para') ?></h6>
											<div class="well">
												<strong>WebsiteUrl</strong>       : <?= __('admin.website_root_url') ?> <br>
												<strong>actionCode</strong>       : <?= __('admin.action_code_create_new_program_tool_like') ?><br>
											</div>
										</p>

										<h6><?= __('admin.available_action_code_is_here') ?></h6>
										<ul>
											<?php foreach ($action_codes as $key => $value) { ?>
												<li> <?= $value['action_code'] ?> </li>
											<?php } ?>
										</ul>
									</div>


									<br><hr>
									
									<h2><?= __('admin.order_tracking') ?></h2>
									<div>
										<p><?= __('admin.order_tracking_intruction_1') ?></p>

										<?php
										$code = array();
										$code[] = '<script type="text/javascript" src="'. $base_url .'integration/general_integration"></script>';
										$code[] = '<script type="text/javascript">';
										$code[] = '	AffTracker.setWebsiteUrl( "WebsiteUrl" );';
										$code[] = '	';
										$code[] = '	AffTracker.setData( "custom_data_1", "value" );';
										$code[] = '	AffTracker.setData( "custom_data_2", "value" );';
										$code[] = '	AffTracker.setData( "custom_data_...", "value" );';
										$code[] = '	';
										$code[] = '	AffTracker.add_order({';
										$code[] = '	    order_id 		: "OrderId",';
										$code[] = '	    order_currency 	: "OrderCurrency",';
										$code[] = '	    order_total		: "OrderTotal",';
										$code[] = '	    product_ids 	: "ProductIDs"';
										$code[] = '	})';
										$code[] = '</script>';
										
										echo funConvertToCode($code,'html');
										?>

										<p>
											<h6><?= __('admin.all_possible_tracking_parameter') ?></h6>
											<div class="well">
												<strong>WebsiteUrl</strong>       : <?= __('admin.website_root_url') ?> <br>
												<strong>OrderId</strong>       : <?= __('admin.unique_orer_id') ?> <br>
												<strong>OrderCurrency</strong> : <?= __('admin.currency_symbol_of_order') ?> <br>
												<strong>OrderTotal</strong>    : <?= __('admin.total_amt_of_order') ?> <br>
												<strong>ProductIDs</strong>    : <?= __('admin.product_ids_of_order') ?> <br>
											</div>

											<div class="alert alert-info">
												<strong><?= __('admin.script_tag') ?></strong> <?= __('admin.script_tag_optional_header_or_footer_include_thank_you_page') ?>
											</div>
										</p>

										<h6><?= __('admin.php_example') ?></h6>

										<?php
										$code = array();
										$code[] = '<script type="text/javascript" src="'. $base_url .'integration/general_integration"></script>';
										$code[] = '<script type="text/javascript">';
										$code[] = '	AffTracker.setWebsiteUrl( "WebsiteUrl" );';
										$code[] = '	';
										$code[] = '	AffTracker.setData( "custom_data_1", "value" );';
										$code[] = '	AffTracker.setData( "custom_data_2", "value" );';
										$code[] = '	AffTracker.setData( "custom_data_...", "value" );';
										$code[] = '	';
										$code[] = '	AffTracker.add_order({';
										$code[] = '	    order_id 		: "<?php echo $variable_OrderId ?>",';
										$code[] = '	    order_currency 	: "<?php echo $variable_OrderCurrency ?>",';
										$code[] = '	    order_total		: "<?php echo $variable_OrderTotal ?>",';
										$code[] = '	    product_ids 	: "<?php echo $variable_ProductIDs ?>"';
										$code[] = '	})';
										$code[] = '</script>';
										
										echo funConvertToCode($code,'html');
										?>
									</br>
								</div>


								<br><hr>

								<h2><?= __('admin.stop_recurring_payment_of_order') ?></h2>
									<div>
										<p> <?= __('admin.to_stop_recurring_pay_orders') ?> "stop-membership.php" </p>

										<?php
										$code = array();
										$code[] = '<script type="text/javascript" src="'. $base_url .'integration/general_integration"></script>';
										$code[] = '<script type="text/javascript">';
										$code[] = '	AffTracker.setWebsiteUrl( "WebsiteUrl" );';
										$code[] = '	AffTracker.stop_recurring("$variable_OrderId ")';
										$code[] = '</script>';
										
										echo funConvertToCode($code,'html');
										?>

										<p>
											<h6><?= __('admin.all_possible_tracking_parameter') ?></h6>
											<div class="well">
												<strong>WebsiteUrl</strong> : <?= __('admin.website_root_url') ?> <br>
												<strong>variable_OrderId </strong>    : <?= __('admin.unique_orer_id') ?> <br>
											</div>

											<div class="alert alert-info">
												<strong>OrderId</strong> variable_OrderId <?= __('admin.must_match_order_tracking_param') ?> variable_OrderId 
											</div>
										</p>
									</br>
								</div>



								<br><hr>
								<h2><?= __('admin.pro_click_tracking') ?></h2>
								<div>
									<p><?= __('admin.pro_click_tracking_info') ?></p>

									<?php
									$code = array();
									$code[] = '<script type="text/javascript" src="'. $base_url .'integration/general_integration"></script>';
									$code[] = '<script type="text/javascript">';
									$code[] = '	AffTracker.setWebsiteUrl( "website_url" );';
									$code[] = '	AffTracker.productClick( "ProductID" );';
									$code[] = '</script>';
									
									echo funConvertToCode($code,'html');
									?>

									<p>
										<h6><?= __('admin.all_possible_tracking_parameter') ?></h6>
										<div class="well">
											<strong>WebsiteUrl</strong>       : <?= __('admin.website_root_url') ?> <br>
											<strong>ProductID</strong> : <?= __('admin.unique_product_id') ?>
										</div>
									</p>

								</div>
							</br>
						<?php } ?>

						<?php if($module_key == 'shopify'){ ?>
							<p><?= __('admin.integrate_affiliate_script_into_shopify') ?></p>

							<ol class="installed-step">
								<li><?= __('admin.login_goto_shopify_admin_dashboard') ?> </code></li>
								<li><?= __('admin.go_to') ?>  <code class="code_"><?= __('admin.online_store_themes_current_theme_action_edit_code') ?></code>
									<ul class="list-unstyled">
										<li><?= __('admin.on_left_side_in') ?> <b><?= __('admin.sections') ?></b> <?= __('admin.section_click_on') ?> <b>header.liquid</b> <?= __('admin.file') ?>. <?= __('admin.and_add_following_code_beginning_of_the_file') ?>

											<?php
											$code = array();
											$code[] = '<script type="text/javascript" src="'. $base_url .'integration/shopify"></script>';
											echo funConvertToCode($code,'html');
											?>
										</li>

										<li>If you're using <strong>Online Store 2.0</strong>, go to <strong>Sections</strong> and locate the <strong>main-product.liquid</strong> file</br>
											If you're using an <strong>older theme</strong>, go to <strong>Templates</strong> and locate the <strong>product.liquid</strong> file

											<?php
											$code = array();
											$code[] = '<script type="text/javascript" src="'. $base_url .'integration/shopify"></script>';
											$code[] = '<script type="text/javascript">';
											$code[] = '	AffTracker.setWebsiteUrl( "{{ shop.url }}" );';
											$code[] = '	AffTracker.productClick( "{{ product.id }}" );';
											$code[] = '</script>';
											
											echo funConvertToCode($code,'html');
											?>
										</li>
									</ul>
								</li>

								<li><?= __('admin.goto_setting_click_checkout_in_order_processing_section_find_additional_scripts') ?>

									<?php
									$code = array();
									$code[] = '<script type="text/javascript" src="'. $base_url .'integration/shopify"></script>';
									$code[] = '<script type="text/javascript">';
									$code[] = '	AffTracker.setWebsiteUrl( "{{ shop.url }}" );';
									$code[] = '	';
									$code[] = '	AffTracker.setData( "custom_data_1", "value" );';
									$code[] = '	AffTracker.setData( "custom_data_2", "value" );';
									$code[] = '	AffTracker.setData( "custom_data_...", "value" );';
									$code[] = '	';
									$code[] = '	AffTracker.add_order({';
									$code[] = '	    order_id 		: "{{ order_number }}",';
									$code[] = '	    order_currency 	: "{{ shop.currency }}",';
									$code[] = '	    order_total		: "{{ total_price | money_without_currency }}",';
									$code[] = '	    product_ids 	: "{% for line_item in line_items %}{{ line_item.product_id }},{% endfor %}"';
									$code[] = '	})';
									$code[] = '</script>';
									echo funConvertToCode($code,'html');
									?>
								</li>
							</ol>
						<?php } ?>

					</div>
				</div>
			</div>
		</div>
	</div>


	<div class="modal fade" id="myModal" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><?= __('admin.wpforms_integration') ?></h4>
					<button type="button" class="btn btn-default" data-bs-dismiss="modal"><?= __('admin.close') ?></button>
				</div>
				<div class="modal-body">
					<ul class="list-group">
						<li class="list-group-item"><?= __('admin.setting_thank_you_page_wordpress_site_wpforms_plugin') ?></br>
							<img class="zoom" src="<?php echo base_url(); ?>assets/guide_images/wpform1.png" alt="" style="width:100%;height:100%; margin-right:0; margin-left:0;">
						</li>
						<li class="list-group-item"><?= __('admin.adding_integration_code_to_thank_you_page') ?></br>
							<img class="zoom" src="<?php echo base_url(); ?>assets/guide_images/thank_you_page_code.png" alt="" style="width:100%;height:100%; margin-right:0; margin-left:0;">
						</li>
					</ul>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-bs-dismiss="modal"><?= __('admin.close') ?></button>
				</div>
			</div>
			
		</div>
	</div>
	
	<div class="modal fade" id="myModal_bridge" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"><?= __('admin.wordpress_woocommerce_bridge_plugin') ?></h5>
					<button type="button" class="btn btn-default" data-bs-dismiss="modal"><?= __('admin.close') ?></button>
				</div>
				<div class="modal-body">
					<ul class="list-group">
						<li class="list-group-item"><?= __('admin.you_can_set_register_only_wp_reg_instr') ?></br>
							<img class="zoom" src="<?php echo base_url(); ?>assets/guide_images/wp_bride_plugin.png" alt="" style="width:100%;height:100%; margin-right:0; margin-left:0;">
						</li>
					</ul>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-bs-dismiss="modal"><?= __('admin.close') ?></button>
				</div>
			</div>
			
		</div>
	</div>