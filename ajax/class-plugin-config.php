<?php
/**
 * Config helper for the plugin
 *
 * @package    Configure 8 Options
 * @subpackage AJAX
 * @since      1.0.0
 */

namespace UPRO_AJAX;

class Plugin_Config {

	/**
	 * Filename
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string
	 */
	private $file;

	/**
	 * Configuration
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    array
	 */
	private $config = [];

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	function __construct( $file, $firstLine = false ) {

		$this->file = $file;
		if ( file_exists( $file ) ) {

			// Read JSON file.
			$lines = file( $file );

			// Remove the first line, the first line is for security reasons.
			if ( $firstLine ) {
				unset( $lines[0] );
			}

			// Regenerate the JSON file.
			$json = implode( $lines );

			// Unserialize, JSON to array
			$this->config = json_decode( $json, true );
		}
	}

	/**
	 * Get field
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $key
	 * @return mixed
	 */
	public function getField( $key ) {

		if ( isset( $this->config[$key] ) ) {
			return $this->config[$key];
		}
		return null;
	}

	/**
	 * Set field
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $key
	 * @param  mixed $value
	 * @return void
	 */
	public function setField( $key, $value ) {
		$this->config[$key] = $value;
	}

	/**
	 * Delete field
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $key
	 * @return void
	 */
	public function deleteField( $key ) {
		unset( $this->config[$key] );
	}

	/**
	 * Save form
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function save() {

		$data = json_encode( $this->config );

		// LOCK_EX flag to prevent anyone else writing to the file at the same time.
		if ( file_put_contents( $this->file, $data, LOCK_EX ) ) {
			return true;
		} else {
			Log :: set( __METHOD__ . LOG_SEP . 'Error occurred when trying to save the database file.', LOG_TYPE_ERROR );
			return false;
		}
	}
}
