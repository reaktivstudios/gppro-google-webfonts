<?php
/*
Plugin Name: Genesis Design Palette Pro - Google Webfonts
Plugin URI: https://genesisdesignpro.com/
Description: Adds a set of popular Google Webfonts to Design Palette Pro
Author: Reaktiv Studios
Version: 1.0.2
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
	define( 'GPGWF_VER', '1.0.2' );
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
		add_action			(	'plugins_loaded',					array(	$this,	'textdomain'				)			);
		add_action			(	'admin_notices',					array(	$this,	'gppro_active_check'		),	10		);
		add_action			(	'admin_notices',					array(	$this,	'pagespeed_alert'			),	10		);

		// front end
		add_action			(	'wp_enqueue_scripts',				array(	$this,	'font_scripts'				)			);

		// GP Pro specific
		add_filter			(	'gppro_font_stacks',				array(	$this,	'google_stack_list'			),	99		);

	}

	/**
	 * If an instance exists, this returns it.  If not, it creates one and
	 * retuns it.
	 *
	 * @return GP_Pro_Freeform_CSS
	 */

	public static function getInstance() {

		if ( !self::$instance ) {
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

		$screen = get_current_screen();

		if ( $screen->parent_file !== 'plugins.php' ) {
			return;
		}

		// run the active check
		$coreactive	= class_exists( 'Genesis_Palette_Pro' ) ? Genesis_Palette_Pro::check_active() : false;

		// not active. show message
		if ( ! $coreactive ) :

			echo '<div id="message" class="error fade below-h2"><p><strong>'.__( 'This plugin requires Genesis Design Palette Pro to function and cannot be activated.', 'gppro-google-webfonts' ).'</strong></p></div>';

			// hide activation method
			unset( $_GET['activate'] );

			// deactivate YOURSELF
			deactivate_plugins( plugin_basename( __FILE__ ) );

		endif;

		return;

	}

	/**
	 * check for high font number
	 *
	 * @return
	 */

	public function pagespeed_alert() {

		$screen = get_current_screen();

		if ( $screen->base !== 'genesis_page_genesis-palette-pro' ) {
			return;
		}

		// check out pagespeed alert
		$alert	= get_option( 'gppro-webfont-alert' );

		if ( !isset( $alert ) || empty( $alert ) || $alert == 'ignore' )
			return;

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

		$alert	= get_option( 'gppro-webfont-alert' );

		$totals	= array_sum( $fontsize );

		$filter	= apply_filters( 'gppro_webfont_alert', 250 );

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

		$string		= self::font_choice_string();

		if ( ! $string ) {
			return;
		}

		wp_enqueue_style( 'gppro-webfonts', '//fonts.googleapis.com/css?family='.$string, array(), GPGWF_VER );

	}

	/**
	 * helper to create array of font options and combine into string
	 *
	 * @return
	 */

	static function font_choice_string() {

		// fetch list of active fonts
		$actives	= self::font_choice_active();

		if ( ! isset( $actives ) || isset( $actives ) && empty( $actives ) ) {
			return false;
		}

		// set value arrays to false
		$fontarr	= false;
		$fontsize	= false;

		foreach ( $actives as $active ) :

			// get individual font data
			$data	= self::single_font_fetch( $active );

			// bail if it came back native (i.e. already loaded )
			if ( $data['src'] == 'native' ) {
				continue;
			}

			// pass it into array and go forth
			$fontarr[]	= $data['val'];
			$fontsize[]	= $data['size'];

		endforeach;

		// bail if nothing is there
		if ( ! $fontarr ) {
			return false;
		}

		// cast into array
		$fontarr	= (array) $fontarr;
		$fontsize	= (array) $fontsize;

		// run font weight check for pagespeed alert
		if ( $fontsize ) {
			$pagespeed	= self::pagespeed_check( $fontsize );
		}

		// implode into string with divider
		$string		= implode( '|', $fontarr );

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
		$stacklist	= self::google_stacks();
		$stackkeys	= array_keys( $stacklist );

		// grab our settings
		$settings	= get_option( 'gppro-settings' );

		if ( ! $settings ) {
			return false;
		}

		$choices	= array();

		// filter through and run comparison
		foreach ( $settings as $key => $value ):

			if ( in_array( $value, $stackkeys ) ) {
				$choices[]	= $value;
			}

		endforeach;

		// return our choices
		return array_unique( $choices );

	}

	/**
	 * helper to fetch the values from a single stack
	 *
	 * @return
	 */

	static function single_font_fetch( $font ) {

		// fetch our list of stacks
		$stacklist	= self::google_stacks();

		return $stacklist[$font];

	}

	/**
	 * this is really an admin-level thing but might be used in the future.
	 * basically lists all the fonts in a table
	 *
	 * @return [type] [description]
	 */
	public function showfonts() {
				// fetch our list of stacks
		$stacklist	= self::google_stacks();

		echo '<table>';

		foreach ( $stacklist as $stack ) {

			$type	= strpos( $stack['css'], 'sans-serif' ) !== false ? 'sans-serif' : 'serif';

			echo '<tr>';

			echo '<td>'.$stack['label'].'</td>';
			echo '<td>'.$type.'</td>';

			echo '</tr>';

		}

		echo '</table>';

	}

	/**
	 * create list of stacks to use in various locations
	 *
	 * @return
	 */

	static function google_stacks() {

		$webfonts	= array(

			// serif fonts
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
				'val'	=> 'Lato:400,700',
				'size'	=> '100',
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

		);

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

		if ( ! isset( $stacks['serif']['pt-serif'] ) )
			$stacks['serif']['pt-serif'] = $stacklist['pt-serif'];

		if ( ! isset( $stacks['serif']['roboto-slab'] ) )
			$stacks['serif']['roboto-slab'] = $stacklist['roboto-slab'];

		if ( ! isset( $stacks['serif']['quattrocento'] ) )
			$stacks['serif']['quattrocento'] = $stacklist['quattrocento'];


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

		if ( ! isset( $stacks['sans']['quattrocento-sans'] ) )
			$stacks['sans']['quattrocento-sans'] = $stacklist['quattrocento-sans'];

		if ( ! isset( $stacks['sans']['raleway'] ) )
			$stacks['sans']['raleway'] = $stacklist['raleway'];

		if ( ! isset( $stacks['sans']['roboto'] ) )
			$stacks['sans']['roboto'] = $stacklist['roboto'];

		if ( ! isset( $stacks['sans']['signika'] ) )
			$stacks['sans']['signika'] = $stacklist['signika'];

		// send back stacks
		return $stacks;

	}

/// end class
}

// Instantiate our class
$GP_Pro_Google_Webfonts = GP_Pro_Google_Webfonts::getInstance();