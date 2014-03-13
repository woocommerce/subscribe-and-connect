<?php
class Subscribe_And_Connect_Admin {
	/**
	 * The hook token for the settings screen.
	 * @access  private
	 * @var     string
	 * @since   1.0.0
	 */
	private $_hook;

	/**
	 * The token used to encapsulate this plugin.
	 * @access  private
	 * @var     string
	 * @since   1.0.0
	 */
	private $_token;

	/**
	 * The URL to the plugin's folder.
	 * @access  private
	 * @var     string
	 * @since   1.0.0
	 */
	private $_plugin_url;

	/**
	 * The path to the plugin's folder.
	 * @access  private
	 * @var     string
	 * @since   1.0.0
	 */
	private $_plugin_path;

	/**
	 * An array of settings objects for the tabs.
	 * @access  public
	 * @var     object
	 * @since   1.0.0
	 */
	public $settings_objs;

	/**
	 * Constructor.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct ( $file ) {
		$this->_token 			= 'subscribe-and-connect';
		$this->_plugin_url 		= plugin_dir_url( $file );
		$this->_plugin_path 	= plugin_dir_path( $file );
		$this->settings_objs 	= array();

		add_action( 'admin_menu', array( $this, 'register_settings_screen' ) );

		// Register necessary scripts and styles, to enable others to enqueue them at will as well.
		add_action( 'admin_init', array( $this, 'register_enqueues' ) );
	} // End __construct()

	/**
	 * Generic getter for private data.
	 * @access  public
	 * @since   1.0.0
	 * @param   string $key The key to retrieve.
	 * @return  string      The information stored.
	 */
	public function __get ( $key ) {
		switch ( $key ) {
			case 'plugin_url':
				return $this->_plugin_url;
			break;
			case 'plugin_path':
				return $this->_plugin_path;
			break;
			default:
			break;
		}
	} // End __get()

	/**
	 * Register the settings screen within the WordPress admin.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function register_settings_screen () {
		$this->_hook = add_options_page( __( 'Subscribe & Connect', 'subscribe-and-connect' ), __( 'Subscribe & Connect', 'subscribe-and-connect' ), 'manage_options', $this->_token, array( $this, 'settings_screen' ) );

		// Enqueue our registered scripts and styles on our own admin screen by default.
		add_action( 'admin_print_scripts-' . $this->_hook, array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_print_styles-' . $this->_hook, array( $this, 'enqueue_styles' ) );
	} // End register_settings_screen()

	/**
	 * The settings screen markup.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function settings_screen () {
		global $subscribe_and_connect;
		$tabs = $this->_get_settings_tabs();
		$current_tab = $this->_get_current_tab();
?>
<div class="wrap">
<?php screen_icon(); ?>
<h2 class="nav-tab-wrapper">
<?php
echo $this->get_settings_tabs_html( $tabs, $current_tab );
do_action( 'subscribe_and_connect_settings_tabs' );
?>
</h2>
<form action="options.php" method="post">
<?php
if ( isset( $subscribe_and_connect->settings_objs[$current_tab] ) && is_object( $subscribe_and_connect->settings_objs[$current_tab] ) ) {
	// $subscribe_and_connect->settings_objs[$current_tab]->settings_errors();
	$subscribe_and_connect->settings_objs[$current_tab]->settings_screen();
}

do_action( 'subscribe_and_connect_settings_tabs_' . $current_tab );

submit_button();
?>
</form>
</div><!--/.wrap-->
<?php
	} // End settings_screen()

	/**
	 * Generate an array of admin section tabs.
	 * @access  private
	 * @since   1.0.0
	 * @return  array Tab data with key, and a value of array( 'name', 'callback' )
	 */
	private function _get_settings_tabs () {
		$tabs = array(
				'general' 		=> __( 'General', 'subscribe-and-connect' ),
				'connect' 		=> __( 'Connections', 'subscribe-and-connect' ),
				'display'	 	=> __( 'Display', 'subscribe-and-connect' )
				);
		return (array)apply_filters( 'subscribe_and_connect_get_settings_tabs', $tabs );
	} // End _get_settings_tabs()

	/**
	 * Generate HTML markup for the section tabs.
	 * @access  public
	 * @since   1.0.0
	 * @param   array $tabs An array of tabs.
	 * @param   string $current_tab The key of the current tab.
	 * @return  string HTML markup for the settings tabs.
	 */
	public function get_settings_tabs_html ( $tabs = false, $current_tab = false ) {
		if ( ! is_array( $tabs ) ) $tabs = $this->_get_settings_tabs(); // Fail-safe, in case we don't pass tab data.
		if ( false == $current_tab ) $current_tab = $this->_get_current_tab();

		$html = '';
		if ( 0 < count( $tabs ) ) {
			foreach ( $tabs as $k => $v ) {
				$class = 'nav-tab';
				if ( $current_tab == $k ) {
					$class .= ' nav-tab-active';
				}
				$url = add_query_arg( 'tab', $k, add_query_arg( 'page', $this->_token, admin_url( 'options-general.php' ) ) );
				$html .= '<a href="' . esc_url( $url ) . '" class="' . esc_attr( $class ) . '">' . esc_html( $v ) . '</a>';
			}
		}
		return $html;
	} // End get_settings_tabs_html()

	/**
	 * Get the current selected tab key.
	 * @access  private
	 * @since   1.0.0
	 * @param   array $tabs Available tabs.
	 * @return  string Current tab's key, or a default value.
	 */
	private function _get_current_tab ( $tabs = false ) {
		if ( ! is_array( $tabs ) ) $tabs = $this->_get_settings_tabs(); // Fail-safe, in case we don't pass tab data.
		if ( isset( $_GET['tab'] ) && in_array( $_GET['tab'], array_keys( $tabs ) ) )
			$current_tab = esc_attr( $_GET['tab'] );
		else
			$current_tab = 'general';

		return $current_tab;
	} // End _get_current_tab()

	/**
	 * Register scripts and styles, preparing for enqueue.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function register_enqueues () {
		global $subscribe_and_connect;
		wp_register_script( $this->_token . '-admin', esc_url( $this->_plugin_url . 'assets/js/admin.js' ), array( 'jquery' ), $subscribe_and_connect->version );
		wp_register_script( $this->_token . '-sortables', esc_url( $this->_plugin_url . 'assets/js/sortables.js' ), array( 'jquery', 'jquery-ui-sortable' ), $subscribe_and_connect->version );
		wp_register_script( $this->_token . '-uploaders', esc_url( $this->_plugin_url . 'assets/js/uploaders.js' ), array( 'jquery' ), $subscribe_and_connect->version );
		wp_register_style( $this->_token . '-settings-api',  esc_url( $this->_plugin_url . 'assets/css/settings.css' ), '', $subscribe_and_connect->version );
	} // End register_enqueues()

	/**
	 * Enqueue JavaScripts for the administration screen.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function enqueue_scripts () {
		wp_enqueue_media();
		wp_enqueue_script( $this->_token . '-admin' );
		wp_enqueue_script( $this->_token . '-uploaders' );
		wp_enqueue_script( $this->_token . '-sortables' );
	} // End enqueue_scripts()

	/**
	 * Enqueue styles for the administration screen.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function enqueue_styles () {
		wp_enqueue_style( $this->_token . '-settings-api' );
	} // End enqueue_styles()
} // End Class
?>