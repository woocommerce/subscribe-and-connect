<?php
global $woothemes_sc;
$woothemes_sc->setup_settings();
$woothemes_sc_settings = $woothemes_sc->get_settings();
if ( true == $woothemes_sc_settings['integration']['disable_theme_sc'] && ! function_exists( 'woo_subscribe_connect' ) ) {
	function woo_subscribe_connect() {} // End woo_subscribe_connect()
}
?>