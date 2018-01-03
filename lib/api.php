<?php
/**
 * Genesis Design Palette Pro - Google Webfonts API integration
 *
 * Enables the user to select any Google font to use with Design Palette Pro.
 *
 * @package Design Palette Pro - Google Webfonts
 */

/*
	Copyright 2017 Reaktiv Studios

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; version 2 of the License (GPL v2) only.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Google Webfonts API class.
 *
 * Contains integration with Google Webfonts Developer API.
 */
class GP_Pro_Google_Webfonts_Api {

	/**
	 * Google API key.
	 *
	 * @var string
	 */
	protected $api_key = '';

	/**
	 * This is the constructor.
	 *
	 * @return
	 */
	public function __construct() {

		// Get the API key from the options table if it exists.
		$this->api_key = get_option( 'gppro_google_webfonts_api_key', '' );

		add_action( 'admin_notices', array( $this, 'api_key_action_response' ) );
		add_action( 'admin_menu', array( $this, 'webfonts_menu' ) );
		add_action( 'admin_init', array( $this, 'maybe_store_api_key' ) );

	}

	/**
	 * Create the Google webfonts page submenu item under the "Tools" menu.
	 *
	 * @return void
	 */
	public function webfonts_menu() {

		// Add a tools page to manage the list of Google Webfonts.
		add_management_page(
			__( 'Design Palette Pro Webfonts', 'gppro-google-webfonts' ),
			__( 'DPP Webfonts', 'gppro-google-webfonts' ),
			apply_filters( 'gppro_caps', 'manage_options' ),
			'dpp-webfonts',
			array( $this, 'webfonts_page' )
		);
	}

	/**
	 * Construct our webfonts page.
	 *
	 * @return void
	 */
	public function webfonts_page() {

		// The wrapper for the admin page.
		echo '<div class="wrap gppro-google-webfonts-admin">';

			// Handle the page title.
			echo '<h1>' . esc_html( get_admin_page_title() ) . '</h1>';

			echo '<div id="poststuff">';

				// Fetch the API Key field layout.
				$this->api_key_layout();

				// Fetch the webfonts layout.
				$this->webfonts_layout();

		// Close the markup.
		echo '</div>';

	}

	/**
	 * Display Google API Key field on the webfonts page.
	 *
	 * @return void
	 */
	protected function api_key_layout() {
?>
		<div class="metabox-holder">
			<div class="postbox-container">
				<form method="post" action="<?php echo esc_url( self::get_webfonts_page_link() ); ?>">

					<?php echo wp_nonce_field( 'gppro-google-webfonts-api-key-nonce', 'gppro-google-webfonts-api-key-nonce', false, false ); ?>

					<?php do_action( 'gppro_before_webfonts_api_key_admin_settings' ); ?>

					<table class="form-table">
						<tr>
							<th scope="row">
								<label for="gppro_google_webfonts_api_key"><?php echo esc_html__( 'Google API Key', 'gppro-google-webfonts' ); ?></label>
							</th>
							<td>
								<input name="gppro_google_webfonts_api_key" id="gppro_google_webfonts_api_key" value="<?php echo esc_attr( $this->api_key ); ?>" aria-describedby="api_key-description" class="regular-text" />
								<p class="description" id="api_key-description">
								<?php
									printf( '%1$s <a href="%2$s" title="%3$s" target="_blank">%4$s</a>',
										esc_html__( 'You must have a Google Fonts Developer API key to use this feature.', 'gppro-google-webfonts' ),
										esc_url( 'https://developers.google.com/fonts/docs/developer_api' ),
										esc_attr__( 'Google Fonts Developer API Key', 'gppro-google-webfonts' ),
										esc_html__( 'Click here to learn more and to retrieve your API key.', 'gppro-google-webfonts' )
									);
								?>
								</p>
							</td>
						</tr>
					</table>

					<?php do_action( 'gppro_after_webfonts_api_key_admin_settings' ); ?>

					<div class="bottom-buttons">
						<?php submit_button( __( 'Submit', 'gppro-google-webfonts' ), 'primary', 'submit', false ); ?>
						<?php submit_button( __( 'Reset', 'gppro-google-webfonts' ), 'secondary genesis-js-confirm-reset', 'reset', false ); ?>
					</div>

					<input type="hidden" name="gppro-google-webfonts-action" value="store-api-key" />

				</form>
			</div>
		</div>
<?php
	}

	/**
	 * Display Google webfonts on the settings page.
	 *
	 * @return void
	 */
	protected function webfonts_layout() {
		if ( '' !== $this->api_key ) {
			$fonts = $this->get_fonts();
			echo '<pre>' . print_r($fonts, true) . '</pre>';
		}
	}

	/**
	 * Display webfonts search form.
	 *
	 * @return void
	 */
	protected function webfonts_search_form() {
?>

<?php
	}

	/**
	 * Build and return the link to send the user to the license entering.
	 *
	 * @param  array  $args    Optional args to add to the link.
	 *
	 * @return string $link    The URL of the Genesis settings page.
	 */
	public static function get_webfonts_page_link( $args = array() ) {

		// Set my base link.
		$base   = menu_page_url( 'dpp-webfonts', 0 );

		// Set my link up.
		$link   = ! empty( $args ) ? add_query_arg( $args, $base ) : $base;

		// And return my link.
		return apply_filters( 'gppro_webfonts_page_url', $link );
	}

	/**
	 * Maybe store the API key.
	 *
	 * @return void
	 */
	public function maybe_store_api_key() {

		// Bail if this is an Ajax or Cron job.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX || defined( 'DOING_CRON' ) && DOING_CRON ) {
			return;
		}

		// Check for our hidden field.
		if ( empty( $_POST['gppro-google-webfonts-action'] ) || 'store-api-key' !== sanitize_key( $_POST['gppro-google-webfonts-action'] ) ) { // Input var okay.
			return;
		}

		$link = self::get_webfonts_page_link();

		// Make sure a nonce was passed and is valid.
		if ( empty( $_POST['gppro-google-webfonts-api-key-nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['gppro-google-webfonts-api-key-nonce'] ), 'gppro-google-webfonts-api-key-nonce' ) ) {

			// Set my redirect link with the error code.
			$link   = add_query_arg(
				array(
					'processed' => 'failure',
					'errcode' => 'MISSING_NONCE',
				),
				$link
			);

			// And do the redirect.
			wp_safe_redirect( $link );
			exit;
		}

		// Bail if the api key field is missing.
		if ( empty( $_POST['gppro_google_webfonts_api_key'] ) ) {

			// Set my redirect link with the error code.
			$link   = add_query_arg(
				array(
					'processed' => 'failure',
					'errcode' => 'EMPTY_KEY',
				),
				$link
			);

			// And do the redirect.
			wp_safe_redirect( $link );
			exit;
		}

		$api_key = sanitize_text_field( $_POST['gppro_google_webfonts_api_key'] );

		if ( $this->api_key_check( $api_key ) ) {
			// Update the API Key.
			update_option( 'gppro_google_webfonts_api_key', $api_key );

			// Set my redirect link with the success code.
			$link   = add_query_arg(
				array(
					'processed' => 'success',
				),
				$link
			);

			// And do the redirect.
			wp_safe_redirect( $link );
			exit;
		} else {
			// Set my redirect link with the error code.
			$link   = add_query_arg(
				array(
					'processed' => 'failure',
					'errcode' => 'INVALID_KEY',
				),
				$link
			);

			// And do the redirect.
			wp_safe_redirect( $link );
			exit;
		}

	}

	/**
	 * Check to make sure the api key is valid.
	 *
	 * @param string $key The api key.
	 *
	 * @return boolean
	 */
	protected function api_key_check( $key ) {
		if ( '' !== $key ) {
			$fonts = $this->get_fonts( array(), $key );

			if ( false !== $fonts ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get a list of fonts from the Google Webfonts API.
	 *
	 * @param array  $args Arguments to add to API call.
	 * @param string $key Optional API key to use instead of $this->api_key
	 *
	 * @return array/boolean List of fonts or false
	 */
	protected function get_fonts( $args = array(), $key = '' ) {
		if ( '' === $key ) {
			$key = $this->api_key;
		}

		if ( '' === $key ) {
			return false;
		}

		$response = wp_remote_get( 'https://www.googleapis.com/webfonts/v1/webfonts?key=' . $key );

		if ( is_array( $response ) ) {
			if ( isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
				return json_decode( $response['body'] );
			}
		}

		return false;
	}

	/**
	 * Display the admin settings based on the provided query string
	 *
	 * @return void
	 */
	public function api_key_action_response() {

		// First check we're on the right page.
		if ( empty( $_GET['page'] ) || 'dpp-webfonts' !== sanitize_key( $_GET['page'] ) ) {
			return;
		}

		// Make sure we have the process result.
		if ( empty( $_GET['processed'] ) || ! in_array( sanitize_key( $_GET['processed'] ), array( 'success', 'failure' ) ) ) {
			return;
		}

		// Set my base class.
		$class  = 'notice is-dismissible';

		// Handle our success message.
		if ( 'success' === sanitize_key( $_GET['processed'] ) ) {

			// Add success to the class.
			$class .= ' notice-success';

			// And my error text.
			$text   = __( 'Google Webfonts API Key successfully saved!', 'gppro-google-webfonts' );
		}

		// Handle our failure messages.
		if ( 'failure' === sanitize_key( $_GET['processed'] ) ) {

			// Get my error message.
			$error  = ! empty( $_GET['errcode'] ) ? strtolower( sanitize_key( $_GET['errcode'] ) ) : 'unknown';

			// Add failure to the class.
			$class .= ' notice-error';

			// And my error text.
			$text   = self::get_message_text( $error );
		}

		// And output it.
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_attr( $text ) );
	}

	/**
	 * Get the text to use on the API key saving process.
	 *
	 * @param  string $key   Which message key to do.
	 *
	 * @return string $text  The resulting text.
	 */
	public static function get_message_text( $key = '' ) {

		// Do our switch check.
		switch ( $key ) {

			case 'missing_nonce' :

				$text   = __( 'There was an error saving this API key. Please try again.', 'gppro-google-webfonts' );
				break;

			case 'empty_key' :

				$text   = __( 'The API key is missing. Please enter an API key before submitting.', 'gppro-google-webfonts' );
				break;

			case 'invalid_key' :

				$text   = __( 'The API key you entered is invalid.', 'gppro-google-webfonts' );
				break;

			default :
				$text   = __( 'There was an error with this API key.', 'gppro-google-webfonts' );
				break;
		}

		// Return the text.
		return $text;
	}

}

$GP_Pro_Google_Webfonts_Api = new GP_Pro_Google_Webfonts_Api();
