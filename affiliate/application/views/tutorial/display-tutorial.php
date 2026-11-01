<!-- page content -->
<div class="right_col" role="main">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel m-t-30">
            <div class="col-md-12">
                <div class="card">
                	<?php 
                	if(!isset($tutorial) || !is_array($tutorial) || count($tutorial)==0 )
                	{
                		?>
                		<div class="card-header">
                			<h4 class="card-title"> </h4>

                			<div class="card-body">
                                <div class="text-center mt-5">
                                    <div class="d-flex justify-content-center align-items-center flex-column mt-5">
                                         <i class="fas fa-exchange-alt fa-5x text-muted"></i>
                                         <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
                                    </div>
                                </div>
	                		</div>
                		</div>
                		<?php
                	}
                	else 
                	{
 					?>
 					<div class="card-header">
                		<h4 class="card-title"><?= $tutorial['name'];?> &gt; <?=$tutorial['title']; ?> </h4>
                	</div>
                	<div class="card-body">
                		 <?=  isset($tutorial) ? $tutorial['content'] :'' ?>
                	</div>
                	<?php
                	}
                	?>
                </div>
          	</div>
        </div>
    </div>
</div>
<!-- /page content -->

