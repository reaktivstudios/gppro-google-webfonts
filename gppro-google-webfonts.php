<?php
/*
Plugin Name: Genesis Design Palette Pro - Google Webfonts
Plugin URI: https://genesisdesignpro.com/
Description: Adds a set of popular Google Webfonts to Design Palette Pro
Author: Reaktiv Studios
Version: 1.0.8
Requires at least: 4.0
Author URI: http://andrewnorcross.com
*/

/*
	Copyright 2014 Andrew Norcross

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

if ( ! defined( 'GPGWF_BASE' ) ) {
	define( 'GPGWF_BASE', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'GPGWF_DIR' ) ) {
	define( 'GPGWF_DIR', dirname( __FILE__ ) );
}

if ( ! defined( 'GPGWF_VER' ) ) {
	define( 'GPGWF_VER', '1.0.7' );
}

/**
 * Load our Google Webfonts class
 */
class GP_Pro_Google_Webfonts
{

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
		add_action( 'plugins_loaded',                   array( $this, 'textdomain'              )           );
		add_action( 'admin_notices',                    array( $this, 'gppro_active_check'      ),  10      );
		add_action( 'admin_notices',                    array( $this, 'pagespeed_alert'         ),  10      );

		// Front end action.
		add_action( 'wp_enqueue_scripts',               array( $this, 'font_scripts'            )           );

		// GP Pro specific filter.
		add_filter( 'gppro_font_stacks',                array( $this, 'google_stack_list'       ),  99      );
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
		load_plugin_textdomain( 'gppro-google-webfonts', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
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
		$coreactive = class_exists( 'Genesis_Palette_Pro' ) ? Genesis_Palette_Pro::check_active() : false;

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
		echo '<strong>' . esc_html__( 'Warning: You have selected multiple webfonts which could have a severe impact on site performance.', 'gppro-google-webfonts' ) . '</strong>';
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
	 * @return string  The string of the Google webfonts.
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
		$stacklist  = self::google_stacks();
		$stackkeys  = array_keys( $stacklist );

		// Grab our settings.
		$settings  = get_option( 'gppro-settings' );

		// Bail with no DPP settings.
		if ( empty( $settings ) ) {
			return false;
		}

		// Set an empty array.
		$choices    = array();

		// Gilter through and run comparison.
		foreach ( $settings as $key => $value ) {

			// Add our things.
			if ( in_array( $value, $stackkeys ) ) {
				$choices[]  = $value;
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

		// Set the array of fonts.
		$webfonts   = array(

			// The list of serif fonts.
			'abril-fatface' => array(
				'label' => __( 'Abril Fatface', 'gppro-google-webfonts' ),
				'css'   => '"Abril Fatface", serif',
				'src'   => 'web',
				'val'   => 'Abril+Fatface',
				'size'  => '14',
			),

			'arvo'  => array(
				'label' => __( 'Arvo', 'gppro-google-webfonts' ),
				'css'   => '"Arvo", serif',
				'src'   => 'web',
				'val'   => 'Arvo:400,700,400italic,700italic',
				'size'  => '104',
			),

			'bitter'    => array(
				'label' => __( 'Bitter', 'gppro-google-webfonts' ),
				'css'   => '"Bitter", serif',
				'src'   => 'web',
				'val'   => 'Bitter:400,700,400italic',
				'size'  => '66',
			),

			'bree-serif'    => array(
				'label' => __( 'Bree Serif', 'gppro-google-webfonts' ),
				'css'   => '"Bree Serif", serif',
				'src'   => 'web',
				'val'   => 'Bree+Serif',
				'size'  => '11',
			),

			'crimson-text'  => array(
				'label' => __( 'Crimson Text', 'gppro-google-webfonts' ),
				'css'   => '"Crimson Text", serif',
				'src'   => 'web',
				'val'   => 'Crimson+Text:400,700',
				'size'  => '186',
			),

			'enriqueta' => array(
				'label' => __( 'Enriqueta', 'gppro-google-webfonts' ),
				'css'   => '"Enriqueta", serif',
				'src'   => 'web',
				'val'   => 'Enriqueta:400,700',
				'size'  => '22',
			),

			'fenix'     => array(
				'label' => __( 'Fenix', 'gppro-google-webfonts' ),
				'css'   => '"Fenix", serif',
				'src'   => 'web',
				'val'   => 'Fenix',
				'size'  => '8',
			),

			'lora'  => array(
				'label' => __( 'Lora', 'gppro-google-webfonts' ),
				'css'   => '"Lora", serif',
				'src'   => 'web',
				'val'   => 'Lora:400,700,400italic,700italic',
				'size'  => '112',
			),

			'josefin-slab'  => array(
				'label' => __( 'Josefin Slab', 'gppro-google-webfonts' ),
				'css'   => '"Josefin Slab", serif',
				'src'   => 'web',
				'val'   => 'Josefin+Slab:400,700',
				'size'  => '102',
			),

			'merriweather'  => array(
				'label' => __( 'Merriweather', 'gppro-google-webfonts' ),
				'css'   => '"Merriweather", serif',
				'src'   => 'web',
				'val'   => 'Merriweather:400,700,400italic,700italic',
				'size'  => '44',
			),

			'neuton'    => array(
				'label' => __( 'Neuton', 'gppro-google-webfonts' ),
				'css'   => '"Neuton", serif',
				'src'   => 'web',
				'val'   => 'Neuton:300,400,700,400italic',
				'size'  => '56',
			),

			'nixie-one' => array(
				'label' => __( 'Nixie One', 'gppro-google-webfonts' ),
				'css'   => '"Nixie One", serif',
				'src'   => 'web',
				'val'   => 'Nixie+One',
				'size'  => '39',
			),

			'noto-serif'   => array(
				'label' => __( 'Noto Serif', 'gppro-google-webfonts' ),
				'css'   => '"Noto Serif", serif',
				'src'   => 'web',
				'val'   => 'Noto+Serif:400,400i,700,700i',
				'size'  => '36',
			),

			'old-standard-tt'   => array(
				'label' => __( 'Old Standard TT', 'gppro-google-webfonts' ),
				'css'   => '"Old Standard TT", serif',
				'src'   => 'web',
				'val'   => 'Old+Standard+TT:400,700,400italic',
				'size'  => '93',
			),

			'playfair-display'  => array(
				'label' => __( 'Playfair Display', 'gppro-google-webfonts' ),
				'css'   => '"Playfair Display", serif',
				'src'   => 'web',
				'val'   => 'Playfair+Display:400,700,400italic',
				'size'  => '78',
			),

			'podkova'   => array(
				'label' => __( 'Podkova', 'gppro-google-webfonts' ),
				'css'   => '"Podkova", serif',
				'src'   => 'web',
				'val'   => 'Podkova:400,700',
				'size'  => '72',
			),

			'rokkitt'   => array(
				'label' => __( 'Rokkitt', 'gppro-google-webfonts' ),
				'css'   => '"Rokkitt", serif',
				'src'   => 'web',
				'val'   => 'Rokkitt:400,700',
				'size'  => '52',
			),

			'pt-serif'  => array(
				'label' => __( 'PT Serif', 'gppro-google-webfonts' ),
				'css'   => '"PT Serif", serif',
				'src'   => 'web',
				'val'   => 'PT+Serif:400,700',
				'size'  => '88',
			),

			'roboto-slab'   => array(
				'label' => __( 'Roboto Slab', 'gppro-google-webfonts' ),
				'css'   => '"Roboto Slab", serif',
				'src'   => 'web',
				'val'   => 'Roboto+Slab:300,400,700',
				'size'  => '36',
			),

			'quattrocento'  => array(
				'label' => __( 'Quattrocento', 'gppro-google-webfonts' ),
				'css'   => '"Quattrocento", serif',
				'src'   => 'web',
				'val'   => 'Quattrocento:400,700',
				'size'  => '54',
			),

			'source-serif-pro'  => array(
				'label' => __( 'Source Serif Pro', 'gppro-google-webfonts' ),
				'css'   => '"Source Serif Pro", serif',
				'src'   => 'web',
				'val'   => 'Source+Serif+Pro:400,700',
				'size'  => '48',
			),

			'vollkorn'  => array(
				'label' => __( 'Vollkorn', 'gppro-google-webfonts' ),
				'css'   => '"Vollkorn", serif',
				'src'   => 'web',
				'val'   => 'Vollkorn:400,700,400italic,700italic',
				'size'  => '124',
			),

			// The list of sans serif fonts.
			'abel'  => array(
				'label' => __( 'Abel', 'gppro-google-webfonts' ),
				'css'   => '"Abel", sans-serif',
				'src'   => 'web',
				'val'   => 'Abel',
				'size'  => '16',
			),

			'archivo-narrow'    => array(
				'label' => __( 'Archivo Narrow', 'gppro-google-webfonts' ),
				'css'   => '"Archivo Narrow", sans-serif',
				'src'   => 'web',
				'val'   => 'Archivo+Narrow:400,700,400italic,700italic',
				'size'  => '100',
			),

			'cabin' => array(
				'label' => __( 'Cabin', 'gppro-google-webfonts' ),
				'css'   => '"Cabin", sans-serif',
				'src'   => 'web',
				'val'   => 'Cabin:400,700',
				'size'  => '166',
			),

			'dosis' => array(
				'label' => __( 'Dosis', 'gppro-google-webfonts' ),
				'css'   => '"Dosis", sans-serif',
				'src'   => 'web',
				'val'   => 'Dosis:300,400,700',
				'size'  => '96',
			),

			'inder' => array(
				'label' => __( 'Inder', 'gppro-google-webfonts' ),
				'css'   => '"Inder", sans-serif',
				'src'   => 'web',
				'val'   => 'Inder',
				'size'  => '9',
			),

			'josefin-sans'  => array(
				'label' => __( 'Josefin Sans', 'gppro-google-webfonts' ),
				'css'   => '"Josefin Sans", sans-serif',
				'src'   => 'web',
				'val'   => 'Josefin+Sans:400,700',
				'size'  => '38',
			),

			'lato'  => array(
				'label' => __( 'Lato', 'gppro-google-webfonts' ),
				'css'   => '"Lato", sans-serif',
				'src'   => 'web',
				'val'   => 'Lato:300,400,700',
				'size'  => '150',
			),

			'montserrat'    => array(
				'label' => __( 'Montserrat', 'gppro-google-webfonts' ),
				'css'   => '"Montserrat", sans-serif',
				'src'   => 'web',
				'val'   => 'Montserrat:400,700',
				'size'  => '28',
			),

			'noto-sans'    => array(
				'label' => __( 'Noto Sans', 'gppro-google-webfonts' ),
				'css'   => '"Noto Sans", sans-serif',
				'src'   => 'web',
				'val'   => 'Noto+Sans:400,400i,700,700i',
				'size'  => '36',
			),

			'open-sans' => array(
				'label' => __( 'Open Sans', 'gppro-google-webfonts' ),
				'css'   => '"Open Sans", sans-serif',
				'src'   => 'web',
				'val'   => 'Open+Sans:300,400,700,300italic,400italic,700italic',
				'size'  => '90',
			),

			'open-sans-condensed'   => array(
				'label' => __( 'Open Sans Condensed', 'gppro-google-webfonts' ),
				'css'   => '"Open Sans Condensed", sans-serif',
				'src'   => 'web',
				'val'   => 'Open+Sans+Condensed:300,700,300italic',
				'size'  => '51',
			),

			'orienta'   => array(
				'label' => __( 'Orienta', 'gppro-google-webfonts' ),
				'css'   => '"Orienta", sans-serif',
				'src'   => 'web',
				'val'   => 'Orienta',
				'size'  => '13',
			),

			'oswald'    => array(
				'label' => __( 'Oswald', 'gppro-google-webfonts' ),
				'css'   => '"Oswald", sans-serif',
				'src'   => 'web',
				'val'   => 'Oswald:400,700',
				'size'  => '26',
			),

			'oxygen'    => array(
				'label' => __( 'Oxygen', 'gppro-google-webfonts' ),
				'css'   => '"Oxygen", sans-serif',
				'src'   => 'web',
				'val'   => 'Oxygen:300,400,700',
				'size'  => '51',
			),

			'pathway-gothic' => array(
				'label' => __( 'Pathway Gothic One', 'gppro-google-webfonts' ),
				'css'   => '"Pathway Gothic One", sans-serif',
				'src'   => 'web',
				'val'   => 'Pathway+Gothic+One',
				'size'  => '7',
			),

			'quicksand' => array(
				'label' => __( 'Quicksand', 'gppro-google-webfonts' ),
				'css'   => '"Quicksand", san-serif',
				'src'   => 'web',
				'val'   => 'Quicksand:300,400,700',
				'size'  => '39',
			),

			'roboto-condensed' => array(
				'label' => __( 'Roboto Condensed', 'gppro-google-webfonts' ),
				'css'   => '"Roboto Condensed", sans-serif',
				'src'   => 'web',
				'val'   => 'Roboto+Condensed:300,400,700,300italic,400italic,700italic',
				'size'  => '66',
			),

			'quattrocento-sans' => array(
				'label' => __( 'Quattrocento Sans', 'gppro-google-webfonts' ),
				'css'   => '"Quattrocento Sans", sans-serif',
				'src'   => 'web',
				'val'   => 'Quattrocento+Sans:400,700,400italic,700italic',
				'size'  => '76',
			),

			'raleway'   => array(
				'label' => __( 'Raleway', 'gppro-google-webfonts' ),
				'css'   => '"Raleway", sans-serif',
				'src'   => 'web',
				'val'   => 'Raleway:400,500,900',
				'size'  => '177',
			),

			'roboto'    => array(
				'label' => __( 'Roboto', 'gppro-google-webfonts' ),
				'css'   => '"Roboto", sans-serif',
				'src'   => 'web',
				'val'   => 'Roboto:400,700,400italic,700italic',
				'size'  => '40',
			),

			'signika'   => array(
				'label' => __( 'Signika', 'gppro-google-webfonts' ),
				'css'   => '"Signika", sans-serif',
				'src'   => 'web',
				'val'   => 'Signika:300,400,600,700',
				'size'  => '148',
			),

			'source-sans-pro'   => array(
				'label' => __( 'Source Sans Pro', 'gppro-google-webfonts' ),
				'css'   => '"Source Sans Pro", sans-serif',
				'src'   => 'web',
				'val'   => 'Source+Sans+Pro:300,400,700,300italic,400italic,700italic',
				'size'  => '108',
			),

			'syncopate' => array(
				'label' => __( 'Syncopate', 'gppro-google-webfonts' ),
				'css'   => '"Syncopate", sans-serif',
				'src'   => 'web',
				'val'   => 'Syncopate:400,700',
				'size'  => '134',
			),

			// The list of cursive fonts.
			'arizonia'  => array(
				'label' => __( 'Arizonia', 'gppro-google-webfonts' ),
				'css'   => '"Arizonia", cursive',
				'src'   => 'web',
				'val'   => 'Arizonia',
				'size'  => '13',
			),

			'bilbo-swash'   => array(
				'label' => __( 'Bilbo Swash Caps', 'gppro-google-webfonts' ),
				'css'   => '"Bilbo Swash Caps", cursive',
				'src'   => 'web',
				'val'   => 'Bilbo+Swash+Caps',
				'size'  => '14',
			),

			'cabin-sketch'  => array(
				'label' => __( 'Cabin Sketch', 'gppro-google-webfonts' ),
				'css'   => '"Cabin Sketch", cursive',
				'src'   => 'web',
				'val'   => 'Cabin+Sketch:400,700',
				'size'  => '202',
			),

			'calligraffitti'    => array(
				'label' => __( 'Calligraffitti', 'gppro-google-webfonts' ),
				'css'   => '"Calligraffitti", cursive',
				'src'   => 'web',
				'val'   => 'Calligraffitti',
				'size'  => '36',
			),

			'dancing-script'    => array(
				'label' => __( 'Dancing Script', 'gppro-google-webfonts' ),
				'css'   => '"Dancing Script", cursive',
				'src'   => 'web',
				'val'   => 'Dancing+Script:400,700',
				'size'  => '116',
			),

			'fredericka-the-great'  => array(
				'label' => __( 'Fredericka the Great', 'gppro-google-webfonts' ),
				'css'   => '"Fredericka the Great", cursive',
				'src'   => 'web',
				'val'   => 'Fredericka+the+Great:400',
				'size'  => '271',
			),

			'great-vibes'   => array(
				'label' => __( 'Great Vibes', 'gppro-google-webfonts' ),
				'css'   => '"Great Vibes", cursive',
				'src'   => 'web',
				'val'   => 'Great+Vibes',
				'size'  => '24',
			),

			'handlee'   => array(
				'label' => __( 'Handlee', 'gppro-google-webfonts' ),
				'css'   => '"Handlee", cursive',
				'src'   => 'web',
				'val'   => 'Handlee:400',
				'size'  => '22',
			),

			'kaushan-script'    => array(
				'label' => __( 'Kaushan Script', 'gppro-google-webfonts' ),
				'css'   => '"Kaushan Script", cursive',
				'src'   => 'web',
				'val'   => 'Kaushan+Script',
				'size'  => '38',
			),

			'londrina-outline'  => array(
				'label' => __( 'Londrina Outline', 'gppro-google-webfonts' ),
				'css'   => '"Londrina Outline", cursive',
				'src'   => 'web',
				'val'   => 'Londrina+Outline:400',
				'size'  => '42',
			),

			'londrina-sketch'   => array(
				'label' => __( 'Londrina Sketch', 'gppro-google-webfonts' ),
				'css'   => '"Londrina Sketch", cursive',
				'src'   => 'web',
				'val'   => 'Londrina+Sketch:400',
				'size'  => '82',
			),

			'meddon'    => array(
				'label' => __( 'Meddon', 'gppro-google-webfonts' ),
				'css'   => '"Meddon", cursive',
				'src'   => 'web',
				'val'   => 'Meddon',
				'size'  => '83',
			),

			'pacifico'  => array(
				'label' => __( 'Pacifico', 'gppro-google-webfonts' ),
				'css'   => '"Pacifico", cursive',
				'src'   => 'web',
				'val'   => 'Pacifico',
				'size'  => '27',
			),

			'rock-salt' => array(
				'label' => __( 'Rock Salt', 'gppro-google-webfonts' ),
				'css'   => '"Rock Salt", cursive',
				'src'   => 'web',
				'val'   => 'Rock+Salt',
				'size'  => '74',
			),

			'sacramento'  => array(
				'label' => __( 'Sacramento', 'gppro-google-webfonts' ),
				'css'   => '"Sacramento", cursive',
				'src'   => 'web',
				'val'   => 'Sacramento',
				'size'  => '20',
			),

			'sofia'     => array(
				'label' => __( 'Sofia', 'gppro-google-webfonts' ),
				'css'   => '"Sofia", cursive',
				'src'   => 'web',
				'val'   => 'Sofia',
				'size'  => '18',
			),

			// The list of monospace fonts.
			'droid-sans-mono'   => array(
				'label' => __( 'Droid Sans Mono', 'gppro-google-webfonts' ),
				'css'   => '"Droid Sans Mono", monospace',
				'src'   => 'web',
				'val'   => 'Droid+Sans+Mono',
				'size'  => '73',
			),

			'source-code-pro'   => array(
				'label' => __( 'Source Code Pro', 'gppro-google-webfonts' ),
				'css'   => '"Source Code Pro", monospace',
				'src'   => 'web',
				'val'   => 'Source+Code+Pro:400,700',
				'size'  => '48',
			),

			'ubuntu-mono'   => array(
				'label' => __( 'Ubuntu Mono', 'gppro-google-webfonts' ),
				'css'   => '"Ubuntu Mono", monospace',
				'src'   => 'web',
				'val'   => 'Ubuntu+Mono',
				'size'  => '18',
			),

		);

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
		$stacklist  = self::google_stacks();

		// Set up the fonts we are adding.
		$fonts  = array(
			'serif'  => array( // Our list of serif fonts.
				'abril-fatface',
				'arvo',
				'bitter',
				'bree-serif',
				'crimson-text',
				'enriqueta',
				'fenix',
				'lora',
				'josefin-slab',
				'merriweather',
				'neuton',
				'nixie-one',
				'noto-serif',
				'old-standard-tt',
				'playfair-display',
				'podkova',
				'rokkitt',
				'pt-serif',
				'roboto-slab',
				'quattrocento',
				'source-serif-pro',
				'vollkorn',
			),
			'sans'  => array( // Our list of sans-serif fonts.
				'abel',
				'archivo-narrow',
				'cabin',
				'dosis',
				'inder',
				'josefin-sans',
				'lato',
				'montserrat',
				'noto-sans',
				'orienta',
				'open-sans',
				'open-sans-condensed',
				'oswald',
				'oxygen',
				'pathway-gothic',
				'quicksand',
				'roboto-condensed',
				'quattrocento-sans',
				'raleway',
				'roboto',
				'signika',
				'source-sans-pro',
				'syncopate',
			),
			'cursive'  => array( // Our list of cursive fonts.
				'arizonia',
				'bilbo-swash',
				'cabin-sketch',
				'calligraffitti',
				'fredericka-the-great',
				'dancing-script',
				'great-vibes',
				'kaushan-script',
				'handlee',
				'londrina-outline',
				'londrina-sketch',
				'meddon',
				'pacifico',
				'rock-salt',
				'sacramento',
				'sofia',
			),
			'mono'  => array( // Our list of monospace fonts.
				'droid-sans-mono',
				'source-code-pro',
				'ubuntu-mono',
			),
		);

		// Filter the list prior to doing the check.
		$fonts  = apply_filters( 'gppro_webfont_families', $fonts );

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
$GP_Pro_Google_Webfonts = GP_Pro_Google_Webfonts::getInstance();
