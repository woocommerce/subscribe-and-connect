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
	 * The hook token for the importer screen.
	 * @access  private
	 * @var     string
	 * @since   1.0.0
	 */
	private $_importer_screen_hook;

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

		$this->_importer_screen_hook = add_submenu_page( 'tools.php', __( 'Subscribe & Connect WooFramework Importer', 'subscribe-and-connect' ), __( 'Subscribe & Connect Importer', 'subscribe-and-connect' ), 'manage_options', 'subscribe-and-connect-wf-importer', array( $this, 'importer_screen' ) );
		remove_submenu_page( 'tools.php', 'subscribe-and-connect-wf-importer' );

		add_action( 'load-' . $this->_importer_screen_hook, array( $this, 'maybe_process_imports' ) );
		add_action( 'admin_notices', array( $this, 'importer_admin_notices' ) );
		add_action( 'admin_notices', array( $this, 'maybe_display_importer_notice' ) );

		// Process the 'Dismiss' link, if valid.
		add_action( 'admin_init', array( $this, 'maybe_process_dismiss_link' ) );
	} // End register_settings_screen()

	/**
	 * If the nonce is valid and the action is "icons-for-features-dismiss", process the dismissal.
	 * @access  public
	 * @since   1.2.1
	 * @return  void
	 */
	public function maybe_process_dismiss_link () {
		if ( isset( $_GET['action'] ) && ( 'subscribe-and-connect-dismiss' == $_GET['action'] ) && isset( $_GET['nonce'] ) && check_admin_referer( 'subscribe-and-connect-dismiss', 'nonce' ) ) {
			update_option( 'subscribe_and_connect_dismiss_activation_notice', true );

			$redirect_url = remove_query_arg( 'action', remove_query_arg( 'nonce', $_SERVER['REQUEST_URI'] ) );

			wp_safe_redirect( esc_url( $redirect_url ) );
			exit;
		}
	} // End maybe_process_dismiss_link()

	/**
	 * Process the form on the importer screen.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function maybe_process_imports () {
		if ( ! isset( $_POST ) || 0 >= count( $_POST ) ) return;
		check_admin_referer( 'subscribe-and-connect-importer', 'subscribe-and-connect-importer' );
		if ( ! current_user_can( 'manage_options' ) ) wp_die( __( 'Cheatin&#8217; uh?' ) );

		global $subscribe_and_connect;
		$subscribe_and_connect->setup_settings();
		$settings = $subscribe_and_connect->get_settings();

		$key_transforms = Subscribe_And_Connect_Utils::get_wf_to_sc_key_transforms();
		$status = 'false';

		$general = $settings['general'];
		$connect = $settings['connect'];
		$display = $settings['display'];

		$keys_to_transform = (array)$_POST['sc_fields_to_import'];

		// Add networks.
		$networks = array();
		if ( 0 < count( $keys_to_transform ) ) {
			foreach ( $keys_to_transform as $k => $v ) {
				if ( ! in_array( $v, $key_transforms ) ) {
					$key = str_replace( 'woo_connect_', '', $v );
					if ( 'googleplus' == $key ) $key = 'google_plus'; // One caveat. :)
					$networks[$key] = $v;
				}
			}
		}

		$key_transforms = array_merge( $key_transforms, $networks );

		if ( 0 < count( $key_transforms ) ) {
			foreach ( $key_transforms as $k => $v ) {
				// Check if the setting is in the first section.
				if ( isset( $general[$k] ) ) $general[$k] = get_option( $v );

				// Check if the setting is in the second section.
				if ( isset( $connect[$k] ) ) $connect[$k] = get_option( $v );

				// Check if the setting is in the networks sub-section.
				if ( isset( $connect['networks'][$k] ) ) $connect['networks'][$k]['url'] = esc_url( get_option( $v ) );

				// Check if the setting is in the third section.
				if ( isset( $display[$k] ) ) $display[$k] = get_option( $v );
			}
		}

		// Fix a quick caveat, with a boolean value.
		if ( isset( $connect['rss'] ) ) {
			if ( 'false' == $connect['rss'] ) {
				$connect['rss'] = false;
			} else {
				$connect['rss'] = true;
			}
		}

		$statuses = array();
		$statuses['general'] = update_option( 'subscribe-and-connect-subscribe', $general );
		$statuses['connect'] = update_option( 'subscribe-and-connect-connect', $connect );
		$statuses['display'] = update_option( 'subscribe-and-connect-integration', $display );

		if ( ! in_array( false, $statuses ) ) {
			$status = 'true';
			update_option( 'subscribe_and_connect_importer_has_run', true );
		}

		wp_safe_redirect( esc_url( add_query_arg( 'updated', urlencode( $status ), add_query_arg( 'page', 'subscribe-and-connect-wf-importer', admin_url( 'tools.php' ) ) ) ) );
		exit;
	} // End maybe_process_imports()

	/**
	 * If there are existing keys, add a notice directing the administrator to run the importer.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function maybe_display_importer_notice () {
		if ( isset( $_GET['page'] ) && 'subscribe-and-connect-wf-importer' == $_GET['page'] ) return;
		if ( true == get_option( 'subscribe_and_connect_dismiss_activation_notice', false ) ) return; // Don't show the message if the user dismissed it.
		if ( true == get_option( 'subscribe_and_connect_importer_has_run', false ) ) return;

		$keys_to_check = array_keys( Subscribe_And_Connect_Utils::get_wf_key_labels() );
		$keys_to_check = Subscribe_And_Connect_Utils::filter_existing_keys( $keys_to_check );

		$class = '';
		$message = '';

		if ( 0 < count( $keys_to_check ) ) {
			$class = 'updated';
			$message = __( '%sSubscribe & Connect%s has detected existing information, from a WooThemes theme you have used. %sClick here%s to transfer this information.', 'subscribe-and-connect' );
			$message = sprintf( $message, '<strong>', '</strong>', '<a href="' . esc_attr( admin_url( 'tools.php?page=subscribe-and-connect-wf-importer' ) ) . '">', '</a>' );
		}

		$dismiss_url = add_query_arg( 'action', 'subscribe-and-connect-dismiss', add_query_arg( 'nonce', wp_create_nonce( 'subscribe-and-connect-dismiss' ) ) );
		if ( '' != $message ) echo '<div class="' . esc_attr( $class ) . ' fade"><p class="alignleft">' . $message . '</p><p class="alignright"><a href="' . esc_url( $dismiss_url ) . '">' . __( 'Dismiss', 'subscribe-and-connect' ) . '</a></p><div class="clear"></div></div>' . "\n";
	} // End maybe_display_importer_notice()

	/**
	 * The importer screen admin notices.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function importer_admin_notices () {
		if ( ! isset( $_GET['page'] ) || 'subscribe-and-connect-wf-importer' != $_GET['page'] ) return;
		if ( ! isset( $_GET['updated'] ) ) return;

		$class = '';
		$message = '';

		switch ( $_GET['updated'] ) {
			case 'true':
				$class = 'updated';
				$message = __( 'The importer has run successfully. You\'re good to go!', 'subscribe-and-connect' );
			break;

			case 'false':
				$class = 'error';
				$message = __( 'There was an error running the importer. Please try again.', 'subscribe-and-connect' );
			break;

			default:
			break;
		}

		if ( '' != $message ) echo '<div class="' . esc_attr( $class ) . ' fade"><p>' . esc_html( $message ) . '</p></div>' . "\n";
	} // End importer_admin_notices()

	/**
	 * The importer screen markup.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function importer_screen () {
		global $subscribe_and_connect;
		$key_transforms = Subscribe_And_Connect_Utils::get_wf_to_sc_key_transforms();
		$settings = $subscribe_and_connect->get_settings();

		// Prepare an array of keys to check for.
		$keys = array();

		// Apply the key transforms detected.
		if ( 0 < count( $key_transforms ) ) {
			$keys = array_merge( $keys, array_values( $key_transforms ) );
		}

		// Add any networks to be checked.
		if ( isset( $settings['connect']['networks'] ) && 0 < count( $settings['connect']['networks'] ) ) {
			$prefix = 'woo_connect_';
			if ( isset( $settings['connect']['networks'] ) && 0 < count( $settings['connect']['networks'] ) ) {
				foreach ( $settings['connect']['networks'] as $k => $v ) {
					if ( ! in_array( $prefix . $k, $keys ) ) {
						if ( 'google_plus' == $k ) $k = 'googleplus'; // A small caveat to match how the option looks in the WooFramework.
						$keys[] = $prefix . $k;
					}
				}
			}
		}

		$keys = Subscribe_And_Connect_Utils::filter_existing_keys( $keys );
?>
<div class="wrap">
<?php screen_icon(); ?>
<h2><?php _e( 'Subscribe & Connect WooFramework Importer', 'subscribe-and-connect' ); ?></h2>
<form action="" method="post">
<?php wp_nonce_field( 'subscribe-and-connect-importer', 'subscribe-and-connect-importer' ); ?>
<?php
do_action( 'subscribe_and_connect_wf_importer_before' );

if ( 0 >= count( $keys ) ) {
	_e( 'No existing Subscribe & Connect data, from your WooThemes theme, was found. You\'re good to go!', 'subscribe-and-connect' );
} else {
	$key_labels = Subscribe_And_Connect_Utils::get_wf_key_labels();
?>
<table class="wp-list-table widefat fixed" style="max-width: 500px;">
<tbody>
<?php
	$counter = 0;
	foreach ( $keys as $k => $v ) {
		$counter++;
		$class = 'alternate';
		if ( 0 == $counter % 2 ) $class = '';
		$label = $k;
		if ( isset( $key_labels[$k] ) && '' != $key_labels[$k] ) $label = $key_labels[$k];
?>
	<tr valign="top" class="<?php echo esc_attr( $class ); ?>">
		<th scope="row" class="check-column"><input type="checkbox" value="<?php echo esc_attr( $k ); ?>" name="sc_fields_to_import[]" /></td>
		<td><?php echo esc_html( $label ) . '<br /><code><small>(' . esc_attr( $k ) . ')</small></code>'; ?></td>
		<td><?php echo esc_html( $v ); ?></td>
	</tr>
<?php
	}
?>
</tbody>
<thead>
	<tr>
		<th scope="col" id="cb" class="manage-column column-cb check-column" style=""><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></th>
		<th><?php _e( 'Title', 'subscribe-and-connect' ); ?></th>
		<th><?php _e( 'Stored Value', 'subscribe-and-connect' ); ?></th>
	</tr>
</thead>
</table>
<?php
submit_button( __( 'Import Settings', 'subscribe-and-connect' ) );
}

do_action( 'subscribe_and_connect_wf_importer_after' );
?>
</form>
</div><!--/.wrap-->
<?php

	} // End importer_screen()

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
		wp_register_script( $this->_token . '-admin', esc_url( $this->_plugin_url . 'assets/js/admin.min.js' ), array( 'jquery' ), $subscribe_and_connect->version );
		wp_register_script( $this->_token . '-sortables', esc_url( $this->_plugin_url . 'assets/js/sortables.min.js' ), array( 'jquery', 'jquery-ui-sortable' ), $subscribe_and_connect->version );
		wp_register_script( $this->_token . '-uploaders', esc_url( $this->_plugin_url . 'assets/js/uploaders.min.js' ), array( 'jquery' ), $subscribe_and_connect->version );
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