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
 * Load Google webfonts legacy class if new fonts functionality does not exist.
 * Load Google webfonts source class if new fonts functionality exists.
 */
function gppro_google_webfonts_load() {
	if ( ! class_exists( '\DPP\Lib\Fonts' ) ) {
		require_once GPGWF_DIR . '/lib/class-legacy.php';
	} else {
		require_once GPGWF_DIR . '/lib/class-google.php';
	}
}
add_action( 'init', 'gppro_google_webfonts_load' );

// The GP_Pro_Google_Webfonts class needs to exist for legacy purposes.
class GP_Pro_Google_Webfonts {}
