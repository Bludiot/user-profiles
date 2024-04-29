<?php
/**
 * List images and albums
 *
 * @package    Configure 8 Options
 * @subpackage Classes
 * @since      1.0.0
 *
 * @link https://novagallery.org
 */

namespace UPRO_Classes;

// Stop if accessed directly.
if ( ! defined( 'BLUDIT' ) ) {
	die( 'You are not allowed direct access to this file.' );
}

class Image_Album {

	/**
	 * Directory
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string
	 */
	protected $dir = '';

	/**
	 * Images
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected $images = [];

	/**
	 * Albums
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected $albums = [];

	/**
	 * Cache directory
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string
	 */
	protected $cacheDir = 'cache';

	/**
	 * Cache file
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string
	 */
	protected $cacheFile = 'files.php';

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $dir
	 * @param  boolean $only_with_images
	 * @param  integer $max_image_cache
	 * @return self
	 */
	public function __construct( $dir, $only_with_images = true, $max_image_cache = 60 ) {

		$this->dir = $dir;

		if ( ! $max_image_cache || ! $this->readCache( $dir, $max_image_cache ) ) {
			$this->images = $this->getImages( $dir );
			$this->albums = $this->getAlbums( $dir );
			if ( $max_image_cache ) {
				$this->writeCache( $dir );
			}
		}

		if ( $only_with_images ) {
			$this->albums = $this->removeEmptyAlbums( $this->albums );
		}
	}

	/**
	 * Get albums
	 *
	 * @since  1.0.0
	 * @access protected
	 * @param  string $dir
	 * @return array
	 */
	protected function getAlbums( $dir ) {

		$dirs   = glob( $dir . '/' . '*', GLOB_ONLYDIR );
		$list   = $this->fileList( $dirs );
		$albums = [];

		// Remove cache dir from album list.
		unset( $list['$this->cacheDir'] );
		foreach ( $list as $album => $image ) {
			$albums[$album] = $this->getImages( $dir . '/' . $album );
		}
		return $albums;
	}

	/**
	 * Get images
	 *
	 * @since  1.0.0
	 * @access protected
	 * @param  string $dir
	 * @return array
	 */
	protected function getImages( $dir ) {
		$images = glob( $dir . '/*{jpg,jpeg,JPG,JPEG,png,PNG}', GLOB_BRACE );
		return $this->fileList( $images, true );
	}

	/**
	 * File list
	 *
	 * Create array of files or dirs without path
	 * and with last modification date.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @param  array $list
	 * @param  boolean $withCaptureDate
	 * @return array
	 */
	protected function fileList( $list, $withCaptureDate = false ) {

		$fileList = [];

		foreach ( $list as $element ) {

			// Add modification date if requested.
			if ( $withCaptureDate ) {
				$value = $this->getImageCaptureDate( $element );

			// Else add as array for sub files.
			} else {
				$value = [];
			}
			$element = strrchr( $element, '/' );
			$element = substr( $element, 1 );
			$fileList[$element] = $value;
		}
		return $fileList;
	}

	/**
	 * Capture date
	 *
	 * @since  1.0.0
	 * @access protected
	 * @param  string $file
	 * @return mixed
	 */
	protected function getImageCaptureDate( $file ) {

		if ( ! file_exists( $file ) ) {
			return false;
		}

		// Use filetime if no image.
		if ( preg_match( '/\.(JPEG|jpeg|JPG|jpg|png|PNG)$/', $file ) === 0 ) {
			return filemtime( $file );
		}

		// Get the photo's EXIF tags.
		try {
			@$exif_data = exif_read_data( $file );

			// Use filemtime if no exif data.
			if ( $exif_data === false ) {
				return filemtime( $file );
			}

		// Use filemtime if exif data error.
		} catch ( Exception $e ) {
			return filemtime( $file );
		}

		// Default value, which represents no date.
		$date = false;

		// Array of EXIF date tags to check.
		$date_tags = [
			'DateTimeOriginal',
			'DateTimeDigitized',
			'DateTime'
			//'FileDateTime'
		];

		// Check for the EXIF date tags, in the order specified above. First value wins.
		foreach ( $date_tags as $date_tag ) {

			if ( isset( $exif_data[$date_tag] ) ) {
				$date = $exif_data[$date_tag];
				$date = $this->timestampFromExif( $date );
				break;
			}
		}

		// If no date tags were found use filemtime.
		if ( ! $date ) {
			return filemtime( $file );
		}

		// If the date that was extracted is a string, convert it to an integer.
		if ( is_string( $date ) ) {
			$date = strtotime( $date );
		}
		return $date;
	}

	/**
	 * Timestamp from EXIF data
	 *
	 * @since  1.0.0
	 * @access protected
	 * @param  string $string
	 * @return string
	 */
	protected function timestampFromExif( $string ) {

		if ( ! (preg_match( '/\d\d\d\d:\d\d:\d\d \d\d:\d\d:\d\d/', $string ) ) ) {
			return $string; // Wrong date.
		}
		$iTimestamp = mktime(
			substr( $string, 11, 2 ),
			substr( $string, 14, 2 ),
			substr( $string, 17, 2 ),
			substr( $string, 5, 2 ),
			substr( $string, 8, 2 ),
			substr( $string, 0, 4 )
		);
		return $iTimestamp;
	}

	/**
	 * List order
	 *
	 * @since  1.0.0
	 * @access protected
	 * @param  array $list
	 * @param  array $order
	 * @return array
	 */
	protected function order( $list, $order ) {

		switch ( $order ) {
			case 'oldest':
				asort( $list );
			break;
			case 'newest':
				arsort( $list );
			break;
			default:
				// Order by name.
				$list = $this->orderByName( $list );
			break;
		}
		return $list;
	}

	// sort array by natcasesort with german umlaute
	// solution based on http://www.marcokrings.de/arrays-sortieren-mit-umlauten/

	/**
	 * Order by name
	 *
	 * @since  1.0.0
	 * @access protected
	 * @param  array $list
	 * @return array
	 */
	protected function orderByName( $list ) {

		// Swap key (name) value (timestamp) for order operations'
		$nameList = [];
		foreach ( $list as $album => $value ) {
			array_push( $nameList, $album );
		}

		// Sort based on http://www.marcokrings.de/arrays-sortieren-mit-umlauten/
		$aOriginal = $nameList;

		if ( count( $aOriginal ) == 0 ) {
			return $aOriginal;
		}

		$aModified = [];
		$aReturn   = [];
		$aSearch   = [ 'Ä', 'ä', 'Ö', 'ö', 'Ü', 'ü', 'ß', '-' ];
		$aReplace  = [ 'A', 'a', 'O', 'o', 'U', 'u', 'ss', ' ' ];

		foreach ( $aOriginal as $key => $val ) {
			$aModified[$key] = str_replace( $aSearch, $aReplace, $val );
		}
		natcasesort( $aModified );
		foreach ( $aModified as $key => $val ) {
			$aReturn[$key] = $aOriginal[$key];
		}

		// Swap back to have a ordered list with the correct key (album) value (timestamp) format.
		$orderedList = [];
		foreach ( $aReturn as $value ) {
			$orderedList[$value] = $list[$value];

		}
		return $orderedList;
	}

	/**
	 * Remove empty albums
	 *
	 * @since  1.0.0
	 * @access protected
	 * @param  array $albums
	 * @return array
	 */
	protected function removeEmptyAlbums( $albums ) {

		foreach ( $albums as $album => $modDate ) {

			if ( ! $this->hasImages( $album ) ) {

				$subAlbum = new Image_Album( $this->dir . '/' . $album );
				if ( ! $subAlbum->hasAlbums() ) {
					unset( $albums[$album] );
				}
			}
		}
		return $albums;
	}

	/**
	 * Read cache
	 *
	 * @since  1.0.0
	 * @access protected
	 * @param  string $dir
	 * @param  integer $maxAge
	 * @return boolean
	 */
	protected function readCache( $dir, $maxAge ) {

		$cacheFile = $dir . '/' . $this->cacheDir . '/' . $this->cacheFile;
		if ( file_exists( $cacheFile ) ) {
			$age = time() - filemtime( $cacheFile );
			if ( $age > $maxAge ) {
				return false;
			}

			$content = file( $cacheFile );

			// Remove first security line (<?php die();).
			unset( $content[0] );

			// Regenerate JSON.
			$content = implode( $content );
			$content = json_decode( $content, true );
			$this->images = $content['images'];
			$this->albums = $content['albums'];

			return true;
		}
		return false;
	}

	/**
	 * Write to cache
	 *
	 * @since  1.0.0
	 * @access protected
	 * @param  string $dir
	 * @return boolean
	 */
	protected function writeCache( $dir ) {

		$cacheDir =  $dir . '/' . $this->cacheDir;
		if ( ! file_exists( $cacheDir ) ) {
			mkdir( $cacheDir, 0755, true );
		}
		$cacheFile = $cacheDir . '/' . $this->cacheFile;
		$content   = [
			'images' => $this->images,
			'albums' => $this->albums
		];
		$content = json_encode( $content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK );

		$data  = '<?php die(); ?>' . PHP_EOL;
		$data .= $content;

		// LOCK_EX flag prevents that anyone else is writing to the file at the same time.
		file_put_contents( $cacheFile, $data, LOCK_EX );

		// Only true because if cache doesn't work, it also work (just only without cache).
		return true;
	}

	/**
	 * Image albums
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $order
	 * @return array
	 */
	public function albums( $order = 'default' ) {

		// Order images in albums.
		$orderedImages = [];
		foreach ( $this->albums as $album => $images ) {
			$orderedImages[$album] = $this->order( $images, $order );
		}

		// Order albums based on first image.
		$orderedAlbums = [];

		// Create array with albums and timestamp of first image.
		foreach ( $orderedImages as $album => $images ) {

			if ( ! empty( $images ) ) {
				$orderedAlbums[$album] = array_values( $images )[0];
			} else {
				$orderedAlbums[$album] = '';
			}
		}
		$orderedAlbums = $this->order( $orderedAlbums, $order );

		// Create array with all albums and all images ordered.
		$albums = [];
		foreach ( $orderedAlbums as $album => $value ) {
			$albums[$album] = $orderedImages[$album];
		}
		return $albums;
	}

	/**
	 * Images in album
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $order
	 * @return array
	 */
	public function images( $order = 'default' ) {
		return $this->order( $this->images, $order );
	}

	/**
	 * Cover Image
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $album
	 * @param  string $order
	 * @return mixed
	 */
	public function coverImage( $album, $order = 'default' ) {

		if ( $this->hasImages( $album ) ) {
			$images = $this->order( $this->albums['$album'], $order );
			reset( $images );
			return key( $images );
		}

		// Get images of sub albums if exists (only for version with sub albums).
		$subGallery = new Image_Album( $this->dir . '/' . $album );

		if ( $subGallery->hasAlbums() ) {
			$albums     = $subGallery->albums( $order );
			$firstAlbum = reset( $albums );
			$albumName  = key( $albums );
			reset( $firstAlbum );
			$image = key( $firstAlbum );
			return $albumName . '/' . $image;
		}
		return false;
	}

	/**
	 * Has albums
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function hasAlbums() {
		if ( empty( $this->albums ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Has images
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  boolean $album
	 * @return boolean
	 */
	public function hasImages( $album = false ) {

		// Choose correct image array.
		$imageList = &$this->images;
		if ( $album ) {
			$imageList = &$this->albums[$album];
		}

		// Check if empty.
		if ( empty( $imageList ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Parent album
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $album
	 * @return string
	 */
	public function parentAlbum( $album ) {
		$parent = strrpos( $album, '/' );
		return substr( $album, 0, $parent );
	}
}
