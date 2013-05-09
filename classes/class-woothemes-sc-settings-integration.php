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

		$sections['manual'] = array(
					'name' 			=> __( 'Manual Integration', 'woothemes-sc' ),
					'description'	=> __( 'Finely tuned control over where Subscribe & Connect integrates into your website.', 'woothemes-sc' )
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

    	// Automated
    	$fields['autoslide'] = array(
								'name' => '',
								'description' => __( 'Animate the slideshows automatically', 'woothemes-sc' ),
								'type' => 'checkbox',
								'default' => true,
								'section' => 'automated'
								);

    	$fields['direction'] = array(
								'name' => __( 'Slide Direction', 'woothemes-sc' ),
								'description' => __( 'The direction to slide (if using the "Slide" animation)', 'woothemes-sc' ),
								'type' => 'select',
								'default' => 'horizontal',
								'section' => 'automated',
								'required' => 0,
								'options' => array( 'horizontal' => __( 'Horizontal', 'woothemes-sc' ), 'vertical' => __( 'Vertical', 'woothemes-sc' ) )
								);

    	// Manual
    	$fields['use_custom_hook'] = array(
								'name' => '',
								'description' => __( 'Display "Subscribe & Connect" on a Custom Hook', 'woothemes-sc' ),
								'type' => 'checkbox',
								'default' => true,
								'section' => 'manual'
								);
    	$fields['custom_hook_name'] = array(
								'name' => __( 'Custom Hook Name', 'woothemes-sc' ),
								'description' => __( 'The name of the hook you want to use (for example, loop_end).', 'woothemes-sc' ),
								'type' => 'text',
								'default' => '',
								'section' => 'manual'
								);

		$this->fields = $fields;
	} // End init_fields()
} // End Class
?>