<?php
/**
 * Variables, for use within the rest of this file (unset at the end).
 * @since  1.0.0
 */
global $subscribe_and_connect;
$subscribe_and_connect->setup_settings();
$subscribe_and_connect_settings = $subscribe_and_connect->get_settings();

/**
 * If enabled, override the Subscribe & Connect functionality in the theme.
 * @since  1.0.0
 */
if ( true == $subscribe_and_connect_settings['display']['disable_theme_sc'] && ! function_exists( 'woo_subscribe_connect' ) ) {
	function woo_subscribe_connect() {} // End woo_subscribe_connect()
}

/**
 * Run through the various auto integration options and, maybe, integrate.
 * @since  1.0.0
 * @return void
 */
function subscribe_and_connect_maybe_auto_integrate () {
	// If not on a single view, or the current single view doesn't support this feature, ignore this function.
	if ( ! is_singular() || ! post_type_supports( get_post_type(), 'subscribe-and-connect' ) ) return;
	global $subscribe_and_connect;
	$subscribe_and_connect->setup_settings();
	$subscribe_and_connect_settings = $subscribe_and_connect->get_settings();

	switch ( $subscribe_and_connect_settings['display']['auto_integration'] ) {
		case 'the_content':
			add_filter( 'the_content', 'subscribe_and_connect_content_filter' );
		break;

		case 'woo_post_after':
			add_action( 'woo_post_after', 'subscribe_and_connect_display' );
		break;

		case 'none':
		default:
		break;
	}
} // End subscribe_and_connect_maybe_auto_integrate()

add_action( 'get_header', 'subscribe_and_connect_maybe_auto_integrate' );

/**
 * Maybe integrate on a custom hook.
 * @since  1.0.0
 * @return void
 */
function subscribe_and_connect_maybe_custom_hook () {
	global $subscribe_and_connect;
	$subscribe_and_connect->setup_settings();
	$subscribe_and_connect_settings = $subscribe_and_connect->get_settings();

	if ( '' != $subscribe_and_connect_settings['display']['custom_hook_name'] ) {
		add_action( esc_attr( $subscribe_and_connect_settings['display']['custom_hook_name'] ), 'subscribe_and_connect_display' );
	}
} // End subscribe_and_connect_maybe_custom_hook()

add_action( 'get_header', 'subscribe_and_connect_maybe_custom_hook' );

/**
 * Return HTML markup for the "subscribe" and "connect" sections, below the given content.
 * @since  1.0.0
 * @return string HTML markup.
 */
function subscribe_and_connect_content_filter ( $content ) {
	$post_type = get_post_type();
	if ( 'post' == $post_type && is_single() ) {
		return $content . subscribe_and_connect_get();
	} else {
		return $content;
	}
} // End subscribe_and_connect_content_filter()

/**
 * Display HTML markup for the "subscribe" and "connect" sections.
 * @since  1.0.0
 * @return void
 */
function subscribe_and_connect_display () {
	echo subscribe_and_connect_get();
} // End subscribe_and_connect_display()

/**
 * Return HTML markup for the "subscribe" and "connect" sections.
 * @since  1.0.0
 * @return string HTML markup.
 */
function subscribe_and_connect_get () {
	$html = '<div class="subscribe-and-connect-connect">' . "\n";
	$html .= subscribe_and_connect_get_welcome_text();
	$html .= subscribe_and_connect_get_subscribe();
	$html .= subscribe_and_connect_get_connect();
	$html .= '</div><!--/.subscribe-and-connect-connect-->' . "\n";
	return $html;
} // End subscribe_and_connect_get()

/**
 * Display HTML markup for the "welcome text" section.
 * @since  1.0.0
 * @return void
 */
function subscribe_and_connect_welcome_text () {
	echo subscribe_and_connect_get_welcome_text();
} // End subscribe_and_connect_welcome_text()

/**
 * Return HTML markup for the "welcome text" section.
 * @since  1.0.0
 * @return string HTML markup.
 */
function subscribe_and_connect_get_welcome_text ( $args = array() ) {
	global $subscribe_and_connect;
	$settings = $subscribe_and_connect->get_settings();

	$defaults = array( 'before_title' => '<h2>', 'after_title' => '</h2>' );
	$args = wp_parse_args( $args, $defaults );

	$html = '';
	if ( '' != $settings['general']['title'] ) {
		$html .= $args['before_title'] . $settings['general']['title'] . $args['after_title'];
	}
	if ( '' != $settings['general']['text'] ) {
		$html .= '<div class="description">' . wpautop( $settings['general']['text'] ) . '</div><!--/.description-->' . "\n";
	}

	return $html;
} // End subscribe_and_connect_get_welcome_text()

/**
 * Display HTML markup for the "subscribe" section.
 * @since  1.0.0
 * @return void
 */
function subscribe_and_connect_subscribe () {
	echo subscribe_and_connect_get_subscribe();
} // End subscribe_and_connect_subscribe()

/**
 * Return HTML markup for the "subscribe" section.
 * @since  1.0.0
 * @return string HTML markup.
 */
function subscribe_and_connect_get_subscribe () {
	global $subscribe_and_connect;
	$settings = $subscribe_and_connect->get_settings();

	// Break out, if we don't want to print a newsletter subscription form.
	if ( 'none' == $settings['connect']['newsletter_service'] ) return '';

	switch ( $settings['connect']['newsletter_service'] ) {
		case 'feedburner':
			$form_action 	= 'http://feedburner.google.com/fb/a/mailverify';
			$text_fields 	= apply_filters( 'subscribe-and-connect-feedburner-text-fields', array(
									'email' => __( 'Your Email Address', 'subscribe-and-connect' )
									) );
			$hidden_fields 	= array( 'uri' => $settings['connect']['newsletter_service_id'], 'title' => get_bloginfo( 'name' ), 'loc' => 'en_US' );
		break;

		case 'campaign_monitor':
			$cm_array = explode( '/', $settings['connect']['newsletter_service_form_action'] );
			array_pop( $cm_array );
			$cm_id 			= end( $cm_array );
			$form_action 	= $settings['connect']['newsletter_service_form_action'];
			$text_fields 	= apply_filters( 'subscribe-and-connect-campaign-monitor-text-fields', array(
									'name' 							=> __( 'Name', 'subscribe-and-connect' ),
									'cm-' . $cm_id . '-' . $cm_id 	=> __( 'Your Email Address', 'subscribe-and-connect' )
									) );
			$hidden_fields 	= array( 'uri' => $settings['connect']['newsletter_service_id'], 'title' => get_bloginfo( 'name' ), 'loc' => 'en_US' );
		break;

		case 'mailchimp':
			$form_action = $settings['connect']['newsletter_mail_chimp_list_subscription_url'];
			$text_fields = apply_filters( 'subscribe-and-connect-mailchimp-text-fields', array(
								'EMAIL' => __( 'Your Email Address', 'subscribe-and-connect' )
								) );
		break;

		case 'aweber':
			$form_action 	= 'http://www.aweber.com/scripts/addlead.pl';
			$text_fields 	= apply_filters( 'subscribe-and-connect-aweber-text-fields', array(
									'name' 		=> __( 'Name', 'subscribe-and-connect' ),
									'email' 	=> __( 'Your Email Address', 'subscribe-and-connect' )
									) );
			$hidden_fields 	= array(
									'meta_web_form_id' => '1687488389',
									'meta_split_id' => '',
									'listname' => $settings['connect']['newsletter_aweber_list_id'],
									'redirect' => esc_url( apply_filters( 'subscribe_and_connect_aweber_redirect', 'http://www.aweber.com/thankyou-coi.htm?m=text' ) ),
									'meta_adtracking' => '',
									'meta_message' => '',
									'meta_required' => 'name,email',
									'meta_tooltip' => ''
								);
		break;

		case 'madmimi':
			$form_action = $settings['general']['newsletter_mad_mimi_subscription_url'];
			$text_fields = apply_filters( 'subscribe-and-connect-madmimi-text-fields', array(
								'email' => __( 'Your Email Address', 'subscribe-and-connect' )
								) );
		break;

		case 'wysija':
			$form_action = '';
			$text_fields 	= apply_filters( 'subscribe-and-connect-wysija-text-fields', array(
									'subscribe_and_connect_wysija_name' 	=> __( 'Name', 'subscribe-and-connect' ),
									'subscribe_and_connect_wysija_email' 	=> __( 'Your Email Address', 'subscribe-and-connect' )
									) );
			$hidden_fields 	= array(
									'list_ids' 								=> $settings['connect']['newsletter_wysija_list_id'],
									'subscribe_and_connect_wysija_submit' 	=> true
								);
		break;

		default:
			$form_action 	= '';
			$text_fields 	= apply_filters( 'subscribe-and-connect-text-fields', array(
									'email' => __( 'Your Email Address', 'subscribe-and-connect' )
									) );
			$hidden_fields 	= array();
		break;
	}

	$html = '';

	$html .= '<form class="newsletter-form" action="' . esc_attr( esc_url( $form_action ) ) . '" method="post">' . "\n";
	if ( 0 < count( $text_fields ) ) {
		foreach ( $text_fields as $k => $v ) {
			$html .= '<input type="text" placeholder="' . esc_attr( $v ) . '" name="' . esc_attr( $k ) . '"/>' . "\n";
		}
	}
	if ( 0 < count( $hidden_fields ) ) {
		foreach ( $hidden_fields as $k => $v ) {
			$html .= '<input type="hidden" value="' . esc_attr( $v ) . '" name="' . esc_attr( $k ) . '"/>' . "\n";
		}
	}
	$html .= '<input class="submit" type="submit" name="submit" value="' . esc_attr__( 'Subscribe', 'subscribe-and-connect' ) . '" />' . "\n";
	$html .= '</form>' . "\n";

	return $html;
} // End subscribe_and_connect_get_subscribe()

/**
 * Display HTML markup for the "connect" section.
 * @since  1.0.0
 * @return void
 */
function subscribe_and_connect_connect () {
	echo subscribe_and_connect_get_connect();
} // End subscribe_and_connect_connect()

/**
 * Return HTML markup for the "connect" section.
 * @since  1.0.0
 * @return string HTML markup.
 */
function subscribe_and_connect_get_connect () {
	global $subscribe_and_connect;
	$settings = $subscribe_and_connect->get_settings();

	$html = '';
	$html .= subscribe_and_connect_get_networks();

	// TODO

	return $html;
} // End subscribe_and_connect_get_connect()

/**
 * Display HTML markup for the networks.
 * @since  1.0.0
 * @return void
 */
function subscribe_and_connect_networks () {
	echo subscribe_and_connect_get_networks();
} // End subscribe_and_connect_networks()

/**
 * Return HTML markup for the networks.
 * @since  1.0.0
 * @return string HTML markup.
 */
function subscribe_and_connect_get_networks () {
	global $subscribe_and_connect;
	$settings = $subscribe_and_connect->get_settings();
	$networks = Subscribe_And_Connect_Utils::get_networks_in_order( $settings['connect']['networks'], $settings['connect']['networks_order'] );

	$list = '';
	if ( 0 < count( $networks ) ) {
		foreach ( $networks as $k => $v ) {
			if ( ! isset( $v['url'] ) || '' == $v['url'] ) continue;

			$class = $k;

			$list .= '<li class="' . esc_attr( $class ) . '"><a href="' . esc_url( $v['url'] ) . '"><span>' . "\n";

			$list .= '</span></a></li>' . "\n";
		}
	}

	$html = '';
	if ( '' != $list ) {
		$theme = 'icons';
		$list = apply_filters( 'subscribe_and_connect_networks_list', $list, $settings, $theme, $networks );
		// Parse and apply the icon theme, if applicable.
		if ( $subscribe_and_connect->context->is_valid_theme( $settings['display']['theme'] ) ) {
			$theme = $subscribe_and_connect->context->get_sanitized_theme_key( $settings['display']['theme'] );
		}
		$html .= '<ul class="icons ' . esc_attr( 'icon-theme-' . $theme ) . '">' . "\n";
		$html .= $list;
		$html .= '</ul><!--/.icons-->' . "\n";
	}

	return $html;
} // End subscribe_and_connect_get_networks()

/**
 * Maybe output an RSS icon, appended to the given input.
 * @since  1.0.0
 * @return string HTML markup.
 */
function subscribe_and_connect_maybe_output_rss_icon ( $html, $settings ) {
	if ( isset( $settings['connect']['rss'] ) && true == $settings['connect']['rss'] ) {
		$html .= '<li class="rss uses-theme"><a href="' . esc_url( get_feed_link() ) . '" title="' . __( 'Subscribe via RSS', 'subscribe-and-connect' ) . '"><span></span></a></li>' . "\n";
	}
	return $html;
} // End subscribe_and_connect_maybe_output_rss_icon()

add_filter( 'subscribe_and_connect_networks_list', 'subscribe_and_connect_maybe_output_rss_icon', 10, 2 );

/**
 * Unset variables declared for use in this file only.
 * @since  1.0.0
 */
unset( $subscribe_and_connect_settings );