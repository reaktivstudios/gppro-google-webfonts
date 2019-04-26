<?php
/**
 * Add the Google Fonts API key setting to the DPP settings tab.
 *
 * @package gppro-google-webfonts
 */

/**
 * Add the Google Web Fonts API Key setting.
 *
 * @param array $settings Existing settings.
 *
 * @return array
 */
function gppro_google_webfonts_api_key( $settings ) {
	$description = sprintf(
		// translators: Note about requirement of Google Fonts developer API key.
		__( 'You must have a <a href="%1$s">Google Fonts Developer API key</a> to access the Google Fonts feature', 'gppro-google-webfonts' ),
		esc_url( 'https://developers.google.com/fonts/docs/developer_api' )
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
