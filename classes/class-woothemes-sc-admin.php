<?php
class Woothemes_SC_Admin {
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
	 * Constructor.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct () {
		$this->_token = 'woothemes-sc';
		add_action( 'admin_menu', array( $this, 'register_settings_screen' ) );
	} // End __construct()

	/**
	 * Register the settings screen within the WordPress admin.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function register_settings_screen () {
		$this->_hook = add_options_page( __( 'Subscribe & Connect', 'woothemes-sc' ), __( 'Subscribe & Connect', 'woothemes-sc' ), 'manage_options', $this->_token, array( $this, 'settings_screen' ) );
	} // End register_settings_screen()

	/**
	 * The settings screen markup.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function settings_screen () {
?>
<div class="wrap">
<?php screen_icon(); ?>
<h2 class="nav-tab-wrapper">
<?php echo $this->_settings_tabs(); ?>
</h2>
</div><!--/.wrap-->
<?php
	} // End settings_screen()

	/**
	 * Generate HTML markup for the section tabs.
	 * @access  private
	 * @since   1.0.0
	 * @return  string HTML markup for the settings tabs.
	 */
	private function _settings_tabs () {
		$tabs = array(
				'subscribe' => __( 'Subscribe', 'woothemes-sc' ),
				'connect' => __( 'Connect', 'woothemes-sc' ),
				'integration' => __( 'Integration', 'woothemes-sc' )
				);
		$tabs = (array)apply_filters( 'woothemes_sc_settings_tabs', $tabs );

		if ( isset( $_GET['tab'] ) && in_array( $_GET['tab'], array_keys( $tabs ) ) ) {
			$current_tab = esc_attr( $_GET['tab'] );
		} else {
			$current_tab = 'subscribe';
		}

		$html = '';
		if ( 0 < count( $tabs ) ) {
			foreach ( $tabs as $k => $v ) {
				$class = 'nav-tab';
				if ( $current_tab == $k ) {
					$class .= ' nav-tab-active';
				}
				$url = add_query_arg( 'tab', $k, add_query_arg( 'page', $this->_token, admin_url( 'options-general.php' ) ) );
				$html .= '<a href="' . esc_url( $url ) . '" class="' . esc_attr( $class ) . '">' . $v . '</a>';
			}
		}
		return $html;
	} // End _settings_tabs()
} // End Class
?>