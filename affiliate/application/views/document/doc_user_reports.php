<!-- start get user reports -->
<div class="top-content" id="get_user_reports">
   <h3 class="page-title">Get User Reports</h3>
   <p>
      Get user reports with HTTP POST request.
   </p>
   <p>
      API token is required for the authentication of the calling program to the API.
   </p>
   <p>
      Here user reports list data display as pagignation wise. only data get using pass page_id and per_page parameter. <br>
      page_id is index of your page so you can pass your page id. <br>
      Ex. : display on first page pass 1, display on second page pass 2 <br>
      per_page is count of data display of per page. <br>
   </p>
   <p>In this statistics totals and transaction available</p>
   <p>
      1) data display click by country, Action click by country, sale by country, refered user by country, client by country
   </p>
   <p>
      2) display all transaction data as pagignation wise 
   </p>
   <p>
      3) display all reports statistics data 
   </p>
</div>
<div class="row">
   <div class="col-md-6">
      <!-- TABLE HOVER -->
      <div class="panel white">
         <div class="panel-heading">
            <h3 class="panel-title">Request :  </h3><br>
            <span class="text-warning">POST</span> : <?=base_url();?>User_Reports/get_user_reports</p>
         </div>
         <div class="panel-body">
            <table class="table table-hover">
               <thead>
                  <tr>
                     <th>Parameter</th>
                     <th>Type</th>
                     <th>Position</th>
                     <th>Description</th>
                  </tr>
               </thead>
               <tbody>
                  <tr>
                     <td>page_id</td>
                     <td><code>number</code></td>
                     <td><code>Body</code></td>
                     <td>pass your page number index for data display as pagignation wise</td>
                  </tr>
                  <tr>
                     <td>per_page</td>
                     <td><code>number</code></td>
                     <td><code>Body</code></td>
                     <td>you want to set count for data display</td>
                  </tr>
               </tbody>
            </table>
         </div>
      </div>
      <!-- END TABLE HOVER -->
   </div>
   <div class="col-md-6">
      <!-- TABLE HOVER -->
      <div class="panel black">
         <div class="panel-heading">
            <h3 class="panel-title">Response</h3>
         </div>
         <div class="panel-body">
            <pre class="response-view">{"status":true,"message":"get user reports details successfully","data":{"refer_status":true,"statistics":{"sale":[],"clicks":[]},"totals":{"unpaid_commition":10,"total_sale_commi":0,"total_in_request":0,"total_click_commi":0,"total_form_click_commi":0,"total_store_m_commission":0,"total_affiliate_click_commission":0,"total_no_click":0,"total_no_form_click":0,"aff_total_no_click":0,"admin_click_earning":0,"all_clicks_comm":0,"all_sale_comm":0,"affiliates_program":0,"total_sale_count":0,"total_sale":0,"total_vendor_sale":0,"total_sale_balance":0,"total_sale_week":0,"total_sale_month":0,"total_sale_year":0,"admin_click_earning_week":0,"admin_click_earning_month":0,"admin_click_earning_year":0,"admin_total_no_click":1,"all_clicks":0,"vendor_order_count":0,"total_paid":0,"total_paid_commition":0,"paid_commition":0,"requested_commition":0,"aff_paid_commition":0,"aff_unpaid_commition":0,"aff_requested_commition":0,"form_paid_commition":0,"form_unpaid_commition":0,"form_requested_commition":0,"total_transaction":1,"wallet_cancel_count":0,"wallet_cancel_amount":0,"wallet_accept_count":0,"wallet_accept_amount":0,"wallet_request_sent_count":0,"wallet_request_sent_amount":0,"wallet_on_hold_count":0,"wallet_on_hold_amount":0,"wallet_unpaid_amount":10,"wallet_unpaid_count":1,"integration":{"hold_action_count":0,"hold_orders":0,"total_commission":0},"admin_transaction":null,"store":{"hold_orders":0,"balance":0,"sale":0,"click_count":0,"click_amount":0,"total_commission":0},"total_sale_amount":0,"total_balance":0,"weekly_balance":0,"monthly_balance":0,"yearly_balance":0},"transaction":[]}}</pre>
         </div>
      </div>
      <!-- END TABLE HOVER -->
   </div>
</div>
<!-- end get user reports -->