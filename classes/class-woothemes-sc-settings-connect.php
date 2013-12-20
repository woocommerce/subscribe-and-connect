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

	    $fields['rss'] = array(
								'name' => __( 'Enable RSS', 'woothemes-sc' ),
								'description' => __( 'Display an RSS icon along with your social networks.', 'woothemes-sc' ),
								'type' => 'checkbox',
								'default' => true,
								'section' => 'networks',
								'required' => 0
								);

    	$fields['networks'] = array(
								'name' => __( 'Social Networks', 'woothemes-sc' ),
								'description' => __( 'The social networks to be linked to in the "Connect" portion of the output.', 'woothemes-sc' ),
								'type' => 'network',
								'default' => 'facebook',
								'section' => 'networks',
								'required' => 0
								);

    	$fields['networks_order'] = array(
								'name' => '',
								'description' => '',
								'type' => 'hidden',
								'default' => '',
								'section' => 'networks',
								'required' => 0
								);

		$this->fields = $fields;
	} // End init_fields()
} // End Class
?>