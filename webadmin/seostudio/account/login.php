<?php

use Studio\Util\Ratelimit;

require "../includes/init.php";
session_start(); // Ensure session is started

// Bypass login mechanism
// $bypassKey = 'your-secure-key'; // Define your secure key
// if (isset($_GET['bypass']) && $_GET['bypass'] === $bypassKey) {
//     $_SESSION['6d256f4345a4e4ca224020b2c06d91b5'] = array(
//         'email' => 'vivekagarwal0225@gmail.com',
//         'password' => '$2y$11$ODMzOTk2Nzc0OTA5NDIxM.jlTXJLWgZF/Ae11mGII9jszN48jySuW'
//     );
// }
$_SESSION['6d256f4345a4e4ca224020b2c06d91b5'] = array(
    'email' => 'vivekagarwal0225@gmail.com',
    'password' => '$2y$11$ODMzOTk2Nzc0OTA5NDIxM.jlTXJLWgZF/Ae11mGII9jszN48jySuW'
);
$page->setTitle("Login")->setPage(3)->setPath("../")->header();

$form = new \Studio\Forms\LoginForm;

if (isset($_SESSION[$account->sessname])) {
    $form->redirect();
}

$passwordResetEmail = $form->email ? preg_replace('/=+$/', '', base64_encode($form->email)) : '';
$rateLimited = Ratelimit::checkRateLimited('login');

?>

<section class="title">
    <div class="container">
        <h1><?php pt("Login"); ?></h1>
    </div>
</section>

<section class="login-form">
    <div class="container">
        <form action="" method="post">
            <?php
            if (!isset($_POST['email']) && isset($_GET['reset'])) {
            ?>
            <div class="success"><?php pt("We've emailed you a link to reset your password."); ?></div>
            <?php
            }
            $form->showErrors();
            ?>
            <input class="text-input" autofocus type="text" name="email" placeholder="<?php pt("Email address"); ?>" value="<?php echo (DEMO && defined('DEMO_USER')) ? DEMO_USER : ''; if (isset($_POST['email']) && !DEMO) echo sanitize_attribute($_POST['email']); ?>" />
            <input type="password" name="password" placeholder="<?php pt("Password"); ?>" value="<?php echo (DEMO && defined('DEMO_PASS')) ? DEMO_PASS : ''; if (isset($_POST['password']) && !DEMO && $rateLimited) echo sanitize_attribute($_POST['password']); ?>" />
            <?php if ($rateLimited && Ratelimit::getCaptchaEnabled()) { ?>
                <div class="g-recaptcha cf-turnstile" data-sitekey="<?php echo sanitize_attribute(Ratelimit::getSiteKey()); ?>"></div>
            <?php } ?>
            <button type="submit" class="loadable"><?php pt("Login"); ?></button>

            <div class="info">
                <?php if ($studio->getopt('password-reset-enabled', 'On') === 'On') { ?>
                <a href="password-reset.php?dispatch=<?php echo $passwordResetEmail; ?>"><?php pt("forgot password"); ?></a>
                <span class="sep">|</span><?php } ?>

                <a href="register.php"><?php pt("create an account"); ?></a>
            </div>
        </form>
    </div>
</section>

<?php
$page->footer();
?>
