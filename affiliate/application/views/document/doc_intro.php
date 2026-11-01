<div class="top-content" id="intro">
   <h2 class="page-title">Introduction</h2>
   <p>Affiliate-Pro SaaS script gives you the ability to track sales, clicks, and so many actions that you need to track on your site/store and share the commission with your affiliates. </p>
   <h4>About</h4>
   Affiliates can share your products&services and when sales record from affiliate link then a commission auto record in affiliate wallet. same for services, leads, clicks, actions.</br>
   Affiliate-Pro SaaS script gives you the ability to allow your affiliates to be your vendors and integrate their own site&store and share it with all system affiliates and increase the sales on their own site. From this option admin also can earn a fee for every vendor activity.</br>
   The script comes with full E-Commerce where you can upload your items for sale and share with affiliates to increase your sales by sharing a commission for sale and product clicks.</br>
   So many features like PostBack, Recurring, Language manager, currency manager, and more are in our advanced script.
   Affiliate script comes with a membership module that allows the admin to charge his users by using the system and so many other features that can be read here on our item page. </br>
   What is important to know that we are adding so many new features every month and for free, so all clients can use our script and it will cover almost 100% of business out there for help and growth!</br>
   <h3>Main Advance Features:</h3>
   <ul>
      <li>
         <strong>Membership</strong> module to charge your affiliates
      </li>
      <li>
         <strong>E-Commerce</strong> module to sell your products
      </li>
      <li>
         <strong>Recurring</strong> commission module Integration
      </li>
      <li>
         <strong>Postback</strong> module to track values from any site
      </li>
      <li>
         <strong>SaaS</strong> module to set your affiliates to your vendors
      </li>
      <li>
         <strong>MLM</strong> module to build your Multi-level marketing
      </li>
      <li>
         <strong>Wallet Manager</strong> module to manage all transactions
      </li>
      <li>
         <strong>Live payouts</strong> module to pay your affiliates
      </li>
      <li>
         <strong>Registration API</strong> module to register from your site
      </li>
      <li>
         <strong>Newsletter</strong> module to notify your users
      </li>
      <li><strong>and many more…</strong></li>
   </ul>
   <h3>SaaS Feature To Create Your Affiliates&Vendors Private Network</h3>
   <ul>
      <li>Admin can activate the SaaS module for a specific&nbsp;user from his panel and&nbsp;allow user to be an affiliate and also a vendor by using the SaaS module</li>
      <li>Admin can set his own commission for every sale/click/action, that will be trigged from his vendor users</li>
      <li>Affiliate user can integrate his own site/store</li>
      <li>Affiliate can add his own commission program</li>
      <li>Affiliate can add his own banners(image/links/text/video)</li>
      <li>Affiliate can share his program commission with all site affiliates</li>
      <li>Affiliate can add his own physical products to the built-in store </li>
      <li>Affiliate can add his own downloadable products to the built-in store </li>
      <li>Affiliate can add his own coupons for adding a discount to his users</li>
      <li>Affiliate can see his own orders &amp; logs on his panel</li>
   </ul>
</div>

<div class="top-content" id="generate-authentication">
   <h2 class="page-title">Generate Authentication</h2>
   <p>Authentication token are required as an authentication method with Affiliate apis. By using an Authentication token you authenticate access to the specific API. Without authentication access to the API is denied.</br>
      You can generate and manage Authentication token from within the Affiliate login api. By login api will generate a unique  your user token for API authentication.
   </p>
</div>

<div class="top-content" id="use-authentication">
   <h2 class="page-title">Use Authentication</h2>
   <p>
      API token is required for the authentication of the calling program to the API.</br>
      The API token is sensitive private information that we strongly advise to have and keep restricted access to it.</br>
      Content-Type: application/json Authorization: {{token}}</br>
   </p>
</div>

<div class="top-content" id="errors-api-status">          
   <h2 class="page-title">Errors and API status</h2>
   <h4>List of errors</h4>
   <p>All requests with a response status code different than 200 (successful HTTP) are considered as a failure of the particular API call and referred to as “HTTPS errors”. When the response (error) is returned an additional JSON is present in the body containing the error message. Depending on what has gone wrong with the API call, the error message is different.</p>
   <p>As best practice we recommend to store all error messages somewhere along with request data for further manual review.</p>
   <p>The errors we use follow the HTTP Error Codes Standard. </p>

   <div class="row">
      <div class="col-md-3">
         <table class="table table-border">
            <thead>
               <tr>
                  <th>HTTP Status Code</th>
                  <th>Error it represents</th>
               </tr>
            </thead>
            <tbody>
               <tr>
                  <td>3xx</td>
                  <td>Redirection Error</td>
               </tr>
               <tr>
                  <td>4xx</td>
                  <td>Client Error</td>
               </tr>
               <tr>
                  <td>5xx</td>
                  <td>Server Error</td>
               </tr>
            </tbody>
         </table>
      </div>
   </div>
   
   <p>The structure of the error <strong>always</strong> returns the following values, as listed and described in the example:</p>
   <pre class="response-view">{"status":"true OR false (boolean)", "message": "Is the human readable error message"}</pre>

   <p>The structure of the validation parameter error <strong>always</strong> returns the following values, as listed and described in the example:</p>
   <pre class="response-view">{ "message": "Please required field", "errors": { "parameter": "Oops ! parameter is required." } }</pre>

   <p>We use the following error codes:</p>
   <h3>General errors</h3>

   <table class="table table-border">
      <thead>
         <tr>
            <th class="text-center">Type of code</th>
            <th class="text-center">HTTP Status Code</th>
            <th class="text-center">Error code</th>
            <th class="text-center">Message</th>
         </tr>
      </thead>
      <tbody>
         <tr>
            <td class="text-center">Client Error</td>
            <td class="text-center">400</td>
            <td class="text-center"><code>uri_not_found</code></td>
            <td class="text-center">"The specified URI has not been found. Check the URI and try again."</td>
         </tr>
         <tr>
            <td class="text-center"></td>
            <td class="text-center">401</td>
            <td class="text-center"><code>missing_api_token</code></td>
            <td class="text-center">"The specific authorization header (API token) is missing or invalid, please check our Authorization section in our Documentation."</td>
         </tr>
         <tr>
            <td class="text-center"></td>
            <td class="text-center">404</td>
            <td class="text-center"><code>resource_not_found</code></td>
            <td class="text-center">"The specified resource has not been found."</td>
         </tr>
         <tr>
            <td class="text-center"></td>
            <td class="text-center">405</td>
            <td class="text-center"><code>request_method_not_supported</code></td>
            <td class="text-center">"The specified request method ({method}) is not supported for this endpoint. Please check our Documentation and make sure you set the right request method."</td>
         </tr>
         <tr>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center"><code>allowed_methods</code></td>
            <td class="text-center">"The specified method <strong>must</strong> be one of the following: {methods}."</td>
         </tr>
         <tr>
            <td class="text-center"></td>
            <td class="text-center">415</td>
            <td class="text-center"><code>unsupported_media_type</code></td>
            <td class="text-center">"The selected Media Type is unavailable. The Content-Type header should be 'application/json'."</td>
         </tr>
         <tr>
            <td class="text-center"></td>
            <td class="text-center">422</td>
            <td class="text-center"><code>unprocessable_enitity</code></td>
            <td class="text-center">"stricly follow on required parameter : missing parameter or attribute error"</td>
         </tr>
         <tr>
            <td class="text-center">Server Error</td>
            <td class="text-center">500</td>
            <td class="text-center"><code>technical_issues</code></td>
            <td class="text-center">"We are currently facing some technical issues, please try again later."</td>
         </tr>
         <tr>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center"><code>unexpected_server_error</code></td>
            <td class="text-center">"An unexpected server error has occurred, we are working to fix this. Please try again later and in case it occurs again please report it to our team via email."</td>
         </tr>
         <tr>
            <td class="text-center"></td>
            <td class="text-center">501</td>
            <td class="text-center"><code>unimplemented</code></td>
            <td class="text-center">"This {feature} has not been implemented yet."</td>
         </tr>
         <tr>
            <td class="text-center"></td>
            <td class="text-center">503</td>
            <td class="text-center"><code>temporary_shutdown_endpoint</code></td>
            <td class="text-center">"This endpoint is temporarily stopped due to performance reasons. For more information please contact our team via email."</td>
         </tr>
      </tbody>
   </table>
</div>

<div class="top-content" id="request-and-response-stands">
   <h2 class="page-title">Request and Response Standards</h2>
   <ul>
      <li>requests are sent through HTTPS OR HTTP to the domain <strong>yourdomain.com</strong>;</li>
      <li>headers must by default incorporate the JSON content type <strong>application/json</strong>;</li>
      <li>request public attributes must be all snake_case, e.g. <code>"api_version": 1</code>, <code>"attribute_name": "attribute_value"</code>;</li>
      <li>we enable CORS (Cross-Origin Resource Sharing), for which the API responds with an <code>Access-Control-Allow-Origin:</code> header. <strong>Nevertheless</strong>, your users <strong>shouldn’t make</strong> direct API requests from a web application that you are building, as our CORS policy may change at some point without warning and any such requests could be then rejected;</li>
      <li>API token must be kept secure and private by the users who own them. </li>
   </ul>
   
   <p>Shortly put, all Affiliate requests include:</p>

   <div class="code-toolbar">
      <pre class=" language-json"><code class=" language-json">REST API Base URL<span class="token operator">:</span> https<span class="token operator">:</span><span class="token comment">//yourdomain.com</span><br/>Authentication (API token + JSON)<br/>Request Type (GET<span class="token punctuation">,</span> POST<span class="token punctuation">,</span> DELETE)</code></pre>
      <div class="toolbar">
         <div class="toolbar-item"></div>
      </div>
      <p>Our API Requests incorporate the following HTTP methods:</p>
      <table class="table table-border">
         <thead>
            <tr>
               <th class="text-center">HTTP method</th>
               <th class="text-center">Definition</th>
            </tr>
         </thead>
         <tbody>
            <tr>
               <td class="text-center">GET</td>
               <td class="text-center">Retrieve a specified resource/information from the server.</td>
            </tr>
            <tr>
               <td class="text-center">POST</td>
               <td class="text-center">Send data to the server and requests to accept it.</td>
            </tr>
            <tr>
               <td class="text-center">DELETE</td>
               <td class="text-center">Deletes a resource.</td>
            </tr>
         </tbody>
      </table>
   </div>
</div>

<div class="row">
   <div class="col-md-12">
      <h2 class="page-title">Note : </h2>
      <p>
         Our all apis list available on postman collection. using below link anyone can import our postman collection. <br>
         <strong>Postman Collection Link :</strong> https://www.getpostman.com/collections/503663c78d24cf47b308 
      </p>
   </div>
</div>