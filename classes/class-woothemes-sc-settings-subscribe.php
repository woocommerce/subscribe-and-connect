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
class Woothemes_SC_Settings_Subscribe extends Woothemes_SC_Settings_API {

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
	    $this->name = __( 'Subscription', 'woothemes-sc' );
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
	 * @uses  WooSlider_Utils::get_slider_types()
	 * @return void
	 */
	public function init_fields () {
		global $pagenow;

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

    	// Subscriptions
    	$fields['feedburner'] = array(
								'name' => __( 'Feedburner ID', 'woothemes-sc' ),
								'description' => sprintf( __( 'Your %sFeedburner ID%s for the e-mail subscription form.', 'woothemes-sc' ), '<a href="' . esc_url( 'http://www.woothemes.com/tutorials/how-to-find-your-feedburner-id-for-email-subscription/' ) . '">', '</a>' ),
								'type' => 'text',
								'default' => '',
								'section' => 'subscriptions'
								);

    	$fields['mailchimp'] = array(
								'name' => __( 'MailChimp', 'woothemes-sc' ),
								'description' => sprintf( __( 'The %sMailChimp List Subscribe URL%s for the list you want your visitors to subscribe to.', 'woothemes-sc' ), '<a href="' . esc_url( 'http://woochimp.heroku.com/' ) . '">', '</a>' ),
								'type' => 'text',
								'default' => '',
								'section' => 'subscriptions'
								);

		$this->fields = $fields;
	} // End init_fields()
} // End Class
?>