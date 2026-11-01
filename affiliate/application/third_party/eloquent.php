<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use Illuminate\Database\Capsule\Manager as Capsule;

require APPPATH.'core/orm/vendor/autoload.php';

require_once APPPATH.'config/database.php';

// // Eloquent ORM
// $capsule = new Capsule;
// $db['default']['driver'] = 'mysql';
// $capsule->addConnection($db['default']);
// $capsule->bootEloquent();

$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => $db['default']['hostname'],
    'database'  => $db['default']['database'],
    'username'  => $db['default']['username'],
    'password'  => $db['default']['password'],
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);
$capsule->bootEloquent();