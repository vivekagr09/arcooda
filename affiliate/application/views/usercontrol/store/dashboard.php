<?php
    $db =& get_instance();
    $userdetails=$db->userdetails();
    $unique_url= base_url().'register/'.base64_encode( $userdetails['id']);
    $ShareUrl = urlencode($unique_url);
    $store_setting =$db->Product_model->getSettings('store');
    $products = $db->Product_model;
?>

<div class="row">
  <div class="col-sm-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title"><?= __( 'admin.total_sale') ?></h5>
        <p class="card-text"><?= c_format($vendor_store_statistic['total_sale']) ?></p>
      </div>
    </div>
  </div>
  <div class="col-sm-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title"><?= __('admin.clicks_statistic') ?></h5>
        <p class="card-text"><?= $vendor_store_statistic['count_click'] ?></p>
      </div>
    </div>
  </div>
  <div class="col-sm-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title"><?= __( 'admin.total_sale') ?></h5>
        <p class="card-text"><?= c_format($vendor_store_statistic['total_sale']) ?></p>
      </div>
    </div>
  </div>
</div>

<div class="row mt-4">
  <div class="col-sm-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title"><?= __( 'admin.total_products') ?> 
        (<?= $vendor_store_statistic['count_product'] ?>)</h5>
        <p class="card-text"><?= c_format($vendor_store_statistic['total_sale']) ?></p>
      </div>
    </div>
  </div>
  <div class="col-sm-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title"><?= __( 'admin.total_products_coupons') ?> </h5>
        <p class="card-text">(<?= $vendor_store_statistic['count_coupon'] ?>)</p>
      </div>
    </div>
  </div>
</div>

<div class="row mt-4">
    <div class="col-sm-12">
        <div class="table-responsive" data-pattern="priority-columns">
            <table id="store-dashboard-orders" class="table  table-striped">
                <thead>
                    <tr>
                        <th><?= __('admin.order_id') ?></th>
                        <th><?= __('admin.price') ?></th>
                        <th class="txt-cntr"><?= __('admin.order_status') ?></th>
                        <th><?= __('admin.payment_method') ?></th>
                        <th><?= __('admin.ip') ?></th>
                        <th><?= __('admin.transaction') ?></th>
                        <th><?= __('admin.status') ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    
    <script type="text/javascript">
    function getPage(url){
        $this = $(this);

        $.ajax({
            url:url,
            type:'POST',
            dataType:'json',
            data:$("#filter-form").serialize(),
            beforeSend:function(){$this.btn("loading");},
            complete:function(){$this.btn("reset");},
            success:function(json){
                if(json['view']){
                    $("#store-dashboard-orders tbody").html(json['view']);
                    $("#store-dashboard-orders").show();
                } else {
                    $(".empty-div").removeClass("d-none");
                    $("#store-dashboard-orders").hide();
                }
                
                $("#store-dashboard-orders .pagination-td").html(json['pagination']);
            },
        })
    }

    getPage('<?= base_url("usercontrol/store_dashboard_order_list?page=1") ?>');
    $("#store-dashboard-orders").delegate(".pagination-td a","click",function(e){
        e.preventDefault();
        getPage($(this).attr("href"));
        return false;
    })
    </script>
</div>