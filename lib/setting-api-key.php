<?php
/**
 * Add the Google Fonts API key setting to the DPP settings tab.
 *
 * @package gppro-google-webfonts
 */

/**
 * Add the Google Webfonts API Key setting.
 *
 * @param array $settings Existing settings.
 *
 * @return array
 */
function gppro_google_webfonts_api_key( $settings ) {
	$settings['gppro_google_webfonts_api_key'] = array(
		'label'       => __( 'Google Fonts API Key', 'gppro-google-webfonts' ),
		'description' => __( 'You must have a Google Fonts Developer API key to access the Google Fonts feature.', 'gppro-google-webfonts' ),
		'default'     => '',
		'type'        => 'text',
		'section'     => 'settings',
	);

	return $settings;
}
add_filter( 'dpp_settings', 'gppro_google_webfonts_api_key', 110 );
