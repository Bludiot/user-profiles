<?php
/**
 * User Profiles profile
 *
 * WARNING: do not reuse the `$avatar` variable
 * because it is assigned to the AJAX image
 * upload classes.
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
	autop
};

// Guide page URL.
$guide_page = DOMAIN_ADMIN . 'plugin/User_Profiles';

// Section heading element depending on admin theme.
$sec_h_el = 'h3';
if ( 'configureight' === site()->adminTheme() ) {
	$sec_h_el = 'h2';
}

?>
<style>
.fa-cog:before { content: '\f369' !important }

.form-control-has-button {
	display: flex;
	align-items: center;
	flex-wrap: nowrap;
	gap: 0.25em;
	width: 100%;
	margin: 0;
	padding: 0;
}
.form-range-row {
	padding: 0 30px;
}
.form-range-controls {
	display: flex;
	align-items: center;
	flex-wrap: nowrap;
	gap: 1em;
	width: 100%;
	max-width: 640px;
	margin: 0;
	padding: 0;
}
.form-range-value {
	display: inline-block;
	min-width: 6ch;
	padding: 0.25em 0.5em;
	border: var( --cfe-form-element--border, var( --cfe-form-element--border, solid 1px #dee2e6 ) );
	text-align: center;
}
</style>
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
<div class="alert alert-primary alert-cats-list" role="alert">
	<p class="m-0"><?php $L->p( "Go to the <a href='{$guide_page}'>user profiles guide</a> page." ); ?></p>
</div>

<nav class="mb-3">
	<div class="nav nav-tabs" id="nav-tab" role="tablist">

		<a class="nav-item nav-link active" id="nav-bios-tab" data-toggle="tab" href="#bios" role="tab" aria-controls="nav-bios" aria-selected="false"><?php $L->p( 'Users' ); ?></a>

		<a class="nav-item nav-link" id="nav-general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="nav-general" aria-selected="false"><?php $L->p( 'General' ); ?></a>

		<a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="nav-profile" aria-selected="false"><?php $L->p( 'Profile' ); ?></a>

		<a class="nav-item nav-link" id="nav-author-tab" data-toggle="tab" href="#author" role="tab" aria-controls="nav-author" aria-selected="false"><?php $L->p( 'Author' ); ?></a>

		<a class="nav-item nav-link" id="nav-widgets-tab" data-toggle="tab" href="#widgets" role="tab" aria-controls="nav-widgets" aria-selected="false"><?php $L->p( 'Widgets' ); ?></a>
	</div>
</nav>

<div class="tab-content" id="nav-tabContent">

	<fieldset id="bios" class="tab-pane fade show mt-4 active" role="tabpanel" aria-labelledby="nav-bios-tab">
		<legend class="screen-reader-text mb-3"><?php $L->p( 'User Information' ); ?></legend>

		<h3 class="tab-section-heading"><?php $L->p( 'Website Users' ); ?></h3>

		<?php include( $this->phpPath() . '/views/fields-users.php' ); ?>
	</fieldset>

	<div id="general" class="tab-pane fade show mt-4" role="tabpanel" aria-labelledby="nav-general-tab">
		<?php include( $this->phpPath() . '/views/fields-general.php' ); ?>
	</div>

	<div id="profile" class="tab-pane fade show mt-4" role="tabpanel" aria-labelledby="nav-profile-tab">
		<?php include( $this->phpPath() . '/views/fields-profile.php' ); ?>
	</div>

	<div id="author" class="tab-pane fade show mt-4" role="tabpanel" aria-labelledby="nav-author-tab">
		<?php include( $this->phpPath() . '/views/fields-author.php' ); ?>
	</div>

	<div id="widgets" class="tab-pane fade show mt-4" role="tabpanel" aria-labelledby="nav-widgets-tab">
		<?php include( $this->phpPath() . '/views/fields-widget.php' ); ?>
	</div>

</div>

<script>
// Open current tab after refresh page.
$( function() {
	$( 'a[data-toggle="tab"]' ).on( 'click', function(e) {
		window.localStorage.setItem( 'profiles_active_tab', $( e.target ).attr( 'href' ) );
	});
	var profiles_active_tab = window.localStorage.getItem( 'profiles_active_tab' );
	if ( profiles_active_tab ) {
		$( '#nav-tab a[href="' + profiles_active_tab + '"]' ).tab( 'show' );
		window.localStorage.removeItem( 'profiles_active_tab' );
	}
});

jQuery(document).ready( function($) {

	// Tooltips.
	$( '.form-tooltip' ).tooltipster({
		distance : 5,
		delay : 150,
		animationDuration : 150,
		theme : 'upro-tooltips'
	});
	$( '.image-in-album' ).tooltipster({
		distance : 5,
		delay : 150,
		animationDuration : 150,
		theme : 'upro-tooltips'
	});
});
</script>