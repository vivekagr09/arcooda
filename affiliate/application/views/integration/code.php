<?php 
if($tool['slug'])
		$a_link = base_url($tool['slug']);
	else 
		$a_link = $tool['redirectLocation'][0];
?>
<div class="modal-header">
	<h5 class="modal-title"><?= $tool['name'] ?></h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
	<div class="modal-body overflow-auto" style="max-height:70vh;">
		<ul class="nav nav-pills flex-column flex-sm-row" id="TabsNav">
		    <li class="nav-item flex-sm-fill text-sm-center">
		        <a class="nav-link bg-secondary text-white active" data-bs-toggle="pill" href="#code-code"><?= __('admin.html_code') ?></a>
		    </li>
		    <li class="nav-item flex-sm-fill text-sm-center">
		        <a class="nav-link" data-bs-toggle="pill" href="#code-link"><?= __('admin.share_link') ?></a>
		    </li>
		</ul>
		<div class="tab-content mt-4">
		  <div class="tab-pane container active" id="code-code">
			<?php if($tool['type'] == 'banner'){ ?>
				<?php foreach ($tool['ads'] as $key => $value){ 
						$code = htmlspecialchars('<a href="'.$tool['redirectLocation'][0].'"><img src="'. $value['value'] .'" ></a>'); ?>
			<div class="table-responsive">
			    <table class="table table-striped table-hover">
			        <tr>
			            <th><?= __('admin.target_url') ?> :</th>
			            <td><?= $tool['target_link'] ?></td>
			        </tr>
			        <tr>
			            <th><?= __('admin.code') ?> :</th>
			            <td><textarea type="text" onclick="this.focus();this.select()" class="form-control" readonly ><?= $code ?></textarea></td>
			        </tr>
			        <tr>
			            <th><?= __('admin.size') ?> :</th>
			            <td><?= $value['size'] ?></td>
			        </tr>
			        <tr>
			            <th><?= __('admin.preview') ?> :</th>
			            <td><img src="<?= $value['value'] ?>" class="img-fluid" style="max-width: 200px;" ></td>
			        </tr>
			    </table>
			</div>

				<?php } ?>
			<?php } else if($tool['type'] == 'text_ads'){
					$value = $tool['ads'][0];
					 
					if ($value) {
					    $style = array(
					        'padding: 5px',
					        'white-space: pre-line',
					        'border: solid ' . (isset($value['text_border_color']) ? $value['text_border_color'] : 'transparent') . ' 1px',
					        'display: inline-block',
					        'line-height: 1',
					        'color: ' . (isset($value['text_color']) ? $value['text_color'] : 'inherit'),
					        'background-color: ' . (isset($value['text_bg_color']) ? $value['text_bg_color'] : 'transparent'),
					        'font-size: ' . (isset($value['text_size']) ? $value['text_size'] . 'px' : 'inherit')
					    );

					    $code = '<span style="' . implode(";", $style) . '">';
					    $code .= '<a style="display: block; color: inherit; font-size: inherit;" href="' . $tool['redirectLocation'][0] . '">';
					    $code .= $value['value'];
					    $code .= '</a></span>';
						?>
					<div class="table-responsive">
				    <table class="table table-striped table-hover">
				        <tr>
				            <th><?= __('admin.target_url') ?> :</th>
				            <td><?= $tool['target_link'] ?></td>
				        </tr>
				        <tr>
				            <th>Code:</th>
				            <td>
				                <textarea type="text" onclick="this.focus();this.select()" class="form-control" readonly><?= htmlspecialchars($code) ?></textarea>
				            </td>
				        </tr>
				        <tr>
				            <th><?= __('admin.preview') ?> :</th>
				            <td class="preview-code"><?= $code ?></td>
				        </tr>
				    </table>
				</div>

			<?php  }
				} else if($tool['type'] == 'link_ads'){
				    $value = $tool['ads'][0];
				    if($value){
				        $code = '<a rel="sponsored" href="'.$tool['redirectLocation'][0].'">'. $value['value'] .'</a>';
				?>

			<div class="table-code table-responsive">
			    <table class="table table-bordered table-striped table-hover">
			        <tr>
			            <th class="bg-light"><?= __('admin.target_url') ?> :</th>
			            <td><?= $tool['target_link'] ?></td>
			        </tr>
			        <tr>
			            <th class="bg-light"><?= __('admin.code') ?> :</th>
			            <td>
			                <textarea type="text" onclick="this.focus();this.select()" class="code-input form-control" readonly><?= htmlspecialchars($code) ?></textarea>
			            </td>
			        </tr>
			        <tr>
			            <th class="bg-light"><?= __('admin.preview') ?> :</th>
			            <td class="preview-code"><?= $code ?></td>
			        </tr>
			    </table>
			</div>


				<?php } 

			} else if($tool['type'] == 'video_ads'){
					$value = $tool['ads'][0];
					if($value){
						$code = isset($value['iframe']) ? $value['iframe'] : '';
						$code .= '<div style="display:table;clear:both;"></div><br><a style="-moz-box-shadow:inset 0 1px 0 0 #fff;-webkit-box-shadow:inset 0 1px 0 0 #fff;box-shadow:inset 0 1px 0 0 #fff;background:-webkit-gradient(linear,left top,left bottom,color-stop(.05,#f9f9f9),color-stop(1,#e9e9e9));background:-moz-linear-gradient(top,#f9f9f9 5%,#e9e9e9 100%);background:-webkit-linear-gradient(top,#f9f9f9 5%,#e9e9e9 100%);background:-o-linear-gradient(top,#f9f9f9 5%,#e9e9e9 100%);background:-ms-linear-gradient(top,#f9f9f9 5%,#e9e9e9 100%);background:linear-gradient(to bottom,#f9f9f9 5%,#e9e9e9 100%);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#f9f9f9\', endColorstr=\'#e9e9e9\', GradientType=0);background-color:#f9f9f9;-moz-border-radius:6px;-webkit-border-radius:6px;border-radius:6px;border:1px solid #dcdcdc;display:inline-block;cursor:pointer;color:#666;font-family:Arial;font-size:15px;font-weight:700;padding:6px 24px;text-decoration:none;text-shadow:0 1px 0 #fff" href="'.$tool['redirectLocation'][0].'">'. $value['size'] .'</a>';
				?>

				<div class="table-code table-responsive">
				    <table class="table table-hover table-striped">
				        <thead class="table-light">
				            <tr>
				                <th><?= __('admin.target_url') ?> :</th>
				                <td><?= $tool['target_link'] ?></td>
				            </tr>
				        </thead>
				        <tbody>
				            <tr>
				                <th><?= __('admin.code') ?> :</th>
				                <td>
				                    <textarea type="text" onclick="this.focus();this.select()" class="code-input form-control" readonly><?= htmlspecialchars($code) ?></textarea>
				                </td>
				            </tr>
				            <tr>
				                <th><?= __('admin.preview') ?> :</th>
				                <td class="preview-code"><?= $code ?></td>
				            </tr>
				        </tbody>
				    </table>
				</div>


				<?php } 
			} ?>
		  </div>

			<div class="tab-pane container fade" id="code-link">
			    <div class="row py-3 align-items-center">
			        <div class="col-11 pe-1">
			            <input type="text" readonly value="<?= $a_link ?>" class="form-control copy-code" onclick="this.focus();this.select()">
			        </div>
			        <div class="col-1 p-0 text-end">
			            <button class="btn btn-primary btn-md" data-social-share data-share-url="<?= $a_link ?>" data-share-title="" data-share-desc=""><i class="fa fa-share-alt" aria-hidden="true"></i></button>
			        </div>
			    </div>
			</div>

		</div>
	</div>
<div class="modal-footer">
	<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('user.close') ?></button>
</div>
<?= $social_share_modal;?>
<script type="text/javascript">
	$(".preview-code *").on('click',function(event){
		event.preventDefault();
		event.stopPropagation();
		return false;
	})
</script>