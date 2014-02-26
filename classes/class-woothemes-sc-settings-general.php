<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
 * Subscribe & Connect Subscribe Settings
 *
 * All functionality pertaining to the subscribe settings screen.
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
class Woothemes_SC_Settings_General extends Woothemes_SC_Settings_API {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct () {
	    parent::__construct(); // Required in extended classes.
	    $this->token = 'woothemes-sc-subscribe';
	    $this->name = __( 'General', 'woothemes-sc' );
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

		$sections['text'] = array(
					'name' 			=> __( 'Heading and Text', 'woothemes-sc' ),
					'description'	=> __( 'Default settings for the heading and text of your "Subscribe" section.', 'woothemes-sc' )
				);

		$this->sections = $sections;
	} // End init_sections()

	/**
	 * init_fields function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function init_fields () {
		global $pagenow;

		$newsletter_services = array( 'none' => __( 'None', 'woothemes-sc' ), 'aweber' => __( 'Aweber', 'woothemes-sc' ), 'campaign_monitor' => __( 'Campaign Monitor', 'woothemes-sc' ), 'feedburner' => __( 'Feedburner', 'woothemes-sc' ), 'mad_mimi' => __( 'Mad Mimi', 'woothemes-sc' ), 'mailchimp' => __( 'Mailchimp', 'woothemes-sc' ) );

	    $fields = array();

    	// Text
    	$fields['title'] = array(
								'name' => __( 'Title', 'woothemes-sc' ),
								'description' => __( 'The default title text.', 'woothemes-sc' ),
								'type' => 'text',
								'default' => sprintf( __( 'Subscribe to %s', 'woothemes-sc' ), get_bloginfo( 'name' ) ),
								'section' => 'text'
								);

    	$fields['text'] = array(
								'name' => __( 'Text', 'woothemes-sc' ),
								'description' => __( 'The default call-to-action text.', 'woothemes-sc' ),
								'type' => 'textarea',
								'default' => sprintf( __( 'Keep up to date on the latest content, here at %s. Subscribe below.', 'woothemes-sc' ), get_bloginfo( 'name' ) ),
								'section' => 'text'
								);

		$this->fields = $fields;
	} // End init_fields()
} // End Class
?>