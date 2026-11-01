<?php
$db =& get_instance();
$userdetails = get_object_vars($db->user_info());
$store_setting =$db->Product_model->getSettings('store');
$products = $db->Product_model;
$notifications_count = $products->getnotificationnew_count('admin',null);
?>

<div class="row g-3">
    <div class="col-xl-12">
        <div class="card-group">
            <div class="card col-md-2">
                <div class="card-body text-center">
                    <h5 class="card-title"><?= __('admin.total_balance') ?></h5>
                    <p class="card-text fs-2 ajax-total_balance"><?= $totals['full_total_balance'] ?></p>
                </div>
            </div>
            <div class="card col-md-2">
                <div class="card-body text-center">
                    <h5 class="card-title"><?= __('admin.total_sales') ?></h5>
                    <p class="card-text fs-2 ajax-total_balance"><?= $totals['total_sale_balance'] ?></p>
                </div>
            </div>
            <div class="card col-md-2">
                <div class="card-body text-center">
                    <h5 class="card-title"><?= __('admin.shipping') ?> / <?= __('admin.tax') ?></h5>
                    <p class="card-text fs-2 ajax-total_balance">
                        <?= c_format($local_store_shipping_cost) ?> /
                        <?= c_format($local_store_tax_cost) ?>
                    </p>
                </div>
            </div>
            <div class="card col-md-2">
                <div class="card-body text-center">
                    <h5 class="card-title"><?= __('admin.clicks_statistic') ?></h5>
                    <p class="card-text fs-2 ajax-all_clicks_comm"><?= $totals['full_all_clicks_comm'] ?></p>
                </div>
            </div>
            <div class="card col-md-2">
                <div class="card-body text-center">
                    <h5 class="card-title">
                        <b><a href="<?= base_url('admincontrol/listclients') ?>" class="link-dark">
                            <?= __('admin.total_clients') ?> / <?= __('admin.total_guests') ?></a>
                        </b>
                    </h5>
                    <p class="card-text fs-2">
                        <?= !empty($client_count) ? count($client_count) : '0'; ?> /
                        <?= !empty($guest_count) ? count($guest_count) : '0'; ?>
                    </p>
                </div>
            </div>
            <div class="card col-md-2">
                <div class="card-body text-center">
                    <h5 class="card-title"><?= __('admin.h_orders') ?></h5>
                    <a href="<?= base_url('admincontrol/listorders') ?>" class="link-success" role="button" data-bs-toggle="tooltip" data-bs-original-title="<?= __('admin.h_orders') ?>">
                        <span class="badge bg-danger ajax-hold_orders blink_me">
                            <?= $totals['full_local_store_hold_orders'] ?></span>
                    </a>
                </div>
            </div>
            <div class="card col-md-2">
                <div class="card-body text-center">
                    <?php $store_url = base_url('store'); ?>
                    <a class="btn btn-lg btn-success" href="<?= $store_url ?>" target="_blank">
                        <?= __('admin.view_store') ?></a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-2">
    <div class="col-xl-12">
        <div class="card-group">
            <div class="card col-md-2">
                <div class="card-body text-center">
                    <h5 class="card-title"><?= __('admin.total_products') ?></h5>
                    <p class="card-text fs-2"><?= (int)$product_count ?></p>
                    <a href="<?= base_url('admincontrol/listproduct') ?>" class="btn btn-outline-primary mt-2"><?= __('admin.view_products') ?></a>
                </div>
            </div>
            <div class="card col-md-2">
                <div class="card-body text-center">
                    <h5 class="card-title"><?= __('admin.total_forms') ?></h5>
                    <p class="card-text fs-2"><?= $form_count ?></p>
                </div>
            </div>
            <div class="card col-md-2">
                <div class="card-body text-center">
                    <h5 class="card-title"><?= __('admin.total_orders') ?></h5>
                    <p class="card-text fs-2"><?= $ordercount ?></p>
                </div>
            </div>
            <div class="card col-md-2">
                <div class="card-body text-center">
                    <h5 class="card-title"><?= __('admin.total_products_coupons') ?></h5>
                    <p class="card-text fs-2"><?= $coupon_count ?></p>
                </div>
            </div>
            <div class="card col-md-2">
                <div class="card-body text-center">
                    <h5 class="card-title"><?= __('admin.total_forms_coupons') ?></h5>
                    <p class="card-text fs-2"><?= $form_coupon_count ?></p>
                </div>
            </div>
            <div class="card col-md-2">
                <div class="card-body text-center">
                    <h5 class="card-title"><?= __('admin.payment_getaway') ?></h5>
                    <p class="card-text fs-2"><?= $payment_gateway_count ?></p>
                </div>
            </div>
        </div>
    </div>
</div>




<div class="row g-3 mt-2">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body d-flex flex-column justify-content-center">
                <div id="chartContainer" class="h-100 w-100"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-primary btn-lg mb-3">CLIENTS WORLD MAP</button>
                </div>
                <div class="world-map-users h-100"></div>
            </div>
        </div>
    </div>
</div>



<div class="row g-3 mt-2">
    <div class="col-xl-2">
        <div class="card bg-light text-center">
            <div class="card-body">
                <h6 class="card-title"><?= __('admin.support_paypal_getaway') ?></h6>
                <img class="img-fluid" src="<?= base_url('assets/payment_gateway/paypal.png') ?>" alt="PayPal">
            </div>
        </div>
    </div>
    <div class="col-xl-2">
        <div class="card bg-light text-center">
            <div class="card-body">
                <h6 class="card-title"><?= __('admin.support_paytm_getaway') ?></h6>
                <img class="img-fluid" src="<?= base_url('assets/payment_gateway/paytm.png') ?>" alt="Paytm">
            </div>
        </div>
    </div>
    <div class="col-xl-2">
        <div class="card bg-light text-center">
            <div class="card-body">
                <h6 class="card-title"><?= __('admin.support_opay_getaway') ?></h6>
                <img class="img-fluid" src="<?= base_url('assets/payment_gateway/opay.png') ?>" alt="Opay">
            </div>
        </div>
    </div>
    <div class="col-xl-2">
        <div class="card bg-light text-center">
            <div class="card-body">
                <h6 class="card-title"><?= __('admin.support_skrill_getaway') ?></h6>
                <img class="img-fluid" src="<?= base_url('assets/payment_gateway/skrill.png') ?>" alt="Skrill">
            </div>
        </div>
    </div>
    <div class="col-xl-2">
        <div class="card bg-light text-center">
            <div class="card-body">
                <h6 class="card-title"><?= __('admin.support_stripe_getaway') ?></h6>
                <img class="img-fluid" src="<?= base_url('assets/payment_gateway/stripe.png') ?>" alt="Stripe">
            </div>
        </div>
    </div>
    <div class="col-xl-2">
        <div class="card bg-light text-center">
            <div class="card-body">
                <h6 class="card-title"><?= __('admin.support_bank_transfer_cod') ?></h6>
                <img class="img-fluid" src="<?= base_url('assets/payment_gateway/bank-transfer.png') ?>" alt="Bank Transfer">
            </div>
        </div>
    </div>
</div>



<div class="row g-3">
    <div class="col-xl-2">
        <div class="card bg-light text-center">
            <div class="card-body">
                <h6 class="card-title"><?= __('admin.support_yookassa_getaway') ?></h6>
                <img class="img-fluid" src="<?= base_url('assets/payment_gateway/yookassa.png') ?>" alt="YooKassa">
            </div>
        </div>
    </div>
    <div class="col-xl-2">
        <div class="card bg-light text-center">
            <div class="card-body">
                <h6 class="card-title"><?= __('admin.support_paystack_getaway') ?></h6>
                <img class="img-fluid" src="<?= base_url('assets/payment_gateway/paystack.png') ?>" alt="Paystack">
            </div>
        </div>
    </div>
    <div class="col-xl-2">
        <div class="card bg-light text-center">
            <div class="card-body">
                <h6 class="card-title"><?= __('admin.support_xendit_getaway') ?></h6>
                <img class="img-fluid" src="<?= base_url('assets/payment_gateway/xendit.png') ?>" alt="Xendit">
            </div>
        </div>
    </div>
    <div class="col-xl-2">
        <div class="card bg-light text-center">
            <div class="card-body">
                <h6 class="card-title"><?= __('admin.support_flutterwave_getaway') ?></h6>
                <img class="img-fluid" src="<?= base_url('assets/payment_gateway/flutterwave.png') ?>" alt="Flutterwave">
            </div>
        </div>
    </div>
    <div class="col-xl-2">
        <div class="card bg-light text-center">
            <div class="card-body">
                <h6 class="card-title"><?= __('admin.support_razorpay_getaway') ?></h6>
                <img class="img-fluid" src="<?= base_url('assets/payment_gateway/razorpay.png') ?>" alt="Razorpay">
            </div>
        </div>
    </div>
    <div class="col-xl-2">
        <div class="card bg-light text-center">
            <div class="card-body">
                <h6 class="card-title"><?= __('admin.support_bank_transfer_cod') ?></h6>
                <img class="img-fluid" src="<?= base_url('assets/payment_gateway/cod.png') ?>" alt="Bank Transfer/COD">
            </div>
        </div>
    </div>
</div>



<div class="row">
    <div class="col-12">
        <div class="table-responsive">
            <table id="store-dashboard-orders" class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th><?= __('admin.order_id') ?></th>
                        <th><?= __('admin.price') ?></th>
                        <th><?= __('admin.order_status') ?></th>
                        <th><?= __('admin.payment_method') ?></th>
                        <th><?= __('admin.ip') ?></th>
                        <th><?= __('admin.transaction') ?></th>
                        <th><?= __('admin.status') ?></th>
                        <th><?= __('admin.action') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="8" class="text-center">
                            <h3 class="text-muted py-3"><?= __("admin.loading_orders_data_text") ?></h3>
                            <p class="text-muted py-3"><?= __("admin.not_taking_longer") ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
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
$(function() {
    getPage('<?= base_url("admincontrol/store_dashboard_order_list?page=1") ?>');
});
$("#store-dashboard-orders").delegate(".pagination-td a","click",function(e){
    e.preventDefault();
    getPage($(this).attr("href"));
    return false;
})
</script>

    <script>
       function renderStackedBarChart(group) {
        var group = group ? group : 'month';
        var selectedyear = $('.yearSelection').val();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {renderChart: group,selectedyear:selectedyear},
            success: function (json) {
                loadChartData(json);
            },
        })
    }
    function toArray(myObj) {
        return $.map(myObj, function(value, index) {
            return [value];
        });
    }

    $( document ).ready(function() {
        renderStackedBarChart();
    });
    
    function loadChartData(json) {
        var saleHigh = toArray(json['series_new']['sale']);
        var orderHigh = toArray(json['series_new']['order']);
        var commissionsHigh = toArray(json['series_new']['commissions']);

        var months = [
        '',
        '<?= substr(__('admin.january'),0,3) ?>',
        '<?= substr(__('admin.february'),0,3) ?>',
        '<?= substr(__('admin.march'),0,3) ?>',
        '<?= substr(__('admin.april'),0,3) ?>',
        '<?= substr(__('admin.may'),0,3) ?>',
        '<?= substr(__('admin.june'),0,3) ?>',
        '<?= substr(__('admin.july'),0,3) ?>',
        '<?= substr(__('admin.august'),0,3) ?>',
        '<?= substr(__('admin.september'),0,3) ?>',
        '<?= substr(__('admin.october'),0,3) ?>',
        '<?= substr(__('admin.november'),0,3) ?>',
        '<?= substr(__('admin.december'),0,3) ?>',
        ];
        
        var dataPoints=[];
        for (var j = 1; j <=12; j++) {
            dataPoints.push({y:j,a:saleHigh[j],b:orderHigh[j],c:commissionsHigh[j]})
        }

        Morris.Line({
          element: 'chartContainer',
          lineColors: ['#fc836e', '#3d5674', '#3d5674'],
          data: dataPoints,
          parseTime: false,
          xkey: 'y',
          ykeys: ['a','b','c'],
          xLabelFormat: function (x) {
            var index = parseInt(x.src.y);
            return months[index];
          },
          labels: ['Sales (<?=$CurrencySymbol?>)', 'Orders','Commission (<?=$CurrencySymbol?>)'],
    });
    }
</script>



<script src="<?= base_url('assets/template/js/jquery-jvectormap-2.0.5.min.js'); ?>"></script>

<script type="text/javascript" src="<?= base_url('assets/plugins/jmap/jquery-jvectormap-world-mill.js') ?>">
</script>

<link rel="stylesheet" type="text/css" href="<?= base_url('assets/plugins/jmap/css.css') ?>">

<script type="text/javascript">
    function load_userworldmap(_data) {
        $('.world-map-users').html('<div class="map"><div id="world-map-users" class="map-content"></div></div>');
        var data = {};
        $.each(_data,function(i,j){
            data[j['code']] = j['total']; 
        })

        $('.world-map-users #world-map-users').vectorMap({
            map: 'world_mill',
            zoomButtons : 1,
            zoomOnScroll: false,
            panOnDrag: 1,
            backgroundColor: 'transparent',
            markerStyle: {
                initial: {
                    fill: '#ff00ff',
                    stroke: '#ffff00',
                    "stroke-width": 1,
                    r: 5
                },
            },
            onRegionTipShow: function(e, el, code, f){
                el.html(el.html() + (data[code] ? ': <small>' + data[code]+'</small>' : ''));
            },
            series: {
                regions: [{
                    values: data,
                    scale: ['#007BFF'],
                    normalizeFunction: 'polynomial'
                }]
            },
            regionStyle: {
                initial: {
                    fill: '#2e4765'
                },
                hover: {
                  "fill-opacity": 0.8
              }
          },
          markers:false,
      });
    };

    load_userworldmap(<?= json_encode($userworldmap) ?>);
</script>

</div>
