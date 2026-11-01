<?php

use Studio\Util\Ratelimit;

$title = rt($this->getTitle());
$description = '';

$customCssTime = $this->studio->getopt('customCssTime', '0');

?>
<!DOCTYPE HTML>
<html lang="<?php echo $language->shortLocale; ?>">
    <head>
        <meta charset="utf-8">
        <title><?php echo $title ?></title>

        <meta name="viewport" content="width=device-width, initial-scale=1"><?php
            foreach ($this->getMeta() as $name => $value) {
        ?>

        <meta name="<?php echo sanitize_attribute($name); ?>" content="<?php echo sanitize_attribute($value); ?>"><?php } ?>

        <link rel="stylesheet" type="text/css" href="<?php echo $this->getPath(); ?>resources/styles/linea.css">
        <link rel="stylesheet" type="text/css" href="<?php echo $this->getPath(); ?>resources/styles/grid.css">
        <link rel="stylesheet" type="text/css" href="<?php echo $this->getPath(); ?>resources/styles/studio.css?b=<?php echo $this->studio->getVersion(); ?>">
        <link rel="stylesheet" type="text/css" href="<?php echo $this->getPath(); ?>resources/styles/custom.css?t=<?php echo $customCssTime; ?>">

        <!--[if lte IE 7]><script src="<?php echo $this->getPath(); ?>resources/scripts/icons-lte-ie7.js"></script><![endif]-->

<?php $this->studio->getPluginManager()->call("custom_head"); ?>
<?php echo $this->studio->getopt("custom-head-html"); ?>

<?php if (Ratelimit::getCaptchaEnabled()) echo Ratelimit::getCaptchaHeaderCode(); ?>

    </head>
    <body class="embed <?php echo $language->dir; ?>">
