<link rel="stylesheet" type="text/css" href="<?= base_url('assets/integration/prism/css.css') ?>?v=<?= av() ?>">

<script type="text/javascript" src="<?= base_url('assets/integration/prism/js.js') ?>"></script>

<?php 
	function ___h($text,$lan){
		$text = implode("\n", $text);
		$text = htmlentities($text);
		$text = '<pre class="language-'.$lan.'"><code class="language-'.$lan.'">'.$text.'</code></pre>';

		return $text;
	}

	$base_url  = base_url();
?>

<div id="doc-html">
	<div class="row">
		<div class="col-sm-12">
		    <div class="card">
		    	<div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
		    		<h5><?= __('admin.how_to_create_payment_method') ?></h5>
		    		<div>
		    			<a id="toggle-uploader" href="<?= base_url('admincontrol/payment_gateway_sample_data_to_pdf') ?>" class="btn btn-light" target="_blank">
		    				<?= __('admin.payment_gateway_doc_sample_data') ?>
		    			</a>
		    			<a id="toggle-uploader" href="<?= base_url('admincontrol/payment_gateway_documentation_to_pdf') ?>" class="btn btn-light" target="_blank">
		    				<?= __('admin.download_as_pdf') ?>
		    			</a>
		    		</div>
		    	</div>

		    	<div class="card-body payment-doc">
		    		<p><?= __('admin.payment_gateway_doc_info_p1') ?></p>
		    		<p><?= __('admin.payment_gateway_doc_info_p2') ?></p>
		    		<p><?= __('admin.payment_gateway_doc_info_p3') ?></p>
		    		<ul>
		    			<li>1. controller -> custom.php</li>
		    			<li>2. setting -> custom.php</li>
		    			<li>3. view -> custom.php</li>
		    		</ul>
		    		<p><?= __('admin.payment_gateway_doc_info_p4') ?></p>
		    		<ul>
		    			<li>4. library -> custom</li>
		    			<li>5. logo -> custom.png</li>
		    		</ul>

		    		<div id="wpg-doc">
		    			<div class="card">
		    				<div class="card-header bg-secondary text-white">
		    					<h5>#1 <?= __('admin.payment_gateway_doc_controller_folder') ?></h5>
		    				</div>
	    					<div class="card-body">
	    						<p>
	    							<?= __('admin.payment_gateway_doc_controller_info').' '.__('admin.payment_gateway_doc_controller_info_following_url').'.' ?>
	    						</p>
	    						<p><?= __('admin.payment_gateway_doc_sample_data') ?></p>
	    						<ul>
	    							<?php foreach($sample_data as $key => $value): ?>
	    								<li>
		    								<a href="<?= base_url('admincontrol/payment_gateway_documentation_sample_data/'.$value) ?>" target="_blank"><?= $value; ?></a>
		    							</li>
	    							<?php endforeach ?>
	    						</ul>
	    						<p><?= __('admin.payment_gateway_doc_folder_structure') ?></p>
	    						<ul>
	    							<li><strong>custom/controller/custom.php</strong></li>
	    						</ul>
	    						<h6><?= __('admin.payment_gateway_doc_example') ?></h6>
	    						<?php
									$code = array();
									$code[] = '<?php';
									$code[] = '	class custom {';
									$code[] = '		public $title = \'Custom Payment Gateway\';';
									$code[] = '		public $icon = \'assets/payment_gateway/custom.png\';';
									$code[] = '		public $website = \'\';';
									$code[] = '		';
									$code[] = '		function __construct($api){ $this->api = $api; }';
									$code[] = '		';
									$code[] = '		public function getPaymentGatewayView($settingData,$gatewayData){';
									$code[] = '			$view = APPPATH."payment_gateway/views/custom.php";';
									$code[] = '		';
									$code[] = '			require $view;';
									$code[] = '		}';
									$code[] = '		';
									$code[] = '		public function setPaymentGatewayRequest($settingData,$gatewayData){}';
									$code[] = '		';
									$code[] = '		public function customCallbackFunction($settingData,$gatewayData){}';
									$code[] = '	}';
									echo ___h($code,'php');
								?>
								<h6><?= __('admin.payment_gateway_doc_file_explanation') ?>:</h6>
								<div>
									<div>
										<b>Class Name</b> Class name <?= __('admin.payment_gateway_doc_controller_explanation_1') ?>
									</div>
									<div>
										<b>Public Property Title</b> <?= __('admin.payment_gateway_doc_controller_explanation_2') ?>
									</div>
									<div>
										<b>Constructor</b> <?= __('admin.payment_gateway_doc_controller_explanation_3') ?>
									</div>
									<div>
										<b>public function getPaymentGatewayView</b> 
										<?= __('admin.payment_gateway_doc_controller_explanation_4') ?>
									</div>
									<div>
										<b>public function setPaymentGatewayRequest</b> 
										<?= __('admin.payment_gateway_doc_controller_explanation_5') ?>
									</div>
									<div>
										<b>public function customCallbackFunction</b> 
										<?= __('admin.payment_gateway_doc_controller_explanation_6') ?>
									</div>
								</div>
	    					</div>
		    			</div>

		    			<div class="card">
		    				<div class="card-header bg-secondary text-white">
		    					<h5>#2 <?= __('admin.payment_gateway_doc_setting_folder') ?></h5>
		    				</div>
	    					<div class="card-body">
	    						<?= __('admin.payment_gateway_doc_setting_info') ?>
	    						<ul>
	    							<li><strong>custom/setting/custom.php</strong></li>
	    						</ul>
	    						<h6><?= __('admin.payment_gateway_doc_example') ?></h6>
	    						<?php
									$code = array();
									$code[] = '<div class="form-group">';
									$code[] = '	<label class="form-control-label">Some Setting</label>';
									$code[] = '	<input class="form-control" name="name" value="<?= $setting_data["name"] ?>" >';
									$code[] = '</div>';
									$code[] = '';
									$code[] = '<div class="form-group">';
									$code[] = '	<label class="control-label" for="input-completed-status">Completed Status</label>';
									$code[] = '	<select name="completed_status_id" id="input-completed-status" class="form-control">';
									$code[] = '	  <?php foreach ($order_status as $order_status_id => $name){';
									$code[] = '	  		if(isset($setting_data["completed_status_id"]))';
									$code[] = '	    		$selected = ($order_status_id == $setting_data["completed_status_id"]) ? "selected" : "";';
									$code[] = '	  		else';
									$code[] = '     			$selected = ($order_status_id == 1) ? "selected" : ""; ?>';
									$code[] = '     ';
									$code[] = '     		<option <?= $selected ?>  value="<?= $order_status_id; ?>"><?= $name ?></option>';
									$code[] = '	  <?php } ?>';
									$code[] = '	</select>';
									$code[] = '</div>';
									echo ___h($code,'php');
								?>
								<h6><?= __('admin.payment_gateway_doc_file_explanation') ?>:</h6>
								<p><?= __('admin.payment_gateway_doc_setting_explanation') ?></p>
	    					</div>
		    			</div>

		    			<div class="card">
		    				<div class="card-header bg-secondary text-white">
		    					<h5>#3 <?= __('admin.payment_gateway_doc_view_folder') ?></h5>
		    				</div>
	    					<div class="card-body">
	    						<?= __('admin.payment_gateway_doc_view_info') ?>
	    						<ul>
	    							<li><strong>custom/view/custom.php</strong></li>
	    						</ul>
	    						<h6><?= __('admin.payment_gateway_doc_example') ?></h6>
	    						<?php
									$code = array();
									$code[] = '<div class="payment-button-group">';
									$code[] = '	<button type="button" class="btn btn-default" onclick="backCheckout()">Back</button>';
									$code[] = '	<button id="button-confirm" class="btn btn-primary">Confirm</button>';
									$code[] = '</div>';
									$code[] = '<script type="text/javascript">';
									$code[] = '	$("#button-confirm").click(function(){';
									$code[] = '	 $this = $(this);';
									$code[] = '	 $this.prop("disabled",true);';
									$code[] = '		';
									$code[] = '	 $.ajax({';
									$code[] = '		url:"<?= $gatewayData["payment_confirmation"] ?>",';
									$code[] = '		type:"POST",';
									$code[] = '		dataType:"json",';
									$code[] = '		data:$("[name^="comment"]").serialize(),';
									$code[] = '		beforeSend:function(){$("#button-confirm").btn("loading");},';
									$code[] = '		complete:function(){$("#button-confirm").btn("reset");},';
									$code[] = '		success:function(json){';
									$code[] = '			$container = $("#checkout-confirm");';
									$code[] = '			$container.find(".has-error").removeClass("has-error");';
									$code[] = '			$container.find("span.text-danger").remove()';
									$code[] = '		';
									$code[] = '			if(json["errors"]){';
									$code[] = '				$.each(json["errors"]["comment"], function(ii,jj){';
									$code[] = '					$ele = $container.find("#comment_textarea"+ ii);';
									$code[] = '					if($ele){';
									$code[] = '						$ele.parents(".form-group").addClass("has-error");';
									$code[] = '						$ele.after("<span class="text-danger">"+ jj +"</span>");';
									$code[] = '					}';
									$code[] = '				});';
									$code[] = '			}';
									$code[] = '			if(json["success"]){';
									$code[] = '				$.ajax({';
									$code[] = '					url:"<?= $gatewayData["confirm_payment"] ?>",';
									$code[] = '					type:"POST",';
									$code[] = '					dataType:"json",';
									$code[] = '					data:{';
									$code[] = '						payment_gateway: $("input[name="payment_gateway"]:checked").val()';
									$code[] = '					},';
									$code[] = '					beforeSend:function(){$this.btn("loading");},';
									$code[] = '					complete:function(){$this.btn("reset");},';
									$code[] = '					success:function(json){';
									$code[] = '						if(json["redirect"])';
									$code[] = '							window.location = json["redirect"];';
									$code[] = '		';
									$code[] = '						if(json["warning"])';
									$code[] = '							alert(json["warning"])';
									$code[] = '					},';
									$code[] = '				});';
									$code[] = '			}';
									$code[] = '	 	},';
									$code[] = '	 });';
									$code[] = '	})';
									$code[] = '</script>';
									echo ___h($code,'php');
								?>
								<h6><?= __('admin.payment_gateway_doc_file_explanation') ?>:</h6>
								<p><?= __('admin.payment_gateway_doc_view_explanation') ?></p>
	    					</div>
		    			</div>

		    			<div class="card">
		    				<div class="card-header bg-secondary text-white">
		    					<h5>#4 <?= __('admin.payment_gateway_doc_library_folder') ?></h5>
		    				</div>
	    					<div class="card-body">
	    						<?= __('admin.payment_gateway_doc_library_info') ?>
	    						<ul>
	    							<li><strong>custom/library/custom/</strong></li>
	    						</ul>
	    					</div>
		    			</div>

		    			<div class="card">
		    				<div class="card-header bg-secondary text-white">
		    					<h5>#5 <?= __('admin.payment_gateway_doc_logo_folder') ?></h5>
		    				</div>
	    					<div class="card-body">
	    						<?= __('admin.payment_gateway_doc_logo_info') ?>
	    						<ul>
	    							<li><strong>custom/logo/custom.png (150x100)</strong></li>
	    						</ul>
	    					</div>
		    			</div>

		    			<div class="card">
		    				<div class="card-header bg-secondary text-white">
		    					<h5><?= __('admin.payment_gateway_doc_payment_confirmation') ?></h5>
		    				</div>
	    					<div class="card-body">
	    						<code>$this->api->confirmPaymentGateway($gatewayData['id'],$status_id,$transaction_id,$payment_status);</code>
	    						<p><?= __('admin.payment_gateway_doc_payment_confirmation_info') ?></p>
	    					</div>
		    			</div>

		    			<div class="card">
		    				<div class="card-header bg-secondary text-white">
		    					<h5><?= __('admin.payment_gateway_doc_status_id_and_title') ?></h5>
		    				</div>

	    					<div class="card-body">
	    						<table class="table-striped table table-sm">
									<tr>
										<th width="90px"><?= __('admin.status_id') ?></th> 
										<th><?= __('admin.title') ?></th>
									</tr>
									<tr><td>0</td><td><?= __('admin.received') ?></td></tr>
							        <tr><td>1</td><td><?= __('admin.complete') ?></td></tr>
							        <tr><td>2</td><td><?= __('admin.total_not_match') ?></td></tr>
							        <tr><td>3</td><td><?= __('admin.denied') ?></td></tr>
							        <tr><td>4</td><td><?= __('admin.expired') ?></td></tr>
							        <tr><td>5</td><td><?= __('admin.failed') ?></td></tr>
							        <tr><td>7</td><td><?= __('admin.processed') ?></td></tr>
							        <tr><td>8</td><td><?= __('admin.refunded') ?></td></tr>
							        <tr><td>9</td><td><?= __('admin.reversed') ?></td></tr>
							        <tr><td>10</td><td><?= __('admin.voided') ?></td></tr>
							        <tr><td>11</td><td><?= __('admin.cancel_reversal') ?></td></tr>
							        <tr><td>12</td><td><?= __('admin.waiting_for_payment') ?></td></tr>
							        <tr><td>13</td><td><?= __('admin.pending') ?></td></tr>
								</table>
	    					</div>
		    			</div>

		    			<div class="card">
		    				<div class="card-header bg-secondary text-white">
		    					<h5><?= __('admin.payment_gateway_doc_zip_file') ?></h5>
		    				</div>
	    					<div class="card-body">
	    						<?= __('admin.payment_gateway_doc_zip_info') ?>
	    						<br>
	    						<ul>
	    							<li>custom/controller/custom.php</li>
	    							<li>custom/setting/custom.php</li>
	    							<li>custom/view/custom.php</li>
	    							<li>custom/library/custom/</li>
	    							<li>custom/logo/custom.png</li>
	    						</ul>
	    					</div>
		    			</div>
		    		</div>
		    	</div>
			</div>
	    </div>
	</div>
</div>