<?php
/**
 * User Profiles
 *
 * Plugin core class, do not namespace.
 *
 * @package    User Profiles
 * @subpackage Core
 * @since      1.0.0
 */

// Stop if accessed directly.
if ( ! defined( 'BLUDIT' ) ) {
	die( 'You are not allowed direct access to this file.' );
}

// Access namespaced functions.
use function UPRO_Func\{
	usernames,
	profile_fields,
	content_filter,
	default_cover,
	autop
};

class User_Profiles extends Plugin {

	/**
	 * Storage root
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string
	 */
	private $storage_root = 'user-profiles';

	/**
	 * Cache age
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    integer
	 */
	private $max_image_cache = 86400;

	/**
	 * Constructor method
	 *
	 * @since  1.0.0
	 * @access public
	 * @return self
	 */
	public function __construct() {

		// Run parent constructor.
		parent :: __construct();

		// Include functionality.
		if ( $this->installed() ) {
			$this->autoload();
			$this->get_files();
		}
	}

	/**
	 * Prepare plugin for installation
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function prepare() {
		$this->autoload();
		$this->get_files();
	}

	/**
	 * Autoload classes
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function autoload() {

		// Path to class files.
		$path = PATH_PLUGINS . 'user-profiles' . DS . 'includes/classes' . DS;

		// Array of namespaced classes & filenames.
		$classes = [
			'UPRO_Classes\Image_Upload'  => $path . 'class-image-upload.php',
			'UPRO_Classes\Avatar_Images' => $path . 'class-avatar-images.php',
			'UPRO_Classes\Avatar_Album'  => $path . 'class-avatar-album.php',
			'UPRO_Classes\Cover_Images'  => $path . 'class-cover-images.php',
			'UPRO_Classes\Cover_Album'   => $path . 'class-cover-album.php',
			'UPRO_Classes\Image_Album'   => $path . 'class-image-album.php'
		];
		spl_autoload_register(
			function ( string $class ) use ( $classes ) {
				if ( isset( $classes[$class] ) ) {
					require $classes[$class];
				}
			}
		);
	}

	/**
	 * Include functionality
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function get_files() {

		// Plugin path.
		$path = PATH_PLUGINS . 'user-profiles' . DS;

		// Get plugin functions.
		foreach ( glob( $path . 'includes/*.php' ) as $filename ) {
			require_once $filename;
		}
	}

	/**
	 * Initiate plugin
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object $L Language class.
	 * @return void
	 */
	public function init() {

		// Access global variables.
		global $L;

		$this->get_files();

		$dynamic_fields = profile_fields();
		$static_fields  = [
			'author_display'      => 'post',
			'author_tabbed'       => true,
			'author_location'     => 'after',
			'author_avatar'       => true,
			'author_role'         => true,
			'author_email'        => true,
			'author_site'         => true,
			'author_social'       => true,
			'author_posts'        => true,
			'author_limit'        => 5,
			'users_slug'          => 'users',
			'avatar_width'        => 320,
			'avatar_height'       => 320,
			'default_avatar'      => 'user',
			'custom_avatar'       => [],
			'cover_images'        => [],
			'default_cover'       => [],
			'cover_thumb_width'   => 320,
			'cover_thumb_height'  => 320,
			'cover_thumb_quality' => 90,
			'cover_large_width'   => 1920,
			'cover_large_height'  => 1080,
			'cover_large_quality' => 90,
			'profile_pages'       => true,
			'header_style'        => 'one',
			'bio_avatar_radius'   => 10,
			'bio_heading'         => '',
			'details_heading'     => $L->get( 'Details' ),
			'profile_role'        => true,
			'profile_email'       => true,
			'profile_site'        => true,
			'profile_social'      => true,
			'profile_posts'       => true,
			'profile_limit'       => 6,
			'sidebar_bio'         => false,
			'sb_bio_limit'        => 200,
			'widgets_label'       => 'h2',
			'sidebar_avatar'      => true,
			'sidebar_links'       => true,
			'sidebar_more'        => true,
			'more_widget_label'   => '',
			'sidebar_limit'       => 5,
			'sidebar_list'        => true,
			'sb_list_label'       => $L->get( 'Authors' ),
			'sb_list_avatar'      => true,
			'sb_list_sort'        => 'abc',
			'sb_widgets_sort'     => 'posts,list'
		];
		$fields = array_merge( $dynamic_fields, $static_fields );

		// Array of custom hooks.
		$this->customHooks = [
			'user_profile_before',
			'user_profile_content',
			'user_profile_after'
		];

		$this->dbFields = $fields;
		if ( ! $this->installed() ) {
			$Tmp = new dbJSON( $this->filenameDb );
			$this->db = $Tmp->db;
			$this->prepare();
		}
	}

	/**
	 * Install plugin
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  integer $position
	 * @return boolean Return true if the installation is successful.
	 */
	public function install( $position = 100 ) {

		// Create workspace.
		$workspace = $this->workspace();
		mkdir( $workspace, DIR_PERMISSIONS, true );

		// Create plugin directory for the database
		mkdir( PATH_PLUGINS_DATABASES . $this->directoryName, DIR_PERMISSIONS, true );

		$this->dbFields['position'] = $position;

		// Sanitize default values to store in the file.
		foreach ( $this->dbFields as $key => $value ) {

			if ( is_array( $value ) ) {
				$final_value = $value;
			} else {
				$value = Sanitize :: html( $value );
			}
			settype( $value, gettype( $this->dbFields[$key] ) );
			$this->db[$key] = $value;
		}

		$storage = PATH_CONTENT . $this->storage_root . DS;
		if ( ! file_exists( $storage ) ) {
			Filesystem :: mkdir( $storage, true );
		}
		file_put_contents( $storage . '.htaccess', 'Deny from all' );

		// Create the database.
		return $this->save();
	}

	/**
	 * Uninstall
	 *
	 * @since  1.0.0
	 * @access public
	 * @return boolean
	 */
	public function uninstall() {

		// Delete database.
		$path = PATH_PLUGINS_DATABASES . $this->directoryName;
		Filesystem :: deleteRecursive( $path );

		// Delete workspace.
		$workspace = $this->workspace();
		Filesystem :: deleteRecursive( $workspace );

		return true;
	}

	/**
	 * Form post
	 *
	 * The form `$_POST` method.
	 *
	 * Essentially the same as the parent method
	 * except that it allows for array field values.
	 *
	 * This was implemented to handle multi-checkbox
	 * and radio button fields. If strings are used
	 * in an array option then be sure to sanitize
	 * the string values.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function post() {

		$args = $_POST;

		foreach ( $this->dbFields as $field => $value ) {

			if ( isset( $args[$field] ) ) {

				// @todo Look into sanitizing array values.
				if ( is_array( $args[$field] ) ) {
					$final_value = $args[$field];
				} else {
					$final_value = Sanitize :: html( $args[$field] );
				}

				if ( $final_value === 'false' ) {
					$final_value = false;
				} elseif ( $final_value === 'true' ) {
					$final_value = true;
				}

				settype( $final_value, gettype( $value ) );
				$this->db[$field] = $final_value;
			}
		}

		if ( empty( $this->getValue( 'users_slug' ) ) ) {
			$this->setField( 'users_slug', 'users' );
		}
		return $this->save();
	}

	/**
	 * Admin settings form
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object $L Language class.
	 * @global object $site Site class.
	 * @return string Returns the markup of the form.
	 */
	public function form() {

		// Access global variables.
		global $L, $site;

		global $layout;
		$layout['title'] = $L->get( 'User Profiles Guide' ) . ' | ' . $site->title();

		$avatar = 'avatar';
		$cover  = 'cover';
		$config['imagesSort'] = 'newest';
		$avatars = new UPRO_Classes\Avatar_Album( $config, true );
		$covers  = new UPRO_Classes\Cover_Album( $config, true );

		$html  = '';
		// ob_start();
		include( $this->phpPath() . '/views/page-form.php' );
		// $html .= ob_get_clean();

		return $html;
	}

	/**
	 * Admin controller
	 *
	 * Change the text of the `<title>` tag.
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object $L The Language class.
	 * @global array $layout
	 * @return string Returns the head content.
	 */
	public function adminController() {
		global $L, $layout, $site;
		$layout['title'] = $L->get( 'User Profiles Guide' ) . ' | ' . $site->title();
	}

	/**
	 * Admin scripts & styles
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object $url Url class.
	 * @return string Returns the head content.
	 */
	public function adminHead() {

		// Access global variables.
		global $url;

		// Maybe get non-minified assets.
		$suffix = '.min';
		if ( defined( 'DEBUG_MODE' ) && DEBUG_MODE ) {
			$suffix = '';
		}
		$assets = '';

		// Load only for this plugin's pages.
		if ( str_contains( $url->slug(), $this->className() ) ) :

			$assets .= "\n";
			$assets .= '<script type="text/javascript" src="' . $this->domainPath() . "assets/js/dropzone{$suffix}.js?version=" . $this->getMetadata( 'version' ) . '"></script>' . PHP_EOL;

			$assets .= '<script type="text/javascript" src="' . $this->domainPath() . "assets/js/tabs{$suffix}.js?version=" . $this->getMetadata( 'version' ) . '"></script>' . PHP_EOL;

			$assets .= '<script type="text/javascript" src="' . $this->domainPath() . "assets/js/tooltips{$suffix}.js?version=" . $this->getMetadata( 'version' ) . '"></script>' . PHP_EOL;

			$assets .= '<script type="text/javascript" src="' . $this->domainPath() . "assets/js/lightbox{$suffix}.js?version=" . $this->getMetadata( 'version' ) . '"></script>' . PHP_EOL;

			$assets .= '<script type="text/javascript" src="' . $this->domainPath() . "assets/js/backend{$suffix}.js?version=" . $this->getMetadata( 'version' ) . '"></script>' . PHP_EOL;

			$assets .= '<link rel="stylesheet" type="text/css" href="' . $this->domainPath() . "assets/css/dropzone.min.css?version=" . $this->getMetadata( 'version' ) . '" />' . PHP_EOL;

			$assets .= '<link rel="stylesheet" type="text/css" href="' . $this->domainPath() . "assets/css/tooltips{$suffix}.css?version=" . $this->getMetadata( 'version' ) . '" />' . PHP_EOL;

			$assets .= '<link rel="stylesheet" type="text/css" href="' . $this->domainPath() . "assets/css/lightbox{$suffix}.css?version=" . $this->getMetadata( 'version' ) . '" />' . PHP_EOL;

			$assets .= '<link rel="stylesheet" type="text/css" href="' . $this->domainPath() . "assets/css/backend{$suffix}.css?version=" . $this->getMetadata( 'version' ) . '" />' . PHP_EOL;

			$assets .= '<style>';
			$assets .= ':root { --upro-profile--avatar--border-radius: ' . $this->bio_avatar_radius() . ' }';
			$assets .= '</style>';
		endif;

		return $assets;
	}

	/**
	 * Admin info pages
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object $L Language class.
	 * @global object $site Site class.
	 * @return string Returns the markup of the page.
	 */
	public function adminView() {

		// Access global variables.
		global $L, $site;

		$html  = '';
		ob_start();
		include( $this->phpPath() . '/views/page-guide.php' );
		$html .= ob_get_clean();

		return $html;
	}

	/**
	 * Sidebar link
	 *
	 * Link to the options screen in the admin sidebar menu.
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object $L Language class.
	 * @return mixed
	 */
	public function adminSidebar() {

		// Access global variables.
		global $L;

		// Check user role.
		if ( ! checkRole( [ 'admin' ], false ) ) {
			return;
		}

		$url  = HTML_PATH_ADMIN_ROOT . 'configure-plugin/' . $this->className();
		$html = sprintf(
			'<a class="nav-link" href="%s"><span class="fa fa-users"></span>%s</a>',
			$url,
			$L->get( 'User Profiles' )
		);
		return $html;
	}

	/**
	 * Admin body end
	 *
	 * Used for adding scripts.
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object $L Language class.
	 * @global object $site Site class.
	 * @global object $url Url class.
	 * @return string
	 */
	public function adminBodyEnd() {

		// Access global variables.
		global $L, $page, $site, $url;

		$html = '';

		// Settings page URL.
		$settings_page = DOMAIN_ADMIN . 'configure-plugin/' . $this->className() . '#options';

		if ( checkRole( [ 'admin' ], false ) && str_contains( $url->slug(), 'edit-user' ) ) {
			return sprintf(
				'<script>var uuid = $("#jsuuid").val(); $uuid = uuid; if ( $uuid != "" ) { $( "#jsform nav" ).prepend( "<div class=\'alert alert-primary alert-search-forms\' role=\'alert\'><p class=\'m-0\'><a href=\'%s\'>%s</a></p></div>"); }</script>',
				$settings_page,
				$L->get( 'Edit advanced user options' )
			);
		}

		if ( checkRole( [ 'admin' ], false ) && 'users' == $url->slug() ) {
			return sprintf(
				'<script>var uuid = $("#jsuuid").val(); $uuid = uuid; if ( $uuid != "" ) { $( "h1 + a" ).append( "<div class=\'alert alert-primary alert-search-forms\' role=\'alert\'><p class=\'m-0\'><a href=\'%s\'>%s</a></p></div>"); }</script>',
				$settings_page,
				$L->get( 'Edit advanced user options' )
			);
		}

		// Load only for this plugin's settings page.
		if ( str_contains( $url->slug(), $this->className() ) ) :

		// AJAX paths for uploads.
		$upload_path  = HTML_PATH_ADMIN_ROOT . 'user-profiles';
		$current_path = strtok( $_SERVER['REQUEST_URI'], '?' );

		if ( $current_path == $upload_path ) {

			 // Fetch content.
			$content = ob_get_contents();
			ob_end_clean();

			$avatar = 'avatar';
			$cover  = 'cover';
			$domain = $this->domainPath();

			// Get helper objects.
			require_once( 'includes/classes/class-avatar-images-helper.php' );
			require_once( 'includes/classes/class-cover-images-helper.php' );
			$avatar_helper = new \UPRO_Classes\Avatar_Images_Helper();
			$cover_helper  = new \UPRO_Classes\Cover_Images_Helper();

			// Load required JS.
			$html .= '<script type="text/javascript" src="' . $this->domainPath() . "assets/js/jquery-confirm.min.js?version=" . $this->getMetadata( 'version' ) . '"></script>' . PHP_EOL;
			$html .= $avatar_helper->adminJSData( $domain );
			$html .= $cover_helper->adminJSData( $domain );

			if ( $avatar ) {
				$html .= $avatar_helper->dropzoneJSData( $avatar );
			}
			if ( $cover ) {
				$html .= $cover_helper->dropzoneJSData( $cover );
			}

			// Remove old admin content (error message)
			$regexp  = "#(\<div class=\"col-lg-10 pt-3 pb-1 h-100\"\>)(.*?)(\<\/div\>)#s";
			$content = preg_replace( $regexp, "$1{$html}$3", $content );
			echo $content;

			return;
		}

		$avatar = 'avatar';
		$cover  = 'cover';
		$domain = $this->domainPath();

		// Get helper objects.
		require_once( 'includes/classes/class-avatar-images-helper.php' );
		require_once( 'includes/classes/class-cover-images-helper.php' );
		$avatar_helper = new \UPRO_Classes\Avatar_Images_Helper();
		$cover_helper  = new \UPRO_Classes\Cover_Images_Helper();

		// Load required JS
		$html .= '<script type="text/javascript" src="' . $this->domainPath() . "assets/js/jquery-confirm.min.js?version=" . $this->getMetadata( 'version' ) . '"></script>' . PHP_EOL;
		$html .= $avatar_helper->adminJSData( $domain );
		$html .= $cover_helper->adminJSData( $domain );

		if ( $avatar ) {
			$html .= $avatar_helper->dropzoneJSData( $avatar );
		}
		if ( $cover ) {
			$html .= $cover_helper->dropzoneJSData( $cover );
		}
		endif;

		return $html;
	}

	/**
	 * Before all hook
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object $url Url class.
	 * @return void
	 */
	public function beforeAll() {

		// Access global variables.
		global $url;

		// Check if the URL matches the webhook.
		$webhook = $this->users_slug();
		if ( $this->webhook( $webhook, true, false ) ) {
			$url->setWhereAmI( $webhook );
		}
	}

	/**
	 * Before site load
	 *
	 * Runs on the front end before content is printed.
	 * Content is or is not modified according to plugin
	 * options and page type.
	 *
	 * @since  1.0.0
	 * @access public
	 * @global array $content Available content.
	 * @return void
	 */
	public function beforeSiteLoad() {

		// Access global variables.
		global $content;

		$webhook = $this->users_slug();
		if ( $this->webhook( $webhook, true, false ) ) {

			$content = [];
			$list    = usernames();

			foreach ( $list as $name ) {
				array_push( $content, $name );
			}
		}

		// Filter page content to add profile.
		content_filter();
	}

	/**
	 * Head section
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object $url Url class.
	 * @return string Returns the head content.
	 */
	public function siteHead() {

		// Access global variables.
		global $url;

		// Maybe get non-minified assets.
		$suffix = '.min';
		if ( defined( 'DEBUG_MODE' ) && DEBUG_MODE ) {
			$suffix = '';
		}
		$assets = '';

		$assets .= '<script type="text/javascript" src="' . $this->domainPath() . "assets/js/frontend{$suffix}.js?version=" . $this->getMetadata( 'version' ) . '"></script>' . PHP_EOL;

		$assets .= '<script type="text/javascript" src="' . $this->domainPath() . "assets/js/tabs{$suffix}.js?version=" . $this->getMetadata( 'version' ) . '"></script>' . PHP_EOL;

		$assets .= '<link rel="stylesheet" type="text/css" href="' . $this->domainPath() . "assets/css/frontend{$suffix}.css?version=" . $this->getMetadata( 'version' ) . '" />' . PHP_EOL;

		$assets .= '<style>';
		$assets .= ':root { --upro-profile--avatar--border-radius: ' . $this->bio_avatar_radius() . ' }';
		$assets .= '</style>';

		return $assets;
	}

	/**
	 * Sidebar content
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object $L Language class.
	 * @global object $site Site class.
	 * @global object $url Url class.
	 * @return string Returns the sidebar markup.
	 */
	public function siteSidebar() {

		// Access global variables.
		global $L, $site, $url;

		$users_slug = $this->users_slug();

		$html  = '';
		ob_start();
		include( $this->phpPath() . '/views/site-sidebar.php' );
		$html .= ob_get_clean();

		return $html;
	}

	/**
	 * User page content
	 *
	 * Content for the singular user page.
	 * Requires that the theme employ the
	 * `user_profile_content` hook.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return mixed Returns the content markup or false.
	 */
	public function user_profile_content() {

		$html  = '';
		ob_start();
		include( $this->phpPath() . '/views/profile.php' );
		$html .= ob_get_clean();

		return $html;
	}

	/**
	 * Option return functions
	 *
	 * @since  1.0.0
	 * @access public
	 */

	// @return string
	public function author_display() {
		return $this->getValue( 'author_display' );
	}

	// @return string
	public function author_location() {
		return $this->getValue( 'author_location' );
	}

	// @return boolean
	public function author_tabbed() {
		return $this->getValue( 'author_tabbed' );
	}

	// @return boolean
	public function author_avatar() {
		return $this->getValue( 'author_avatar' );
	}

	// @return boolean
	public function author_role() {
		return $this->getValue( 'author_role' );
	}

	// @return boolean
	public function author_email() {
		return $this->getValue( 'author_email' );
	}

	// @return boolean
	public function author_site() {
		return $this->getValue( 'author_site' );
	}

	// @return boolean
	public function author_social() {
		return $this->getValue( 'author_social' );
	}

	// @return boolean
	public function author_posts() {
		return $this->getValue( 'author_posts' );
	}

	// @return integer
	public function author_limit() {
		return $this->getValue( 'author_limit' );
	}

	// @return string
	public function users_slug() {

		$field   = $this->getValue( 'users_slug' );
		$decode  = htmlspecialchars_decode( $field );
		$strip   = strip_tags( $decode );
		$replace = str_replace( [ '_', '.', '/', ' ' ], '-', $strip );
		$slug    = mb_strtolower( $replace, CHARSET );

		return $slug;
	}

	// @return integer
	public function avatar_width() {
		return $this->getValue( 'avatar_width' );
	}

	// @return integer
	public function avatar_height() {
		return $this->getValue( 'avatar_height' );
	}

	// @return string
	public function default_avatar() {
		return $this->getValue( 'default_avatar' );
	}

	// @return array
	public function custom_avatar() {
		return $this->getValue( 'custom_avatar' );
	}

	// @return array
	public function cover_images() {
		return $this->getValue( 'cover_images' );
	}

	// @return array
	public function default_cover() {
		return $this->getValue( 'default_cover' );
	}

	// @return boolean
	public function profile_pages() {
		return $this->getValue( 'profile_pages' );
	}

	// @return string
	public function header_style() {
		return $this->getValue( 'header_style' );
	}

	// @return integer
	public function bio_avatar_radius() {
		return $this->getValue( 'bio_avatar_radius' ) . '%';
	}

	// @return string
	public function bio_heading() {
		return $this->getValue( 'bio_heading' );
	}

	// @return string
	public function details_heading() {
		return $this->getValue( 'details_heading' );
	}

	// @return boolean
	public function profile_role() {
		return $this->getValue( 'profile_role' );
	}

	// @return boolean
	public function profile_email() {
		return $this->getValue( 'profile_email' );
	}

	// @return boolean
	public function profile_site() {
		return $this->getValue( 'profile_site' );
	}

	// @return boolean
	public function profile_social() {
		return $this->getValue( 'profile_social' );
	}

	// @return boolean
	public function profile_posts() {
		return $this->getValue( 'profile_posts' );
	}

	// @return integer
	public function profile_limit() {
		return $this->getValue( 'profile_limit' );
	}

	// @return boolean
	public function sidebar_bio() {
		return $this->getValue( 'sidebar_bio' );
	}

	// @return integer
	public function sb_bio_limit() {
		return $this->getValue( 'sb_bio_limit' );
	}

	// @return string
	public function widgets_label() {
		return $this->getValue( 'widgets_label' );
	}

	// @return boolean
	public function sidebar_avatar() {
		return $this->getValue( 'sidebar_avatar' );
	}

	// @return boolean
	public function sidebar_links() {
		return $this->getValue( 'sidebar_links' );
	}

	// @return boolean
	public function sidebar_more() {
		return $this->getValue( 'sidebar_more' );
	}

	// @return string
	public function more_widget_label() {
		return $this->getValue( 'more_widget_label' );
	}

	// @return integer
	public function sidebar_limit() {
		return $this->getValue( 'sidebar_limit' );
	}

	// @return boolean
	public function sidebar_list() {
		return $this->getValue( 'sidebar_list' );
	}

	// @return string
	public function sb_list_label() {
		if ( ! empty( $this->getValue( 'sb_list_label' ) ) ) {
			return ucwords( $this->getValue( 'sb_list_label' ) );
		} else {
			return $this->dbFields['sb_list_label'];
		}
	}

	// @return boolean
	public function sb_list_avatar() {
		return $this->getValue( 'sb_list_avatar' );
	}

	// @return string
	public function sb_list_sort() {
		return $this->getValue( 'sb_list_sort' );
	}

	// @return array
	public function sb_widgets_sort() {
		return $this->getValue( 'sb_widgets_sort' );
	}

	/**
	 * User cover image
	 *
	 * Gets the URL for a user cover image.
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object $site Site class.
	 * @global object $url The Url class.
	 * @return mixed Returns the URL or false.
	 */
	public function cover_src( $name = '', $cropped = true ) {

		// Access global variables.
		global $site, $url;

		// Get cover field value.
		$cover = $this->getValue( 'cover_' . $name );

		if ( ! $cover || ! is_array( $cover ) ) {
			return default_cover();
		} elseif ( ! isset( $cover[0] ) ) {
			return default_cover();
		}

		if ( $cropped ) {
			$album = PATH_CONTENT . $this->storage_root . DS . 'cover' . DS . 'cache' . DS . 'large' . DS . $cover[0];
			$src   = DOMAIN_CONTENT . $this->storage_root . '/cover/cache/large/' . $cover[0];
		} else {
			$album = PATH_CONTENT . $this->storage_root . DS . 'cover' . DS . $cover[0];
			$src   = DOMAIN_CONTENT . $this->storage_root . '/cover/' . $cover[0];
		}

		if ( file_exists( $album ) ) {
			return $src;
		}
		return false;
	}
}
