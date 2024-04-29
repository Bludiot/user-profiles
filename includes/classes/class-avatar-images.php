<?php
/**
 * Avatar images
 *
 * @package    Configure 8 Options
 * @subpackage Classes
 * @since      1.0.0
 */

namespace UPRO_Classes;

// Stop if accessed directly.
if ( ! defined( 'BLUDIT' ) ) {
	die( 'You are not allowed direct access to this file.' );
}

class Avatar_Images {

	/**
	 * Get album
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    mixed
	 */
	protected $get_album = null;

	/**
	 * Storage root
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string
	 */
	protected $storage_root = 'user-profiles';

	/**
	 * Cache age
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    integer
	 */
	protected $max_image_cache = 360;

	/**
	 * Only images
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    boolean
	 */
	protected $only_with_images = true;

	/**
	 * Icon image path
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string
	 */
	protected $path_avatar = 'cache' . DS . 'avatar' . DS;

	/**
	 * Configuration
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected $config = [ 'imagesSort' => 'newest' ];

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $config
	 * @param  boolean $adminView
	 * @return self
	 */
	public function __construct( $config, $adminView = false ) {

		$this->config = array_merge( $this->config, $config );
		if ( $adminView ) {
			$this->only_with_images = false;
			$this->max_image_cache    = false;
		}
	}

	/**
	 * Load gallery
	 *
	 * @since  1.0.0
	 * @access protected
	 * @param  string $album
	 * @return void
	 */
	protected function loadGallery( $album = '' ) {

		if ( is_null( $this->get_album ) ) {
			$storage = $this->storage( $album );
			$this->get_album = new Image_Album( $storage, $this->only_with_images, $this->max_image_cache );
		}
	}

	/**
	 * Album URL
	 *
	 * @since  1.0.0
	 * @access protected
	 * @param  string $album
	 * @global object $site The Site class.
	 * @return string Return the URL to the album.
	 */
	protected function urlPath( $album = '' ) {

		// Access global variables
		global $site;

		$url = $this->addSlash( $site->url(), true );
		$path = $url . 'bl-content/' . $this->storage_root . '/' . $album;

		return $this->addSlash( $path, true );
	}

	/**
	 * Album path
	 *
	 * @since  1.0.0
	 * @access protected
	 * @param  string $album
	 * @return string
	 */
	protected function storage( $album ) {
		$path = PATH_CONTENT . $this->storage_root . DS . $album;
		return $this->addSlash( $path );
	}

	/**
	 * Add slash to string
	 *
	 * Returns the string with trailing slash added.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @param  string $string
	 * @param  boolean $urlPath
	 * @return string
	 */
	protected function addSlash( $string, $urlPath = false ) {

		// If urlPath always use '/' else use delimiter of the filesystem.
		$delimiter = $urlPath ? '/' : DS;
		$lastChar  = substr( $string, -1 );

		if ( $lastChar != $delimiter ) {
			$string = $string . $delimiter;
		}
		return $string;
	}

	/**
	 * Load images
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $album
	 * @param  string $sort
	 * @return array
	 */
	public function images( $album, $sort = 'default' ) {

		$this->loadGallery( $album );
		$list   = $this->get_album->images( $sort );
		$images = [];

		foreach ( $list as $image => $timestamp ) {
			$images[$image]['filename']  = $image;
			$images[$image]['avatar'] = $this->urlPath( $album ) . $this->path_avatar . $image;
		}
		return $images;
	}
}
