<?php
class Woothemes_SC {
	/**
	 * Property to contain the version number.
	 * @access  public
	 * @since   1.0.0
	 * @var     object
	 */
	public $version;

	/**
	 * Property to contain the Woothemes_SC_Admin or Woothemes_SC_Frontend object.
	 * @access  public
	 * @since   1.0.0
	 * @var     object
	 */
	public $context;
	/**
	 * Constructor.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct ( $file ) {
		// Load in the utility functions.
		require_once( 'class-woothemes-sc-utils.php' );
		if ( is_admin() ) {
			// Load in the admin functionality.
			require_once( 'class-woothemes-sc-admin.php' );
			$this->context = new Woothemes_SC_Admin( $file );
		} else {
			// Load in the frontend functionality.
			require_once( 'class-woothemes-sc-frontend.php' );
			$this->context = new Woothemes_SC_Frontend( $file );
		}
	} // End __construct()
} // End Class
?>