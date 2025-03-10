<?php
/**
 * Posts grid display
 *
 * @package    User Profiles
 * @subpackage Views
 * @category   Frontend
 * @since      1.0.0
 */

// Import namespaced functions.
use function UPRO_Func\{
	plugin,
	url,
	user_posts
};
use function UPRO_Tags\{
	page_description
};

$theme = getPlugin( 'configureight' );

if ( plugin()->users_slug() == url()->whereAmI() ) {
	$user_posts = user_posts( $name, plugin()->profile_limit() );
} else {
	$user_posts = user_posts( $name, plugin()->author_limit() );
}

// Related style.
$related_style = 'list';
$cover_image_class = '';
if ( $theme ) {
	$related_style = $theme->related_style() . ' cover-overlay';

	if (
		'blend' == $theme->cover_style() &&
		is_array( $theme->cover_blend_use() ) &&
		in_array( 'related', $theme->cover_blend_use() )
	) {
		$related_style = $theme->related_style() . ' cover-blend';
	}
	if ( in_array( 'related', $theme->cover_desaturate_use() ) ) {
		$cover_image_class = 'desaturate';
	}
}

?>
<div class="profile-loop related-style-<?php echo $related_style; ?>">
<?php foreach ( $user_posts as $key ) :
	$post = buildPage( $key );
	?>
	<article class="related-post">
		<a href="<?php echo $post->permalink(); ?>">
			<?php if ( $post->coverImage() ) : ?>
			<figure class="related-cover">
				<img class="<?php echo $cover_image_class; ?>" src="<?php echo $post->thumbCoverImage(); ?>" alt="">
			</figure>
			<?php endif; ?>
			<div class="related-content">
				<p class="related-title">
					<?php echo $post->title(); ?>
				</p>
				<p class="related-description"><?php echo page_description( $post->key() ); ?></p>
			</div>
		</a>
	</article>
	<?php endforeach; ?>
</div>
