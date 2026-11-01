<!-- start subscription plan -->
<!-- start membership plan -->
<div class="row">
   <div class="col-md-12">
      <div class="page-intro" id="subscription_plan">
         <h2 class="page-title">Subscription Plan</h2>
         <p>Admin can create unlimited packages/plans of Time/Price. <br>
            also display your plan history. if you want buy any plan to process set in our web. so you can purchase our plan using our web. here only display our packages and price.
         </p>
      </div>
      <div class="top-content" id="buy_membership_plan">
         <h3 class="page-title">Buy Memebership Plan</h3>
         <p>
            Buy memebership plan request with HTTP GET request.
         </p>
         <p>
            API token is required for the authentication of the calling program to the API.
         </p>
         <p>
            Here display all plan history and all available payment method. also display here your active plan details. 
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
            <span class="text-success">GET</span> : <?=base_url();?>Subscription_Plan/get_membership_plan</p>
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
            <pre class="response-view"><?= htmlspecialchars('{
    "status": true,
    "message": "get membership plan successfully",
    "data": {
        "notcheckmember": 1,
        "MembershipSetting": {
            "status": "1",
            "notificationbefore": "10",
            "default_plan_id": "1",
            "default_affiliate_plan_id": "2",
            "default_vendor_plan_id": ""
        },
        "plans": [
            {
                "id": 2,
                "name": "test",
                "type": "free",
                "billing_period": "monthly",
                "price": 0,
                "special": 0,
                "custom_period": 0,
                "have_trail": 0,
                "free_trail": 0,
                "total_day": 30,
                "bonus": 10,
                "commission_sale_status": 0,
                "level_id": 0,
                "status": 1,
                "user_type": 1,
                "campaign": null,
                "product": null,
                "description": "",
                "plan_icon": null,
                "label_text": "Free 30 Days Trial",
                "label_background": "#FF9900",
                "label_color": "#FFFFFF",
                "sort_order": 0,
                "updated_at": "2022-08-12 10:18:29",
                "created_at": "2022-07-28 08:08:14"
            },
            {
                "id": 3,
                "name": "vendor package",
                "type": "paid",
                "billing_period": "monthly",
                "price": 10,
                "special": 7,
                "custom_period": 0,
                "have_trail": 0,
                "free_trail": 0,
                "total_day": 30,
                "bonus": 1,
                "commission_sale_status": 0,
                "level_id": 0,
                "status": 1,
                "user_type": 2,
                "campaign": null,
                "product": 5,
                "description": "<p>vendor package</p><p>vendor package</p><p>vendor package</p><p>vendor package<br></p>",
                "plan_icon": null,
                "label_text": "vendor package",
                "label_background": "#0000FF",
                "label_color": "#FFFFFF",
                "sort_order": 0,
                "updated_at": "2022-08-21 18:05:02",
                "created_at": "2022-07-28 09:06:56"
            }
        ],
        "methods": {
            "bank_transfer": {
                "is_install": "1",
                "title": "Bank Transfer",
                "icon": "assets/payment_gateway/bank-transfer.png",
                "name": "bank_transfer"
            },
            "flutterwave": {
                "is_install": "1",
                "title": "Flutterwave",
                "icon": "assets/payment_gateway/flutterwave.png",
                "name": "flutterwave"
            },
            "paypal": {
                "is_install": "1",
                "title": "Paypal",
                "icon": "assets/payment_gateway/paypal.png",
                "name": "paypal"
            },
            "paypalstandard": {
                "is_install": "1",
                "title": "Paypal Standard",
                "icon": "assets/payment_gateway/paypal.png",
                "name": "paypalstandard"
            },
            "paystack": {
                "is_install": "1",
                "title": "paystack",
                "icon": "assets/payment_gateway/paystack.png",
                "name": "paystack"
            },
            "razorpay": {
                "is_install": "1",
                "title": "Razorpay",
                "icon": "assets/payment_gateway/razorpay.png",
                "name": "razorpay"
            },
            "skrill": {
                "is_install": "1",
                "title": "Skrill",
                "icon": "assets/payment_gateway/skrill.png",
                "name": "skrill"
            },
            "stripe": {
                "is_install": "1",
                "environment": "0",
                "test_public_key": "pk_test_51HIplPAflMT1sQX0od48Wk2ZSXpFfk9c2Oy19lJBBDTqgla6Q8uzZpWjF39oeNt05ROLbFAOIZnrXEKzZJiqr4g200HSDMgxRR",
                "test_secret_key": "sk_test_51HIplPAflMT1sQX0Vy58Mh9fEIzVJrMxWnRBK2mHnBgafMhO96LEpYDr9ayoEXp1MaJfDnQ1VAI9LsaSjbUJGpir006JxQIE6W",
                "live_public_key": "",
                "live_secret_key": "",
                "order_success_status": "1",
                "order_failed_status": "5",
                "title": "Stripe",
                "icon": "assets/payment_gateway/stripe.png",
                "name": "stripe"
            },
            "toyyibpay": {
                "is_install": "1",
                "toyyibpay_userSecretKey": "g5e90x5p-zpuy-p70s-zkdw-4e62egju15mq",
                "toyyibpay_category_id": "jjm6glon",
                "order_success_status_id": "1",
                "pending_status_id": "6",
                "order_failed_status_id": "5",
                "title": "Toyyibpay",
                "icon": "assets/payment_gateway/toyyibpay.png",
                "name": "toyyibpay"
            },
            "xendit": {
                "is_install": "1",
                "title": "Xendit",
                "icon": "assets/payment_gateway/xendit.png",
                "name": "xendit"
            },
            "yappy": {
                "is_install": "1",
                "title": "Yappy",
                "icon": "assets/payment_gateway/yappy.png",
                "name": "yappy"
            },
            "yookassa": {
                "is_install": "1",
                "title": "Yookassa",
                "icon": "assets/payment_gateway/yookassa.png",
                "name": "yookassa"
            }
        }
    }
}') ?></pre>
         </div>
      </div>
      <!-- END TABLE HOVER -->
   </div>
</div>
<!-- end membership plan -->
<!-- start plan history -->
<div class="top-content" id="plan_history">
   <h3 class="page-title">Plan History</h3>
   <p>
      Plan history request with HTTP GET request.
   </p>
   <p>
      API token is required for the authentication of the calling program to the API.
   </p>
   <p>
      Here display all my purchase plan
   </p>
   <p>
      Here product order list data display as pagignation wise. only data get using pass page_id and per_page parameter. <br>
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
            <span class="text-warning"> POST </span> : <?=base_url();?>Subscription_Plan/purchase_history</p>
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
            <pre class="response-view">{"status":true,"message":"get purchase plan successfully",
               "data":{"notcheckmember":1,
               "plans":{"current_page":1,"data": [
                {
                    "id": 20,
                    "plan_id": 2,
                    "user_id": 4,
                    "total_day": 30,
                    "expire_at": "2022-10-14 08:43:18",
                    "started_at": "2022-09-14 08:43:18",
                    "status_id": 1,
                    "is_active": 1,
                    "is_lifetime": 0,
                    "payment_method": "",
                    "payment_details": "[]",
                    "total": 0,
                    "bonus_commission": 10,
                    "expire_mail_sent": 0,
                    "created_at": "2022-09-14 08:43:18",
                    "plan": {
                        "id": 2,
                        "name": "test",
                        "type": "free",
                        "billing_period": "monthly",
                        "price": 0,
                        "special": 0,
                        "custom_period": 0,
                        "have_trail": 0,
                        "free_trail": 0,
                        "total_day": 30,
                        "bonus": 10,
                        "commission_sale_status": 0,
                        "level_id": 0,
                        "status": 1,
                        "user_type": 1,
                        "campaign": null,
                        "product": null,
                        "description": "",
                        "plan_icon": null,
                        "label_text": "Free 30 Days Trial",
                        "label_background": "#FF9900",
                        "label_color": "#FFFFFF",
                        "sort_order": 0,
                        "updated_at": "2022-08-12 10:18:29",
                        "created_at": "2022-07-28 08:08:14"
                    }
                },
                {
                    "id": 17,
                    "plan_id": 2,
                    "user_id": 4,
                    "total_day": 30,
                    "expire_at": "2022-10-13 09:11:59",
                    "started_at": "2022-09-13 09:11:59",
                    "status_id": 1,
                    "is_active": 0,
                    "is_lifetime": 0,
                    "payment_method": "",
                    "payment_details": "[]",
                    "total": 0,
                    "bonus_commission": 10,
                    "expire_mail_sent": 0,
                    "created_at": "2022-09-13 09:11:59",
                    "plan": {
                        "id": 2,
                        "name": "test",
                        "type": "free",
                        "billing_period": "monthly",
                        "price": 0,
                        "special": 0,
                        "custom_period": 0,
                        "have_trail": 0,
                        "free_trail": 0,
                        "total_day": 30,
                        "bonus": 10,
                        "commission_sale_status": 0,
                        "level_id": 0,
                        "status": 1,
                        "user_type": 1,
                        "campaign": null,
                        "product": null,
                        "description": "",
                        "plan_icon": null,
                        "label_text": "Free 30 Days Trial",
                        "label_background": "#FF9900",
                        "label_color": "#FFFFFF",
                        "sort_order": 0,
                        "updated_at": "2022-08-12 10:18:29",
                        "created_at": "2022-07-28 08:08:14"
                    }
                }
            ],"first_page_url":"\/?page=1","from":null,"last_page":1,"last_page_url":"\/?page=1","next_page_url":null,"path":"\/","per_page":"1","prev_page_url":null,"to":null,"total":0}}}</pre>
         </div>
      </div>
      <!-- END TABLE HOVER -->
   </div>
</div>
<!-- end plan history -->
<!-- end subscription plan -->