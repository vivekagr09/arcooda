<!-- start my network -->
<div class="top-content" id="my_network">
   <h3 class="page-title">My Network</h3>
   <p>
      My network with HTTP POST request.
   </p>
   <p>
      API token is required for the authentication of the calling program to the API.
   </p>
   <p>
      Here display your affiliate tree. <br>
      in this all your child user tree display. also display child user commision and cliks details.
   </p>
</div>
<div class="row">
   <div class="col-md-6">
      <!-- TABLE HOVER -->
      <div class="panel white">
         <div class="panel-heading">
            <h3 class="panel-title">Request :  </h3><br>
            <span class="text-success">GET</span> : <?=base_url();?>My_Network/my_network</p>
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
            <pre class="response-view">{ "status": true, "message": "my network get successfully", "data": { "userslist": [ { "name": "System Admin", "children": [ { "name": "test", "children": [ { "name": "user12", "children": [] } ] } ] } ], "refer_total": { "total_product_click": { "amounts": null, "clicks": 0 }, "total_ganeral_click": { "total_clicks": "0" }, "total_action": { "click_count": "0" }, "total_product_sale": { "amounts": null, "counts": "0", "paid": null, "request": null, "unpaid": null } }, "referred_users_tree": [ { "title": "user12 user12", "email": "user12@gmail.com", "click": 0, "external_click": 0, "form_click": 0, "aff_click": 0, "click_commission": "$0.00", "external_action_click": 0, "action_click_commission": "$0.00", "amount_external_sale_amount": "$0.00", "sale_commission": "$0.00", "paid_commition": "$0.00", "unpaid_commition": "$0.00", "in_request_commiton": "$0.00", "all_commition": "$0.00", "children": [] } ] } }</pre>
         </div>
      </div>
      <!-- END TABLE HOVER -->
   </div>
</div>
<!-- end my network -->