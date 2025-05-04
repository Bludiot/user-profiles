<?php
/**
 * Functions guide info
 *
 * @package    User Profiles
 * @subpackage Views
 * @since      1.0.0
 */

// Access namespaced functions.
use function UPRO_Func\{
	lang
};

// Helper function for CMS version.
if ( BLUDIT_VERSION >= '4.0.0' ) {
	$hook = "execPluginsByHook( 'user_profile_content' );";
} else {
	$hook = "Theme::plugins( 'user_profile_content' );";
}

?>
<style>
	pre, code {
		user-select: all;
		cursor: pointer;
	}
	pre {
		max-width: 720px;
		margin: 1rem 0;
		white-space: pre-wrap;
	}
	<?php if ( ! $site->adminTheme() || ( 'booty' == $site->adminTheme() && 'configureight' != $site->theme() ) ) : ?>
	pre {
		padding: 1em 2em;
		background: #eaeaea;
		background: rgba( 0,0,0,0.07 );
		border: solid 1px #cccccc;
		color: #444444;
	}
	<?php endif; ?>
</style>

<h3 class="tab-section-heading "><?php lang()->p( 'User Functions' ) ?></h3>

<p><?php lang()->p( 'This plugin can be used only for it\'s various functions without enabling profiles, author sections, or widgets. These functions are namespaced with <code>UPRO_Func</code> or <code>UPRO_Tags</code>.' ); ?></p>

<p><?php lang()->p( 'Unless otherwise stated, the <code>$name</code> parameter is a string, the username (not first name or last name), and is required.' ); ?></p>

<h3 class="form-heading"><?php $L->p( 'Usernames' ); ?></h3>
<p><?php lang()->p( 'Returns an array of usernames of all registered users.' ); ?><br />
<code>&lt;php UPRO_Func\usernames(); ?&gt;</code></p>

<h3 class="form-heading"><?php $L->p( 'User' ); ?></h3>
<p><?php lang()->p( 'Returns a user object by username.' ); ?><br />
<code>&lt;php UPRO_Func\user( $name ); ?&gt;</code></p>

<h3 class="form-heading"><?php $L->p( 'User Role' ); ?></h3>
<p><?php lang()->p( 'Gets the user role by username. The <code>$name</code> string parameter defaults to `reader`. The optional <code>$return</code> string parameter accepts `role` or `title` and defaults to role.' ); ?><br />
<code>&lt;php UPRO_Tags\role( $name, $return ); ?&gt;</code></p>

<h3 class="form-heading"><?php $L->p( 'First Name' ); ?></h3>
<p><?php lang()->p( "Returns the user's first name or false if not set." ); ?><br />
<code>&lt;php UPRO_Tags\user_first_name( $name ); ?&gt;</code></p>

<h3 class="form-heading"><?php $L->p( 'Last Name' ); ?></h3>
<p><?php lang()->p( "Returns the user's last name or false if not set." ); ?><br />
<code>&lt;php UPRO_Tags\user_last_name( $name ); ?&gt;</code></p>

<h3 class="form-heading"><?php $L->p( 'Full Name' ); ?></h3>
<p><?php lang()->p( "Returns the user's first & last name or false if either or both are not set." ); ?><br />
<code>&lt;php UPRO_Tags\user_full_name( $name ); ?&gt;</code></p>

<h3 class="form-heading"><?php $L->p( 'Nickname' ); ?></h3>
<p><?php lang()->p( "Returns the user's nickname or false if not set." ); ?><br />
<code>&lt;php UPRO_Tags\user_nickname( $name ); ?&gt;</code></p>

<h3 class="form-heading"><?php $L->p( 'Display Name' ); ?></h3>
<p><?php lang()->p( 'The cascade of return possibilities begins with nickname, then full name, first name, and defaults to a prettified username if no other names are set.' ); ?><br />
<code>&lt;php UPRO_Func\user_display_name( $name ); ?&gt;</code></p>

<h3 class="form-heading"><?php $L->p( 'Profile Link' ); ?></h3>
<p><?php lang()->p( 'Returns the URL to the frontend profile of the specified user.' ); ?><br />
<code>&lt;php UPRO_Tags\user_link( $name ); ?&gt;</code></p>

<h3 class="form-heading"><?php $L->p( 'User Avatar' ); ?></h3>
<p><?php lang()->p( 'Returns the user-uploaded avatar URL or the default avatar URL.' ); ?><br />
<code>&lt;php UPRO_Tags\user_avatar( $name ); ?&gt;</code></p>

<h3 class="form-heading"><?php $L->p( 'Formatted Bio' ); ?></h3>
<p><?php lang()->p( 'Returns a user bio with paragraphs and line breaks.' ); ?><br />
<code>&lt;php UPRO_Tags\formatted_bio( $name ); ?&gt;</code></p>

<h3 class="form-heading"><?php $L->p( 'Users List' ); ?></h3>
<p><?php lang()->p( 'Returns an unordered list of users using the display name. The optional <code>$args</code> array parameter will override any default keys in your new array.' ); ?><br />
<code>&lt;php UPRO_Tags\users_list( $args ); ?&gt;</code></p>

<p><?php lang()->p( "Following is the default arguments array." ); ?></p>

<pre lang="PHP">&lt;?php
$args = array(
	'wrap'       => false,
	'wrap_class' => 'list-wrap users-list-wrap',
	'direction'  => 'vert', // `horz` or `vert`
	'list_class' => 'users-list standard-users-list',
	'label'      => false, // List heading.
	'label_el'   => 'h2', // List heading element.
	'links'      => false, // Links to profile pages.
	'avatars'    => true, // Inline avatars precede user names.
	'sort_by'    => 'abc' // `abc` or `date`
); ?&gt;</pre>

<h3 class="form-heading"><?php $L->p( 'User Posts List' ); ?></h3>
<p><?php lang()->p( 'Returns an unordered list of most recent posts authored by the user, or false. The <code>$limit</code> integer parameter, number of posts returned, is optional. Default is 5 posts.' ); ?><br />
<code>&lt;php UPRO_Tags\user_posts_list( $name, $limit ); ?&gt;</code></p>
