<?php
/**
 * Genesis Design Palette Pro - Google Webfonts API integration
 *
 * Enables the user to select any Google font to use with Design Palette Pro.
 *
 * @package Design Palette Pro - Google Webfonts
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
 * Google Webfonts API class.
 *
 * Contains integration with Google Webfonts Developer API.
 */
class Google extends \DPP\Admin\Fonts\Source {

	/**
	 * Google API key.
	 *
	 * @var string
	 */
	protected $api_key = '';

	/**
	 * Fon source name.
	 *
	 * @var string
	 */
	protected $name = 'google';

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
			'label' => __( 'Google Webfonts', 'gppro-google-webfonts' ),
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
	 * @return void
	 */
	protected function load_fonts() {
		if ( empty( $this->fonts ) ) {
			$key = $this->api_key;

			if ( '' === $key ) {
				return false;
			}

			$response = wp_remote_get( esc_url( 'https://www.googleapis.com/webfonts/v1/webfonts?key=' . $key ) );

			if ( is_array( $response ) ) {
				if ( isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
					$response_fonts = json_decode( $response['body'] )->items;
					$fonts          = array();

					array_walk(
						$response_fonts,
						function( $font, $font_index ) use ( &$fonts ) {
							$font_key = sanitize_title( $font->family );

							$type     = $font->category;
							$alt_font = $font->category;
							switch ( $type ) {
								case 'handwriting':
									$type     = 'cursive';
									$alt_font = 'serif';
									break;

								case 'display':
									$type     = 'serif';
									$alt_font = 'serif';
									break;

								case 'sans-serif':
									$type = 'sans';
									break;
							}
							$variants = array_map(
								function( $variant ) {
									if ( 'regular' === $variant ) {
										return '400';
									}
									if ( 'italic' === $variant ) {
										return '400italic';
									}

									return $variant;
								},
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
					);

					$this->fonts = $fonts;
				}
			}
		}
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
	 * Load Google font link ref in the header.
	 */
	public function font_scripts() {
		$active_fonts   = dpp_get_active_fonts();
		$selected_fonts = dpp_get_selected_font_stacks();

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

		if ( ! empty( $google_font_values ) ) {
			$google_font_values = implode( '|', $google_font_values );
			wp_enqueue_style(
				'gppro-webfonts',
				'//fonts.googleapis.com/css?family=' . $google_font_values,
				array(),
				GPGWF_VER
			);
		}
	}

}

// Instantiate the font source.
$google = new Google();
$google->init();
