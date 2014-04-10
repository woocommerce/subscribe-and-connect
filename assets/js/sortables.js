jQuery( document ).ready( function ( e ) {
	jQuery( '.subscribe-and-connect-network-fields tbody' ).sortable();

	jQuery( '.subscribe-and-connect-network-fields tbody' ).bind( 'sortstop', function ( e, ui ) {
		var orderString = '';

		jQuery( e.target ).find( 'tr' ).each( function ( i, e ) {
			if ( i > 0 ) { orderString += ','; }
			orderString += jQuery( this ).attr( 'id' );
		});

		jQuery( 'input[id="networks_order"]' ).attr( 'value', orderString );
	});
});