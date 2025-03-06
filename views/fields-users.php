<?php
/**
 * Users options fields
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
	user
};
use function UPRO_Tags\{
	role,
	user_display_name,
	user_avatar
};

?>
<div class="accordion-container">
<?php

$list = usernames();
asort( $list );
foreach ( $list as $name ) {

	try {
		$get_user = user( $name );
		$username = $get_user->username();

		echo '<div class="accordion-section">';
		printf(
			'<%s class="accordion-section-title form-heading "><span class="fa fa-angle-down accordion-title-icon"></span> %s</%s>',
			$sec_h_el,
			user_display_name( $username ),
			$sec_h_el
		);
		echo '<div class="accordion-section-content">'; ?>
		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="info_<?php echo $username; ?>"><?php $L->p( 'Info' ); ?></label>
			<div class="col-sm-10">
				<header class="row upro-form-details-section">
					<figure>
						<a href="<?php echo DOMAIN_ADMIN . 'edit-user/' . $username . '#picture'; ?>">
							<img class="avatar user-avatar upro-form-details-avatar" alt="User avatar" src="<?php echo user_avatar( $username ); ?>" width="80" height="80" />
						</a>
					</figure>
					<div>
						<p>
						<?php printf(
							'<span class="upro-form-label">%s</span> <span class="upro-form-detail">%s</span>',
							$L->get( 'Role:' ),
							role( $username, 'title' )
						); ?>
						<br />
						<?php printf(
							'<span class="upro-form-label">%s</span> <span class="upro-form-detail">%s</span>',
							$L->get( 'Email:' ),
							$get_user->email()
						); ?>
						<br />
						<?php printf(
							'<a href="%s">%s</a>',
							DOMAIN_ADMIN . 'edit-user/' . $username,
							$L->get( 'Edit' )
						); ?>
						</p>
					</div>
					</header>
			</div>
		</div>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="email_<?php echo $username; ?>"><?php $L->p( 'Email Display' ); ?></label>
			<div class="col-sm-10">
				<select class="form-select" id="email_" name="email_<?php echo $username; ?>">
					<option value="true" <?php echo ( $this->getValue( 'email_' . $username ) === true ? 'selected' : '' ); ?>><?php $L->p( 'Show' ); ?></option>
					<option value="false" <?php echo ( $this->getValue( 'email_' . $username ) === false ? 'selected' : '' ); ?>><?php $L->p( 'Hide' ); ?></option>
				</select>
				<small class="form-text"><?php $L->p( 'Override the default email display setting.' ); ?></small>
			</div>
		</div>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="website_<?php echo $username; ?>"><?php $L->p( 'Website' ); ?></label>
			<div class="col-sm-10">
				<input type="text" id="website_<?php echo $username; ?>" name="website_<?php echo $username; ?>" value="<?php echo $this->getValue( 'website_' . $username ); ?>" placeholder="https://example.com" />
				<small class="form-text"><?php $L->p( '' ); ?></small>
			</div>
		</div>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="bio-<?php echo $username; ?>"><?php $L->p( 'Bio' ); ?></label>
			<div class="col-sm-10">
				<textarea name="bio_<?php echo $username; ?>" id="bio-<?php echo $username; ?>" class="editable" cols="30" rows="10"><?php echo $this->getValue( 'bio_' . $username ); ?></textarea>
				<small class="form-text text-muted"><?php $L->p( 'Line breaks and paragraphs automatically added. HTML tags accepted.' ); ?></small>
			</div>
		</div>

		<div class="form-field form-group row">
			<label class="form-label col-sm-2 col-form-label" for="bio-<?php echo $username; ?>"><?php $L->p( 'Cover Image' ); ?></label>
			<div id="cover-select" class="col-sm-10">
				<p><?php $L->p( 'Select from uploaded cover images.' ); ?></p>
				<?php echo $covers->select_images( $cover, $username ); ?>
			</div>
		</div>
		<?php
		// Close accordion section.
		echo '</div></div>';

	} catch ( Exception $e ) {
		// Continue.
	}
} ?>
</div>

<script>
jQuery(document).ready( function($) {
<?php
// Cover image fields for each user.
foreach ( usernames() as $user ) { ?>
	$( '.cover-select-label-<?php echo $user; ?>' ).click( function() {
		$( '.cover-select-label-<?php echo $user; ?>' ).removeClass( 'selected' );
		$(this).addClass( 'selected' );
	});
<?php } ?>
});
</script>
