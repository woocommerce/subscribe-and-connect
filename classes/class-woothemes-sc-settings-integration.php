<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
 * Subscribe & Connect Integration Settings
 *
 * All functionality pertaining to the integration settings screen.
 *
 * @package WordPress
 * @subpackage Woothemes_SC
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
class Woothemes_SC_Settings_Integration extends Woothemes_SC_Settings_API {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct () {
	    parent::__construct(); // Required in extended classes.
	    $this->token = 'woothemes-sc-integration';
	    $this->name = __( 'Integration', 'woothemes-sc' );
	    $this->_themes = Woothemes_SC_Utils::get_icon_themes();
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
					'name' 			=> __( 'Automated Integration', 'woothemes-sc' ),
					'description'	=> __( 'Attempt to automatically integrate Subscribe & Connect into your website.', 'woothemes-sc' )
				);

		if ( function_exists( 'woo_subscribe_connect' ) ) {
			$theme = wp_get_theme();
			$sections['woothemes'] = array(
						'name' 			=> __( 'WooThemes Overrides', 'woothemes-sc' ),
						'description'	=> sprintf( __( 'We noticed your current active theme, %s, uses the WooFramework and includes our original Subscribe & Connect feature. Override it here.', 'woothemes-sc' ), $theme->__get( 'name' ) )
					);
		}

		$sections['manual'] = array(
					'name' 			=> __( 'Advanced Integration', 'woothemes-sc' ),
					'description'	=> __( 'Finely tuned control over where Subscribe & Connect integrates into your website.', 'woothemes-sc' )
				);

		if ( 1 < count( $this->_themes ) ) {
			$sections['presentation'] = array(
					'name' 			=> __( 'Presentation', 'woothemes-sc' ),
					'description'	=> __( 'Determine the look and feel of the connect icons.', 'woothemes-sc' )
				);
		}

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
    	$auto_options = array( 'none' => __( 'No automated integration', 'woothemes-sc' ), 'the_content' => __( 'Display after the post content', 'woothemes-sc' ) );
    	if ( defined( 'THEME_FRAMEWORK' ) && 'woothemes' == constant( 'THEME_FRAMEWORK' ) ) {
    		$auto_options['woo_post_after'] = sprintf( __( 'Display on the %s hook %s', 'woothemes-sc' ), '<code>woo_post_after-single</code>', '<small>(thanks for using WooThemes!)</small>' );
    	}
    	$fields['auto_integration'] = array(
								'name' => __( 'Automated integration method', 'woothemes-sc' ),
								'description' => '',
								'type' => 'radio',
								'default' => 'none',
								'section' => 'automated',
								'options' => $auto_options
								);
    	// Manual
    	$fields['custom_hook_name'] = array(
								'name' => __( 'Display on a Custom Hook', 'woothemes-sc' ),
								'description' => __( 'The name of the hook you want to use (for example, loop_end).', 'woothemes-sc' ),
								'type' => 'text',
								'default' => '',
								'section' => 'manual'
								);

    	// WooThemes
    	$fields['disable_theme_sc'] = array(
								'name' => '',
								'description' => sprintf( __( 'Hide the Subscribe & Connect feature in %s.', 'woothemes-sc' ), $theme->__get( 'name' ) ),
								'type' => 'checkbox',
								'default' => true,
								'section' => 'woothemes'
								);

    	// Presentation
    	if ( 1 < count( $this->_themes ) ) {
    		$themes = array();
    		foreach ( $this->_themes as $k => $v ) {
    			$themes[$k] = $v['name'];
    		}
    		$fields['theme'] = array(
								'name' => 'Icon Design',
								'description' => sprintf( __( 'Choose a design for how your social icons will be presented within %s.', 'woothemes-sc' ), $theme->__get( 'name' ) ),
								'type' => 'select',
								'default' => 'default',
								'options' => $themes,
								'section' => 'presentation'
								);
    	} else {
    		$fields['theme'] = array(
								'name' => '',
								'description' => '',
								'type' => 'hidden',
								'default' => 'default',
								'section' => 'manual'
								);
    	}

		$this->fields = $fields;
	} // End init_fields()
} // End Class
?>