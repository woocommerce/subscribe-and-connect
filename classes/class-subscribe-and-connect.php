<?php
class Subscribe_And_Connect {
	/**
	 * Property to contain the main plugin's file path.
	 * @access  public
	 * @since   1.0.0
	 * @var     object
	 */
	public $file;

	/**
	 * Property to contain the version number.
	 * @access  public
	 * @since   1.0.0
	 * @var     object
	 */
	public $version;

	/**
	 * Property to contain the Subscribe_And_Connect_Admin or Subscribe_And_Connect_Frontend object.
	 * @access  public
	 * @since   1.0.0
	 * @var     object
	 */
	public $context;

	/**
	 * Property to contain the various settings objects.
	 * @access  public
	 * @since   1.0.0
	 * @var     array
	 */
	public $settings_objs;

	/**
	 * Property to contain all settings, distributed by object.
	 * @access  private
	 * @since   1.0.0
	 * @var     array
	 */
	private $_settings;

	/**
	 * Constructor.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct ( $file ) {
		$this->file = $file;

		// Setup the settings objects.
		add_action( 'admin_init', array( $this, 'setup_settings' ) );

		// Maybe override the WooThemes settings.
		add_filter( 'option_woo_template', array( $this, 'maybe_override_woo_options' ) );
		add_action( 'widgets_init', array( $this, 'maybe_unregister_widget' ) );

		// Load in the utility functions.
		require_once( 'class-subscribe-and-connect-utils.php' );
		if ( is_admin() ) {
			// Load in the admin functionality.
			require_once( 'class-subscribe-and-connect-admin.php' );
			$this->context = new Subscribe_And_Connect_Admin( $file );
		} else {
			// Load in the frontend functionality.
			require_once( 'class-subscribe-and-connect-frontend.php' );
			$this->context = new Subscribe_And_Connect_Frontend( $file );
		}

		// Add support for posts and pages.
		add_action( 'after_setup_theme', array( $this, 'add_default_post_types_support' ) );

		// Load the localisation files for this plugin.
		add_action( 'plugins_loaded', array( $this, 'load_localisation' ) );
	} // End __construct()

	/**
	 * Load this plugin's localisation files.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'subscribe-and-connect', false, plugin_dir_path( $this->file ) . 'languages' );
	} // End load_localisation()

	/**
	 * Add support for the default post types.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function add_default_post_types_support () {
		add_post_type_support( 'post', 'subscribe-and-connect' );
		do_action( 'add_default_post_types_support' );
	} // End add_default_post_types_support()

	/**
	 * Setup a settings object for our current tab, if applicable.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function setup_settings () {
		require_once( 'class-subscribe-and-connect-settings-api.php' );

		// Load in the different settings sections.
		require_once( 'class-subscribe-and-connect-settings-general.php' );
		require_once( 'class-subscribe-and-connect-settings-connect.php' );
		require_once( 'class-subscribe-and-connect-settings-display.php' );

		$this->settings_objs = array();

		// Setup "Subscribe" settings.
		$this->settings_objs['general'] = new Subscribe_And_Connect_Settings_General();
		$this->settings_objs['general']->setup_settings();

		// Setup "Connect" settings.
		$this->settings_objs['connect'] = new Subscribe_And_Connect_Settings_Connect();
		$this->settings_objs['connect']->setup_settings();

		// Setup "Integration" settings.
		$this->settings_objs['display'] = new Subscribe_And_Connect_Settings_Display();
		$this->settings_objs['display']->setup_settings();

		$this->settings_objs = (array)apply_filters( 'subscribe_and_connect_setup_settings', $this->settings_objs );
	} // End setup_settings()

	/**
	 * Retrieve the settings from each section, separated by object.
	 * @access  public
	 * @since   1.0.0
	 * @return  array Settings.
	 */
	public function get_settings () {
		if ( is_array( $this->_settings ) && 0 < count( $this->_settings ) ) return $this->_settings;

		$this->_settings = array();
		if ( 0 < count( $this->settings_objs ) ) {
			foreach ( $this->settings_objs as $k => $v ) {
				$this->_settings[$k] = $v->get_settings();
			}
		}

		return $this->_settings;
	} // End get_settings()

	/**
	 * Attempt to remove the Subscribe & Connect theme options.
	 * @access  public
	 * @since   1.0.0
	 * @param   array $options Array of options.
	 * @return  array          Modified array of options.
	 */
	public function maybe_override_woo_options ( $options ) {
		$settings = $this->get_settings();

		if ( isset( $settings['display']['disable_theme_sc'] ) && true == $settings['display']['disable_theme_sc'] ) {
			$detected_sc = false;
			foreach ( $options as $k => $v ) {
				// Remove the section heading. This will kick start the removal of fields.
				if ( 'heading' == $v['type'] && 'connect' == $v['icon'] ) {
					$detected_sc = true;
					unset( $options[$k] );
					continue; // Move to the next itteration.
				}
				if ( true == $detected_sc ) {
					if ( 'heading' == $v['type'] ) $detected_sc = false; // If we're at the next section heading, stop removing fields.
					if ( true == $detected_sc ) unset( $options[$k] ); // Remove the field, if we're still set to remove fields.
				}

				// Remove the "Enable Subscribe & Connect" option from the theme options.
				if ( isset( $v['id'] ) && 'woo_contact_subscribe_and_connect' == $v['id'] ) unset( $options[$k] );
			}
		}

		return $options;
	} // End maybe_override_woo_options()

	/**
	 * Attempt to unregiter the Subscribe & Connect widget.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function maybe_unregister_widget () {
		$settings = $this->get_settings();

		if ( isset( $settings['display']['disable_theme_sc'] ) && true == $settings['display']['disable_theme_sc'] ) {
			unregister_widget( 'Woo_Subscribe' );
		}
	} // End maybe_unregister_widget()
} // End Class
?>