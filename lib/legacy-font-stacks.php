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
			'val'   => 'Abril+Fatface',
			'size'  => '14',
			'type'  => 'serif',
			'url'   => 'https://fonts.google.com/specimen/Abril+Fatface',
		),

		'arvo'                 => array(
			'label' => __( 'Arvo', 'gppro-google-webfonts' ),
			'css'   => '"Arvo", serif',
			'val'   => 'Arvo:400,700,400italic,700italic',
			'size'  => '104',
			'type'  => 'serif',
			'url'   => 'https://fonts.google.com/specimen/Arvo',
		),

		'bitter'               => array(
			'label' => __( 'Bitter', 'gppro-google-webfonts' ),
			'css'   => '"Bitter", serif',
			'val'   => 'Bitter:400,700,400italic',
			'size'  => '66',
			'type'  => 'serif',
			'url'   => 'https://fonts.google.com/specimen/Bitter',
		),

		'bree-serif'           => array(
			'label' => __( 'Bree Serif', 'gppro-google-webfonts' ),
			'css'   => '"Bree Serif", serif',
			'val'   => 'Bree+Serif',
			'size'  => '11',
			'type'  => 'serif',
			'url'   => 'https://fonts.google.com/specimen/Bree+Serif',
		),

		'crimson-text'         => array(
			'label' => __( 'Crimson Text', 'gppro-google-webfonts' ),
			'css'   => '"Crimson Text", serif',
			'val'   => 'Crimson+Text:400,700',
			'size'  => '186',
			'type'  => 'serif',
			'url'   => 'https://fonts.google.com/specimen/Crimson+Text',
		),

		'enriqueta'            => array(
			'label' => __( 'Enriqueta', 'gppro-google-webfonts' ),
			'css'   => '"Enriqueta", serif',
			'val'   => 'Enriqueta:400,700',
			'size'  => '22',
			'type'  => 'serif',
			'url'   => 'https://fonts.google.com/specimen/Enriqueta',
		),

		'fenix'                => array(
			'label' => __( 'Fenix', 'gppro-google-webfonts' ),
			'css'   => '"Fenix", serif',
			'val'   => 'Fenix',
			'size'  => '8',
			'type'  => 'serif',
			'url'   => 'https://fonts.google.com/specimen/Fenix',
		),

		'lora'                 => array(
			'label' => __( 'Lora', 'gppro-google-webfonts' ),
			'css'   => '"Lora", serif',
			'val'   => 'Lora:400,700,400italic,700italic',
			'size'  => '112',
			'type'  => 'serif',
			'url'   => 'https://fonts.google.com/specimen/Lora',
		),

		'josefin-slab'         => array(
			'label' => __( 'Josefin Slab', 'gppro-google-webfonts' ),
			'css'   => '"Josefin Slab", serif',
			'val'   => 'Josefin+Slab:400,700',
			'size'  => '102',
			'type'  => 'serif',
			'url'   => 'https://fonts.google.com/specimen/Josefin+Slab',
		),

		'merriweather'         => array(
			'label' => __( 'Merriweather', 'gppro-google-webfonts' ),
			'css'   => '"Merriweather", serif',
			'val'   => 'Merriweather:400,700,400italic,700italic',
			'size'  => '44',
			'type'  => 'serif',
			'url'   => 'https://fonts.google.com/specimen/Merriweather',
		),

		'neuton'               => array(
			'label' => __( 'Neuton', 'gppro-google-webfonts' ),
			'css'   => '"Neuton", serif',
			'val'   => 'Neuton:300,400,700,400italic',
			'size'  => '56',
			'type'  => 'serif',
			'url'   => 'https://fonts.google.com/specimen/Neuton',
		),

		'nixie-one'            => array(
			'label' => __( 'Nixie One', 'gppro-google-webfonts' ),
			'css'   => '"Nixie One", serif',
			'val'   => 'Nixie+One',
			'size'  => '39',
			'type'  => 'serif',
			'url'   => 'https://fonts.google.com/specimen/Nixie+One',
		),

		'noto-serif'           => array(
			'label' => __( 'Noto Serif', 'gppro-google-webfonts' ),
			'css'   => '"Noto Serif", serif',
			'val'   => 'Noto+Serif:400,400i,700,700i',
			'size'  => '36',
			'type'  => 'serif',
			'url'   => 'https://fonts.google.com/specimen/Noto+Serif',
		),

		'old-standard-tt'      => array(
			'label' => __( 'Old Standard TT', 'gppro-google-webfonts' ),
			'css'   => '"Old Standard TT", serif',
			'val'   => 'Old+Standard+TT:400,700,400italic',
			'size'  => '93',
			'type'  => 'serif',
			'url'   => 'https://fonts.google.com/specimen/Old+Standard+TT',
		),

		'playfair-display'     => array(
			'label' => __( 'Playfair Display', 'gppro-google-webfonts' ),
			'css'   => '"Playfair Display", serif',
			'val'   => 'Playfair+Display:400,700,400italic',
			'size'  => '78',
			'type'  => 'serif',
			'url'   => 'https://fonts.google.com/specimen/Playfair+Display',
		),

		'podkova'              => array(
			'label' => __( 'Podkova', 'gppro-google-webfonts' ),
			'css'   => '"Podkova", serif',
			'val'   => 'Podkova:400,700',
			'size'  => '72',
			'type'  => 'serif',
			'url'   => 'https://fonts.google.com/specimen/Podkova',
		),

		'rokkitt'              => array(
			'label' => __( 'Rokkitt', 'gppro-google-webfonts' ),
			'css'   => '"Rokkitt", serif',
			'val'   => 'Rokkitt:400,700',
			'size'  => '52',
			'type'  => 'serif',
			'url'   => 'https://fonts.google.com/specimen/Rokkitt',
		),

		'pt-serif'             => array(
			'label' => __( 'PT Serif', 'gppro-google-webfonts' ),
			'css'   => '"PT Serif", serif',
			'val'   => 'PT+Serif:400,700',
			'size'  => '88',
			'type'  => 'serif',
			'url'   => 'https://fonts.google.com/specimen/PT+Serif',
		),

		'roboto-slab'          => array(
			'label' => __( 'Roboto Slab', 'gppro-google-webfonts' ),
			'css'   => '"Roboto Slab", serif',
			'val'   => 'Roboto+Slab:300,400,700',
			'size'  => '36',
			'type'  => 'serif',
			'url'   => 'https://fonts.google.com/specimen/Roboto+Slab',
		),

		'quattrocento'         => array(
			'label' => __( 'Quattrocento', 'gppro-google-webfonts' ),
			'css'   => '"Quattrocento", serif',
			'val'   => 'Quattrocento:400,700',
			'size'  => '54',
			'type'  => 'serif',
			'url'   => 'https://fonts.google.com/specimen/Quattrocento',
		),

		'source-serif-pro'     => array(
			'label' => __( 'Source Serif Pro', 'gppro-google-webfonts' ),
			'css'   => '"Source Serif Pro", serif',
			'val'   => 'Source+Serif+Pro:400,700',
			'size'  => '48',
			'type'  => 'serif',
			'url'   => 'https://fonts.google.com/specimen/Source+Serif+Pro',
		),

		'vollkorn'             => array(
			'label' => __( 'Vollkorn', 'gppro-google-webfonts' ),
			'css'   => '"Vollkorn", serif',
			'val'   => 'Vollkorn:400,700,400italic,700italic',
			'size'  => '124',
			'type'  => 'serif',
			'url'   => 'https://fonts.google.com/specimen/Vollkorn',
		),

		// The list of sans serif fonts.
		'abel'                 => array(
			'label' => __( 'Abel', 'gppro-google-webfonts' ),
			'css'   => '"Abel", sans-serif',
			'val'   => 'Abel',
			'size'  => '16',
			'type'  => 'sans',
			'url'   => 'https://fonts.google.com/specimen/Abel',
		),

		'archivo-narrow'       => array(
			'label' => __( 'Archivo Narrow', 'gppro-google-webfonts' ),
			'css'   => '"Archivo Narrow", sans-serif',
			'val'   => 'Archivo+Narrow:400,700,400italic,700italic',
			'size'  => '100',
			'type'  => 'sans',
			'url'   => 'https://fonts.google.com/specimen/Archivo+Narrow',
		),

		'cabin'                => array(
			'label' => __( 'Cabin', 'gppro-google-webfonts' ),
			'css'   => '"Cabin", sans-serif',
			'val'   => 'Cabin:400,700',
			'size'  => '166',
			'type'  => 'sans',
			'url'   => 'https://fonts.google.com/specimen/Cabin',
		),

		'dosis'                => array(
			'label' => __( 'Dosis', 'gppro-google-webfonts' ),
			'css'   => '"Dosis", sans-serif',
			'val'   => 'Dosis:300,400,700',
			'size'  => '96',
			'type'  => 'sans',
			'url'   => 'https://fonts.google.com/specimen/Dosis',
		),

		'inder'                => array(
			'label' => __( 'Inder', 'gppro-google-webfonts' ),
			'css'   => '"Inder", sans-serif',
			'val'   => 'Inder',
			'size'  => '9',
			'type'  => 'sans',
			'url'   => 'https://fonts.google.com/specimen/Inder',
		),

		'josefin-sans'         => array(
			'label' => __( 'Josefin Sans', 'gppro-google-webfonts' ),
			'css'   => '"Josefin Sans", sans-serif',
			'val'   => 'Josefin+Sans:400,700',
			'size'  => '38',
			'type'  => 'sans',
			'url'   => 'https://fonts.google.com/specimen/Josefin+Sans',
		),

		'lato'                 => array(
			'label' => __( 'Lato', 'gppro-google-webfonts' ),
			'css'   => '"Lato", sans-serif',
			'val'   => 'Lato:300,400,700',
			'size'  => '150',
			'type'  => 'sans',
			'url'   => 'https://fonts.google.com/specimen/Lato',
		),

		'montserrat'           => array(
			'label' => __( 'Montserrat', 'gppro-google-webfonts' ),
			'css'   => '"Montserrat", sans-serif',
			'val'   => 'Montserrat:400,700',
			'size'  => '28',
			'type'  => 'sans',
			'url'   => 'https://fonts.google.com/specimen/Montserrat',
		),

		'noto-sans'            => array(
			'label' => __( 'Noto Sans', 'gppro-google-webfonts' ),
			'css'   => '"Noto Sans", sans-serif',
			'val'   => 'Noto+Sans:400,400i,700,700i',
			'size'  => '36',
			'type'  => 'sans',
			'url'   => 'https://fonts.google.com/specimen/Noto+Sans',
		),

		'open-sans'            => array(
			'label' => __( 'Open Sans', 'gppro-google-webfonts' ),
			'css'   => '"Open Sans", sans-serif',
			'val'   => 'Open+Sans:300,400,700,300italic,400italic,700italic',
			'size'  => '90',
			'type'  => 'sans',
			'url'   => 'https://fonts.google.com/specimen/Open+Sans',
		),

		'open-sans-condensed'  => array(
			'label' => __( 'Open Sans Condensed', 'gppro-google-webfonts' ),
			'css'   => '"Open Sans Condensed", sans-serif',
			'val'   => 'Open+Sans+Condensed:300,700,300italic',
			'size'  => '51',
			'type'  => 'sans',
			'url'   => 'https://fonts.google.com/specimen/Open+Sans+Condensed',
		),

		'orienta'              => array(
			'label' => __( 'Orienta', 'gppro-google-webfonts' ),
			'css'   => '"Orienta", sans-serif',
			'val'   => 'Orienta',
			'size'  => '13',
			'type'  => 'sans',
			'url'   => 'https://fonts.google.com/specimen/Orienta',
		),

		'oswald'               => array(
			'label' => __( 'Oswald', 'gppro-google-webfonts' ),
			'css'   => '"Oswald", sans-serif',
			'val'   => 'Oswald:400,700',
			'size'  => '26',
			'type'  => 'sans',
			'url'   => 'https://fonts.google.com/specimen/Oswald',
		),

		'oxygen'               => array(
			'label' => __( 'Oxygen', 'gppro-google-webfonts' ),
			'css'   => '"Oxygen", sans-serif',
			'val'   => 'Oxygen:300,400,700',
			'size'  => '51',
			'type'  => 'sans',
			'url'   => 'https://fonts.google.com/specimen/Oxygen',
		),

		'pathway-gothic'       => array(
			'label' => __( 'Pathway Gothic One', 'gppro-google-webfonts' ),
			'css'   => '"Pathway Gothic One", sans-serif',
			'val'   => 'Pathway+Gothic+One',
			'size'  => '7',
			'type'  => 'sans',
			'url'   => 'https://fonts.google.com/specimen/Pathway+Gothic+One',
		),

		'quicksand'            => array(
			'label' => __( 'Quicksand', 'gppro-google-webfonts' ),
			'css'   => '"Quicksand", san-serif',
			'val'   => 'Quicksand:300,400,700',
			'size'  => '39',
			'type'  => 'sans',
			'url'   => 'https://fonts.google.com/specimen/Quicksand',
		),

		'roboto-condensed'     => array(
			'label' => __( 'Roboto Condensed', 'gppro-google-webfonts' ),
			'css'   => '"Roboto Condensed", sans-serif',
			'val'   => 'Roboto+Condensed:300,400,700,300italic,400italic,700italic',
			'size'  => '66',
			'type'  => 'sans',
			'url'   => 'https://fonts.google.com/specimen/Roboto+Condensed',
		),

		'quattrocento-sans'    => array(
			'label' => __( 'Quattrocento Sans', 'gppro-google-webfonts' ),
			'css'   => '"Quattrocento Sans", sans-serif',
			'val'   => 'Quattrocento+Sans:400,700,400italic,700italic',
			'size'  => '76',
			'type'  => 'sans',
			'url'   => 'https://fonts.google.com/specimen/Quattrocento+Sans',
		),

		'raleway'              => array(
			'label' => __( 'Raleway', 'gppro-google-webfonts' ),
			'css'   => '"Raleway", sans-serif',
			'val'   => 'Raleway:400,500,900',
			'size'  => '177',
			'type'  => 'sans',
			'url'   => 'https://fonts.google.com/specimen/Raleway',
		),

		'roboto'               => array(
			'label' => __( 'Roboto', 'gppro-google-webfonts' ),
			'css'   => '"Roboto", sans-serif',
			'val'   => 'Roboto:400,700,400italic,700italic',
			'size'  => '40',
			'type'  => 'sans',
			'url'   => 'https://fonts.google.com/specimen/Roboto',
		),

		'signika'              => array(
			'label' => __( 'Signika', 'gppro-google-webfonts' ),
			'css'   => '"Signika", sans-serif',
			'val'   => 'Signika:300,400,600,700',
			'size'  => '148',
			'type'  => 'sans',
			'url'   => 'https://fonts.google.com/specimen/Signika',
		),

		'source-sans-pro'      => array(
			'label' => __( 'Source Sans Pro', 'gppro-google-webfonts' ),
			'css'   => '"Source Sans Pro", sans-serif',
			'val'   => 'Source+Sans+Pro:300,400,700,300italic,400italic,700italic',
			'size'  => '108',
			'type'  => 'sans',
			'url'   => 'https://fonts.google.com/specimen/Source+Sans+Pro',
		),

		'syncopate'            => array(
			'label' => __( 'Syncopate', 'gppro-google-webfonts' ),
			'css'   => '"Syncopate", sans-serif',
			'val'   => 'Syncopate:400,700',
			'size'  => '134',
			'type'  => 'sans',
			'url'   => 'https://fonts.google.com/specimen/Syncopate',
		),

		// The list of cursive fonts.
		'arizonia'             => array(
			'label' => __( 'Arizonia', 'gppro-google-webfonts' ),
			'css'   => '"Arizonia", cursive',
			'val'   => 'Arizonia',
			'size'  => '13',
			'type'  => 'cursive',
			'url'   => 'https://fonts.google.com/specimen/Arizonia',
		),

		'bilbo-swash'          => array(
			'label' => __( 'Bilbo Swash Caps', 'gppro-google-webfonts' ),
			'css'   => '"Bilbo Swash Caps", cursive',
			'val'   => 'Bilbo+Swash+Caps',
			'size'  => '14',
			'type'  => 'cursive',
			'url'   => 'https://fonts.google.com/specimen/Bilbo+Swash+Caps',
		),

		'cabin-sketch'         => array(
			'label' => __( 'Cabin Sketch', 'gppro-google-webfonts' ),
			'css'   => '"Cabin Sketch", cursive',
			'val'   => 'Cabin+Sketch:400,700',
			'size'  => '202',
			'type'  => 'cursive',
			'url'   => 'https://fonts.google.com/specimen/Cabin+Sketch',
		),

		'calligraffitti'       => array(
			'label' => __( 'Calligraffitti', 'gppro-google-webfonts' ),
			'css'   => '"Calligraffitti", cursive',
			'val'   => 'Calligraffitti',
			'size'  => '36',
			'type'  => 'cursive',
			'url'   => 'https://fonts.google.com/specimen/Calligraffitti',
		),

		'dancing-script'       => array(
			'label' => __( 'Dancing Script', 'gppro-google-webfonts' ),
			'css'   => '"Dancing Script", cursive',
			'val'   => 'Dancing+Script:400,700',
			'size'  => '116',
			'type'  => 'cursive',
			'url'   => 'https://fonts.google.com/specimen/Dancing+Script',
		),

		'fredericka-the-great' => array(
			'label' => __( 'Fredericka the Great', 'gppro-google-webfonts' ),
			'css'   => '"Fredericka the Great", cursive',
			'val'   => 'Fredericka+the+Great:400',
			'size'  => '271',
			'type'  => 'cursive',
			'url'   => 'https://fonts.google.com/specimen/Fredericka+the+Great',
		),

		'great-vibes'          => array(
			'label' => __( 'Great Vibes', 'gppro-google-webfonts' ),
			'css'   => '"Great Vibes", cursive',
			'val'   => 'Great+Vibes',
			'size'  => '24',
			'type'  => 'cursive',
			'url'   => 'https://fonts.google.com/specimen/Great+Vibes',
		),

		'handlee'              => array(
			'label' => __( 'Handlee', 'gppro-google-webfonts' ),
			'css'   => '"Handlee", cursive',
			'val'   => 'Handlee:400',
			'size'  => '22',
			'type'  => 'cursive',
			'url'   => 'https://fonts.google.com/specimen/Handlee',
		),

		'kaushan-script'       => array(
			'label' => __( 'Kaushan Script', 'gppro-google-webfonts' ),
			'css'   => '"Kaushan Script", cursive',
			'val'   => 'Kaushan+Script',
			'size'  => '38',
			'type'  => 'cursive',
			'url'   => 'https://fonts.google.com/specimen/Kaushan+Script',
		),

		'londrina-outline'     => array(
			'label' => __( 'Londrina Outline', 'gppro-google-webfonts' ),
			'css'   => '"Londrina Outline", cursive',
			'val'   => 'Londrina+Outline:400',
			'size'  => '42',
			'type'  => 'cursive',
			'url'   => 'https://fonts.google.com/specimen/Londrina+Outline',
		),

		'londrina-sketch'      => array(
			'label' => __( 'Londrina Sketch', 'gppro-google-webfonts' ),
			'css'   => '"Londrina Sketch", cursive',
			'val'   => 'Londrina+Sketch:400',
			'size'  => '82',
			'type'  => 'cursive',
			'url'   => 'https://fonts.google.com/specimen/Londrina+Sketch',
		),

		'meddon'               => array(
			'label' => __( 'Meddon', 'gppro-google-webfonts' ),
			'css'   => '"Meddon", cursive',
			'val'   => 'Meddon',
			'size'  => '83',
			'type'  => 'cursive',
			'url'   => 'https://fonts.google.com/specimen/Meddon',
		),

		'pacifico'             => array(
			'label' => __( 'Pacifico', 'gppro-google-webfonts' ),
			'css'   => '"Pacifico", cursive',
			'val'   => 'Pacifico',
			'size'  => '27',
			'type'  => 'cursive',
			'url'   => 'https://fonts.google.com/specimen/Pacifico',
		),

		'rock-salt'            => array(
			'label' => __( 'Rock Salt', 'gppro-google-webfonts' ),
			'css'   => '"Rock Salt", cursive',
			'val'   => 'Rock+Salt',
			'size'  => '74',
			'type'  => 'cursive',
			'url'   => 'https://fonts.google.com/specimen/Rock+Salt',
		),

		'sacramento'           => array(
			'label' => __( 'Sacramento', 'gppro-google-webfonts' ),
			'css'   => '"Sacramento", cursive',
			'val'   => 'Sacramento',
			'size'  => '20',
			'type'  => 'cursive',
			'url'   => 'https://fonts.google.com/specimen/Sacramento',
		),

		'sofia'                => array(
			'label' => __( 'Sofia', 'gppro-google-webfonts' ),
			'css'   => '"Sofia", cursive',
			'val'   => 'Sofia',
			'size'  => '18',
			'type'  => 'cursive',
			'url'   => 'https://fonts.google.com/specimen/Sofia',
		),

		// The list of monospace fonts.
		'droid-sans-mono'      => array(
			'label' => __( 'Droid Sans Mono', 'gppro-google-webfonts' ),
			'css'   => '"Droid Sans Mono", monospace',
			'val'   => 'Droid+Sans+Mono',
			'size'  => '73',
			'type'  => 'mono',
			'url'   => 'https://fonts.google.com/specimen/Droid+Sans+Mono',
		),

		'source-code-pro'      => array(
			'label' => __( 'Source Code Pro', 'gppro-google-webfonts' ),
			'css'   => '"Source Code Pro", monospace',
			'val'   => 'Source+Code+Pro:400,700',
			'size'  => '48',
			'type'  => 'mono',
			'url'   => 'https://fonts.google.com/specimen/Source+Code+Pro',
		),

		'ubuntu-mono'          => array(
			'label' => __( 'Ubuntu Mono', 'gppro-google-webfonts' ),
			'css'   => '"Ubuntu Mono", monospace',
			'val'   => 'Ubuntu+Mono',
			'size'  => '18',
			'type'  => 'mono',
			'url'   => 'https://fonts.google.com/specimen/Ubuntu+Mono',
		),

	);

	foreach ( $webfonts as $font_key => $webfont ) {
		$webfonts[ $font_key ]['src']    = 'web';
		$webfonts[ $font_key ]['source'] = 'google';
		$webfonts[ $font_key ]['link']   = '//fonts.googleapis.com/css?family=' . $webfont['val'];
	}

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
