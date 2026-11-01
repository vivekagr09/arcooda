<?php
/******************************************************************************************
 * @name:       send-to-gravatar.php - part of jQuery script for creating vector avatars
 * @version:    1.6
 * @URL:        https://svgavatars.com
 * @copyright:  (c) 2014-2021 DeeThemes (https://codecanyon.net/user/DeeThemes)
 * @licenses:   https://codecanyon.net/licenses/regular
 *              https://codecanyon.net/licenses/extended
 *
 * Enable to set user's avatar as their Gravatar via XML-RPC
 * Based on public Gravatar's XML-RPC API - https://en.gravatar.com/site/implement/
*******************************************************************************************/

// require IXR - The Incutio XML-RPC Library
require_once( 'IXR.inc.php' );

// require validation functions
require_once( 'validate-avatar-data.php' );

if ( ! isset( $_POST['imgdata'] ) ) {
	die( 'Received PNG file data is empty.' );
}

if ( ! isset( $_POST['datastring1'] ) || empty( $_POST['datastring1'] ) ) {
	die( 'gravatar_email_fail' );
}

if ( ! isset( $_POST['datastring2'] ) || empty( $_POST['datastring2'] ) ) {
	die( 'gravatar_password_fail' );
}

// allow only valid email characters
$email = preg_replace( '/[^a-z0-9+_.@-]/i', '', strtolower( $_POST['datastring1'] ) );
$hash = md5( strtolower( $email ) );
$password = $_POST['datastring2'];

if ( isset( $_POST['rating'] ) && in_array( $_POST['rating'], array("0", "1", "2", "3"), true ) ) {
	$rating = $_POST['rating'];
} else {
	die( 'Received Gravatar rating value is out of required range and/or invalid' );
}

// validating PNG image data (since Gravatar doesn't accept SVG)
$valid_data = svgAvatars_validate_imagedata( $_POST['imgdata'], 'png' );
if ( $valid_data == false ) {
	die( 'Received PNG data is not valid.' );
}

// create the XML-RPC request
$request = new IXR_Client( "secure.gravatar.com", "/xmlrpc?user=" . $hash );

// create and call the first query for saving gravatar
$params = array(
	"data" => $valid_data,
	"rating" => $rating,
	"password" => $password
);
$request->query("grav.saveData", $params);
if ( $request->isError() ) {
	die( trim( 'gravatar_faultcode' . htmlentities( $request->getErrorCode() ) ) );
}

// create and call the second query for using as default gravatar
$params = array(
	"userimage" => $request->getResponse(),
	"addresses" => array( $email ),
	"password" => $password
);
$request->query("grav.useUserimage", $params);
if ( $request->isError() ) {
	die( trim( 'gravatar_faultcode' . htmlentities( $request->getErrorCode() ) ) );
}

// all is fine
die( "gravatar_success" );
