<?php
/*
Plugin Name: Genesis Design Palette Pro - Google Webfonts
Plugin URI: https://genesisdesignpro.com/
Description: Adds a set of popular Google Webfonts to Design Palette Pro
Author: Reaktiv Studios
Version: 1.0.5
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
	define( 'GPGWF_VER', '1.0.5' );
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
		if ( $screen->parent_file !== 'plugins.php' ) {
			return;
		}

		// run the active check
		$coreactive = class_exists( 'Genesis_Palette_Pro' ) ? Genesis_Palette_Pro::check_active() : false;

		// active. bail
		if ( $coreactive ) {
			return;
		}

		// not active. show message
		echo '<div id="message" class="error fade below-h2"><p><strong>' . __( sprintf( 'This plugin requires Genesis Design Palette Pro to function and cannot be activated.' ), 'gppro-google-webfonts' ).'</strong></p></div>';
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
		if ( $screen->base !== 'genesis_page_genesis-palette-pro' ) {
			return;
		}

		// check out pagespeed alert
		$alert  = get_option( 'gppro-webfont-alert' );

		// check for each possible alert
		if ( ! isset( $alert ) || empty( $alert ) || $alert == 'ignore' ) {
			return;
		}

		// check child theme, display warning
		echo '<div id="message" class="error fade below-h2 gppro-admin-warning"><p>';
		echo '<strong>'.__( 'Warning: You have selected multiple webfonts which could have a severe impact on site performance.', 'gppro-google-webfonts' ).'</strong>';
		echo '<span class="webfont-ignore">'.__( 'Ignore this message', 'gppro-google-webfonts' ).'</span>';
		echo '</p></div>';
	}

	/**
	 * register alert for pagespeed test
	 *
	 * @return
	 */
	static function pagespeed_check( $fontsize ) {

		$alert  = get_option( 'gppro-webfont-alert' );

		$totals = array_sum( $fontsize );

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
			update_option( 'gppro-webfont-alert', true );
		}
	}

	/**
	 * call webfont CSS files
	 *
	 * @return
	 */
	static function font_scripts() {

		// fetch our font string
		$string = self::font_choice_string();

		// bail with no string
		if ( ! $string ) {
			return;
		}

		// enqueue the CSS
		wp_enqueue_style( 'gppro-webfonts', '//fonts.googleapis.com/css?family=' . $string, array(), GPGWF_VER );
	}

	/**
	 * helper to create array of font options and combine into string
	 *
	 * @return
	 */
	static function font_choice_string() {

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

		// implode into string with divider
		$string     = implode( '|', $fontarr );

		// send it back
		return $string;
	}

	/**
	 * helper to determine if a font has actually been selected and creates an array
	 *
	 * @return
	 */
	static function font_choice_active() {

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
	static function single_font_fetch( $font = '' ) {

		// fetch our list of stacks
		$stacklist	= self::google_stacks();

		// return the single requested
		return $stacklist[$font];
	}

	/**
	 * create list of stacks to use in various locations
	 *
	 * @return
	 */
	static function google_stacks() {

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

		// filter them all
		$webfonts	= apply_filters( 'gppro_webfont_stacks', $webfonts );

		return $webfonts;

	}

	/**
	 * add stacks to the dropdown
	 *
	 * @return
	 */
	public function google_stack_list( $stacks ) {

		// fetch our list of stacks
		$stacklist	= self::google_stacks();

		// serif fonts

		if ( ! isset( $stacks['serif']['abril-fatface'] ) )
			$stacks['serif']['abril-fatface'] = $stacklist['abril-fatface'];

		if ( ! isset( $stacks['serif']['arvo'] ) )
			$stacks['serif']['arvo'] = $stacklist['arvo'];

		if ( ! isset( $stacks['serif']['bitter'] ) )
			$stacks['serif']['bitter'] = $stacklist['bitter'];

		if ( ! isset( $stacks['serif']['bree-serif'] ) )
			$stacks['serif']['bree-serif'] = $stacklist['bree-serif'];

		if ( ! isset( $stacks['serif']['crimson-text'] ) )
			$stacks['serif']['crimson-text'] = $stacklist['crimson-text'];

		if ( ! isset( $stacks['serif']['enriqueta'] ) )
			$stacks['serif']['enriqueta'] = $stacklist['enriqueta'];

		if ( ! isset( $stacks['serif']['fenix'] ) )
			$stacks['serif']['fenix'] = $stacklist['fenix'];

		if ( ! isset( $stacks['serif']['lora'] ) )
			$stacks['serif']['lora'] = $stacklist['lora'];

		if ( ! isset( $stacks['serif']['josefin-slab'] ) )
			$stacks['serif']['josefin-slab'] = $stacklist['josefin-slab'];

		if ( ! isset( $stacks['serif']['merriweather'] ) )
			$stacks['serif']['merriweather'] = $stacklist['merriweather'];

		if ( ! isset( $stacks['serif']['nixie-one'] ) )
			$stacks['serif']['nixie-one'] = $stacklist['nixie-one'];

		if ( ! isset( $stacks['serif']['old-standard-tt'] ) )
			$stacks['serif']['old-standard-tt'] = $stacklist['old-standard-tt'];

		if ( ! isset( $stacks['serif']['playfair-display'] ) )
			$stacks['serif']['playfair-display'] = $stacklist['playfair-display'];

		if ( ! isset( $stacks['serif']['podkova'] ) )
			$stacks['serif']['podkova'] = $stacklist['podkova'];

		if ( ! isset( $stacks['serif']['rokkitt'] ) )
			$stacks['serif']['rokkitt'] = $stacklist['rokkitt'];

		if ( ! isset( $stacks['serif']['pt-serif'] ) )
			$stacks['serif']['pt-serif'] = $stacklist['pt-serif'];

		if ( ! isset( $stacks['serif']['roboto-slab'] ) )
			$stacks['serif']['roboto-slab'] = $stacklist['roboto-slab'];

		if ( ! isset( $stacks['serif']['quattrocento'] ) )
			$stacks['serif']['quattrocento'] = $stacklist['quattrocento'];

		if ( ! isset( $stacks['serif']['vollkorn'] ) )
			$stacks['serif']['vollkorn'] = $stacklist['vollkorn'];

		// sans-serif fonts

		if ( ! isset( $stacks['sans']['abel'] ) )
			$stacks['sans']['abel'] = $stacklist['abel'];

		if ( ! isset( $stacks['sans']['archivo-narrow'] ) )
			$stacks['sans']['archivo-narrow'] = $stacklist['archivo-narrow'];

		if ( ! isset( $stacks['sans']['cabin'] ) )
			$stacks['sans']['cabin'] = $stacklist['cabin'];

		if ( ! isset( $stacks['sans']['dosis'] ) )
			$stacks['sans']['dosis'] = $stacklist['dosis'];

		if ( ! isset( $stacks['sans']['inder'] ) )
			$stacks['sans']['inder'] = $stacklist['inder'];

		if ( ! isset( $stacks['sans']['josefin-sans'] ) )
			$stacks['sans']['josefin-sans'] = $stacklist['josefin-sans'];

		if ( ! isset( $stacks['sans']['lato'] ) )
			$stacks['sans']['lato'] = $stacklist['lato'];

		if ( ! isset( $stacks['sans']['montserrat'] ) )
			$stacks['sans']['montserrat'] = $stacklist['montserrat'];

		if ( ! isset( $stacks['sans']['orienta'] ) )
			$stacks['sans']['orienta'] = $stacklist['orienta'];

		if ( ! isset( $stacks['sans']['open-sans'] ) )
			$stacks['sans']['open-sans'] = $stacklist['open-sans'];

		if ( ! isset( $stacks['sans']['open-sans-condensed'] ) )
			$stacks['sans']['open-sans-condensed'] = $stacklist['open-sans-condensed'];

		if ( ! isset( $stacks['sans']['oswald'] ) )
			$stacks['sans']['oswald'] = $stacklist['oswald'];

		if ( ! isset( $stacks['sans']['oxygen'] ) )
			$stacks['sans']['oxygen'] = $stacklist['oxygen'];

		if ( ! isset( $stacks['sans']['pathway-gothic'] ) )
			$stacks['sans']['pathway-gothic'] = $stacklist['pathway-gothic'];

		if ( ! isset( $stacks['sans']['quattrocento-sans'] ) )
			$stacks['sans']['quattrocento-sans'] = $stacklist['quattrocento-sans'];

		if ( ! isset( $stacks['sans']['raleway'] ) )
			$stacks['sans']['raleway'] = $stacklist['raleway'];

		if ( ! isset( $stacks['sans']['roboto'] ) )
			$stacks['sans']['roboto'] = $stacklist['roboto'];

		if ( ! isset( $stacks['sans']['signika'] ) )
			$stacks['sans']['signika'] = $stacklist['signika'];

		if ( ! isset( $stacks['sans']['source-sans-pro'] ) )
			$stacks['sans']['source-sans-pro'] = $stacklist['source-sans-pro'];

		if ( ! isset( $stacks['sans']['syncopate'] ) )
			$stacks['sans']['syncopate'] = $stacklist['syncopate'];

		// cursive fonts

		if ( ! isset( $stacks['cursive']['arizonia'] ) )
			$stacks['cursive']['arizonia'] = $stacklist['arizonia'];

		if ( ! isset( $stacks['cursive']['bilbo-swash'] ) )
			$stacks['cursive']['bilbo-swash'] = $stacklist['bilbo-swash'];

		if ( ! isset( $stacks['cursive']['calligraffitti'] ) )
			$stacks['cursive']['calligraffitti'] = $stacklist['calligraffitti'];

		if ( ! isset( $stacks['cursive']['dancing-script'] ) )
			$stacks['cursive']['dancing-script'] = $stacklist['dancing-script'];

		if ( ! isset( $stacks['cursive']['great-vibes'] ) )
			$stacks['cursive']['great-vibes'] = $stacklist['great-vibes'];

		if ( ! isset( $stacks['cursive']['kaushan-script'] ) )
			$stacks['cursive']['kaushan-script'] = $stacklist['kaushan-script'];

		if ( ! isset( $stacks['cursive']['meddon'] ) )
			$stacks['cursive']['meddon'] = $stacklist['meddon'];

		if ( ! isset( $stacks['cursive']['pacifico'] ) )
			$stacks['cursive']['pacifico'] = $stacklist['pacifico'];

		if ( ! isset( $stacks['cursive']['rock-salt'] ) )
			$stacks['cursive']['rock-salt'] = $stacklist['rock-salt'];

		// monospace fonts
		if ( ! isset( $stacks['mono']['droid-sans-mono'] ) )
			$stacks['mono']['droid-sans-mono'] = $stacklist['droid-sans-mono'];

		if ( ! isset( $stacks['monospace']['ubuntu-mono'] ) )
			$stacks['mono']['ubuntu-mono'] = $stacklist['ubuntu-mono'];

		// filter them all
		$stacks	= apply_filters( 'gppro_webfont_stack_list', $stacks );

		// send back stacks
		return $stacks;

	}

/// end class
}

// Instantiate our class
$GP_Pro_Google_Webfonts = GP_Pro_Google_Webfonts::getInstance();