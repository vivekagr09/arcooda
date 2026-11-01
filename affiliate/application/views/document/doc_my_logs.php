<!-- start logs -->
<!-- start delete intrigation tools -->
<div class="top-content" id="my_log_list">
   <h3 class="page-title">My Logs List</h3>
   <p>
      My logs list with HTTP POST request.
   </p>
   <p>
      API token is required for the authentication of the calling program to the API.
   </p>
   <p>
      Here my logs list data display as pagignation wise. only data get using pass page_id and per_page parameter. <br>
      page_id is index of your page so you can pass your page id. <br>
      Ex. : display on first page pass 1, display on second page pass 2 <br>
      per_page is count of data display of per page. <br>
   </p>
   <p>
      your logs data in display all your activity details types wise.
   </p>
</div>
<div class="row">
   <div class="col-md-6">
      <!-- TABLE HOVER -->
      <div class="panel white">
         <div class="panel-heading">
            <h3 class="panel-title">Request :  </h3>
            <br>
            <span class="text-warning">POST</span> : <?=base_url();?>My_Log/my_log_list</p>
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
            <pre class="response-view">{ "status": true, "message": "my log list get successfully", "data": { "clicks": [ { "type": "ex", "id": "18", "base_url": "http://localhost/aff/ver-4-0-0-5/store", "link": "http://localhost/aff/ver-4-0-0-5/store", "agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.114 Safari/537.36", "browserName": "Google Chrome", "browserVersion": "89.0.4389.114", "systemString": "Windows NT 10.0; Win64; x64", "osPlatform": "Windows", "osVersion": "10.0", "osShortVersion": "10.0", "isMobile": "0", "mobileName": "", "osArch": "64", "isIntel": "1", "isAMD": "0", "isPPC": "0", "ip": null, "country_code": "", "created_at": "10-04-2021 04:31 PM", "click_id": "3", "username": null, "click_type": "Store sale", "flag": "", "custom_data": [] } ], "start_from": 1 } }</pre>
         </div>
      </div>
      <!-- END TABLE HOVER -->
   </div>
</div>
<!-- end my-logs -->