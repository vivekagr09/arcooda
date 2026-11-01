<?php
namespace app\models;

use PHPMailer\PHPMailer\PHPMailer;

class PHPMailerEmail extends PHPMailer
{
    function __construct($exceptions = null)
    {
        moduleLoad('phpmailer');

        //Note: Copy/Pasted from PHPMailer __construct
        if (null !== $exceptions) {
            $this->exceptions = (bool) $exceptions;
        }
        //Pick an appropriate debug output format automatically
        $this->Debugoutput = (strpos(PHP_SAPI, 'cli') !== false ? 'echo' : 'html');

        $this->Sender   = 'no-reply@highwaygroup.net';
        $this->From     = 'sales@arcooda.com';
        $this->FromName = 'Arcooda Customer Service';
        //$this->Mailer   = "sendmail";
        $this->CharSet  = PHPMailer::CHARSET_UTF8;
        $this->AddBcc('backup@highwaygroup.net');
    }
}
