<?php
/*
Plugin Name: Genesis Design Palette Pro - Google Webfonts
Plugin URI: https://genesisdesignpro.com/
Description: Adds a set of popular Google Webfonts to Design Palette Pro
Author: Reaktiv Studios
Version: 1.0.6
Requires at least: 3.7
Author URI: http://andrewnorcross.com
*/
/*  Copyright 2014 Andrew Norcross

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

if( ! defined( 'GPGWF_BASE' ) ) {
	define( 'GPGWF_BASE', plugin_basename(__FILE__) );
}

if( ! defined( 'GPGWF_DIR' ) ) {
	define( 'GPGWF_DIR', dirname( __FILE__ ) );
}

if( ! defined( 'GPGWF_VER' ) ) {
	define( 'GPGWF_VER', '1.0.6' );
}

class GP_Pro_Google_Webfonts
{

	/**
	 * Static property to hold our singleton instance
	 * @var
	 */
	static $instance = false;

	/**
	 * This is our constructor
	 *
	 * @return
	 */
	private function __construct() {

		// general backend
		add_action( 'plugins_loaded',                   array( $this, 'textdomain'              )           );
		add_action( 'admin_notices',                    array( $this, 'gppro_active_check'      ),  10      );
		add_action( 'admin_notices',                    array( $this, 'pagespeed_alert'         ),  10      );

		// front end
		add_action( 'wp_enqueue_scripts',               array( $this, 'font_scripts'            )           );

		// GP Pro specific
		add_filter( 'gppro_font_stacks',                array( $this, 'google_stack_list'       ),  99      );
	}

	/**
	 * If an instance exists, this returns it.  If not, it creates one and
	 * retuns it.
	 *
	 * @return GP_Pro_Freeform_CSS
	 */
	public static function getInstance() {

		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * load textdomain
	 *
	 * @return
	 */
	public function textdomain() {
		load_plugin_textdomain( 'gppro-google-webfonts', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * check for GP Pro being active
	 *
	 * @return
	 */
	public function gppro_active_check() {

		// get the current screen
		$screen = get_current_screen();

		// bail if not on the plugins page
		if ( ! is_object( $screen ) || empty( $screen->parent_file ) || $screen->parent_file !== 'plugins.php' ) {
			return;
		}

		// run the active check
		$coreactive = class_exists( 'Genesis_Palette_Pro' ) ? Genesis_Palette_Pro::check_active() : false;

		// active. bail
		if ( $coreactive ) {
			return;
		}

		// not active. show message
		echo '<div id="message" class="error fade below-h2"><p><strong>' . __( 'This plugin requires Genesis Design Palette Pro to function and cannot be activated.', 'gppro-google-webfonts' ).'</strong></p></div>';

		// hide activation method
		unset( $_GET['activate'] );

		// deactivate the plugin
		deactivate_plugins( plugin_basename( __FILE__ ) );

		// and finish
		return;
	}

	/**
	 * check for high font number
	 *
	 * @return
	 */
	public function pagespeed_alert() {

		// get the current screen
		$screen = get_current_screen();

		// bail if not on the base DPP page
		if ( ! is_object( $screen ) || is_object( $screen ) && $screen->base !== 'genesis_page_genesis-palette-pro' ) {
			return;
		}

		// check out pagespeed alert
		$alert  = get_option( 'gppro-webfont-alert' );

		// check for each possible alert
		if ( ! isset( $alert ) || empty( $alert ) || $alert == 'ignore' ) {
			return;
		}

		// check child theme, display warning
		echo '<div id="message" class="error fade below-h2 gppro-admin-warning gppro-admin-warning-webfonts"><p>';
		echo '<strong>' . __( 'Warning: You have selected multiple webfonts which could have a severe impact on site performance.', 'gppro-google-webfonts' ) . '</strong>';
		echo '<span class="webfont-ignore">'.__( 'Ignore this message', 'gppro-google-webfonts' ).'</span>';
		echo '</p></div>';
	}

	/**
	 * register alert for pagespeed test
	 *
	 * @return
	 */
	public static function pagespeed_check( $fontsize ) {

		// fetch the existing alert, if it exists
		$alert  = get_option( 'gppro-webfont-alert' );

		// sum the total we are adding
		$totals = array_sum( $fontsize );

		// set our high number point with a filter
		$filter = apply_filters( 'gppro_webfont_alert', 250 );

		// delete the alert if less than alert amount
		if ( $totals < absint( $filter ) ) {
			delete_option( 'gppro-webfont-alert' );
		}

		// check my alert status before moving on
		if ( ! empty( $alert ) && $alert == 'ignore' ) {
			return;
		}

		// set alert flag for over alert amount
		if ( $totals >= absint( $filter ) ) {
			add_option( 'gppro-webfont-alert', true, null, 'no' );
		}
	}

	/**
	 * call webfont CSS files
	 *
	 * @return
	 */
	public static function font_scripts() {

		// fetch our font string and enqueue the CSS
		if ( false !== $string = self::font_choice_string() ) {
			wp_enqueue_style( 'gppro-webfonts', '//fonts.googleapis.com/css?family=' . $string, array(), GPGWF_VER );
		}
	}

	/**
	 * helper to create array of font options and combine into string
	 *
	 * @return
	 */
	public static function font_choice_string() {

		// fetch list of active fonts
		$actives    = self::font_choice_active();

		// bail with no actives
		if ( empty( $actives ) ) {
			return false;
		}

		// set value arrays to false
		$fontarr    = false;
		$fontsize   = false;

		// loop them all
		foreach ( $actives as $active ) {

			// get individual font data
			$data   = self::single_font_fetch( $active );

			// bail if it came back native (i.e. already loaded )
			if ( $data['src'] == 'native' ) {
				continue;
			}

			// pass it into array and go forth
			$fontarr[]	= $data['val'];
			$fontsize[]	= $data['size'];
		}

		// bail if nothing is there
		if ( ! $fontarr ) {
			return false;
		}

		// cast into array
		$fontarr    = (array) $fontarr;
		$fontsize   = (array) $fontsize;

		// run font weight check for pagespeed alert
		if ( $fontsize ) {
			$pagespeed	= self::pagespeed_check( $fontsize );
		}

		// implode into string with divider and send it back
		return implode( '|', $fontarr );
	}

	/**
	 * helper to determine if a font has actually been selected and creates an array
	 *
	 * @return
	 */
	public static function font_choice_active() {

		// fetch our list of stacks
		$stacklist  = self::google_stacks();
		$stackkeys  = array_keys( $stacklist );

		// grab our settings
		$settings  = get_option( 'gppro-settings' );

		// bail with no DPP settings
		if ( ! $settings ) {
			return false;
		}

		// set an empty array
		$choices    = array();

		// filter through and run comparison
		foreach ( $settings as $key => $value ) {

			// add our things
			if ( in_array( $value, $stackkeys ) ) {
				$choices[]	= $value;
			}
		}

		// return our choices
		return array_unique( $choices );
	}

	/**
	 * helper to fetch the values from a single stack
	 *
	 * @return
	 */
	public static function single_font_fetch( $font = '' ) {

		// bail if no font was passed
		if ( empty( $font ) ) {
			return false;
		}

		// fetch our list of stacks
		$stacklist	= self::google_stacks();

		// return the single requested
		return ! empty( $stacklist[$font] ) ? $stacklist[$font] : false;
	}

	/**
	 * create list of stacks to use in various locations
	 *
	 * @return
	 */
	public static function google_stacks() {

		// set the array of fonts
		$webfonts	= array(

			// serif fonts
			'abril-fatface'	=> array(
				'label'	=> __( 'Abril Fatface', 'gppro-google-webfonts' ),
				'css'	=> '"Abril Fatface", serif',
				'src'	=> 'web',
				'val'	=> 'Abril+Fatface',
				'size'	=> '14',
			),

			'arvo'	=> array(
				'label'	=> __( 'Arvo', 'gppro-google-webfonts' ),
				'css'	=> '"Arvo", serif',
				'src'	=> 'web',
				'val'	=> 'Arvo:400,700,400italic,700italic',
				'size'	=> '104',
			),

			'bitter'	=> array(
				'label'	=> __( 'Bitter', 'gppro-google-webfonts' ),
				'css'	=> '"Bitter", serif',
				'src'	=> 'web',
				'val'	=> 'Bitter:400,700,400italic',
				'size'	=> '66',
			),

			'bree-serif'	=> array(
				'label'	=> __( 'Bree Serif', 'gppro-google-webfonts' ),
				'css'	=> '"Bree Serif", serif',
				'src'	=> 'web',
				'val'	=> 'Bree+Serif',
				'size'	=> '11',
			),

			'crimson-text'	=> array(
				'label'	=> __( 'Crimson Text', 'gppro-google-webfonts' ),
				'css'	=> '"Crimson Text", serif',
				'src'	=> 'web',
				'val'	=> 'Crimson+Text:400,700',
				'size'	=> '186',
			),

			'enriqueta'	=> array(
				'label'	=> __( 'Enriqueta', 'gppro-google-webfonts' ),
				'css'	=> '"Enriqueta", serif',
				'src'	=> 'web',
				'val'	=> 'Enriqueta:400,700',
				'size'	=> '22',
			),

			'fenix'		=> array(
				'label'	=> __( 'Fenix', 'gppro-google-webfonts' ),
				'css'	=> '"Fenix", serif',
				'src'	=> 'web',
				'val'	=> 'Fenix',
				'size'	=> '8',
			),

			'lora'	=> array(
				'label'	=> __( 'Lora', 'gppro-google-webfonts' ),
				'css'	=> '"Lora", serif',
				'src'	=> 'web',
				'val'	=> 'Lora:400,700,400italic,700italic',
				'size'	=> '112',
			),

			'josefin-slab'	=> array(
				'label'	=> __( 'Josefin Slab', 'gppro-google-webfonts' ),
				'css'	=> '"Josefin Slab", serif',
				'src'	=> 'web',
				'val'	=> 'Josefin+Slab:400,700',
				'size'	=> '102',
			),

			'merriweather'	=> array(
				'label'	=> __( 'Merriweather', 'gppro-google-webfonts' ),
				'css'	=> '"Merriweather", serif',
				'src'	=> 'web',
				'val'	=> 'Merriweather:400,700,400italic,700italic',
				'size'	=> '44',
			),

			'neuton'    => array(
				'label'	=> __( 'Neuton', 'gppro-google-webfonts' ),
				'css'	=> '"Neuton", serif',
				'src'	=> 'web',
				'val'	=> 'Neuton:300,400,700,400italic',
				'size'	=> '56',
			),

			'nixie-one'	=> array(
				'label'	=> __( 'Nixie One', 'gppro-google-webfonts' ),
				'css'	=> '"Nixie One", serif',
				'src'	=> 'web',
				'val'	=> 'Nixie+One',
				'size'	=> '39',
			),

			'old-standard-tt'	=> array(
				'label'	=> __( 'Old Standard TT', 'gppro-google-webfonts' ),
				'css'	=> '"Old Standard TT", serif',
				'src'	=> 'web',
				'val'	=> 'Old+Standard+TT:400,700,400italic',
				'size'	=> '93',
			),

			'playfair-display'	=> array(
				'label'	=> __( 'Playfair Display', 'gppro-google-webfonts' ),
				'css'	=> '"Playfair Display", serif',
				'src'	=> 'web',
				'val'	=> 'Playfair+Display:400,700,400italic',
				'size'	=> '78',
			),

			'podkova'	=> array(
				'label'	=> __( 'Podkova', 'gppro-google-webfonts' ),
				'css'	=> '"Podkova", serif',
				'src'	=> 'web',
				'val'	=> 'Podkova:400,700',
				'size'	=> '72',
			),

			'rokkitt'	=> array(
				'label'	=> __( 'Rokkitt', 'gppro-google-webfonts' ),
				'css'	=> '"Rokkitt", serif',
				'src'	=> 'web',
				'val'	=> 'Rokkitt:400,700',
				'size'	=> '52',
			),

			'pt-serif'	=> array(
				'label'	=> __( 'PT Serif', 'gppro-google-webfonts' ),
				'css'	=> '"PT Serif", serif',
				'src'	=> 'web',
				'val'	=> 'PT+Serif:400,700',
				'size'	=> '88',
			),

			'roboto-slab'	=> array(
				'label'	=> __( 'Roboto Slab', 'gppro-google-webfonts' ),
				'css'	=> '"Roboto Slab", serif',
				'src'	=> 'web',
				'val'	=> 'Roboto+Slab:300,400,700',
				'size'	=> '36',
			),

			'quattrocento'	=> array(
				'label'	=> __( 'Quattrocento', 'gppro-google-webfonts' ),
				'css'	=> '"Quattrocento", serif',
				'src'	=> 'web',
				'val'	=> 'Quattrocento:400,700',
				'size'	=> '54',
			),

			'source-serif-pro'  => array(
				'label'	=> __( 'Source Serif Pro', 'gppro-google-webfonts' ),
				'css'	=> '"Source Serif Pro", serif',
				'src'	=> 'web',
				'val'	=> 'Source+Serif+Pro:400,700',
				'size'	=> '48',
			),

			'vollkorn'	=> array(
				'label'	=> __( 'Vollkorn', 'gppro-google-webfonts' ),
				'css'	=> '"Vollkorn", serif',
				'src'	=> 'web',
				'val'	=> 'Vollkorn:400,700,400italic,700italic',
				'size'	=> '124',
			),

			// sans serif fonts

			'abel'	=> array(
				'label'	=> __( 'Abel', 'gppro-google-webfonts' ),
				'css'	=> '"Abel", sans-serif',
				'src'	=> 'web',
				'val'	=> 'Abel',
				'size'	=> '16',
			),

			'archivo-narrow'	=> array(
				'label'	=> __( 'Archivo Narrow', 'gppro-google-webfonts' ),
				'css'	=> '"Archivo Narrow", sans-serif',
				'src'	=> 'web',
				'val'	=> 'Archivo+Narrow:400,700,400italic,700italic',
				'size'	=> '100',
			),

			'cabin'	=> array(
				'label'	=> __( 'Cabin', 'gppro-google-webfonts' ),
				'css'	=> '"Cabin", sans-serif',
				'src'	=> 'web',
				'val'	=> 'Cabin:400,700',
				'size'	=> '166',
			),

			'dosis'	=> array(
				'label'	=> __( 'Dosis', 'gppro-google-webfonts' ),
				'css'	=> '"Dosis", sans-serif',
				'src'	=> 'web',
				'val'	=> 'Dosis:300,400,700',
				'size'	=> '96',
			),

			'inder'	=> array(
				'label'	=> __( 'Inder', 'gppro-google-webfonts' ),
				'css'	=> '"Inder", sans-serif',
				'src'	=> 'web',
				'val'	=> 'Inder',
				'size'	=> '9',
			),

			'josefin-sans'	=> array(
				'label'	=> __( 'Josefin Sans', 'gppro-google-webfonts' ),
				'css'	=> '"Josefin Sans", sans-serif',
				'src'	=> 'web',
				'val'	=> 'Josefin+Sans:400,700',
				'size'	=> '38',
			),

			'lato'	=> array(
				'label'	=> __( 'Lato', 'gppro-google-webfonts' ),
				'css'	=> '"Lato", sans-serif',
				'src'	=> 'web',
				'val'	=> 'Lato:300,400,700',
				'size'	=> '150',
			),

			'montserrat'	=> array(
				'label'	=> __( 'Montserrat', 'gppro-google-webfonts' ),
				'css'	=> '"Montserrat", sans-serif',
				'src'	=> 'web',
				'val'	=> 'Montserrat:400,700',
				'size'	=> '28',
			),

			'open-sans'	=> array(
				'label'	=> __( 'Open Sans', 'gppro-google-webfonts' ),
				'css'	=> '"Open Sans", sans-serif',
				'src'	=> 'web',
				'val'	=> 'Open+Sans:300,400,700,300italic,400italic,700italic',
				'size'	=> '90',
			),

			'open-sans-condensed'	=> array(
				'label'	=> __( 'Open Sans Condensed', 'gppro-google-webfonts' ),
				'css'	=> '"Open Sans Condensed", sans-serif',
				'src'	=> 'web',
				'val'	=> 'Open+Sans+Condensed:300,700,300italic',
				'size'	=> '51',
			),

			'orienta'	=> array(
				'label'	=> __( 'Orienta', 'gppro-google-webfonts' ),
				'css'	=> '"Orienta", sans-serif',
				'src'	=> 'web',
				'val'	=> 'Orienta',
				'size'	=> '13',
			),

			'oswald'	=> array(
				'label'	=> __( 'Oswald', 'gppro-google-webfonts' ),
				'css'	=> '"Oswald", sans-serif',
				'src'	=> 'web',
				'val'	=> 'Oswald:400,700',
				'size'	=> '26',
			),

			'oxygen'	=> array(
				'label'	=> __( 'Oxygen', 'gppro-google-webfonts' ),
				'css'	=> '"Oxygen", sans-serif',
				'src'	=> 'web',
				'val'	=> 'Oxygen:300,400,700',
				'size'	=> '51',
			),

			'pathway-gothic' => array(
				'label'	=> __( 'Pathway Gothic One', 'gppro-google-webfonts' ),
				'css'	=> '"Pathway Gothic One", sans-serif',
				'src'	=> 'web',
				'val'	=> 'Pathway+Gothic+One',
				'size'	=> '7',
			),

			'roboto-condensed' => array(
				'label'	=> __( 'Roboto Condensed', 'gppro-google-webfonts' ),
				'css'	=> '"Roboto Condensed", sans-serif',
				'src'	=> 'web',
				'val'	=> 'Roboto+Condensed:300,400,700,300italic,400italic,700italic',
				'size'	=> '66',
			),

			'quattrocento-sans'	=> array(
				'label'	=> __( 'Quattrocento Sans', 'gppro-google-webfonts' ),
				'css'	=> '"Quattrocento Sans", sans-serif',
				'src'	=> 'web',
				'val'	=> 'Quattrocento+Sans:400,700,400italic,700italic',
				'size'	=> '76',
			),

			'raleway'	=> array(
				'label'	=> __( 'Raleway', 'gppro-google-webfonts' ),
				'css'	=> '"Raleway", sans-serif',
				'src'	=> 'web',
				'val'	=> 'Raleway:400,500,900',
				'size'	=> '177',
			),

			'roboto'	=> array(
				'label'	=> __( 'Roboto', 'gppro-google-webfonts' ),
				'css'	=> '"Roboto", sans-serif',
				'src'	=> 'web',
				'val'	=> 'Roboto:400,700,400italic,700italic',
				'size'	=> '40',
			),

			'signika'	=> array(
				'label'	=> __( 'Signika', 'gppro-google-webfonts' ),
				'css'	=> '"Signika", sans-serif',
				'src'	=> 'web',
				'val'	=> 'Signika:300,400,600,700',
				'size'	=> '148',
			),

			'source-sans-pro'	=> array(
				'label'	=> __( 'Source Sans Pro', 'gppro-google-webfonts' ),
				'css'	=> '"Source Sans Pro", sans-serif',
				'src'	=> 'web',
				'val'	=> 'Source+Sans+Pro:300,400,700,300italic,400italic,700italic',
				'size'	=> '108',
			),

			'syncopate'	=> array(
				'label'	=> __( 'Syncopate', 'gppro-google-webfonts' ),
				'css'	=> '"Syncopate", sans-serif',
				'src'	=> 'web',
				'val'	=> 'Syncopate:400,700',
				'size'	=> '134',
			),

			// cursive fonts

			'arizonia'	=> array(
				'label'	=> __( 'Arizonia', 'gppro-google-webfonts' ),
				'css'	=> '"Arizonia", cursive',
				'src'	=> 'web',
				'val'	=> 'Arizonia',
				'size'	=> '13',
			),

			'bilbo-swash'	=> array(
				'label'	=> __( 'Bilbo Swash Caps', 'gppro-google-webfonts' ),
				'css'	=> '"Bilbo Swash Caps", cursive',
				'src'	=> 'web',
				'val'	=> 'Bilbo+Swash+Caps',
				'size'	=> '14',
			),

			'calligraffitti'	=> array(
				'label'	=> __( 'Calligraffitti', 'gppro-google-webfonts' ),
				'css'	=> '"Calligraffitti", cursive',
				'src'	=> 'web',
				'val'	=> 'Calligraffitti',
				'size'	=> '36',
			),

			'dancing-script'	=> array(
				'label'	=> __( 'Dancing Script', 'gppro-google-webfonts' ),
				'css'	=> '"Dancing Script", cursive',
				'src'	=> 'web',
				'val'	=> 'Dancing+Script:400,700',
				'size'	=> '116',
			),

			'great-vibes'	=> array(
				'label'	=> __( 'Great Vibes', 'gppro-google-webfonts' ),
				'css'	=> '"Great Vibes", cursive',
				'src'	=> 'web',
				'val'	=> 'Great+Vibes',
				'size'	=> '24',
			),

			'kaushan-script'	=> array(
				'label'	=> __( 'Kaushan Script', 'gppro-google-webfonts' ),
				'css'	=> '"Kaushan Script", cursive',
				'src'	=> 'web',
				'val'	=> 'Kaushan+Script',
				'size'	=> '38',
			),

			'meddon'	=> array(
				'label'	=> __( 'Meddon', 'gppro-google-webfonts' ),
				'css'	=> '"Meddon", cursive',
				'src'	=> 'web',
				'val'	=> 'Meddon',
				'size'	=> '83',
			),

			'pacifico'	=> array(
				'label'	=> __( 'Pacifico', 'gppro-google-webfonts' ),
				'css'	=> '"Pacifico", cursive',
				'src'	=> 'web',
				'val'	=> 'Pacifico',
				'size'	=> '27',
			),

			'rock-salt'	=> array(
				'label'	=> __( 'Rock Salt', 'gppro-google-webfonts' ),
				'css'	=> '"Rock Salt", cursive',
				'src'	=> 'web',
				'val'	=> 'Rock+Salt',
				'size'	=> '74',
			),

			'sacramento'  => array(
				'label'	=> __( 'Sacramento', 'gppro-google-webfonts' ),
				'css'	=> '"Sacramento", cursive',
				'src'	=> 'web',
				'val'	=> 'Sacramento',
				'size'	=> '20',
			),

			'sofia'     => array(
				'label'	=> __( 'Sofia', 'gppro-google-webfonts' ),
				'css'	=> '"Sofia", cursive',
				'src'	=> 'web',
				'val'	=> 'Sofia',
				'size'	=> '18',
			),

			// monospace fonts

			'droid-sans-mono'	=> array(
				'label'	=> __( 'Droid Sans Mono', 'gppro-google-webfonts' ),
				'css'	=> '"Droid Sans Mono", monospace',
				'src'	=> 'web',
				'val'	=> 'Droid+Sans+Mono',
				'size'	=> '73',
			),

			'ubuntu-mono'	=> array(
				'label'	=> __( 'Ubuntu Mono', 'gppro-google-webfonts' ),
				'css'	=> '"Ubuntu Mono", monospace',
				'src'	=> 'web',
				'val'	=> 'Ubuntu+Mono',
				'size'	=> '18',
			),

		);

		// filter them all and return
		return apply_filters( 'gppro_webfont_stacks', $webfonts );
	}

	/**
	 * add stacks to the dropdown
	 *
	 * @return
	 */
	public function google_stack_list( $stacks = array() ) {

		// fetch our list of stacks
		$stacklist	= self::google_stacks();

		// set up the fonts we are adding
		$fonts  = array(
			// serif fonts
			'serif'  => array(
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
				'old-standard-tt',
				'playfair-display',
				'podkova',
				'rokkitt',
				'pt-serif',
				'roboto-slab',
				'quattrocento',
				'source-serif-pro',
				'vollkorn'
			),
			// sans-serif fonts
			'sans'  => array(
				'abel',
				'archivo-narrow',
				'cabin',
				'dosis',
				'inder',
				'josefin-sans',
				'lato',
				'montserrat',
				'orienta',
				'open-sans',
				'open-sans-condensed',
				'oswald',
				'oxygen',
				'pathway-gothic',
				'roboto-condensed',
				'quattrocento-sans',
				'raleway',
				'roboto',
				'signika',
				'source-sans-pro',
				'syncopate'
			),
			// cursive fonts
			'cursive'  => array(
				'arizonia',
				'bilbo-swash',
				'calligraffitti',
				'dancing-script',
				'great-vibes',
				'kaushan-script',
				'meddon',
				'pacifico',
				'rock-salt',
				'sacramento',
				'sofia'
			),
			// monospace fonts
			'mono'  => array(
				'droid-sans-mono',
				'ubuntu-mono'
			)
		);

		// filter the list prior to doing the check
		$fonts  = apply_filters( 'gppro_webfont_families', $fonts );

		// loop the type groups
		foreach ( $fonts as $type => $families ) {

			// now loop the individual families
			foreach ( $families as $family ) {

				// if we dont already have the font, add it
				if ( ! isset( $stacks[$type][$family] ) ) {
					$stacks[$type][$family] = $stacklist[$family];
				}
			}
		}

		// filter them all and send back stacks
		return apply_filters( 'gppro_webfont_stack_list', $stacks );
	}

/// end class
}

// Instantiate our class
$GP_Pro_Google_Webfonts = GP_Pro_Google_Webfonts::getInstance();