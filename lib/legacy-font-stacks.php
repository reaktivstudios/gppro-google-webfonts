<?php
/**
 * Helper functions to get the legacy font stacks.
 *
 * @package gppro-google-webfonts
 */

/**
 * Get the legacy font stacks.
 *
 * @return array
 */
function gppro_google_webfonts_get_legacy_stacks() {

	// Set the array of fonts.
	$webfonts = array(

		// The list of serif fonts.
		'abril-fatface'        => array(
			'label' => __( 'Abril Fatface', 'gppro-google-webfonts' ),
			'css'   => '"Abril Fatface", serif',
			'src'   => 'web',
			'val'   => 'Abril+Fatface',
			'size'  => '14',
			'type'  => 'serif',
		),

		'arvo'                 => array(
			'label' => __( 'Arvo', 'gppro-google-webfonts' ),
			'css'   => '"Arvo", serif',
			'src'   => 'web',
			'val'   => 'Arvo:400,700,400italic,700italic',
			'size'  => '104',
			'type'  => 'serif',
		),

		'bitter'               => array(
			'label' => __( 'Bitter', 'gppro-google-webfonts' ),
			'css'   => '"Bitter", serif',
			'src'   => 'web',
			'val'   => 'Bitter:400,700,400italic',
			'size'  => '66',
			'type'  => 'serif',
		),

		'bree-serif'           => array(
			'label' => __( 'Bree Serif', 'gppro-google-webfonts' ),
			'css'   => '"Bree Serif", serif',
			'src'   => 'web',
			'val'   => 'Bree+Serif',
			'size'  => '11',
			'type'  => 'serif',
		),

		'crimson-text'         => array(
			'label' => __( 'Crimson Text', 'gppro-google-webfonts' ),
			'css'   => '"Crimson Text", serif',
			'src'   => 'web',
			'val'   => 'Crimson+Text:400,700',
			'size'  => '186',
			'type'  => 'serif',
		),

		'enriqueta'            => array(
			'label' => __( 'Enriqueta', 'gppro-google-webfonts' ),
			'css'   => '"Enriqueta", serif',
			'src'   => 'web',
			'val'   => 'Enriqueta:400,700',
			'size'  => '22',
			'type'  => 'serif',
		),

		'fenix'                => array(
			'label' => __( 'Fenix', 'gppro-google-webfonts' ),
			'css'   => '"Fenix", serif',
			'src'   => 'web',
			'val'   => 'Fenix',
			'size'  => '8',
			'type'  => 'serif',
		),

		'lora'                 => array(
			'label' => __( 'Lora', 'gppro-google-webfonts' ),
			'css'   => '"Lora", serif',
			'src'   => 'web',
			'val'   => 'Lora:400,700,400italic,700italic',
			'size'  => '112',
			'type'  => 'serif',
		),

		'josefin-slab'         => array(
			'label' => __( 'Josefin Slab', 'gppro-google-webfonts' ),
			'css'   => '"Josefin Slab", serif',
			'src'   => 'web',
			'val'   => 'Josefin+Slab:400,700',
			'size'  => '102',
			'type'  => 'serif',
		),

		'merriweather'         => array(
			'label' => __( 'Merriweather', 'gppro-google-webfonts' ),
			'css'   => '"Merriweather", serif',
			'src'   => 'web',
			'val'   => 'Merriweather:400,700,400italic,700italic',
			'size'  => '44',
			'type'  => 'serif',
		),

		'neuton'               => array(
			'label' => __( 'Neuton', 'gppro-google-webfonts' ),
			'css'   => '"Neuton", serif',
			'src'   => 'web',
			'val'   => 'Neuton:300,400,700,400italic',
			'size'  => '56',
			'type'  => 'serif',
		),

		'nixie-one'            => array(
			'label' => __( 'Nixie One', 'gppro-google-webfonts' ),
			'css'   => '"Nixie One", serif',
			'src'   => 'web',
			'val'   => 'Nixie+One',
			'size'  => '39',
			'type'  => 'serif',
		),

		'noto-serif'           => array(
			'label' => __( 'Noto Serif', 'gppro-google-webfonts' ),
			'css'   => '"Noto Serif", serif',
			'src'   => 'web',
			'val'   => 'Noto+Serif:400,400i,700,700i',
			'size'  => '36',
			'type'  => 'serif',
		),

		'old-standard-tt'      => array(
			'label' => __( 'Old Standard TT', 'gppro-google-webfonts' ),
			'css'   => '"Old Standard TT", serif',
			'src'   => 'web',
			'val'   => 'Old+Standard+TT:400,700,400italic',
			'size'  => '93',
			'type'  => 'serif',
		),

		'playfair-display'     => array(
			'label' => __( 'Playfair Display', 'gppro-google-webfonts' ),
			'css'   => '"Playfair Display", serif',
			'src'   => 'web',
			'val'   => 'Playfair+Display:400,700,400italic',
			'size'  => '78',
			'type'  => 'serif',
		),

		'podkova'              => array(
			'label' => __( 'Podkova', 'gppro-google-webfonts' ),
			'css'   => '"Podkova", serif',
			'src'   => 'web',
			'val'   => 'Podkova:400,700',
			'size'  => '72',
			'type'  => 'serif',
		),

		'rokkitt'              => array(
			'label' => __( 'Rokkitt', 'gppro-google-webfonts' ),
			'css'   => '"Rokkitt", serif',
			'src'   => 'web',
			'val'   => 'Rokkitt:400,700',
			'size'  => '52',
			'type'  => 'serif',
		),

		'pt-serif'             => array(
			'label' => __( 'PT Serif', 'gppro-google-webfonts' ),
			'css'   => '"PT Serif", serif',
			'src'   => 'web',
			'val'   => 'PT+Serif:400,700',
			'size'  => '88',
			'type'  => 'serif',
		),

		'roboto-slab'          => array(
			'label' => __( 'Roboto Slab', 'gppro-google-webfonts' ),
			'css'   => '"Roboto Slab", serif',
			'src'   => 'web',
			'val'   => 'Roboto+Slab:300,400,700',
			'size'  => '36',
			'type'  => 'serif',
		),

		'quattrocento'         => array(
			'label' => __( 'Quattrocento', 'gppro-google-webfonts' ),
			'css'   => '"Quattrocento", serif',
			'src'   => 'web',
			'val'   => 'Quattrocento:400,700',
			'size'  => '54',
			'type'  => 'serif',
		),

		'source-serif-pro'     => array(
			'label' => __( 'Source Serif Pro', 'gppro-google-webfonts' ),
			'css'   => '"Source Serif Pro", serif',
			'src'   => 'web',
			'val'   => 'Source+Serif+Pro:400,700',
			'size'  => '48',
			'type'  => 'serif',
		),

		'vollkorn'             => array(
			'label' => __( 'Vollkorn', 'gppro-google-webfonts' ),
			'css'   => '"Vollkorn", serif',
			'src'   => 'web',
			'val'   => 'Vollkorn:400,700,400italic,700italic',
			'size'  => '124',
			'type'  => 'serif',
		),

		// The list of sans serif fonts.
		'abel'                 => array(
			'label' => __( 'Abel', 'gppro-google-webfonts' ),
			'css'   => '"Abel", sans-serif',
			'src'   => 'web',
			'val'   => 'Abel',
			'size'  => '16',
			'type'  => 'sans',
		),

		'archivo-narrow'       => array(
			'label' => __( 'Archivo Narrow', 'gppro-google-webfonts' ),
			'css'   => '"Archivo Narrow", sans-serif',
			'src'   => 'web',
			'val'   => 'Archivo+Narrow:400,700,400italic,700italic',
			'size'  => '100',
			'type'  => 'sans',
		),

		'cabin'                => array(
			'label' => __( 'Cabin', 'gppro-google-webfonts' ),
			'css'   => '"Cabin", sans-serif',
			'src'   => 'web',
			'val'   => 'Cabin:400,700',
			'size'  => '166',
			'type'  => 'sans',
		),

		'dosis'                => array(
			'label' => __( 'Dosis', 'gppro-google-webfonts' ),
			'css'   => '"Dosis", sans-serif',
			'src'   => 'web',
			'val'   => 'Dosis:300,400,700',
			'size'  => '96',
			'type'  => 'sans',
		),

		'inder'                => array(
			'label' => __( 'Inder', 'gppro-google-webfonts' ),
			'css'   => '"Inder", sans-serif',
			'src'   => 'web',
			'val'   => 'Inder',
			'size'  => '9',
			'type'  => 'sans',
		),

		'josefin-sans'         => array(
			'label' => __( 'Josefin Sans', 'gppro-google-webfonts' ),
			'css'   => '"Josefin Sans", sans-serif',
			'src'   => 'web',
			'val'   => 'Josefin+Sans:400,700',
			'size'  => '38',
			'type'  => 'sans',
		),

		'lato'                 => array(
			'label' => __( 'Lato', 'gppro-google-webfonts' ),
			'css'   => '"Lato", sans-serif',
			'src'   => 'web',
			'val'   => 'Lato:300,400,700',
			'size'  => '150',
			'type'  => 'sans',
		),

		'montserrat'           => array(
			'label' => __( 'Montserrat', 'gppro-google-webfonts' ),
			'css'   => '"Montserrat", sans-serif',
			'src'   => 'web',
			'val'   => 'Montserrat:400,700',
			'size'  => '28',
			'type'  => 'sans',
		),

		'noto-sans'            => array(
			'label' => __( 'Noto Sans', 'gppro-google-webfonts' ),
			'css'   => '"Noto Sans", sans-serif',
			'src'   => 'web',
			'val'   => 'Noto+Sans:400,400i,700,700i',
			'size'  => '36',
			'type'  => 'sans',
		),

		'open-sans'            => array(
			'label' => __( 'Open Sans', 'gppro-google-webfonts' ),
			'css'   => '"Open Sans", sans-serif',
			'src'   => 'web',
			'val'   => 'Open+Sans:300,400,700,300italic,400italic,700italic',
			'size'  => '90',
			'type'  => 'sans',
		),

		'open-sans-condensed'  => array(
			'label' => __( 'Open Sans Condensed', 'gppro-google-webfonts' ),
			'css'   => '"Open Sans Condensed", sans-serif',
			'src'   => 'web',
			'val'   => 'Open+Sans+Condensed:300,700,300italic',
			'size'  => '51',
			'type'  => 'sans',
		),

		'orienta'              => array(
			'label' => __( 'Orienta', 'gppro-google-webfonts' ),
			'css'   => '"Orienta", sans-serif',
			'src'   => 'web',
			'val'   => 'Orienta',
			'size'  => '13',
			'type'  => 'sans',
		),

		'oswald'               => array(
			'label' => __( 'Oswald', 'gppro-google-webfonts' ),
			'css'   => '"Oswald", sans-serif',
			'src'   => 'web',
			'val'   => 'Oswald:400,700',
			'size'  => '26',
			'type'  => 'sans',
		),

		'oxygen'               => array(
			'label' => __( 'Oxygen', 'gppro-google-webfonts' ),
			'css'   => '"Oxygen", sans-serif',
			'src'   => 'web',
			'val'   => 'Oxygen:300,400,700',
			'size'  => '51',
			'type'  => 'sans',
		),

		'pathway-gothic'       => array(
			'label' => __( 'Pathway Gothic One', 'gppro-google-webfonts' ),
			'css'   => '"Pathway Gothic One", sans-serif',
			'src'   => 'web',
			'val'   => 'Pathway+Gothic+One',
			'size'  => '7',
			'type'  => 'sans',
		),

		'quicksand'            => array(
			'label' => __( 'Quicksand', 'gppro-google-webfonts' ),
			'css'   => '"Quicksand", san-serif',
			'src'   => 'web',
			'val'   => 'Quicksand:300,400,700',
			'size'  => '39',
			'type'  => 'sans',
		),

		'roboto-condensed'     => array(
			'label' => __( 'Roboto Condensed', 'gppro-google-webfonts' ),
			'css'   => '"Roboto Condensed", sans-serif',
			'src'   => 'web',
			'val'   => 'Roboto+Condensed:300,400,700,300italic,400italic,700italic',
			'size'  => '66',
			'type'  => 'sans',
		),

		'quattrocento-sans'    => array(
			'label' => __( 'Quattrocento Sans', 'gppro-google-webfonts' ),
			'css'   => '"Quattrocento Sans", sans-serif',
			'src'   => 'web',
			'val'   => 'Quattrocento+Sans:400,700,400italic,700italic',
			'size'  => '76',
			'type'  => 'sans',
		),

		'raleway'              => array(
			'label' => __( 'Raleway', 'gppro-google-webfonts' ),
			'css'   => '"Raleway", sans-serif',
			'src'   => 'web',
			'val'   => 'Raleway:400,500,900',
			'size'  => '177',
			'type'  => 'sans',
		),

		'roboto'               => array(
			'label' => __( 'Roboto', 'gppro-google-webfonts' ),
			'css'   => '"Roboto", sans-serif',
			'src'   => 'web',
			'val'   => 'Roboto:400,700,400italic,700italic',
			'size'  => '40',
			'type'  => 'sans',
		),

		'signika'              => array(
			'label' => __( 'Signika', 'gppro-google-webfonts' ),
			'css'   => '"Signika", sans-serif',
			'src'   => 'web',
			'val'   => 'Signika:300,400,600,700',
			'size'  => '148',
			'type'  => 'sans',
		),

		'source-sans-pro'      => array(
			'label' => __( 'Source Sans Pro', 'gppro-google-webfonts' ),
			'css'   => '"Source Sans Pro", sans-serif',
			'src'   => 'web',
			'val'   => 'Source+Sans+Pro:300,400,700,300italic,400italic,700italic',
			'size'  => '108',
			'type'  => 'sans',
		),

		'syncopate'            => array(
			'label' => __( 'Syncopate', 'gppro-google-webfonts' ),
			'css'   => '"Syncopate", sans-serif',
			'src'   => 'web',
			'val'   => 'Syncopate:400,700',
			'size'  => '134',
			'type'  => 'sans',
		),

		// The list of cursive fonts.
		'arizonia'             => array(
			'label' => __( 'Arizonia', 'gppro-google-webfonts' ),
			'css'   => '"Arizonia", cursive',
			'src'   => 'web',
			'val'   => 'Arizonia',
			'size'  => '13',
			'type'  => 'cursive',
		),

		'bilbo-swash'          => array(
			'label' => __( 'Bilbo Swash Caps', 'gppro-google-webfonts' ),
			'css'   => '"Bilbo Swash Caps", cursive',
			'src'   => 'web',
			'val'   => 'Bilbo+Swash+Caps',
			'size'  => '14',
			'type'  => 'cursive',
		),

		'cabin-sketch'         => array(
			'label' => __( 'Cabin Sketch', 'gppro-google-webfonts' ),
			'css'   => '"Cabin Sketch", cursive',
			'src'   => 'web',
			'val'   => 'Cabin+Sketch:400,700',
			'size'  => '202',
			'type'  => 'cursive',
		),

		'calligraffitti'       => array(
			'label' => __( 'Calligraffitti', 'gppro-google-webfonts' ),
			'css'   => '"Calligraffitti", cursive',
			'src'   => 'web',
			'val'   => 'Calligraffitti',
			'size'  => '36',
			'type'  => 'cursive',
		),

		'dancing-script'       => array(
			'label' => __( 'Dancing Script', 'gppro-google-webfonts' ),
			'css'   => '"Dancing Script", cursive',
			'src'   => 'web',
			'val'   => 'Dancing+Script:400,700',
			'size'  => '116',
			'type'  => 'cursive',
		),

		'fredericka-the-great' => array(
			'label' => __( 'Fredericka the Great', 'gppro-google-webfonts' ),
			'css'   => '"Fredericka the Great", cursive',
			'src'   => 'web',
			'val'   => 'Fredericka+the+Great:400',
			'size'  => '271',
			'type'  => 'cursive',
		),

		'great-vibes'          => array(
			'label' => __( 'Great Vibes', 'gppro-google-webfonts' ),
			'css'   => '"Great Vibes", cursive',
			'src'   => 'web',
			'val'   => 'Great+Vibes',
			'size'  => '24',
			'type'  => 'cursive',
		),

		'handlee'              => array(
			'label' => __( 'Handlee', 'gppro-google-webfonts' ),
			'css'   => '"Handlee", cursive',
			'src'   => 'web',
			'val'   => 'Handlee:400',
			'size'  => '22',
			'type'  => 'cursive',
		),

		'kaushan-script'       => array(
			'label' => __( 'Kaushan Script', 'gppro-google-webfonts' ),
			'css'   => '"Kaushan Script", cursive',
			'src'   => 'web',
			'val'   => 'Kaushan+Script',
			'size'  => '38',
			'type'  => 'cursive',
		),

		'londrina-outline'     => array(
			'label' => __( 'Londrina Outline', 'gppro-google-webfonts' ),
			'css'   => '"Londrina Outline", cursive',
			'src'   => 'web',
			'val'   => 'Londrina+Outline:400',
			'size'  => '42',
			'type'  => 'cursive',
		),

		'londrina-sketch'      => array(
			'label' => __( 'Londrina Sketch', 'gppro-google-webfonts' ),
			'css'   => '"Londrina Sketch", cursive',
			'src'   => 'web',
			'val'   => 'Londrina+Sketch:400',
			'size'  => '82',
			'type'  => 'cursive',
		),

		'meddon'               => array(
			'label' => __( 'Meddon', 'gppro-google-webfonts' ),
			'css'   => '"Meddon", cursive',
			'src'   => 'web',
			'val'   => 'Meddon',
			'size'  => '83',
			'type'  => 'cursive',
		),

		'pacifico'             => array(
			'label' => __( 'Pacifico', 'gppro-google-webfonts' ),
			'css'   => '"Pacifico", cursive',
			'src'   => 'web',
			'val'   => 'Pacifico',
			'size'  => '27',
			'type'  => 'cursive',
		),

		'rock-salt'            => array(
			'label' => __( 'Rock Salt', 'gppro-google-webfonts' ),
			'css'   => '"Rock Salt", cursive',
			'src'   => 'web',
			'val'   => 'Rock+Salt',
			'size'  => '74',
			'type'  => 'cursive',
		),

		'sacramento'           => array(
			'label' => __( 'Sacramento', 'gppro-google-webfonts' ),
			'css'   => '"Sacramento", cursive',
			'src'   => 'web',
			'val'   => 'Sacramento',
			'size'  => '20',
			'type'  => 'cursive',
		),

		'sofia'                => array(
			'label' => __( 'Sofia', 'gppro-google-webfonts' ),
			'css'   => '"Sofia", cursive',
			'src'   => 'web',
			'val'   => 'Sofia',
			'size'  => '18',
			'type'  => 'cursive',
		),

		// The list of monospace fonts.
		'droid-sans-mono'      => array(
			'label' => __( 'Droid Sans Mono', 'gppro-google-webfonts' ),
			'css'   => '"Droid Sans Mono", monospace',
			'src'   => 'web',
			'val'   => 'Droid+Sans+Mono',
			'size'  => '73',
			'type'  => 'mono',
		),

		'source-code-pro'      => array(
			'label' => __( 'Source Code Pro', 'gppro-google-webfonts' ),
			'css'   => '"Source Code Pro", monospace',
			'src'   => 'web',
			'val'   => 'Source+Code+Pro:400,700',
			'size'  => '48',
			'type'  => 'mono',
		),

		'ubuntu-mono'          => array(
			'label' => __( 'Ubuntu Mono', 'gppro-google-webfonts' ),
			'css'   => '"Ubuntu Mono", monospace',
			'src'   => 'web',
			'val'   => 'Ubuntu+Mono',
			'size'  => '18',
			'type'  => 'mono',
		),

	);

	return $webfonts;
}

/**
 * Add legacy font stacks to the default fonts if no active fonts yet.
 *
 * @param array $fonts List of default fonts.
 *
 * @return array
 */
function gppro_google_webfonts_default_fonts( $fonts ) {
	$gppro_legacy_fonts = gppro_google_webfonts_get_legacy_stacks();

	$new_fonts_list = array_merge( $fonts, $gppro_legacy_fonts );

	return $new_fonts_list;
}
add_filter( 'dpp_default_fonts', 'gppro_google_webfonts_default_fonts' );
