<?php
class Woothemes_SC_Utils {
	/**
	 * Get an array of supported networks.
	 * @access  public
	 * @since   1.0.0
	 * @return  array key => value pairs of supported networks.
	 */
	public static function get_supported_networks () {
		return (array)apply_filters( 'woothemes_sc_supported_networks', array(
			'facebook' => __( 'Facebook', 'woothemes-sc' ),
			'twitter' => __( 'Twitter', 'woothemes-sc' )
			) );
	} // End get_supported_networks()

	/**
	 * Get the placeholder thumbnail image.
	 * @access  public
	 * @since   1.0.0
	 * @return  string The URL to the placeholder thumbnail image.
	 */
	public static function get_placeholder_image () {
		global $woothemes_sc;
		return esc_url( apply_filters( 'woothemes_sc_placeholder_thumbnail', $woothemes_sc->context->__get( 'plugin_url' ) . 'assets/images/placeholder.png' ) );
	} // End get_placeholder_image()
} // End Class
?>