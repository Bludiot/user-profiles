<?php
/**
 * User page content
 *
 * @package    User Profiles
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
	user_slug,
	user,
	user_posts,
	default_cover,
	has_social,
	user_socials,
	sidebar_users_list,
	autop
};
use function UPRO_Tags\{
	role,
	user_link,
	user_first_name,
	user_display_name,
	user_avatar,
	bio_heading,
	details_heading,
	formatted_bio,
	user_posts_list,
	social_list
};

$user        = user( user_slug() );
$name        = $user->username();
$cover       = plugin()->cover_src( $name );
$website     = plugin()->getValue( 'website_' . $name );
$email       = $user->email();
$bio         = plugin()->getValue( 'bio_' . $name );
$theme       = getPlugin( 'configureight' );
$header      = plugin()->header_style();
$show_role   = plugin()->profile_role();
$show_site   = plugin()->profile_site();
$show_social = plugin()->profile_social();
$show_posts  = plugin()->profile_posts();
if ( plugin()->getValue( 'email_' . $name ) !== plugin()->profile_email() ) {
	$show_email  = plugin()->getValue( 'email_' . $name );
} else {
	$show_email  = plugin()->profile_email();
}

// Heading elements vary by theme and options.
if ( $theme ) {
	if ( 'header' == $theme->cover_in_profile() ) {
		$name_el_open     = '<h1>';
		$name_el_close    = '</h1>';
		$section_el_open  = '<h2>';
		$section_el_close = '</h2>';
	} else {
		$name_el_open     = '<h2>';
		$name_el_close    = '</h2>';
		$section_el_open  = '<h3>';
		$section_el_close = '</h3>';
	}
}

// Cover image classes.
$cover_wrap_class  = 'profile-cover-wrap cover-overlay';
$cover_image_class = '';
if ( $theme ) {
	if (
		'blend' == $theme->cover_style() &&
		is_array( $theme->cover_blend_use() ) &&
		in_array( 'profile', $theme->cover_blend_use() )
	) {
		$cover_wrap_class = 'profile-cover-wrap cover-blend';
	}
	if ( in_array( 'profile', $theme->cover_desaturate_use() ) ) {
		$cover_image_class = 'desaturate';
	}
}

?>
<div class="profile-page profile-style-<?php echo $header; ?>">

	<?php if ( ! $theme || 'profile' == $theme->cover_in_profile() || 'both' == $theme->cover_in_profile() ) : ?>
	<header id="profile-page-header" class="profile-page-header" data-page-header>
		<div class="<?php echo $cover_wrap_class; ?>">
			<figure class="profile-cover">
				<img src="<?php echo $cover; ?>" class="<?php echo $cover_image_class; ?>" role="presentation" />
			</figure>
		</div>
		<div class="profile-header-inner">
			<div class="profile-intro">
				<?php echo $name_el_open; ?>
					<?php echo user_display_name( $name ); ?>
				<?php echo $name_el_close; ?>
			</div>
			<figure class="profile-avatar">
				<a href="<?php echo user_avatar( $name ); ?>" data-fancybox><img src="<?php echo user_avatar( $name ); ?>" /></a>
				<figcaption class="screen-reader-text"><?php echo user_display_name( $name ); ?></figcaption>
			</figure>
		</div>
	</header>
	<?php else : ?>
	<header id="profile-page-header" class="profile-page-header-no-cover" data-page-header>
		<figure class="profile-avatar">
			<a href="<?php echo user_avatar( $name ); ?>" data-fancybox><img src="<?php echo user_avatar( $name ); ?>" /></a>
			<figcaption class="screen-reader-text"><?php echo user_display_name( $name ); ?></figcaption>
		</figure>
		<div class="profile-intro">
			<?php echo $name_el_open; ?>
				<?php echo user_display_name( $name ); ?>
			<?php echo $name_el_close; ?>
		</div>
	</header>
	<?php endif; ?>

	<div id="profile-details-bio" class="profile-details-bio">
		<div id="profile-page-details" class="profile-page-details">
			<?php echo $section_el_open; ?><?php echo details_heading( $name ); ?><?php echo $section_el_close; ?>
			<ul class="profile-details-list">
				<?php if ( $show_role ) {
					printf(
						'<li class="profile-details-list-role">%s</li>',
						role( $name, 'title' )
					);
				} ?>
				<?php if ( $email && $show_email ) {
					printf(
						'<li class="profile-details-list-email"><a href="mailto:%s" target="_blank" rel="noopener noreferrer">%s</a></li>',
						$email,
						$email
					);
				} ?>
				<?php if ( $website && $show_site ) {
					printf(
						'<li class="profile-details-list-site"><a href="%s" target="_blank" rel="noopener noreferrer">%s</a></li>',
						$website,
						str_replace( [ 'http://', 'https://' ], '', rtrim( $website, '/\\' ) )
					);
				} ?>
			</ul>
			<?php if ( has_social( $name ) && $show_social ) { echo social_list( $name ); } ?>
		</div>

		<div id="profile-page-bio" class="profile-page-bio">
			<?php if ( $bio ) : ?>
			<?php echo $section_el_open; ?><?php echo bio_heading( $name ); ?><?php echo $section_el_close; ?>
			<?php echo formatted_bio( $name ); ?>
			<?php else : ?>
			<?php echo $section_el_open; ?><?php lang()->p( 'No biographical information available.' ); ?><?php echo $section_el_close; ?>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( $show_posts && ! empty( user_posts( $name ) ) ) : ?>
	<div id="profile-page-posts" class="profile-page-posts">
		<?php printf(
			'%s%s %s%s',
			$section_el_open,
			lang()->get( 'Posts By' ),
			user_display_name( $name ),
			$section_el_close
		); ?>
		<?php include( $this->phpPath() . '/views/posts-list.php' ); ?>
	</div>
	<?php elseif ( $show_posts && empty( user_posts( $name ) ) ) : ?>
	<div id="profile-page-posts" class="profile-page-posts">
		<?php printf(
			'<p>%s %s</p>',
			user_display_name( $name ),
			lang()->get( 'has not yet created any posts.' )
		); ?>
	</div>
	<?php endif; ?>
</div>
