<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * WooThemes Subscribe & Connect Widget
 *
 * A WooThemes standardized Subscribe & Connect widget.
 *
 * @package WordPress
 * @category Widgets
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * protected $subscribe_and_connect_widget_cssclass
 * protected $subscribe_and_connect_widget_description
 * protected $subscribe_and_connect_widget_idbase
 * protected $subscribe_and_connect_widget_title
 *
 * - __construct()
 * - widget()
 * - form()
 */
class Subscribe_And_Connect_Widget extends WP_Widget {
	protected $subscribe_and_connect_widget_cssclass;
	protected $subscribe_and_connect_widget_description;
	protected $subscribe_and_connect_widget_idbase;
	protected $subscribe_and_connect_widget_title;

	/**
	 * Constructor function.
	 * @since  1.0.0
	 * @return  void
	 */
	public function __construct() {
		/* Widget variable settings. */
		$this->subscribe_and_connect_widget_cssclass 	= 'widget_subscribe_and_connect_items';
		$this->subscribe_and_connect_widget_description = __( 'Subcribe & Connect.', 'subscribe-and-connect' );
		$this->subscribe_and_connect_widget_idbase 		= 'subscribe-and-connect';
		$this->subscribe_and_connect_widget_title 		= __( 'Subscribe & Connect', 'subscribe-and-connect' );

		// Cache
		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );

		/* Widget settings. */
		$widget_ops = array(
			'classname' 	=> $this->subscribe_and_connect_widget_cssclass,
			'description' 	=> $this->subscribe_and_connect_widget_description
			);

		/* Widget control settings. */
		$control_ops = array(
			'id_base' 	=> $this->subscribe_and_connect_widget_idbase
			);

		/* Create the widget. */
		$this->WP_Widget( $this->subscribe_and_connect_widget_idbase, $this->subscribe_and_connect_widget_title, $widget_ops, $control_ops );
	} // End __construct()

	/**
	 * Display the widget on the frontend.
	 * @since  1.0.0
	 * @param  array $args     Widget arguments.
	 * @param  array $instance Widget settings for this instance.
	 * @return void
	 */
	public function widget( $args, $instance ) {

		$cache = wp_cache_get( 'widget_subscribe_and_connect_items', 'widget' );

		if ( !is_array($cache) )
			$cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		if ( isset( $instance['description'] ) ) {
			$args['description'] = $instance['description'];
		} else {
			$args['description'] = $args['description'];
		}

		ob_start();

		extract( $args, EXTR_SKIP );

		/* Our variables from the widget settings. */
		$title 			= apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$description 	= $args['description'];

		$args = array();

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title ) { $args['title'] = $title; }

		/* Widget content. */
		// Add actions for plugins/themes to hook onto.
		do_action( $this->subscribe_and_connect_widget_cssclass . '_top' );

		if ( isset( $instance['social'] ) ) { $args['social'] = $instance['social']; }
		if ( isset( $instance['subscribe'] ) ) { $args['subscribe'] = $instance['subscribe']; }

		// Display S&C.
		echo $before_widget;

		if ( $title )
				echo $before_title . $title . $after_title;

		echo '<div class="subscribe-and-connect-connect">';
		echo $description;

		if ( true == $instance['subscribe'] ) {
			subscribe_and_connect_subscribe();
		}

		if ( true == $instance['social'] ) {
			subscribe_and_connect_connect();
		}
		echo '</div><!--/.subscribe-and-connect-connect-->';

		echo $after_widget;

		// Add actions for plugins/themes to hook onto.
		do_action( $this->subscribe_and_connect_widget_cssclass . '_bottom' );

		$cache[ $widget_id ] = ob_get_flush();

		wp_cache_set( 'widget_subscribe_and_connect_items', $cache, 'widget' );

	} // End widget()

	/**
	 * Method to update the settings from the form() method.
	 * @since  1.0.0
	 * @param  array $new_instance New settings.
	 * @param  array $old_instance Previous settings.
	 * @return array               Updated settings.
	 */
	public function update ( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] 			= strip_tags( $new_instance['title'] );
		$instance['description'] 	= wp_kses( $new_instance['description'] );

		/* Escape checkbox values */
		$instance['social']  		= esc_attr( $new_instance['social'] );
		$instance['subscribe']  	= esc_attr( $new_instance['subscribe'] );

		/* Flush cache. */
		$this->flush_widget_cache();
		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_woothemes_wc_items']) )
			delete_option( 'widget_woothemes_wc_items' );

		return $instance;
	} // End update()

	/**
	 * The form on the widget control in the widget administration area.
	 * Make use of the get_field_id() and get_field_name() function when creating your form elements. This handles the confusing stuff.
	 * @since  1.0.0
	 * @param  array $instance The settings for this instance.
	 * @return void
	 */
    public function form( $instance ) {
    	global $subscribe_and_connect;
		$settings = $subscribe_and_connect->get_settings();

		/* Set up some default widget settings. */
		/* Make sure all keys are added here, even with empty string values. */
		$defaults = array(
			'title' 		=> __( 'Subscribe & Connect', 'subscribe-and-connect' ),
			'description'	=> $settings['general']['text'],
			'social'		=> 0,
			'subscribe'		=> 0,
		);

		$instance = wp_parse_args( (array) $instance, $defaults );
?>
		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title (optional):', 'subscribe-and-connect' ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>"  value="<?php echo $instance['title']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'description' ); ?>"><?php _e( 'Description (optional):', 'subscribe-and-connect' ); ?></label>
			<textarea type="text" name="<?php echo $this->get_field_name( 'description' ); ?>"  rows="4" class="widefat" id="<?php echo $this->get_field_id( 'description' ); ?>"><?php echo $instance['description']; ?></textarea>
		</p>

		<!-- Widget Show Subscribe: Checkbox Input -->
		<p>
			<input id="<?php echo $this->get_field_id( 'subscribe' ); ?>" name="<?php echo $this->get_field_name( 'subscribe' ); ?>" type="checkbox" value="1" <?php checked( '1', $instance['subscribe'] ); ?> />
			<label for="<?php echo $this->get_field_id( 'subscribe' ); ?>"><?php _e( 'Display subscribe form', 'subscribe-and-connect' ); ?></label>
		</p>

		<!-- Widget Show Social: Checkbox Input -->
		<p>
			<input id="<?php echo $this->get_field_id( 'social' ); ?>" name="<?php echo $this->get_field_name( 'social' ); ?>" type="checkbox" value="1" <?php checked( '1', $instance['social'] ); ?> />
			<label for="<?php echo $this->get_field_id( 'social' ); ?>"><?php _e( 'Display social icons', 'subscribe-and-connect' ); ?></label>
		</p>
<?php
	} // End form()

	/**
	 * Flush widget cache
	 * @since  1.0.0
	 * @return void
	 */
	public function flush_widget_cache() {
		wp_cache_delete( 'widget_subscribe_and_connect_items', 'widget' );
	}

} // End Class

/* Register the widget. */
add_action( 'widgets_init', create_function( '', 'return register_widget( "Subscribe_And_Connect_Widget" );' ), 1 );