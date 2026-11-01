<?php
use Studio\Util\Algorithm\Punycode;
use Studio\Util\Ratelimit;

require "../seostudio/includes/init.php";
$requireSavedProjects = $studio->getopt('project-mode') === 'On';
$currentWebsite = null;
$product_id = $_GET['id'];
$_GET['id'] = 'keyword-density';
$_GET['site'] = 'https://www.arcooda.com';
$_GET['is_updated'] = 1;
$currentWebsite = 'https://www.arcooda.com/series/'.$data['slug'];


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
    $toolEnabled = !in_array($tool->id, $toolsDisabled);
    $toolRequiresAuth = in_array($tool->id, $toolsAuthenticated);
}

if ($tool == null || !$toolEnabled) {
    $page->setTitle("Not Found")->setPage(2.5)->header();

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

if ($toolRequiresAuth && !$account->isLoggedIn()) {
    $studio->redirect("account/login.php?return=" . urlencode($_SERVER['REQUEST_URI']));
}

$plugins->call("tool_init", [$tool->id]);

if ($studio->getopt('show-tools-without-site') === 'On' || $currentWebsite) {
    $tool->prerun($currentWebsite);
}

$topFileHtml = dirname(__FILE__) . '/resources/templates/custom/' . $tool->id . '-top.html';
$topFilePHP = dirname(__FILE__) . '/resources/templates/custom/' . $tool->id . '-top.php';
$bottomFileHtml = dirname(__FILE__) . '/resources/templates/custom/' . $tool->id . '-bottom.html';
$bottomFilePHP = dirname(__FILE__) . '/resources/templates/custom/' . $tool->id . '-bottom.php';

$toolPage = $tool->getPage($language->locale);
$title = rt($tool->name);

if ($toolPage->getTitle() !== '') {
    $title = $toolPage->getTitle();
}

if (isset($_GET['site']) && $_GET['site']) {
    $page->setMeta('robots', 'noindex, nofollow');
}

$page->setMeta($toolPage->getMetaTags());
// $page->setTitle($title)->setPage(2.5)->header();

$currentDomain = '';

if (!is_null($currentWebsite)) {
    try {
        $url = new \SEO\Helper\Url($currentWebsite);
        $currentDomain = $url->unicodeHostName;
    }
    catch (Exception $ex) {}
}

?>
<?php if (($snippet = Ads::commit('header')) !== false) { ?>
    <?php if ($studio->getopt('ad-header-container') === 'On') { ?><div class="container"><?php } ?>
    <?php echo $snippet; ?>
    <?php if ($studio->getopt('ad-header-container') === 'On') { ?></div><?php } ?>
<?php } ?>

<?php

if (file_exists($topFileHtml)) {
    $html = file_get_contents($topFileHtml);
    $allowedPostFlag = stripos($html, 'no-post') === false || empty($_POST);
    $allowedWebsiteFlag = stripos($html, 'no-website') === false || (!$currentWebsite && $tool->requiresWebsite);

    if ($allowedPostFlag && $allowedWebsiteFlag) {
        echo sanitize_trusted($html);
    }
}

if (file_exists($topFilePHP)) {
    require $topFilePHP;
}

$topContent = $toolPage->getTopHTML();
$bottomContent = $toolPage->getBottomHTML();
?>
<section class="tool">
    <div class="container">
        <div class="advertising">
            <div class="content">
                <script>
                    window.site = "<?php echo $tool->requiresWebsite ? Punycode::toUnicode($currentWebsite) : ''; ?>";
                    window.tool_id = "<?php echo $tool->id; ?>";
                    window.tool_name = "<?php echo sanitize_attribute(rt($tool->name)); ?>";
                    window.force_show_copy = <?php echo DEMO ? 'true' : 'false'; ?>;
                </script>
<?php
    if (empty($topContent)) {
        if (($snippet = Ads::commit('728x90', 'center mb-2')) !== false) {
            echo $snippet;
        }
        else if (($snippet = Ads::commit('468x60', 'center mb-2')) !== false) {
            echo $snippet;
        }
    }
    
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

<div class="captcha">
    <form action="" method="post">
        <?php if ($error) { ?><div class="captcha-error"><?php et("Incorrect captcha. Please try again."); ?></div><?php } ?>
        <?php foreach ($_POST as $key => $value) { if (!in_array($key, Ratelimit::getCaptchaKeys())) { ?>
            <input type="hidden" name="<?php echo sanitize_attribute($key); ?>" value="<?php echo sanitize_attribute($value); ?>">
        <?php } } ?>
        <div class="g-recaptcha cf-turnstile" data-sitekey="<?php echo sanitize_attribute(Ratelimit::getSiteKey()); ?>"></div>
        <button type="submit" class="captcha-button loadable">Submit</button>
    </form>
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
        Ratelimit::refundRequest('tools');
        $plugins->call("tool_error");
        echo sanitize_trusted("<section class=\"generic error-message\">
            <div>
                <img src=\"{$page->getPath()}resources/images/error128.png\" width=\"64px\" />
            </div>
            <p>{$studio->ptext($e->getMessage())}</p>
        </section>");
    }
    
?>
            </div>
        </div>
    </div>
</section>
