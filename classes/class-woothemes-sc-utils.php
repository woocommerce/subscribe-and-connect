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
			'facebook' 		=> __( 'Facebook', 'woothemes-sc' ),
			'twitter' 		=> __( 'Twitter', 'woothemes-sc' ),
			'pinterest' 	=> __( 'Pinterest', 'woothemes-sc' ),
			'youtube' 		=> __( 'YouTube', 'woothemes-sc' ),
			'instagram' 	=> __( 'Instagram', 'woothemes-sc' ),
			'flickr' 		=> __( 'Flickr', 'woothemes-sc' ),
			'google_plus' 	=> __( 'Google +', 'woothemes-sc' ),
			'linkedin' 		=> __( 'LinkedIn', 'woothemes-sc' ),
			'vimeo' 		=> __( 'Vimeo', 'woothemes-sc' ),
			'tumblr' 		=> __( 'Tumblr', 'woothemes-sc' ),
			'dribbble' 		=> __( 'Dribbble', 'woothemes-sc' ),
			'appdotnet' 	=> __( 'App.net', 'woothemes-sc' ),
			'github' 		=> __( 'Github', 'woothemes-sc' )
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

	/**
	 * Get an array of networks, in a specified order.
	 * @access  public
	 * @since   1.0.0
	 * @return  array The ordered array of networks.
	 */
	public static function get_networks_in_order ( $networks, $order ) {
		$order_entries = array();
		if ( '' != $order ) {
			$order_entries = explode( ',', $order );
		}

		// Re-order the networks according to the stored order.
		if ( 0 < count( $order_entries ) ) {
			$original_networks = $networks; // Make a backup before we overwrite.
			$networks = array();
			foreach ( $order_entries as $k => $v ) {
				$networks[$v] = $original_networks[$v];
				unset( $original_networks[$v] );
			}
			if ( 0 < count( $original_networks ) ) {
				$networks = array_merge( $networks, $original_networks );
			}
		}

		return $networks;
	} // End get_networks_in_order()

	/**
	 * Get an array of the supported icon themes.
	 * @access  public
	 * @since   1.0.0
	 * @return  array The icon themes supported by Subscribe & Connect.
	 */
	public static function get_icon_themes () {
		global $woothemes_sc;
		return (array)apply_filters( 'woothemes_sc_icon_themes', array(
										'none' 		=> array(
															'name' 			=> __( 'No style', 'woothemes-sc' ),
															'stylesheet' 	=> ''
															),
										'icons' 	=> array(
															'name' 			=> __( 'Icons Only', 'woothemes-sc' ),
															'stylesheet' 	=> esc_url( $woothemes_sc->context->__get( 'plugin_url' ) . 'assets/css/themes/icons.css' )
															),
										'boxed' 	=> array(
															'name' 			=> __( 'Boxed', 'woothemes-sc' ),
															'stylesheet' 	=> esc_url( $woothemes_sc->context->__get( 'plugin_url' ) . 'assets/css/themes/boxed.css' )
															),
										'rounded' 	=> array(
															'name' 			=> __( 'Rounded', 'woothemes-sc' ),
															'stylesheet' 	=> esc_url( $woothemes_sc->context->__get( 'plugin_url' ) . 'assets/css/themes/rounded.css' )
															),
										'circles' 	=> array(
															'name' 			=> __( 'Circles', 'woothemes-sc' ),
															'stylesheet' 	=> esc_url( $woothemes_sc->context->__get( 'plugin_url' ) . 'assets/css/themes/circles.css' )
															),
										)
									);
	} // End get_icon_themes()

	/**
	 * Get an array of the supported post types.
	 * @access  public
	 * @since   1.0.0
	 * @return  array The post types supported by Subscribe & Connect. Only public post types.
	 */
	public static function get_supported_post_types () {
		$supported = array();
		$all = get_post_types( array( 'public' => true ), 'objects' );
		if ( ! is_wp_error( $all ) && 0 < count( $all ) ) {
			foreach ( $all as $k => $v ) {
				if ( post_type_supports( $k, 'subscribe-and-connect' ) ) {
					$supported[$k] = $v;
				}
			}
		}

		return $supported;
	} // End get_supported_post_types()
} // End Class
?>