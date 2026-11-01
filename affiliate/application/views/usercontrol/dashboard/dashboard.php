<?php
$db =& get_instance();
$method=$this->CI->router->fetch_method();
$userdetails = $db->userdetails();
$products = $db->Product_model;
$notifications_count = $products->getnotificationnew_count('admin',null);
$payment_details=json_decode($plan->payment_details);
$loginUser = $_SESSION['user'];

$user_side_bar_clock_text_color = $products->getSettings('theme','user_side_bar_clock_text_color');
$PrimaryPaymentMethodStatus = $products->getUserPaymentMethodStatus($userdetails['id'],$userdetails['primary_payment_method']);
$paymentlist         = $products->getPaymentWarning();
  if(empty($payment_methods) && ($method != 'purchase_plan' && $method !='user_reports')){
    
    $payment_methods = $this->session->userdata('payment_methods'); 
  }
  $loginUser = $_SESSION['user'];
  if(isset($loginUser['is_vendor']) && $loginUser['is_vendor'] == 1) {
    $store_setting =$db->Product_model->getSettings('store');
    $vendor_setting = $db->Product_model->getSettings('vendor');
      $marketVendorStatus= $db->Product_model->getSettings('market_vendor', 'marketvendorstatus');
      $vendoerMinDeposit = $db->Product_model->getSettings('site', 'vendor_min_deposit');
      $userdepbal['vendor_min_deposit'] = isset($vendoerMinDeposit['vendor_min_deposit']) ? $vendoerMinDeposit['vendor_min_deposit'] : 0;

      $db->load->model('Total_model');
      $depbalence = $db->Total_model->getUserBalance($loginUser['id']);

      $userdepbal['show_deposit_warning'] = ($depbalence < $userdepbal['vendor_min_deposit']) ? 1 : 0;
      $userdepbal['vendor_min_deposit_warning'] = __('user.minimum_deposit_warning');

      $vendorDepositStatus = $this->Product_model->getSettings('vendor', 'depositstatus');
      $userdepbal['vendor_deposit_status'] = isset($vendorDepositStatus['depositstatus']) ? $vendorDepositStatus['depositstatus'] : 0;
   }
?>
<script src="<?= base_url('assets/plugins/qrcode.min.js') ?>"></script>
  
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-4">
              <div class="card bg-soft-danger">
                <div class="card-body paidbalance ">
                  <div class="d-flex align-itmes-center">
                    <div>
                      <div class="customebtn bg-soft-primary">
                        <i class="fa-sharp fa-solid fa-money-bill-trend-up icon-symbol"></i>
                      </div>
                    </div>
                    <div class="action-balnce">
                      <p class="mb-0"><?= __('user.balance') ?> </p>
                      <h1><?= $fun_c_format($user_totals['user_balance']) ?></h1>
                    </div>
                    <div>
                      <div class="badge bg-primary">
                        <i class="fa-sharp fa-solid fa-arrow-up py-2"></i>
                        <span><?= __('user.paid_balance') ?>  <?= $fun_c_format($user_totals['wallet_accept_amount']) ?></span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card bg-soft-primary">
                <div class="card-body paidbalance">
                  <div class="d-flex align-itmes-center">
                    <div>
                      <div class=" customebtn bg-soft-danger">
                        <i class="fa-solid fa-location-crosshairs icon-symbol"></i>
                      </div>
                    </div>
                    <div class="action-balnce">
                      <p class="mb-0"><?= __('user.actions') ?> </p>
                      <h1><?= (int)$user_totals['click_action_total'] + (int)$user_totals['vendor_action_external_total'] ?>
                        /
                        <?= $fun_c_format($user_totals['click_action_commission'] + $user_totals['vendor_action_external_commission']) ?></h1>
                    </div>
                    <div>
                      <div class="badge bg-primary">
                        <i class="fa-solid fa-mobile-retro py-2"></i>
                        <span><?= ($userdetails['is_vendor']) ? __('user.vendor_pay') : '' ?> <?= ($userdetails['is_vendor']) ? $fun_c_format($user_totals['vendor_action_external_commission_pay']) : '' ?></span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card bg-soft-warning">
                <div class="card-body paidbalance">
                  <div class="d-flex align-itmes-center">
                    <div>
                      <div class="customebtn bg-soft-primary">
                        <i class="fa-solid fa-computer-mouse icon-symbol"></i>
                      </div>
                    </div>
                    <div class="action-balnce">
                      <p class="mb-0"><?= __('user.clicks') ?></p>
                      <h1><?= (int)($user_totals['total_clicks_count']) ?>
                       /
                       <?= $fun_c_format($user_totals['total_clicks_commission']) ?></h1>
                    </div>
                    <div>
                      <div class="badge bg-primary">
                        <i class="fa-sharp fa-solid fa-arrow-up py-2"></i>
                        <span><?= ($userdetails['is_vendor']) ? __('user.vendor_pay') : '' ?> <?= ($userdetails['is_vendor']) ? $fun_c_format(
                            $user_totals['vendor_click_localstore_commission_pay'] +
                            $user_totals['vendor_click_external_commission_pay']
                          ) : '' ?></span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12 col-xl-8 mt-3">
              <div class="row">
                <div class="container graph-filter mb-2">
                  <div class="row">
                    <div class="col-sm-6">
                      <select onchange="loadDashboardChart()" class="renderChart chart-input form-control" name="group">
                        <option value="day" ><?= __('admin.day') ?></option>
                        <option value="week"><?= __('admin.week') ?></option>
                        <option value="month" selected=""><?= __('admin.month') ?></option>
                        <option value="year"><?= __('admin.year') ?></option>
                      </select>
                    </div>
                    <div class="col-sm-6 mx-sm-auto mt-3 mt-sm-0">
                      <select onchange="loadDashboardChart()" class="yearSelection chart-input form-control" name="year">
                        <?php for($i=2016; $i<= date("Y"); $i++){ ?>
                          <option value="<?= $i ?>" <?php echo $i==date("Y") ? "selected='selected'" : '' ?>><?= $i ?></option>
                        <?php  } ?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="card" data-aos="fade-up" data-aos-delay="800">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                      <div class="header-title">
                        <h4 class="card-title" id="tottal_order_sum"></h4>
                      </div>
                      <div class="d-flex align-items-center align-self-center">
                        <div class="d-flex align-items-center text-primary">
                          <i class="fa-solid fa-circle"></i>
                          <div class="ms-2">
                            <span class="text-secondary"><?= __('admin.weekly_earnings')?></span>
                          </div>
                        </div>
                        <div class="d-flex align-items-center ms-3 text-info">
                          <i class="fa-solid fa-circle"></i>
                          <div class="ms-2">
                            <span class="text-secondary"> <?= __('admin.monthly_earnings')?></span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-body">
                      <p class="mb-0"><?= __('user.user_overview') ?></p>
                      <div id="d-main" class="d-main"></div>
                    </div>
                  </div>
                </div>
              
                <div class="col-lg-4 col-md-4 mt-3">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex align-items-center justify-content-between">
                        <div class="bg-soft-primary customebtn">
                          <i class="fa-solid fa-circle-radiation icon-symbol"></i>
                        </div>
                        <div>
                          <span> <?= __('admin.weekly_earnings') ?></span>
                        </div>
                      </div>
                      <div class="flex justify-content-between align-items-center" style="position: relative;">
                        <h6 class="counter" style="visibility: visible;">
                          <b><?= $user_totals_week ?></b>
                        </h6>
                        <div id="iq-chart-box1" class="chat-short" style="min-height: 50px;">
                          <div id="apexchartslnl34wm0i" class="apexcharts-canvas apexchartslnl34wm0i apexcharts-theme-light" style="width: 100px; height: 50px;">
                            <svg id="SvgjsSvg2229" width="100" height="50" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" class="apexcharts-svg" xmlns:data="ApexChartsNS" transform="translate(0, 0)" style="background: transparent;">
                              <g id="SvgjsG2231" class="apexcharts-inner apexcharts-graphical" transform="translate(0, 10)">
                                <defs id="SvgjsDefs2230">
                                  <clipPath id="gridRectMasklnl34wm0i">
                                    <rect id="SvgjsRect2237" width="109" height="45" x="-4.5" y="-2.5" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect>
                                  </clipPath>
                                  <clipPath id="forecastMasklnl34wm0i"></clipPath>
                                  <clipPath id="nonForecastMasklnl34wm0i"></clipPath>
                                  <clipPath id="gridRectMarkerMasklnl34wm0i">
                                    <rect id="SvgjsRect2238" width="104" height="44" x="-2" y="-2" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect>
                                  </clipPath>
                                </defs>
                                <line id="SvgjsLine2236" x1="0" y1="0" x2="0" y2="40" stroke="#b6b6b6" stroke-dasharray="3" class="apexcharts-xcrosshairs" x="0" y="0" width="1" height="40" fill="#b1b9c4" filter="none" fill-opacity="0.9" stroke-width="1"></line>
                                <g id="SvgjsG2244" class="apexcharts-xaxis" transform="translate(0, 0)">
                                  <g id="SvgjsG2245" class="apexcharts-xaxis-texts-g" transform="translate(0, -4)"></g>
                                </g>
                                <g id="SvgjsG2251" class="apexcharts-grid">
                                  <g id="SvgjsG2252" class="apexcharts-gridlines-horizontal" style="display: none;">
                                    <line id="SvgjsLine2254" x1="0" y1="0" x2="100" y2="0" stroke="#e0e0e0" stroke-dasharray="0" class="apexcharts-gridline"></line>
                                    <line id="SvgjsLine2255" x1="0" y1="8" x2="100" y2="8" stroke="#e0e0e0" stroke-dasharray="0" class="apexcharts-gridline"></line>
                                    <line id="SvgjsLine2256" x1="0" y1="16" x2="100" y2="16" stroke="#e0e0e0" stroke-dasharray="0" class="apexcharts-gridline"></line>
                                    <line id="SvgjsLine2257" x1="0" y1="24" x2="100" y2="24" stroke="#e0e0e0" stroke-dasharray="0" class="apexcharts-gridline"></line>
                                    <line id="SvgjsLine2258" x1="0" y1="32" x2="100" y2="32" stroke="#e0e0e0" stroke-dasharray="0" class="apexcharts-gridline"></line>
                                    <line id="SvgjsLine2259" x1="0" y1="40" x2="100" y2="40" stroke="#e0e0e0" stroke-dasharray="0" class="apexcharts-gridline"></line>
                                  </g>
                                  <g id="SvgjsG2253" class="apexcharts-gridlines-vertical" style="display: none;"></g>
                                  <line id="SvgjsLine2261" x1="0" y1="40" x2="100" y2="40" stroke="transparent" stroke-dasharray="0"></line>
                                  <line id="SvgjsLine2260" x1="0" y1="1" x2="0" y2="40" stroke="transparent" stroke-dasharray="0"></line>
                                </g>
                                <g id="SvgjsG2239" class="apexcharts-line-series apexcharts-plot-series">
                                  <g id="SvgjsG2240" class="apexcharts-series" seriesName="Totalxsales" data:longestSeries="true" rel="1" data:realIndex="0">
                                    <path id="SvgjsPath2243" d="M 0 34.666666666666664L 33.333333333333336 34.666666666666664L 66.66666666666667 1.3333333333333357L 100 34.666666666666664" fill="none" fill-opacity="1" stroke="rgba(52,78,209,0.85)" stroke-opacity="1" stroke-linecap="butt" stroke-width="5" stroke-dasharray="0" class="apexcharts-line" index="0" clip-path="url(#gridRectMasklnl34wm0i)" pathTo="M 0 34.666666666666664L 33.333333333333336 34.666666666666664L 66.66666666666667 1.3333333333333357L 100 34.666666666666664" pathFrom="M -1 48L -1 48L 33.333333333333336 48L 66.66666666666667 48L 100 48"></path>
                                    <g id="SvgjsG2241" class="apexcharts-series-markers-wrap" data:realIndex="0"></g>
                                  </g>
                                  <g id="SvgjsG2242" class="apexcharts-datalabels" data:realIndex="0"></g>
                                </g>
                                <line id="SvgjsLine2262" x1="0" y1="0" x2="100" y2="0" stroke="#b6b6b6" stroke-dasharray="0" stroke-width="1" class="apexcharts-ycrosshairs"></line>
                                <line id="SvgjsLine2263" x1="0" y1="0" x2="100" y2="0" stroke-dasharray="0" stroke-width="0" class="apexcharts-ycrosshairs-hidden"></line>
                                <g id="SvgjsG2264" class="apexcharts-yaxis-annotations"></g>
                                <g id="SvgjsG2265" class="apexcharts-xaxis-annotations"></g>
                                <g id="SvgjsG2266" class="apexcharts-point-annotations"></g>
                              </g>
                              <text id="SvgjsText2233" font-family="Helvetica, Arial, sans-serif" x="10" y="16.5" text-anchor="start" dominant-baseline="auto" font-size="14px" font-weight="900" fill="#373d3f" class="apexcharts-title-text" style="font-family: Helvetica, Arial, sans-serif; opacity: 1;"></text>
                              <rect id="SvgjsRect2235" width="0" height="0" x="0" y="0" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fefefe"></rect>
                              <g id="SvgjsG2250" class="apexcharts-yaxis" rel="0" transform="translate(-18, 0)"></g>
                              <g id="SvgjsG2232" class="apexcharts-annotations"></g>
                            </svg>
                            <div class="apexcharts-legend" style="max-height: 25px;"></div>
                          </div>
                        </div>
                        <div class=" d-flex align-items-center text-primary">
                          <b><?= $user_totals_week_grouwth?>%</b>
                          <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                          </svg>
                        </div>
                        <div class="resize-triggers">
                          <div class="expand-trigger">
                            <div style="width: 322px; height: 51px;"></div>
                          </div>
                          <div class="contract-trigger"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 mt-3">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex align-items-center justify-content-between">
                        <div class="customebtn bg-soft-danger">
                          <i class="fa-solid fa-mobile-screen icon-symbol"></i>
                        </div>
                        <div>
                          <span><?= __('admin.monthly_earnings') ?></span>
                        </div>
                      </div>
                      <div class="flex justify-content-between align-items-center" style="position: relative;">
                        <h6 class="counter" style="visibility: visible;">
                          <b><?= $user_totals_month ?></b>
                        </h6>
                        <div id="iq-chart-box2" class="chat-short" style="min-height: 50px;">
                          <div id="apexcharts1n03tiam" class="apexcharts-canvas apexcharts1n03tiam apexcharts-theme-light" style="width: 100px; height: 50px;">
                            <svg id="SvgjsSvg2267" width="100" height="50" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" class="apexcharts-svg" xmlns:data="ApexChartsNS" transform="translate(0, 0)" style="background: transparent;">
                              <g id="SvgjsG2269" class="apexcharts-inner apexcharts-graphical" transform="translate(0, 10)">
                                <defs id="SvgjsDefs2268">
                                  <clipPath id="gridRectMask1n03tiam">
                                    <rect id="SvgjsRect2275" width="109" height="45" x="-4.5" y="-2.5" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect>
                                  </clipPath>
                                  <clipPath id="forecastMask1n03tiam"></clipPath>
                                  <clipPath id="nonForecastMask1n03tiam"></clipPath>
                                  <clipPath id="gridRectMarkerMask1n03tiam">
                                    <rect id="SvgjsRect2276" width="104" height="44" x="-2" y="-2" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect>
                                  </clipPath>
                                </defs>
                                <line id="SvgjsLine2274" x1="0" y1="0" x2="0" y2="40" stroke="#b6b6b6" stroke-dasharray="3" class="apexcharts-xcrosshairs" x="0" y="0" width="1" height="40" fill="#b1b9c4" filter="none" fill-opacity="0.9" stroke-width="1"></line>
                                <g id="SvgjsG2282" class="apexcharts-xaxis" transform="translate(0, 0)">
                                  <g id="SvgjsG2283" class="apexcharts-xaxis-texts-g" transform="translate(0, -4)"></g>
                                </g>
                                <g id="SvgjsG2289" class="apexcharts-grid">
                                  <g id="SvgjsG2290" class="apexcharts-gridlines-horizontal" style="display: none;">
                                    <line id="SvgjsLine2292" x1="0" y1="0" x2="100" y2="0" stroke="#e0e0e0" stroke-dasharray="0" class="apexcharts-gridline"></line>
                                    <line id="SvgjsLine2293" x1="0" y1="8" x2="100" y2="8" stroke="#e0e0e0" stroke-dasharray="0" class="apexcharts-gridline"></line>
                                    <line id="SvgjsLine2294" x1="0" y1="16" x2="100" y2="16" stroke="#e0e0e0" stroke-dasharray="0" class="apexcharts-gridline"></line>
                                    <line id="SvgjsLine2295" x1="0" y1="24" x2="100" y2="24" stroke="#e0e0e0" stroke-dasharray="0" class="apexcharts-gridline"></line>
                                    <line id="SvgjsLine2296" x1="0" y1="32" x2="100" y2="32" stroke="#e0e0e0" stroke-dasharray="0" class="apexcharts-gridline"></line>
                                    <line id="SvgjsLine2297" x1="0" y1="40" x2="100" y2="40" stroke="#e0e0e0" stroke-dasharray="0" class="apexcharts-gridline"></line>
                                  </g>
                                  <g id="SvgjsG2291" class="apexcharts-gridlines-vertical" style="display: none;"></g>
                                  <line id="SvgjsLine2299" x1="0" y1="40" x2="100" y2="40" stroke="transparent" stroke-dasharray="0"></line>
                                  <line id="SvgjsLine2298" x1="0" y1="1" x2="0" y2="40" stroke="transparent" stroke-dasharray="0"></line>
                                </g>
                                <g id="SvgjsG2277" class="apexcharts-line-series apexcharts-plot-series">
                                  <g id="SvgjsG2278" class="apexcharts-series" seriesName="SalexToday" data:longestSeries="true" rel="1" data:realIndex="0">
                                    <path id="SvgjsPath2281" d="M 0 34.666666666666664L 33.333333333333336 34.666666666666664L 66.66666666666667 1.3333333333333357L 100 34.666666666666664" fill="none" fill-opacity="1" stroke="rgba(185,29,18,0.85)" stroke-opacity="1" stroke-linecap="butt" stroke-width="5" stroke-dasharray="0" class="apexcharts-line" index="0" clip-path="url(#gridRectMask1n03tiam)" pathTo="M 0 34.666666666666664L 33.333333333333336 34.666666666666664L 66.66666666666667 1.3333333333333357L 100 34.666666666666664" pathFrom="M -1 48L -1 48L 33.333333333333336 48L 66.66666666666667 48L 100 48"></path>
                                    <g id="SvgjsG2279" class="apexcharts-series-markers-wrap" data:realIndex="0"></g>
                                  </g>
                                  <g id="SvgjsG2280" class="apexcharts-datalabels" data:realIndex="0"></g>
                                </g>
                                <line id="SvgjsLine2300" x1="0" y1="0" x2="100" y2="0" stroke="#b6b6b6" stroke-dasharray="0" stroke-width="1" class="apexcharts-ycrosshairs"></line>
                                <line id="SvgjsLine2301" x1="0" y1="0" x2="100" y2="0" stroke-dasharray="0" stroke-width="0" class="apexcharts-ycrosshairs-hidden"></line>
                                <g id="SvgjsG2302" class="apexcharts-yaxis-annotations"></g>
                                <g id="SvgjsG2303" class="apexcharts-xaxis-annotations"></g>
                                <g id="SvgjsG2304" class="apexcharts-point-annotations"></g>
                              </g>
                              <text id="SvgjsText2271" font-family="Helvetica, Arial, sans-serif" x="10" y="16.5" text-anchor="start" dominant-baseline="auto" font-size="14px" font-weight="900" fill="#373d3f" class="apexcharts-title-text" style="font-family: Helvetica, Arial, sans-serif; opacity: 1;"></text>
                              <rect id="SvgjsRect2273" width="0" height="0" x="0" y="0" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fefefe"></rect>
                              <g id="SvgjsG2288" class="apexcharts-yaxis" rel="0" transform="translate(-18, 0)"></g>
                              <g id="SvgjsG2270" class="apexcharts-annotations"></g>
                            </svg>
                            <div class="apexcharts-legend" style="max-height: 25px;"></div>
                          </div>
                        </div>
                        <div class="d-flex align-items-center text-danger">
                          <b><?= $user_totals_month_grouwth?>%</b>
                          <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                          </svg>
                        </div>
                        <div class="resize-triggers">
                          <div class="expand-trigger">
                            <div style="width: 322px; height: 51px;"></div>
                          </div>
                          <div class="contract-trigger"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 mt-3">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex align-items-center justify-content-between">
                        <div class="customebtn bg-soft-success">
                          <i class="fa-solid fa-layer-group icon-symbol"></i>
                        </div>
                        <div>
                          <span><?= __('admin.yearly_earnings') ?></span>
                        </div>
                      </div>
                      <div class="flex justify-content-between align-items-center" style="position: relative;">
                        <h6 class="counter" style="visibility: visible;">
                          <b><?= $user_totals_year ?></b>
                        </h6>
                        <div id="iq-chart-box3" class="chat-short" style="min-height: 50px;">
                          <div id="apexcharts5zhbehzb" class="apexcharts-canvas apexcharts5zhbehzb apexcharts-theme-light" style="width: 100px; height: 50px;">
                            <svg id="SvgjsSvg2305" width="100" height="50" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" class="apexcharts-svg" xmlns:data="ApexChartsNS" transform="translate(0, 0)" style="background: transparent;">
                              <g id="SvgjsG2307" class="apexcharts-inner apexcharts-graphical" transform="translate(0, 10)">
                                <defs id="SvgjsDefs2306">
                                  <clipPath id="gridRectMask5zhbehzb">
                                    <rect id="SvgjsRect2313" width="109" height="45" x="-4.5" y="-2.5" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect>
                                  </clipPath>
                                  <clipPath id="forecastMask5zhbehzb"></clipPath>
                                  <clipPath id="nonForecastMask5zhbehzb"></clipPath>
                                  <clipPath id="gridRectMarkerMask5zhbehzb">
                                    <rect id="SvgjsRect2314" width="104" height="44" x="-2" y="-2" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect>
                                  </clipPath>
                                </defs>
                                <line id="SvgjsLine2312" x1="0" y1="0" x2="0" y2="40" stroke="#b6b6b6" stroke-dasharray="3" class="apexcharts-xcrosshairs" x="0" y="0" width="1" height="40" fill="#b1b9c4" filter="none" fill-opacity="0.9" stroke-width="1"></line>
                                <g id="SvgjsG2320" class="apexcharts-xaxis" transform="translate(0, 0)">
                                  <g id="SvgjsG2321" class="apexcharts-xaxis-texts-g" transform="translate(0, -4)"></g>
                                </g>
                                <g id="SvgjsG2327" class="apexcharts-grid">
                                  <g id="SvgjsG2328" class="apexcharts-gridlines-horizontal" style="display: none;">
                                    <line id="SvgjsLine2330" x1="0" y1="0" x2="100" y2="0" stroke="#e0e0e0" stroke-dasharray="0" class="apexcharts-gridline"></line>
                                    <line id="SvgjsLine2331" x1="0" y1="8" x2="100" y2="8" stroke="#e0e0e0" stroke-dasharray="0" class="apexcharts-gridline"></line>
                                    <line id="SvgjsLine2332" x1="0" y1="16" x2="100" y2="16" stroke="#e0e0e0" stroke-dasharray="0" class="apexcharts-gridline"></line>
                                    <line id="SvgjsLine2333" x1="0" y1="24" x2="100" y2="24" stroke="#e0e0e0" stroke-dasharray="0" class="apexcharts-gridline"></line>
                                    <line id="SvgjsLine2334" x1="0" y1="32" x2="100" y2="32" stroke="#e0e0e0" stroke-dasharray="0" class="apexcharts-gridline"></line>
                                    <line id="SvgjsLine2335" x1="0" y1="40" x2="100" y2="40" stroke="#e0e0e0" stroke-dasharray="0" class="apexcharts-gridline"></line>
                                  </g>
                                  <g id="SvgjsG2329" class="apexcharts-gridlines-vertical" style="display: none;"></g>
                                  <line id="SvgjsLine2337" x1="0" y1="40" x2="100" y2="40" stroke="transparent" stroke-dasharray="0"></line>
                                  <line id="SvgjsLine2336" x1="0" y1="1" x2="0" y2="40" stroke="transparent" stroke-dasharray="0"></line>
                                </g>
                                <g id="SvgjsG2315" class="apexcharts-line-series apexcharts-plot-series">
                                  <g id="SvgjsG2316" class="apexcharts-series" seriesName="TotalxClasson" data:longestSeries="true" rel="1" data:realIndex="0">
                                    <path id="SvgjsPath2319" d="M 0 34.666666666666664L 33.333333333333336 34.666666666666664L 66.66666666666667 1.3333333333333357L 100 34.666666666666664" fill="none" fill-opacity="1" stroke="rgba(7,117,11,0.85)" stroke-opacity="1" stroke-linecap="butt" stroke-width="5" stroke-dasharray="0" class="apexcharts-line" index="0" clip-path="url(#gridRectMask5zhbehzb)" pathTo="M 0 34.666666666666664L 33.333333333333336 34.666666666666664L 66.66666666666667 1.3333333333333357L 100 34.666666666666664" pathFrom="M -1 48L -1 48L 33.333333333333336 48L 66.66666666666667 48L 100 48"></path>
                                    <g id="SvgjsG2317" class="apexcharts-series-markers-wrap" data:realIndex="0"></g>
                                  </g>
                                  <g id="SvgjsG2318" class="apexcharts-datalabels" data:realIndex="0"></g>
                                </g>
                                <line id="SvgjsLine2338" x1="0" y1="0" x2="100" y2="0" stroke="#b6b6b6" stroke-dasharray="0" stroke-width="1" class="apexcharts-ycrosshairs"></line>
                                <line id="SvgjsLine2339" x1="0" y1="0" x2="100" y2="0" stroke-dasharray="0" stroke-width="0" class="apexcharts-ycrosshairs-hidden"></line>
                                <g id="SvgjsG2340" class="apexcharts-yaxis-annotations"></g>
                                <g id="SvgjsG2341" class="apexcharts-xaxis-annotations"></g>
                                <g id="SvgjsG2342" class="apexcharts-point-annotations"></g>
                              </g>
                              <text id="SvgjsText2309" font-family="Helvetica, Arial, sans-serif" x="10" y="16.5" text-anchor="start" dominant-baseline="auto" font-size="14px" font-weight="900" fill="#373d3f" class="apexcharts-title-text" style="font-family: Helvetica, Arial, sans-serif; opacity: 1;"></text>
                              <rect id="SvgjsRect2311" width="0" height="0" x="0" y="0" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fefefe"></rect>
                              <g id="SvgjsG2326" class="apexcharts-yaxis" rel="0" transform="translate(-18, 0)"></g>
                              <g id="SvgjsG2308" class="apexcharts-annotations"></g>
                            </svg>
                            <div class="apexcharts-legend" style="max-height: 25px;"></div>
                          </div>
                        </div>
                        <div class="d-flex align-items-center text-success">
                          <b><?= $user_totals_year_grouwth?>%</b>
                          <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                          </svg>
                        </div>
                        <div class="resize-triggers">
                          <div class="expand-trigger">
                            <div style="width: 322px; height: 51px;"></div>
                          </div>
                          <div class="contract-trigger"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <?php 
                  $googleAdsCenter = $this->Setting_model->getGoogleAds(5,1);
                  if(!empty($googleAdsCenter)){
                  ?>
                  <div class="col-xl-12 col-lg-12 col-md-12 mt-3">
                    <div class="googleaddsecond-bg text-center">
                      Google Add Here 
                      <ins class="adsbygoogle"
                         style="display:block"
                         data-ad-client="<?= @$googleAdsCenter[0]['client_key']?>"
                         data-ad-slot="<?= @$googleAdsCenter[0]['unit_key']?>"
                         data-ad-format="auto"
                         data-full-width-responsive="true"></ins>
                        <script>
                             (adsbygoogle = window.adsbygoogle || []).push({});
                        </script>
                     </div>
                  </div>
                <?php }?>
                <?php if(allowMarketVendorPanelSections($marketvendorpanelmode, $userdetails['is_vendor'])) { ?>
                <script type="text/javascript">
                  $( document ).ready(function() {
                      getMarketTools(1);
                  
                  $(".display-vendor-links").change(function(){
                    
                    getMarketTools();
                  })



                  });
                  function getMarketTools(page) {
                    $this = $(this);
                    $.ajax({
                      url:'<?= base_url('usercontrol/dashboard') ?>',
                      type:'POST',
                      dataType:'json',
                      data:{
                        get_tools:true,
                        page:page
                      },
                      beforeSend:function(){
                        $(".site-order #affiliate-accordion").html("<p class='text-center'>"+'<?= __('user.loading') ?>'+"....</p>");
                      },
                      complete:function(){
                      },
                      success:function(json){
                        if(json['html']){
                          $(".site-order .pagination-div").html(json['pagination']);
                          $(".site-order #affiliate-accordion").html(json['html']);
                        }

                        $('[copyToClipboard]').tooltip({
                          trigger: 'click',
                          placement: 'bottom'
                        });
                      },
                    })
                  }
                  $(document).on('click','.site-order .pagination-div ul li a',function(e){
                    e.preventDefault();

                    let page = $(this).data('ci-pagination-page');

                    if(page)
                      getMarketTools(page);
                  })
                  $(document).on('click','.qrcode',function(){
                      $('#model-codemodal .modal-body').html("<span id='QRDataModal'></span>");
                      $("#model-codemodal").modal("show");
                      var qrdata = $(this).attr('data-id');
                      var qrcode = new QRCode(document.getElementById("QRDataModal"), {
                          text: qrdata,
                          width: 128,
                          height: 128,
                          colorDark : "#000000",
                          colorLight : "#ffffff",
                          correctLevel : QRCode.CorrectLevel.H
                      });

                  })
                </script>
                <div class="col-sm-12 mt-3">
                  <div class="card site-order">
                    <div class="card-header d-flex justify-content-between">
                      <div class="header-title">
                        <h4 class="card-title"><?= __('user.affiliates_links...') ?></h4>
                      </div>
                    </div>
                    <div class="user-header">
                      <div class="site-order-wrapp d-flex align-items-center justify-content-between flex-wrap">
                        <div>
                          <div class="pagination-div bg-area ps-3 pt-2"></div>
                        </div>
                      </div>
                    </div>
                    <div class="card-body mrketplacece">
                      <div class="accordin-market">
                        <div class="market-heade">
                          <ul>
                            <li><?= __('user.image') ?></li>
                            <li class="name-text"><?= __('user.name') ?></li>
                            <li class="offer-text"><?= __('user.commission') ?></li>
                            <li><?= __('user.link') ?></li>
                          </ul>
                        </div>
                        <div class="accordion" id="affiliate-accordion">
                         
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <?php } ?>
                <div class="col-md-12 mt-3">
                  <div class="row row-cols-1 row-cols-md-2 row-cols-lg-2 mb-3 text-center">
                    <div class="col">
                      <div class="card mb-3 rounded-3 shadow-sm">
                        <div class="card-body">
                          <h4 class="my-0 fw-normal">
                            <b><?= __('user.wallet_statistics') ?></b>
                          </h4>
                          <div class="d-flex justify-content-between align-items-center my-3">
                            <div>
                              <span><?= __('user.hold') ?></span>
                            </div>
                            <div>
                              <span><?= (int)$user_totals['wallet_unpaid_amounton_hold_count'] ?>
                              / 
                              <?= $fun_c_format($user_totals['wallet_on_hold_amount']) ?>
                                
                              </span>
                            </div>
                          </div>
                          
                          <div class="d-flex justify-content-between align-items-center my-3">
                            <div>
                              <span><?= __('user.unpaid') ?></span>
                            </div>
                            <div>
                              <span>
                                <?= (int)$user_totals['wallet_unpaid_count'] ?>
                                / 
                                <?= $fun_c_format($user_totals['wallet_unpaid_amount']) ?>
                              </span>
                            </div>
                          </div>
                          <div class="d-flex justify-content-between align-items-center my-3">
                            <div>
                              <span><?= __('user.request') ?></span>
                            </div>
                            <div>
                              <span>
                                <?= (int)$user_totals['wallet_request_sent_count'] ?> 
                                / 
                                <?= $fun_c_format($user_totals['wallet_request_sent_amount']) ?>

                              </span>
                            </div>
                          </div>
                          <div class="d-flex justify-content-between align-items-center my-3">
                            <div>
                              <span><?= __('user.paid') ?></span>
                            </div>
                            <div>
                              <span>
                                <?= (int)$user_totals['wallet_accept_count'] ?> 
                                / 
                                <?= $fun_c_format($user_totals['wallet_accept_amount']) ?>
                                  
                              </span>
                            </div>
                          </div>
                          <div class="d-flex justify-content-between align-items-center my-3">
                            <div>
                              <span><?= __('user.cancel') ?></span>
                            </div>
                            <div>
                              <span>
                                <?= (int)$user_totals['wallet_cancel_count'] ?>  
                                / 
                                <?= $fun_c_format($user_totals['wallet_cancel_amount']) ?>
                              </span>
                            </div>
                          </div>
                          <div class="d-flex justify-content-between align-items-center my-3">
                            <div>
                              <span><?= __('user.trash') ?></span>
                            </div>
                            <div>
                              <span>
                                <?= (int)$user_totals['wallet_trash_count'] ?>  
                                / 
                                <?= $fun_c_format($user_totals['wallet_trash_amount']) ?>
                              </span>
                            </div>
                          </div>
                          <a href="<?= base_url('usercontrol/mywallet') ?>" class="btn btn-info  mb-2"><?= __('user.check_all_transactions') ?> </a>
                          <a href="<?= base_url('usercontrol/mywallet') ?>" class="btn btn-info mb-2"><?= __('user.click_here') ?> </a>
                        </div>
                      </div>
                    </div>
                    <div class="col">
                      <div class="card mb-3 rounded-3 shadow-sm">
                        <div class="card-body">
                          <h4 class="my-0 fw-normal">
                            <b><?= __('user.all_clicks') ?></b>
                          </h4>
                          <div class="d-flex justify-content-between align-items-center my-3">
                            <div>
                              <span><?= __('user.local_store') ?></span>
                            </div>
                            <div>
                              <span>
                                
                                <?= (int)$user_totals['click_localstore_total'] ?>
                                / 
                                <?= $fun_c_format($user_totals['click_localstore_commission']) ?>
                              </span>
                            </div>
                          </div>
                          <div class="d-flex justify-content-between align-items-center my-3">
                            <div>
                              <span><?= __('user.external') ?></span>
                            </div>
                            <div>
                              <span>
                                <?= (int)$user_totals['click_external_total'] ?>
                                / 
                                <?= $fun_c_format($user_totals['click_external_commission']) ?>
                              </span>
                            </div>
                          </div>
                          <div class="d-flex justify-content-between align-items-center my-3">
                            <div>
                              <span><?= __('user.forms') ?></span>
                            </div>
                            <div>
                              <span>
                                
                                <?= (int)$user_totals['click_form_total'] ?>
                                / 
                                <?= $fun_c_format($user_totals['click_form_commission']) ?>
                              </span>
                            </div>
                          </div>
                          <div class="d-flex justify-content-between align-items-center my-3">
                            <div>
                              <span><?= __('user.vendor_local_store') ?></span>
                            </div>
                            <div>
                              <span>
                                
                                <?= (int)$user_totals['vendor_click_localstore_total'] ?>
                                / 
                                <?= $fun_c_format($user_totals['vendor_click_localstore_commission_pay']) ?>
                              </span>
                            </div>
                          </div>
                          <div class="d-flex justify-content-between align-items-center my-3">
                            <div>
                              <span><?= __('user.vendor_external') ?></span>
                            </div>
                            <div>
                              <span>
                                <?= (int)$user_totals['vendor_click_external_total'] ?>
                                / 
                                <?= $fun_c_format($user_totals['vendor_click_external_commission_pay']) ?>

                              </span>
                            </div>
                          </div>
                          <div class="d-flex justify-content-between align-items-center my-3" style="margin-bottom: 0.8rem !important;">
                            <div>
                              <span></span>
                            </div>
                            <div>
                              <span>
                                
                                
                              </span>
                            </div>
                          </div>
                         <a href="<?= base_url('usercontrol/mywallet') ?>" class="btn btn-info mb-2 mt-sm-4"><?= __('user.check_all_transactions') ?> </a>
                          <a href="<?= base_url('usercontrol/mywallet') ?>" class="btn btn-info mb-2 mt-sm-4"><?= __('user.click_here') ?> </a>
                        </div>
                      </div>
                    </div>
                    <div class="col">
                      <div class="card mb-3 rounded-3 shadow-sm">
                        <div class="card-body">
                          <h4 class="my-0 fw-normal">
                            <b><?= __('user.order_commission') ?></b>
                          </h4>
                          <div class="d-flex justify-content-between align-items-center my-3">
                            <div>
                              <span><?= __('user.local_store') ?></span>
                            </div>
                            <div>
                              <span>
                                
                                <?= (int)$user_totals['sale_localstore_count'] ?>
                                / 
                                <?= $fun_c_format($user_totals['sale_localstore_commission']) ?>
                              </span>
                            </div>
                          </div>
                          <div class="d-flex justify-content-between align-items-center my-3">
                            <div>
                              <span><?= __('user.external') ?></span>
                            </div>
                            <div>
                              <span>
                                
                                <?= (int)$user_totals['order_external_count'] ?>
                                / 
                                <?= $fun_c_format($user_totals['order_external_commission']) ?>
                              </span>
                            </div>
                          </div>
                          <div class="d-flex justify-content-between align-items-center my-3">
                            <div>
                              <span><?= __('user.vendor_local_store') ?></span>
                            </div>
                            <div>
                              <span>
                                
                                <?= (int)$user_totals['vendor_sale_localstore_count'] ?>
                                / 
                                <?= $fun_c_format($user_totals['vendor_sale_localstore_commission_pay']) ?>
                              </span>
                            </div>
                          </div>
                          <div class="d-flex justify-content-between align-items-center my-3">
                            <div>
                              <span><?= __('user.vendor_external') ?></span>
                            </div>
                            <div>
                              <span>
                                
                                <?= (int)$user_totals['vendor_order_external_count'] ?>
                                / 
                                <?= $fun_c_format($user_totals['vendor_order_external_commission_pay']) ?>
                              </span>
                            </div>
                          </div>
                          <div class="d-flex justify-content-between align-items-center my-3">
                            <div>
                              <span></span>
                            </div>
                            <div>
                              <span>
                                
                                
                              </span>
                            </div>
                          </div>
                          <a href="<?= base_url('usercontrol/mywallet') ?>" class="btn btn-info  mb-2 mt-sm-5"><?= __('user.check_all_transactions') ?> </a>
                          <a href="<?= base_url('usercontrol/mywallet') ?>" class="btn btn-info mb-2 mt-sm-5"><?= __('user.click_here') ?> </a>
                        </div>
                      </div>
                    </div>
                    <?php if($refer_status){ ?>
                    <div class="col">
                      <div class="card mb-3 rounded-3 shadow-sm">
                        <div class="card-body">
                          <h4 class="my-0 fw-normal">
                            <b><?= __('user.refered_levels') ?></b>
                          </h4>
                          <div class="d-flex justify-content-between align-items-center my-3">
                            <div>
                              <span><?= __('user.product_click') ?></span>
                            </div>
                            <div>
                              <span>
                                
                                <?= (int)$refer_total['total_product_click']['clicks'] ?> 
                                / 
                                <?= $fun_c_format($refer_total['total_product_click']['amounts']) ?>
                              </span>
                            </div>
                          </div>
                          <div class="d-flex justify-content-between align-items-center my-3">
                            <div>
                              <span><?= __('user.sale') ?></span>
                            </div>
                            <div>
                              <span>
                                
                                <?= (int)$refer_total['total_product_sale']['counts'] ?> 
                                / 
                                <?= $fun_c_format($refer_total['total_product_sale']['amounts']) ?>
                              </span>
                            </div>
                          </div>
                          <div class="d-flex justify-content-between align-items-center my-3">
                            <div>
                              <span><?= __('user.general_click') ?></span>
                            </div>
                            <div>
                              <span>
                                
                                <?= (int)$refer_total['total_ganeral_click']['total_clicks'] ?> 
                                / 
                                <?= $fun_c_format($refer_total['total_ganeral_click']['total_amount']) ?>
                              </span>
                            </div>
                          </div>
                          <div class="d-flex justify-content-between align-items-center my-3">
                            <div>
                              <span><?= __('user.action') ?></span>
                            </div>
                            <div>
                              <span>
                                
                                <?= (int)$refer_total['total_action']['click_count'] ?> 
                                / 
                                <?= $fun_c_format($refer_total['total_action']['total_amount']) ?>
                              </span>
                            </div>
                          </div>
                          <div class="d-flex justify-content-between align-items-center my-3">
                            <div>
                              <span></span>
                            </div>
                            <div>
                              <span>
                                
                                
                              </span>
                            </div>
                          </div>
                          <a href="<?= base_url('usercontrol/mywallet') ?>" class="btn btn-info  mb-2 mt-sm-5"><?= __('user.check_all_transactions') ?> </a>
                          <a href="<?= base_url('usercontrol/mywallet') ?>" class="btn btn-info mb-2 mt-sm-5"><?= __('user.click_here') ?> </a>
                        </div>
                      </div>
                    </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12 col-xl-4 mt-3">
              <div class="row">
                <?php if($userdetails['is_vendor']): ?>
                <div class="col-md-12 col-lg-12">
                  <div class="card credit-card-widget" data-aos="fade-up" data-aos-delay="900">
                    <div class="card-header pb-4 border-0"></div>
                    <div class="card-body">
                      <div class="d-flex align-itmes-center justify-content-between flex-wrap  mb-4">
                        <div class="d-flex align-itmes-center me-0 me-md-4">
                          <div>
                            <div class="customebtn mb-2 bg-soft-primary">
                              <i class="fa-solid fa-bag-shopping icon-symbol"></i>
                            </div>
                          </div>
                          <div class="ms-1">
                            <h5><?= $fun_c_format($user_totals['vendor_order_external_total']) ?></h5>
                            <small class="mb-0"><?= __('user.external_sales') ?></small>
                          </div>
                        </div>
                        <div class="d-flex align-itmes-center">
                          <div>
                            <div class="customebtn mb-2 bg-soft-info">
                              <i class="fa-solid fa-cart-shopping icon-symbol"></i>
                            </div>
                          </div>
                          <div class="ms-1">
                            <h5><?= $fun_c_format($user_totals['vendor_sale_localstore_total'])?></h5>
                            <small class="mb-0"><?= __('user.local_store') ?></small>
                          </div>
                        </div>
                      </div>
                      <div class="mb-4">
                        <div class="d-flex justify-content-between flex-wrap">
                          <h2 class="mb-2"><?= $fun_c_format($user_totals['vendor_sale_localstore_total'] + $user_totals['vendor_order_external_total']) ?></h2>
                        </div>
                        <p class="text-info"><?= __('user.total_sale') ?></p>
                      </div>
                      <div class="d-grid grid-cols-2 gap-card">
                        <button class="btn btn-primary text-uppercase p-2"><?= __('user.summary') ?></button>
                        <button class="btn btn-info text-uppercase p-2"><?= __('user.analytics') ?></button>
                      </div>
                    </div>
                  </div>
                </div>
                <?php endif ?>
                <div class="col-md-12 col-xl-12 col-lg-12 mt-3">
                  <div class="card">
                  <?php if($MembershipSetting['status']){ ?>
                    <div class="card-header">
                      <div class="header-title">
                        <h4 class="card-title text-center"><?= __('user.membership_plan') ?></h4>
                      </div>
                      <?php if((isset($is_lifetime_plan) && $is_lifetime_plan) || !$isMembershipAccess){ ?>
                        <div class="card-body">
                              <div class="text-center">
                                <div class="user-profile">
                                  <?php $image = !empty($userdetails['avatar']) ? base_url('assets/images/users/'. $userdetails['avatar']) : base_url('assets/vertical/assets/images/users/avatar-1.jpg') ?> 
                                  <img src="<?= $image ?>" class="img-fluid rounded-pill avatar-100">
                                </div>
                              </div>
                            </div>
                      <div class="card-body text-center">
                        <h4><?= __('user.lifetime_free_membership') ?></h4>
                        <p><?= __('user.lifetime_free_membership_access_all_system_functions') ?></p>
                      </div>
                    <?php }?>
                      <?php if(isset($plan) && $plan){
                        $checkDay = max((int)$MembershipSetting['notificationbefore'],1);
                          if($plan->remainDay() != 'lifetime' && $plan->remainDay() <= $checkDay && !$plan->is_lifetime && $isMembershipAccess){ ?>
                            <div class="membership-alert"><?= __('user.your_account_will_expire_in') ?> 
                            <span data-time-remains="<?= $plan->strToTimeRemains(); ?>"><?= $plan->remainDay() ?></span> 
                            <a href="<?= base_url('/usercontrol/purchase_plan/') ?>">
                              <?= __('user.click_here') ?>
                            </a> 
                            <?= __('user.to_renew_plan') ?>
                          </div>
                        <?php }
                        if($isMembershipAccess){
                          $remain = $plan->remainDay();
                          $planto = ($plan->is_lifetime) ? __('user.lifetime') : dateFormat($plan->expire_at,'d F Y h:i A');
                           ?>

                            <div class="card-body">
                              <div class="text-center">
                                <div class="user-profile">
                                  <?php $image = !empty($userdetails['avatar']) ? base_url('assets/images/users/'. $userdetails['avatar']) : base_url('assets/vertical/assets/images/users/avatar-1.jpg') ?> 
                                  <img src="<?= $image ?>" class="img-fluid rounded-pill avatar-100">
                                </div>
                                <div class="mt-3">
                                  <h3 class="d-inline-block"><?= __('user.hello') ?> <?= $userdetails['firstname'].' '.$userdetails['lastname'] ?></h3>
                                  <p class="d-inline-block pl-3"> </p>
                                  <p class="mb-0"> <?= dateFormat($plan->started_at,'d F Y h:i A') ?> / <b><?= __('user.to') ?></b> <?= $planto ?></p>
                                </div>
                              </div>
                            </div>
                          
                          </div>
                          <div class="card-body">
                            <ul class="list-inline m-0 p-0">
                              <li class="d-flex mb-4 align-items-center">
                                <div class="ms-0 flex-grow-1">
                                  <h6><?= __('user.remaining_time') ?></h6>
                                  <?php if($plan->is_lifetime){ ?>
                                    <p>&infin;</p>
                                  <?php } else { ?>
                                    
                                    <p data-time-remains="<?= $plan->strToTimeRemains() ?>" class="mb-0"><?= $remain ?></p>
                                  <?php } ?>
                                  
                                </div>
                                <a href="javascript:void(0);" class="btn btn-outline-primary rounded-circle btn-icon btn-sm p-2">
                                  <span class="btn-inner plus-sign">
                                    <i class="fa-solid fa-square-plus"></i>
                                  </span>
                                </a>
                              </li>
                          <?php if(isset($payment_details)) { ?>
                            <li class="d-flex mb-4 align-items-center">
                              <div class="ms-0 flex-grow-1">
                                <h6><?= __('user.payment_status') ?></h6>
                                <p class="mb-0"><?= __('user.active') ?><?= $payment_details->payment_status ?></p>
                              </div>
                              <a href="javascript:void(0);" class="btn btn-outline-primary rounded-circle btn-icon btn-sm p-2">
                                <span class="btn-inner">
                                  <i class="fa-solid fa-square-plus"></i>
                                </span>
                              </a>
                            </li>
                          <?php } ?>
                              <li class="d-flex mb-4 align-items-center">
                                <div class="ms-0 flex-grow-1">
                                  <h6><?= __('user.plan') ?></h6>
                                  <p class="mb-0"><?= $plan->plan ? $plan->plan->name : '' ?></p>
                                </div>
                                <a href="javascript:void(0);" class="btn btn-outline-primary rounded-circle btn-icon btn-sm p-2">
                                  <span class="btn-inner">
                                    <i class="fa-solid fa-square-plus"></i>
                                  </span>
                                </a>
                              </li>
                            </ul>
                            <a class="align-items-center" data-bs-toggle="modal" data-bs-target=".description" href="#">+ Description</a>
                          </div>
                        <?php } ?>
                        <?php if($isMembershipAccess){ ?>  

                          <div class="modal description fade" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered  scanner">
                              <div class="modal-content ">
                                <div class="modal-header">
                                  <h5 class="modal-title exampleModalLabel1"><?= __('user.description') ?></h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                  <?= $plan->plan ? $plan->plan->description : '' ?>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary text-center" data-bs-dismiss="modal"><?= __('user.close') ?>Close</button>
                                </div>
                              </div>
                            </div>
                          </div>
                    <?php }}?>
                  <?php }?>
                  </div>
                </div>
              </div>

              <div class="col-lg-12 mt-3">
                <div class="col-sm-12">
                  <div class="card">
                  <?php 
                  if($store['status'] || $refer_status){ 

                    $invitationlinkid=0;
                    if(isset($userdashboard_settings) && isset($userdashboard_settings['invitation_link_id']))
                    {
                      $invitationlinkidarray=$userdashboard_settings['invitation_link_id'];
                      $invitationlinkid=$invitationlinkidarray['setting_value'];
                    }

                    if($invitationlinkid==0)
                    {
                    ?>

                    <div class="card-header flex justify-content-between">
                      <div class="row">
                        <div class="col-md-8">
                          <div class="header-title">
                            <h4 class="card-title mb-md-0 mb-3"><?= __('user.your_affiliate_id') ?> : <?= $userdetails['id'] ?></h4>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="show_my_id" required="">
                            <label class="form-check-label" for="invalidCheck1"><?= __('user.show_my_id') ?></label>
                            <div class="invalid-feedback"> You must agree before submitting. </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <?php
                        } 
                    } ?>

                    <div class="card-body">
                      <?php if($store['status'] || $refer_status){ ?>
                      <?php if(($store['status'])){
                        
                         $share_url = ($store_slug) ? base_url($store_slug) : base_url('store/' . base64_encode($userdetails['id'])); ?>
                        <div class=" border-custom show-tiny-link <?php if($invitationlinkid==1) { echo 'd-none';} ?>" >
                          <div class="form-group">
                           <label class="form-label"><?= __('user.affiliate_store_url') ?></label>
                           
                            <input type="text" readonly="readonly" value="<?= $share_url ?>" class="input-store-url-0 form-control">
                          </div>
                          <div class="flex align-items-center justify-content-end list-user-action">
                            <a class="bt-all btn-success qrcode" href="javascript:void(0)"   data-id="<?= $share_url ?>">
                              <i class="fas fa-walkie-talkie"></i>
                            </a>
                            <a class="bt-all btn-warning target-share-link" href="<?= $share_url ?>"  target="_blank">
                              <span class="btn-inner">
                                <i class="fa fa-share"></i>
                              </span>
                            </a>
                            <a class="bt-all btn-warning" href="javascript:void(0)" copyToClipboard="<?= $share_url ?>" title="<?= __('user.copied'); ?>">
                              <span class="btn-inner">
                                <i class="far fa-copy" alt="<?= __('user.copy') ?>"></i>
                              </span>
                            </a>
                            <a  href="javascript:void(0)" class="bt-all btn-danger dashboard-model-slug" data-type="store" data-related-id="0" data-input-class="input-store-url-0">
                              <span class="btn-inner">
                                <i class="fas fa-cog" alt="<?= __('user.setting') ?>"></i>
                              </span>
                            </a>
                            <a  class="bt-all btn-success"  href="javascript:void(0)" data-social-share data-share-url="<?= $share_url; ?>?id=<?= $userdetails['id'] ?>" data-share-title="" data-share-desc="">
                              <span class="ms-1">
                                <i class="fa-solid fa-share-from-square" alt="<?= __('user.share') ?>"></i>
                              </span>
                            </a>
                          </div>
                        </div>
                        <div class=" border-custom show-mega-link <?php if($invitationlinkid==0) { echo 'd-none';} ?>" >
                          <div class="form-group">
                            <label class="form-label"><?= __('user.affiliate_store_url') ?></label>
                            <input type="text" name="text"  readonly="readonly" value="<?= $share_url.'/?id='.$userdetails['id'] ?>" class="input-store-url-0 form-control" data-addition-url="/?id=<?= $userdetails['id'] ?>">
                          </div>
                          <div class="flex align-items-center justify-content-end list-user-action">
                            <a class="bt-all btn-success qrcode" href="javascript:void(0)"   data-id="<?= $share_url.'/?id='.$userdetails['id'] ?>">
                              <i class="fas fa-walkie-talkie"></i>
                            </a>
                            <a class="bt-all btn-warning target-share-link" href="<?= $share_url.'/?id='.$userdetails['id'] ?>"  target="_blank">
                              <span class="btn-inner">
                                <i class="fa fa-share"></i>
                              </span>
                            </a>
                            <a class="bt-all btn-warning" href="javascript:void(0)" copyToClipboard="<?= $share_url.'/?id='.$userdetails['id'] ?>" title="<?= __('user.copied'); ?>">
                              <span class="btn-inner">
                                <i class="far fa-copy" alt="<?= __('user.copy') ?>"></i>
                              </span>
                            </a>
                            <a  href="javascript:void(0)" class="bt-all btn-danger dashboard-model-slug" data-type="store" data-related-id="0" data-input-class="input-store-url-0">
                              <span class="btn-inner">
                                <i class="fas fa-cog" alt="<?= __('user.setting') ?>"></i>
                              </span>
                            </a>
                            <a href="javascript:void(0);" class="bt-all btn-success" data-social-share data-share-url="<?= $share_url; ?>?id=<?= $userdetails['id'] ?>" data-share-title="" data-share-desc="">
                              <span class="ms-1">
                                <i class="fa-solid fa-share-from-square" alt="<?= __('user.share') ?>"></i>
                              </span>
                            </a>
                          </div>
                        </div>
                        <?php 
                        if(isset($userdetails['store_slug']) && !empty($userdetails['store_slug'])){
                          $store_page_url = base_url('store/' .$userdetails['store_slug'].'/'.base64_encode($userdetails['id'])); ?>
                        <div class=" border-custom show-tiny-link <?php if($invitationlinkid==1) { echo 'd-none';} ?>">
                          <div class="form-group">
                            <label class="form-label"><?= __('user.your_store_page') ?></label>
                            <input type="text"  readonly="readonly" value="<?= $store_page_url ?>" class="input-store-url-0 form-control"  name="text">
                          </div>
                          <div class="flex align-items-center justify-content-end list-user-action">
                            <a  href="javascript:void(0)" class="bt-all btn-success qrcode"  data-id="<?= $store_page_url ?>">
                              <i class="fas fa-walkie-talkie"></i>
                            </a>
                            <a class="bt-all btn-warning" href="javascript:void(0)" copyToClipboard="<?= $store_page_url ?>" title="<?= __('user.copied'); ?>">
                              <span class="btn-inner">
                                <i class="far fa-copy" alt="<?= __('user.copy') ?>"></i>
                              </span>
                            </a>
                            <a  href="javascript:void(0)" class="bt-all btn-danger dashboard-model-slug" data-type="store" data-related-id="0" data-input-class="input-store-url-0">
                              <span class="btn-inner">
                                <i class="fas fa-cog" alt="<?= __('user.setting') ?>"></i>
                              </span>
                            </a>
                            <a class="bt-all btn-success" href="javascript:void(0)" data-social-share data-share-url="<?= $share_url; ?>?id=<?= $userdetails['id'] ?>" data-share-title="" data-share-desc="">
                              <span class="ms-1">
                                <i class="fa-solid fa-share-from-square" alt="<?= __('user.share') ?>"></i>
                              </span>
                            </a>
                          </div>
                        </div>
                        <div class=" border-custom show-mega-link <?php if($invitationlinkid==0) { echo 'd-none';} ?>">
                          <div class="form-group">
                            <label class="form-label"><?= __('user.your_store_page') ?></label>
                            <input type="text"  readonly="readonly" value="<?= $store_page_url.'/?id='.$userdetails['id'] ?>" class="input-store-url-0 form-control" data-addition-url="/?id=<?= $userdetails['id'] ?>">
                          </div>

                          <div class="flex align-items-center justify-content-end list-user-action">
                            <a  href="javascript:void(0)" class="bt-all btn-success qrcode"  data-id="<?= $store_page_url.'/?id='.$userdetails['id'] ?>">
                              <i class="fas fa-walkie-talkie"></i>
                            </a>
                            <a class="bt-all btn-warning" href="javascript:void(0)" copyToClipboard="<?= $store_page_url.'/?id='.$userdetails['id'] ?>" title="<?= __('user.copied'); ?>">
                              <span class="btn-inner">
                                <i class="far fa-copy" alt="<?= __('user.copy') ?>"></i>
                              </span>
                            </a>
                            <a  href="javascript:void(0)" class="bt-all btn-danger dashboard-model-slug" data-type="store" data-related-id="0" data-input-class="input-store-url-0">
                              <span class="btn-inner">
                                <i class="fas fa-cog" alt="<?= __('user.setting') ?>"></i>
                              </span>
                            </a>
                            <a class="bt-all btn-success" href="javascript:void(0)" data-social-share data-share-url="<?= $share_url; ?>?id=<?= $userdetails['id'] ?>" data-share-title="" data-share-desc="">
                              <span class="ms-1">
                                <i class="fa-solid fa-share-from-square" alt="<?= __('user.share') ?>"></i>
                              </span>
                            </a>
                          </div>
                        </div>
                        <?php }
                      } ?>
                      <?php if($refer_status && allowMarketVendorPanelSections($marketvendorpanelmode, $userdetails['is_vendor'])){
                        if($register_slug)
                          $share_url = base_url($register_slug);
                        else
                          $share_url = base_url('register/' . base64_encode($userdetails['id']));?>
                      <div class=" border-custom show-tiny-link <?php if($invitationlinkid==1) { echo 'd-none';} ?>">
                        <div class="form-group">
                          <label class="form-label"><?= __('user.your_unique_reseller_link') ?></label>

                          <input type="text" name="text" readonly="readonly" value="<?= $share_url ?>" class="input-register-url-0 form-control">
                        </div>
                        <div class="flex align-items-center justify-content-end list-user-action">
                          <a href="javascript:void(0)" class="bt-all btn-success qrcode"  data-id="<?= $share_url ?>">
                            <i class="fas fa-walkie-talkie"></i>
                          </a>

                          <a class="bt-all btn-warning" href="javascript:void(0)" copyToClipboard="<?= $share_url ?>" title="<?= __('user.copied'); ?>">
                            <span class="btn-inner">
                              <i class="far fa-copy" alt="<?= __('user.copy') ?>"></i>
                            </span>
                          </a>
                          <a  href="javascript:void(0)" class="dashboard-model-slug bt-all btn-danger" data-type="register" data-related-id="0" data-input-class="input-register-url-0">
                            <span class="btn-inner">
                              <i class="fas fa-cog" alt="<?= __('user.setting') ?>"></i>
                            </span>
                          </a>
                          <a  class="bt-all btn-success" href="javascript:void(0)" data-social-share data-share-url="<?= $share_url; ?>?id=<?= $userdetails['id'] ?>" data-share-title="" data-share-desc="">
                            <span class="ms-1">
                              <i class="fa-solid fa-share-from-square" alt="<?= __('user.share') ?>"></i>
                            </span>
                          </a>
                        </div>
                      </div>
                      <div class=" border-custom show-mega-link <?php if($invitationlinkid==0) { echo 'd-none';} ?>">
                        <div class="form-group">
                         
                          <label class="form-label"><?= __('user.your_unique_reseller_link') ?></label>
                          <input type="text" name="text" readonly="readonly" value="<?= $share_url.'/?id='.$userdetails['id'] ?>" class="input-register-url-0 form-control" data-addition-url="/?id=<?= $userdetails['id'] ?>">
                        </div>
                        <div class="flex align-items-center justify-content-end list-user-action">
                          <a href="javascript:void(0)" class="bt-all btn-success qrcode"  data-id="<?= $share_url.'/?id='.$userdetails['id'] ?>">
                            <i class="fas fa-walkie-talkie"></i>
                          </a>

                          <a class="bt-all btn-warning" href="javascript:void(0)" copyToClipboard="<?= $share_url.'/?id='.$userdetails['id'] ?>" title="<?= __('user.copied'); ?>">
                            <span class="btn-inner">
                              <i class="far fa-copy" alt="<?= __('user.copy') ?>"></i>
                            </span>
                          </a>
                          <a  href="javascript:void(0)" class="dashboard-model-slug bt-all btn-danger" data-type="register" data-related-id="0" data-input-class="input-register-url-0">
                            <span class="btn-inner">
                              <i class="fas fa-cog" alt="<?= __('user.setting') ?>"></i>
                            </span>
                          </a>
                          <a  class="bt-all btn-success" href="javascript:void(0)" data-social-share data-share-url="<?= $share_url; ?>?id=<?= $userdetails['id'] ?>" data-share-title="" data-share-desc="">
                            <span class="ms-1">
                              <i class="fa-solid fa-share-from-square" alt="<?= __('user.share') ?>"></i>
                            </span>
                          </a>
                        </div>
                      </div>
                      <?php } ?>

                      <?php } ?>
                    </div>
                  </div>
                </div>
                
                <?php 
                  $googleAdsRightSide = $this->Setting_model->getGoogleAds(4,1);
                  if(!empty($googleAdsRightSide)){
                  ?>
                  <div class="col-xl-12 col-lg-12 col-md-12 mt-3">
                    <div class="googleadd-bg text-center">
                      Google Add Here
                      <ins class="adsbygoogle"
                         style="display:block"
                         data-ad-client="<?= @$googleAdsRightSide[0]['client_key']?>"
                         data-ad-slot="<?= @$googleAdsRightSide[0]['unit_key']?>"
                         data-ad-format="auto"
                         data-full-width-responsive="true"></ins>
                        <script>
                             (adsbygoogle = window.adsbygoogle || []).push({});
                        </script>
                     </div>
                  </div>
                <?php }?>
                <?php if(isShowUserControlParts($userdashboard_settings['top_affiliate']) && allowMarketVendorPanelSections($marketvendorpanelmode, $userdetails['is_vendor'])){ ?>
                <div class="col-md-12 col-lg-12">
                  <div class="card ">
                    <h4 class="card-title my-3 ms-3"><?= __('user.popular_affiliates') ?></h4>
                    <div class="card-header  bg-color">
                      <h4 class="header-title font-title"> <?= __('user.name') ?> <?= __('user.country') ?>& <?= __('user.commission') ?> </h4>
                    </div>
                    <div class="card-body">
                      <?php foreach ($populer_users as $key => $users){
                        $flag = '';
                        if($users['sortname'] != '')
                          $flag = base_url('assets/vertical/assets/images/flags/' . strtolower($users['sortname']) . '.png'); ?>
                        <div class="d-flex flex-wrap align-items-center justify-content-between" >
                        <div class="col-sm-4 col-lg-4" style="text-align: left;">
                          <?php $username=$users['firstname'].' '.$users['lastname'];
                          if(strlen($username) > 9){
                            $username=substr($username, 0,9);
                          }else{
                            $username=$username;
                          }
                          ?>
                          <p class="p-mw">
                            <img src="<?= $products->getAvatar($users['avatar']) ?>" alt="" class="img-fluid rounded-pill avatar-30 me-2"><?= $username; ?>
                          </p>
                        </div>
                        <div class="col-sm-4 col-lg-4" style="text-align: center;">
                          <img src="<?= $flag; ?>" alt="<?= __('user.flag') ?>" class="img-fluid rounded-pill avatar-20 me-2">
                        </div>
                        <div class="col-sm-4 col-lg-4" style="text-align: right;">
                          <span class="text-danger"><?= $fun_c_format($users['all_commition']); ?></span>
                        </div>
                      </div>
                      
                      <?php } ?>  
                    </div>
                  </div>
                </div>
                <?php } ?> 
              </div>
            </div>
          </div>
        </div>

      <?php 
      $googleAdsFooter = $this->Setting_model->getGoogleAds(3,1);
      if(!empty($googleAdsFooter)){
      ?>
      <div class="col-xl-12 col-lg-12 col-md-12 mb-3">
        <div class="googleadd-bg text-center">
          Google Add Here
          <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="<?= @$googleAdsFooter[0]['client_key']?>"
             data-ad-slot="<?= @$googleAdsFooter[0]['unit_key']?>"
             data-ad-format="auto"
             data-full-width-responsive="true"></ins>
            <script>
                 (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
         </div>
      </div>
    <?php }?>

      <div class="modal fade" id="model-codemodal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered  scanner">
        <div class="modal-content ">
          <div class="modal-header">
            <h5 class="modal-title exampleModalLabel2"><?= __('user.scanner') ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body py-4">
            
            
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary text-center btn-green" data-bs-dismiss="modal" aria-label="Close"><?= __('user.footer_close') ?></button>
          </div>
        </div>
      </div>
  </div>


    <div class="modal fade" id="slugtting" data-backdrop="static" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content ">
          <form action="<?= base_url('/usercontrol/create_slug') ?>" method="post">
          <div class="modal-header">
            <h5 class="modal-title exampleModalLabel1"><?= __('user.create_slug'); ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="slug-wrapp">
            <div class="form-group">
              
              <label class="form-label" for="exampleInputText1"><?= __('user.slug'); ?></label>
              <input type="text" name="slug" class="form-control"  placeholder="<?= __('user.enter_slug_here') ?>">
              <input type="hidden" name="type" />
              <input type="hidden" name="related_id" />
              <input type="hidden" name="target" />
            
            </div>
            <div class="link-area align-items-center slug-url">
              <input type="text" readonly="readonly" class="form-control" >
              <a class="bt-all btn-warning" href="javascript:void(0)" title="<?= __('user.copied'); ?>" style="margin-left: 5px;">
                <span class="btn-inner">
                  <i class="far fa-copy" alt="<?= __('user.copy') ?>"></i>
                </span>
              </a> 
            </div>
          </div>
        </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-secondary"><?= __('user.create'); ?></button>
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal" aria-label="Close"><?= __('user.close'); ?></button>
           <button type="button" class="btn btn-primary btn-delete-slug"><?= __('user.delete'); ?></button>
          </div>
        </form>
        </div>
      </div>
    </div>

    <?= $social_share_modal ?>
    <?php if(isset($welcome['show_popup']) && $welcome['show_popup']=='enable' ) { ?>
<div id="welcome-model" class="modal fade" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center w-100"><?= $welcome['heading'] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="ratio ratio-16x9 text-center">
                    <iframe class="embed-responsive-item" allowfullscreen id="ifrm_id"></iframe>
                </div>
                <br/>
                <div style="max-height: 150px; overflow:auto;">
                    <div>
                        <?php echo nl2br($welcome['content']); ?>
                    </div>
                </div>
                <div id="video-placeholder" class="text-center" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                    <i class="fas fa-exchange-alt fa-5x text-muted"></i>
                    <h3 class="text-muted"><?= __('admin.no_data_found') ?></h3>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" aria-label="Close"><?= __('user.close') ?></button>
                <button type="button" class="btn btn-primary" onclick="return closeWelcomePopup()"><?= __('user.close_forever') ?></button>
            </div>
        </div>
    </div>
</div>




<div class="modal fade" id="integration-code">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>


<script type="text/javascript">
  function loadIframe(iframeName, url) {
    var $iframe = $('#' + iframeName);
    if ( $iframe.length ) {
        $iframe.attr('src',url);   
        return false;
    }
    return true;
}
   $(document).ready(function() { 

     
});
   
$(window).on('load', function() {
    var welcome_popup = localStorage.getItem("welcome_popup_" + <?= $userdetails['id']; ?>);
    if (welcome_popup != 1) {
        $("#welcome-model").modal('show');
        var url = '<?= $welcome['video_link'] ?>';

        if (url) {
            if (url.toLowerCase().includes("youtube") && !url.toLowerCase().includes("embed")) {
                $id = url.split("v=");
                url = 'https://www.youtube.com/embed/' + $id[1];
            } else if (url.toLowerCase().includes("youtu") && !url.toLowerCase().includes("embed")) {
                $id = url.split("/");
                url = 'https://www.youtube.com/embed/' + $id[3];
            }
            loadIframe('ifrm_id', url);
        } else {
            // No video available, display placeholder
            $("#video-placeholder").show();
        }
    }
});

   function closeWelcomePopup()
   { 
    var ans=confirm('<?=__('user.are_you_sure');?>');
    if(ans)
      localStorage.setItem("welcome_popup_"+<?=$userdetails['id'];?>, 1);

    $("#welcome-model").modal('hide');   
   }
 </script>

<?php } ?>


  <script>
    function renderDashboardChart(chartData,symbol_left,symbol_right){
      var months = Object.values(chartData['keys']);
      if (jQuery('#d-main').length) {
        
        var serieslist=[
          {
                  name: '<?= __('user.action_count') ?>',
                  data: Object.values(chartData['action_count'])
              }, {
                  name: '<?= __('user.order_count') ?>',
                  data:  Object.values(chartData['order_count'])
              }, {
                  name: '<?= __('user.order_commission') ?>',
                  data: Object.values(chartData['order_commission'])
              }, {
                  name: '<?= __('user.action_commission') ?>',
                  data: Object.values(chartData['action_commission'])
              }, {
                  name: '<?= __('user.order_total') ?>',
                  data: Object.values(chartData['order_total'])
              }
          ];
       // console.log(serieslist);
       
          const options = {
              series: serieslist,
              chart: {
                  fontFamily: '"Inter", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"',
                  height: 400,
                  type: 'area',
                  toolbar: {
                      show: false
                  },
                  sparkline: {
                      enabled: false,
                  },
              },
              noData: {
              text: 'No data available',
              align: 'center',
              verticalAlign: 'middle',
              offsetX: 0,
              offsetY: 0,
              style: {
                fontSize: '18px',
                color: '#5ec394 '
              }
            },
              colors: ["#36a2eb", "#ffcd56","#1dc9b7","#4bc0c0","#fd397a"],
              dataLabels: {
                  enabled: false
              },
              stroke: {
                  curve: 'smooth',
                  width: 3,
              },
              yaxis: {
                show: true,
                labels: {
                  show: true,
                  minWidth: 19,
                  maxWidth: 40,
                  style: {
                    colors: "#8A92A6",
                  },
                formatter: function (val) {

                    return val.toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
                  },

                  offsetX: -5,
                         
                },
              },
              legend: {
                  show: false,
              },
              xaxis: {
                  labels: {
                      minHeight:22,
                      maxHeight:60,
                      show: true,
                      style: {
                        colors: "#8A92A6",
                      },
                  },
                  lines: {
                      show: false  //or just here to disable only x axis grids
                  },
                  categories: months
              },
              grid: {
                  show: false,
              },
              fill: {
                  type: 'gradient',
                  gradient: {
                      shade: 'dark',
                      type: "vertical",
                      shadeIntensity: 0,
                      gradientToColors: undefined, // optional, if not defined - uses the shades of same color in series
                      inverseColors: true,
                      opacityFrom: .4,
                      opacityTo: .1,
                      stops: [0, 50, 80],
                      colors: ["#0e1133", "#4bc7d2"]
                  }
              },
              tooltip: {
                enabled: true,
                y: {
                  formatter: function(val, { seriesIndex, dataPointIndex }) {
                    if (seriesIndex == 2 || seriesIndex == 3 || seriesIndex == 4) {
                      if(symbol_left != ""){
                        return symbol_left + val;
                    }else{
                      return symbol_right + val;
                    }
                      
                    } else {
                      return val;
                    }
                  }
                  
                }
              },
              
          };
          var count=0;
          for (var i = 0; i < serieslist.length; i++) {
            var dataArr = serieslist[i].data;
            if (dataArr.some(function(value) {
              return value > 0;
            })) {
              count++;
            }
          }
          console.log(count);
          if(count == 0){
             options.series = [];
          }
          var chart = new ApexCharts(document.querySelector("#d-main"), options);

          chart.render();
          chart.updateOptions({
            chart: {
              flush: false // or flush: true
            }
          });
        }
    }
    function isInt(value) {
        var er = /^-?[0-9]+$/;
        return er.test(value);
    }
    function loadDashboardChart(){
      $.ajax({
        url:'<?= base_url("usercontrol/dashboard?getChartData=1") ?>',
        type:'POST',
        dataType:'json',
        data:$(".chart-input"),
        beforeSend:function(){},
        complete:function(){},
        success:function(json){
          if(json['chart']){
            $("#dashboard-chart-empty").addClass('d-none');
           $("#dashboard-chart").removeClass('d-none');
           $("#tottal_order_sum").text(json['chart']['order_total_sum']);
            renderDashboardChart(json['chart'],json.symbol.symbol_left,json.symbol.symbol_right);
          } else {
            $("#dashboard-chart-empty").removeClass('d-none');
            $("#dashboard-chart").addClass('d-none');
          }
        },
      })
    }
    $( document ).ready(function() {
     loadDashboardChart();

    });
  </script>
                
<script type="text/javascript">
  $("#show_my_id").change(function(){
    if($(this).prop("checked")){
      $(".show-mega-link").removeClass("d-none");
      $(".show-tiny-link").addClass("d-none");
    } else {
      $(".show-mega-link").addClass("d-none");
      $(".show-tiny-link").removeClass("d-none");
    }
  })

  function setColors() {
    $(".set-color").each(function(i,ele){
      var val =  parseInt($(ele).html().toString().replace(/[^0-9-.]/g, '') || 0);

      $(ele).removeClass("text-primary")
      $(ele).removeClass("text-danger")
      if(val >= 0){
        $(ele).addClass("text-primary");
      } else{
        $(ele).addClass("text-danger");
      }
    })
  }

  setColors();

  $(".card-toggle .open-close-button").click(function(){
    $(this).parents(".card-toggle").toggleClass("open")
  });

  function generateCode(affiliate_id,t){
    $this = $(t);
    $.ajax({
      url:'<?= base_url('usercontrol/generateproductcode/') ?>'+affiliate_id,
      type:'POST',
      dataType:'html',
      success:function(json){
        $('#model-codemodal .modal-body').html(json)
        $("#model-codemodal").modal("show")
      },
    })
  }

  function generateCodeForm(form_id,t){ 
    $this = $(t);
    $.ajax({
      url:'<?= base_url('usercontrol/generateformcode/') ?>'+form_id,
      type:'POST',
      dataType:'html',
      success:function(json){
        $('#model-codeformmodal .modal-body').html(json)
        $("#model-codeformmodal").modal("show")
      },
    })
  }

  $(document).delegate(".get-code",'click',function(){
    $this = $(this);
    $.ajax({
      url:'<?= base_url('integration/tool_get_code/usercontrol') ?>',
      type:'POST',
      dataType:'json',
      data:{id:$this.attr("data-id")},
      success:function(json){
        if(json['html']){
          $("#integration-code .modal-content").html(json['html']);
          $("#integration-code").modal("show");
        }
      },
    })
  })

  $(document).delegate(".get-terms",'click',function(){
    $this = $(this);
    $.ajax({
      url:'<?= base_url('integration/tool_get_terms/usercontrol') ?>',
      type:'POST',
      dataType:'json',
      data:{id:$this.attr("data-id")},
      success:function(json){
        if(json['html']){
          $("#integration-code .modal-content").html(json['html']);
          $("#integration-code").modal("show");
        }
      },
    })
  });
</script>