<?php
/**
 * Replacement error handler
 *
 * @param int          $errno
 * @param string       $errstr
 * @param string|null  $errfile
 * @param int|null     $errline
 * @param mixed[]|null $errcontext
 *
 * @return bool
 */
function phpErrorHandler($errno, $errstr, $errfile = null, $errline = null, $errcontext = null)
{
    return;
    /**
     * Keep track of errors, to prevent email spamming
     */
    static $prev_errors = array();

    /**
     * Attempt to auto-detect site name and error email
     */
    $site_name  = strtolower($_SERVER['SERVER_NAME']);
    $site_name  = preg_replace('/[^a-z0-9_\\.\\-]+/', '', $site_name);
    $site_name  = preg_replace('/^www\\./', '', $site_name);
    $site_name  = preg_replace('/^m\\./', '', $site_name);
    $from_email = $error_email = 'errors@' . $site_name;

    /**
     * Override via globals
     */
    global $_GLOBALS;
    if (isset($_GLOBALS)) {
        /**
         * Apply site settings
         */
        if (array_key_exists('site_settings', $_GLOBALS)) {
            if (array_key_exists('home_uri', $_GLOBALS['site_settings'])) {
                $site_name  = str_replace('www.', '', parse_url($_GLOBALS['site_settings']['home_uri'], PHP_URL_HOST));
                $from_email = $error_email = 'errors@' . $site_name;
            }
            if (array_key_exists('site_name', $_GLOBALS['site_settings']) && '' != $_GLOBALS['site_settings']['site_name']) {
                $site_name = $_GLOBALS['site_settings']['site_name'];
            }
            if (array_key_exists('error_email', $_GLOBALS['site_settings']) && '' != $_GLOBALS['site_settings']['error_email']) {
                $from_email = $error_email = $_GLOBALS['site_settings']['error_email'];
            }
            if (array_key_exists('email_error', $_GLOBALS['site_settings']) && '' != $_GLOBALS['site_settings']['email_error']) {
                $from_email = $error_email = $_GLOBALS['site_settings']['email_error'];
            }
        }

        /**
         * If error is caused by a developer, only email to that developer
         */
        if (array_key_exists('staff', $_GLOBALS) && array_key_exists('developer', $_GLOBALS['staff']) && 'y' == $_GLOBALS['staff']['developer'] && '' != $_GLOBALS['staff']['email']) {
            $error_email = $_GLOBALS['staff']['email'];
        }
    }

    $error_type = array(
        1/*E_ERROR*/               => 'Error',
        2/*E_WARNING*/             => 'Warning',
        4/*E_PARSE*/               => 'Parsing Error',
        8/*E_NOTICE*/              => 'Notice',
        16/*E_CORE_ERROR*/         => 'Core Error',
        32/*E_CORE_WARNING*/       => 'Core Warning',
        64/*E_COMPILE_ERROR*/      => 'Compile Error',
        128/*E_COMPILE_WARNING*/   => 'Compile Warning',
        256/*E_USER_ERROR*/        => 'User Error',
        512/*E_USER_WARNING*/      => 'User Warning',
        1024/*E_USER_NOTICE*/      => 'User Notice',
        2048/*E_STRICT*/           => 'Strict',
        4096/*E_RECOVERABLE_ERROR*/=> 'Recoverable Error',
        8192/*E_DEPRECATED*/       => 'Deprecated',
        16384/*E_USER_DEPRECATED*/ => 'User Deprecated',
    );
    if (in_array($errno, array(8, 2048))) {
        return;
    }

    $mail_message   = array();
    $mail_message[] = '#### PHP ERROR ####';
    $mail_message[] = 'PHP error in ' . $site_name . ' Website';
    $mail_message[] = 'Description:	(' . $error_type[$errno] . ') ' . $errstr;
    $mail_message[] = 'Date:	' . date('r');
    $mail_message[] = 'Request URI:	https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if (array_key_exists('HTTP_REFERER', $_SERVER)) {
        $mail_message[] = 'Referer URI:	' . $_SERVER['HTTP_REFERER'];
    }

    $mail_message[] = 'Remote Address:	' . $_SERVER['REMOTE_ADDR'];
    $mail_message[] = 'Local Address:	' . $_SERVER['SERVER_ADDR'];
    if (array_key_exists('REMOTE_USER', $_SERVER)) {
        $mail_message[] = 'Remote User:	' . $_SERVER['REMOTE_USER'];
    }

    if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
        $mail_message[] = 'User Agent:	' . $_SERVER['HTTP_USER_AGENT'];
    }

    $mail_message[] = '---------------------------------------------------------------------';

    foreach (debug_backtrace() as $k => $v) {
        $args = array();
        if (array_key_exists('args', $v) && is_array($v['args'])) {
            foreach ($v['args'] as $k2 => $v2) {
                if (is_null($v2)) {
                    $args[] = 'null';
                } elseif (is_object($v2)) {
                    $args[] = '(' . get_class($v2) . ')';
                } elseif (is_resource($v2)) {
                    $args[] = '(Resource)';
                } elseif (is_int($v2)) {
                    $args[] = $v2;
                } elseif (is_bool($v2)) {
                    $args[] = ($v2) ? 'true' : 'false';
                } elseif (is_float($v2)) {
                    $args[] = $v2;
                } elseif (is_string($v2)) {
                    $args[] = '"' . addslashes($v2) . '"';
                } elseif (is_array($v2)) {
                    ob_start();
                    var_dump($v2);
                    $args[] = ob_get_clean();
                } else {
                    $args[] = '"' . addslashes($v2) . '"';
                }
            }
        }

        $mail_message[] = sprintf(
            '#%02d %s(%s) called at [%s:%d]',
            $k,
            (array_key_exists('function', $v)) ? $v['function'] : 'anon',
            implode(', ', $args),
            (array_key_exists('file', $v)) ? $v['file'] : '(anonymous)',
            (array_key_exists('line', $v)) ? $v['line'] : '??'
        );
    }

    $mail_message[] = '---------------------------------------------------------------------';

    foreach (array(
        'Session' => isset($_SESSION) ? $_SESSION : null,
        'Cookie'  => isset($_COOKIE) ? $_COOKIE : null,
        'Post'    => isset($_POST) ? $_POST : null,
        'Files'   => isset($_FILES) ? $_FILES : null,
    ) as $name => $array) {
        if (is_array($array) && count($array) > 0) {
            $mail_message[] = $name . ' Values:';
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $mail_message[] = $key . ':	' . var_export($value, 1);
                } else {
                    $mail_message[] = $key . ':	' . $value;
                }
            }

            $mail_message[] = '---------------------------------------------------------------------';
        }
    }

    $mail_message[] = 'Filename: ' . $errfile;
    $mail_message[] = 'Line Number: ' . $errline;

    if (in_array($errno, array(1))) {
?>
<p>There was a problem with the page you are trying to view.<br />
Please try again later.</p>
<p>An E-Mail has been sent to our Technical Staff.</p>
<p>We apologise for any inconvenience.</p>
<?php
    }
    $compare_me = md5($errline . ' ' . $errfile . ' ' . $errstr);
    if (!in_array($compare_me, $prev_errors)) {
        @mail($error_email, 'PHP ' . ((array_key_exists($errno, $error_type)) ? $error_type[$errno] : 'Error') . ': ' . $site_name, implode("\n", $mail_message), 'From:' . $from_email);
        $prev_errors[] = $compare_me;
    }
}

if (defined('CUSTOM_ERROR_HANDLER')) {
    return;
}

define('CUSTOM_ERROR_HANDLER', 1);

//Sets Error Handlers
$old_error_handler = set_error_handler('phpErrorHandler');
