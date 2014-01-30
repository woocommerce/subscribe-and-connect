<?php
/**
 * Variables, for use within the rest of this file (unset at the end).
 * @since  1.0.0
 */
global $woothemes_sc;
$woothemes_sc->setup_settings();
$woothemes_sc_settings = $woothemes_sc->get_settings();

/**
 * Run through the various auto integration options and, maybe, integrate.
 * @since  1.0.0
 * @return void
 */
function woothemes_sc_maybe_auto_integrate () {
	// If not on a single view, or the current single view doesn't support this feature, ignore this function.
	if ( ! is_singular() || ! post_type_supports( get_post_type(), 'subscribe-and-connect' ) ) return;
	global $woothemes_sc;
	$woothemes_sc->setup_settings();
	$woothemes_sc_settings = $woothemes_sc->get_settings();

	switch ( $woothemes_sc_settings['integration']['auto_integration'] ) {
		case 'the_content':
			add_filter( 'the_content', 'woothemes_sc_content_filter' );
		break;

		case 'woo_post_after':
			add_action( 'woo_post_after-single', 'woothemes_sc_display' );
		break;

		case 'none':
		default:
		break;
	}
} // End woothemes_sc_maybe_auto_integrate()

add_action( 'get_header', 'woothemes_sc_maybe_auto_integrate' );

/**
 * Maybe integrate on a custom hook.
 * @since  1.0.0
 * @return void
 */
function woothemes_sc_maybe_custom_hook () {
	global $woothemes_sc;
	$woothemes_sc->setup_settings();
	$woothemes_sc_settings = $woothemes_sc->get_settings();

	if ( '' != $woothemes_sc_settings['integration']['custom_hook_name'] ) {
		add_action( esc_attr( $woothemes_sc_settings['integration']['custom_hook_name'] ), 'woothemes_sc_display' );
	}
} // End woothemes_sc_maybe_custom_hook()

add_action( 'get_header', 'woothemes_sc_maybe_custom_hook' );

/**
 * If enabled, override the Subscribe & Connect functionality in the theme.
 * @since  1.0.0
 */
if ( true == $woothemes_sc_settings['integration']['disable_theme_sc'] && ! function_exists( 'woo_subscribe_connect' ) ) {
	function woo_subscribe_connect() {} // End woo_subscribe_connect()
}

/**
 * Return HTML markup for the "subscribe" and "connect" sections, below the given content.
 * @since  1.0.0
 * @return string HTML markup.
 */
function woothemes_sc_content_filter ( $content ) {
	return $content . woothemes_sc_get();
} // End woothemes_sc_content_filter()

/**
 * Display HTML markup for the "subscribe" and "connect" sections.
 * @since  1.0.0
 * @return void
 */
function woothemes_sc_display () {
	echo woothemes_sc_get();
} // End woothemes_sc_display()

/**
 * Return HTML markup for the "subscribe" and "connect" sections.
 * @since  1.0.0
 * @return string HTML markup.
 */
function woothemes_sc_get () {
	$html = '<div class="woothemes-sc-connect">' . "\n";
	$html .= woothemes_sc_get_welcome_text();
	$html .= woothemes_sc_get_subscribe();
	$html .= woothemes_sc_get_connect();
	$html .= '</div><!--/.woothemes-sc-connect-->' . "\n";
	return $html;
} // End woothemes_sc_get()

/**
 * Display HTML markup for the "welcome text" section.
 * @since  1.0.0
 * @return void
 */
function woothemes_sc_welcome_text () {
	echo woothemes_sc_get_welcome_text();
} // End woothemes_sc_welcome_text()

/**
 * Return HTML markup for the "welcome text" section.
 * @since  1.0.0
 * @return string HTML markup.
 */
function woothemes_sc_get_welcome_text ( $args = array() ) {
	global $woothemes_sc;
	$settings = $woothemes_sc->get_settings();

	$defaults = array( 'before_title' => '<h2>', 'after_title' => '</h2>' );
	$args = wp_parse_args( $args, $defaults );

	$html = '';
	if ( '' != $settings['subscribe']['title'] ) {
		$html .= $args['before_title'] . $settings['subscribe']['title'] . $args['after_title'];
	}
	if ( '' != $settings['subscribe']['text'] ) {
		$html .= '<div class="description">' . wpautop( $settings['subscribe']['text'] ) . '</div><!--/.description-->' . "\n";
	}

	return $html;
} // End woothemes_sc_get_welcome_text()

/**
 * Display HTML markup for the "subscribe" section.
 * @since  1.0.0
 * @return void
 */
function woothemes_sc_subscribe () {
	echo woothemes_sc_get_subscribe();
} // End woothemes_sc_subscribe()

/**
 * Return HTML markup for the "subscribe" section.
 * @since  1.0.0
 * @return string HTML markup.
 */
function woothemes_sc_get_subscribe () {
	global $woothemes_sc;
	$settings = $woothemes_sc->get_settings();

	// Break out, if we don't want to print a newsletter subscription form.
	if ( 'none' == $settings['subscribe']['newsletter_service'] ) return false;

	switch ( $settings['subscribe']['newsletter_service'] ) {
		case 'feedburner':
			$form_action = 'http://feedburner.google.com/fb/a/mailverify';
			$text_fields = array( 'email' => __( 'Your Email Address', 'woothemes-sc' ) );
			$hidden_fields = array( 'uri' => $settings['subscribe']['newsletter_service_id'], 'title' => get_bloginfo( 'name' ), 'loc' => 'en_US' );
		break;

		case 'campaign_monitor':
			$cm_array = explode( '/', $settings['subscribe']['newsletter_service_form_action'] );
			array_pop( $cm_array );
			$cm_id = end( $cm_array );
			$form_action = $settings['subscribe']['newsletter_service_form_action'];
			$text_fields = array( 'name' => __( 'Name', 'woothemes-sc' ), 'cm-' . $cm_id . '-' . $cm_id => __( 'Your Email Address', 'woothemes-sc' ) );
			$hidden_fields = array( 'uri' => $settings['subscribe']['newsletter_service_id'], 'title' => get_bloginfo( 'name' ), 'loc' => 'en_US' );
		break;

		case 'mailchimp':
			$form_action = $settings['subscribe']['newsletter_mail_chimp_list_subscription_url'];
			$text_fields = array( 'EMAIL' => __( 'Your Email Address', 'woothemes-sc' ) );
		break;

		case 'aweber':
			$form_action = 'http://www.aweber.com/scripts/addlead.pl';
			$text_fields = array( 'name' => __( 'Name', 'woothemes-sc' ), 'email' => __( 'Your Email Address', 'woothemes-sc' ) );
			$hidden_fields = array(
									'meta_web_form_id' => '1687488389',
									'meta_split_id' => '',
									'listname' => $settings['subscribe']['newsletter_aweber_list_id'],
									'redirect' => esc_url( apply_filters( 'woothemes_sc_aweber_redirect', 'http://www.aweber.com/thankyou-coi.htm?m=text' ) ),
									'meta_adtracking' => '',
									'meta_message' => '',
									'meta_required' => 'name,email',
									'meta_tooltip' => ''
								);
		break;

		case 'madmimi':
			$form_action = $settings['subscribe']['newsletter_mad_mimi_subscription_url'];
			$text_fields = array( 'email' => __( 'Your Email Address', 'woothemes-sc' ) );
		break;

		default:
			$form_action = '';
			$text_fields = array( 'email' => __( 'Your Email Address', 'woothemes-sc' ) );
			$hidden_fields = array();
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
	$html .= '<input class="submit" type="submit" name="submit" value="' . esc_attr__( 'Subscribe', 'woothemes-sc' ) . '" />' . "\n";
	$html .= '</form>' . "\n";

	return $html;
} // End woothemes_sc_get_subscribe()

/**
 * Display HTML markup for the "connect" section.
 * @since  1.0.0
 * @return void
 */
function woothemes_sc_connect () {
	echo woothemes_sc_get_connect();
} // End woothemes_sc_connect()

/**
 * Return HTML markup for the "connect" section.
 * @since  1.0.0
 * @return string HTML markup.
 */
function woothemes_sc_get_connect () {
	global $woothemes_sc;
	$settings = $woothemes_sc->get_settings();

	$html = '';
	$html .= woothemes_sc_get_networks();

	// TODO

	return $html;
} // End woothemes_sc_get_connect()

/**
 * Display HTML markup for the networks.
 * @since  1.0.0
 * @return void
 */
function woothemes_sc_networks () {
	echo woothemes_sc_get_networks();
} // End woothemes_sc_networks()

/**
 * Return HTML markup for the networks.
 * @since  1.0.0
 * @return string HTML markup.
 */
function woothemes_sc_get_networks () {
	global $woothemes_sc;
	$settings = $woothemes_sc->get_settings();
	$networks = Woothemes_SC_Utils::get_networks_in_order( $settings['connect']['networks'], $settings['connect']['network_order'] );

	$list = '';
	if ( 0 < count( $networks ) ) {
		foreach ( $networks as $k => $v ) {
			if ( ! isset( $v['url'] ) || '' == $v['url'] ) continue;

			$class = $k;
			if ( '' != $v['image'] ) {
				$class .= ' has-image';
				$img = '<img src="' . esc_url( $v['image'] ) . '" />';
			} else {
				$class .= ' uses-theme';
				$img = '';
			}

			$list .= '<li class="' . esc_attr( $class ) . '"><a href="' . esc_url( $v['url'] ) . '"><span>' . "\n";
			if ( '' != $img ) {
				$list .= $img;
			} else {

			}
			$list .= '</span></a></li>' . "\n";
		}
	}

	$html = '';
	if ( '' != $list ) {
		$list = apply_filters( 'woothemes_sc_networks_list', $list, $settings, $theme, $networks );
		// Parse and apply the icon theme, if applicable.
		$theme = 'default';
		if ( $woothemes_sc->context->is_valid_theme( $settings['integration']['theme'] ) ) {
			$theme = $woothemes_sc->context->get_sanitized_theme_key( $settings['integration']['theme'] );
		}
		$html .= '<ul class="icons ' . esc_attr( 'icon-theme-' . $theme ) . '">' . "\n";
		$html .= $list;
		$html .= '</ul><!--/.icons-->' . "\n";
	}

	return $html;
} // End woothemes_sc_get_networks()

/**
 * Maybe output an RSS icon, appended to the given input.
 * @since  1.0.0
 * @return string HTML markup.
 */
function woothemes_sc_maybe_output_rss_icon ( $html, $settings ) {
	if ( isset( $settings['connect']['rss'] ) && true == $settings['connect']['rss'] ) {
		$html .= '<li class="rss uses-theme"><a href="' . esc_url( get_feed_link() ) . '" title="' . __( 'Subscribe via RSS', 'woothemes-sc' ) . '"><span></span></a></li>' . "\n";
	}
	return $html;
} // End woothemes_sc_maybe_output_rss_icon()

add_filter( 'woothemes_sc_networks_list', 'woothemes_sc_maybe_output_rss_icon', 10, 2 );

/**
 * Unset variables declared for use in this file only.
 * @since  1.0.0
 */
unset( $woothemes_sc_settings );
?>