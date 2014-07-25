jQuery( document ).ready( function( $ ) {
	// Show Hide newsletter fields based on service
	$( '#newsletter_service' ).change( function() {
		$( 'input[id^="newsletter_"]' ).closest( 'tr' ).hide();
		$( '#newsletter_wysija_list_id' ).closest( 'tr' ).hide();

		switch ( $( this ).val() ) {
			case 'aweber':
				$( '#newsletter_aweber_list_id' ).closest( 'tr' ).show();
			break;
			case 'campaign_monitor':
				$( '#newsletter_service_form_action' ).closest( 'tr' ).show();
			break;
			case 'feedburner':
				$( '#newsletter_service_id' ).closest( 'tr' ).show();
			break;
			case 'mad_mimi':
				$( '#newsletter_mad_mimi_subscription_url' ).closest( 'tr' ).show();
			break;
			case 'mailchimp':
				$( '#newsletter_mail_chimp_list_subscription_url' ).closest( 'tr' ).show();
			break;
			case 'wysija':
				$( '#newsletter_wysija_list_id' ).closest( 'tr' ).show();
			break;
			case 'none':
			break;
			default:
		}
	});
	// Trigger initial change event.
	$( '#newsletter_service' ).trigger( 'change' );

	// Add alternate class to table tr's
	$( '.subscribe-and-connect-network-fields tbody tr:nth-child(odd)' ).addClass( 'alternate' );
});