<!-- start vendor market place -->
<div class="row">
   <div class="col-md-12">
      <div class="page-intro" id="vendor_market_place">
         <h2 class="page-title">Vendor Market Place</h2>
         <p>
            In this manage your product and coupon also you can manage your store setting. 
         </p>
      </div>
      <div class="top-content" id="my_products">
         <h3 class="page-title">My Product</h3>
         <p>
            My product request with HTTP GET request.
         </p>
         <p>
            API token is required for the authentication of the calling program to the API.
         </p>
         <p>
            Here display all product listed with it's clicks / commission, sales / commission with all commission details.
         </p>
      </div>
   </div>
</div>
<!-- start my products --> 
<div class="row">
   <div class="col-md-6">
      <!-- TABLE HOVER -->
      <div class="panel white">
         <div class="panel-heading">
            <h3 class="panel-title">Request :  </h3>
            <br>
            <span class="text-success">GET</span> : <?=base_url();?>Vendor_Market_Place/store_product_list</p>
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
            <pre class="response-view">{ "status": true, "message": "store product list get successfully", "data": { "default_commition": { "click_allow": "single", "product_commission_type": "percentage", "product_commission": "25", "product_ppc": "1", "product_noofpercommission": "1", "product_recursion": "", "recursion_custom_time": "0", "recursion_endtime": null }, "productlist": [ { "product_id": "17", "product_name": "check product - Duplicate", "product_description": "this is a test description of the product", "product_short_description": "this is a test product add by me", "product_tags": "", "product_msrp": "0", "product_price": "500", "product_sku": "SKU", "product_slug": "check-product-duplicate-17", "product_share_count": "", "product_click_count": "0", "product_view_count": "0", "product_sales_count": "0", "product_featured_image": "yXgDzuwN6SUFKYGivqBVlchjW598sIn1.jpg", "product_banner": "", "product_video": "", "product_type": "virtual", "product_commision_type": "default", "product_commision_value": "0", "product_status": "0", "product_ipaddress": "::1", "product_created_date": "2021-04-01 18:14:06", "product_updated_date": "0000-00-00 00:00:00", "product_created_by": "3", "product_updated_by": "0", "product_click_commision_type": "default", "product_click_commision_ppc": "0", "product_click_commision_per": "0", "product_total_commission": "0", "product_recursion_type": "custom", "product_recursion": "every_day", "recursion_custom_time": "0", "recursion_endtime": "2021-03-20 03:00:00", "view": "0", "on_store": "1", "downloadable_files": "[]", "allow_shipping": "1", "allow_upload_file": "1", "allow_comment": "1", "state_id": "12", "product_avg_rating": "0", "product_variations": "", "seller_firstname": "user1", "seller_lastname": "user1", "seller_username": "user1", "seller_id": "3", "commission": null, "order_count": "0", "commition_click_count": "0", "commition_click_count_admin": "0", "commition_click": null } ] } }</pre>
         </div>
      </div>
      <!-- END TABLE HOVER -->
   </div>
</div>
<!-- end my products -->
<!-- start get prodcut name -->  
<div class="top-content" id="get_product_name">
   <h3 class="page-title">Get Product Name</h3>
   <p>
      My product request with HTTP GET request.
   </p>
   <p>
      API token is required for the authentication of the calling program to the API.
   </p>
   <p>
      Here display all products name with it's ids.
   </p>
</div>
<div class="row">
   <div class="col-md-6">
      <!-- TABLE HOVER -->
      <div class="panel white">
         <div class="panel-heading">
            <h3 class="panel-title">Request :  </h3>
            <br>
            <span class="text-success">GET</span> : <?=base_url();?>Vendor_Market_Place/get_product_name</p>
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
            <pre class="response-view">{ "status": true, "message": "product name list get successfully", "data": [ { "product_id": "2", "product_name": "vendor product" }, { "product_id": "3", "product_name": "vendor product - Duplicate" }, { "product_id": "4", "product_name": "check product" }, { "product_id": "16", "product_name": "check product - Duplicate" }, { "product_id": "17", "product_name": "check product - Duplicate" } ] }</pre>
         </div>
      </div>
      <!-- END TABLE HOVER -->
   </div>
</div>
<!-- end get prodcut name -->    
<!-- start manage product coupon -->
<div class="top-content" id="manage_product_coupon">
   <h3 class="page-title">Manage Product Coupon</h3>
   <p>
      My product request with HTTP POST request.
   </p>
   <p>
      API token is required for the authentication of the calling program to the API.
   </p>
   <p>
      Using order manage api you can add and edit your product coupon code. <br>
      Used id when update your coupon code 
   </p>
</div>
<div class="row">
   <div class="col-md-6">
      <!-- TABLE HOVER -->
      <div class="panel white">
         <div class="panel-heading">
            <h3 class="panel-title">Request :  </h3>
            <br>
            <span class="text-warning">POST</span> : <?=base_url();?>Vendor_Market_Place/manage_product_coupon</p>
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
                     <td>name</td>
                     <td><code>string</code></td>
                     <td><code>Body</code></td>
                     <td>compolsory pass this authentication token in your header also get from login api</td>
                  </tr>
                  <tr>
                     <td>code</td>
                     <td><code>string</code></td>
                     <td><code>Body</code></td>
                     <td>compolsory pass this authentication token in your header also get from login api</td>
                  </tr>
                  <tr>
                     <td>type</td>
                     <td><code>string</code></td>
                     <td><code>Body</code></td>
                     <td>P OR F (P = Percentage , F = Fixed)</td>
                  </tr>
                  <tr>
                     <td>allow_for</td>
                     <td><code>string</code></td>
                     <td><code>Body</code></td>
                     <td>A OR S (A = All, S = Selected Only)</td>
                  </tr>
                  <tr>
                     <td>discount</td>
                     <td><code>date</code></td>
                     <td><code>Body</code></td>
                     <td>-</td>
                  </tr>
                  <tr>
                     <td>date_start</td>
                     <td><code>date</code></td>
                     <td><code>Body</code></td>
                     <td>date formate is Y-m-d</td>
                  </tr>
                  <tr>
                     <td>date_end</td>
                     <td><code>string</code></td>
                     <td><code>Body</code></td>
                     <td>date formate is Y-m-d</td>
                  </tr>
                  <tr>
                     <td>status</td>
                     <td><code>number</code></td>
                     <td><code>Body</code></td>
                     <td>1  = Enable, 0 = Disable</td>
                  </tr>
                  <tr>
                     <td>products</td>
                     <td><code>string</code></td>
                     <td><code>Body</code></td>
                     <td>Add product id as a sepreted comma . Used product name service</td>
                  </tr>
                  <tr>
                     <td>uses_total</td>
                     <td><code>number</code></td>
                     <td><code>Body</code></td>
                     <td>optional variable</td>
                  </tr>
                  <tr>
                     <td>id</td>
                     <td><code>number</code></td>
                     <td><code>Body</code></td>
                     <td>Used id when update your coupon code </td>
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
            <pre class="response-view">{ "status": true, "message": "coupon code add successfully" }</pre>
         </div>
      </div>
      <!-- END TABLE HOVER -->
   </div>
</div>
<!-- end manage product coupon -->
<!-- start delete coupon -->
<div class="top-content" id="delete_coupon">
   <h3 class="page-title">Delete Coupon</h3>
   <p>
      My product request with HTTP DELETE request.
   </p>
   <p>
      API token is required for the authentication of the calling program to the API.
   </p>
   <p>
      Using this api you can removed your coupon.  in this api you can pass id in query string
   </p>
</div>
<div class="row">
   <div class="col-md-6">
      <!-- TABLE HOVER -->
      <div class="panel white">
         <div class="panel-heading">
            <h3 class="panel-title">Request :  </h3>
            <br>
            <span class="text-danger">DELETE</span> : <?=base_url();?>Vendor_Market_Place/delete_coupon</p>
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
                     <td>coupon_id</td>
                     <td><code>number</code></td>
                     <td><code>query string</code></td>
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
            <pre class="response-view">{ "status": true, "message": "delete coupon successfully" }</pre>
         </div>
      </div>
      <!-- END TABLE HOVER -->
   </div>
</div>
<!-- end delete coupon -->
<!-- start get store setting details -->
<div class="top-content" id="get_store_setting_details">
   <h3 class="page-title">Get Store Setting details</h3>
   <p>
      My product request with HTTP GET request.
   </p>
   <p>
      API token is required for the authentication of the calling program to the API.
   </p>
   <p>
      Using this api you can get your affiliate store setting details.
   </p>
</div>
<div class="row">
   <div class="col-md-6">
      <!-- TABLE HOVER -->
      <div class="panel white">
         <div class="panel-heading">
            <h3 class="panel-title">Request :  </h3>
            <br>
            <span class="text-success">GET</span> : <?=base_url();?>Vendor_Market_Place/get_store_setting_details</p>
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
            <pre class="response-view">{ "status": true, "message": "store setting get successfully", "data": { "user_id": "3", "vendor_status": "1", "affiliate_sale_commission_type": "fixed", "affiliate_commission_value": "10", "affiliate_click_count": "1", "affiliate_click_amount": "20", "form_affiliate_click_count": "60", "form_affiliate_click_amount": "0", "form_affiliate_sale_commission_type": "", "form_affiliate_commission_value": "0" } }</pre>
         </div>
      </div>
      <!-- END TABLE HOVER -->
   </div>
</div>
<!-- end get store setting details -->
<!-- start manage store setting details -->
<div class="top-content" id="manage_store_setting_details">
   <h3 class="page-title">Manage Store Setting Details</h3>
   <p>
      My product request with HTTP POST request.
   </p>
   <p>
      API token is required for the authentication of the calling program to the API.
   </p>
   <p>
      Using this api you can change your store setting details
   </p>
</div>
<div class="row">
   <div class="col-md-6">
      <!-- TABLE HOVER -->
      <div class="panel white">
         <div class="panel-heading">
            <h3 class="panel-title">Request :  </h3>
            <br>
            <span class="text-warning">POST</span> : <?=base_url();?>Vendor_Market_Place/manage_store_setting_details</p>
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
                     <td>vendor_status</td>
                     <td><code>string</code></td>
                     <td><code>Body</code></td>
                     <td>O OR 1 (0 = No , 1 = Yes)</td>
                  </tr>
                  <tr>
                     <td>affiliate_click_count</td>
                     <td><code>string</code></td>
                     <td><code>Body</code></td>
                     <td>(optional)</td>
                  </tr>
                  <tr>
                     <td>affiliate_click_amount</td>
                     <td><code>string</code></td>
                     <td><code>Body</code></td>
                     <td>(optional)</td>
                  </tr>
                  <tr>
                     <td>affiliate_sale_commission_type</td>
                     <td><code>string</code></td>
                     <td><code>Body</code></td>
                     <td>percentage OR fixed</td>
                  </tr>
                  <tr>
                     <td>affiliate_commission_value</td>
                     <td><code>string</code></td>
                     <td><code>Body</code></td>
                     <td>(optional)</td>
                  </tr>
                  <tr>
                     <td>form_affiliate_click_count</td>
                     <td><code>string</code></td>
                     <td><code>Body</code></td>
                     <td>(optional)</td>
                  </tr>
                  <tr>
                     <td>form_affiliate_click_amount</td>
                     <td><code>string</code></td>
                     <td><code>Body</code></td>
                     <td>(optional)</td>
                  </tr>
                  <tr>
                     <td>form_affiliate_sale_commission_type</td>
                     <td><code>string</code></td>
                     <td><code>Body</code></td>
                     <td>(optional)</td>
                  </tr>
                  <tr>
                     <td>form_affiliate_commission_value</td>
                     <td><code>string</code></td>
                     <td><code>Body</code></td>
                     <td>(optional)</td>
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
            <pre class="response-view">{ "status": true, "message": "store setting change successfully" }</pre>
         </div>
      </div>
      <!-- END TABLE HOVER -->
   </div>
</div>
<!-- end manage store setting details -->
<!-- start Get Store Coupon List -->
<div class="top-content" id="get_store_coupon_list">
   <h3 class="page-title">Get Store Coupon List</h3>
   <p>
      My product request with HTTP GET request.
   </p>
   <p>
      API token is required for the authentication of the calling program to the API.
   </p>
   <p>
      Using this api you can get all your coupons
   </p>
</div>
<div class="row">
   <div class="col-md-6">
      <!-- TABLE HOVER -->
      <div class="panel white">
         <div class="panel-heading">
            <h3 class="panel-title">Request :  </h3>
            <br>
            <span class="text-success">GET</span> : <?=base_url();?>Vendor_Market_Place/get_store_coupon_list</p>
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
            <pre class="response-view">{ "status": true, "message": "get store coupon list successfully", "data": [ { "coupon_id": "2", "name": "Test coupon", "code": "#TTY77", "type": "P", "discount": "5.0000", "date_start": "2021-03-28", "date_end": "2021-03-31", "uses_total": "50", "status": "0", "products": null, "vendor_id": "3", "allow_for": "A", "date_added": "2021-03-28 12:30:15", "product_count": 7, "count_coupon": 0 } ] }</pre>
         </div>
      </div>
      <!-- END TABLE HOVER -->
   </div>
</div>
<!-- end Get Store Coupon List -->
<!-- start get countrie list -->
<div class="top-content" id="get_countrie_list">
   <h3 class="page-title">Get Countrie List</h3>
   <p>
      My product request with HTTP GET request.
   </p>
   <p>
      API token is required for the authentication of the calling program to the API.
   </p>
   <p>
      You can get all country data using this api. whenever you need countries you can get this api.
   </p>
   <p>
   </div>
   <div class="row">
      <div class="col-md-6">
         <!-- TABLE HOVER -->
         <div class="panel white">
            <div class="panel-heading">
               <h3 class="panel-title">Request :  </h3>
               <br>
               <span class="text-success">GET</span> : <?=base_url();?>Vendor_Market_Place/get_countrie_list</p>
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
               <pre class="response-view">{ "status": true, "message": "country list get successfully", "data": [ { "name": "Afghanistan", "id": "1" }, { "name": "Albania", "id": "2" }, { "name": "Algeria", "id": "3" } ] }</pre>
            </div>
         </div>
         <!-- END TABLE HOVER -->
      </div>
   </div>
   <!-- end get countrie list -->
   <!-- start get state list -->
   <div class="top-content" id="get_state_list">
      <h3 class="page-title">Get State List</h3>
      <p>
         My product request with HTTP POST request.
      </p>
      <p>
         API token is required for the authentication of the calling program to the API.
      </p>
      <p>
         You can all state as country wise. in this pass country id to get all states of this country.
      </p>
   </div>
   <div class="row">
      <div class="col-md-6">
         <!-- TABLE HOVER -->
         <div class="panel white">
            <div class="panel-heading">
               <h3 class="panel-title">Request :  </h3>
               <br>
               <span class="text-warning">POST</span> : <?=base_url();?>Vendor_Market_Place/get_state_list</p>
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
                  <tbody>
                     <tr>
                        <td>country_id</td>
                        <td><code>number</code></td>
                        <td><code>body</code></td>
                        <td>pass country_id to get states for that country</td>
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
               <pre class="response-view">{ "status": true, "message": "state list get successfully", "data": [ { "id": "42", "name": "Badakhshan" }, { "id": "43", "name": "Badgis" }, { "id": "44", "name": "Baglan" }, { "id": "45", "name": "Balkh" } ] }</pre>
            </div>
         </div>
         <!-- END TABLE HOVER -->
      </div>
   </div>
   <!-- end get state list -->
   <!-- start manage product -->
   <div class="top-content" id="manage_product">
      <h3 class="page-title">Manage Product</h3>
      <p>
         My product request with HTTP POST request.
      </p>
      <p>
         API token is required for the authentication of the calling program to the API.
      </p>
      <p>
         You can add and edit your product using this api. in this many modules available like that
         you can add prodcut as multiple category wise,
         you can add product as sales and click commision wise also you can manage your custom sales and click commision. 
      </p>
   </div>
   <div class="row">
      <div class="col-md-6">
         <!-- TABLE HOVER -->
         <div class="panel white">
            <div class="panel-heading">
               <h3 class="panel-title">Request :  </h3>
               <br>
               <span class="text-warning">POST</span> : <?=base_url();?>Vendor_Market_Place/manage_product</p>
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
                        <td>product_name</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>-</td>
                     </tr>
                     <tr>
                        <td>product_description</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>-</td>
                     </tr>
                     <tr>
                        <td>product_short_description</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>-</td>
                     </tr>
                     <tr>
                        <td>category[]</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>data get from category list and add as a array formate</td>
                     </tr>
                     <tr>
                        <td>product_price</td>
                        <td><code>number</code></td>
                        <td><code>Body</code></td>
                        <td>-</td>
                     </tr>
                     <tr>
                        <td>product_sku</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>-</td>
                     </tr>
                     <tr>
                        <td>allow_country</td>
                        <td><code>number</code></td>
                        <td><code>Body</code></td>
                        <td>1 OR 0 (1 = Enable, 0 = Disable) (optional)</td>
                     </tr>
                     <tr>
                        <td>country_id</td>
                        <td><code>number</code></td>
                        <td><code>Body</code></td>
                        <td>get from country list api (optional if allow_country=0)</td>
                     </tr>
                     <tr>
                        <td>state_id</td>
                        <td><code>number</code></td>
                        <td><code>Body</code></td>
                        <td>get from state list api ((optional if allow_country=0)</td>
                     </tr>
                     <tr>
                        <td>product_recursion_type</td>
                        <td><code>number</code></td>
                        <td><code>Body</code></td>
                        <td>custom OR default</td>
                     </tr>
                     <tr>
                        <td>product_recursion</td>
                        <td><code>number</code></td>
                        <td><code>Body</code></td>
                        <td>every_day OR every_week OR every_month OR every_year OR custom_time</td>
                     </tr>
                     <tr>
                        <td>recursion_custom_time</td>
                        <td><code>number</code></td>
                        <td><code>Body</code></td>
                        <td>every_day OR every_week OR every_month OR every_year OR custom_time</td>
                     </tr>
                     <tr>
                        <td>product_id</td>
                        <td><code>number</code></td>
                        <td><code>Body</code></td>
                        <td>if you want to edit record set product id</td>
                     </tr>
                     <tr>
                        <td>product_featured_image</td>
                        <td><code>file</code></td>
                        <td><code>Body</code></td>
                        <td>-</td>
                     </tr>
                     <tr>
                        <td>downloadable_file[]</td>
                        <td><code>file</code></td>
                        <td><code>Body</code></td>
                        <td>you can add downloadable_file as a multiple</td>
                     </tr>
                     <tr>
                        <td>product_type</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>downloadable OR virtual</td>
                     </tr>
                     <tr>
                        <td>keep_files</td>
                        <td><code>file</code></td>
                        <td><code>Body</code></td>
                        <td>uploads zip file</td>
                     </tr>
                     <tr>
                        <td>admin_comment</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>comment add by admin and save</td>
                     </tr>
                     <tr>
                        <td>affiliate_click_commission_type</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>default OR fixed</td>
                     </tr>
                     <tr>
                        <td>affiliate_click_count</td>
                        <td><code>number</code></td>
                        <td><code>Body</code></td>
                        <td>default OR fixed</td>
                     </tr>
                     <tr>
                        <td>affiliate_click_amount</td>
                        <td><code>number</code></td>
                        <td><code>Body</code></td>
                        <td>default OR fixed</td>
                     </tr>
                     <tr>
                        <td>affiliate_sale_commission_type</td>
                        <td><code>number</code></td>
                        <td><code>Body</code></td>
                        <td>default OR percentage OR fixed</td>
                     </tr>
                     <tr>
                        <td>affiliate_commission_value</td>
                        <td><code>number</code></td>
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
               <pre class="response-view">{ "status": true, "message": "product manage successfully" }</pre>
            </div>
         </div>
         <!-- END TABLE HOVER -->
      </div>
   </div>
   <!-- end manage product -->
   <!-- start delete product -->
   <div class="top-content" id="delete_product">
      <h3 class="page-title">Delete Product</h3>
      <p>
         My product request with HTTP DELETE request.
      </p>
      <p>
         API token is required for the authentication of the calling program to the API.
      </p>
      <p>
         You can delete your product using this api. in this need to pass id in query string.
      </p>
   </div>
   <div class="row">
      <div class="col-md-6">
         <!-- TABLE HOVER -->
         <div class="panel white">
            <div class="panel-heading">
               <h3 class="panel-title">Request :  </h3>
               <br>
               <span class="text-danger">DELETE</span> : <?=base_url();?>Vendor_Market_Place/delete_product</p>
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
                        <td>product_id</td>
                        <td><code>number</code></td>
                        <td><code>Query String</code></td>
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
               <pre class="response-view">{ "status": true, "message": "product delete successfully" }</pre>
            </div>
         </div>
         <!-- END TABLE HOVER -->
      </div>
   </div>
   <!-- end delete product -->
   <!-- start create duplicate product -->
   <div class="top-content" id="create_duplicate_product">
      <h3 class="page-title">Create Duplicate Product</h3>
      <p>
         My product request with HTTP POST request.
      </p>
      <p>
         API token is required for the authentication of the calling program to the API.
      </p>
      <p>
         You can create new dupliacte product same as your old product using this api. in this you can pass your product id.
      </p>
   </div>
   <div class="row">
      <div class="col-md-6">
         <!-- TABLE HOVER -->
         <div class="panel white">
            <div class="panel-heading">
               <h3 class="panel-title">Request :  </h3>
               <br>
               <span class="text-warning">POST</span> : <?=base_url();?>Vendor_Market_Place/create_duplicate_product</p>
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
                        <td>product_id</td>
                        <td><code>number</code></td>
                        <td><code>Header</code></td>
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
               <pre class="response-view">{ "status": true, "message": "create duplicate product successfully" }</pre>
            </div>
         </div>
         <!-- END TABLE HOVER -->
      </div>
   </div>
   <!-- end create duplicate product -->
   <!-- start get product all images -->
   <div class="top-content" id="get_product_all_images">
      <h3 class="page-title">Get Product All Images</h3>
      <p>
         My product request with HTTP POST request.
      </p>
      <p>
         API token is required for the authentication of the calling program to the API.
      </p>
      <p>
         In this you can get all product images of perticualr product. need to pass peroduct id and get all product album images. 
      </p>
   </div>
   <div class="row">
      <div class="col-md-6">
         <!-- TABLE HOVER -->
         <div class="panel white">
            <div class="panel-heading">
               <h3 class="panel-title">Request :  </h3>
               <br>
               <span class="text-warning">POST</span> : <?=base_url();?>Vendor_Market_Place/get_product_all_images</p>
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
                        <td>product_id</td>
                        <td><code>number</code></td>
                        <td><code>Query String</code></td>
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
               <pre class="response-view">{ "status": true, "message": "get all images list successfully", "data": [ { "product_media_upload_id": "4", "product_id": "17", "product_media_upload_type": "image", "product_media_upload_path": "idLDZQ4G9jIqXgeh1RmC8HusBPk5NUct.png", "product_media_upload_video_image": "no-image.jpg", "product_media_upload_status": "1", "product_media_upload_ipaddress": "::1", "product_media_upload_created_date": "2021-03-30 17:10:13", "product_media_upload_created_by": "3", "product_media_upload_os": "Unknown Windows OS", "product_media_upload_browser": "Chrome", "product_media_upload_isp": "DESKTOP-1AUGRKG" }, { "product_media_upload_id": "5", "product_id": "17", "product_media_upload_type": "image", "product_media_upload_path": "dShcaoJNY9IK6fPs0l2xqTwEzyFAgWjt.jpg", "product_media_upload_video_image": "no-image.jpg", "product_media_upload_status": "1", "product_media_upload_ipaddress": "::1", "product_media_upload_created_date": "2021-04-01 16:18:05", "product_media_upload_created_by": "3", "product_media_upload_os": "Unknown Windows OS", "product_media_upload_browser": "Chrome", "product_media_upload_isp": "DESKTOP-1AUGRKG" } ] }</pre>
            </div>
         </div>
         <!-- END TABLE HOVER -->
      </div>
   </div>
   <!-- end get product all images -->
   <!-- start delete product image -->
   <div class="top-content" id="delete_product_image">
      <h3 class="page-title">Delete Product Image</h3>
      <p>
         My product request with HTTP DELETE request.
      </p>
      <p>
         API token is required for the authentication of the calling program to the API.
      </p>
      <p>
         Using this api you can delete your perticular product album image. in this pass images id in query string.
      </p>
   </div>
   <div class="row">
      <div class="col-md-6">
         <!-- TABLE HOVER -->
         <div class="panel white">
            <div class="panel-heading">
               <h3 class="panel-title">Request :  </h3>
               <br>
               <span class="text-danger">DELETE</span> : <?=base_url();?>Vendor_Market_Place/delete_product_image</p>
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
                        <td>image_id</td>
                        <td><code>number</code></td>
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
               <pre class="response-view">{ "status": true, "message": "product image delete successfully" }</pre>
            </div>
         </div>
         <!-- END TABLE HOVER -->
      </div>
   </div>
   <!-- end delete product image -->
   <!-- start Add Product Images -->
   <div class="top-content" id="add_product_images">
      <h3 class="page-title">Add Product Images</h3>
      <p>
         My product request with HTTP POST request.
      </p>
      <p>
         API token is required for the authentication of the calling program to the API.
      </p>
      <p>
         You can add add images of perticular product. in this need to pass file as multipart formadata and you can also uploads image in multiple files.
      </p>
   </div>
   <div class="row">
      <div class="col-md-6">
         <!-- TABLE HOVER -->
         <div class="panel white">
            <div class="panel-heading">
               <h3 class="panel-title">Request :  </h3>
               <br>
               <span class="text-warning">POST</span> : <?=base_url();?>Vendor_Market_Place/add_product_images</p>
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
                        <td>product_multiple_image[]</td>
                        <td><code>file</code></td>
                        <td><code>Body</code></td>
                        <td>uploads multiples file</td>
                     </tr>
                     <tr>
                        <td>product_id</td>
                        <td><code>number</code></td>
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
               <pre class="response-view">{ "status": true, "message": "product images add successfully" }</pre>
            </div>
         </div>
         <!-- END TABLE HOVER -->
      </div>
   </div>
   <!-- end Add Product Images -->
<!-- end vendor market place -->