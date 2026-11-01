<div class="row">
   <div class="col-md-12">
      <div class="top-content" id="dashboard">
         <h2 class="page-title">Dashboard</h2>
         <p>Dashboard with HTTP GET request.</p>
         <p>API token is required for the authentication of the calling program to the API.</p>
         <p>Initially, dashboard API provides just basic details about users like refer status and refer links, but you can fetch more data by sending comma (,) separeted optional parameters in the "includes" key, check the below table for more details.</p>
         <p>For purpose of use in the dashboard here, we provided limited market tools available for users. If you want to access all tools please check the separate My Affiliate Links API.</p>
         <div class="row">
            <div class="col-md-6">
               <!-- TABLE HOVER -->
               <div class="panel white">
                  <div class="panel-heading">
                     <h3 class="panel-title">Request :  </h3><br>
                     <span class="text-success">GET</span> : <?=base_url();?>User/dashboard</p>
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
                           <tr>
                              <td>includes</td>
                              <td><code>string</code></td>
                              <td><code>optional</code></td>
                              <td>plan_details,totals_count,top_affiliate,notifications,market_tools,chart_data</td>
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
                     <pre class="response-view">{"refer_status": true,"unique_reseller_link": "http://localhost/aff/4-0-0-7/register/ODg=","top_affiliate": [{"amount": "41","all_commition": "41","user_id": "65","type": "user","avatar": "","firstname": "aff11","lastname": "aff11","Country": "13","email": "aff11@gmail.com","sortname": "AU"}],"isMembershipAccess": true,"user_plan": {"total_day":"7","expire_at":"2021-06-18 15:20:14","started_at":"2021-06-11 15:20:14","status_id":"1","is_active":"1","is_lifetime":"0"},"user_totals": {"click_localstore_total":0,"click_localstore_commission":null,"sale_localstore_total":0,"sale_localstore_commission":0,"sale_localstore_count":0,"click_external_total":0,"click_external_commission":null,"order_external_total":null,"order_external_count":"0","order_external_commission":null,"click_action_total":0,"click_action_commission":null,"vendor_click_localstore_total":0,"vendor_click_localstore_commission_pay":null,"vendor_sale_localstore_total":null,"vendor_sale_localstore_commission_pay":null,"vendor_sale_localstore_count":null,"vendor_click_external_total":0,"vendor_click_external_commission_pay":null,"vendor_action_external_total":0,"vendor_action_external_commission_pay":null,"vendor_order_external_commission_pay":null,"vendor_order_external_count":"0","vendor_order_external_total":null,"click_form_total":0,"click_form_commission":null,"wallet_unpaid_amount":10,"wallet_unpaid_count":1,"user_balance":10},"refer_total": {"total_product_click": {"amounts": null,"clicks": 0},"total_ganeral_click": {"total_clicks": "0"},"total_action": {"click_count": "0"},"total_product_sale": {"amounts": null,"counts": "0","paid": null,"request": null,"unpaid": null}},"user_totals_week": "$10.00","user_totals_month": "$10.00","user_totals_year": "$10.00","notifications": [{"notification_id": "18","notification_viewfor": "admin","notification_view_user_id": null,"notification_title": "New User Registration","notification_url": "/userslist/88","notification_description": "fresh user register as a  on affiliate Program on 2021-06-11 16:44:16","notification_actionID": "88","notification_type": "user","store_contactus_description": null,"notification_is_read": "0","notification_ipaddress": "::1","notification_created_date": "2021-06-11 16:44:16"}],"chart": {"order_total": {"January": 0,"February": 0,"March": 0,"April": 0,"May": 0,"June": 0,"July": 0,"August": 0,"September": 0,"October": 0,"November": 0,"December": 0},"order_count": {"January": 0,"February": 0,"March": 0,"April": 0,"May": 0,"June": 0,"July": 0,"August": 0,"September": 0,"October": 0,"November": 0,"December": 0},"order_commission": {"January": 0,"February": 0,"March": 0,"April": 0,"May": 0,"June": 0,"July": 0,"August": 0,"September": 0,"October": 0,"November": 0,"December": 0},"action_commission": {"January": 0,"February": 0,"March": 0,"April": 0,"May": 0,"June": 0,"July": 0,"August": 0,"September": 0,"October": 0,"November": 0,"December": 0},"action_count": {"January": 0,"February": 0,"March": 0,"April": 0,"May": 0,"June": 0,"July": 0,"August": 0,"September": 0,"October": 0,"November": 0,"December": 0},"keys": {"1": "January","2": "February","3": "March","4": "April","5": "May","6": "June","7": "July","8": "August","9": "September","10": "October","11": "November","12": "December"}},"market_tools":{"form_default_commission":{"recaptcha":"","product_commission_type":"percentage","product_commission":"15","product_ppc":"7","product_noofpercommission":"1","form_recursion":"","recursion_custom_time":"0","recursion_endtime":null},"default_commition":{"click_allow":"multiple","product_commission_type":"percentage","product_commission":"10","product_ppc":"5","product_noofpercommission":"1","product_recursion":"","recursion_custom_time":"0","recursion_endtime":null},"data":[{"id":"11","redirectLocation":["https://google.com?af_id=blpHNUswNmVNQzViUitDK3IyWDZnZz09-ODgtMjA="],"program_id":"4","name":"Vendor LInk Ads","vendor_id":"75","program_name":"vendor program","target_link":"https://google.com","status":"1","action_click":"0","action_amount":"0","general_click":"0","general_amount":"0","admin_action_click":"0","admin_action_amount":"0","admin_general_click":"0","admin_general_amount":"0","_tool_type":"program","type":"Link ads","_type":"link_ads","commission_type":"percentage","commission_sale":"30","commission_number_of_click":"1","commission_click_commission":"5","click_status":"1","sale_status":"1","admin_commission_type":"percentage","admin_commission_sale":"25","admin_commission_number_of_click":"1","admin_commission_click_commission":"1","admin_click_status":"1","admin_sale_status":"1","recursion":"","recursion_custom_time":"0","username":"aff1","recursion_endtime":null,"featured_image":"EfdCYkajeqi9lDxMQNTcyK3PBnL4rXZF.png","total_sale_amount":"$0.00","total_click_amount":"$0.00","total_action_click_amount":"$0.00","total_general_click_amount":"$0.00","total_sale_count":0,"total_click_count":0,"total_action_click_count":0,"total_general_click_count":0,"tool_type":"Program","created_at":"11-06-2021 10:20 AM","product_created_date":"11-06-2021 10:20 AM","is_tool":1,"slug":"","groups":""}]}}</pre>
                  </div>
               </div>
               <!-- END TABLE HOVER -->
            </div>
         </div>
      </div>
   </div>
</div>