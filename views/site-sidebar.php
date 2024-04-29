<?php
/**
 * Frontend sidebar content
 *
 * @package    Boilerplate
 * @subpackage Views
 * @category   Frontend
 * @since      1.0.0
 */

// Access namespaced functions.
use function UPRO_Func\{
	plugin,
	site,
	lang,
	page,
	user,
	has_social,
	sidebar_users_list,
	autop
};
use function UPRO_Tags\{
	user_link,
	user_first_name,
	user_display_name,
	user_avatar,
	user_posts_list,
	social_list
};

// Author posts limit.
$posts_limit = plugin()->sidebar_limit();

// Users list.
if (  plugin()->profile_pages() && plugin()->sidebar_list() && 'before' == plugin()->sb_list_location() ) {
	echo sidebar_users_list();
}

if ( 'page' == $url->whereAmI() ) :

$username = page()->username();
$get_user = user( $username );
$website  = plugin()->getValue( 'website_' . $username );
$email    = $get_user->email();
$bio      = plugin()->getValue( 'bio_' . $username );

// Shorten bio and add a link to profile page.
if ( strlen( $bio ) > plugin()->sb_bio_limit() ) {
	$cut = substr( $bio, 0, plugin()->sb_bio_limit() );
	$end = strrpos( $cut, ' ' );

	$bio  = $end ? substr( $cut, 0, $end ) : substr( $cut, 0 );
	$bio .= sprintf(
		'&hellip; <a href="%s">%s</a>',
		user_link( $username ),
		$L->get( 'Read Profile' )
	);
}

// Heading element(s).
$get_open  = str_replace( ',', '><', plugin()->sb_bio_label() );
$get_close = str_replace( ',', '></', plugin()->sb_bio_label() );

$label_el_open  = "<{$get_open}>";
$label_el_close = "</{$get_close}>";

if ( plugin()->profile_pages() ) {
	$heading_name = sprintf(
		'%1$s<a href="%2$s">%3$s</a>%4$s',
		$label_el_open,
		user_link( $username ),
		user_display_name( $username ),
		$label_el_close
	);
} else {
	$heading_name = sprintf(
		'%1$s%2$s%3$s',
		$label_el_open,
		user_display_name( $username ),
		$label_el_close
	);
}

$heading_more = sprintf(
	'%1$s%2$s %3$s%4$s',
	$label_el_open,
	lang()->get( 'More from' ),
	( user_first_name( $username ) ? user_first_name( $username ) : user_display_name( $username ) ),
	$label_el_close
);

if ( ! page()->isStatic() ) :

if ( $this->sidebar_bio() ) :
?>
<div id="sidebar-author-profile" class="plugin plugin-user-profiles plugin-user-profile">
	<?php echo $heading_name; ?>

	<?php if ( plugin()->sidebar_avatar() ) : ?>
	<figure id="sidebar-author-avatar" class="author-info-figure author-widget-figure">
		<?php if ( plugin()->profile_pages() ) : ?>
		<a href="<?php echo user_link( $username ); ?>">
		<?php endif; ?>
		<img class="avatar author-info-avatar author-widget-avatar" src="<?php echo user_avatar( $username ); ?>" alt="<?php echo user_display_name( $username ); ?>" width="320" height="320" />
		<?php if ( plugin()->profile_pages() ) : ?>
		</a>
		<?php endif; ?>
	</figure>
	<?php endif; ?>

	<?php if ( plugin()->sidebar_links() ) : ?>
	<div id="sidebar-author-links" class="author-info-links author-widget-links">
		<?php if ( ! empty( $website ) ) {
		printf(
			'<p><a href="%s" target="_blank" rel="noopener noreferrer">%s</a></p>',
			$website,
			str_replace( [ 'http://', 'https://' ], '', rtrim( $website, '/\\' ) )
		);
		} ?>
		<p><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></p>
		<?php if ( has_social( $username ) ) { echo social_list( $username ); } ?>
	</div>
	<?php endif; ?>

	<div id="sidebar-author-bio" class="author-info-bio author-widget-bio">
		<?php if ( plugin()->profile_pages() ) {
			echo htmlspecialchars_decode( autop( $bio ) );
		} ?>
	</div>
</div>
<?php endif; ?>

<?php if ( user_posts_list( $username ) && plugin()->sidebar_more() ) : ?>
<div id="sidebar-author-more" class="plugin plugin-user-profiles plugin-user-links">
	<div class="author-post-links">
		<?php
		echo $heading_more;
		echo user_posts_list( $username, $posts_limit ); ?>
	</div>
</div>
<?php endif; endif; ?>

<?php

// If post.
endif;

// Users list.
if (  plugin()->profile_pages() && plugin()->sidebar_list() && 'after' == plugin()->sb_list_location() ) {
	echo sidebar_users_list();
}
