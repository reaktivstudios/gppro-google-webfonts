<?php
/**
 * Genesis Design Palette Pro - Google Web Fonts API integration
 *
 * Enables the user to select any Google font to use with Design Palette Pro.
 *
 * @package Design Palette Pro - Google Web Fonts
 */

namespace DPP\Webfonts;

/*
	Copyright 2018 Reaktiv Studios

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
 * Google Web Fonts API class.
 *
 * Contains integration with Google Web Fonts Developer API.
 */
class Google extends \DPP\Admin\Fonts\Source {

	/**
	 * Google API key.
	 *
	 * @var string
	 */
	protected $api_key = '';

	/**
	 * Font source name.
	 *
	 * @var string
	 */
	protected $name = 'google';

	/**
	 * Transient key.
	 *
	 * @var string
	 */
	protected $transient_key = 'gppro-google-webfonts--transient';

	/**
	 * Option key.
	 *
	 * @var string
	 */
	protected $option_key = 'gppro-google-webfonts--option';

	/**
	 * Logging key.
	 *
	 * @var string
	 */
	protected $logging_enabled_key = 'gppro_google_webfonts_logging';

	/**
	 * Handle our checks then call our hooks.
	 *
	 * @return void
	 */
	public function init() {

		// First make sure we have our main class. not sure how we wouldn't but then again...
		if ( ! class_exists( 'Genesis_Palette_Pro' ) ) {
			return;
		}

		// Get the API key from the options table if it exists.
		$this->api_key = $this->get_api_key();

		parent::init();

		add_action( 'admin_notices', array( $this, 'api_key_action_response' ) );

		// Enqueue any scripts that the source may need.
		add_action( 'wp_enqueue_scripts', array( $this, 'font_scripts' ) );

		add_action( 'dpp_preview_head_before_styles', array( $this, 'preview_head' ) );

		add_action( 'updated_option', array( $this, 'updated_option' ), 10, 3 );

		add_action( 'admin_init', array( $this, 'maybe_import_fonts' ) );

		$this->maybe_delete_font_cache();
	}

	/**
	 * Set the font source config.
	 *
	 * @param array $defaults Default config parameters.
	 *
	 * @return void
	 */
	protected function set_config( $defaults ) {
		$this->config = array(
			'label' => __( 'Google Web Fonts', 'gppro-google-webfonts' ),
		);

		$this->config = wp_parse_args( $this->config, $defaults );
	}

	/**
	 * Get the API Key from the options table.
	 *
	 * @return string
	 */
	protected function get_api_key() {
		if ( empty( $this->api_key ) ) {
			// Get the API key from the options table if it exists.
			$this->api_key = get_option( 'gppro_google_webfonts_api_key', '' );
		}

		return $this->api_key;
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
			$fonts = $this->get_fonts( array() );

			if ( false !== $fonts ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Load the list of fonts available from the soruce.
	 *
	 * @return boolean
	 */
	protected function load_fonts() {
		if ( empty( $this->fonts ) ) {

			// Try to load the cached fonts.
			$this->fonts = $this->get_cached_fonts();

			// If no cached fonts or transient is expired, load from API.
			if ( empty( $this->fonts ) ) {
				$this->fonts = $this->fetch_api_fonts();
			}

			// If still no fonts, try to load fonts saved in options.
			if ( empty( $this->fonts ) ) {
				$this->fonts = $this->get_fonts_option();
			}

			// If still no fonts, there's a problem.
			if ( empty( $this->fonts ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Fetch fonts from the API.
	 *
	 * @return array
	 */
	protected function fetch_api_fonts() {
		$key = $this->api_key;

		if ( '' === $key ) {
			return array();
		}

		$response = wp_remote_get( esc_url( 'https://www.googleapis.com/webfonts/v1/webfonts?key=' . $key ) );

		if ( is_wp_error( $response ) ) {
			$this->log_api_error( $response->get_error_message() );
			return array();
		}

		if ( is_array( $response ) ) {
			if ( isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
				$response_fonts = json_decode( $response['body'] )->items;
				$fonts          = array();

				foreach ( $response_fonts as $font ) {
					$font_key = sanitize_title( $font->family );

					$type     = $this->get_font_type( $font->category, false );
					$alt_font = $this->get_font_type( $font->category, true );

					$variants = array_map(
						array( $this, 'map_variants' ),
						$font->variants
					);

					$val = str_replace( ' ', '+', $font->family ) . ':' . implode( ',', $variants );

					$fonts[ $font_key ] = dpp_font(
						array(
							'src'    => 'web',
							'url'    => esc_url( 'https://fonts.google.com/specimen/' . str_replace( ' ', '+', $font->family ) ),
							'label'  => $font->family,
							'css'    => '"' . $font->family . '", ' . $alt_font,
							'type'   => $type,
							'source' => $this->name,
							'val'    => $val,
							'link'   => '//fonts.googleapis.com/css?family=' . $val,
						)
					);
				}

				// Cache the fonts.
				$this->cache_fonts( $fonts );

				return $fonts;
			} else {
				$this->log_api_error( $response['body'] );
			}
		} else {
			$this->log_api_error( $response['body'] );
		}

		return array();
	}

	/**
	 * Log Google API errors.
	 *
	 * @param mixed $api_error The API error to log.
	 */
	protected function log_api_error( $api_error ) {
		$logging_enabled = get_option( $this->logging_enabled_key, false );

		// Only log errors if logging is enabled.
		if ( empty( $logging_enabled ) ) {
			return;
		}

		$log_key         = 'gppro_google_webfonts_log';
		$error_log       = get_option( $log_key, array() );
		$error_log_count = count( $error_log );
		$max_error_count = 100;

		if ( $error_log_count >= $max_error_count ) {
			array_shift( $error_log );
		}

		$error_log[] = array(
			'date'    => date( 'Y-m-d H:i:s' ),
			'message' => $api_error,
		);

		update_option( $log_key, $error_log );
	}

	/**
	 * Cache the fonts.
	 *
	 * @param array $fonts The fonts to cache.
	 */
	protected function cache_fonts( $fonts ) {
		// Set the expiration to 6 hours.
		$expiration = 6 * HOUR_IN_SECONDS;

		set_transient( $this->transient_key, $fonts, $expiration );
		update_option( $this->option_key, $fonts );
	}

	/**
	 * Maybe delete the font transient.
	 */
	protected function maybe_delete_font_cache() {
		// First make sure this happens in the WP admin and that the user is an administrator.
		if ( ! is_admin() && ! current_user_can( 'administrator' ) ) {
			return;
		}

		// Check for the dpp-delete-font-cache flag.
		if ( isset( $_GET['dpp-delete-font-cache'] ) ) {
			delete_transient( $this->transient_key );

			add_action( 'admin_notices', array( $this, 'delete_cache_admin_notice' ) );
		}
	}

	/**
	 * Trigger when an option is updated.
	 *
	 * @param string $option    The updated option.
	 * @param mixed  $old_value The old option value.
	 * @param mixed  $value     The new option value.
	 */
	public function updated_option( $option, $old_value, $value ) {
		if ( $option !== $this->logging_enabled_key ) {
			return;
		}

		// Clear the cache when logging is enabled.
		if ( '1' === $value ) {
			$this->log_api_error( 'Logging enabled' );
			delete_transient( $this->transient_key );
		}
	}

	/**
	 * Display an admin notice that the cache was cleared.
	 */
	public function delete_cache_admin_notice() {
		?>
	<div class="notice notice-success is-dismissible">
		<p><?php esc_html_e( 'Font cache successfully cleared', 'gppro-google-webfonts' ); ?></p>
	</div>
		<?php
	}

	/**
	 * Get the cached fonts.
	 *
	 * @return array
	 */
	protected function get_cached_fonts() {
		$fonts = get_transient( $this->transient_key );

		if ( ! empty( $fonts ) ) {
			return $fonts;
		}

		return array();
	}

	/**
	 * Get fonts from the font option.
	 *
	 * @return array
	 */
	protected function get_fonts_option() {
		$fonts = get_option( $this->option_key, array() );

		return $fonts;
	}

	/**
	 * Get the font type.
	 *
	 * @param string $font_category The font category.
	 * @param bool   $alt           Get alt font type if true.
	 *
	 * @return string
	 */
	protected function get_font_type( $font_category, $alt ) {
		$type = $font_category;

		switch ( $font_category ) {
			case 'handwriting':
				$type = $alt ? 'serif' : 'cursive';
				break;

			case 'display':
				$type = 'serif';
				break;

			case 'sans-serif':
				$type = $alt ? $font_category : 'sans';
				break;
		}

		return $type;
	}

	/**
	 * Map the font variants array.
	 *
	 * @param string $variant The variant to map.
	 *
	 * @return string
	 */
	protected function map_variants( $variant ) {
		if ( 'regular' === $variant ) {
			return '400';
		}
		if ( 'italic' === $variant ) {
			return '400italic';
		}

		return $variant;
	}

	/**
	 * Display the admin settings based on the provided query string
	 *
	 * @return void
	 */
	public function api_key_action_response() {

		$screen = get_current_screen();
		if ( empty( $screen ) ) {
			return;
		}

		$page = $screen->id;

		// First check we're on the right page.
		if ( 'genesis_page_genesis-palette-pro' !== $page ) {
			return;
		}

		// Set the class.
		$class  = 'notice is-dismissible notice-error';

		// Initialize the message.
		$text = '';

		$this->api_key = $this->get_api_key();

		if ( empty( $this->api_key ) ) {
			$text = __( 'You must enter a Google Fonts Developer API key to access the Google Fonts feature.', 'gppro-google-webfonts' );
		} elseif ( ! $this->api_key_check( $this->api_key ) ) {
			$text = __( 'The API key you entered is invalid.', 'gppro-google-webfonts' );
		}

		if ( '' !== $text ) {
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_attr( $text ) );
		}
	}

	/**
	 * Get Google font values.
	 *
	 * @param array $active_fonts   List of active fonts.
	 * @param array $selected_fonts List of selected fonts.
	 *
	 * @return array
	 */
	protected function get_google_font_values( $active_fonts, $selected_fonts ) {
		$google_font_values = array();
		foreach ( $active_fonts as $key => $font ) {
			if (
				in_array( $key, $selected_fonts, true )
				&& ! empty( $font['source'] )
				&& 'google' === $font['source']
				&& ! empty( $font['val'] )
			) {
				$google_font_values[] = $font['val'];
			}
		}

		return $google_font_values;
	}

	/**
	 * Load Google font link ref in the header.
	 */
	public function font_scripts() {
		$active_fonts   = dpp_get_active_fonts();
		$selected_fonts = dpp_get_selected_font_stacks();

		$google_font_values = $this->get_google_font_values( $active_fonts, $selected_fonts );

		if ( ! empty( $google_font_values ) ) {
			$google_font_values = implode( '|', $google_font_values );
			$google_url         = '//fonts.googleapis.com/css?family=' . $google_font_values;
			wp_enqueue_style(
				'gppro-webfonts',
				$google_url,
				array(),
				GPGWF_VER
			);
		}
	}

	/**
	 * Load Google fonts ref into the head for the customizer preview.
	 *
	 * @param array \DPP\Customizer\Data $data The DPP customizer data.
	 *
	 * @return void
	 */
	public function preview_head( $data ) {
		$active_fonts           = dpp_get_active_fonts();
		$selected_desktop_fonts = dpp_get_selected_font_stacks( $data->data['desktop'] );
		$selected_tablet_fonts  = dpp_get_selected_font_stacks( $data->data['tablet'] );
		$selected_mobile_fonts  = dpp_get_selected_font_stacks( $data->data['mobile'] );

		$selected_fonts = array_unique(
			array_merge(
				$selected_desktop_fonts,
				$selected_tablet_fonts,
				$selected_mobile_fonts
			)
		);

		$google_font_values = $this->get_google_font_values( $active_fonts, $selected_fonts );

		if ( ! empty( $google_font_values ) ) {
			$google_font_values = implode( '|', $google_font_values );
			$google_url = '//fonts.googleapis.com/css?family=' . $google_font_values;
			?>
	<link href="<?php echo esc_url( $google_url ); ?>" rel="stylesheet" /> 
			<?php
		}
	}

	/**
	 * Maybe import fonts.
	 */
	public function maybe_import_fonts() {
		// check nonce and bail if missing.
		if ( empty( $_POST['gppro_webfonts_import_nonce'] ) || ! wp_verify_nonce( $_POST['gppro_webfonts_import_nonce'], 'gppro_webfonts_import' ) ) {
			return;
		}

		// bail if no page reference.
		if ( empty( $_GET['gppro-import'] ) || ! empty( $_GET['gppro-import'] ) && 'go' !== $_GET['gppro-import'] ) {
			return;
		}

		// bail if no file present.
		if ( ! isset( $_FILES['gppro-google-webfonts-import-fonts'] ) ) {
			// set my redirect URL.
			$failure = menu_page_url( 'genesis-palette-pro', 0 ) . '&section=build_settings&uploaded=failure&reason=nofile';

			// and do the redirect.
			wp_safe_redirect( $failure );
			exit;
		}

		// bail if no file present.
		if ( ! empty( $_FILES['gppro-google-webfonts-import-fonts']['error'] ) && 4 === $_FILES['gppro-google-webfonts-import-fonts']['error'] ) {
			// set my redirect URL.
			$failure = menu_page_url( 'genesis-palette-pro', 0 ) . '&section=build_settings&uploaded=failure&reason=nofile';

			// and do the redirect.
			wp_safe_redirect( $failure );
			exit;
		}

		// check file extension.
		$name = explode( '.', $_FILES['gppro-google-webfonts-import-fonts']['name'] );
		if ( end( $name ) !== 'json' ) {

			// set my redirect URL.
			$failure = menu_page_url( 'genesis-palette-pro', 0 ) . '&section=build_settings&uploaded=failure&reason=notjson';

			// and do the redirect.
			wp_safe_redirect( $failure );
			exit;
		}

		// passed our initial checks, now decode the file and check the contents.
		$upload  = file_get_contents( $_FILES['gppro-google-webfonts-import-fonts']['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents, WordPress.WP.AlternativeFunctions.file_system_read_file_get_contents
		$options = json_decode( $upload, true );

		// check for valid JSON.
		if ( null === $options ) {

			// set my redirect URL.
			$failure = menu_page_url( 'genesis-palette-pro', 0 ) . '&section=build_settings&uploaded=failure&reason=badjson';

			// and do the redirect.
			wp_safe_redirect( $failure );
			exit;
		}

		update_option( $this->option_key, $options );
	}

}

// Instantiate the font source.
$google = new Google();
$google->init();
