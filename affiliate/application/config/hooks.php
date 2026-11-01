<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH."application/config/database.php";

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/


$hook['post_controller_constructor'] = array(
    'class'    => 'Affiliate_Hook',
    'function' => 'sync_aff_session',
    'filename' => 'Affiliate_Hook.php',
    'filepath' => 'hooks'
);


$hook['display_override'][] = array(
    'class'    => '',
    'function' => 'version_images',
    'filename' => 'version_images.php',
    'filepath' => 'hooks'
);

$hook['post_controller_constructor'][] = array(
    'class'    => 'DemoMode',
    'function' => 'disableFeatures',
    'filename' => 'DemoMode.php',
    'filepath' => 'hooks'
);