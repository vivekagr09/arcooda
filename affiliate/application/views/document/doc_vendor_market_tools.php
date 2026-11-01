<!-- start vendor market tools -->
         <!-- start manage my marketing program -->
         <div class="row">
            <div class="col-md-12">
               <div class="page-intro" id="vendor_market_tools">
                  <h2 class="page-title">Vendor Market Tools</h2>
                  <p>
                     In this section including marketing programs and marketing ads. we have many types of ads available like : banners, text ads, invisible links and viral videos  also we can manage this add. in this ads creation, list, edit and delete option available.
                  </p>
                  <p>
                     Perticular ads have it's unique setting like : general setting, level setting, recurring setting, and postback setting.
                  </p>
               </div>
               <div class="top-content" id="manage_my_marketing_program">
                  <h3 class="page-title">Manage My Marketing Program</h3>
                  <p>
                     My product request with HTTP POST request.
                  </p>
                  <p>
                     API token is required for the authentication of the calling program to the API.
                  </p>
                  <p>
                     Using this api you can add you custom program in this you can set your  affiliate sale settings and  affiliate click settings
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
                     <span class="text-warning">POST</span> : <?=base_url();?>Vendor_Market_Tools/manage_my_marketing_program</p>
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
                              <td>-</td>
                           </tr>
                           <tr>
                              <td>sale_status</td>
                              <td><code>number</code></td>
                              <td><code>Body</code></td>
                              <td>Oprional (0 = Disable, 1 = Enable)</td>
                           </tr>
                           <tr>
                              <td>commission_type</td>
                              <td><code>string</code></td>
                              <td><code>Body</code></td>
                              <td>if required sale_status = 1 (percentage OR fixed)</td>
                           </tr>
                           <tr>
                              <td>commission_sale</td>
                              <td><code>number</code></td>
                              <td><code>Body</code></td>
                              <td>if required sale_status = 1</td>
                           </tr>
                           <tr>
                              <td>click_status</td>
                              <td><code>number</code></td>
                              <td><code>Body</code></td>
                              <td>Optional (0 = Disable, 1 = Enable)</td>
                           </tr>
                           <tr>
                              <td>commission_number_of_click</td>
                              <td><code>number</code></td>
                              <td><code>Body</code></td>
                              <td>if required click_status= 1</td>
                           </tr>
                           <tr>
                              <td>commission_click_commission</td>
                              <td><code>number</code></td>
                              <td><code>Body</code></td>
                              <td>if required click_status= 1</td>
                           </tr>
                           <tr>
                              <td>program_id</td>
                              <td><code>number</code></td>
                              <td><code>Body</code></td>
                              <td>(optional) you can pass when you want edit record</td>
                           </tr>
                           <tr>
                              <td>comment</td>
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
                     <pre class="response-view">{ "status": true, "message": "my marketing program manage successfully" }</pre>
                  </div>
               </div>
               <!-- END TABLE HOVER -->
            </div>
         </div>
         <!-- end manage my marketing program -->
         <!-- start get my marketing program list -->
         <div class="top-content" id="get_my_marketing_program_list">
            <h3 class="page-title">Get My Marketing Program List</h3>
            <p>
               Get my marketing program list request with GET request.
            </p>
            <p>
               API token is required for the authentication of the calling program to the API.
            </p>
            <p>
               Using this api you can get all program list data. in this you can see your sales commision and clicks commision 
            </p>
         </div>
         <div class="row">
            <div class="col-md-6">
               <!-- TABLE HOVER -->
               <div class="panel white">
                  <div class="panel-heading">
                     <h3 class="panel-title">Request :  </h3>
                     <br>
                     <span class="text-warning">GET</span> : <?=base_url();?>Vendor_Market_Tools/get_my_marketing_program_list</p>
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
                     <pre class="response-view">{ "status": true, "message": "my marketing program list get successfully", "data": [ { "id": "14", "vendor_id": "31", "status": "0", "name": "check edit", "commission_type": "percentage", "commission_sale": "1", "sale_status": "1", "commission_number_of_click": "50", "commission_click_commission": "1", "click_status": "1", "admin_commission_type": "", "admin_commission_sale": "0", "admin_commission_number_of_click": "0", "admin_commission_click_commission": "0", "admin_click_status": "0", "admin_sale_status": "0", "comment": "[{'from': 'admin','comment':'check edit'}]", "click_allow": null, "created_at": "2021-04-09 18:17:43", "username": "user1", "associate_programns": "0" } ] }</pre>
               </div>
            </div>
            <!-- END TABLE HOVER -->
         </div>
      </div>
      <!-- end get my marketing program list -->
      <!-- start delete my marketing program -->
      <div class="top-content" id="delete_my_marketing_program">
         <h3 class="page-title">Delete My Marketing Program</h3>
         <p>
            Delete my marketing program request with DELETE request.
         </p>
         <p>
            API token is required for the authentication of the calling program to the API.
         </p>
         <p>
            You can delete your program data using this api.  in this api you can pass id in query string
         </p>
      </div>
      <div class="row">
         <div class="col-md-6">
            <!-- TABLE HOVER -->
            <div class="panel white">
               <div class="panel-heading">
                  <h3 class="panel-title">Request :  </h3>
                  <br>
                  <span class="text-danger">DELETE</span> : <?=base_url();?>Vendor_Market_Tools/delete_my_marketing_program</p>
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
                           <td>program_id</td>
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
                  <pre class="response-view">{ "status": true, "message": "my marketing program delete successfully" }</pre>
               </div>
            </div>
            <!-- END TABLE HOVER -->
         </div>
      </div>
      <!-- end delete my marketing program -->
      <!-- start get integration tools -->
      <div class="top-content" id="get_integration_tools">
         <h3 class="page-title">Get Integration Tools</h3>
         <p>
            Get integration tools request with POST request.
         </p>
         <p>
            API token is required for the authentication of the calling program to the API.
         </p>
         <p>
            You can find all intrigatin tools data using this api. also you can filter using category and ads name wise.
         </p>
         <p>
            Here integration tools data display as pagignation wise. only data get using pass page_id and per_page parameter. <br>
            page_id is index of your page so you can pass your page id. <br>
            Ex. : display on first page pass 1, display on second page pass 2 <br>
            per_page is count of data display of per page. <br>
         </p>
      </div>
      <div class="row">
         <div class="col-md-6">
            <!-- TABLE HOVER -->
            <div class="panel white">
               <div class="panel-heading">
                  <h3 class="panel-title">Request :  </h3>
                  <br>
                  <span class="text-warning">POST</span> : <?=base_url();?>Vendor_Market_Tools/get_integration_tools</p>
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
                           <td><code>initger</code></td>
                           <td><code>Body</code></td>
                           <td>-</td>
                        </tr>
                        <tr>
                           <td>page_count</td>
                           <td><code>number</code></td>
                           <td><code>Body</code></td>
                           <td>-</td>
                        </tr>
                        <tr>
                           <td>category_id</td>
                           <td><code>number</code></td>
                           <td><code>Body</code></td>
                           <td>get data from get integration category api (optional)</td>
                        </tr>
                        <tr>
                           <td>ads_name</td>
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
                  <pre class="response-view">{ "status": true, "message": "integration tools get successfully", "data": [ { "id": "89", "redirectLocation": [], "program_id": "1", "name": "First Ads check", "vendor_id": "31", "program_name": "test", "target_link": "https://localhost/aff/ver-4-0-0-5/usercontrol/integration_tools_form/", "status": "0", "action_click": "100", "action_amount": "5", "general_click": "100", "general_amount": "50", "admin_action_click": null, "admin_action_amount": "0", "admin_general_click": null, "admin_general_amount": "0", "_tool_type": "program", "type": "Banner", "_type": "banner", "commission_type": "percentage", "commission_sale": "50", "commission_number_of_click": "2", "commission_click_commission": "1", "click_status": "1", "sale_status": "1", "admin_commission_type": null, "admin_commission_sale": null, "admin_commission_number_of_click": null, "admin_commission_click_commission": null, "admin_click_status": null, "admin_sale_status": null, "recursion": "every_week", "recursion_custom_time": "0", "username": "user1", "recursion_endtime": "2021-02-18 10:30:05", "featured_image": null, "total_sale_amount": "$0.00", "total_click_amount": "$0.00", "total_action_click_amount": "$0.00", "total_general_click_amount": "$0.00", "total_sale_count": 0, "total_click_count": 0, "total_action_click_count": 0, "total_general_click_count": 0, "tool_type": "Program", "created_at": "09-04-2021 07:10 PM", "product_created_date": "09-04-2021 07:10 PM", "is_tool": 1, "slug": "" } ] }</pre>
            </div>
         </div>
         <!-- END TABLE HOVER -->
      </div>
   </div>
   <!-- end get integration tools -->
   <!-- start get dynamic param -->
   <div class="top-content" id="get_dynamic_param">
      <h3 class="page-title">Get Dynamic Param</h3>
      <p>
         Get dynamic param request with GET request.
      </p>
      <p>
         API token is required for the authentication of the calling program to the API.
      </p>
      <p>
         You can use dynaic param data in integration tools times.
      </p>
   </div>
   <div class="row">
      <div class="col-md-6">
         <!-- TABLE HOVER -->
         <div class="panel white">
            <div class="panel-heading">
               <h3 class="panel-title">Request :  </h3>
               <br>
               <span class="text-success">GET</span> : <?=base_url();?>Vendor_Market_Tools/get_dynamic_param</p>
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
               <pre class="response-view">{ "status": true, "message": "dynamic param get successfully", "data": { "city": "City", "regionCode": "Region Code", "regionName": "Region Name", "countryCode": "Country Code", "countryName": "Country Name", "continentName": "Continent Name", "timezone": "Timezone", "currencyCode": "Currency Code", "currencySymbol": "Currency Symbol", "ip": "IP", "type": "Type action,general_click,product_click,sale", "id": "ID (Sale ID OR Click ID)" } }</pre>
            </div>
         </div>
         <!-- END TABLE HOVER -->
      </div>
   </div>
   <!-- end get dynamic param -->
   <!-- start duplicate intrigation tools ads -->
   <div class="top-content" id="duplicate_intrigation_tools">
      <h3 class="page-title">Duplicate Intrigation Tools Ads</h3>
      <p>
         Get dynamic param request with POST request.
      </p>
      <p>
         API token is required for the authentication of the calling program to the API.
      </p>
      <p>
         You can create new duplicate intrigation tools ad same as your old intrigation tools ad using this api. in this you can pass your tools id.
      </p>
   </div>
   <div class="row">
      <div class="col-md-6">
         <!-- TABLE HOVER -->
         <div class="panel white">
            <div class="panel-heading">
               <h3 class="panel-title">Request :  </h3>
               <br>
               <span class="text-warning">POST</span> : <?=base_url();?>Vendor_Market_Tools/duplicate_intrigation_tools</p>
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
                        <td>tools_id</td>
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
               <pre class="response-view">{ "status": true, "message": "duplicate ads create successfully" }</pre>
            </div>
         </div>
         <!-- END TABLE HOVER -->
      </div>
   </div>
   <!-- end duplicate intrigation tools ads -->
   <!-- start intrigation tools manage -->
   <div class="top-content" id="intrigation_tools_manage">
      <h3 class="page-title">Intrigation Tools Manage</h3>
      <p>
         Intrigation tools manage request with POST request.
      </p>
      <p>
         API token is required for the authentication of the calling program to the API.
      </p>
      <p>
         Using this api you can create one ads. there is 4 types of ads : banner, text, links and videos. <br>
         perticualr ads have 4 setting available general setting, level setting, recurring settting,  postback setting<br>
         general setting have 3 tool types program, action and general click<br>
         level setting have default and custom commission types available in this you can pass referlevel1 to 10.<br>
         recurring setting have every day, week, month and year and custom time recursion.<br>
         postback setting have default and custom postback setting. also here add our dynamic and statics parameter.<br>
         if you can set type banner so in this you can add multiple banner images<br>
         if you can set type text so in this you can add test size and text color, background color etc.<br>
         if you can set type link so in this you can add link title<br>
         if you can set type video so in this you can add video url and it's height width<br>
         also you can edit record using this parameter program_tool_id
      </p>
   </div>
   <div class="row">
      <div class="col-md-6">
         <!-- TABLE HOVER -->
         <div class="panel white">
            <div class="panel-heading">
               <h3 class="panel-title">Request :  </h3>
               <br>
               <span class="text-warning"> POST </span> : <?=base_url();?>Vendor_Market_Tools/intrigation_tools_manage</p>
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
                        <td>-</td>
                     </tr>
                     <tr>
                        <td>type</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>banner OR text_ads OR video_ads OR link_ads</td>
                     </tr>
                     <tr>
                        <td>tool_type</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>program OR action OR general_click</td>
                     </tr>
                     <tr>
                        <td>target_link</td>
                        <td><code>url</code></td>
                        <td><code>Body</code></td>
                        <td>-</td>
                     </tr>
                     <tr>
                        <td>program_id</td>
                        <td><code>number</code></td>
                        <td><code>Body</code></td>
                        <td>if required you can select vendor program (fixed value = 3)</td>
                     </tr>
                     <tr>
                        <td>action_click</td>
                        <td><code>number</code></td>
                        <td><code>Body</code></td>
                        <td>if required tool_type = action</td>
                     </tr>
                     <tr>
                        <td>action_amount</td>
                        <td><code>number</code></td>
                        <td><code>Body</code></td>
                        <td>if required tool_type = action</td>
                     </tr>
                     <tr>
                        <td>action_code</td>
                        <td><code>number</code></td>
                        <td><code>Body</code></td>
                        <td>if required(unique code)  tool_type = action</td>
                     </tr>
                     <tr>
                        <td>general_click</td>
                        <td><code>number</code></td>
                        <td><code>Body</code></td>
                        <td>if required tool_type = general_click</td>
                     </tr>
                     <tr>
                        <td>general_amount</td>
                        <td><code>number</code></td>
                        <td><code>Body</code></td>
                        <td>if required tool_type = general_click</td>
                     </tr>
                     <tr>
                        <td>general_code</td>
                        <td><code>number</code></td>
                        <td><code>Body</code></td>
                        <td>if required (unique code) tool_type = general_click</td>
                     </tr>
                     <tr>
                        <td>featured_image</td>
                        <td><code>file</code></td>
                        <td><code>Body</code></td>
                        <td>-</td>
                     </tr>
                     <tr>
                        <td>recursion</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>every_day OR every_week OR every_month OR every_year OR custom_time (required)</td>
                     </tr>
                     <tr>
                        <td>text_ads_content</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>if required type = text_ads</td>
                     </tr>
                     <tr>
                        <td>text_color</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>if required type = text_ads</td>
                     </tr>
                     <tr>
                        <td>text_border_color</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>if required type = text_ads</td>
                     </tr>
                     <tr>
                        <td>text_size</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>if required type = text_ads</td>
                     </tr>
                     <tr>
                        <td>text_bg_color</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>if required type = text_ads</td>
                     </tr>
                     <tr>
                        <td>recursion_custom_time</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>if required recursion = custom_time (you can add minitues)</td>
                     </tr>
                     <tr>
                        <td>allow_for_radio</td>
                        <td><code>number</code></td>
                        <td><code>Body</code></td>
                        <td>1 OR 0 (by default 0 == selected all affiliate user)</td>
                     </tr>
                     <tr>
                        <td>program_tool_id</td>
                        <td><code>number</code></td>
                        <td><code>Body</code></td>
                        <td>1 OR 0 (by default 0 == selected all affiliate user)</td>
                     </tr>
                     <tr>
                        <td>category[]</td>
                        <td><code>array</code></td>
                        <td><code>Body</code></td>
                        <td>-</td>
                     </tr>
                     <tr>
                        <td>allow_for[]</td>
                        <td><code>array</code></td>
                        <td><code>Body</code></td>
                        <td>-</td>
                     </tr>
                     <tr>
                        <td>recursion_endtime_status</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>if you are set allow pass on</td>
                     </tr>
                     <tr>
                        <td>recursion_endtime</td>
                        <td><code>date-time</code></td>
                        <td><code>Body</code></td>
                        <td>you can pass here date and time</td>
                     </tr>
                     <tr>
                        <td>custom_banner[]</td>
                        <td><code>file</code></td>
                        <td><code>Body</code></td>
                        <td>optional (you can add type = banner)</td>
                     </tr>
                     <tr>
                        <td>video_link</td>
                        <td><code>link</code></td>
                        <td><code>Body</code></td>
                        <td>if required type = video_ads</td>
                     </tr>
                     <tr>
                        <td>button_text</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>if required type = video_ads</td>
                     </tr>
                     <tr>
                        <td>video_height</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>if required type = video_ads</td>
                     </tr>
                     <tr>
                        <td>video_width</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>if required type = video_ads</td>
                     </tr>
                     <tr>
                        <td>link_title</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>if required type = link_ads</td>
                     </tr>
                     <tr>
                        <td>comment</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>(optional)</td>
                     </tr>
                     <tr>
                        <td>commission_type</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>optional (custom OR '')</td>
                     </tr>
                     <tr>
                        <td>referlevel[sale_type]</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>optional  (percentage OR fixed)  add level setting time</td>
                     </tr>
                     <tr>
                        <td>referlevel_1[commition]</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>(number) add level setting time</td>
                     </tr>
                     <tr>
                        <td>referlevel_1[sale_commition]</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>(number) add level setting time</td>
                     </tr>
                     <tr>
                        <td>referlevel_1[ex_commition]</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>(number) add level setting time</td>
                     </tr>
                     <tr>
                        <td>referlevel_1[ex_action_commition]</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>(number) add level setting time</td>
                     </tr>
                     <tr>
                        <td>marketpostback</td>
                        <td><code>string</code></td>
                        <td><code>Body</code></td>
                        <td>
                           (default OR custom) postback setting time
                           {"status":"custom","url":"","dynamicparam":{"city":"city","countryName":"countryName","currencySymbol":"currencySymbol"},"static":[{"key":"","value":""},{"key":"","value":""}]}
                        </td>
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
               <pre class="response-view">{ "status": true, "message": "intrigation tools manage successfully" }</pre>
            </div>
         </div>
         <!-- END TABLE HOVER -->
      </div>
   </div>
   <!-- end intrigation tools manage -->
   <!-- start get affiliate list -->
   <div class="top-content" id="get_affiliate_list">
      <h3 class="page-title">Get Affiliate List</h3>
      <p>
         Get affiliate list request with GET request.
      </p>
      <p>
         API token is required for the authentication of the calling program to the API.
      </p>
      <p>
         Text ads add time if you want to add affiliate so that time you need to call get affiliate api.
      </p>
   </div>
   <div class="row">
      <div class="col-md-6">
         <!-- TABLE HOVER -->
         <div class="panel white">
            <div class="panel-heading">
               <h3 class="panel-title">Request :  </h3>
               <br>
               <span class="text-success">GET</span> : <?=base_url();?>Vendor_Market_Tools/get_affiliate_list</p>
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
               <pre class="response-view">{ "status": true, "message": "affiliate list get successfully", "data": [ { "name": "affiliate1", "id": "2" }, { "name": "test", "id": "3" } ] }</pre>
         </div>
      </div>
      <!-- END TABLE HOVER -->
   </div>
</div>
<!-- end get affiliate list -->
<!-- start delete intrigation tools -->
<div class="top-content" id="delete_intrigation_tools">
   <h3 class="page-title">Delete Intrigation Tools</h3>
   <p>
      Delete intrigation tools request with DELETE request.
   </p>
   <p>
      API token is required for the authentication of the calling program to the API.
   </p>
   <p>
      You can delete intrigation tools using this api.  in this api you can pass id in query string
   </p>
</div>
<div class="row">
   <div class="col-md-6">
      <!-- TABLE HOVER -->
      <div class="panel white">
         <div class="panel-heading">
            <h3 class="panel-title">Request :  </h3>
            <br>
            <span class="text-danger">DELETE</span> : <?=base_url();?>Vendor_Market_Tools/delete_intrigation_tools</p>
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
                     <td>tools_id</td>
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
            <pre class="response-view">{ "status": true, "message": "intrigation tools ads delete successfully" }</pre>
         </div>
      </div>
      <!-- END TABLE HOVER -->
   </div>
</div>
<!-- end delete intrigation tools -->
<!-- end vendor market tools -->