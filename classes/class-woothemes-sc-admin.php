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
		$tabs = $this->_get_settings_tabs();
		$current_tab = $this->_get_current_tab();
?>
<div class="wrap">
<?php screen_icon(); ?>
<h2 class="nav-tab-wrapper">
<?php echo $this->get_settings_tabs_html( $tabs ); ?>
</h2>
<?php
switch ( $_GET['tab'] ) {
	case 'subscribe':
	break;

	case 'connect':
	break;

	case 'integration':
	break;
}
// Cater for custom tabs, added via the filter.
if ( in_array( $current_tab, array_keys( $tabs ) ) && isset( $tabs[$current_tab]['callback'] ) && is_callable( $tabs[$current_tab]['callback'] ) ) {
call_user_func( $tabs[$current_tab]['callback'] );
}
?>
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
				'subscribe' => array( 'name' => __( 'Subscribe', 'woothemes-sc' ) ),
				'connect' => array( 'name' => __( 'Connect', 'woothemes-sc' ) ),
				'integration' => array( 'name' => __( 'Integration', 'woothemes-sc' ) )
				);
		return (array)apply_filters( 'woothemes_sc_get_settings_tabs', $tabs );
	} // End _get_settings_tabs()

	/**
	 * Generate HTML markup for the section tabs.
	 * @access  public
	 * @since   1.0.0
	 * @return  string HTML markup for the settings tabs.
	 */
	public function get_settings_tabs_html ( $tabs = false ) {
		if ( ! is_array( $tabs ) ) $tabs = $this->_get_settings_tabs(); // Fail-safe, in case we don't pass tab data.

		$current_tab = $this->_get_current_tab();

		$html = '';
		if ( 0 < count( $tabs ) ) {
			foreach ( $tabs as $k => $v ) {
				if ( ! is_array( $v ) || ! isset( $v['name'] ) ) continue; // Sanity check.

				$class = 'nav-tab';
				if ( $current_tab == $k ) {
					$class .= ' nav-tab-active';
				}
				$url = add_query_arg( 'tab', $k, add_query_arg( 'page', $this->_token, admin_url( 'options-general.php' ) ) );
				$html .= '<a href="' . esc_url( $url ) . '" class="' . esc_attr( $class ) . '">' . $v['name'] . '</a>';
			}
		}
		return $html;
	} // End get_settings_tabs_html()

	/**
	 * Get the current selected tab key.
	 * @access  private
	 * @since   1.0.0
	 * @return  string Current tab's key, or a default value.
	 */
	private function _get_current_tab () {
		if ( isset( $_GET['tab'] ) && in_array( $_GET['tab'], array_keys( $tabs ) ) )
			$current_tab = esc_attr( $_GET['tab'] );
		else
			$current_tab = 'subscribe';

		return $current_tab;
	} // End _get_current_tab()
} // End Class
?>