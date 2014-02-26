<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
 * Subscribe & Connect Connect Settings
 *
 * All functionality pertaining to the connect settings screen.
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
class Woothemes_SC_Settings_Connect extends Woothemes_SC_Settings_API {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct () {
	    parent::__construct(); // Required in extended classes.
	    $this->token = 'woothemes-sc-connect';
	    $this->name = __( 'Connect', 'woothemes-sc' );
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

		$sections['networks'] = array(
					'name' 			=> __( 'Social Networks', 'woothemes-sc' ),
					'description'	=> __( 'Add links and icons to the social networks you\'d like to link to.', 'woothemes-sc' )
				);

		$sections['subscriptions'] = array(
					'name' 			=> __( 'Subscription Options', 'woothemes-sc' ),
					'description'	=> __( 'Setup the various ways in which your visitors can subscribe to your content.', 'woothemes-sc' )
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

	    $fields = array();

    	$fields['networks'] = array(
								'name' 			=> __( 'Social Networks', 'woothemes-sc' ),
								'description' 	=> __( 'The social networks to be linked to in the "Connect" portion of the output.', 'woothemes-sc' ),
								'type' 			=> 'network',
								'default' 		=> 'facebook',
								'section' 		=> 'networks',
								'required' 		=> 0
								);

    	$fields['networks_order'] = array(
								'name' 			=> '',
								'description' 	=> '',
								'type' 			=> 'hidden',
								'default' 		=> '',
								'section' 		=> 'networks',
								'required' 		=> 0
								);

    	// Subscriptions
    	$newsletter_services = array(
    							'none' 				=> __( 'None', 'woothemes-sc' ),
    							'aweber' 			=> __( 'Aweber', 'woothemes-sc' ),
    							'campaign_monitor' 	=> __( 'Campaign Monitor', 'woothemes-sc' ),
    							'feedburner' 		=> __( 'Feedburner', 'woothemes-sc' ),
    							'mad_mimi' 			=> __( 'Mad Mimi', 'woothemes-sc' ),
    							'mailchimp' 		=> __( 'Mailchimp', 'woothemes-sc' )
    							);

    	$fields['newsletter_service'] = array(
								'name' 			=> __( 'Newsletter Service', 'woothemes-sc' ),
								'description' 	=> __( 'Select the newsletter service you are using', 'woothemes-sc' ),
								'type' 			=> 'select',
								'default' 		=> 'none',
								'options' 		=> $newsletter_services,
								'section' 		=> 'subscriptions'
								);

    	$fields['newsletter_service_id'] = array(
								'name' 			=> __( 'Feedburner Feed ID', 'woothemes-sc' ),
								'description' 	=> sprintf( __( 'Enter the your Feedburner Feed ID %s(?)%s.', 'woothemes-sc' ), '<a href="' . esc_url( 'http://support.google.com/feedburner/bin/answer.py?hl=en&answer=78982' ) . '" target="_blank">', '</a>' ),
								'type' 			=> 'text',
								'default' 		=> '' ,
								'section' 		=> 'subscriptions'
								);

		$fields['newsletter_service_form_action'] = array(
								'name' 			=> __( 'Newsletter Service Form Action', 'woothemes-sc' ),
								'description' 	=> __( 'Enter the the form action if required.', 'woothemes-sc' ),
								'type' 			=> 'text',
								'default' 		=> '' ,
								'section' 		=> 'subscriptions'
								);

		$fields['newsletter_mail_chimp_list_subscription_url'] = array(
								'name' 			=> __( 'MailChimp List Subscription URL', 'woothemes-sc' ),
								'description' 	=> sprintf( __( 'If you have a MailChimp account you can enter the %sMailChimp List Subscribe URL%s to allow your users to subscribe to a MailChimp List.', 'woothemes-sc' ), '<a href="' . esc_url( 'http://woochimp.heroku.com/' ) . '" target="_blank">', '</a>' ),
								'type' 			=> 'text',
								'default' 		=> '' ,
								'section' 		=> 'subscriptions'
								);

		$fields['newsletter_mad_mimi_subscription_url'] = array(
								'name' 			=> __( 'Mad Mimi Webform URL', 'woothemes-sc' ),
								'description' 	=> __( 'Your Mad Mini Webform URL, eg. https://madmimi.com/signups/subscribe/84680', 'woothemes-sc' ),
								'type' 			=> 'text',
								'default' 		=> '' ,
								'section' 		=> 'subscriptions'
								);

		$fields['newsletter_aweber_list_id'] = array(
								'name' 			=> __( 'Aweber List Name', 'woothemes-sc' ),
								'description' 	=> __( 'The name of the list to subscribe users to.', 'woothemes-sc' ),
								'type' 			=> 'text',
								'default' 		=> '' ,
								'section' 		=> 'subscriptions'
								);

		$fields['rss'] = array(
								'name' 			=> __( 'Enable RSS', 'woothemes-sc' ),
								'description' 	=> __( 'Display an RSS icon along with your social networks.', 'woothemes-sc' ),
								'type' 			=> 'checkbox',
								'default' 		=> true,
								'section' 		=> 'subscriptions',
								'required' 		=> 0
								);

		$this->fields = $fields;
	} // End init_fields()
} // End Class
?>