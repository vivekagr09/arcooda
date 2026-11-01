<?php
/**
 * @var string $action
 */

namespace app\controllers;

use app\helpers\Image;
use app\models\TwigManufacturer;

global $_GLOBALS;

switch ($action) {

    case 'image':
        if ($manufacturers = TwigManufacturer::search(['id' => $_GET['id']])) {
            $manufacturer = reset($manufacturers);
            $test         = TwigManufacturer::img_path() . $manufacturer->img;
            if (is_file($test)) {
                Image::HTTPSendFile($test);
                die();
            }
        }
        break;

}
