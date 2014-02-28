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
 * protected $woothemes_sc_widget_cssclass
 * protected $woothemes_sc_widget_description
 * protected $woothemes_sc_widget_idbase
 * protected $woothemes_sc_widget_title
 *
 * - __construct()
 * - widget()
 * - form()
 */
class Woothemes_Widget_Subscribe_Connect extends WP_Widget {
	protected $woothemes_sc_widget_cssclass;
	protected $woothemes_sc_widget_description;
	protected $woothemes_sc_widget_idbase;
	protected $woothemes_sc_widget_title;

	/**
	 * Constructor function.
	 * @since  1.0.0
	 * @return  void
	 */
	public function __construct() {
		/* Widget variable settings. */
		$this->woothemes_sc_widget_cssclass 	= 'widget_woothemes_sc_items';
		$this->woothemes_sc_widget_description = __( 'Subcribe & Connect.', 'woothemes-sc' );
		$this->woothemes_sc_widget_idbase 		= 'woothemes-sc';
		$this->woothemes_sc_widget_title 		= __( 'Subscribe & Connect', 'woothemes-sc' );

		// Cache
		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );

		/* Widget settings. */
		$widget_ops = array(
			'classname' 	=> $this->woothemes_sc_widget_cssclass,
			'description' 	=> $this->woothemes_sc_widget_description
			);

		/* Widget control settings. */
		$control_ops = array(
			'id_base' 	=> $this->woothemes_sc_widget_idbase
			);

		/* Create the widget. */
		$this->WP_Widget( $this->woothemes_sc_widget_idbase, $this->woothemes_sc_widget_title, $widget_ops, $control_ops );
	} // End __construct()

	/**
	 * Display the widget on the frontend.
	 * @since  1.0.0
	 * @param  array $args     Widget arguments.
	 * @param  array $instance Widget settings for this instance.
	 * @return void
	 */
	public function widget( $args, $instance ) {

		$cache = wp_cache_get( 'widget_woothemes_sc_items', 'widget' );

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
		do_action( $this->woothemes_sc_widget_cssclass . '_top' );

		// Display S&C.
		echo $before_widget;

		if ( $title )
				echo $before_title . $title . $after_title;

		echo '<div class="woothemes-sc-connect">';
		echo $description;
		woothemes_sc_subscribe();
		woothemes_sc_connect();
		echo '</div><!--/.woothemes-sc-connect-->';

		echo $after_widget;

		// Add actions for plugins/themes to hook onto.
		do_action( $this->woothemes_sc_widget_cssclass . '_bottom' );

		$cache[ $widget_id ] = ob_get_flush();

		wp_cache_set( 'widget_woothemes_sc_items', $cache, 'widget' );

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
    	global $woothemes_sc;
		$settings = $woothemes_sc->get_settings();

		/* Set up some default widget settings. */
		/* Make sure all keys are added here, even with empty string values. */
		$defaults = array(
			'title' 		=> __( 'Subscribe & Connect', 'woothemes-sc' ),
			'description'	=> $settings['general']['text']
		);

		$instance = wp_parse_args( (array) $instance, $defaults );
?>
		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title (optional):', 'woothemes-sc' ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>"  value="<?php echo $instance['title']; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'description' ); ?>"><?php _e( 'Description (optional):', 'woothemes-sc' ); ?></label>
			<textarea type="text" name="<?php echo $this->get_field_name( 'description' ); ?>"  rows="4" class="widefat" id="<?php echo $this->get_field_id( 'description' ); ?>"><?php echo $instance['description']; ?></textarea>
		</p>
<?php
	} // End form()

	/**
	 * Flush widget cache
	 * @since  1.0.0
	 * @return void
	 */
	public function flush_widget_cache() {
		wp_cache_delete( 'widget_woothemes_sc_items', 'widget' );
	}

} // End Class

/* Register the widget. */
add_action( 'widgets_init', create_function( '', 'return register_widget( "Woothemes_Widget_Subscribe_Connect" );' ), 1 );