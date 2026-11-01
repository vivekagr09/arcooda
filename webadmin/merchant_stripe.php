<?php

die("Work in progress");

//credit class for accessing the stripe merchant

//Requires the following functions:
/*
function credit_connect($server='127.0.0.1',$port=9050)
function credit_query($fp,$request)
function credit_close($fp)
 */

//Supplies the following functions:
/*
merchant_stripe::connect()
merchant_stripe::query_first()
merchant_stripe::start_request($reference,$amount,$order_info='',$ticket_number='')
merchant_stripe::fetch_receipt($digital_receipt)
merchant_stripe::fetch_payment($reference,$amount,$order_info,$ticket_number,$card_number,$expiration,$security_code='')
merchant_stripe::close()
 */
class MerchantStripe
{
    public $fp         = false;
    public $server     = '127.0.0.1';
    public $port       = 9050;
    public $user       = 'HIGROUCOM01';
    public $return_url = 'https://www.arcooda.com/payment/return-credit.php?DR=$DR';
    public $card_type  = '';
    public $query_log  = [];
    public $destruct   = false;

    public $private_key = 'sk_test_16EI0qL0OKM3UT2Rwc5Oovxq00Tq70GT37';
    public $public_key  = 'pk_test_ALNJKWFLmupnJtNQUnjAeBCu00AkRgPz2q';

    public function __construct()
    {
        Stripe::setApiKey($this->private_key);
        Stripe::setAppInfo("VirtualDialectPaymentClientPlugin", "19.08.06", "https://www.arcooda.com/");
    }

    public function __destruct()
    {
        if (count($this->query_log) > 0) {
            $headers   = [];
            $headers[] = 'A new merchant_stripe constructed at ' . date(DATE_RSS, time());
            $headers[] = '';
            $headers[] = 'Server Properties:';
            foreach ($_SERVER as $key => $value) {
                $headers[] = ' ' . $key . " => '" . addslashes($value) . "'";
            }

            $headers[] = '';
            $headers[] = 'Communication Log:';
            $message   = implode("\n", $headers) . "\n" . implode("\n", $this->query_log);
            mail('backup@highwaygroup.net', 'Stripe Processing Log', $message);
        }
        $this->query_log = [];
    }
    public function connect()
    {
        return true;
    }
    public function queryFirst()
    {
        $query_array = func_get_args();
        foreach ($query_array as $key => $value) {
            $query_array[$key] = preg_replace('/[, ]+/', ' ', trim($value));
        }

        $query_string = implode(',', $query_array);
        if (7 == $query_array[0] && in_array(strtolower($query_array[1]), ['cardnum', 'cardnumber', 'cardexp', 'cardexpiry', 'cardsecuritycode'])) {
            $query_array[2]    = str_repeat('*', strlen($query_array[2]));
            $this->query_log[] = 'OUT ' . implode(',', $query_array);
        } else {
            $this->query_log[] = 'OUT ' . $query_string;
        }

        $result            = credit_query_first($this->fp, $query_string);
        $this->query_log[] = 'IN ' . $result;
        return $result;
    }
    //Note: Amount must be in Australian dollars
    public function startRequest($reference, $amount, $order_info = '', $ticket_number = '')
    {
        if ('' == $order_info) {
            $order_info = $reference;
        }

        if ($this->addDigitalOrderField('MerchantTxnRef', $reference) != '1,1') {
            return false;
        }

        if ('' != $order_info) {
            if ($this->addDigitalOrderField('OrderInfo', $order_info) != '1,1') {
                return false;
            }
        }

        if ('' != $ticket_number) {
            if ($this->addDigitalOrderField('TicketNo', $ticket_number) != '1,1') {
                return false;
            }
        }

        if ('' != $this->card_type) {
            if ($this->addDigitalOrderField('gateway', 'ssl') != '1,1') {
                return false;
            }

            if ($this->addDigitalOrderField('card', $this->card_type) != '1,1') {
                return false;
            }

        }
        $amount_cents = /*intval(*/
        $amount * 100/*)*/; //taken out intval() - doing weird things like round .95 to .94 :S
        $response = $this->getDigitalOrder($order_info, $this->user, $amount_cents, 'en', $this->return_url);
        $response = explode(',', $response);
        $result   = array_shift($response);
        if (1 != $result) {
            return false;
        }

        return implode(',', $response);
    }
    public function fetchReceipt($digital_receipt)
    {
        if ('' == $digital_receipt) {
            return false;
        }

        $response = $this->decryptDigitalReceipt($digital_receipt);
        if ('0,0' == $response) {
            return false;
        }

        $response = $this->nextResult();
        if ('0,0' == $response) {
            return false;
        }

        $result = [];
        $fields = explode(',', $this->getResultField('DigitalReceipt.Result'));
        if (array_shift($fields) != 1) {
            $fields = ['MerchantTxnRef', 'OrderInfo', 'Merchant', 'Amount', 'ReceiptNo', 'TxnResponseCode', 'AcqResponseCode', 'TransactionNo', 'AuthorizeId', 'BatchNo', 'Card', 'VerType', 'VerStatus', 'VerSecurityLevel', 'VerToken', '3DSXID', '3DSECI', '3DSenrolled', '3DSstatus', 'Locale', 'TicketNo', 'MerchTxnRef'];
        }

        foreach ($fields as $field_name) {
            $response = explode(',', $this->getResultField('DigitalReceipt.' . $field_name));
            if (array_shift($response) == 1) {
                $result[$field_name] = implode(',', $response);
            }

        }
        if ('' != $result['MerchantTxnRef'] && '' == $result['MerchTxnRef']) {
            $result['MerchTxnRef'] = $result['MerchantTxnRef'];
        }

        if ('' != $result['MerchTxnRef']) {
            $result['MerchantTxnRef'] = $result['MerchTxnRef'];
        }

        return $result;
    }
    public function fetchPayment($reference, $amount, $order_info, $ticket_number, $card_number, $expiration, $security_code = '')
    {
        if ($this->addDigitalOrderField('MerchTxnRef', $reference) != '1,1') {
            return false;
        }

        if ($this->addDigitalOrderField('CardNum', $card_number) != '1,1') {
            return false;
        }

        if ($this->addDigitalOrderField('CardExp', $expiration) != '1,1') {
            return false;
        }

        if ('' != $security_code) {
            if ($this->addDigitalOrderField('CardSecurityCode', $security_code) != '1,1') {
                return false;
            }
        }

        if ('' != $order_info) {
            if ($this->addDigitalOrderField('OrderInfo', $order_info) != '1,1') {
                return false;
            }
        }

        if ('' != $ticket_number) {
            if ($this->addDigitalOrderField('TicketNo', $ticket_number) != '1,1') {
                return false;
            }
        }

        if ('' != $this->card_type) {
            if ($this->addDigitalOrderField('gateway', 'ssl') != '1,1') {
                return false;
            }

            if ($this->addDigitalOrderField('card', $this->card_type) != '1,1') {
                return false;
            }

        }
        $amount_cents = intval(bcmul($amount, '100', 0));
        $response     = $this->sendMOTODigitalOrder($reference, $this->user, $amount_cents, 'en', '');
        $temp         = explode(',', $response);
        if (reset($temp) != 1) {
            $this->getResultField('PaymentClient.Error');
            return false;
        }
        $response = $this->nextResult();
        if ('0,0' == $response) {
            return false;
        }

        $result = [];
        $fields = explode(',', $this->getResultField('DigitalReceipt.Result'));
        if (array_shift($fields) != 1) {
            $fields = ['MerchantTxnRef', 'OrderInfo', 'Merchant', 'Amount', 'ReceiptNo', 'TxnResponseCode', 'AcqResponseCode', 'TransactionNo', 'AuthorizeId', 'BatchNo', 'Card', 'VerType', 'VerStatus', 'VerSecurityLevel', 'VerToken', '3DSXID', '3DSECI', '3DSenrolled', '3DSstatus', 'Locale', 'TicketNo', 'MerchTxnRef'];
        }

        foreach ($fields as $field_name) {
            $response = explode(',', $this->getResultField('DigitalReceipt.' . $field_name));
            if (array_shift($response) == 1) {
                $result[$field_name] = implode(',', $response);
            }

        }
        if ('' != $result['MerchantTxnRef'] && '' == $result['MerchTxnRef']) {
            $result['MerchTxnRef'] = $result['MerchantTxnRef'];
        }

        if ('' != $result['MerchTxnRef']) {
            $result['MerchantTxnRef'] = $result['MerchTxnRef'];
        }

        return $result;
    }
    public function close()
    {
        if ($this->fp) {
            credit_close($this->fp);
        }

        $this->fp = false;
        return true;
    }
    //---------------------------------------------------------------------------
    //Standard CommWeb Functions
    //---------------------------------------------------------------------------
    public function _escape($value, $type = '')
    {
        $before  = $value;
        $type    = strtolower($type);
        $value   = preg_replace('/[,]/', '_', $value);
        $lengths = [
            'acqresponsecode'       => 3,
            'amount'                => 10,
            'authorizeid'           => 12,
            'batchno'               => 6,
            'cardexpiry'            => 4,
            'cardnumber'            => 40,
            'cardsecuritycode'      => 4,
            'card'                  => 2,
            'locale'                => 5,
            'merchant'              => 16,
            'merchanttxnref'        => 40,
            'orderinfo'             => 34,
            'password'              => 24,
            'pcerror'               => 2048,
            'pcversion'             => 30,
            'amount'                => 10,
            'txnresponsecode'       => 1,
            'receiptno'             => 12,
            'recordnumber'          => 450,
            'result'                => 450,
            'returnurl'             => 255,
            'ticketno'              => 15,
            'transactionno'         => 21,
            'transactionnumber'     => 19,
            'username'              => 20,
        ];
        switch ($type) {
            case 'cardnumber':
            case 'cardexpiry':
            case 'amount':
                $value = preg_replace('/[^0-9]/', '', $value);
                break;
            case 'merchanttxnref':
            case 'orderinfo':
                $value = preg_replace("/[^\\x14-\\x7b\\x7d\\x7e]/", '', $value);
                break;
                //case "argument field":
                //    if (!array_key_exists($value, $lengths)) $value = "";
                //    break;
        }
        if (array_key_exists($type, $lengths)) {
            $value = substr($value, 0, $lengths[$type]);
        }

        $value = trim($value);
        return $value;
    }
    public function _echo($echo_string)
    {
        return $this->queryFirst(
            1,
            $this->_escape($echo_string)
        );
    }
    public function getDigitalOrder($order_info, $merchant_id = '', $amount = 0, $local = 'en', $return_url = '')
    {
        if ($amount <= 0) {
            return '0,0';
        }

        if ('' == $merchant_id) {
            $merchant_id = $this->user;
        }

        if ('' == $return_url) {
            $return_url = $this->return_url;
        }

        return $this->queryFirst(
            2,
            $this->_escape($order_info, 'orderInfo'),
            $this->_escape($merchant_id, 'merchant'),
            $this->_escape($amount, 'amount'),
            $this->_escape($local, 'locale'),
            $this->_escape($return_url, 'returnURL')
        );
    }
    public function decryptDigitalReceipt($digital_receipt = '')
    {
        if ('' == $digital_receipt) {
            return '0,0';
        }

        return $this->queryFirst(
            3,
            $this->_escape($digital_receipt, 'DigitalReceipt')
        );
    }
    public function getResultField($argument_field = '')
    {
        if ('' == $argument_field) {
            return '0,0';
        }

        return $this->queryFirst(
            4,
            $this->_escape($argument_field, 'Argument Field')
        );
    }
    public function nextResult()
    {
        return $this->queryFirst(
            5
        );
    }
    public function sendMOTODigitalOrder($order_info, $merchant_id = '', $amount = 0, $locale = 'en', $return_url = '')
    {
        if ($amount <= 0) {
            return '0,0';
        }

        if ('' == $merchant_id) {
            $merchant_id = $this->user;
        }

        return $this->queryFirst(
            6,
            $this->_escape($order_info, 'OrderInfo'),
            $this->_escape($merchant_id, 'Merchant'),
            $this->_escape($amount, 'Amount'),
            $this->_escape($locale, 'Locale'),
            $this->_escape($return_url, 'ReturnURL')
        );
    }
    public function addDigitalOrderField($field_name, $field_value)
    {
        return $this->queryFirst(
            7,
            $this->_escape($field_name, 'Argument Field'),
            $this->_escape($field_value, $field_name)
        );
    }
    public function doGetVersion()
    {
        return $this->queryFirst(
            13
        );
    }
    public function doAdminCapture($merchant_id = '', $transaction_number = 0, $amount = 0, $username = '', $password = '')
    {
        if ($transaction_number <= 0) {
            return '0,0';
        }

        if ($amount <= 0) {
            return '0,0';
        }

        if ('' == $merchant_id) {
            $merchant_id = $this->user;
        }

        return $this->queryFirst(
            14,
            $this->_escape($merchant_id, 'Merchant'),
            $this->_escape($transaction_number, 'TransactionNumber'),
            $this->_escape($amount, 'Amount'),
            $this->_escape($username, 'Username'),
            $this->_escape($password, 'Password')
        );
    }
    public function doAdminRefund($merchant_id = '', $transaction_number = 0, $amount = 0, $username = '', $password = '')
    {
        if ($transaction_number <= 0) {
            return '0,0';
        }

        if ($amount <= 0) {
            return '0,0';
        }

        if ('' == $merchant_id) {
            $merchant_id = $this->user;
        }

        return $this->queryFirst(
            15,
            $this->_escape($merchant_id, 'Merchant'),
            $this->_escape($transaction_number, 'TransactionNumber'),
            $this->_escape($amount, 'Amount'),
            $this->_escape($username, 'Username'),
            $this->_escape($password, 'Password')
        );
    }
    public function doAdminVoidCapture($merchant_id = '', $transaction_number = 0, $amount = 0, $username = '', $password = '')
    {
        if ($transaction_number <= 0) {
            return '0,0';
        }

        if ($amount <= 0) {
            return '0,0';
        }

        if ('' == $merchant_id) {
            $merchant_id = $this->user;
        }

        return $this->queryFirst(
            16,
            $this->_escape($merchant_id, 'Merchant'),
            $this->_escape($transaction_number, 'TransactionNumber'),
            $this->_escape($amount, 'Amount'),
            $this->_escape($username, 'Username'),
            $this->_escape($password, 'Password')
        );
    }
    public function doAdminVoidRefund($merchant_id = '', $transaction_number = 0, $amount = 0, $username = '', $password = '')
    {
        if ($transaction_number <= 0) {
            return '0,0';
        }

        if ($amount <= 0) {
            return '0,0';
        }

        if ('' == $merchant_id) {
            $merchant_id = $this->user;
        }

        return $this->queryFirst(
            17,
            $this->_escape($merchant_id, 'Merchant'),
            $this->_escape($transaction_number, 'TransactionNumber'),
            $this->_escape($amount, 'Amount'),
            $this->_escape($username, 'Username'),
            $this->_escape($password, 'Password')
        );
    }
    public function getResultCount()
    {
        return $this->queryFirst(
            23
        );
    }
    public function getResultRecord($record_number)
    {
        if ($record_number <= 0) {
            return '0,0';
        }

        return $this->queryFirst(
            24,
            $this->_escape($record_number, 'RecordNumber')
        );
    }
    public function doAdminVoidPurchase($merchant_id = '', $transaction_number = 0, $username = '', $password = '')
    {
        if ($transaction_number <= 0) {
            return '0,0';
        }

        if ('' == $merchant_id) {
            $merchant_id = $this->user;
        }

        return $this->queryFirst(
            25,
            $this->_escape($merchant_id, 'Merchant'),
            $this->_escape($transaction_number, 'TransactionNumber'),
            $this->_escape($username, 'Username'),
            $this->_escape($password, 'Password')
        );
    }
    public function doQueryDR($merchtxnref = '', $username = '', $password = '')
    {
        if ('' == $merchtxnref) {
            return '0,0';
        }

        return $this->queryFirst(
            29,
            $this->_escape($merchtxnref, 'MerchTxnRef'),
            $this->_escape($username, 'Username'),
            $this->_escape($password, 'Password')
        );
    }
    //---------------------------------------------------------------------------
}
