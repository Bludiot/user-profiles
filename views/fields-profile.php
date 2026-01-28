<?php
/**
 * Profile options fields
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
	user_slug,
	user,
	default_cover
};
use function UPRO_Tags\{
	user_display_name,
	user_avatar,
	default_avatar
};

$user   = user( 'admin' );
$name   = $user->username();
$header = plugin()->header_style();
$theme  = getPlugin( 'configureight' );

$preview_class = 'in-form-preview';
if ( $theme ) {
	if ( 'default' == $theme->admin_theme() ) {
		$preview_class = 'in-form-preview default-admin-theme';
	}
}

// Path to default avatars.
$avatars_path = plugin()->phpPath() . 'assets/images/avatars' . DS;

?>
<fieldset>
	<legend class="screen-reader-text mb-3"><?php $L->p( 'Profile Options' ); ?></legend>

	<h3 class="tab-section-heading"><?php $L->p( 'Profile Options' ); ?></h3>

	<p><?php $L->p( 'Read the User Profiles guide for implementing profile pages in your theme.' ); ?></p>

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

	<div class="form-field form-group row">
		<label class="form-label col-sm-2 col-form-label" for="profile_pages"><?php $L->p( 'Profile Pages' ); ?></label>
		<div class="col-sm-10">
			<select class="form-select" id="profile_pages" name="profile_pages">

				<option value="true" <?php echo ( $this->getValue( 'profile_pages' ) === true ? 'selected' : '' ); ?>><?php $L->p( 'Enabled' ); ?></option>

				<option value="false" <?php echo ( $this->getValue( 'profile_pages' ) === false ? 'selected' : '' ); ?>><?php $L->p( 'Disabled' ); ?></option>
			</select>
			<small class="form-text"><?php $L->p( 'Whether to allow user profile pages and include links.' ); ?></small>
		</div>
	</div>

	<div id="profile-options-wrap" style="display: <?php echo ( true == $this->profile_pages() ? 'block' : 'none' ); ?>;">

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="users_slug"><?php $L->p( 'Users Slug' ); ?></label>
			<div class="col-sm-10">
				<input type="text" id="users_slug" name="users_slug" value="<?php echo ( $this->getValue( 'users_slug' ) ? $this->getValue( 'users_slug' ) : $this->dbFields['users_slug'] ); ?>" placeholder="<?php echo $this->dbFields['users_slug']; ?>" />
				<small class="form-text"><?php echo $L->get( 'The URL slug for user profile pages. Default: ' ) . site()->url() . '<strong>' . $this->dbFields['users_slug'] . '</strong>/username '; ?></small>
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
							<a class="nav-link" role="tab" aria-controls="avatar-album" aria-selected="false" href="#avatar-album"><?php $L->p( 'Album' ); ?><span><?php echo ' (' . $avatars->count_images() . ')'; ?></span></span></a>
						</li>
						<li class="nav-item">
							<a class="nav-link" role="tab" aria-controls="avatar-upload" aria-selected="false" href="#avatar-upload"><?php $L->p( 'Upload' ); ?><span id="avatar-images-count"></a>
						</li>
					</ul>
					<div id="avatar-select" role="tabpanel" aria-labelledby="avatar-select">
						<p><?php $L->p( 'Select one from uploaded avatar images.' ); ?></p>
						<?php echo $avatars->select_images( $avatar ); ?>
					</div>

					<div id="avatar-album" class="tab-pane tab-pane-image-upload" role="tabpanel" aria-labelledby="avatar-album">
						<p><?php $L->p( 'Manage uploaded avatar images.' ); ?></p>
						<div id="avatar-album-wrap"><?php echo $avatars->manage_images( $avatar ); ?></div>
					</div>

					<div id="avatar-upload" class="tab-pane tab-pane-image-upload" role="tabpanel" aria-labelledby="avatar-upload">

						<p><?php $L->p( 'Drag & drop images or click to browse. Allowed file types: .jpg, .jpeg, .png, .gif' ); ?></p>

						<div class="dropzone" id="avatar-upload"></div>

						<div id="avatar-upload-notice" style="display: none;">
							<p><?php $L->p( '<strong>Note:</strong> this page needs to be refreshed before new images can be managed or selected as a avatar image.' ); ?></p>
							<p><button class="button button-small btn btn-sm btn-primary" onClick="location.reload();"><?php $L->p( 'Refresh' ); ?></button></p>
						</div>

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

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="header_style"><?php $L->p( 'Header Style' ); ?></label>
			<div class="col-sm-10">
				<select class="form-select" id="header_style" name="header_style">

					<option value="one" <?php echo ( $this->getValue( 'header_style' ) === 'one' ? 'selected' : '' ); ?>><?php $L->p( 'One' ); ?></option>
					<option value="two" <?php echo ( $this->getValue( 'header_style' ) === 'two' ? 'selected' : '' ); ?>><?php $L->p( 'Two' ); ?></option>
					<option value="three" <?php echo ( $this->getValue( 'header_style' ) === 'three' ? 'selected' : '' ); ?>><?php $L->p( 'Three' ); ?></option>
					<option value="four" <?php echo ( $this->getValue( 'header_style' ) === 'four' ? 'selected' : '' ); ?>><?php $L->p( 'Four' ); ?></option>
					<?php
					$count   = 0;
					$headers = glob( $this->phpPath() . 'assets/images/profile-headers/*.png', GLOB_BRACE );

					foreach ( $headers as $header ) {

						$count++;
						$style = str_replace( '.', '', basename( $header, pathinfo( $header,  PATHINFO_EXTENSION ) ) );
						$style = str_replace( $count . '-', '', $style );
						printf(
							'<option value="%s" %s>%s</option>',
							$style,
							( $this->getValue( 'header_style' ) === $style ? 'selected' : '' ),
							ucwords( $style )
						);
					} ?>
				</select>
				<small class="form-text"><?php $L->p( 'Choose the layout for the header on user profile pages.' ); ?></small>

				<?php
				// Header preview.
				$covers  = glob( $this->phpPath() . 'assets/images/header-preview/*.jpg', GLOB_BRACE );
				$avatar  = $this->domainPath() . 'assets/images/avatars/user.png';
				$preview = [];
				$random  = '';

				foreach ( $covers as $cover ) {
					$preview[] = $this->domainPath() . 'assets/images/header-preview/' . basename( $cover );
				}
				if ( default_cover() ) {
					$cover_src = default_cover();
				} else {
					$random    = array_rand( $preview );
					$cover_src = $preview[$random];
				} ?>
				<div id="header-preview" class="profile-page profile-style-<?php echo $this->header_style(); ?> <?php echo $preview_class; ?>">
					<header id="profile-page-header" class="profile-page-header" data-page-header>
						<div class="profile-cover-wrap cover-overlay">
							<figure class="profile-cover <?php echo $preview_class; ?>">
								<img src="<?php echo $cover_src; ?>" role="presentation" />
							</figure>
						</div>
						<div class="profile-header-inner">
							<div class="profile-intro <?php echo $preview_class; ?>">
								<h2><?php lang()->p( 'User Name' ); ?></h2>
								<p><?php lang()->p( 'User tagline or short description' ); ?></p>
							</div>
							<figure class="profile-avatar <?php echo $preview_class; ?>">
								<img id="radius-preview" src="<?php echo default_avatar(); ?>" width="130" height="130" />
								<figcaption class="screen-reader-text"><?php lang()->p( 'User Name' ); ?></figcaption>
							</figure>
						</div>
					</header>
				</div>
			</div>
		</div>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="bio_avatar_radius"><?php $L->p( 'Avatar Radius' ); ?></label>
			<div class="col-sm-10 row">
				<div class="form-range-controls">
					<span class="form-range-value"><span id="content_width_value"><?php echo ( $this->getValue( 'bio_avatar_radius' ) ? $this->getValue( 'bio_avatar_radius' ) : $this->dbFields['bio_avatar_radius'] ); ?></span>%</span>
					<input type="range" class="form-control-range custom-range" onInput="$('#content_width_value').html($(this).val());$('#radius-preview').css('border-radius',$(this).val()+'%')" id="bio_avatar_radius" name="bio_avatar_radius" value="<?php echo $this->getValue( 'bio_avatar_radius' ); ?>" min="0" max="50" step="1" />
					<span class="btn btn-secondary btn-md form-range-button hide-if-no-js" onClick="$('#content_width_value').text('<?php echo $this->dbFields['bio_avatar_radius']; ?>');$('#bio_avatar_radius').val('<?php echo $this->dbFields['bio_avatar_radius']; ?>');$('#radius-preview').css('border-radius','<?php echo $this->dbFields['bio_avatar_radius']; ?>%')"><?php $L->p( 'Default' ); ?></span>
				</div>
				<small class="form-text"><?php $L->p( 'Sets the curvature of avatar image corners.' ); ?></small>
			</div>
		</div>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="bio_heading"><?php $L->p( 'Bio Heading' ); ?></label>
			<div class="col-sm-10">
				<input type="text" id="bio_heading" name="bio_heading" value="<?php echo $this->getValue( 'bio_heading' ); ?>" placeholder="" />
				<small class="form-text">
					<?php $L->p( 'The heading for the user bio. Use <code class="select">{{user}}</code> as a placeholder for the user\'s display name. Use <code class="select">{{first}}</code> as a placeholder for the user\'s first name.' ); ?>
				</small>
			</div>
		</div>
		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="details_heading"><?php $L->p( 'Details Heading' ); ?></label>
			<div class="col-sm-10">
				<input type="text" id="details_heading" name="details_heading" value="<?php echo $this->getValue( 'details_heading' ); ?>" placeholder="<?php echo $this->dbFields['details_heading']; ?>" />
				<small class="form-text">
					<?php $L->p( 'The heading for the user details, such as role, email, socials. Save as blank for no heading. Use <code class="select">{{user}}</code> as a placeholder for the user\'s display name. Use <code class="select">{{first}}</code> as a placeholder for the user\'s first name.' ); ?>
				</small>
			</div>
		</div>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="profile_role"><?php $L->p( 'User Role' ); ?></label>
			<div class="col-sm-10">
				<select class="form-select" id="profile_role" name="profile_role">

					<option value="true" <?php echo ( $this->getValue( 'profile_role' ) === true ? 'selected' : '' ); ?>><?php $L->p( 'Show' ); ?></option>

					<option value="false" <?php echo ( $this->getValue( 'profile_role' ) === false ? 'selected' : '' ); ?>><?php $L->p( 'Hide' ); ?></option>
				</select>
				<small class="form-text"><?php $L->p( 'Display the user\'s role in the website.' ); ?></small>
			</div>
		</div>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="profile_email"><?php $L->p( 'User Email' ); ?></label>
			<div class="col-sm-10">
				<select class="form-select" id="profile_email" name="profile_email">

					<option value="true" <?php echo ( $this->getValue( 'profile_email' ) === true ? 'selected' : '' ); ?>><?php $L->p( 'Show' ); ?></option>

					<option value="false" <?php echo ( $this->getValue( 'profile_email' ) === false ? 'selected' : '' ); ?>><?php $L->p( 'Hide' ); ?></option>
				</select>
				<small class="form-text"><?php $L->p( 'Display the user\'s email.' ); ?></small>
			</div>
		</div>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="profile_site"><?php $L->p( 'User Website' ); ?></label>
			<div class="col-sm-10">
				<select class="form-select" id="profile_site" name="profile_site">

					<option value="true" <?php echo ( $this->getValue( 'profile_site' ) === true ? 'selected' : '' ); ?>><?php $L->p( 'Show' ); ?></option>

					<option value="false" <?php echo ( $this->getValue( 'profile_site' ) === false ? 'selected' : '' ); ?>><?php $L->p( 'Hide' ); ?></option>
				</select>
				<small class="form-text"><?php $L->p( 'Display the user\'s website/link.' ); ?></small>
			</div>
		</div>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="profile_social"><?php $L->p( 'User Socials' ); ?></label>
			<div class="col-sm-10">
				<select class="form-select" id="profile_social" name="profile_social">

					<option value="true" <?php echo ( $this->getValue( 'profile_social' ) === true ? 'selected' : '' ); ?>><?php $L->p( 'Show' ); ?></option>

					<option value="false" <?php echo ( $this->getValue( 'profile_social' ) === false ? 'selected' : '' ); ?>><?php $L->p( 'Hide' ); ?></option>
				</select>
				<small class="form-text"><?php $L->p( 'Display the user\'s social media links.' ); ?></small>
			</div>
		</div>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="profile_posts"><?php $L->p( 'User Posts' ); ?></label>
			<div class="col-sm-10">
				<select class="form-select" id="profile_posts" name="profile_posts">

					<option value="true" <?php echo ( $this->getValue( 'profile_posts' ) === true ? 'selected' : '' ); ?>><?php $L->p( 'Show' ); ?></option>

					<option value="false" <?php echo ( $this->getValue( 'profile_posts' ) === false ? 'selected' : '' ); ?>><?php $L->p( 'Hide' ); ?></option>
				</select>
				<small class="form-text"><?php $L->p( 'Display a linked list of posts authored by the user.' ); ?></small>
			</div>
		</div>

		<div id="profile-list-limit" class="form-field form-group row" style="display: <?php echo ( $this->getValue( 'profile_posts' ) === true ? 'flex' : 'none' ); ?>;">
			<label class="form-label col-sm-2 col-form-label" for="profile_limit"><?php $L->p( 'Posts Limit' ); ?></label>
			<div class="col-sm-10 row">
				<div class="form-range-controls">
					<span class="form-range-value"><span id="profile_limit_value"><?php echo ( $this->getValue( 'profile_limit' ) ? $this->getValue( 'profile_limit' ) : $this->dbFields['profile_limit'] ); ?></span></span>
					<input type="range" class="form-control-range custom-range" onInput="$('#profile_limit_value').html($(this).val())" id="profile_limit" name="profile_limit" value="<?php echo $this->getValue( 'profile_limit' ); ?>" min="1" max="20" step="1" />
					<span class="btn btn-secondary btn-md form-range-button hide-if-no-js" onClick="$('#profile_limit_value').text('<?php echo $this->dbFields['profile_limit']; ?>');$('#profile_limit').val('<?php echo $this->dbFields['profile_limit']; ?>');"><?php $L->p( 'Default' ); ?></span>
				</div>
				<small class="form-text"><?php $L->p( 'Sets a maximum number of posts to display. ' ); ?></small>
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
		$avatar_id = str_replace( '.', '', basename( $default_avatar, pathinfo( $default_avatar, PATHINFO_EXTENSION ) ) ); ?>
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

<script>
jQuery(document).ready( function($) {

	// Show/hide profile options.
	$( '#profile_pages' ).on( 'change', function() {
		var show = $(this).val();
		if ( 'true' == show ) {
			$( '#profile-options-wrap' ).css( 'display', 'block' );
		} else if ( 'false' == show ) {
			$( '#profile-options-wrap' ).css( 'display', 'none' );
		}
	});

	// Show/hide custom avatar.
	$( '#header_style' ).on( 'change', function() {
		var show = $(this).val();
		$( '#header-preview' ).removeClass( function( index, className ) {
			return ( className.match(  /(^|\s)profile-style-\S+/g) || [] ).join( ' ' );
		} )
		$( '#header-preview' ).addClass( 'profile-style-' + $(this).val() )
	});

	// Show/hide profile list options.
	$( '#profile_posts' ).on( 'change', function() {
		var show = $(this).val();
		if ( 'true' == show ) {
			$( '#profile-list-limit' ).css( 'display', 'flex' );
		} else if ( 'false' == show ) {
			$( '#profile-list-limit' ).css( 'display', 'none' );
		}
	});
});
</script>
