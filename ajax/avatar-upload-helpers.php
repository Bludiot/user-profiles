<?php
/**
 * AJAX requests helper functions
 *
 * For avatar image uploads.
 *
 * @package    Configure 8 Options
 * @subpackage AJAX
 * @since      1.0.0
 */

/**
 * Directory or file exists
 *
 * @since  1.0.0
 * @param  string $fileDir
 * @return mixed
 */
function checkFileDirExists( $fileDir ) {

	if ( file_exists( $fileDir ) ) {
		return true;
	} else{
		AJAX :: exit( 404, 'File Not Found' );
	}
}
/**
 * Sanitize filename
 *
 * @since  1.0.0
 * @param  string $filename
 * @return string
 */
function sanitizeFilename( $filename ) {

	// Transform file or dir name white spaces and special chars to beautiful filename.
	$filename = preg_replace( '~[<>:"/\\\|?*]|[\x00-\x1F]|[\x7F\xA0\xAD]|[#\[\]@!$&\'()+,;=]|[{}^\~`]~x', '-', $filename );

	$filename = ltrim( $filename, '.-' );
	$filename = preg_replace( [ '/ +/','/_+/','/-+/' ], '-', $filename );
	$filename = preg_replace( [ '/-*\.-*/','/\.{2,}/' ], '.', $filename );
	$filename = mb_strtolower( $filename, mb_detect_encoding( $filename ) );
	$filename = trim( $filename, '.-' );

	// Cut length to 255 characters.
	$ext = pathinfo( $filename, PATHINFO_EXTENSION );
	$filename = mb_strcut( pathinfo( $filename, PATHINFO_FILENAME ), 0, 255 - ( $ext ? strlen( $ext ) + 1 : 0 ), mb_detect_encoding( $filename ) ) . ( $ext ? '.' . $ext : '' );

	return $filename;
}

/**
 * Delete image
 *
 * @since  1.0.0
 * @param  string $storage
 * @param  string $album
 * @param  string $file
 * @return void
 */
function deleteImage( $storage, $album, $file ) {

	$cacheName = 'cache';
	$dir = $storage . DS . $album;
	@unlink( $dir . DS . $cacheName . DS . 'avatar' . DS . $file );  // delete cache image
	$success = unlink( $dir . DS . $file );                  // delete original image
	@unlink( $dir . DS . $cacheName . DS . 'files.php' );       // clear files cache
	@unlink( $storage . DS . 'cache' . DS . 'files.php' );       // clear global cache
	return $success;
}

/**
 * Upload image
 *
 * @since  1.0.0
 * @param  string $pluginPath
 * @param  string $albumDir
 * @param  array $config
 * @return boolean
 */
function uploadImage( $pluginPath, $albumDir, $config ) {

	if ( empty( $_FILES ) ) {
		return false;
	}

	$imageSettings = [
		'avatar' => [
			'cacheName' => 'avatar',
			'width'     => $config->getField( 'avatar_width' ),
			'height'    => $config->getField( 'avatar_height' ),
			'format'    => 'crop',
			'quality'   => 100
		]
	];

	$fileName = sanitizeFilename( $_FILES['file']['name'] );
	$tempFile = $_FILES['file']['tmp_name'];
	$file     = $albumDir . DS . $fileName;
	$cache    = $albumDir . DS . 'cache';
	$success  = false;

	if ( ! file_exists( $albumDir ) ) {
		mkdir( $albumDir, 0755 );
	}

	if ( ! file_exists( $cache  )) {
		mkdir( $cache, 0755 );
	}

	$success = move_uploaded_file( $tempFile,$file );
	if ( ! $success ) {
		return false;
	}

	// Create thumb & large.
	require( $pluginPath . '/includes/classes/class-simple-image.php' );
	require( $pluginPath . '/includes/classes/class-image-upload.php' );

	foreach ( $imageSettings as $value ) {

		$cacheName = $value['cacheName'];
		$cacheDir  = $cache . DS . $cacheName;
		$cacheFile = $cacheDir . DS . $fileName;

		if ( ! file_exists( $cacheDir ) ) {
			mkdir( $cacheDir, 0755 );
		}

		$image = new \UPRO_Classes\Image_Upload( $file );
		$image->resize( $value['width'], $value['height'], $value['format'] );
		$image->toFile( $cacheFile, $value['quality'] );
	}
	return true;
}
