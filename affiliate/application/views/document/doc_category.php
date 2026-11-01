<div class="row">
   <div class="col-md-12">
      <div class="page-intro" id="category">
         <h2 class="page-title">Category</h2>
         <p>
            Here display two types of category
            1) integration category
            2) store category
         </p>
      </div>
      <div class="top-content" id="get_integration_category">
         <h3 class="page-title">Interegation Category</h3>
         <p>
            Interegation category with HTTP GET request.
         </p>
         <p>
            API token is required for the authentication of the calling program to the API.
         </p>
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
            <span class="text-success">GET</span> : <?=base_url();?>Integration_Category/get_integration_category</p>
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
            <pre class="response-view">{ "status": true, "message": "integration category list get successfully", "data": [ { "id": "2", "name": "test 2" }, { "id": "1", "name": "test 1" } ] }</pre>
         </div>
      </div>
      <!-- END TABLE HOVER -->
   </div>
</div>
<!-- end interegation category -->
<!-- start store category -->
<div class="top-content" id="get_store_category">
   <h3 class="page-title">Store Category</h3>
   <p>
      Store category with HTTP GET request.
   </p>
   <p>
      API token is required for the authentication of the calling program to the API.
   </p>
</div>
<div class="row">
   <div class="col-md-6">
      <!-- TABLE HOVER -->
      <div class="panel white">
         <div class="panel-heading">
            <h3 class="panel-title">Request :  </h3><br>
            <span class="text-success">GET</span> : <?=base_url();?>Store_Category/get_store_category</p>
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
            <pre class="response-view">{ "status": true, "message": "store list get successfully", "data": [ { "id": "8", "name": "Cat Main 4" }, { "id": "7", "name": "Cat Main 3" }, { "id": "6", "name": "Cat Main 2" }, { "id": "5", "name": "Cat Main 1" }, { "id": "4", "name": "sub1" }, { "id": "3", "name": "clothing and footwear" }, { "id": "2", "name": "shoes" }, { "id": "1", "name": "Shirts" } ] }</pre>
         </div>
      </div>
      <!-- END TABLE HOVER -->
   </div>
</div>
<!-- end store category -->