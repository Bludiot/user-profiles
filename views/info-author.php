<?php
/**
 * Author guide info
 *
 * @package    User Profiles
 * @subpackage Views
 * @since      1.0.0
 */

// Access namespaced functions.
use function UPRO_Func\{
	site,
	lang
};

?>
<h3 class="tab-section-heading "><?php lang()->p( 'Author Sections' ) ?></h3>

<p><?php lang()->p( "The author section only appears on individual posts (default and sticky pages) by default, not static or loop pages. However, static pages may be selected as well." ); ?></p>

<p><?php lang()->p( "This section displays details about the current page's author as well as links to more posts by that author. By default it appears below the post content but it can be displayed above the content if you prefer." ); ?></p>

<h3 class="form-heading"><?php $L->p( 'Author Details' ); ?></h3>

<p><?php lang()->p( 'The author details section displays the author\'s name and any biographical info in the custom bio field. If user profiles are enabled then links are included to the author\'s profile page.' ); ?></p>

<p><?php lang()->p( 'Options for the author details include avatar/photo, user role, website, email, and social media links.' ); ?></p>

<h3 class="form-heading"><?php $L->p( 'More Author Posts' ); ?></h3>

<p><?php lang()->p( 'The more posts section may be disabled, leaving only the author details. The More Posts sidebar widget may still be displayed with this section disabled.' ); ?></p>

<p><?php lang()->p( 'The posts list includes the post title, featured image thumbnail, the post description or excerpt, and the post date. There is an option for the number of recent posts to be displayed.' ); ?></p>

<p><?php lang()->p( 'When the more posts section is displayed it can be shown directly below the author details or switched via a tabbed layout.' ); ?></p>
