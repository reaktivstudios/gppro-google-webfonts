<?php
/**
 * Plugin Name: Genesis Design Palette Pro - Google Webfonts
 * Plugin URI: https://genesisdesignpro.com/
 * Description: Adds a set of popular Google Webfonts to Design Palette Pro
 * Author: Reaktiv Studios
 * Version: 2.0.0
 * Requires at least: 4.0
 * Author URI: https://genesisdesignpro.com
 *
 * @package gppro-google-webfonts
 */

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

if ( ! defined( 'GPGWF_BASE' ) ) {
	define( 'GPGWF_BASE', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'GPGWF_DIR' ) ) {
	define( 'GPGWF_DIR', dirname( __FILE__ ) );
}

if ( ! defined( 'GPGWF_VER' ) ) {
	define( 'GPGWF_VER', '2.0.0' );
}

// Load helper function to retrieve legacy font stacks.
require_once GPGWF_DIR . '/lib/legacy-font-stacks.php';

/**
 * Load Google webfonts legacy class if new fonts functionality does not exist.
 * Load Google webfonts source class if new fonts functionality exists.
 */
function gppro_google_webfonts_load() {
	if ( class_exists( '\DPP\Admin\Fonts' ) ) {
		// Load Google webfonts source class.
		require_once GPGWF_DIR . '/lib/class-google.php';
	} else {
		// Load google webfonts legacy class.
		require_once GPGWF_DIR . '/lib/class-legacy.php';
	}
}
add_action( 'init', 'gppro_google_webfonts_load' );

/**
 * Add filters to work on DPP\Admin\Setup.
 */
function gppro_google_webfonts_add_filters() {
	// Load the Google API Key setting.
	require_once GPGWF_DIR . '/lib/setting-api-key.php';
}
add_action( 'dpp_before_admin_setup', 'gppro_google_webfonts_add_filters' );

/**
 * The GP_Pro_Google_Webfonts class needs to exist for legacy purposes.
 */
class GP_Pro_Google_Webfonts {}
