<?php
/**
 * Genesis Design Palette Pro - Google Webfonts Font Search layout
 *
 * Enables the user to select any Google font to use with Design Palette Pro.
 *
 * @package Design Palette Pro - Google Webfonts
 */
?>

<div class="gppro-google-webfonts-fonts">
	<ul>
	<?php
	foreach( $fonts->items as $font ) :
		$url = 'https://fonts.google.com/specimen/' . str_replace( ' ', '+', $font->family );
	?>
		<li
			data-family="<?php echo esc_attr( $font->family ); ?>"
			data-category="<?php echo esc_attr( $font->category ); ?>"
			data-variants="<?php echo esc_attr( implode( '|', $font->variants ) ); ?>"
		>
			<a href="<?php echo esc_url( $url ); ?>" target="_blank"><?php echo esc_html( $font->family ); ?></a>
		</li>
	<?php endforeach; ?>
	</ul>
</div>
