<?php
global $woothemes_sc;
$woothemes_sc->setup_settings();
$woothemes_sc_settings = $woothemes_sc->get_settings();
if ( true == $woothemes_sc_settings['integration']['disable_theme_sc'] && ! function_exists( 'woo_subscribe_connect' ) ) {
	function woo_subscribe_connect() {} // End woo_subscribe_connect()
}

/**
 * Return HTML markup for the "connect" section.
 * @since  1.0.0
 * @return string HTML markup.
 */
function woothemes_sc_get_networks () {
	global $woothemes_sc;
	$settings = $woothemes_sc->get_settings();
	$networks = Woothemes_SC_Utils::get_networks_in_order( $settings['connect']['networks'], $settings['connect']['network_order'] );

	$list = '';
	if ( 0 < count( $networks ) ) {
		foreach ( $networks as $k => $v ) {
			if ( ! isset( $v['url'] ) || '' == $v['url'] ) continue;
			$class = $k;
			if ( '' != $v['image'] ) {
				$class .= ' has-image';
				$img = '<img src="' . esc_url( $v['image'] ) . '" />';
			} else {
				$class .= ' uses-theme';
				$img = '';
			}

			// Parse and apply the icon theme, if applicable.
			$theme = 'default';
			if ( $woothemes_sc->context->is_valid_theme( $settings['integration']['theme'] ) ) {
				$theme = $woothemes_sc->context->get_sanitized_theme_key( $settings['integration']['theme'] );
			}
			$class .= ' icon-theme-' . $theme;

			$list .= '<li class="' . esc_attr( $class ) . '"><a href="' . esc_url( $v['url'] ) . '"><span>' . "\n";
			if ( '' != $img ) {
				$list .= $img;
			} else {

			}
			$list .= '</span></a></li>' . "\n";
		}
	}

	$html = '';
	$html .= '<div class="woothemes-sc-connect">' . "\n";
	if ( '' != $list ) {
		$html .= '<ul class="icons">' . "\n";
		$html .= $list;
		$html .= '</ul><!--/.icons-->' . "\n";
	}
	$html .= '</div><!--/.woothemes-sc-connect-->' . "\n";

	return $html;
} // End woothemes_sc_get_networks()
?>