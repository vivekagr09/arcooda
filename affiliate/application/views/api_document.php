<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Api documentation!</title>
  </head>
  <body>
   
<div class="jumbotron">
  <div class="container text-center">
    <h1>Api Documentation</h1>      
  </div>
</div>
  
<div class="container-fluid bg-3">    
  
  <h3 class="text-center">Registration</h3><br>
  <h4 class="text-left">Method :  <span class="text-success">POST</span></h4><br>
  <h4 class="text-left text-warning">URL : http://192.168.1.153/aff/ver-4-0-0-5/User/registarion</h4><br>
  <h5 class="text-left">Parameter</h5><br>
  <div class="row">
    <div class="col-sm-12">
      <table class="table table-dark text-center">
      <thead>
        <tr>
          <th scope="col">firstname</th>
          <th scope="col">lastname</th>
          <th scope="col">username</th>
          <th scope="col">email</th>
          <th scope="col">password</th>
          <th scope="col">cpassword</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row">test</th>
          <td>user</td>
          <td>test_user</td>
          <td>test_user@gmail.com</td>
          <td>test@123</td>
          <td>test@123</td>
        </tr>
       
      </tbody>
    </table>
    <h5 class="text-left">Response</h5><br>
       <pre id="registration" style= 
        "color:green; font-size: 20px; font-weight: bold;"> 
    </pre> 
    </div>
    
  </div>

  <!-- login -->

  <h3 class="text-center">Login</h3><br>
  <h4 class="text-left">Method :  <span class="text-success">POST</span></h4><br>
  <h4 class="text-left text-warning">URL : http://192.168.1.153/aff/ver-4-0-0-5/User/login</h4><br>
  <h5 class="text-left">Parameter</h5><br>
  <div class="row">
    <div class="col-sm-12">
      <table class="table table-dark text-center">
      <thead>
        <tr>
          <th scope="col">username</th>
          <th scope="col">password</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row">test_user</th>
          <td>test@123</td>
        </tr>
       
      </tbody>
    </table>
    <h5 class="text-left">Response</h5><br>
      <pre id="login" style= 
        "color:green; font-size: 20px; font-weight: bold;"> 
    </pre> 
    </div>
    
  </div>

  <!-- Dashboard -->

  <h3 class="text-center">Dashboard</h3><br>
  <h4 class="text-left">Method :  <span class="text-success">GET</span></h4><br>
  <h4 class="text-left text-warning">URL : http://192.168.1.153/aff/ver-4-0-0-5/User/dashboard</h4><br>
  <div class="row">
    <div class="col-sm-12">
      

    <h4 class="text-left text-success">Request Headers</h4><br>
  <h4 class="text-left text-warning">Authorization : eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.IjIwMjEtMDMtMjEgMDk6MDg6MTR1c2VyMSI.PafbosOYhrbso8B7Ic4qug4aK_ro_gUpdhJt-fpjdWo
</h4><br>
    <h5 class="text-left">Response</h5><br>

      
    <pre id="dashboard" style= 
        "color:green; font-size: 20px; font-weight: bold;"> 
    </pre> 

     
    </div>
    
  </div>

<!-- Integration Category List -->

  <h3 class="text-center">Integration Category List</h3><br>
  <h4 class="text-left">Method :  <span class="text-success">GET</span></h4><br>
  <h4 class="text-left text-warning">URL : http://192.168.1.153/aff/ver-4-0-0-5/Integration_Category/get_integration_category</h4><br>
   <div class="row">
    <div class="col-sm-12">
      

    <h4 class="text-left text-success">Request Headers</h4><br>
  <h4 class="text-left text-warning">Authorization : eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.IjIwMjEtMDMtMjEgMDk6MDg6MTR1c2VyMSI.PafbosOYhrbso8B7Ic4qug4aK_ro_gUpdhJt-fpjdWo
</h4><br>
    <h5 class="text-left">Response</h5><br>

      
    <pre id="integration_category_list" style= 
        "color:green; font-size: 20px; font-weight: bold;"> 
    </pre> 

     
    </div>
    
  </div>


<!-- Store Category List -->

  <h3 class="text-center">Store Category List</h3><br>
  <h4 class="text-left">Method :  <span class="text-success">GET</span></h4><br>
  <h4 class="text-left text-warning">URL : http://192.168.1.153/aff/ver-4-0-0-5/Store_Category/get_store_category</h4><br>
   <div class="row">
    <div class="col-sm-12">
      

    <h4 class="text-left text-success">Request Headers</h4><br>
  <h4 class="text-left text-warning">Authorization : eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.IjIwMjEtMDMtMjEgMDk6MDg6MTR1c2VyMSI.PafbosOYhrbso8B7Ic4qug4aK_ro_gUpdhJt-fpjdWo
</h4><br>
    <h5 class="text-left">Response</h5><br>

      
    <pre id="store_category_list" style= 
        "color:green; font-size: 20px; font-weight: bold;"> 
    </pre> 

     
    </div>
    
  </div>

<!--  My Affiliate Links List -->

  <h3 class="text-center">My Affiliate Links List</h3><br>
  <h4 class="text-left">Method :  <span class="text-success">GET</span></h4><br>
  <h4 class="text-left text-warning">URL : http://192.168.1.153/aff/ver-4-0-0-5/User/my_affiliate_links</h4><br>
   <div class="row">
    <div class="col-sm-12">
      

    <h4 class="text-left text-success">Request Headers</h4><br>
  <h4 class="text-left text-warning">Authorization : eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.IjIwMjEtMDMtMjEgMDk6MDg6MTR1c2VyMSI.PafbosOYhrbso8B7Ic4qug4aK_ro_gUpdhJt-fpjdWo
</h4><br>
    <h5 class="text-left">Response</h5><br>

      
    <pre id="my_affiliate_links" style= 
        "color:green; font-size: 20px; font-weight: bold;"> 
    </pre> 

     
    </div>
    
  </div>


</div><br>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script type="text/javascript">
       // var el_up = document.getElementById("GFG_UP"); 
        var dashboard = document.getElementById("dashboard"); 
        var login = document.getElementById("login"); 
        var registration = document.getElementById("registration"); 
        var integration_category_list = document.getElementById("integration_category_list"); 
        var store_category_list = document.getElementById("store_category_list"); 
        var my_affiliate_links = document.getElementById("my_affiliate_links"); 
        var dashboard_obj = {
          "status": true,
          "message": "dahsboard data get successfully",
          "data": {
              "user_plan": {
                  "total_day": "365",
                  "expire_at": "2022-03-21 06:25:37",
                  "started_at": "2021-03-21 06:25:37",
                  "status_id": "1",
                  "is_active": "1",
                  "is_lifetime": "0"
              },
              "user_totals": {
                  "click_localstore_total": 0,
                  "click_localstore_commission": null,
                  "sale_localstore_total": 0,
                  "sale_localstore_commission": 0,
                  "sale_localstore_count": 0,
                  "click_external_total": 2,
                  "click_external_commission": "2",
                  "order_external_total": "61938.959333333",
                  "order_external_count": "14",
                  "order_external_commission": "15484.739833333299",
                  "click_action_total": 1,
                  "click_action_commission": "5",
                  "vendor_click_localstore_total": 0,
                  "vendor_click_localstore_commission_pay": null,
                  "vendor_sale_localstore_total": null,
                  "vendor_sale_localstore_commission_pay": null,
                  "vendor_sale_localstore_count": null,
                  "vendor_click_external_total": 0,
                  "vendor_click_external_commission_pay": null,
                  "vendor_action_external_total": 0,
                  "vendor_action_external_commission_pay": null,
                  "vendor_order_external_commission_pay": null,
                  "vendor_order_external_count": "0",
                  "vendor_order_external_total": null,
                  "click_form_total": 0,
                  "click_form_commission": null,
                  "wallet_on_hold_amount": 7911.815666666701,
                  "wallet_unpaid_amounton_hold_count": 9,
                  "wallet_unpaid_amount": 2553.0908333333,
                  "wallet_unpaid_count": 4,
                  "wallet_request_sent_amount": 5,
                  "wallet_request_sent_count": 1,
                  "wallet_accept_amount": 5021.833333333299,
                  "wallet_accept_count": 3,
                  "user_balance": 7579.9241666665985
              },
              "refer_total": {
                  "total_product_click": {
                      "amounts": null,
                      "clicks": 0
                  },
                  "total_ganeral_click": {
                      "total_clicks": "0"
                  },
                  "total_action": {
                      "click_count": "0"
                  },
                  "total_product_sale": {
                      "amounts": null,
                      "counts": "0",
                      "paid": null,
                      "request": null,
                      "unpaid": null
                  }
              },
              "affiliate_store_url": "http://192.168.1.153/aff/ver-4-0-0-5/store/Mw==",
              "unique_reseller_link": "http://192.168.1.153/aff/ver-4-0-0-5/register/Mw==",
              "top_affiliate": [
                  {
                      "all_commition": "7579.9241666665985",
                      "user_id": "3",
                      "type": "user",
                      "avatar": "dhbFk6BWZr37QvOaMtC9io4uem1qKNLf.jpg",
                      "firstname": "user1",
                      "lastname": "user1",
                      "Country": "212",
                      "email": "user1@affiliatepro.org",
                      "sortname": "CH"
                  }
              ],
              "affiliate_links": [
                  {
                      "id": "9",
                      "redirectLocation": [
                          "http://192.168.1.153/test/click.php?af_id=cnBNbjhvZUxoMitQYVREQzhkMktQUT09-My02"
                      ],
                      "program_id": "0",
                      "name": "click test",
                      "vendor_id": "0",
                      "program_name": null,
                      "target_link": "http://192.168.1.153/test/click.php",
                      "status": "1",
                      "action_click": "0",
                      "action_amount": "0",
                      "general_click": "1",
                      "general_amount": "1",
                      "admin_action_click": "0",
                      "admin_action_amount": "0",
                      "admin_general_click": "0",
                      "admin_general_amount": "0",
                      "_tool_type": "general_click",
                      "type": "Banner",
                      "_type": "banner",
                      "commission_type": null,
                      "commission_sale": null,
                      "commission_number_of_click": null,
                      "commission_click_commission": null,
                      "click_status": null,
                      "sale_status": null,
                      "admin_commission_type": null,
                      "admin_commission_sale": null,
                      "admin_commission_number_of_click": null,
                      "admin_commission_click_commission": null,
                      "admin_click_status": null,
                      "admin_sale_status": null,
                      "recursion": "",
                      "recursion_custom_time": "0",
                      "username": null,
                      "recursion_endtime": null,
                      "featured_image": "0Goh4ckXjeOK3MCY9m6FgzNBl7trT5Ew.jpg",
                      "total_sale_amount": "$0.00",
                      "total_click_amount": "$0.00",
                      "total_action_click_amount": "$0.00",
                      "total_general_click_amount": "$1.00",
                      "total_sale_count": 0,
                      "total_click_count": 0,
                      "total_action_click_count": 0,
                      "total_general_click_count": 1,
                      "tool_type": "General click",
                      "created_at": "03-03-2021 12:08 PM",
                      "product_created_date": "03-03-2021 12:08 PM",
                      "is_tool": 1,
                      "slug": "clickslugtest"
                  },
                  {
                      "id": "6",
                      "redirectLocation": [
                          "http://192.168.1.153/test/order.php?af_id=bi9RcXBpeXZDMDkzSFN4aUUwN0plUT09-My01"
                      ],
                      "program_id": "2",
                      "name": "Sale test slug",
                      "vendor_id": "0",
                      "program_name": "sale program",
                      "target_link": "http://192.168.1.153/test/order.php",
                      "status": "1",
                      "action_click": "0",
                      "action_amount": "0",
                      "general_click": "0",
                      "general_amount": "0",
                      "admin_action_click": "0",
                      "admin_action_amount": "0",
                      "admin_general_click": "0",
                      "admin_general_amount": "0",
                      "_tool_type": "program",
                      "type": "Banner",
                      "_type": "banner",
                      "commission_type": "percentage",
                      "commission_sale": "25",
                      "commission_number_of_click": "1",
                      "commission_click_commission": "1",
                      "click_status": "1",
                      "sale_status": "1",
                      "admin_commission_type": null,
                      "admin_commission_sale": null,
                      "admin_commission_number_of_click": null,
                      "admin_commission_click_commission": null,
                      "admin_click_status": null,
                      "admin_sale_status": null,
                      "recursion": "",
                      "recursion_custom_time": "0",
                      "username": null,
                      "recursion_endtime": null,
                      "featured_image": "MtIPazx7VheorF4OfUqANbwBETky8Ljn.jpg",
                      "total_sale_amount": "$7,572.92",
                      "total_click_amount": "$1.00",
                      "total_action_click_amount": "$0.00",
                      "total_general_click_amount": "$0.00",
                      "total_sale_count": 14,
                      "total_click_count": 1,
                      "total_action_click_count": 0,
                      "total_general_click_count": 0,
                      "tool_type": "Program",
                      "created_at": "24-02-2021 01:54 PM",
                      "product_created_date": "24-02-2021 01:54 PM",
                      "is_tool": 1,
                      "slug": ""
                  },
                  {
                      "id": "4",
                      "redirectLocation": [
                          "http://192.168.1.153/test/action.php?af_id=U2s2ODR6LytLUEp1dHg5R0hZVGFyQT09-My0z"
                      ],
                      "program_id": "0",
                      "name": "Action Test New",
                      "vendor_id": "0",
                      "program_name": null,
                      "target_link": "http://192.168.1.153/test/action.php",
                      "status": "1",
                      "action_click": "1",
                      "action_amount": "5",
                      "general_click": "0",
                      "general_amount": "0",
                      "admin_action_click": "0",
                      "admin_action_amount": "0",
                      "admin_general_click": "0",
                      "admin_general_amount": "0",
                      "_tool_type": "action",
                      "type": "Banner",
                      "_type": "banner",
                      "commission_type": null,
                      "commission_sale": null,
                      "commission_number_of_click": null,
                      "commission_click_commission": null,
                      "click_status": null,
                      "sale_status": null,
                      "admin_commission_type": null,
                      "admin_commission_sale": null,
                      "admin_commission_number_of_click": null,
                      "admin_commission_click_commission": null,
                      "admin_click_status": null,
                      "admin_sale_status": null,
                      "recursion": "",
                      "recursion_custom_time": "0",
                      "username": null,
                      "recursion_endtime": null,
                      "featured_image": "1XJWa4AQGgxDYivoKOunrlVZfpzBkNRF.jpg",
                      "total_sale_amount": "$0.00",
                      "total_click_amount": "$0.00",
                      "total_action_click_amount": "$5.00",
                      "total_general_click_amount": "$0.00",
                      "total_sale_count": 0,
                      "total_click_count": 0,
                      "total_action_click_count": 1,
                      "total_general_click_count": 0,
                      "tool_type": "Action",
                      "created_at": "11-02-2021 08:16 AM",
                      "product_created_date": "11-02-2021 08:16 AM",
                      "is_tool": 1,
                      "slug": ""
                  },
                  {
                      "id": "2",
                      "redirectLocation": [
                          "https://4.natanphp.com/test/test.php?af_id=S1diV05HSzE5WlkwWDJaYWRyNE1Mdz09-My0x"
                      ],
                      "program_id": "0",
                      "name": "Something",
                      "vendor_id": "0",
                      "program_name": null,
                      "target_link": "https://4.natanphp.com/test/test.php",
                      "status": "1",
                      "action_click": "0",
                      "action_amount": "0",
                      "general_click": "2",
                      "general_amount": "1",
                      "admin_action_click": "0",
                      "admin_action_amount": "0",
                      "admin_general_click": "0",
                      "admin_general_amount": "0",
                      "_tool_type": "general_click",
                      "type": "Banner",
                      "_type": "banner",
                      "commission_type": null,
                      "commission_sale": null,
                      "commission_number_of_click": null,
                      "commission_click_commission": null,
                      "click_status": null,
                      "sale_status": null,
                      "admin_commission_type": null,
                      "admin_commission_sale": null,
                      "admin_commission_number_of_click": null,
                      "admin_commission_click_commission": null,
                      "admin_click_status": null,
                      "admin_sale_status": null,
                      "recursion": "",
                      "recursion_custom_time": "0",
                      "username": null,
                      "recursion_endtime": null,
                      "featured_image": "0KV7lgdczPfMSykxBYb36JFNOoXUmWvn.jpg",
                      "total_sale_amount": "$0.00",
                      "total_click_amount": "$0.00",
                      "total_action_click_amount": "$0.00",
                      "total_general_click_amount": "$0.00",
                      "total_sale_count": 0,
                      "total_click_count": 0,
                      "total_action_click_count": 0,
                      "total_general_click_count": 0,
                      "tool_type": "General click",
                      "created_at": "01-02-2021 12:08 PM",
                      "product_created_date": "01-02-2021 12:08 PM",
                      "is_tool": 1,
                      "slug": "click-test-slug"
                  }
              ],
              "form_default_commission": {
                  "recaptcha": "",
                  "product_commission_type": "percentage",
                  "product_commission": "10",
                  "product_ppc": "5",
                  "product_noofpercommission": "1",
                  "form_recursion": "",
                  "recursion_custom_time": "0",
                  "recursion_endtime": null
              },
              "default_commition": {
                  "click_allow": "single",
                  "product_commission_type": "percentage",
                  "product_commission": "25",
                  "product_ppc": "1",
                  "product_noofpercommission": "1",
                  "product_recursion": "",
                  "recursion_custom_time": "0",
                  "recursion_endtime": null
              },
              "data_list": null,
              "pagination": 20
          }
        }; 
        var login_obj = {
          "status": true,
          "message": "user login successfully",
          "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.IjIwMjEtMDMtMjEgMDk6MDg6MTR1c2VyMSI.PafbosOYhrbso8B7Ic4qug4aK_ro_gUpdhJt-fpjdWo"
        };

        var registration_obj = {
            "status": true,
            "message": "user registration successfully"
        };

        var integration_category_list_obj = {
            "status": true,
            "message": "integration category list get successfully",
            "data": [
                {
                    "id": "2",
                    "name": "test 2"
                },
                {
                    "id": "1",
                    "name": "test 1"
                }
            ]
        };

        var store_category_list_obj = {
          "status": true,
          "message": "store list get successfully",
          "data": [
              {
                  "id": "8",
                  "name": "Cat Main 4"
              },
              {
                  "id": "7",
                  "name": "Cat Main 3"
              },
              {
                  "id": "6",
                  "name": "Cat Main 2"
              },
              {
                  "id": "5",
                  "name": "Cat Main 1"
              },
              {
                  "id": "4",
                  "name": "sub1"
              },
              {
                  "id": "3",
                  "name": "clothing and footwear"
              },
              {
                  "id": "2",
                  "name": "shoes"
              },
              {
                  "id": "1",
                  "name": "Shirts"
              }
          ]
      };

      var integration_category_list_obj = {
            "status": true,
            "message": "integration category list get successfully",
            "data": [
                {
                    "id": "2",
                    "name": "test 2"
                },
                {
                    "id": "1",
                    "name": "test 1"
                }
            ]
        };

        var my_affiliate_links_obj = {
          "form_default_commission": {
              "recaptcha": "",
              "product_commission_type": "percentage",
              "product_commission": "10",
              "product_ppc": "5",
              "product_noofpercommission": "1",
              "form_recursion": "",
              "recursion_custom_time": "0",
              "recursion_endtime": null
          },
          "default_commition": {
              "click_allow": "single",
              "product_commission_type": "percentage",
              "product_commission": "25",
              "product_ppc": "1",
              "product_noofpercommission": "1",
              "product_recursion": "",
              "recursion_custom_time": "0",
              "recursion_endtime": null
          },
          "tools": [
              {
                  "id": "12",
                  "redirectLocation": [
                      "https://google.com?af_id=MEJvZG5xSVdOdFlGM1hnUzhLaW1sZz09-My05"
                  ],
                  "program_id": "3",
                  "name": "vendor banner",
                  "vendor_id": "3",
                  "program_name": "vendor program",
                  "target_link": "https://google.com",
                  "status": "1",
                  "action_click": "0",
                  "action_amount": "0",
                  "general_click": "0",
                  "general_amount": "0",
                  "admin_action_click": "0",
                  "admin_action_amount": "0",
                  "admin_general_click": "0",
                  "admin_general_amount": "0",
                  "_tool_type": "program",
                  "type": "Banner",
                  "_type": "banner",
                  "commission_type": "percentage",
                  "commission_sale": "10",
                  "commission_number_of_click": "1",
                  "commission_click_commission": "0.25",
                  "click_status": "1",
                  "sale_status": "1",
                  "admin_commission_type": "percentage",
                  "admin_commission_sale": "5",
                  "admin_commission_number_of_click": "1",
                  "admin_commission_click_commission": "0.15",
                  "admin_click_status": "1",
                  "admin_sale_status": "1",
                  "recursion": "",
                  "recursion_custom_time": "0",
                  "username": "user1",
                  "recursion_endtime": null,
                  "featured_image": "gP61NkqtUu4ST82WBjVnYX7HxdfLCliv.jpg",
                  "total_sale_amount": "$0.00",
                  "total_click_amount": "$0.00",
                  "total_action_click_amount": "$0.00",
                  "total_general_click_amount": "$0.00",
                  "total_sale_count": 0,
                  "total_click_count": 0,
                  "total_action_click_count": 0,
                  "total_general_click_count": 0,
                  "tool_type": "Program",
                  "created_at": "22-03-2021 07:01 PM",
                  "product_created_date": "22-03-2021 07:01 PM",
                  "is_tool": 1,
                  "slug": ""
              },
              {
                  "id": "9",
                  "redirectLocation": [
                      "http://localhost/test/click.php?af_id=cnBNbjhvZUxoMitQYVREQzhkMktQUT09-My02"
                  ],
                  "program_id": "0",
                  "name": "click test",
                  "vendor_id": "0",
                  "program_name": null,
                  "target_link": "http://localhost/test/click.php",
                  "status": "1",
                  "action_click": "0",
                  "action_amount": "0",
                  "general_click": "1",
                  "general_amount": "1",
                  "admin_action_click": "0",
                  "admin_action_amount": "0",
                  "admin_general_click": "0",
                  "admin_general_amount": "0",
                  "_tool_type": "general_click",
                  "type": "Banner",
                  "_type": "banner",
                  "commission_type": null,
                  "commission_sale": null,
                  "commission_number_of_click": null,
                  "commission_click_commission": null,
                  "click_status": null,
                  "sale_status": null,
                  "admin_commission_type": null,
                  "admin_commission_sale": null,
                  "admin_commission_number_of_click": null,
                  "admin_commission_click_commission": null,
                  "admin_click_status": null,
                  "admin_sale_status": null,
                  "recursion": "",
                  "recursion_custom_time": "0",
                  "username": null,
                  "recursion_endtime": null,
                  "featured_image": "0Goh4ckXjeOK3MCY9m6FgzNBl7trT5Ew.jpg",
                  "total_sale_amount": "$0.00",
                  "total_click_amount": "$0.00",
                  "total_action_click_amount": "$0.00",
                  "total_general_click_amount": "$1.00",
                  "total_sale_count": 0,
                  "total_click_count": 0,
                  "total_action_click_count": 0,
                  "total_general_click_count": 1,
                  "tool_type": "General click",
                  "created_at": "03-03-2021 12:08 PM",
                  "product_created_date": "03-03-2021 12:08 PM",
                  "is_tool": 1,
                  "slug": "clickslugtest"
              }
          ],
          "data_list": [
              {
                  "id": "12",
                  "redirectLocation": [
                      "https://google.com?af_id=MEJvZG5xSVdOdFlGM1hnUzhLaW1sZz09-My05"
                  ],
                  "program_id": "3",
                  "name": "vendor banner",
                  "vendor_id": "3",
                  "program_name": "vendor program",
                  "target_link": "https://google.com",
                  "status": "1",
                  "action_click": "0",
                  "action_amount": "0",
                  "general_click": "0",
                  "general_amount": "0",
                  "admin_action_click": "0",
                  "admin_action_amount": "0",
                  "admin_general_click": "0",
                  "admin_general_amount": "0",
                  "_tool_type": "program",
                  "type": "Banner",
                  "_type": "banner",
                  "commission_type": "percentage",
                  "commission_sale": "10",
                  "commission_number_of_click": "1",
                  "commission_click_commission": "0.25",
                  "click_status": "1",
                  "sale_status": "1",
                  "admin_commission_type": "percentage",
                  "admin_commission_sale": "5",
                  "admin_commission_number_of_click": "1",
                  "admin_commission_click_commission": "0.15",
                  "admin_click_status": "1",
                  "admin_sale_status": "1",
                  "recursion": "",
                  "recursion_custom_time": "0",
                  "username": "user1",
                  "recursion_endtime": null,
                  "featured_image": "gP61NkqtUu4ST82WBjVnYX7HxdfLCliv.jpg",
                  "total_sale_amount": "$0.00",
                  "total_click_amount": "$0.00",
                  "total_action_click_amount": "$0.00",
                  "total_general_click_amount": "$0.00",
                  "total_sale_count": 0,
                  "total_click_count": 0,
                  "total_action_click_count": 0,
                  "total_general_click_count": 0,
                  "tool_type": "Program",
                  "created_at": "22-03-2021 07:01 PM",
                  "product_created_date": "22-03-2021 07:01 PM",
                  "is_tool": 1,
                  "slug": ""
              },
              {
                  "product_id": "2",
                  "product_name": "vendor product",
                  "product_description": "<p><span xss=removed>Product Description </span><span xss=removed>Product Description </span><span xss=removed>Product Description </span><span xss=removed>Product Description </span><span xss=removed>Product Description </span><span xss=removed>Product Description</span></p><p><span xss=removed>Product Description </span><span xss=removed>Product Description </span><span xss=removed>Product Description </span><span xss=removed>Product Description </span><span xss=removed>Product Description </span><span xss=removed>Product Description</span><span xss=removed><br></span><br></p>",
                  "product_short_description": "Short Description\r\nShort Description",
                  "product_tags": "null",
                  "product_msrp": "0",
                  "product_price": "100",
                  "product_sku": "123",
                  "product_slug": "vendor-product-2",
                  "product_share_count": "",
                  "product_click_count": "0",
                  "product_view_count": "0",
                  "product_sales_count": "0",
                  "product_featured_image": "vZ7HA36iFyMkoBCPNp9n1defcsO4QgbX.jpg",
                  "product_banner": "",
                  "product_video": "",
                  "product_type": "virtual",
                  "product_commision_type": "",
                  "product_commision_value": "0",
                  "product_status": "1",
                  "product_ipaddress": "127.0.0.1",
                  "product_created_date": "2021-03-22 16:59:56",
                  "product_updated_date": "0000-00-00 00:00:00",
                  "product_created_by": "1",
                  "product_updated_by": "0",
                  "product_click_commision_type": "",
                  "product_click_commision_ppc": "0",
                  "product_click_commision_per": "0",
                  "product_total_commission": "0",
                  "product_recursion_type": "",
                  "product_recursion": "",
                  "recursion_custom_time": "0",
                  "recursion_endtime": null,
                  "view": "0",
                  "on_store": "1",
                  "downloadable_files": "[]",
                  "allow_shipping": "0",
                  "allow_upload_file": "0",
                  "allow_comment": "0",
                  "state_id": "283",
                  "product_avg_rating": "0",
                  "product_variations": "[]",
                  "seller_firstname": "user1",
                  "seller_lastname": "user1",
                  "seller_username": "user1",
                  "seller_id": "3",
                  "commission": null,
                  "order_count": "0",
                  "commition_click_count": "0",
                  "commition_click_count_admin": "0",
                  "commition_click": null,
                  "slug": "",
                  "is_product": 1
              },
              {
                  "product_id": "1",
                  "product_name": "Pizza",
                  "product_description": "<p>testestestestestestest</p><p>testestestestestestest</p><p>testestestestestestest</p><p>testestestestestestest</p><p>testestestestestestest<br></p>",
                  "product_short_description": "testestestestestestest",
                  "product_tags": "null",
                  "product_msrp": "0",
                  "product_price": "5",
                  "product_sku": "123",
                  "product_slug": "pizza-1",
                  "product_share_count": "",
                  "product_click_count": "0",
                  "product_view_count": "0",
                  "product_sales_count": "0",
                  "product_featured_image": "e9lnTNOzxXtsb4ArZLpBgc0uEGfH5v1m.jpg",
                  "product_banner": "",
                  "product_video": "",
                  "product_type": "downloadable",
                  "product_commision_type": "default",
                  "product_commision_value": "0",
                  "product_status": "1",
                  "product_ipaddress": "85.203.45.17",
                  "product_created_date": "2021-03-16 08:45:00",
                  "product_updated_date": "0000-00-00 00:00:00",
                  "product_created_by": "1",
                  "product_updated_by": "0",
                  "product_click_commision_type": "default",
                  "product_click_commision_ppc": "0",
                  "product_click_commision_per": "0",
                  "product_total_commission": "0",
                  "product_recursion_type": "",
                  "product_recursion": "",
                  "recursion_custom_time": "0",
                  "recursion_endtime": null,
                  "view": "78",
                  "on_store": "1",
                  "downloadable_files": "[{\"mask\":\"logo.zip\",\"name\":\"8efb3c68bef8a87372e04d2225134456\",\"type\":\"zip\"}]",
                  "allow_shipping": "1",
                  "allow_upload_file": "0",
                  "allow_comment": "0",
                  "state_id": "259",
                  "product_avg_rating": "0",
                  "product_variations": "[]",
                  "seller_firstname": null,
                  "seller_lastname": null,
                  "seller_username": null,
                  "seller_id": null,
                  "commission": null,
                  "order_count": "0",
                  "commition_click_count": "0",
                  "commition_click_count_admin": "0",
                  "commition_click": null,
                  "slug": "storeslug",
                  "is_product": 1
              },
              {
                  "id": "9",
                  "redirectLocation": [
                      "http://localhost/test/click.php?af_id=cnBNbjhvZUxoMitQYVREQzhkMktQUT09-My02"
                  ],
                  "program_id": "0",
                  "name": "click test",
                  "vendor_id": "0",
                  "program_name": null,
                  "target_link": "http://localhost/test/click.php",
                  "status": "1",
                  "action_click": "0",
                  "action_amount": "0",
                  "general_click": "1",
                  "general_amount": "1",
                  "admin_action_click": "0",
                  "admin_action_amount": "0",
                  "admin_general_click": "0",
                  "admin_general_amount": "0",
                  "_tool_type": "general_click",
                  "type": "Banner",
                  "_type": "banner",
                  "commission_type": null,
                  "commission_sale": null,
                  "commission_number_of_click": null,
                  "commission_click_commission": null,
                  "click_status": null,
                  "sale_status": null,
                  "admin_commission_type": null,
                  "admin_commission_sale": null,
                  "admin_commission_number_of_click": null,
                  "admin_commission_click_commission": null,
                  "admin_click_status": null,
                  "admin_sale_status": null,
                  "recursion": "",
                  "recursion_custom_time": "0",
                  "username": null,
                  "recursion_endtime": null,
                  "featured_image": "0Goh4ckXjeOK3MCY9m6FgzNBl7trT5Ew.jpg",
                  "total_sale_amount": "$0.00",
                  "total_click_amount": "$0.00",
                  "total_action_click_amount": "$0.00",
                  "total_general_click_amount": "$1.00",
                  "total_sale_count": 0,
                  "total_click_count": 0,
                  "total_action_click_count": 0,
                  "total_general_click_count": 1,
                  "tool_type": "General click",
                  "created_at": "03-03-2021 12:08 PM",
                  "product_created_date": "03-03-2021 12:08 PM",
                  "is_tool": 1,
                  "slug": "clickslugtest"
              },
              {
                  "form_id": "1",
                  "title": "Form1",
                  "description": "<p>test test test test</p><p>test test test test</p><p>test test test test</p><p>test test test test</p><p>test test test test<br></p>",
                  "seo": "form2",
                  "fevi_icon": "assets/images/form/favi/3KikdOIVZyUsMtJucp8h7G1lnNeWBq42.png",
                  "sale_commision_type": "default",
                  "sale_commision_value": "10",
                  "click_commision_type": "default",
                  "click_commision_ppc": "1",
                  "click_commision_per": "1",
                  "comment": null,
                  "form_recursion_type": "",
                  "form_recursion": "",
                  "recursion_custom_time": "0",
                  "recursion_endtime": null,
                  "product": null,
                  "coupon": "",
                  "status": "1",
                  "allow_for": "A",
                  "footer_title": "form footer",
                  "google_analitics": "",
                  "created_at": "2021-02-24 19:02:11",
                  "total_commission": null,
                  "count_commission": "0",
                  "commition_click_count": "0",
                  "commition_click": null,
                  "slug": "",
                  "coupon_name": "",
                  "public_page": "http://192.168.1.153/aff/ver-4-0-0-5/form/form2/",
                  "count_coupon": 0,
                  "is_form": 1,
                  "product_created_date": "2021-02-24 19:02:11"
              }
          ]
};
  
        function gfg_Run() { 
            dashboard.innerHTML = JSON.stringify(dashboard_obj, undefined, 4); 
            login.innerHTML = JSON.stringify(login_obj, undefined, 4); 
            registration.innerHTML = JSON.stringify(registration_obj, undefined, 4); 
            integration_category_list.innerHTML = JSON.stringify(integration_category_list_obj, undefined, 4); 
            store_category_list.innerHTML = JSON.stringify(store_category_list_obj, undefined, 4); 
            my_affiliate_links.innerHTML = JSON.stringify(my_affiliate_links_obj, undefined, 4); 
        } 
        gfg_Run();
    </script>
  </body>
</html>