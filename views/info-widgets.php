<?php
/**
 * Widgets guide info
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
<h3 class="tab-section-heading"><?php lang()->p( 'Sidebar Widgets' ) ?></h3>

<p><?php lang()->p( 'The User Profiles plugin provides three optional widgets for the standard Bludit site sidebar, if the active theme supports it.' ); ?></p>
<p><?php lang()->p( 'The Bludit CMS only allows for one sidebar entry per plugin so all of this plugin\'s widgets are grouped together. However, a drag & drop system is provided to sort the appearance order of the active widgets.' ); ?></p>

<h3 class="form-heading"><?php $L->p( 'Profile Widget' ); ?></h3>

<p><?php lang()->p( "The profile widget only appears on individual pages, not loop pages. Choose posts, static pages, or both. It displays details about the current page's author. Links to the author's profile page are only active if profile pages are enabled." ); ?></p>
<p><?php lang()->p( 'See the Profiles tab for more information on enabling profile pages.' ); ?></p>

<h3 class="form-heading"><?php $L->p( 'More Posts Widget' ); ?></h3>

<p><?php lang()->p( "The more posts widget only appears on individual posts (default and sticky pages), not static or loop pages. It displays a simple list of links to more posts authored by the current post's author." ); ?></p>

<h3 class="form-heading"><?php $L->p( 'Users Widget' ); ?></h3>

<p><?php lang()->p( 'The users widget appears on all pages. It displays a list of all site users or select users. If profile pages are enabled then the user names are linked to their page.' ); ?></p>
