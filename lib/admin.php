<?php
/**
 * Add/update settings and tabs for the DPP admin.
 *
 * @package gppro-google-webfonts
 */

/**
 * Add Google Web Fonts settings.
 *
 * @param array $settings Existing settings.
 *
 * @return array
 */
function gppro_google_webfonts_settings( $settings ) {
	// Add the Google Web Fonts API key setting.
	$api_key_description = sprintf(
		// translators: Note about requirement of Google Fonts developer API key.
		__( 'You must have a <a href="%1$s">Google Fonts Developer API key</a> to access the Google Fonts feature', 'gppro-google-webfonts' ),
		esc_url( 'https://developers.google.com/fonts/docs/developer_api' )
	);

	$settings['gppro_google_webfonts_api_key'] = array(
		'label'       => __( 'Google Fonts API Key', 'gppro-google-webfonts' ),
		'description' => $api_key_description,
		'default'     => '',
		'type'        => 'text',
		'section'     => 'settings',
	);

	// Add logging option.
	$settings['gppro_google_webfonts_logging'] = array(
		'label'       => __( 'Enable Google Fonts error logging', 'gppro-google-webfonts' ),
		'description' => __( 'Check to enable logging if you encounter any errors with the Google Fonts API.', 'gppro-google-webfonts' ),
		'default'     => 1,
		'type'        => 'checkbox',
		'section'     => 'settings',
	);

	// Add link to clear font cache under utilities.
	$settings['gppro_google_webfonts_clear_cache'] = array(
		'label'    => __( 'Clear Google Font cache', 'gppro-google-webfonts' ),
		'section'  => 'utilities',
		'callback' => 'gppro_google_webfonts_clear_cache_link',
	);

	return $settings;
}
add_filter( 'dpp_settings', 'gppro_google_webfonts_settings', 3000 );

/**
 * Maybe add tabs to the DPP admin.
 *
 * @param array $tabs The DPP admin tabs.
 *
 * @return array
 */
function gppro_google_webfonts_tabs( $tabs ) {
	$logging_enabled = get_option( 'gppro_google_webfonts_logging', false );

	if ( ! empty( $logging_enabled ) ) {
		$tabs['gppro_google_webfonts_log'] = array(
			'label'  => __( 'Google Fonts Log', 'gppro-google-webfonts' ),
			'single' => true,
			'form'   => false,
		);
	}

	return $tabs;
}
add_filter( 'dpp_settings_tabs', 'gppro_google_webfonts_tabs', 3000 );

/**
 * Maybe add sections to the DPP admin.
 *
 * @param array $sections The DPP admin sections.
 *
 * @return array
 */
function gppro_google_webfonts_sections( $sections ) {
	$logging_enabled = get_option( 'gppro_google_webfonts_logging', false );

	if ( ! empty( $logging_enabled ) ) {
		$sections['gppro_google_webfonts_log'] = array(
			'title'    => __( 'Google Fonts Log', 'gppro-google-webfonts' ),
			'callback' => 'gppro_google_webfonts_section_log',
		);
	}
	return $sections;
}
add_filter( 'dpp_settings_sections', 'gppro_google_webfonts_sections', 3000 );

/**
 * Callback for the Google Fonts log section.
 */
function gppro_google_webfonts_section_log() {
	$gppro_google_webfonts_log = get_option( 'gppro_google_webfonts_log', array() );

	if ( ! empty( $gppro_google_webfonts_log ) ) {
		$clear_log_url = admin_url( 'admin.php?page=genesis-palette-pro&current-tab=genesis-palette-pro-gppro_google_webfonts_log&dpp-clear-font-log=1' );
		?>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th class="manage-column column-date"><?php esc_html_e( 'Date', 'gppro-google-webfonts' ); ?></th>
					<th class="manage-column"><?php esc_html_e( 'Message', 'gppro-google-webfonts' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $gppro_google_webfonts_log as $log_item ) : ?>
				<tr>
					<td><?php echo esc_html( $log_item['date'] ); ?></td>
					<td><?php echo esc_html( $log_item['message'] ); ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<p><a href="<?php echo esc_url( $clear_log_url ); ?>"><?php esc_html_e( 'Clear font log.', 'gppro-google-webfonts' ); ?></a>
		<?php
	} else {
		echo '<p>' . esc_html__( 'The log is empty.', 'gppro-google-webfonts' ) . '</p>';
	}
}

/**
 * Display a link to clear the font cache.
 */
function gppro_google_webfonts_clear_cache_link() {
	$clear_cache_url = admin_url( 'admin.php?page=genesis-palette-pro&current-tab=genesis-palette-pro-utilities&dpp-delete-font-cache=1' );
	?>
	<p>
		<a href="<?php echo esc_url( $clear_cache_url ); ?>"><?php esc_html_e( 'Clear font cache', 'gppro-google-webfonts' ); ?></a>
	</p>
	<?php
}
