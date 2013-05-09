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
} // End Class
?>