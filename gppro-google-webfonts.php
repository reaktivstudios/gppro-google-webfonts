<?php
/*
Plugin Name: Genesis Design Palette Pro - Google Webfonts
Plugin URI: http://andrewnorcross.com/plugins/
Description: Adds a set of popular Google Webfonts to Design Palette Pro
Author: Andrew Norcross
Version: 0.0.1.0
Requires at least: 3.5
Author URI: http://andrewnorcross.com
*/
/*  Copyright 2013 Andrew Norcross

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

if( !defined( 'GPGWF_BASE' ) )
	define( 'GPGWF_BASE', plugin_basename(__FILE__) );

if( !defined( 'GPGWF_DIR' ) )
	define( 'GPGWF_DIR', dirname( __FILE__ ) );

if( !defined( 'GPGWF_VER' ) )
	define( 'GPGWF_VER', '0.0.1.0' );

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
		if ( !self::$instance )
			self::$instance = new self;
		return self::$instance;
	}

	/**
	 * load textdomain
	 *
	 * @return
	 */

	public function textdomain() {

		load_plugin_textdomain( 'gpgwf', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	}

	/**
	 * check for GP Pro being active
	 *
	 * @return
	 */

	public function gppro_active_check() {

		$screen = get_current_screen();

		if ( $screen->parent_file !== 'plugins.php' )
			return;

		if ( !is_plugin_active( 'genesis-palette-pro/genesis-palette-pro.php' ) ) :

			echo '<div id="message" class="error fade below-h2"><p><strong>'.__( 'This plugin requires Genesis Design Palette Pro to function.', 'gpgwf' ).'</strong></p></div>';

			// hide activation method
			unset( $_GET['activate'] );

			deactivate_plugins( plugin_basename( __FILE__ ) );

		endif;

	}

	/**
	 * call webfont CSS files
	 *
	 * @return
	 */

	static function font_scripts() {

		$string		= self::font_choice_string();

		if ( ! $string )
			return;

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

		if ( ! isset( $actives ) || isset( $actives ) && empty( $actives ) )
			return false;

		// set fonts to false
		$fonts	= false;

		foreach ( $actives as $active ) :
			// get individual font data
			$data	= self::single_font_fetch( $active );

			// bail if it came back native (i.e. already loaded )
			if ( $data['src'] == 'native' )
				continue;

			// get items from data
			$name	= urlencode( $data['label'] );
			$val	= $data['val'];

			// create string
			$setup	= $name.':'.$val;

			// pass it into array and go forth
			$fonts[]	= $setup;

		endforeach;

		// bail if nothing is there
		if ( ! $fonts )
			return false;

		// cast into array
		$fontarr	= (array) $fonts;

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

		$choices	= array();

		// filter through and run comparison
		foreach ( $settings as $key => $value ):

			if ( in_array( $value, $stackkeys ) )
				$choices[]	= $value;

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
	 * create list of stacks to use in various locations
	 *
	 * @return
	 */

	static function google_stacks() {

		$webfonts	= array(

			'lato'	=> array(
				'label'	=> __( 'Lato', 'gpgwf' ),
				'css'	=> '"Lato", sans-serif',
				'src'	=> 'web',
				'val'	=> '400,700'
			),

			'quattrocento'	=> array(
				'label'	=> __( 'Quattrocento', 'gpgwf' ),
				'css'	=> '"Quattrocento", serif',
				'src'	=> 'web',
				'val'	=> '400,700'
			),

			'quattrocento-sans'	=> array(
				'label'	=> __( 'Quattrocento Sans', 'gpgwf' ),
				'css'	=> '"Quattrocento Sans", sans-serif',
				'src'	=> 'web',
				'val'	=> '400,400italic,700,700italic'
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

		// add Lato
		if ( ! isset( $stacks['sans']['lato'] ) )
			$stacks['sans']['lato'] = $stacklist['lato'];

		// add Quattrocento
		if ( ! isset( $stacks['serif']['quattrocento'] ) )
			$stacks['serif']['quattrocento'] = $stacklist['quattrocento'];

		// add Quattrocento Sans
		if ( ! isset( $stacks['sans']['quattrocento-sans'] ) )
			$stacks['sans']['quattrocento-sans'] = $stacklist['quattrocento-sans'];


		return $stacks;

	}

/// end class
}

// Instantiate our class
$GP_Pro_Google_Webfonts = GP_Pro_Google_Webfonts::getInstance();

