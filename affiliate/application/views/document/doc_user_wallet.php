<!-- start my wallet -->
<div class="row">
   <div class="col-md-12">
      <div class="page-intro" id="my_wallet">
         <h2 class="page-title">My Wallet</h2>
         <p>
            Here manage user wallet in this includes clicks, balance, sales , withdraw request etc.
         </p>
      </div>
      <div class="top-content" id="my_transaction">
         <h3 class="page-title">My Transaction</h3>
         <p>
            My transaction with HTTP POST request.
         </p>
         <p>
            API token is required for the authentication of the calling program to the API.
         </p>
         <p>
            Here display all my wallet transaction with filter perameter. filter available filter by type and filter by paid type. this type is fixed for filter.
         </p>
         <p>
            Here user reports list data display as pagignation wise. only data get using pass page_id and per_page parameter. <br>
            page_id is index of your page so you can pass your page id. <br>
            Ex. : display on first page pass 1, display on second page pass 2 <br>
            per_page is count of data display of per page. <br>
         </p>
      </div>
   </div>
</div>
<div class="row">
   <div class="col-md-6">
      <!-- TABLE HOVER -->
      <div class="panel white">
         <div class="panel-heading">
            <h3 class="panel-title">Request :  </h3><br>
            <p><span class="text-warning">POST</span> : <?=base_url();?>My_Wallet/my_wallet</p>
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
                  <tr>
                     <td>paid_status</td>
                     <td><code>sting</code></td>
                     <td><code>Body</code></td>
                     <td>(optional parameter) paid OR unpaid</td>
                  </tr>
                  <tr>
                     <td>type</td>
                     <td><code>sting</code></td>
                     <td><code>Body</code></td>
                     <td>(optional parameter) actions OR clicks OR sale OR external_integration</td>
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
            <pre class="response-view">{"status":true,"message":"my wallet list get successfully","data":{"user_totals":{"click_localstore_total":0,"click_localstore_commission":null,"sale_localstore_total":0,"sale_localstore_commission":0,"sale_localstore_count":0,"click_external_total":0,"click_external_commission":null,"order_external_total":null,"order_external_count":"0","order_external_commission":null,"click_action_total":0,"click_action_commission":null,"vendor_click_localstore_total":0,"vendor_click_localstore_commission_pay":null,"vendor_sale_localstore_total":null,"vendor_sale_localstore_commission_pay":null,"vendor_sale_localstore_count":null,"vendor_click_external_total":0,"vendor_click_external_commission_pay":null,"vendor_action_external_total":0,"vendor_action_external_commission_pay":null,"vendor_order_external_commission_pay":null,"vendor_order_external_count":"0","vendor_order_external_total":null,"click_form_total":0,"click_form_commission":null,"wallet_unpaid_amount":10,"wallet_unpaid_count":1,"user_balance":10},"wallet_unpaid_amount":10,"transaction":[{"id":"28","user_id":"88","from_user_id":null,"amount":"10","comment":"Welcome Bonus","type":"welcome_bonus","dis_type":null,"status":"1","commission_status":"0","reference_id":"41","reference_id_2":null,"ip_details":"[{\"ip\":null,\"country_code\":\"\"}]","comm_from":"store","domain_name":null,"page_name":null,"is_action":"0","parent_id":"0","group_id":"0","is_vendor":"0","wv":null,"created_at":"2021-06-11 15:20:14","username":"fresh_user2","firstname":"test","lastname":"user","wallet_recursion_id":null,"wallet_recursion_status":null,"wallet_recursion_type":null,"wallet_recursion_custom_time":null,"wallet_recursion_next_transaction":null,"wallet_recursion_endtime":null,"payment_method":null,"integration_orders_total":null,"local_orders_total":null,"total_recurring":"0","total_recurring_amount":null}]}}</pre>
         </div>
      </div>
      <!-- END TABLE HOVER -->
   </div>
</div>
<!-- start recurring transactions -->
<div class="top-content" id="recurring_transaction_list">
   <h3 class="page-title">Recursion Transaction List</h3>
   <p>
      My transaction with HTTP POST request.
   </p>
   <p>
      API token is required for the authentication of the calling program to the API.
   </p>
   <p>
      here display transaction recursion list
   </p>
</div>
<div class="row">
   <div class="col-md-6">
      <!-- TABLE HOVER -->
      <div class="panel white">
         <div class="panel-heading">
            <h3 class="panel-title">Request :  </h3><br>
            <p><span class="text-success">POST</span> : <?=base_url();?>My_Wallet/getRecurringTransaction</p>
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
                     <td>transaction_id</td>
                     <td><code>number</code></td>
                     <td><code>Body</code></td>
                     <td>compolsory pass this transaction ID</td>
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
            <pre class="response-view"><?= htmlspecialchars('{"status":true,"message":"transaction recursion list get successfully","data":{"transaction":[{"id":"78","user_id":"10","from_user_id":"0","amount":"1","comment":"Commission for 1 Action On localhost\/aff\/test\/admin_action.php |  Action Code : admin_action  <br> Clicked done from ip_message","type":"external_click_commission","dis_type":"banner","status":"1","commission_status":"0","reference_id":"2","reference_id_2":"admin_action","ip_details":"[{\"id\":\"6\",\"ip\":\"::1\",\"country_code\":\"\",\"script_name\":\"general_integration\",\"page_name\":\"\"}]","comm_from":"ex","domain_name":"localhost\/aff\/test\/admin_action.php","page_name":"","is_action":"1","parent_id":"66","group_id":"167955256314","is_vendor":"0","wv":null,"created_at":"2023-03-23 11:17:45","username":"ven2","firstname":"ven2","lastname":"ven2","usertype":"user","wallet_recursion_id":null,"wallet_recursion_status":null,"wallet_recursion_type":null,"wallet_recursion_custom_time":null,"wallet_recursion_next_transaction":null,"wallet_recursion_endtime":null,"payment_method":"0","integration_orders_total":null,"local_orders_total":null,"total_recurring":"0","total_recurring_amount":null,"has_recursion_records":null},{"id":"72","user_id":"10","from_user_id":"0","amount":"1","comment":"Commission for 1 Action On localhost\/aff\/test\/admin_action.php |  Action Code : admin_action  <br> Clicked done from ip_message","type":"external_click_commission","dis_type":"banner","status":"1","commission_status":"0","reference_id":"2","reference_id_2":"admin_action","ip_details":"[{\"id\":\"6\",\"ip\":\"::1\",\"country_code\":\"\",\"script_name\":\"general_integration\",\"page_name\":\"\"}]","comm_from":"ex","domain_name":"localhost\/aff\/test\/admin_action.php","page_name":"","is_action":"1","parent_id":"66","group_id":"167955256314","is_vendor":"0","wv":null,"created_at":"2023-03-23 10:45:49","username":"ven2","firstname":"ven2","lastname":"ven2","usertype":"user","wallet_recursion_id":null,"wallet_recursion_status":null,"wallet_recursion_type":null,"wallet_recursion_custom_time":null,"wallet_recursion_next_transaction":null,"wallet_recursion_endtime":null,"payment_method":"0","integration_orders_total":null,"local_orders_total":null,"total_recurring":"0","total_recurring_amount":null,"has_recursion_records":null}]}}'); ?>
            </pre>
         </div>
      </div>
      <!-- END TABLE HOVER -->
   </div>
</div>


<!-- start withdraw request list -->
<div class="top-content" id="withdraw_request_list">
   <h3 class="page-title">Withdraw Request List</h3>
   <p>
      My transaction with HTTP POST request.
   </p>
   <p>
      API token is required for the authentication of the calling program to the API.
   </p>
   <p>
      here display new and old request data list
   </p>
</div>
<div class="row">
   <div class="col-md-6">
      <!-- TABLE HOVER -->
      <div class="panel white">
         <div class="panel-heading">
            <h3 class="panel-title">Request :  </h3><br>
            <p><span class="text-success">GET</span> : <?=base_url();?>Withdraw_Request/withdraw_request_list</p>
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
                     <td>Authorization</td>
                     <td><code>string</code></td>
                     <td><code>Header</code></td>
                     <td>compolsory pass this authentication token in your header also get from login api</td>
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
            <pre class="response-view"><?= htmlspecialchars('{"status":true,"message":"withdraw request list get successfully","data":{"list":[{"id":"2","tran_ids":"1","total":"5","status":"1","user_id":"64","prefer_method":"bank_transfer","settings":"[]","created_at":"2021-06-12 14:32:04"},{"id":"1","tran_ids":"29,14,2","total":"21","status":"0","user_id":"64","prefer_method":"bank_transfer","settings":"[]","created_at":"2021-06-12 14:29:50"}],"status":["ON HOLD","IN WALLET","REQUEST SENT","ACCEPT","DECLINE"],"status_icon":[["<small style=\'font-size:15px;\' class=\'badge badge-danger\'>ON HOLD</small>","<small style=\'font-size:15px;\' class=\'badge badge-primary\'>IN WALLET</small>","<small style=\'font-size:15px;\' class=\'badge badge-warning\'>REQUEST SENT</small>","<small style=\'font-size:15px;\' class=\'badge badge-success\'>ACCEPT</small>","<small style=\'font-size:15px;\' class=\'badge badge-danger\'>DECLINE</small>"]],"payout_transaction":[{"id":  "1", "user_id":  "3", "from_user_id":  null,"amount":  "29.75","comment":  "Commission for general_integration | external_order_id 426 | Sale done from ip_message","type":  "sale_commission","dis_type":  null,"status":  "0","reference_id":  "6","reference_id_2":  "1","ip_details":  "[{\"ip\":\"::1\",\"country_code\":\"\",\"script_name\":\"general_integration\"}]","comm_from":  "ex","domain_name":  "localhost/test/order.php","page_name":  null,"is_action":  "0","parent_id":  "0","group_id":  "1616082550","is_vendor":  "0","wv":  null,"created_at":  "2021-03-18 15 : 49 : 11","username":  "user1","firstname":  "user1","lastname":  "user1","wallet_recursion_id":  null,"wallet_recursion_status":  null,"wallet_recursion_type":  null,"wallet_recursion_custom_time":  null,"wallet_recursion_next_transaction":  null,"wallet_recursion_endtime":  null,"payment_method":  null,"integration_orders_total":  "119","local_orders_total":  null,"total_recurring":  "0","total_recurring_amount":  null}]}}'); ?>
            </pre>
         </div>
      </div>
      <!-- END TABLE HOVER -->
   </div>
</div>
<!-- end withdraw request list category -->
<!-- start withdraw request -->
<div class="top-content" id="withdraw_request">
   <h3 class="page-title">Withdraw Request</h3>
   <p>
      My transaction with HTTP POST request.
   </p>
   <p>
      API token is required for the authentication of the calling program to the API.
   </p>
   <p>
      Here you can pass ids in separated coma
   </p>
</div>
<div class="row">
   <div class="col-md-6">
      <!-- TABLE HOVER -->
      <div class="panel white">
         <div class="panel-heading">
            <h3 class="panel-title">Request :  </h3><br>
            <p><span class="text-warning">POST</span> : <?=base_url();?>Withdraw_Request/send_withdraw_request</p>
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
                     <td>ids</td>
                     <td><code>coma sepreted</code></td>
                     <td><code>Body</code></td>
                     <td>pass ids as coma seprated</td>
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
            <pre class="response-view">{"status":true,"message":"withdraw request send successfully"}</pre>
         </div>
      </div>
      <!-- END TABLE HOVER -->
   </div>
</div>
<!-- end send withdraw request -->
<!-- start perticular withdraw request -->
<!-- start withdraw request -->
<div class="top-content" id="perticular_withdraw_request_details">
   <h3 class="page-title">Perticular Withdraw Request</h3>
   <p>
      Perticular withdraw request with HTTP POST request.
   </p>
   <p>
      API token is required for the authentication of the calling program to the API.
   </p>
   <p>
      send withdraw request id (you can get id using withdraw request list)
   </p>
</div>
<div class="row">
   <div class="col-md-6">
      <!-- TABLE HOVER -->
      <div class="panel white">
         <div class="panel-heading">
            <h3 class="panel-title">Request :  </h3><br>
            <p><span class="text-warning">POST</span> : <?=base_url();?>Withdraw_Request/perticular_withdraw_request_details</p>
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
                     <td>id</td>
                     <td><code>number</code></td>
                     <td><code>Body</code></td>
                     <td>send withdraw request id (you can get using withdraw request list)</td>
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
            <pre class="response-view"><?= htmlspecialchars('{"status":true,"message":"perticular withdraw request details get successfully","data":{"request":{"id":"2","tran_ids":"1","total":"5","status":"1","user_id":"64","prefer_method":"bank_transfer","settings":"[]","created_at":"2021-06-12 14:32:04"},"transaction":[{"id":"1","user_id":"64","from_user_id":null,"amount":"5","comment":"Commission for 1 click on product <br> Clicked done from ip_message","type":"click_commission","dis_type":null,"status":"3","commission_status":"0","reference_id":"3","reference_id_2":"","ip_details":"[{\"ip\":\"::1\",\"country_code\":\"\"}]","comm_from":"store","domain_name":null,"page_name":null,"is_action":"0","parent_id":"0","group_id":"1623256015","is_vendor":"0","wv":"V2","created_at":"2021-06-09 16:26:56","username":"aff12","firstname":"aff12","lastname":"aff12","wallet_recursion_id":null,"wallet_recursion_status":null,"wallet_recursion_type":null,"wallet_recursion_custom_time":null,"wallet_recursion_next_transaction":null,"wallet_recursion_endtime":null,"payment_method":null,"integration_orders_total":null,"local_orders_total":null,"total_recurring":"0","total_recurring_amount":null}],"status":["ON HOLD","IN WALLET","REQUEST SENT","ACCEPT","DECLINE"],"status_icon":["<small style=\'font-size:15px;\' class=\'badge badge-danger\'>ON HOLD<\/small>","<small style=\'font-size:15px;\' class=\'badge badge-primary\'>IN WALLET<\/small>","<small style=\'font-size:15px;\' class=\'badge badge-warning\'>REQUEST SENT<\/small>","<small style=\'font-size:15px;\' class=\'badge badge-success\'>ACCEPT<\/small>","<small style=\'font-size:15px;\' class=\'badge badge-danger\'>DECLINE<\/small>"]}}')?></pre>
         </div>
      </div>
      <!-- END TABLE HOVER -->
   </div>
</div>
<!-- end withdraw request -->
<!-- end perticular withdraw request -->
<!-- end my wallet -->