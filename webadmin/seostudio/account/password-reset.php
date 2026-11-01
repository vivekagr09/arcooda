<?php

use Studio\Util\Ratelimit;

require "../includes/init.php";
$page->setTitle("Reset Password")->setPage(3)->setPath("../")->header();

if (isset($_SESSION[$account->sessname])) {
    header("Location: index.php");
    $studio->stop();
}

if (isset($_GET['token'])) {
	require dirname(__FILE__) . '/password-reset-handle.php';
	die;
}

if ($studio->getopt('password-reset-enabled', 'On') !== 'On') {
    $studio->redirect("account/login.php");
}

$email = null;
if (isset($_GET['dispatch'])) {
	$dispatch = $_GET['dispatch'];

	if (strlen($dispatch) <= 256) {
		$decoded = @base64_decode($dispatch, true);

		if (is_string($decoded) && !empty($decoded)) {
			$email = trim($decoded);
		}
	}
}

$form = new \Studio\Forms\PasswordResetRequestForm;
$displayEmail = $email !== null ? sanitize_attribute($email) : ($form->email ? sanitize_attribute($form->email) : '');
$rateLimited = Ratelimit::checkRateLimited('login');

?>

<section class="title">
    <div class="container">
        <h1><?php pt("Reset Password"); ?></h1>
    </div>
</section>

<section class="login-form">
    <div class="container">
        <form action="" method="post">
            <?php
            $form->showErrors();
            ?>
            <input class="text-input" type="text" name="email" placeholder="<?php pt("Email address"); ?>" value="<?php echo $displayEmail; ?>" />
            <?php if ($rateLimited && Ratelimit::getCaptchaEnabled()) { ?>
                <div class="g-recaptcha cf-turnstile" data-sitekey="<?php echo sanitize_attribute(Ratelimit::getSiteKey()); ?>"></div>
            <?php } ?>
            <button type="submit" class="loadable"><?php pt("Reset"); ?></button>

            <div class="info">
                <a href="login.php"><?php pt("back to login"); ?></a>
            </div>
        </form>
    </div>
</section>

<?php
$page->footer();
?>
