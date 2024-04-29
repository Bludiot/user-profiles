<?php
/**
 * AJAX helper for the plugin
 *
 * @package    Configure 8 Options
 * @subpackage AJAX
 * @since      1.0.0
 */

class AJAX {

	/**
	 * Set header
	 *
	 * @since  1.0.0
	 * @access public
	 * @static
	 * @return void
	 */
	public static function set_header() {
		header( 'Content-Type: application/json' );
	}

	/**
	 * Authorize request
	 *
	 * @since  1.0.0
	 * @access public
	 * @static
	 * @return void
	 */
	public static function auth() {
		self :: check_session();
		self :: check_role();
		self :: check_CSRF();
	}

	/**
	 * Check session
	 *
	 * @since  1.0.0
	 * @access private
	 * @static
	 * @return void
	 */
	private static function check_session() {
		session_name( 'BLUDIT-KEY' );
		session_start();
		if ( ! isset( $_SESSION['s_role'] ) ) {
			self :: exit( 401 );
		}
	}

	/**
	 * Check user role
	 *
	 * @since  1.0.0
	 * @access private
	 * @static
	 * @return void
	 */
	private static function check_role() {
		$role = $_SESSION['s_role'];
		if ( $role !== 'admin' && $role != 'author' && $role != 'editor' ) {
			self :: exit( 401 );
		}
	}

	/**
	 * Check security token
	 *
	 * @since  1.0.0
	 * @access public
	 * @static
	 * @return void
	 */
	public static function check_CSRF() {

		if ( ! isset( $_SESSION['s_tokenCSRF'] ) || ! isset( $_POST['tokenCSRF'] ) ) {
			self :: exit( 401 );
		}
		if ( $_SESSION['s_tokenCSRF'] != $_POST['tokenCSRF'] ) {
			self :: exit( 401 );
		}
	}

	/**
	 * Exit request
	 *
	 * @since  1.0.0
	 * @access public
	 * @static
	 * @return void
	 */
	public static function exit( $statusCode = 403, $message = false ) {

		switch ( $statusCode ) {
			case 200:
				$header = 'HTTP/1.1 200 Found';
				$defaultMessage = 'Success';
				break;
			case 400:
				$header = 'HTTP/1.1 400 Bad Request';
				$defaultMessage = '400 Bad Request';
				break;
			case 401:
				$header = 'HTTP/1.1 401 Unauthorized';
				$defaultMessage = '401 Unauthorized';
				break;
			case 404:
				$header = 'HTTP/1.1 404 Not Found';
				$defaultMessage = '404 Not Found';
				break;
			case 500:
				$header = 'HTTP/1.1 500 Internal Server Error';
				$defaultMessage = 'Internal Server Error';
				break;
			default:
				$header = 'HTTP/1.1 403 Forbidden';
				$defaultMessage = '403 Forbidden';
				break;
		}

		if ( ! $message ) {
			$message = $defaultMessage;
		}

		header( $header );
		echo json_encode( $message );

		exit;
	}
}
