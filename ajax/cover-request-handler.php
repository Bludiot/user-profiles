<?php
/**
 * AJAX request handler
 *
 * For cover image uploads.
 *
 * @package    Configure 8 Options
 * @subpackage AJAX
 * @since      1.0.0
 */

// Security constants.
define( 'BLUDIT', true );
define( 'JSON_CMS', true );

// Path separator.
define( 'DS', DIRECTORY_SEPARATOR );

// Load AJAX helper object.
require 'class-ajax.php';

AJAX :: set_header();
AJAX :: auth();

// Check parameters.
if ( ! isset( $_POST['action'] ) ) {
	AJAX :: exit( '400', 'Missing action in request.' );
}

// Clear POST data.
foreach ( $_POST as $key => $value ) {
	if ( strpos ( $value, '..'.DS ) !== false ) {
	AJAX :: exit();
	}
	$value = filter_var( $value, FILTER_SANITIZE_STRING );
	$_POST[$key] = $value;
}

//Set variables.
$action       = $_POST['action']; // @todo some protection.
$success      = false;
$pluginPath   = dirname( pathinfo( __FILE__, PATHINFO_DIRNAME ) );
$basePath     = dirname( __FILE__, 4 ); // CMS base.
$storage_root = 'user-profiles';
$storage      = $basePath . DS . 'bl-content' . DS . $storage_root;
$configFile   = $basePath . DS . 'bl-content' . DS . 'databases' . DS . 'plugins' . DS . 'user-profiles' . DS . 'db.php';

// load helpers.
require 'class-plugin-config.php';
require 'cover-upload-helpers.php';

// Perform action.
switch ( $action ) {
	case 'deleteImage':
		$album = $_POST['album']; // @todo add some protection.
		$file  = $_POST['file']; // @todo add some protection.
		checkFileDirExists( $storage.DS . $album.DS . $file );
		$success = deleteImage( $storage, $album, $file );
		break;

	case 'uploadImage':
		$album   = $_POST['album']; // @todo add some protection.
		$config  = new UPRO_AJAX\Plugin_Config( $configFile, true );
		$success = uploadImage( $pluginPath, $storage . DS . $album, $config );
		@unlink( $storage . DS . $album . DS . 'cache' . DS . 'files.php' ); // Clear album cache.
		@unlink( $storage . DS . 'cache' . DS . 'files.php' ); // Clear global cache.
		break;

	default:
		$success = false;
		break;
}

if ( $success ) {
	AJAX :: exit( 200, $success );
} else {
	AJAX :: exit( 500 );
}
