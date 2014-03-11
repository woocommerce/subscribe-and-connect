<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
 * Subscribe & Connect Integration Settings
 *
 * All functionality pertaining to the integration settings screen.
 *
 * @package WordPress
 * @subpackage Subscribe_And_Connect
 * @category Admin
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * - __construct()
 * - init_sections()
 * - init_fields()
 * - get_duration_options()
 */
class Subscribe_And_Connect_Settings_Display extends Subscribe_And_Connect_Settings_API {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct () {
	    parent::__construct(); // Required in extended classes.
	    $this->token = 'subscribe-and-connect-integration';
	    $this->name = __( 'Display', 'subscribe-and-connect' );
	    $this->_themes = Subscribe_And_Connect_Utils::get_icon_themes();

	    if ( is_admin() ) add_action( 'subscribe_and_connect_field_radio_after', array( $this, 'display_supported_post_types' ) );
	} // End __construct()

	/**
	 * init_sections function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function init_sections () {
		$sections = array();

		$sections['automated'] = array(
					'name' 			=> __( 'Display Options', 'subscribe-and-connect' ),
					'description'	=> __( 'Attempt to automatically display Subscribe & Connect into your website.', 'subscribe-and-connect' )
				);

		if ( function_exists( 'woo_subscribe_connect' ) ) {
			$theme = wp_get_theme();
			$sections['woothemes'] = array(
						'name' 			=> __( 'WooThemes Overrides', 'subscribe-and-connect' ),
						'description'	=> sprintf( __( 'We noticed your current active theme, %s, uses the WooFramework and includes our original Subscribe & Connect feature. Override it here.', 'subscribe-and-connect' ), $theme->__get( 'name' ) )
					);
		}

		if ( 1 < count( $this->_themes ) ) {
			$sections['presentation'] = array(
					'name' 			=> __( 'Design', 'subscribe-and-connect' ),
					'description'	=> __( 'Determine the look and feel of the connect icons.', 'subscribe-and-connect' )
				);
		}

		$sections['manual'] = array(
					'name' 			=> __( 'Advanced Integration', 'subscribe-and-connect' ),
					'description'	=> __( 'Finely tuned control over where Subscribe & Connect integrates into your website.', 'subscribe-and-connect' )
				);

		$this->sections = $sections;
	} // End init_sections()

	/**
	 * init_fields function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @uses  WooSlider_Utils::get_slider_types()
	 * @return void
	 */
	public function init_fields () {
		global $pagenow;

	    $fields = array();

	    $theme = wp_get_theme();

    	// Automated
    	$auto_options = array(
    		'none' 			=> __( 'Don\'t display automatically', 'subscribe-and-connect' ),
    		'the_content' 	=> __( 'Display beneath posts', 'subscribe-and-connect' )
    		);

    	if ( defined( 'THEME_FRAMEWORK' ) && 'woothemes' == constant( 'THEME_FRAMEWORK' ) ) {
    		$auto_options['woo_post_after'] = sprintf( __( 'Use the %s hook provided by your WooTheme (recommended)', 'subscribe-and-connect' ), '<code>woo_post_after</code>' );
    	}

    	$fields['auto_integration'] = array(
								'name' 			=> __( 'Display method', 'subscribe-and-connect' ),
								'description' 	=> '',
								'type' 			=> 'radio',
								'default' 		=> 'none',
								'section' 		=> 'automated',
								'options' 		=> $auto_options
								);

    	// WooThemes
    	$fields['disable_theme_sc'] = array(
								'name' 			=> '',
								'description' 	=> sprintf( __( 'Hide the Subscribe & Connect feature in %s.', 'subscribe-and-connect' ), $theme->__get( 'name' ) ),
								'type' 			=> 'checkbox',
								'default' 		=> true,
								'section' 		=> 'woothemes'
								);

    	// Manual
    	$fields['custom_hook_name'] = array(
								'name' 			=> __( 'Display on a custom hook', 'subscribe-and-connect' ),
								'description' 	=> __( 'The name of the hook you want to use (for example, loop_end).', 'subscribe-and-connect' ),
								'type' 			=> 'text',
								'default' 		=> '',
								'section' 		=> 'manual'
								);

    	// Presentation
    	if ( 1 < count( $this->_themes ) ) {
    		$themes = array();
    		foreach ( $this->_themes as $k => $v ) {
    			$themes[$k] = $v['name'];
    		}
    		$fields['theme'] = array(
								'name' 			=> 'Icon Style',
								'description' 	=> sprintf( __( 'Choose a design for how your social icons will be presented within %s. Select "No Style" if you want to apply your own custom design.', 'subscribe-and-connect' ), $theme->__get( 'name' ) ),
								'type' 			=> 'select',
								'default' 		=> 'default',
								'options' 		=> $themes,
								'section' 		=> 'presentation'
								);
    	} else {
    		$fields['theme'] = array(
								'name' 			=> '',
								'description' 	=> '',
								'type' 			=> 'hidden',
								'default' 		=> 'default',
								'section' 		=> 'manual'
								);
    	}

		$this->fields = $fields;
	} // End init_fields()

	/**
	 * Display a list of supported post types below the "automated integration" setting.
	 * @access  public
	 * @since   1.0.0
	 * @return  string $html Rendered HTML markup.
	 */
	public function display_supported_post_types () {
		$supported = Subscribe_And_Connect_Utils::get_supported_post_types();
		$html = '';

		if ( 0 < count( $supported ) ) {
			$post_type_names = array();
			$last_one = '';
			if ( 1 < count( $supported ) ) {
				$last_item_obj = array_pop( $supported );
				$last_one = __( 'and', 'subscribe-and-connect' ) . ' <code>' . $last_item_obj->labels->name . '</code>';
			}
			foreach ( $supported as $k => $v ) {
				$post_type_names[] = '<code>' . $v->labels->name . '</code>';
			}
			$post_types = join( $post_type_names ) . $last_one;
			$html .= sprintf( __( 'If the automated integration is used, Subscribe & Connect will display on posts of the %1$s types.', 'subscribe-and-connect' ), $post_types ) . '<br /><br />';
		}

		$html .= wpautop( '<small>' . sprintf( __( 'To integrate a specific post type with Subscribe & Connect, add %1$s to your %2$s in your theme, or to your custom plugin.', 'subscribe-and-connect' ), '<code><small>add_post_type_support( \'your-post-type\', \'subscribe-and-connect\' );</small></code>', '<code><small>functions.php</small></code>' ) . '</small>' );

		return $html;
	} // End display_supported_post_types()
} // End Class
?>