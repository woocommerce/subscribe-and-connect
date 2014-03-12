<?php
class Subscribe_And_Connect_Utils {
	/**
	 * Get an array of supported networks.
	 * @access  public
	 * @since   1.0.0
	 * @return  array key => value pairs of supported networks.
	 */
	public static function get_supported_networks () {
		return (array)apply_filters( 'subscribe_and_connect_supported_networks', array(
			'facebook' 		=> __( 'Facebook', 'subscribe-and-connect' ),
			'twitter' 		=> __( 'Twitter', 'subscribe-and-connect' ),
			'pinterest' 	=> __( 'Pinterest', 'subscribe-and-connect' ),
			'youtube' 		=> __( 'YouTube', 'subscribe-and-connect' ),
			'instagram' 	=> __( 'Instagram', 'subscribe-and-connect' ),
			'flickr' 		=> __( 'Flickr', 'subscribe-and-connect' ),
			'google_plus' 	=> __( 'Google +', 'subscribe-and-connect' ),
			'linkedin' 		=> __( 'LinkedIn', 'subscribe-and-connect' ),
			'vimeo' 		=> __( 'Vimeo', 'subscribe-and-connect' ),
			'tumblr' 		=> __( 'Tumblr', 'subscribe-and-connect' ),
			'dribbble' 		=> __( 'Dribbble', 'subscribe-and-connect' ),
			'appdotnet' 	=> __( 'App.net', 'subscribe-and-connect' ),
			'github' 		=> __( 'Github', 'subscribe-and-connect' )
			) );
	} // End get_supported_networks()

	/**
	 * Get the placeholder thumbnail image.
	 * @access  public
	 * @since   1.0.0
	 * @return  string The URL to the placeholder thumbnail image.
	 */
	public static function get_placeholder_image () {
		global $subscribe_and_connect;
		return esc_url( apply_filters( 'subscribe_and_connect_placeholder_thumbnail', $subscribe_and_connect->context->__get( 'plugin_url' ) . 'assets/images/placeholder.png' ) );
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

		return (array)$networks;
	} // End get_networks_in_order()

	/**
	 * Get an array of the supported icon themes.
	 * @access  public
	 * @since   1.0.0
	 * @return  array The icon themes supported by Subscribe & Connect.
	 */
	public static function get_icon_themes () {
		global $subscribe_and_connect;
		return (array)apply_filters( 'subscribe_and_connect_icon_themes', array(
										'none' 		=> array(
															'name' 			=> __( 'No style', 'subscribe-and-connect' ),
															'stylesheet' 	=> ''
															),
										'icons' 	=> array(
															'name' 			=> __( 'Icons Only', 'subscribe-and-connect' ),
															'stylesheet' 	=> esc_url( $subscribe_and_connect->context->__get( 'plugin_url' ) . 'assets/css/themes/icons.css' )
															),
										'boxed' 	=> array(
															'name' 			=> __( 'Boxed', 'subscribe-and-connect' ),
															'stylesheet' 	=> esc_url( $subscribe_and_connect->context->__get( 'plugin_url' ) . 'assets/css/themes/boxed.css' )
															),
										'rounded' 	=> array(
															'name' 			=> __( 'Rounded', 'subscribe-and-connect' ),
															'stylesheet' 	=> esc_url( $subscribe_and_connect->context->__get( 'plugin_url' ) . 'assets/css/themes/rounded.css' )
															),
										'circular' 	=> array(
															'name' 			=> __( 'Circular', 'subscribe-and-connect' ),
															'stylesheet' 	=> esc_url( $subscribe_and_connect->context->__get( 'plugin_url' ) . 'assets/css/themes/circular.css' )
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