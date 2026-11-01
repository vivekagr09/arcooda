<?php
/***********************
 * Suppliers & Purchase Order Config directives
 * Filename: spo_config.php
 ***********************
 * Purpose:
 *	Central repository for spo config directives
 * Configuration Variables:
 *	just read the file ;)
 ***********************/

// Cookie Magic (Here's a nice place to put it)
if (isset($_COOKIE['session_key'])) {
    $session_key = $_COOKIE['session_key'];
} else {
    setcookie('session_key', $session_key = md5(microtime()), null, "/");
    header("Location: " . $_SERVER['PHP_SELF']);
}

// Global Configuration________________________________________________________
$config['db_prefix'] = "new_";					// Appended to the front of DB table names
$config['sitename'] = "www.coinopexpress.com";	// Where the site is supposed to live
$config['base_dir'] = $_SERVER['DOCUMENT_ROOT'] . "/webadmin/new_suppliers";
$config['template_path'] = $config['base_dir'] . "/templates/";	// Where the templates live

/*   DON'T CHANGE THIS!   */
$config['live'] = stristr($_SERVER['SERVER_NAME'], $config['sitename']);
/* END DON'T CHANGE THIS! */
