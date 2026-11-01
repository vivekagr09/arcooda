<!-- start notification -->
<div class="row">
   <div class="col-md-12">
      <div class="page-intro" id="notification">
         <h2 class="page-title">Notification</h2>
         <p>
            Notification section have all notification list available as notification type wise. you can manage you notification here.
         </p>
      </div>
      <div class="top-content" id="notification_list">
         <h3 class="page-title">Notification List</h3>
         <p>
            Notification list request with POST request.
         </p>
         <p>
            API token is required for the authentication of the calling program to the API.
         </p>
         <p>
            Here notification list data display as pagignation wise. only data get using pass page_id and per_page parameter. <br>
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
            <h3 class="panel-title">Request :  </h3>
            <br>
            <span class="text-warning">POST</span> : <?=base_url();?>Notification/notification_list</p>
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
            <pre class="response-view">{ "status": true, "message": "notification list get successfully", "data": [ { "notification_id": "117", "notification_viewfor": "user", "notification_view_user_id": "3", "notification_title": "New Order Generated With Your Vendor Product by freshclient", "notification_url": "/vieworder/3", "notification_description": "fresh client Last client name created a new order with your vendor product at 2021-04-10 16:31:58", "notification_actionID": "3", "notification_type": "order", "notification_is_read": "0", "notification_ipaddress": "::1", "notification_created_date": "2021-04-10 16:31:58" } ] }</pre>
      </div>
   </div>
   <!-- END TABLE HOVER -->
</div>
</div>
<!-- end notification -->
<!-- start delete notification -->
<div class="top-content" id="delete_notifications">
   <h3 class="page-title">Delete Notifications</h3>
   <p>
      Delete notifications request with POST request.
   </p>
   <p>
      API token is required for the authentication of the calling program to the API.
   </p>
   <p>
      Using this api you can remove your notification. in this api you can pass id in query string
   </p>
</div>
<div class="row">
   <div class="col-md-6">
      <!-- TABLE HOVER -->
      <div class="panel white">
         <div class="panel-heading">
            <h3 class="panel-title">Request :  </h3>
            <br>
            <span class="text-danger"> DELETE</span> : <?=base_url();?>Notification/delete_notifications</p>
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
                     <td>delete_ids[]</td>
                     <td><code>string</code></td>
                     <td><code>Body</code></td>
                     <td>pass multiple ids in array</td>
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
            <pre class="response-view">{ "status": true, "message": "notification delete successfully" }</pre>
         </div>
      </div>
      <!-- END TABLE HOVER -->
   </div>
</div>
<!-- end delete notification -->