<?php
/**
 * User Profiles guide
 *
 * @package    User Profiles
 * @subpackage Views
 * @category   Guides
 * @since      1.0.0
 */

// Form page URL.
$form_page = DOMAIN_ADMIN . 'configure-plugin/' . $this->className();

?>
<h1><span class="page-title-icon fa fa-book"></span> <span class="page-title-text"><?php $L->p( 'User Profiles Guide' ) ?></span></h1>

<div class="alert alert-primary alert-cats-list" role="alert">
	<p class="m-0"><?php $L->p( "Go to the <a href='{$form_page}'>user profiles options</a> page." ); ?></p>
</div>

<nav class="mb-3">
	<div class="nav nav-tabs" id="nav-tab" role="tablist">
		<a class="nav-item nav-link active" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false"><?php $L->p( 'Profiles' ); ?></a>

		<a class="nav-item nav-link" id="nav-author-tab" data-toggle="tab" href="#author" role="tab" aria-controls="nav-author" aria-selected="false"><?php $L->p( 'Author' ); ?></a>

		<a class="nav-item nav-link" id="nav-widgets-tab" data-toggle="tab" href="#widgets" role="tab" aria-controls="nav-widgets" aria-selected="false"><?php $L->p( 'Widgets' ); ?></a>
	</div>
</nav>

<div class="tab-content" id="nav-tabContent">
	<div id="profile" class="tab-pane active fade show mt-4" role="tabpanel" aria-labelledby="nav-profile-tab">
		<?php include( $this->phpPath() . '/views/info-profile.php' ); ?>
	</div>

	<div id="author" class="tab-pane fade show mt-4" role="tabpanel" aria-labelledby="nav-author-tab">
		<?php include( $this->phpPath() . '/views/info-author.php' ); ?>
	</div>

	<div id="widgets" class="tab-pane fade show mt-4" role="tabpanel" aria-labelledby="nav-widgets-tab">
		<?php include( $this->phpPath() . '/views/info-widgets.php' ); ?>
	</div>
</div>

<script>
// Open current tab after refresh page.
$( function() {
	$( 'a[data-toggle="tab"]' ).on( 'click', function(e) {
		window.localStorage.setItem( 'profiles_guide_active_tab', $( e.target ).attr( 'href' ) );
	});
	var active_tab = window.localStorage.getItem( 'profiles_guide_active_tab' );
	if ( active_tab ) {
		$( '#nav-tab a[href="' + active_tab + '"]' ).tab( 'show' );
		window.localStorage.removeItem( 'profiles_guide_active_tab' );
	}
});
</script>
