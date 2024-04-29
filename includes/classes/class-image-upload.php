<?php
/**
 * Create image based on Simple_Image
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

class Image_Upload {

	/**
	 * Image
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string
	 */
	private $image;

	/**
	 * MIME type
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    array
	 */
	private $mimeType;

	/**
	 * Crop error
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    boolean
	 */
	private $error = false;

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $file
	 * @return self
	 */
	public function __construct( $file ) {

		$this->image = new Simple_Image( $file );
		$this->image->autoOrient();
		$this->mimeType = $this->image->getMimeType();
	}

	/**
	 * Resize images
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  integer $width
	 * @param  integer $height
	 * @param  mixed $format
	 * @return void
	 */
	public function resize( $width, $height, $format = null ) {

		switch ( $format ) {
			case 'crop':
			$this->image->thumbnail( $width, $height );
			break;

			default:
			$this->image->bestFit( $width, $height );
			break;
		}
	}

	/**
	 * File quality
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $file
	 * @param  integer $quality
	 * @return object
	 */
	public function toFile( $file, $quality = 100 ) {
		return $this->image->toFile( $file, $this->mimeType, $quality );
	}

	/**
	 * Screen quality
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  integer $quality
	 * @return object
	 */
	public function toScreen( $quality = 100 ) {
		$this->image->toScreen( $this->mimeType, $quality );
	}
}
