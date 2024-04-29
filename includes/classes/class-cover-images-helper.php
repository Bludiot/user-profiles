<?php
/**
 * Cover images helper
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

class Cover_Images_Helper {

	/**
	 * Undocumented function
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $domainPath
	 * @global object $L The Language class.
	 * @return string
	 */
	public function adminJSData( $domainPath ) {

		// Access global variables.
		global $L;

		return '
		<script>
			var cover = {
			config : {
				ajaxUrl : "' . $domainPath . 'ajax/cover-request-handler.php"
			},
			L: {
				deleteImageError : "' . $L->get( 'Error: Image could not be deleted.' ) . '"
			}
			};
		</script>
		';
	}

	/**
	 * Dropzone options
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $album
	 * @global object $L The Language class.
	 * @return string Returns a script tag.
	 */
	public function dropzoneJSData( $album ) {

		global $security, $L;

		$upload  = "<strong>{$L->get( 'Upload cover images here' )}</strong>";
		$preview = "<div class='dz-preview dz-file-preview'> <div class='dz-image'><img data-dz-thumbnail/></div> <div class='dz-details'> </div> <div class='dz-progress'> <span class='dz-upload' data-dz-uploadprogress></span> </div> <div class='dz-error-message'><span data-dz-errormessage></span></div> <div class='dz-success-mark'><svg width='32' height='32' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><title>{$L->get( 'Check' )}</title><path d='M173.898 439.404l-166.4-166.4c-9.997-9.997-9.997-26.206 0-36.204l36.203-36.204c9.997-9.998 26.207-9.998 36.204 0L192 312.69 432.095 72.596c9.997-9.997 26.207-9.997 36.204 0l36.203 36.204c9.997 9.997 9.997 26.206 0 36.204l-294.4 294.401c-9.998 9.997-26.207 9.997-36.204-.001z' fill-opacity='0.8' fill='#ffffff' /></svg> </div> <div class='dz-error-mark'> <svg width='32' height='32' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 320 512'><title>{$L->get( 'Error' )}</title><path d='M193.94 256L296.5 153.44l21.15-21.15c3.12-3.12 3.12-8.19 0-11.31l-22.63-22.63c-3.12-3.12-8.19-3.12-11.31 0L160 222.06 36.29 98.34c-3.12-3.12-8.19-3.12-11.31 0L2.34 120.97c-3.12 3.12-3.12 8.19 0 11.31L126.06 256 2.34 379.71c-3.12 3.12-3.12 8.19 0 11.31l22.63 22.63c3.12 3.12 8.19 3.12 11.31 0L160 289.94 262.56 392.5l21.15 21.15c3.12 3.12 8.19 3.12 11.31 0l22.63-22.63c3.12-3.12 3.12-8.19 0-11.31L193.94 256z'' fill-opacity='0.8' fill='#ffffff' /></svg></div></div>";

		return '
		<script>
		Dropzone.options.coverUpload = {
			url : cover.config.ajaxUrl,
			params : {
				tokenCSRF : "' . $security->getTokenCSRF() . '",
				action : "uploadImage",
				album : "' . $album . '"
			},
			addRemoveLinks : false,
			acceptedFiles : ".jpg,.jpeg,.png",
			dictDefaultMessage : "' . $upload . '",
			dictFileTooBig : "' . $L->get( 'File is too big. Max size:' ) . ' {{maxFilesize}} MiB",
			dictInvalidFileType : "' . $L->get( 'File format not accepted' ) . '",
			dictResponseError : "{{statusCode}} ' . $L->get( 'Server error during upload.' ) . '",
			dictCancelUpload : "' . $L->get( 'Cancel' ) . '",
			dictUploadCanceled : "' . $L->get( 'Canceled' ) . '",
			dictCancelUploadConfirmation : "' . $L->get( 'Cancel?' ) . '",
			dictRemoveFile : "' . $L->get( 'Remove' ) . '",
			previewTemplate : "' . $preview . '",
			init : function() {
				this.on( "queuecomplete", function() {
					$( "#cover-upload-notice" ).fadeIn( 250 );
					$( "#cover-images-count" ).load( window.location.href + " #cover-images-count > span" );
					$( "#cover-album" ).load( window.location.href + " #cover-album > div" );
					$( "#cover-album-empty" ).css( "display", "flex" );
				});
				this.on( "addedfile", function(file) {});
			}
		};
		</script>
		';
	}
}
