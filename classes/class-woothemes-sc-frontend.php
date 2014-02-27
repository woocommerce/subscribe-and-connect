<?php
class Woothemes_SC_Frontend {
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
	 * Constructor.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct ( $file ) {
		$this->_plugin_url = plugin_dir_url( $file );
		$this->_plugin_path = plugin_dir_path( $file );

		add_action( 'wp_enqueue_scripts', array( $this, 'register_frontend_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_styles' ) );
		add_action( 'wp_footer', array( $this, 'maybe_load_theme_stylesheets' ) );
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
	 * Register frontend stylesheets.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function register_frontend_styles () {
		global $woothemes_sc;
		wp_register_style( 'woothemes-sc', $this->__get( 'plugin_url' ) . 'assets/css/frontend.css', '', $woothemes_sc->version );
	} // End register_frontend_styles()

	/**
	 * Enqueue frontend stylesheets.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function enqueue_frontend_styles () {
		wp_enqueue_style( 'woothemes-sc' );
	} // End enqueue_frontend_styles()

	/**
	 * Make sure the desired theme is valid. If not, return 'default'.
	 * @access  public
	 * @since   1.0.0
	 * @param   array $args  Arguments for the current theme.
	 * @return  string       The slug of the theme, or 'default'.
	 */
	public function is_valid_theme ( $key ) {
		$response = false;
		if ( in_array( $key, array_keys( Woothemes_SC_Utils::get_icon_themes() ) ) ) {
			$response = true;
		}
		return $response;
	} // End is_valid_theme()

	/**
	 * Make sure the desired theme is valid. If not, return 'default'.
	 * @access  public
	 * @since   1.0.0
	 * @param   array $args  Arguments for the current theme.
	 * @return  string       The slug of the theme, or 'default'.
	 */
	public function get_sanitized_theme_key ( $args ) {
		$theme = 'icons';
		if ( in_array( $key, array_keys( Woothemes_SC_Utils::get_icon_themes() ) ) ) {
			$theme = esc_attr( strtolower( $key ) );
		}
		return $theme;
	} // End get_sanitized_theme_key()

	/**
	 * Get data for a specified theme.
	 * @access  public
	 * @since   1.0.0
	 * @param   array $args  Arguments for the current theme.
	 * @return  string       The slug of the theme, or 'default'.
	 */
	public function get_theme_data ( $key ) {
		$theme = array( 'name' => 'icons', 'stylesheet' => '' );
		$available_themes = Woothemes_SC_Utils::get_icon_themes();
		if ( in_array( $key, array_keys( $available_themes ) ) ) {
			$theme = $available_themes[esc_attr( $key )];
		}
		return $theme;
	} // End get_theme_data()

	/**
	 * Maybe load the stylesheet for the theme in use.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function maybe_load_theme_stylesheets () {
		global $woothemes_sc;
		$woothemes_sc->setup_settings();
		$settings = $woothemes_sc->get_settings();
		$theme_data = $this->get_theme_data( $settings['display']['theme'] );
		if ( isset( $theme_data['stylesheet'] ) && ( '' != $theme_data['stylesheet'] ) ) {
			wp_enqueue_style( 'woothemes-sc-theme-' . esc_attr( $settings['display']['theme'] ), esc_url( $theme_data['stylesheet'] ) );
		}
	} // End maybe_load_theme_stylesheets()
} // End Class
?>