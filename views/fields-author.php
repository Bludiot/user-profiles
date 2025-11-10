<?php
/**
 * Author box options fields
 *
 * @package    User Profiles
 * @subpackage Views
 * @since      1.0.0
 */

// Access namespaced functions.
use function UPRO_Func\{
	plugin,
	site,
	lang
};

?>
<fieldset>
	<legend class="screen-reader-text mb-3"><?php $L->p( 'Author Options' ); ?></legend>

	<h3 class="tab-section-heading"><?php $L->p( 'Author Box' ); ?></h3>

	<div class="form-field form-group row">
		<label class="form-label col-sm-2 col-form-label" for="author_display"><?php $L->p( 'Author Box' ); ?></label>
		<div class="col-sm-10">
			<select class="form-select" id="author_display" name="author_display">

				<option value="post" <?php echo ( $this->getValue( 'author_display' ) === 'post' ? 'selected' : '' ); ?>><?php $L->p( 'Posts' ); ?></option>

				<option value="page" <?php echo ( $this->getValue( 'author_display' ) === 'page' ? 'selected' : '' ); ?>><?php $L->p( 'Pages' ); ?></option>

				<option value="both" <?php echo ( $this->getValue( 'author_display' ) === 'both' ? 'selected' : '' ); ?>><?php $L->p( 'Posts & Pages' ); ?></option>

				<option value="none" <?php echo ( $this->getValue( 'author_display' ) === 'none' ? 'selected' : '' ); ?>><?php $L->p( 'None' ); ?></option>
			</select>
			<small class="form-text"><?php $L->p( 'Page types on which to display the author/user profile box. Posts include sticky pages.' ); ?></small>
		</div>
	</div>

	<div id="author-box-wrap" style="display: <?php echo ( 'none' == $this->getValue( 'author_display' ) ? 'none' : 'block' ); ?>;">

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="author_location"><?php $L->p( 'Box Location' ); ?></label>
			<div class="col-sm-10">
				<select class="form-select" id="author_location" name="author_location">

					<option value="author_box" <?php echo ( $this->getValue( 'author_location' ) === 'author_box' ? 'selected' : '' ); ?>><?php $L->p( 'Use `author_box` hook.' ); ?></option>

					<option value="after" <?php echo ( $this->getValue( 'author_location' ) === 'after' ? 'selected' : '' ); ?>><?php $L->p( 'After Content' ); ?></option>

					<option value="before" <?php echo ( $this->getValue( 'author_location' ) === 'before' ? 'selected' : '' ); ?>><?php $L->p( 'Before Content' ); ?></option>
				</select>
				<small class="form-text"><?php $L->p( 'Where on the page to display the profile.' ); ?></small>
			</div>
		</div>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="author_avatar"><?php $L->p( 'Author Avatar' ); ?></label>
			<div class="col-sm-10">
				<select class="form-select" id="author_avatar" name="author_avatar">

					<option value="true" <?php echo ( $this->getValue( 'author_avatar' ) === true ? 'selected' : '' ); ?>><?php $L->p( 'Show' ); ?></option>

					<option value="false" <?php echo ( $this->getValue( 'author_avatar' ) === false ? 'selected' : '' ); ?>><?php $L->p( 'Hide' ); ?></option>
				</select>
				<small class="form-text"><?php $L->p( 'Display the user\'s avatar/profile picture.' ); ?></small>
			</div>
		</div>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="author_role"><?php $L->p( 'Author Role' ); ?></label>
			<div class="col-sm-10">
				<select class="form-select" id="author_role" name="author_role">

					<option value="true" <?php echo ( $this->getValue( 'author_role' ) === true ? 'selected' : '' ); ?>><?php $L->p( 'Show' ); ?></option>

					<option value="false" <?php echo ( $this->getValue( 'author_role' ) === false ? 'selected' : '' ); ?>><?php $L->p( 'Hide' ); ?></option>
				</select>
				<small class="form-text"><?php $L->p( 'Display the user\'s role in the website.' ); ?></small>
			</div>
		</div>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="author_email"><?php $L->p( 'Author Email' ); ?></label>
			<div class="col-sm-10">
				<select class="form-select" id="author_email" name="author_email">

					<option value="true" <?php echo ( $this->getValue( 'author_email' ) === true ? 'selected' : '' ); ?>><?php $L->p( 'Show' ); ?></option>

					<option value="false" <?php echo ( $this->getValue( 'author_email' ) === false ? 'selected' : '' ); ?>><?php $L->p( 'Hide' ); ?></option>
				</select>
				<small class="form-text"><?php $L->p( 'Display the user\'s email.' ); ?></small>
			</div>
		</div>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="author_site"><?php $L->p( 'Author Website' ); ?></label>
			<div class="col-sm-10">
				<select class="form-select" id="author_site" name="author_site">

					<option value="true" <?php echo ( $this->getValue( 'author_site' ) === true ? 'selected' : '' ); ?>><?php $L->p( 'Show' ); ?></option>

					<option value="false" <?php echo ( $this->getValue( 'author_site' ) === false ? 'selected' : '' ); ?>><?php $L->p( 'Hide' ); ?></option>
				</select>
				<small class="form-text"><?php $L->p( 'Display the user\'s website/link.' ); ?></small>
			</div>
		</div>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="author_social"><?php $L->p( 'Author Socials' ); ?></label>
			<div class="col-sm-10">
				<select class="form-select" id="author_social" name="author_social">

					<option value="true" <?php echo ( $this->getValue( 'author_social' ) === true ? 'selected' : '' ); ?>><?php $L->p( 'Show' ); ?></option>

					<option value="false" <?php echo ( $this->getValue( 'author_social' ) === false ? 'selected' : '' ); ?>><?php $L->p( 'Hide' ); ?></option>
				</select>
				<small class="form-text"><?php $L->p( 'Display the user\'s social media links.' ); ?></small>
			</div>
		</div>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="author_posts"><?php $L->p( 'Author Posts' ); ?></label>
			<div class="col-sm-10">
				<select class="form-select" id="author_posts" name="author_posts">

					<option value="true" <?php echo ( $this->getValue( 'author_posts' ) === true ? 'selected' : '' ); ?>><?php $L->p( 'Show' ); ?></option>

					<option value="false" <?php echo ( $this->getValue( 'author_posts' ) === false ? 'selected' : '' ); ?>><?php $L->p( 'Hide' ); ?></option>
				</select>
				<small class="form-text"><?php $L->p( 'Display a linked list of posts authored by the user.' ); ?></small>
			</div>
		</div>

		<div id="author-list-tabbed" class="form-field form-group row" style="display: <?php echo ( $this->getValue( 'author_posts' ) === true ? 'flex' : 'none' ); ?>;">
			<label class="form-label col-sm-2 col-form-label" for="author_tabbed"><?php $L->p( 'Tabbed Box' ); ?></label>
			<div class="col-sm-10">
				<select class="form-select" id="author_tabbed" name="author_tabbed">

					<option value="true" <?php echo ( $this->getValue( 'author_tabbed' ) === true ? 'selected' : '' ); ?>><?php $L->p( 'Enabled' ); ?></option>

					<option value="false" <?php echo ( $this->getValue( 'author_tabbed' ) === false ? 'selected' : '' ); ?>><?php $L->p( 'Disabled' ); ?></option>
				</select>
				<small class="form-text"><?php $L->p( 'Display user posts in a separate UI tab from user details.' ); ?></small>
			</div>
		</div>

		<div id="author-list-limit" class="form-field form-group row" style="display: <?php echo ( $this->getValue( 'author_posts' ) === true ? 'flex' : 'none' ); ?>;">
			<label class="form-label col-sm-2 col-form-label" for="author_limit"><?php $L->p( 'Posts Limit' ); ?></label>
			<div class="col-sm-10 row">
				<div class="form-range-controls">
					<span class="form-range-value"><span id="author_limit_value"><?php echo ( $this->getValue( 'author_limit' ) ? $this->getValue( 'author_limit' ) : $this->dbFields['author_limit'] ); ?></span></span>
					<input type="range" class="form-control-range custom-range" onInput="$('#author_limit_value').html($(this).val())" id="author_limit" name="author_limit" value="<?php echo $this->getValue( 'author_limit' ); ?>" min="1" max="20" step="1" />
					<span class="btn btn-secondary btn-md form-range-button hide-if-no-js" onClick="$('#author_limit_value').text('<?php echo $this->dbFields['author_limit']; ?>');$('#author_limit').val('<?php echo $this->dbFields['author_limit']; ?>');"><?php $L->p( 'Default' ); ?></span>
				</div>
				<small class="form-text"><?php $L->p( 'Sets a maximum number of posts to display. ' ); ?></small>
			</div>
		</div>
	</div>
</fieldset>

<script>
jQuery(document).ready( function($) {

	// Show/hide author box options.
	$( '#author_display' ).on( 'change', function() {
		var show = $(this).val();
		if ( 'none' == show ) {
			$( '#author-box-wrap' ).css( 'display', 'none' );
		} else {
			$( '#author-box-wrap' ).css( 'display', 'block' );
		}
	});

	// Show/hide author posts options.
	$( '#author_posts' ).on( 'change', function() {
		var show = $(this).val();
		if ( 'false' == show ) {
			$( '#author-list-tabbed' ).css( 'display', 'none' );
			$( '#author-list-limit' ).css( 'display', 'none' );
		} else {
			$( '#author-list-tabbed' ).css( 'display', 'flex' );
			$( '#author-list-limit' ).css( 'display', 'flex' );
		}
	});
});
</script>
