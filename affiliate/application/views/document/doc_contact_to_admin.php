<!-- start contact to admin -->
<div class="top-content" id="contact_to_admin">
   <h3 class="page-title">Contact To Admin</h3>
   <p>
      Get user reports with HTTP POST request.
   </p>
   <p>
      API token is required for the authentication of the calling program to the API.
   </p>
   <p>
      if you want to contact with admin to pass your subject and message. here send mail to admin and it will contact to you.
   </p>
</div>
<div class="row">
   <div class="col-md-6">
      <!-- TABLE HOVER -->
      <div class="panel white">
         <div class="panel-heading">
            <h3 class="panel-title">Request :  </h3><br>
            <span class="text-warning">POST</span> : <?=base_url();?>Contact_Admin/contact_to_admin</p>
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
                     <td>subject</td>
                     <td><code>string</code></td>
                     <td><code>Body</code></td>
                     <td>-</td>
                  </tr>
                  <tr>
                     <td>email</td>
                     <td><code>string</code></td>
                     <td><code>Body</code></td>
                     <td>-</td>
                  </tr>
                  <tr>
                     <td>fname</td>
                     <td><code>string</code></td>
                     <td><code>Body</code></td>
                     <td>-</td>
                  </tr>
                  <tr>
                     <td>lastname</td>
                     <td><code>string</code></td>
                     <td><code>Body</code></td>
                     <td>-</td>
                  </tr>
                  <tr>
                     <td>domain</td>
                     <td><code>string</code></td>
                     <td><code>Body</code></td>
                     <td>-</td>
                  </tr>
                  <tr>
                     <td>body</td>
                     <td><code>string</code></td>
                     <td><code>Body</code></td>
                     <td>-</td>
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
            <pre class="response-view">{ "status": true, "message": "contact to admin successfully" }</pre>
         </div>
      </div>
      <!-- END TABLE HOVER -->
   </div>
</div>
<!-- end contact to admin -->