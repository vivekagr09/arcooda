<?php
// Stripe singleton
require(APPPATH . 'payment_gateway/library/stripe/stripe/Stripe.php');

// Utilities
require(APPPATH . 'payment_gateway/library/stripe/stripe/Util/AutoPagingIterator.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Util/CaseInsensitiveArray.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Util/LoggerInterface.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Util/DefaultLogger.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Util/RandomGenerator.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Util/RequestOptions.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Util/Set.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Util/Util.php');

// HttpClient
require(APPPATH . 'payment_gateway/library/stripe/stripe/HttpClient/ClientInterface.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/HttpClient/CurlClient.php');

// Errors
require(APPPATH . 'payment_gateway/library/stripe/stripe/Error/Base.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Error/Api.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Error/ApiConnection.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Error/Authentication.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Error/Card.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Error/Idempotency.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Error/InvalidRequest.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Error/Permission.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Error/RateLimit.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Error/SignatureVerification.php');

// OAuth errors
require(APPPATH . 'payment_gateway/library/stripe/stripe/Error/OAuth/OAuthBase.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Error/OAuth/InvalidClient.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Error/OAuth/InvalidGrant.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Error/OAuth/InvalidRequest.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Error/OAuth/InvalidScope.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Error/OAuth/UnsupportedGrantType.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Error/OAuth/UnsupportedResponseType.php');

// API operations
require(APPPATH . 'payment_gateway/library/stripe/stripe/ApiOperations/All.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/ApiOperations/Create.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/ApiOperations/Delete.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/ApiOperations/NestedResource.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/ApiOperations/Request.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/ApiOperations/Retrieve.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/ApiOperations/Update.php');

// Plumbing
require(APPPATH . 'payment_gateway/library/stripe/stripe/ApiResponse.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/StripeObject.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/ApiRequestor.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/ApiResource.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/SingletonApiResource.php');

// Stripe API Resources
require(APPPATH . 'payment_gateway/library/stripe/stripe/Account.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/AlipayAccount.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/ApplePayDomain.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/ApplicationFee.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/ApplicationFeeRefund.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Balance.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/BalanceTransaction.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/BankAccount.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/BitcoinReceiver.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/BitcoinTransaction.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Card.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Charge.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Collection.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/CountrySpec.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Coupon.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Customer.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Discount.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Dispute.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/EphemeralKey.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Event.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/ExchangeRate.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/File.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/FileLink.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/FileUpload.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Invoice.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/InvoiceItem.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/InvoiceLineItem.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/IssuerFraudRecord.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Issuing/Authorization.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Issuing/Card.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Issuing/CardDetails.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Issuing/Cardholder.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Issuing/Dispute.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Issuing/Transaction.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/LoginLink.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Order.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/OrderItem.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/OrderReturn.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/PaymentIntent.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Payout.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Plan.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Product.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Recipient.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/RecipientTransfer.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Refund.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Reporting/ReportRun.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Reporting/ReportType.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/SKU.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Sigma/ScheduledQueryRun.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Source.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/SourceTransaction.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Subscription.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/SubscriptionItem.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Terminal/ConnectionToken.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Terminal/Location.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Terminal/Reader.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/ThreeDSecure.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Token.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Topup.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/Transfer.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/TransferReversal.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/UsageRecord.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/UsageRecordSummary.php');

// OAuth
require(APPPATH . 'payment_gateway/library/stripe/stripe/OAuth.php');

// Webhooks
require(APPPATH . 'payment_gateway/library/stripe/stripe/Webhook.php');
require(APPPATH . 'payment_gateway/library/stripe/stripe/WebhookSignature.php');
