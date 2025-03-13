<?php
/**
 * Template tags
 *
 * @package    User Profiles
 * @subpackage Core
 * @category   Functions
 * @since      1.0.0
 */

namespace UPRO_Tags;

// Access namespaced functions.
use function UPRO_Func\{
	plugin,
	site,
	lang,
	url,
	page,
	icon,
	user,
	usernames,
	user_posts,
	users_list_min_role,
	has_social,
	default_socials,
	user_socials,
	autop
};

// Stop if accessed directly.
if ( ! defined( 'BLUDIT' ) ) {
	die( 'You are not allowed direct access to this file.' );
}

/**
 * User role
 *
 * Gets the user role by username.
 *
 * @since  1.0.0
 * @param  string $name Username from database.
 * @param  string $return Role name or title.
 * @return string
 */
function role( $name = '', $return = 'role' ) {

	// Get the user object.
	$get = user( $name );

	// Role text.
	if ( 'admin' == $get->role() ) {
		$role  = 'admin';
		$title = lang()->g( 'Administrator' );
	} elseif ( 'editor' == $get->role() ) {
		$role  = 'editor';
		$title = lang()->g( 'Editor' );
	} elseif ( 'author' == $get->role() ) {
		$role  = 'author';
		$title = lang()->g( 'Author' );
	} else {
		$role  = 'reader';
		$title = lang()->g( 'Reader' );
	}

	if ( 'title' == $return ) {
		return $title;
	}
	return $role;
}

/**
 * First name
 *
 * @since  1.0.0
 * @param  string $name
 * @return mixed
 */
function user_first_name( $name = '' ) {
	$user = user( $name );
	if ( $user->firstName() ) {
		return $user->firstName();
	}
	return false;
}

/**
 * Last name
 *
 * @since  1.0.0
 * @param  string $name
 * @return mixed
 */
function user_last_name( $name = '' ) {
	$user = user( $name );
	if ( $user->lastName() ) {
		return $user->lastName();
	}
	return false;
}

/**
 * Full name
 *
 * @since  1.0.0
 * @param  string $name
 * @return mixed
 */
function user_full_name( $name = '' ) {
	$user = user( $name );
	if ( $user->firstName() && $user->lastName() ) {
		return $user->firstName() . ' ' . $user->lastName();
	}
	return false;
}

/**
 * Nickname
 *
 * @since  1.0.0
 * @param  string $name
 * @return mixed
 */
function user_nickname( $name = '' ) {
	$user = user( $name );
	if ( $user->nickname() ) {
		return $user->nickname();
	}
	return false;
}

/**
 * Display name
 *
 * @since  1.0.0
 * @param  string $name
 * @return string
 */
function user_display_name( $name = '' ) {

	$user = user( $name );

	if ( user_nickname( $name ) ) {
		return  user_nickname( $name );
	} elseif ( user_full_name( $name ) ) {
		return user_full_name( $name );
	} elseif ( user_first_name( $name ) ) {
		return user_first_name( $name );
	} else {
		return ucwords(
			str_replace(
				[ '-', '_', '.' ],
				' ',
				$user->username()
			)
		);
	}
}

/**
 * User link
 *
 * The URL to the frontend profile
 * of the specified user.
 *
 * @since  1.0.0
 * @param  string $name Username
 * @return string
 */
function user_link( $user = '' ) {
	return sprintf(
		'%s%s/%s',
		site()->url(),
		plugin()->users_slug(),
		$user
	);
}

/**
 * Default avatar
 *
 * The URL for the default, fallback avatar.
 *
 * @since  1.0.0
 * @return string
 */
function default_avatar() {

	$default = plugin()->default_avatar();
	$custom  = plugin()->custom_avatar();
	$path    = PATH_PLUGINS . 'user-profiles/assets/images/avatars' . DS;
	$src     = '';

	foreach ( glob( $path . "*.{png,jpeg,jpeg,gif,svg,PNG,JPG,JPEG,GIF,SVG}", GLOB_BRACE ) as $default_avatar ) :

		$avatar_id  = str_replace( '.', '', basename( $default_avatar, pathinfo( $default_avatar, PATHINFO_EXTENSION ) ) );

		if ( $avatar_id == $default ) {
			$src = plugin()->domainPath() . 'assets/images/avatars/' . basename( $default_avatar );
		}
	endforeach;

	if ( 'custom' == $default ) {
		$src = DOMAIN_CONTENT . 'user-profiles/avatar/cache/avatar/' . $custom[0];
	}
	return $src;
}

/**
 * User avatar
 *
 * @since  1.0.0
 * @param  string $name Username
 * @return string Returns the user-uploaded avatar URL or
 *                the default avatar URL.
 */
function user_avatar( $name = '' ) {

	$src = '';
	if ( \Sanitize :: pathFile( PATH_UPLOADS_PROFILES . $name . '.png' ) ) {
		$src = DOMAIN_UPLOADS_PROFILES . $name . '.png?version=' . time();
	} else {
		$src = default_avatar();
	}
	return $src;
}

/**
 * Bio heading
 *
 * @since  1.0.0
 * @param  string $name
 * @return string Returns the heading text.
 */
function bio_heading( $name = '' ) {

	$field = plugin()->bio_heading();

	if ( ! empty( $field ) ) {

		$text = str_replace( '{{user}}', user_display_name( $name ), $field );
		if ( ! empty( user_first_name( $name ) ) ) {
			$text = str_replace( '{{first}}', user_first_name( $name ), $text );
		} else {
			$text = lang()->get( 'About' );
		}
	} else {
		$text = lang()->get( 'About' ) . ' ' . user_display_name( $name );
	}
	return $text;
}

/**
 * Details heading
 *
 * @since  1.0.0
 * @param  string $name
 * @return string Returns the heading text.
 */
function details_heading( $name = '' ) {

	$field = plugin()->details_heading();

	if ( ! empty( $field ) ) {

		$text = str_replace( '{{user}}', user_display_name( $name ), $field );
		if ( ! empty( user_first_name( $name ) ) ) {
			$text = str_replace( '{{first}}', user_first_name( $name ), $text );
		} else {
			$text = lang()->get( 'Details' );
		}
	} else {
		$text = lang()->get( 'Details' );
	}
	return $text;
}

/**
 * Formatted bio
 *
 * Print a user bio with paragraphs and line breaks,
 *
 * @since  1.0.0
 * @param  string $name
 * @return string Returns the bio markup.
 */
function formatted_bio( $name = '' ) {
	$bio = plugin()->getValue( 'bio_' . $name );
	return autop( htmlspecialchars_decode( $bio ) );
}

/**
 * User posts list
 *
 * @since  1.0.0
 * @param  string $name Username
 * @return mixed Returns an array of page links or false.
 */
function user_posts_list( $name = '', $limit = 5 ) {

	$posts = user_posts( $name );

	if ( ! $posts ) {
		return false;
	}

	$list  = '<ul>';
	$count = 0;
	foreach ( $posts as $post => $key ) {

		// Stop loop if list limit is reached.
		if ( $count == $limit ) {
			break;
		}

		// List item.
		$page = buildPage( $key );
		$list .= sprintf(
			'<li><a href="%s">%s</a></li>',
			$page->permalink(),
			$page->title()
		);
		$count++;
	}
	$list .= '</ul>';

	return $list;
}

/**
 * Get social link
 *
 * @since  1.0.0
 * @param  string $name
 * @param  string $site
 * @return mixed
 */
function get_social( $name = '', $site = '' ) {

	$allowed = default_socials();

	if ( ! in_array( $site, $allowed ) ) {
		return false;
	}

	$user   = user( $name );
	$social = $user->getValue( $site );

	if ( $social && ! empty( $social ) ) {
		return $social;
	}
	return false;
}

/**
 * Social network list
 *
 * @since  1.0.0
 * @param  string $name
 * @return string
 */
function social_list( $name = '' ) {

	if ( ! has_social( $name ) ) {
		return '';
	}

	$socials = user_socials( $name );
	$list    = '<ul class="user-profile-socials-list">';

	foreach ( $socials as $social => $link ) {

		if ( empty( $link['url'] ) ) {
			continue;
		}

		$list .= sprintf(
			'<li><a href="%s" target="_blank" rel="noopener noreferrer" title="%s">%s<span class="screen-reader-text">%s</span></a></li>',
			$link['url'],
			$link['name'],
			icon( $social, true ),
			$link['name']
		);
	}
	$list .= '</ul>';

	return $list;
}

/**
 * Author profile box
 *
 * To be merged with post/page content.
 *
 * @since  1.0.0
 * @param  string $template The full path to the author box template.
 * @return string Returns the author box markup.
 */
function author_box( $template = '' ) {

	if ( plugin()->author_tabbed() ) {
		$template = plugin()->phpPath() . '/views/author-box-tabbed.php';
	} else {
		$template = plugin()->phpPath() . '/views/author-box.php';
	}

	if ( file_exists( $template ) ) {
		ob_start();
		include( $template );
		return ob_get_clean();
	}
	return null;
}

/**
 * More by author tab
 *
 * @since  1.0.0
 * @return mixed
 */
function more_box_tab() {

	if ( 'page' != url()->whereAmI() ) {
		return false;
	}

	$tab = lang()->get( 'Posts' );
	if ( 'page' == plugin()->author_display() ) {
		$tab = lang()->get( 'Pages' );
	} elseif ( 'both' == plugin()->author_display() ) {
		$tab = lang()->get( 'More' );
	}
	return $tab;
}

/**
 * More by author heading
 *
 * @since  1.0.0
 * @param  string $name Username of author.
 * @return string
 */
function more_box_heading( $name = '' ) {

	if ( 'page' != url()->whereAmI() ) {
		return false;
	}

	$type = lang()->get( 'Posts' );
	if ( 'page' == plugin()->author_display() ) {
		$type = lang()->get( 'Pages' );
	} elseif ( 'both' == plugin()->author_display() ) {
		$type = lang()->get( 'Content' );
	}
	return sprintf(
		lang()->get( 'More %s by %s' ),
		$type,
		$name
	);
}

/**
 * Users list
 *
 * Used as a sidebar option but designed to also
 * be used as a standalone template tag.
 *
 * @since  1.0.0
 * @param  mixed $args Arguments to be passed.
 * @param  array $defaults Default arguments.
 * @return string Returns the list markup.
 */
function users_list( $args = null, $defaults = [] ) {

	// Default arguments.
	$defaults = [
		'wrap'       => false,
		'wrap_class' => 'list-wrap users-list-wrap',
		'direction'  => 'vert', // horz or vert
		'list_class' => 'users-list standard-users-list',
		'label'      => false,
		'label_el'   => 'h2',
		'links'      => true,
		'avatars'    => true,
		'sort_by'    => 'abc' // abc or date
	];

	// Maybe override defaults.
	if ( is_array( $args ) && $args ) {
		if ( isset( $args['direction'] ) && 'horz' == $args['direction'] && ! isset( $args['list_class'] ) ) {
			$defaults['list_class'] = 'users-list inline-users-list';
		}
		$args = array_merge( $defaults, $args );
	} else {
		$args = $defaults;
	}

	// Label wrapping elements.
	$get_open  = str_replace( ',', '><', $args['label_el'] );
	$get_close = str_replace( ',', '></', $args['label_el'] );

	$label_el_open  = "<{$get_open}>";
	$label_el_close = "</{$get_close}>";

	// List markup.
	$html = '';
	if ( $args['wrap'] ) {
		$html = sprintf(
			'<div class="%s">',
			$args['wrap_class']
		);
	}
	if ( $args['label'] ) {
		$html .= sprintf(
			'%1$s%2$s%3$s',
			$label_el_open,
			$args['label'],
			$label_el_close
		);
	}
	$html .= sprintf(
		'<ul class="%s">',
		$args['list_class'] . ( $args['avatars'] ? ' has-avatars' : '' )
	);

	$users = usernames();
	if ( 'select' == plugin()->sb_list_display() ) {
		$users = plugin()->sb_list_select();
	}
	asort( $users );

	$count = 0;
	foreach ( $users as $user ) {
		$count++;

		if ( 'select' != plugin()->sb_list_display() ) {
			if ( ! in_array( role( $user ), users_list_min_role() ) ) {
				continue;
			}
		}

		if ( 'limit' == plugin()->sb_list_display() ) {
			if ( $count > plugin()->sb_list_limit() ) {
				break;
			}
		}

		$html .= '<li class="user-list-entry">';
		if ( $args['avatars'] ) {
			$html .= sprintf(
				'<span class="user-list-avatar user-widget-avatar"><img class="avatar" src="%s" width="24" height="24" role="presentation" /></span>',
				user_avatar( $user )
			);
		}
		if ( $args['links'] ) {
			$html .= '<a href="' . user_link( $user ) . '">';
		}
		$html .= sprintf(
			'<span class="user-list-name user-widget-name">%s</span>',
			user_display_name( $user )
		);
		if ( $args['links'] ) {
			$html .= '</a>';
		}
		$html .= '</li>';
	}
	$html .= '</ul>';

	if ( $args['wrap'] ) {
		$html .= '</div>';
	}
	if ( ! empty( plugin()->sb_list_select() ) ) {
		return $html;
	}
	return null;
}

/**
 * Page description
 *
 * Gets the page description or
 * an excerpt of the content.
 *
 * @since  1.0.0
 * @return string Returns the description.
 */
function page_description( $key = '' ) {

	if ( empty( $key ) ) {
		$key = page()->key();
	}

	$page = buildPage( $key );

	if ( $page->description() ) {
		$page_desc = $page->description();
	} else {
		$page_desc  = substr( strip_tags( $page->content() ), 0, 85 );
		if ( ! empty( $page->content() ) && ! ctype_space( $page->content() ) ) {
			$page_desc .= '&hellip;';
		}
	}
	return $page_desc;
}
