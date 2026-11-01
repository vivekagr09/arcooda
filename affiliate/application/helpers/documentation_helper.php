<?php 

function documentationToPdf(){
  $table = '<h2 style="text-align:center;line-height:10px;">'.__('admin.payment_api_documentation').'</h2>
  			
  			<div class="card">
	      	 	<h4 style="line-height:5px;">'. __('admin.how_to_create_payment_method') .'</h4>
      	 		'. __('admin.payment_gateway_doc_info_p1') .'<br/>
	    		'. __('admin.payment_gateway_doc_info_p2') .'<br/><br/>
	    		'. __('admin.payment_gateway_doc_info_p3') .'<br/>
	    		1. controller -> custom.php<br/>
	    		2. setting -> custom.php<br/>
	    		3. view -> custom.php<br/><br/>
	    		'. __('admin.payment_gateway_doc_info_p4') .'<br/>
	    		4. library -> custom<br/>
	    		5. logo -> custom.png<br/>
			</div>

			<div class="card">
				<h2 style="line-height:5px;">#1 '. __('admin.payment_gateway_doc_controller_folder') .'</h2>
				'. __('admin.payment_gateway_doc_controller_info').' '.__('admin.payment_gateway_doc_controller_info_sample_data_button').'.' .'<br/>
				'.__('admin.payment_gateway_doc_folder_structure') .'<br/>
				<strong>custom/controller/custom.php</strong><br/><br/>
				<h4 style="line-height:5px;">'. __('admin.payment_gateway_doc_example') .'</h4><br/>
				<div style="background-color:#272822;color:#fff;">';

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
				$code[] = '		';
				$code[] = '	}';

				$table .= ___h($code,'php');

				$table .= '</div>
				<h4>'. __('admin.payment_gateway_doc_file_explanation') .':</h4>
				<p><b>Class Name</b> Class name '.__('admin.payment_gateway_doc_controller_explanation_1').'</p>
				<p><b>Public Property Title</b> '.__('admin.payment_gateway_doc_controller_explanation_2').'</p>
				<p><b>Constructor</b> '.__('admin.payment_gateway_doc_controller_explanation_3').'</p>
				<p><b>public function getPaymentGatewayView</b> '.__('admin.payment_gateway_doc_controller_explanation_4').'</p>
				<p><b>public function setPaymentGatewayRequest</b> '.__('admin.payment_gateway_doc_controller_explanation_5').'</p>
				<p><b>public function customCallbackFunction</b> '.__('admin.payment_gateway_doc_controller_explanation_6').'
					</p>
			</div>

			<div class="card">
				<h2 style="line-height:5px;">#2 '.__('admin.payment_gateway_doc_setting_folder').'</h2>
				'.__('admin.payment_gateway_doc_setting_info').'<br/>
				<strong>custom/setting/custom.php</strong><br/><br/>
				<h4 style="line-height:5px;">'.__('admin.payment_gateway_doc_example').'</h4><br/>
				<div style="background-color:#272822;color:#fff;">';

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

				$table .= ___h($code,'php');

				$table .= '</div>
				<h4>'.__('admin.payment_gateway_doc_file_explanation').':</h4>
				'.__('admin.payment_gateway_doc_setting_explanation').'
			</div>

			<div class="card">
				<h2 style="line-height:5px;">#3 '.__('admin.payment_gateway_doc_view_folder').'</h2>
				'.__('admin.payment_gateway_doc_view_info').'<br/>
				<strong>custom/view/custom.php</strong><br/><br/>
				<h4 style="line-height:5px;">'.__('admin.payment_gateway_doc_example').'</h4><br/>
				<div style="background-color:#272822;color:#fff;">';

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

				$table .= ___h($code,'php');

				$table .= '</div>
				<h4>'.__('admin.payment_gateway_doc_file_explanation').':</h4>
				'.__('admin.payment_gateway_doc_view_explanation').'
			</div>

			<div class="card">
				<h2 style="line-height:5px;">#4 '.__('admin.payment_gateway_doc_library_folder').'</h2>
				'.__('admin.payment_gateway_doc_library_info').'<br/>
				<strong>custom/library/custom/</strong><br/>
			</div>

			<div class="card">
				<h2 style="line-height:5px;">#5 '.__('admin.payment_gateway_doc_logo_folder').'</h2>
				'.__('admin.payment_gateway_doc_logo_info').'<br/>
				<strong>custom/logo/custom.png</strong><br/>
			</div>

			<div class="card">
				<h2 style="line-height:5px;">'.__('admin.payment_gateway_doc_payment_confirmation').'</h2>
				<strong style="font-size:11px;">
					<i>$this->api->confirmPaymentGateway($gatewayData["id"],$status_id,$transaction_id,$payment_status);</i>
				</strong><br/>
				'. __('admin.payment_gateway_doc_payment_confirmation_info').'<br/>
			</div>

			<div class="card">
				<h2 style="line-height:5px;">'.__('admin.payment_gateway_doc_status_id_and_title').'</h2>
				<table style="font-size:12px;">
					<tr>
						<th width="90px">'.__('admin.status_id').'</th> 
						<th>'.__('admin.title').'</th>
					</tr>
					<tr><td>0</td><td>'. __('admin.received').'</td></tr>
			        <tr><td>1</td><td>'. __('admin.complete').'</td></tr>
			        <tr><td>2</td><td>'. __('admin.total_not_match').'</td></tr>
			        <tr><td>3</td><td>'. __('admin.denied').'</td></tr>
			        <tr><td>4</td><td>'. __('admin.expired').'</td></tr>
			        <tr><td>5</td><td>'. __('admin.failed').'</td></tr>
			        <tr><td>7</td><td>'. __('admin.processed').'</td></tr>
			        <tr><td>8</td><td>'. __('admin.refunded').'</td></tr>
			        <tr><td>9</td><td>'. __('admin.reversed').'</td></tr>
			        <tr><td>10</td><td>'. __('admin.voided').'</td></tr>
			        <tr><td>11</td><td>'. __('admin.cancel_reversal').'</td></tr>
			        <tr><td>12</td><td>'. __('admin.waiting_for_payment').'</td></tr>
			        <tr><td>13</td><td>'. __('admin.pending').'</td></tr>
				</table>
			</div>

			<div class="card">
				<h2 style="line-height:5px;">'. __('admin.payment_gateway_doc_zip_file') .'</h2>
				'.__('admin.payment_gateway_doc_zip_info').'<br/>
				custom/controller/custom.php<br/>
				custom/setting/custom.php<br/>
				custom/view/custom.php<br/>
				custom/library/custom/<br/>
				custom/logo/custom.png<br/>
			</div>';

    require_once(APPPATH.'third_party/tcpdf/tcpdf.php');

    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle(__('admin.payment_api_documentation'));
    $pdf->SetSubject(__('admin.payment_api_documentation'));
    $pdf->SetKeywords(__('admin.payment_api_documentation'));

    $pdf->SetMargins(5,5,5);
    $pdf->SetAutoPageBreak(TRUE,5);
    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);

    $pdf->AddPage();
    $pdf->writeHTML($table, true, false, true, false, '');
    ob_end_clean();
    $pdf->Output(__('admin.payment_api_documentation').'.pdf', 'D');
}

function ___h($text,$lan){
	$text = implode("\n", $text);
	$text = htmlentities($text);
	$text = '<pre class="language-'.$lan.'"><code class="language-'.$lan.'">'.$text.'</code></pre>';

	return $text;
}

function sampleDataToPdf($data){
	$table = '<div class="card">
							<h2 style="text-align:center;line-height:10px;">'.__('admin.payment_gateway_doc_sample_data').'</h2><br/>';

	foreach($data as $key => $value){
		$table .= '<h4 style="line-height:5px;">'.$value['filename'].'</h4>';
		$table .= '<p style="background-color:#272822;color:#fff;"><pre>'.$value['structure'].'</pre></p>';
		$table .= '<div></div><div></div>';
	}
  $table .= '</div>';

  require_once(APPPATH.'third_party/tcpdf/tcpdf.php');

  $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

  $pdf->SetCreator(PDF_CREATOR);
  $pdf->SetTitle(__('admin.payment_gateway_doc_sample_data'));
  $pdf->SetSubject(__('admin.payment_gateway_doc_sample_data'));
  $pdf->SetKeywords(__('admin.payment_gateway_doc_sample_data'));

  $pdf->SetMargins(5,5,5);
  $pdf->SetAutoPageBreak(TRUE,5);
  $pdf->SetPrintHeader(false);
  $pdf->SetPrintFooter(false);

  $pdf->AddPage();
  $pdf->writeHTML($table, true, false, true, false, '');
  ob_end_clean();
  $pdf->Output(__('admin.payment_gateway_doc_sample_data').'.pdf', 'D');
}