<?php
 
add_action( 'parse_request', 'subscribe_and_connect_process_wysija' );

function subscribe_and_connect_process_wysija () {
	global $subscribe_and_connect;
	$settings = $subscribe_and_connect->get_settings();
	
	if ( isset( $_POST['subscribe_and_connect_wysija_submit'] ) ) {
	
		if ( is_email( $_POST['subscribe_and_connect_wysija_email'] ) )
			$email = sanitize_email( $_POST['subscribe_and_connect_wysija_email'] );

		if ( isset( $_POST['subscribe_and_connect_wysija_name'] ) )
			$name = sanitize_text_field( $_POST['subscribe_and_connect_wysija_name'] );

	    $data = array(
			'user' => array( 'email' => $email, 'firstname' => $name ),
			'user_list'=>array( 'list_ids'=>array( $settings['connect']['newsletter_wysija_list_id'] ) )
	    );
	    
	    $userHelper = &WYSIJA::get('user','helper');
	    $userHelper->addSubscriber( $data );
		echo '<div id="subscribe_and_connect_wysija_message">' . apply_filters( 'subscribe_and_connect_wysija_subscribed_message', __( 'Thanks for subscribing to our newsletter.', 'subscribe-and-connect' ) ) . '</div>';
	}
}