<?php
/**
 * General options fields
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

// Path to default avatars.
$avatars_path = plugin()->phpPath() . 'assets/images/avatars' . DS;

?>
<fieldset>
	<legend class="screen-reader-text mb-3"><?php $L->p( 'Options' ); ?></legend>

	<h3 class="tab-section-heading"><?php $L->p( 'General Options' ); ?></h3>

	<div class="form-field form-group row">
		<label class="form-label col-sm-2 col-form-label" for="users_slug"><?php $L->p( 'Users Slug' ); ?></label>
		<div class="col-sm-10">
			<input type="text" id="users_slug" name="users_slug" value="<?php echo ( $this->getValue( 'users_slug' ) ? $this->getValue( 'users_slug' ) : $this->dbFields['users_slug'] ); ?>" placeholder="<?php echo $this->dbFields['users_slug']; ?>" />
			<small class="form-text"><?php echo $L->get( 'The URL slug for user pages. Default: ' ) . site()->url() . '<strong>' . $this->dbFields['users_slug'] . '</strong>/username '; ?></small>
		</div>
	</div>

	<div class="form-field form-group row">
		<label class="form-label col-sm-2 col-form-label" for="default_avatar"><?php $L->p( 'Default Avatar' ); ?></label>
		<div class="col-sm-10">
			<select class="form-select" id="default_avatar" name="default_avatar">

				<?php foreach ( glob( $avatars_path . "*.{png,jpeg,jpeg,gif,svg,PNG,JPG,JPEG,GIF,SVG}", GLOB_BRACE ) as $default_avatar ) {

					$avatar_id = str_replace( '.', '', basename( $default_avatar, pathinfo( $default_avatar, PATHINFO_EXTENSION ) ) );

					printf(
						'<option value="%s"%s>%s</option>',
						$avatar_id,
						( $avatar_id == $this->getValue( 'default_avatar' ) ? ' selected' : '' ),
						ucwords( str_replace( [ '-', '_' ], ' ', $avatar_id ) )
					);
				} ?>
				<option value="custom" <?php echo ( $this->getValue( 'default_avatar' ) === 'custom' ? ' selected' : '' ); ?>><?php $L->p( 'Custom' ); ?></option>
			</select>
			<small class="form-text"><?php $L->p( 'Choose the fallback avatar for users without a profile image.' ); ?></small>
			<?php
			foreach ( glob( $avatars_path . "*.{png,jpeg,jpeg,gif,svg,PNG,JPG,JPEG,GIF,SVG}", GLOB_BRACE ) as $default_avatar ) :

				$avatar_src = plugin()->domainPath() . 'assets/images/avatars/' . basename( $default_avatar );
				$avatar_id  = str_replace( '.', '', basename( $default_avatar, pathinfo( $default_avatar, PATHINFO_EXTENSION ) ) );

			?>
			<figure id="<?php echo $avatar_id; ?>" class="default-avatar-preview" style="display: <?php echo( $avatar_id == $this->default_avatar() ? 'block' : 'none' ); ?>;">
				<img class="avatar" src="<?php echo $avatar_src; ?>" width="80" height="80" />
			</figure>
			<?php endforeach; ?>
		</div>
	</div>

	<div id="custom-avatar-wrap" class="form-field form-group row" style="display: <?php echo ( 'custom' === $this->getValue( 'default_avatar' ) ? 'flex' : 'none' ) ?>;">
		<label class="form-label col-sm-2 col-form-label" for="custom_avatar"><?php $L->p( 'Custom Avatar' ); ?></label>
		<div class="col-sm-10">
			<p><?php $L->p( 'Custom fallback avatar for users without a profile image.' ); ?></p>

			<div id="avatar-tabs" class="tab-content" data-toggle="tabslet" data-deeplinking="false" data-animation="true">

				<ul class="nav nav-tabs" id="avatar-nav-tabs" role="tablist">
					<li class="nav-item">
						<a class="nav-link" role="tab" aria-controls="avatar-select" aria-selected="false" href="#avatar-select"><?php $L->p( 'Select' ); ?></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" role="tab" aria-controls="avatar-upload" aria-selected="false" href="#avatar-upload"><?php $L->p( 'Upload' ); ?></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" role="tab" aria-controls="avatar-album" aria-selected="false" href="#avatar-album"><?php $L->p( 'Album' ); ?></a>
					</li>
				</ul>
				<div id="avatar-select" role="tabpanel" aria-labelledby="avatar-select">
					<p><?php $L->p( 'Select one from uploaded avatar images.' ); ?></p>
					<?php echo $avatars->select_images( $avatar ); ?>
				</div>

				<div id="avatar-upload" class="tab-pane tab-pane-image-upload" role="tabpanel" aria-labelledby="avatar-upload">

					<p><?php $L->p( 'Drag & drop images or click to browse. Allowed file types: .jpg, .jpeg, .png, .gif' ); ?></p>

					<div class="dropzone" id="avatar-upload"></div>

					<div id="avatar-upload-notice" style="display: none;">
						<p><?php $L->p( '<strong>Note:</strong> this page needs to be refreshed before new images can be managed or selected as a avatar image.' ); ?></p>
						<p><button class="button button-small btn btn-sm btn-primary" onClick="location.reload();"><?php $L->p( 'Refresh' ); ?></button></p>
					</div>

				</div>

				<div id="avatar-album" class="tab-pane tab-pane-image-upload" role="tabpanel" aria-labelledby="avatar-album">
					<p><?php $L->p( 'Manage uploaded avatar images.' ); ?></p>
					<div id="avatar-album-wrap"><?php echo $avatars->manage_images( $avatar ); ?></div>
				</div>
			</div>
		</div>
	</div>

	<div class="form-field form-group row">
		<label class="form-label col-sm-2 col-form-label" for=""><?php $L->p( 'Cover Images' ); ?></label>

		<div class="col-sm-10 cover-form">

			<p><?php $L->p( 'Album of cover images available for selection in settings for each user.' ); ?></p>

			<div id="cover-tabs" class="tab-content" data-toggle="tabslet" data-deeplinking="false" data-animation="true">

				<ul class="nav nav-tabs" id="cover-nav-tabs" role="tablist">
					<li class="nav-item">
						<a class="nav-link" role="tab" aria-controls="cover-select" aria-selected="false" href="#cover-select"><?php $L->p( 'Default' ); ?></a>
					</li>
					<li class="nav-item">
						<a id="cover-album-tab" class="nav-link" role="tab" aria-controls="cover-album" aria-selected="false" href="#cover-album"><?php $L->p( 'Album' ); ?><span id="cover-images-count"><span><?php echo ' (' . $covers->count_images() . ')'; ?></span></span></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" role="tab" aria-controls="cover-upload" aria-selected="false" href="#cover-upload"><?php $L->p( 'Upload' ); ?></a>
					</li>
				</ul>

				<div id="cover-select" role="tabpanel" aria-labelledby="cover-select">
					<p><?php $L->p( 'Select default cover from uploaded images.' ); ?></p>
					<?php echo $covers->default_cover( $cover ); ?>
				</div>
				<div id="cover-album" class="tab-pane tab-pane-image-upload" role="tabpanel" aria-labelledby="cover-album">
					<div>
						<p><?php $L->p( 'Manage uploaded cover images.' ); ?></p>
						<div id="cover-album-wrap"><?php echo $covers->manage_images( $cover ); ?></div>
					</div>
					<div id="cover-album-empty" class="upload-album-empty" style="display: <?php echo ( $covers->count_images() > 0 ? 'none' : 'flex' ); ?>"><p><?php $L->p( 'No images uploaded' ) ?></p></div>
				</div>

				<div id="cover-upload" class="tab-pane tab-pane-image-upload" role="tabpanel" aria-labelledby="cover-upload">

					<p><?php $L->p( 'Drag & drop images or click to browse. Allowed file types: .jpg, .jpeg, .png' ); ?></p>

					<div class="dropzone" id="cover-upload"></div>

					<div id="cover-upload-notice" style="display: none;">
						<p><?php $L->p( '<strong>Note:</strong> this page needs to be refreshed before new images can be managed or selected as cover images.' ); ?></p>
						<p><button class="button button-small btn btn-sm btn-primary" onClick="location.reload();"><?php $L->p( 'Refresh' ); ?></button></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</fieldset>

<script>
$( function() {
	$( '.delete-avatar' ).bind( 'click', function() {
		if ( ! confirm( '<?php $L->p( 'Are you sure you want to delete this image?' ); ?>' ) ) { return; }
		deleteAvatar(this);
	});
});

function deleteAvatar(el) {
	$.post( avatar.config.ajaxUrl, {
		tokenCSRF : $( '#jstokenCSRF' ).val(),
		action    : 'deleteImage',
		album     : $(el).data( 'album' ),
		file      : $(el).data( 'file' )
	},
	function() {
		let manage = '#avatar-image-' + $(el).data( 'number' );
		let select = '#avatar-select-item-' + $(el).data( 'number' );
		let input  = '#avatar-select-item-' + $(el).data( 'number' ) + ' input';
		$( manage ).fadeOut( 450 );
		$( select ).hide();
		$( input ).removeAttr( 'checked' );

	}).fail( function() {
		$.alert({
			title   : avatar.L.error,
			content : avatar.L.deleteImageError
		});
	});
}
</script>

<script>
jQuery(document).ready( function($) {

	// Show/hide custom avatar.
	$( '#default_avatar' ).on( 'change', function() {
		var show = $(this).val();
		if ( 'custom' == show ) {
			$( '#custom-avatar-wrap' ).css( 'display', 'flex' );
		} else {
			$( '#custom-avatar-wrap' ).css( 'display', 'none' );
		}

		<?php
		foreach ( glob( $avatars_path . "*.{png,jpeg,jpeg,PNG,JPG,JPEG}", GLOB_BRACE ) as $default_avatar ) :
		$avatar_id  = str_replace( '.', '', basename( $default_avatar, pathinfo( $default_avatar, PATHINFO_EXTENSION ) ) ); ?>
		if ( '<?php echo $avatar_id; ?>' == show ) {
			$( '#<?php echo $avatar_id; ?>' ).css( 'display', 'block' );
		} else {
			$( '#<?php echo $avatar_id; ?>' ).css( 'display', 'none' );
		}
		<?php endforeach; ?>
	});

	// Selected class for image uploads.
	$( '.avatar-select-label' ).click( function() {
		$( '.avatar-select-label' ).removeClass( 'selected' );
		$(this).addClass( 'selected' );
	});

	// Selected class for image uploads.
	$( '.cover-select-label' ).click( function() {
		$( '.cover-select-label' ).removeClass( 'selected' );
		$(this).addClass( 'selected' );
	});
});
</script>

<script>
$( function() {
	$( '.delete-cover' ).bind( 'click', function() {
		if ( ! confirm( '<?php $L->p( 'Are you sure you want to delete this image?' ); ?>' ) ) { return; }
		deleteCover(this);
		$( "#cover-images-count" ).load( window.location.href + " #cover-images-count > span" );
	});
});

function deleteCover(el) {
	$.post( cover.config.ajaxUrl, {
		tokenCSRF : $( '#jstokenCSRF' ).val(),
		action    : 'deleteImage',
		album     : $(el).data( 'album' ),
		file      : $(el).data( 'file' )
	},
	function() {
		let manage = '#cover-image-' + $(el).data( 'number' );
		let select = '#cover-select-item-' + $(el).data( 'number' );
		let input  = '#cover-select-item-' + $(el).data( 'number' ) + ' input';
		$( manage ).fadeOut( 450, function() {
			$(this).remove();
		} );
		$( select ).hide();
		$( input ).removeAttr( 'checked' );

	}).fail( function() {
		$.alert({
			title   : cover.L.error,
			content : cover.L.deleteImageError
		});
	});
}
</script>
