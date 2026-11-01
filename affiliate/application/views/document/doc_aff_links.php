<div class="row">
   <div class="col-md-12">
      <div class="top-content" id="my_affiliate_links">
         <h2 class="page-title">My Affiliate Links</h2>
         <p>
            My affiliate links with HTTP POST request.
         </p>
         <p>
            API token is required for the authentication of the calling program to the API.
         </p>
         <p>
            Here display all affilaite link data. you can filter using below parametr as per your need without filter paramater pass you can get all affiliate link data .
         </p>
         <div class="row">
            <div class="col-md-6">
               <!-- TABLE HOVER -->
               <div class="panel white">
                  <div class="panel-heading">
                     <h3 class="panel-title">Request :  </h3><br>
                     <span class="text-warning">POST</span> : <?=base_url();?>User/my_affiliate_links</p>
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
                              <td>category_id</td>
                              <td><code>number</code></td>
                              <td><code>Body</code></td>
                              <td>this is optional parameter for filter</td>
                           </tr>
                           <tr>
                              <td>ads_name</td>
                              <td><code>string</code></td>
                              <td><code>Body</code></td>
                              <td>this is optional parameter for filter</td>
                           </tr>
                           <tr>
                              <td>market_category_id</td>
                              <td><code>number</code></td>
                              <td><code>Body</code></td>
                              <td>this is optional parameter for filter</td>
                           </tr>
                           <tr>
                              <td>check_vendor</td>
                              <td><code>number</code></td>
                              <td><code>Body</code></td>
                              <td>this is optional parameter for filter this parametr have fixed value false OR true</td>
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
                     <pre class="response-view">
                        {"status":true,"message":"my affiliate links get successfully","data":[{"aff_tool_type":"product","is_campaign_product":false,"public_page":"http:\/\/localhost.aff.com\/store\/Mw==\/product\/best-video-from-youtube-new-2-175","share_url":"http:\/\/localhost.aff.com\/best-youtube-videos","fevi_icon":"http:\/\/localhost.aff.com\/assets\/image_cache\/cache\/assets\/images\/product\/upload\/thumb\/c1oqMnid7C0QjPhGSagN4bXOUA9Tev8f-100x100.jpg","title":"Best Video From YouTube  - new 2","sale_commision_you_will_get":"35% Per Sale","click_commision_you_will_get":"$2.00 Per 1 Click","description":"sdfsdf","price":"$785.00","product_sku":"123","sales_commission":"0\/$0.00","clicks_commission":"0\/$0.00","total_commission":"$0.00","displayed_on_store":true},{"aff_tool_type":"product","is_campaign_product":false,"public_page":"http:\/\/localhost.aff.com\/store\/Mw==\/product\/flutter-online-course-links-video-new-1-174","share_url":"http:\/\/localhost.aff.com\/store\/Mw==\/product\/flutter-online-course-links-video-new-1-174","fevi_icon":"http:\/\/localhost.aff.com\/assets\/image_cache\/cache\/assets\/images\/product\/upload\/thumb\/YgfnObI0vWdkwRuDHGcq6t9MyQoZA5jm-100x100.jpeg","title":"Flutter online course - links video - new 1","sale_commision_you_will_get":"35% Per Sale","click_commision_you_will_get":"$2.00 Per 1 Click","description":"Flutter online course - links video\r\nFlutter online course - links video","price":"$79.00","product_sku":"123","sales_commission":"0\/$0.00","clicks_commission":"0\/$0.00","total_commission":"$0.00","displayed_on_store":true},{"public_page":"http:\/\/localhost.aff.com\/ref\/YThIZFUwVnM4VTM1OVBYaXpZN0MvUT09-My03Nw==","fevi_icon":"http:\/\/localhost.aff.com\/assets\/image_cache\/cache\/assets\/images\/product\/upload\/thumb\/woo-100x100.png","title":"test","share_url":"http:\/\/localhost.aff.com\/ref\/YThIZFUwVnM4VTM1OVBYaXpZN0MvUT09-My03Nw==","sale_commision_you_will_get":"5% Per Sale","click_commision_you_will_get":"$1.00 Per 1 Clicks","sale_count":0,"sale_amount":"$0.00","click_count":0,"click_amount":"$0.00"},{"public_page":"http:\/\/localhost.aff.com\/ref\/TUMxK3JNeUc2RnBEc29SQzE2M3c2QT09-My0zOA==","fevi_icon":"http:\/\/localhost.aff.com\/assets\/image_cache\/cache\/assets\/images\/product\/upload\/thumb\/click-100x100.jpg","title":"Admin click","share_url":"http:\/\/localhost.aff.com\/ref\/TUMxK3JNeUc2RnBEc29SQzE2M3c2QT09-My0zOA==","click_commision_you_will_get":"$1.00 Per 1 Clicks","click_ratio":"0%","general_count":0,"general_amount":"$0.00"},{"public_page":"http:\/\/localhost.aff.com\/ref\/NjhQS1hwNURvRllxZGpVcVlDK09sZz09-My00Ng==","fevi_icon":"http:\/\/localhost.aff.com\/assets\/image_cache\/cache\/assets\/images\/product\/upload\/thumb\/order-100x100.jpg","title":"Admin Order","share_url":"http:\/\/localhost.aff.com\/ref\/NjhQS1hwNURvRllxZGpVcVlDK09sZz09-My00Ng==","sale_commision_you_will_get":"50% Per Sale","click_commision_you_will_get":"$1.00 Per 1 Clicks","click_ratio":"0%","sale_ratio":"0%","sale_count":0,"sale_amount":"$0.00","click_count":0,"click_amount":"$0.00"},{"public_page":"http:\/\/localhost.aff.com\/ref\/b0xiQ1psMzluazhSekJHTzUwN3BEdz09-My00NA==","fevi_icon":"http:\/\/localhost.aff.com\/assets\/image_cache\/cache\/assets\/images\/product\/upload\/thumb\/action-100x100.jpg","title":"Admin Action","share_url":"http:\/\/localhost.aff.com\/ref\/b0xiQ1psMzluazhSekJHTzUwN3BEdz09-My00NA==","click_commision_you_will_get":"$5.00 Per 1 Actions","click_ratio":"0%","action_count":0,"action_amount":"$0.00"},{"aff_tool_type":"product","is_campaign_product":false,"public_page":"http:\/\/localhost.aff.com\/store\/Mw==\/product\/flutter-online-course-links-video-168","share_url":"http:\/\/localhost.aff.com\/store\/Mw==\/product\/flutter-online-course-links-video-168","fevi_icon":"http:\/\/localhost.aff.com\/assets\/image_cache\/cache\/assets\/images\/product\/upload\/thumb\/YgfnObI0vWdkwRuDHGcq6t9MyQoZA5jm-100x100.jpeg","title":"Flutter online course - links video","sale_commision_you_will_get":"35% Per Sale","click_commision_you_will_get":"$2.00 Per 1 Click","description":"Flutter online course - links video\r\nFlutter online course - links video","price":"$79.00","product_sku":"123","sales_commission":"0\/$0.00","clicks_commission":"0\/$0.00","total_commission":"$0.00","displayed_on_store":true},{"aff_tool_type":"product","is_campaign_product":false,"public_page":"http:\/\/localhost.aff.com\/store\/Mw==\/product\/flutter-online-course-hosted-files-167","share_url":"http:\/\/localhost.aff.com\/store\/Mw==\/product\/flutter-online-course-hosted-files-167","fevi_icon":"http:\/\/localhost.aff.com\/assets\/image_cache\/cache\/assets\/images\/product\/upload\/thumb\/Qnu8YPTVUA0ZW6mzbqXvaHc7CxB5dkh9-100x100.jpeg","title":"Flutter online course - hosted files","sale_commision_you_will_get":"35% Per Sale","click_commision_you_will_get":"$2.00 Per 1 Click","description":"Flutter online course","price":"$150.00","product_sku":"123","sales_commission":"0\/$0.00","clicks_commission":"0\/$0.00","total_commission":"$0.00","displayed_on_store":true},{"aff_tool_type":"product","is_campaign_product":false,"public_page":"http:\/\/localhost.aff.com\/store\/Mw==\/product\/lms-product-php-cource-mp4-149","share_url":"http:\/\/localhost.aff.com\/store\/Mw==\/product\/lms-product-php-cource-mp4-149","fevi_icon":"http:\/\/localhost.aff.com\/assets\/image_cache\/cache\/assets\/images\/product\/upload\/thumb\/g9tPcXR2yIuw87zDeSYFs01Lqh4GBfbn-100x100.png","title":"LMS Product - PHP Cource - MP4","sale_commision_you_will_get":"35% Per Sale","click_commision_you_will_get":"$2.00 Per 1 Click","recurring":"Every week","description":"gh gh fgh","price":"$10.00","product_sku":"test","sales_commission":"0\/$0.00","clicks_commission":"0\/$0.00","total_commission":"$0.00","displayed_on_store":true},{"aff_tool_type":"product","is_campaign_product":false,"public_page":"http:\/\/localhost.aff.com\/store\/Mw==\/product\/lms-product-php-cource-live-links-148","share_url":"http:\/\/localhost.aff.com\/store\/Mw==\/product\/lms-product-php-cource-live-links-148","fevi_icon":"http:\/\/localhost.aff.com\/assets\/image_cache\/cache\/assets\/images\/product\/upload\/thumb\/g9tPcXR2yIuw87zDeSYFs01Lqh4GBfbn-100x100.png","title":"LMS Product - PHP Cource - Live Links","sale_commision_you_will_get":"35% Per Sale","click_commision_you_will_get":"$2.00 Per 1 Click","recurring":"Every week","description":"This is product short description This is product short description This is product short description This is product short description","price":"$10.00","product_sku":"test","sales_commission":"0\/$0.00","clicks_commission":"0\/$0.00","total_commission":"$0.00","displayed_on_store":true},{"aff_tool_type":"product","is_campaign_product":false,"public_page":"http:\/\/localhost.aff.com\/store\/Mw==\/product\/best-video-from-youtube-147","share_url":"http:\/\/localhost.aff.com\/store\/Mw==\/product\/best-video-from-youtube-147","fevi_icon":"http:\/\/localhost.aff.com\/assets\/image_cache\/cache\/assets\/images\/product\/upload\/thumb\/c1oqMnid7C0QjPhGSagN4bXOUA9Tev8f-100x100.jpg","title":"Best Video From YouTube ","sale_commision_you_will_get":"35% Per Sale","click_commision_you_will_get":"$2.00 Per 1 Click","description":"sdfsdf","price":"$785.00","product_sku":"123","sales_commission":"0\/$0.00","clicks_commission":"0\/$0.00","total_commission":"$0.00","displayed_on_store":true},{"aff_tool_type":"product","is_campaign_product":false,"public_page":"http:\/\/localhost.aff.com\/store\/Mw==\/product\/downloadable-product-php-course-146","share_url":"http:\/\/localhost.aff.com\/store\/Mw==\/product\/downloadable-product-php-course-146","fevi_icon":"http:\/\/localhost.aff.com\/assets\/image_cache\/cache\/assets\/images\/product\/upload\/thumb\/ELnkqy9HXhJtRelOdZm8cjYu3SovAwg2-100x100.png","title":"Downloadable product - PHP Course","sale_commision_you_will_get":"35% Per Sale","click_commision_you_will_get":"$2.00 Per 1 Click","description":"Downloadable product\r\nDownloadable product\r\nDownloadable product","price":"$75.00","product_sku":"123","sales_commission":"0\/$0.00","clicks_commission":"0\/$0.00","total_commission":"$0.00","displayed_on_store":true},{"public_page":"http:\/\/localhost.aff.com\/ref\/UU04TTR5azA2amVpV3phOXY4SDBnUT09-My01NA==","fevi_icon":"http:\/\/localhost.aff.com\/assets\/image_cache\/cache\/assets\/images\/product\/upload\/thumb\/woo-100x100.png","title":"WooCommerce Integration Admin Test","share_url":"http:\/\/localhost.aff.com\/ref\/UU04TTR5azA2amVpV3phOXY4SDBnUT09-My01NA==","sale_commision_you_will_get":"50% Per Sale","click_commision_you_will_get":"$1.00 Per 1 Clicks","sale_count":0,"sale_amount":"$0.00","click_count":0,"click_amount":"$0.00"},{"public_page":"http:\/\/localhost.aff.com\/ref\/MlFQZ3liY2pGM3Nsd0dqcDlCQi9Rdz09-My01MQ==","fevi_icon":"http:\/\/localhost.aff.com\/assets\/image_cache\/cache\/assets\/images\/product\/upload\/thumb\/click-100x100.jpg","title":"Admin click 2","share_url":"http:\/\/localhost.aff.com\/ref\/MlFQZ3liY2pGM3Nsd0dqcDlCQi9Rdz09-My01MQ==","click_commision_you_will_get":"$1.00 Per 1 Clicks","click_ratio":"0%","general_count":0,"general_amount":"$0.00"},{"aff_tool_type":"form","fevi_icon":"aVGrf1yFcnHZzE7OCiJuSjwpM23oqs9B.jpg","title":"test 1","share_url":"http:\/\/localhost.aff.com\/form\/test_1\/Mw==","public_page":"http:\/\/localhost.aff.com\/form\/test_1\/Mw==","sale_commision_you_will_get":"15% Per Sale","click_commision_you_will_get":"$3.00 of Per 1 Click","recurring":"Every week","description":"<p>test<\/p>","coupon_code":"N\/A","coupon_use":"- \/ 0","sales_commission":"0 \/ $0.00","clicks_commission":"0 \/ $0.00","total_commission":"$0.00"},{"public_page":"http:\/\/localhost.aff.com\/ref\/L2h3NkhaYzR4S3YxLzR2bkFNbG9DZz09-My01Mw==","fevi_icon":"http:\/\/localhost.aff.com\/assets\/image_cache\/cache\/assets\/images\/product\/upload\/thumb\/action-100x100.jpg","title":"Admin Action 2","share_url":"http:\/\/localhost.aff.com\/ref\/L2h3NkhaYzR4S3YxLzR2bkFNbG9DZz09-My01Mw==","click_commision_you_will_get":"$5.00 Per 1 Actions","click_ratio":"0%","action_count":0,"action_amount":"$0.00"},{"aff_tool_type":"form","fevi_icon":"i7T3DfGSuJQqHIgpLkrvWsnYAcjV2mhU.png","title":"test 2","share_url":"http:\/\/localhost.aff.com\/form\/test_2\/Mw==","public_page":"http:\/\/localhost.aff.com\/form\/test_2\/Mw==","sale_commision_you_will_get":"15% Per Sale","click_commision_you_will_get":"$3.00 of Per 1 Click","description":"<p>test 2<\/p><p>test 2<\/p><p>test 2<br><\/p>","coupon_code":"N\/A","coupon_use":"- \/ 0","sales_commission":"0 \/ $0.00","clicks_commission":"0 \/ $0.00","total_commission":"$0.00"},{"aff_tool_type":"product","is_campaign_product":false,"public_page":"http:\/\/localhost.aff.com\/store\/Mw==\/product\/default-store-theme-product-2-137","share_url":"http:\/\/localhost.aff.com\/store\/Mw==\/product\/default-store-theme-product-2-137","fevi_icon":"http:\/\/localhost.aff.com\/assets\/image_cache\/cache\/assets\/images\/product\/upload\/thumb\/sWAclrK6X5bpDMTm7weNJqgVQSfYoxIk-100x100.jpg","title":"Default Store Theme Product - 2","sale_commision_you_will_get":"35% Per Sale","click_commision_you_will_get":"$2.00 Per 1 Click","description":"Soft, cosy and light weight reversible comforter with 200 GSM hollow siliconized polyester filling. ","price":"$25.00","product_sku":"12345","sales_commission":"0\/$0.00","clicks_commission":"0\/$0.00","total_commission":"$0.00","displayed_on_store":true},{"aff_tool_type":"product","is_campaign_product":false,"public_page":"http:\/\/localhost.aff.com\/store\/Mw==\/product\/default-store-theme-product-1-1","share_url":"http:\/\/localhost.aff.com\/store\/Mw==\/product\/default-store-theme-product-1-1","fevi_icon":"http:\/\/localhost.aff.com\/assets\/image_cache\/cache\/assets\/images\/product\/upload\/thumb\/BsLof5wCi0qD1XuIK2AdxTSma94hNJRW-100x100.jpg","title":"Default Store Theme Product -1","sale_commision_you_will_get":"35% Per Sale","click_commision_you_will_get":"$2.00 Per 1 Click","description":"Soft, cosy and light weight reversible comforter with 200 GSM hollow siliconized polyester filling. ","price":"$10.00","product_sku":"123","sales_commission":"0\/$0.00","clicks_commission":"0\/$0.00","total_commission":"$0.00","displayed_on_store":true}]}
                     </pre>
                  </div>
               </div>
               <!-- END TABLE HOVER -->
            </div>
         </div>
         <!-- end my affiliate -->
      </div>
   </div>
</div>
<!-- row -->