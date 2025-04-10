<?php
/**
 * Functions
 *
 * @package    User Profiles
 * @subpackage Core
 * @category   Functions
 * @since      1.0.0
 */

namespace UPRO_Func;

// Access namespaced functions.
use function UPRO_Tags\{
	user_link,
	user_display_name,
	user_avatar,
	author_box,
	users_list,
	get_social
};

// Stop if accessed directly.
if ( ! defined( 'BLUDIT' ) ) {
	die( 'You are not allowed direct access to this file.' );
}

/**
 * Plugin object
 *
 * Gets this plugin's core class.
 *
 * @since  1.0.0
 * @return object Returns the class object.
 */
function plugin() {
	return new \User_Profiles();
}

/**
 * Site class object
 *
 * Function to use inside other functions and
 * methods rather than calling the global.
 *
 * @since  1.0.0
 * @global object $site Site class
 * @return object
 */
function site() {
	global $site;
	return $site;
}

/**
 * Url class object
 *
 * Function to use inside other functions and
 * methods rather than calling the global.
 *
 * @since  1.0.0
 * @global object $url Url class
 * @return object
 */
function url() {
	global $url;
	return $url;
}

/**
 * Language class object
 *
 * Function to use inside other functions and
 * methods rather than calling the global.
 *
 * @since  1.0.0
 * @global object $L Language class
 * @return object
 */
function lang() {
	global $L;
	return $L;
}

/**
 * Page class object
 *
 * Function to use inside other functions and
 * methods rather than calling the global.
 *
 * @since  1.0.0
 * @global object $page Page class
 * @return object
 */
function page() {
	global $page;
	return $page;
}

/**
 * Usernames
 *
 * Gets the usernames of all registered users.
 *
 * @since  1.0.0
 * @global object $users The Users class.
 * @return array Returns an array of usernames.
 */
function usernames() {

	// Access global variables.
	global $users;

	$users_list = $users->keys();
	$usernames  = [];

	foreach ( $users_list as $username ) {
		$get_user    = new \User( $username );
		$usernames[] = $get_user->username();
	}
	return $usernames;
}

/**
 * User slug
 *
 * Get the user slug from the URL.
 *
 * @since  1.0.0
 * @return string
 */
function user_slug() {

	$slug = url()->explodeSlug();
	if ( isset( $slug[1] ) ) {
		return $slug[1];
	} else {
		return $slug;
	}
}

/**
 * User
 *
 * Returns a user object by username.
 *
 * @since  1.0.0
 * @param  string $name The username.
 * @return object User class.
 */
function user( $name = '' ) {
	return new \User( $name );
}

/**
 * Profile fields
 *
 * Generate an associative array of user
 * bio field keys and empty values for
 * plugin installation/initialization.
 *
 * @since  1.0.0
 * @return array Returns an array of fields.
 */
function profile_fields() {

	$usernames = usernames();
	$fields    = [];

	foreach ( $usernames as $name ) {

		$cover   = 'cover_'   . $name;
		$email   = 'email_'   . $name;
		$website = 'website_' . $name;
		$bio     = 'bio_'     . $name;

		$fields[$cover]   = [];
		$fields[$email]   = true;
		$fields[$website] = '';
		$fields[$bio]     = '';
	}
	return $fields;
}

/**
 * User posts
 *
 * @since  1.0.0
 * @param  string $name Username
 * @param  integer $limit Maximum pages.
 * @global object $pages The Pages class.
 * @return mixed Returns an array of page keys or false.
 */
function user_posts( $name = '', $limit = 5 ) {

	// Access global variables.
	global $pages;

	$exclude = [
		'scheduled',
		'draft',
		'autosave'
	];

	if ( 'post' == plugin()->author_display() ) {
		$exclude = array_merge( [ 'static' ], $exclude );
	}
	if ( 'page' == plugin()->author_display() ) {
		$exclude = array_merge( [ 'published' ], $exclude );
	}

	$posts = [];

	$get_pages = $pages->getDB();

	if ( ! $get_pages[0] ) {
		return false;
	}

	$count = 0;
	foreach ( $get_pages as $key ) {

		if ( $count == $limit ) {
			break;
		}

		$page = buildPage( $key );
		$type = $page->type();

		if ( in_array( $type, $exclude ) ) {
			continue;
		}

		$user = user( $name );
		if ( $user->username() != $page->username() ) {
			continue;
		}

		if ( 'page' == url()->whereAmI() ) {
			if ( page()->key() === $page->key() ) {
				continue;
			}
		}
		$posts[] = $page->key();
		$count++;
	}
	return $posts;
}

/**
 * Author display
 *
 * Whether to display the author box in posts.
 *
 * @since  1.0.0
 * @return boolean
 */
function author_display() {

	$author = false;

	if ( 'page' != url()->whereAmI() || 'search' == url()->whereAmI() ) {
		return $author;
	}

	$location = plugin()->author_display();

	if ( page()->draft() || page()->autosave() ) {
		$author = false;
	} elseif ( page()->isStatic() && 'page' == $location ) {
		$author = true;
	} elseif ( ! page()->isStatic() && 'post' == $location ) {
		$author = true;
	} elseif ( 'both' == $location ) {
		$author = true;
	}
	return $author;
}

/**
 * Default cover
 *
 * The URL for the default, fallback cover.
 *
 * @since  1.0.0
 * @param  boolean $cropped
 * @return mixed
 */
function default_cover( $cropped = true ) {

	$default  = plugin()->default_cover();
	$fallback = plugin()->phpPath() . '/assets/images/transparent.png';
	$src      = '';

	if ( file_exists( $fallback ) && ( ! $default || ! is_array( $default ) ) ) {
		return plugin()->domainPath() . '/assets/images/transparent.png';
	} elseif ( file_exists( $fallback ) && ! isset( $default[0] ) ) {
		return plugin()->domainPath() . '/assets/images/transparent.png';
	}

	if ( $cropped ) {
		$album = PATH_CONTENT . 'user-profiles' . DS . 'cover' . DS . 'cache' . DS . 'large' . DS . $default[0];
		$src   = DOMAIN_CONTENT . 'user-profiles' . '/cover/cache/large/' . $default[0];
	} else {
		$album = PATH_CONTENT . 'user-profiles' . DS . 'cover' . DS . $default[0];
		$src   = DOMAIN_CONTENT . 'user-profiles' . '/cover/' . $default[0];
	}

	if ( file_exists( $album ) ) {
		return $src;
	}
	return false;
}

/**
 * Default social networks
 *
 * Returns an array of the social networks
 * built into the CMS user form.
 *
 * @since  1.0.0
 * @return array
 */
function default_socials() {

	$socials = [
		'twitter',
		'facebook',
		'codepen',
		'instagram',
		'github',
		'gitlab',
		'linkedin',
		'xing',
		'mastodon',
		'vk'
	];
	return $socials;
}

/**
 * User has socials
 *
 * Returns true if the user has at least
 * one social networks field filled.
 *
 * @since  1.0.0
 * @param  string $name Username to check for socials.
 * @return boolean
 */
function has_social( $name = '' ) {

	if ( empty( $name ) ) {
		return false;
	}

	$socials    = default_socials();
	$has_social = false;

	foreach ( $socials as $site ) {
		if ( get_social( $name, $site ) ) {
			$has_social = true;
			break;
		}
	}
	return $has_social;
}

/**
 * User socials
 *
 * Returns the names and URLs of social networks.
 *
 * @since  1.0.0
 * @param  string $name Username to check for socials.
 * @return mixed
 */
function user_socials( $name = '' ) {

	// Return false if no socials.
	if ( ! has_social( $name ) ) {
		return false;
	}

	$socials = [
		'twitter'   => [
			'url'  => get_social( $name, 'twitter' ),
			'name' => 'X/Twitter'
		],
		'facebook'  => [
			'url'  => get_social( $name, 'facebook' ),
			'name' => 'Facebook'
		],
		'codepen'   => [
			'url'  => get_social( $name, 'codepen' ),
			'name' => 'CodePen'
		],
		'instagram' => [
			'url'  => get_social( $name, 'instagram' ),
			'name' => 'Instagram'
		],
		'github'    => [
			'url'  => get_social( $name, 'github' ),
			'name' => 'GitHub'
		],
		'gitlab'    => [
			'url'  => get_social( $name, 'gitlab' ),
			'name' => 'GitLab'
		],
		'linkedin'  => [
			'url'  => get_social( $name, 'linkedin' ),
			'name' => 'LinkedIn'
		],
		'xing'      => [
			'url'  => get_social( $name, 'xing' ),
			'name' => 'Xing'
		],
		'mastodon'  => [
			'url'  => get_social( $name, 'mastodon' ),
			'name' => 'Mastodon'
		],
		'vk'        => [
			'url'  => get_social( $name, 'vk' ),
			'name' => 'VK'
		]
	];
	return $socials;
}

/**
 * Content filter
 *
 * @since  1.0.0
 * @global array $content
 * @return void
 */
function content_filter() {

	// Access global variables.
	global $content;

	if ( 'page' != url()->whereAmI() ) {
		return;
	}

	foreach ( $content as $key => $page ) {

		if ( ! author_display() ) {
			continue;
		}

		$page_content = $page->contentRaw();
		$author_box   = author_box();

		$box_content  = $page_content . $author_box;
		if ( 'before' == plugin()->author_location() ) {
			$box_content  = $author_box . $page_content;
		}

		$page->setField( 'content', $box_content );
	}
}

/**
 * Sidebar profile display
 *
 * Whether to display the profile widget
 * on an individual page.
 *
 * @since  1.0.0
 * @return boolean
 */
function sidebar_profile_display() {

	if ( 'page' != url()->whereAmI() ) {
		return false;
	}

	if ( 'post' == plugin()->sb_bio_display() ) {
		if ( page()->isStatic() ) {
			return false;
		}
		return true;

	} elseif ( 'page' == plugin()->sb_bio_display() ) {
		if ( ! page()->isStatic() ) {
			return false;
		}
		return true;
	}
	return true;
}

/**
 * Sidebar list
 *
 * @since  1.0.0
 * @return string Returns the list markup.
 */
function sidebar_users_list()  {

	// Override default function arguments.
	$args = [
		'wrap'       => true,
		'wrap_class' => 'list-wrap users-list-wrap plugin plugin-users-list'
	];

	$label = plugin()->sb_list_label();
	if ( ! empty( $label ) && ! ctype_space( $label ) ) {
		$args['label'] = $label;
	}

	$label_el = plugin()->widgets_label();
	if ( ! $label_el ) {
		$args = array_merge( $args, [ 'label_el' => false ] );
	} elseif ( $label_el ) {
		$args = array_merge( $args, [ 'label_el' => $label_el ] );
	}

	if ( ! plugin()->sb_list_avatar() ) {
		$args['avatars'] = false;
	}

	// Return a modified list.
	return users_list( $args );
}

/**
 * Active widgets
 *
 * @since  1.0.0
 * @return array
 */
function active_widgets() {

	$widgets = [];

	if ( plugin()->sidebar_bio() ) {
		$profile = [
			'profile' => lang()->get( 'Author Profile' )
		];
		$widgets = array_merge( $widgets, $profile );
	}

	if ( plugin()->sidebar_more() ) {
		$posts = [
			'posts' => lang()->get( 'More Posts' )
		];
		$widgets = array_merge( $widgets, $posts );
	}

	if ( true === plugin()->sidebar_list() ) {
		$list = [
			'list' => lang()->get( 'Users List' )
		];
		$widgets = array_merge( $widgets, $list );
	}

	return $widgets;
}

/**
 * Users list minimum role
 *
 * @since  1.0.0
 * @return array
 */
function users_list_min_role() {

	$min  = plugin()->sb_list_role();
	$role = [ 'admin', 'editor', 'author' ];

	if ( 'editor' == $min ) {
		$role = [ 'admin', 'editor' ];
	}
	if ( 'admin' == $min ) {
		$role = [ 'admin' ];
	}
	return $role;
}

/**
 * Count active widgets
 *
 * @since  1.0.0
 * @return integer
 */
function count_widgets() {
	return count( active_widgets() );
}

/**
 * Get SVG icon
 *
 * @since  1.0.0
 * @param  string $$file Name of the SVG file.
 * @return array
 */
function icon( $filename = '', $wrap = false, $class = '' ) {

	$exists = file_exists( sprintf(
		plugin()->phpPath() . 'assets/images/svg-icons/%s.svg',
		$filename
	) );
	if ( ! empty( $filename ) && $exists ) {

		if ( true == $wrap ) {
			return sprintf(
				'<span class="social-icon theme-icon %s">%s</span>',
				$class,
				file_get_contents( plugin()->phpPath() . "assets/images/svg-icons/{$filename}.svg" )
			);
		} else {
			return file_get_contents( plugin()->phpPath() . "assets/images/svg-icons/{$filename}.svg" );
		}
	}
	return '';
}

/**
 * Auto paragraph
 *
 * Replaces double line breaks with paragraph elements.
 *
 * Modified from WordPress' `wpautop` function.
 *
 * A group of regex replaces used to identify text formatted with newlines and
 * replace double line breaks with HTML paragraph tags. The remaining line breaks
 * after conversion become `<br />` tags, unless `$br` is set to '0' or 'false'.
 *
 * @since  1.0.0
 * @param  string $text The text which has to be formatted.
 * @param  bool   $br   Optional. If set, this will convert all remaining line breaks
 *                      after paragraphing.
 * @return string Text which has been converted into correct paragraph tags.
 */
function autop( $text, $br = true ) {

	$pre_tags = [];

	if ( trim( $text ) === '' ) {
		return '';
	}

	// Just to make things a little easier, pad the end.
	$text = $text . "\n";

	/*
	 * Pre tags shouldn't be touched by autop.
	 * Replace pre tags with placeholders and bring them back after autop.
	 */
	if ( str_contains( $text, '<pre' ) ) {
		$text_parts = explode( '</pre>', $text );
		$last_part  = array_pop( $text_parts );
		$text       = '';
		$i          = 0;

		foreach ( $text_parts as $text_part ) {
			$start = strpos( $text_part, '<pre' );

			// Malformed HTML?
			if ( false === $start ) {
				$text .= $text_part;
				continue;
			}

			$name = "<pre pre-tag-$i></pre>";
			$pre_tags[ $name ] = substr( $text_part, $start ) . '</pre>';

			$text .= substr( $text_part, 0, $start ) . $name;
			++$i;
		}
		$text .= $last_part;
	}

	// Change multiple <br>'s into two line breaks, which will turn into paragraphs.
	$text = preg_replace( '|<br\s*/?>\s*<br\s*/?>|', "\n\n", $text );

	$allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|form|map|area|blockquote|address|style|p|h[1-6]|hr|fieldset|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary)';

	// Add a double line break above block-level opening tags.
	$text = preg_replace( '!(<' . $allblocks . '[\s/>])!', "\n\n$1", $text );

	// Add a double line break below block-level closing tags.
	$text = preg_replace( '!(</' . $allblocks . '>)!', "$1\n\n", $text );

	// Add a double line break after hr tags, which are self closing.
	$text = preg_replace( '!(<hr\s*?/?>)!', "$1\n\n", $text );

	// Standardize newline characters to "\n".
	$text = str_replace( [ "\r\n", "\r" ], "\n", $text );

	// Collapse line breaks before and after <option> elements so they don't get autop'd.
	if ( str_contains( $text, '<option' ) ) {
		$text = preg_replace( '|\s*<option|', '<option', $text );
		$text = preg_replace( '|</option>\s*|', '</option>', $text );
	}

	/*
	 * Collapse line breaks inside <object> elements, before <param> and <embed> elements
	 * so they don't get autop'd.
	 */
	if ( str_contains( $text, '</object>' ) ) {
		$text = preg_replace( '|(<object[^>]*>)\s*|', '$1', $text );
		$text = preg_replace( '|\s*</object>|', '</object>', $text );
		$text = preg_replace( '%\s*(</?(?:param|embed)[^>]*>)\s*%', '$1', $text );
	}

	/*
	 * Collapse line breaks inside <audio> and <video> elements,
	 * before and after <source> and <track> elements.
	 */
	if ( str_contains( $text, '<source' ) || str_contains( $text, '<track' ) ) {
		$text = preg_replace( '%([<\[](?:audio|video)[^>\]]*[>\]])\s*%', '$1', $text );
		$text = preg_replace( '%\s*([<\[]/(?:audio|video)[>\]])%', '$1', $text );
		$text = preg_replace( '%\s*(<(?:source|track)[^>]*>)\s*%', '$1', $text );
	}

	// Collapse line breaks before and after <figcaption> elements.
	if ( str_contains( $text, '<figcaption' ) ) {
		$text = preg_replace( '|\s*(<figcaption[^>]*>)|', '$1', $text );
		$text = preg_replace( '|</figcaption>\s*|', '</figcaption>', $text );
	}

	// Remove more than two contiguous line breaks.
	$text = preg_replace( "/\n\n+/", "\n\n", $text );

	// Split up the contents into an array of strings, separated by double line breaks.
	$paragraphs = preg_split( '/\n\s*\n/', $text, -1, PREG_SPLIT_NO_EMPTY );

	// Reset $text prior to rebuilding.
	$text = '';

	// Rebuild the content as a string, wrapping every bit with a <p>.
	foreach ( $paragraphs as $paragraph ) {
		$text .= '<p>' . trim( $paragraph, "\n" ) . "</p>\n";
	}

	// Under certain strange conditions it could create a P of entirely whitespace.
	$text = preg_replace( '|<p>\s*</p>|', '', $text );

	// Add a closing <p> inside <div>, <address>, or <form> tag if missing.
	$text = preg_replace( '!<p>([^<]+)</(div|address|form)>!', '<p>$1</p></$2>', $text );

	// If an opening or closing block element tag is wrapped in a <p>, unwrap it.
	$text = preg_replace( '!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', '$1', $text );

	// In some cases <li> may get wrapped in <p>, fix them.
	$text = preg_replace( '|<p>(<li.+?)</p>|', '$1', $text );

	// If a <blockquote> is wrapped with a <p>, move it inside the <blockquote>.
	$text = preg_replace( '|<p><blockquote([^>]*)>|i', '<blockquote$1><p>', $text );
	$text = str_replace( '</blockquote></p>', '</p></blockquote>', $text );

	// If an opening or closing block element tag is preceded by an opening <p> tag, remove it.
	$text = preg_replace( '!<p>\s*(</?' . $allblocks . '[^>]*>)!', '$1', $text );

	// If an opening or closing block element tag is followed by a closing <p> tag, remove it.
	$text = preg_replace( '!(</?' . $allblocks . '[^>]*>)\s*</p>!', '$1', $text );

	// Optionally insert line breaks.
	if ( $br ) {

		// Normalize <br>
		$text = str_replace( [ '<br>', '<br/>' ], '<br />', $text );

		// Replace any new line characters that aren't preceded by a <br /> with a <br />.
		$text = preg_replace( '|(?<!<br />)\s*\n|', "<br />\n", $text );
	}

	// If a <br /> tag is after an opening or closing block tag, remove it.
	$text = preg_replace( '!(</?' . $allblocks . '[^>]*>)\s*<br />!', '$1', $text );

	// If a <br /> tag is before a subset of opening or closing block tags, remove it.
	$text = preg_replace( '!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $text );
	$text = preg_replace( "|\n</p>$|", '</p>', $text );

	// Replace placeholder <pre> tags with their original content.
	if ( ! empty( $pre_tags ) ) {
		$text = str_replace( array_keys( $pre_tags ), array_values( $pre_tags ), $text );
	}

	return $text;
}

/**
 * File size format
 *
 * Converts a number of bytes to the largest unit the bytes will fit into.
 * Taken from WordPress.
 *
 * It is easier to read 1 KB than 1024 bytes and 1 MB than 1048576 bytes. Converts
 * number of bytes to human readable number by taking the number of that unit
 * that the bytes will go into it. Supports YB value.
 *
 * Please note that integers in PHP are limited to 32 bits, unless they are on
 * 64 bit architecture, then they have 64 bit size. If you need to place the
 * larger size then what PHP integer type will hold, then use a string. It will
 * be converted to a double, which should always have 64 bit length.
 *
 * Technically the correct unit names for powers of 1024 are KiB, MiB etc.
 *
 * @since  1.0.0
 * @param  integer|string $bytes Number of bytes. Note max integer size for integers.
 * @param  integer $decimals Optional. Precision of number of decimal places. Default 0.
 * @return mixed Number string on success, false on failure.
 */
function size_format( $bytes, $decimals = 0 ) {

	// Read bytes in chunks.
	$KB = 1024;
	$MB = 1024 * $KB;
	$GB = 1024 * $MB;
	$TB = 1024 * $GB;

	// Assign relevant units.
	$units = [
		'TB' => $TB,
		'GB' => $GB,
		'MB' => $MB,
		'KB' => $KB,
		'B'  => 1,
	];

	// Return 0 bytes if so.
	if ( 0 === $bytes ) {
		return '0 B';
	}

	// Return the size in relevant units.
	foreach ( $units as $unit => $mag ) {
		if ( (float) $bytes >= $mag ) {
			return number_format( $bytes / $mag, abs( (int) $decimals ) ) . ' ' . $unit;
		}
	}
	return false;
}
