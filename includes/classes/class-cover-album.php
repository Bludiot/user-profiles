<?php
/**
 * Cover images album
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

// Access namespaced functions.
use function UPRO_Func\{
	plugin,
	size_format
};

class Cover_Album extends Cover_Images {

	/**
	 * Album name
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string
	 */
	protected $album = 'cover';

	/**
	 * Count album images
	 *
	 * @since  1.0.0
	 * @access public
	 * @return integer
	 */
	public function count_images() {
		return count( $this->images( $this->album ) );
	}

	/**
	 * Default cover image
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $album
	 * @global object $L The Language class.
	 * @return string Returns the form markup.
	 */
	public function default_cover( $album ) {

		// Access global variables.
		global $L;

		$this->loadGallery( $album );
		$imagesSort = $this->config['imagesSort'];

		// Get images.
		$images   = $this->images( $album, $imagesSort );
		$count    = 0;

		// Generate HTML output.
		$html = '<ul class="image-select-list">';

		foreach ( $images as $image => $timestamp ) {

			$count++;
			$html .= sprintf(
				'<li id="cover-select-item-%s"><label for="cover-image-select-%s" class="%s form-tooltip" title="%s"><img src="%s%s%s" /><input type="checkbox" name="default_cover[]" id="cover-image-select-%s" value="%s" %s /><span class="screen-reader-text">%s</span></label></li>',
				$count,
				$count,
				( in_array( $image, plugin()->default_cover() ) ? 'image-select-label cover-select-label image-in-album selected' : 'image-select-label cover-select-label image-in-album' ),
				$image,
				$this->urlPath( $album ),
				$this->path_thumbnail,
				$image,
				$count,
				$image,
				( in_array( $image, plugin()->default_cover() ) ? 'checked' : '' ),
				$image
			);
		}
		$html .= '</ul>';

		if ( 0 == $count ) {
			$html = sprintf(
				'<div class="upload-album-empty"><p>%s</p></div>',
				$L->get( 'No images uploaded' )
			);
		}
		return $html;
	}

	/**
	 * Select cover images
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $album
	 * @global object $L The Language class.
	 * @return string Returns the album markup.
	 */
	public function select_images( $album, $user = '' ) {

		// Access global variables.
		global $L;

		$this->loadGallery( $album );
		$imagesSort = $this->config['imagesSort'];

		// Get images.
		$images  = $this->images( $album, $imagesSort );
		$count   = 0;

		// Generate HTML output.
		$html = '<ul class="image-select-list">';
		foreach ( $images as $image => $timestamp ) {

			$count++;

			$html .= sprintf(
				'<li id="cover-select-item-%s" class="%s"><label for="cover-image-select-%s" class="%s form-tooltip" title="%s"><img src="%s%s%s" /><input type="radio" name="cover_%s[]" id="cover-image-select-%s" value="%s" %s /><span class="screen-reader-text">%s</span></label></li>',
				$count,
				( in_array( $image, plugin()->getValue( 'cover_' . $user ) ) ? 'cover-select-item selected' : 'cover-select-item' ),
				$count,
				( in_array( $image, plugin()->getValue( 'cover_' . $user ) ) ? "image-select-label cover-select-label-{$user} selected" : "image-select-label cover-select-label-{$user}" ),
				$image,
				$this->urlPath( $album ),
				$this->path_thumbnail,
				$image,
				$user,
				$count,
				$image,
				( in_array( $image, plugin()->getValue( 'cover_' . $user ) ) ? 'checked' : '' ),
				$image
			);
		}
		$db = plugin()->getValue( 'cover_' . $user );
		$src   = DOMAIN_CONTENT . plugin()->storage_root . '/cover/' . $db[0];
		if ( ! @file_get_contents( $src ) ) {
			$html .= sprintf(
				'<li class="screen-reader-text"><label for="no-image-found">%s<input id="no-image-found" type="radio" name="cover_%s[]" value="" checked="checked" /></label></li>',
				$L->get( 'Database image not found' ),
				$user
			);
		}
		$html .= '</ul>';

		if ( 0 == $count ) {
			$html = sprintf(
				'<div class="upload-album-empty"><p>%s</p></div>',
				$L->get( 'No images uploaded' )
			);
		}
		return $html;
	}

	/**
	 * Manage cover images
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $album
	 * @global object $L The Language class.
	 * @return string Returns the album markup.
	 */
	public function manage_images( $album ) {

		// Access global variables.
		global $L;

		$this->loadGallery( $album );
		$imagesSort = $this->config['imagesSort'];

		// Get images.
		$images = $this->images( $album, $imagesSort );
		$count  = 0;

		// Generate HTML output.
		$html = '<ul class="image-upload-list">';
		foreach ( $images as $image => $timestamp ) {

			$count++;

			$cover = PATH_CONTENT . $this->storage_root . DS . $album . DS . 'cache' . DS . 'large' . DS . $image;
			$thumb = PATH_CONTENT . $this->storage_root . DS . $album . DS . 'cache' . DS . 'thumb' . DS . $image;

			$html .= sprintf(
				'<li class="upload-form-album image-upload-item" id="cover-image-%s">',
				$count
			);
			$html .= '<div class="image-album-preview">';
			$html .= sprintf(
				'<a href="%s%s%s" class="image-in-album" title="%s" rel="gallery" data-fancybox="cover-gallery" data-caption="%s">',
				$this->urlPath( $album ),
				$this->path_large,
				$image,
				$L->get( 'View Cover Size' ),
				$image . $L->get( ' — Cover Size' )
			);

			$html .= sprintf(
				'<img src="%s%s%s" width="213" height="120" />',
				$this->urlPath( $album ),
				$this->path_large,
				$image
			);
			$html .= '</a>';

			$html .= sprintf(
				'<a href="%s%s%s" class="image-in-album" title="%s" rel="gallery" data-fancybox="cover-gallery-thumbs" data-caption="%s">',
				$this->urlPath( $album ),
				$this->path_thumbnail,
				$image,
				$L->get( 'View Thumbnail Size' ),
				$image . $L->get( ' — Thumbnail Size' )
			);

			$html .= sprintf(
				'<img src="%s%s%s" width="120" height="120" />',
				$this->urlPath( $album ),
				$this->path_thumbnail,
				$image
			);
			$html .= '</a></div>';

			$html .= sprintf(
				'<div class="image-album-details"><p class="image-album-name">%s<br />%s %s<br />%s %s</p><p class="image-album-buttons"><span class="button button-small btn btn-secondary btn-sm btn-danger delete-cover" data-album="%s" data-file="%s" data-number="%s">%s</span></p></div>',
				$image,
				$L->get( 'Cover:' ),
				size_format( filesize( $cover ), 2 ),
				$L->get( 'Thumb:' ),
				size_format( filesize( $thumb ), 2 ),
				$album,
				$image,
				$count,
				$L->get( 'Delete' )
			);
			$html .= '</li>';
		}
		$html .= '</ul>';

		return $html;
	}
}
