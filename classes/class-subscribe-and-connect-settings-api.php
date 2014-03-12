<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Subscribe & Connect Settings API Class
 *
 * A settings API (wrapping the WordPress Settings API).
 *
 * @package WordPress
 * @subpackage Subscribe_And_Connect
 * @category Settings
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * public $token
 * public $name
 * private $_settings
 * public $sections
 * public $fields
 * private $_errors
 *
 * private $_has_range
 * private $_has_imageselector
 *
 * - __construct()
 * - setup_settings()
 * - init_sections()
 * - init_fields()
 * - create_sections()
 * - create_fields()
 * - determine_method()
 * - parse_fields()
 * - settings_screen()
 * - get_settings()
 * - settings_fields()
 * - settings_errors()
 * - settings_description()
 * - form_field_text()
 * - form_field_checkbox()
 * - form_field_textarea()
 * - form_field_select()
 * - form_field_radio()
 * - form_field_multicheck()
 * - form_field_network()
 * - form_field_info()
 * - validate_fields()
 * - validate_field_text()
 * - validate_field_checkbox()
 * - validate_field_multicheck()
 * - validate_field_url()
 * - check_field_text()
 * - add_error()
 * - parse_errors()
 * - get_array_field_types()
 */
class Subscribe_And_Connect_Settings_API {
	public $token;
	public $name;
	private $_settings;
	public $sections;
	public $fields;
	private $_errors;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct () {
		$this->token = 'subscribe-and-connect';
		$this->name = __( 'Subscribe & Connect', 'subscribe-and-connect' );

		$this->sections = array();
		$this->fields = array();
		$this->remaining_fields = array();
		$this->_errors = array();

		add_action( 'admin_print_styles', array( $this, 'enqueue_styles' ) );
	} // End __construct()

	/**
	 * setup_settings function.
	 *
	 * @access public
	 * @return void
	 */
	public function setup_settings () {
		$this->init_sections();
		$this->init_fields();
		$this->get_settings();
		if ( is_admin() ) $this->settings_fields(); // Run this only in the admin.
	} // End setup_settings()

	/**
	 * init_sections function.
	 *
	 * @access public
	 * @return void
	 */
	public function init_sections () {
		// Override this function in your class and assign the array of sections to $this->sections.
		_e( 'Override init_sections() in your class.', 'subscribe-and-connect' );
	} // End init_sections()

	/**
	 * init_fields function.
	 *
	 * @access public
	 * @return void
	 */
	public function init_fields () {
		// Override this function in your class and assign the array of sections to $this->fields.
		_e( 'Override init_fields() in your class.', 'subscribe-and-connect' );
	} // End init_fields()

	/**
	 * create_sections function.
	 *
	 * @access public
	 * @return void
	 */
	public function create_sections () {
		// if ( ! function_exists( 'add_settings_section' ) ) return;
		if ( 0 < count( $this->sections ) ) {
			foreach ( $this->sections as $k => $v ) {
				add_settings_section( $k, $v['name'], array( $this, 'section_description' ), $this->token );
			}
		}
	} // End create_sections()

	/**
	 * create_fields function.
	 *
	 * @access public
	 * @return void
	 */
	public function create_fields () {
		// if ( ! function_exists( 'add_settings_field' ) ) return;
		if ( 0 < count( $this->sections ) ) {
			foreach ( $this->fields as $k => $v ) {
				$method = $this->determine_method( $v, 'form' );
				$name = $v['name'];
				if ( 'info' == $v['type']/* || 'network' == $v['type']*/ ) { $name = ''; }
				add_settings_field( $k, $name, $method, $this->token, $v['section'], array( 'key' => $k, 'data' => $v ) );
			}
		}
	} // End create_fields()

	/**
	 * determine_method function.
	 *
	 * @access protected
	 * @param array $data
	 * @return array or string
	 */
	protected function determine_method ( $data, $type = 'form' ) {
		$method = '';

		if ( ! in_array( $type, array( 'form', 'validate', 'check' ) ) ) { return; }

		// Check for custom functions.
		if ( isset( $data[$type] ) ) {
			if ( function_exists( $data[$type] ) ) {
				$method = $data[$type];
			}

			if ( '' == $method && method_exists( $this, $data[$type] ) ) {
				if ( $type == 'form' ) {
					$method = array( $this, $data[$type] );
				} else {
					$method = $data[$type];
				}
			}
		}

		if ( '' == $method && method_exists ( $this, $type . '_field_' . $data['type'] ) ) {
			if ( $type == 'form' ) {
				$method = array( $this, $type . '_field_' . $data['type'] );
			} else {
				$method = $type . '_field_' . $data['type'];
			}
		}

		if ( '' == $method ) {
			if ( $type == 'form' ) {
				$method = array( $this, $type . '_field_text' );
			} else {
				$method = $type . '_field_text';
			}
		}

		return $method;
	} // End determine_method()

	/**
	 * settings_screen function.
	 *
	 * @access public
	 * @return void
	 */
	public function settings_screen () {
		settings_fields( $this->token );
		do_settings_sections( $this->token );
	} // End settings_screen()

	/**
	 * get_settings function.
	 *
	 * @access public
	 * @return void
	 */
	public function get_settings () {
		if ( ! is_array( $this->_settings ) ) {
			$this->_settings = get_option( $this->token, array() );
		}

		foreach ( $this->fields as $k => $v ) {
			if ( ! isset( $this->_settings[$k] ) && isset( $v['default'] ) ) {
				$this->_settings[$k] = $v['default'];
			}
			if ( $v['type'] == 'checkbox' && $this->_settings[$k] != true ) {
				$this->_settings[$k] = 0;
			}
		}

		return $this->_settings;
	} // End get_settings()

	/**
	 * settings_fields function.
	 *
	 * @access public
	 * @return void
	 */
	public function settings_fields () {
		// if ( ! function_exists( 'register_setting' ) ) return;
		$this->create_sections();
		$this->create_fields();
		register_setting( $this->token, $this->token, array( $this, 'validate_fields' ) );
	} // End settings_fields()

	/**
	 * settings_errors function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function settings_errors () {
		echo settings_errors( $this->token . '-errors' );
	} // End settings_errors()

	/**
	 * section_description function.
	 *
	 * @access public
	 * @return void
	 */
	public function section_description ( $section ) {
		if ( isset( $this->sections[$section['id']]['description'] ) ) {
			echo wpautop( esc_html( $this->sections[$section['id']]['description'] ) );
		}
	} // End section_description_main()

	/**
	 * form_field_text function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_text ( $args ) {
		do_action( 'subscribe_and_connect_field_text_before', $args );
		$options = $this->get_settings();

		echo '<input id="' . esc_attr( $args['key'] ) . '" name="' . $this->token . '[' . esc_attr( $args['key'] ) . ']" size="40" type="text" value="' . esc_attr( $options[$args['key']] ) . '" />' . "\n";
		if ( isset( $args['data']['description'] ) ) {
			echo '<p class="description">' . wp_kses_post( $args['data']['description'] ) . '</p>' . "\n";
		}
		do_action( 'subscribe_and_connect_field_text_after', $args );
	} // End form_field_text()

	/**
	 * A hidden input field.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_hidden ( $args ) {
		do_action( 'subscribe_and_connect_field_hidden_before', $args );
		$options = $this->get_settings();

		echo '<input id="' . esc_attr( $args['key'] ) . '" name="' . $this->token . '[' . esc_attr( $args['key'] ) . ']" size="40" type="hidden" value="' . esc_attr( $options[$args['key']] ) . '" />' . "\n";
		do_action( 'subscribe_and_connect_field_hidden_after', $args );
	} // End form_field_hidden()

	/**
	 * form_field_checkbox function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_checkbox ( $args ) {
		do_action( 'subscribe_and_connect_field_checkbox_before', $args );
		$options = $this->get_settings();

		$has_description = false;
		if ( isset( $args['data']['description'] ) ) {
			$has_description = true;
			echo '<label for="' . $this->token . '[' . esc_attr( $args['key'] ) . ']">' . "\n";
		}
		echo '<input id="' . $args['key'] . '" name="' . $this->token . '[' . esc_attr( $args['key'] ) . ']" type="checkbox" value="1"' . checked( esc_attr( $options[$args['key']] ), '1', false ) . ' />' . "\n";
		if ( $has_description ) {
			echo wp_kses_post( $args['data']['description'] ) . '</label>' . "\n";
		}
		do_action( 'subscribe_and_connect_field_checkbox_after', $args );
	} // End form_field_text()

	/**
	 * form_field_textarea function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_textarea ( $args ) {
		do_action( 'subscribe_and_connect_field_textarea_before', $args );
		$options = $this->get_settings();

		echo '<textarea id="' . esc_attr( $args['key'] ) . '" name="' . $this->token . '[' . esc_attr( $args['key'] ) . ']" cols="42" rows="5">' . esc_html( $options[$args['key']] ) . '</textarea>' . "\n";
		if ( isset( $args['data']['description'] ) ) {
			echo '<p class="description">' . wp_kses_post( $args['data']['description'] ) . '</p>' . "\n";
		}
		do_action( 'subscribe_and_connect_field_textarea_after', $args );
	} // End form_field_textarea()

	/**
	 * form_field_select function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_select ( $args ) {
		do_action( 'subscribe_and_connect_field_select_before', $args );
		$options = $this->get_settings();

		if ( isset( $args['data']['options'] ) && ( count( (array)$args['data']['options'] ) > 0 ) ) {
			$html = '';
			$html .= '<select id="' . esc_attr( $args['key'] ) . '" name="' . esc_attr( $this->token ) . '[' . esc_attr( $args['key'] ) . ']">' . "\n";
				foreach ( $args['data']['options'] as $k => $v ) {
					$html .= '<option value="' . esc_attr( $k ) . '"' . selected( esc_attr( $options[$args['key']] ), $k, false ) . '>' . $v . '</option>' . "\n";
				}
			$html .= '</select>' . "\n";
			echo $html;

			if ( isset( $args['data']['description'] ) ) {
				echo '<p class="description">' . wp_kses_post( $args['data']['description'] ) . '</p>' . "\n";
			}
		}
		do_action( 'subscribe_and_connect_field_select_after', $args );
	} // End form_field_select()

	/**
	 * form_field_radio function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_radio ( $args ) {
		do_action( 'subscribe_and_connect_field_radio_before', $args );
		$options = $this->get_settings();

		if ( isset( $args['data']['options'] ) && ( count( (array)$args['data']['options'] ) > 0 ) ) {
			$html = '';
			foreach ( $args['data']['options'] as $k => $v ) {
				$html .= '<p><label><input type="radio" name="' . $this->token . '[' . esc_attr( $args['key'] ) . ']" value="' . esc_attr( $k ) . '"' . checked( esc_attr( $options[$args['key']] ), $k, false ) . ' /> ' . $v . '<br />' . "\n";
			}
			echo $html;

			if ( isset( $args['data']['description'] ) ) {
				echo '<span class="description">' . wp_kses_post( $args['data']['description'] ) . '</span></label></p>' . "\n";
			}
		}
		do_action( 'subscribe_and_connect_field_radio_after', $args );
	} // End form_field_radio()

	/**
	 * form_field_multicheck function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_multicheck ( $args ) {
		do_action( 'subscribe_and_connect_field_multicheck_before', $args );
		$options = $this->get_settings();

		if ( isset( $args['data']['options'] ) && ( count( (array)$args['data']['options'] ) > 0 ) ) {
			$html = '<div class="multicheck-container" style="height: 100px; overflow-y: auto;">' . "\n";
			foreach ( $args['data']['options'] as $k => $v ) {
				$checked = '';

				if ( in_array( $k, (array)$options[$args['key']] ) ) { $checked = ' checked="checked"'; }
				$html .= '<input type="checkbox" name="' . esc_attr( $this->token ) . '[' . esc_attr( $args['key'] ) . '][]" class="multicheck multicheck-' . esc_attr( $args['key'] ) . '" value="' . esc_attr( $k ) . '"' . $checked . ' /> ' . $v . '<br />' . "\n";
			}
			$html .= '</div>' . "\n";
			echo $html;

			if ( isset( $args['data']['description'] ) ) {
				echo '<p class="description">' . wp_kses_post( $args['data']['description'] ) . '</p>' . "\n";
			}
		}
		do_action( 'subscribe_and_connect_field_multicheck_after', $args );
	} // End form_field_multicheck()

	/**
	 * form_field_network function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_network ( $args ) {
		$options = $this->get_settings();
		$networks = Subscribe_And_Connect_Utils::get_supported_networks();
		$html = '';

		$order = '';
		if ( isset( $options['networks_order'] ) ) {
			$order = $options['networks_order'];
		}

		$networks = Subscribe_And_Connect_Utils::get_networks_in_order( $networks, $order );

		if ( 0 < count( $networks ) ) {
			$html .= '<table class="form-table widefat subscribe-and-connect-network-fields"><thead><tr><th class="title">' . __( 'Social Network', 'woothemes-wc' ) . '</th><th class="title">' . __( 'Your URL', 'woothemes-wc' ) . '</th></tr></thead><tbody>' . "\n";
			foreach ( $networks as $k => $v ) {
				$html .= $this->_single_network_field( $k, $v, $args, $options );
			}
			$html .= '</tbody></table>' . "\n";
		}

		// Used to store the placeholder image temporarily, for use with JavaScript.
		$html .= '<img src="' . esc_url( Subscribe_And_Connect_Utils::get_placeholder_image() ) . '" class="subscribe-and-connect-placeholder-image" style="display: none;" width="0" height="0" />' . "\n";

		echo $html;

		if ( isset( $args['data']['description'] ) ) {
			echo '<p class="description">' . wp_kses_post( $args['data']['description'] ) . '</p>' . "\n";
		}
	} // End form_field_network()

	/**
	 * Output a single instance of a "network" field type.
	 * @access  private
	 * @since   1.0.0
	 * @param   array $data      	Data for the network to be output.
	 * @param   array $args      	Arguments used when generating the field instance.
	 * @param   array $networks  	Supported networks to list.
	 * @param  	int   $i 		 	Itterator.
	 * @param 	bool  $show_remove 	Whether or not to show the "Remove" link.
	 * @return  string           	Formatted HTML.
	 */
	private function _single_network_field ( $key, $value, $args, $options ) {
		$html = '';

		$data = array( 'url' => '', 'image_id' => '' );
		if ( isset( $options[$args['key']][$key] ) ) $data = $options[$args['key']][$key];

		$html .= '<tr id="' . esc_attr( $key ) . '" class="subscribe-and-connect-network-item">' . "\n";
		$html .= '<th class="title">' . "\n";
		$html .= '<label for="' . esc_attr( $this->token ) . '[' . esc_attr( $args['key'] ) . '][' . esc_attr( $key ) . '][url]">' . esc_html( $value ) . '</label>' . "\n";
		$html .= '</th><td class="url">' . "\n";
		$html .= '<input type="text" class="regular-text input-text url" name="' . esc_attr( $this->token ) . '[' . esc_attr( $args['key'] ) . '][' . esc_attr( $key ) . '][url]" placeholder="' . sprintf( __( 'Place your %s URL here', 'subscribe-and-connect' ), esc_attr( $value ) ) . '" value="' . esc_attr( $data['url'] ) . '" />' . "\n";
		$html .= '</td></tr>' . "\n";

		return $html;
	} // End _single_network_field()

	/**
	 * form_field_info function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_info ( $args ) {
		$class = '';
		if ( isset( $args['data']['class'] ) ) {
			$class = ' ' . esc_attr( $args['data']['class'] );
		}
		$html = '<div id="' . $args['key'] . '" class="subscribe-and-connect-info-box' . $class . '">' . "\n";
		if ( isset( $args['data']['name'] ) && ( $args['data']['name'] != '' ) ) {
			$html .= '<h3 class="title">' . esc_html( $args['data']['name'] ) . '</h3>' . "\n";
		}
		if ( isset( $args['data']['description'] ) && ( $args['data']['description'] != '' ) ) {
			$html .= '<p>' . wp_kses_post( $args['data']['description'] ) . '</p>' . "\n";
		}
		$html .= '</div>' . "\n";

		echo $html;
	} // End form_field_info()

	/**
	 * validate_fields function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $input
	 * @uses $this->parse_errors()
	 * @return array $options
	 */
	public function validate_fields ( $input ) {
		$options = $this->get_settings();

		foreach ( $this->fields as $k => $v ) {
			// Make sure checkboxes are present even when false.
			if ( $v['type'] == 'checkbox' && ! isset( $input[$k] ) ) { $input[$k] = false; }

			if ( isset( $input[$k] ) ) {
				// Perform checks on required fields.
				if ( isset( $v['required'] ) && ( true == $v['required'] ) ) {
					if ( in_array( $v['type'], $this->get_array_field_types() ) && ( count( (array) $input[$k] ) <= 0 ) ) {
						$this->add_error( $k, $v );
						continue;
					} else {
						if ( $input[$k] == '' ) {
							$this->add_error( $k, $v );
							continue;
						}
					}
				}

				$value = $input[$k];

				// Check if the field is valid.
				$method = $this->determine_method( $v, 'check' );

				if ( method_exists( $this, $method ) ) {
					$is_valid = $this->$method( $value );
				}

				if ( ! $is_valid ) {
					$this->add_error( $k, $v );
					continue;
				}

				$method = $this->determine_method( $v, 'validate' );

				if ( method_exists( $this, $method ) ) {
					$options[$k] = $this->$method( $value );
				}
			}
		}

		// Parse error messages into the Settings API.
		$this->parse_errors();
		return $options;
	} // End validate_fields()

	/**
	 * validate_field_text function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param string $input
	 * @return string
	 */
	public function validate_field_text ( $input ) {
		return trim( esc_attr( $input ) );
	} // End validate_field_text()

	/**
	 * validate_field_checkbox function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param string $input
	 * @return string
	 */
	public function validate_field_checkbox ( $input ) {
		if ( ! isset( $input ) ) {
			return 0;
		} else {
			return (bool)$input;
		}
	} // End validate_field_checkbox()

	/**
	 * validate_field_multicheck function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param string $input
	 * @return string
	 */
	public function validate_field_multicheck ( $input ) {
		$input = (array) $input;

		$input = array_map( 'esc_attr', $input );

		return $input;
	} // End validate_field_multicheck()

	/**
	 * validate_field_url function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param string $input
	 * @return string
	 */
	public function validate_field_url ( $input ) {
		return trim( esc_url( $input ) );
	} // End validate_field_url()

	/**
	 * validate_field_network function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param string $input
	 * @return string
	 */
	public function validate_field_network ( $input ) {
		$input = (array) $input;

		// TODO

		return $input;
	} // End validate_field_network()

	/**
	 * check_field_text function.
	 * @param  string $input String of the value to be validated.
	 * @since  1.1.0
	 * @return boolean Is the value valid?
	 */
	public function check_field_text ( $input ) {
		$is_valid = true;

		return $is_valid;
	} // End check_field_text()

	/**
	 * add_error function.
	 *
	 * @access protected
	 * @since 1.0.0
	 * @param string $key
	 * @param array $data
	 * @return void
	 */
	protected function add_error ( $key, $data ) {
		if ( isset( $data['error_message'] ) ) {
			$message = $data['error_message'];
		} else {
			$message = sprintf( __( '%s is a required field', 'subscribe-and-connect' ), $data['name'] );
		}
		$this->_errors[$key] = $message;
	} // End add_error()

	/**
	 * If there are error messages logged, parse them.
	 * @access  protected
	 * @since   1.0.0
	 * @return  void
	 */
	protected function parse_errors () {
		if ( count ( $this->_errors ) > 0 ) {
			foreach ( $this->_errors as $k => $v ) {
				add_settings_error( $this->token . '-errors', $k, $v, 'error' );
			}
		} else {
			$message = sprintf( __( '%s settings updated', 'subscribe-and-connect' ), $this->name );
			add_settings_error( $this->token . '-errors', $this->token, $message, 'updated' );
		}
	} // End parse_errors()

	/**
	 * get_array_field_types function.
	 *
	 * @description Return an array of field types expecting an array value returned.
	 * @access protected
	 * @since 1.0.0
	 * @return void
	 */
	protected function get_array_field_types () {
		return array( 'multicheck', 'network' );
	} // End get_array_field_types()

	/**
	 * Load in CSS styles where necessary.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function enqueue_styles () {
		global $subscribe_and_connect;
		wp_enqueue_style( $this->token . '-admin' );

		wp_enqueue_style( 'subscribe-and-connect-settings-api', $subscribe_and_connect->context->__get( 'plugin_url' ) . 'assets/css/settings.css', '', '1.0.0' );
	} // End enqueue_styles()
} // End Class
?>