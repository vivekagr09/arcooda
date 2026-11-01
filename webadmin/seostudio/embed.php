<?php

use SEO\Common\SEOException;
use Studio\Util\Algorithm\Punycode;
use Studio\Util\Ratelimit;

if (isset($_GET['cookies']) && $_GET['cookies'] === "0") {
    define('DISABLE_COOKIES', true);
}

define('STUDIO_EMBEDDED', true);

require "includes/init.php";

if ($studio->getopt('embedding-enabled') !== 'On') {
    die("We do not allow our tools to be embedded, apologies!");
}

// Disable cache to prevent proxies conflicting with referer header
@header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
@header('Cache-Control: no-store, no-cache, must-revalidate');
@header('Cache-Control: post-check=0, pre-check=0', FALSE);
@header('Pragma: no-cache');

$page->setMeta('robots', 'noindex, nofollow');

$showToolHeader = !isset($_GET['h']) || $_GET['h'] == "1";
$showSiteInput = !isset($_GET['si']) || $_GET['si'] == "1";

$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$token = sha1($studio->config['session']['token'] . $studio->getopt('install-time'));

if (isset($_REQUEST['ref']) && isset($_REQUEST['ref_hash'])) {
    $inputRef = $_REQUEST['ref'];
    $inputRefHash = $_REQUEST['ref_hash'];

    if (hash('sha256', $token . '/ref/' . $inputRef) === $inputRefHash) {
        $referer = $inputRef;
    }
}

$refererHash = hash('sha256', $token . '/ref/' . $referer);

$allowedDomains = [];
$allowedDomainsInput = trim($studio->getopt('embedding-domains', ''));

$embedMode = $studio->getopt('embedding-method', 'local');
$strictMode = $studio->getopt('embedding-strict') === 'On';

$lines = preg_split("/(?:\r?\n)+/", $allowedDomainsInput);

$currentDomain = '';
$parsed = @parse_url($referer);

if ($parsed && isset($parsed['host'])) {
    $currentDomain = $parsed['host'];
}

foreach ($lines as $line) {
    $host = trim($line);

    if (strlen($host) === 0) {
        continue;
    }

    $wwwCurrentHost = 'www.' . $host;

    $line = preg_quote($host, '/');
    $line = str_replace('\\*', '.+', $line);
    $allowedDomains[] = '/^' . $line . '$/i';

    $line = preg_quote($wwwCurrentHost, '/');
    $line = str_replace('\\*', '.+', $line);
    $allowedDomains[] = '/^' . $line . '$/i';
}

$match = false;

$publicLink = $studio->getopt('public-url');
$publicParsed = @parse_url($publicLink);

if ($publicParsed && isset($publicParsed['host'])) {
    $currentHost = $publicParsed['host'];
    $wwwCurrentHost = 'www.' . $publicParsed['host'];
    $allowedDomains[] = '/^' . preg_quote($currentHost) . '$/i';
    $allowedDomains[] = '/^' . preg_quote($wwwCurrentHost) . '$/i';
}

foreach ($allowedDomains as $exp) {
    if (preg_match($exp, $currentDomain)) {
        $match = true;
        break;
    }
}

if ($strictMode && empty($currentDomain)) {
    die("This page can only be loaded from a frame.");
}

if ($embedMode !== 'all' && !$match && !empty($currentDomain)) {
    die("We do not allow our tools to be embedded on this website.");
}

if (empty($currentDomain)) {
    $referer = 'Unknown';
}

$requireSavedProjects = $studio->getopt('project-mode') === 'On';
$currentWebsite = null;
$noCookies =
    isset($_GET['cookies']) ? $_GET['cookies'] === "0" :
    $studio->getopt('no-cookie-siteselector-embed', $studio->getopt('no-cookie-siteselector')) === 'On';

if (isset($_GET['site']) && $account->isLoggedIn() && $requireSavedProjects) {
    if ($p = $studio->sql->prepare("SELECT domain, userId FROM websites WHERE userId = {$account->getId()} AND domain LIKE ? ORDER BY timeCreated DESC")) {
        $p->bind_param("s", $_GET['site']);
        $p->execute();
        $p->store_result();

        if ($p->num_rows == 1) {
            $p->bind_result($domain, $userId);
            $p->fetch();

            $account->setCurrentWebsite($domain);
        }
        else {
            $studio->redirect("account/websites.php");
        }
    }
}
elseif (isset($_GET['site']) && !empty($_GET['site'])) {
    $site = $_GET['site'];
    $expiration = isset($_GET['exp']) ? @intval($_GET['exp']) : 0;

    if (!$noCookies || ($expiration >= time() && $expiration <= time() + 44200)) {
        try {
            $url = new \SEO\Helper\Url($site);
            $currentWebsite = $url->getAbsoluteURL();

            // Store the current website in cookies
            if (!$noCookies) {
                $account->setCurrentWebsite($currentWebsite);
            }
        }
        catch (Exception $e) {
            $badInput = true;
        }
    }
    else {
        unset($_GET['site']);
        if (isset($_GET['exp'])) unset($_GET['exp']);

        if ($studio->usePermalinks()) {
            $traversal = $studio->permalinks->getPathTraversal();
            $path = $studio->permalinks->getLink('embed.php')->getUri($_GET);
            header("Location: " . $traversal . $path);
        }
        else {
            $path = $page->getPath() . "embed.php?id=" . urlencode($_GET['id']);
            header("Location: " . $path);
        }

        die;
    }
}

if (is_null($currentWebsite) && !$noCookies) {
    $currentWebsite = $account->getCurrentWebsite();
}

if (!isset($_GET['id'])) {
    $studio->redirect("tools.php");
}

if ($embedMode === 'all' && !$account->isLoggedIn() && $studio->getopt("login-tools") == "On") {
    $studio->redirect("account/login.php?return=" . urlencode($_SERVER['REQUEST_URI']));
}

$tools = $studio->getTools();
$tool = null;

foreach ($tools as $t) {
    if ($t->id == $_GET['id']) {
        $tool = $t;
    }
}

$toolEnabled = true;
$toolRequiresAuth = false;

if ($tool) {
    $toolsDisabled = json_decode($studio->getopt('tools.disabled', '[]'), true);
    $toolsAuthenticated = json_decode($studio->getopt('tools.authenticated', '[]'), true);
    $toolEnabled = !in_array($tool->id, $toolsDisabled) && !in_array($tool->id, $toolsAuthenticated);
}

if ($tool == null || !$toolEnabled) {
    $page->setTitle("Not Found")->setPage(2.5)->embedHeader();

    echo sanitize_trusted("<section class=\"generic\">
        <div class=\"container\">
            <h3>" . sanitize_html(rt("Tool not found")) . "</h3>
        </div>
    </section>");

    if (function_exists('http_response_code')) {
        http_response_code(404);
    }

    $page->footer();
    die;
}

$plugins->call("tool_init", [$tool->id]);

if ($studio->getopt('show-tools-without-site') === 'On' || $currentWebsite) {
    $tool->prerun($currentWebsite);
}

$page->setTitle(rt($tool->name))->setPage(2.5)->embedHeader();

if (isset($_GET['site']) && !empty($_GET['site']) && !$noCookies) {
    try {
        $account->setCurrentWebsite(($_GET['site']));
    }
    catch (SEOException $e) {
        echo "Invalid site.";
        die;
    }
}

$headerHTML = $studio->getopt('embedding-header', '');
$footerHTML = $studio->getopt('embedding-footer', '');

echo sanitize_trusted($headerHTML);
?>

<?php if ($showToolHeader) { ?>
    <section class="title tool-title embedded <?php echo (!$showSiteInput) ? 'no-input' : '' ?>">
        <div class="container">
            <div class="flex">
                <div class="icon">
                    <img src="resources/icons/<?php echo sanitize_attribute($tool->icon); ?>.png">
                </div>
                <div class="name">
                    <h1><?php echo sanitize_html(rt($tool->name)); ?></h1>
                </div>
            </div>
        </div>
    </section>
<?php } ?>

<?php if ($tool->requiresWebsite && $showSiteInput) { ?>
<section class="website alt embedded <?php echo (!$showToolHeader) ? 'no-header' : '' ?>">
    <div class="container">
        <form action="" method="get" class="loadable">
            <input type="hidden" name="id" value="<?php echo sanitize_attribute($_GET['id']); ?>">
            <input type="hidden" name="ref" value="<?php echo sanitize_attribute($referer); ?>">
            <input type="hidden" name="ref_hash" value="<?php echo sanitize_attribute($refererHash); ?>">

            <?php if (isset($_GET['h'])) { ?>
            <input type="hidden" name="h" value="<?php echo sanitize_attribute($_GET['h']); ?>">
            <?php } ?>

            <?php if (isset($_GET['si'])) { ?>
            <input type="hidden" name="si" value="<?php echo sanitize_attribute($_GET['si']); ?>">
            <?php } ?>

            <?php if (isset($_GET['r'])) { ?>
            <input type="hidden" name="r" value="<?php echo sanitize_attribute($_GET['r']); ?>">
            <?php } ?>

            <input type="text" class="text-input" name="site" placeholder="<?php pt("Click here to enter a website..."); ?>" value="<?php
                if ($currentWebsite != null) echo sanitize_html($currentWebsite);
            ?>"/>
            <div class="apply">
                <input type="hidden" name="exp" value="<?php echo time() + 43200; ?>">
                <button type="submit" class="loadable"><?php pt("Submit"); ?></button>
            </div>
        </form>
    </div>
</section>
<?php } ?>

<section class="embed <?php echo (!$showSiteInput && !$showToolHeader) ? 'no-header' : ''; ?>">
<?php

if (!$currentWebsite && $tool->requiresWebsite) {
?>
<section class="generic">
    <div class="container">
        <h3><?php echo sanitize_html(rt("Enter a website above to get started.")); ?></h3>
    </div>
</section>
<?php
    echo sanitize_trusted($footerHTML);
    $page->embedFooter();
}
else {
?>
<section class="tool">
    <div class="container">
        <script>
            window.site = "<?php echo $tool->requiresWebsite ? Punycode::toUnicode($currentWebsite) : ''; ?>";
            window.tool_id = "<?php echo $tool->id; ?>";
            window.tool_name = "<?php echo sanitize_attribute(rt($tool->name)); ?>";
            window.force_show_copy = <?php echo DEMO ? 'true' : 'false'; ?>;
        </script>
<?php
    try {
        $url = !!$currentWebsite ? new \SEO\Helper\Url($currentWebsite) : null;
        $plugins->call("tool_head", [$tool, $url]);

        $ok = Ratelimit::guardRequest('tools');

        if ($ok && $studio->getopt("google-enabled") === 'On' && $tool->usesSearchNetwork) {
            $ok = !Ratelimit::checkRateLimited('search');
        }

        if ($ok && $tool->usesKeywords) {
            $ok = !Ratelimit::checkRateLimited('keywords');
        }

        if ($ok) {
            if (!$tool->start($url)) {
                Ratelimit::refundRequest('tools');
            }
        }
        else {
            $response = null;
            $error = false;

            if (($response = Ratelimit::getCaptchaSubmittedToken()) !== null) {
                if (Ratelimit::checkCaptchaInput($response)) {
                    Ratelimit::guardRequest('tools');
                    $tool->start($url);
                }
                else {
                    $error = true;
                }
            }

            if (Ratelimit::getCaptchaEnabled() && (!$response || $error)) {
?>

<div class="captcha-whitespace" style="min-height: 600px;">
    <div class="captcha">
        <form action="" method="post">
            <input type="hidden" name="ref" value="<?php echo sanitize_attribute($referer); ?>">
            <input type="hidden" name="ref_hash" value="<?php echo sanitize_attribute($refererHash); ?>">
        <?php if ($error) { ?><div class="captcha-error"><?php et("Incorrect captcha. Please try again."); ?></div><?php } ?>
            <?php foreach ($_POST as $key => $value) { if (!in_array($key, Ratelimit::getCaptchaKeys())) { ?>
                <input type="hidden" name="<?php echo sanitize_attribute($key); ?>" value="<?php echo sanitize_attribute($value); ?>">
            <?php } } ?>
            <div class="g-recaptcha cf-turnstile" data-sitekey="<?php echo sanitize_attribute(Ratelimit::getSiteKey()); ?>"></div>
            <button type="submit" class="captcha-button loadable">Submit</button>
        </form>
    </div>
</div>

<?php
            }
            else if (!Ratelimit::getCaptchaEnabled()) {
?>

    <div class="rate-limited">
        <h2><?php et("You're doing this too quickly!"); ?></h2>
        <p><?php et("Please try again later."); ?></p>
    </div>

<?php
            }
        }
    }
    catch (Exception $e) {
        $plugins->call("tool_error");
        $message = sanitize_html($e->getMessage());

        echo sanitize_trusted("<section class=\"generic error-message\">
            <div class=\"container\">
                <div>
                    <img src=\"{$page->getPath()}resources/images/error128.png\" width=\"64px\" />
                </div>
                <p>{$message}</p>
            </div>
        </section>");

        echo sanitize_trusted($footerHTML);
        $page->embedFooter();
        die;
    }

    echo sanitize_trusted($footerHTML);
    $plugins->call("tool_foot", [$tool, $url]);
    $page->embedFooter();
?>
</div>
</section>
<?php
}
?>
</section>
