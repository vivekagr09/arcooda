<?php
namespace app\forms;

use app\models\PHPMailerEmail;
use \Template;

//moduleLoad('invoice'); //invoiceToDatabase, invoiceProductsToDatabase, invoiceChargesToDatabase
//moduleLoad('member'); //memberToDatabase
//moduleLoad('spamcheck'); //spamcheck

class AssistedCheckoutForm
{
    /**
     * @return string
     */
    public function formName()
    {
        return '';
    }

    /** @var string|null */
    public $givenName;

    /** @var string|null */
    public $familyName;

    /** @var string|null */
    public $email;

    /** @var string|null */
    public $country;

    /** @var string|null */
    public $cont_subject;

    /** @var string|null */
    public $cont_msg;

    /**
     * @return mixed[][]
     */
    public function rules()
    {
        return [
            [['email', 'givenName', 'familyName', 'country', 'cont_subject', 'cont_msg'], 'safe'],
            /*
            [['email'], 'email'],
            [['country'], 'exist', 'targetRelation' => 'country'],
            [['email'], 'unique', 'targetClass' => Member::class, 'targetAttribute' => 'email_address'],
            */
            [['email', 'givenName', 'familyName', 'country'], 'required'],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels()
    {
        return [
            'givenName'    => _('First Name'),
            'familyName'   => _('Last Name'),
            'email'        => _('Email'),
            'country'      => _('Country'),
            'cont_subject' => _('Issue Encountered'),
            'cont_msg'     => _('Comments'),
        ];
    }

    /**
     * @return string[]
     */
    public function attributeHints()
    {
        return [
        ];
    }

    /**
     * @param string[]|string|null $attributeNames
     * @param bool                 $clearErrors
     *
     * @return bool
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        //@todo replace with parent::validate($attributeNames, $clearErrors);
        if ($clearErrors) {
            $this->clearErrors();
        }
        foreach ($this->requiredAttributes() as $attribute) {
            if (empty($this->$attribute)) {
                $this->addError($attribute, 'Field is required');
            }
        }

        /*
        moduleLoad('spamcheck');

        // More intensive validation
        if (!$this->hasErrors() && function_exists('spamcheck')) {
            $_REQUEST['email'] = $this->email;

            $spamcheck = spamcheck();
            if (SPAMCHECK_REFERER == $spamcheck) {
                $this->addError('*', 'There was a problem detecting the source address');
            } elseif (SPAMCHECK_RATELIMIT == $spamcheck) {
                $this->addError('*', 'Too many attempts, please wait some time before trying again');
            } elseif (SPAMCHECK_HONEYPOT == $spamcheck) {
                $this->addError('*', 'Honeypot has been filled');
            } elseif (SPAMCHECK_REMOTE_ADDR == $spamcheck) {
                $this->addError('*', 'IP Address has been flagged for spam');
            } elseif (SPAMCHECK_EMAIL == $spamcheck) {
                $this->addError('*', 'Email Address has been flagged for spam');
            } elseif (SPAMCHECK_RECAPTCHA == $spamcheck) {
                $this->addError('*', 'Please complete the recaptcha');
            } elseif (SPAMCHECK_OK !== $spamcheck) {
                $this->addError('*', 'Unspecified spam detection error');
            }
        }
        */

        return !$this->hasErrors();
    }

    /**
     * @return string[]
     */
    public function getCountries()
    {
        return getCountryList(false);
    }

    /**
     * @return string[]
     */
    public function getProblems()
    {
        return array(
            1 => "Cannot login to my member account",
            2 => "Unable to register as a new member",
            3 => "The checkout does not allow me to make a payment",
            4 => "Other issue",
        );
    }

    /** @var mixed[] */
    private $member;

    /**
     * @return void
     */
   /* private function buildMember()
    {
        if (!is_null($this->member)) {
            return;
        }

        include __DIR__ . '/../build_member.inc.php';

        $member['first_name']    = $member['name'] = $this->givenName ?? null;
        $member['last_name']     = $member['surname'] = $this->familyName ?? null;
        $member['email_address'] = $member['email'] = $this->email ?? null;
        $member['country']       = $this->country ?? $member['country'] ?? null;

        $this->member = $member;
    }*/
     private function buildMember()
    {
        if (!is_null($this->member)) {
            return;
        }
        global $_GLOBALS;
        /**
         * @var mixed[]   $member
         * @var mixed[][] $member_shippingaddresses
         */
        include __DIR__ . '/../build_member.inc.php';
        moduleLoad('member'); 
        if (!array_key_exists('member', $_GLOBALS)) {
            $member_email=$_REQUEST['email'];
            $member_id = memberIdFromEmail($member_email);
            if ($member_id) {
               $member=$_GLOBALS['member'] = memberFromDatabase($member_id);
            } else {
                $member_id = memberToDatabase($member);
                $member = memberFromDatabase($member_id);
                $_GLOBALS['member'] = $member;

                // Add to shipping address

                $address = [
                    'member_id'  => $_GLOBALS['member']['member_id'],
                    'updated_by' => 0,
                    'default'    => 'y',
                ];

                foreach ([
                    'country'   => 'country',
                    'email'     => 'email',
                    'default'   => 'default',
                    'givenName' => 'contact',
                    'familyName'=> 'contact_last',
                ] as $k => $v) {
                    if (array_key_exists($k, $_POST)) {
                        $address[$v] = trim($_POST[$k]);
                    }
                }

                $shipaddr_id = memberShippingAddressToDatabase($_GLOBALS['member']['id'], $address);
                if ($shipaddr_id) {
                    $address['id']=$shipaddr_id;
                    $_GLOBALS['member']['ship_addr']=$address;
                }
                //end
            }
        }
        $this->member = $member;
    }
    /**
     * @return mixed[]
     */
    public function getMember()
    {
        global $_GLOBALS;

        if ($_GLOBALS['member']['id'] ?? 0) {
            return $_GLOBALS['member'];
        }

        $this->buildMember();
        return $this->member;
    }

    /** @var mixed[] */
    private $invoice;

    /** @var mixed[][] */
    private $invoice_products = [];

    /** @var mixed[][] */
    private $invoice_charges = [];

    /** @var mixed[][] */
    private $invoice_courier_quotes = [];

    /**
     * @return void
     */
    private function buildInvoice()
    {
        global $_GLOBALS;
        global $_STACK;

        if (!is_null($this->invoice)) {
            return;
        }

        if (!is_array($_STACK ?? null)) {
            $_STACK = [];
        }

        $_STACK[] = $_GLOBALS['member'] ?? null;

        $_GLOBALS['member'] = $this->getMember();

        /**
         * @var mixed[]   $invoice
         * @var mixed[][] $invoice_products
         * @var mixed[][] $invoice_charges
         * @var mixed[][] $invoice_courier_quotes
         */
        include __DIR__ . '/../build_invoice.inc.php';

        $invoice['invoice_status']   = 'inquiry';
        $invoice['invoice_action']   = 'working';
        $invoice['ship_locate_date'] = null;
        $invoice['ship_staff_id']    = 0;

        $invoice['bill_country'] = $this->country ?? $invoice['bill_country'] ?? null;
        $invoice['bill_email']   = $this->email ?? $invoice['bill_email'] ?? null;

        $name = trim(sprintf(
            '%s %s',
            $this->givenName ?? '',
            $this->familyName ?? ''
        ));
        $invoice['bill_name'] = (empty($name)) ? $invoice['bill_name'] : $name;

        $message = array();
        if (!empty($this->cont_subject)) {
            $problem_list = $this->getProblems();
            $message[]    = "Issue: " . $problem_list[$this->cont_subject];
        }
        if (!empty($this->cont_msg)) {
            $message[] = $this->cont_msg;
        }
        $invoice["ship_needs"] = trim(implode("\n", $message) . "\n" . ($invoice["ship_needs"] ?? ''));

        $this->invoice                = $invoice;
        $this->invoice_products       = $invoice_products;
        $this->invoice_charges        = $invoice_charges;
        $this->invoice_courier_quotes = $invoice_courier_quotes;

        $_GLOBALS['member'] = array_pop($_STACK);
    }

    /**
     * @return mixed[]
     */
    public function getInvoice()
    {
        $this->buildInvoice();
        return $this->invoice;
    }

    /**
     * @return mixed[][]
     */
    public function getProducts()
    {
        $this->buildInvoice();
        return $this->invoice_products;
    }

    /**
     * @return mixed[][]]
     */
    public function getCharges()
    {
        $this->buildInvoice();
        return $this->invoice_charges;
    }
    /**
     * @return mixed[][]]
     */
    public function getCourierQuotes()
    {
        $this->buildInvoice();
        return $this->invoice_courier_quotes;
    }

    /**
     * @return int|false
     */
    public function save()
    {
        moduleLoad('invoice');
        moduleLoad('member');
        moduleLoad('notify');
        moduleLoad('template');

        if ($this->hasErrors()) {
            return false;
        }

        $member    = $this->getMember();
        $member_id = $member['member_id'] ?? $member['id'] ?? 0;

        if (!$member_id) {
            $this->member['id'] = $this->member['member_id'] = $member_id = memberIdFromEmail($member['email_address'] ?? $member['email']);
        }
        if (!$member_id) {
            $this->member['id'] = $this->member['member_id'] = $member_id = memberToDatabase($member);
        }
        if (!$member_id) {
            return false;
        }
        if($member['spammer']=='y'){
            $rdirect['value']   = 'n';
            $rdirect['message'] = 'Sorry this message cannot be sent. Please contact our office.';
            $data               = json_encode($rdirect);
            setcookie('redirect', $data, strtotime('+1 year'), '/');
            ?>
             <script>
                var data = <?=json_encode(['invoice_id'=>0])?>;
                if (window.opener != null && window.opener.redirectToInvoiceSuccess != null) {
                    window.opener.redirectToInvoiceSuccess(data);
                    window.close();
                }else{
                    window.close();
                    window.location.href='/';
                }
            </script>
            <?php
            die();
        }
        $this->buildInvoice();
        $invoice_id = invoiceToDatabase($this->invoice);

        if (!$invoice_id) {
            return false;
        }

        invoiceProductsToDatabase($invoice_id, $this->invoice_products);
        invoiceChargesToDatabase($invoice_id, $this->invoice_charges);
        invoiceCourierQuotesToDatabase($invoice_id, $this->invoice_courier_quotes);

        // pdf_invoice/setup_invoice looks for the following globals

        /** @global mixed[] $invoice */
        global $invoice;

        /** @global mixed[][] $invoice_products */
        global $invoice_products;

        /** @global mixed[][] $invoice_charges */
        global $invoice_charges;

        $invoice          = invoiceFromDatabase($invoice_id);
        $invoice_products = invoiceProductsFromDatabase($invoice_id);
        $invoice_charges  = invoiceChargesFromDatabase($invoice_id);

        if (is_null($invoice)) {
            return false;
        }
        if(!empty($invoice['customer_requests'])){
            $history                    = [];
            $history['message_sender']  =  $this->member['member_id'];
            $history['message_sentto']  = 'System Admin';
            $history['message_title']   = $invoice['invoice_number'] . ' Created';
            $history['message_type_id'] = 3;
            $history['message_notes']   = $invoice['customer_requests'];
            $history['message_raw']     = $invoice['customer_requests'];
            invoiceHistoryToDatabase($invoice_id, $history);
        }
        /**
         * Send notification to staff
         */
        if (function_exists('notifyActionMade')) {
            notifyActionMade(
                NOTIFY_INQUIRY_CREATED,
                $invoice['site_id'],
                invoiceTemplateVars($invoice, $invoice_products, $invoice_charges)
            );
        }

        /**
         * Generate an invoice attachment
         *
         * @var string $invoice_buffer
         */
        // include $_SERVER['DOCUMENT_ROOT'] . '/webadmin/invoice/templates/pdf_invoice.tpl.php';

        /**
         * We need template class to proceed further with notifications
         */
        if (!class_exists('Template')) {
            return $invoice_id;
        }

        /**
         * Generate the email content
         */
        $email_template = new Template();
        $email_template->set_vars(invoiceTemplateVars($invoice, $invoice_products, $invoice_charges));
        $invoice_template = invoiceEmailTemplatesList();
        $invoice_template = $invoice_template[16];
        foreach ($invoice_template as $k => $v) {
            $email_template->set_contents($v);
            $invoice_template[$k] = $email_template->apply();
        }

        /**
         * Put the email together and send to customer
         */
        $email          = new PHPMailerEmail();
        $email->Subject = $invoice_template['template_subject'];
        $email->Body    = $invoice_template['template_text'];
        $email->IsHTML(false);
        $email->AddStringAttachment(
            $invoice_buffer,
            formatFileSafe('invoice_' . $email_template->vars['[INVOICE_NUMBER]'] . '.pdf'),
            'base64',
            'application/pdf'
        );
        $email->AddAddress($invoice['bill_email'], $invoice['bill_name']);
        $email->addBCC('backup@highwaygroup.net');
        /*if (function_exists('notifyActionRecipients')) {
            foreach (notifyActionRecipients(NOTIFY_GENERIC, $invoice['site_id']) as $email_address => $email_name) {
                $email->addBCC($email_address, $email_name);
            }
        }*/
        $result = $email->Send();

        if (!$result) {
            return $invoice_id;
        }

        /**
         * Add the email to the inquiry history
         */
        /*
        if (function_exists('notifyActionMade')) {
            $vars = $email_template->vars;
            $vars = array_merge($vars, array(
                '[EMAIL_SUBJECT]' => $email->Subject,
                '[EMAIL_BODY]'    => $email->Body,
                '[ATTACHMENTS]'   => array(),
            ));
            foreach ($email->GetAttachments() as $attach) {
                $vars['[ATTACHMENTS]'][] = array(
                    'contents'     => $attach[0],
                    'file_name'    => $attach[1],
                    'content_type' => $attach[4],
                );
            }
            notifyActionMade(NOTIFY_GENERIC, $_GLOBALS['site_settings']['site_id'], $vars);
        }
        */

        $mail_to = array();
        foreach ($email->getToAddresses() as $row) {
            $mail_to[] = $row[0] . ' <' . $row[1] . '>';
        }
        $mail_to = implode(', ', $mail_to);
        $message = array(
            'invoice_id'      => $invoice_id,
            'message_sentto'  => $mail_to,
            'message_sender'  => $email->FromName . ' <' . $email->From . '>',
            'message_type_id' => INVOICE_MESSAGE_INQUIRY_SENT,
            'message_title'   => $email->Subject,
            'message_notes'   => $email->Body,
        );
        invoiceHistoryToDatabase($invoice_id, $message);

        return $invoice_id;
    }

    /**
     * @param mixed[] $member
     *
     * @return void
     */
    public function loadMember($member)
    {
        $this->load([
            'givenName'  => $member['first_name'] ?? $member['name'],
            'familyName' => $member['last_name'] ?? $member['surname'],
            'email'      => $member['email_address'] ?? $member['email'],
            'country'    => $member['country'],
        ]);
    }

    //@todo Re-add yii2 to composer and adopt the Model class to replace the following functions

    /**
     * @var string[][] validation errors (attribute name => array of errors)
     */
    private $_errors;

    /**
     * Returns a value indicating whether there is any validation error.
     *
     * @param string|null $attribute attribute name. Use null to check all attributes.
     *
     * @return bool whether there is any error.
     */
    public function hasErrors($attribute = null)
    {
        return null === $attribute ? !empty($this->_errors) : isset($this->_errors[$attribute]);
    }

    /**
     * Adds a new error to the specified attribute.
     *
     * @param string $attribute attribute name
     * @param string $error new error message
     *
     * @return void
     */
    public function addError($attribute, $error = '')
    {
        $this->_errors[$attribute][] = $error;
    }

    /**
     * Removes errors for all attributes or a single attribute.
     *
     * @param string|null $attribute attribute name. Use null to remove errors for all attributes.
     *
     * @return void
     */
    public function clearErrors($attribute = null)
    {
        if (null === $attribute) {
            $this->_errors = [];
        } else {
            unset($this->_errors[$attribute]);
        }
    }

    /**
     * @return string[]
     */
    public function safeAttributes()
    {
        foreach ($this->rules() as $rule) {
            if ('safe' == $rule[1]) {
                return $rule[0];
            }
        }
        return [];
    }

    /**
     * @return string[]
     */
    public function requiredAttributes()
    {
        foreach ($this->rules() as $rule) {
            if ('required' == $rule[1]) {
                return $rule[0];
            }
        }
        return [];
    }

    /**
     * @todo this is a stub function to replace Yii\base\Model functionality
     *
     * @param mixed[] $data
     *
     * @return true
     */
    public function load($data)
    {
        foreach ($this->safeAttributes() as $attribute) {
            if (array_key_exists($attribute, $data)) {
                $this->$attribute = $data[$attribute];
            }
        }
        return true;
    }
}
