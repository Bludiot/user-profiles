<?php
/**
 * Widget options fields
 *
 * @package    User Profiles
 * @subpackage Views
 * @since      1.0.0
 */

// Access namespaced functions.
use function UPRO_Func\{
	plugin,
	site,
	lang,
	usernames,
	user,
	count_widgets,
	active_widgets
};

?>
<fieldset id="users-widgets-general">
	<legend class="screen-reader-text mb-3"><?php $L->p( 'Widget Options' ); ?></legend>

	<h3 class="tab-section-heading"><?php $L->p( 'Widget Options' ); ?></h3>

	<div class="form-field form-group row">
		<label class="form-label col-sm-2 col-form-label" for="widgets_label"><?php $L->p( 'Headings' ); ?></label>
		<div class="col-sm-10">
			<div class="form-control-has-button">
				<input type="text" id="widgets_label" name="widgets_label" value="<?php echo $this->getValue( 'widgets_label' ); ?>" placeholder="<?php $L->p( 'h2' ); ?>" />
				<span class="btn btn-secondary btn-md button hide-if-no-js" onClick="$('#widgets_label').val('<?php echo $this->dbFields['widgets_label']; ?>');"><?php $L->p( 'Default' ); ?></span>
			</div>
			<small class="form-text text-muted"><?php $L->p( 'Wrap the widget headings in an HTML element. Accepts HTML tags without brackets (e.g. h3), and comma-separated tags (e.g. span,strong,em). Save as blank for no wrapping element.' ); ?></small>
		</div>
	</div>
</fieldset>

<fieldset id="users-widgets-profile">
	<legend class="screen-reader-text mb-3"><?php $L->p( 'Profile Widget' ); ?></legend>

	<h3 class="tab-section-heading"><?php $L->p( 'Profile Widget' ); ?></h3>

	<p class="tab-section-description"><?php $L->p( 'Display an author widget in the frontend sidebar on posts (default and sticky pages, not static or loop  pages).' ); ?></p>

	<div class="form-field form-group row">
		<label class="form-label col-sm-2 col-form-label" for="sidebar_bio"><?php $L->p( 'Author Profile' ); ?></label>
		<div class="col-sm-10">
			<select class="form-select" id="sidebar_bio" name="sidebar_bio">

				<option value="false" <?php echo ( $this->getValue( 'sidebar_bio' ) === false ? 'selected' : '' ); ?>><?php $L->p( 'Disabled' ); ?></option>

				<option value="true" <?php echo ( $this->getValue( 'sidebar_bio' ) === true ? 'selected' : '' ); ?>><?php $L->p( 'Enabled' ); ?></option>
			</select>
		</div>
	</div>

	<div id="widget-profile-wrap" style="display: <?php echo ( $this->getValue( 'sidebar_bio' ) === true ? 'block' : 'none' ); ?>;">

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="sidebar_avatar"><?php $L->p( 'Author Avatar' ); ?></label>
			<div class="col-sm-10">
				<select class="form-select" id="sidebar_avatar" name="sidebar_avatar">

					<option value="false" <?php echo ( $this->getValue( 'sidebar_avatar' ) === false ? 'selected' : '' ); ?>><?php $L->p( 'Disabled' ); ?></option>

					<option value="true" <?php echo ( $this->getValue( 'sidebar_avatar' ) === true ? 'selected' : '' ); ?>><?php $L->p( 'Enabled' ); ?></option>
				</select>
				<small class="form-text"><?php $L->p( 'Display author avatar in the bio.' ); ?></small>
			</div>
		</div>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="sb_bio_limit"><?php $L->p( 'Bio Limit' ); ?></label>
			<div class="col-sm-10 row">
				<div class="form-range-controls">
					<span class="form-range-value"><span id="sb_bio_limit_value"><?php echo ( $this->getValue( 'sb_bio_limit' ) ? $this->getValue( 'sb_bio_limit' ) : $this->dbFields['sb_bio_limit'] ); ?></span></span>
					<input type="range" class="form-control-range custom-range" onInput="$('#sb_bio_limit_value').html($(this).val())" id="sb_bio_limit" name="sb_bio_limit" value="<?php echo $this->getValue( 'sb_bio_limit' ); ?>" min="60" max="400" step="5" />
					<span class="btn btn-secondary btn-md form-range-button hide-if-no-js" onClick="$('#sb_bio_limit_value').text('<?php echo $this->dbFields['sb_bio_limit']; ?>');$('#sb_bio_limit').val('<?php echo $this->dbFields['sb_bio_limit']; ?>');"><?php $L->p( 'Default' ); ?></span>
				</div>
				<small class="form-text"><?php $L->p( 'Sets a maximum number of characters to display before the profile link. ' ); ?></small>
			</div>
		</div>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="sidebar_links"><?php $L->p( 'Author Links' ); ?></label>
			<div class="col-sm-10">
				<select class="form-select" id="sidebar_links" name="sidebar_links">

					<option value="false" <?php echo ( $this->getValue( 'sidebar_links' ) === false ? 'selected' : '' ); ?>><?php $L->p( 'Disabled' ); ?></option>

					<option value="true" <?php echo ( $this->getValue( 'sidebar_links' ) === true ? 'selected' : '' ); ?>><?php $L->p( 'Enabled' ); ?></option>
				</select>
				<small class="form-text"><?php $L->p( 'Display author personal links in the bio.' ); ?></small>
			</div>
		</div>
	</div>
</fieldset>

<fieldset id="users-widgets-posts">
	<legend class="screen-reader-text mb-3"><?php $L->p( 'More Posts Widget' ); ?></legend>

	<h3 class="tab-section-heading"><?php $L->p( 'More Posts Widget' ); ?></h3>

	<p class="tab-section-description"><?php $L->p( 'Display a widget of more posts by the author in the frontend sidebar on posts (default and sticky pages, not static or loop  pages).' ); ?></p>

	<div class="form-field form-group row">
		<label class="form-label col-sm-2 col-form-label" for="sidebar_more"><?php $L->p( 'More Posts' ); ?></label>
		<div class="col-sm-10">
			<select class="form-select" id="sidebar_more" name="sidebar_more">

				<option value="false" <?php echo ( $this->getValue( 'sidebar_more' ) === false ? 'selected' : '' ); ?>><?php $L->p( 'Disabled' ); ?></option>

				<option value="true" <?php echo ( $this->getValue( 'sidebar_more' ) === true ? 'selected' : '' ); ?>><?php $L->p( 'Enabled' ); ?></option>
			</select>
		</div>
	</div>

	<div id="widget-posts-wrap" class="form-field form-group row" style="display: <?php echo ( $this->getValue( 'sidebar_more' ) === true ? 'flex' : 'none' ); ?>;">
		<label class="form-label col-sm-2 col-form-label" for="sidebar_limit"><?php $L->p( 'List Limit' ); ?></label>
		<div class="col-sm-10 row">
			<div class="form-range-controls">
				<span class="form-range-value"><span id="sidebar_limit_value"><?php echo ( $this->getValue( 'sidebar_limit' ) ? $this->getValue( 'sidebar_limit' ) : $this->dbFields['sidebar_limit'] ); ?></span></span>
				<input type="range" class="form-control-range custom-range" onInput="$('#sidebar_limit_value').html($(this).val())" id="sidebar_limit" name="sidebar_limit" value="<?php echo $this->getValue( 'sidebar_limit' ); ?>" min="1" max="20" step="1" />
				<span class="btn btn-secondary btn-md form-range-button hide-if-no-js" onClick="$('#sidebar_limit_value').text('<?php echo $this->dbFields['sidebar_limit']; ?>');$('#sidebar_limit').val('<?php echo $this->dbFields['sidebar_limit']; ?>');"><?php $L->p( 'Default' ); ?></span>
			</div>
			<small class="form-text"><?php $L->p( 'Sets a maximum number of posts to display in the links list. ' ); ?></small>
		</div>
	</div>
</fieldset>

<fieldset id="profiles-list-widget" style="display: <?php echo ( true == $this->profile_pages() ? 'block' : 'none' ); ?>;">
	<legend class="screen-reader-text mb-3"><?php $L->p( 'Users Widget' ); ?></legend>

	<h3 class="tab-section-heading"><?php $L->p( 'Users Widget' ); ?></h3>

	<p class="tab-section-description"><?php $L->p( 'Display a list widget of users in the frontend sidebar, with links to their profile pages.' ); ?></p>

	<div class="form-field form-group row">
		<label class="form-label col-sm-2 col-form-label" for="sidebar_list"><?php $L->p( 'Users List' ); ?></label>
		<div class="col-sm-10">
			<select class="form-select" id="sidebar_list" name="sidebar_list">

				<option value="false" <?php echo ( $this->getValue( 'sidebar_list' ) === false ? 'selected' : '' ); ?>><?php $L->p( 'Disabled' ); ?></option>

				<option value="true" <?php echo ( $this->getValue( 'sidebar_list' ) === true ? 'selected' : '' ); ?>><?php $L->p( 'Enabled' ); ?></option>
			</select>
		</div>
	</div>

	<div id="widget-list-wrap" style="display: <?php echo ( $this->getValue( 'sidebar_list' ) === true ? 'block' : 'none' ); ?>;">

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="sb_list_label"><?php $L->p( 'List Heading' ); ?></label>
			<div class="col-sm-10">
				<input type="text" id="sb_list_label" name="sb_list_label" value="<?php echo $this->getValue( 'sb_list_label' ); ?>" placeholder="<?php echo $this->dbFields['sb_list_label']; ?>" />
				<small class="form-text">
					<?php $L->p( 'The heading text for the users list.' ); ?>
				</small>
			</div>
		</div>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="sb_list_avatar"><?php $L->p( 'User Avatars' ); ?></label>
			<div class="col-sm-10">
				<select class="form-select" id="sb_list_avatar" name="sb_list_avatar">

					<option value="false" <?php echo ( $this->getValue( 'sb_list_avatar' ) === false ? 'selected' : '' ); ?>><?php $L->p( 'Disabled' ); ?></option>

					<option value="true" <?php echo ( $this->getValue( 'sb_list_avatar' ) === true ? 'selected' : '' ); ?>><?php $L->p( 'Enabled' ); ?></option>
				</select>
				<small class="form-text"><?php $L->p( 'Display user avatars in the list.' ); ?></small>
			</div>
		</div>
	</div>
	<?php if ( count_widgets() > 0 ) : ?>

	<h3 class="tab-section-heading"><?php $L->p( 'Sidebar Order' ); ?></h3>

	<p class="tab-section-description"><?php $L->p( 'Sort the order of user widgets in the sidebar. If a widget is enabled or disabled then save settings before sorting the widget order.' ); ?></p>

	<div class="form-field form-group row">
		<label class="form-label col-sm-2 col-form-label" for="user-widgets-sort"><?php $L->p( 'Sort Widgets' ); ?></label>
		<div class="col-sm-10">
			<?php
			$active = active_widgets();
			$sort   = plugin()->sb_widgets_sort();
			if ( ! empty( $sort ) ) {
				$order = explode( ',', $sort );
				$list  = array_replace( array_flip( $order ), $active );
			} else {
				$list = $active;
			} ?>
			<input type="hidden" id="sb_widgets_sort" name="sb_widgets_sort" value="<?php echo $sort; ?>" />

			<ul id="user-widgets-sort" class="list-group list-group-sortable">
			<?php
			foreach ( $list as $widget => $name ) {
				if ( ! array_key_exists( $widget, $active ) ) {
					continue;
				}
				echo '<li class="list-group-item" data-widget="' . $widget . '"><span class="fa fa-arrows-v"></span> ' . $name . '</li>';
			} ?>
			</ul>
			<small><?php $L->p( 'Drag & drop to set the appearance order of widgets. This order is relative to this plugin\'s widgets. All User Profiles widgets appear relative to other plugins in the sidebar, as ordered in the Bludit setting.' ); ?></small>
		</div>
	</div>
	<script>
	$(document).ready( function() {
		$( '.list-group-sortable' ).sortable({
			placeholderClass: 'list-group-item'
		});

		$( '#jsform button[type="submit"]' ).on( 'click', function() {
			var tmp = [];

			$( 'li.list-group-item' ).each( function() {
				tmp.push( $(this).attr( 'data-widget' ) );
			});
			$( '#sb_widgets_sort' ).attr( 'value', tmp.join( ',' ) );
			$( '#jsform' ).submit();
		});
	});
	</script>
	<?php endif; ?>
</fieldset>

<script>
jQuery(document).ready( function($) {

	// Show/hide widget options.
	$( '#sidebar_bio' ).on( 'change', function() {
		var show = $(this).val();
		if ( 'true' == show ) {
			$( '#widget-profile-wrap' ).css( 'display', 'block' );
		} else if ( 'false' == show ) {
			$( '#widget-profile-wrap' ).css( 'display', 'none' );
		}
	});

	// Show/hide sidebar list options.
	$( '#sidebar_more' ).on( 'change', function() {
		var show = $(this).val();
		if ( 'true' == show ) {
			$( '#widget-posts-wrap' ).css( 'display', 'flex' );
		} else if ( 'false' == show ) {
			$( '#widget-posts-wrap' ).css( 'display', 'none' );
		}
	});

	// Show/hide profile options.
	$( '#profile_pages' ).on( 'change', function() {
		var show = $(this).val();
		if ( 'true' == show ) {
			$( '#profiles-list-widget' ).css( 'display', 'block' );
		} else if ( 'false' == show ) {
			$( '#profiles-list-widget' ).css( 'display', 'none' );
		}
	});

	// Show/hide list options.
	$( '#sidebar_list' ).on( 'change', function() {
		var show = $(this).val();
		if ( 'true' == show ) {
			$( '#widget-list-wrap' ).css( 'display', 'block' );
		} else if ( 'false' == show ) {
			$( '#widget-list-wrap' ).css( 'display', 'none' );
		}
	});
});
</script>
