<?php
namespace app\forms;

use app\models\PHPMailerEmail;
use \Template;

//moduleLoad('invoice'); //invoiceToDatabase, invoiceProductsToDatabase, invoiceChargesToDatabase
//moduleLoad('member'); //memberToDatabase
//moduleLoad('spamcheck'); //spamcheck

class ExpressCheckoutForm
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
    public $organization;

    /** @var string|null */
    public $country;

    /** @var string|null */
    public $addressLine1;

    /** @var string|null */
    public $addressLine2;

    /** @var string|null */
    public $addressLevel2;

    /** @var string|null */
    public $addressLevel1;

    /** @var string|null */
    public $postalCode;

    /** @var string|null */
    public $email;

    /** @var string|null */
    public $tel;

    /** @var string */
    public $register = 'off';

    /** @var string */
    public $alternateShippingAddress = 'off';

    /** @var string|null */
    public $comment;

    /** @var string */
    public $termsAndConditions = 'off';

    /**
     * @return mixed[][]
     */
    public function rules()
    {
        return [
            [['givenName', 'familyName', 'organization', 'country', 'addressLine1', 'addressLine2', 'addressLevel2', 'addressLevel1', 'postalCode', 'email', 'tel', 'register', 'alternateShippingAddress', 'comment', 'termsAndConditions'], 'safe'],
            /*
            [['email'], 'email'],
            [['country'], 'exist', 'targetRelation' => 'country'],
            [['email'], 'unique', 'targetClass' => Member::class, 'targetAttribute' => 'email_address'],
            */
            [['givenName', 'familyName', 'country', 'addressLine1', 'addressLevel2', 'addressLevel1', 'postalCode', 'email', 'tel', 'termsAndConditions'], 'required'],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels()
    {
        return [
            'givenName'                => _('First Name'),
            'familyName'               => _('Last Name'),
            'organization'             => _('Company Name'),
            'country'                  => _('Country'),
            'addressLine1'             => _('Street Address'),
            'addressLine2'             => _('Apartment, suite, unit etc.'),
            'addressLevel2'            => _('Town / City'),
            'addressLevel1'            => _('State / County'),
            'postalCode'               => _('Postcode / ZIP'),
            'email'                    => _('Email Address'),
            'tel'                      => _('Phone'),
            'register'                 => _('Create an account?'),
            'alternateShippingAddress' => _('Ship to a different address?'),
            'comment'                  => _('Order Notes'),
            'termsAndConditions'       => _('I have read and agree to the website terms and conditions'),
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
        moduleLoad('member');

        if (function_exists('memberGetOptionList')) {
            return memberGetOptionList('country');
        }

        return getCountryList(false);
    }

    /** @var mixed[] */
    private $member;

    /** @var mixed[] */
    private $member_shippingaddress;

    /**
     * @return void
     */
    private function buildMember()
    {
        if (!is_null($this->member)) {
            return;
        }

        /**
         * @var mixed[]   $member
         * @var mixed[][] $member_shippingaddresses
         */
        include __DIR__ . '/../build_member.inc.php';

        $member['first_name']    = $member['name'] = $this->givenName ?? null;
        $member['last_name']     = $member['surname'] = $this->familyName ?? null;
        $member['company_name']  = $this->organization;
        $member['country']       = $this->country ?? $member['country'];
        $member['address_1']     = $this->addressLine1 ?? '';
        $member['address_2']     = $this->addressLine2 ?? '';
        $member['address']       = trim($member['address_1'] . "\n" . $member['address_2']);
        $member['city']          = $this->addressLevel2 ?? '';
        $member['state']         = $this->addressLevel1 ?? '';
        $member['post_code']     = $member['zip'] = $this->postalCode;
        $member['email_address'] = $member['email'] = $this->email;
        $member['phone_no']      = $member['phone_number'] = $this->tel;

        $this->member = $member;

        $member_shippingaddress = reset($member_shippingaddresses);

        if ('on' == ($this->alternateShippingAddress ?? 'off')) {

        }

        $this->member_shippingaddress = $member_shippingaddress;
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

    /**
     * @return mixed[]|null
     */
    public function getShippingAddress()
    {
        global $_GLOBALS;

        if ($_GLOBALS['member']['id'] ?? 0) {
            $member_shippingaddress = null;
            foreach (memberShippingAddressesFromDatabase($_GLOBALS['member']['id']) as $member_shippingaddress) {
                if ('y' == $member_shippingaddress['default']) {
                    break;
                }
            }
            return $member_shippingaddress;
        }

        $this->buildMember();
        return $this->member_shippingaddress;
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
        $_GLOBALS['member']['ship_addr'] = [
            $this->getShippingAddress(),
        ];

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

        $invoice['bill_country'] = $this->country ?? $invoice['bill_country'];
        $invoice['bill_email']   = $this->email ?? $invoice['bill_email'];

        $name = trim(sprintf(
            '%s %s',
            $this->givenName ?? '',
            $this->familyName ?? ''
        ));
        $invoice["bill_name"] = (empty($name)) ? $invoice['bill_name'] : $name;

        $message = array();
        if (!empty($this->comment)) {
            $message[] = $this->comment;
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
        if (!$member_id && 'off' != $this->register) {
            $this->member['id'] = $this->member['member_id'] = $member_id = memberToDatabase($member);
            if (!$member_id) {
                return false;
            }
        }
        if($member['spammer']=='y'){
            $rdirect['value']   = 'n';
            $rdirect['message'] = 'Sorry this message cannot be sent. Please contact our office.';
            $data               = json_encode($rdirect);
            setcookie('redirect', $data, strtotime('+1 year'), '/');
            ?>
            <script type="text/javascript">
                window.location.href='/';
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
        include $_SERVER['DOCUMENT_ROOT'] . '/webadmin/invoice/templates/pdf_invoice.tpl.php';

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
        if (function_exists('notifyActionRecipients')) {
            foreach (notifyActionRecipients(NOTIFY_GENERIC, $invoice['site_id']) as $email_address => $email_name) {
                $email->addBCC($email_address, $email_name);
            }
        }
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
        $address = explode("\n", $member['address'] ?? '');
        $this->load([
            'givenName'     => $member['first_name'] ?? $member['name'],
            'familyName'    => $member['last_name'] ?? $member['surname'],
            'organization'  => $member['company_name'] ?? $member['company'],
            'country'       => $member['country'],
            'addressLine1'  => (empty($address[0] ?? '')) ? $member['address_1'] : $address[0],
            'addressLine2'  => (empty($address[1] ?? '')) ? $member['address_2'] : $address[1],
            'addressLevel2' => $member['city'],
            'addressLevel1' => $member['state'],
            'postalCode'    => $member['post_code'] ?? $member['zip'],
            'email'         => $member['email_address'] ?? $member['email'],
            'tel'           => $member['phone_no'] ?? $member['phone'],
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
