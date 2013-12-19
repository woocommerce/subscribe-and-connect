<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Subscribe & Connect Settings API Class
 *
 * A settings API (wrapping the WordPress Settings API).
 *
 * @package WordPress
 * @subpackage Woothemes_SC
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
 * - form_field_range()
 * - form_field_images()
 * - form_field_network()
 * - form_field_info()
 * - validate_fields()
 * - validate_field_text()
 * - validate_field_checkbox()
 * - validate_field_multicheck()
 * - validate_field_range()
 * - validate_field_url()
 * - check_field_timestamp()
 * - check_field_text()
 * - add_error()
 * - parse_errors()
 * - get_array_field_types()
 */
class Woothemes_SC_Settings_API {
	public $token;
	public $name;
	private $_settings;
	public $sections;
	public $fields;
	private $_errors;

	private $_has_range;
	private $_has_imageselector;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct () {
		$this->token = 'woothemes-sc';
		$this->name = __( 'Subscribe & Connect', 'woothemes-sc' );

		$this->sections = array();
		$this->fields = array();
		$this->remaining_fields = array();
		$this->_errors = array();

		$this->_has_range = false;
		$this->_has_imageselector = false;
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
		_e( 'Override init_sections() in your class.', 'woothemes-sc' );
	} // End init_sections()

	/**
	 * init_fields function.
	 *
	 * @access public
	 * @return void
	 */
	public function init_fields () {
		// Override this function in your class and assign the array of sections to $this->fields.
		_e( 'Override init_fields() in your class.', 'woothemes-sc' );
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

				// Let the API know that we have a colourpicker field.
				if ( $v['type'] == 'range' && $this->_has_range == false ) { $this->_has_range = true; }
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

			if ( $method == '' && method_exists( $this, $data[$type] ) ) {
				if ( $type == 'form' ) {
					$method = array( $this, $data[$type] );
				} else {
					$method = $data[$type];
				}
			}
		}

		if ( $method == '' && method_exists ( $this, $type . '_field_' . $data['type'] ) ) {
			if ( $type == 'form' ) {
				$method = array( $this, $type . '_field_' . $data['type'] );
			} else {
				$method = $type . '_field_' . $data['type'];
			}
		}

		if ( $method == '' ) {
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
		$options = $this->get_settings();

		echo '<input id="' . esc_attr( $args['key'] ) . '" name="' . $this->token . '[' . esc_attr( $args['key'] ) . ']" size="40" type="text" value="' . esc_attr( $options[$args['key']] ) . '" />' . "\n";
		if ( isset( $args['data']['description'] ) ) {
			echo '<span class="description">' . wp_kses_post( $args['data']['description'] ) . '</span>' . "\n";
		}
	} // End form_field_text()

	/**
	 * form_field_checkbox function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_checkbox ( $args ) {
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
		$options = $this->get_settings();

		echo '<textarea id="' . esc_attr( $args['key'] ) . '" name="' . $this->token . '[' . esc_attr( $args['key'] ) . ']" cols="42" rows="5">' . esc_html( $options[$args['key']] ) . '</textarea>' . "\n";
		if ( isset( $args['data']['description'] ) ) {
			echo '<p><span class="description">' . wp_kses_post( $args['data']['description'] ) . '</span></p>' . "\n";
		}
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
				echo '<p><span class="description">' . wp_kses_post( $args['data']['description'] ) . '</span></p>' . "\n";
			}
		}
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
		$options = $this->get_settings();

		if ( isset( $args['data']['options'] ) && ( count( (array)$args['data']['options'] ) > 0 ) ) {
			$html = '';
			foreach ( $args['data']['options'] as $k => $v ) {
				$html .= '<input type="radio" name="' . $this->token . '[' . esc_attr( $args['key'] ) . ']" value="' . esc_attr( $k ) . '"' . checked( esc_attr( $options[$args['key']] ), $k, false ) . ' /> ' . $v . '<br />' . "\n";
			}
			echo $html;

			if ( isset( $args['data']['description'] ) ) {
				echo '<span class="description">' . wp_kses_post( $args['data']['description'] ) . '</span>' . "\n";
			}
		}
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
				echo '<span class="description">' . wp_kses_post( $args['data']['description'] ) . '</span>' . "\n";
			}
		}
	} // End form_field_multicheck()

	/**
	 * form_field_range function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_range ( $args ) {
		$options = $this->get_settings();

		if ( isset( $args['data']['options'] ) && ( count( (array)$args['data']['options'] ) > 0 ) ) {
			$html = '';
			$html .= '<select id="' . esc_attr( $args['key'] ) . '" name="' . esc_attr( $this->token ) . '[' . esc_attr( $args['key'] ) . ']" class="range-input">' . "\n";
				foreach ( $args['data']['options'] as $k => $v ) {
					$html .= '<option value="' . esc_attr( $k ) . '"' . selected( esc_attr( $options[$args['key']] ), $k, false ) . '>' . $v . '</option>' . "\n";
				}
			$html .= '</select>' . "\n";
			echo $html;

			if ( isset( $args['data']['description'] ) ) {
				echo '<p><span class="description">' . wp_kses_post( $args['data']['description'] ) . '</span></p>' . "\n";
			}
		}
	} // End form_field_range()

	/**
	 * form_field_images function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param array $args
	 * @return void
	 */
	public function form_field_images ( $args ) {
		$options = $this->get_settings();

		if ( isset( $args['data']['options'] ) && ( count( (array)$args['data']['options'] ) > 0 ) ) {
			$html = '';
			foreach ( $args['data']['options'] as $k => $v ) {
				$html .= '<input type="radio" name="' . esc_attr( $this->token ) . '[' . esc_attr( $args['key'] ) . ']" value="' . esc_attr( $k ) . '"' . checked( esc_attr( $options[$args['key']] ), $k, false ) . ' /> ' . $v . '<br />' . "\n";
			}
			echo $html;

			if ( isset( $args['data']['description'] ) ) {
				echo '<span class="description">' . wp_kses_post( $args['data']['description'] ) . '</span>' . "\n";
			}
		}
	} // End form_field_images()

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
		$networks = Woothemes_SC_Utils::get_supported_networks();
		$html = '';

		if ( isset( $options['networks'] ) && is_array( $options['networks'] ) && 0 < count( $options['networks'] ) ) {
			$i = 0;
			foreach ( $options['networks'] as $k => $v ) {
				$this->_single_network_field( array( 'url' => $v['url'], 'network' => $v['network'], 'image' => $v['image'] ), $args, $networks, $i );
				$i++;
			}
			// Add a blank item each time, to cater for new additions without using JavaScript.
			$this->_single_network_field( array( 'url' => '', 'network' => '', 'image' => '' ), $args, $networks, $i, false );
		} else {
			$this->_single_network_field( array( 'url' => '', 'network' => '', 'image' => '' ), $args, $networks, 0, false );
		}

		// Used to store the placeholder image temporarily, for use with JavaScript.
		$html .= '<img src="' . esc_url( Woothemes_SC_Utils::get_placeholder_image() ) . '" class="woothemes-sc-placeholder-image" style="display: none;" width="0" height="0" />' . "\n";

		echo $html;

		if ( isset( $args['data']['description'] ) ) {
			echo '<span class="description">' . wp_kses_post( $args['data']['description'] ) . '</span>' . "\n";
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
	private function _single_network_field ( $data, $args, $networks, $i, $show_remove = true ) {
		$html = '';

		$html .= '<div class="woothemes-sc-network-item">' . "\n";
		$html .= '<div class="fields">' . "\n";
		$html .= '<input type="text" name="' . esc_attr( $this->token ) . '[' . esc_attr( $args['key'] ) . '][' . intval( $i ) . '][url]" placeholder="' . __( 'Place your profile URL here', 'woothemes-sc' ) . '" value="' . esc_attr( $data['url'] ) . '" />' . "\n";
		if ( 0 < count( $networks ) ) {
			$html .= '<select name="' . esc_attr( $this->token ) . '[' . esc_attr( $args['key'] ) . '][' . intval( $i ) . '][network]">' . "\n";
			$html .= '<option value="custom">' . __( 'Custom', 'woothemes-sc' ) . '</option>' . "\n";
			foreach ( $networks as $k => $v ) {
				$html .= '<option value="' . esc_attr( $k ) . '"' . selected( $k, $data['network'], false ) . '>' . esc_html( $v ) . '</option>' . "\n";
			}
			$html .= '</select>' . "\n";
		}
		$html .= '<span class="image-upload">' . "\n";
		$html .= '<input type="hidden" name="' . esc_attr( $this->token ) . '[' . esc_attr( $args['key'] ) . '][' . intval( $i ) . '][image]" value="' . esc_attr( $data['image'] ) . '" class="upload-url" />' . "\n";
		$html .= '</span>' . "\n";
		$html .= '<a href="#" class="remove-button">' . __( 'Remove', 'woothemes-sc' ) . '</a>' . "\n";
		$html .= '</div><!--/.fields-->' . "\n";

		$image_url = Woothemes_SC_Utils::get_placeholder_image();
		$button_type = 'add';
		if ( isset( $data['image'] ) && '' != $data['image'] ) {
			$image_url = $data['image'];
			$button_type = 'remove';
		}

		$html .= '<span class="image-preview">' . "\n";
			$html .= '<a href="#" title="' . esc_attr__( 'Preview', 'woothemes-sc' ) . '" target="_blank" class="preview-link ' . esc_attr( $button_type ) . '" data-uploader-title="' . esc_attr__( 'Select Icon', 'woothemes-sc' ) . '" data-uploader-button-text="' . esc_attr__( 'Select Icon', 'woothemes-sc' ) . '">' . "\n";
				$html .= '<img src="' . esc_url( $image_url ) . '" />' . "\n";
				$html .= '<span class="overlay"></span>' . "\n";
			$html .= '</a>' . "\n";
		$html .= '</span>' . "\n";
		$html .= '</div>' . "\n";

		echo $html;
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
		$html = '<div id="' . $args['key'] . '" class="info-box' . $class . '">' . "\n";
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
	 * validate_field_range function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param string $input
	 * @return string
	 */
	public function validate_field_range ( $input ) {
		$input = number_format( floatval( $input ), 1 );

		return $input;
	} // End validate_field_range()

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
			$message = sprintf( __( '%s is a required field', 'woothemes-sc' ), $data['name'] );
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
			$message = sprintf( __( '%s settings updated', 'woothemes-sc' ), $this->name );
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
} // End Class
?>