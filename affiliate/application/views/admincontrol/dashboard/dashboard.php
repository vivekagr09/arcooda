<?php
$db =& get_instance();
$userdetails = get_object_vars($db->user_info());
$products = $db->Product_model;
$serverReq = checkReq();
$notifications_count = $products->getnotificationnew_count('admin',null);
$store_setting =$db->Product_model->getSettings('store');
$vendor_store_data = $this->Product_model->getSettings('vendor');
$vendor_market_data = $this->Product_model->getSettings('market_vendor');

$integration_data_per_page = 10;
$page_count = ceil(count($integration_data['array']) / $integration_data_per_page);

$enable_disable = array ('store_is_enable' => isset($store_setting['status']) ? $store_setting['status'] : 0,
);
?>

<link rel="stylesheet" type="text/css" href="<?= base_url('assets/plugins/flag/css/main.min.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= base_url("assets/plugins/table/datatables.min.css") ?>">
<link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/wallet.css?v='. time()) ?>">

<div id="notificationDiv" class="mb-2"></div>

<div class="server-errors"> 
    <?php
        $setting_market_vendor_status= $this->Product_model->getSettings('market_vendor', 'marketvendorstatus');
        $setting_vendor_min_deposit = $this->Product_model->getSettings('site', 'vendor_min_deposit');
        $setting_vendor_deposit_status = $this->Product_model->getSettings('vendor', 'depositstatus');

        if($setting_market_vendor_status['marketvendorstatus'] == 1 && $setting_vendor_min_deposit['vendor_min_deposit'] == 0 && $setting_vendor_deposit_status['depositstatus'] == 1){
            echo "<div class='bg-danger text-white p-2 rounded mb-3 fs-5'>";
            echo "<p>".__('admin.vendor_min_deposit_alert')." 
            <a href='".base_url('/admincontrol/saas_setting')."' class='text-white'>".__('admin.set_here')."</a>
            </p>";
            echo "</div>";
        }
    ?>
</div>


<!--row 1-->
<div class="row">
    <!-- Left Side: Graph-->
    <div class="col-md-7 d-flex flex-column">
        <div class="card border-secondary flex-grow-1">
            <div class="card-header fs-5 bg-primary text-white d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-bar-chart-fill me-2"></i><?= __('admin.dashboard') ?>
                    <span class="badge bg-light text-dark ms-2" id="timeRange"></span>
                </div>
                <div class="btn-group">
                  <button class="btn btn-light btn-sm" title="Export"><i class="bi bi-download"></i></button>
                  <button type="button" class="btn btn-light btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"></button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="#" id="downloadPng"><?= __('admin.download_as_png') ?></a>
                    <a class="dropdown-item" href="#" id="downloadJpeg"><?= __('admin.download_as_jpg') ?></a>
                    <a class="dropdown-item" href="#" id="downloadPdf"><?= __('admin.download_as_pdf') ?></a>
                    <a class="dropdown-item" href="#" id="downloadExcel"><?= __('admin.download_as_excel') ?></a>
                  </div>
                </div>
            </div>
            <div class="card-body">
            <div class="row sectionfour">
                <div class="col-lg-12">
                    <div class="dashboard-div mt-2">
                        <div class="graph-filter">
                            <select id="timeGroup" onchange="loadDashboardChart()" class="renderChart chart-input form-control" name="group">
                                <option value="day"><?= __('admin.day') ?></option>
                                <option value="week"><?= __('admin.week') ?></option>
                                <option value="month" selected=""><?= __('admin.month') ?></option>
                                <option value="year"><?= __('admin.year') ?></option>
                            </select>

                            <select onchange="loadDashboardChart()" class="yearSelection chart-input form-control" name='year'>
                                <?php for($i=2016; $i<= date("Y"); $i++){ ?>
                                    <option value="<?= $i ?>" <?php echo $i==date("Y") ? "selected='selected'" : '' ?>><?= $i ?></option>
                                <?php  } ?>
                            </select>
                        </div>
                        <div id="graph-chart">
                            <script src="<?= base_url('assets/plugins/chart/chart.min.js') ?>">
                            </script>
                            <canvas id="dashboard-chart" style="height: 470px; width: 100%;"class="ct-chart ct-golden-section">
                            </canvas>
                            <div id="dashboard-chart-empty" class="ct-chart d-none ct-golden-section">
                                 <div class="d-flex justify-content-center align-items-center flex-column">
                                     <i class="fas fa-exchange-alt fa-5x text-muted"></i>
                                     <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
                                 </div>
                            </div>

                            <script type="text/javascript">

                                var ctx = document.getElementById('dashboard-chart').getContext('2d');
                                var chartData = <?= json_encode($chart) ?>;

                                var months = [
                                '<?= __('admin.january') ?>',
                                '<?= __('admin.february') ?>',
                                '<?= __('admin.march') ?>',
                                '<?= __('admin.april') ?>',
                                '<?= __('admin.may') ?>',
                                '<?= __('admin.june') ?>',
                                '<?= __('admin.july') ?>',
                                '<?= __('admin.august') ?>',
                                '<?= __('admin.september') ?>',
                                '<?= __('admin.october') ?>',
                                '<?= __('admin.november') ?>',
                                '<?= __('admin.december') ?>',
                                ];

                              // Initialize chart only once
                              var chart = new Chart(ctx, {
                                type: 'line',
                                data: {},
                                options: {
                                  tooltips: {
                                    mode: 'index',
                                    intersect: false
                                  },
                                  plugins: {
                                    legend: {
                                      position: 'top',
                                      labels: {
                                        usePointStyle: true,
                                      },
                                    }
                                  },
                                  responsive: true,
                                }
                              });


      function renderDashboardChart(chartData) {
        const toNumericArray = (data) => data.map(item => (typeof item === "number" ? item : parseFloat(item)));


            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, '#4CAF50');
                gradient.addColorStop(1, '#FFFFFF');

            const gradients = {
                'order_total': ctx.createLinearGradient(0, 0, 0, 400),
                'order_commission': ctx.createLinearGradient(0, 0, 0, 400),
                'action_commission': ctx.createLinearGradient(0, 0, 0, 400)
            };

            gradients['order_total'].addColorStop(0, 'blue');
            gradients['order_total'].addColorStop(1, 'red');
            gradients['order_commission'].addColorStop(0, '#007FFF');
            gradients['order_commission'].addColorStop(1, '#00FFFF');
            gradients['action_commission'].addColorStop(0, '#8A2BE2');
            gradients['action_commission'].addColorStop(1, '#DDA0DD');

                chart.data = {
                    labels: Object.keys(chartData['order_total']),
                    datasets: [
                        {
                            label: '<?= __("admin.action_count") ?>',
                            fill: false,
                            borderWidth: 3,
                            borderColor: '#4CAF50',
                            backgroundColor: '#4CAF50',
                            data: toNumericArray(Object.values(chartData['action_count'])),
                            pointStyle: 'rectRounded',
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointHoverBorderColor: '#4CAF50',
                            pointHoverBorderWidth: 4
                        },
                        {
                            label: '<?= __("admin.order_count") ?>',
                            fill: false,
                            borderWidth: 3,
                            borderColor: '#FF9800',
                            backgroundColor: '#FF9800',
                            data: toNumericArray(Object.values(chartData['order_count'])),
                            pointStyle: 'rectRounded',
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointHoverBorderColor: '#FF9800',
                            pointHoverBorderWidth: 4
                        },
                        {
                            label: '<?= __("admin.order_commission") ?>',
                            fill: false,
                            borderWidth: 3,
                            borderColor: '#2196F3',
                            backgroundColor: gradients['order_commission'],
                            data: toNumericArray(Object.values(chartData['order_commission'])),
                            pointStyle: 'rectRounded',
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointHoverBorderColor: '#2196F3',
                            pointHoverBorderWidth: 4
                        },
                        {
                            label: '<?= __("admin.action_commission") ?>',
                            fill: false,
                            borderWidth: 3,
                            borderColor: '#9C27B0',
                            backgroundColor: gradients['action_commission'],
                            data: toNumericArray(Object.values(chartData['action_commission'])),
                            pointStyle: 'rectRounded',
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointHoverBorderColor: '#9C27B0',
                            pointHoverBorderWidth: 4
                        },
                        {
                            label: '<?= __("admin.order_total") ?>',
                            fill: true,
                            borderWidth: 3,
                            borderColor: '#E91E63',
                            backgroundColor: gradients['order_total'],
                            data: toNumericArray(Object.values(chartData['order_total'])),
                            pointStyle: 'rectRounded',
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointHoverBorderColor: '#E91E63',
                            pointHoverBorderWidth: 4
                        }
                    ]
                };

                chart.options = {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 1000,
                        easing: 'easeInOutQuad'
                    },
                    layout: {
                        padding: {
                            top: 20,
                            right: 20,
                            bottom: 20,
                            left: 20
                        }
                    },
                    elements: {
                        line: {
                            tension: 0.4,
                            shadowOffsetX: 3,
                            shadowOffsetY: 3,
                            shadowColor: "rgba(0,0,0,0.3)",
                            shadowBlur: 7
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                padding: 10
                            },
                            grid: {
                                drawBorder: false,
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            ticks: {
                                padding: 10
                            },
                            grid: {
                                drawBorder: false,
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            footerColor: '#ffffff',
                            displayColors: false,
                            cornerRadius: 4,
                            xPadding: 10,
                            yPadding: 10
                        },
                        legend: {
                            labels: {
                                boxWidth: 20,
                                padding: 20,
                                usePointStyle: true
                            }
                        },
                        beforeDraw: function (chart) {
                            let width = chart.width,
                                size = Math.floor(width / 32);
                            chart.ctx.font = size + "px Arial";
                        }
                    }
                }

                chart.update();
            }


                                function loadDashboardChart(){
                                    $.ajax({
                                        url:'<?= base_url("admincontrol/dashboard?getChartData=1") ?>',
                                        type:'POST',
                                        dataType:'json',
                                        data:$(".chart-input"),
                                        beforeSend:function(){},
                                        complete:function(){},
                                        success:function(json){
                                            // console.log(json['chart']);
                                            if(json['chart']){
                                                $("#dashboard-chart-empty").addClass('d-none');
                                                $("#dashboard-chart").removeClass('d-none');

                                                renderDashboardChart(json['chart']);
                                            } else {
                                                $("#dashboard-chart-empty").removeClass('d-none');
                                                $("#dashboard-chart").addClass('d-none');
                                            }
                                        },
                                    })
                                }

                                loadDashboardChart()
                            </script>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            <div class="card-footer bg-light d-flex flex-wrap align-items-center justify-content-between">
                <!-- Weekly Earnings -->
                <div class="d-flex align-items-center fw-bold me-3">
                    <i class="bi bi-calendar-week fs-5 me-2 text-primary"></i>
                    <div class="ms-2">
                        <span class="text-muted"><?= __('admin.weekly_earnings') ?>:</span>
                        <em class="ajax-weekly_balance text-success"><?= $admin_totals_week ?></em>
                    </div>
                </div>

                <!-- Monthly Earnings -->
                <div class="d-flex align-items-center fw-bold me-3">
                    <i class="bi bi-calendar-month fs-5 me-2 text-secondary"></i>
                    <div class="ms-2">
                        <span class="text-muted"><?= __('admin.monthly_earnings') ?>:</span>
                        <em class="rad-color ajax-monthly_balance text-primary"><?= $admin_totals_month ?></em>
                    </div>
                </div>

                <!-- Yearly Earnings -->
                <div class="d-flex align-items-center fw-bold">
                    <i class="bi bi-calendar-check fs-5 me-2 text-danger"></i>
                    <div class="ms-2">
                        <span class="text-muted"><?= __('admin.yearly_earnings') ?>:</span>
                        <em class="ajax-yearly_balance text-info"><?= $admin_totals_year ?></em>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side: DATA-->
    <div class="col-md-5 d-flex flex-column">
        <div class="card border-secondary flex-grow-1">
            <div class="card-header fs-5 bg-white text-white d-flex justify-content-between align-items-center">
                <!-- Progress Bar -->
                <div class="progress bg-primary">
                    <div class="progress-bar bg-secondary" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <!-- Icons -->
                <div class="d-flex align-items-center">
                    <?php if (is_countable($serverReq) && count($serverReq) > 0): ?>
                    <a href="javascript:void(0);" id="errorTooltip" data-bs-toggle="tooltip" title="<?= __('admin.server_errors_click') ?>" onclick="window.location.href='<?php echo base_url('admincontrol/system_status'); ?>'" class="ms-1">
                        <i class="bi bi-exclamation-diamond-fill fs-5"></i>
                    </a>
                    <?php else: ?>
                    <a href="javascript:void(0);" id="successIcon" data-bs-toggle="tooltip" title="<?= __('admin.no_server_errors') ?>" onclick="window.location.href='<?php echo base_url('admincontrol/system_status'); ?>'" class="ms-1">
                        <i class="bi bi-check-all fs-5"></i>
                    </a>
                    <?php endif; ?>

                    <?php if ($showMissingDetailsModal): ?>
                    <a href="javascript:void(0);" onclick="new bootstrap.Modal(document.getElementById('missingDetailsModal')).show();" data-bs-toggle="tooltip" title="AI-Powered Automated Tester" class="ms-1">
                        <i class="bi bi-lightbulb-fill fs-5"></i>
                    </a>
                    <?php endif; ?>

                    <a href="<?= base_url($front_url_slug) ?>" target="_blank" data-bs-toggle="tooltip" title="<?= __('admin.open_front_site') ?>" class="ms-1">
                        <i class="bi bi-house-door-fill fs-5"></i>
                    </a>
                    <a href="javascript:void(0);" data-bs-toggle="tooltip" title="<?= __('admin.dashboard_settings') ?>" class="ms-1">
                        <i class="bi bi-gear-fill fs-5 btn-setting" data-key='live_dashboard' data-type='admin'></i>
                    </a>
                    <a href="<?= base_url($front_url_slug) ?>/info.php" target="_blank" data-bs-toggle="tooltip" title="PHP Info" class="ms-1">
                        <i class="bi bi-file-code-fill fs-5"></i>
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">
                        <!-- Tabs -->
                        <ul class="nav nav-pills flex-column flex-sm-row tab-container" role="tablist" id="AdminTabs">
                            <li class="nav-item flex-sm-fill text-sm-center me-1">
                                <a class="nav-link active bg-secondary show" id="dashboard-tab" data-bs-toggle="tab" href="#dashboard" role="tab" aria-controls="dashboard" aria-selected="true">
                                    <i class="bi bi-speedometer2"></i>
                                    <?= __('admin.admin_balance') ?>
                                </a>
                            </li>
                            <li class="nav-item flex-sm-fill text-sm-center me-1">
                                <a class="nav-link" id="settings-tab" data-bs-toggle="tab" href="#settings" role="tab" aria-controls="settings" aria-selected="false">
                                    <i class="bi bi-gear"></i>
                                    <?= __('admin.system_mode') ?>
                                </a>
                            </li>
                            <li class="nav-item flex-sm-fill text-sm-center me-1">
                                <a class="nav-link" id="users-tab" data-bs-toggle="tab" href="#users" role="tab" aria-controls="users" aria-selected="false">
                                    <i class="bi bi-people"></i>
                                    <?= __('admin.online_users') ?>
                                </a>
                            </li>
                            <li class="nav-item flex-sm-fill text-sm-center me-1">
                                <a class="nav-link" id="invitation-tab" data-bs-toggle="tab" href="#invitation" role="tab" aria-controls="invitation" aria-selected="false">
                                    <i class="bi bi-share"></i>
                                    <?= __('admin.invitation_links') ?>
                                </a>
                            </li>
                        </ul>
                        <script>
                            $(document).ready(function(){
                                function manageTabClasses(target) {
                                    $('#AdminTabs .nav-link').removeClass('bg-primary').addClass('bg-secondary text-white');
                                    $(target).addClass('bg-primary text-white').removeClass('bg-secondary');
                                }

                                // Initialize with the default active tab
                                manageTabClasses('#AdminTabs .nav-link.active');

                                // Event handler for tab change
                                $('#AdminTabs a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e){
                                    manageTabClasses(e.target);
                                });
                            });
                        </script>

                        <!-- Tab Contents -->
                        <div class="tab-content">
                            <!-- Balance Data -->
                            <div class="tab-pane fade show active" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                                <!-- Section Information 1-->
                                <div id="dashboard" class="section-content active">
                                    <div class="row row-cols-sm-2 mt-3">
                                        <!-- Card 1: Balance -->
                                        <div class="col">
                                            <div class="card bg-light position-relative">
                                                <div class="position-absolute top-0 end-0 card-dashboard-css1"></div>
                                                <div class="card-body">
                                                    <!-- Row 1: Title -->
                                                    <div class="row justify-content-center mb-2">
                                                        <div class="col text-center">
                                                            <h6 class="card-title fw-bold text-primary py-2">
                                                                <?= __('admin.admin_balance') ?>
                                                            </h6>
                                                        </div>
                                                    </div>
                                                    <!-- Row 2: First Amount -->
                                                    <div class="row justify-content-center mb-2">
                                                        <div class="col text-center">
                                                            <span class="card-text h5">
                                                                <span class="card-text h4 ajax-admin_balance"><?= $fun_c_format($admin_totals['admin_balance']) ?>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <!-- Row 3: Second Amount -->
                                                    <div class="row justify-content-center mb-2">
                                                        <div class="col text-center">
                                                            <span class="card-text h5">
                                                                <span class="badge bg-success fs-6">
                                                                    <span class="ajax-admin_balance"><?= $admin_totals['admin_balance_growth']; ?>
                                                                    </span>% <i class="bi bi-arrow-up-short"></i>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Card 2: Sales -->
                                        <div class="col">
                                            <div class="card bg-light position-relative">
                                                <div class="position-absolute top-0 end-0 card-dashboard-css2"></div>
                                                <div class="card-body">
                                                    <!-- Row 1: Title -->
                                                    <div class="row justify-content-center mb-2">
                                                        <div class="col text-center">
                                                            <h6 class="card-title fw-bold text-primary py-2">
                                                                <?= __('admin.admin_sales') ?>
                                                            </h6>
                                                        </div>
                                                    </div>
                                                    <!-- Row 2: First Amount -->
                                                    <div class="row justify-content-center mb-2">
                                                        <div class="col text-center">
                                                            <span class="card-text h5">
                                                                <span class="card-text h3 ajax-sale_total_admin_store"><?= $fun_c_format($admin_totals['sale_localstore_total'] + $admin_totals['order_external_total']) ?>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <!-- Row 3: Second Amount -->
                                                    <div class="row justify-content-center mb-2">
                                                        <div class="col text-center">
                                                            <span class="card-text h5">
                                                                <span class="badge bg-success fs-6">
                                                                    <span class="ajax-admin_all_sales_growth"><?= $admin_totals['admin_all_sales_growth']; ?>
                                                                    </span>% <i class="bi bi-arrow-up-short"></i>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Card 3: Actions -->
                                        <div class="col">
                                            <div class="card bg-light position-relative">
                                                <div class="position-absolute top-0 end-0 card-dashboard-css3"></div>
                                                <div class="card-body">
                                                    <!-- Row 1: Title -->
                                                    <div class="row justify-content-center mb-2">
                                                        <div class="col text-center">
                                                            <h6 class="card-title fw-bold text-primary py-2">
                                                                <?= __('admin.admin_actions') ?>
                                                            </h6>
                                                        </div>
                                                    </div>
                                                    <!-- Row 2: First Amount -->
                                                    <div class="row justify-content-center mb-2">
                                                        <div class="col text-center">
                                                            <span class="card-text h5">
                                                                <span class="card-text h3 ajax-click_action_total"><?= (int)$admin_totals['click_action_total'] ?></span>
                                                                <span class="mx-1">/</span>
                                                                <span class="card-text h3 ajax-click_action_commission"><?= $fun_c_format($admin_totals['click_action_commission']) ?></span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <!-- Row 3: Second Amount -->
                                                    <div class="row justify-content-center mb-2">
                                                        <div class="col text-center">
                                                            <span class="card-text h5">
                                                                <span class="badge bg-success fs-6">
                                                                    <span class="ajax-click_action_commission_growth"><?= $admin_totals['click_action_commission_growth']; ?></span>% <i class="bi bi-arrow-up-short"></i>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Card 4: Clicks -->
                                        <div class="col">
                                            <div class="card bg-light position-relative">
                                                <div class="position-absolute top-0 end-0 card-dashboard-css4"></div>
                                                <div class="card-body">
                                                    <!-- Row 1: Title -->
                                                    <div class="row justify-content-center mb-2">
                                                        <div class="col text-center">
                                                            <h6 class="card-title fw-bold text-primary py-2">
                                                                <?= __('admin.admin_clicks') ?>
                                                            </h6>
                                                        </div>
                                                    </div>
                                                    <!-- Row 2: First Amount -->
                                                    <div class="row justify-content-center mb-2">
                                                        <div class="col text-center">
                                                            <span class="card-text h5">
                                                                <span class="card-text h3 ajax-all_click_total"><?= (int)($admin_totals['click_localstore_total'] + $admin_totals['click_integration_total'] + $admin_totals['click_form_total']) ?></span>
                                                                <span class="mx-1">/</span>
                                                                <span class="card-text h3 ajax-all_click_commission"><?= $fun_c_format($admin_totals['click_localstore_commission'] + $admin_totals['click_integration_commission'] + $admin_totals['click_form_commission']) ?></span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <!-- Row 3: Second Amount -->
                                                    <div class="row justify-content-center mb-2">
                                                        <div class="col text-center">
                                                            <span class="card-text h5">
                                                                <span class="badge bg-success fs-6">
                                                                    <span class="ajax-all_clicks_comission_growth"><?= $admin_totals['all_clicks_comission_growth']; ?></span>% <i class="bi bi-arrow-up-short"></i>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!--vendor sales-->
                                    <div class="col" <?= $enable_disable['store_is_enable'] == 0 ? 'style="display:none;"' : ''; ?>>
                                        <div class="card shadow h-100">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center justify-content-between bg-light p-2 rounded flex-wrap">
                                                    <!-- Left - Sales Icon -->
                                                    <div class="mb-2 mb-md-0">
                                                        <span class="bi bi-cash text-primary fs-4"></span>
                                                    </div>
                                                    <!-- Center - Sales Info -->
                                                    <div class="text-center mb-2 mb-md-0">
                                                        <span class="text-muted fs-5"><?= __('admin.admin_vendor_sales') ?>:</span>
                                                        <span class="ajax-sale_localstore_vendor_total fs-4 text-dark fw-bold"><?= $fun_c_format($admin_totals['sale_localstore_vendor_total']) ?></span>
                                                    </div>
                                                    <!-- Right - Sales Growth Badge -->
                                                    <div class="mb-2 mb-md-0">
                                                        <span class="badge bg-success fs-5">
                                                            <span class="ajax-vendor_all_sales_growth"><?= $admin_totals['vendor_all_sales_growth']; ?></span>% <i class="bi bi-arrow-up-short"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--vendor sales-->
                                </div>
                            </div>
                                    
    <!-- System Mode -->
    <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
        <!-- Section Information -->
        <div class="row mt-4">
            <label>
                <div class="mb-1"><strong><?= __('admin.admin_mode_explanation_1') ?></strong></div>
                <ul>
                    <li><span class="fw-bold"><?= __('admin.local_store_mode') ?>:</span> <?= __('admin.local_store_mode_description') ?></li>
                    <li><span class="fw-bold"><?= __('admin.external_mode') ?>:</span> <?= __('admin.external_mode_description') ?></li>
                </ul>
                <p>
                    <strong><?= __('admin.note') ?>:</strong> <?= __('admin.admin_mode_explanation_2') ?>
                </p>
            </label>
        </div>
        <!-- Mode Settings -->
        <div class="row mt-3">
            <!-- Admin Settings -->
            <div class="col-12 col-md-6">
                <h5><?= __('admin.admin_mode_settings')?></h5>
                <!-- Store Mode -->
                <div class="card mb-3">
                    <div class="card-body">
                        <label class="form-check form-switch fs-6">
                            <input id="storeModeCheckbox" class="form-check-input activity" type="checkbox" data-setting_type="store" data-setting_key="status" data-sidebar="store" <?= ((int)$store_is_enable > 0) ? "checked" : ""; ?>>
                            <i class="bi bi-shop fs-6" data-toggle="tooltip" title="<?= __('admin.store_module') ?>" style="margin-right: 5px;"></i>
                            <span><?= __('admin.store_mode') ?></span>
                        </label>
                    </div>
                </div>
                <!-- External Mode -->
                <div class="card mb-3">
                    <div class="card-body">
                        <label class="form-check form-switch fs-6">
                            <input id="externalModeCheckbox" class="form-check-input activity" type="checkbox" data-setting_type="market_tools" data-setting_key="markettools_status" data-sidebar="market_tools" <?= ((int)$market_tools_is_enable > 0) ? "checked" : ""; ?>>
                            <i class="bi bi-gear fs-6" data-toggle="tooltip" title="<?= __('admin.market_tools_module') ?>" style="margin-right: 5px;"></i>
                            <span><?= __('admin.external_mode') ?></span>
                        </label>
                    </div>
                </div>
            </div>
            <!-- Vendor Settings -->
            <div class="col-12 col-md-6">
                <h5><?= __('admin.vendor_mode_settings')?></h5>
                <!-- Vendor Setting 1 for Store Mode -->
                <div class="card mb-3">
                    <div class="card-body">
                        <label class="form-check form-switch fs-6">
                        <input 
                            id="vendor_setting" 
                            class="form-check-input activity" 
                            type="checkbox" 
                            <?= ((int)$vendor_store_data['storestatus'] > 0) ? "checked" : ""; ?> 
                            data-toggle="toggle" 
                            data-size="normal" 
                            data-on="<?= __('admin.status_on'); ?>" 
                            data-off="<?= __('admin.status_off'); ?>" 
                            data-setting_key="storestatus" 
                            data-setting_type="vendor"
                        >
                            <i class="bi bi-person fs-6" data-toggle="tooltip" title="<?= __('vendor.setting_1') ?>" style="margin-right: 5px;"></i>
                            <span><?= __('admin.store_mode') ?></span>
                        </label>
                    </div>
                </div>
                <!-- Vendor Setting 2 for External Mode -->
                <div class="card mb-3">
                    <div class="card-body">
                        <label class="form-check form-switch fs-6">
                            <input 
                                id="market_vendor-setting" 
                                class="form-check-input activity" 
                                type="checkbox" 
                                <?= ((int)$vendor_market_data['marketvendorstatus'] > 0) ? "checked" : ""; ?>
                                data-toggle="toggle" 
                                data-size="normal" 
                                data-on="<?= __('admin.status_on'); ?>" 
                                data-off="<?= __('admin.status_off'); ?>" 
                                data-setting_key="marketvendorstatus" 
                                data-setting_type="market_vendor"
                            >
                            <i class="bi bi-person fs-6" data-toggle="tooltip" title="<?= __('vendor.setting_2') ?>" style="margin-right: 5px;"></i>
                            <span><?= __('admin.external_mode') ?></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
                            <!-- Users Data -->
                            <div class="tab-pane fade" id="users" role="tabpanel" aria-labelledby="users-tab">
                                <!-- Section Information -->
                                <div class="row mt-5">
                                    <div class="col-12">
                                        <div class="card mb-3">
                                            <div class="card-body d-flex align-items-center justify-content-center flex-wrap">
                                                <?php
                                                $top_user = isset($populer_users[0]) ? $populer_users[0] : false;
                                                if ($top_user) {
                                                    $users_pic = (!empty($products->getAvatar($top_user['avatar']))) ? ($products->getAvatar($top_user['avatar'])) : base_url('assets/vertical/assets/images/no-image.jpg');
                                                ?>
                                                <!-- User Info Container -->
                                                <div class="position-relative me-3">
                                                    <!-- User Image -->
                                                    <img src="<?= $users_pic ?>" alt="<?= $top_user['firstname'] . ' ' . $top_user['lastname'] ?>" class="rounded-circle" style="width: 68px;">
                                                    <!-- Country Flag -->
                                                    <div class="position-absolute bottom-0 start-0">
                                                        <?php if ($top_user['sortname']) { ?>
                                                        <img src="<?= base_url('assets/vertical/assets/images/flags/' . strtolower($top_user['sortname']) . '.png') ?>" alt="<?= strtolower($top_user['sortname']) ?>" style="width: 20px; margin-bottom: -7px;">
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                
                                                <!-- User Info -->
                                                <span class="badge bg-success py-2 px-3 me-2 fs-6 mb-1"><?= __('admin.top_user') ?>: <strong><?= $top_user['firstname'] . ' ' . $top_user['lastname'] ?></strong></span>
                                                <span class="badge bg-success py-2 px-3 me-2 fs-6 mb-1"><?= __('admin.balance') ?>: <strong><?= $fun_c_format($top_user['amount']) ?></strong></span>
                                                <span class="badge bg-success py-2 px-3 fs-6 mb-1"><?= __('admin.commission') ?>: <strong><?= $fun_c_format($top_user['all_commition']) ?></strong></span>
                                                
                                                <?php } else { ?>
                                                <!-- No Data Found -->
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="fas fa-exchange-alt fa-3x text-muted"></i>
                                                    <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
                                                </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Online Users -->
                                    <div class="col-12">
                                        <div class="card mb-3">
                                            <div class="card-body d-flex align-items-center justify-content-center flex-wrap">
                                                <h5 class="me-3 mb-0"><?= __('admin.admin_total_online') ?>:</h5>
                                                <span class="badge bg-success py-2 px-3 me-2 fs-6 mb-1"><?= __('admin.admin_admin') ?>: <span class="ajax-online-admin"><?= (int)$online_count['admin']['online'] ?></span></span>
                                                <span class="badge bg-success py-2 px-3 me-2 fs-6 mb-1"><?= __('admin.admin_vendor') ?>: <span class="ajax-online-vendor"><?= (int)$online_count['vendor']['online'] ?></span></span>
                                                <span class="badge bg-success py-2 px-3 me-2 fs-6 mb-1"><?= __('admin.admin_affiliate') ?>: <span class="ajax-online-affiliate"><?= (int)$online_count['user']['online'] ?></span></span>
                                                <span class="badge bg-success py-2 px-3 fs-6 mb-1"><?= __('admin.admin_client') ?>: <span class="ajax-online-client"><?= (int)$online_count['client']['online'] ?></span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Invitation Data -->
                            <div class="tab-pane fade" id="invitation" role="tabpanel" aria-labelledby="invitation-tab">
                                <div class="col mt-5">
                                    <div class="card shadow rounded">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <h4 class="card-title mb-0"><?= __('admin.register_new_affiliate_account_link') ?></h4>
                                                <div class="input-group">
                                                    <?php $affiliate_share_url = base_url('register/' . base64_encode($userdetails['id'])); ?>
                                                    <input id="unique_re_link_affiliate" type="text" class="form-control" readonly="readonly" value="<?= $affiliate_share_url ?>">
                                                    <div class="input-group-append">
                                                        <a href="javascript:void(0);" copyToClipboard="<?= $affiliate_share_url ?>" class="btn btn-outline-secondary">
                                                            <i class="bi bi-clipboard"></i>
                                                            <span class="copy-status"></span>
                                                        </a>
                                                        <a href="javascript:void(0)" data-social-share data-share-url="<?= $affiliate_share_url; ?>" data-share-title="" data-share-desc="" class="btn btn-outline-secondary">
                                                            <i class="bi bi-share"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card shadow rounded">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <h4 class="card-title mb-0"><?= __('admin.register_new_vendor_account_link') ?></h4>
                                                <div class="input-group">
                                                    <?php $vendor_share_url = base_url('register/vendor'); ?>
                                                    <input id="unique_re_link_vendor" type="text" class="form-control" readonly="readonly" value="<?= $vendor_share_url ?>">
                                                    <div class="input-group-append">
                                                        <a href="javascript:void(0);" copyToClipboard="<?= $vendor_share_url ?>" class="btn btn-outline-secondary">
                                                            <i class="bi bi-clipboard"></i>
                                                            <span class="copy-status"></span>
                                                        </a>
                                                        <a href="javascript:void(0)" data-social-share data-share-url="<?= $vendor_share_url; ?>" data-share-title="" data-share-desc="" class="btn btn-outline-secondary">
                                                            <i class="bi bi-share"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light d-flex flex-wrap align-items-center justify-content-between">
                <!-- Last Updated -->
                <div class="d-flex align-items-center text-muted fw-bold">
                    <i class="bi bi-clock-fill fs-5 me-2"></i>
                    <small>
                        <?= __('admin.last_update') ?>: 
                        <span class="ajax-time text-primary">
                            <?= date("h:i:s A") ?>
                        </span>
                    </small>
                </div>

                <!-- Session Timeout -->
                <div class="d-flex align-items-center text-muted fw-bold">
                    <i class="bi bi-alarm-fill fs-5 me-2"></i>
                    <small>
                        <?= __('admin.session_timeout') ?>: 
                        <span class="session-timer text-danger">
                            <em>
                                <?php
                                    $hours = floor($timeout / 3600);
                                    $minutes = floor(($timeout % 3600) / 60);
                                    $seconds = ($timeout % 3600) % 60;
                                    echo sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
                                ?>
                            </em>
                        </span>
                    </small>
                </div>

                <!-- Version Number -->
                <div class="d-flex align-items-center text-muted fw-bold">
                    <i class="bi bi-info-circle-fill fs-5 me-2"></i>
                    <small><?= __('admin.version') ?>: <span class="text-success"><a href="<?= base_url('admincontrol/script_details') ?>" class="text-decoration-none"><?= SCRIPT_VERSION ?></a></span></small>
                </div>
            </div>
        </div>
    </div>
</div>
<!--row 1-->

<!--row 2-->
<div class="row mb-2">
    <?php 
        $categories = ['clicks', 'action_clicks', 'sale', 'affiliate_user', 'client_user'];
        foreach($categories as $category) { 
    ?>
        <div class="col">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="text-center mb-0">
                        <span class="badge bg-secondary float-start fs-6"><?= (int)$statistics[$category . '_count'] ?></span>
                        <?= __('admin.' . $category . '_by_country') ?>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if(isset($statistics[$category]) && !empty($statistics[$category])): ?>
                        <div id="<?= $category ?>-chart" class="morris-chart w-100" style="height: 200px;"></div>
                        <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#<?= $category ?>-large-modal">
                            <?= __('admin.view_larger') ?>
                        </button>
                    <?php else: ?>
                        <div class="text-center">
                            <i class="fas fa-exchange-alt fa-5x text-muted"></i>
                            <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Large Modal -->
        <?php if(isset($statistics[$category]) && !empty($statistics[$category])): ?>
        <div class="modal fade" id="<?= $category ?>-large-modal" tabindex="-1">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-body">
                        <div id="<?= $category ?>-chart-large" class="morris-chart w-100" style="height:300px;"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

    <?php } ?>
</div>
<!--row 2-->


<!--morrise chart scripts-->
<script>
    var statistics = <?php echo json_encode($statistics); ?>;
    var colors = ['#007bff', '#6c757d', '#28a745', '#17a2b8', '#ffc107'];
    var categories = ['clicks', 'action_clicks', 'sale', 'affiliate_user', 'client_user'];

    function debounce(func, wait) {
        let timeout;
        return function() {
            clearTimeout(timeout);
            timeout = setTimeout(func, wait);
        };
    }

    function createMorrisDonut(elementId, data) {
        Morris.Donut({
            element: elementId,
            data: data,
            resize: true,
            colors: colors,
            labelColor: '#333333',
            formatter: function (y, data) { return y + ' (' + data.label + ')' }
        });
    }

    $(document).ready(function() {
        categories.forEach(category => {
            if (statistics[category]) {
                var data = Object.keys(statistics[category]).map(function(country) {
                    return { label: country, value: statistics[category][country] };
                });

                if ($("#" + category + "-chart").length) {
                    createMorrisDonut(category + "-chart", data);
                }

                $('#' + category + '-large-modal').on('shown.bs.modal', function() {
                    createMorrisDonut(category + "-chart-large", data);
                });
            } else {
                // console.log("Statistics data for category " + category + " is missing or null.");
            }
        });
    });

    function resizeCharts() {
        try {
            categories.forEach(category => {
                if (statistics[category]) {
                    if ($("#" + category + "-chart").length > 0) { 
                        var data = Object.keys(statistics[category]).map(function(country) {
                            return { label: country, value: statistics[category][country] };
                        });
                        $("#" + category + "-chart").empty();
                        createMorrisDonut(category + "-chart", data);
                    }
                }
            });
        } catch (e) {
            console.error("An error occurred while resizing the charts: ", e);
        }
    }

    $(window).resize(debounce(function() {
        resizeCharts();
    }, 250));

</script>
<!--morrise chart scripts-->


<!--Orders-->
<div class="col-md-12 d-flex flex-column mt-3">
    <div class="card border-secondary flex-grow-1">
        <div class="card-header fs-5 bg-primary text-white d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-list-ol me-2"></i><?= __('admin.latest_orders') ?>
                <span class="badge bg-light text-dark ms-2"></span>
            </div>
        </div>
        <div class="card-body d-flex flex-column">
            <div class="table-responsive flex-grow-1">
                <table class="table table-striped table-hover orders-table-new">
                    <thead class="table-light">
                        <tr>
                            <th><?= __('admin.order_id') ?></th>
                            <th><?= __('admin.total') ?></th>
                            <th><?= __('admin.country') ?></th>
                            <th><?= __('admin.store') ?></th>
                            <th><?= __('admin.status') ?></th>
                            <th><?= __('admin.commission') ?></th>
                            <th><?= __('admin.date') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="100%" class="text-center">
                                <div class="py-4">
                                    <h3 class="text-muted"><?= __("admin.loading_orders_data_text") ?></h3>
                                    <h5 class="text-muted"><?= __("admin.not_taking_longer") ?></h5>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <nav aria-label="Page navigation" class="mt-3">
                <ul class="pagination justify-content-center" id="pagination">
                </ul>
            </nav>
        </div>

        <div class="card-footer bg-light d-flex flex-wrap align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <a href="<?= base_url('admincontrol/store_orders') ?>" class="btn btn-primary">
                    <i class="bi bi-arrow-right-circle-fill me-2">
                    </i><?= __('admin.view_all_orders') ?>
                </a>
            </div>
        </div>
    </div>
</div>
<!--Orders-->

<!--To-do-list + Map-->
<div class="col-12 mb-3">
    <div class="row">
    <!-- Left Side: Calendar-->
    <script src="<?= base_url('assets/js') ?>/moment.js" type="text/javascript"></script>
    <script src="<?= base_url('assets/js') ?>/main.min.js"></script>
    <script src="<?= base_url('assets/js') ?>/fullcalendar.min.js"></script>
    <link rel="stylesheet" href="<?= base_url('assets/css') ?>/fullcalendar.min.css"/>
        <div class="col-md-6 d-flex flex-column">
            <div class="card border-secondary flex-grow-1">
            <div class="card-header fs-5 bg-primary text-white d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-list-task me-2"></i><?= __('admin.to_do_list') ?>
                    <span class="badge bg-light text-dark ms-2"></span>
                </div>
            </div>
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-add-todo" tabindex="-1" aria-labelledby="to_do_list_title" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title h4" id="to_do_list_title"><?= __('admin.add_to_do_list') ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="todonotesCal" placeholder="Add To-do note">
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="tododateCal" placeholder="To-do date">
                        </div>
                        <div class="mb-3">
                            <button type="button" class="btn btn-primary w-100" id="btnAddCalnote"><?= __('admin.add') ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!-- Right Side: Map-->
<div class="col-md-6 d-flex flex-column">
    <div class="card border-secondary flex-grow-1">
        <div class="card-header fs-5 bg-primary text-white d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-geo-alt me-2"></i><?= __('admin.affiliates_map') ?>
                <span class="badge bg-light text-dark ms-2"></span>
            </div>
        </div>
        <div class="card-body">
            <!--Admin users map-->
            <div class="d-flex flex-column flex-grow-1 world-map pl-4 pr-4 pt-5 pb-4">
                <script type="text/javascript" src="<?= base_url('assets/plugins/jmap/jquery-jvectormap-2.0.3.min.js') ?>">
                </script>
                <script type="text/javascript" src="<?= base_url('assets/plugins/jmap/jquery-jvectormap-world-mill.js') ?>">
                </script>
                <link rel="stylesheet" type="text/css" href="<?= base_url('assets/plugins/jmap/css.css') ?>">
                <div class="world-map-users"></div>
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
            <!--Admin users map-->
        </div>
    </div>
</div>

    </div>
</div>
<!--To-do-list + Map-->


 
<!--Top affiliates + Logs-->
<div class="row mb-3">
    <!-- Popular Affiliates -->
        <div class="col-md-6 d-flex flex-column">
            <div class="card border-secondary flex-grow-1">
            <!-- Header -->
            <div class="card-header fs-5 bg-primary text-white d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-people me-2"></i><?= __('admin.popular_affiliates') ?>
                    <span class="badge bg-light text-dark ms-2"></span>
                </div>
                <div>
                    <select class="form-select" id="popular_affiliates_sorting" name="popular_affiliates_sorting">
                        <option value="All" <?= ($popular_affiliates == "All") ? 'selected' : ''; ?> >All</option>
                        <option value="Week" <?= ($popular_affiliates == "Week") ? 'selected' : ''; ?> >Week</option>
                        <option value="Month" <?= ($popular_affiliates == "Month") ? 'selected' : ''; ?> >Month</option>
                        <option value="Year" <?= ($popular_affiliates == "Year") ? 'selected' : ''; ?> >Year</option>
                    </select>
                </div>
            </div>
            <!-- Content -->
            <div class="affiliate-table scroll-bar">
                <?php if (empty($populer_users)): ?>
                    <div class="d-flex justify-content-center align-items-center flex-column mt-5">
                        <i class="fas fa-exchange-alt fa-5x text-muted"></i>
                        <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
                    </div>
                <?php else: ?>
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th><?= __('admin.name') ?></th>
                                <th><?= __('admin.country') ?></th>
                                <th><?= __('admin.balance') ?></th>
                                <th><?= __('admin.commission') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $rank = 1; ?>
                            <?php foreach ($populer_users as $key => $users): ?>
                                <tr class="<?= ($rank <= 3) ? 'table-primary' : ''; ?>">
                                    <td><span class="badge bg-primary"><?= $rank ?></span></td>
                                    <td><img class="top-affiliate-image" src="<?= $products->getAvatar($users['avatar']); ?>" alt="<?= $users['firstname'] . ' ' . $users['lastname']; ?>" /><?= $users['firstname'] . ' ' . $users['lastname']; ?></td>
                                    <td><img class="top-affiliate-country-flag" src="<?= $users['sortname'] != '' ? base_url('assets/vertical/assets/images/flags/' . strtolower($users['sortname']) . '.png') : ''; ?>" alt="<?= strtoupper($users['sortname']) ?>" /></td>
                                    <td><?= $fun_c_format($users['amount']); ?></td>
                                    <td><?= $fun_c_format($users['all_commition']); ?></td>
                                </tr>
                                <?php $rank++; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            <!-- Footer -->
            <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                <small class="text-muted"><?= __('admin.popular_affiliates') ?></small>
                <a href="<?= base_url('admincontrol/userslist') ?>" class="btn btn-primary btn-sm"><?= __('admin.view_all_users') ?></a>
            </div>
        </div>
    </div>


    <!-- Live Logs -->
        <div class="col-md-6 d-flex flex-column">
            <div class="card border-secondary flex-grow-1">
            <!-- Header -->
            <div class="card-header fs-5 bg-primary text-white d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-flag me-2"></i><?= __('admin.live_logs') ?>
                <span class="badge bg-light text-dark ms-2"></span>
            </div>
                <div class="setting-area">
                    <a href="javascript:void(0);" class="btn-count-notification" data-key='live_log' data-type='admin'>
                        <i class="bi bi-bell"></i>
                    </a>
                    <a href="javascript:void(0);" class="log-setting btn-setting" data-key='live_log' data-type='admin'>
                        <i class="bi bi-gear"></i>
                    </a>
                </div>
            </div>
            <!-- Content -->
            <div class="live-wrap scroll-bar">
                <?php if(empty($live_window)): ?>
                    <div class="live-wrap-empty-data d-flex justify-content-center align-items-center flex-column">
                        <i class="fas fa-exchange-alt fa-5x text-muted"></i>
                        <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
                    </div>
                <?php else: ?>
                    <ul class="ajax-live_window list-unstyled">
                        <?php foreach($live_window as $key => $value){ ?>
                            <?= $value['title'] ?>
                        <?php } ?>
                    </ul>
                <?php endif; ?>
            </div>
            <!-- Footer -->
            <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                <small class="text-muted"><?= __('admin.live_logs') ?></small>
                <a href="<?= base_url('admincontrol/notification') ?>" class="btn btn-primary btn-sm"><?= __('admin.view_all') ?></a>
            </div>
        </div>
    </div>
</div>
<!--Top affiliates + Logs-->



<!--row 8-->
<div class="col-12 mb-3">
    <div class="row">
        <!--clicks cube-->
        <div class="col-lg-3 mb-3"> 
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="text-center pe-3">
                            <i class="bi bi-mouse text-white fs-3 bg-primary p-1 rounded-circle"></i>
                        </div>
                        <div class="text-center">
                            <h2 class="card-title"><?= __( 'admin.admin_all_clicks' ) ?></h2>
                            <p class="card-text"><?= __('admin.total') ?> 
                                <span class="ajax-click_all_total">
                                    <?= (int)(
                                        $admin_totals['click_localstore_total'] +
                                        $admin_totals['click_integration_total'] +
                                        $admin_totals['click_form_total'] 
                                    ) ?>
                                </span>
                                / 
                                <span class="click_all_commission">
                                    <?= $fun_c_format(
                                        $admin_totals['click_localstore_commission'] +
                                        $admin_totals['click_integration_commission'] +
                                        $admin_totals['click_form_commission']
                                    ) ?>
                                </span>
                            </p> 
                        </div>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between"> 
                            <span><?= __( 'admin.admin_ecommerce' ) ?></span> 
                            <strong>
                                <span class="ajax-click_localstore_total"><?= (int)$admin_totals['click_localstore_total'] ?></span> 
                                / 
                                <span class="ajax-click_localstore_commission"><?= $fun_c_format($admin_totals['click_localstore_commission']) ?></span>
                            </strong> 
                        </li>
                        <li class="list-group-item d-flex justify-content-between"> 
                            <span><?= __( 'admin.admin_external' ) ?></span> 
                            <strong>
                                <span class="ajax-click_integration_total"><?= (int)$admin_totals['click_integration_total'] ?></span>
                                / 
                                <span class="ajax-click_integration_commission"><?= $fun_c_format($admin_totals['click_integration_commission']) ?></span>  
                            </strong> 
                        </li>
                        <li class="list-group-item d-flex justify-content-between"> 
                            <span><?= __( 'admin.admin_forms' ) ?></span> 
                            <strong>
                                <span class="ajax-click_form_total"><?= (int)$admin_totals['click_form_total'] ?></span>
                                / 
                                <span class="ajax-click_form_commission"><?= $fun_c_format($admin_totals['click_form_commission']) ?></span>
                            </strong> 
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!--clicks cube-->

        <!--order cube-->
        <div class="col-lg-3 mb-3"> 
            <div class="card h-100">
                <div class="card-body">

                <div class="d-flex align-items-center justify-content-center mb-3">
                    <div class="text-center pe-3">
                        <i class="bi bi-bar-chart-fill text-white fs-3 bg-primary p-1 rounded-circle"></i>
                    </div>
                    <div class="text-center">
                        <h2 class="card-title"><?= __( 'admin.admin_order_commission' ) ?></h2>
                        <p class="card-text"><?= __('admin.total') ?> 
                            <span class="ajax-all_sale_count">
                                <?= (int)(
                                    $admin_totals['sale_localstore_count'] +
                                    $admin_totals['order_external_count'] +
                                    $admin_totals['sale_localstore_vendor_count']
                                ) ?>
                            </span>
                            / 
                            <span class="ajax-all_sale_commission">
                                <?= $fun_c_format(
                                    $admin_totals['sale_localstore_commission'] +
                                    $admin_totals['order_external_commission'] +
                                    $admin_totals['sale_localstore_vendor_commission']
                                ) ?>
                            </span>
                        </p> 
                    </div>
                </div>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between"> 
                            <span><?= __( 'admin.admin_ecommerce' ) ?></span> 
                            <strong>
                                <span class="ajax-sale_localstore_count"><?= (int)$admin_totals['sale_localstore_count'] ?></span>
                                / 
                                <span class="ajax-sale_localstore_commission"><?= $fun_c_format($admin_totals['sale_localstore_commission']) ?></span>
                            </strong> 
                        </li>
                        <li class="list-group-item d-flex justify-content-between"> 
                            <span><?= __( 'admin.admin_vendor' ) ?></span> 
                            <strong>
                                <span class="ajax-sale_localstore_vendor_count"><?= (int)$admin_totals['sale_localstore_vendor_count'] ?></span>
                                / 
                                <span class="ajax-sale_localstore_vendor_commission"><?= $fun_c_format($admin_totals['sale_localstore_vendor_commission']) ?></span>
                            </strong> 
                        </li>
                        <li class="list-group-item d-flex justify-content-between"> 
                            <span><?= __( 'admin.admin_external' ) ?></span> 
                            <strong>
                                <span class="ajax-order_external_count"><?= (int)$admin_totals['order_external_count'] ?></span>
                                / 
                                <span class="ajax-order_external_commission"><?= $fun_c_format($admin_totals['order_external_commission']) ?></span>
                            </strong> 
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!--order cube-->

        <!--admin statistics cube-->
        <div class="col-lg-3 mb-3"> 
            <div class="card h-100">
                <div class="card-body">
                    <div class="user-header d-flex align-items-center justify-content-center mb-3">
                        <div class="text-center pe-3">
                            <i class="bi bi-wallet text-white fs-3 bg-primary p-1 rounded-circle"></i>
                        </div>
                        <div class="text-center">
                            <h2 class="card-title"><?= __( 'admin.admin_wallet_statistics' ) ?></h2>
                        </div>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between"> 
                            <span><?= __( 'admin.admin_hold' ) ?></span> 
                            <strong>
                                <span class="ajax-wallet_unpaid_amounton_hold_count"><?= (int)$admin_totals['wallet_unpaid_amounton_hold_count'] ?></span>
                                / 
                                <span class="ajax-wallet_on_hold_amount"><?= $fun_c_format($admin_totals['wallet_on_hold_amount']) ?></span>
                            </strong> 
                        </li>
                        <li class="list-group-item d-flex justify-content-between"> 
                            <span><?= __( 'admin.admin_unpaid' ) ?></span> 
                            <strong>
                                <span class='ajax-wallet_unpaid_count'><?= (int)$admin_totals['wallet_unpaid_count'] ?></span>
                                / 
                                <span class='ajax-wallet_unpaid_amount'><?= $fun_c_format($admin_totals['wallet_unpaid_amount']) ?></span>
                            </strong> 
                        </li>
                        <li class="list-group-item d-flex justify-content-between"> 
                            <span><?= __( 'admin.admin_request' ) ?></span> 
                            <strong>
                                <span class="ajax-wallet_request_sent_count"><?= (int)$admin_totals['wallet_request_sent_count'] ?></span>
                                / 
                                <span class="ajax-wallet_request_sent_amount"><?= $fun_c_format($admin_totals['wallet_request_sent_amount']) ?></span>
                            </strong> 
                        </li>
                        <li class="list-group-item d-flex justify-content-between"> 
                            <span><?= __( 'admin.admin_paid' ) ?></span> 
                            <strong>
                                <span class="ajax-wallet_accept_count"><?= (int)$admin_totals['wallet_accept_count'] ?></span>
                                / 
                                <span class="ajax-wallet_accept_amount"><?= $fun_c_format($admin_totals['wallet_accept_amount']) ?></span>
                            </strong> 
                        </li>
                        <li class="list-group-item d-flex justify-content-between"> 
                            <span><?= __( 'admin.admin_cancel' ) ?></span> 
                            <strong>
                                <span class="ajax-wallet_cancel_count"><?= (int)$admin_totals['wallet_cancel_count'] ?></span>
                                / 
                                <span class="ajax-wallet_cancel_amount"><?= $fun_c_format($admin_totals['wallet_cancel_amount']) ?></span>
                            </strong> 
                        </li>
                        <li class="list-group-item d-flex justify-content-between"> 
                            <span><?= __( 'admin.trash' ) ?></span> 
                            <strong>
                                <span class="ajax-wallet_trash_count"><?= (int)$admin_totals['wallet_trash_count'] ?></span>
                                / 
                                <span class="ajax-wallet_trash_amount"><?= $fun_c_format($admin_totals['wallet_trash_amount']) ?></span>
                            </strong> 
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!--admin statistics cube-->

        <!--vendor statistics cube-->
        <div class="col-lg-3 mb-3"> 
            <div class="card h-100">
                <div class="card-body">
                    <div class="user-header d-flex align-items-center justify-content-center mb-3">
                        <div class="text-center pe-3">
                            <i class="bi bi-person text-white fs-3 bg-primary p-1 rounded-circle"></i>
                        </div>
                        <div class="text-center">
                            <h2 class="card-title"><?= __( 'admin.admin_vendor_order_statistics' ) ?></h2>
                        </div>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between"> 
                            <span><?= __( 'admin.admin_paid' ) ?></span> 
                            <strong>
                                <span class="ajax-vendor_wallet_accept_count"><?= (int)$admin_totals['vendor_wallet_accept_count'] ?></span>
                                / 
                                <span class="ajax-vendor_wallet_accept_amount"><?= $fun_c_format($admin_totals['vendor_wallet_accept_amount']) ?></span>
                            </strong> 
                        </li>
                        <li class="list-group-item d-flex justify-content-between"> 
                            <span><?= __( 'admin.admin_request' ) ?></span> 
                            <strong>
                                <span class="ajax-vendor_wallet_request_sent_count"><?= (int)$admin_totals['vendor_wallet_request_sent_count'] ?></span>
                                / 
                                <span class="ajax-vendor_wallet_request_sent_amount"><?= $fun_c_format($admin_totals['vendor_wallet_request_sent_amount']) ?></span>
                            </strong> 
                        </li>
                        <li class="list-group-item d-flex justify-content-between"> 
                            <span><?= __( 'admin.admin_unpaid' ) ?></span> 
                            <strong>
                                <span class="ajax-vendor_wallet_unpaid_count"><?= (int)$admin_totals['vendor_wallet_unpaid_count'] ?></span>
                                / 
                                <span class="ajax-vendor_wallet_unpaid_amount"><?= $fun_c_format($admin_totals['vendor_wallet_unpaid_amount']) ?></span>
                            </strong> 
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong><?= __('admin.admin_total_orders') ?></strong>
                            <strong>
                                <span class="ajax-order_vendor_total"><?= (int)$admin_totals['order_vendor_total'] ?></span>/
                                <span class="ajax-order_vendor_total"><?= (int)$admin_totals['order_vendor_total'] ?></span>
                            </strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!--vendor statistics cube-->
    </div>
</div>
<!--row 8-->

<div id="wallet-details-model" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="walletDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="walletDetailsModalLabel"><?= __('admin.order_details') ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= __('admin.close') ?></button>
      </div>
    </div>
  </div>
</div>

<?= $social_share_modal ?>
<?php
$last_id_integration_logs = 0;
$last_id_integration_orders = 0;
$last_id_store_orders = 0;
$last_id_newuser = 0;
$last_id_notifications = 0;
foreach ($integration_logs as $key => $log){
    if($last_id_integration_logs <= $log['id']){ $last_id_integration_logs = $log['id']; }
}
foreach ($integration_orders as $key => $order) {
    if($last_id_integration_orders <= $order['id']){ $last_id_integration_orders = $order['id']; }
}
foreach ($store_orders as $key => $order) {
    if($last_id_store_orders <= $order['id']){ $last_id_store_orders = $order['id']; }
}
foreach ($newuser as $users) {
    if($last_id_newuser <= $users['id']){ $last_id_newuser = $users['id']; }
}
foreach ($notifications as $key => $notification) {
    if($last_id_notifications <= $notification['notification_id']){ $last_id_notifications = $notification['notification_id']; }
}

?>


<script type="text/javascript" src="<?= base_url("assets/plugins/table/datatables.min.js") ?>"></script>
<script type="text/javascript" src="<?= base_url("assets/plugins/table/dataTables.responsive.min.js") ?>"></script>

<script type="text/javascript">

        var DTexternal_site_order =  $("#external-site-order").dataTable({
           lengthMenu: [
           [5,10, 25, 50, -1],
           [5,10, 25, 50, 'All'],
           ],
       })
        var ajax_interval = 2000;
        <?php  if((float)$live_dashboard['admin_data_load_interval'] >= 2){ ?>
            ajax_interval  = <?= (float)$live_dashboard['admin_data_load_interval'] * 1000 ?>;
        <?php } ?>

        var dashboard_xhr;
        var last_id_integration_logs = <?= (int)$last_id_integration_logs ?>;
        var last_id_integration_orders = <?= (int)$last_id_integration_orders ?>;
        var last_id_store_orders = <?= (int)$last_id_store_orders ?>;
        var last_id_newuser = <?= (int)$last_id_newuser ?>;
        var last_id_notifications = <?= (int)$last_id_notifications ?>;
        var total_commision_filter_year = '<?= date('Y') ?>';
        var total_commision_filter_month = '<?= date('m') ?>';
        var settings_clear = false;
        var homepage_integration_data = JSON.parse('<?= json_encode($integration_data['array']); ?>');
        var integration_data_per_page = <?= $integration_data_per_page ?>;

        function playSound(notification_sound){
            var audio = '<?= base_url('/assets/notify/') ?>'+notification_sound;
            $("body").append('<iframe id="noti-sound-iframe" src="'+audio+'"></iframe>')
            $("#noti-sound-iframe").on('load',function(){
                setTimeout(function(){
                    $("#noti-sound-iframe").remove();
                },1000)
            });
        }


  function setTimeout2(callnexttime, show_popup) {
    $("<div />").css("height", "0px").animate({ height: '90px' }, {
      duration: ajax_interval,
      step: function (now) {
        $(".progress").css('width', now + "%");
      },
      complete: function () {
        getDashboard(callnexttime, show_popup);
        getLatestOrders(1);
        $(".progress .progress-bar").css('width', '0%'); // Reset to 0%
      }
    });
  }

        var checkdata = {
            '.ajax-admin_balance'                     : 'admin_balance',
            '.ajax-sale_total_admin_store'            : 'sale_total_admin_store',
            '.ajax-sale_localstore_vendor_total'      : 'sale_localstore_vendor_total',
            '.ajax-click_action_total'                : 'click_action_total',
            '.ajax-click_action_commission'           : 'click_action_commission',
            '.ajax-all_click_total'                   : 'all_click_total',
            '.ajax-all_click_commission'              : 'all_click_commission',
            '.ajax-click_localstore_total'            : 'click_localstore_total',
            '.ajax-click_localstore_commission'       : 'click_localstore_commission',
            '.ajax-click_integration_total'           : 'click_integration_total',
            '.ajax-click_integration_commission'      : 'click_integration_commission',
            '.ajax-click_form_total'                  : 'click_form_total',
            '.ajax-click_form_commission'             : 'click_form_commission',
            '.ajax-click_all_total'                   : 'click_all_total',
            '.ajax-click_all_commission'              : 'click_all_commission',
            '.ajax-sale_localstore_count'             : 'sale_localstore_count',
            '.ajax-sale_localstore_commission'        : 'sale_localstore_commission',
            '.ajax-sale_localstore_vendor_count'      : 'sale_localstore_vendor_count',
            '.ajax-sale_localstore_vendor_commission' : 'sale_localstore_vendor_commission',
            '.ajax-order_external_count'              : 'order_external_count',
            '.ajax-order_external_commission'         : 'order_external_commission',
            '.ajax-all_sale_count'                    : 'all_sale_count',
            '.ajax-all_sale_commission'               : 'all_sale_commission',
            '.ajax-wallet_unpaid_amounton_hold_count' : 'wallet_unpaid_amounton_hold_count',
            '.ajax-wallet_on_hold_amount'             : 'wallet_on_hold_amount',
            '.ajax-wallet_unpaid_count'               : 'wallet_unpaid_count',
            '.ajax-wallet_unpaid_amount'              : 'wallet_unpaid_amount',
            '.ajax-wallet_request_sent_count'         : 'wallet_request_sent_count',
            '.ajax-wallet_request_sent_amount'        : 'wallet_request_sent_amount',
            '.ajax-wallet_accept_count'               : 'wallet_accept_count',
            '.ajax-wallet_accept_amount'              : 'wallet_accept_amount',
            '.ajax-wallet_cancel_count'               : 'wallet_cancel_count',
            '.ajax-wallet_cancel_amount'              : 'wallet_cancel_amount',
            '.ajax-wallet_trash_count'                : 'wallet_trash_count',
            '.ajax-wallet_trash_amount'               : 'wallet_trash_amount',
            '.ajax-vendor_wallet_accept_count'        : 'vendor_wallet_accept_count',
            '.ajax-vendor_wallet_accept_amount'       : 'vendor_wallet_accept_amount',
            '.ajax-vendor_wallet_request_sent_count'  : 'vendor_wallet_request_sent_count',
            '.ajax-vendor_wallet_request_sent_amount' : 'vendor_wallet_request_sent_amount',
            '.ajax-vendor_wallet_unpaid_count'        : 'vendor_wallet_unpaid_count',
            '.ajax-vendor_wallet_unpaid_amount'       : 'vendor_wallet_unpaid_amount',
            '.ajax-order_vendor_total'                : 'order_vendor_total',
            '.ajax-admin_balance_growth'              : 'admin_balance_growth',
            '.ajax-click_action_commission_growth'    : 'click_action_commission_growth',
            '.ajax-all_clicks_comission_growth'       : 'all_clicks_comission_growth',
            '.ajax-admin_all_sales_growth'            : 'admin_all_sales_growth',
            '.ajax-vendor_all_sales_growth'           : 'vendor_all_sales_growth',
            '.ajax-time'                              :  'time',
        }

        function setColors() {
            $.each(checkdata,function(ele,Key){
                if($(ele).length){
                    var val =  parseInt($(ele).html().toString().replace(/[^0-9-.]/g, '') || 0);

                    $(ele).removeClass("text-primary")
                    $(ele).removeClass("text-danger")
                    if(val >= 0){
                        $(ele).addClass("text-primary");
                    } else{
                        $(ele).addClass("text-danger");
                    }
                }
            })
        }

    function getDashboard(callnexttime,show_popup,actions){
        if(dashboard_xhr && dashboard_xhr.readyState != 4) dashboard_xhr.abort();

        if(actions == 'clearlog'){
            settings_clear = true;
            last_id_integration_logs = 0;
            last_id_integration_orders = 0;
            last_id_store_orders = 0;
            last_id_newuser = 0;
            last_id_notifications = 0;
        }

        dashboard_xhr = $.ajax({
            url:'<?= base_url('admincontrol/ajax_dashboard') ?>',
            type:'POST',
            dataType:'json',
            data:{
                renderChart  : $(".renderChart").val(),
                selectedyear :$('.yearSelection').val(),
                last_id_integration_logs :last_id_integration_logs,
                last_id_integration_orders :last_id_integration_orders,
                last_id_store_orders :last_id_store_orders,
                last_id_newuser :last_id_newuser,
                last_id_notifications :last_id_notifications,
                last_id_top_notifications :$("#last_id_notifications").val(),
                total_commision_filter_year : $('select[name="filter_commission[year]"]').val(),
                total_commision_filter_month : $('select[name="filter_commission[month]"]').val(),
                integration_data_year : $('select[name="filter_integration[year]"]').val(),
                integration_data_month : $('select[name="filter_integration[month]"]').val(),
                integration_data_selected : $("#integration-chart-type").val(),
            },
            beforeSend:function(){},
            complete:function(){
                if(callnexttime){
                    setTimeout2(true,true);
                }
            },
            success:function(json){
                setTimeout(function(){
                    $('.ajax-live_window .fa-bell').removeClass('blink-icon');
                    $(".mini-stat-icon i").removeClass("blink-icon");
                }, 5000);

                var play_sound = false;
                var sound_on = false;

                $.each(checkdata,function(ele,Key){
                    if($.trim($(ele).html()) != json['admin_totals'][Key]){
                        play_sound = true;
                        $(ele).html(json['admin_totals'][Key]);
                    }
                })

                if(json['online_count']){
                    if (typeof json['online_count']['admin'] == 'object' && json['online_count']['admin']['online'] ) {
                        $(".ajax-online-admin").html( json['online_count']['admin']['online']);
                    }
                    if (typeof json['online_count']['user'] == 'object' && json['online_count']['user']['online'] ) {
                        $(".ajax-online-affiliate").html(json['online_count']['user']['online']);
                    }
                    if (typeof json['online_count']['vendor'] == 'object' && json['online_count']['vendor']['online'] ) {
                        $(".ajax-online-vendor").html(json['online_count']['vendor']['online']);
                    }
                    if (typeof json['online_count']['client'] == 'object' && json['online_count']['client']['online'] ) {
                        $(".ajax-online-client").html(json['online_count']['client']['online']);
                    }
                }

                $(".ajax-weekly_balance").html(json['admin_totals_week']);
                $(".ajax-monthly_balance").html(json['admin_totals_month']);
                $(".ajax-yearly_balance").html(json['admin_totals_year']);
                
                if (json['time']) {
                    $(".ajax-time").html(json['time']);
                }

                if(json['chart']){
                    $("#dashboard-chart-empty").addClass('d-none');
                    $("#dashboard-chart").removeClass('d-none');
                    
                    renderDashboardChart(json['chart']);
                } else {
                    $("#dashboard-chart-empty").removeClass('d-none');
                    $("#dashboard-chart").addClass('d-none');
                }
                
                load_userworldmap(json['userworldmap']);

                homepage_integration_data = json['integration_data']['array'];
                let homepage_integration_pagination_template = createIntegrationPaginationTemplate(1);
                $(".dashboard-div .pagination-div ul").html(homepage_integration_pagination_template);
                
                let homepage_integration_data_template = createIntegrationDataTemplate(1);
                $("#external-site-order tbody").html(homepage_integration_data_template);
                $("#external-site-order").dataTable().fnDestroy();
                $("#external-site-order").dataTable({
                   lengthMenu: [
                   [5,10, 25, 50, -1],
                   [5,10, 25, 50, 'All'],
                   ],
               })
                $('.popover.bs-popover-top').remove();
                $('[data-toggle="popover"]').popover();

                if($.trim($(".ajax-notifications_count").html()) != json['notifications_count']){
                    play_sound = true;
                }
                $(".ajax-notifications_count").html(parseInt(json['notifications_count']) > 99 ? "99+" : json['notifications_count']);
                if(parseInt(json['notifications_count']) > 99) {

                    $(".bell");
                    $(".notifications-count");
                } else {
                    $(".bell");
                    $(".notifications-count");
                }

                if(json['ajax_newuser']){
                    $.each(json['ajax_newuser'], function(i,j){
                        last_id_newuser = last_id_newuser <= parseInt(j['id']) ? parseInt(j['id']) : last_id_newuser;
                        if(show_popup && json['live_dashboard']['admin_affiliate_register_status']){
                            sound_on = true;
                            show_tost("success",'<?= __('admin.new_affiliate_register') ?>','<?= __('admin.new_affiliate') ?>'+" "+ j['firstname'] +" "+ j['lastname'] +'<?= __('admin.register_just_now') ?>');
                        }
                    })
                }

var count = 0;
if (json['live_window']) {
    var notifications = '';
    var play_sound = false;

    count = 0;  

    $.each(json['live_window'], function(i, j) {
        play_sound = true;
        count++;
        notifications += j['title'];
    });

    if (notifications) {
        $('.btn-count-notification .count-notifications').text(count);
        $(".ajax-live_window").html(notifications);

        $(".live-wrap-empty-data").hide();
        $(".ajax-live_window").show();
    } else {
        $(".live-wrap-empty-data").show();
        $(".ajax-live_window").hide();
    }
}

                if(json['ajax_integration_logs']){
                    $.each(json['ajax_integration_logs'], function(i,j){
                        last_id_integration_logs = last_id_integration_logs <= parseInt(j['id']) ? parseInt(j['id']) : last_id_integration_logs;
                        if(j['click_type'] == 'Action'){
                            if(show_popup && json['live_dashboard']['admin_action_status']){
                                sound_on = true;
                                show_tost("success",'<?= __('admin.new_action') ?>','<?= __('admin.new_action_click_done_just_now') ?>');
                            }
                        }
                    })
                }

                if(json['ajax_integration_orders']){
                    $.each(json['ajax_integration_orders'], function(i,j){
                        last_id_integration_orders = last_id_integration_orders <= parseInt(j['id']) ? parseInt(j['id']) : last_id_integration_orders;
                        if(show_popup && json['live_dashboard']['admin_integration_order_status']){
                            sound_on = true;
                            show_tost("success",'<?= __('admin.new_integration_order') ?>','<?= __('admin.new_integration_order_place_just_now') ?>');
                        }
                    })
                }

                var top_notifications = '';
                if(json['notifications']){
                    $.each(json['notifications'], function(i,j){
                        top_notifications += '<a href="javascript:void(0)" onclick="shownofication('+ j['notification_id'] +',\'<?= base_url('admincontrol') ?>'+ j['notification_url'] + '\')" class="dropdown-item notify-item">\
                        <div class="notify-icon bg-primary"><i class="mdi mdi-cart-outline"></i></div>\
                        <p class="notify-details"><b>'+ j['notification_title'] +'</b><small class="text-muted">'+ j['notification_description'] +'</small></p>\
                        </a>';
                    })
                }
                
                if(json['last_id_notifications']){
                    $.each(json['last_id_notifications'], function(i,j){
                        if(j['notification_type'] == 'order'){
                            if(show_popup && json['live_dashboard']['admin_local_store_order_status']){
                                sound_on = true;
                                show_tost("success",'<?= __('admin.new_local_store_order') ?>', j['notification_title'] + '<?= __('admin.just_now') ?>');
                            }
                        }

                        last_id_notifications = last_id_notifications <= parseInt(j['notification_id']) ? parseInt(j['notification_id']) : last_id_notifications;
                    })
                }

                $("#last_id_top_notifications").val(last_id_notifications);
                $(".ajax-notifications_count").html(json['notifications'].length);
                $(".ajax-top_notifications_count").html(json['notifications'].length);
                $('#allnotification').html(top_notifications);

                if(play_sound && json['sound_status'] == "1" && show_popup && sound_on){
                    playSound(json['notification_sound']);
                }
            },
        })
}

$(function() {
    $(".progress").on('each',function() {
        var value = $(this).attr('data-value');
        var left = $(this).find('.progress-left .progress-bar');
        var right = $(this).find('.progress-right .progress-bar');
        if (value > 0) {
            if (value <= 50) {
                right.css('transform', 'rotate(' + percentageToDegrees(value) + 'deg)')
            } else {
                right.css('transform', 'rotate(180deg)')
                left.css('transform', 'rotate(180deg)')
            }
        }
    })
    function percentageToDegrees(percentage) {
        return percentage / 100 * 360
    }
});

setTimeout2(true,true);

$(document).on('click','.dashboard-div .pagination-div ul li a',function(e){
    e.preventDefault();

    let page = $(this).data('page');
    $('.dashboard-div .pagination-div ul li.prev a').attr('data-page',page-1);
    $('.dashboard-div .pagination-div ul li.next a').attr('data-page',page+1);

    let homepage_integration_pagination_template = createIntegrationPaginationTemplate(page);
    $("#order-listing_paginate > ul").html(homepage_integration_pagination_template);

    let homepage_integration_data_template = createIntegrationDataTemplate(page);
    $(".dashboard-div #external-site-order tbody").html(homepage_integration_data_template);

    $('.popover.bs-popover-top').remove();
    $('[data-toggle="popover"]').popover();
})  

function createIntegrationPaginationTemplate(page){
    let template = '';
    let count = homepage_integration_data.length;
    let page_count = Math.ceil(count/integration_data_per_page);

    let diff = page_count - page;
    let i = 1;

    if(diff < 3)
        i = page + diff - 3;
    else 
        i = page;

    if(page > 2 && ((page + 2) < page_count))
        i--;

    if(i < 1)
        i = 1;

    if(page != 1)
        template += '<li class="prev"><a href="javascript:void(0)" data-page="' + (page - 1) +'"><i class="lni lni-chevron-left"></i></a></li>';

    let counter = 1;
    for(i; i < page_count+1; i++){
        if(counter < 5){
            let activeClass = (i == page) ? 'class="active"' : '';
            template += '<li ' + activeClass + '><a href="javascript:void(0)" data-page="' + i +'">' + i + '</a></li>';
        }
        counter++;
    }

    if(page != page_count && diff > 2)
        template += '<li class="next"><a href="javascript:void(0)" data-page="' + (page + 1) +'"><i class="lni lni-chevron-right"></i></a></li>';

    return template;
}

function createIntegrationDataTemplate(page){
    let template = '';
    let offset = (page - 1) * integration_data_per_page;
    for(let i = 0; i < homepage_integration_data.length; i++){
        if(homepage_integration_data[i]){
            template += '<tr>';
            template += '<td class="no-wrap" data-container="body" data-toggle="popover" data-trigger="hover"data-placement="top" data-content="'+homepage_integration_data[i].website+'" copyToClipboard="'+homepage_integration_data[i].website+'">'+stringLimiter(homepage_integration_data[i].website,20)+'</td>';
            template += '<td class="no-wrap">'+homepage_integration_data[i].balance+'</td>';
            template += '<td class="no-wrap">'+homepage_integration_data[i].total_count_sale+'</td>';
            template += '<td class="no-wrap">'+homepage_integration_data[i].click_count_amount+'</td>';
            template += '<td class="no-wrap">'+homepage_integration_data[i].action_count_amount+'</td>';
            template += '<td class="no-wrap">'+homepage_integration_data[i].total_commission+'</td>';
            template += '<td class="no-wrap">'+homepage_integration_data[i].total_orders+'</td>';
            template += '</tr>';
        }
    }

    return template;
}

function stringLimiter(text,length){
    if(text.length <= length){
        return text;
    } else {
        text = text.substr(text,length) + '...';
        return text;
    }
}

$(".btn_setting").on('click',function(){
    $this = $(this);

    $.ajax({
        url:'<?= base_url('setting/getModal') ?>',
        type:'POST',
        dataType:'json',
        data:{
            'key' : $this.attr('data-key'),
            'type' : $this.attr('data-type'),
        },
            success:function(json){
                if(json['html']){
                    $("#setting-widzard").html(json['html']);
                    $("#setting-widzard").modal('show');
                }
            },
        })
})

function getodolistonly()
{
    var data ={
        action:'todolist'
    }
    $.ajax({
        url:'<?= base_url("todo/getodolistonly") ?>',
        type:'POST',
        dataType:'json',
        data:data,
        beforeSend:function(){$this.btn("loading");},
        complete:function(){$this.btn("reset");},
        success:function(json){
            $(".todolist-table-new tbody").html(json['html']);
            $(".card-footer").hide();
             
        },
    })
}  

function getLatestOrders(page, t) {
    $this = $(t);
    var data = {
        page: page,
        filter_status: $(".filter_status").val(),
        action: 'dashboard'
    };

    $.ajax({
        url: '<?= base_url("admincontrol/get_latest_dashboard_orders") ?>/' + page,
        type: 'POST',
        dataType: 'json',
        data: data,
        beforeSend: function() { $this.btn("loading"); },
        complete: function() { $this.btn("reset"); },
        success: function(json) {
            $(".orders-table-new tbody").html(json['html']);
            $(".card-footer").hide();

            // Handle pagination
            $('#pagination').html("");
            const totalPages = json['total_pages'];
            const maxPagesToShow = 5;

            let startPage = Math.max(page - Math.floor(maxPagesToShow / 2), 1);
            let endPage = Math.min(startPage + maxPagesToShow - 1, totalPages);

            if (endPage - startPage < maxPagesToShow - 1) {
                startPage = Math.max(endPage - maxPagesToShow + 1, 1);
            }

            // First button
            if (startPage > 1) {
                $('#pagination').append(
                    `<li class="page-item">
                        <a class="page-link" href="#" onclick="getLatestOrders(1, this); return false;">First</a>
                    </li>`
                );
            }

            // Page numbers
            for (let i = startPage; i <= endPage; i++) {
                const activeClass = i === page ? 'active' : '';
                $('#pagination').append(
                    `<li class="page-item ${activeClass}">
                        <a class="page-link" href="#" onclick="getLatestOrders(${i}, this); return false;">${i}</a>
                    </li>`
                );
            }

            // Last button
            if (endPage < totalPages) {
                $('#pagination').append(
                    `<li class="page-item">
                        <a class="page-link" href="#" onclick="getLatestOrders(${totalPages}, this); return false;">Last</a>
                    </li>`
                );
            }
        },
    });
}



$(document).ready(function() {
    getLatestOrders(1, null);
    getodolistonly();
});

$(document).ready(function() {
        var calendar;
        function initCalender() {
          calendar = $('#calendar').fullCalendar({
            themeSystem: 'bootstrap4',
            defaultView: 'month',
            editable: false,
            disableDragging:true,
            header: {
                left: 'today',
                center: 'title ',
                right: ' prev,next,month'
            },
            buttonText : {
                prev : 'Prev',
                next : 'Next',
                month : 'Month',
                today : 'Today',
            },
            events:'<?=base_url()?>'+"todo/getodolist?isCalView=1",
            eventRender: function(event, element) {
                if(event.is_done=="1"){
                    element.find('.fc-title').addClass('isTodaCompleted').attr('title','Click to view/update');
                }
                var isTodoDone = event.is_done=="1" ? 'checked':'';
                element.find(".fc-title").prepend("<input type='checkbox' data-id='"+event.id+"' class='completedTodoCalView mr-3' "+isTodoDone+">");
                element.find(".fc-title").append("<div class='float-right'><a class='removetodolisCalView' data-id='"+event.id+"' ><i class=' fa fa-trash'></i></a></div>")
            },
            dayClick: function(events) {

                var check = moment(events._d).format('YYYY-MM-DD');
                var today = moment(new Date()).format('YYYY-MM-DD');
                if(check < today)
                {
                    return showPrintMessage("<?= __('admin.you_cant_select_past_dates')  ?>", 'error');
                }
                
                $("#tododateCal").val(check)
                $("#todoListItemid").val(0);
                $('#btnAddCalnote').text('Add');
                $('#modal-add-todo').modal('show');
            },
            eventClick: function(event, jsEvent, view) {

                var markascomplete=$(jsEvent.target).hasClass('completedTodoCalView');
                var deletetask=$(jsEvent.target).hasClass('fa-trash');
                
                if(markascomplete == false && deletetask == false){
                    
                    $('#todonotesCal').val(event.notes)

                    $("#todoListItemid").val(event.id);
                    $("#tododateCal").val( moment(event.start).format('YYYY-MM-DD'));
                    $('#modal-add-todo').modal('show');
                    $('#btnAddCalnote').text('Update');
                }

            },
        });  
        }
        
         calendar = $('#calendar').fullCalendar({
            themeSystem: 'bootstrap4',
            defaultView: 'month',
            editable: false,
            disableDragging:true,
            header: {
                left: 'today',
                center: 'title ',
                right: ' prev,next,month'
            },
            buttonText : {
                prev : 'Prev',
                next : 'Next',
                month : 'Month',
                today : 'Today',
            },
            events:'<?=base_url()?>'+"todo/getodolist?isCalView=1",
            eventRender: function(event, element) {
                if(event.is_done=="1"){
                    element.find('.fc-title').addClass('isTodaCompleted').attr('title','Click to view/update');
                }
                var isTodoDone = event.is_done=="1" ? 'checked':'';
                element.find(".fc-title").prepend("<input type='checkbox' data-id='"+event.id+"' class='completedTodoCalView mr-3' "+isTodoDone+">");
                element.find(".fc-title").append("<div class='float-right'><a class='removetodolisCalView' data-id='"+event.id+"' ><i class=' fa fa-trash'></i></a></div>")
            },
            dayClick: function(events) {

                var check = moment(events._d).format('YYYY-MM-DD');
                var today = moment(new Date()).format('YYYY-MM-DD');
                if(check < today)
                {
                    return showPrintMessage("<?= __('admin.you_cant_select_past_dates')  ?>", 'error');
                }
                
                $("#tododateCal").val(check);
                $("#todonotesCal").val('');
                $("#todoListItemid").val(0);
                $('#btnAddCalnote').text('Add');
                $('#modal-add-todo').modal('show');
            },
            eventClick: function(event, jsEvent, view) {
                var markascomplete=$(jsEvent.target).hasClass('completedTodoCalView');
                var deletetask=$(jsEvent.target).hasClass('fa-trash');
                
                if(markascomplete == false && deletetask == false){
                    $('#todonotesCal').val(event.notes)

                    $("#todoListItemid").val(event.id);
                    $("#tododateCal").val( moment(event.start).format('YYYY-MM-DD'));
                    $('#modal-add-todo').modal('show');
                    $('#btnAddCalnote').text('Update');
                }

            },
        });
        $(document).on('change','#popular_affiliates_sorting',function(){
            
            var value = $(this).val();
            var type="popular_affiliates_sorting";
            $.ajax({
                url:'<?= base_url('admincontrol/popular_affiliates_sorting') ?>',
                type:'POST',
                dataType:'json',
                data:{value:value,type:type},
                async:false,
                success:function(json){
                     $(".popular_affiliates_table tbody").html(json['view']);
                },
            });
        });
        $(document).on('click','.completedTodoCalView',function(){
            var id = $(this).data('id');
            var is_completed = 0;
            if ($(this).attr('checked')) {
                $(this).removeAttr('checked');
                is_completed=0;
            } else {
                $(this).attr('checked', 'checked');
                is_completed=1;
                $(this).parent().addClass('isTodaCompleted')
            }
            var id = $(this).data('id');
            var $that = $(this);
            $.ajax({
                url:'<?= base_url('todo/actiontodolist') ?>',
                type:'POST',
                dataType:'json',
                data:{id:id,action:2,is_completed:is_completed},
                async:false,
                success:function(data){
                    if(data.status) {
                        gettodoList();
                        calendar.fullCalendar('destroy');
                        initCalender();
                        showPrintMessage(data.message, 'success');
                    }
                    else{
                        showPrintMessage(data.message, 'error');
                    }
                },
            });
        });
        $(document).on('click', '.removetodolisCalView', function() {
            if(confirm('<?= __('admin.are_you_sure')?>')){
                var id = $(this).data('id');
                var $that = $(this);
                $.ajax({
                    url:'<?= base_url('todo/actiontodolist') ?>',
                    type:'POST',
                    dataType:'json',
                    data:{id:id,action:1},
                    async:false,
                    success:function(data){
                        if(data.status) {
                            gettodoList();
                            calendar.fullCalendar('destroy');
                            initCalender();
                            showPrintMessage(data.message, 'success');
                        }
                        else{
                            showPrintMessage(data.message, 'error');
                        }
                    },
                });
            }
        });
        $("#btnAddCalnote").click(function(){
            var todo_date = $("#tododateCal").val();
            var todonotesCal = $("#todonotesCal").val();
            var id = $("#todoListItemid").val();

            if (todonotesCal && todo_date) {
                $.ajax({
                    url:'<?= base_url('todo/addtodolist') ?>',
                    type:'POST',
                    dataType:'json',
                    async:false,
                    data: { note :todonotesCal,id:id,todo_date:todo_date},
                    success:function(data){
                        if(data.status){
                            $('#btnAddCalnote').text('Add');
                            $('#modal-add-todo').modal('hide');
                            $('#calendar').fullCalendar('prev');
                            $('#calendar').fullCalendar('next');
                            gettodoList();
                            showPrintMessage(data.message, 'success');
                        }
                        else{
                            showPrintMessage(data.message, 'error');
                        }
                    },
                });
            }
        })
    });
</script>


<!--system settings popup-->
<div class="modal fade" id="missingDetailsModal" tabindex="-1" aria-labelledby="missingDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="modalTitle" class="modal-title"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <!-- First Row -->
          <div class="row g-4 mb-5">
            <!-- Section 1: General Settings -->
            <div class="col-lg-4">
                <div class="list-group mb-4">
                    <div class="list-group-item bg-primary text-white fs-6"> <?= __('admin.general_important_settings') ?> </div>
                    <ul id="generalSettingsList1" class="list-group mb-0"></ul>
                    <a href="<?= base_url('/firstsetting') ?>" class="list-group-item list-group-item-action text-center bg-secondary text-white"> <?= __('admin.go_to_settings') ?> </a>
                </div>
            </div>
            <!-- Section 2: Mail Settings -->
            <div class="col-lg-4">
                <div class="list-group mb-4">
                    <div class="list-group-item bg-primary text-white fs-6"><?= __('admin.store_important_settings') ?></div>
                    <ul id="generalSettingsList2" class="list-group mb-0"></ul>
                    <a href="<?= base_url('/admincontrol/store_setting') ?>" class="list-group-item list-group-item-action text-center bg-secondary text-white"><?= __('admin.go_to_settings') ?></a>
                </div>
            </div>
            <!-- Section 3: Wallet Settings -->
            <div class="col-lg-4">
                <div class="list-group mb-4">
                    <div class="list-group-item bg-primary text-white fs-6"><?= __('admin.wallet_important_settings') ?></div>
                    <ul id="generalSettingsList3" class="list-group mb-0"></ul>
                    <a href="<?= base_url('/admincontrol/wallet_setting') ?>" class="list-group-item list-group-item-action text-center bg-secondary text-white"><?= __('admin.go_to_settings') ?></a>
                </div>
            </div>
          </div>
          <!-- Second Row -->
          <!-- <div class="row g-4"> -->
            <!-- Section 4: Store Settings -->
            <!-- <div class="col-lg-4">
              <h6 class="text-muted"><?= __('admin.store_setting') ?></h6>
              <ul id="generalSettingsList4" class="list-group mb-4"></ul>
              <a href="<?= base_url('/admincontrol/store_setting') ?>" class="btn btn-info btn-sm"><?= __('admin.store_settings') ?></a>
            </div> -->
            <!-- Section 5: Advanced Settings -->
            <!-- <div class="col-lg-4">
              <h6 class="text-muted"><?= __('admin.advanced_important_settings') ?></h6>
              <ul id="generalSettingsList5" class="list-group mb-4"></ul>
            </div> -->
            <!-- Section 6: Security Settings -->
            <!-- <div class="col-lg-4">
              <h6 class="text-muted"><?= __('admin.security_important_settings') ?></h6>
              <ul id="generalSettingsList6" class="list-group mb-4"></ul>
            </div> -->
          <!-- </div> -->
        </div>
      </div>
        <div class="modal-footer d-flex justify-content-center align-items-center bg-light">
          <div class="badge bg-danger text-white fs-5">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            <span class="modal-title fw-bold"><?= __('admin.system_main_settings_go') ?></span>
          </div>
        </div>
    </div>
  </div>
</div>
<!--system settings popup-->



<!--system settings script-->
<script>

  var showModal = false;

  function populateSettings(settingsArray, elementId) {
      var listElement = document.getElementById(elementId);
      listElement.innerHTML = ''; // Clear previous items
      var settingsValues = <?php echo json_encode($missing); ?>;

      settingsArray.forEach(function(settingObj) {
          var value = settingsValues[settingObj.group]?.[settingObj.key] ?? 'Not Set';

          // Hide statement code
          var isPhpMailer = settingsValues.email?.mail_type === 'PHP Mailer';
          if (isPhpMailer && ['smtp_hostname', 'smtp_username', 'smtp_password', 'smtp_port', 'smtp_crypto'].includes(settingObj.key)) {
              return;
          }

          var textColorClass = value === 'Off' ? 'text-danger' : value === 'Not Set' ? 'badge bg-danger text-white' : 'text-success';
          var listItem = `<li class="list-group-item">${settingObj.label}: <span class="${textColorClass}">${value}</span></li>`;
          listElement.innerHTML += listItem;

          if (value === 'Not Set') {
              showModal = true;
          }
      });
  }

    function handleModal() {
      var modalTitle = document.getElementById('modalTitle');
      var notificationDiv = document.getElementById('notificationDiv'); // assuming you have a div with id 'notificationDiv' to hold the notification

      var modalText = showModal ? '<i class="bi-exclamation-triangle-fill text-danger"></i> Important Data Missing' : '<i class="bi-exclamation-triangle-fill text-success"></i> All Required Data Filled';
      modalTitle.innerHTML = modalText;

      if (showModal) {
        var notificationHtml = '<div class="bg-danger text-white p-3 rounded">';
        notificationHtml += '<i class="bi-exclamation-triangle-fill"></i> Important data is missing. <a href="#" id="reopenModalLink" style="color:white;">Click to view details</a>.';
        notificationHtml += '</div>';

        notificationDiv.innerHTML = notificationHtml;

        document.getElementById('reopenModalLink').addEventListener('click', function(event) {
          event.preventDefault();
          new bootstrap.Modal(document.getElementById('missingDetailsModal')).show();
        });
      } else {
        notificationDiv.innerHTML = '';
      }
    }


  // General settings
  var section1Settings = [
      {group: "site", key: "time_zone", label: "Time Zone"},
      {group: "login", key: "front_template", label: "front_template"},
      {group: "email", key: "mail_type", label: "<?= __('admin.mail_mode') ?>"},
      {group: "site", key: "notify_email", label: "<?= __('admin.notify_email') ?>"},
      {group: "email", key: "from_email", label: "<?= __('admin.from_email') ?>"},
      {group: "email", key: "smtp_hostname", label: "<?= __('admin.smtp_hostname') ?>"},
      {group: "email", key: "smtp_username", label: "<?= __('admin.smtp_username') ?>"},
      {group: "email", key: "smtp_password", label: "<?= __('admin.smtp_password') ?>"},
      {group: "email", key: "smtp_port", label: "<?= __('admin.smtp_port') ?>"},
      {group: "email", key: "smtp_crypto", label: "<?= __('admin.smtp_crypto') ?>"},
  ];

  // Store settings
  var section2Settings = [
      {group: "store", key: "status", label: "<?= __('admin.store_status') ?>"},
      {group: "store", key: "theme", label: "<?= __('admin.store_mode') ?>"},
      {group: "productsetting", key: "product_commission_type", label: "<?= __('admin.store_commission_type') ?>"},
      {group: "productsetting", key: "product_commission", label: "<?= __('admin.store_product_cps') ?>"},
      {group: "productsetting", key: "product_ppc", label: "<?= __('admin.store_product_cpc') ?>"},
      {group: "productsetting", key: "product_noofpercommission", label: "<?= __('admin.store_product_clicks') ?>"},
  ];

  // Wallet settings
  var section3Settings = [
      {group: "site", key: "wallet_min_amount", label: "<?= __('admin.wallet_min_amount') ?>"},
      {group: "site", key: "wallet_max_amount", label: "<?= __('admin.wallet_max_amount') ?>"},
      // {group: "site", key: "wallet_min_message", label: "<?= __('admin.wallet_min_message') ?>"},
      {group: "site", key: "wallet_min_message_new", label: "<?= __('admin.wallet_min_message_new') ?>"},
      {group: "site", key: "wallet_auto_withdrawal", label: "<?= __('admin.wallet_auto_withdrawal') ?>"},
      {group: "site", key: "wallet_auto_withdrawal_days", label: "<?= __('admin.wallet_auto_withdrawal_days') ?>"},
      {group: "site", key: "wallet_auto_withdrawal_limit", label: "<?= __('admin.wallet_auto_withdrawal_limit') ?>"},
  ];

   // Store settings
  var section4Settings = [
      {group: "store", key: "status", label: "<?= __('admin.store_status') ?>"},
      {group: "store", key: "theme", label: "<?= __('admin.store_mode') ?>"},
  ];

  populateSettings(section1Settings, 'generalSettingsList1');
  populateSettings(section2Settings, 'generalSettingsList2');
  populateSettings(section3Settings, 'generalSettingsList3');

  handleModal();
</script>
<!--system settings script-->

<!--switch buttons code-->
<script>
    $(document).on('change', '.activity', function(){
      let setting_type = $(this).data('setting_type');
      let setting_key = $(this).data('setting_key');
      let val = $(this).prop('checked') ? 1 : 0;
      
      let menu =  $(this).data('sidebar');
      
      if(val) {
        $('#sidebar_'+menu).show();
        $(this).closest('.card').find('.card-header').addClass('bg-info');
      } else {
        $('#sidebar_'+menu).hide();
        $(this).closest('.card').find('.card-header').removeClass('bg-info');
      }

      $.ajax({
        type: "POST",
        data: {
          action: 'change_status', 
          setting_type: setting_type, 
          setting_key : setting_key,
          val : val
        },
        success: function(res){
            showPrintMessage("<?= __('admin.system_mode_changed_successfully') ?>", 'success');
        },
      });
    });
</script>
<!--switch buttons code-->

<script>
document.addEventListener('DOMContentLoaded', function() {

    // Function to open chart as PNG or JPEG in a new tab
    function openImage(type) {
        const imgURL = chart.toBase64Image(type);
        const win = window.open("", "_blank");
        win.document.body.innerHTML = `<img src="${imgURL}" alt="Chart Image"/>`;
    }

    // Function to open PDF in a new tab
    function openPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        doc.text('Sales Report', 10, 10);

        const chartImg = chart.toBase64Image('image/png');
        doc.addImage(chartImg, 'PNG', 10, 20, 180, 100);
        
        const tableData = getTableData();
        doc.autoTable({
            head: [['Month', 'Action Count', 'Order Count', 'Order Commission', 'Action Commission', 'Order Total']],
            body: tableData,
            startY: 130
        });

        // Open PDF in a new tab
        window.open(doc.output('bloburl'), '_blank');
    }

    // Function to download Excel file
    function downloadExcel() {
        const wb = XLSX.utils.book_new();
        wb.Props = {
            Title: "Dashboard Report",
            Subject: "Sales Report",
            Author: "Admin"
        };

        wb.SheetNames.push("Report");
        
        const data = [
            ["Month", "Action Count", "Order Count", "Order Commission", "Action Commission", "Order Total"],
            ...getTableData()
        ];

        const ws = XLSX.utils.aoa_to_sheet(data);
        wb.Sheets["Report"] = ws;

        // Download Excel file
        XLSX.writeFile(wb, "DashboardReport.xlsx");
    }

    // Function to get table data based on the chart data
    function getTableData() {
        const tableData = [];
        chart.data.labels.forEach((month, index) => {
            let rowData = [month];
            chart.data.datasets.forEach(dataset => {
                rowData.push(dataset.data[index] || 0);
            });
            tableData.push(rowData);
        });
        return tableData;
    }

    document.getElementById('downloadPng').addEventListener('click', function() {
        openImage('image/png');
    });
    document.getElementById('downloadJpeg').addEventListener('click', function() {
        openImage('image/jpeg');
    });
    document.getElementById('downloadPdf').addEventListener('click', openPDF);
    document.getElementById('downloadExcel').addEventListener('click', downloadExcel);
});
</script>