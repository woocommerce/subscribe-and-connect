<?php
class Woothemes_SC {
	public $admin;
	public $frontend;
	/**
	 * Constructor.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct ( $file ) {
		if ( is_admin() ) {
			require_once( 'class-woothemes-sc-admin.php' );
			$this->admin = new Woothemes_SC_Admin( $file );
		} else {
			require_once( 'class-woothemes-sc-frontend.php' );
			$this->frontend = new Woothemes_SC_Frontend( $file );
		}
	} // End __construct()
} // End Class
?>