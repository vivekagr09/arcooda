<?php

use Studio\Content\AdsManager;
use Studio\Util\TrustedNetwork;

/*error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);
@ini_set('display_errors', 1);
@ini_set('log_errors', 1);*/

@ob_start();

if (!defined('DISABLE_COOKIES')) {
    @session_start();
}

@set_time_limit(0);

require_once dirname(dirname(__FILE__)) . "/libraries/autoload.php";

// Check PHP version
if (version_compare(phpversion(), '5.6.1', '<')) {
    echo "<h1>Incompatible server!</h1>";
    echo "<p>Please upgrade to at least PHP 5.6.1 to run this script.</p>";
    die;
}

// Autoload composer
require __DIR__ . '/vendor/autoload.php';

// Start the studio
global $studio;
global $language;
global $account;
$studio = new Studio\Base\Studio();
$plugins = $studio->getPluginManager();

// Start services that rely on the database
if (!isset($continueWithoutSQL) && $studio->sql !== null) {
    $studio->errors->register('public');

    $account = new Studio\Base\Account($studio);
    $language = new Studio\Base\Language(dirname(dirname(__FILE__)) . "/resources/languages");
    $page = new Studio\Display\Page($studio);
    $site = $account->getCurrentWebsite();

    $api = new API\API($studio->getopt("api.secretkey"));
}
// Apply trusted proxies
TrustedNetwork::init();

// Timeout enforcement
if (!function_exists('getTimeout')) {
    function getTimeout() {
        global $studio;

        $limit = max(0, @intval(@ini_get('max_execution_time')));
        $limit = $limit > 0 ? $limit : 60 * 10;
        $default = min($limit - 5, 300);

        if (!$studio || !$studio->sql) {
            return $default;
        }

        try {
            $timeout = (int) $studio->getopt("webrequest-timeout", $default);
            return min($timeout, $limit);
        }
        catch (Exception $ex) {
            return $default;
        }
    }
}
// CURL encoding
if (!function_exists('getCurlEncoding')) {
    function getCurlEncoding() {
        global $studio;

        $eaActive = false;
        $eaIniConsiderations = array(
            ini_get('session.save_path'),
            ini_get('extension_dir'),
            ini_get('include_path')
        );

        if ($studio && $studio->sql) {
            try {
                $option = trim($studio->getopt('curl-encoding-override', ''));

                if (strlen($option) > 0) {
                    return $option;
                }
            }
            catch (Exception $ex) {}
        }

        foreach ($eaIniConsiderations as $consideration) {
            if (is_string($consideration)) {
                if (strpos($consideration, '/ea-') !== false) {
                    $eaActive = true;
                    break;
                }
            }
        }

        // EasyApache breaks brotli in some cases
        if ($eaActive) {
            return 'gzip, deflate';
        }

        return '';
    }
}
// Mount permalinks if enabled
if ($studio->usePermalinks() && isset($_SERVER['REQUEST_URI'])) {
    $studio->permalinks->mount();
}

// Private mode
if ($studio->getopt("private-mode") === "On") {
    $page->setMeta('robots', 'noindex, nofollow');
}

// Define variables for the API
$apiCertificatePath = dirname(dirname(__FILE__)) . "/resources/certificates/baileyherbert.DSTRootCAX3.crt";
$seohubCertificatePath = dirname(dirname(__FILE__)) . "/resources/certificates/baileyherbert.DSTRootCAX3.crt";

if (!function_exists('rt')) {
    /**
     * Returns a translation, filling in the specified variable values.
     *
     * @param string $p The input string to translate.
     * @param mixed $a  Value to replace the {$1} variable.
     * @param mixed $b  Value to replace the {$2} variable.
     * @param mixed $c  Value to replace the {$3} variable.
     * @param mixed $d  Value to replace the {$4} variable.
     * @return string The new, translated phrase.
     */
    function rt($p, $a = null, $b = null, $c = null, $d = null) {
        global $language;
        $t = $language->translate($p);

        if ($a !== null) $t = str_replace('{$1}', Sanitize::generic($a), $t);
        if ($b !== null) $t = str_replace('{$2}', Sanitize::generic($b), $t);
        if ($c !== null) $t = str_replace('{$3}', Sanitize::generic($c), $t);
        if ($d !== null) $t = str_replace('{$4}', Sanitize::generic($d), $t);

        return $t;
    }
}

if (!function_exists('rtt')) {
    /**
     * Returns a translation, filling in the specified variable values, without sanitization (trusted input only).
     *
     * @param string $p The input string to translate.
     * @param mixed $a  Value to replace the {$1} variable.
     * @param mixed $b  Value to replace the {$2} variable.
     * @param mixed $c  Value to replace the {$3} variable.
     * @param mixed $d  Value to replace the {$4} variable.
     * @return string The new, translated phrase.
     */
    function rtt($p, $a = null, $b = null, $c = null, $d = null) {
        global $language;
        $t = $language->translate($p);

        if ($a !== null) $t = str_replace('{$1}', $a, $t);
        if ($b !== null) $t = str_replace('{$2}', $b, $t);
        if ($c !== null) $t = str_replace('{$3}', $c, $t);
        if ($d !== null) $t = str_replace('{$4}', $d, $t);

        return $t;
    }
}

if (!function_exists('et')) {
    /**
     * Translates and echoes.
     *
     * @param string $p
     * @param mixed $a
     * @param mixed $b
     * @param mixed $c
     * @param mixed $d
     * @return void
     */
    function et($p, $a = null, $b = null, $c = null, $d = null) {
        echo rt($p, $a, $b, $c, $d);
    }
}

if (!function_exists('pt')) {
    /**
     * Alias for `et()` – translates and echoes.
     *
     * @param string $p
     * @param mixed $a
     * @param mixed $b
     * @param mixed $c
     * @param mixed $d
     * @return void
     */
    function pt($p, $a = null, $b = null, $c = null, $d = null) {
        et($p, $a, $b, $c, $d);
    }
}

if (!function_exists('sanitize_html')) {
	/**
	 * Sanitizes the output for inclusion in HTML. The angle bracket characters (<>) will be escaped.
	 *
	 * @param mixed $output
	 * @return string
	 */
    function sanitize_html($output) {
        return Sanitize::html($output);
    }
}

if (!function_exists('sanitize_trusted')) {
	/**
	 * Sanitizes trusted output.
	 *
	 * @param mixed $output
	 * @return string
	 */
    function sanitize_trusted($output) {
        return Sanitize::trusted($output);
    }
}

if (!function_exists('sanitize_generic')) {
	/**
	 * Sanitizes untrusted output for generic usage (within an attribute or HTML).
	 *
	 * @param mixed $output
	 * @return string
	 */
    function sanitize_generic($output) {
        return Sanitize::generic($output);
    }
}

if (!function_exists('sanitize_attribute')) {
	/**
	 * Sanitizes the given output for use within an HTML attribute.
	 *
	 * @param mixed $output
	 * @param int $quotes Which style of quotes to escape (defaults to both).
	 * @return string
	 */
    function sanitize_attribute($output, $quotes = 3) {
        return Sanitize::attribute($output, $quotes);
    }
}

# Call the studio_loaded plugin hook

$plugins->start();
$plugins->call("studio_loaded");

// Facades

class Ads extends AdsManager {}
