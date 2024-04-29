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
	user
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

?>
<fieldset>
	<legend class="screen-reader-text mb-3"><?php $L->p( 'Profile Page Options' ); ?></legend>

	<h3 class="tab-section-heading"><?php $L->p( 'Profile Pages' ); ?></h3>

	<div class="form-field form-group row">
		<label class="form-label col-sm-2 col-form-label" for="profile_pages"><?php $L->p( 'Profile Pages' ); ?></label>
		<div class="col-sm-10">
			<select class="form-select" id="profile_pages" name="profile_pages">

				<option value="true" <?php echo ( $this->getValue( 'profile_pages' ) === true ? 'selected' : '' ); ?>><?php $L->p( 'Enable' ); ?></option>

				<option value="false" <?php echo ( $this->getValue( 'profile_pages' ) === false ? 'selected' : '' ); ?>><?php $L->p( 'Disable' ); ?></option>
			</select>
			<small class="form-text"><?php $L->p( 'Whether to allow user profile pages and include links.' ); ?></small>
		</div>
	</div>

	<div id="profile-options-wrap" style="display: <?php echo ( true == $this->profile_pages() ? 'block' : 'none' ); ?>;">
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

				foreach ( $covers as $cover ) {
					$preview[] = $this->domainPath() . 'assets/images/header-preview/' . basename( $cover );
				}
				$random = array_rand( $preview );
				?>
				<div id="header-preview" class="profile-page profile-style-<?php echo $this->header_style(); ?> <?php echo $preview_class; ?>">
					<header id="profile-page-header" class="profile-page-header" data-page-header>
						<div class="profile-cover-wrap cover-overlay">
							<figure class="profile-cover <?php echo $preview_class; ?>">
								<img src="<?php echo $preview[$random]; ?>" role="presentation" />
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
