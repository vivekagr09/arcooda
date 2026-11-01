<?php

use SEO\Helper\Url;
use Studio\Content\Pages\StandardPageManager;
use Studio\Util\Ratelimit;

$isAdmin = $this->hasPermission("admin-access");
$title = rt($this->getTitle());

if ($this->getId()) {
    $page = StandardPageManager::getPage($this->getId(), $language->locale);
    $title = $page->getTitle();
    $this->setMeta($page->getMetaTags());
}

$customCssTime = $this->studio->getopt('customCssTime', '0');
$siteSelectorAttr = '';

if ($this->studio->getopt('no-cookie-siteselector') === 'On') {
    if (isset($_GET['site'])) {
        try {
            $site = new Url($_GET['site']);
            $siteSelectorAttr = '?site=' . sanitize_attribute(urlencode($site->getAbsoluteURL()));
        }
        catch (\Exception $e) {}
    }
}

$faviconEnabled = $this->studio->getopt('favicon-name') !== null;
$faviconName = $this->studio->getopt('favicon-name');
$faviconMime = $this->studio->getopt('favicon-mime');
$faviconTime = $this->studio->getopt('favicon-timestamp');

?>
<!DOCTYPE HTML>
<html lang="<?php echo $language->shortLocale; ?>">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title><?php echo $title ?></title>

        <meta name="viewport" content="width=device-width, initial-scale=1"><?php
            foreach ($this->getMeta() as $name => $value) {
        ?>

        <meta name="<?php echo sanitize_attribute($name); ?>" content="<?php echo sanitize_attribute($value); ?>"><?php } ?>


<?php if ($faviconEnabled) { ?>
        <link rel="icon" type="<?php echo $faviconMime; ?>" href="<?php echo $this->getPath(); ?><?php echo $faviconName; ?>?t=<?php echo $faviconTime; ?>">
<?php } ?>
<?php
    if ($this->studio->usePermalinks()) {
        if (!($canonical = $this->studio->permalinks->getCanonical()) !== null) { ?>
        <link rel="canonical" href="<?php echo sanitize_attribute($canonical); ?>">
<?php   }
    }
?>

        <link rel="stylesheet" type="text/css" href="<?php echo $this->getPath(); ?>resources/styles/linea.css">
        <link rel="stylesheet" type="text/css" href="<?php echo $this->getPath(); ?>resources/styles/grid.css">
        <link rel="stylesheet" type="text/css" href="<?php echo $this->getPath(); ?>resources/styles/studio.css?b=<?php echo $this->studio->getVersion(); ?>">
        <link rel="stylesheet" type="text/css" href="<?php echo $this->getPath(); ?>resources/styles/custom.css?t=<?php echo $customCssTime; ?>">

        <!--[if lte IE 7]><script src="<?php echo $this->getPath(); ?>resources/scripts/icons-lte-ie7.js"></script><![endif]-->

        <?php $this->studio->getPluginManager()->call("custom_head"); ?>
        <?php echo $this->studio->getopt("custom-head-html"); ?>

        <?php if (Ratelimit::getCaptchaEnabled()) echo Ratelimit::getCaptchaHeaderCode(); ?>

    </head>
    <body class="<?php echo $language->dir; if ($isAdmin || DEMO) echo " margin-top"; ?>">
        <?php if ($isAdmin || DEMO) { ?>
        <div class="admin-bar">
            <div class="container">
                <div class="padding">
                    <div class="pull-right">
                        <a href="<?php echo $this->getPath(); ?>admin/">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-lock" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <rect x="5" y="11" width="14" height="10" rx="2" />
  <circle cx="12" cy="16" r="1" />
  <path d="M8 11v-4a4 4 0 0 1 8 0v4" />
</svg>
                            Admin panel
                        </a>
                    </div>
                    Logged in as an admin.
                </div>
            </div>
        </div>
        <?php } ?>
        <?php
        $langQuery = $this->studio->sql->query("SELECT * FROM languages");
        if ($langQuery->num_rows > 1) {
        ?>
        <div class="language-bar">
            <div class="container">
                <div class="pull-right">
                    <a class="dropdown">
                        <?php echo $language->name; ?> <span class="arrow-down"></span>
                    </a>
                    <div class="language-menu">
                        <?php
                        while ($lang = $langQuery->fetch_array()) {
                            $active = ($lang['name'] == $language->name) ? "active" : "";
                            $locale = "";

                            if (strpos($lang['locale'], '-') !== false) {
                                $parts = explode('-', $lang['locale']);
                                $locale = $parts[0];
                            }

                            $query = $this->studio->permalinks->getQueryArguments();
                            $query['setlang'] = $lang['locale'];
                            $new = http_build_query($query);

                            echo "<a href=\"?$new\" class=\"$active\" hreflang=\"$locale\">{$lang['name']}</a>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        }
        ?>

        <header>
            <div class="container">
                <div class="flex">
                    <div class="logo-container">
                        <?php
                            $homedir = "./" . $this->getPath();

                            $url = $this->studio->getopt('nav-logo-url', '%homedir%');
                            $url = str_ireplace('%homedir%/', '%homedir%', $url);
                            $url = str_ireplace('%homedir%', $homedir, $url);
                            $url = $this->studio->attr($url);
                        ?>
                        <a href="<?php echo $url; ?>" class="logo image <?php echo $this->studio->getopt('logo-height', 'small'); ?>">
                            <img src="<?php echo $this->getPath(); ?>resources/images/logo.png?t=<?php echo $this->studio->getopt('logo-timestamp', 0); ?>" alt="Logo" />
                        </a>
                    </div>
                    <div class="nav-toggle">
                        <a class="toggle">
                            <div></div>
                            <div></div>
                            <div></div>
                        </a>
                    </div>
                    <div class="break"></div>
                    <div class="nav-container">
                        <nav class="navigation">
                            <ul>
                                <?php if ($this->studio->getopt('nav-show-home', 'On') === 'On' && $this->studio->getopt('disable-home-page') !== 'On') { ?>
                                <li>
                                    <a href="./<?php echo $this->getPath(); ?>">
                                        <div data-icon="Z" class="icon"></div>
                                        <?php pt("Home"); ?>
                                        <?php if ($this->getPage() == 1) { ?><div class="arrow-down home"></div><?php } ?>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php
                                $this->studio->getPluginManager()->call("page_menu_1", [$this->getPath(), $this->getPage()]);
                                if ($this->studio->getopt("show-tools") != "On" || ($this->studio->getopt("show-tools") == "On" && $account->isLoggedIn())) {
                                    if ($this->studio->getopt('nav-show-tools', 'On') === 'On') {
                                        $toolsPath = './' . $this->getPath() . 'tools.php' . $siteSelectorAttr;
                                        if ($this->studio->getopt('disable-home-page') === 'On') {
                                            $toolsPath = './' . $this->getPath() . $siteSelectorAttr;
                                        }
                                ?>
                                <li>
                                    <a href="<?php echo $toolsPath; ?>">
                                        <div data-icon="?" class="icon"></div>
                                        <?php pt("Tools"); ?>
                                        <?php if ($this->getPage() == 2) { ?><div class="arrow-down dark"></div><?php }
                                        else if ($this->getPage() == 2.5 && $this->studio->getopt('experimental-tool-design') === 'On') { ?><div class="arrow-down tool"></div><?php }
                                        else if ($this->getPage() == 2.5) { ?><div class="arrow-down blue"></div><?php } ?>
                                    </a>
                                </li>
                                <?php
                                    }
                                }
                                if ($this->studio->getopt('navlink1t')) {
                                    $attrs = '';

                                    if ($this->studio->getopt('navlink1b') === '1') {
                                        $attrs = 'target="_blank" rel="noopener"';
                                    }
                                ?>
                                <li>
                                    <a href="<?php echo $this->studio->getopt('navlink1'); ?>" <?php echo $attrs; ?>>
                                        <div data-icon="<?php echo $this->studio->getopt('navlink1i'); ?>" class="icon"></div>
                                        <?php echo $this->studio->getopt('navlink1t'); ?>
                                    </a>
                                </li>
                                <?php
                                }
                                if ($this->studio->getopt('navlink2t')) {
                                    $attrs = '';

                                    if ($this->studio->getopt('navlink2b') === '1') {
                                        $attrs = 'target="_blank" rel="noopener"';
                                    }
                                ?>
                                <li>
                                    <a href="<?php echo $this->studio->getopt('navlink2'); ?>" <?php echo $attrs; ?>>
                                        <div data-icon="<?php echo $this->studio->getopt('navlink2i'); ?>" class="icon"></div>
                                        <?php echo $this->studio->getopt('navlink2t'); ?>
                                    </a>
                                </li>
                                <?php
                                }
                                if ($this->studio->getopt('navlink3t')) {
                                    $attrs = '';

                                    if ($this->studio->getopt('navlink3b') === '1') {
                                        $attrs = 'target="_blank" rel="noopener"';
                                    }
                                ?>
                                <li>
                                    <a href="<?php echo $this->studio->getopt('navlink3'); ?>" <?php echo $attrs; ?>>
                                        <div data-icon="<?php echo $this->studio->getopt('navlink3i'); ?>" class="icon"></div>
                                        <?php echo $this->studio->getopt('navlink3t'); ?>
                                    </a>
                                </li>
                                <?php
                                }
                                if (!$account->isLoggedIn() && $this->studio->getopt("show-login") == "On") {
                                    if ($this->studio->getopt('nav-show-login', 'On') === 'On') {
                                ?>
                                <li>
                                    <a href="./<?php echo $this->getPath(); ?>account/login.php">
                                        <div data-icon="9" class="icon"></div>
                                        <?php pt("Login"); ?>
                                        <?php if ($this->getPage() == 3) { ?><div class="arrow-down blue"></div><?php } ?>
                                    </a>
                                </li>
                                <?php
                                    }
                                }
                                elseif ($account->isLoggedIn()) {
                                    if ($this->studio->getopt('nav-show-account', 'On') === 'On') {
                                ?>
                                <li>
                                    <a href="./<?php echo $this->getPath(); ?>account/">
                                        <div data-icon="9" class="icon"></div>
                                        <?php pt("Account"); ?>
                                        <?php if ($this->getPage() == 3) { ?><div class="arrow-down blue"></div><?php } ?>
                                    </a>
                                </li>
                                <?php
                                    }
                                }
                                ?>
                                <?php
                                $this->studio->getPluginManager()->call("page_menu_2", [$this->getPath(), $this->getPage()]);
                                ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </header>
