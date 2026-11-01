<?php

use Studio\Content\Pages\StandardPageManager;

require "includes/init.php";

if ($studio->getopt('disable-home-page') === 'On') {
    require 'tools.php';
    die;
}

$page->setId('home')->setPage(1)->header();

?>

<section class="jumbo">
    <div class="container">
        <h1><?php pt("Get ahead of your competition"); ?></h1>
        <h2><?php pt("Powerful next-generation tools for search engine optimization at your fingertips."); ?></h2>

        <a href="tools.php"><?php pt("Get started"); ?></a>
    </div>
</section>

<?php
    $home = StandardPageManager::getPage('home', $language->locale);
    $html = $home->getMiddleHTML();

    if ($html !== '') {
?>

<section class="custom-content home-content--middle">
        <div class="container">
            <?php echo $html; ?>
        </div>
</section>

<?php
    }
?>

<section class="icons">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><path d="m24 41.95-2.05-1.85q-5.3-4.85-8.75-8.375-3.45-3.525-5.5-6.3T4.825 20.4Q4 18.15 4 15.85q0-4.5 3.025-7.525Q10.05 5.3 14.5 5.3q2.85 0 5.275 1.35Q22.2 8 24 10.55q2.1-2.7 4.45-3.975T33.5 5.3q4.45 0 7.475 3.025Q44 11.35 44 15.85q0 2.3-.825 4.55T40.3 25.425q-2.05 2.775-5.5 6.3T26.05 40.1Z"/></svg>
                </div>
                <h3><?php pt("Simple"); ?></h3>
                <p><?php pt("Get the information you need quickly and neatly, with no hassle."); ?></p>
            </div>
            <div class="col-md-4">
                <div class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><path d="m24 41.5-18-14 2.5-1.85L24 37.7l15.5-12.05L42 27.5Zm0-7.6-18-14 18-14 18 14Z"/></svg>
                </div>
                <h3><?php pt("Organized"); ?></h3>
                <p><?php pt("Create an account and easily switch between multiple websites."); ?></p>
            </div>
            <div class="col-md-4">
                <div class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><path d="m21.8 30.2 11.4-11.4-2.1-2.05-9.15 9.15-5.05-5.05-2.2 2.2ZM24 43.95q-7-1.75-11.5-8.125T8 21.85V9.95l16-6 16 6v11.9q0 7.6-4.5 13.975T24 43.95Z"/></svg>
                </div>
                <h3><?php pt("Safe"); ?></h3>
                <p><?php pt("Our tools are safe and don't harm your search engine ranks."); ?></p>
            </div>
        </div>
    </div>
</section>

<section class="home-tools">
    <div class="container">
        <form action="tools.php" method="get">
            <input type="text" class="text-input" name="site" placeholder="<?php pt("www.example.com"); ?>">
            <button type="submit" class="loadable"><?php pt("Get started"); ?></button>
        </form>
    </div>
</section>

<?php
    $html = $home->getBottomHTML();

    if ($html !== '') {
?>

<section class="custom-content home-content--bottom">
        <div class="container">
            <?php echo $html; ?>
        </div>
</section>

<?php
    }
?>

<?php
$page->footer();
?>
