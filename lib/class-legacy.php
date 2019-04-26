<?php
/**
 * Genesis Design Palette Pro - Google Web Fonts Legacy Plugin
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
 * Load our Google Web Fonts class
 */
class Legacy {

	/**
	 * Static property to hold our singleton instance
	 *
	 * @var GP_Pro_Google_Webfonts
	 */
	static $instance = false;

	/**
	 * This is our constructor
	 *
	 * @return
	 */
	private function __construct() {
		// General backend actions.
		add_action( 'plugins_loaded', array( $this, 'textdomain' ) );
		add_action( 'admin_notices', array( $this, 'gppro_active_check' ), 10 );
		add_action( 'admin_notices', array( $this, 'pagespeed_alert' ), 10 );

		// Front end action.
		add_action( 'wp_enqueue_scripts', array( $this, 'font_scripts' ) );

		// GP Pro specific filter.
		add_filter( 'gppro_font_stacks', array( $this, 'google_stack_list' ), 99 );
	}

	/**
	 * If an instance exists, this returns it.  If not, it creates one and
	 * retuns it.
	 *
	 * @return $instance
	 */
	public static function getInstance() {

		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Load textdomain.
	 */
	public function textdomain() {
		load_plugin_textdomain( 'gppro-google-webfonts', false, GPGWF_DIR . '/languages/' );
	}

	/**
	 * Check for GP Pro being active.
	 *
	 * @return HTML message or nothing.
	 */
	public function gppro_active_check() {

		// Make sure the function exists.
		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}

		// Get the current screen.
		$screen = get_current_screen();

		// Bail if not on the plugins page.
		if ( ! is_object( $screen ) || empty( $screen->parent_file ) || 'plugins.php' !== esc_attr( $screen->parent_file ) ) {
			return;
		}

		// Run the active check.
		$coreactive = class_exists( 'Genesis_Palette_Pro' ) ? \Genesis_Palette_Pro::check_active() : false;

		// Active. bail.
		if ( $coreactive ) {
			return;
		}

		// Not active. Show message.
		echo '<div id="message" class="notice settings-error is-dismissible gppro-admin-warning"><p><strong>' . esc_html__( 'This plugin requires Genesis Design Palette Pro to function and cannot be activated.', 'gppro-google-webfonts' ).'</strong></p></div>';

		// Hide activation method.
		unset( $_GET['activate'] );

		// Deactivate the plugin.
		deactivate_plugins( plugin_basename( __FILE__ ) );

		// And finish.
		return;
	}

	/**
	 * Check for high font number.
	 *
	 * @return HTML  A message alert if we have a high number.
	 */
	public function pagespeed_alert() {

		// Make sure the function exists.
		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}

		// Get the current screen.
		$screen = get_current_screen();

		// Bail if not on the main DPP page.
		if ( ! is_object( $screen ) || empty( $screen->base ) || 'genesis_page_genesis-palette-pro' !== esc_attr( $screen->base ) ) {
			return;
		}

		// Check out pagespeed alert.
		$alert  = get_option( 'gppro-webfont-alert' );

		// Check for each possible alert.
		if ( empty( $alert ) || 'ignore' === $alert ) {
			return;
		}

		// Display the pagespeed warning.
		echo '<div id="message" class="error fade below-h2 gppro-admin-warning gppro-admin-warning-webfonts"><p>';
		echo '<strong>' . esc_html__( 'Warning: You have selected multiple web fonts which could have a severe impact on site performance.', 'gppro-google-webfonts' ) . '</strong>';
		echo '<span class="webfont-ignore">' . esc_html__( 'Ignore this message', 'gppro-google-webfonts' ) . '</span>';
		echo '</p></div>';
	}

	/**
	 * Register alert for pagespeed test.
	 *
	 * @param  integer $fontsize  The value of all the fonts loaded.
	 *
	 * @return void.
	 */
	public static function pagespeed_check( $fontsize ) {

		// Fetch the existing alert, if it exists.
		$alert  = get_option( 'gppro-webfont-alert' );

		// Sum the total we are adding.
		$totals = array_sum( $fontsize );

		// Set our high number point with a filter.
		$filter = apply_filters( 'gppro_webfont_alert', 250 );

		// Delete the alert if less than alert amount.
		if ( $totals < absint( $filter ) ) {
			delete_option( 'gppro-webfont-alert' );
		}

		// Check my alert status before moving on.
		if ( ! empty( $alert ) && 'ignore' === $alert ) {
			return;
		}

		// Set alert flag for over alert amount.
		if ( $totals >= absint( $filter ) ) {
			add_option( 'gppro-webfont-alert', true, null, 'no' );
		}
	}

	/**
	 * Call our webfont CSS files.
	 *
	 * @return void
	 */
	public static function font_scripts() {

		// Fetch our font string and enqueue the CSS.
		if ( false !== $string = self::font_choice_string() ) {
			wp_enqueue_style( 'gppro-webfonts', '//fonts.googleapis.com/css?family=' . $string, array(), GPGWF_VER );
		}
	}

	/**
	 * Helper to create array of font options and combine into string.
	 *
	 * @return string  The string of the Google Web Fonts.
	 */
	public static function font_choice_string() {

		// Fetch list of active fonts and bail without.
		if ( false === $actives = self::font_choice_active() ) {
			return false;
		}

		// Set value arrays to false.
		$fontarr    = false;
		$fontsize   = false;

		// Loop them all.
		foreach ( $actives as $active ) {

			// Get individual font data.
			$data   = self::single_font_fetch( $active );

			// Bail if it came back native (i.e. already loaded ).
			if ( empty( $data['src'] ) || 'native' === $data['src'] ) {
				continue;
			}

			// Bail if the value (i.e. string itself) is empty.
			if ( empty( $data['val'] ) ) {
				continue;
			}

			// Pass it into array and go forth.
			$fontarr[]  = $data['val'];
			$fontsize[] = ! empty( $data['size'] ) ? $data['size'] : 0;
		}

		// Bail if nothing is there.
		if ( ! $fontarr ) {
			return false;
		}

		// Cast into array.
		$fontarr    = (array) $fontarr;
		$fontsize   = (array) $fontsize;

		// Run font weight check for pagespeed alert.
		if ( $fontsize ) {
			$pagespeed  = self::pagespeed_check( $fontsize );
		}

		// Implode into string with divider and send it back.
		return implode( '|', $fontarr );
	}

	/**
	 * Helper to determine if a font has actually been selected and creates an array.
	 *
	 * @return array $choices  The active fonts.
	 */
	public static function font_choice_active() {

		// Fetch our list of stacks.
		$stacklist = self::google_stacks();
		$stackkeys = array_keys( $stacklist );

		// Grab our settings.
		$settings = get_option( 'gppro-settings' );

		// Bail with no DPP settings.
		if ( empty( $settings ) ) {
			return false;
		}

		// Set an empty array.
		$choices = array();

		// Gilter through and run comparison.
		foreach ( $settings as $key => $value ) {

			// Add our things.
			if ( in_array( $value, $stackkeys, true ) ) {
				$choices[] = $value;
			}
		}

		// Return our choices without duplicates.
		return array_unique( $choices );
	}

	/**
	 * Helper to fetch the values from a single stack.
	 *
	 * @param  string $font   The font stack to pull from the overall array.
	 *
	 * @return array  $stack  A single font stack from the set or false.
	 */
	public static function single_font_fetch( $font = '' ) {

		// Bail if no font was passed.
		if ( empty( $font ) ) {
			return false;
		}

		// Fetch our list of stacks and bail if we don't have one.
		if ( false === $stacklist = self::google_stacks() ) {
			return false;
		}

		// Return the single requested.
		return ! empty( $stacklist[ $font ] ) ? $stacklist[ $font ] : false;
	}

	/**
	 * Create list of stacks to use in various locations.
	 *
	 * @return array $webfonts  The big array of fonts.
	 */
	public static function google_stacks() {

		$webfonts = gppro_google_webfonts_get_legacy_stacks();

		// Filter them all and return.
		return apply_filters( 'gppro_webfont_stacks', $webfonts );
	}

	/**
	 * Add stacks to the dropdown
	 *
	 * @param  array $stacks  The existing fonts in the dropdown list.
	 *
	 * @return array $stacks  The updated list of fonts.
	 */
	public function google_stack_list( $stacks = array() ) {

		// Fetch our list of stacks.
		$stacklist = self::google_stacks();

		// Set up the fonts we are adding.
		$fonts = array(
			'serif'   => array(),
			'sans'    => array(),
			'cursive' => array(),
			'mono'    => array(),
		);

		foreach ( $stacklist as $family => $font ) {
			$fonts[ $font['type'] ][] = $family;
		}

		// Filter the list prior to doing the check.
		$fonts = apply_filters( 'gppro_webfont_families', $fonts );

		// If we emptied the font stack for some reason, return.
		if ( empty( $fonts ) ) {
			return $stacks;
		}

		// Loop the type groups.
		foreach ( $fonts as $type => $families ) {

			// Now loop the individual families.
			foreach ( $families as $family ) {

				// If we dont already have the font, add it.
				if ( ! isset( $stacks[ $type ][ $family ] ) ) {
					$stacks[ $type ][ $family ] = $stacklist[ $family ];
				}
			}
		}

		// Filter them all and send back stacks.
		return apply_filters( 'gppro_webfont_stack_list', $stacks );
	}

	// End class.
}

// Instantiate our class.
$legacy = Legacy::getInstance();
