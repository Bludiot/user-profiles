<?php
/**
 * User Profile
 *
 * @package    User Profiles
 * @subpackage Views
 * @category   Forms
 * @since      1.0.0
 */

// Access namespaced functions.
use function UPRO_Func\{
	plugin,
	site,
	lang,
	page,
	user,
	user_posts,
	has_social,
	get_social,
	autop
};
use function UPRO_Tags\{
	role,
	user_link,
	user_display_name,
	user_avatar,
	user_posts_list,
	social_list
};

$name     = page()->username();
$get_user = user( $name );
$website  = plugin()->getValue( 'website_' . $name );
$email    = $get_user->email();
$bio      = plugin()->getValue( 'bio_' . $name );
$limit    = plugin()->profile_limit();

?>
<div id="author-box-wrap">

	<div id="about-author" class="author-info-content author-info-padded" role="tabpanel" aria-labelledby="about-author">
		<div class="author-info-details">

			<?php if ( plugin()->author_avatar() ) : ?>
			<figure class="author-info-figure">
				<?php if ( plugin()->profile_pages() ) : ?>
				<a href="<?php echo user_link( $name ); ?>">
				<?php endif; ?>
				<img class="avatar author-info-avatar" src="<?php echo user_avatar( $name ); ?>" alt="<?php echo user_display_name( $name ); ?>" width="320" height="320" />
				<?php if ( plugin()->profile_pages() ) : ?>
				</a>
				<?php endif; ?>
			</figure>
			<?php endif; ?>

			<div class="author-info-links">

				<?php if ( ! empty( $website ) && plugin()->author_site() ) {
				printf(
					'<p><a href="%s" target="_blank" rel="noopener noreferrer">%s</a></p>',
					$website,
					str_replace( [ 'http://', 'https://' ], '', $website )
				);
				} ?>
				<?php if ( plugin()->author_email() ) {
					printf(
						'<p><a href="mailto:%s">%s</a></p>',
						$email,
						$email
					);
				} ?>
				<?php if ( has_social( $name ) && plugin()->author_social() ) {
					echo social_list( $name );
				} ?>
			</div>
		</div>

		<div class="author-info-bio">

			<header class="author-info-header">
				<h3 class="author-info-name">
					<?php if ( plugin()->profile_pages() ) : ?>
					<a href="<?php echo user_link( $name ); ?>">
					<?php endif; ?>
					<?php echo user_display_name( $name ); ?>
					<?php if ( plugin()->profile_pages() ) : ?>
					</a>
					<?php endif; ?>
				</h3>

				<?php if ( plugin()->author_role() ) {
					printf(
						'<p class="author-info-role">%s</p>',
						role( $name, 'title' )
					);
				} ?>
			</header>
			<?php if ( $bio )  {
				echo autop( htmlspecialchars_decode( $bio ) );
			} ?>
		</div>
	</div>
	<?php
	// More posts by the offer, if any, if option allows.
	if ( plugin()->author_posts() && count( user_posts( $name ) ) > 1 ) : ?>
	<div id="posts-by-author" class="author-info-posts author-info-padded" role="tabpanel" aria-labelledby="posts-by-author">
		<h3 class="author-info-more"><?php lang()->p( 'More Posts by' ); ?> <?php echo user_display_name( $name ); ?></h3>
		<?php include( plugin()->phpPath() . '/views/posts-list.php' ); ?>
	</div>
	<?php endif; ?>
</div>
