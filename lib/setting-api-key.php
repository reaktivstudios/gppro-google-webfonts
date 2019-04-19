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
	$description = sprintf(
		'%1$s <a href="%2$s" target="_blank">%3$s</a> %4$s.',
		__( 'You must have a', 'gppro-google-webfonts' ),
		esc_url( 'https://developers.google.com/fonts/docs/developer_api' ),
		__( 'Google Fonts Developer API key', 'gppro-google-webfonts' ),
		__( 'to access the Google Fonts feature', 'gppro-google-webfonts' )
	);

	$settings['gppro_google_webfonts_api_key'] = array(
		'label'       => __( 'Google Fonts API Key', 'gppro-google-webfonts' ),
		'description' => $description,
		'default'     => '',
		'type'        => 'text',
		'section'     => 'settings',
	);

	return $settings;
}
add_filter( 'dpp_settings', 'gppro_google_webfonts_api_key', 110 );
