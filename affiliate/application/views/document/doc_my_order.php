<!-- start my order -->
<div class="row">
   <div class="col-md-12">
      <div class="page-intro" id="my_order">
         <h2 class="page-title">My Order</h2>
         <p> All order list, payment details, with order information and it's status details available in order section. also display product information and prodcut commision details.</p>
      </div>
      <div class="top-content" id="my_order_list">
         <h3 class="page-title">My Order List</h3>
         <p>
            Perticular withdraw request with HTTP POST request.
         </p>
         <p>
            API token is required for the authentication of the calling program to the API.
         </p>
         <p>
            You can set filter as order status wise.
         </p>
         <p>
            Here product order list data display as pagignation wise. only data get using pass page_id and per_page parameter. <br>
            page_id is index of your page so you can pass your page id. <br>
            Ex. : display on first page pass 1, display on second page pass 2 <br>
            per_page is count of data display of per page. <br>
         </p>
      </div>
   </div>
</div>
<!-- start my order list -->
<div class="row">
   <div class="col-md-6">
      <!-- TABLE HOVER -->
      <div class="panel white">
         <div class="panel-heading">
            <h3 class="panel-title">Request :  </h3>
            <br>
            <span class="text-warning">POST</span> : <?=base_url();?>Order/my_orders_list</p>
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
                     <td>filter_status</td>
                     <td><code>string</code></td>
                     <td><code>Body</code></td>
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
            <pre class="response-view"><?= htmlspecialchars('{"status":true,"message":"order list get successfully","data":{"orders":[{"id":"3","wallet_status":"2","wallet_commission_status":"0","type":"ex","order_id":"757","product_ids":"119","total":"114","currency":"USD","user_id":"64","commission_type":"fixed","commission":"10","ip":"::1","country_code":"","base_url":"localhost\/test\/store1.php","ads_id":"5","script_name":"general_integration","custom_data":"[]","created_at":"12-06-2021 10:58 AM","user_name":"aff12 aff12","order_country_flag":"<img style=\'width: 20px;margin: 0 10px;\' src=\'http://localhost/aff/4-0-0-7/assets/vertical/assets/images/flags/us.png\'> IP: 192.168.1.21"},{"id":"1","wallet_status":"2","wallet_commission_status":"0","type":"ex","order_id":"133","product_ids":"169","total":"105","currency":"USD","user_id":"64","commission_type":"fixed","commission":"10","ip":"192.168.1.21","country_code":"","base_url":"localhost/test/store1.php","ads_id":"5","script_name":"general_integration","custom_data":"[]","created_at":"09-06-2021 04:27 PM","user_name":"aff12 aff12","order_country_flag":"<img style=\'width: 20px;margin: 0 10px;\' src=\'http://localhost/aff/4-0-0-7/assets/vertical/assets/images/flags/us.png\'> IP: 192.168.1.21"}],"start_from":1,"wallet_status":["Pending","Complete","Proccessing","Cancel","Decline"]}}'); ?></pre>
         </div>
      </div>
      <!-- END TABLE HOVER -->
   </div>
</div>
<!-- end my order list -->
<!-- start order list status -->
<div class="top-content" id="my_order_status_list">
   <h3 class="page-title">My Order List Status</h3>
   <p>
      Perticular withdraw request with HTTP POST request.
   </p>
   <p>
      API token is required for the authentication of the calling program to the API.
   </p>
   <p>
      Display all order status and it's used for search in order list.
   </p>
</div>
<div class="row">
   <div class="col-md-6">
      <!-- TABLE HOVER -->
      <div class="panel white">
         <div class="panel-heading">
            <h3 class="panel-title">Request :  </h3>
            <br>
            <span class="text-success">GET</span> : <?=base_url();?>Order/my_orders_status_list</p>
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
            <pre class="response-view">{"status":true,"message":"my orders status list get successfully","data":{"12":"Waiting For Payment","0":"Waiting For Payment","1":"Complete","2":"Total not match","3":"Denied","4":"Expired","5":"Failed","6":"Pending","7":"Processed","8":"Refunded","9":"Reversed","10":"Voided","11":"Canceled Reversal"}}</pre>
         </div>
      </div>
      <!-- END TABLE HOVER -->
   </div>
</div>
<!-- end order list status -->
<!-- end my order -->