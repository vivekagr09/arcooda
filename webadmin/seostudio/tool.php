<?php

use Studio\Util\Algorithm\Punycode;
use Studio\Util\Ratelimit;

require "includes/init.php";

$requireSavedProjects = $studio->getopt('project-mode') === 'On';
$currentWebsite = null;

if (!isset($_GET['id'])) {
    $studio->redirect("tools.php");
}

if (isset($_GET['site']) && $account->isLoggedIn() && $requireSavedProjects) {
    try {
        $url = new \SEO\Helper\Url($_GET['site']);

        if ($p = $studio->sql->prepare("SELECT domain, userId FROM websites WHERE userId = {$account->getId()} AND domain LIKE ? ORDER BY timeCreated DESC")) {
            $p->bind_param("s", $url->hostName);
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
    catch (Exception $ex) {
        $studio->redirect("tools");
    }
}
elseif (isset($_GET['site']) && $_GET['site'] !== "") {
    $site = $_GET['site'];
    $expiration = isset($_GET['exp']) ? @intval($_GET['exp']) : 0;

    if ($studio->getopt('no-cookie-siteselector') !== 'On') {
        $expiration = time() + 100;
    }

    if (($account->isLoggedIn() && !DEMO) || ($expiration >= time() && $expiration <= time() + 44200)) {
        try {
            $url = new \SEO\Helper\Url($site);
            $currentWebsite = $url->getAbsoluteURL();

            // Store the current website in cookies
            if ($studio->getopt('no-cookie-siteselector') !== 'On') {
                $account->setCurrentWebsite($currentWebsite);

                if ($studio->usePermalinks()) {
                    $traversal = $studio->permalinks->getPathTraversal();
                    $path = $studio->permalinks->getLink('tool.php')->getUri(['id' => $_GET['id']]);

                    header("Location: " . $traversal . $path);
                    die;
                }
                else {
                    header("Location: tool.php?id=" . urlencode($_GET['id']));
                    die;
                }
            }
        }
        catch (Exception $e) {
            $badInput = true;
        }
    }
    else {
        if ($studio->usePermalinks()) {
            $traversal = $studio->permalinks->getPathTraversal();
            $path = $studio->permalinks->getLink('tool.php')->getUri(['id' => $_GET['id']]);

            header("Location: " . $traversal . $path);
            die;
        }
        else {
            header("Location: tool.php?id=" . urlencode($_GET['id']));
            die;
        }
    }
}

if (is_null($currentWebsite)) {
    $currentWebsite = $account->getCurrentWebsite();
}

if (!$account->isLoggedIn() && $studio->getopt("login-tools") == "On") {
    $studio->redirect("account/login.php?return=" . urlencode($_SERVER['REQUEST_URI']));
}

$tools = $studio->getTools();
$tool = null;

foreach ($tools as $t) {
    if ($t->id == $_GET['id']) {
        $tool = $t;
    }
}

if ($currentWebsite == null && $studio->getopt('show-tools-without-site') !== 'On') {
    $studio->redirect("tools.php");
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
$page->setTitle($title)->setPage(2.5)->header();

$currentDomain = '';

if (!is_null($currentWebsite)) {
    try {
        $url = new \SEO\Helper\Url($currentWebsite);
        $currentDomain = $url->unicodeHostName;
    }
    catch (Exception $ex) {}
}

?>

<?php if ($studio->getopt('experimental-tool-design') === 'On') { ?>
    <section class="title tool-title">
        <div class="container">
            <div class="flex">
                <div class="icon">
                    <img src="<?php echo sanitize_attribute($toolPage->getIcon()); ?>">
                </div>
                <div class="name">
                    <h1><?php echo sanitize_html(rt($tool->name)); ?></h1>
                </div>
            </div>
        </div>
    </section>

    <?php if ($tool->requiresWebsite) { ?>
    <section class="website alt">
        <div class="container">
            <?php
            if ($account->isLoggedIn()) {
            ?>
            <form action="" method="get" class="loadable">
                <div class="site-selector">
                    <?php if (!$requireSavedProjects) { ?>
                    <?php if (!$studio->usePermalinks()) { ?>
                    <input type="hidden" name="id" value="<?php echo sanitize_attribute($_GET['id']); ?>">
                    <?php } ?>
                    <input type="text" class="text-input" name="site" placeholder="<?php pt("Click here to enter a website..."); ?>" value="<?php
                        if ($currentWebsite != null) echo sanitize_html(Punycode::toUnicode($currentWebsite));
                    ?>"/>
                    <?php } ?>
                    <div class="apply hidden">
                        <?php if (!$account->isLoggedIn() || DEMO) { ?>
                        <input type="hidden" name="exp" value="<?php echo time() + 43200; ?>">
                        <?php } ?>
                        <button type="submit" class="loadable"><?php pt("Submit"); ?></button>
                    </div>
                    <div class="select <?php echo ($requireSavedProjects) ? 'project' : '' ?>">
                        <?php if ($requireSavedProjects) { ?>
                        <div class="label">
                            <?php
                                if ($currentWebsite != null) echo sanitize_html($currentDomain);
                                else echo "Select a site...";
                            ?>
                        </div>
                        <?php } ?>
                        <div class="button <?php echo ($requireSavedProjects) ? 'project' : '' ?>">
                            <div class="arrow-down"></div>
                        </div>
                        <div class="dropdown">
                            <ul>
                                <?php
                                $websites = $studio->sql->query("SELECT domain, userId FROM websites WHERE userId = {$account->getId()} ORDER BY timeCreated DESC");

                                if ($websites->num_rows == 0) {
                                ?>
                                <li>
                                    <a><?php pt("You haven't added any sites yet."); ?></a>
                                </li>
                                <?php
                                }
                                else {
                                    while ($site = $websites->fetch_array()) {
                                        $active = "";

                                        if ($currentWebsite == $site['domain']) $active = " class=\"active\"";
                                ?>
                                <li<?php echo $active; ?>><a href="?site=<?php echo sanitize_attribute($site['domain']); ?>&id=<?php echo sanitize_attribute($_GET['id']); ?>"><?php echo sanitize_html(Punycode::toUnicode($site['domain'])); ?></a></li>
                                <?php
                                    }
                                }
                                ?>
                                <li><hr /></li>
                                <li><a href="./<?php echo $page->getPath(); ?>account/websites.php"><div class="icon" data-icon="P"></div> <?php pt("Manage sites"); ?></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>
            <?php
            }
            else {
            ?>
            <form action="" method="get" class="loadable">
                <div class="site-selector">
                    <?php if (!$studio->usePermalinks()) { ?>
                    <input type="hidden" name="id" value="<?php echo sanitize_attribute($_GET['id']); ?>">
                    <?php } ?>
                    <input type="text" class="text-input" name="site" placeholder="<?php pt("Click here to enter a website..."); ?>" value="<?php
                        if ($currentWebsite != null) echo sanitize_html(Punycode::toUnicode($currentWebsite));
                        ?>"/>
                    <div class="apply hidden">
                        <?php if (!$account->isLoggedIn() || DEMO) { ?>
                        <input type="hidden" name="exp" value="<?php echo time() + 43200; ?>">
                        <?php } ?>
                        <button type="submit" class="loadable"><?php pt("Submit"); ?></button>
                    </div>
                </div>
            </form>
            <?php
            }
            ?>
        </div>
    </section>
    <?php } ?>
<?php } else { ?>
    <section class="title">
        <div class="container">
            <h1><?php echo sanitize_html(rt($tool->name)); ?></h1>
        </div>
    </section>
<?php } ?>

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

if (!empty($topContent)) {
?>
<section class="custom-content tool-top">
    <div class="container">
        <div class="content">
            <div class="slots">
                <?php
                    if (strlen($topContent) > 4000 && ($snippet = Ads::commit('300x600')) !== false) {
                        echo $snippet;
                    }
                    else if (($snippet = Ads::commit('300x250')) !== false) {
                        echo $snippet;
                    }
                    else if (($snippet = Ads::commit('250x250')) !== false) {
                        echo $snippet;
                    }
                    else if (($snippet = Ads::commit('200x200')) !== false) {
                        echo $snippet;
                    }
                ?>
            </div>

            <?php echo $topContent; ?>
        </div>
    </div>
</section>
<?php
}

if (!$currentWebsite && $tool->requiresWebsite) {
?>
<section class="generic">
    <div class="container">
        <div class="advertising">
            <div class="content">
                <h3><?php echo sanitize_html(rt("Enter a website above to get started.")); ?></h3>
            </div>
            <div class="slots">
                <?php
                    if (empty($topContent) && empty($bottomContent)) {
                        if (($snippet = Ads::commit('468x60')) !== false) {
                            echo $snippet;
                        }
                        else if (($snippet = Ads::commit('300x250')) !== false) {
                            echo $snippet;
                        }
                        else if (($snippet = Ads::commit('250x250')) !== false) {
                            echo $snippet;
                        }
                        else if (($snippet = Ads::commit('200x200')) !== false) {
                            echo $snippet;
                        }
                    }
                ?>
            </div>
        </div>
    </div>
</section>
<?php
    if (file_exists($bottomFileHtml)) {
        $html = file_get_contents($bottomFileHtml);
        $allowedPostFlag = stripos($html, 'no-post') === false || empty($_POST);
        $allowedWebsiteFlag = stripos($html, 'no-website') === false || (!$currentWebsite && $tool->requiresWebsite);

        if ($allowedPostFlag && $allowedWebsiteFlag) {
            echo sanitize_trusted($html);
        }
    }

    if (file_exists($bottomFilePHP)) {
        require $bottomFilePHP;
    }

    if (!empty($bottomContent)) {
    ?>
    <section class="custom-content tool-bottom">
        <div class="container">
            <div class="content">
                <div class="slots">
                    <?php
                        if (($snippet = Ads::commit('300x250')) !== false) {
                            echo $snippet;
                        }
                        else if (($snippet = Ads::commit('250x250')) !== false) {
                            echo $snippet;
                        }
                        else if (($snippet = Ads::commit('200x200')) !== false) {
                            echo $snippet;
                        }
                    ?>
                </div>

                <?php echo $bottomContent; ?>
            </div>
        </div>
    </section>
    <?php
    }

    $page->footer();
}
else {
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

    if ($tool) {
        $plugins->call("tool_foot", [$tool, $url]);
    }
?>
<?php
    if (empty($bottomContent)) {
        if (($snippet = Ads::commit('728x90', 'center mt-2')) !== false) {
            echo $snippet;
        }
    }
?>
            </div>
            <div class="slots">
                <?php
                    $bottomContent = $toolPage->getBottomHTML();
                    if (empty($bottomContent) && empty($topContent) && ($snippet = Ads::commit('120x600')) !== false) {
                        echo $snippet;
                    }
                ?>
            </div>
        </div>
    </div>
</section>

<?php
    if (file_exists($bottomFileHtml)) echo sanitize_trusted(file_get_contents($bottomFileHtml));
    if (file_exists($bottomFilePHP)) require $bottomFilePHP;

    if (!empty($bottomContent)) {
    ?>
    <section class="custom-content tool-bottom">
        <div class="container">
            <div class="content">
                <div class="slots">
                    <?php
                        if (($snippet = Ads::commit('300x250')) !== false) {
                            echo $snippet;
                        }
                        else if (($snippet = Ads::commit('250x250')) !== false) {
                            echo $snippet;
                        }
                        else if (($snippet = Ads::commit('200x200')) !== false) {
                            echo $snippet;
                        }
                    ?>
                </div>

                <?php echo $bottomContent; ?>
            </div>
        </div>
    </section>
    <?php
    }

    $page->footer();
}
?>
