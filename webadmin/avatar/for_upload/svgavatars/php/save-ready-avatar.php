<?php
/******************************************************************************************
 * @name:       save-ready-avatar.php - part of WP plugin for creating vector avatars
 * @version:    1.6
 * @URL:        https://svgavatars.com
 * @copyright:  (c) 2014-2021 DeeThemes (https://codecanyon.net/user/DeeThemes)
 * @licenses:   https://codecanyon.net/licenses/regular
 *              https://codecanyon.net/licenses/extended
 *
 * Store avatars on a server in the 'ready-avatars' directory
*******************************************************************************************/
// require validation functions
require_once( 'validate-avatar-data.php' );

// getting and validating file name and image data from POST
// returned $file variable will be an array with ["name"] and ["type"]
$file = svgAvatars_validate_filename( $_POST['filename'] );
if ( $file['name'] === 'invalid' ) {
    die( "Received file name doesn't match required pattern." );
}
if ( $file['type'] === 'invalid' ) {
    die( 'Received file type is not PNG or SVG.' );
}

// returned $data will be false if the data is extrinsical to PNG or SVG
$data =  svgAvatars_validate_imagedata( $_POST['imgdata'], $file['type'] );
// set the dirctory for constantly saved avatars
$uploads_dir = '../ready-avatars/';

// PNG or SVG file format
if ( $file['type'] === 'png' ) {
    // cheking that validated image data is not empty
    if ( $data == false ) {
        die( 'Received PNG file data is not valid.' );
    }
    $data = base64_decode( $data );
    $file_name = $file['name'] . '.png';
} elseif ( $file['type'] === 'svg' ) {
    // cheking that validated image data is not empty
    if ( $data == false ) {
        die( 'Received PNG file data is not valid.' );
    }
    $data = stripcslashes( $data );
    // cheking that validated code is SVG
    if ( strpos( $data, '<svg xmlns="http://www.w3.org/2000/svg" version="1.1"' ) === false || strrpos($data, "</svg>", -6) === false ) {
        die( 'Received PNG file data is not valid.' );
    }
    $file_name = $file['name'] . '.svg';
} else {
    die( 'Received file type is not PNG or SVG.' );
}

// check if directory is ok
if ( ! is_dir( $uploads_dir ) || ! is_writable( $uploads_dir ) ) {
    die( "The {$uploads_dir} directory does not exist or is not writable." );
}

// save new avatar
if ( ! file_put_contents( $uploads_dir . $file_name, $data ) ) {
    die( 'Error writing avatar on a disk.' );
}

/**
* You can place here an additional PHP code, for example, to store links of saved avatars
* in your database.
*/

// avatar is saved, echo the result to show user a custom success message
die( 'saved_custom' );
