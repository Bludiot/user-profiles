<?php
/**
 * Posts list display
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
$loop_style = 'list';
$cover_image_class = '';
if ( $theme ) {
	$loop_style = $theme->related_style() . ' cover-overlay';

	if (
		'blend' == $theme->cover_style() &&
		is_array( $theme->cover_blend_use() ) &&
		in_array( 'profile', $theme->cover_blend_use() )
	) {
		$loop_style = $theme->related_style() . ' cover-blend';
	}
	if ( in_array( 'profile', $theme->cover_desaturate_use() ) ) {
		$cover_image_class = 'desaturate';
	}
}

// Use loop date.
$loop_date = true;
if ( $theme ) {
	$loop_date = $theme->loop_date();
}

?>
<div class="profile-loop profile-loop-style-<?php echo $loop_style; ?>">
	<?php foreach ( $user_posts as $key ) :
	$post = buildPage( $key );
	?>
	<article class="profile-post">
		<?php if ( $post->coverImage() ) : ?>
		<a href="<?php echo $post->permalink(); ?>">
			<figure class="profile-post-cover">
				<img class="<?php echo $cover_image_class; ?>" src="<?php echo $post->thumbCoverImage(); ?>" alt="">
			</figure>
		</a>
		<?php endif; ?>
		<div class="profile-post-content">
			<p class="profile-post-title"><a href="<?php echo $post->permalink(); ?>">
				<?php echo $post->title(); ?>
			</a></p>
			<p class="profile-post-description"><?php echo page_description( $post->key() ); ?></p>
			<div class="profile-post-meta">
				<?php if ( $loop_date ) : ?>
				<time datetime="<?php echo $post->dateRaw( 'c' ); ?>">
					<?php echo $post->date(); ?>
				</time>
				<?php endif; ?>
			</div>
		</div>
	</article>
	<?php endforeach; ?>
</div>
