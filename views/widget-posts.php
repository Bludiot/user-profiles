<?php
/**
 * More posts widget
 *
 * @package    User Profiles
 * @subpackage Views
 * @category   Frontend
 * @since      1.0.0
 */

// Access namespaced functions.
use function UPRO_Func\{
	plugin,
	lang,
	page,
	user
};
use function UPRO_Tags\{
	user_first_name,
	user_display_name,
	user_posts_list
};

if ( 'page' == $url->whereAmI() ) :
if ( ! page()->isStatic() ) :

// User variables.
$username = page()->username();
$get_user = user( $username );

// Author posts limit.
$posts_limit = plugin()->sidebar_limit();

// Heading element(s).
$get_open  = str_replace( ',', '><', plugin()->widgets_label() );
$get_close = str_replace( ',', '></', plugin()->widgets_label() );

$label_el_open  = "<{$get_open}>";
$label_el_close = "</{$get_close}>";

$get_text = plugin()->more_widget_label();
if ( ! empty( $get_text ) && ! ctype_space( $get_text ) ) {

	$text = $get_text;
	if ( strstr( $get_text, '{{first_name}}' ) ) {
		$text = str_replace( '{{first_name}}', user_first_name( $username ), $text );
	}
	if ( strstr( $get_text, '{{display_name}}' ) ) {
		$text = str_replace( '{{display_name}}', user_display_name( $username ), $text );
	}

	$heading = sprintf(
		'%1$s%2$s%3$s',
		$label_el_open,
		$text,
		$label_el_close
	);
} else {
	$heading = sprintf(
		'%1$s%2$s %3$s%4$s',
		$label_el_open,
		lang()->get( 'More from' ),
		( user_first_name( $username ) ? user_first_name( $username ) : user_display_name( $username ) ),
		$label_el_close
	);
}

?>
<div id="sidebar-author-more" class="plugin plugin-user-profiles plugin-user-links">
	<div class="author-post-links">
		<?php
		echo $heading;
		echo user_posts_list( $username, $posts_limit ); ?>
	</div>
</div>
<?php
endif; // If not static.
endif; // If page.
