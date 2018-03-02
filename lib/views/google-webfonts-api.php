<?php
/**
 * Genesis Design Palette Pro - Google Webfonts API layout
 *
 * Enables the user to select any Google font to use with Design Palette Pro.
 *
 * @package Design Palette Pro - Google Webfonts
 */
?>
<div class="wrap gppro-google-webfonts-admin">

	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<form name="my_form" method="post">
		<input type="hidden" name="action" value="gppro-google-webfonts-store-api-key">
		<?php
		wp_nonce_field( 'gppro-google-webfonts-api-key-nonce', 'gppro-google-webfonts-api-key-nonce', false );

		/* Used to save closed meta boxes and their order */
		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>

		<div id="poststuff">

			<div id="post-body" class="metabox-holder">

				<div class="postbox-container">
					<?php do_meta_boxes( '', 'api', null ); ?>
					<?php do_meta_boxes( '', 'font', null ); ?>
				</div>

			</div> <!-- #post-body -->

		</div> <!-- #poststuff -->

	</form>

</div>
