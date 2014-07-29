<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
 * Subscribe & Connect Subscribe Settings
 *
 * All functionality pertaining to the subscribe settings screen.
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
class Subscribe_And_Connect_Settings_General extends Subscribe_And_Connect_Settings_API {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct () {
	    parent::__construct(); // Required in extended classes.
	    $this->token = 'subscribe-and-connect-subscribe';
	    $this->name = __( 'General', 'subscribe-and-connect' );
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
					'name' 			=> __( 'Content', 'subscribe-and-connect' ),
					'description'	=> __( 'Subscribe &amp; Connect component title and description.', 'subscribe-and-connect' )
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

		$newsletter_services = array(
								'none' 				=> __( 'None', 'subscribe-and-connect' ),
								'aweber' 			=> __( 'Aweber', 'subscribe-and-connect' ),
								'campaign_monitor' 	=> __( 'Campaign Monitor', 'subscribe-and-connect' ),
								'feedburner' 		=> __( 'Feedburner', 'subscribe-and-connect' ),
								'mad_mimi' 			=> __( 'Mad Mimi', 'subscribe-and-connect' ),
								'mailchimp' 		=> __( 'Mailchimp', 'subscribe-and-connect' )
								);
		
		// Check if MailPoet is installed and add as option
		if ( class_exists( 'WYSIJA' ) ) {
			$newsletter_services['wysija'] = __( 'MailPoet Newsletters', 'subscribe-and-connect' );
		}


	    $fields = array();

    	// Text
    	$fields['title'] = array(
								'name' 				=> __( 'Title', 'subscribe-and-connect' ),
								'type' 				=> 'text',
								'default' 			=> sprintf( __( 'Subscribe to %s', 'subscribe-and-connect' ), get_bloginfo( 'name' ) ),
								'section' 			=> 'text'
								);

    	$fields['text'] = array(
								'name' 				=> __( 'Description', 'subscribe-and-connect' ),
								'type' 				=> 'textarea',
								'default' 			=> sprintf( __( 'Keep up to date on the latest content, here at %s. Subscribe below.', 'subscribe-and-connect' ), get_bloginfo( 'name' ) ),
								'section' 			=> 'text'
								);

		$this->fields = $fields;
	} // End init_fields()
} // End Class