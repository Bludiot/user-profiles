<?php
/**
 * Profiles guide info
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

<h3 class="tab-section-heading "><?php lang()->p( 'User Profiles' ) ?></h3>

<p><?php lang()->p( 'The User Profiles plugin adds user information fields and provides several optional ways to display user details, including a profile page for each registered user.' ); ?></p>

<h3 class="form-heading"><?php $L->p( 'Profile Pages' ); ?></h3>

<p><?php lang()->p( 'The user profile pages need custom development of the active theme to display them, even with the setting enabled. The profile content is called with the following hook.' ); ?></p>

<pre lang="PHP">&lt;?php <?php echo $hook; ?> ?&gt;</pre>

<p><?php lang()->p( 'This plugin adds a custom "webhook" for checking if the visitor is at a profile URL. This can be used to conditionally get the profile content or page content, as in the following example.' ); ?></p>

<pre lang="PHP">&lt;?php
if ( 'profile' == $url->whereAmI() ) {
	echo <?php echo $hook . "\r"; ?>
} else {
	echo $page->content();
} ?&gt;</pre>

<p><?php lang()->p( 'Or the webhook can be used to get a custom template, as follows.' ); ?></p>

<pre lang="PHP">&lt;?php
if ( 'profile' == $url->whereAmI() ) {
	echo include( THEME_DIR . 'template/path/profile.php' );
} else {
	echo include( THEME_DIR . 'template/path/page.php' );
} ?&gt;</pre>

<p><?php lang()->p( '' ); ?></p>
<p><?php lang()->p( '' ); ?></p>
<p><?php lang()->p( '' ); ?></p>
<p><?php lang()->p( '' ); ?></p>
<p><?php lang()->p( '' ); ?></p>

<h3 class="form-heading"><?php $L->p( 'User Settings' ); ?></h3>

<p><?php lang()->p( 'Each user, under the Users tab of the User Profiles page, has a website field and a text area for biographical information. These are combined with the default Bludit user fields for the profiles.' ); ?></p>

<p><?php lang()->p( 'A specific cover image may be uploaded for each user\'s profile page, overriding the default cover image.' ); ?></p>
